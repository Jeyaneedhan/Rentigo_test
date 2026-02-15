<?php
require_once '../app/helpers/helper.php';

// Manager (Property Manager) controller - handles all PM-related pages
// Property managers oversee properties assigned to them by landlords
class Manager extends Controller
{
    private $userModel;
    private $notificationModel;

    public function __construct()
    {
        $this->userModel = $this->model('M_Users');
        $this->notificationModel = $this->model('M_Notifications');
        // Security check - only property managers can access this controller
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'property_manager') {
            redirect('users/login');
        }
    }

    // Helper method to get unread notification count
    // We use this on every page to show the notification badge
    private function getUnreadNotificationCount()
    {
        return $this->notificationModel->getUnreadCount($_SESSION['user_id']);
    }

    // Default landing page - redirects to dashboard
    public function index()
    {
        $this->dashboard();
    }

    // Main dashboard - shows overview of managed properties and financial stats
    public function dashboard()
    {
        // Load all the models we need
        $propertyModel = $this->model('M_ManagerProperties');
        $maintenanceModel = $this->model('M_Maintenance');
        $paymentModel = $this->model('M_Payments');
        $maintenanceQuotationModel = $this->model('M_MaintenanceQuotations');

        // Get this property manager's data
        $manager_id = $_SESSION['user_id'];
        $properties = $propertyModel->getAssignedProperties($manager_id);

        // Get recent maintenance requests for the dashboard
        $allMaintenance = $maintenanceModel->getAllMaintenanceRequests();
        $recentMaintenance = array_slice($allMaintenance, 0, 5);

        // Get all rental payments
        $allPayments = $paymentModel->getAllPayments();

        // Get all maintenance service payments
        $maintenancePayments = $maintenanceQuotationModel->getAllMaintenancePayments();

        // Combine both types of payments and sort by date (newest first)
        $combinedPayments = array_merge($allPayments, $maintenancePayments);
        usort($combinedPayments, function ($a, $b) {
            $dateA = $a->payment_date ?? $a->due_date ?? $a->created_at;
            $dateB = $b->payment_date ?? $b->due_date ?? $b->created_at;
            return strtotime($dateB) - strtotime($dateA);
        });
        $recentPayments = array_slice($combinedPayments, 0, 10);

        // Get property stats using model method (All Time by default)
        $propertyStats = $propertyModel->getPropertyStats($manager_id, 'all');
        $totalProperties = $propertyStats->total_properties ?? 0;
        $totalUnits = $propertyStats->total_units ?? 0;
        $occupiedUnits = $propertyStats->occupied_units ?? 0;

        // Get income stats using model method (All Time by default)
        // Platform Income = 10% service fees from rent + maintenance fees
        $incomeStats = $paymentModel->getManagerIncomeStats($manager_id, 'all');
        $rentalServiceFee = $incomeStats->total_income ?? 0;

        // Add maintenance income
        $maintenanceIncomeStats = $maintenanceQuotationModel->getManagerMaintenanceIncome($manager_id, 'all');
        $maintenanceIncome = $maintenanceIncomeStats->total_income ?? 0;

        $totalIncome = $rentalServiceFee + $maintenanceIncome;

        // Get expense stats using model method (All Time by default)
        $maintenanceStats = $maintenanceModel->getMaintenanceStats(null, $manager_id, 'all');
        $totalExpenses = $maintenanceStats->total_cost ?? 0;

        // Get issue statistics
        $issueModel = $this->model('M_Issue');
        $issueStats = $issueModel->getIssueStats($manager_id, 'manager');

        // Get unpaid tenants (overdue payments)
        $unpaidTenants = $paymentModel->getOverduePaymentsByManager($manager_id);

        $data = [
            'title' => 'Property Manager Dashboard',
            'page' => 'dashboard',
            'user_name' => $_SESSION['user_name'],
            'totalProperties' => $totalProperties,
            'totalUnits' => $totalUnits,
            'occupiedUnits' => $occupiedUnits,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'recentPayments' => $recentPayments,
            'recentMaintenance' => $recentMaintenance,
            'issueStats' => $issueStats,
            'unpaidTenants' => $unpaidTenants,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('manager/v_dashboard', $data);
    }

    // Redirect to properties management page
    public function properties()
    {
        redirect('ManagerProperties/index');
    }

    // Tenant management page - shows all tenants in properties this PM manages
    // Includes filtering options
    public function tenants()
    {
        // Load models
        $bookingModel = $this->model('M_Bookings');
        $propertyModel = $this->model('M_ManagerProperties');
        $manager_id = $_SESSION['user_id'];

        // Get properties assigned to this manager
        $assignedProperties = $propertyModel->getAssignedProperties($manager_id);
        $propertyIds = array_map(fn($p) => $p->id, $assignedProperties ?? []);

        // Get bookings only for assigned properties
        $allBookings = [];
        if (!empty($propertyIds)) {
            $allBookings = $bookingModel->getBookingsByProperties($propertyIds);
        }

        // Get filter parameters from GET request
        $statusFilter = $_GET['status'] ?? 'all';
        $propertyFilter = $_GET['property_id'] ?? 'all';
        $dateFromFilter = $_GET['date_from'] ?? '';
        $dateToFilter = $_GET['date_to'] ?? '';

        // Calculate stats for all bookings (for stats cards - always show totals)
        $activeBookings = array_filter($allBookings, fn($b) => $b->status === 'active' || $b->status === 'approved');
        $pendingBookings = array_filter($allBookings, fn($b) => $b->status === 'pending');
        $vacatedBookings = array_filter($allBookings, fn($b) => $b->status === 'completed' || $b->status === 'cancelled');

        // Apply filters to bookings
        $filteredBookings = $allBookings;

        // Filter by status
        if ($statusFilter !== 'all') {
            if ($statusFilter === 'active') {
                $filteredBookings = array_filter($filteredBookings, fn($b) => $b->status === 'active' || $b->status === 'approved');
            } elseif ($statusFilter === 'pending') {
                $filteredBookings = array_filter($filteredBookings, fn($b) => $b->status === 'pending');
            } elseif ($statusFilter === 'vacated') {
                $filteredBookings = array_filter($filteredBookings, fn($b) => $b->status === 'completed' || $b->status === 'cancelled');
            } else {
                $filteredBookings = array_filter($filteredBookings, fn($b) => $b->status === $statusFilter);
            }
        }

        // Filter by property
        if ($propertyFilter !== 'all' && is_numeric($propertyFilter)) {
            $filteredBookings = array_filter($filteredBookings, fn($b) => $b->property_id == $propertyFilter);
        }

        // Filter by date range (move-in date)
        if (!empty($dateFromFilter)) {
            $filteredBookings = array_filter($filteredBookings, function ($b) use ($dateFromFilter) {
                return strtotime($b->move_in_date) >= strtotime($dateFromFilter);
            });
        }

        if (!empty($dateToFilter)) {
            $filteredBookings = array_filter($filteredBookings, function ($b) use ($dateToFilter) {
                return strtotime($b->move_in_date) <= strtotime($dateToFilter);
            });
        }

        // Re-index array after filtering
        $filteredBookings = array_values($filteredBookings);

        $data = [
            'title' => 'Tenant Management',
            'page' => 'tenants',
            'user_name' => $_SESSION['user_name'],
            'assignedPropertiesCount' => count($assignedProperties ?? []),
            'allBookings' => $filteredBookings, // Filtered bookings for display
            'assignedProperties' => $assignedProperties, // For property filter dropdown
            'activeCount' => count($activeBookings), // Total stats
            'pendingCount' => count($pendingBookings), // Total stats
            'vacatedCount' => count($vacatedBookings), // Total stats
            // Current filter values (for maintaining form state)
            'currentStatusFilter' => $statusFilter,
            'currentPropertyFilter' => $propertyFilter,
            'currentDateFromFilter' => $dateFromFilter,
            'currentDateToFilter' => $dateToFilter,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('manager/v_tenants', $data);
    }

    public function maintenance()
    {
        // Load maintenance model
        $maintenanceModel = $this->model('M_Maintenance');
        $maintenanceQuotationModel = $this->model('M_MaintenanceQuotations');
        $manager_id = $_SESSION['user_id'];

        // Get maintenance requests for this manager's properties only
        $allRequests = $maintenanceModel->getMaintenanceByManager($manager_id);

        // Get maintenance statistics
        $maintenanceStats = $maintenanceModel->getMaintenanceStats(null, $manager_id);

        // Get maintenance income (from completed maintenance payments)
        $incomeStats = $maintenanceQuotationModel->getManagerMaintenanceIncome($manager_id, 'all');
        $maintenanceIncome = $incomeStats->total_income ?? 0;

        // Filter by status
        $requestedRequests = array_filter($allRequests, fn($r) => $r->status === 'requested');
        $quotedRequests = array_filter($allRequests, fn($r) => $r->status === 'quoted');
        $approvedRequests = array_filter($allRequests, fn($r) => $r->status === 'approved' || $r->status === 'in_progress');
        $completedRequests = array_filter($allRequests, fn($r) => $r->status === 'completed');

        // Get pending quotation approvals (quoted status)
        $pendingApprovals = $quotedRequests;

        $data = [
            'title' => 'Maintenance Management',
            'page' => 'maintenance',
            'user_name' => $_SESSION['user_name'],
            'maintenanceRequests' => $allRequests,  // View expects this
            'maintenanceStats' => $maintenanceStats,  // View expects this
            'maintenanceIncome' => $maintenanceIncome,  // Total income from maintenance payments
            'allRequests' => $allRequests,
            'requestedRequests' => $requestedRequests,
            'quotedRequests' => $quotedRequests,
            'approvedRequests' => $approvedRequests,
            'completedRequests' => $completedRequests,
            'pendingApprovals' => $pendingApprovals,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('manager/v_maintenance', $data);
    }

    // Redirect to inspections page
    public function inspections()
    {
        redirect('inspections/index'); // route to inspection controller
    }

    // Issue tracking page - shows all tenant issues for managed properties
    public function issues()
    {
        $issueModel = $this->model('M_Issue');
        $manager_id = $_SESSION['user_id'];

        // Get issues for properties assigned to this PM
        $allIssues = $issueModel->getIssuesByManager($manager_id);

        $openIssues = array_filter($allIssues, fn($issue) => $issue->status === 'pending');
        $assignedIssues = array_filter($allIssues, fn($issue) => $issue->status === 'assigned');
        $inProgressIssues = array_filter($allIssues, fn($issue) => $issue->status === 'in_progress');
        $resolvedIssues = array_filter($allIssues, fn($issue) => $issue->status === 'resolved');

        // Get issue statistics
        $stats = $issueModel->getIssueStats($manager_id, 'manager');

        $data = [
            'title' => 'Issue Tracking',
            'page' => 'issues',
            'user_name' => $_SESSION['user_name'],
            'allIssues' => $allIssues,
            'openIssues' => $openIssues,
            'assignedIssues' => $assignedIssues,
            'inProgressIssues' => $inProgressIssues,
            'resolvedIssues' => $resolvedIssues,
            'issueStats' => $stats,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('manager/v_issues', $data);
    }

    // Issue details and management page
    public function issueDetails($id = null)
    {
        if (!$id) {
            flash('issue_error', 'Issue not found', 'alert alert-danger');
            redirect('manager/issues');
        }

        $issueModel = $this->model('M_Issue');
        $issue = $issueModel->getIssueById($id);

        if (!$issue) {
            flash('issue_error', 'Issue not found', 'alert alert-danger');
            redirect('manager/issues');
        }

        // Verify this PM manages this property
        $propertyModel = $this->model('M_ManagerProperties');
        $manager = $propertyModel->getManagerByProperty($issue->property_id);

        if (!$manager || $manager->manager_id != $_SESSION['user_id']) {
            flash('issue_error', 'Unauthorized access', 'alert alert-danger');
            redirect('manager/issues');
        }

        $data = [
            'title' => 'Issue Details',
            'page' => 'issues',
            'user_name' => $_SESSION['user_name'],
            'issue' => $issue,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('manager/v_issue_details', $data);
    }

    // Update issue status
    public function updateIssueStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('manager/issues');
        }

        header('Content-Type: application/json');

        $issue_id = $_POST['issue_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $resolution_notes = $_POST['resolution_notes'] ?? null;

        if (!$issue_id || !$status) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit();
        }

        $issueModel = $this->model('M_Issue');
        $issue = $issueModel->getIssueById($issue_id);

        if (!$issue) {
            echo json_encode(['success' => false, 'message' => 'Issue not found']);
            exit();
        }

        // Verify this PM manages this property
        $propertyModel = $this->model('M_ManagerProperties');
        $manager = $propertyModel->getManagerByProperty($issue->property_id);

        if (!$manager || $manager->manager_id != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit();
        }

        if ($issueModel->updateStatus($issue_id, $status, $resolution_notes)) {
            // Send notification to tenant
            $notificationModel = $this->model('M_Notifications');
            $statusText = ucfirst(str_replace('_', ' ', $status));

            // Get current date/time
            $updateDateTime = date('F j, Y \a\t g:i A');

            // Get Property Manager name
            $pmName = $_SESSION['user_name'] ?? 'Property Manager';

            // Create enhanced notification message
            $notificationMessage = sprintf(
                'Your issue "%s" has been updated to: %s by %s on %s',
                $issue->title,
                $statusText,
                $pmName,
                $updateDateTime
            );

            $notificationModel->createNotification([
                'user_id' => $issue->tenant_id,
                'type' => 'issue_update',
                'title' => 'Issue Status Updated',
                'message' => $notificationMessage,
                'link' => 'issues/track'
            ]);

            echo json_encode(['success' => true, 'message' => 'Issue status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update issue status']);
        }
        exit();
    }

    // Notify landlord about issue
    public function notifyLandlordAboutIssue()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('manager/issues');
        }

        header('Content-Type: application/json');

        $issue_id = $_POST['issue_id'] ?? null;
        $message = $_POST['message'] ?? '';

        if (!$issue_id) {
            echo json_encode(['success' => false, 'message' => 'Issue ID is required']);
            exit();
        }

        $issueModel = $this->model('M_Issue');
        $issue = $issueModel->getIssueById($issue_id);

        if (!$issue) {
            echo json_encode(['success' => false, 'message' => 'Issue not found']);
            exit();
        }

        // Verify this PM manages this property
        $propertyModel = $this->model('M_ManagerProperties');
        $manager = $propertyModel->getManagerByProperty($issue->property_id);

        if (!$manager || $manager->manager_id != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit();
        }

        // Send notification to landlord
        $notificationModel = $this->model('M_Notifications');

        $notificationMessage = $message ?: 'Your property manager has flagged a tenant issue that may require a maintenance request: "' . $issue->title . '". Please review and take appropriate action.';

        $notificationModel->createNotification([
            'user_id' => $issue->landlord_id,
            'type' => 'issue_landlord_action',
            'title' => 'Tenant Issue Requires Your Attention',
            'message' => $notificationMessage,
            'link' => 'landlord/issueDetails/' . $issue_id
        ]);

        // Mark landlord as notified
        $issueModel->markLandlordNotified($issue_id);

        echo json_encode(['success' => true, 'message' => 'Landlord has been notified']);
        exit();
    }

    public function leases()
    {
        // Load lease agreement model
        $leaseModel = $this->model('M_LeaseAgreements');
        $propertyModel = $this->model('M_ManagerProperties');

        // Get manager's assigned property leases
        $manager_id = $_SESSION['user_id'];
        $allLeases = $leaseModel->getLeasesByManager($manager_id);

        // Get assigned properties for filter dropdown
        $assignedProperties = $propertyModel->getAssignedProperties($manager_id);

        // Get filter parameters from GET request
        $statusFilter = $_GET['status'] ?? 'all';
        $propertyFilter = $_GET['property_id'] ?? 'all';
        $dateFromFilter = $_GET['date_from'] ?? '';
        $dateToFilter = $_GET['date_to'] ?? '';

        // Calculate stats for all leases (for stats cards - always show totals)
        $draftLeases = array_filter($allLeases, fn($l) => $l->status === 'draft');
        $activeLeases = array_filter($allLeases, fn($l) => $l->status === 'active');
        $completedLeases = array_filter($allLeases, fn($l) => $l->status === 'completed');

        // Apply filters to leases
        $filteredLeases = $allLeases;

        // Filter by status
        if ($statusFilter !== 'all') {
            $filteredLeases = array_filter($filteredLeases, fn($l) => $l->status === $statusFilter);
        }

        // Filter by property
        if ($propertyFilter !== 'all' && is_numeric($propertyFilter)) {
            $filteredLeases = array_filter($filteredLeases, fn($l) => $l->property_id == $propertyFilter);
        }

        // Filter by date range (start date)
        if (!empty($dateFromFilter)) {
            $filteredLeases = array_filter($filteredLeases, function ($l) use ($dateFromFilter) {
                return strtotime($l->start_date) >= strtotime($dateFromFilter);
            });
        }

        if (!empty($dateToFilter)) {
            $filteredLeases = array_filter($filteredLeases, function ($l) use ($dateToFilter) {
                return strtotime($l->start_date) <= strtotime($dateToFilter);
            });
        }

        // Re-index array after filtering
        $filteredLeases = array_values($filteredLeases);

        $data = [
            'title' => 'Lease Agreements',
            'page' => 'leases',
            'user_name' => $_SESSION['user_name'],
            'allLeases' => $filteredLeases, // Filtered leases for display
            'assignedProperties' => $assignedProperties, // For property filter dropdown
            'draftCount' => count($draftLeases), // Total stats
            'activeCount' => count($activeLeases), // Total stats
            'completedCount' => count($completedLeases), // Total stats
            // Current filter values (for maintaining form state)
            'currentStatusFilter' => $statusFilter,
            'currentPropertyFilter' => $propertyFilter,
            'currentDateFromFilter' => $dateFromFilter,
            'currentDateToFilter' => $dateToFilter,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('manager/v_leases', $data);
    }

    public function providers()
    {
        // Load service provider model
        $providerModel = $this->model('M_ServiceProviders');

        // Get all service providers
        $allProviders = $providerModel->getAllProviders();

        $data = [
            'title' => 'Service Providers',
            'page' => 'providers',
            'user_name' => $_SESSION['user_name'],
            'providers' => $allProviders,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('manager/v_providers', $data);
    }

    public function bookings()
    {
        // Load booking model
        $bookingModel = $this->model('M_Bookings');
        $propertyModel = $this->model('M_ManagerProperties');

        // Get manager's assigned properties
        $manager_id = $_SESSION['user_id'];
        $assignedProperties = $propertyModel->getAssignedProperties($manager_id);

        // Get property IDs
        $propertyIds = array_map(fn($p) => $p->id, $assignedProperties ?? []);

        // Get all bookings for assigned properties
        $allBookings = [];
        if (!empty($propertyIds)) {
            $allBookings = $bookingModel->getBookingsByProperties($propertyIds);
        }

        // Get filter parameters from GET request
        $statusFilter = $_GET['status'] ?? 'all';
        $propertyFilter = $_GET['property_id'] ?? 'all';
        $dateFromFilter = $_GET['date_from'] ?? '';
        $dateToFilter = $_GET['date_to'] ?? '';

        // Calculate stats for all bookings (for stats cards - always show totals)
        $pendingBookings = array_filter($allBookings, fn($b) => $b->status === 'pending');
        $approvedBookings = array_filter($allBookings, fn($b) => $b->status === 'approved');
        $rejectedBookings = array_filter($allBookings, fn($b) => $b->status === 'rejected');

        // Apply filters to bookings
        $filteredBookings = $allBookings;

        // Filter by status
        if ($statusFilter !== 'all') {
            $filteredBookings = array_filter($filteredBookings, fn($b) => $b->status === $statusFilter);
        }

        // Filter by property
        if ($propertyFilter !== 'all' && is_numeric($propertyFilter)) {
            $filteredBookings = array_filter($filteredBookings, fn($b) => $b->property_id == $propertyFilter);
        }

        // Filter by date range (move-in date)
        if (!empty($dateFromFilter)) {
            $filteredBookings = array_filter($filteredBookings, function ($b) use ($dateFromFilter) {
                return strtotime($b->move_in_date) >= strtotime($dateFromFilter);
            });
        }

        if (!empty($dateToFilter)) {
            $filteredBookings = array_filter($filteredBookings, function ($b) use ($dateToFilter) {
                return strtotime($b->move_in_date) <= strtotime($dateToFilter);
            });
        }

        // Re-index array after filtering
        $filteredBookings = array_values($filteredBookings);

        $data = [
            'title' => 'Booking Management',
            'page' => 'bookings',
            'user_name' => $_SESSION['user_name'],
            'allBookings' => $filteredBookings, // Filtered bookings for display
            'assignedProperties' => $assignedProperties, // For property filter dropdown
            'pendingCount' => count($pendingBookings), // Total stats
            'approvedCount' => count($approvedBookings), // Total stats
            'rejectedCount' => count($rejectedBookings), // Total stats
            // Current filter values (for maintaining form state)
            'currentStatusFilter' => $statusFilter,
            'currentPropertyFilter' => $propertyFilter,
            'currentDateFromFilter' => $dateFromFilter,
            'currentDateToFilter' => $dateToFilter,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('manager/v_bookings', $data);
    }

    public function notifications()
    {
        // Get all notifications for the property manager
        $notifications = $this->notificationModel->getNotificationsByUser($_SESSION['user_id']);
        $unreadCount = $this->notificationModel->getUnreadCount($_SESSION['user_id']);

        $data = [
            'title' => 'Notifications',
            'page' => 'notifications',
            'user_name' => $_SESSION['user_name'],
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('manager/v_notifications', $data);
    }

    // Mark notification as read
    public function markNotificationRead($id)
    {
        if ($this->notificationModel->markAsRead($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    // Mark all notifications as read
    public function markAllNotificationsRead()
    {
        if ($this->notificationModel->markAllAsRead($_SESSION['user_id'])) {
            flash('notification_message', 'All notifications marked as read', 'alert alert-success');
        } else {
            flash('notification_message', 'Failed to mark notifications as read', 'alert alert-danger');
        }

        redirect('manager/notifications');
    }

    // Delete notification
    public function deleteNotification($id)
    {
        if ($this->notificationModel->deleteNotification($id)) {
            flash('notification_message', 'Notification deleted', 'alert alert-success');
        } else {
            flash('notification_message', 'Failed to delete notification', 'alert alert-danger');
        }

        redirect('manager/notifications');
    }

    // Payment tracking for assigned properties
    public function payments()
    {
        $paymentModel = $this->model('M_Payments');
        $propertyModel = $this->model('M_ManagerProperties');

        $manager_id = $_SESSION['user_id'];

        // Get assigned properties
        $assignedProperties = $propertyModel->getAssignedProperties($manager_id);
        $propertyIds = array_map(fn($p) => $p->id, $assignedProperties ?? []);

        // Get all payments for assigned properties
        $allPayments = [];
        if (!empty($propertyIds)) {
            foreach ($propertyIds as $propertyId) {
                $propertyPayments = $paymentModel->getPaymentsByProperty($propertyId);
                $allPayments = array_merge($allPayments, $propertyPayments);
            }
        }

        // Sort payments by date (newest first)
        usort($allPayments, function ($a, $b) {
            $dateA = $a->payment_date ?? $a->due_date;
            $dateB = $b->payment_date ?? $b->due_date;
            return strtotime($dateB) - strtotime($dateA);
        });

        // Calculate statistics
        $totalIncome = 0;
        $completedCount = 0;
        $pendingCount = 0;
        $pendingAmount = 0;

        foreach ($allPayments as $payment) {
            if ($payment->status === 'completed') {
                $totalIncome += $payment->amount;
                $completedCount++;
            } else if ($payment->status === 'pending') {
                $pendingCount++;
                $pendingAmount += $payment->amount;
            }
        }

        $data = [
            'title' => 'Payment Tracking',
            'page' => 'payments',
            'user_name' => $_SESSION['user_name'],
            'payments' => $allPayments,
            'totalIncome' => $totalIncome,
            'completedCount' => $completedCount,
            'pendingCount' => $pendingCount,
            'pendingAmount' => $pendingAmount,
            'propertyCount' => count($assignedProperties),
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('manager/v_payments', $data);
    }

    // View all payments (rental + maintenance)
    public function allPayments()
    {
        $paymentModel = $this->model('M_Payments');
        $maintenanceQuotationModel = $this->model('M_MaintenanceQuotations');
        $propertyModel = $this->model('M_ManagerProperties');
        $manager_id = $_SESSION['user_id'];

        // Get assigned properties
        $assignedProperties = $propertyModel->getAssignedProperties($manager_id);
        $propertyIds = array_map(fn($p) => $p->id, $assignedProperties ?? []);

        // Get rental payments for manager's properties
        $allPayments = [];
        if (!empty($propertyIds)) {
            foreach ($propertyIds as $propertyId) {
                $propertyPayments = $paymentModel->getPaymentsByProperty($propertyId);
                $allPayments = array_merge($allPayments, $propertyPayments);
            }
        }

        // Get maintenance payments for manager's properties using the proper method
        $maintenancePayments = $maintenanceQuotationModel->getManagerMaintenancePayments($manager_id);

        // Combine and sort all payments by date
        $combinedPayments = array_merge($allPayments, $maintenancePayments);
        usort($combinedPayments, function ($a, $b) {
            $dateA = $a->payment_date ?? $a->due_date ?? $a->created_at;
            $dateB = $b->payment_date ?? $b->due_date ?? $b->created_at;
            return strtotime($dateB) - strtotime($dateA);
        });

        // Get stats using proper model methods (All Time by default)
        $rentalStats = $paymentModel->getManagerIncomeStats($manager_id, 'all');
        $maintenanceIncomeStats = $maintenanceQuotationModel->getManagerMaintenanceIncome($manager_id, 'all');

        $totalIncome = ($rentalStats->total_income ?? 0) + ($maintenanceIncomeStats->total_income ?? 0);
        $completedCount = ($rentalStats->completed_count ?? 0) + ($maintenanceIncomeStats->payment_count ?? 0);
        $pendingCount = $rentalStats->pending_count ?? 0;
        $pendingAmount = $rentalStats->pending_amount ?? 0;

        $data = [
            'title' => 'All Payments',
            'page' => 'payments',
            'user_name' => $_SESSION['user_name'],
            'allPayments' => $combinedPayments,
            'totalIncome' => $totalIncome,
            'completedCount' => $completedCount,
            'pendingCount' => $pendingCount,
            'pendingAmount' => $pendingAmount,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('manager/v_all_payments', $data);
    }

    /**
     * AJAX endpoint for getting stat card data by period
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
        $manager_id = $_SESSION['user_id'];

        $response = ['value' => 0, 'subtitle' => ''];

        // Load models
        $propertyModel = $this->model('M_ManagerProperties');
        $paymentModel = $this->model('M_Payments');
        $maintenanceModel = $this->model('M_Maintenance');
        $bookingModel = $this->model('M_Bookings');
        $leaseModel = $this->model('M_LeaseAgreements');
        $notificationModel = $this->model('M_Notifications');

        switch ($statType) {
            // Dashboard stats
            case 'mgr_properties':
                $stats = $propertyModel->getPropertyStats($manager_id, $period);
                $response['value'] = $stats->total_properties ?? 0;
                $response['subtitle'] = 'Assigned to you';
                break;

            case 'mgr_units':
                $stats = $propertyModel->getPropertyStats($manager_id, $period);
                $response['value'] = $stats->total_units ?? 0;
                $response['subtitle'] = ($stats->occupied_units ?? 0) . ' occupied';
                break;

            case 'mgr_income':
                // Platform Income = 10% service fees from rent + maintenance fees
                $maintenanceQuotationModel = $this->model('M_MaintenanceQuotations');
                $rentalStats = $paymentModel->getManagerIncomeStats($manager_id, $period);
                $rentalServiceFee = $rentalStats->total_income ?? 0;
                $maintenanceIncomeStats = $maintenanceQuotationModel->getManagerMaintenanceIncome($manager_id, $period);
                $maintenanceIncome = $maintenanceIncomeStats->total_income ?? 0;
                $totalIncome = $rentalServiceFee + $maintenanceIncome;
                $response['value'] = 'LKR ' . number_format($totalIncome, 0);
                $response['subtitle'] = '10% rent fees + maintenance';
                break;

            case 'mgr_expenses':
                $stats = $maintenanceModel->getMaintenanceStats(null, $manager_id, $period);
                $response['value'] = 'LKR ' . number_format($stats->total_cost ?? 0, 0);
                $response['subtitle'] = 'Maintenance costs';
                break;

            // Tenant stats
            case 'tenant_active':
                $stats = $bookingModel->getBookingStats($manager_id, 'manager', $period);
                $activeCount = ($stats->active ?? 0) + ($stats->approved ?? 0);
                $response['value'] = $activeCount;
                $response['subtitle'] = 'Currently active tenants';
                break;

            case 'tenant_pending':
                $stats = $bookingModel->getBookingStats($manager_id, 'manager', $period);
                $response['value'] = $stats->pending ?? 0;
                $response['subtitle'] = 'Awaiting approval';
                break;

            case 'tenant_vacated':
                $stats = $bookingModel->getBookingStats($manager_id, 'manager', $period);
                $vacatedCount = ($stats->completed ?? 0) + ($stats->cancelled ?? 0);
                $response['value'] = $vacatedCount;
                $response['subtitle'] = 'Completed or cancelled';
                break;

            // Maintenance stats
            case 'maint_pending':
                $stats = $maintenanceModel->getMaintenanceStats(null, $manager_id, $period);
                $response['value'] = $stats->pending ?? 0;
                $response['subtitle'] = 'Awaiting provider assignment';
                break;

            case 'maint_quotation':
                $stats = $maintenanceModel->getMaintenanceStats(null, $manager_id, $period);
                $response['value'] = $stats->quotation_needed ?? 0;
                $response['subtitle'] = 'Ready to upload';
                break;

            case 'maint_completed':
                $stats = $maintenanceModel->getMaintenanceStats(null, $manager_id, $period);
                $response['value'] = $stats->completed ?? 0;
                $response['subtitle'] = 'Total completed';
                break;

            case 'maint_cost':
                // Total Income from maintenance payments
                $maintenanceQuotationModel = $this->model('M_MaintenanceQuotations');
                $incomeStats = $maintenanceQuotationModel->getManagerMaintenanceIncome($manager_id, $period);
                $response['value'] = 'LKR ' . number_format($incomeStats->total_income ?? 0, 0);
                $response['subtitle'] = 'Maintenance income';
                break;

            // Lease stats
            case 'lease_draft':
                $stats = $leaseModel->getLeaseStats($manager_id, 'manager', $period);
                $response['value'] = $stats->draft ?? 0;
                $response['subtitle'] = 'Pending creation';
                break;

            case 'lease_active':
                $stats = $leaseModel->getLeaseStats($manager_id, 'manager', $period);
                $response['value'] = $stats->active ?? 0;
                $response['subtitle'] = 'Currently active';
                break;

            case 'lease_completed':
                $stats = $leaseModel->getLeaseStats($manager_id, 'manager', $period);
                $response['value'] = $stats->completed ?? 0;
                $response['subtitle'] = 'Ended leases';
                break;

            // Booking stats
            case 'booking_pending':
                $stats = $bookingModel->getBookingStats($manager_id, 'manager', $period);
                $response['value'] = $stats->pending ?? 0;
                $response['subtitle'] = 'Awaiting response';
                break;

            case 'booking_approved':
                $stats = $bookingModel->getBookingStats($manager_id, 'manager', $period);
                $response['value'] = $stats->approved ?? 0;
                $response['subtitle'] = 'Accepted bookings';
                break;

            case 'booking_rejected':
                $stats = $bookingModel->getBookingStats($manager_id, 'manager', $period);
                $response['value'] = $stats->rejected ?? 0;
                $response['subtitle'] = 'Declined requests';
                break;

            // Payment stats
            case 'payment_total':
                $stats = $paymentModel->getManagerIncomeStats($manager_id, $period);
                $totalWithFees = ($stats->total_income ?? 0) * 1.10;
                $response['value'] = 'Rs ' . number_format($totalWithFees, 0);
                $response['subtitle'] = 'Total Collected (with fees)';
                break;

            case 'payment_completed':
                $stats = $paymentModel->getManagerIncomeStats($manager_id, $period);
                $response['value'] = $stats->completed_count ?? 0;
                $response['subtitle'] = 'Completed Payments';
                break;

            case 'payment_pending':
                $stats = $paymentModel->getManagerIncomeStats($manager_id, $period);
                $pendingWithFees = ($stats->pending_amount ?? 0) * 1.10;
                $response['value'] = $stats->pending_count ?? 0;
                $response['subtitle'] = 'Rs ' . number_format($pendingWithFees, 0) . ' due';
                break;

            case 'payment_properties':
                $stats = $propertyModel->getPropertyStats($manager_id, $period);
                $response['value'] = $stats->total_properties ?? 0;
                $response['subtitle'] = 'Properties Managed';
                break;

            // All payments stats (rental service fee + maintenance income)
            case 'all_payment_total':
                $maintenanceQuotationModel = $this->model('M_MaintenanceQuotations');
                // Get rental service fees (10% of rent)
                $rentalStats = $paymentModel->getManagerIncomeStats($manager_id, $period);
                $rentalServiceFee = $rentalStats->total_income ?? 0;
                $rentalCompletedCount = $rentalStats->completed_count ?? 0;
                // Get maintenance income
                $maintenanceIncomeStats = $maintenanceQuotationModel->getManagerMaintenanceIncome($manager_id, $period);
                $maintenanceIncome = $maintenanceIncomeStats->total_income ?? 0;
                $maintenanceCount = $maintenanceIncomeStats->payment_count ?? 0;
                // Combined total
                $totalIncome = $rentalServiceFee + $maintenanceIncome;
                $totalCount = $rentalCompletedCount + $maintenanceCount;
                $response['value'] = 'LKR ' . number_format($totalIncome, 0);
                $response['subtitle'] = $totalCount . ' completed payments';
                break;

            case 'all_payment_pending':
                $stats = $paymentModel->getManagerIncomeStats($manager_id, $period);
                $response['value'] = 'LKR ' . number_format($stats->pending_amount ?? 0, 2);
                $response['subtitle'] = ($stats->pending_count ?? 0) . ' pending payments';
                break;

            // Notification stats
            case 'notif_total':
                $stats = $notificationModel->getUserNotificationStats($manager_id, $period);
                $response['value'] = $stats->total_notifications ?? 0;
                $response['subtitle'] = 'All time';
                break;

            case 'notif_unread':
                $stats = $notificationModel->getUserNotificationStats($manager_id, $period);
                $response['value'] = $stats->unread_count ?? 0;
                $response['subtitle'] = 'Requires attention';
                break;

            case 'notif_read':
                $stats = $notificationModel->getUserNotificationStats($manager_id, $period);
                $response['value'] = $stats->read_count ?? 0;
                $response['subtitle'] = 'Acknowledged';
                break;

            default:
                $response['error'] = 'Unknown stat type';
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
