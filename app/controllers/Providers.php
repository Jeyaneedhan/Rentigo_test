<?php
require_once '../app/helpers/helper.php';

class Providers extends Controller
{
    private $serviceProviderModel;

    public function __construct()
    {
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
            redirect('users/login');
        }
        $this->serviceProviderModel = $this->model('M_ServiceProviders');
    }

    // READ - Service providers page
    public function index()
    {
        $searchTerm = $_GET['search'] ?? '';
        $specialty = $_GET['specialty'] ?? '';
        $status = $_GET['status'] ?? '';

        if (!empty($searchTerm) || !empty($specialty) || !empty($status)) {
            $providers = $this->serviceProviderModel->searchProviders($searchTerm, $specialty, $status);
        } else {
            $providers = $this->serviceProviderModel->getAllProviders();
        }

        $counts = $this->serviceProviderModel->getProviderCounts();

        $data = [
            'title' => 'Service Providers - Rentigo Admin',
            'page' => 'providers',
            'providers' => $providers,
            'counts' => $counts,
            'search' => $searchTerm,
            'specialty_filter' => $specialty,
            'status_filter' => $status
        ];

        $this->view('admin/v_providers', $data);
    }

    // CREATE - Add new service provider
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'company' => trim($_POST['company'] ?? ''),
                'specialty' => $_POST['specialty'] ?? '',
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'rating' => $_POST['rating'] ?? 0.0,
                'status' => $_POST['status'] ?? 'active'
            ];

            // Create validator
            $validator = new Validator();

            // Validate name
            if ($validator->required('name', $data['name'], 'Provider name is required')) {
                $validator->minLength('name', $data['name'], 2, 'Name must be at least 2 characters');
                $validator->maxLength('name', $data['name'], 100, 'Name must not exceed 100 characters');
            }

            // Validate company (optional but has length constraint if provided)
            if (!empty($data['company'])) {
                $validator->minLength('company', $data['company'], 2, 'Company name must be at least 2 characters');
                $validator->maxLength('company', $data['company'], 100, 'Company name must not exceed 100 characters');
            }

            // Validate specialty
            $validSpecialties = ['plumbing', 'electrical', 'hvac', 'carpentry', 'painting', 'cleaning', 'landscaping', 'pest_control', 'general'];
            if ($validator->required('specialty', $data['specialty'], 'Please select a specialty')) {
                $validator->inArray('specialty', $data['specialty'], $validSpecialties, 'Please select a valid specialty');
            }

            // Validate phone
            if ($validator->required('phone', $data['phone'], 'Phone number is required')) {
                $validator->minLength('phone', $data['phone'], 10, 'Phone number must be at least 10 digits');
                $validator->maxLength('phone', $data['phone'], 15, 'Phone number must not exceed 15 digits');
                // Custom: Phone should contain only numbers, spaces, +, -, ()
                $validator->custom(
                    'phone',
                    preg_match('/^[0-9\s\+\-\(\)]+$/', $data['phone']),
                    'Phone number contains invalid characters'
                );

                //  Check if phone already exists
                if ($this->serviceProviderModel->phoneExists($data['phone'])) {
                    $validator->custom('phone', false, 'This phone number is already registered');
                }
            }

            // Validate email
            if ($validator->required('email', $data['email'], 'Email is required')) {
                if ($validator->email('email', $data['email'], 'Please enter a valid email address')) {
                    //  Check if email already exists
                    if ($this->serviceProviderModel->emailExists($data['email'])) {
                        $validator->custom('email', false, 'This email is already registered');
                    }
                }
            }

            // Validate address (optional but has length constraint if provided)
            if (!empty($data['address'])) {
                $validator->minLength('address', $data['address'], 5, 'Address must be at least 5 characters');
                $validator->maxLength('address', $data['address'], 255, 'Address must not exceed 255 characters');
            }

            // Validate rating
            if (!empty($data['rating'])) {
                $validator->custom(
                    'rating',
                    is_numeric($data['rating']) && $data['rating'] >= 0 && $data['rating'] <= 5,
                    'Rating must be a number between 0 and 5'
                );
            }

            // Validate status
            $validStatuses = ['active', 'inactive', 'suspended'];
            if (!empty($data['status'])) {
                $validator->inArray('status', $data['status'], $validStatuses, 'Please select a valid status');
            }

            // Check validation
            if (!$validator->hasErrors()) {
                if ($this->serviceProviderModel->create($data)) {
                    flash('provider_message', 'Service provider added successfully!', 'alert alert-success');
                    redirect('providers/index');
                } else {
                    flash('provider_message', 'Failed to add service provider. Please try again.', 'alert alert-danger');
                    redirect('providers/add');
                }
            } else {
                // Map errors to data array
                $errors = $validator->getErrors();
                $data['name_err'] = $errors['name'] ?? '';
                $data['company_err'] = $errors['company'] ?? '';
                $data['specialty_err'] = $errors['specialty'] ?? '';
                $data['phone_err'] = $errors['phone'] ?? '';
                $data['email_err'] = $errors['email'] ?? '';
                $data['address_err'] = $errors['address'] ?? '';
                $data['rating_err'] = $errors['rating'] ?? '';
                $data['status_err'] = $errors['status'] ?? '';
                $data['title'] = 'Add Service Provider - Rentigo Admin';
                $data['page'] = 'providers';

                $this->view('admin/v_add_provider', $data);
            }
        } else {
            // GET request
            $data = [
                'title' => 'Add Service Provider - Rentigo Admin',
                'page' => 'providers',
                'name' => '',
                'company' => '',
                'specialty' => '',
                'phone' => '',
                'email' => '',
                'address' => '',
                'rating' => 0.0,
                'status' => 'active',
                'name_err' => '',
                'company_err' => '',
                'specialty_err' => '',
                'phone_err' => '',
                'email_err' => '',
                'address_err' => '',
                'rating_err' => '',
                'status_err' => ''
            ];
            $this->view('admin/v_add_provider', $data);
        }
    }

    // UPDATE - Edit service provider
    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'id' => $id,
                'name' => trim($_POST['name'] ?? ''),
                'company' => trim($_POST['company'] ?? ''),
                'specialty' => $_POST['specialty'] ?? '',
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'rating' => $_POST['rating'] ?? 0.0,
                'status' => $_POST['status'] ?? 'active'
            ];

            // Create validator
            $validator = new Validator();

            // Validate name
            if ($validator->required('name', $data['name'], 'Provider name is required')) {
                $validator->minLength('name', $data['name'], 2, 'Name must be at least 2 characters');
                $validator->maxLength('name', $data['name'], 100, 'Name must not exceed 100 characters');
            }

            // Validate company (optional but has length constraint if provided)
            if (!empty($data['company'])) {
                $validator->minLength('company', $data['company'], 2, 'Company name must be at least 2 characters');
                $validator->maxLength('company', $data['company'], 100, 'Company name must not exceed 100 characters');
            }

            // Validate specialty
            $validSpecialties = ['plumbing', 'electrical', 'hvac', 'carpentry', 'painting', 'cleaning', 'landscaping', 'pest_control', 'general'];
            if ($validator->required('specialty', $data['specialty'], 'Please select a specialty')) {
                $validator->inArray('specialty', $data['specialty'], $validSpecialties, 'Please select a valid specialty');
            }

            // Validate phone
            if ($validator->required('phone', $data['phone'], 'Phone number is required')) {
                $validator->minLength('phone', $data['phone'], 10, 'Phone number must be at least 10 digits');
                $validator->maxLength('phone', $data['phone'], 15, 'Phone number must not exceed 15 digits');
                $validator->custom(
                    'phone',
                    preg_match('/^[0-9\s\+\-\(\)]+$/', $data['phone']),
                    'Phone number contains invalid characters'
                );

                //  FIXED: Check if phone exists for other providers (exclude current provider)
                if ($this->serviceProviderModel->phoneExists($data['phone'], $id)) {
                    $validator->custom('phone', false, 'This phone number is already in use by another provider');
                }
            }

            // Validate email
            if ($validator->required('email', $data['email'], 'Email is required')) {
                if ($validator->email('email', $data['email'], 'Please enter a valid email address')) {
                    //  FIXED: Check if email exists for other providers (exclude current provider)
                    if ($this->serviceProviderModel->emailExists($data['email'], $id)) {
                        $validator->custom('email', false, 'This email is already in use by another provider');
                    }
                }
            }

            // Validate address (optional)
            if (!empty($data['address'])) {
                $validator->minLength('address', $data['address'], 5, 'Address must be at least 5 characters');
                $validator->maxLength('address', $data['address'], 255, 'Address must not exceed 255 characters');
            }

            // Validate rating
            if (!empty($data['rating'])) {
                $validator->custom(
                    'rating',
                    is_numeric($data['rating']) && $data['rating'] >= 0 && $data['rating'] <= 5,
                    'Rating must be a number between 0 and 5'
                );
            }

            // Validate status
            $validStatuses = ['active', 'inactive', 'suspended'];
            if (!empty($data['status'])) {
                $validator->inArray('status', $data['status'], $validStatuses, 'Please select a valid status');
            }

            // Check validation
            if (!$validator->hasErrors()) {
                if ($this->serviceProviderModel->update($data)) {
                    flash('provider_message', 'Service provider updated successfully!', 'alert alert-success');
                    redirect('providers/index');
                } else {
                    flash('provider_message', 'Failed to update service provider. Please try again.', 'alert alert-danger');
                    redirect('providers/edit/' . $id);
                }
            } else {
                // Map errors to data array
                $errors = $validator->getErrors();
                $data['name_err'] = $errors['name'] ?? '';
                $data['company_err'] = $errors['company'] ?? '';
                $data['specialty_err'] = $errors['specialty'] ?? '';
                $data['phone_err'] = $errors['phone'] ?? '';
                $data['email_err'] = $errors['email'] ?? '';
                $data['address_err'] = $errors['address'] ?? '';
                $data['rating_err'] = $errors['rating'] ?? '';
                $data['status_err'] = $errors['status'] ?? '';
                $data['provider'] = $this->serviceProviderModel->getProviderById($id);
                $data['title'] = 'Edit Service Provider - Rentigo Admin';
                $data['page'] = 'providers';

                $this->view('admin/v_edit_provider', $data);
            }
        } else {
            // GET request
            $provider = $this->serviceProviderModel->getProviderById($id);

            if (!$provider) {
                flash('provider_message', 'Service provider not found!', 'alert alert-danger');
                redirect('providers/index');
            }

            $data = [
                'title' => 'Edit Service Provider - Rentigo Admin',
                'page' => 'providers',
                'provider' => $provider,
                'name_err' => '',
                'company_err' => '',
                'specialty_err' => '',
                'phone_err' => '',
                'email_err' => '',
                'address_err' => '',
                'rating_err' => '',
                'status_err' => ''
            ];
            $this->view('admin/v_edit_provider', $data);
        }
    }

    // DELETE - Remove service provider
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');

            // Verify provider exists
            $provider = $this->serviceProviderModel->getProviderById($id);

            if (!$provider) {
                echo json_encode(['success' => false, 'message' => 'Provider not found']);
                exit();
            }

            if ($this->serviceProviderModel->delete($id)) {
                echo json_encode(['success' => true, 'message' => 'Provider deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete provider']);
            }
            exit();
        } else {
            redirect('providers/index');
        }
    }

    // UPDATE STATUS - Change provider status
    public function updateStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $status = $_POST['status'] ?? '';

            // Validate status
            $validator = new Validator();
            $validStatuses = ['active', 'inactive', 'suspended'];

            if ($validator->required('status', $status, 'Status is required')) {
                $validator->inArray('status', $status, $validStatuses, 'Invalid status value');
            }

            if (!$validator->hasErrors()) {
                if ($this->serviceProviderModel->updateStatus($id, $status)) {
                    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
                }
            } else {
                $errors = $validator->getErrors();
                echo json_encode(['success' => false, 'message' => $errors['status'] ?? 'Invalid status']);
            }
            exit();
        } else {
            redirect('providers/index');
        }
    }
}
