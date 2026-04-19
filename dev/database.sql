-- ======================================================
-- Database: rentigo_db_test (Property Management System for Colombo)
-- Time Zone: Asia/Colombo (UTC +05:30)
-- ======================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = '+05:30';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Database: rentigo_db_test
-- --------------------------------------------------------
DROP DATABASE IF EXISTS `rentigo_db_test`;
CREATE DATABASE IF NOT EXISTS `rentigo_db_test` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rentigo_db_test`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_code` varchar(6) DEFAULT NULL,
  `reset_code_expires` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_type` enum('admin','property_manager','tenant','landlord') NOT NULL DEFAULT 'tenant',
  `account_status` enum('pending','active','suspended','rejected') NOT NULL DEFAULT 'active',
  `terms_accepted_at` timestamp NULL DEFAULT NULL,
  `terms_version` varchar(10) DEFAULT '1.0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `properties`
-- --------------------------------------------------------
CREATE TABLE `properties` (
  `id` int NOT NULL,
  `landlord_id` int NOT NULL,
  `manager_id` int DEFAULT NULL,
  `address` text NOT NULL,
  `property_type` enum('apartment','house','condo','townhouse') NOT NULL,
  `listing_type` enum('rent','maintenance','rental') NOT NULL DEFAULT 'rent',
  `bedrooms` int NOT NULL,
  `bathrooms` int NOT NULL,
  `sqft` int DEFAULT NULL,
  `rent` int NOT NULL,
  `deposit` decimal(10,2) DEFAULT NULL,
  `description` text,
  `current_occupant` varchar(100) DEFAULT NULL,
  `status` enum('available','occupied','maintenance','pending','approved','rejected','reserved') NOT NULL DEFAULT 'pending',
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `available_date` date DEFAULT NULL,
  `parking` varchar(50) DEFAULT '0',
  `pet_policy` enum('no','cats','dogs','both') NOT NULL DEFAULT 'no',
  `laundry` enum('none','shared','in_unit','hookups','included') NOT NULL DEFAULT 'none',
  `tenant` varchar(200) DEFAULT NULL,
  `issue` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `market_properties`
-- --------------------------------------------------------
CREATE TABLE `market_properties` (
  `id` int NOT NULL,
  `landlord_id` int NOT NULL DEFAULT '1',
  `manager_id` int DEFAULT NULL,
  `address` text NOT NULL,
  `property_type` enum('apartment','house','condo','townhouse') NOT NULL,
  `bedrooms` int NOT NULL,
  `bathrooms` decimal(2,1) NOT NULL,
  `sqft` int DEFAULT NULL,
  `rent` decimal(10,2) NOT NULL,
  `deposit` decimal(10,2) DEFAULT NULL,
  `description` text,
  `status` enum('available','occupied','maintenance','vacant','pending') NOT NULL DEFAULT 'available',
  `available_date` date DEFAULT NULL,
  `parking` varchar(50) DEFAULT '0',
  `pet_policy` enum('no','cats','dogs','both') NOT NULL DEFAULT 'no',
  `laundry` enum('none','shared','in_unit','hookups','included') NOT NULL DEFAULT 'none',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `bookings`
-- --------------------------------------------------------
CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL,
  `property_id` int NOT NULL,
  `landlord_id` int NOT NULL,
  `move_in_date` date NOT NULL,
  `move_out_date` date NOT NULL,
  `monthly_rent` decimal(10,2) NOT NULL,
  `deposit_amount` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','rejected','active','completed','cancelled') NOT NULL DEFAULT 'pending',
  `rejection_reason` text,
  `cancellation_reason` text,
  `notes` text,
  `tenant_document_path` varchar(255) DEFAULT NULL,
  `tenant_document_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `inspections`
-- --------------------------------------------------------
CREATE TABLE `inspections` (
  `id` int NOT NULL,
  `property_id` int DEFAULT NULL,
  `issue_id` int DEFAULT NULL,
  `type` enum('routine','move_in','move_out','maintenance','annual','emergency','issue') NOT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_time` time DEFAULT NULL,
  `notes` text,
  `inspection_notes` text,
  `manager_id` int DEFAULT NULL COMMENT 'PM who scheduled the inspection',
  `landlord_id` int DEFAULT NULL,
  `tenant_id` int DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled','pending') NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `issues`
-- --------------------------------------------------------
CREATE TABLE `issues` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL,
  `property_id` int NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `priority` enum('low','medium','high','emergency') NOT NULL DEFAULT 'medium',
  `status` enum('pending','in_progress','resolved','cancelled') NOT NULL DEFAULT 'pending',
  `maintenance_request_id` int DEFAULT NULL,
  `inspection_id` int DEFAULT NULL,
  `resolution_notes` text,
  `assigned_to` int DEFAULT NULL COMMENT 'PM assigned to handle this issue',
  `landlord_id` int DEFAULT NULL COMMENT 'Landlord of the property',
  `resolved_at` timestamp NULL DEFAULT NULL,
  `pm_notified` tinyint(1) NOT NULL DEFAULT '0',
  `landlord_notified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `service_providers`
-- --------------------------------------------------------
CREATE TABLE `service_providers` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `address` text,
  `phone` varchar(20) NOT NULL,
  `specialty` varchar(100) NOT NULL,
  `description` text,
  `rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `maintenance_requests`
-- --------------------------------------------------------
CREATE TABLE `maintenance_requests` (
  `id` int NOT NULL,
  `property_id` int NOT NULL,
  `landlord_id` int NOT NULL,
  `issue_id` int DEFAULT NULL,
  `provider_id` int DEFAULT NULL,
  `requester_id` int NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) NOT NULL,
  `priority` enum('low','medium','high','emergency') NOT NULL DEFAULT 'medium',
  `status` enum('pending','scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `estimated_cost` decimal(10,2) DEFAULT NULL,
  `actual_cost` decimal(10,2) DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `completion_date` timestamp NULL DEFAULT NULL,
  `completion_notes` text,
  `cancellation_reason` text,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `maintenance_quotations`
-- --------------------------------------------------------
CREATE TABLE `maintenance_quotations` (
  `id` int NOT NULL,
  `request_id` int NOT NULL,
  `provider_id` int NOT NULL,
  `uploaded_by` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `quotation_file` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `rejection_reason` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `maintenance_payments`
-- --------------------------------------------------------
CREATE TABLE `maintenance_payments` (
  `id` int NOT NULL,
  `request_id` int NOT NULL,
  `quotation_id` int NOT NULL,
  `landlord_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `lease_agreements`
-- --------------------------------------------------------
CREATE TABLE `lease_agreements` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL,
  `landlord_id` int NOT NULL,
  `property_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `monthly_rent` decimal(10,2) NOT NULL,
  `deposit_amount` decimal(10,2) NOT NULL,
  `lease_duration_months` int NOT NULL,
  `terms_and_conditions` text,
  `status` enum('draft','pending_signatures','active','completed','terminated') NOT NULL DEFAULT 'draft',
  `signed_by_tenant` tinyint(1) NOT NULL DEFAULT '0',
  `signed_by_landlord` tinyint(1) NOT NULL DEFAULT '0',
  `tenant_signature_date` timestamp NULL DEFAULT NULL,
  `landlord_signature_date` timestamp NULL DEFAULT NULL,
  `termination_reason` text,
  `termination_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `payments`
-- --------------------------------------------------------
CREATE TABLE `payments` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL,
  `landlord_id` int NOT NULL,
  `property_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT '',
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `due_date` date NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `notifications`
-- --------------------------------------------------------
CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT '',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `property_manager`
-- --------------------------------------------------------
CREATE TABLE `property_manager` (
  `manager_id` int NOT NULL,
  `user_id` int NOT NULL,
  `employee_id_document` longblob,
  `employee_id_filename` varchar(255) NOT NULL DEFAULT '',
  `employee_id_filetype` varchar(100) DEFAULT NULL,
  `employee_id_filesize` int DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text,
  `approved_by` int DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `policies`
-- --------------------------------------------------------
CREATE TABLE `policies` (
  `policy_id` int NOT NULL,
  `policy_name` varchar(255) NOT NULL,
  `policy_category` enum('rental','security','maintenance','financial','general','privacy','terms_of_service','refund','data_protection') NOT NULL,
  `policy_description` text NOT NULL DEFAULT '',
  `policy_content` text NOT NULL,
  `policy_version` varchar(20) DEFAULT '1.0',
  `policy_status` enum('draft','active','inactive','archived','under_review') NOT NULL DEFAULT 'draft',
  `policy_type` enum('standard','custom') NOT NULL DEFAULT 'standard',
  `effective_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `reviews`
-- --------------------------------------------------------
CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `reviewer_id` int NOT NULL,
  `reviewee_id` int NOT NULL,
  `property_id` int DEFAULT NULL,
  `booking_id` int DEFAULT NULL,
  `rating` int NOT NULL,
  `review_text` text,
  `review_type` enum('property','tenant') NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `password_resets`
-- --------------------------------------------------------
CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `Posts`
-- --------------------------------------------------------
CREATE TABLE `Posts` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- INSERT SAMPLE DATA
-- ======================================================

-- --------------------------------------------------------
-- Users (only 4: admin, landlord, tenant, property_manager)
-- Password: 'rentigo12345' hashed
-- --------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `account_status`, `terms_accepted_at`, `created_at`) VALUES
(1, 'Admin User', 'admin@rentigo.com', '$2y$10$Sv7hEf2jHEjfgtmAlKjq5uGk9r0KUPP6i0fkTWIEd9TM4XSm09U5C', 'admin', 'active', '2025-01-01 00:00:00', '2025-01-01 00:00:00'),
(2, 'Nimal Perera', 'nimal@landlord.com', '$2y$10$Sv7hEf2jHEjfgtmAlKjq5uGk9r0KUPP6i0fkTWIEd9TM4XSm09U5C', 'landlord', 'active', '2025-01-15 00:00:00', '2025-01-15 00:00:00'),
(3, 'Amal Fernando', 'amal@tenant.com', '$2y$10$Sv7hEf2jHEjfgtmAlKjq5uGk9r0KUPP6i0fkTWIEd9TM4XSm09U5C', 'tenant', 'active', '2025-02-05 00:00:00', '2025-02-05 00:00:00'),
(4, 'Samantha Perera', 'sam@pm.com', '$2y$10$Sv7hEf2jHEjfgtmAlKjq5uGk9r0KUPP6i0fkTWIEd9TM4XSm09U5C', 'property_manager', 'active', '2025-01-10 00:00:00', '2025-01-10 00:00:00');

-- --------------------------------------------------------
-- Properties (mix of 2025 and 2026)
-- --------------------------------------------------------
INSERT INTO `properties` (`id`, `landlord_id`, `manager_id`, `address`, `property_type`, `listing_type`, `bedrooms`, `bathrooms`, `sqft`, `rent`, `deposit`, `description`, `current_occupant`, `status`, `approval_status`, `approved_at`, `available_date`, `parking`, `pet_policy`, `laundry`, `tenant`, `issue`, `created_at`, `updated_at`) VALUES
(1, 2, 4, 'No. 15, Galle Road, Colombo 03', 'apartment', 'rent', 2, 2, 950, 85000, 85000.00, 'Modern sea-view apartment', 'Amal Fernando', 'occupied', 'approved', '2025-01-20 10:00:00', '2026-02-02', '1', 'cats', 'in_unit', 'Amal Fernando', 'None reported', '2025-01-18 08:00:00', '2026-03-25 14:00:00'),
(2, 2, 4, 'No. 22, Duplication Road, Colombo 04', 'apartment', 'rent', 3, 2, 1250, 110000, 110000.00, 'Spacious family apartment', 'Vacant', 'available', 'approved', '2025-01-22 09:00:00', '2026-04-01', '2', 'both', 'in_unit', 'N/A', 'No active issues', '2025-01-20 09:00:00', '2026-03-20 12:00:00'),
(3, 2, 4, 'No. 12, Marine Drive, Colombo 06', 'apartment', 'rent', 1, 1, 550, 55000, 55000.00, 'Cozy studio near beach', 'Amal Fernando', 'occupied', 'approved', '2025-02-01 09:30:00', '2026-01-21', '1', 'no', 'shared', 'Amal Fernando', 'Electrical issue resolved', '2025-01-30 10:00:00', '2025-02-06 15:00:00'),
(4, 2, 4, 'No. 45, Horton Place, Colombo 07', 'house', 'rent', 4, 3, 2200, 180000, 180000.00, 'Independent house with garden', 'Vacant', 'available', 'approved', '2025-01-28 14:00:00', '2025-02-15', '2', 'dogs', 'in_unit', 'N/A', 'No active issues', '2025-01-26 08:00:00', '2025-01-28 14:00:00'),
(5, 2, 4, 'No. 7, Independence Avenue, Colombo 07', 'condo', 'rent', 3, 3, 1600, 165000, 165000.00, 'Premium condo, 24/7 security', 'Amal Fernando', 'occupied', 'approved', '2025-02-08 12:00:00', '2026-02-06', '2', 'both', 'in_unit', 'Amal Fernando', 'AC repair completed', '2025-02-06 09:00:00', '2025-06-25 14:00:00'),
(6, 2, 4, 'No. 88, Ward Place, Colombo 07', 'condo', 'rent', 2, 2, 1100, 120000, 120000.00, 'Luxury condo in Cinnamon Gardens', 'Vacant', 'available', 'approved', '2025-03-10 11:00:00', '2025-04-01', '2', 'no', 'in_unit', 'N/A', 'No active issues', '2025-03-05 10:00:00', '2025-03-10 11:00:00'),
(7, 2, 4, 'No. 33, Thimbirigasyaya Road, Colombo 05', 'apartment', 'rent', 2, 1, 780, 75000, 75000.00, 'Well-maintained apartment near hospitals', 'Vacant', 'available', 'approved', '2026-01-10 09:00:00', '2026-02-01', '1', 'cats', 'hookups', 'N/A', 'No active issues', '2026-01-05 11:00:00', '2026-01-10 09:00:00'),
(8, 2, 4, 'No. 55, Galle Road, Colombo 03', 'apartment', 'rent', 2, 1, 820, 78000, 78000.00, 'Furnished apartment, close to US Embassy', 'Vacant', 'available', 'approved', '2026-02-12 14:30:00', '2026-03-01', '1', 'no', 'in_unit', 'N/A', 'No active issues', '2026-02-08 08:00:00', '2026-02-12 14:30:00'),
(9, 2, 4, 'No. 19, Rosmead Place, Colombo 07', 'townhouse', 'rent', 3, 3, 1850, 195000, 195000.00, 'Modern townhouse in prime area', 'Vacant', 'available', 'approved', '2026-02-20 09:00:00', '2026-03-15', '2', 'dogs', 'in_unit', 'N/A', 'No active issues', '2026-02-15 10:00:00', '2026-02-20 09:00:00'),
(10, 2, 4, 'No. 25, Park Road, Colombo 05', 'apartment', 'rent', 2, 2, 980, 88000, 88000.00, 'Renovated apartment with backup power', 'Amal Fernando', 'occupied', 'approved', '2026-03-01 11:00:00', '2027-03-02', '1', 'cats', 'in_unit', 'Amal Fernando', 'Water heater repair in progress', '2026-02-25 12:00:00', '2026-03-12 10:00:00');

-- --------------------------------------------------------
-- Market Properties (Full 108 records)
-- --------------------------------------------------------
INSERT INTO `market_properties` (`id`, `landlord_id`, `manager_id`, `address`, `property_type`, `bedrooms`, `bathrooms`, `sqft`, `rent`, `deposit`, `description`, `status`, `available_date`, `parking`, `pet_policy`, `laundry`, `created_at`) VALUES
(1, 1, NULL, '15 York Street, Colombo 01', 'apartment', 2, 1.0, 900, 25000.00, 25000.00, 'Modern apartment near Fort Railway Station', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(2, 1, NULL, '88 Chatham Street, Colombo 01', 'apartment', 1, 1.0, 600, 20000.00, 20000.00, 'Compact studio in Fort area', 'available', NULL, '0', 'no', 'none', '2025-10-20 01:02:42'),
(3, 1, NULL, '22 Braybrooke Place, Colombo 02', 'apartment', 2, 1.0, 850, 22000.00, 22000.00, 'Apartment near Beira Lake', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(4, 1, NULL, '45 Union Place, Colombo 02', 'apartment', 3, 2.0, 1150, 26000.00, 26000.00, 'Spacious 3BR near Dematagoda', 'occupied', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(5, 1, NULL, '77 Park Street, Colombo 02', 'apartment', 2, 1.5, 950, 24000.00, 24000.00, 'Well-maintained property', 'available', NULL, '1', 'no', 'hookups', '2025-10-20 01:02:42'),
(6, 1, NULL, '33 Sir Baron Jayatilaka Mawatha, Colombo 02', 'condo', 2, 2.0, 1000, 28000.00, 28000.00, 'Modern condo with amenities', 'occupied', NULL, '2', 'no', 'in_unit', '2025-10-20 01:02:42'),
(7, 1, NULL, '15 Galle Road, Colombo 03', 'apartment', 2, 1.0, 850, 24000.00, 24000.00, 'Sea-facing apartment on Galle Road', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(8, 1, NULL, '88 Marine Drive, Colombo 03', 'apartment', 3, 2.0, 1250, 32000.00, 32000.00, 'Luxury apartment with sea view', 'occupied', NULL, '2', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(9, 1, NULL, '22 Dharmapala Mawatha, Colombo 03', 'condo', 3, 2.5, 1400, 38000.00, 38000.00, 'Premium condo in Kollupitiya', 'available', NULL, '2', 'both', 'in_unit', '2025-10-20 01:02:42'),
(10, 1, NULL, '45 Clifford Place, Colombo 03', 'apartment', 1, 1.0, 650, 20000.00, 20000.00, 'Studio near Galle Face Green', 'occupied', NULL, '0', 'no', 'none', '2025-10-20 01:02:42'),
(11, 1, NULL, '77 Flower Road, Colombo 03', 'apartment', 3, 2.0, 1200, 30000.00, 30000.00, 'Centrally located 3BR', 'occupied', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(12, 1, NULL, '33 Union Place, Colombo 03', 'apartment', 2, 2.0, 1000, 26000.00, 26000.00, 'Modern 2BR with parking', 'available', NULL, '1', 'no', 'hookups', '2025-10-20 01:02:42'),
(13, 1, NULL, '99 Queens Road, Colombo 03', 'apartment', 2, 1.0, 900, 25000.00, 25000.00, 'Recently renovated apartment', 'occupied', NULL, '1', 'dogs', 'shared', '2025-10-20 01:02:42'),
(14, 1, NULL, '12 Leyden Bastian Road, Colombo 03', 'condo', 2, 2.0, 1100, 32000.00, 32000.00, 'High-rise condo with gym', 'occupied', NULL, '2', 'no', 'in_unit', '2025-10-20 01:02:42'),
(15, 1, NULL, '125 Galle Road, Colombo 03', 'apartment', 2, 2.0, 1050, 27000.00, 27000.00, 'Modern beachfront apartment', 'occupied', NULL, '1', 'no', 'in_unit', '2025-10-20 01:02:42'),
(16, 1, NULL, '210 Dharmapala Mawatha, Colombo 03', 'apartment', 1, 1.0, 700, 22000.00, 22000.00, 'Studio with modern amenities', 'available', NULL, '1', 'no', 'in_unit', '2025-10-20 01:02:42'),
(17, 1, NULL, '250 Galle Road, Colombo 03', 'apartment', 3, 2.0, 1220, 29000.00, 29000.00, 'Corner unit with sea view', 'occupied', NULL, '1', 'no', 'in_unit', '2025-10-20 01:02:42'),
(18, 1, NULL, '175 Marine Drive, Colombo 03', 'condo', 2, 2.0, 1150, 33000.00, 33000.00, 'Modern condo with gym and pool', 'available', NULL, '2', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(19, 1, NULL, '300 Dharmapala Mawatha, Colombo 03', 'apartment', 2, 1.5, 980, 26000.00, 26000.00, 'Close to shopping centers', 'occupied', NULL, '1', 'no', 'hookups', '2025-10-20 01:02:42'),
(20, 1, NULL, '15 Galle Road, Colombo 04', 'apartment', 2, 1.0, 800, 20000.00, 20000.00, 'Affordable Galle Road apartment', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(21, 1, NULL, '88 Duplication Road, Colombo 04', 'apartment', 3, 2.0, 1100, 25000.00, 25000.00, 'Spacious family apartment', 'occupied', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(22, 1, NULL, '22 Lauries Road, Colombo 04', 'apartment', 2, 1.5, 950, 22000.00, 22000.00, 'Well-located 2BR', 'available', NULL, '0', 'no', 'hookups', '2025-10-20 01:02:42'),
(23, 1, NULL, '45 Dickman Road, Colombo 04', 'apartment', 3, 2.0, 1150, 26000.00, 26000.00, 'Family-friendly neighborhood', 'occupied', NULL, '1', 'both', 'in_unit', '2025-10-20 01:02:42'),
(24, 1, NULL, '77 Horton Place, Colombo 04', 'apartment', 1, 1.0, 600, 18000.00, 18000.00, 'Compact 1BR for single/couple', 'occupied', NULL, '0', 'no', 'none', '2025-10-20 01:02:42'),
(25, 1, NULL, '33 Station Road, Colombo 04', 'apartment', 2, 1.0, 850, 21000.00, 21000.00, 'Near Bambalapitiya Station', 'available', NULL, '1', 'cats', 'shared', '2025-10-20 01:02:42'),
(26, 1, NULL, '99 Park Road, Colombo 04', 'apartment', 3, 2.0, 1200, 27000.00, 27000.00, '3BR with modern amenities', 'occupied', NULL, '1', 'no', 'in_unit', '2025-10-20 01:02:42'),
(27, 1, NULL, '12 Magazine Road, Colombo 04', 'condo', 2, 2.0, 1000, 29000.00, 29000.00, 'Condo with pool access', 'occupied', NULL, '2', 'no', 'in_unit', '2025-10-20 01:02:42'),
(28, 1, NULL, '56 Green Path, Colombo 04', 'apartment', 2, 1.0, 900, 23000.00, 23000.00, 'Quiet residential area', 'available', NULL, '1', 'dogs', 'hookups', '2025-10-20 01:02:42'),
(29, 1, NULL, '200 Duplication Road, Colombo 04', 'condo', 3, 2.0, 1300, 31000.00, 31000.00, 'Luxury condo with gym', 'available', NULL, '2', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(30, 1, NULL, '111 Galle Road, Colombo 04', 'apartment', 2, 1.5, 920, 23500.00, 23500.00, 'Ocean view apartment', 'occupied', NULL, '1', 'no', 'in_unit', '2025-10-20 01:02:42'),
(31, 1, NULL, '155 Bambalapitiya Road, Colombo 04', 'apartment', 3, 2.0, 1180, 27500.00, 27500.00, 'Near schools and shops', 'occupied', NULL, '1', 'cats', 'hookups', '2025-10-20 01:02:42'),
(32, 1, NULL, '175 Galle Road, Colombo 04', 'apartment', 3, 2.0, 1180, 27500.00, 27500.00, 'Spacious beachfront apartment', 'occupied', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(33, 1, NULL, '225 Duplication Road, Colombo 04', 'apartment', 2, 1.5, 940, 23500.00, 23500.00, 'Near Bambalapitiya junction', 'available', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(34, 1, NULL, '88 Koswatte Road, Colombo 04', 'apartment', 3, 2.0, 1160, 26500.00, 26500.00, 'Family-friendly building', 'occupied', NULL, '1', 'dogs', 'in_unit', '2025-10-20 01:02:42'),
(35, 1, NULL, '15 Havelock Road, Colombo 05', 'apartment', 3, 2.0, 1300, 28000.00, 28000.00, 'Large 3BR on Havelock Road', 'occupied', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(36, 1, NULL, '88 Thimbirigasyaya Road, Colombo 05', 'apartment', 2, 2.0, 1000, 24000.00, 24000.00, 'Modern 2BR apartment', 'occupied', NULL, '1', 'no', 'in_unit', '2025-10-20 01:02:42'),
(37, 1, NULL, '22 Park Avenue, Colombo 05', 'house', 3, 2.0, 1800, 45000.00, 45000.00, 'Spacious house with garden', 'available', NULL, '2', 'both', 'in_unit', '2025-10-20 01:02:42'),
(38, 1, NULL, '45 Bullers Road, Colombo 05', 'apartment', 3, 2.0, 1200, 27000.00, 27000.00, 'Family apartment near schools', 'occupied', NULL, '1', 'cats', 'hookups', '2025-10-20 01:02:42'),
(39, 1, NULL, '77 Hospital Road, Colombo 05', 'apartment', 2, 1.0, 900, 22000.00, 22000.00, 'Convenient location', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(40, 1, NULL, '33 Elvitigala Mawatha, Colombo 05', 'condo', 3, 2.5, 1500, 36000.00, 36000.00, 'Luxury condo with amenities', 'available', NULL, '2', 'both', 'in_unit', '2025-10-20 01:02:42'),
(41, 1, NULL, '99 Kirimandala Mawatha, Colombo 05', 'apartment', 2, 1.5, 950, 23000.00, 23000.00, 'Well-maintained 2BR', 'occupied', NULL, '1', 'no', 'in_unit', '2025-10-20 01:02:42'),
(42, 1, NULL, '12 Rosemead Place, Colombo 05', 'apartment', 3, 2.0, 1150, 26000.00, 26000.00, 'Near Narahenpita market', 'occupied', NULL, '1', 'cats', 'shared', '2025-10-20 01:02:42'),
(43, 1, NULL, '56 Queen Mary Road, Colombo 05', 'townhouse', 3, 2.5, 1600, 40000.00, 40000.00, 'Modern townhouse', 'available', NULL, '2', 'both', 'in_unit', '2025-10-20 01:02:42'),
(44, 1, NULL, '75 Havelock Road, Colombo 05', 'apartment', 2, 1.5, 950, 24000.00, 24000.00, 'Centrally located', 'occupied', NULL, '1', 'no', 'hookups', '2025-10-20 01:02:42'),
(45, 1, NULL, '165 Thimbirigasyaya Road, Colombo 05', 'house', 4, 3.0, 2200, 55000.00, 55000.00, 'Large family house', 'occupied', NULL, '3', 'both', 'in_unit', '2025-10-20 01:02:42'),
(46, 1, NULL, '88 Narahenpita Road, Colombo 05', 'apartment', 2, 1.0, 880, 23000.00, 23000.00, 'Near public transport', 'available', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(47, 1, NULL, '125 Havelock Road, Colombo 05', 'apartment', 2, 2.0, 1050, 25000.00, 25000.00, 'Modern amenities', 'occupied', NULL, '1', 'no', 'in_unit', '2025-10-20 01:02:42'),
(48, 1, NULL, '200 Thimbirigasyaya Road, Colombo 05', 'apartment', 3, 2.0, 1280, 29000.00, 29000.00, 'Near office areas', 'available', NULL, '1', 'cats', 'hookups', '2025-10-20 01:02:42'),
(49, 1, NULL, '44 Anderson Road, Colombo 05', 'condo', 2, 2.0, 1120, 27000.00, 27000.00, 'Condo with security', 'occupied', NULL, '2', 'no', 'in_unit', '2025-10-20 01:02:42'),
(50, 1, NULL, '15 Galle Road, Colombo 06', 'apartment', 2, 1.0, 800, 19000.00, 19000.00, 'Affordable Wellawatta apartment', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(51, 1, NULL, '88 Ramakrishna Road, Colombo 06', 'apartment', 3, 2.0, 1050, 23000.00, 23000.00, 'Spacious 3BR', 'occupied', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(52, 1, NULL, '22 Parsons Road, Colombo 06', 'apartment', 2, 1.5, 900, 21000.00, 21000.00, 'Near beach area', 'available', NULL, '1', 'no', 'hookups', '2025-10-20 01:02:42'),
(53, 1, NULL, '45 De Saram Road, Colombo 06', 'apartment', 3, 2.0, 1100, 24000.00, 24000.00, 'Family-friendly area', 'occupied', NULL, '1', 'dogs', 'shared', '2025-10-20 01:02:42'),
(54, 1, NULL, '77 Station Avenue, Colombo 06', 'apartment', 1, 1.0, 650, 16000.00, 16000.00, 'Budget-friendly 1BR', 'occupied', NULL, '0', 'no', 'none', '2025-10-20 01:02:42'),
(55, 1, NULL, '33 Fife Road, Colombo 06', 'apartment', 2, 1.0, 850, 20000.00, 20000.00, 'Convenient location', 'available', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(56, 1, NULL, '99 Gower Street, Colombo 06', 'apartment', 3, 2.0, 1150, 25000.00, 25000.00, 'Near Wellawatta market', 'occupied', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(57, 1, NULL, '12 Pringle Road, Colombo 06', 'apartment', 2, 2.0, 950, 22000.00, 22000.00, 'Modern 2BR', 'occupied', NULL, '1', 'no', 'hookups', '2025-10-20 01:02:42'),
(58, 1, NULL, '150 Galle Road, Colombo 06', 'apartment', 3, 2.0, 1100, 23000.00, 23000.00, 'Spacious family apartment', 'occupied', NULL, '1', 'dogs', 'shared', '2025-10-20 01:02:42'),
(59, 1, NULL, '66 Wellawatta Road, Colombo 06', 'apartment', 2, 1.5, 920, 21500.00, 21500.00, 'Near beach access', 'available', NULL, '1', 'no', 'in_unit', '2025-10-20 01:02:42'),
(60, 1, NULL, '175 Galle Road, Colombo 06', 'apartment', 2, 1.5, 910, 20500.00, 20500.00, 'Near Wellawatta beach', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(61, 1, NULL, '225 Ramakrishna Road, Colombo 06', 'apartment', 3, 2.0, 1080, 23500.00, 23500.00, 'Close to religious sites', 'available', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(62, 1, NULL, '15 Rosmead Place, Colombo 07', 'apartment', 3, 2.0, 1350, 34000.00, 34000.00, 'Premium Cinnamon Gardens apartment', 'occupied', NULL, '2', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(63, 1, NULL, '88 Ward Place, Colombo 07', 'condo', 3, 2.5, 1600, 42000.00, 42000.00, 'Luxury condo with all amenities', 'available', NULL, '2', 'both', 'in_unit', '2025-10-20 01:02:42'),
(64, 1, NULL, '22 Barnes Place, Colombo 07', 'apartment', 2, 2.0, 1100, 30000.00, 30000.00, 'High-end 2BR apartment', 'occupied', NULL, '1', 'no', 'in_unit', '2025-10-20 01:02:42'),
(65, 1, NULL, '45 Independence Avenue, Colombo 07', 'house', 4, 3.0, 2400, 65000.00, 65000.00, 'Spacious house in prime area', 'occupied', NULL, '3', 'both', 'in_unit', '2025-10-20 01:02:42'),
(66, 1, NULL, '77 Cambridge Place, Colombo 07', 'apartment', 3, 2.0, 1300, 32000.00, 32000.00, 'Well-located family home', 'available', NULL, '2', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(67, 1, NULL, '33 Jawatte Road, Colombo 07', 'apartment', 2, 1.5, 1000, 28000.00, 28000.00, 'Modern apartment', 'occupied', NULL, '1', 'no', 'hookups', '2025-10-20 01:02:42'),
(68, 1, NULL, '99 Flower Road, Colombo 07', 'condo', 2, 2.0, 1200, 35000.00, 35000.00, 'High-rise luxury condo', 'occupied', NULL, '2', 'no', 'in_unit', '2025-10-20 01:02:42'),
(69, 1, NULL, '12 Park Road, Colombo 07', 'apartment', 3, 2.5, 1450, 38000.00, 38000.00, 'Spacious premium apartment', 'available', NULL, '2', 'both', 'in_unit', '2025-10-20 01:02:42'),
(70, 1, NULL, '56 Gregory Road, Colombo 07', 'townhouse', 3, 3.0, 1800, 50000.00, 50000.00, 'Modern townhouse', 'occupied', NULL, '2', 'both', 'in_unit', '2025-10-20 01:02:42'),
(71, 1, NULL, '50 Independence Avenue, Colombo 07', 'condo', 2, 2.0, 1200, 36000.00, 36000.00, 'Premium condo', 'available', NULL, '2', 'both', 'in_unit', '2025-10-20 01:02:42'),
(72, 1, NULL, '95 Rosmead Place, Colombo 07', 'apartment', 3, 2.5, 1400, 37000.00, 37000.00, 'Luxury apartment', 'occupied', NULL, '2', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(73, 1, NULL, '90 Ward Place, Colombo 07', 'condo', 3, 3.0, 1700, 45000.00, 45000.00, 'Penthouse condo', 'available', NULL, '2', 'both', 'in_unit', '2025-10-20 01:02:42'),
(74, 1, NULL, '125 Ward Place, Colombo 07', 'apartment', 2, 2.0, 1180, 32000.00, 32000.00, 'Premium location', 'occupied', NULL, '2', 'no', 'in_unit', '2025-10-20 01:02:42'),
(75, 1, NULL, '200 Independence Avenue, Colombo 07', 'condo', 3, 2.5, 1550, 40000.00, 40000.00, 'Luxury high-rise condo', 'available', NULL, '2', 'both', 'in_unit', '2025-10-20 01:02:42'),
(76, 1, NULL, '75 Horton Place, Colombo 07', 'apartment', 2, 1.5, 1050, 29000.00, 29000.00, 'Near Viharamahadevi Park', 'occupied', NULL, '1', 'cats', 'hookups', '2025-10-20 01:02:42'),
(77, 1, NULL, '15 Kynsey Road, Colombo 08', 'apartment', 2, 1.0, 850, 20000.00, 20000.00, 'Central Borella location', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(78, 1, NULL, '88 De Saram Place, Colombo 08', 'apartment', 3, 2.0, 1100, 24000.00, 24000.00, 'Spacious 3BR', 'occupied', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(79, 1, NULL, '22 Castle Street, Colombo 08', 'apartment', 2, 1.5, 900, 21000.00, 21000.00, 'Near Castle Street hospital', 'available', NULL, '1', 'no', 'hookups', '2025-10-20 01:02:42'),
(80, 1, NULL, '45 Generals Lake Road, Colombo 08', 'house', 3, 2.0, 1700, 40000.00, 40000.00, 'House near lake', 'occupied', NULL, '2', 'both', 'in_unit', '2025-10-20 01:02:42'),
(81, 1, NULL, '77 Borella Road, Colombo 08', 'apartment', 2, 1.0, 800, 19000.00, 19000.00, 'Budget-friendly option', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(82, 1, NULL, '33 Wijerama Mawatha, Colombo 08', 'apartment', 3, 2.0, 1150, 25000.00, 25000.00, 'Family apartment', 'available', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(83, 1, NULL, '180 Kynsey Road, Colombo 08', 'apartment', 2, 1.0, 850, 21000.00, 21000.00, 'Well-maintained apartment', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(84, 1, NULL, '99 Baseline Road, Colombo 08', 'apartment', 3, 2.0, 1120, 24500.00, 24500.00, 'Near schools', 'occupied', NULL, '1', 'cats', 'hookups', '2025-10-20 01:02:42'),
(85, 1, NULL, '125 Kynsey Road, Colombo 08', 'apartment', 2, 1.5, 920, 22000.00, 22000.00, 'Near Borella junction', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(86, 1, NULL, '200 De Saram Place, Colombo 08', 'apartment', 3, 2.0, 1140, 25500.00, 25500.00, 'Spacious family unit', 'available', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(87, 1, NULL, '15 Deans Road, Colombo 09', 'apartment', 2, 1.0, 850, 21000.00, 21000.00, 'Quiet residential area', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(88, 1, NULL, '88 Horton Place, Colombo 09', 'apartment', 3, 2.0, 1150, 26000.00, 26000.00, 'Spacious family apartment', 'occupied', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(89, 1, NULL, '22 Melbourne Avenue, Colombo 09', 'apartment', 2, 1.5, 950, 23000.00, 23000.00, 'Well-maintained property', 'available', NULL, '1', 'no', 'hookups', '2025-10-20 01:02:42'),
(90, 1, NULL, '55 Deans Road, Colombo 09', 'apartment', 2, 1.0, 870, 21500.00, 21500.00, 'Near public transport', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(91, 1, NULL, '99 Melbourne Avenue, Colombo 09', 'apartment', 2, 1.0, 880, 22000.00, 22000.00, 'Quiet neighborhood', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(92, 1, NULL, '15 Maradana Road, Colombo 10', 'apartment', 2, 1.0, 750, 17000.00, 17000.00, 'Budget apartment near station', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(93, 1, NULL, '88 George R De Silva Mawatha, Colombo 10', 'apartment', 3, 2.0, 1000, 20000.00, 20000.00, 'Affordable 3BR', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(94, 1, NULL, '22 Bloemendhal Road, Colombo 10', 'apartment', 2, 1.0, 800, 18000.00, 18000.00, 'Near Maradana market', 'available', NULL, '0', 'no', 'none', '2025-10-20 01:02:42'),
(95, 1, NULL, '50 Maradana Road, Colombo 10', 'apartment', 2, 1.5, 820, 18500.00, 18500.00, 'Near railway station', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(96, 1, NULL, '15 Main Street, Colombo 11', 'apartment', 1, 1.0, 600, 15000.00, 15000.00, 'Compact apartment in Pettah', 'occupied', NULL, '0', 'no', 'none', '2025-10-20 01:02:42'),
(97, 1, NULL, '88 Keyzer Street, Colombo 11', 'apartment', 2, 1.0, 750, 17000.00, 17000.00, 'Budget-friendly option', 'available', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(98, 1, NULL, '15 Hulftsdorp Street, Colombo 12', 'apartment', 2, 1.0, 850, 20000.00, 20000.00, 'Near court complex', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(99, 1, NULL, '88 Norris Canal Road, Colombo 12', 'apartment', 3, 2.0, 1100, 23000.00, 23000.00, 'Spacious apartment', 'occupied', NULL, '1', 'cats', 'in_unit', '2025-10-20 01:02:42'),
(100, 1, NULL, '15 Main Street, Colombo 13', 'apartment', 2, 1.0, 750, 16000.00, 16000.00, 'Affordable Kotahena apartment', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(101, 1, NULL, '88 Sea Street, Colombo 13', 'apartment', 3, 2.0, 950, 19000.00, 19000.00, 'Budget 3BR', 'available', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(102, 1, NULL, '15 Grandpass Road, Colombo 14', 'apartment', 2, 1.0, 800, 17000.00, 17000.00, 'Budget apartment', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(103, 1, NULL, '88 Kochchikade Road, Colombo 14', 'apartment', 3, 2.0, 1000, 20000.00, 20000.00, 'Affordable family home', 'occupied', NULL, '1', 'cats', 'shared', '2025-10-20 01:02:42'),
(104, 1, NULL, '15 Mutwal Road, Colombo 15', 'apartment', 2, 1.0, 750, 16000.00, 16000.00, 'Budget-friendly option', 'occupied', NULL, '1', 'no', 'none', '2025-10-20 01:02:42'),
(105, 1, NULL, '88 Mattakkuliya Road, Colombo 15', 'apartment', 3, 2.0, 950, 19000.00, 19000.00, 'Affordable 3BR', 'available', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(106, 1, NULL, '22 Mutwal Lane, Colombo 15', 'apartment', 2, 1.0, 780, 16500.00, 16500.00, 'Near harbor area', 'occupied', NULL, '1', 'no', 'shared', '2025-10-20 01:02:42'),
(107, 1, NULL, '44 Mattakkuliya Avenue, Colombo 15', 'apartment', 3, 2.0, 980, 19500.00, 19500.00, 'Family apartment', 'occupied', NULL, '1', 'cats', 'hookups', '2025-10-20 01:02:42'),
(108, 1, NULL, '125 Mutwal Road, Colombo 15', 'apartment', 2, 1.0, 800, 17000.00, 17000.00, 'Near harbor access', 'available', NULL, '1', 'no', 'none', '2025-10-20 01:02:42');

-- --------------------------------------------------------
-- Service Providers (Colombo based) - includes IDs 1-5
-- --------------------------------------------------------
INSERT INTO `service_providers` (`id`, `name`, `company`, `email`, `phone`, `specialty`, `description`, `rating`, `status`) VALUES
(1, 'Samantha Fernando', 'Colombo Plumbing', 'contact@cps.lk', '0771234567', 'plumbing', 'Licensed plumber with 15 years experience', 4.80, 'active'),
(2, 'Mahesh Gunasekara', 'Mahesh Electric', 'mahesh@electrics.lk', '0712345678', 'electrical', 'Expert in residential and commercial electrical work', 4.90, 'active'),
(3, 'Lakshman Weerasinghe', 'Lakshman AC', 'lakshman@ac.lk', '0765432109', 'hvac', 'Air conditioning installation, repair, and maintenance', 4.70, 'active'),
(4, 'Priyantha Dissanayake', 'Priyantha Pest Control', 'priyantha@pest.lk', '0777654321', 'pest_control', 'Safe and effective pest management', 4.60, 'active'),
(5, 'Ranjith Bandara', 'Ranjith Handyman', 'ranjith@handyman.lk', '0701234567', 'general', 'General repairs, carpentry, painting, minor renovations', 4.50, 'active');

-- --------------------------------------------------------
-- Bookings (spanning 2025 and 2026)
-- --------------------------------------------------------
INSERT INTO `bookings` (`id`, `tenant_id`, `property_id`, `landlord_id`, `move_in_date`, `move_out_date`, `monthly_rent`, `deposit_amount`, `total_amount`, `status`, `rejection_reason`, `cancellation_reason`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 2, '2025-02-01', '2026-02-01', 85000.00, 85000.00, 170000.00, 'active', 'N/A', 'N/A', 'Standard lease - sea-view apartment', '2025-01-25 08:00:00', '2025-02-01 00:00:00'),
(2, 3, 3, 2, '2025-01-20', '2026-01-20', 55000.00, 55000.00, 110000.00, 'active', 'N/A', 'N/A', 'Studio near beach', '2025-01-15 10:00:00', '2025-01-20 00:00:00'),
(3, 3, 5, 2, '2025-02-05', '2026-02-05', 165000.00, 165000.00, 330000.00, 'active', 'N/A', 'N/A', 'Premium condo', '2025-01-30 11:00:00', '2025-02-05 00:00:00'),
(4, 3, 10, 2, '2026-03-01', '2027-03-01', 88000.00, 88000.00, 176000.00, 'active', 'N/A', 'N/A', 'Renovated apartment', '2026-02-20 09:00:00', '2026-03-01 00:00:00'),
(5, 3, 2, 2, '2026-04-01', '2027-04-01', 110000.00, 110000.00, 220000.00, 'active', 'N/A', 'N/A', 'Family apartment - pending lease signing', '2026-03-15 10:00:00', '2026-03-20 12:00:00'),
(6, 3, 8, 2, '2026-05-01', '2027-05-01', 78000.00, 78000.00, 156000.00, 'pending', 'N/A', 'N/A', 'Recent booking request pending landlord review', '2026-04-16 10:00:00', '2026-04-16 10:00:00'),
(7, 3, 9, 2, '2026-05-15', '2027-05-15', 92000.00, 92000.00, 184000.00, 'pending', 'N/A', 'N/A', 'Second recent booking request pending landlord review', '2026-04-18 09:30:00', '2026-04-18 09:30:00'),
(8, 3, 4, 2, '2025-04-01', '2026-03-31', 180000.00, 180000.00, 360000.00, 'completed', 'N/A', 'N/A', 'Completed stay - awaiting tenant review', '2025-03-20 09:15:00', '2026-04-10 14:00:00'),
(9, 3, 6, 2, '2025-05-01', '2026-04-01', 120000.00, 120000.00, 240000.00, 'completed', 'N/A', 'N/A', 'Completed stay - awaiting tenant review', '2025-04-15 10:00:00', '2026-04-12 11:30:00');

-- --------------------------------------------------------
-- Inspections (2025 and 2026)
-- --------------------------------------------------------
INSERT INTO `inspections` (`id`, `property_id`, `issue_id`, `type`, `scheduled_date`, `scheduled_time`, `notes`, `inspection_notes`, `manager_id`, `landlord_id`, `tenant_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'move_in', '2025-01-30', '10:00:00', 'Initial move-in inspection', 'All ok, minor paint touch-up needed', 4, 2, 3, 'completed', '2025-01-25 08:00:00', '2025-01-30 11:00:00'),
(2, 5, 2, 'annual', '2025-12-10', '14:30:00', 'Annual safety check', 'All systems operational', 4, 2, 3, 'completed', '2025-12-01 09:00:00', '2025-12-10 15:30:00'),
(3, 3, 3, 'maintenance', '2025-02-10', '09:00:00', 'Check electrical issue', 'Replaced faulty socket and tested circuit', 4, 2, 3, 'completed', '2025-02-05 10:00:00', '2025-02-10 11:00:00'),
(4, 10, 4, 'move_in', '2026-02-28', '11:00:00', 'Pre-move-in inspection', 'Property ready, deep cleaning scheduled', 4, 2, 3, 'completed', '2026-02-25 08:00:00', '2026-02-28 12:00:00'),
(5, 2, 6, 'routine', '2026-04-15', '10:30:00', 'Quarterly inspection', 'Planned routine inspection for building systems', 4, 2, 3, 'scheduled', '2026-04-01 09:00:00', '2026-04-01 09:00:00');

-- --------------------------------------------------------
-- Issues (2025 and 2026)
-- --------------------------------------------------------
INSERT INTO `issues` (`id`, `tenant_id`, `property_id`, `title`, `description`, `category`, `priority`, `status`, `maintenance_request_id`, `inspection_id`, `resolution_notes`, `assigned_to`, `landlord_id`, `resolved_at`, `pm_notified`, `landlord_notified`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'Leaking kitchen faucet', 'Kitchen sink faucet leaking continuously', 'plumbing', 'high', 'resolved', 1, 1, 'Cartridge replaced and leak tested', 4, 2, '2025-02-15 10:00:00', 1, 1, '2025-02-10 14:00:00', '2025-02-15 10:00:00'),
(2, 3, 5, 'AC not cooling', 'Master bedroom AC not cooling properly', 'hvac', 'medium', 'resolved', 2, 2, 'Gas refilled and filters cleaned', 4, 2, '2025-06-25 14:00:00', 1, 1, '2025-06-20 09:00:00', '2025-06-25 14:00:00'),
(3, 3, 3, 'Power outage', 'One bedroom has no electricity', 'electrical', 'high', 'resolved', 3, 3, 'Replaced wiring and restored power', 4, 2, '2025-02-06 15:00:00', 1, 1, '2025-02-05 11:00:00', '2025-02-06 15:00:00'),
(4, 3, 10, 'Water heater not working', 'No hot water in bathroom', 'plumbing', 'medium', 'in_progress', 4, 4, 'Technician scheduled for replacement parts', 4, 2, '2026-03-20 12:00:00', 1, 1, '2026-03-10 08:00:00', '2026-03-12 10:00:00'),
(5, 3, 1, 'Window lock broken', 'Living room window lock is jammed', 'general', 'low', 'pending', 5, 5, 'Awaiting quotation approval', 4, 2, '2026-04-05 10:00:00', 1, 1, '2026-03-25 14:00:00', '2026-03-26 09:00:00'),
(6, 3, 2, 'Loose balcony railing', 'Balcony railing needs tightening for safety', 'general', 'medium', 'pending', 6, 5, 'Scheduled for routine inspection', 4, 2, '2026-04-20 12:00:00', 1, 1, '2026-03-28 09:00:00', '2026-04-01 09:00:00');

-- --------------------------------------------------------
-- Maintenance Requests (2025 and 2026)
-- --------------------------------------------------------
INSERT INTO `maintenance_requests` (`id`, `property_id`, `landlord_id`, `issue_id`, `provider_id`, `requester_id`, `title`, `description`, `category`, `priority`, `status`, `estimated_cost`, `actual_cost`, `scheduled_date`, `completion_date`, `completion_notes`, `cancellation_reason`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, 1, 4, 'Fix leaking faucet', 'Replace faucet cartridge and seals', 'plumbing', 'high', 'completed', 5500.00, 5500.00, '2025-02-12', '2025-02-15 10:00:00', 'Leak fixed and tested', 'N/A', 'Tenant confirmed fix', '2025-02-10 14:30:00', '2025-02-15 10:00:00'),
(2, 5, 2, 2, 3, 4, 'AC repair', 'Check gas level and compressor', 'hvac', 'medium', 'completed', 12000.00, 11500.00, '2025-06-22', '2025-06-25 14:00:00', 'Cooling restored and filters cleaned', 'N/A', 'Follow-up check completed', '2025-06-20 09:30:00', '2025-06-25 14:00:00'),
(3, 3, 2, 3, 2, 4, 'Electrical issue', 'Check wiring and outlet', 'electrical', 'high', 'completed', 3500.00, 3200.00, '2025-02-06', '2025-02-06 15:00:00', 'Faulty wiring replaced', 'N/A', 'Safety test passed', '2025-02-05 11:30:00', '2025-02-06 15:00:00'),
(4, 10, 2, 4, 1, 4, 'Water heater repair', 'Inspect and repair water heater', 'plumbing', 'medium', 'in_progress', 8500.00, 0.00, '2026-03-15', '2026-03-20 12:00:00', 'Awaiting spare parts delivery', 'N/A', 'Tenant informed about schedule', '2026-03-10 08:30:00', '2026-03-12 10:00:00'),
(5, 1, 2, 5, 5, 4, 'Window lock replacement', 'Replace broken window lock', 'general', 'low', 'pending', 2500.00, 0.00, '2026-03-28', '2026-04-05 10:00:00', 'Pending approval from landlord', 'N/A', 'Quotation under review', '2026-03-25 14:30:00', '2026-03-26 09:00:00'),
(6, 2, 2, 6, 5, 4, 'Balcony railing tightening', 'Tighten screws and check railing stability', 'general', 'medium', 'scheduled', 3000.00, 0.00, '2026-04-18', '2026-04-18 16:00:00', 'Scheduled for routine visit', 'N/A', 'Safety inspection requested', '2026-03-28 10:00:00', '2026-04-01 09:00:00');

-- --------------------------------------------------------
-- Maintenance Quotations (uploaded_by fixed: id 5 changed to 1)
-- --------------------------------------------------------
INSERT INTO `maintenance_quotations` (`id`, `request_id`, `provider_id`, `uploaded_by`, `amount`, `description`, `quotation_file`, `status`, `approved_at`, `approved_by`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 5500.00, 'Replace faucet cartridge and seals', 'quotation_faucet_001.pdf', 'approved', '2025-02-11 10:00:00', 2, 'N/A', '2025-02-11 09:00:00', '2025-02-11 10:00:00'),
(2, 2, 3, 3, 12000.00, 'Refill gas and clean filters', 'quotation_ac_002.pdf', 'approved', '2025-06-21 11:00:00', 2, 'N/A', '2025-06-21 10:00:00', '2025-06-21 11:00:00'),
(3, 3, 2, 2, 3500.00, 'Replace faulty wiring and socket', 'quotation_elec_003.pdf', 'approved', '2025-02-06 14:00:00', 2, 'N/A', '2025-02-06 09:00:00', '2025-02-06 14:00:00'),
(4, 4, 1, 1, 8500.00, 'Replace heating element and thermostat', 'quotation_heater_004.pdf', 'pending', '2026-03-18 10:00:00', 2, 'Pending landlord approval', '2026-03-12 10:00:00', '2026-03-12 10:00:00'),
(5, 5, 5, 1, 2500.00, 'Replace window lock mechanism', 'quotation_lock_005.pdf', 'pending', '2026-04-02 09:00:00', 2, 'Pending landlord approval', '2026-03-26 09:00:00', '2026-03-26 09:00:00'),
(6, 6, 5, 4, 3000.00, 'Tighten railing and replace screws', 'quotation_railing_006.pdf', 'approved', '2026-04-05 09:30:00', 2, 'N/A', '2026-04-01 09:00:00', '2026-04-05 09:30:00');

-- --------------------------------------------------------
-- Maintenance Payments
-- --------------------------------------------------------
INSERT INTO `maintenance_payments` (`id`, `request_id`, `quotation_id`, `landlord_id`, `amount`, `payment_method`, `transaction_id`, `status`, `payment_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 5500.00, 'bank_transfer', 'MNT1001', 'completed', '2025-02-11 15:00:00', 'Paid after approval', '2025-02-11 15:00:00', '2025-02-11 15:00:00'),
(2, 2, 2, 2, 11500.00, 'credit_card', 'MNT1002', 'completed', '2025-06-22 09:00:00', 'Paid with company card', '2025-06-22 09:00:00', '2025-06-22 09:00:00'),
(3, 3, 3, 2, 3200.00, 'bank_transfer', 'MNT1003', 'completed', '2025-02-06 16:00:00', 'Bank transfer cleared', '2025-02-06 16:00:00', '2025-02-06 16:00:00'),
(4, 6, 6, 2, 3000.00, 'bank_transfer', 'MNT1004', 'completed', '2026-04-06 10:00:00', 'Routine maintenance payment', '2026-04-06 10:00:00', '2026-04-06 10:00:00');

-- --------------------------------------------------------
-- Lease Agreements (2025 and 2026)
-- --------------------------------------------------------
INSERT INTO `lease_agreements` (`id`, `tenant_id`, `landlord_id`, `property_id`, `booking_id`, `start_date`, `end_date`, `monthly_rent`, `deposit_amount`, `lease_duration_months`, `terms_and_conditions`, `status`, `signed_by_tenant`, `signed_by_landlord`, `tenant_signature_date`, `landlord_signature_date`, `termination_reason`, `termination_date`, `created_at`, `updated_at`) VALUES
(1, 3, 2, 1, 1, '2025-02-01', '2026-02-01', 85000.00, 85000.00, 12, 'Standard terms apply. Rent due on 1st of each month.', 'active', 1, 1, '2025-01-28 10:00:00', '2025-01-29 14:00:00', 'N/A', '2026-02-01', '2025-01-25 08:00:00', '2026-01-15 09:00:00'),
(2, 3, 2, 3, 2, '2025-01-20', '2026-01-20', 55000.00, 55000.00, 12, 'No pets allowed. Parking included.', 'active', 1, 1, '2025-01-18 10:30:00', '2025-01-19 15:00:00', 'N/A', '2026-01-20', '2025-01-15 10:00:00', '2025-12-15 10:00:00'),
(3, 3, 2, 5, 3, '2025-02-05', '2026-02-05', 165000.00, 165000.00, 12, 'Condo rules apply. No smoking inside premises.', 'active', 1, 1, '2025-02-02 11:00:00', '2025-02-03 16:00:00', 'N/A', '2026-02-05', '2025-01-30 11:00:00', '2026-01-10 11:00:00'),
(4, 3, 2, 10, 4, '2026-03-01', '2027-03-01', 88000.00, 88000.00, 12, 'Water and electricity meters to be read monthly.', 'active', 1, 1, '2026-02-25 09:00:00', '2026-02-26 13:00:00', 'N/A', '2027-03-01', '2026-02-20 09:00:00', '2026-03-01 09:00:00');

-- --------------------------------------------------------
-- Payments (2025 and 2026)
-- --------------------------------------------------------
INSERT INTO `payments` (`id`, `tenant_id`, `landlord_id`, `property_id`, `booking_id`, `amount`, `payment_method`, `transaction_id`, `status`, `payment_date`, `due_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, 2, 1, 1, 85000.00, 'bank_transfer', 'TXN1001', 'completed', '2025-02-01 08:00:00', '2025-02-01', 'Rent for February', '2025-02-01 08:00:00', '2025-02-01 08:00:00'),
(2, 3, 2, 1, 1, 85000.00, 'bank_transfer', 'TXN1002', 'completed', '2025-03-01 09:00:00', '2025-03-01', 'Rent for March', '2025-03-01 09:00:00', '2025-03-01 09:00:00'),
(3, 3, 2, 1, 1, 85000.00, 'credit_card', 'TXN1003', 'completed', '2025-04-01 10:00:00', '2025-04-01', 'Rent for April', '2025-04-01 10:00:00', '2025-04-01 10:00:00'),
(4, 3, 2, 3, 2, 55000.00, 'bank_transfer', 'TXN1004', 'completed', '2025-01-20 10:00:00', '2025-01-20', 'Rent for January', '2025-01-20 10:00:00', '2025-01-20 10:00:00'),
(5, 3, 2, 3, 2, 55000.00, 'debit_card', 'TXN1005', 'completed', '2025-02-20 11:00:00', '2025-02-20', 'Rent for February', '2025-02-20 11:00:00', '2025-02-20 11:00:00'),
(6, 3, 2, 3, 2, 55000.00, 'bank_transfer', 'TXN1006', 'completed', '2025-03-20 09:30:00', '2025-03-20', 'Rent for March', '2025-03-20 09:30:00', '2025-03-20 09:30:00'),
(7, 3, 2, 5, 3, 165000.00, 'bank_transfer', 'TXN1007', 'completed', '2025-02-05 11:00:00', '2025-02-05', 'Rent for February', '2025-02-05 11:00:00', '2025-02-05 11:00:00'),
(8, 3, 2, 5, 3, 165000.00, 'credit_card', 'TXN1008', 'completed', '2025-03-05 10:00:00', '2025-03-05', 'Rent for March', '2025-03-05 10:00:00', '2025-03-05 10:00:00'),
(9, 3, 2, 10, 4, 88000.00, 'bank_transfer', 'TXN1009', 'completed', '2026-03-01 09:00:00', '2026-03-01', 'Rent for March', '2026-03-01 09:00:00', '2026-03-01 09:00:00'),
(10, 3, 2, 10, 4, 88000.00, 'bank_transfer', 'TXN1010', 'pending', '2026-04-01 09:00:00', '2026-04-01', 'Rent for April', '2026-03-01 09:00:00', '2026-03-25 08:00:00');

-- --------------------------------------------------------
-- Notifications (for all users, 2025 and 2026)
-- --------------------------------------------------------
INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 3, 'booking', 'Booking Confirmed', 'Your booking for property at Galle Road, Colombo 03 is confirmed.', '/tenant/bookings', 1, '2025-01-25 08:30:00'),
(2, 2, 'payment', 'Payment Received', 'Tenant paid LKR 85,000 for property ID 1.', '/landlord/payments', 0, '2025-02-01 08:15:00'),
(3, 4, 'maintenance_request', 'New Request', 'New maintenance request for property ID 1 - leaking faucet.', '/maintenance/details/1', 0, '2025-02-10 14:35:00'),
(4, 3, 'maintenance', 'Issue Resolved', 'Your reported power outage issue has been resolved.', '/issues/track', 0, '2025-02-07 09:00:00'),
(5, 2, 'lease', 'Lease Expiring', 'Lease for property ID 1 expires in 30 days.', '/landlord/leases', 0, '2026-01-01 10:00:00'),
(6, 3, 'payment', 'Payment Reminder', 'Rent payment of LKR 88,000 for property ID 10 is due on April 1, 2026.', '/tenant/payments', 0, '2026-03-25 08:00:00'),
(7, 4, 'inspection', 'Inspection Scheduled', 'Routine inspection scheduled for property ID 2 on April 15, 2026.', '/inspections', 0, '2026-04-01 09:00:00'),
(8, 2, 'maintenance', 'Quotation Pending', 'New quotation for water heater repair (property ID 10) requires your approval.', '/landlord/maintenance', 0, '2026-03-12 10:00:00'),
(9, 1, 'admin', 'New Property Registered', 'New property at No. 25, Park Road, Colombo 05 has been registered.', '/admin/properties', 0, '2026-02-25 12:00:00'),
(10, 3, 'booking', 'Booking Approved', 'Your booking for property at Duplication Road, Colombo 04 has been approved.', '/tenant/bookings', 0, '2026-03-20 12:00:00');

-- --------------------------------------------------------
-- Property Manager Details
-- --------------------------------------------------------
INSERT INTO `property_manager` (`manager_id`, `user_id`, `employee_id_filename`, `approval_status`, `approved_at`, `phone`) VALUES
(1, 4, 'pm_id_001.pdf', 'approved', '2025-01-10 10:00:00', '0771234567');

-- --------------------------------------------------------
-- Policies
-- --------------------------------------------------------
INSERT INTO `policies` (`policy_id`, `policy_name`, `policy_category`, `policy_description`, `policy_content`, `policy_version`, `policy_status`, `policy_type`, `effective_date`, `created_by`, `created_at`) VALUES
(1, 'Rental Policy', 'rental', 'Rental terms', 'Rent due on 1st of each month. Late fee 5% after 5 days.', 'v1.0', 'active', 'standard', '2025-01-01', 1, '2025-01-01 00:00:00'),
(2, 'Maintenance Policy', 'maintenance', 'Request rules', 'Emergency: 24h, Non-emergency: 7 days.', 'v1.0', 'active', 'standard', '2025-01-01', 1, '2025-01-01 00:00:00'),
(3, 'Security Deposit Policy', 'financial', 'Deposit rules', 'Refundable within 30 days after lease end minus damages.', 'v1.0', 'active', 'standard', '2025-01-01', 1, '2025-01-01 00:00:00'),
(4, 'Privacy Policy', 'privacy', 'Data privacy', 'User data is not shared with third parties.', 'v1.0', 'active', 'standard', '2025-01-01', 1, '2025-01-01 00:00:00'),
(5, 'Terms of Service', 'terms_of_service', 'Platform terms', 'By using this platform you agree to all terms.', 'v1.0', 'active', 'standard', '2025-01-01', 1, '2025-01-01 00:00:00'),
(6, 'Privacy Policy - Data Use', 'privacy', 'Data usage policy', 'We only use data to provide services and improve support.', 'v2.0', 'active', 'standard', '2025-02-01', 1, '2025-02-01 00:00:00'),
(7, 'Privacy Policy - Security', 'privacy', 'Data security', 'We apply encryption and access controls to protect data.', 'v2.0', 'active', 'standard', '2025-02-01', 1, '2025-02-01 00:00:00'),
(8, 'Privacy Policy - Cookies', 'privacy', 'Cookie notice', 'Cookies are used for login, preferences, and analytics.', 'v2.0', 'active', 'standard', '2025-02-01', 1, '2025-02-01 00:00:00'),
(9, 'Terms of Service - Usage', 'terms_of_service', 'Service usage', 'Users must follow platform rules and applicable laws.', 'v3.0', 'active', 'standard', '2025-02-01', 1, '2025-02-01 00:00:00'),
(10, 'Terms of Service - Payments', 'terms_of_service', 'Payment terms', 'Late payments may incur fees and account restrictions.', 'v3.0', 'active', 'standard', '2025-02-01', 1, '2025-02-01 00:00:00'),
(11, 'Terms of Service - Termination', 'terms_of_service', 'Account termination', 'Accounts may be suspended for misuse or fraud.', 'v3.0', 'active', 'standard', '2025-02-01', 1, '2025-02-01 00:00:00');

-- --------------------------------------------------------
-- Reviews
-- --------------------------------------------------------
INSERT INTO `reviews` (`id`, `reviewer_id`, `reviewee_id`, `property_id`, `booking_id`, `rating`, `review_text`, `review_type`, `status`, `created_at`) VALUES
(1, 3, 2, 1, 1, 5, 'Excellent property and responsive landlord. Very clean and well maintained.', 'property', 'approved', '2025-02-05 10:00:00'),
(2, 3, 2, 3, 2, 4, 'Great location and good value. Minor maintenance issues resolved quickly.', 'property', 'approved', '2025-01-25 14:00:00'),
(3, 3, 2, 5, 3, 5, 'Absolutely love this condo! Premium amenities and perfect location.', 'property', 'approved', '2025-03-10 11:00:00'),
(4, 2, 3, 4, 8, 5, 'Tenant maintained the house very well and communicated professionally throughout the lease.', 'tenant', 'approved', '2026-04-11 09:00:00'),
(5, 2, 3, 6, 9, 4, 'Responsible tenant with on-time payments and cooperative behavior during inspections.', 'tenant', 'approved', '2026-04-13 10:30:00');

-- --------------------------------------------------------
-- Password Resets (for demo)
-- --------------------------------------------------------
INSERT INTO `password_resets` (`id`, `email`, `code`, `expires_at`, `created_at`) VALUES
(1, 'amal@tenant.com', '482913', '2026-04-18 10:30:00', '2026-04-18 09:30:00'),
(2, 'nimal@landlord.com', '739205', '2026-04-18 11:00:00', '2026-04-18 10:00:00');

-- --------------------------------------------------------
-- Posts (demo content)
-- --------------------------------------------------------
INSERT INTO `Posts` (`id`, `user_id`, `title`, `body`, `created_at`) VALUES
(1, 1, 'Welcome to Rentigo', 'We are excited to launch the Colombo property marketplace.', '2025-01-05 09:00:00'),
(2, 2, 'Landlord Tips: Screening Tenants', 'Always verify references and keep clear documentation.', '2025-02-10 15:30:00'),
(3, 4, 'Maintenance Best Practices', 'Log issues early to avoid larger repairs later.', '2026-03-05 12:00:00');

-- --------------------------------------------------------
-- Indexes
-- --------------------------------------------------------
ALTER TABLE `users` ADD PRIMARY KEY (`id`);
ALTER TABLE `properties` ADD PRIMARY KEY (`id`), ADD KEY `idx_landlord_id` (`landlord_id`), ADD KEY `idx_manager_id` (`manager_id`), ADD KEY `idx_status` (`status`);
ALTER TABLE `market_properties` ADD PRIMARY KEY (`id`), ADD KEY `idx_type_bedrooms` (`property_type`,`bedrooms`), ADD KEY `idx_rent` (`rent`), ADD KEY `idx_status` (`status`);
ALTER TABLE `bookings` ADD PRIMARY KEY (`id`), ADD KEY `idx_tenant_id` (`tenant_id`), ADD KEY `idx_property_id` (`property_id`), ADD KEY `idx_landlord_id` (`landlord_id`), ADD KEY `idx_status` (`status`);
ALTER TABLE `inspections` ADD PRIMARY KEY (`id`), ADD KEY `idx_status` (`status`), ADD KEY `fk_inspections_property` (`property_id`), ADD KEY `fk_inspections_issue` (`issue_id`), ADD KEY `fk_inspections_manager` (`manager_id`);
ALTER TABLE `issues` ADD PRIMARY KEY (`id`), ADD KEY `idx_tenant_id` (`tenant_id`), ADD KEY `idx_property_id` (`property_id`), ADD KEY `idx_status` (`status`);
ALTER TABLE `service_providers` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `idx_email` (`email`), ADD KEY `idx_specialty` (`specialty`), ADD KEY `idx_status` (`status`);
ALTER TABLE `maintenance_requests` ADD PRIMARY KEY (`id`), ADD KEY `idx_property_id` (`property_id`), ADD KEY `idx_landlord_id` (`landlord_id`), ADD KEY `idx_issue_id` (`issue_id`), ADD KEY `idx_provider_id` (`provider_id`), ADD KEY `idx_requester_id` (`requester_id`), ADD KEY `idx_status` (`status`);
ALTER TABLE `maintenance_quotations` ADD PRIMARY KEY (`id`), ADD KEY `request_id` (`request_id`), ADD KEY `provider_id` (`provider_id`), ADD KEY `uploaded_by` (`uploaded_by`), ADD KEY `approved_by` (`approved_by`);
ALTER TABLE `maintenance_payments` ADD PRIMARY KEY (`id`), ADD KEY `request_id` (`request_id`), ADD KEY `quotation_id` (`quotation_id`), ADD KEY `landlord_id` (`landlord_id`);
ALTER TABLE `lease_agreements` ADD PRIMARY KEY (`id`), ADD KEY `idx_tenant_id` (`tenant_id`), ADD KEY `idx_landlord_id` (`landlord_id`), ADD KEY `idx_property_id` (`property_id`), ADD KEY `idx_booking_id` (`booking_id`), ADD KEY `idx_status` (`status`);
ALTER TABLE `payments` ADD PRIMARY KEY (`id`), ADD KEY `idx_tenant_id` (`tenant_id`), ADD KEY `idx_landlord_id` (`landlord_id`), ADD KEY `idx_property_id` (`property_id`), ADD KEY `idx_booking_id` (`booking_id`), ADD KEY `idx_status` (`status`), ADD KEY `idx_due_date` (`due_date`);
ALTER TABLE `notifications` ADD PRIMARY KEY (`id`), ADD KEY `idx_user_id` (`user_id`), ADD KEY `idx_is_read` (`is_read`), ADD KEY `idx_type` (`type`);
ALTER TABLE `property_manager` ADD PRIMARY KEY (`manager_id`), ADD UNIQUE KEY `idx_user_id` (`user_id`);
ALTER TABLE `policies` ADD PRIMARY KEY (`policy_id`), ADD KEY `idx_policy_status` (`policy_status`), ADD KEY `idx_policy_category` (`policy_category`), ADD KEY `idx_created_by` (`created_by`);
ALTER TABLE `reviews` ADD PRIMARY KEY (`id`), ADD KEY `idx_reviewer_id` (`reviewer_id`), ADD KEY `idx_reviewee_id` (`reviewee_id`), ADD KEY `idx_property_id` (`property_id`), ADD KEY `idx_booking_id` (`booking_id`), ADD KEY `idx_review_type` (`review_type`), ADD KEY `idx_status` (`status`);
ALTER TABLE `password_resets` ADD PRIMARY KEY (`id`);
ALTER TABLE `Posts` ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`);

-- --------------------------------------------------------
-- AUTO_INCREMENT
-- --------------------------------------------------------
ALTER TABLE `users` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `properties` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `market_properties` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;
ALTER TABLE `bookings` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
ALTER TABLE `inspections` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `issues` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `service_providers` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `maintenance_requests` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `maintenance_quotations` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `maintenance_payments` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `lease_agreements` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `payments` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `notifications` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `property_manager` MODIFY `manager_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `policies` MODIFY `policy_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
ALTER TABLE `reviews` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `password_resets` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `Posts` MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- --------------------------------------------------------
-- Foreign Keys
-- --------------------------------------------------------
ALTER TABLE `bookings` ADD CONSTRAINT `fk_bookings_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `bookings` ADD CONSTRAINT `fk_bookings_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
ALTER TABLE `bookings` ADD CONSTRAINT `fk_bookings_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `inspections` ADD CONSTRAINT `fk_inspections_issue` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE SET NULL;
ALTER TABLE `inspections` ADD CONSTRAINT `fk_inspections_manager` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `inspections` ADD CONSTRAINT `fk_inspections_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
ALTER TABLE `issues` ADD CONSTRAINT `fk_issues_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
ALTER TABLE `issues` ADD CONSTRAINT `fk_issues_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `lease_agreements` ADD CONSTRAINT `fk_leases_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
ALTER TABLE `lease_agreements` ADD CONSTRAINT `fk_leases_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `lease_agreements` ADD CONSTRAINT `fk_leases_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
ALTER TABLE `lease_agreements` ADD CONSTRAINT `fk_leases_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `maintenance_payments` ADD CONSTRAINT `fk_payment_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `maintenance_payments` ADD CONSTRAINT `fk_payment_quotation` FOREIGN KEY (`quotation_id`) REFERENCES `maintenance_quotations` (`id`) ON DELETE CASCADE;
ALTER TABLE `maintenance_payments` ADD CONSTRAINT `fk_payment_request` FOREIGN KEY (`request_id`) REFERENCES `maintenance_requests` (`id`) ON DELETE CASCADE;
ALTER TABLE `maintenance_quotations` ADD CONSTRAINT `fk_quotation_provider` FOREIGN KEY (`provider_id`) REFERENCES `service_providers` (`id`) ON DELETE CASCADE;
ALTER TABLE `maintenance_quotations` ADD CONSTRAINT `fk_quotation_request` FOREIGN KEY (`request_id`) REFERENCES `maintenance_requests` (`id`) ON DELETE CASCADE;
ALTER TABLE `maintenance_quotations` ADD CONSTRAINT `fk_quotation_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `maintenance_requests` ADD CONSTRAINT `fk_maintenance_issue` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE SET NULL;
ALTER TABLE `maintenance_requests` ADD CONSTRAINT `fk_maintenance_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `maintenance_requests` ADD CONSTRAINT `fk_maintenance_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
ALTER TABLE `maintenance_requests` ADD CONSTRAINT `fk_maintenance_provider` FOREIGN KEY (`provider_id`) REFERENCES `service_providers` (`id`) ON DELETE SET NULL;
ALTER TABLE `maintenance_requests` ADD CONSTRAINT `fk_maintenance_requester` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `notifications` ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `payments` ADD CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
ALTER TABLE `payments` ADD CONSTRAINT `fk_payments_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `payments` ADD CONSTRAINT `fk_payments_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
ALTER TABLE `payments` ADD CONSTRAINT `fk_payments_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `policies` ADD CONSTRAINT `fk_policies_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `Posts` ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `properties` ADD CONSTRAINT `fk_properties_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `properties` ADD CONSTRAINT `fk_properties_manager` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `property_manager` ADD CONSTRAINT `fk_property_manager_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `reviews` ADD CONSTRAINT `fk_reviews_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
ALTER TABLE `reviews` ADD CONSTRAINT `fk_reviews_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
ALTER TABLE `reviews` ADD CONSTRAINT `fk_reviews_reviewee` FOREIGN KEY (`reviewee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `reviews` ADD CONSTRAINT `fk_reviews_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;