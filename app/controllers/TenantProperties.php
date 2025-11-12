<?php
class TenantProperties extends Controller
{
    private $tenantPropertyModel;

    public function __construct()
    {
        // Optionally require tenant login:
        // if (!isLoggedIn() || $_SESSION['user_type'] !== 'tenant') {
        //     redirect('users/login');
        // }

        $this->tenantPropertyModel = $this->model('M_TenantProperties');
    }

    // List all approved and available properties
    public function index()
    {
        $properties = $this->tenantPropertyModel->getApprovedProperties();

        // Optionally add images, etc.
        if ($properties) {
            foreach ($properties as $property) {
                $property->primary_image = $this->getPrimaryPropertyImage($property->id);
            }
        }

        $data = [
            'properties' => $properties,
            'page' => 'search_properties',
        ];
        $this->view('tenant/v_search_properties', $data);
    }

    // View property details
    public function details($id)
    {
        $property = $this->tenantPropertyModel->getPropertyById($id);

        if (!$property) {
            flash('tenant_property_message', 'Property not found or not available', 'alert alert-danger');
            redirect('tenantproperties/index');
            return;
        }

        // Optionally fetch images, docs, etc.
        $property->images = $this->getPropertyImages($property->id);
        $property->documents = $this->getPropertyDocuments($property->id);

        $data = [
            'property' => $property
        ];
        $this->view('tenant/v_property_details', $data);
    }

    // (Optional) AJAX or form-based search endpoint
    public function search()
    {
        // If using AJAX, receive parameters and return JSON or filtered view
        // Not implemented here—client-side filtering used in view.
        $this->index();
    }

    // Helpers (reuse your existing image/document helpers)
    private function getPrimaryPropertyImage($propertyId)
    {
        $propertyDir = APPROOT . '/../public/uploads/properties/property_' . $propertyId . '/';
        $primaryFile = $propertyDir . 'primary.txt';
        $urlBase = URLROOT . '/uploads/properties/property_' . $propertyId . '/';

        if (file_exists($primaryFile)) {
            $primaryImageName = trim(file_get_contents($primaryFile));
            if ($primaryImageName && file_exists($propertyDir . $primaryImageName)) {
                return $urlBase . $primaryImageName;
            }
        }

        // Fallback: get the first image if exists
        $images = $this->getPropertyImages($propertyId);
        if (!empty($images)) {
            return $images[0]['url'];
        }

        return URLROOT . '/img/property-placeholder.jpg';
    }

    private function getPropertyImages($propertyId)
    {
        $propertyDir = APPROOT . '/../public/uploads/properties/property_' . $propertyId . '/';
        $urlBase = URLROOT . '/uploads/properties/property_' . $propertyId . '/';

        if (!is_dir($propertyDir)) {
            return [];
        }

        $images = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        $files = scandir($propertyDir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'primary.txt' || is_dir($propertyDir . $file)) {
                continue;
            }

            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, $allowedExtensions)) {
                $images[] = [
                    'name' => $file,
                    'url' => $urlBase . $file,
                    'path' => $propertyDir . $file,
                    'size' => filesize($propertyDir . $file),
                    'modified' => filemtime($propertyDir . $file)
                ];
            }
        }

        usort($images, function ($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $images;
    }

    private function getPropertyDocuments($propertyId)
    {
        $documentsDir = APPROOT . '/../public/uploads/properties/property_' . $propertyId . '/documents/';
        $urlBase = URLROOT . '/uploads/properties/property_' . $propertyId . '/documents/';

        if (!is_dir($documentsDir)) {
            return [];
        }

        $documents = [];
        $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'];

        $files = scandir($documentsDir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, $allowedExtensions)) {
                $documents[] = [
                    'name' => $file,
                    'url' => $urlBase . $file,
                    'path' => $documentsDir . $file,
                    'size' => filesize($documentsDir . $file),
                    'modified' => filemtime($documentsDir . $file),
                    'type' => $extension
                ];
            }
        }

        usort($documents, function ($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $documents;
    }
}
