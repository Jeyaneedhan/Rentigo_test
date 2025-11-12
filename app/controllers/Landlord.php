<?php
require_once '../app/helpers/helper.php';

class Landlord extends Controller
{
    private $userModel;

    public function __construct()
    {
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'landlord') {
            redirect('users/login');
        }

        $this->userModel = $this->model('M_Users');
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $data = [
            'title' => 'Landlord Dashboard',
            'page' => 'dashboard',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_dashboard', $data);
    }

    public function properties()
    {
        redirect('properties/index');
    }

    public function maintenance()
    {
        redirect('maintenance/index');
    }

    public function inquiries()
    {
        $data = [
            'title' => 'Tenant Inquiries',
            'page' => 'inquiries',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_inquiries', $data);
    }

    public function payment_history()
    {
        $data = [
            'title' => 'Payment History',
            'page' => 'payment_history',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_payment_history', $data);
    }

    public function feedback()
    {
        $data = [
            'title' => 'Tenant Feedback',
            'page' => 'feedback',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_feedback', $data);
    }

    public function notifications()
    {
        $data = [
            'title' => 'Notifications',
            'page' => 'notifications',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_notifications', $data);
    }

    public function settings()
    {
        $data = [
            'title' => 'Settings',
            'page' => 'settings',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_settings', $data);
    }

    public function income()
    {
        $data = [
            'title' => 'Income Reports',
            'page' => 'income',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_income', $data);
    }
}
