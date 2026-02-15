<?php

/**
 * M_AdminProperties Model
 * This is the "Control Panel" for house listings. 
 * Admins use this to review, approve, or reject new properties uploaded by landlords.
 */
class M_AdminProperties
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get every single property on the platform (approved or not).
     */
    public function getAllProperties($status = null)
    {
        $query = "SELECT 
                    p.*,
                    u.name as landlord_name,
                    u.email as landlord_email,
                    pm.name as manager_name
                  FROM properties p
                  LEFT JOIN users u ON p.landlord_id = u.id
                  LEFT JOIN users pm ON p.manager_id = pm.id";

        // If a status is provided, filter by it (pending, approved, rejected)
        if ($status) {
            $query .= " WHERE p.approval_status = :status";
        }
        $query .= " ORDER BY p.created_at DESC";

        $this->db->query($query);
        if ($status) {
            $this->db->bind(':status', $status);
        }
        $result = $this->db->resultSet();
        return is_array($result) ? $result : [];
    }

    /**
     * Counts for the dashboard: How many houses are waiting to be checked?
     * @param string $period 'all', 'month', or 'year' for date filtering
     */
    public function getPropertyCounts($period = 'all')
    {
        $dateFilter = getDateRangeByPeriod('created_at', $period);
        $this->db->query("SELECT 
                            COUNT(*) as total,
                            SUM(CASE WHEN approval_status = 'pending' THEN 1 ELSE 0 END) as pending,
                            SUM(CASE WHEN approval_status = 'approved' THEN 1 ELSE 0 END) as approved,
                            SUM(CASE WHEN approval_status = 'rejected' THEN 1 ELSE 0 END) as rejected
                          FROM properties WHERE " . $dateFilter);
        return $this->db->single();
    }

    // Get a single property by ID with all its details
    public function getPropertyById($id)
    {
        $this->db->query("SELECT 
                            p.*,
                            u.name as landlord_name,
                            u.email as landlord_email,
                            u.user_type,
                            pm.name as manager_name
                          FROM properties p
                          LEFT JOIN users u ON p.landlord_id = u.id
                          LEFT JOIN users pm ON p.manager_id = pm.id
                          WHERE p.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * The BIG button: Approving a property so it goes live!
     * If it's a rental, it becomes 'available' for booking immediately.
     */
    public function approveProperty($id)
    {
        // First, check what type of property this is
        $this->db->query("SELECT listing_type FROM properties WHERE id = :id");
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        $listingType = $row ? $row->listing_type : 'rent';

        if ($listingType === 'maintenance') {
            // Maintenance properties don't need a status change, just approval
            $this->db->query("UPDATE properties SET approval_status = 'approved', approved_at = NOW() WHERE id = :id");
        } else {
            // Rental properties should be marked as 'available' when approved
            $this->db->query("UPDATE properties SET approval_status = 'approved', status = 'available', approved_at = NOW() WHERE id = :id");
        }
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Reject a property listing
    public function rejectProperty($id)
    {
        $this->db->query("UPDATE properties 
                          SET approval_status = 'rejected',
                              approved_at = NULL
                          WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Delete a property completely from the database
    public function deleteProperty($id)
    {
        $this->db->query("DELETE FROM properties WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Helper: Get a list of "Vetted" managers so an admin can assign them to houses.
     */
    public function getApprovedPropertyManagers()
    {
        $this->db->query("SELECT u.id, u.name, u.email, pm.phone
                          FROM users u
                          INNER JOIN property_manager pm ON u.id = pm.user_id
                          WHERE u.user_type = 'property_manager' 
                          AND pm.approval_status = 'approved'
                          ORDER BY u.name ASC");
        $result = $this->db->resultSet();
        return is_array($result) ? $result : [];
    }

    // Assign a property to a property manager
    public function assignPropertyToManager($property_id, $manager_id)
    {
        $this->db->query("UPDATE properties 
                          SET manager_id = :manager_id
                          WHERE id = :property_id");
        $this->db->bind(':property_id', $property_id);
        $this->db->bind(':manager_id', $manager_id);
        return $this->db->execute();
    }

    // Remove property manager assignment from a property
    public function unassignProperty($property_id)
    {
        $this->db->query("UPDATE properties 
                          SET manager_id = NULL
                          WHERE id = :property_id");
        $this->db->bind(':property_id', $property_id);
        return $this->db->execute();
    }
}
