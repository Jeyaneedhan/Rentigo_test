/**
 * M_TenantProperties Model
 * This is the "House Hunting" logic for tenants.
 * It strictly only shows properties that have been vetted and approved by admins.
 */
class M_TenantProperties
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    /**
     * Get the main list of houses for the marketplace.
     * We don't show pending or rejected properties here!
     */
    public function getApprovedProperties()
    {
        $this->db->query(
            "SELECT *
             FROM properties
             WHERE approval_status = 'approved'
               AND status = 'available'
             ORDER BY created_at DESC"
        );
        return $this->db->resultSet();
    }

    /**
     * Get full details for one house—if you're thinking about booking it.
     */
    public function getPropertyById($id)
    {
        $this->db->query(
            "SELECT *
             FROM properties
             WHERE id = :id
               AND approval_status = 'approved'
             LIMIT 1"
        );
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * FILTERS: Help the tenant find exactly what they want.
     * (e.g., "An apartment in Colombo for under 50k")
     */
    public function searchProperties($filters)
    {
        $sql = "SELECT * FROM properties WHERE approval_status = 'approved' AND status = 'available'";
        $params = [];

        if (!empty($filters['location'])) {
            $sql .= " AND address LIKE :location";
            $params[':location'] = '%' . $filters['location'] . '%';
        }
        if (!empty($filters['min_price'])) {
            $sql .= " AND rent >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND rent <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        if (!empty($filters['type'])) {
            $sql .= " AND property_type = :type";
            $params[':type'] = $filters['type'];
        }
        $sql .= " ORDER BY created_at DESC";

        $this->db->query($sql);
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }

        return $this->db->resultSet();
    }
}
