<?php
require_once '../app/helpers/helper.php';

class Manager extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = $this->model('M_Users');
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'property_manager') {
            redirect('users/login');
        }
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $data = [
            'title' => 'Property Manager Dashboard',
            'page' => 'dashboard',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('manager/v_dashboard', $data);
    }

    public function properties()
    {
        redirect('ManagerProperties/index');
    }

    public function tenants()
    {
        $data = [
            'title' => 'Tenant Management',
            'page' => 'tenants',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('manager/v_tenants', $data);
    }

    public function maintenance()
    {
        $data = [
            'title' => 'Maintenance Management',
            'page' => 'maintenance',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('manager/v_maintenance', $data);
    }

    public function inspections()
    {
        redirect('inspections/index'); // route to inspection controller
    }

    public function issues()
    {
        $issueModel = $this->model('Issue');
        $allIssues = $issueModel->getAllIssues();

        $openIssues = array_filter($allIssues, fn($issue) => $issue->status === 'pending');
        $assignedIssues = array_filter($allIssues, fn($issue) => $issue->status === 'assigned');
        $inProgressIssues = array_filter($allIssues, fn($issue) => $issue->status === 'in_progress');
        $resolvedIssues = array_filter($allIssues, fn($issue) => $issue->status === 'resolved');

        $data = [
            'title' => 'Issue Tracking',
            'page' => 'issues',
            'user_name' => $_SESSION['user_name'],
            'allIssues' => $allIssues,
            'openIssues' => $openIssues,
            'assignedIssues' => $assignedIssues,
            'inProgressIssues' => $inProgressIssues,
            'resolvedIssues' => $resolvedIssues
        ];

        $this->view('manager/v_issues', $data);
    }

    public function leases()
    {
        $data = [
            'title' => 'Lease Agreements',
            'page' => 'leases',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('manager/v_leases', $data);
    }

    public function providers()
    {
        $data = [
            'title' => 'Service Providers',
            'page' => 'providers',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('manager/v_providers', $data);
    }
}
