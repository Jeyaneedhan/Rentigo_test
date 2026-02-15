<?php
require_once '../app/helpers/helper.php';

// Admin controller - handles all admin-related pages and actions
// Only admins can access these methods
class Admin extends Controller
{
    private $userModel;

    public function __construct()
    {
        // Make sure only admins can access this controller
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
            redirect('users/login');
        }
        $this->userModel = $this->model('M_Users');
    }

    // Main dashboard page - shows overview stats and recent activity
    public function index()
    {
        // Load all the models we need for dashboard data
        $propertyModel = $this->model('M_Properties');
        $adminPropertyModel = $this->model('M_AdminProperties');
        $bookingModel = $this->model('M_Bookings');
        $paymentModel = $this->model('M_Payments');
        $maintenanceQuotationModel = $this->model('M_MaintenanceQuotations');
        $policyModel = $this->model('M_Policies');

        // Get all the data from the database
        $allProperties = $propertyModel->getAllProperties();
        $allBookings = $bookingModel->getAllBookings();
        $allPayments = $paymentModel->getAllPayments();
        $pendingPMs = $this->userModel->getPendingPMs();

        // Calculate all-time revenue (default for stat cards with dropdowns)
        // Platform earns 10% from rental payments + 100% from maintenance payments
        $rentalRevenue = $paymentModel->getPlatformRentalIncome('all');
        $maintenanceRevenue = $maintenanceQuotationModel->getTotalMaintenanceIncome('all');
        $totalRevenue = $rentalRevenue + $maintenanceRevenue;

        // Count all active tenants (all time)
        $activeTenants = $bookingModel->getActiveTenantsCount('all');

        // Get all properties count
        $propertyCounts = $adminPropertyModel->getPropertyCounts('all');
        $totalProperties = $propertyCounts->total ?? 0;

        // Get manager stats for pending approvals
        $managerStats = $this->userModel->getManagerStats('all');
        $pendingApprovals = $managerStats->pending ?? 0;

        // Get counts for Admin Attention Summary (always all-time)
        $pendingPropertyApprovals = $propertyCounts->pending ?? 0;
        $pendingPMApprovals = count($pendingPMs);
        $activePolicies = $policyModel->getActivePolicies();
        $activePoliciesCount = count($activePolicies);

        // Build Recent Activity Feed
        $maintenanceModel = $this->model('M_Maintenance');
        $recentUsers = $this->userModel->getRecentUsers(10);
        $recentMaintenance = $maintenanceModel->getAllMaintenance();
        $recentMaintenance = array_slice($recentMaintenance, 0, 10);

        // Combine all activities into one array
        $activities = [];

        // Add user registrations
        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user_registration',
                'icon' => 'fa-user-plus',
                'color' => 'info',
                'title' => 'New ' . ucfirst(str_replace('_', ' ', $user->user_type)) . ' Registered',
                'description' => $user->name . ' joined the platform',
                'created_at' => $user->created_at
            ];
        }

        // Add bookings
        foreach (array_slice($allBookings, 0, 10) as $booking) {
            $activities[] = [
                'type' => 'booking',
                'icon' => 'fa-calendar-check',
                'color' => 'primary',
                'title' => 'New Booking Request',
                'description' => ($booking->tenant_name ?? 'Tenant') . ' requested ' . ($booking->address ?? 'a property'),
                'created_at' => $booking->created_at
            ];
        }

        // Add payments
        foreach (array_slice($allPayments, 0, 10) as $payment) {
            $statusText = $payment->status === 'completed' ? 'Payment Received' : 'Payment ' . ucfirst($payment->status);
            $activities[] = [
                'type' => 'payment',
                'icon' => 'fa-dollar-sign',
                'color' => $payment->status === 'completed' ? 'success' : 'warning',
                'title' => $statusText,
                'description' => 'LKR ' . number_format($payment->amount) . ' from ' . ($payment->tenant_name ?? 'Tenant'),
                'created_at' => $payment->created_at
            ];
        }

        // Add maintenance requests
        foreach ($recentMaintenance as $maintenance) {
            $activities[] = [
                'type' => 'maintenance',
                'icon' => 'fa-wrench',
                'color' => $maintenance->priority === 'emergency' ? 'danger' : 'secondary',
                'title' => 'Maintenance Request',
                'description' => $maintenance->title . ' at ' . ($maintenance->property_address ?? 'Property'),
                'created_at' => $maintenance->created_at
            ];
        }

        // Add new properties
        foreach (array_slice($allProperties, 0, 10) as $property) {
            $activities[] = [
                'type' => 'property',
                'icon' => 'fa-home',
                'color' => 'primary',
                'title' => 'New Property Listed',
                'description' => ($property->address ?? 'Property') . ' by ' . ($property->landlord_name ?? 'Landlord'),
                'created_at' => $property->created_at
            ];
        }

        // Sort all activities by created_at descending and take top 10
        usort($activities, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        $recentActivities = array_slice($activities, 0, 10);

        $data = [
            'title' => 'Admin Dashboard - Rentigo',
            'page' => 'dashboard',
            'totalProperties' => $totalProperties,
            'activeTenants' => $activeTenants,
            'monthlyRevenue' => $totalRevenue,
            'pendingApprovals' => $pendingApprovals,
            'pendingPropertyApprovals' => $pendingPropertyApprovals,
            'pendingPMApprovals' => $pendingPMApprovals,
            'activePoliciesCount' => $activePoliciesCount,
            'recentActivities' => $recentActivities
        ];
        $this->view('admin/v_dashboard', $data);
    }

    // Properties management page
    public function properties()
    {
        redirect('AdminProperties/index');
    }

    // Property managers page
    public function managers()
    {
        $allManagers = $this->userModel->getAllPropertyManagers();

        $data = [
            'title' => 'Property Managers - Rentigo Admin',
            'page' => 'managers',
            'allManagers' => $allManagers
        ];
        $this->view('admin/v_managers', $data);
    }

    // Financial management page - shows all transactions and revenue stats
    public function financials()
    {
        // Load the payment models
        $paymentModel = $this->model('M_Payments');
        $maintenanceQuotationsModel = $this->model('M_MaintenanceQuotations');

        // Get all rental payments from tenants
        $allPayments = $paymentModel->getAllPayments();

        // Get all maintenance service payments
        $maintenancePayments = $maintenanceQuotationsModel->getAllMaintenancePayments();

        // Get all-time financial stats using the new model method
        $financialStats = $paymentModel->getAdminFinancialStats('all');
        $maintenanceRevenue = $maintenanceQuotationsModel->getTotalMaintenanceIncome('all');

        // Stat card values (all-time by default, updated via AJAX)
        $totalRevenue = ($financialStats->total_revenue ?? 0) + $maintenanceRevenue;
        $collected = ($financialStats->collected ?? 0) + $maintenanceRevenue;
        $pending = $financialStats->pending ?? 0;
        $overdue = $financialStats->overdue ?? 0;
        $pendingCount = $financialStats->pending_count ?? 0;
        $overdueCount = $financialStats->overdue_count ?? 0;

        // Combine all transactions into one array for display
        $allTransactions = array_merge($allPayments, $maintenancePayments);

        // Sort all transactions by date, newest first
        usort($allTransactions, function ($a, $b) {
            $dateA = strtotime($a->payment_date ?? $a->due_date ?? $a->created_at);
            $dateB = strtotime($b->payment_date ?? $b->due_date ?? $b->created_at);
            return $dateB - $dateA;  // Descending order
        });

        // ==================== APPLY FILTERS ====================
        // Start with all transactions, then filter based on user's selections
        $filteredTransactions = $allTransactions;

        // Get filter parameters from the URL
        $filterType = $_GET['filter_type'] ?? '';
        $filterStatus = $_GET['filter_status'] ?? '';
        $filterDateFrom = $_GET['filter_date_from'] ?? '';
        $filterDateTo = $_GET['filter_date_to'] ?? '';

        // Apply Type Filter
        if (!empty($filterType)) {
            $filteredTransactions = array_filter($filteredTransactions, function ($transaction) use ($filterType) {
                $isMaintenance = isset($transaction->payment_type) && $transaction->payment_type === 'maintenance';

                if ($filterType === 'rental') {
                    return !$isMaintenance;
                } elseif ($filterType === 'maintenance') {
                    return $isMaintenance;
                }
                return true;
            });
        }

        // Apply Status Filter
        if (!empty($filterStatus)) {
            $filteredTransactions = array_filter($filteredTransactions, function ($transaction) use ($filterStatus) {
                return strtolower($transaction->status) === strtolower($filterStatus);
            });
        }

        // Apply Date Range Filter
        if (!empty($filterDateFrom) || !empty($filterDateTo)) {
            $filteredTransactions = array_filter($filteredTransactions, function ($transaction) use ($filterDateFrom, $filterDateTo) {
                $displayDate = $transaction->payment_date ?? $transaction->due_date ?? $transaction->created_at;
                $transactionDate = strtotime($displayDate);

                // Check FROM date
                if (!empty($filterDateFrom)) {
                    $fromDate = strtotime($filterDateFrom);
                    if ($transactionDate < $fromDate) {
                        return false;
                    }
                }

                // Check TO date
                if (!empty($filterDateTo)) {
                    $toDate = strtotime($filterDateTo . ' 23:59:59'); // End of day
                    if ($transactionDate > $toDate) {
                        return false;
                    }
                }

                return true;
            });
        }

        // Re-index array after filtering
        $filteredTransactions = array_values($filteredTransactions);

        $data = [
            'title' => 'Financials - Rentigo Admin',
            'page' => 'financials',
            'totalRevenue' => $totalRevenue,
            'collected' => $collected,
            'pending' => $pending,
            'overdue' => $overdue,
            'pendingCount' => $pendingCount,
            'overdueCount' => $overdueCount,
            'recentTransactions' => $filteredTransactions,

            // Pass filter values back to view for persistence
            'filter_type' => $filterType,
            'filter_status' => $filterStatus,
            'filter_date_from' => $filterDateFrom,
            'filter_date_to' => $filterDateTo,
        ];
        $this->view('admin/v_financials', $data);
    }

    public function providers()
    {
        redirect('providers/index');
    }

    // Policies management page
    public function policies()
    {
        redirect('policies/index');
    }

    // Notifications page
    public function notifications()
    {
        // Load notification model
        $notificationModel = $this->model('M_Notifications');

        // Get admin-sent notifications and other notifications separately
        $adminNotifications = $notificationModel->getAdminSentNotifications();
        $otherNotifications = $notificationModel->getOtherNotifications();

        // Get statistics
        $stats = $notificationModel->getNotificationStats();

        $data = [
            'title' => 'Notifications - Rentigo Admin',
            'page' => 'notifications',
            'adminNotifications' => $adminNotifications,
            'otherNotifications' => $otherNotifications,
            'stats' => $stats
        ];
        $this->view('admin/v_notifications', $data);
    }

    // Send notification form page
    public function sendNotification()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            // Validate inputs
            $errors = [];
            $recipient_type = $_POST['recipient_type'] ?? '';
            $title = trim($_POST['title'] ?? '');
            $message = trim($_POST['message'] ?? '');

            if (empty($recipient_type)) {
                $errors[] = 'Please select recipient type';
            }
            if (empty($title)) {
                $errors[] = 'Please enter notification title';
            }
            if (empty($message)) {
                $errors[] = 'Please enter notification message';
            }

            if (empty($errors)) {
                $notificationModel = $this->model('M_Notifications');

                // Get user IDs based on recipient type
                $recipients = [];
                switch ($recipient_type) {
                    case 'all':
                        $recipients = array_merge(
                            $this->userModel->getAllUsersByType('tenant'),
                            $this->userModel->getAllUsersByType('landlord'),
                            $this->userModel->getAllUsersByType('property_manager')
                        );
                        break;
                    case 'tenants':
                        $recipients = $this->userModel->getAllUsersByType('tenant');
                        break;
                    case 'landlords':
                        $recipients = $this->userModel->getAllUsersByType('landlord');
                        break;
                    case 'managers':
                        $recipients = $this->userModel->getAllUsersByType('property_manager');
                        break;
                }

                // Send notification to each recipient
                $sent_count = 0;
                foreach ($recipients as $user) {
                    $notificationModel->createNotification([
                        'user_id' => $user->id,
                        'type' => 'system',
                        'title' => $title,
                        'message' => $message,
                        'link' => ''
                    ]);
                    $sent_count++;
                }

                flash('notification_message', "Successfully sent notification to $sent_count user(s)", 'alert alert-success');
                redirect('admin/notifications');
            } else {
                $data = [
                    'title' => 'Send Notification - Rentigo Admin',
                    'page' => 'notifications',
                    'errors' => $errors,
                    'recipient_type' => $recipient_type,
                    'notification_title' => $title,
                    'notification_message' => $message
                ];
                $this->view('admin/v_send_notification', $data);
            }
        } else {
            $data = [
                'title' => 'Send Notification - Rentigo Admin',
                'page' => 'notifications',
                'errors' => [],
                'recipient_type' => '',
                'notification_title' => '',
                'notification_message' => ''
            ];
            $this->view('admin/v_send_notification', $data);
        }
    }

    // Property Manager approvals page
    public function pm_approvals()
    {
        $pendingPMs = $this->userModel->getPendingPMs();

        $data = [
            'title' => 'PM Approvals - Rentigo Admin',
            'page' => 'pm_approvals',
            'pending_pms' => $pendingPMs
        ];
        $this->view('admin/v_pm_approvals', $data);
    }

    // Approve Property Manager
    public function approvePM($userId)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');

            error_log("ApprovePM called with userId: " . $userId);

            if (!isLoggedIn()) {
                error_log("User not logged in");
                echo json_encode([
                    'success' => false,
                    'message' => 'You are not logged in'
                ]);
                exit();
            }

            if ($_SESSION['user_type'] !== 'admin') {
                error_log("User is not admin: " . $_SESSION['user_type']);
                echo json_encode([
                    'success' => false,
                    'message' => 'Unauthorized access. Admin role required.'
                ]);
                exit();
            }

            error_log("Attempting to approve PM with ID: " . $userId);

            try {
                $result = $this->userModel->approvePM($userId);
                error_log("ApprovePM result: " . ($result ? 'true' : 'false'));

                if ($result) {
                    // Send approval email to PM
                    $user = $this->userModel->getUserById($userId);
                    $emailSent = false;
                    $emailWarning = '';

                    if ($user && !empty($user->email)) {
                        $emailSent = sendPMApprovalEmail($user->email, $user->name);
                        if (!$emailSent) {
                            global $smtp_error;
                            $emailWarning = ' However, the notification email could not be sent.';
                            error_log("Failed to send PM approval email to: " . $user->email . " Error: " . $smtp_error);
                        }
                    }

                    echo json_encode([
                        'success' => true,
                        'message' => 'Property Manager approved successfully' . $emailWarning
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to approve Property Manager. Database update returned false.'
                    ]);
                }
            } catch (Exception $e) {
                error_log("Exception in approvePM: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
            exit();
        } else {
            redirect('admin/managers');
        }
    }

    // Remove Property Manager
    public function removePropertyManager($userId)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');

            if (!isLoggedIn()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'You are not logged in'
                ]);
                exit();
            }

            if ($_SESSION['user_type'] !== 'admin') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Unauthorized access. Admin role required.'
                ]);
                exit();
            }

            if ($this->userModel->removePropertyManager($userId)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Property Manager removed successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to remove Property Manager'
                ]);
            }
            exit();
        } else {
            redirect('admin/managers');
        }
    }

    // Reject Property Manager
    public function rejectPM($userId)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');

            if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ]);
                exit();
            }

            $adminId = $_SESSION['user_id'];

            // Get user details before rejection for email
            $user = $this->userModel->getUserById($userId);

            if ($this->userModel->rejectPM($userId, $adminId)) {
                // Send rejection email to PM
                $emailSent = false;
                $emailWarning = '';

                if ($user && !empty($user->email)) {
                    $emailSent = sendPMRejectionEmail($user->email, $user->name);
                    if (!$emailSent) {
                        global $smtp_error;
                        $emailWarning = ' However, the notification email could not be sent.';
                        error_log("Failed to send PM rejection email to: " . $user->email . " Error: " . $smtp_error);
                    }
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Property Manager application rejected' . $emailWarning
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to reject application'
                ]);
            }
            exit();
        } else {
            redirect('admin/managers');
        }
    }

    // View all inspections from all PMs
    public function inspections()
    {
        $inspectionModel = $this->model('M_Inspection');
        $allInspections = $inspectionModel->getAllInspections();

        // ==================== APPLY FILTERS ====================
        $filteredInspections = $allInspections;

        // Get filter parameters
        $filterStatus = $_GET['filter_status'] ?? '';
        $filterType = $_GET['filter_type'] ?? '';
        $filterDateFrom = $_GET['filter_date_from'] ?? '';
        $filterDateTo = $_GET['filter_date_to'] ?? '';

        // Apply Status Filter
        if (!empty($filterStatus)) {
            $filteredInspections = array_filter($filteredInspections, function ($inspection) use ($filterStatus) {
                return strtolower($inspection->status) === strtolower($filterStatus);
            });
        }

        // Apply Type Filter
        if (!empty($filterType)) {
            $filteredInspections = array_filter($filteredInspections, function ($inspection) use ($filterType) {
                return strtolower($inspection->type) === strtolower($filterType);
            });
        }

        // Apply Date Range Filter (on scheduled_date)
        if (!empty($filterDateFrom) || !empty($filterDateTo)) {
            $filteredInspections = array_filter($filteredInspections, function ($inspection) use ($filterDateFrom, $filterDateTo) {
                $scheduledDate = strtotime($inspection->scheduled_date);

                // Check FROM date
                if (!empty($filterDateFrom)) {
                    $fromDate = strtotime($filterDateFrom);
                    if ($scheduledDate < $fromDate) {
                        return false;
                    }
                }

                // Check TO date
                if (!empty($filterDateTo)) {
                    $toDate = strtotime($filterDateTo . ' 23:59:59'); // End of day
                    if ($scheduledDate > $toDate) {
                        return false;
                    }
                }

                return true;
            });
        }

        // Re-index array after filtering
        $filteredInspections = array_values($filteredInspections);

        $data = [
            'title' => 'All Inspections',
            'page' => 'inspections',
            'user_name' => $_SESSION['user_name'],
            'inspections' => $filteredInspections,

            // Pass filter values back to view for persistence
            'filter_status' => $filterStatus,
            'filter_type' => $filterType,
            'filter_date_from' => $filterDateFrom,
            'filter_date_to' => $filterDateTo,
        ];

        $this->view('admin/v_inspections', $data);
    }

    /**
     * AJAX endpoint for fetching stat card data by period
     * Used by stat card dropdowns across all admin pages
     */
    public function getStatData()
    {
        // Ensure this is an AJAX request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $statType = $input['statType'] ?? '';
        $period = $input['period'] ?? 'all';

        // Validate period
        if (!in_array($period, ['all', 'month', 'year'])) {
            $period = 'all';
        }

        $result = ['value' => 0, 'subtitle' => 'All time'];

        // Set subtitle based on period
        switch ($period) {
            case 'month':
                $result['subtitle'] = date('F Y');
                break;
            case 'year':
                $result['subtitle'] = 'Year ' . date('Y');
                break;
            default:
                $result['subtitle'] = 'All time';
        }

        // Load models as needed
        $propertyModel = $this->model('M_AdminProperties');
        $bookingModel = $this->model('M_Bookings');
        $paymentModel = $this->model('M_Payments');
        $maintenanceQuotationModel = $this->model('M_MaintenanceQuotations');
        $notificationModel = $this->model('M_Notifications');
        $policyModel = $this->model('M_Policies');
        $providerModel = $this->model('M_ServiceProviders');

        switch ($statType) {
            // ===== DASHBOARD STATS =====
            case 'admin_properties':
                $counts = $propertyModel->getPropertyCounts($period);
                $result['value'] = number_format($counts->total ?? 0);
                break;

            case 'admin_tenants':
                $result['value'] = number_format($bookingModel->getActiveTenantsCount($period));
                break;

            case 'admin_income':
                $rentalRevenue = $paymentModel->getPlatformRentalIncome($period);
                $maintenanceRevenue = $maintenanceQuotationModel->getTotalMaintenanceIncome($period);
                $result['value'] = 'LKR ' . number_format($rentalRevenue + $maintenanceRevenue, 0);
                $result['subtitle'] = '10% rental + maintenance (' . ($period === 'all' ? 'All time' : $result['subtitle']) . ')';
                break;

            case 'admin_approvals':
                $managerStats = $this->userModel->getManagerStats($period);
                $result['value'] = $managerStats->pending ?? 0;
                break;

            // ===== FINANCIALS STATS =====
            case 'fin_revenue':
                $rentalRevenue = $paymentModel->getPlatformRentalIncome($period);
                $maintenanceRevenue = $maintenanceQuotationModel->getTotalMaintenanceIncome($period);
                $result['value'] = 'LKR ' . number_format($rentalRevenue + $maintenanceRevenue, 0);
                break;

            case 'fin_collected':
                $stats = $paymentModel->getAdminFinancialStats($period);
                $maintenanceRevenue = $maintenanceQuotationModel->getTotalMaintenanceIncome($period);
                $result['value'] = 'LKR ' . number_format(($stats->collected ?? 0) + $maintenanceRevenue, 0);
                break;

            case 'fin_pending':
                $stats = $paymentModel->getAdminFinancialStats($period);
                $result['value'] = 'LKR ' . number_format($stats->pending ?? 0, 0);
                $result['subtitle'] = ($stats->pending_count ?? 0) . ' pending (' . ($period === 'all' ? 'All time' : $result['subtitle']) . ')';
                break;

            case 'fin_overdue':
                $stats = $paymentModel->getAdminFinancialStats($period);
                $result['value'] = 'LKR ' . number_format($stats->overdue ?? 0, 0);
                $result['subtitle'] = ($stats->overdue_count ?? 0) . ' overdue (' . ($period === 'all' ? 'All time' : $result['subtitle']) . ')';
                break;

            // ===== MANAGERS STATS =====
            case 'mgr_total':
                $stats = $this->userModel->getManagerStats($period);
                $result['value'] = $stats->total ?? 0;
                break;

            case 'mgr_pending':
                $stats = $this->userModel->getManagerStats($period);
                $result['value'] = $stats->pending ?? 0;
                break;

            case 'mgr_approved':
                $stats = $this->userModel->getManagerStats($period);
                $result['value'] = $stats->approved ?? 0;
                break;

            case 'mgr_rejected':
                $stats = $this->userModel->getManagerStats($period);
                $result['value'] = $stats->rejected ?? 0;
                break;

            // ===== NOTIFICATIONS STATS =====
            case 'notif_sent':
                $stats = $notificationModel->getNotificationStats($period);
                $result['value'] = $stats->total_sent ?? 0;
                break;

            case 'notif_recipients':
                $stats = $notificationModel->getNotificationStats($period);
                $result['value'] = $stats->total_recipients ?? 0;
                break;

            case 'notif_read':
                $stats = $notificationModel->getNotificationStats($period);
                $result['value'] = $stats->read_count ?? 0;
                break;

            case 'notif_unread':
                $stats = $notificationModel->getNotificationStats($period);
                $result['value'] = $stats->unread_count ?? 0;
                break;

            // ===== POLICIES STATS =====
            case 'policy_total':
                $stats = $policyModel->getPolicyStats($period);
                $result['value'] = $stats['total'] ?? 0;
                break;

            case 'policy_active':
                $stats = $policyModel->getPolicyStats($period);
                $result['value'] = $stats['active'] ?? 0;
                break;

            case 'policy_draft':
                $stats = $policyModel->getPolicyStats($period);
                $result['value'] = $stats['draft'] ?? 0;
                break;

            // ===== PROVIDERS STATS =====
            case 'prov_total':
                $stats = $providerModel->getProviderCounts($period);
                $result['value'] = $stats->total ?? 0;
                break;

            case 'prov_active':
                $stats = $providerModel->getProviderCounts($period);
                $result['value'] = $stats->active ?? 0;
                break;

            case 'prov_inactive':
                $stats = $providerModel->getProviderCounts($period);
                $result['value'] = $stats->inactive ?? 0;
                break;

            case 'prov_rating':
                $stats = $providerModel->getProviderCounts($period);
                $result['value'] = ($stats->average_rating && $stats->average_rating > 0)
                    ? number_format($stats->average_rating, 1)
                    : 'N/A';
                $result['subtitle'] = ($stats->rated_providers ?? 0) > 0
                    ? 'From ' . $stats->rated_providers . ' providers'
                    : 'No ratings yet';
                break;

            default:
                $result['error'] = 'Unknown stat type';
        }

        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
