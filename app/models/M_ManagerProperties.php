<?php

/**
 * M_ManagerProperties Model
 * This model manages the relationship between properties and Property Managers.
 * It helps PMs see exactly which houses they are responsible for looking after.
 */
class M_ManagerProperties
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    /**
     * Get the full portfolio of houses assigned to a specific manager.
     */
    public function getAssignedProperties($manager_id)
    {
        $this->db->query(
            "SELECT p.*, u.name AS owner_name
             FROM properties p
             JOIN users u ON p.landlord_id = u.id
             WHERE p.manager_id = :manager_id
             ORDER BY p.id DESC"
        );
        $this->db->bind(':manager_id', $manager_id);
        return $this->db->resultSet();
    }

    /**
     * Fetch details for a house, but only if this manager has permission to see it.
     */
    public function getPropertyById($id, $manager_id)
    {
        $this->db->query(
            "SELECT p.*
             FROM properties p
             WHERE p.id = :id
               AND p.manager_id = :manager_id
             LIMIT 1"
        );
        $this->db->bind(':id', $id);
        $this->db->bind(':manager_id', $manager_id);
        return $this->db->single();
    }

    // Get the manager assigned to a property
    public function getManagerByProperty($property_id)
    {
        $this->db->query(
            "SELECT u.id as manager_id, u.name, u.email
             FROM properties p
             JOIN users u ON p.manager_id = u.id
             WHERE p.id = :property_id
               AND p.manager_id IS NOT NULL
             LIMIT 1"
        );
        $this->db->bind(':property_id', $property_id);
        return $this->db->single();
    }

    /**
     * Get property statistics for a manager by period
     * @param int $manager_id The manager's user ID
     * @param string $period 'all', 'month', or 'year' for date filtering
     */
    public function getPropertyStats($manager_id, $period = 'all')
    {
        $dateFilter = getDateRangeByPeriod('p.created_at', $period);
        // Count total properties and occupied properties (those with active bookings)
        $this->db->query(
            "SELECT 
                COUNT(DISTINCT p.id) as total_properties,
                COUNT(DISTINCT CASE WHEN b.status IN ('active', 'approved') THEN p.id END) as occupied_units
             FROM properties p
             LEFT JOIN bookings b ON p.id = b.property_id AND b.status IN ('active', 'approved')
             WHERE p.manager_id = :manager_id AND " . $dateFilter
        );
        $this->db->bind(':manager_id', $manager_id);
        $result = $this->db->single();
        // Set total_units to same as total_properties for consistency
        $result->total_units = $result->total_properties;
        return $result;
    }
}
