<?php
class M_Inspection
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    // Get properties that have maintenance issues
    public function getPropertiesWithIssues()
    {
        $this->db->query("
        SELECT 
            p.id,
            p.address,
            COUNT(i.id) as issue_count
        FROM properties p
        INNER JOIN issues i ON p.id = i.property_id
        WHERE i.status IN ('pending', 'in_progress', 'assigned')
        GROUP BY p.id, p.address
        ORDER BY p.address ASC
    ");

        return $this->db->resultSet();
    }

    // Get issues by property ID
    public function getIssuesByPropertyId($property_id)
    {
        $this->db->query("
        SELECT 
            i.id,
            i.title,
            i.description,
            i.category,
            i.priority,
            i.status,
            i.created_at,
            u.name as tenant_name
        FROM issues i
        LEFT JOIN users u ON i.tenant_id = u.id
        WHERE i.property_id = :property_id
        AND i.status IN ('pending', 'in_progress', 'assigned')
        ORDER BY 
            FIELD(i.priority, 'emergency', 'high', 'medium', 'low'),
            i.created_at DESC
    ");

        $this->db->bind(':property_id', $property_id);

        return $this->db->resultSet();
    }

    // Get property by ID
    public function getPropertyById($id)
    {
        $this->db->query("SELECT * FROM properties WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Insert inspection
    public function addInspection($data)
    {
        try {
            // Insert inspection - inspector is required in DB, so make sure it's passed in controller
            $this->db->query("INSERT INTO inspections (property, `type`, issues, scheduled_date, status) 
                              VALUES (:property, :type, :issues, :date, 'scheduled')");

            // Bind parameters
            $this->db->bind(':property', $data['property']);
            $this->db->bind(':type', $data['type']);
            $this->db->bind(':issues', ($data['issues'] === '' ? 0 : (int)$data['issues']));
            $this->db->bind(':date', $data['date']);

            return $this->db->execute();
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Fetch all inspections
    public function getInspections()
    {
        $this->db->query("SELECT * FROM inspections ORDER BY scheduled_date DESC");
        return $this->db->resultSet();
    }

    // Fetch inspection by ID
    public function getInspectionById($id)
    {
        $this->db->query("SELECT * FROM inspections WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Update inspection
    public function updateInspection($id, $data)
    {
        $this->db->query("UPDATE inspections 
                          SET property = :property, 
                              `type` = :type, 
                              scheduled_date = :date, 
                              status = :status, 
                              issues = :issues 
                          WHERE id = :id");

        $this->db->bind(':property', $data['property']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':issues', ($data['issues'] === '' ? 0 : (int)$data['issues']));
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    // Delete inspection
    public function deleteInspection($id)
    {
        $this->db->query("DELETE FROM inspections WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
