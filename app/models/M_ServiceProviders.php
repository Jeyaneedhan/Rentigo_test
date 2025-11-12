<?php
class M_ServiceProviders
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // CREATE - Add new service provider
    public function create($data)
    {
        $this->db->query('INSERT INTO service_providers (name, company, specialty, phone, email, address, rating, status) 
                         VALUES (:name, :company, :specialty, :phone, :email, :address, :rating, :status)');

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':company', $data['company']);
        $this->db->bind(':specialty', $data['specialty']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':status', $data['status']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // READ - Get all service providers
    public function getAllProviders()
    {
        $this->db->query('SELECT * FROM service_providers ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    // READ - Get service provider by ID
    public function getProviderById($id)
    {
        $this->db->query('SELECT * FROM service_providers WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // READ - Get providers by specialty
    public function getProvidersBySpecialty($specialty)
    {
        $this->db->query('SELECT * FROM service_providers WHERE specialty = :specialty AND status = "active"');
        $this->db->bind(':specialty', $specialty);
        return $this->db->resultSet();
    }

    // READ - Get active providers only
    public function getActiveProviders()
    {
        $this->db->query('SELECT * FROM service_providers WHERE status = "active" ORDER BY name ASC');
        return $this->db->resultSet();
    }

    // UPDATE - Edit service provider
    public function update($data)
    {
        $this->db->query('UPDATE service_providers 
                         SET name = :name, company = :company, specialty = :specialty, 
                             phone = :phone, email = :email, address = :address, 
                             rating = :rating, status = :status
                         WHERE id = :id');

        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':company', $data['company']);
        $this->db->bind(':specialty', $data['specialty']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':status', $data['status']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // DELETE - Remove service provider
    public function delete($id)
    {
        $this->db->query('DELETE FROM service_providers WHERE id = :id');
        $this->db->bind(':id', $id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // UTILITY - Update provider status
    public function updateStatus($id, $status)
    {
        $this->db->query('UPDATE service_providers SET status = :status WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // UTILITY - Search providers
    public function searchProviders($searchTerm, $specialty = '', $status = '')
    {
        $query = 'SELECT * FROM service_providers WHERE 1=1';

        if (!empty($searchTerm)) {
            $query .= ' AND (name LIKE :search OR company LIKE :search OR email LIKE :search)';
        }

        if (!empty($specialty)) {
            $query .= ' AND specialty = :specialty';
        }

        if (!empty($status)) {
            $query .= ' AND status = :status';
        }

        $query .= ' ORDER BY created_at DESC';

        $this->db->query($query);

        if (!empty($searchTerm)) {
            $this->db->bind(':search', '%' . $searchTerm . '%');
        }

        if (!empty($specialty)) {
            $this->db->bind(':specialty', $specialty);
        }

        if (!empty($status)) {
            $this->db->bind(':status', $status);
        }

        return $this->db->resultSet();
    }

    // UTILITY - Get provider count by status and calculate average rating
    public function getProviderCounts()
    {
        $this->db->query('SELECT 
                            COUNT(*) as total,
                            SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active,
                            SUM(CASE WHEN status = "inactive" THEN 1 ELSE 0 END) as inactive,
                            ROUND(AVG(CASE WHEN rating > 0 THEN rating ELSE NULL END), 1) as average_rating,
                            COUNT(CASE WHEN rating > 0 THEN 1 ELSE NULL END) as rated_providers
                         FROM service_providers');
        return $this->db->single();
    }

    // ✅ VALIDATION - Check if email exists
    /**
     * Check if email already exists in database
     * @param string $email - Email to check
     * @param int|null $excludeId - Provider ID to exclude (for edit validation)
     * @return bool - Returns true if email exists, false otherwise
     */
    public function emailExists($email, $excludeId = null)
    {
        if ($excludeId) {
            // For edit - exclude current provider's ID
            $this->db->query('SELECT id FROM service_providers WHERE email = :email AND id != :id');
            $this->db->bind(':email', $email);
            $this->db->bind(':id', $excludeId);
        } else {
            // For add - check if email exists
            $this->db->query('SELECT id FROM service_providers WHERE email = :email');
            $this->db->bind(':email', $email);
        }

        $row = $this->db->single();

        // Returns true if email exists, false otherwise
        return $row ? true : false;
    }

    // ✅ VALIDATION - Check if phone exists
    /**
     * Check if phone number already exists in database
     * @param string $phone - Phone number to check
     * @param int|null $excludeId - Provider ID to exclude (for edit validation)
     * @return bool - Returns true if phone exists, false otherwise
     */
    public function phoneExists($phone, $excludeId = null)
    {
        if ($excludeId) {
            // For edit - exclude current provider's ID
            $this->db->query('SELECT id FROM service_providers WHERE phone = :phone AND id != :id');
            $this->db->bind(':phone', $phone);
            $this->db->bind(':id', $excludeId);
        } else {
            // For add - check if phone exists
            $this->db->query('SELECT id FROM service_providers WHERE phone = :phone');
            $this->db->bind(':phone', $phone);
        }

        $row = $this->db->single();

        // Returns true if phone exists, false otherwise
        return $row ? true : false;
    }

    // ✅ VALIDATION - Check if company name exists
    /**
     * Check if company name already exists in database
     * @param string $company - Company name to check
     * @param int|null $excludeId - Provider ID to exclude (for edit validation)
     * @return bool - Returns true if company exists, false otherwise
     */
    public function companyExists($company, $excludeId = null)
    {
        if ($excludeId) {
            // For edit - exclude current provider's ID
            $this->db->query('SELECT id FROM service_providers WHERE company = :company AND id != :id');
            $this->db->bind(':company', $company);
            $this->db->bind(':id', $excludeId);
        } else {
            // For add - check if company exists
            $this->db->query('SELECT id FROM service_providers WHERE company = :company');
            $this->db->bind(':company', $company);
        }

        $row = $this->db->single();

        // Returns true if company exists, false otherwise
        return $row ? true : false;
    }
}
