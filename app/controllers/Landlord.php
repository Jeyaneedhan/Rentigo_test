<?php
require_once '../app/helpers/helper.php';

// Landlord controller - handles all landlord-specific pages and actions
// Only logged-in landlords can access these methods
class Landlord extends Controller
{
    // Store all the models we'll need throughout this controller
    private $userModel;
    private $bookingModel;
    private $paymentModel;
    private $leaseModel;
    private $propertyModel;
    private $maintenanceModel;
    private $notificationModel;
    private $reviewModel;

    public function __construct()
    {
        // Security check - only landlords can access this controller
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'landlord') {
            redirect('users/login');
        }

        // Load all the models we'll need
        $this->userModel = $this->model('M_Users');
        $this->bookingModel = $this->model('M_Bookings');
        $this->paymentModel = $this->model('M_Payments');
        $this->leaseModel = $this->model('M_LeaseAgreements');
        $this->propertyModel = $this->model('M_Properties');
        $this->maintenanceModel = $this->model('M_Maintenance');
        $this->notificationModel = $this->model('M_Notifications');
        $this->reviewModel = $this->model('M_Reviews');
    }

    // Helper method to get unread notification count
    // We use this on every page to show the notification badge
    private function getUnreadNotificationCount()
    {
        return $this->notificationModel->getUnreadCount($_SESSION['user_id']);
    }

    // Default landing page - just redirects to dashboard
    public function index()
    {
        $this->dashboard();
    }

    // Main dashboard - shows overview of landlord's properties and income
    public function dashboard()
    {
        // Get all the stats we need for the dashboard
        // Most of these are already filtered to last 30 days in the models
        $propertyStats = $this->propertyModel->getPropertyStatsByLandlord($_SESSION['user_id']);
        $bookingStats = $this->bookingModel->getBookingStats($_SESSION['user_id'], 'landlord');
        $pendingBookings = $this->bookingModel->getPendingBookingsCount($_SESSION['user_id']);
        $totalIncome = $this->paymentModel->getTotalIncomeByLandlord($_SESSION['user_id']);
        $activeLeases = $this->leaseModel->getActiveLeasesCount($_SESSION['user_id']);
        $pendingMaintenance = $this->maintenanceModel->getPendingMaintenanceCount($_SESSION['user_id']);

        // Get recent bookings and filter to last 30 days
        $allRecentBookings = $this->bookingModel->getBookingsByLandlord($_SESSION['user_id']);
        $recentBookings = array_filter($allRecentBookings, function ($b) {
            return strtotime($b->created_at ?? '') >= strtotime('-30 days');
        });

        // Get recent payments and filter to last 30 days
        $allRecentPayments = $this->paymentModel->getRecentPayments($_SESSION['user_id'], 'landlord', 10);
        $recentPayments = array_filter($allRecentPayments, function ($p) {
            $date = $p->payment_date ?? $p->created_at;
            return strtotime($date) >= strtotime('-30 days');
        });

        $unreadNotifications = $this->notificationModel->getUnreadCount($_SESSION['user_id']);

        // Get issue/inquiry statistics
        $issueModel = $this->model('M_Issue');
        $issueStats = $issueModel->getIssueStats($_SESSION['user_id'], 'landlord');

        // Limit recent bookings to 5 for the dashboard
        if (count($recentBookings) > 5) {
            $recentBookings = array_slice($recentBookings, 0, 5);
        }

        // Limit recent payments to 5 for the dashboard
        if (count($recentPayments) > 5) {
            $recentPayments = array_slice($recentPayments, 0, 5);
        }

        $data = [
            'title' => 'Landlord Dashboard',
            'page' => 'dashboard',
            'user_name' => $_SESSION['user_name'],
            'propertyStats' => $propertyStats,
            'bookingStats' => $bookingStats,
            'pendingBookings' => $pendingBookings,
            'totalIncome' => $totalIncome,
            'activeLeases' => $activeLeases,
            'pendingMaintenance' => $pendingMaintenance,
            'recentBookings' => $recentBookings,
            'recentPayments' => $recentPayments,
            'unreadNotifications' => $unreadNotifications,
            'issueStats' => $issueStats,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('landlord/v_dashboard', $data);
    }

    // Redirect to properties management page
    public function properties()
    {
        redirect('properties/index');
    }

    // Redirect to maintenance requests page
    public function maintenance()
    {
        redirect('maintenance/index');
    }

    // Show all bookings for this landlord's properties
    // Includes filtering options
    public function bookings()
    {
        // Get all bookings for landlord's properties
        $allBookings = $this->bookingModel->getBookingsByLandlord($_SESSION['user_id']);
        $bookingStats = $this->bookingModel->getBookingStats($_SESSION['user_id'], 'landlord');

        // ==================== APPLY FILTERS ====================
        $filteredBookings = $allBookings;

        // Get filter parameters
        $filterStatus = $_GET['filter_status'] ?? '';
        $filterDateFrom = $_GET['filter_date_from'] ?? '';
        $filterDateTo = $_GET['filter_date_to'] ?? '';

        // Apply Status Filter
        if (!empty($filterStatus)) {
            $filteredBookings = array_filter($filteredBookings, function ($booking) use ($filterStatus) {
                return strtolower($booking->status) === strtolower($filterStatus);
            });
        }

        // Apply Date Range Filter (on move_in_date)
        if (!empty($filterDateFrom) || !empty($filterDateTo)) {
            $filteredBookings = array_filter($filteredBookings, function ($booking) use ($filterDateFrom, $filterDateTo) {
                $moveInDate = strtotime($booking->move_in_date);

                // Check FROM date
                if (!empty($filterDateFrom)) {
                    $fromDate = strtotime($filterDateFrom);
                    if ($moveInDate < $fromDate) {
                        return false;
                    }
                }

                // Check TO date
                if (!empty($filterDateTo)) {
                    $toDate = strtotime($filterDateTo . ' 23:59:59'); // End of day
                    if ($moveInDate > $toDate) {
                        return false;
                    }
                }

                return true;
            });
        }

        // Re-index array after filtering
        $filteredBookings = array_values($filteredBookings);

        $data = [
            'title' => 'Property Bookings',
            'page' => 'bookings',
            'user_name' => $_SESSION['user_name'],
            'bookings' => $filteredBookings,
            'bookingStats' => $bookingStats,
            'unread_notifications' => $this->getUnreadNotificationCount(),

            // Pass filter values back to view for persistence
            'filter_status' => $filterStatus,
            'filter_date_from' => $filterDateFrom,
            'filter_date_to' => $filterDateTo,
        ];
        $this->view('landlord/v_bookings', $data);
    }

    public function inquiries()
    {
        $issueModel = $this->model('M_Issue');
        $landlord_id = $_SESSION['user_id'];

        // Get all issues for this landlord's properties
        $allIssues = $issueModel->getIssuesByLandlord($landlord_id);

        // Filter by status
        $pendingIssues = array_filter($allIssues, fn($issue) => $issue->status === 'pending');
        $inProgressIssues = array_filter($allIssues, fn($issue) => $issue->status === 'in_progress');
        $resolvedIssues = array_filter($allIssues, fn($issue) => $issue->status === 'resolved');

        // Get issue statistics
        $stats = $issueModel->getIssueStats($landlord_id, 'landlord');

        $data = [
            'title' => 'Tenant Inquiries',
            'page' => 'inquiries',
            'user_name' => $_SESSION['user_name'],
            'allIssues' => $allIssues,
            'pendingIssues' => $pendingIssues,
            'inProgressIssues' => $inProgressIssues,
            'resolvedIssues' => $resolvedIssues,
            'issueStats' => $stats,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];

        $this->view('landlord/v_inquiries', $data);
    }

    public function payment_history()
    {
        // Get all payments for landlord (Full history for table)
        $payments = $this->paymentModel->getPaymentsByLandlord($_SESSION['user_id']);

        // Get summary statistics
        $totalIncome = $this->paymentModel->getTotalIncomeByLandlord($_SESSION['user_id']);
        $paymentStatsAll = $this->paymentModel->getPaymentStatsByLandlord($_SESSION['user_id']); // For stat cards
        $paymentStats = $this->paymentModel->getPaymentStatsByLandlordMonthly($_SESSION['user_id']); // For monthly table

        // Filter payments for 30-day stat cards
        $recentPayments = array_filter($payments, function ($p) {
            $date = $p->payment_date ?? $p->created_at;
            return strtotime($date) >= strtotime('-30 days');
        });

        $data = [
            'title' => 'Payment History',
            'page' => 'payment_history',
            'user_name' => $_SESSION['user_name'],
            'payments' => $payments, // Full history
            'recentPayments' => $recentPayments, // 30-day filtered for cards
            'totalIncome' => $totalIncome,
            'paymentStatsAll' => $paymentStatsAll, // Single object for stat cards
            'paymentStats' => $paymentStats, // Monthly breakdown for table
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('landlord/v_payment_history', $data);
    }

    public function feedback()
    {
        // Get all reviews about landlord's properties
        $myReviews = $this->reviewModel->getReviewsByReviewer($_SESSION['user_id']);
        $reviewsAboutMe = $this->reviewModel->getReviewsAboutUser($_SESSION['user_id'], 'tenant');

        $data = [
            'title' => 'Tenant Feedback',
            'page' => 'feedback',
            'user_name' => $_SESSION['user_name'],
            'myReviews' => $myReviews,
            'reviewsAboutMe' => $reviewsAboutMe,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('landlord/v_feedback', $data);
    }

    public function notifications()
    {
        // Get all notifications for the landlord
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
        $this->view('landlord/v_notifications', $data);
    }

    public function settings()
    {
        // Get user details
        $user = $this->userModel->getUserById($_SESSION['user_id']);

        $data = [
            'title' => 'Settings',
            'page' => 'settings',
            'user_name' => $_SESSION['user_name'],
            'user' => $user,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('landlord/v_settings', $data);
    }

    public function income()
    {
        // Get income statistics and reports
        $totalIncome = $this->paymentModel->getTotalIncomeByLandlord($_SESSION['user_id']);
        $paymentStats = $this->paymentModel->getPaymentStatsByLandlord($_SESSION['user_id']);
        $payments = $this->paymentModel->getPaymentsByLandlord($_SESSION['user_id']);
        $maintenanceStats = $this->maintenanceModel->getMaintenanceStats($_SESSION['user_id']);

        // Calculate monthly income
        $monthlyIncome = [];
        if ($paymentStats) {
            foreach ($paymentStats as $stat) {
                $key = $stat->year . '-' . str_pad($stat->month, 2, '0', STR_PAD_LEFT);
                $monthlyIncome[$key] = $stat->completed_amount;
            }
        }

        $data = [
            'title' => 'Income Reports',
            'page' => 'income',
            'user_name' => $_SESSION['user_name'],
            'totalIncome' => $totalIncome,
            'paymentStats' => $paymentStats,
            'payments' => $payments,
            'maintenanceStats' => $maintenanceStats,
            'monthlyIncome' => $monthlyIncome,
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('landlord/v_income', $data);
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

        redirect('landlord/notifications');
    }

    // Delete notification
    public function deleteNotification($id)
    {
        if ($this->notificationModel->deleteNotification($id)) {
            flash('notification_message', 'Notification deleted', 'alert alert-success');
        } else {
            flash('notification_message', 'Failed to delete notification', 'alert alert-danger');
        }

        redirect('landlord/notifications');
    }

    // View inquiry/issue details
    public function issueDetails($id = null)
    {
        if (!$id) {
            flash('issue_error', 'Issue not found', 'alert alert-danger');
            redirect('landlord/inquiries');
        }

        $issueModel = $this->model('M_Issue');
        $issue = $issueModel->getIssueById($id);

        if (!$issue) {
            flash('issue_error', 'Issue not found', 'alert alert-danger');
            redirect('landlord/inquiries');
        }

        // Verify this landlord owns the property
        if ($issue->landlord_id != $_SESSION['user_id']) {
            flash('issue_error', 'Unauthorized access', 'alert alert-danger');
            redirect('landlord/inquiries');
        }

        $data = [
            'title' => 'Inquiry Details',
            'page' => 'inquiries',
            'user_name' => $_SESSION['user_name'],
            'issue' => $issue
        ];

        $this->view('landlord/v_issue_details', $data);
    }

    /**
     * AJAX endpoint for fetching filtered stat card data
     * Called when user selects a period from stat card dropdown
     * Accepts POST with: stat_type and period (all|month|year)
     * Supports stat types from all landlord pages:
     * - Dashboard: properties, leases, income, maintenance
     * - Bookings: booking_total, booking_pending, booking_approved, booking_active
     * - Maintenance: maint_pending, maint_in_progress, maint_completed, maint_total_cost
     * - Payment History: payment_income, payment_completed, payment_pending
     * - Inquiries: inquiry_total, inquiry_pending, inquiry_in_progress, inquiry_resolved
     * - Income: income_total, income_maintenance, income_net
     * - Feedback: feedback_about_me, feedback_rating, feedback_my_reviews
     * - Notifications: notif_total, notif_unread, notif_read
     */
    public function getStatData()
    {
        // Only accept POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $statType = $input['stat_type'] ?? '';
        $period = $input['period'] ?? 'all';

        // Validate period
        if (!in_array($period, ['all', 'month', 'year'])) {
            $period = 'all';
        }

        // Validate stat type and fetch data
        $value = 0;
        $formatted = '0';
        $subtitle = '';
        $periodLabel = $period === 'month' ? 'This month' : ($period === 'year' ? 'This year' : 'All time');

        switch ($statType) {
            // === DASHBOARD STATS ===
            case 'properties':
                $stats = $this->propertyModel->getPropertyStatsByLandlord($_SESSION['user_id'], $period);
                $value = $stats->total_properties ?? 0;
                $formatted = (string) $value;
                $subtitle = ($stats->active_properties ?? 0) . ' active listings';
                break;

            case 'leases':
                $value = $this->leaseModel->getActiveLeasesCount($_SESSION['user_id'], $period);
                $formatted = (string) $value;
                $subtitle = 'Currently occupied';
                break;

            case 'income':
                $result = $this->paymentModel->getTotalIncomeByLandlord($_SESSION['user_id'], $period);
                $value = $result->total_income ?? 0;
                $formatted = 'LKR ' . number_format($value, 0);
                $subtitle = $periodLabel . ' earnings';
                break;

            case 'maintenance':
                $value = $this->maintenanceModel->getPendingMaintenanceCount($_SESSION['user_id'], null, $period);
                $formatted = (string) $value;
                $subtitle = 'Pending requests';
                break;

            // === BOOKINGS PAGE STATS ===
            case 'booking_total':
                $stats = $this->bookingModel->getBookingStats($_SESSION['user_id'], 'landlord', $period);
                $value = $stats->total ?? 0;
                $formatted = (string) $value;
                $subtitle = $periodLabel;
                break;

            case 'booking_pending':
                $stats = $this->bookingModel->getBookingStats($_SESSION['user_id'], 'landlord', $period);
                $value = $stats->pending ?? 0;
                $formatted = (string) $value;
                $subtitle = 'Awaiting response';
                break;

            case 'booking_approved':
                $stats = $this->bookingModel->getBookingStats($_SESSION['user_id'], 'landlord', $period);
                $value = $stats->approved ?? 0;
                $formatted = (string) $value;
                $subtitle = 'Accepted bookings';
                break;

            case 'booking_active':
                $stats = $this->bookingModel->getBookingStats($_SESSION['user_id'], 'landlord', $period);
                $value = $stats->active ?? 0;
                $formatted = (string) $value;
                $subtitle = 'Currently occupied';
                break;

            // === MAINTENANCE PAGE STATS ===
            case 'maint_pending':
                $stats = $this->maintenanceModel->getMaintenanceStats($_SESSION['user_id'], null, $period);
                $value = $stats->pending ?? 0;
                $formatted = (string) $value;
                $subtitle = 'Awaiting action';
                break;

            case 'maint_in_progress':
                $stats = $this->maintenanceModel->getMaintenanceStats($_SESSION['user_id'], null, $period);
                $value = $stats->in_progress ?? 0;
                $formatted = (string) $value;
                $subtitle = 'Being worked on';
                break;

            case 'maint_completed':
                $stats = $this->maintenanceModel->getMaintenanceStats($_SESSION['user_id'], null, $period);
                $value = $stats->completed ?? 0;
                $formatted = (string) $value;
                $subtitle = 'Total completed';
                break;

            case 'maint_total_cost':
                $stats = $this->maintenanceModel->getMaintenanceStats($_SESSION['user_id'], null, $period);
                $value = $stats->total_cost ?? 0;
                $formatted = 'LKR ' . number_format($value, 0);
                $subtitle = 'Maintenance expenses';
                break;

            // === PAYMENT HISTORY PAGE STATS ===
            case 'payment_income':
                $result = $this->paymentModel->getTotalIncomeByLandlord($_SESSION['user_id'], $period);
                $value = $result->total_income ?? 0;
                $formatted = 'LKR ' . number_format($value, 0);
                $subtitle = $periodLabel;
                break;

            case 'payment_completed':
                $stats = $this->paymentModel->getPaymentStatsByLandlord($_SESSION['user_id'], $period);
                $value = $stats->completed_count ?? 0;
                $formatted = (string) $value;
                $subtitle = 'LKR ' . number_format($stats->completed_amount ?? 0, 0) . ' (' . $periodLabel . ')';
                break;

            case 'payment_pending':
                $stats = $this->paymentModel->getPaymentStatsByLandlord($_SESSION['user_id'], $period);
                $value = $stats->pending_count ?? 0;
                $formatted = (string) $value;
                $subtitle = 'LKR ' . number_format($stats->pending_amount ?? 0, 0) . ' (' . $periodLabel . ')';
                break;

            case 'payment_overdue':
                $stats = $this->paymentModel->getPaymentStatsByLandlord($_SESSION['user_id'], $period);
                $value = $stats->overdue_count ?? 0;
                $formatted = (string) $value;
                $subtitle = 'LKR ' . number_format($stats->overdue_amount ?? 0, 0) . ' (' . $periodLabel . ')';
                break;

            // === INQUIRIES PAGE STATS ===
            case 'inquiry_total':
                $issueModel = $this->model('M_Issue');
                $stats = $issueModel->getIssueStats($_SESSION['user_id'], 'landlord', $period);
                $value = $stats->total_issues ?? 0;
                $formatted = (string) $value;
                $subtitle = $periodLabel;
                break;

            case 'inquiry_pending':
                $issueModel = $this->model('M_Issue');
                $stats = $issueModel->getIssueStats($_SESSION['user_id'], 'landlord', $period);
                $value = $stats->pending_count ?? 0;
                $formatted = (string) $value;
                $subtitle = 'Awaiting action';
                break;

            case 'inquiry_in_progress':
                $issueModel = $this->model('M_Issue');
                $stats = $issueModel->getIssueStats($_SESSION['user_id'], 'landlord', $period);
                $value = $stats->in_progress_count ?? 0;
                $formatted = (string) $value;
                $subtitle = 'Being worked on';
                break;

            case 'inquiry_resolved':
                $issueModel = $this->model('M_Issue');
                $stats = $issueModel->getIssueStats($_SESSION['user_id'], 'landlord', $period);
                $value = $stats->resolved_count ?? 0;
                $formatted = (string) $value;
                $subtitle = 'Completed';
                break;

            // === INCOME PAGE STATS ===
            case 'income_total':
                $result = $this->paymentModel->getTotalIncomeByLandlord($_SESSION['user_id'], $period);
                $value = $result->total_income ?? 0;
                $formatted = 'LKR ' . number_format($value, 0);
                $subtitle = $periodLabel . ' earnings';
                break;

            case 'income_maintenance':
                $stats = $this->maintenanceModel->getMaintenanceStats($_SESSION['user_id'], null, $period);
                $value = ($stats->total_cost ?? 0);
                $formatted = 'LKR ' . number_format($value, 0);
                $subtitle = 'Total expenses';
                break;

            case 'income_net':
                $incomeResult = $this->paymentModel->getTotalIncomeByLandlord($_SESSION['user_id'], $period);
                $maintenanceStats = $this->maintenanceModel->getMaintenanceStats($_SESSION['user_id'], null, $period);
                $income = $incomeResult->total_income ?? 0;
                $costs = $maintenanceStats->total_cost ?? 0;
                $value = $income - $costs;
                $formatted = 'LKR ' . number_format($value, 0);
                $subtitle = 'After expenses (' . $periodLabel . ')';
                break;

            // === FEEDBACK PAGE STATS ===
            case 'feedback_about_me':
                $reviews = $this->reviewModel->getReviewsAboutUser($_SESSION['user_id'], $period);
                $value = count($reviews);
                $formatted = (string) $value;
                $subtitle = 'From tenants';
                break;

            case 'feedback_rating':
                $reviews = $this->reviewModel->getReviewsAboutUser($_SESSION['user_id'], $period);
                if (!empty($reviews)) {
                    $totalRating = 0;
                    foreach ($reviews as $review) {
                        $totalRating += $review->rating;
                    }
                    $value = $totalRating / count($reviews);
                    $formatted = number_format($value, 1);
                } else {
                    $formatted = '0.0';
                }
                $subtitle = 'Tenant ratings';
                break;

            case 'feedback_my_reviews':
                $reviews = $this->reviewModel->getReviewsByUser($_SESSION['user_id'], $period);
                $value = count($reviews);
                $formatted = (string) $value;
                $subtitle = 'Reviews written';
                break;

            // === NOTIFICATIONS PAGE STATS ===
            case 'notif_total':
                $notifications = $this->notificationModel->getNotificationsByUser($_SESSION['user_id'], $period);
                $value = count($notifications);
                $formatted = (string) $value;
                $subtitle = $periodLabel;
                break;

            case 'notif_unread':
                $count = $this->notificationModel->getUnreadCountByPeriod($_SESSION['user_id'], $period);
                $value = $count;
                $formatted = (string) $value;
                $subtitle = 'Requires attention';
                break;

            case 'notif_read':
                $total = $this->notificationModel->getNotificationsByUser($_SESSION['user_id'], $period);
                $unread = $this->notificationModel->getUnreadCountByPeriod($_SESSION['user_id'], $period);
                $value = count($total) - $unread;
                $formatted = (string) $value;
                $subtitle = 'Acknowledged';
                break;

            default:
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid stat type']);
                return;
        }

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'value' => $value,
            'formatted' => $formatted,
            'subtitle' => $subtitle
        ]);
    }
}
