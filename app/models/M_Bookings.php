<?php

/**
 * M_Bookings Model
 * This is the "Sales Pipeline" for Rentigo. 
 * It manages everything from the moment a tenant requests a house 
 * to when they actually move in and eventually move out.
 */
class M_Bookings
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    /**
     * Start the process: A tenant wants to move in!
     * This creates a 'pending' request that the landlord needs to review.
     */
    public function createBooking($data)
    {
        $this->db->query('INSERT INTO bookings (tenant_id, property_id, landlord_id, move_in_date, move_out_date, monthly_rent, deposit_amount, total_amount, status, notes)
                         VALUES (:tenant_id, :property_id, :landlord_id, :move_in_date, :move_out_date, :monthly_rent, :deposit_amount, :total_amount, :status, :notes)');

        // Bind all the details for the new request
        $this->db->bind(':tenant_id', $data['tenant_id']);
        $this->db->bind(':property_id', $data['property_id']);
        $this->db->bind(':landlord_id', $data['landlord_id']);
        $this->db->bind(':move_in_date', $data['move_in_date']);
        $this->db->bind(':move_out_date', $data['move_out_date']);
        $this->db->bind(':monthly_rent', $data['monthly_rent']);
        $this->db->bind(':deposit_amount', $data['deposit_amount']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':status', $data['status']); // Usually starts as 'pending'
        $this->db->bind(':notes', $data['notes']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    /**
     * Fetch a booking with all the important names (Tenant, Landlord, House Address)
     */
    public function getBookingById($id)
    {
        $this->db->query('SELECT b.*,
                         p.address, p.property_type, p.bedrooms, p.bathrooms,
                         t.name as tenant_name, t.email as tenant_email,
                         l.name as landlord_name, l.email as landlord_email
                         FROM bookings b
                         LEFT JOIN properties p ON b.property_id = p.id
                         LEFT JOIN users t ON b.tenant_id = t.id
                         LEFT JOIN users l ON b.landlord_id = l.id
                         WHERE b.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get all bookings for a specific tenant (their history)
     */
    public function getBookingsByTenant($tenant_id)
    {
        $this->db->query('SELECT b.*,
                         p.address, p.property_type, p.bedrooms, p.bathrooms, p.rent,
                         l.name as landlord_name, l.email as landlord_email
                         FROM bookings b
                         LEFT JOIN properties p ON b.property_id = p.id
                         LEFT JOIN users l ON b.landlord_id = l.id
                         WHERE b.tenant_id = :tenant_id
                         ORDER BY b.created_at DESC');
        $this->db->bind(':tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    /**
     * Get all bookings for properties owned by a specific landlord
     */
    public function getBookingsByLandlord($landlord_id)
    {
        $this->db->query('SELECT b.*,
                         p.address, p.property_type, p.bedrooms, p.bathrooms,
                         t.name as tenant_name, t.email as tenant_email
                         FROM bookings b
                         LEFT JOIN properties p ON b.property_id = p.id
                         LEFT JOIN users t ON b.tenant_id = t.id
                         WHERE b.landlord_id = :landlord_id
                         ORDER BY b.created_at DESC');
        $this->db->bind(':landlord_id', $landlord_id);
        return $this->db->resultSet();
    }

    /**
     * Find all bookings associated with one particular property
     */
    public function getBookingsByProperty($property_id)
    {
        $this->db->query('SELECT b.*,
                         t.name as tenant_name, t.email as tenant_email
                         FROM bookings b
                         LEFT JOIN users t ON b.tenant_id = t.id
                         WHERE b.property_id = :property_id
                         ORDER BY b.created_at DESC');
        $this->db->bind(':property_id', $property_id);
        return $this->db->resultSet();
    }

    /**
     * Update the status of a booking (e.g., from 'pending' to 'approved')
     * Also saves a reason if it was rejected.
     */
    public function updateBookingStatus($id, $status, $rejection_reason = null)
    {
        $this->db->query('UPDATE bookings SET status = :status, rejection_reason = :rejection_reason, updated_at = NOW() WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        $this->db->bind(':rejection_reason', $rejection_reason);
        return $this->db->execute();
    }

    /**
     * Cancel a booking by the landlord or tenant
     */
    public function cancelBooking($id, $cancellation_reason)
    {
        $this->db->query('UPDATE bookings SET status = :status, cancellation_reason = :cancellation_reason, updated_at = NOW() WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', 'cancelled');
        $this->db->bind(':cancellation_reason', $cancellation_reason);
        return $this->db->execute();
    }

    /**
     * THE DOUBLE-BOOKING CHECK:
     * This makes sure two different tenants don't book the same dates for the same house.
     * It's some of the most important logic in the whole app!
     */
    public function checkDateConflict($property_id, $move_in_date, $move_out_date, $exclude_booking_id = null)
    {
        $query = 'SELECT COUNT(*) as count FROM bookings
                  WHERE property_id = :property_id
                  AND status IN ("approved", "active")
                  AND (
                      (:move_in_date BETWEEN move_in_date AND move_out_date) OR
                      (:move_out_date BETWEEN move_in_date AND move_out_date) OR
                      (move_in_date BETWEEN :move_in_date AND :move_out_date) OR
                      (move_out_date BETWEEN :move_in_date AND :move_out_date)
                  )';

        // If we're updating a booking, we ignore itself when checking for conflicts
        if ($exclude_booking_id) {
            $query .= ' AND id != :exclude_booking_id';
        }

        $this->db->query($query);
        $this->db->bind(':property_id', $property_id);
        $this->db->bind(':move_in_date', $move_in_date);
        $this->db->bind(':move_out_date', $move_out_date);

        if ($exclude_booking_id) {
            $this->db->bind(':exclude_booking_id', $exclude_booking_id);
        }

        $result = $this->db->single();
        return $result->count > 0;
    }

    /**
     * Get the one active place where a tenant is currently living
     */
    public function getActiveBookingByTenant($tenant_id)
    {
        $this->db->query('SELECT b.*,
                         p.address, p.property_type, p.bedrooms, p.bathrooms,
                         l.name as landlord_name, l.email as landlord_email
                         FROM bookings b
                         LEFT JOIN properties p ON b.property_id = p.id
                         LEFT JOIN users l ON b.landlord_id = l.id
                         WHERE b.tenant_id = :tenant_id AND b.status = "active"
                         ORDER BY b.created_at DESC LIMIT 1');
        $this->db->bind(':tenant_id', $tenant_id);
        return $this->db->single();
    }

    /**
     * Stats for the landlord's dashboard card
     */
    public function getPendingBookingsCount($landlord_id)
    {
        $this->db->query('SELECT COUNT(*) as count FROM bookings WHERE landlord_id = :landlord_id AND status = "pending" AND ' . getDateRangeSql('created_at'));
        $this->db->bind(':landlord_id', $landlord_id);
        $result = $this->db->single();
        return $result->count;
    }

    /**
     * Get a row of counts (pending, active, etc.) for a user's dashboard
     * @param int $user_id The user ID
     * @param string $user_type 'tenant' or 'landlord'
     * @param string $period 'all', 'month', or 'year' for date filtering
     */
    public function getBookingStats($user_id, $user_type, $period = 'all')
    {
        $stats = [];
        $dateFilter = getDateRangeByPeriod('created_at', $period);

        if ($user_type == 'tenant') {
            $this->db->query('SELECT
                            COUNT(*) as total,
                            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                            SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                            SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active,
                            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                            SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                            SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled
                            FROM bookings WHERE tenant_id = :user_id AND ' . $dateFilter);
        } else if ($user_type == 'landlord') {
            $this->db->query('SELECT
                            COUNT(*) as total,
                            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                            SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                            SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active,
                            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                            SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                            SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled
                            FROM bookings WHERE landlord_id = :user_id AND ' . $dateFilter);
        }

        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    /**
     * Set a booking to 'active' (meaning the lease is signed and in effect)
     */
    public function activateBooking($id)
    {
        $this->db->query('UPDATE bookings SET status = "active", updated_at = NOW() WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * MARK DOWN: The tenant has moved out and the house is free again.
     */
    public function completeBooking($id)
    {
        $this->db->query('UPDATE bookings SET status = "completed", updated_at = NOW() WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Global fetch for admin logs
     */
    public function getAllBookings()
    {
        $this->db->query('SELECT b.*,
                         p.address, p.property_type,
                         t.name as tenant_name, t.email as tenant_email,
                         l.name as landlord_name, l.email as landlord_email
                         FROM bookings b
                         LEFT JOIN properties p ON b.property_id = p.id
                         LEFT JOIN users t ON b.tenant_id = t.id
                         LEFT JOIN users l ON b.landlord_id = l.id
                         ORDER BY b.created_at DESC');
        return $this->db->resultSet();
    }

    /**
     * Fetch all bookings across a list of properties (useful for a Property Manager)
     */
    public function getBookingsByProperties($propertyIds)
    {
        if (empty($propertyIds)) {
            return [];
        }

        // Prepare placeholders (?,?,?) for the IN clause
        $placeholders = implode(',', array_fill(0, count($propertyIds), '?'));

        $this->db->query("SELECT b.*,
                         p.address, p.property_type, p.bedrooms, p.bathrooms,
                         t.name as tenant_name, t.email as tenant_email,
                         l.name as landlord_name, l.email as landlord_email
                         FROM bookings b
                         LEFT JOIN properties p ON b.property_id = p.id
                         LEFT JOIN users t ON b.tenant_id = t.id
                         LEFT JOIN users l ON b.landlord_id = l.id
                         WHERE b.property_id IN ($placeholders)
                         ORDER BY b.created_at DESC");

        // Bind property IDs to the positional placeholders
        foreach ($propertyIds as $index => $propertyId) {
            $this->db->bind($index + 1, $propertyId);
        }

        return $this->db->resultSet();
    }
}
