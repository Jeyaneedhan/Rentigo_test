-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 14, 2025 at 05:49 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rentigo_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

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
  `status` enum('pending','approved','rejected','active','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancellation_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspections`
--

CREATE TABLE `inspections` (
  `id` int NOT NULL,
  `property` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `issues` int DEFAULT '0',
  `type` enum('routine','move_in','move_out','maintenance','annual','emergency','issue') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `scheduled_date` date NOT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inspections`
--

INSERT INTO `inspections` (`id`, `property`, `issues`, `type`, `scheduled_date`, `status`, `created_at`, `updated_at`) VALUES
(1, '123 Main Street, Colombo 03, Sri Lanka', 0, 'routine', '2025-12-01', 'scheduled', '2025-11-13 13:00:29', '2025-11-13 13:00:29'),
(2, '456 Ocean View Road, Colombo 03, Sri Lanka', 0, 'move_in', '2025-11-20', 'scheduled', '2025-11-13 13:00:29', '2025-11-13 13:00:29');

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL,
  `property_id` int NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` enum('low','medium','high','emergency') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `status` enum('pending','in_progress','resolved','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lease_agreements`
--

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
  `terms_and_conditions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','pending_signatures','active','completed','terminated') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `signed_by_tenant` tinyint(1) NOT NULL DEFAULT '0',
  `signed_by_landlord` tinyint(1) NOT NULL DEFAULT '0',
  `tenant_signature_date` timestamp NULL DEFAULT NULL,
  `landlord_signature_date` timestamp NULL DEFAULT NULL,
  `termination_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `termination_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `id` int NOT NULL,
  `property_id` int NOT NULL,
  `landlord_id` int NOT NULL,
  `issue_id` int DEFAULT NULL,
  `provider_id` int DEFAULT NULL,
  `requester_id` int NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('low','medium','high','emergency') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` enum('pending','scheduled','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `estimated_cost` decimal(10,2) DEFAULT NULL,
  `actual_cost` decimal(10,2) DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `completion_date` timestamp NULL DEFAULT NULL,
  `completion_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancellation_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `market_properties`
--

CREATE TABLE `market_properties` (
  `id` int NOT NULL,
  `landlord_id` int NOT NULL DEFAULT '1',
  `manager_id` int DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_type` enum('apartment','house','condo','townhouse') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bedrooms` int NOT NULL,
  `bathrooms` decimal(2,1) NOT NULL,
  `sqft` int DEFAULT NULL,
  `rent` decimal(10,2) NOT NULL,
  `deposit` decimal(10,2) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('available','occupied','maintenance','vacant','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `available_date` date DEFAULT NULL,
  `parking` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `pet_policy` enum('no','cats','dogs','both') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `laundry` enum('none','shared','in_unit','hookups','included') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `market_properties`
--

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

--
-- Table structure for table `messages`
--
- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL,
  `landlord_id` int NOT NULL,
  `property_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `transaction_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` enum('pending','completed','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `due_date` date NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `policy_id` int NOT NULL,
  `policy_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_category` enum('rental','security','maintenance','financial','general','privacy','terms_of_service','refund','data_protection') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `policy_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `policy_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_version` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1.0',
  `policy_status` enum('draft','active','inactive','archived','under_review') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'draft',
  `policy_type` enum('standard','custom') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'standard',
  `effective_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `policies`
--

INSERT INTO `policies` (`policy_id`, `policy_name`, `policy_category`, `policy_description`, `policy_content`, `policy_version`, `policy_status`, `policy_type`, `effective_date`, `expiry_date`, `last_updated`, `created_by`, `created_at`) VALUES
(1, 'ewewewee333', 'security', NULL, 'kgghkghghghkgkhghkghkghhgkkghghkhjkhkjhjhjjhhjkhjktvtvytyotoytoyvytouytovutoyvuotyvuyuto', 'v1.0', 'draft', 'standard', '2025-11-14', NULL, '2025-11-14 03:40:18', 7, '2025-11-14 03:40:18');

-- --------------------------------------------------------

--
-- Table structure for table `Posts`
--

CREATE TABLE `Posts` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int NOT NULL,
  `landlord_id` int NOT NULL,
  `manager_id` int DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_type` enum('apartment','house','condo','townhouse') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_purpose` enum('rent','maintenance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rent',
  `listing_type` enum('rent','maintenance','rental') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rent',
  `bedrooms` int NOT NULL,
  `bathrooms` decimal(2,1) NOT NULL,
  `sqft` int DEFAULT NULL,
  `rent` decimal(10,2) NOT NULL,
  `deposit` decimal(10,2) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `current_occupant` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('available','occupied','maintenance','pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approval_status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `available_date` date DEFAULT NULL,
  `parking` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `pet_policy` enum('no','cats','dogs','both') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `laundry` enum('none','shared','in_unit','hookups','included') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `tenant` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `landlord_id`, `manager_id`, `address`, `property_type`, `property_purpose`, `listing_type`, `bedrooms`, `bathrooms`, `sqft`, `rent`, `deposit`, `description`, `current_occupant`, `status`, `approval_status`, `approved_at`, `available_date`, `parking`, `pet_policy`, `laundry`, `tenant`, `issue`, `created_at`, `updated_at`) VALUES
(3, 3, NULL, '789 Park Avenue, Colombo 05, Sri Lanka', 'house', 'rent', 'rental', 4, 3.0, 2000, 75000.00, 75000.00, 'Spacious family house with garden and parking', NULL, 'available', 'approved', NULL, NULL, '2', 'both', 'in_unit', NULL, NULL, '2025-11-13 12:59:04', '2025-11-14 03:43:54'),
(4, 2, NULL, '321 Green Street, Colombo 07, Sri Lanka', 'apartment', 'rent', 'rental', 1, 1.0, 650, 25000.00, 25000.00, 'Cozy studio apartment perfect for singles', NULL, 'occupied', 'approved', NULL, NULL, '1', 'no', 'shared', NULL, NULL, '2025-11-13 12:59:04', '2025-11-13 13:25:40'),
(5, 3, NULL, '654 Hill Road, Colombo 04, Sri Lanka', 'townhouse', 'rent', 'rental', 3, 2.5, 1500, 55000.00, 55000.00, 'Modern townhouse in quiet neighborhood', NULL, 'available', 'approved', NULL, NULL, '2', 'dogs', 'in_unit', NULL, NULL, '2025-11-13 12:59:04', '2025-11-13 13:25:40');

-- --------------------------------------------------------

--
-- Table structure for table `property_manager`
--

CREATE TABLE `property_manager` (
  `manager_id` int NOT NULL,
  `user_id` int NOT NULL,
  `employee_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_id_document` longblob,
  `employee_id_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_id_filetype` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_id_filesize` int DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `approved_by` int DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_manager`
--

INSERT INTO `property_manager` (`manager_id`, `user_id`, `employee_id`, `employee_id_document`, `employee_id_filename`, `employee_id_filetype`, `employee_id_filesize`, `approval_status`, `approved_at`, `rejection_reason`, `approved_by`, `phone`, `created_at`, `updated_at`) VALUES
(1, 7, 'EMP001', NULL, NULL, NULL, NULL, 'approved', '2025-11-13 13:00:29', NULL, NULL, NULL, '2025-11-13 13:00:29', '2025-11-13 13:41:54');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `reviewer_id` int NOT NULL,
  `reviewee_id` int NOT NULL,
  `property_id` int DEFAULT NULL,
  `booking_id` int DEFAULT NULL,
  `rating` int NOT NULL,
  `review_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `review_type` enum('property','tenant') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `service_providers`
--

CREATE TABLE `service_providers` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialty` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT '0.00',
  `status` enum('active','inactive','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_providers`
--

INSERT INTO `service_providers` (`id`, `name`, `company`, `email`, `address`, `phone`, `specialty`, `description`, `hourly_rate`, `rating`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Quick Fix Plumbing', NULL, 'contact@quickfixplumbing.lk', NULL, '+94112345678', 'Plumbing', 'Professional plumbing services for residential and commercial properties', 3500.00, 4.50, 'inactive', '2025-11-13 13:00:29', '2025-11-14 03:45:59'),
(2, 'Cool Air HVAC Services', NULL, 'info@coolair.lk', NULL, '+94112345679', 'HVAC', 'Air conditioning installation, repair, and maintenance services', 4000.00, 4.80, 'active', '2025-11-13 13:00:29', '2025-11-13 13:00:29'),
(3, 'Bright Spark Electricians', NULL, 'hello@brightspark.lk', NULL, '+94112345680', 'Electrical', 'Licensed electricians for all electrical work', 3800.00, 4.60, 'active', '2025-11-13 13:00:29', '2025-11-13 13:00:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_type` enum('admin','property_manager','tenant','landlord') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'tenant',
  `account_status` enum('pending','active','suspended','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'active',
  `terms_accepted_at` timestamp NULL DEFAULT NULL,
  `terms_version` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '1.0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`, `user_type`, `account_status`, `terms_accepted_at`, `terms_version`) VALUES
(1, 'System Administrator', 'admin@rentigo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-13 12:59:04', '2025-11-13 12:59:04', 'admin', 'active', '2025-11-13 12:59:04', '1.0'),
(2, 'John Landlord', 'landlord1@rentigo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-13 12:59:04', '2025-11-13 12:59:04', 'landlord', 'active', NULL, '1.0'),
(3, 'Sarah Property Owner', 'landlord2@rentigo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-13 12:59:04', '2025-11-13 12:59:04', 'landlord', 'active', NULL, '1.0'),
(5, 'admin 3', 'admin21@gmail.com', '$2y$10$XSQmVw0J04AGvytHo8Q5GePpZguh910aXM5h5urDCM5BftaMIMnEu', '2025-08-15 21:44:14', '2025-10-19 02:39:06', 'admin', 'active', NULL, '1.0'),
(6, 'admin2', 'admin33@gmail.com', '$2y$10$wx1nbtg8j4cmy8nJRgdQEedsxCg/YOI83dxAIVBc4DLsFi57bRnfS', '2025-08-16 21:42:34', '2025-10-19 02:39:06', 'admin', 'active', NULL, '1.0'),
(7, 'admin', 'admin@gmail.com', '$2y$10$fbFy0ru98K8utip/GHOUg.O3SNvHdWZVv8UryeTG3AophzeFl/OVa', '2025-08-18 15:11:40', '2025-11-13 13:31:35', 'admin', 'active', NULL, '1.0'),
(8, 'admin', 'admin334@gmail.com', '$2y$10$ht3QE05bXzTaO9zRacm3w.m5xuMgTX1HS7n1bnrr20FXe.kdp8Vi.', '2025-08-18 15:11:46', '2025-10-19 02:39:06', 'admin', 'active', NULL, '1.0'),
(9, 'land', 'land@gmail.com', '$2y$10$CIY8aWJi5PUm.85VBK.v3OdwqVBER3fAIDBWhayXK07OENb4/xKui', '2025-08-19 23:37:18', '2025-10-19 02:39:06', 'landlord', 'active', NULL, '1.0'),
(10, 'land', 'land2@gmail.com', '$2y$10$YgTjrEcRqlT5A0.xp.tlqOqXHBYmtdwRazFCOyAAsFp9IkE12g9k2', '2025-08-20 10:58:30', '2025-10-19 02:39:06', 'landlord', 'active', NULL, '1.0'),
(12, 'tenat', 'tenat@gmail.com', '$2y$10$HIY4JR/pxqdEEG26RCHGTe2OH8tC99p5t2zyOFE9ncH55gmm3D0Vi', '2025-08-21 13:50:26', '2025-10-19 02:39:06', 'tenant', 'active', NULL, '1.0'),
(13, 'tenant 22', 'tenant@gmail.com', '$2y$10$hz2VNOYBK0ZXEnzXwhNFFO6MuAIw5/SwZAeSRY4rjjXKs8/EiAxxm', '2025-08-25 14:46:07', '2025-10-21 04:04:43', 'tenant', 'active', NULL, '1.0'),
(14, 'landlord', 'landlord@gmail.com', '$2y$10$27MpCTraIRlTrBcbi.eP4uzAHsfoICuCbG7bQkgKX9ooBKlEBvbuO', '2025-08-25 15:17:48', '2025-10-19 02:39:06', 'landlord', 'active', NULL, '1.0'),
(16, 'admin main', 'admin3@gmail.com', '$2y$10$L0dpFtyyXQ6/idRdr8myN.ccmtvfOnM4PGYPe/aGJY9.RHYB8qJ32', '2025-08-25 17:35:19', '2025-10-19 02:39:06', 'tenant', 'active', NULL, '1.0'),
(17, 'tenant2', 'tenant2@gmail.com', '$2y$10$vOv0aOgB1wje7E5ZrAfmwu5XkCPbPMCg8Oep0Oy5guJaF5MTM0h9C', '2025-08-26 13:26:16', '2025-10-19 02:39:06', 'tenant', 'active', NULL, '1.0'),
(33, 'PropertyManager2', 'pmmanager@gmail.com', '$2y$10$LoqVjW6yvBgh.AToIQ6.AOImH6xa0eQ5ynjz5R0CJrzXZb0gxeade', '2025-10-19 08:59:19', '2025-10-21 05:04:20', 'property_manager', 'active', '2025-10-19 03:29:19', '1.0'),
(34, 'PropertyManager2', '2pmmanager@gmail.com', '$2y$10$AdHemlyiuhYf8.P8Cd4yoekpekkxgSL8Euvs0E6l30DE.MTi3v93e', '2025-10-19 09:02:19', '2025-10-19 03:32:34', 'property_manager', 'active', '2025-10-19 03:32:19', '1.0'),
(35, '3PropertyManager', '3pmmanager@gmail.com', '$2y$10$lOXMauSob2plQ3cdtUeCP.1bwhqSxXYER5NFIoBS9KP4qbmxVAVbG', '2025-10-19 09:03:57', '2025-10-19 03:37:37', 'property_manager', 'rejected', '2025-10-19 03:33:57', '1.0'),
(36, 'landlord2', 'landlord2@gmail.com', '$2y$10$V9z4FwIP.Mtv7mV34YkQPOLE8Ksk0n7ncMEXfbaSh.51Y8csqdmI6', '2025-10-21 08:14:42', '2025-10-21 02:44:42', 'landlord', 'active', '2025-10-21 02:44:42', '1.0'),
(38, 'property manager4', 'propertymanager3@gmail.com', '$2y$10$qX5dmuAJpNl/q62JOeOtE..T3kvjtKCDhHU3fiE4TwDq8hUMvwnbe', '2025-10-21 13:47:19', '2025-10-21 08:38:28', 'property_manager', 'active', '2025-10-21 08:17:19', '1.0');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_posts`
-- (See below for the actual view)
--
CREATE TABLE `v_posts` (
`post_id` int
,`user_id` int
,`user_name` varchar(255)
,`title` varchar(255)
,`body` text
,`post_created_at` datetime
,`user_created_at` datetime
);

-- --------------------------------------------------------

--
-- Structure for view `v_posts`
--
DROP TABLE IF EXISTS `v_posts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_posts`  AS SELECT `posts`.`id` AS `post_id`, `users`.`id` AS `user_id`, `users`.`name` AS `user_name`, `posts`.`title` AS `title`, `posts`.`body` AS `body`, `posts`.`created_at` AS `post_created_at`, `users`.`created_at` AS `user_created_at` FROM (`posts` join `users` on((`posts`.`user_id` = `users`.`id`))) ORDER BY `posts`.`created_at` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tenant_id` (`tenant_id`),
  ADD KEY `idx_property_id` (`property_id`),
  ADD KEY `idx_landlord_id` (`landlord_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `inspections`
--
ALTER TABLE `inspections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tenant_id` (`tenant_id`),
  ADD KEY `idx_property_id` (`property_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `lease_agreements`
--
ALTER TABLE `lease_agreements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tenant_id` (`tenant_id`),
  ADD KEY `idx_landlord_id` (`landlord_id`),
  ADD KEY `idx_property_id` (`property_id`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_property_id` (`property_id`),
  ADD KEY `idx_landlord_id` (`landlord_id`),
  ADD KEY `idx_issue_id` (`issue_id`),
  ADD KEY `idx_provider_id` (`provider_id`),
  ADD KEY `idx_requester_id` (`requester_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `market_properties`
--
ALTER TABLE `market_properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type_bedrooms` (`property_type`,`bedrooms`),
  ADD KEY `idx_rent` (`rent`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender_id` (`sender_id`),
  ADD KEY `idx_recipient_id` (`recipient_id`),
  ADD KEY `idx_property_id` (`property_id`),
  ADD KEY `idx_parent_message_id` (`parent_message_id`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_type` (`type`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tenant_id` (`tenant_id`),
  ADD KEY `idx_landlord_id` (`landlord_id`),
  ADD KEY `idx_property_id` (`property_id`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`policy_id`),
  ADD KEY `idx_policy_status` (`policy_status`),
  ADD KEY `idx_policy_category` (`policy_category`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `Posts`
--
ALTER TABLE `Posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_landlord_id` (`landlord_id`),
  ADD KEY `idx_manager_id` (`manager_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_property_type` (`property_type`),
  ADD KEY `idx_listing_type` (`listing_type`);

--
-- Indexes for table `property_manager`
--
ALTER TABLE `property_manager`
  ADD PRIMARY KEY (`manager_id`),
  ADD UNIQUE KEY `idx_user_id` (`user_id`),
  ADD UNIQUE KEY `idx_employee_id` (`employee_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reviewer_id` (`reviewer_id`),
  ADD KEY `idx_reviewee_id` (`reviewee_id`),
  ADD KEY `idx_property_id` (`property_id`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_review_type` (`review_type`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `service_providers`
--
ALTER TABLE `service_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_email` (`email`),
  ADD KEY `idx_specialty` (`specialty`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inspections`
--
ALTER TABLE `inspections`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lease_agreements`
--
ALTER TABLE `lease_agreements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `market_properties`
--
ALTER TABLE `market_properties`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `policy_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Posts`
--
ALTER TABLE `Posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `property_manager`
--
ALTER TABLE `property_manager`
  MODIFY `manager_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_providers`
--
ALTER TABLE `service_providers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `issues`
--
ALTER TABLE `issues`
  ADD CONSTRAINT `fk_issues_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_issues_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lease_agreements`
--
ALTER TABLE `lease_agreements`
  ADD CONSTRAINT `fk_leases_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_leases_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_leases_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_leases_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `fk_maintenance_issue` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_maintenance_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_maintenance_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_maintenance_provider` FOREIGN KEY (`provider_id`) REFERENCES `service_providers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_maintenance_requester` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_parent` FOREIGN KEY (`parent_message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_messages_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_messages_recipient` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_payments_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_payments_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_payments_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `policies`
--
ALTER TABLE `policies`
  ADD CONSTRAINT `fk_policies_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `Posts`
--
ALTER TABLE `Posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `fk_properties_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_properties_manager` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `property_manager`
--
ALTER TABLE `property_manager`
  ADD CONSTRAINT `fk_property_manager_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_reviewee` FOREIGN KEY (`reviewee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
