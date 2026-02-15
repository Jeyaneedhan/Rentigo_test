<?php
require_once '../app/helpers/helper.php';

class Issues extends Controller
{
    private $issueModel;

    public function __construct()
    {
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'tenant') {
            redirect('users/login');
        }

        $this->issueModel = $this->model('M_Issue');
    }

    public function report()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'tenant_id' => $_SESSION['user_id'],
                'property_id' => trim($_POST['property_id']),
                'issue_title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'category' => trim($_POST['category']),
                'priority' => trim($_POST['priority']),
                'status' => 'pending'
            ];

            $validator = new Validator();

            $validator->required('property', $data['property_id'], 'Please select a property');

            if ($validator->required('title', $data['issue_title'])) {
                $validator->minLength('title', $data['issue_title'], 5);
                $validator->maxLength('title', $data['issue_title'], 200);
            }

            if ($validator->required('description', $data['description'])) {
                $validator->minLength('description', $data['description'], 10);
                $validator->maxLength('description', $data['description'], 1000);
            }

            $validCategories = ['plumbing', 'electrical', 'hvac', 'appliance', 'structural', 'pest', 'other'];
            if ($validator->required('category', $data['category'], 'Please select a category')) {
                $validator->inArray('category', $data['category'], $validCategories);
            }

            $validPriorities = ['low', 'medium', 'high', 'emergency'];
            if ($validator->required('priority', $data['priority'], 'Please select a priority')) {
                $validator->inArray('priority', $data['priority'], $validPriorities);
            }

            if ($data['priority'] === 'emergency') {
                $validator->custom(
                    'description',
                    strlen($data['description']) >= 50,
                    'Emergency issues require detailed description (at least 50 characters)'
                );
            }

            $criticalCategories = ['electrical', 'structural'];
            if (in_array($data['category'], $criticalCategories) && $data['priority'] === 'low') {
                $validator->custom(
                    'priority',
                    false,
                    ucfirst($data['category']) . ' issues should not be low priority for safety reasons'
                );
            }

            if (!$validator->hasErrors()) {
                // Get property details to find landlord
                $propertyModel = $this->model('M_Properties');
                $property = $propertyModel->getPropertyById($data['property_id']);

                $dbData = [
                    'tenant_id' => $data['tenant_id'],
                    'property_id' => $data['property_id'],
                    'title' => $data['issue_title'],
                    'description' => $data['description'],
                    'category' => $data['category'],
                    'priority' => $data['priority'],
                    'status' => $data['status'],
                    'landlord_id' => $property->landlord_id ?? null
                ];

                if ($issue_id = $this->issueModel->addIssue($dbData)) {
                    // Send notification to PM
                    $managerModel = $this->model('M_ManagerProperties');
                    $manager = $managerModel->getManagerByProperty($data['property_id']);

                    if ($manager) {
                        $notificationModel = $this->model('M_Notifications');
                        $notificationModel->createNotification([
                            'user_id' => $manager->manager_id,
                            'type' => 'issue_reported',
                            'title' => 'New Issue Reported',
                            'message' => 'A tenant has reported a new ' . $data['priority'] . ' priority issue: ' . $data['issue_title'],
                            'link' => 'manager/issueDetails/' . $issue_id
                        ]);
                    }

                    flash('issue_message', 'Issue reported successfully. Property manager has been notified.', 'alert alert-success');
                    redirect('issues/track');
                } else {
                    die('Something went wrong when saving issue');
                }
            } else {
                $errors = $validator->getErrors();
                $data['property_err'] = $errors['property'] ?? '';
                $data['title_err'] = $errors['title'] ?? '';
                $data['description_err'] = $errors['description'] ?? '';
                $data['category_err'] = $errors['category'] ?? '';
                $data['priority_err'] = $errors['priority'] ?? '';

                $data['properties'] = $this->issueModel->getProperties();
                $data['recentIssues'] = $this->issueModel->getRecentIssuesByTenant($_SESSION['user_id'], 2);
                $data['page_title'] = 'Report Issues - TenantHub';
                $data['page'] = 'report_issue';
                $data['user_name'] = $_SESSION['user_name'];

                $this->view('tenant/v_report_issue', $data);
            }
        } else {
            $properties = $this->issueModel->getProperties();
            $recentIssues = $this->issueModel->getRecentIssuesByTenant($_SESSION['user_id'], 2);

            $data = [
                'page_title' => 'Report Issues - TenantHub',
                'page' => 'report_issue',
                'user_name' => $_SESSION['user_name'],
                'properties' => $properties,
                'recentIssues' => $recentIssues,
                'property_id' => '',
                'issue_title' => '',
                'description' => '',
                'category' => '',
                'priority' => '',
                'property_err' => '',
                'title_err' => '',
                'description_err' => '',
                'category_err' => '',
                'priority_err' => ''
            ];

            $this->view('tenant/v_report_issue', $data);
        }
    }

    public function track()
    {
        $tenant_id = $_SESSION['user_id'];
        $issues = $this->issueModel->getIssuesByTenant($tenant_id);

        // Get issue stats using model method with 'all' period
        $issueStats = $this->issueModel->getIssueStats($tenant_id, 'tenant', 'all');

        $data = [
            'page_title' => 'Track Issues - TenantHub',
            'page' => 'track_issues',
            'user_name' => $_SESSION['user_name'],
            'issues' => $issues,
            'issueStats' => $issueStats,
            'pending_issues' => $issueStats->pending_count ?? 0,
            'in_progress_issues' => $issueStats->in_progress_count ?? 0,
            'resolved_issues' => $issueStats->resolved_count ?? 0
        ];

        $this->view('tenant/v_track_issues', $data);
    }

    public function details($id = null)
    {
        if (!$id) {
            flash('issue_message', 'Invalid issue ID', 'alert alert-danger');
            redirect('issues/track');
        }

        // Get issue details
        $issue = $this->issueModel->getIssueById($id);

        if (!$issue) {
            flash('issue_message', 'Issue not found', 'alert alert-danger');
            redirect('issues/track');
        }

        // Verify this issue belongs to the logged-in tenant
        if ($issue->tenant_id != $_SESSION['user_id']) {
            flash('issue_message', 'Unauthorized access', 'alert alert-danger');
            redirect('issues/track');
        }

        // Initialize related data
        $maintenanceRequest = null;
        $quotations = [];
        $serviceProvider = null;
        $statusHistory = [];

        // Get maintenance request if linked
        if (!empty($issue->maintenance_request_id)) {
            $maintenanceModel = $this->model('M_Maintenance');
            $maintenanceRequest = $maintenanceModel->getMaintenanceById($issue->maintenance_request_id);

            // Get quotations if maintenance request exists
            if ($maintenanceRequest) {
                $quotationModel = $this->model('M_MaintenanceQuotations');
                $quotations = $quotationModel->getQuotationsByRequest($issue->maintenance_request_id);

                // Get service provider if assigned
                if (!empty($maintenanceRequest->provider_id)) {
                    $providerModel = $this->model('M_ServiceProviders');
                    $serviceProvider = $providerModel->getProviderById($maintenanceRequest->provider_id);
                }
            }
        }

        // Build status history
        // 1. Initial creation
        $statusHistory[] = [
            'status' => 'pending',
            'status_text' => 'Created',
            'updated_by' => 'Tenant',
            'updated_by_name' => $_SESSION['user_name'] ?? 'You',
            'date_time' => $issue->created_at,
            'notes' => 'Issue reported'
        ];

        // 2. Get status updates from notifications
        $notificationModel = $this->model('M_Notifications');
        $allNotifications = $notificationModel->getNotificationsByUser($_SESSION['user_id']);

        // Filter notifications related to this issue
        foreach ($allNotifications as $notification) {
            if (
                $notification->type === 'issue_update' &&
                strpos($notification->message, $issue->title) !== false
            ) {
                // Extract status from notification message
                preg_match('/updated to: ([^"]+)/', $notification->message, $matches);
                $statusText = $matches[1] ?? 'Updated';

                // Extract who updated it from message
                preg_match('/by ([^"]+) on/', $notification->message, $updaterMatches);
                $updaterName = $updaterMatches[1] ?? 'Property Manager';

                $statusHistory[] = [
                    'status' => strtolower(str_replace(' ', '_', $statusText)),
                    'status_text' => $statusText,
                    'updated_by' => 'Property Manager',
                    'updated_by_name' => $updaterName,
                    'date_time' => $notification->created_at,
                    'notes' => $notification->message
                ];
            }
        }

        // 3. Add resolved status if applicable
        if ($issue->status === 'resolved' && !empty($issue->resolved_at)) {
            // Check if already in history
            $alreadyResolved = false;
            foreach ($statusHistory as $history) {
                if (strpos(strtolower($history['status_text']), 'resolved') !== false) {
                    $alreadyResolved = true;
                    break;
                }
            }

            if (!$alreadyResolved) {
                $statusHistory[] = [
                    'status' => 'resolved',
                    'status_text' => 'Resolved',
                    'updated_by' => 'Property Manager',
                    'updated_by_name' => 'Property Manager',
                    'date_time' => $issue->resolved_at,
                    'notes' => $issue->resolution_notes ?? 'Issue has been resolved'
                ];
            }
        }

        // Sort status history by date (oldest first)
        usort($statusHistory, function ($a, $b) {
            return strtotime($a['date_time']) - strtotime($b['date_time']);
        });

        $data = [
            'page_title' => 'Issue Details - TenantHub',
            'page' => 'track_issues',
            'user_name' => $_SESSION['user_name'],
            'issue' => $issue,
            'maintenanceRequest' => $maintenanceRequest,
            'quotations' => $quotations,
            'serviceProvider' => $serviceProvider,
            'statusHistory' => $statusHistory
        ];

        $this->view('tenant/v_issue_details', $data);
    }

    public function edit($id = null)
    {
        if (!$id) {
            flash('issue_message', 'Invalid issue ID', 'alert alert-danger');
            redirect('issues/track');
        }

        // Get the issue first to check time restriction
        $issue = $this->issueModel->getIssueById($id);

        if (!$issue) {
            flash('issue_message', 'Issue not found', 'alert alert-danger');
            redirect('issues/track');
        }

        if ($issue->tenant_id != $_SESSION['user_id']) {
            flash('issue_message', 'Unauthorized access', 'alert alert-danger');
            redirect('issues/track');
        }

        // Check if issue can be edited (within 1 minute of creation)
        $createdTime = new DateTime($issue->created_at);
        $currentTime = new DateTime();
        $timeDiff = $currentTime->getTimestamp() - $createdTime->getTimestamp();

        if ($timeDiff > 60) {
            flash('issue_message', 'Issues can only be edited within 1 minute of creation', 'alert alert-warning');
            redirect('issues/track');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'id' => $id,
                'tenant_id' => $_SESSION['user_id'],
                'property_id' => trim($_POST['property_id']),
                'issue_title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'category' => trim($_POST['category']),
                'priority' => trim($_POST['priority']),
                'status' => trim($_POST['status'])
            ];

            $validator = new Validator();

            $validator->required('property', $data['property_id'], 'Please select a property');

            if ($validator->required('title', $data['issue_title'])) {
                $validator->minLength('title', $data['issue_title'], 5);
                $validator->maxLength('title', $data['issue_title'], 200);
            }

            if ($validator->required('description', $data['description'])) {
                $validator->minLength('description', $data['description'], 10);
                $validator->maxLength('description', $data['description'], 1000);
            }

            $validCategories = ['plumbing', 'electrical', 'hvac', 'appliance', 'structural', 'pest', 'other'];
            if ($validator->required('category', $data['category'], 'Please select a category')) {
                $validator->inArray('category', $data['category'], $validCategories);
            }

            $validPriorities = ['low', 'medium', 'high', 'emergency'];
            if ($validator->required('priority', $data['priority'], 'Please select a priority')) {
                $validator->inArray('priority', $data['priority'], $validPriorities);
            }

            if ($data['priority'] === 'emergency') {
                $validator->custom(
                    'description',
                    strlen($data['description']) >= 50,
                    'Emergency issues require detailed description (at least 50 characters)'
                );
            }

            $criticalCategories = ['electrical', 'structural'];
            if (in_array($data['category'], $criticalCategories) && $data['priority'] === 'low') {
                $validator->custom(
                    'priority',
                    false,
                    ucfirst($data['category']) . ' issues should not be low priority for safety reasons'
                );
            }

            if (!$validator->hasErrors()) {
                $dbData = [
                    'id' => $data['id'],
                    'property_id' => $data['property_id'],
                    'title' => $data['issue_title'],
                    'description' => $data['description'],
                    'category' => $data['category'],
                    'priority' => $data['priority'],
                    'status' => $data['status']
                ];

                if ($this->issueModel->updateIssue($dbData)) {
                    flash('issue_message', 'Issue updated successfully', 'alert alert-success');
                    redirect('issues/track');
                } else {
                    die('Something went wrong when updating issue');
                }
            } else {
                $errors = $validator->getErrors();
                $data['property_err'] = $errors['property'] ?? '';
                $data['title_err'] = $errors['title'] ?? '';
                $data['description_err'] = $errors['description'] ?? '';
                $data['category_err'] = $errors['category'] ?? '';
                $data['priority_err'] = $errors['priority'] ?? '';

                $data['issue'] = $issue;
                $data['properties'] = $this->issueModel->getProperties();
                $data['page_title'] = 'Edit Issue - TenantHub';
                $data['page'] = 'edit_issue';
                $data['user_name'] = $_SESSION['user_name'];

                $this->view('tenant/v_edit_issue', $data);
            }
        } else {
            $properties = $this->issueModel->getProperties();

            $data = [
                'page_title' => 'Edit Issue - TenantHub',
                'page' => 'edit_issue',
                'user_name' => $_SESSION['user_name'],
                'issue' => $issue,
                'properties' => $properties,
                'property_id' => $issue->property_id,
                'issue_title' => $issue->title,
                'description' => $issue->description,
                'category' => $issue->category,
                'priority' => $issue->priority,
                'property_err' => '',
                'title_err' => '',
                'description_err' => '',
                'category_err' => '',
                'priority_err' => ''
            ];

            $this->view('tenant/v_edit_issue', $data);
        }
    }

    public function delete($id = null)
    {
        if (!$id) {
            flash('issue_message', 'Invalid issue ID', 'alert alert-danger');
            redirect('issues/track');
        }

        $issue = $this->issueModel->getIssueById($id);

        if (!$issue) {
            flash('issue_message', 'Issue not found', 'alert alert-danger');
            redirect('issues/track');
        }

        if ($issue->tenant_id != $_SESSION['user_id']) {
            flash('issue_message', 'Unauthorized action', 'alert alert-danger');
            redirect('issues/track');
        }

        $createdTime = new DateTime($issue->created_at);
        $currentTime = new DateTime();
        $timeDiff = $currentTime->getTimestamp() - $createdTime->getTimestamp();

        if ($timeDiff > 60) {
            flash('issue_message', 'Issues can only be deleted within 1 minute of creation', 'alert alert-warning');
            redirect('issues/track');
        }

        if ($this->issueModel->deleteIssue($id)) {
            flash('issue_message', 'Issue deleted successfully', 'alert alert-success');
            redirect('issues/track');
        } else {
            flash('issue_message', 'Something went wrong. Unable to delete issue.', 'alert alert-danger');
            redirect('issues/track');
        }
    }

    /**
     * AJAX endpoint for getting issue stat card data by period
     */
    public function getStatData()
    {
        // Only accept POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Get JSON data from request body
        $input = json_decode(file_get_contents('php://input'), true);
        $statType = $input['stat_type'] ?? '';
        $period = $input['period'] ?? 'all';
        $tenant_id = $_SESSION['user_id'];

        // Validate period
        if (!in_array($period, ['all', 'month', 'year'])) {
            $period = 'all';
        }

        $value = 0;
        $subtitle = '';

        switch ($statType) {
            case 'tenant_issues_pending':
                $stats = $this->issueModel->getIssueStats($tenant_id, 'tenant', $period);
                $value = $stats->pending_count ?? 0;
                $subtitle = 'Awaiting response';
                break;

            case 'tenant_issues_progress':
                $stats = $this->issueModel->getIssueStats($tenant_id, 'tenant', $period);
                $value = $stats->in_progress_count ?? 0;
                $subtitle = 'Being worked on';
                break;

            case 'tenant_issues_resolved':
                $stats = $this->issueModel->getIssueStats($tenant_id, 'tenant', $period);
                $value = $stats->resolved_count ?? 0;
                $subtitle = 'Completed';
                break;

            case 'tenant_issues_avg_days':
                $stats = $this->issueModel->getIssueStats($tenant_id, 'tenant', $period);
                $avgDays = $stats->avg_resolution_days ?? 0;
                $value = number_format($avgDays, 1);
                $subtitle = 'Average resolution';
                break;

            default:
                $value = 0;
                $subtitle = 'Unknown stat type';
        }

        header('Content-Type: application/json');
        echo json_encode([
            'value' => $value,
            'subtitle' => $subtitle
        ]);
    }
}
