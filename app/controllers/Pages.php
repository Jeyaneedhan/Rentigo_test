<?php

class Pages extends Controller
{
    private $pagesModel; // Added -> ERROR: Undefined property '$pagesModel'.intelephense(P1014)
    private $policyModel;

    public function __construct()
    {
        $this->pagesModel = $this->model('M_Pages');
        $this->policyModel = $this->model('M_Policies');
    }

    public function index()
    {
        $data = [];

        $this->view('pages/v_index', $data);
    }

    public function about()
    {
        $users = $this->pagesModel->getUsers();

        $data = [
            'users' => $users
        ];

        $this->view('v_about', $data);
    }

    public function terms()
    {
        // Get latest active Terms of Service policy
        $policy = $this->policyModel->getLatestActivePolicyByCategory('terms_of_service');

        $data = [
            'title' => 'Terms and Conditions - Rentigo',
            'policy' => $policy
        ];

        $this->view('pages/v_terms', $data);
    }

    public function privacy()
    {
        // Get latest active Privacy Policy
        $policy = $this->policyModel->getLatestActivePolicyByCategory('privacy');

        $data = [
            'title' => 'Privacy Policy - Rentigo',
            'policy' => $policy
        ];

        $this->view('pages/v_privacy', $data);
    }
}
