<?php
require_once '../app/helpers/helper.php';

class Policies extends Controller
{
    private $policyModel;

    public function __construct()
    {
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
            redirect('users/login');
        }
        $this->policyModel = $this->model('M_Policies');
    }

    // READ - Policies page
    public function index()
    {
        $searchTerm = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $status = $_GET['status'] ?? '';

        if (!empty($searchTerm) || !empty($category) || !empty($status)) {
            $policies = $this->policyModel->searchPolicies($searchTerm, $category, $status);
        } else {
            $policies = $this->policyModel->getAllPolicies();
        }

        // Get policy statistics
        $stats = $this->policyModel->getPolicyStats();

        $data = [
            'title' => 'Policy Management - Rentigo Admin',
            'page' => 'policies',
            'policies' => $policies,
            'stats' => $stats,
            'search' => $searchTerm,
            'category_filter' => $category,
            'status_filter' => $status,
            'categories' => $this->getCategories(),
            'statuses' => $this->getStatuses()
        ];

        $this->view('admin/v_policies', $data);
    }

    // CREATE - Add new policy
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'policy_name' => trim($_POST['policy_name'] ?? ''),
                'policy_category' => trim($_POST['policy_category'] ?? ''),
                'policy_version' => trim($_POST['policy_version'] ?? ''),
                'policy_status' => trim($_POST['policy_status'] ?? ''),
                'policy_type' => trim($_POST['policy_type'] ?? ''),
                'policy_description' => trim($_POST['policy_description'] ?? ''),
                'policy_content' => $_POST['policy_content'] ?? '',
                'effective_date' => trim($_POST['effective_date'] ?? ''),
                'expiry_date' => !empty($_POST['expiry_date']) ? trim($_POST['expiry_date']) : null,
                'created_by' => $_SESSION['user_id']
            ];

            // Create validator
            $validator = new Validator();

            // Validate policy name
            if ($validator->required('policy_name', $data['policy_name'], 'Policy name is required')) {
                $validator->minLength('policy_name', $data['policy_name'], 3, 'Policy name must be at least 3 characters');
                $validator->maxLength('policy_name', $data['policy_name'], 255, 'Policy name must not exceed 255 characters');

                // Check if policy name already exists
                if ($this->policyModel->policyNameExists($data['policy_name'])) {
                    $validator->custom('policy_name', false, 'This policy name already exists');
                }
            }

            // Validate category
            $validCategories = array_keys($this->getCategories());
            if ($validator->required('policy_category', $data['policy_category'], 'Please select a category')) {
                $validator->inArray('policy_category', $data['policy_category'], $validCategories, 'Please select a valid category');
            }

            // Validate version
            if ($validator->required('policy_version', $data['policy_version'], 'Version is required')) {
                $validator->custom(
                    'policy_version',
                    preg_match('/^v\d+\.\d+$/', $data['policy_version']),
                    'Version must be in format v1.0'
                );
            }

            // Validate status
            $validStatuses = array_keys($this->getStatuses());
            if (!empty($data['policy_status'])) {
                $validator->inArray('policy_status', $data['policy_status'], $validStatuses, 'Please select a valid status');
            }

            // Validate type
            $validTypes = array_keys($this->getTypes());
            if (!empty($data['policy_type'])) {
                $validator->inArray('policy_type', $data['policy_type'], $validTypes, 'Please select a valid type');
            }

            // Validate description
            if ($validator->required('policy_description', $data['policy_description'], 'Description is required')) {
                $validator->minLength('policy_description', $data['policy_description'], 10, 'Description must be at least 10 characters');
                $validator->maxLength('policy_description', $data['policy_description'], 1000, 'Description must not exceed 1000 characters');
            }

            // Validate content
            if ($validator->required('policy_content', $data['policy_content'], 'Policy content is required')) {
                // Strip HTML tags for length validation
                $contentText = strip_tags($data['policy_content']);
                $validator->custom(
                    'policy_content',
                    strlen(trim($contentText)) >= 50,
                    'Policy content must be at least 50 characters (excluding HTML)'
                );
            }

            // Validate effective date
            if ($validator->required('effective_date', $data['effective_date'], 'Effective date is required')) {
                $validator->custom(
                    'effective_date',
                    strtotime($data['effective_date']) !== false,
                    'Invalid effective date format'
                );
            }

            // Validate expiry date if provided
            if (!empty($data['expiry_date'])) {
                if ($validator->custom(
                    'expiry_date',
                    strtotime($data['expiry_date']) !== false,
                    'Invalid expiry date format'
                )) {
                    // Check if expiry date is after effective date
                    if (!empty($data['effective_date'])) {
                        $validator->custom(
                            'expiry_date',
                            strtotime($data['expiry_date']) > strtotime($data['effective_date']),
                            'Expiry date must be after effective date'
                        );
                    }
                }
            }

            // Check validation
            if (!$validator->hasErrors()) {
                if ($this->policyModel->createPolicy($data)) {
                    flash('policy_message', 'Policy created successfully!', 'alert alert-success');
                    redirect('policies/index');
                } else {
                    flash('policy_message', 'Failed to create policy. Please try again.', 'alert alert-danger');
                    redirect('policies/add');
                }
            } else {
                // Map errors to data array
                $errors = $validator->getErrors();
                $data['policy_name_err'] = $errors['policy_name'] ?? '';
                $data['policy_category_err'] = $errors['policy_category'] ?? '';
                $data['policy_version_err'] = $errors['policy_version'] ?? '';
                $data['policy_status_err'] = $errors['policy_status'] ?? '';
                $data['policy_type_err'] = $errors['policy_type'] ?? '';
                $data['policy_description_err'] = $errors['policy_description'] ?? '';
                $data['policy_content_err'] = $errors['policy_content'] ?? '';
                $data['effective_date_err'] = $errors['effective_date'] ?? '';
                $data['expiry_date_err'] = $errors['expiry_date'] ?? '';
                $data['title'] = 'Add Policy - Rentigo Admin';
                $data['page'] = 'policies';
                $data['categories'] = $this->getCategories();
                $data['statuses'] = $this->getStatuses();
                $data['types'] = $this->getTypes();

                $this->view('admin/v_add_policy', $data);
            }
        } else {
            // GET request
            $data = [
                'title' => 'Add Policy - Rentigo Admin',
                'page' => 'policies',
                'policy_name' => '',
                'policy_category' => '',
                'policy_version' => 'v1.0',
                'policy_status' => 'draft',
                'policy_type' => 'standard',
                'policy_description' => '',
                'policy_content' => '',
                'effective_date' => date('Y-m-d'),
                'expiry_date' => '',
                'policy_name_err' => '',
                'policy_category_err' => '',
                'policy_version_err' => '',
                'policy_status_err' => '',
                'policy_type_err' => '',
                'policy_description_err' => '',
                'policy_content_err' => '',
                'effective_date_err' => '',
                'expiry_date_err' => '',
                'categories' => $this->getCategories(),
                'statuses' => $this->getStatuses(),
                'types' => $this->getTypes()
            ];
            $this->view('admin/v_add_policy', $data);
        }
    }

    // UPDATE - Edit policy
    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'policy_id' => $id,
                'policy_name' => trim($_POST['policy_name'] ?? ''),
                'policy_category' => trim($_POST['policy_category'] ?? ''),
                'policy_version' => trim($_POST['policy_version'] ?? ''),
                'policy_status' => trim($_POST['policy_status'] ?? ''),
                'policy_type' => trim($_POST['policy_type'] ?? ''),
                'policy_description' => trim($_POST['policy_description'] ?? ''),
                'policy_content' => $_POST['policy_content'] ?? '',
                'effective_date' => trim($_POST['effective_date'] ?? ''),
                'expiry_date' => !empty($_POST['expiry_date']) ? trim($_POST['expiry_date']) : null
            ];

            // Create validator
            $validator = new Validator();

            // Validate policy name
            if ($validator->required('policy_name', $data['policy_name'], 'Policy name is required')) {
                $validator->minLength('policy_name', $data['policy_name'], 3, 'Policy name must be at least 3 characters');
                $validator->maxLength('policy_name', $data['policy_name'], 255, 'Policy name must not exceed 255 characters');

                // Check if policy name exists for other policies (exclude current policy)
                if ($this->policyModel->policyNameExists($data['policy_name'], $id)) {
                    $validator->custom('policy_name', false, 'This policy name is already in use by another policy');
                }
            }

            // Validate category
            $validCategories = array_keys($this->getCategories());
            if ($validator->required('policy_category', $data['policy_category'], 'Please select a category')) {
                $validator->inArray('policy_category', $data['policy_category'], $validCategories, 'Please select a valid category');
            }

            // Validate version
            if ($validator->required('policy_version', $data['policy_version'], 'Version is required')) {
                $validator->custom(
                    'policy_version',
                    preg_match('/^v\d+\.\d+$/', $data['policy_version']),
                    'Version must be in format v1.0'
                );
            }

            // Validate status
            $validStatuses = array_keys($this->getStatuses());
            if (!empty($data['policy_status'])) {
                $validator->inArray('policy_status', $data['policy_status'], $validStatuses, 'Please select a valid status');
            }

            // Validate type
            $validTypes = array_keys($this->getTypes());
            if (!empty($data['policy_type'])) {
                $validator->inArray('policy_type', $data['policy_type'], $validTypes, 'Please select a valid type');
            }

            // Validate description
            if ($validator->required('policy_description', $data['policy_description'], 'Description is required')) {
                $validator->minLength('policy_description', $data['policy_description'], 10, 'Description must be at least 10 characters');
                $validator->maxLength('policy_description', $data['policy_description'], 1000, 'Description must not exceed 1000 characters');
            }

            // Validate content
            if ($validator->required('policy_content', $data['policy_content'], 'Policy content is required')) {
                // Strip HTML tags for length validation
                $contentText = strip_tags($data['policy_content']);
                $validator->custom(
                    'policy_content',
                    strlen(trim($contentText)) >= 50,
                    'Policy content must be at least 50 characters (excluding HTML)'
                );
            }

            // Validate effective date
            if ($validator->required('effective_date', $data['effective_date'], 'Effective date is required')) {
                $validator->custom(
                    'effective_date',
                    strtotime($data['effective_date']) !== false,
                    'Invalid effective date format'
                );
            }

            // Validate expiry date if provided
            if (!empty($data['expiry_date'])) {
                if ($validator->custom(
                    'expiry_date',
                    strtotime($data['expiry_date']) !== false,
                    'Invalid expiry date format'
                )) {
                    // Check if expiry date is after effective date
                    if (!empty($data['effective_date'])) {
                        $validator->custom(
                            'expiry_date',
                            strtotime($data['expiry_date']) > strtotime($data['effective_date']),
                            'Expiry date must be after effective date'
                        );
                    }
                }
            }

            // Check validation
            if (!$validator->hasErrors()) {
                if ($this->policyModel->updatePolicy($data)) {
                    flash('policy_message', 'Policy updated successfully!', 'alert alert-success');
                    redirect('policies/index');
                } else {
                    flash('policy_message', 'Failed to update policy. Please try again.', 'alert alert-danger');
                    redirect('policies/edit/' . $id);
                }
            } else {
                // Map errors to data array
                $errors = $validator->getErrors();
                $data['policy_name_err'] = $errors['policy_name'] ?? '';
                $data['policy_category_err'] = $errors['policy_category'] ?? '';
                $data['policy_version_err'] = $errors['policy_version'] ?? '';
                $data['policy_status_err'] = $errors['policy_status'] ?? '';
                $data['policy_type_err'] = $errors['policy_type'] ?? '';
                $data['policy_description_err'] = $errors['policy_description'] ?? '';
                $data['policy_content_err'] = $errors['policy_content'] ?? '';
                $data['effective_date_err'] = $errors['effective_date'] ?? '';
                $data['expiry_date_err'] = $errors['expiry_date'] ?? '';
                $data['policy'] = $this->policyModel->getPolicyById($id);
                $data['title'] = 'Edit Policy - Rentigo Admin';
                $data['page'] = 'policies';
                $data['categories'] = $this->getCategories();
                $data['statuses'] = $this->getStatuses();
                $data['types'] = $this->getTypes();

                $this->view('admin/v_edit_policy', $data);
            }
        } else {
            // GET request
            $policy = $this->policyModel->getPolicyById($id);

            if (!$policy) {
                flash('policy_message', 'Policy not found!', 'alert alert-danger');
                redirect('policies/index');
            }

            $data = [
                'title' => 'Edit Policy - Rentigo Admin',
                'page' => 'policies',
                'policy' => $policy,
                'policy_name_err' => '',
                'policy_category_err' => '',
                'policy_version_err' => '',
                'policy_status_err' => '',
                'policy_type_err' => '',
                'policy_description_err' => '',
                'policy_content_err' => '',
                'effective_date_err' => '',
                'expiry_date_err' => '',
                'categories' => $this->getCategories(),
                'statuses' => $this->getStatuses(),
                'types' => $this->getTypes()
            ];
            $this->view('admin/v_edit_policy', $data);
        }
    }

    // VIEW - View single policy details
    public function view_policy($id)
    {
        // GET request - Show policy view page
        $policy = $this->policyModel->getPolicyById($id);

        if (!$policy) {
            flash('policy_message', 'Policy not found!', 'alert alert-danger');
            redirect('policies/index');
        }

        $data = [
            'title' => 'View Policy - Rentigo Admin',
            'page' => 'policies',
            'policy' => $policy
        ];

        $this->view('admin/v_view_policy', $data);
    }

    // DELETE - Remove policy
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verify policy exists
            $policy = $this->policyModel->getPolicyById($id);

            if (!$policy) {
                flash('policy_message', 'Policy not found', 'alert alert-danger');
                redirect('policies/index');
                exit();
            }

            if ($this->policyModel->deletePolicy($id)) {
                flash('policy_message', 'Policy deleted successfully', 'alert alert-success');
            } else {
                flash('policy_message', 'Failed to delete policy', 'alert alert-danger');
            }
            redirect('policies/index');
            exit();
        } else {
            redirect('policies/index');
        }
    }

    // UPDATE STATUS - Change policy status
    public function updateStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $status = $_POST['status'] ?? '';

            // Validate status
            $validator = new Validator();
            $validStatuses = array_keys($this->getStatuses());

            if ($validator->required('status', $status, 'Status is required')) {
                $validator->inArray('status', $status, $validStatuses, 'Invalid status value');
            }

            if (!$validator->hasErrors()) {
                // Get policy details before update to check if status changed
                $policy = $this->policyModel->getPolicyById($id);

                if (!$policy) {
                    flash('policy_message', 'Policy not found', 'alert alert-danger');
                    redirect('policies/index');
                    exit();
                }

                $oldStatus = $policy->policy_status;
                $statusChanged = ($oldStatus !== $status);

                // Update policy status
                if ($this->policyModel->updatePolicyStatus($id, $status)) {
                    // Send notifications only if status changed to 'active' or 'inactive'
                    if ($statusChanged && ($status === 'active' || $status === 'inactive')) {
                        $this->sendPolicyStatusNotifications($id, $status, $policy);
                    }

                    flash('policy_message', 'Status updated successfully', 'alert alert-success');
                } else {
                    flash('policy_message', 'Failed to update status', 'alert alert-danger');
                }
            } else {
                $errors = $validator->getErrors();
                flash('policy_message', $errors['status'] ?? 'Invalid status', 'alert alert-danger');
            }
            redirect('policies/index');
            exit();
        } else {
            redirect('policies/index');
        }
    }

    // Send notifications to all non-admin users when policy status changes
    private function sendPolicyStatusNotifications($policyId, $newStatus, $policy)
    {
        // Load required models
        $userModel = $this->model('M_Users');
        $notificationModel = $this->model('M_Notifications');

        // Get all non-admin users (tenant, landlord, property_manager)
        $allUsers = array_merge(
            $userModel->getAllUsersByType('tenant'),
            $userModel->getAllUsersByType('landlord'),
            $userModel->getAllUsersByType('property_manager')
        );

        // Determine action text
        $action = ($newStatus === 'active') ? 'activated' : 'deactivated';
        $actionCapitalized = ($newStatus === 'active') ? 'Activated' : 'Deactivated';

        // Get current date and time
        $dateTime = date('F j, Y \a\t g:i A');

        // Get admin name who made the change
        $adminName = $_SESSION['user_name'] ?? 'Administrator';

        // Create notification message
        $notificationMessage = sprintf(
            'The policy "%s" has been %s on %s by %s.',
            $policy->policy_name,
            $action,
            $dateTime,
            $adminName
        );

        // Send notification to each user
        $sentCount = 0;
        foreach ($allUsers as $user) {
            $notificationModel->createNotification([
                'user_id' => $user->id,
                'type' => 'policy_update',
                'title' => 'Policy ' . $actionCapitalized,
                'message' => $notificationMessage,
                'link' => ''
            ]);
            $sentCount++;
        }

        // Log notification count (optional - for debugging)
        // You can remove this or add it to a log file
        return $sentCount;
    }
    // Helper methods to get dropdown options
    private function getCategories()
    {
        return [
            'rental' => 'Rental',
            'security' => 'Security',
            'maintenance' => 'Maintenance',
            'financial' => 'Financial',
            'general' => 'General',
            'privacy' => 'Privacy',
            'terms_of_service' => 'Terms of Service',
            'refund' => 'Refund',
            'data_protection' => 'Data Protection'
        ];
    }

    private function getStatuses()
    {
        return [
            'draft' => 'Draft',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'archived' => 'Archived',
            'under_review' => 'Under Review'
        ];
    }

    private function getTypes()
    {
        return [
            'standard' => 'Standard',
            'custom' => 'Custom'
        ];
    }

    // AJAX - Get policy statistics
    public function getStats()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            header('Content-Type: application/json');

            $stats = $this->policyModel->getPolicyStats();

            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
            exit();
        } else {
            redirect('policies/index');
        }
    }
}
