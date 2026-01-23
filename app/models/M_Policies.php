/**
 * M_Policies Model
 * This manages the "Boring Legal Stuff"—Terms of Service, Privacy Policies, etc.
 * It lets Admins update the legal text of the platform without touching the code.
 */
class M_Policies
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Fetch all the different legal documents we have.
     * We can filter by status (like 'active' or 'draft') or category.
     */
    public function getAllPolicies($filters = [])
    {
        $sql = "SELECT p.*, u.name as created_by_name 
                FROM policies p 
                LEFT JOIN users u ON p.created_by = u.id";

        $conditions = [];
        $params = [];

        // Apply filters
        if (!empty($filters['status'])) {
            $conditions[] = "p.policy_status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['category'])) {
            $conditions[] = "p.policy_category = :category";
            $params[':category'] = $filters['category'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(p.policy_name LIKE :search OR p.policy_description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY p.created_at DESC";

        $this->db->query($sql);

        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }

        return $this->db->resultSet();
    }

    // Get policy by ID
    public function getPolicyById($policyId)
    {
        $this->db->query("SELECT p.*, u.name as created_by_name 
                         FROM policies p 
                         LEFT JOIN users u ON p.created_by = u.id 
                         WHERE p.policy_id = :policy_id");
        $this->db->bind(':policy_id', $policyId);
        return $this->db->single();
    }

    /**
     * Create a new legal document draft.
     */
    public function createPolicy($data)
    {
        $this->db->query("INSERT INTO policies (
            policy_name,
            policy_category,
            policy_description,
            policy_content,
            policy_version,
            policy_status,
            policy_type,
            effective_date,
            expiry_date,
            created_by
        ) VALUES (
            :policy_name,
            :policy_category,
            :policy_description,
            :policy_content,
            :policy_version,
            :policy_status,
            :policy_type,
            :effective_date,
            :expiry_date,
            :created_by
        )");

        // Bind values
        $this->db->bind(':policy_name', $data['policy_name']);
        $this->db->bind(':policy_category', $data['policy_category']);
        $this->db->bind(':policy_description', $data['policy_description'] ?? '');
        $this->db->bind(':policy_content', $data['policy_content']);
        $this->db->bind(':policy_version', $data['policy_version'] ?? '1.0');
        $this->db->bind(':policy_status', $data['policy_status'] ?? 'draft');
        $this->db->bind(':policy_type', $data['policy_type'] ?? 'standard');
        $this->db->bind(':effective_date', $data['effective_date'] ?? date('Y-m-d'));
        $this->db->bind(':expiry_date', !empty($data['expiry_date']) ? $data['expiry_date'] : null);
        $this->db->bind(':created_by', $data['created_by']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Update policy
    public function updatePolicy($data)
    {
        $this->db->query("UPDATE policies
                         SET policy_name = :policy_name,
                             policy_category = :policy_category,
                             policy_description = :policy_description,
                             policy_content = :policy_content,
                             policy_version = :policy_version,
                             policy_status = :policy_status,
                             policy_type = :policy_type,
                             effective_date = :effective_date,
                             expiry_date = :expiry_date
                         WHERE policy_id = :policy_id");

        // Bind values - policy_id comes from $data array
        $this->db->bind(':policy_id', $data['policy_id']);
        $this->db->bind(':policy_name', $data['policy_name']);
        $this->db->bind(':policy_category', $data['policy_category']);
        $this->db->bind(':policy_description', $data['policy_description'] ?? '');
        $this->db->bind(':policy_content', $data['policy_content']);
        $this->db->bind(':policy_version', $data['policy_version']);
        $this->db->bind(':policy_status', $data['policy_status']);
        $this->db->bind(':policy_type', $data['policy_type'] ?? 'standard');
        $this->db->bind(':effective_date', $data['effective_date']);
        $this->db->bind(':expiry_date', !empty($data['expiry_date']) ? $data['expiry_date'] : null);

        return $this->db->execute();
    }

    // Update policy status
    public function updatePolicyStatus($policyId, $status)
    {
        $this->db->query("UPDATE policies SET
            policy_status = :status
        WHERE policy_id = :policy_id");

        $this->db->bind(':policy_id', $policyId);
        $this->db->bind(':status', $status);

        return $this->db->execute();
    }

    // Delete policy
    public function deletePolicy($policyId)
    {
        $this->db->query("DELETE FROM policies WHERE policy_id = :policy_id");
        $this->db->bind(':policy_id', $policyId);
        return $this->db->execute();
    }

    /**
     * Get a quick summary of how many active documents we have.
     */
    public function getPolicyStats()
    {
        // Total policies
        $this->db->query("SELECT COUNT(*) as total FROM policies");
        $total = $this->db->single()->total;

        // Active policies
        $this->db->query("SELECT COUNT(*) as active FROM policies WHERE policy_status = 'active'");
        $active = $this->db->single()->active;

        // Draft policies
        $this->db->query("SELECT COUNT(*) as draft FROM policies WHERE policy_status = 'draft'");
        $draft = $this->db->single()->draft;

        // Archived policies
        $this->db->query("SELECT COUNT(*) as archived FROM policies WHERE policy_status = 'archived'");
        $archived = $this->db->single()->archived;

        // Under review policies
        $this->db->query("SELECT COUNT(*) as under_review FROM policies WHERE policy_status = 'under_review'");
        $underReview = $this->db->single()->under_review;

        // Latest update
        $this->db->query("SELECT MAX(last_updated) as last_updated FROM policies");
        $lastUpdated = $this->db->single()->last_updated;

        return [
            'total' => $total,
            'active' => $active,
            'draft' => $draft,
            'archived' => $archived,
            'under_review' => $underReview,
            'last_updated' => $lastUpdated
        ];
    }

    // Get policies by category
    public function getPoliciesByCategory($category)
    {
        $this->db->query("SELECT * FROM policies WHERE policy_category = :category ORDER BY policy_name ASC");
        $this->db->bind(':category', $category);
        return $this->db->resultSet();
    }

    // Get active policies only
    public function getActivePolicies()
    {
        $this->db->query("SELECT * FROM policies WHERE policy_status = 'active' ORDER BY policy_name ASC");
        return $this->db->resultSet();
    }

    /**
     * Public View: Get the most recent active version of a policy (e.g., for the footer links).
     */
    public function getLatestActivePolicyByCategory($category)
    {
        $this->db->query("SELECT * FROM policies
                         WHERE policy_category = :category
                         AND policy_status = 'active'
                         ORDER BY effective_date DESC, last_updated DESC
                         LIMIT 1");
        $this->db->bind(':category', $category);
        return $this->db->single();
    }

    // Check if policy name exists (for validation)
    public function policyNameExists($policyName, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM policies WHERE policy_name = :policy_name";
        $params = [':policy_name' => $policyName];

        if ($excludeId) {
            $sql .= " AND policy_id != :policy_id";
            $params[':policy_id'] = $excludeId;
        }

        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }

        $result = $this->db->single();
        return $result->count > 0;
    }

    // Search policies
    public function searchPolicies($searchTerm)
    {
        $this->db->query("SELECT p.*, u.name as created_by_name
                         FROM policies p
                         LEFT JOIN users u ON p.created_by = u.id
                         WHERE p.policy_name LIKE :search
                            OR p.policy_content LIKE :search
                         ORDER BY p.last_updated DESC");

        $this->db->bind(':search', '%' . $searchTerm . '%');
        return $this->db->resultSet();
    }

    // Get policy categories (matches database enum)
    public function getPolicyCategories()
    {
        return [
            'privacy' => 'Privacy Policy',
            'terms_of_service' => 'Terms of Service',
            'refund' => 'Refund Policy',
            'security' => 'Security Policy',
            'data_protection' => 'Data Protection',
            'general' => 'General Policy'
        ];
    }

    // Get policy types (not used in database but kept for compatibility)
    public function getPolicyTypes()
    {
        return [
            'standard' => 'Standard',
            'custom' => 'Custom'
        ];
    }

    // Get policy statuses (matches database enum)
    public function getPolicyStatuses()
    {
        return [
            'draft' => 'Draft',
            'active' => 'Active',
            'archived' => 'Archived',
            'under_review' => 'Under Review'
        ];
    }
}
