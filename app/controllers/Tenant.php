<?php
require_once '../app/helpers/helper.php';

// Tenant controller - handles all tenant-specific pages and actions
// Only logged-in tenants can access these methods
class Tenant extends Controller
{
    // Store all the models we'll need throughout this controller
    private $bookingModel;
    private $paymentModel;
    private $leaseModel;
    private $reviewModel;
    private $notificationModel;
    private $issueModel;

    public function __construct()
    {
        // Security check - only tenants can access this controller
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'tenant') {
            redirect('users/login');
        }

        // Load all the models we'll need
        $this->bookingModel = $this->model('M_Bookings');
        $this->paymentModel = $this->model('M_Payments');
        $this->leaseModel = $this->model('M_LeaseAgreements');
        $this->reviewModel = $this->model('M_Reviews');
        $this->notificationModel = $this->model('M_Notifications');
        $this->issueModel = $this->model('M_Issue');
    }

    // Helper method to get how many unread notifications this tenant has
    // We use this in every page to show the notification badge
    private function getUnreadNotificationCount()
    {
        return $this->notificationModel->getUnreadCount($_SESSION['user_id']);
    }

    // Main dashboard page - shows overview of tenant's current status
    public function index()
    {
        $tenant_id = $_SESSION['user_id'];

        // Get dashboard data
        $activeBooking = $this->bookingModel->getActiveBookingByTenant($tenant_id);
        $activeLease = $this->leaseModel->getActiveLeaseByTenant($tenant_id);

        // Get pending payments for the payments list section
        $pendingPayments = $this->paymentModel->getPendingPaymentsByTenant($tenant_id);

        // Get recent issues for the issues list section
        $recentIssues = $this->issueModel->getRecentIssues($tenant_id, 5);

        // Get stats using 'all' period by default (for stat cards with dropdowns)
        $bookingStats = $this->bookingModel->getBookingStats($tenant_id, 'tenant', 'all');
        $paymentStats = $this->paymentModel->getTenantPaymentStats($tenant_id, 'all');
        $issueStats = $this->issueModel->getIssueStats($tenant_id, 'tenant', 'all');
        $unreadNotifications = $this->notificationModel->getUnreadCount($tenant_id);

        $data = [
            'title' => 'Tenant Dashboard - TenantHub',
            'page' => 'dashboard',
            'user_name' => $_SESSION['user_name'],
            'activeBooking' => $activeBooking,
            'activeLease' => $activeLease,
            'pendingPayments' => $pendingPayments,
            'recentIssues' => $recentIssues,
            'bookingStats' => $bookingStats,
            'paymentStats' => $paymentStats,
            'issueStats' => $issueStats,
            'unreadNotifications' => $unreadNotifications,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('tenant/v_dashboard', $data);
    }

    // Redirect to the property search page
    public function search_properties()
    {
        redirect('tenantproperties/index');
    }

    // Show all bookings for this tenant
    public function bookings()
    {
        $tenant_id = $_SESSION['user_id'];

        // Get all bookings for the tenant
        $bookings = $this->bookingModel->getBookingsByTenant($tenant_id);
        $bookingStats = $this->bookingModel->getBookingStats($tenant_id, 'tenant', 'all');

        $data = [
            'title' => 'My Bookings - TenantHub',
            'page' => 'bookings',
            'user_name' => $_SESSION['user_name'],
            'bookings' => $bookings,
            'bookingStats' => $bookingStats,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('tenant/v_bookings', $data);
    }

    // Pay rent page - shows pending payments, payment history, and overdue amounts
    public function pay_rent()
    {
        $tenant_id = $_SESSION['user_id'];

        // Get all payment data (full lists for displaying in sections)
        $pendingPayments = $this->paymentModel->getPendingPaymentsByTenant($tenant_id);
        $paymentHistory = $this->paymentModel->getPaymentsByTenant($tenant_id);
        $overduePayments = $this->paymentModel->getOverduePayments($tenant_id);

        // Get stats using 'all' period for stat cards with dropdowns
        $paymentStats = $this->paymentModel->getTenantPaymentStats($tenant_id, 'all');

        $data = [
            'title' => 'Pay Rent - TenantHub',
            'page' => 'pay_rent',
            'user_name' => $_SESSION['user_name'],
            'pendingPayments' => $pendingPayments,
            'paymentHistory' => $paymentHistory,
            'overduePayments' => $overduePayments,
            'paymentStats' => $paymentStats,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('tenant/v_pay_rent', $data);
    }

    public function agreements()
    {
        $tenant_id = $_SESSION['user_id'];

        // Get all lease agreements for the tenant
        $leases = $this->leaseModel->getLeasesByTenant($tenant_id);
        $activeLease = $this->leaseModel->getActiveLeaseByTenant($tenant_id);
        $leaseStats = $this->leaseModel->getLeaseStats($tenant_id, 'tenant', 'all');

        $data = [
            'title' => 'Lease Agreements - TenantHub',
            'page' => 'agreements',
            'user_name' => $_SESSION['user_name'],
            'leases' => $leases,
            'activeLease' => $activeLease,
            'leaseStats' => $leaseStats,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('tenant/v_agreements', $data);
    }

    public function report_issue()
    {
        redirect('issues/report');
    }

    public function track_issues()
    {
        redirect('issues/track');
    }

    public function my_reviews()
    {
        $tenant_id = $_SESSION['user_id'];

        // Get all reviews by the tenant
        $myReviews = $this->reviewModel->getReviewsByReviewer($tenant_id);
        $reviewableBookings = $this->reviewModel->getReviewableBookings($tenant_id);

        // Get review stats using 'all' period
        $reviewStats = $this->reviewModel->getUserReviewStats($tenant_id, 'written', 'all');

        $data = [
            'title' => 'My Reviews - TenantHub',
            'page' => 'my_reviews',
            'user_name' => $_SESSION['user_name'],
            'myReviews' => $myReviews,
            'reviewableBookings' => $reviewableBookings,
            'reviewStats' => $reviewStats,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('tenant/v_my_reviews', $data);
    }

    public function notifications()
    {
        // Get all notifications for the tenant
        $notifications = $this->notificationModel->getNotificationsByUser($_SESSION['user_id']);
        $unreadCount = $this->notificationModel->getUnreadCount($_SESSION['user_id']);

        $data = [
            'title' => 'Notifications - TenantHub',
            'page' => 'notifications',
            'user_name' => $_SESSION['user_name'],
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('tenant/v_notifications', $data);
    }

    public function feedback()
    {
        $tenant_id = $_SESSION['user_id'];

        // Get reviews about the tenant (from landlords)
        $reviewsAboutMe = $this->reviewModel->getReviewsAboutUser($tenant_id);

        // Get feedback stats using 'all' period
        $feedbackStats = $this->reviewModel->getUserReviewStats($tenant_id, 'received', 'all');

        $data = [
            'title' => 'Landlord Reviews - TenantHub',
            'page' => 'feedback',
            'user_name' => $_SESSION['user_name'],
            'reviewsAboutMe' => $reviewsAboutMe,
            'feedbackStats' => $feedbackStats,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('tenant/v_feedback', $data);
    }

    public function settings()
    {
        // User settings page
        $userModel = $this->model('M_Users');
        $user = $userModel->getUserById($_SESSION['user_id']);

        $data = [
            'title' => 'Settings - TenantHub',
            'page' => 'settings',
            'user_name' => $_SESSION['user_name'],
            'user' => $user,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('tenant/v_settings', $data);
    }

    // Mark notification as read (AJAX endpoint)
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

        redirect('tenant/notifications');
    }

    // Delete notification
    public function deleteNotification($id)
    {
        if ($this->notificationModel->deleteNotification($id)) {
            flash('notification_message', 'Notification deleted', 'alert alert-success');
        } else {
            flash('notification_message', 'Failed to delete notification', 'alert alert-danger');
        }

        redirect('tenant/notifications');
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
        $tenant_id = $_SESSION['user_id'];

        // Validate period
        if (!in_array($period, ['all', 'month', 'year'])) {
            $period = 'all';
        }

        $value = 0;
        $subtitle = '';

        switch ($statType) {
            // Dashboard stats
            case 'tenant_total_bookings':
                $stats = $this->bookingModel->getBookingStats($tenant_id, 'tenant', $period);
                $value = $stats->total ?? 0;
                $subtitle = ($stats->active ?? 0) . ' active, ' . ($stats->pending ?? 0) . ' pending';
                break;

            case 'tenant_pending_payments':
                $stats = $this->paymentModel->getTenantPaymentStats($tenant_id, $period);
                $value = $stats->pending_count ?? 0;
                $subtitle = $value > 0 ? 'Action required' : 'All clear';
                break;

            case 'tenant_recent_issues':
                $stats = $this->issueModel->getIssueStats($tenant_id, 'tenant', $period);
                $value = $stats->total_issues ?? 0;
                $subtitle = ($stats->pending_count ?? 0) . ' pending';
                break;

            // Pay Rent stats
            case 'tenant_total_paid':
                $stats = $this->paymentModel->getTenantPaymentStats($tenant_id, $period);
                $value = 'LKR ' . number_format($stats->total_paid ?? 0, 0);
                $subtitle = ($stats->completed_count ?? 0) . ' payments';
                break;

            case 'tenant_payments_made':
                $stats = $this->paymentModel->getTenantPaymentStats($tenant_id, $period);
                $value = $stats->completed_count ?? 0;
                $subtitle = 'Completed payments';
                break;

            case 'tenant_payments_pending':
                $stats = $this->paymentModel->getTenantPaymentStats($tenant_id, $period);
                $value = $stats->pending_count ?? 0;
                $subtitle = 'LKR ' . number_format($stats->pending_amount ?? 0, 0);
                break;

            case 'tenant_payments_overdue':
                $stats = $this->paymentModel->getTenantPaymentStats($tenant_id, $period);
                $value = $stats->overdue_count ?? 0;
                $subtitle = $value > 0 ? 'Immediate attention' : 'Good standing';
                break;

            // Bookings stats
            case 'tenant_bookings_pending':
                $stats = $this->bookingModel->getBookingStats($tenant_id, 'tenant', $period);
                $value = $stats->pending ?? 0;
                $subtitle = 'Awaiting approval';
                break;

            case 'tenant_bookings_approved':
                $stats = $this->bookingModel->getBookingStats($tenant_id, 'tenant', $period);
                $value = $stats->approved ?? 0;
                $subtitle = 'Ready to proceed';
                break;

            case 'tenant_bookings_active':
                $stats = $this->bookingModel->getBookingStats($tenant_id, 'tenant', $period);
                $value = $stats->active ?? 0;
                $subtitle = 'Current rentals';
                break;

            case 'tenant_bookings_completed':
                $stats = $this->bookingModel->getBookingStats($tenant_id, 'tenant', $period);
                $value = $stats->completed ?? 0;
                $subtitle = 'Past bookings';
                break;

            // Agreements stats
            case 'tenant_agreements_total':
                $stats = $this->leaseModel->getLeaseStats($tenant_id, 'tenant', $period);
                $value = $stats->total ?? 0;
                $subtitle = 'All agreements';
                break;

            case 'tenant_agreements_active':
                $stats = $this->leaseModel->getLeaseStats($tenant_id, 'tenant', $period);
                $value = $stats->active ?? 0;
                $subtitle = 'Current leases';
                break;

            case 'tenant_agreements_completed':
                $stats = $this->leaseModel->getLeaseStats($tenant_id, 'tenant', $period);
                $value = $stats->completed ?? 0;
                $subtitle = 'Past agreements';
                break;

            // Reviews stats (My Reviews)
            case 'tenant_reviews_written':
                $stats = $this->reviewModel->getUserReviewStats($tenant_id, 'written', $period);
                $value = $stats->review_count ?? 0;
                $subtitle = 'Reviews submitted';
                break;

            case 'tenant_avg_rating_given':
                $stats = $this->reviewModel->getUserReviewStats($tenant_id, 'written', $period);
                $value = number_format($stats->avg_rating ?? 0, 1);
                $subtitle = 'Average rating given';
                break;

            // Feedback stats (Reviews about you)
            case 'tenant_reviews_received':
                $stats = $this->reviewModel->getUserReviewStats($tenant_id, 'received', $period);
                $value = $stats->review_count ?? 0;
                $subtitle = 'From landlords';
                break;

            case 'tenant_avg_rating_received':
                $stats = $this->reviewModel->getUserReviewStats($tenant_id, 'received', $period);
                $value = number_format($stats->avg_rating ?? 0, 1);
                $subtitle = 'Your reputation';
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
