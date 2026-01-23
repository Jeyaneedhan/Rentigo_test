<?php
/**
 * M_Users Model
 * This is the ultimate "User Manager" for Rentigo. 
 * It handles everyone: Tenants, Landlords, Admins, and Property Managers.
 */
class M_Users
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // ==========================================
    // REGISTRATION METHODS
    // ==========================================

    /**
     * Create a basic account (Tenant or Landlord)
     * These users can usually log in and start using the site right away.
     */
    public function register($data)
    {
        $this->db->query('INSERT INTO users (name, email, password, user_type, account_status, terms_accepted_at, terms_version) 
                          VALUES (:name, :email, :password, :user_type, :account_status, :terms_accepted_at, :terms_version)');

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']); // Hashed and salted version!
        $this->db->bind(':user_type', $data['user_type']);
        $this->db->bind(':account_status', 'active'); // For regular users, we activate them immediately
        $this->db->bind(':terms_accepted_at', $data['accept_terms'] ? date('Y-m-d H:i:s') : null);
        $this->db->bind(':terms_version', '1.0');

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Property Managers (PMs) are a bit special.
     * They need to upload their job ID/documents, and an Admin has to 
     * manualy "approve" them before they can start managed properties.
     */
    public function registerPM($data)
    {
        // Step 1: Insert into the generic 'users' table
        $this->db->query('INSERT INTO users (name, email, password, user_type, account_status, terms_accepted_at, terms_version) 
                          VALUES (:name, :email, :password, :user_type, :account_status, :terms_accepted_at, :terms_version)');

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':user_type', 'property_manager');
        $this->db->bind(':account_status', 'pending'); // PMs need admin approval before they can log in
        $this->db->bind(':terms_accepted_at', $data['accept_terms'] ? date('Y-m-d H:i:s') : null);
        $this->db->bind(':terms_version', '1.0');

        if (!$this->db->execute()) {
            return false;
        }

        // We need the ID of the user we just created to link the tables together
        $user_id = $this->db->lastInsertId();

        // Step 2: Insert PM-specific info (like their uploaded ID document)
        $this->db->query('INSERT INTO property_manager (user_id, employee_id_document, employee_id_filename, 
                          employee_id_filetype, employee_id_filesize, approval_status) 
                          VALUES (:user_id, :employee_id_document, :employee_id_filename, 
                          :employee_id_filetype, :employee_id_filesize, :approval_status)');

        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':employee_id_document', $data['employee_id_data']); // Storing the file locally in the DB as a BLOB
        $this->db->bind(':employee_id_filename', $data['employee_id_filename']);
        $this->db->bind(':employee_id_filetype', $data['employee_id_filetype']);
        $this->db->bind(':employee_id_filesize', $data['employee_id_filesize']);
        $this->db->bind(':approval_status', 'pending');

        if ($this->db->execute()) {
            return true;
        } else {
            // Note: If this fails, we effectively have an "orphaned" user in the users table.
            // In a pro system, we'd use a transaction here!
            return false;
        }
    }

    // ==========================================
    // LOGIN & AUTHENTICATION
    // ==========================================

    /**
     * Check if an email is already in the database
     * Very useful for registration validation.
     */
    public function findUserByEmail($email)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Check rows
        if ($this->db->rowcount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * The Big Login: Checks email and password.
     * For PMs, it also checks if their account is actually active/approved yet.
     */
    public function login($email, $password)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if (!$row) {
            return false; // Email doesn't exist
        }

        $hashed_password = $row->password;

        if (password_verify($password, $hashed_password)) {
            // If it's a PM, check if they are actually allowed to log in yet
            if ($row->user_type === 'property_manager') {
                if ($row->account_status === 'pending') {
                    return 'pending'; // Tell the controller they're still waiting for approval
                }
                if ($row->account_status === 'rejected') {
                    return 'rejected';
                }
                if ($row->account_status === 'suspended') {
                    return 'suspended';
                }
            }
            return $row; // Success!
        } else {
            return false; // Password mismatch
        }
    }

    // ==========================================
    // USER PROFILE METHODS
    // ==========================================

    /**
     * Get a user's full details by their ID
     */
    public function getUserById($id)
    {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Update basic user info (name/email) and optionally their password
     */
    public function updateUser($data)
    {
        if (isset($data['password'])) {
            $this->db->query('UPDATE users 
                              SET name = :name, email = :email, password = :password 
                              WHERE id = :id');
            $this->db->bind(':password', $data['password']);
        } else {
            $this->db->query('UPDATE users 
                              SET name = :name, email = :email 
                              WHERE id = :id');
        }

        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // ==========================================
    // PROPERTY MANAGER SPECIFIC METHODS
    // ==========================================

    /**
     * Get a list of PMs who are currently waiting for admin approval
     */
    public function getPendingPMs()
    {
        $this->db->query('SELECT u.id, u.name, u.email, u.created_at,
                                 pm.approval_status
                          FROM users u
                          INNER JOIN property_manager pm ON u.id = pm.user_id
                          WHERE u.user_type = :user_type AND pm.approval_status = :status
                          ORDER BY u.created_at DESC');

        $this->db->bind(':user_type', 'property_manager');
        $this->db->bind(':status', 'pending');

        return $this->db->resultSet();
    }

    /**
     * Get every single property manager in the system, sorted by their status
     * We want to see pending ones at the top so we don't forget them!
     */
    public function getAllPropertyManagers()
    {
        $this->db->query('SELECT u.id, u.name, u.email, u.account_status, u.created_at,
                                 pm.approval_status, pm.phone, pm.approved_at, pm.employee_id_filename
                          FROM users u
                          INNER JOIN property_manager pm ON u.id = pm.user_id
                          WHERE u.user_type = :user_type
                          ORDER BY
                              CASE
                                  WHEN pm.approval_status = "pending" THEN 1
                                  WHEN pm.approval_status = "approved" THEN 2
                                  WHEN pm.approval_status = "rejected" THEN 3
                                  ELSE 4
                              END,
                              u.created_at DESC');

        $this->db->bind(':user_type', 'property_manager');

        return $this->db->resultSet();
    }

    /**
     * Get specific managers based on status (e.g., 'approved' only)
     */
    public function getManagersByStatus($status)
    {
        $this->db->query('SELECT u.id, u.name, u.email, u.created_at,
                                 pm.employee_id_filename, pm.phone
                          FROM users u
                          INNER JOIN property_manager pm ON u.id = pm.user_id
                          WHERE u.user_type = :user_type AND pm.approval_status = :status 
                          ORDER BY u.created_at DESC');

        $this->db->bind(':user_type', 'property_manager');
        $this->db->bind(':status', $status);

        return $this->db->resultSet();
    }

    /**
     * Useful for sending announcements/notifications to all users of a certain type
     */
    public function getAllUsersByType($user_type)
    {
        $this->db->query('SELECT id, name, email FROM users WHERE user_type = :user_type AND account_status = :status');
        $this->db->bind(':user_type', $user_type);
        $this->db->bind(':status', 'active');
        return $this->db->resultSet();
    }

    /**
     * Get breakdown of PM counts for the admin dashboard
     */
    public function getManagerCounts()
    {
        $this->db->query('SELECT
                              COUNT(*) as total,
                              SUM(CASE WHEN pm.approval_status = "pending" THEN 1 ELSE 0 END) as pending,
                              SUM(CASE WHEN pm.approval_status = "approved" THEN 1 ELSE 0 END) as approved,
                              SUM(CASE WHEN pm.approval_status = "rejected" THEN 1 ELSE 0 END) as rejected
                          FROM users u
                          INNER JOIN property_manager pm ON u.id = pm.user_id
                          WHERE u.user_type = :user_type');

        $this->db->bind(':user_type', 'property_manager');

        return $this->db->single();
    }

    /**
     * Retrieve the binary file data for a PM's uploaded ID
     */
    public function getEmployeeIdDocument($userId)
    {
        $this->db->query('SELECT employee_id_document, employee_id_filename, employee_id_filetype 
                          FROM property_manager 
                          WHERE user_id = :user_id');

        $this->db->bind(':user_id', $userId);

        return $this->db->single();
    }

    /**
     * The Admin "Greenlight" for a PM.
     * We have to flip switches in two different tables to make them fully active.
     */
    public function approvePM($userId)
    {
        // 1. Activate their user account
        $this->db->query('UPDATE users 
                          SET account_status = :status 
                          WHERE id = :id AND user_type = :user_type');

        $this->db->bind(':status', 'active');
        $this->db->bind(':id', $userId);
        $this->db->bind(':user_type', 'property_manager');

        if (!$this->db->execute()) {
            return false;
        }

        // 2. Mark their PM record as approved
        $this->db->query('UPDATE property_manager 
                          SET approval_status = :approval_status, approved_at = NOW() 
                          WHERE user_id = :user_id');

        $this->db->bind(':approval_status', 'approved');
        $this->db->bind(':user_id', $userId);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Reject a PM account
     */
    public function rejectPM($userId, $adminId)
    {
        // Mark user as rejected
        $this->db->query('UPDATE users 
                          SET account_status = :status 
                          WHERE id = :id');

        $this->db->bind(':status', 'rejected');
        $this->db->bind(':id', $userId);

        if (!$this->db->execute()) {
            return false;
        }

        // Update PM record with rejection info
        $this->db->query('UPDATE property_manager 
                          SET approval_status = :approval_status, approved_by = :admin_id, approved_at = NOW() 
                          WHERE user_id = :user_id');

        $this->db->bind(':approval_status', 'rejected');
        $this->db->bind(':admin_id', $adminId);
        $this->db->bind(':user_id', $userId);

        return $this->db->execute();
    }

    /**
     * Delete a PM entirely
     * The DB should handle cleaning up the property_manager table if FK constraints are set to CASCADE
     */
    public function removePropertyManager($userId)
    {
        $this->db->query('DELETE FROM users 
                          WHERE id = :id AND user_type = :user_type');

        $this->db->bind(':id', $userId);
        $this->db->bind(':user_type', 'property_manager');

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Self-explanatory!
     */
    public function updatePMPhone($userId, $phone)
    {
        $this->db->query('UPDATE property_manager 
                          SET phone = :phone 
                          WHERE user_id = :user_id');

        $this->db->bind(':user_id', $userId);
        $this->db->bind(':phone', $phone);

        return $this->db->execute();
    }
}
