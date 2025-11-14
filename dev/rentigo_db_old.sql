-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 12, 2025 at 12:08 PM
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
-- Table structure for table `inspections`
--

CREATE TABLE `inspections` (
  `id` int NOT NULL,
  `property` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `issues` int DEFAULT '0',
  `type` enum('routine','move_in','move_out','maintenance','annual','emergency','issue') COLLATE utf8mb4_general_ci NOT NULL,
  `scheduled_date` date NOT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled','pending') COLLATE utf8mb4_general_ci DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inspections`
--

INSERT INTO `inspections` (`id`, `property`, `issues`, `type`, `scheduled_date`, `status`, `created_at`) VALUES
(5, '123 Palm Grove Road, Colombo 06, Sri Lanka', 4, 'issue', '2025-10-03', 'scheduled', '2025-10-29 08:15:36'),
(11, '123 Palm Grove Road, Colombo 06, Sri Lanka', 4, 'issue', '2025-10-30', 'scheduled', '2025-10-30 07:26:57');

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `id` int NOT NULL,
  `tenant_id` int NOT NULL,
  `property_id` int NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `priority` enum('low','medium','high','emergency') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'medium',
  `status` enum('pending','in_progress','resolved','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`id`, `tenant_id`, `property_id`, `title`, `description`, `category`, `priority`, `status`, `created_at`, `updated_at`) VALUES
(1, 13, 4, 'iwejwije', 'ewkjkwjekwkewlkewke', 'Heating/Cooling', 'medium', 'resolved', '2025-10-21 03:33:24', '2025-10-21 03:39:59'),
(2, 13, 4, 'plumbing issue', 'plumbing issue', 'Plumbing', 'low', 'pending', '2025-10-21 04:06:12', '2025-10-21 04:06:12'),
(3, 12, 6, 'plumbing', 'issue', 'Plumbing', 'low', 'pending', '2025-10-21 09:16:50', '2025-10-21 09:16:50'),
(4, 13, 14, 'water', 'hjkhwjhew', 'Other', 'high', 'pending', '2025-10-22 08:03:21', '2025-10-22 08:03:21'),
(5, 12, 14, '2322', 'sdsddsds', 'Heating/Cooling', 'medium', 'cancelled', '2025-10-22 09:13:52', '2025-10-22 09:13:52'),
(7, 13, 16, 'eewewewew', 'wewweewewewewewewe', 'plumbing', 'low', 'pending', '2025-10-29 14:16:11', '2025-10-29 14:16:11'),
(8, 13, 16, 'wewewewew', 'wewewewewewewewewewewewewewewe', 'plumbing', 'low', 'pending', '2025-10-29 15:15:38', '2025-10-29 15:15:38'),
(9, 13, 20, '1221221', 'qwqwqwqwqqwqwqwqwqw', 'electrical', 'medium', 'pending', '2025-10-30 01:25:27', '2025-10-30 01:31:41'),
(10, 13, 26, 'rerererereeeeeeeee', 'rererererere', 'appliance', 'medium', 'pending', '2025-10-30 07:29:21', '2025-10-30 07:31:41');

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
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `policy_id` int NOT NULL,
  `policy_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `policy_category` enum('rental','security','maintenance','financial','general') COLLATE utf8mb4_general_ci NOT NULL,
  `policy_description` text COLLATE utf8mb4_general_ci,
  `policy_content` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `policy_version` varchar(10) COLLATE utf8mb4_general_ci DEFAULT 'v1.0',
  `policy_status` enum('draft','active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'draft',
  `policy_type` enum('standard','custom') COLLATE utf8mb4_general_ci DEFAULT 'standard',
  `effective_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policies`
--

INSERT INTO `policies` (`policy_id`, `policy_name`, `policy_category`, `policy_description`, `policy_content`, `policy_version`, `policy_status`, `policy_type`, `effective_date`, `expiry_date`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'ewewewee333', 'rental', 'wewewewewewew', '1djfofowbeofjwoiejoieoiefniwefinofeniofewniefwnioefoinweewnoifenoifwnoieoinwfe', 'v1.0', 'inactive', 'standard', '2025-11-09', NULL, 8, '2025-11-09 13:58:11', '2025-11-10 01:48:32'),
(3, 'wewewewewewewe', 'security', 'wewewewewewewewewewe', 'eweweweweweqweqeqweqweweqweqwewewweweweowejjejwjwjwkqwljkejwklew', 'v1.0', 'active', 'standard', '2025-11-09', NULL, 8, '2025-11-09 15:17:03', '2025-11-10 01:48:45');

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
  `property_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_purpose` enum('rent','maintenance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rent',
  `bedrooms` int NOT NULL,
  `bathrooms` int NOT NULL,
  `sqft` int DEFAULT NULL,
  `rent` decimal(10,2) NOT NULL,
  `deposit` decimal(10,2) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `current_occupant` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('available','occupied','maintenance','vacant','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approved_at` datetime DEFAULT NULL,
  `listing_type` enum('rent','maintenance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rent',
  `available_date` date DEFAULT NULL,
  `parking` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `pet_policy` enum('no','cats','dogs','both') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `laundry` enum('none','shared','in_unit','hookups','included') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `tenant` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `landlord_id`, `manager_id`, `address`, `property_type`, `property_purpose`, `bedrooms`, `bathrooms`, `sqft`, `rent`, `deposit`, `description`, `current_occupant`, `status`, `approval_status`, `approved_at`, `listing_type`, `available_date`, `parking`, `pet_policy`, `laundry`, `tenant`, `issue`, `created_at`, `updated_at`) VALUES
(31, 14, 38, 'R Luxury Apartment in, Independence Avenue, Colombo 07', 'apartment', 'rent', 2, 1, NULL, 20000.00, 20000.00, '', NULL, 'vacant', 'approved', '2025-10-31 12:25:31', 'rent', NULL, '0', 'no', 'none', NULL, NULL, '2025-10-31 06:53:43', '2025-10-31 07:39:51'),
(33, 10, 38, 'RR Modern Apartment in, Independence Avenue, Colombo 05', 'apartment', 'rent', 1, 1, NULL, 20000.00, 20000.00, '', NULL, 'vacant', 'approved', '2025-10-31 15:54:18', 'rent', NULL, '0', 'no', 'none', NULL, NULL, '2025-10-31 10:23:31', '2025-10-31 10:24:32'),
(35, 14, 38, '45 Colombo Street, Colombo 05', 'apartment', 'rent', 2, 2, 1500, 29900.00, 29900.00, 'Beautiful 2-bedroom, 2-bathroom apartment located in the heart of Colombo 05. Features modern amenities, well-lit spaces, nearby shopping centers, restaurants, and public transportation. Perfect for young professionals or small families. Air conditioning, spacious kitchen, and secure building with 24-hour management.', NULL, 'vacant', 'approved', '2025-11-07 21:04:36', 'rent', NULL, '1', 'dogs', 'shared', NULL, NULL, '2025-11-01 11:05:25', '2025-11-07 15:35:03');

-- --------------------------------------------------------

--
-- Table structure for table `property_manager`
--

CREATE TABLE `property_manager` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `employee_id_document` longblob,
  `employee_id_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_id_filetype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_id_filesize` int DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_manager`
--

INSERT INTO `property_manager` (`id`, `user_id`, `employee_id_document`, `employee_id_filename`, `employee_id_filetype`, `employee_id_filesize`, `approval_status`, `approved_by`, `approved_at`, `rejection_reason`, `phone`, `created_at`, `updated_at`) VALUES
(2, 33, 0xffd8ffe000104a46494600010100000100010000ffdb0084000906071312111312121216161515181a1a17161816191915181a18121a16151a1515181d28201d1a271b171523312125292b2e302e171f3338332d37282d2e2b010a0a0a0e0d0e1b10101b2d2620252d2d352d2f2d2f2d2d2d2d2d2d2d2d352d2d2d352b2d2d2d2d2d2d2d2d2d2b2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2dffc0001108011b00b203012200021101031101ffc4001c0001000203010101000000000000000000000506030407020108ffc4004c1000020102030406030b0905080300000001020003110412210506314113225161718132729107142333425273a1b1b2c134436274a2b3b4d1f0355363829215162445b5c2c4e28394e1ffc400190101000301010000000000000000000000000102030405ffc4002611010100020202020103050000000000000001021112310321045141133233237181b1f0ffda000c03010002110311003f00c7b0b7f996c98a5ce3fbc51d71eb2f06f1163dc65f3038ea75903d275753cc1e1dc47107b8ce17363018ea945f3d27646ed078f730e047718799a7748954dcedec38a6346aa015154b665f4580201ea9e07ac39f6f096b85488880888808888088880888808888088880889a7b631dd050ab5b8e452403c09b754799b0f381b35eb2a297760aa352cc4003c49949dbbbfeab74c2ae63fde303947aabc4f89b0ee3297b5b6c56c4b66ace5adc14688beaaf0f3e3df34216912adbc78b249f7cd5d7b1ac3c80d079448a884bdd4a45788fe53c49722f356ae0fe6fb21499fdb7f72b13d1e3a81be8c4a1ff003a951fb5967609c22954349d5edaa3061e2ac08fb2776470402381171e075109afb111085128ef3562aac2ba3d574ac6b6185301b0a69d17752df286575543d27a4585adc2666da98c14f09f1f9abb90572e133951857ab7a5d6c996e07a641b032eb10d39cfa53711b72b2bbd3a9585245ac94debbad3bd207034ead9bf3619aa31198dd4701ca7c1bc0defba747df88699a74595c1c3a0aa5ead4562054376b85516a7dba5ae25a36be3450a15ab15cc2953672b7b66caa5ad7f2914dbca43542d40f454eb2506a81c160ce29e56e8c81d4bd6404824f7412eff000d6da1b6997688c39aeb4d32d1214b5052c5ea54571f0bd66d1545935d7bc48fc36f3631930e0d300b62290a95adf06d42a62452409d95492c08e429b1f94b2c78adacfd3b50a340d56a6a8f50e7540a2a16081737a4c7231b683be6bd4de84157134fa36f804a8ead7196a9a28ad5953b0a974173ccb7cd3099fd9014b6fe3461db1049e8c5362d51d292a2b0c42a20a594dd814e92f986961ac92dadbc6a6ada9e368d1a029175add4a8956a0721a986272f5405254758f482c45a64a3bdd9faab4559cb51002d656a7f0e1f2dea05d18643752398e326364ed1e9854050d37a4f91d090d660aae0ab0d082aea41d0eba8105f5dc406136be319e95370a9d2d118937519a9aad30b568e53cfa56a6431d42b30e2a0c8ac36f555384a554e2973bbd0153af85f831529bb3f0d1351f9cd465f19d0e2d08e53e9a7b2311d250a6fd20a9987a60a3026e41eb52ea1b1d3aba693722214253bdd371b970e9481d6abdcfaa9a9fda292e3395fba2637a4c6141c29285b7e91ebb7de03fcb0455e7a5527402f36296109f4b4eee7371100d0082e72347de6ddd1242214e74888851e5d011622f2edbbbbd68a8946b02b91428a9c410a2c337307bf51e1297109974ec54ea060194820f020dc1f0227a9ca365ed6ad8737a6f61cd4ea87c57f11632edb1f7b2955b2d4f827ef3d43e0dcbc0fd70bcab0c444258719854ab4de954174a8a5585c8bab0b1171a8d0f29a1feeee1fa535723162e2a106ad434cba8015cd1cd90b00ab6397e48ec92b10996c47e376351aae2a3860e005252a54a79941b857e8d8675049d1afc4f699ae775b076f88507af7717151ba4565a99ea839dae1df893c7b8498883954554ddcc3104642a0946ea54a89d6a6b9518146166cba661a9b0bdec26decfd9f4e8294a4b60496624b33331e2ceec4b33683524f01d936a20dd222210448ddb3b6e96180ce49622e107a4795fb00ef328bb63792b622eb7c94cfc853c7d66e27ea1dd08b74b6ed8deaa346ea9f0afd8a7aa3d66fc05fca73caef9ea3d43e93b1627bd8dcdbba789f614b4889f5109d00bc21f226c7bc9bb47f5e5108e51b756886e23cf9cd3ab852386a3fae5246219ccac43c492ab870ddc7b66955c395ef1db0d2652b1444425d23733119f08839a165f61b8fd96127253fdcf2be95a9f61561e60a9fb165c21a4e89f09b0b9d00e27948ada7b7a9d2634d01ab547e6d3e4fd23f041e3af60322a8d27c4d4538a60cb9c7c0afc48d47a40eb50fada7708698f8ee497a5b45f104ae0d03806c6bbdc505f56dad53dcba7e909e31a989c2904bfbe5585d94aad3a8a6faf444754afe8b6bfa53e6d1df0c2e0d9d092ee08f83a4b72b6502c5ae157c09bf7489a9bfb86c410acaf474b02f97271e6ca4dbcf4ef8754f063c562d9bb5295704d36b95f4908cb510f63a1d47d937253f19835660daabafa3510e575f061cbb8dc4dbc26dcab4b4c42f489fdf535eb0fa5a43ef27b0439f3f159d2cb13161b1095143d360ea78329041f313ce3b1229537a87e4296f60b810c9ce77b317d262aa1e49d41fe5d0fed669110cc49249b93a93da4f18021993eaa93a09b14b064fa5a77739b94e985e0214b948d5a583f9decfff0066da281a0169ea219db6911108222202222060ab8507b8ff005ca6955a0578f0ede52526feefec57c6563455c535550eee4666b16cb645e17d0ea4d8761869e3996578c686e9ed25a188bb6621d5942aa96663a300aaba9375fae5e6860ebe275acc70d47fbb43f0efdd52a8d107726bfa525b0bbb987c1a01413ac41cd51bad51bd66ecfd1161dd36a8826fe5ca1e9f8fe3cc7f72abb53054e8d4e8e8a2a2051d551617d6e4f69ef3a99a18b765a15190d985ec7b0e9a8ef92fbc1f1edea8fc656f6e63722ad3c97e90b0bdce8465000b0efbf948b75375bcc6dba8ac6cca2a410c2faf1e67ce4a52d9345b8afd6660da1827b05a472f02483af7dbfa131ecda38ae8aa0352ee05d781e7adae35b09c76db77b7a524935a5b28d10aaaab7ca06809bdac48b027969f5ccb4e686c3a75852b576ced72735c1d085b0d00efede724544ebc6ee3cef24d65589700558d4a0e68d43c4817a6ff00494f83788b377cd4de8db353dec6955a451999417537a2c01bf55b8a9242f55bb74264cd292bb36d72194302a415201520e841074225d867e299394d2c213c741f5cdca5482f01fce5ab78b7516983570c7a30356a44e6a761727a33c50f76abdc256aa2d891d848f61b4879de6f1e585f6f91110c08888088880888808888096af731fcaf11f429fbd695596af731fcaf11f429fbd30e8f8bfcb17bdabc07819a5445afc39774dcdaade8f9cd3a44ebe5c07876c3d8fcabfbc1f1edea8fc6469d9cb5c046d0e7eab5ae549b0b8925b7b5aede03f198766b0560cc6c035c93c0016249ee8d6e7b57765dc5371cd91ad7e035f2e323701b40f4de95400f15b2dcf6e87ac7d9c386b37b7ae915af5b4b758b00798624fb26b6c14ad52a285a487f0079936d271f1f6f4397a5cb07ad2537bdc5fda349914423a02f494eb448561d974561e4430fae7a0275e33534e0cf2e595acf4a49ecee27c3f1123a9c91d9c753e1f88968abc6da07a1a9eab7dc328389f4dfd63f78cbfedbb74352ff0035bee1940c4fa6feb1fb4c570fccea31c44487011110111101125768ec1ab4ae40cebdaa351e2bc7d9791309b2cedf6222104b47b9ad40b8ac4126c3a05faaa9957933b9cd6af88fa05fde18747c5fe58e8d8ec4a38055af6bf0bf64d7a6f7b8d4f0eee7353660ba378ffdb297eeadb5aa51a3469d372bd2312d62412aa00b13d9761ec96e3ef4f5b97adb6b7c77870f87aac59c3358008843313dfc879ce77b6f7c2ad6a6ea3a8ac3d01d9cf31e2c797671d2572b54275330b34d263233b76eadbf1b5af4865a0181361598dbacdca9a8372343c74361a1e337b7536a2d3c396ab873875519b37157b0bdc93d604f20dddaf2950dff00c7354ae9495acb4821ff00311989f1cb61e67bed29bddb5c9d9d4b5eb622d7f0b02de56b8f394e33b5f95eb6a7aef055e9aa620394a8ee5fbacdf2483a100586bd825bf77b7e92a5971002372750721f59788fafca735733e03acbd92a8fd038775601958329e041047b4492d98a4b1005f4fc44e15bb7b62ad17cc8e4768f92ddcc0e93baec2a998861cd330f300894b34b6376f3b6fe26a7aadf70ca0627d37f58fde32fbb60fc0d43fa2df70ca1627d37f58fde32b5c5f33a8c711121c0444dbc06cda958f5174e6c7451e7f8084c9b6a44b28dd2edadfb1ffb4fb0b7e9e4b348cda5b0e956b9b647f9cbcfd61c0fdbdf24e21d164bda87b476355a3724665f9cba8f31c448f9d3243ed1ddea552e57e0dbb40ea9f15fe56865978be94b92bba6f6c455d2f7a4bfbc335b686cbab47d35d3e70d54f9f2f39bbb987fe26ae9f9a5fde185fe34feac5d302dd5e16eb77fcd9cb7dd7f139ab535be8b4c1f6b9278f7013ac61ce87c7f09c4fdd6b139b1b50762a8fd9bfe265f0edea67d2a0f35eafa2de1361cdd437681edb6bf5cd72668a45876bd7152bd46ed23ea503f01fd7a58f7831d9861e9f2a5457fd4e331fd9c922f0d5091737bdcff005f6fb7bee35eabdf8ebc3ea161f548349cddcdd6c4630934d72a0bfc235c2923e4af3637d34d073908e6c09ec9676dfdc6650b4cd3a4aa02a84a7c001600672c255b69552da9e2cd73de49249f6c7b4fa6ee00dad3bc6e262ba5a349f9f4641f15eafe13f3fe16a5ad3b57b95d6f80604e819c0f3507faf39197463fb939b65be01fd56fb8651f11e9b7ac7ed979da5418e1ea369a231e22f6c8794a58c33d4a8ca8a58e63a0e5af33c079ca64e3f99d46bccf83c154aa6d4d49ed3c8789e0258b676ec0166ac6e7e62f0f36e27cad2c34a98501540007000587b255cb8f8aded05b3b76516c6a9ce7e68f407e27fad24f2a800002c0700380f013ec43698c9d11110922220222207c22fa1e121abec32953a6c2b8a552d62a466a4e2f7b30e2baf353e464d44265d5dc46e076d80c296213a0aac740dad37d3f3554684fe89b3774e2bee8988cfb43127b1f2ffa4053f5833bc6270e9514a5450cadc558020f88339def67b9a0a99aa611acc7534d8defeab9ff00bbdb2d8dd3a31f3efd64e5b82a99a965e6a4fb0ebfce6373360ecead87aaf4eb2143cee08e1316216693a6db9bf4f949ecade3f6ff005fd6b3106bebec98831b11da7ecd266490964a6d35f1efd602675986a61ddd8e452d95731039004024fb47b632e89db3612a6a2fc3b7f9ceb5ee63b529515aa6bb855eadb996243001146acc6c3400994cdd5dc2c46280761d1d23f2d871f547caf2d3bc4ebbbbfbb387c181d1addc0b748dabf7dbe68f0f3bcadcbd698e5e592fa667ab5b13a2d3f7b513c7300d88a8398b7a34948f16d7e4c90a5455459542826f602da99ee251cf96772bba44442a444404444044440444404444044440d1dafb1e8e253256a618723c197d56e23c384e5fbd1ee715695df0f7aa9d96f8403bd47a5e5ec13af449974b6395c7a7e63a987b1ca410471bf384a439cefbbc7ba386c6025d7254e55146b7fd21f2bedef95dd89ee6688e5b12f9c03d545b804722cdc4780f6cbcca37fd69af6e7bb1776abe2db2d142473622cabe2dc07dbd979d4f767dcff0f86b3d502b55e648ea0e7a29e3e7d9c04b661b0e94d425350aa382a8b01e426495b96d965e4b911112acc88880888808888088880888808888088953de0deb7a18c185518650688abd2622b9a4bad429941ca6eda5ede3d90998dbd2d912b5477aff00e268615e9166ab483f49473d4a5d660172304eb53b3026a5c01ce6b6c5df8a4e4a57395fdf0f4415a553a204542b4d5aa1ba8722c78f3e50b70c96e890f5779282d65a2fd22333f46acd46a2d26a9c916a95ca49b1b6b63359b7d3062a3532ee0a55345dba2a9d1a540d96cd532e5173df08e37e96189a9b50e2028f7b2d22f9b51559d572d8dec5149bdf2f2ed9a3b9fb69b1b83a58964086a67ba8248196a32713eac235eb699895dc66f4ad2da54f0351405ab4d596a5ff0038cecaa8470d721b77d84d1c46f886ab8ea34b2a2e16897e9d94b82ead95ec83d255371a73530b70ab844a56d6decaf45316545263430d87aaac518066aad67ccb9f45e60711da64de377a30d49d91d9ee817a565a4ed4e966175e96a282a97ef308e153512269ef1506c53611598d65b660118a8069f48097032816b713c4da4b422cd1111082222022220222202222025636d6ecd4ab8c18ba556929e8051c956874ab6e94d4cc3ae2c7503dbdb2cf10996ce95e3b0ab74f86c40af4c54a54cd2a80513d1ba3386b5350fd4200b71330ff00ba87dea70fd28d713ef8cd93fc7e972dafe57963c45608b99b85c0d05c92cc15401da4903ce6856db48ac14a54cd7008097cac6a53450c41b5cf4a874b8b6bd972d32c9018adcba8f5fa46c4ab28c4ae2066a6cd56c1f37459cd4ca100d05947e133d7dcf2d85c661fa600e2714d880d93d1bd547ca466d7d0b5f4e327e9ed4a44039ada29b71f482955badc16eba754127ac3b44f55b68535547274760a2c09d4920dc72b59af7e194df84279e4c5b630f8870a70d5d68b024b66a42a2b02a40045c11626f707948fddeddf7c1d3c351a788bd1a48e1d0d317a8eee5c306bdd4024e9ae9dbc648d4dad483e42daf5b5009175a8b4ca8b0d5b3b81617d411c60ed7a36073823b790051981d788391c5c5f507b0d8afbd6919b5b7597115ead5a8e72d4c3ad101459d192b1aab515efa106d6d394d4adb94a0554a550223e0c6180cb720f48ce6a31b8b9258923b4932c0bb4e9136cdd9c8f12cea415f481069bdee0016f1b79a5b5a9370637b0394a306b1728bd5b5f52ad61cf29b70309e59207696e71aa9895e980e9f0f428df25f2f42d72de96b7ece53c63f72cbd7ab552a53cb5f29a895689a962aa158d321d746005c3032c29b5e89b90fa0b6b95ac6e99c6536eb0cba9b70b1bda6cd0c42bdca1b80482470b8e22fcfca0e79446ec9d89d0627175c302311d0d902db20a34ba3b66bea0f95a4bc442b6ec88884111101111011110111101111031d7a2aea55869a1e2410410ca411a8208041ee9806cda77bd89370c49624921d1c127c69a792db84db9ad5f1f4d1b2b137b5ec159ac3b4e506c3be1336c1fec6a597258e4b2f54b315ba65cad949b5c7469ecef37f7536552645a6c97550c00b9b75c598f8f1d795cf6ccaf8ea61b29719b2ab5bb559f22916e23369e63b67b4c5230243a900e5243020312005363c6e469df06eb5d766530c1fad704917662066a8b51ac2f6d5d41fab8690fb2e91b5c1ea8006a790703f78fedee9eaaed2a4b9b33119735ce5623aaa5982b016660013945ce874d0cf431c9982ddae6c35471627806256ca4f20d63a8ed109f6c5576552624906e4827ac7886761f5d47f68ec13d0d9b4c70cc0e96218dd72b3b2e53cbe31c781b199abe295080d719ad639588d48517602c2e4802f310da54ec0e622eeb4c02ac1b3bd8a82a45c68ca751a03ac23db1aec8a60586716e61dafe8943d6bdf55363e00f1179b586c3ad35ca82cb726dd9998b1b775c99f30b8a5a8095be96bdd594ea0106cc068411accd05d9111082222022220222202222022220227c26d0a6fa8d440fb34ebecf0cfd207746b5aeb9787f994cdc884a3eb6c8a6d97d25cbd105ca46828bb32817074218a9bf2e163acf783d9a29fa351f4544d727a14ef913d1e02edaf1eb1d7416dd883751d88d908e19599f2317393ab941a8ac1c8eadcdf3b9b1245d8e9c2d99702158946645241645ca1090a147c9b81955458103aa3befb64cf97d6dcfb39e9c7ed1ed8375a7576621c96ea043982a0502f7bdc756ea7883948b8241b89806c2a7a5cb960e1f306c9ae7473d5a7956c5a9a93a6a6e789bc936602c0902e6c3bcd89b0ed3604f918636e3a41bad7c0e13a30c33b39639999b2e6248b6b9540e000ee0001a09b313e6617b5c5cdc81cec2d736ecd47b4421f6222022220222202222022220222206bed0a06a52a94c5aee8ca2fc355235ee91cdb26a1a81c54c80be62886cabf17c3abd627a337d17e30f78699884cba57f05b2ab7474f3358e440c85ea6ac29b0676606f9eeeba0b8f831af02b97fd88d6525eed95d4b667b8ce13543c47c5dac2de913c46b3720b686d3a898ea14148c8f4cb30b0bdc626953e3eabb42d2db596bec77f9150aead7199b506b23a2eb70005575e07d2e045c4c6fb11cd365350b16d0966620afbd853ca7ff9006bdbeb95ec06f4e25f0f87a8597354acc8dd41c063f0f445872ea547f6c623797123673e2038e9055450722dacd5ca1d2d6e10b71c96ada5b35aaa2a0654cac5f817b36bd1917b7a2c43f732adb84d7a9b19daa876716bb5ecd533306c4d2ac178d8285a652dcc11c05c481da1bc5884f7f6571f02ae69f51742af8d0396ba50a5c7e6f799e976fe23a2da0dd26b469d56a672a754ae170eebcb5b35473adf8f70838e49dabb15cae55aac2e2c7aef7272564bdef707e129ebc7e0c76099b19b2cb53c80a9b3b32e6b90a19596dadc1b073a3022da5868456b666f0621ce18354be7c3e76ea26add0611afa2e9ad6a9a0d3addc2d9375b6f622b56a2952a6657a74cb0ca82e5862efa81fe0d3ff4f79b8b8e5da7696c97ce59eab32e72d6b90082c580b002c1410a05cdc0e5c27c5d93545af5735ae3296601941a4298622e7d1a6493add9db4b12255b767797155a8ecf6a95731ad5b2d43910665be274d174f8aa7c2de8f799d0a1196f1bed08db1eadd8f4cd6216c33b5b454197ac18dae8c6e4b7a66e0eb9a5b0b4caa22b1b955009d7520589d493ed332c4296ec88884111101111031d0aeaeb991830ed07fad6649ce30b8a7a6d9918a9eee7e2381f3965d9dbcea6cb58653f387a3e6388fafca19e3e497b58a279a750300ca41078106e0f9cf50d088880956daffdab85fa13fc650969956daffdab85fa13fc65085f0ed50d93f91e13f587ff00aae127bc67f63d4fa7a5fc519e364fe4784fd61ffeab8499319fd8f53e9e97f1461bfe7fcb36d7ff009a7a953f79b4a644f88dabf435ff0082c2cc7b5ffe69ea54fde6d29913e236afd0d7fe0b0b08ff00bfd3c6c6f4b07faa7fe360265dc7fca30df454beee3e62d8de960ff54ffc6c04cbb8ff009461be8a97ddc7c197551db99f93ec9fd63f1c6ceb33936e67e4fb27f58fc71b3acc33f37ee22221911110113cd470a0962001c493603c4cafed1de75175a2331f9c7d1f21c4fd5e708b949dac5128476d623fbd6fabf94433fd58d0888860d8c163aa5237a6c4768e2a7c44b3ecede547b0aa32376fc83e7cbcfdb2a110b639d8e960dc5c6a0f39f673fd9fb4ead1f41b4e6a7553e5cbca5a3676f0d2a960ff0006dde7aa7c1bf9da1be3e4953123715b215f134f1058834d0a05b0b106aa54b93e34c0f392510d25d2b386dce44a54a90aac45372e0d85c96c4d2c458f9d203c099eaaee8536c2b614d57ca5d5f359735d6a6703b2d7964884f3aaf62774e9bfbe2f51c7be0306d06999b104e5ff00ecb7fa44f6bbad4f262133bdb10aeadc2e03d2a74895d3b292f1ed327a20e550185dd4a486910eff00074fa317cba8e8e8a5ce9c6d417da67ad91baf4f0ef4dd5dc9455517cba851580bd87f8edfe9127620e555bd97b9d4a8261916a54230ef9d6f96e4deae8d61c3e19b876096488845b6f644487da3bc34a9dc2fc237603d51e2dfcaf0adb2769726da9e120f68ef2d34b8a5d76edf903cf9f97b6573686d4ab5bd36d3e68d17d9cfce69c32cbcbf4d9c6e3ea5637a8c4f60e0a3c04d68886444442088880888808888121b3b6cd5a3a03997e6b6a3cb98f2969d9bb72955b0be47f9adcfd53c0fdbdd28d3e42f8e763a6c4acee9632a31746625540b03adb5ede36ee96687463773644442488880919b4b6e52a3717ceff35797ac780fb7ba46ef6e32a29445621581b81a5f5ede32af0cb3f26bd448ed1db356b684e55f9aba0f33c4c8f888636efb2222104444044440ffd9, 'em_idcard.jpeg', 'image/jpeg', 7267, 'approved', NULL, '2025-10-19 03:30:51', NULL, NULL, '2025-10-19 03:29:19', '2025-10-19 03:30:51'),
(3, 34, 0xffd8ffe000104a46494600010100000100010000ffdb0084000906071312111312121216161515181a1a17161816191915181a18121a16151a1515181d28201d1a271b171523312125292b2e302e171f3338332d37282d2e2b010a0a0a0e0d0e1b10101b2d2620252d2d352d2f2d2f2d2d2d2d2d2d2d2d352d2d2d352b2d2d2d2d2d2d2d2d2d2b2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2dffc0001108011b00b203012200021101031101ffc4001c0001000203010101000000000000000000000506030407020108ffc4004c1000020102030406030b0905080300000001020003110412210506314113225161718132729107142333425273a1b1b2c134436274a2b3b4d1f0355363829215162445b5c2c4e28394e1ffc400190101000301010000000000000000000000000102030405ffc4002611010100020202020103050000000000000001021112310321045141133233237181b1f0ffda000c03010002110311003f00c7b0b7f996c98a5ce3fbc51d71eb2f06f1163dc65f3038ea75903d275753cc1e1dc47107b8ce17363018ea945f3d27646ed078f730e047718799a7748954dcedec38a6346aa015154b665f4580201ea9e07ac39f6f096b85488880888808888088880888808888088880889a7b631dd050ab5b8e452403c09b754799b0f381b35eb2a297760aa352cc4003c49949dbbbfeab74c2ae63fde303947aabc4f89b0ee3297b5b6c56c4b66ace5adc14688beaaf0f3e3df34216912adbc78b249f7cd5d7b1ac3c80d079448a884bdd4a45788fe53c49722f356ae0fe6fb21499fdb7f72b13d1e3a81be8c4a1ff003a951fb5967609c22954349d5edaa3061e2ac08fb2776470402381171e075109afb111085128ef3562aac2ba3d574ac6b6185301b0a69d17752df286575543d27a4585adc2666da98c14f09f1f9abb90572e133951857ab7a5d6c996e07a641b032eb10d39cfa53711b72b2bbd3a9585245ac94debbad3bd207034ead9bf3619aa31198dd4701ca7c1bc0defba747df88699a74595c1c3a0aa5ead4562054376b85516a7dba5ae25a36be3450a15ab15cc2953672b7b66caa5ad7f2914dbca43542d40f454eb2506a81c160ce29e56e8c81d4bd6404824f7412eff000d6da1b6997688c39aeb4d32d1214b5052c5ea54571f0bd66d1545935d7bc48fc36f3631930e0d300b62290a95adf06d42a62452409d95492c08e429b1f94b2c78adacfd3b50a340d56a6a8f50e7540a2a16081737a4c7231b683be6bd4de84157134fa36f804a8ead7196a9a28ad5953b0a974173ccb7cd3099fd9014b6fe3461db1049e8c5362d51d292a2b0c42a20a594dd814e92f986961ac92dadbc6a6ada9e368d1a029175add4a8956a0721a986272f5405254758f482c45a64a3bdd9faab4559cb51002d656a7f0e1f2dea05d18643752398e326364ed1e9854050d37a4f91d090d660aae0ab0d082aea41d0eba8105f5dc406136be319e95370a9d2d118937519a9aad30b568e53cfa56a6431d42b30e2a0c8ac36f555384a554e2973bbd0153af85f831529bb3f0d1351f9cd465f19d0e2d08e53e9a7b2311d250a6fd20a9987a60a3026e41eb52ea1b1d3aba693722214253bdd371b970e9481d6abdcfaa9a9fda292e3395fba2637a4c6141c29285b7e91ebb7de03fcb0455e7a5527402f36296109f4b4eee7371100d0082e72347de6ddd1242214e74888851e5d011622f2edbbbbd68a8946b02b91428a9c410a2c337307bf51e1297109974ec54ea060194820f020dc1f0227a9ca365ed6ad8737a6f61cd4ea87c57f11632edb1f7b2955b2d4f827ef3d43e0dcbc0fd70bcab0c444258719854ab4de954174a8a5585c8bab0b1171a8d0f29a1feeee1fa535723162e2a106ad434cba8015cd1cd90b00ab6397e48ec92b10996c47e376351aae2a3860e005252a54a79941b857e8d8675049d1afc4f699ae775b076f88507af7717151ba4565a99ea839dae1df893c7b8498883954554ddcc3104642a0946ea54a89d6a6b9518146166cba661a9b0bdec26decfd9f4e8294a4b60496624b33331e2ceec4b33683524f01d936a20dd222210448ddb3b6e96180ce49622e107a4795fb00ef328bb63792b622eb7c94cfc853c7d66e27ea1dd08b74b6ed8deaa346ea9f0afd8a7aa3d66fc05fca73caef9ea3d43e93b1627bd8dcdbba789f614b4889f5109d00bc21f226c7bc9bb47f5e5108e51b756886e23cf9cd3ab852386a3fae5246219ccac43c492ab870ddc7b66955c395ef1db0d2652b1444425d23733119f08839a165f61b8fd96127253fdcf2be95a9f61561e60a9fb165c21a4e89f09b0b9d00e27948ada7b7a9d2634d01ab547e6d3e4fd23f041e3af60322a8d27c4d4538a60cb9c7c0afc48d47a40eb50fada7708698f8ee497a5b45f104ae0d03806c6bbdc505f56dad53dcba7e909e31a989c2904bfbe5585d94aad3a8a6faf444754afe8b6bfa53e6d1df0c2e0d9d092ee08f83a4b72b6502c5ae157c09bf7489a9bfb86c410acaf474b02f97271e6ca4dbcf4ef8754f063c562d9bb5295704d36b95f4908cb510f63a1d47d937253f19835660daabafa3510e575f061cbb8dc4dbc26dcab4b4c42f489fdf535eb0fa5a43ef27b0439f3f159d2cb13161b1095143d360ea78329041f313ce3b1229537a87e4296f60b810c9ce77b317d262aa1e49d41fe5d0fed669110cc49249b93a93da4f18021993eaa93a09b14b064fa5a77739b94e985e0214b948d5a583f9decfff0066da281a0169ea219db6911108222202222060ab8507b8ff005ca6955a0578f0ede52526feefec57c6563455c535550eee4666b16cb645e17d0ea4d8761869e3996578c686e9ed25a188bb6621d5942aa96663a300aaba9375fae5e6860ebe275acc70d47fbb43f0efdd52a8d107726bfa525b0bbb987c1a01413ac41cd51bad51bd66ecfd1161dd36a8826fe5ca1e9f8fe3cc7f72abb53054e8d4e8e8a2a2051d551617d6e4f69ef3a99a18b765a15190d985ec7b0e9a8ef92fbc1f1edea8fc656f6e63722ad3c97e90b0bdce8465000b0efbf948b75375bcc6dba8ac6cca2a410c2faf1e67ce4a52d9345b8afd6660da1827b05a472f02483af7dbfa131ecda38ae8aa0352ee05d781e7adae35b09c76db77b7a524935a5b28d10aaaab7ca06809bdac48b027969f5ccb4e686c3a75852b576ced72735c1d085b0d00efede724544ebc6ee3cef24d65589700558d4a0e68d43c4817a6ff00494f83788b377cd4de8db353dec6955a451999417537a2c01bf55b8a9242f55bb74264cd292bb36d72194302a415201520e841074225d867e299394d2c213c741f5cdca5482f01fce5ab78b7516983570c7a30356a44e6a761727a33c50f76abdc256aa2d891d848f61b4879de6f1e585f6f91110c08888088880888808888096af731fcaf11f429fbd695596af731fcaf11f429fbd30e8f8bfcb17bdabc07819a5445afc39774dcdaade8f9cd3a44ebe5c07876c3d8fcabfbc1f1edea8fc6469d9cb5c046d0e7eab5ae549b0b8925b7b5aede03f198766b0560cc6c035c93c0016249ee8d6e7b57765dc5371cd91ad7e035f2e323701b40f4de95400f15b2dcf6e87ac7d9c386b37b7ae915af5b4b758b00798624fb26b6c14ad52a285a487f0079936d271f1f6f4397a5cb07ad2537bdc5fda349914423a02f494eb448561d974561e4430fae7a0275e33534e0cf2e595acf4a49ecee27c3f1123a9c91d9c753e1f88968abc6da07a1a9eab7dc328389f4dfd63f78cbfedbb74352ff0035bee1940c4fa6feb1fb4c570fccea31c44487011110111101125768ec1ab4ae40cebdaa351e2bc7d9791309b2cedf6222104b47b9ad40b8ac4126c3a05faaa9957933b9cd6af88fa05fde18747c5fe58e8d8ec4a38055af6bf0bf64d7a6f7b8d4f0eee7353660ba378ffdb297eeadb5aa51a3469d372bd2312d62412aa00b13d9761ec96e3ef4f5b97adb6b7c77870f87aac59c3358008843313dfc879ce77b6f7c2ad6a6ea3a8ac3d01d9cf31e2c797671d2572b54275330b34d263233b76eadbf1b5af4865a0181361598dbacdca9a8372343c74361a1e337b7536a2d3c396ab873875519b37157b0bdc93d604f20dddaf2950dff00c7354ae9495acb4821ff00311989f1cb61e67bed29bddb5c9d9d4b5eb622d7f0b02de56b8f394e33b5f95eb6a7aef055e9aa620394a8ee5fbacdf2483a100586bd825bf77b7e92a5971002372750721f59788fafca735733e03acbd92a8fd038775601958329e041047b4492d98a4b1005f4fc44e15bb7b62ad17cc8e4768f92ddcc0e93baec2a998861cd330f300894b34b6376f3b6fe26a7aadf70ca0627d37f58fde32fbb60fc0d43fa2df70ca1627d37f58fde32b5c5f33a8c711121c0444dbc06cda958f5174e6c7451e7f8084c9b6a44b28dd2edadfb1ffb4fb0b7e9e4b348cda5b0e956b9b647f9cbcfd61c0fdbdf24e21d164bda87b476355a3724665f9cba8f31c448f9d3243ed1ddea552e57e0dbb40ea9f15fe56865978be94b92bba6f6c455d2f7a4bfbc335b686cbab47d35d3e70d54f9f2f39bbb987fe26ae9f9a5fde185fe34feac5d302dd5e16eb77fcd9cb7dd7f139ab535be8b4c1f6b9278f7013ac61ce87c7f09c4fdd6b139b1b50762a8fd9bfe265f0edea67d2a0f35eafa2de1361cdd437681edb6bf5cd72668a45876bd7152bd46ed23ea503f01fd7a58f7831d9861e9f2a5457fd4e331fd9c922f0d5091737bdcff005f6fb7bee35eabdf8ebc3ea161f548349cddcdd6c4630934d72a0bfc235c2923e4af3637d34d073908e6c09ec9676dfdc6650b4cd3a4aa02a84a7c001600672c255b69552da9e2cd73de49249f6c7b4fa6ee00dad3bc6e262ba5a349f9f4641f15eafe13f3fe16a5ad3b57b95d6f80604e819c0f3507faf39197463fb939b65be01fd56fb8651f11e9b7ac7ed979da5418e1ea369a231e22f6c8794a58c33d4a8ca8a58e63a0e5af33c079ca64e3f99d46bccf83c154aa6d4d49ed3c8789e0258b676ec0166ac6e7e62f0f36e27cad2c34a98501540007000587b255cb8f8aded05b3b76516c6a9ce7e68f407e27fad24f2a800002c0700380f013ec43698c9d11110922220222207c22fa1e121abec32953a6c2b8a552d62a466a4e2f7b30e2baf353e464d44265d5dc46e076d80c296213a0aac740dad37d3f3554684fe89b3774e2bee8988cfb43127b1f2ffa4053f5833bc6270e9514a5450cadc558020f88339def67b9a0a99aa611acc7534d8defeab9ff00bbdb2d8dd3a31f3efd64e5b82a99a965e6a4fb0ebfce6373360ecead87aaf4eb2143cee08e1316216693a6db9bf4f949ecade3f6ff005fd6b3106bebec98831b11da7ecd266490964a6d35f1efd602675986a61ddd8e452d95731039004024fb47b632e89db3612a6a2fc3b7f9ceb5ee63b529515aa6bb855eadb996243001146acc6c3400994cdd5dc2c46280761d1d23f2d871f547caf2d3bc4ebbbbfbb387c181d1addc0b748dabf7dbe68f0f3bcadcbd698e5e592fa667ab5b13a2d3f7b513c7300d88a8398b7a34948f16d7e4c90a5455459542826f602da99ee251cf96772bba44442a444404444044440444404444044440d1dafb1e8e253256a618723c197d56e23c384e5fbd1ee715695df0f7aa9d96f8403bd47a5e5ec13af449974b6395c7a7e63a987b1ca410471bf384a439cefbbc7ba386c6025d7254e55146b7fd21f2bedef95dd89ee6688e5b12f9c03d545b804722cdc4780f6cbcca37fd69af6e7bb1776abe2db2d142473622cabe2dc07dbd979d4f767dcff0f86b3d502b55e648ea0e7a29e3e7d9c04b661b0e94d425350aa382a8b01e426495b96d965e4b911112acc88880888808888088880888808888088953de0deb7a18c185518650688abd2622b9a4bad429941ca6eda5ede3d90998dbd2d912b5477aff00e268615e9166ab483f49473d4a5d660172304eb53b3026a5c01ce6b6c5df8a4e4a57395fdf0f4415a553a204542b4d5aa1ba8722c78f3e50b70c96e890f5779282d65a2fd22333f46acd46a2d26a9c916a95ca49b1b6b63359b7d3062a3532ee0a55345dba2a9d1a540d96cd532e5173df08e37e96189a9b50e2028f7b2d22f9b51559d572d8dec5149bdf2f2ed9a3b9fb69b1b83a58964086a67ba8248196a32713eac235eb699895dc66f4ad2da54f0351405ab4d596a5ff0038cecaa8470d721b77d84d1c46f886ab8ea34b2a2e16897e9d94b82ead95ec83d255371a73530b70ab844a56d6decaf45316545263430d87aaac518066aad67ccb9f45e60711da64de377a30d49d91d9ee817a565a4ed4e966175e96a282a97ef308e153512269ef1506c53611598d65b660118a8069f48097032816b713c4da4b422cd1111082222022220222202222025636d6ecd4ab8c18ba556929e8051c956874ab6e94d4cc3ae2c7503dbdb2cf10996ce95e3b0ab74f86c40af4c54a54cd2a80513d1ba3386b5350fd4200b71330ff00ba87dea70fd28d713ef8cd93fc7e972dafe57963c45608b99b85c0d05c92cc15401da4903ce6856db48ac14a54cd7008097cac6a53450c41b5cf4a874b8b6bd972d32c9018adcba8f5fa46c4ab28c4ae2066a6cd56c1f37459cd4ca100d05947e133d7dcf2d85c661fa600e2714d880d93d1bd547ca466d7d0b5f4e327e9ed4a44039ada29b71f482955badc16eba754127ac3b44f55b68535547274760a2c09d4920dc72b59af7e194df84279e4c5b630f8870a70d5d68b024b66a42a2b02a40045c11626f707948fddeddf7c1d3c351a788bd1a48e1d0d317a8eee5c306bdd4024e9ae9dbc648d4dad483e42daf5b5009175a8b4ca8b0d5b3b81617d411c60ed7a36073823b790051981d788391c5c5f507b0d8afbd6919b5b7597115ead5a8e72d4c3ad101459d192b1aab515efa106d6d394d4adb94a0554a550223e0c6180cb720f48ce6a31b8b9258923b4932c0bb4e9136cdd9c8f12cea415f481069bdee0016f1b79a5b5a9370637b0394a306b1728bd5b5f52ad61cf29b70309e59207696e71aa9895e980e9f0f428df25f2f42d72de96b7ece53c63f72cbd7ab552a53cb5f29a895689a962aa158d321d746005c3032c29b5e89b90fa0b6b95ac6e99c6536eb0cba9b70b1bda6cd0c42bdca1b80482470b8e22fcfca0e79446ec9d89d0627175c302311d0d902db20a34ba3b66bea0f95a4bc442b6ec88884111101111011110111101111031d7a2aea55869a1e2410410ca411a8208041ee9806cda77bd89370c49624921d1c127c69a792db84db9ad5f1f4d1b2b137b5ec159ac3b4e506c3be1336c1fec6a597258e4b2f54b315ba65cad949b5c7469ecef37f7536552645a6c97550c00b9b75c598f8f1d795cf6ccaf8ea61b29719b2ab5bb559f22916e23369e63b67b4c5230243a900e5243020312005363c6e469df06eb5d766530c1fad704917662066a8b51ac2f6d5d41fab8690fb2e91b5c1ea8006a790703f78fedee9eaaed2a4b9b33119735ce5623aaa5982b016660013945ce874d0cf431c9982ddae6c35471627806256ca4f20d63a8ed109f6c5576552624906e4827ac7886761f5d47f68ec13d0d9b4c70cc0e96218dd72b3b2e53cbe31c781b199abe295080d719ad639588d48517602c2e4802f310da54ec0e622eeb4c02ac1b3bd8a82a45c68ca751a03ac23db1aec8a60586716e61dafe8943d6bdf55363e00f1179b586c3ad35ca82cb726dd9998b1b775c99f30b8a5a8095be96bdd594ea0106cc068411accd05d9111082222022220222202222022220227c26d0a6fa8d440fb34ebecf0cfd207746b5aeb9787f994cdc884a3eb6c8a6d97d25cbd105ca46828bb32817074218a9bf2e163acf783d9a29fa351f4544d727a14ef913d1e02edaf1eb1d7416dd883751d88d908e19599f2317393ab941a8ac1c8eadcdf3b9b1245d8e9c2d99702158946645241645ca1090a147c9b81955458103aa3befb64cf97d6dcfb39e9c7ed1ed8375a7576621c96ea043982a0502f7bdc756ea7883948b8241b89806c2a7a5cb960e1f306c9ae7473d5a7956c5a9a93a6a6e789bc936602c0902e6c3bcd89b0ed3604f918636e3a41bad7c0e13a30c33b39639999b2e6248b6b9540e000ee0001a09b313e6617b5c5cdc81cec2d736ecd47b4421f6222022220222202222022220222206bed0a06a52a94c5aee8ca2fc355235ee91cdb26a1a81c54c80be62886cabf17c3abd627a337d17e30f78699884cba57f05b2ab7474f3358e440c85ea6ac29b0676606f9eeeba0b8f831af02b97fd88d6525eed95d4b667b8ce13543c47c5dac2de913c46b3720b686d3a898ea14148c8f4cb30b0bdc626953e3eabb42d2db596bec77f9150aead7199b506b23a2eb70005575e07d2e045c4c6fb11cd365350b16d0966620afbd853ca7ff9006bdbeb95ec06f4e25f0f87a8597354acc8dd41c063f0f445872ea547f6c623797123673e2038e9055450722dacd5ca1d2d6e10b71c96ada5b35aaa2a0654cac5f817b36bd1917b7a2c43f732adb84d7a9b19daa876716bb5ecd533306c4d2ac178d8285a652dcc11c05c481da1bc5884f7f6571f02ae69f51742af8d0396ba50a5c7e6f799e976fe23a2da0dd26b469d56a672a754ae170eebcb5b35473adf8f70838e49dabb15cae55aac2e2c7aef7272564bdef707e129ebc7e0c76099b19b2cb53c80a9b3b32e6b90a19596dadc1b073a3022da5868456b666f0621ce18354be7c3e76ea26add0611afa2e9ad6a9a0d3addc2d9375b6f622b56a2952a6657a74cb0ca82e5862efa81fe0d3ff4f79b8b8e5da7696c97ce59eab32e72d6b90082c580b002c1410a05cdc0e5c27c5d93545af5735ae3296601941a4298622e7d1a6493add9db4b12255b767797155a8ecf6a95731ad5b2d43910665be274d174f8aa7c2de8f799d0a1196f1bed08db1eadd8f4cd6216c33b5b454197ac18dae8c6e4b7a66e0eb9a5b0b4caa22b1b955009d7520589d493ed332c4296ec88884111101111031d0aeaeb991830ed07fad6649ce30b8a7a6d9918a9eee7e2381f3965d9dbcea6cb58653f387a3e6388fafca19e3e497b58a279a750300ca41078106e0f9cf50d088880956daffdab85fa13fc650969956daffdab85fa13fc65085f0ed50d93f91e13f587ff00aae127bc67f63d4fa7a5fc519e364fe4784fd61ffeab8499319fd8f53e9e97f1461bfe7fcb36d7ff009a7a953f79b4a644f88dabf435ff0082c2cc7b5ffe69ea54fde6d29913e236afd0d7fe0b0b08ff00bfd3c6c6f4b07faa7fe360265dc7fca30df454beee3e62d8de960ff54ffc6c04cbb8ff009461be8a97ddc7c197551db99f93ec9fd63f1c6ceb33936e67e4fb27f58fc71b3acc33f37ee22221911110113cd470a0962001c493603c4cafed1de75175a2331f9c7d1f21c4fd5e708b949dac5128476d623fbd6fabf94433fd58d0888860d8c163aa5237a6c4768e2a7c44b3ecede547b0aa32376fc83e7cbcfdb2a110b639d8e960dc5c6a0f39f673fd9fb4ead1f41b4e6a7553e5cbca5a3676f0d2a960ff0006dde7aa7c1bf9da1be3e4953123715b215f134f1058834d0a05b0b106aa54b93e34c0f392510d25d2b386dce44a54a90aac45372e0d85c96c4d2c458f9d203c099eaaee8536c2b614d57ca5d5f359735d6a6703b2d7964884f3aaf62774e9bfbe2f51c7be0306d06999b104e5ff00ecb7fa44f6bbad4f262133bdb10aeadc2e03d2a74895d3b292f1ed327a20e550185dd4a486910eff00074fa317cba8e8e8a5ce9c6d417da67ad91baf4f0ef4dd5dc9455517cba851580bd87f8edfe9127620e555bd97b9d4a8261916a54230ef9d6f96e4deae8d61c3e19b876096488845b6f644487da3bc34a9dc2fc237603d51e2dfcaf0adb2769726da9e120f68ef2d34b8a5d76edf903cf9f97b6573686d4ab5bd36d3e68d17d9cfce69c32cbcbf4d9c6e3ea5637a8c4f60e0a3c04d68886444442088880888808888121b3b6cd5a3a03997e6b6a3cb98f2969d9bb72955b0be47f9adcfd53c0fdbdd28d3e42f8e763a6c4acee9632a31746625540b03adb5ede36ee96687463773644442488880919b4b6e52a3717ceff35797ac780fb7ba46ef6e32a29445621581b81a5f5ede32af0cb3f26bd448ed1db356b684e55f9aba0f33c4c8f888636efb2222104444044440ffd9, 'em_idcard.jpeg', 'image/jpeg', 7267, 'approved', NULL, '2025-10-19 03:32:34', NULL, NULL, '2025-10-19 03:32:19', '2025-10-19 03:32:34'),
(4, 35, 0xffd8ffe000104a46494600010100000100010000ffdb0084000906071312111312121216161515181a1a17161816191915181a18121a16151a1515181d28201d1a271b171523312125292b2e302e171f3338332d37282d2e2b010a0a0a0e0d0e1b10101b2d2620252d2d352d2f2d2f2d2d2d2d2d2d2d2d352d2d2d352b2d2d2d2d2d2d2d2d2d2b2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2dffc0001108011b00b203012200021101031101ffc4001c0001000203010101000000000000000000000506030407020108ffc4004c1000020102030406030b0905080300000001020003110412210506314113225161718132729107142333425273a1b1b2c134436274a2b3b4d1f0355363829215162445b5c2c4e28394e1ffc400190101000301010000000000000000000000000102030405ffc4002611010100020202020103050000000000000001021112310321045141133233237181b1f0ffda000c03010002110311003f00c7b0b7f996c98a5ce3fbc51d71eb2f06f1163dc65f3038ea75903d275753cc1e1dc47107b8ce17363018ea945f3d27646ed078f730e047718799a7748954dcedec38a6346aa015154b665f4580201ea9e07ac39f6f096b85488880888808888088880888808888088880889a7b631dd050ab5b8e452403c09b754799b0f381b35eb2a297760aa352cc4003c49949dbbbfeab74c2ae63fde303947aabc4f89b0ee3297b5b6c56c4b66ace5adc14688beaaf0f3e3df34216912adbc78b249f7cd5d7b1ac3c80d079448a884bdd4a45788fe53c49722f356ae0fe6fb21499fdb7f72b13d1e3a81be8c4a1ff003a951fb5967609c22954349d5edaa3061e2ac08fb2776470402381171e075109afb111085128ef3562aac2ba3d574ac6b6185301b0a69d17752df286575543d27a4585adc2666da98c14f09f1f9abb90572e133951857ab7a5d6c996e07a641b032eb10d39cfa53711b72b2bbd3a9585245ac94debbad3bd207034ead9bf3619aa31198dd4701ca7c1bc0defba747df88699a74595c1c3a0aa5ead4562054376b85516a7dba5ae25a36be3450a15ab15cc2953672b7b66caa5ad7f2914dbca43542d40f454eb2506a81c160ce29e56e8c81d4bd6404824f7412eff000d6da1b6997688c39aeb4d32d1214b5052c5ea54571f0bd66d1545935d7bc48fc36f3631930e0d300b62290a95adf06d42a62452409d95492c08e429b1f94b2c78adacfd3b50a340d56a6a8f50e7540a2a16081737a4c7231b683be6bd4de84157134fa36f804a8ead7196a9a28ad5953b0a974173ccb7cd3099fd9014b6fe3461db1049e8c5362d51d292a2b0c42a20a594dd814e92f986961ac92dadbc6a6ada9e368d1a029175add4a8956a0721a986272f5405254758f482c45a64a3bdd9faab4559cb51002d656a7f0e1f2dea05d18643752398e326364ed1e9854050d37a4f91d090d660aae0ab0d082aea41d0eba8105f5dc406136be319e95370a9d2d118937519a9aad30b568e53cfa56a6431d42b30e2a0c8ac36f555384a554e2973bbd0153af85f831529bb3f0d1351f9cd465f19d0e2d08e53e9a7b2311d250a6fd20a9987a60a3026e41eb52ea1b1d3aba693722214253bdd371b970e9481d6abdcfaa9a9fda292e3395fba2637a4c6141c29285b7e91ebb7de03fcb0455e7a5527402f36296109f4b4eee7371100d0082e72347de6ddd1242214e74888851e5d011622f2edbbbbd68a8946b02b91428a9c410a2c337307bf51e1297109974ec54ea060194820f020dc1f0227a9ca365ed6ad8737a6f61cd4ea87c57f11632edb1f7b2955b2d4f827ef3d43e0dcbc0fd70bcab0c444258719854ab4de954174a8a5585c8bab0b1171a8d0f29a1feeee1fa535723162e2a106ad434cba8015cd1cd90b00ab6397e48ec92b10996c47e376351aae2a3860e005252a54a79941b857e8d8675049d1afc4f699ae775b076f88507af7717151ba4565a99ea839dae1df893c7b8498883954554ddcc3104642a0946ea54a89d6a6b9518146166cba661a9b0bdec26decfd9f4e8294a4b60496624b33331e2ceec4b33683524f01d936a20dd222210448ddb3b6e96180ce49622e107a4795fb00ef328bb63792b622eb7c94cfc853c7d66e27ea1dd08b74b6ed8deaa346ea9f0afd8a7aa3d66fc05fca73caef9ea3d43e93b1627bd8dcdbba789f614b4889f5109d00bc21f226c7bc9bb47f5e5108e51b756886e23cf9cd3ab852386a3fae5246219ccac43c492ab870ddc7b66955c395ef1db0d2652b1444425d23733119f08839a165f61b8fd96127253fdcf2be95a9f61561e60a9fb165c21a4e89f09b0b9d00e27948ada7b7a9d2634d01ab547e6d3e4fd23f041e3af60322a8d27c4d4538a60cb9c7c0afc48d47a40eb50fada7708698f8ee497a5b45f104ae0d03806c6bbdc505f56dad53dcba7e909e31a989c2904bfbe5585d94aad3a8a6faf444754afe8b6bfa53e6d1df0c2e0d9d092ee08f83a4b72b6502c5ae157c09bf7489a9bfb86c410acaf474b02f97271e6ca4dbcf4ef8754f063c562d9bb5295704d36b95f4908cb510f63a1d47d937253f19835660daabafa3510e575f061cbb8dc4dbc26dcab4b4c42f489fdf535eb0fa5a43ef27b0439f3f159d2cb13161b1095143d360ea78329041f313ce3b1229537a87e4296f60b810c9ce77b317d262aa1e49d41fe5d0fed669110cc49249b93a93da4f18021993eaa93a09b14b064fa5a77739b94e985e0214b948d5a583f9decfff0066da281a0169ea219db6911108222202222060ab8507b8ff005ca6955a0578f0ede52526feefec57c6563455c535550eee4666b16cb645e17d0ea4d8761869e3996578c686e9ed25a188bb6621d5942aa96663a300aaba9375fae5e6860ebe275acc70d47fbb43f0efdd52a8d107726bfa525b0bbb987c1a01413ac41cd51bad51bd66ecfd1161dd36a8826fe5ca1e9f8fe3cc7f72abb53054e8d4e8e8a2a2051d551617d6e4f69ef3a99a18b765a15190d985ec7b0e9a8ef92fbc1f1edea8fc656f6e63722ad3c97e90b0bdce8465000b0efbf948b75375bcc6dba8ac6cca2a410c2faf1e67ce4a52d9345b8afd6660da1827b05a472f02483af7dbfa131ecda38ae8aa0352ee05d781e7adae35b09c76db77b7a524935a5b28d10aaaab7ca06809bdac48b027969f5ccb4e686c3a75852b576ced72735c1d085b0d00efede724544ebc6ee3cef24d65589700558d4a0e68d43c4817a6ff00494f83788b377cd4de8db353dec6955a451999417537a2c01bf55b8a9242f55bb74264cd292bb36d72194302a415201520e841074225d867e299394d2c213c741f5cdca5482f01fce5ab78b7516983570c7a30356a44e6a761727a33c50f76abdc256aa2d891d848f61b4879de6f1e585f6f91110c08888088880888808888096af731fcaf11f429fbd695596af731fcaf11f429fbd30e8f8bfcb17bdabc07819a5445afc39774dcdaade8f9cd3a44ebe5c07876c3d8fcabfbc1f1edea8fc6469d9cb5c046d0e7eab5ae549b0b8925b7b5aede03f198766b0560cc6c035c93c0016249ee8d6e7b57765dc5371cd91ad7e035f2e323701b40f4de95400f15b2dcf6e87ac7d9c386b37b7ae915af5b4b758b00798624fb26b6c14ad52a285a487f0079936d271f1f6f4397a5cb07ad2537bdc5fda349914423a02f494eb448561d974561e4430fae7a0275e33534e0cf2e595acf4a49ecee27c3f1123a9c91d9c753e1f88968abc6da07a1a9eab7dc328389f4dfd63f78cbfedbb74352ff0035bee1940c4fa6feb1fb4c570fccea31c44487011110111101125768ec1ab4ae40cebdaa351e2bc7d9791309b2cedf6222104b47b9ad40b8ac4126c3a05faaa9957933b9cd6af88fa05fde18747c5fe58e8d8ec4a38055af6bf0bf64d7a6f7b8d4f0eee7353660ba378ffdb297eeadb5aa51a3469d372bd2312d62412aa00b13d9761ec96e3ef4f5b97adb6b7c77870f87aac59c3358008843313dfc879ce77b6f7c2ad6a6ea3a8ac3d01d9cf31e2c797671d2572b54275330b34d263233b76eadbf1b5af4865a0181361598dbacdca9a8372343c74361a1e337b7536a2d3c396ab873875519b37157b0bdc93d604f20dddaf2950dff00c7354ae9495acb4821ff00311989f1cb61e67bed29bddb5c9d9d4b5eb622d7f0b02de56b8f394e33b5f95eb6a7aef055e9aa620394a8ee5fbacdf2483a100586bd825bf77b7e92a5971002372750721f59788fafca735733e03acbd92a8fd038775601958329e041047b4492d98a4b1005f4fc44e15bb7b62ad17cc8e4768f92ddcc0e93baec2a998861cd330f300894b34b6376f3b6fe26a7aadf70ca0627d37f58fde32fbb60fc0d43fa2df70ca1627d37f58fde32b5c5f33a8c711121c0444dbc06cda958f5174e6c7451e7f8084c9b6a44b28dd2edadfb1ffb4fb0b7e9e4b348cda5b0e956b9b647f9cbcfd61c0fdbdf24e21d164bda87b476355a3724665f9cba8f31c448f9d3243ed1ddea552e57e0dbb40ea9f15fe56865978be94b92bba6f6c455d2f7a4bfbc335b686cbab47d35d3e70d54f9f2f39bbb987fe26ae9f9a5fde185fe34feac5d302dd5e16eb77fcd9cb7dd7f139ab535be8b4c1f6b9278f7013ac61ce87c7f09c4fdd6b139b1b50762a8fd9bfe265f0edea67d2a0f35eafa2de1361cdd437681edb6bf5cd72668a45876bd7152bd46ed23ea503f01fd7a58f7831d9861e9f2a5457fd4e331fd9c922f0d5091737bdcff005f6fb7bee35eabdf8ebc3ea161f548349cddcdd6c4630934d72a0bfc235c2923e4af3637d34d073908e6c09ec9676dfdc6650b4cd3a4aa02a84a7c001600672c255b69552da9e2cd73de49249f6c7b4fa6ee00dad3bc6e262ba5a349f9f4641f15eafe13f3fe16a5ad3b57b95d6f80604e819c0f3507faf39197463fb939b65be01fd56fb8651f11e9b7ac7ed979da5418e1ea369a231e22f6c8794a58c33d4a8ca8a58e63a0e5af33c079ca64e3f99d46bccf83c154aa6d4d49ed3c8789e0258b676ec0166ac6e7e62f0f36e27cad2c34a98501540007000587b255cb8f8aded05b3b76516c6a9ce7e68f407e27fad24f2a800002c0700380f013ec43698c9d11110922220222207c22fa1e121abec32953a6c2b8a552d62a466a4e2f7b30e2baf353e464d44265d5dc46e076d80c296213a0aac740dad37d3f3554684fe89b3774e2bee8988cfb43127b1f2ffa4053f5833bc6270e9514a5450cadc558020f88339def67b9a0a99aa611acc7534d8defeab9ff00bbdb2d8dd3a31f3efd64e5b82a99a965e6a4fb0ebfce6373360ecead87aaf4eb2143cee08e1316216693a6db9bf4f949ecade3f6ff005fd6b3106bebec98831b11da7ecd266490964a6d35f1efd602675986a61ddd8e452d95731039004024fb47b632e89db3612a6a2fc3b7f9ceb5ee63b529515aa6bb855eadb996243001146acc6c3400994cdd5dc2c46280761d1d23f2d871f547caf2d3bc4ebbbbfbb387c181d1addc0b748dabf7dbe68f0f3bcadcbd698e5e592fa667ab5b13a2d3f7b513c7300d88a8398b7a34948f16d7e4c90a5455459542826f602da99ee251cf96772bba44442a444404444044440444404444044440d1dafb1e8e253256a618723c197d56e23c384e5fbd1ee715695df0f7aa9d96f8403bd47a5e5ec13af449974b6395c7a7e63a987b1ca410471bf384a439cefbbc7ba386c6025d7254e55146b7fd21f2bedef95dd89ee6688e5b12f9c03d545b804722cdc4780f6cbcca37fd69af6e7bb1776abe2db2d142473622cabe2dc07dbd979d4f767dcff0f86b3d502b55e648ea0e7a29e3e7d9c04b661b0e94d425350aa382a8b01e426495b96d965e4b911112acc88880888808888088880888808888088953de0deb7a18c185518650688abd2622b9a4bad429941ca6eda5ede3d90998dbd2d912b5477aff00e268615e9166ab483f49473d4a5d660172304eb53b3026a5c01ce6b6c5df8a4e4a57395fdf0f4415a553a204542b4d5aa1ba8722c78f3e50b70c96e890f5779282d65a2fd22333f46acd46a2d26a9c916a95ca49b1b6b63359b7d3062a3532ee0a55345dba2a9d1a540d96cd532e5173df08e37e96189a9b50e2028f7b2d22f9b51559d572d8dec5149bdf2f2ed9a3b9fb69b1b83a58964086a67ba8248196a32713eac235eb699895dc66f4ad2da54f0351405ab4d596a5ff0038cecaa8470d721b77d84d1c46f886ab8ea34b2a2e16897e9d94b82ead95ec83d255371a73530b70ab844a56d6decaf45316545263430d87aaac518066aad67ccb9f45e60711da64de377a30d49d91d9ee817a565a4ed4e966175e96a282a97ef308e153512269ef1506c53611598d65b660118a8069f48097032816b713c4da4b422cd1111082222022220222202222025636d6ecd4ab8c18ba556929e8051c956874ab6e94d4cc3ae2c7503dbdb2cf10996ce95e3b0ab74f86c40af4c54a54cd2a80513d1ba3386b5350fd4200b71330ff00ba87dea70fd28d713ef8cd93fc7e972dafe57963c45608b99b85c0d05c92cc15401da4903ce6856db48ac14a54cd7008097cac6a53450c41b5cf4a874b8b6bd972d32c9018adcba8f5fa46c4ab28c4ae2066a6cd56c1f37459cd4ca100d05947e133d7dcf2d85c661fa600e2714d880d93d1bd547ca466d7d0b5f4e327e9ed4a44039ada29b71f482955badc16eba754127ac3b44f55b68535547274760a2c09d4920dc72b59af7e194df84279e4c5b630f8870a70d5d68b024b66a42a2b02a40045c11626f707948fddeddf7c1d3c351a788bd1a48e1d0d317a8eee5c306bdd4024e9ae9dbc648d4dad483e42daf5b5009175a8b4ca8b0d5b3b81617d411c60ed7a36073823b790051981d788391c5c5f507b0d8afbd6919b5b7597115ead5a8e72d4c3ad101459d192b1aab515efa106d6d394d4adb94a0554a550223e0c6180cb720f48ce6a31b8b9258923b4932c0bb4e9136cdd9c8f12cea415f481069bdee0016f1b79a5b5a9370637b0394a306b1728bd5b5f52ad61cf29b70309e59207696e71aa9895e980e9f0f428df25f2f42d72de96b7ece53c63f72cbd7ab552a53cb5f29a895689a962aa158d321d746005c3032c29b5e89b90fa0b6b95ac6e99c6536eb0cba9b70b1bda6cd0c42bdca1b80482470b8e22fcfca0e79446ec9d89d0627175c302311d0d902db20a34ba3b66bea0f95a4bc442b6ec88884111101111011110111101111031d7a2aea55869a1e2410410ca411a8208041ee9806cda77bd89370c49624921d1c127c69a792db84db9ad5f1f4d1b2b137b5ec159ac3b4e506c3be1336c1fec6a597258e4b2f54b315ba65cad949b5c7469ecef37f7536552645a6c97550c00b9b75c598f8f1d795cf6ccaf8ea61b29719b2ab5bb559f22916e23369e63b67b4c5230243a900e5243020312005363c6e469df06eb5d766530c1fad704917662066a8b51ac2f6d5d41fab8690fb2e91b5c1ea8006a790703f78fedee9eaaed2a4b9b33119735ce5623aaa5982b016660013945ce874d0cf431c9982ddae6c35471627806256ca4f20d63a8ed109f6c5576552624906e4827ac7886761f5d47f68ec13d0d9b4c70cc0e96218dd72b3b2e53cbe31c781b199abe295080d719ad639588d48517602c2e4802f310da54ec0e622eeb4c02ac1b3bd8a82a45c68ca751a03ac23db1aec8a60586716e61dafe8943d6bdf55363e00f1179b586c3ad35ca82cb726dd9998b1b775c99f30b8a5a8095be96bdd594ea0106cc068411accd05d9111082222022220222202222022220227c26d0a6fa8d440fb34ebecf0cfd207746b5aeb9787f994cdc884a3eb6c8a6d97d25cbd105ca46828bb32817074218a9bf2e163acf783d9a29fa351f4544d727a14ef913d1e02edaf1eb1d7416dd883751d88d908e19599f2317393ab941a8ac1c8eadcdf3b9b1245d8e9c2d99702158946645241645ca1090a147c9b81955458103aa3befb64cf97d6dcfb39e9c7ed1ed8375a7576621c96ea043982a0502f7bdc756ea7883948b8241b89806c2a7a5cb960e1f306c9ae7473d5a7956c5a9a93a6a6e789bc936602c0902e6c3bcd89b0ed3604f918636e3a41bad7c0e13a30c33b39639999b2e6248b6b9540e000ee0001a09b313e6617b5c5cdc81cec2d736ecd47b4421f6222022220222202222022220222206bed0a06a52a94c5aee8ca2fc355235ee91cdb26a1a81c54c80be62886cabf17c3abd627a337d17e30f78699884cba57f05b2ab7474f3358e440c85ea6ac29b0676606f9eeeba0b8f831af02b97fd88d6525eed95d4b667b8ce13543c47c5dac2de913c46b3720b686d3a898ea14148c8f4cb30b0bdc626953e3eabb42d2db596bec77f9150aead7199b506b23a2eb70005575e07d2e045c4c6fb11cd365350b16d0966620afbd853ca7ff9006bdbeb95ec06f4e25f0f87a8597354acc8dd41c063f0f445872ea547f6c623797123673e2038e9055450722dacd5ca1d2d6e10b71c96ada5b35aaa2a0654cac5f817b36bd1917b7a2c43f732adb84d7a9b19daa876716bb5ecd533306c4d2ac178d8285a652dcc11c05c481da1bc5884f7f6571f02ae69f51742af8d0396ba50a5c7e6f799e976fe23a2da0dd26b469d56a672a754ae170eebcb5b35473adf8f70838e49dabb15cae55aac2e2c7aef7272564bdef707e129ebc7e0c76099b19b2cb53c80a9b3b32e6b90a19596dadc1b073a3022da5868456b666f0621ce18354be7c3e76ea26add0611afa2e9ad6a9a0d3addc2d9375b6f622b56a2952a6657a74cb0ca82e5862efa81fe0d3ff4f79b8b8e5da7696c97ce59eab32e72d6b90082c580b002c1410a05cdc0e5c27c5d93545af5735ae3296601941a4298622e7d1a6493add9db4b12255b767797155a8ecf6a95731ad5b2d43910665be274d174f8aa7c2de8f799d0a1196f1bed08db1eadd8f4cd6216c33b5b454197ac18dae8c6e4b7a66e0eb9a5b0b4caa22b1b955009d7520589d493ed332c4296ec88884111101111031d0aeaeb991830ed07fad6649ce30b8a7a6d9918a9eee7e2381f3965d9dbcea6cb58653f387a3e6388fafca19e3e497b58a279a750300ca41078106e0f9cf50d088880956daffdab85fa13fc650969956daffdab85fa13fc65085f0ed50d93f91e13f587ff00aae127bc67f63d4fa7a5fc519e364fe4784fd61ffeab8499319fd8f53e9e97f1461bfe7fcb36d7ff009a7a953f79b4a644f88dabf435ff0082c2cc7b5ffe69ea54fde6d29913e236afd0d7fe0b0b08ff00bfd3c6c6f4b07faa7fe360265dc7fca30df454beee3e62d8de960ff54ffc6c04cbb8ff009461be8a97ddc7c197551db99f93ec9fd63f1c6ceb33936e67e4fb27f58fc71b3acc33f37ee22221911110113cd470a0962001c493603c4cafed1de75175a2331f9c7d1f21c4fd5e708b949dac5128476d623fbd6fabf94433fd58d0888860d8c163aa5237a6c4768e2a7c44b3ecede547b0aa32376fc83e7cbcfdb2a110b639d8e960dc5c6a0f39f673fd9fb4ead1f41b4e6a7553e5cbca5a3676f0d2a960ff0006dde7aa7c1bf9da1be3e4953123715b215f134f1058834d0a05b0b106aa54b93e34c0f392510d25d2b386dce44a54a90aac45372e0d85c96c4d2c458f9d203c099eaaee8536c2b614d57ca5d5f359735d6a6703b2d7964884f3aaf62774e9bfbe2f51c7be0306d06999b104e5ff00ecb7fa44f6bbad4f262133bdb10aeadc2e03d2a74895d3b292f1ed327a20e550185dd4a486910eff00074fa317cba8e8e8a5ce9c6d417da67ad91baf4f0ef4dd5dc9455517cba851580bd87f8edfe9127620e555bd97b9d4a8261916a54230ef9d6f96e4deae8d61c3e19b876096488845b6f644487da3bc34a9dc2fc237603d51e2dfcaf0adb2769726da9e120f68ef2d34b8a5d76edf903cf9f97b6573686d4ab5bd36d3e68d17d9cfce69c32cbcbf4d9c6e3ea5637a8c4f60e0a3c04d68886444442088880888808888121b3b6cd5a3a03997e6b6a3cb98f2969d9bb72955b0be47f9adcfd53c0fdbdd28d3e42f8e763a6c4acee9632a31746625540b03adb5ede36ee96687463773644442488880919b4b6e52a3717ceff35797ac780fb7ba46ef6e32a29445621581b81a5f5ede32af0cb3f26bd448ed1db356b684e55f9aba0f33c4c8f888636efb2222104444044440ffd9, 'em_idcard.jpeg', 'image/jpeg', 7267, 'rejected', 8, '2025-10-19 03:37:37', NULL, NULL, '2025-10-19 03:33:57', '2025-10-19 03:37:37');
INSERT INTO `property_manager` (`id`, `user_id`, `employee_id_document`, `employee_id_filename`, `employee_id_filetype`, `employee_id_filesize`, `approval_status`, `approved_by`, `approved_at`, `rejection_reason`, `phone`, `created_at`, `updated_at`) VALUES
(6, 38, 0xffd8ffe000104a46494600010100000100010000ffdb0084000906071312111312121216161515181a1a17161816191915181a18121a16151a1515181d28201d1a271b171523312125292b2e302e171f3338332d37282d2e2b010a0a0a0e0d0e1b10101b2d2620252d2d352d2f2d2f2d2d2d2d2d2d2d2d352d2d2d352b2d2d2d2d2d2d2d2d2d2b2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2dffc0001108011b00b203012200021101031101ffc4001c0001000203010101000000000000000000000506030407020108ffc4004c1000020102030406030b0905080300000001020003110412210506314113225161718132729107142333425273a1b1b2c134436274a2b3b4d1f0355363829215162445b5c2c4e28394e1ffc400190101000301010000000000000000000000000102030405ffc4002611010100020202020103050000000000000001021112310321045141133233237181b1f0ffda000c03010002110311003f00c7b0b7f996c98a5ce3fbc51d71eb2f06f1163dc65f3038ea75903d275753cc1e1dc47107b8ce17363018ea945f3d27646ed078f730e047718799a7748954dcedec38a6346aa015154b665f4580201ea9e07ac39f6f096b85488880888808888088880888808888088880889a7b631dd050ab5b8e452403c09b754799b0f381b35eb2a297760aa352cc4003c49949dbbbfeab74c2ae63fde303947aabc4f89b0ee3297b5b6c56c4b66ace5adc14688beaaf0f3e3df34216912adbc78b249f7cd5d7b1ac3c80d079448a884bdd4a45788fe53c49722f356ae0fe6fb21499fdb7f72b13d1e3a81be8c4a1ff003a951fb5967609c22954349d5edaa3061e2ac08fb2776470402381171e075109afb111085128ef3562aac2ba3d574ac6b6185301b0a69d17752df286575543d27a4585adc2666da98c14f09f1f9abb90572e133951857ab7a5d6c996e07a641b032eb10d39cfa53711b72b2bbd3a9585245ac94debbad3bd207034ead9bf3619aa31198dd4701ca7c1bc0defba747df88699a74595c1c3a0aa5ead4562054376b85516a7dba5ae25a36be3450a15ab15cc2953672b7b66caa5ad7f2914dbca43542d40f454eb2506a81c160ce29e56e8c81d4bd6404824f7412eff000d6da1b6997688c39aeb4d32d1214b5052c5ea54571f0bd66d1545935d7bc48fc36f3631930e0d300b62290a95adf06d42a62452409d95492c08e429b1f94b2c78adacfd3b50a340d56a6a8f50e7540a2a16081737a4c7231b683be6bd4de84157134fa36f804a8ead7196a9a28ad5953b0a974173ccb7cd3099fd9014b6fe3461db1049e8c5362d51d292a2b0c42a20a594dd814e92f986961ac92dadbc6a6ada9e368d1a029175add4a8956a0721a986272f5405254758f482c45a64a3bdd9faab4559cb51002d656a7f0e1f2dea05d18643752398e326364ed1e9854050d37a4f91d090d660aae0ab0d082aea41d0eba8105f5dc406136be319e95370a9d2d118937519a9aad30b568e53cfa56a6431d42b30e2a0c8ac36f555384a554e2973bbd0153af85f831529bb3f0d1351f9cd465f19d0e2d08e53e9a7b2311d250a6fd20a9987a60a3026e41eb52ea1b1d3aba693722214253bdd371b970e9481d6abdcfaa9a9fda292e3395fba2637a4c6141c29285b7e91ebb7de03fcb0455e7a5527402f36296109f4b4eee7371100d0082e72347de6ddd1242214e74888851e5d011622f2edbbbbd68a8946b02b91428a9c410a2c337307bf51e1297109974ec54ea060194820f020dc1f0227a9ca365ed6ad8737a6f61cd4ea87c57f11632edb1f7b2955b2d4f827ef3d43e0dcbc0fd70bcab0c444258719854ab4de954174a8a5585c8bab0b1171a8d0f29a1feeee1fa535723162e2a106ad434cba8015cd1cd90b00ab6397e48ec92b10996c47e376351aae2a3860e005252a54a79941b857e8d8675049d1afc4f699ae775b076f88507af7717151ba4565a99ea839dae1df893c7b8498883954554ddcc3104642a0946ea54a89d6a6b9518146166cba661a9b0bdec26decfd9f4e8294a4b60496624b33331e2ceec4b33683524f01d936a20dd222210448ddb3b6e96180ce49622e107a4795fb00ef328bb63792b622eb7c94cfc853c7d66e27ea1dd08b74b6ed8deaa346ea9f0afd8a7aa3d66fc05fca73caef9ea3d43e93b1627bd8dcdbba789f614b4889f5109d00bc21f226c7bc9bb47f5e5108e51b756886e23cf9cd3ab852386a3fae5246219ccac43c492ab870ddc7b66955c395ef1db0d2652b1444425d23733119f08839a165f61b8fd96127253fdcf2be95a9f61561e60a9fb165c21a4e89f09b0b9d00e27948ada7b7a9d2634d01ab547e6d3e4fd23f041e3af60322a8d27c4d4538a60cb9c7c0afc48d47a40eb50fada7708698f8ee497a5b45f104ae0d03806c6bbdc505f56dad53dcba7e909e31a989c2904bfbe5585d94aad3a8a6faf444754afe8b6bfa53e6d1df0c2e0d9d092ee08f83a4b72b6502c5ae157c09bf7489a9bfb86c410acaf474b02f97271e6ca4dbcf4ef8754f063c562d9bb5295704d36b95f4908cb510f63a1d47d937253f19835660daabafa3510e575f061cbb8dc4dbc26dcab4b4c42f489fdf535eb0fa5a43ef27b0439f3f159d2cb13161b1095143d360ea78329041f313ce3b1229537a87e4296f60b810c9ce77b317d262aa1e49d41fe5d0fed669110cc49249b93a93da4f18021993eaa93a09b14b064fa5a77739b94e985e0214b948d5a583f9decfff0066da281a0169ea219db6911108222202222060ab8507b8ff005ca6955a0578f0ede52526feefec57c6563455c535550eee4666b16cb645e17d0ea4d8761869e3996578c686e9ed25a188bb6621d5942aa96663a300aaba9375fae5e6860ebe275acc70d47fbb43f0efdd52a8d107726bfa525b0bbb987c1a01413ac41cd51bad51bd66ecfd1161dd36a8826fe5ca1e9f8fe3cc7f72abb53054e8d4e8e8a2a2051d551617d6e4f69ef3a99a18b765a15190d985ec7b0e9a8ef92fbc1f1edea8fc656f6e63722ad3c97e90b0bdce8465000b0efbf948b75375bcc6dba8ac6cca2a410c2faf1e67ce4a52d9345b8afd6660da1827b05a472f02483af7dbfa131ecda38ae8aa0352ee05d781e7adae35b09c76db77b7a524935a5b28d10aaaab7ca06809bdac48b027969f5ccb4e686c3a75852b576ced72735c1d085b0d00efede724544ebc6ee3cef24d65589700558d4a0e68d43c4817a6ff00494f83788b377cd4de8db353dec6955a451999417537a2c01bf55b8a9242f55bb74264cd292bb36d72194302a415201520e841074225d867e299394d2c213c741f5cdca5482f01fce5ab78b7516983570c7a30356a44e6a761727a33c50f76abdc256aa2d891d848f61b4879de6f1e585f6f91110c08888088880888808888096af731fcaf11f429fbd695596af731fcaf11f429fbd30e8f8bfcb17bdabc07819a5445afc39774dcdaade8f9cd3a44ebe5c07876c3d8fcabfbc1f1edea8fc6469d9cb5c046d0e7eab5ae549b0b8925b7b5aede03f198766b0560cc6c035c93c0016249ee8d6e7b57765dc5371cd91ad7e035f2e323701b40f4de95400f15b2dcf6e87ac7d9c386b37b7ae915af5b4b758b00798624fb26b6c14ad52a285a487f0079936d271f1f6f4397a5cb07ad2537bdc5fda349914423a02f494eb448561d974561e4430fae7a0275e33534e0cf2e595acf4a49ecee27c3f1123a9c91d9c753e1f88968abc6da07a1a9eab7dc328389f4dfd63f78cbfedbb74352ff0035bee1940c4fa6feb1fb4c570fccea31c44487011110111101125768ec1ab4ae40cebdaa351e2bc7d9791309b2cedf6222104b47b9ad40b8ac4126c3a05faaa9957933b9cd6af88fa05fde18747c5fe58e8d8ec4a38055af6bf0bf64d7a6f7b8d4f0eee7353660ba378ffdb297eeadb5aa51a3469d372bd2312d62412aa00b13d9761ec96e3ef4f5b97adb6b7c77870f87aac59c3358008843313dfc879ce77b6f7c2ad6a6ea3a8ac3d01d9cf31e2c797671d2572b54275330b34d263233b76eadbf1b5af4865a0181361598dbacdca9a8372343c74361a1e337b7536a2d3c396ab873875519b37157b0bdc93d604f20dddaf2950dff00c7354ae9495acb4821ff00311989f1cb61e67bed29bddb5c9d9d4b5eb622d7f0b02de56b8f394e33b5f95eb6a7aef055e9aa620394a8ee5fbacdf2483a100586bd825bf77b7e92a5971002372750721f59788fafca735733e03acbd92a8fd038775601958329e041047b4492d98a4b1005f4fc44e15bb7b62ad17cc8e4768f92ddcc0e93baec2a998861cd330f300894b34b6376f3b6fe26a7aadf70ca0627d37f58fde32fbb60fc0d43fa2df70ca1627d37f58fde32b5c5f33a8c711121c0444dbc06cda958f5174e6c7451e7f8084c9b6a44b28dd2edadfb1ffb4fb0b7e9e4b348cda5b0e956b9b647f9cbcfd61c0fdbdf24e21d164bda87b476355a3724665f9cba8f31c448f9d3243ed1ddea552e57e0dbb40ea9f15fe56865978be94b92bba6f6c455d2f7a4bfbc335b686cbab47d35d3e70d54f9f2f39bbb987fe26ae9f9a5fde185fe34feac5d302dd5e16eb77fcd9cb7dd7f139ab535be8b4c1f6b9278f7013ac61ce87c7f09c4fdd6b139b1b50762a8fd9bfe265f0edea67d2a0f35eafa2de1361cdd437681edb6bf5cd72668a45876bd7152bd46ed23ea503f01fd7a58f7831d9861e9f2a5457fd4e331fd9c922f0d5091737bdcff005f6fb7bee35eabdf8ebc3ea161f548349cddcdd6c4630934d72a0bfc235c2923e4af3637d34d073908e6c09ec9676dfdc6650b4cd3a4aa02a84a7c001600672c255b69552da9e2cd73de49249f6c7b4fa6ee00dad3bc6e262ba5a349f9f4641f15eafe13f3fe16a5ad3b57b95d6f80604e819c0f3507faf39197463fb939b65be01fd56fb8651f11e9b7ac7ed979da5418e1ea369a231e22f6c8794a58c33d4a8ca8a58e63a0e5af33c079ca64e3f99d46bccf83c154aa6d4d49ed3c8789e0258b676ec0166ac6e7e62f0f36e27cad2c34a98501540007000587b255cb8f8aded05b3b76516c6a9ce7e68f407e27fad24f2a800002c0700380f013ec43698c9d11110922220222207c22fa1e121abec32953a6c2b8a552d62a466a4e2f7b30e2baf353e464d44265d5dc46e076d80c296213a0aac740dad37d3f3554684fe89b3774e2bee8988cfb43127b1f2ffa4053f5833bc6270e9514a5450cadc558020f88339def67b9a0a99aa611acc7534d8defeab9ff00bbdb2d8dd3a31f3efd64e5b82a99a965e6a4fb0ebfce6373360ecead87aaf4eb2143cee08e1316216693a6db9bf4f949ecade3f6ff005fd6b3106bebec98831b11da7ecd266490964a6d35f1efd602675986a61ddd8e452d95731039004024fb47b632e89db3612a6a2fc3b7f9ceb5ee63b529515aa6bb855eadb996243001146acc6c3400994cdd5dc2c46280761d1d23f2d871f547caf2d3bc4ebbbbfbb387c181d1addc0b748dabf7dbe68f0f3bcadcbd698e5e592fa667ab5b13a2d3f7b513c7300d88a8398b7a34948f16d7e4c90a5455459542826f602da99ee251cf96772bba44442a444404444044440444404444044440d1dafb1e8e253256a618723c197d56e23c384e5fbd1ee715695df0f7aa9d96f8403bd47a5e5ec13af449974b6395c7a7e63a987b1ca410471bf384a439cefbbc7ba386c6025d7254e55146b7fd21f2bedef95dd89ee6688e5b12f9c03d545b804722cdc4780f6cbcca37fd69af6e7bb1776abe2db2d142473622cabe2dc07dbd979d4f767dcff0f86b3d502b55e648ea0e7a29e3e7d9c04b661b0e94d425350aa382a8b01e426495b96d965e4b911112acc88880888808888088880888808888088953de0deb7a18c185518650688abd2622b9a4bad429941ca6eda5ede3d90998dbd2d912b5477aff00e268615e9166ab483f49473d4a5d660172304eb53b3026a5c01ce6b6c5df8a4e4a57395fdf0f4415a553a204542b4d5aa1ba8722c78f3e50b70c96e890f5779282d65a2fd22333f46acd46a2d26a9c916a95ca49b1b6b63359b7d3062a3532ee0a55345dba2a9d1a540d96cd532e5173df08e37e96189a9b50e2028f7b2d22f9b51559d572d8dec5149bdf2f2ed9a3b9fb69b1b83a58964086a67ba8248196a32713eac235eb699895dc66f4ad2da54f0351405ab4d596a5ff0038cecaa8470d721b77d84d1c46f886ab8ea34b2a2e16897e9d94b82ead95ec83d255371a73530b70ab844a56d6decaf45316545263430d87aaac518066aad67ccb9f45e60711da64de377a30d49d91d9ee817a565a4ed4e966175e96a282a97ef308e153512269ef1506c53611598d65b660118a8069f48097032816b713c4da4b422cd1111082222022220222202222025636d6ecd4ab8c18ba556929e8051c956874ab6e94d4cc3ae2c7503dbdb2cf10996ce95e3b0ab74f86c40af4c54a54cd2a80513d1ba3386b5350fd4200b71330ff00ba87dea70fd28d713ef8cd93fc7e972dafe57963c45608b99b85c0d05c92cc15401da4903ce6856db48ac14a54cd7008097cac6a53450c41b5cf4a874b8b6bd972d32c9018adcba8f5fa46c4ab28c4ae2066a6cd56c1f37459cd4ca100d05947e133d7dcf2d85c661fa600e2714d880d93d1bd547ca466d7d0b5f4e327e9ed4a44039ada29b71f482955badc16eba754127ac3b44f55b68535547274760a2c09d4920dc72b59af7e194df84279e4c5b630f8870a70d5d68b024b66a42a2b02a40045c11626f707948fddeddf7c1d3c351a788bd1a48e1d0d317a8eee5c306bdd4024e9ae9dbc648d4dad483e42daf5b5009175a8b4ca8b0d5b3b81617d411c60ed7a36073823b790051981d788391c5c5f507b0d8afbd6919b5b7597115ead5a8e72d4c3ad101459d192b1aab515efa106d6d394d4adb94a0554a550223e0c6180cb720f48ce6a31b8b9258923b4932c0bb4e9136cdd9c8f12cea415f481069bdee0016f1b79a5b5a9370637b0394a306b1728bd5b5f52ad61cf29b70309e59207696e71aa9895e980e9f0f428df25f2f42d72de96b7ece53c63f72cbd7ab552a53cb5f29a895689a962aa158d321d746005c3032c29b5e89b90fa0b6b95ac6e99c6536eb0cba9b70b1bda6cd0c42bdca1b80482470b8e22fcfca0e79446ec9d89d0627175c302311d0d902db20a34ba3b66bea0f95a4bc442b6ec88884111101111011110111101111031d7a2aea55869a1e2410410ca411a8208041ee9806cda77bd89370c49624921d1c127c69a792db84db9ad5f1f4d1b2b137b5ec159ac3b4e506c3be1336c1fec6a597258e4b2f54b315ba65cad949b5c7469ecef37f7536552645a6c97550c00b9b75c598f8f1d795cf6ccaf8ea61b29719b2ab5bb559f22916e23369e63b67b4c5230243a900e5243020312005363c6e469df06eb5d766530c1fad704917662066a8b51ac2f6d5d41fab8690fb2e91b5c1ea8006a790703f78fedee9eaaed2a4b9b33119735ce5623aaa5982b016660013945ce874d0cf431c9982ddae6c35471627806256ca4f20d63a8ed109f6c5576552624906e4827ac7886761f5d47f68ec13d0d9b4c70cc0e96218dd72b3b2e53cbe31c781b199abe295080d719ad639588d48517602c2e4802f310da54ec0e622eeb4c02ac1b3bd8a82a45c68ca751a03ac23db1aec8a60586716e61dafe8943d6bdf55363e00f1179b586c3ad35ca82cb726dd9998b1b775c99f30b8a5a8095be96bdd594ea0106cc068411accd05d9111082222022220222202222022220227c26d0a6fa8d440fb34ebecf0cfd207746b5aeb9787f994cdc884a3eb6c8a6d97d25cbd105ca46828bb32817074218a9bf2e163acf783d9a29fa351f4544d727a14ef913d1e02edaf1eb1d7416dd883751d88d908e19599f2317393ab941a8ac1c8eadcdf3b9b1245d8e9c2d99702158946645241645ca1090a147c9b81955458103aa3befb64cf97d6dcfb39e9c7ed1ed8375a7576621c96ea043982a0502f7bdc756ea7883948b8241b89806c2a7a5cb960e1f306c9ae7473d5a7956c5a9a93a6a6e789bc936602c0902e6c3bcd89b0ed3604f918636e3a41bad7c0e13a30c33b39639999b2e6248b6b9540e000ee0001a09b313e6617b5c5cdc81cec2d736ecd47b4421f6222022220222202222022220222206bed0a06a52a94c5aee8ca2fc355235ee91cdb26a1a81c54c80be62886cabf17c3abd627a337d17e30f78699884cba57f05b2ab7474f3358e440c85ea6ac29b0676606f9eeeba0b8f831af02b97fd88d6525eed95d4b667b8ce13543c47c5dac2de913c46b3720b686d3a898ea14148c8f4cb30b0bdc626953e3eabb42d2db596bec77f9150aead7199b506b23a2eb70005575e07d2e045c4c6fb11cd365350b16d0966620afbd853ca7ff9006bdbeb95ec06f4e25f0f87a8597354acc8dd41c063f0f445872ea547f6c623797123673e2038e9055450722dacd5ca1d2d6e10b71c96ada5b35aaa2a0654cac5f817b36bd1917b7a2c43f732adb84d7a9b19daa876716bb5ecd533306c4d2ac178d8285a652dcc11c05c481da1bc5884f7f6571f02ae69f51742af8d0396ba50a5c7e6f799e976fe23a2da0dd26b469d56a672a754ae170eebcb5b35473adf8f70838e49dabb15cae55aac2e2c7aef7272564bdef707e129ebc7e0c76099b19b2cb53c80a9b3b32e6b90a19596dadc1b073a3022da5868456b666f0621ce18354be7c3e76ea26add0611afa2e9ad6a9a0d3addc2d9375b6f622b56a2952a6657a74cb0ca82e5862efa81fe0d3ff4f79b8b8e5da7696c97ce59eab32e72d6b90082c580b002c1410a05cdc0e5c27c5d93545af5735ae3296601941a4298622e7d1a6493add9db4b12255b767797155a8ecf6a95731ad5b2d43910665be274d174f8aa7c2de8f799d0a1196f1bed08db1eadd8f4cd6216c33b5b454197ac18dae8c6e4b7a66e0eb9a5b0b4caa22b1b955009d7520589d493ed332c4296ec88884111101111031d0aeaeb991830ed07fad6649ce30b8a7a6d9918a9eee7e2381f3965d9dbcea6cb58653f387a3e6388fafca19e3e497b58a279a750300ca41078106e0f9cf50d088880956daffdab85fa13fc650969956daffdab85fa13fc65085f0ed50d93f91e13f587ff00aae127bc67f63d4fa7a5fc519e364fe4784fd61ffeab8499319fd8f53e9e97f1461bfe7fcb36d7ff009a7a953f79b4a644f88dabf435ff0082c2cc7b5ffe69ea54fde6d29913e236afd0d7fe0b0b08ff00bfd3c6c6f4b07faa7fe360265dc7fca30df454beee3e62d8de960ff54ffc6c04cbb8ff009461be8a97ddc7c197551db99f93ec9fd63f1c6ceb33936e67e4fb27f58fc71b3acc33f37ee22221911110113cd470a0962001c493603c4cafed1de75175a2331f9c7d1f21c4fd5e708b949dac5128476d623fbd6fabf94433fd58d0888860d8c163aa5237a6c4768e2a7c44b3ecede547b0aa32376fc83e7cbcfdb2a110b639d8e960dc5c6a0f39f673fd9fb4ead1f41b4e6a7553e5cbca5a3676f0d2a960ff0006dde7aa7c1bf9da1be3e4953123715b215f134f1058834d0a05b0b106aa54b93e34c0f392510d25d2b386dce44a54a90aac45372e0d85c96c4d2c458f9d203c099eaaee8536c2b614d57ca5d5f359735d6a6703b2d7964884f3aaf62774e9bfbe2f51c7be0306d06999b104e5ff00ecb7fa44f6bbad4f262133bdb10aeadc2e03d2a74895d3b292f1ed327a20e550185dd4a486910eff00074fa317cba8e8e8a5ce9c6d417da67ad91baf4f0ef4dd5dc9455517cba851580bd87f8edfe9127620e555bd97b9d4a8261916a54230ef9d6f96e4deae8d61c3e19b876096488845b6f644487da3bc34a9dc2fc237603d51e2dfcaf0adb2769726da9e120f68ef2d34b8a5d76edf903cf9f97b6573686d4ab5bd36d3e68d17d9cfce69c32cbcbf4d9c6e3ea5637a8c4f60e0a3c04d68886444442088880888808888121b3b6cd5a3a03997e6b6a3cb98f2969d9bb72955b0be47f9adcfd53c0fdbdd28d3e42f8e763a6c4acee9632a31746625540b03adb5ede36ee96687463773644442488880919b4b6e52a3717ceff35797ac780fb7ba46ef6e32a29445621581b81a5f5ede32af0cb3f26bd448ed1db356b684e55f9aba0f33c4c8f888636efb2222104444044440ffd9, 'em_idcard.jpeg', 'image/jpeg', 7267, 'approved', NULL, '2025-10-21 08:17:45', NULL, NULL, '2025-10-21 08:17:19', '2025-10-21 08:17:45');

-- --------------------------------------------------------

--
-- Table structure for table `service_providers`
--

CREATE TABLE `service_providers` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `specialty` enum('plumbing','electrical','hvac','general','cleaning','landscaping') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `rating` decimal(3,2) DEFAULT '0.00',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_providers`
--

INSERT INTO `service_providers` (`id`, `name`, `company`, `specialty`, `phone`, `email`, `address`, `rating`, `status`, `created_at`, `updated_at`) VALUES
(6, 'Mike Wilson', 'Cool Air HVAC', 'hvac', '+94 779 555 888', 'mike@coolair.com', '789 Pine Rd, Galle', 5.00, 'inactive', '2025-08-30 14:34:28', '2025-08-31 06:42:37'),
(17, 'Peter', 'Cool Air HVAC', 'hvac', '(232) 323-23232', 'provider@gmail.com', '', 5.00, 'active', '2025-10-23 03:32:08', '2025-10-30 08:21:35');

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
(5, 'admin 3', 'admin21@gmail.com', '$2y$10$XSQmVw0J04AGvytHo8Q5GePpZguh910aXM5h5urDCM5BftaMIMnEu', '2025-08-15 21:44:14', '2025-10-19 02:39:06', 'admin', 'active', NULL, '1.0'),
(6, 'admin2', 'admin33@gmail.com', '$2y$10$wx1nbtg8j4cmy8nJRgdQEedsxCg/YOI83dxAIVBc4DLsFi57bRnfS', '2025-08-16 21:42:34', '2025-10-19 02:39:06', 'admin', 'active', NULL, '1.0'),
(7, 'admin', 'admin333@gmail.com', '$2y$10$fbFy0ru98K8utip/GHOUg.O3SNvHdWZVv8UryeTG3AophzeFl/OVa', '2025-08-18 15:11:40', '2025-10-19 02:39:06', 'admin', 'active', NULL, '1.0'),
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
-- Indexes for table `inspections`
--
ALTER TABLE `inspections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tenant_id` (`tenant_id`),
  ADD KEY `idx_property_id` (`property_id`);

--
-- Indexes for table `market_properties`
--
ALTER TABLE `market_properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type_bedrooms` (`property_type`,`bedrooms`),
  ADD KEY `idx_rent` (`rent`),
  ADD KEY `idx_status` (`status`);

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
  ADD KEY `landlord_id` (`landlord_id`),
  ADD KEY `manager_id` (`manager_id`),
  ADD KEY `idx_listing_type` (`listing_type`);

--
-- Indexes for table `property_manager`
--
ALTER TABLE `property_manager`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `service_providers`
--
ALTER TABLE `service_providers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inspections`
--
ALTER TABLE `inspections`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `market_properties`
--
ALTER TABLE `market_properties`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `policy_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Posts`
--
ALTER TABLE `Posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `property_manager`
--
ALTER TABLE `property_manager`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `service_providers`
--
ALTER TABLE `service_providers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `policies`
--
ALTER TABLE `policies`
  ADD CONSTRAINT `policies_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `Posts`
--
ALTER TABLE `Posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `properties_ibfk_2` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `property_manager`
--
ALTER TABLE `property_manager`
  ADD CONSTRAINT `property_manager_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `property_manager_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
