<?php
class TenantProperties extends Controller
{
    private $tenantPropertyModel;
    private $propertyModel;
    private $notificationModel;
    private $bookingModel;

    public function __construct()
    {
        // Optionally require tenant login:
        // if (!isLoggedIn() || $_SESSION['user_type'] !== 'tenant') {
        //     redirect('users/login');
        // }

        $this->tenantPropertyModel = $this->model('M_TenantProperties');
        $this->propertyModel = $this->model('M_Properties');
        $this->notificationModel = $this->model('M_Notifications');
        $this->bookingModel = $this->model('M_Bookings');
    }

    // Helper method to get unread notification count
    private function getUnreadNotificationCount()
    {
        if (isLoggedIn()) {
            return $this->notificationModel->getUnreadCount($_SESSION['user_id']);
        }
        return 0;
    }

    // List all approved and available properties
    public function index()
    {
        $this->releaseExpiredReservations();

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
            'unread_notifications' => $this->getUnreadNotificationCount()
        ];
        $this->view('tenant/v_search_properties', $data);
    }

    // View property details
    public function details($id)
    {
        $this->releaseExpiredReservations();

        $property = $this->tenantPropertyModel->getPropertyById($id);

        if (!$property) {
            flash('tenant_property_message', 'Property not found or not available', 'alert alert-danger');
            redirect('tenantproperties/index');
            return;
        }

        // Optionally fetch images, docs, etc.
        $property->images = $this->getPropertyImages($property->id);
        $property->documents = $this->getPropertyDocuments($property->id);

        // Load reviews and ratings
        $reviewModel = $this->model('M_Reviews');
        $reviews = $reviewModel->getReviewsByProperty($id, 'approved');
        $ratingData = $reviewModel->getPropertyAverageRating($id);

        // Safely extract rating data
        $averageRating = 0;
        $reviewCount = 0;
        if ($ratingData) {
            $averageRating = $ratingData->avg_rating ?? 0;
            $reviewCount = $ratingData->review_count ?? 0;
        }

        $data = [
            'property' => $property,
            'reviews' => $reviews ?? [],
            'averageRating' => $averageRating,
            'reviewCount' => $reviewCount,
            'unread_notifications' => $this->getUnreadNotificationCount()
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

    // Reserve property - Step 1 (Tenant)
    public function reserve($id)
    {
        $this->releaseExpiredReservations();

        if (!isLoggedIn() || $_SESSION['user_type'] !== 'tenant') {
            flash('reservation_message', 'Only tenants can reserve properties', 'alert alert-danger');
            redirect('users/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $property = $this->propertyModel->getPropertyById($id);

            if (!$property) {
                flash('reservation_message', 'Property not found', 'alert alert-danger');
                redirect('tenantproperties/index');
                return;
            }

            // Check if property is available
            if ($property->status !== 'available') {
                flash('reservation_message', 'This property is not available for reservation', 'alert alert-danger');
                redirect('tenantproperties/details/' . $id);
                return;
            }

            // Update property status to reserved
            if ($this->propertyModel->updatePropertyStatus($id, 'reserved')) {
                // Get user emails for notifications
                $userModel = $this->model('M_Users');
                $tenantUser = $userModel->getUserById($_SESSION['user_id']);
                $tenantEmail = $tenantUser->email ?? 'N/A';

                // Send notification to tenant
                $managerEmail = 'N/A';
                if (!empty($property->manager_id)) {
                    $managerUser = $userModel->getUserById($property->manager_id);
                    $managerEmail = $managerUser->email ?? 'N/A';
                }

                $this->notificationModel->createNotification([
                    'user_id' => $_SESSION['user_id'],
                    'type' => 'property',
                    'title' => 'Property Reserved Successfully',
                    'message' => 'Your reservation for "' . substr($property->address, 0, 50) . '..." has been confirmed. The property manager will contact you shortly. Please visit our office within 48 hours to proceed with the physical viewing and booking process. Property Manager Email: ' . $managerEmail,
                    'link' => 'tenantproperties/details/' . $id
                ]);

                // Notify assigned manager to contact tenant within 24 hours.
                if (!empty($property->manager_id)) {
                    $tenantName = $_SESSION['user_name'] ?? 'A tenant';
                    $this->notificationModel->createNotification([
                        'user_id' => $property->manager_id,
                        'type' => 'property',
                        'title' => 'New Property Reservation',
                        'message' => $tenantName . ' reserved "' . substr($property->address, 0, 50) . '...". Please contact the tenant within 24 hours to begin the physical process. Tenant Email: ' . $tenantEmail,
                        'link' => 'managerproperties/details/' . $id
                    ]);
                }

                flash('reservation_message', 'Property reserved successfully! Please visit our office to view the property and proceed with booking.', 'alert alert-success');
                redirect('tenant/dashboard');
            } else {
                flash('reservation_message', 'Failed to reserve property. Please try again.', 'alert alert-danger');
                redirect('tenantproperties/details/' . $id);
            }
        } else {
            redirect('tenantproperties/index');
        }
    }

    // Auto-release reservations older than 48 hours with no active booking
    private function releaseExpiredReservations()
    {
        $reservedProperties = $this->propertyModel->getAllPropertiesByStatus('reserved');
        if (empty($reservedProperties)) {
            return;
        }

        $expiryWindowSeconds = 48 * 60 * 60;
        $now = time();

        foreach ($reservedProperties as $property) {
            $latestReservation = $this->notificationModel->getLatestPropertyReservationNotification($property->id);
            if (!$latestReservation || empty($latestReservation->created_at)) {
                continue;
            }

            $reservedAt = strtotime($latestReservation->created_at);
            if (!$reservedAt || ($now - $reservedAt) < $expiryWindowSeconds) {
                continue;
            }

            if ($this->bookingModel->hasOpenBookingForProperty($property->id)) {
                continue;
            }

            if ($this->propertyModel->updatePropertyStatus($property->id, 'available')) {
                $propertyLink = 'tenantproperties/details/' . $property->id;
                $propertyAddress = $property->address ?? ('Property #' . $property->id);

                // Notify the tenant who made the reservation
                if (!empty($latestReservation->user_id)) {
                    $this->notificationModel->createNotification([
                        'user_id' => $latestReservation->user_id,
                        'type' => 'property',
                        'title' => 'Reservation Expired',
                        'message' => "Your reservation for {$propertyAddress} has expired because no follow-up was completed within 48 hours. The property is now available again.",
                        'link' => $propertyLink
                    ]);
                }

                // Notify the assigned manager if available
                if (!empty($property->manager_id)) {
                    $this->notificationModel->createNotification([
                        'user_id' => $property->manager_id,
                        'type' => 'property',
                        'title' => 'Reservation Auto-Released',
                        'message' => "Reservation for {$propertyAddress} was automatically released after 48 hours without tenant follow-up.",
                        'link' => 'managerproperties/details/' . $property->id
                    ]);
                }
            }
        }
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
