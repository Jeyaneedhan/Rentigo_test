<?php
require_once '../app/helpers/helper.php';

class Maintenance extends Controller
{
    // private $maintenanceModel;
    private $propertyModel;

    public function __construct()
    {
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'landlord') {
            redirect('users/login');
        }

        // $this->maintenanceModel = $this->model('M_Maintenance');
        $this->propertyModel = $this->model('M_Properties');
    }

    public function index()
    {
        $data = [
            'title' => 'Maintenance Requests',
            'page' => 'maintenance',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_maintenance', $data);
    }

    // Show new maintenance request form
    public function create()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }

        $data = [
            'title' => 'New Maintenance Request - Rentigo',
            'property_id' => '',
            'request_title' => '',
            'category' => '',
            'priority' => 'medium',
            'description' => '',
            'tenant_name' => '',
            'tenant_contact' => '',
            'access_instructions' => '',
            'property_err' => '',
            'title_err' => '',
            'category_err' => '',
            'priority_err' => '',
            'description_err' => ''
        ];

        $this->view('landlord/v_new_maintenance_request', $data);
    }
}
