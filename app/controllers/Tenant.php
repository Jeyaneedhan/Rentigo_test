<?php
require_once '../app/helpers/helper.php';

class Tenant extends Controller
{
    public function __construct()
    {
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'tenant') {
            redirect('users/login');
        }
    }

    // Main dashboard page
    public function index()
    {
        $data = [
            'title' => 'Tenant Dashboard - TenantHub',
            'page' => 'dashboard',
            'user_name' => $_SESSION['user_name']
        ];

        $this->view('tenant/v_dashboard', $data);
    }

    public function search_properties()
    {
        redirect('tenantproperties/index');
    }

    public function bookings()
    {
        $data = [
            'title' => 'My Bookings - TenantHub',
            'page' => 'bookings',
            'user_name' => $_SESSION['user_name']
        ];

        $this->view('tenant/v_bookings', $data);
    }

    public function pay_rent()
    {
        $data = [
            'title' => 'Pay Rent - TenantHub',
            'page' => 'pay_rent',
            'user_name' => $_SESSION['user_name']
        ];

        $this->view('tenant/v_pay_rent', $data);
    }

    public function agreements()
    {
        $data = [
            'title' => 'Lease Agreements - TenantHub',
            'page' => 'agreements',
            'user_name' => $_SESSION['user_name']
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
        $data = [
            'title' => 'My Reviews - TenantHub',
            'page' => 'my_reviews',
            'user_name' => $_SESSION['user_name']
        ];

        $this->view('tenant/v_my_reviews', $data);
    }

    public function notifications()
    {
        $data = [
            'title' => 'Notifications - TenantHub',
            'page' => 'notifications',
            'user_name' => $_SESSION['user_name']
        ];

        $this->view('tenant/v_notifications', $data);
    }

    public function feedback()
    {
        $data = [
            'title' => 'Feedback - TenantHub',
            'page' => 'feedback',
            'user_name' => $_SESSION['user_name']
        ];

        $this->view('tenant/v_feedback', $data);
    }
}
