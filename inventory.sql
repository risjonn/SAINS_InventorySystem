-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2025 at 06:20 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Router', 1, '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(2, 'Switch', 1, '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(3, 'Modem', 1, '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(4, 'Firewall', 1, '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(5, 'Server', 1, '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(6, 'Laptop', 1, '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(7, 'Printer', 1, '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(8, 'Storage', 1, '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(9, 'Access Point', 1, '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(10, 'UPS', 1, '2025-03-26 04:52:37', '2025-03-26 04:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `divisions`
--

INSERT INTO `divisions` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Kuching', '2025-03-26 04:52:36', '2025-03-26 04:52:36'),
(2, 'Miri', '2025-03-26 04:52:36', '2025-03-26 04:52:36'),
(3, 'Samarahan', '2025-03-26 04:52:36', '2025-03-26 04:52:36'),
(4, 'Sri Aman', '2025-03-26 04:52:36', '2025-03-26 04:52:36'),
(5, 'Bintulu', '2025-03-26 04:52:36', '2025-03-26 04:52:36'),
(6, 'Kapit', '2025-03-26 04:52:36', '2025-03-26 04:52:36');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2025_03_26_103245_create_divisions_table_new', 1),
(3, '2025_03_26_103322_create_users_table', 1),
(4, '2025_03_26_103356_create_user_product_thresholds_table', 1),
(5, '2025_03_26_103416_create_categories_table', 1),
(6, '2025_03_26_103442_create_products_table', 1),
(7, '2025_03_26_103505_create_stocks_table', 1),
(8, '2025_03_26_103547_create_stock_transactions_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_code`, `category`, `brand`, `model`, `status`, `created_at`, `updated_at`) VALUES
(1, 'RO001', 'Router', 'Huawei', 'MA5600T', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(2, 'RO002', 'Router', 'Cisco', 'Cisco ISR 4000', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(3, 'SW001', 'Switch', 'Cisco', 'Cisco Catalyst 2960', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(4, 'SW002', 'Switch', 'HP', 'HP ProCurve 5400', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(5, 'SW003', 'Switch', 'Dell EMC', 'Dell EMC Networking N1548', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(6, 'MO001', 'Modem', 'Netgear', 'Nighthawk CM1200', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(7, 'MO002', 'Modem', 'Arris', 'Surfboard SB8200', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(8, 'FI001', 'Firewall', 'Fortinet', 'FortiGate 60F', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(9, 'FI002', 'Firewall', 'Palo Alto', 'PA-220', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(10, 'SE001', 'Server', 'Dell', 'PowerEdge R740', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(11, 'SE002', 'Server', 'HP', 'ProLiant DL380 Gen10', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(12, 'LA001', 'Laptop', 'Dell', 'XPS 13', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(13, 'LA002', 'Laptop', 'Apple', 'MacBook Pro 13', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(14, 'LA003', 'Laptop', 'Lenovo', 'ThinkPad X1 Carbon', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(15, 'PR001', 'Printer', 'HP', 'OfficeJet Pro 9015', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(16, 'PR002', 'Printer', 'Canon', 'PIXMA TS9120', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(17, 'ST001', 'Storage', 'Seagate', 'Barracuda 4TB', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(18, 'ST002', 'Storage', 'Western Digital', 'WD Blue 4TB', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(19, 'AC001', 'Access Point', 'Ubiquiti', 'UniFi AC Pro', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(20, 'AC002', 'Access Point', 'Cisco', 'Aironet 2800', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(21, 'UP001', 'UPS', 'APC', 'Back-UPS Pro 1500VA', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(22, 'UP002', 'UPS', 'CyberPower', 'CP1500PFCLCD', 'active', '2025-03-26 04:52:37', '2025-03-26 04:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `requisition_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erp_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loan_period` int(10) DEFAULT NULL,
  `loan_start_date` date DEFAULT NULL,
  `status` enum('available','IN','OUT','LOAN','RETURN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`id`, `serial_number`, `product_code`, `product_id`, `requisition_no`, `erp_number`, `division_id`, `remarks`, `loan_period`, `loan_start_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'SN001', 'UP002', 22, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(2, 'SN002', 'SW003', 5, NULL, NULL, 1, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(3, 'SN003', 'LA002', 13, NULL, NULL, 3, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(4, 'SN004', 'UP002', 22, NULL, NULL, 5, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(5, 'SN005', 'SE002', 11, NULL, NULL, 3, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(6, 'SN006', 'AC002', 20, NULL, NULL, 4, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(7, 'SN007', 'SW001', 3, NULL, NULL, 6, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(8, 'SN008', 'LA002', 13, NULL, NULL, 5, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(9, 'SN009', 'MO002', 7, NULL, NULL, 5, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(10, 'SN010', 'LA001', 12, NULL, NULL, 5, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(11, 'SN011', 'UP002', 22, NULL, NULL, 2, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(12, 'SN012', 'LA001', 12, NULL, NULL, 5, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(13, 'SN013', 'SE001', 10, NULL, NULL, 2, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(14, 'SN014', 'LA002', 13, NULL, NULL, 2, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(15, 'SN015', 'ST001', 17, NULL, NULL, 4, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(16, 'SN016', 'SE002', 11, NULL, NULL, 6, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(17, 'SN017', 'MO001', 6, NULL, NULL, 5, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(18, 'SN018', 'SE001', 10, NULL, NULL, 2, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(19, 'SN019', 'RO002', 2, NULL, NULL, 5, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(20, 'SN020', 'AC002', 20, NULL, NULL, 3, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(21, 'SN021', 'RO002', 2, NULL, NULL, 1, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(22, 'SN022', 'UP002', 22, NULL, NULL, 2, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(23, 'SN023', 'FI002', 9, NULL, NULL, 6, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(24, 'SN024', 'AC001', 19, NULL, NULL, 4, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(25, 'SN025', 'FI002', 9, NULL, NULL, 1, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(26, 'SN026', 'LA002', 13, NULL, NULL, 1, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(27, 'SN027', 'SE001', 10, NULL, NULL, 6, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(28, 'SN028', 'MO002', 7, NULL, NULL, 4, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(29, 'SN029', 'RO001', 1, NULL, NULL, 2, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(30, 'SN030', 'SE002', 11, NULL, NULL, 2, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(31, 'SN031', 'AC002', 20, NULL, NULL, 5, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(32, 'SN032', 'MO002', 7, NULL, NULL, 1, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(33, 'SN033', 'RO002', 2, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(34, 'SN034', 'MO001', 6, NULL, NULL, 1, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(35, 'SN035', 'ST002', 18, NULL, NULL, 4, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(36, 'SN036', 'AC002', 20, NULL, NULL, 5, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(37, 'SN037', 'LA003', 14, NULL, NULL, 4, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(38, 'SN038', 'SW002', 4, NULL, NULL, 5, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(39, 'SN039', 'RO002', 2, NULL, NULL, 2, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(40, 'SN040', 'MO002', 7, NULL, NULL, 2, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(41, 'SN041', 'LA003', 14, NULL, NULL, 5, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(42, 'SN042', 'SW001', 3, NULL, NULL, 3, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(43, 'SN043', 'PR001', 15, NULL, NULL, 6, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(44, 'SN044', 'SW002', 4, NULL, NULL, 2, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(45, 'SN045', 'MO002', 7, NULL, NULL, 5, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(46, 'SN046', 'PR001', 15, NULL, NULL, 2, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(47, 'SN047', 'UP001', 21, NULL, NULL, 3, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(48, 'SN048', 'LA002', 13, NULL, NULL, 3, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(49, 'SN049', 'SE002', 11, NULL, NULL, 5, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(50, 'SN050', 'SW001', 3, NULL, NULL, 2, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(51, 'SN051', 'PR002', 16, NULL, NULL, 6, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(52, 'SN052', 'UP001', 21, NULL, NULL, 6, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(53, 'SN053', 'LA001', 12, NULL, NULL, 3, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(54, 'SN054', 'MO002', 7, NULL, NULL, 3, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(55, 'SN055', 'LA001', 12, NULL, NULL, 6, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(56, 'SN056', 'PR002', 16, NULL, NULL, 2, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(57, 'SN057', 'SW003', 5, NULL, NULL, 2, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(58, 'SN058', 'AC002', 20, NULL, NULL, 4, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(59, 'SN059', 'UP002', 22, NULL, NULL, 4, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(60, 'SN060', 'LA001', 12, NULL, NULL, 4, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(61, 'SN061', 'SE001', 10, NULL, NULL, 4, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(62, 'SN062', 'SE001', 10, NULL, NULL, 6, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(63, 'SN063', 'SW002', 4, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(64, 'SN064', 'ST002', 18, NULL, NULL, 4, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(65, 'SN065', 'LA001', 12, NULL, NULL, 4, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(66, 'SN066', 'LA003', 14, NULL, NULL, 3, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(67, 'SN067', 'PR002', 16, NULL, NULL, 2, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(68, 'SN068', 'UP001', 21, NULL, NULL, 4, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(69, 'SN069', 'RO001', 1, NULL, NULL, 4, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(70, 'SN070', 'ST001', 17, NULL, NULL, 4, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(71, 'SN071', 'PR001', 15, NULL, NULL, 5, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(72, 'SN072', 'AC002', 20, NULL, NULL, 6, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(73, 'SN073', 'SW002', 4, NULL, NULL, 1, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(74, 'SN074', 'MO002', 7, NULL, NULL, 2, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(75, 'SN075', 'ST001', 17, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(76, 'SN076', 'SE002', 11, NULL, NULL, 1, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(77, 'SN077', 'FI001', 8, NULL, NULL, 2, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(78, 'SN078', 'MO001', 6, NULL, NULL, 2, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(79, 'SN079', 'AC002', 20, NULL, NULL, 4, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(80, 'SN080', 'RO002', 2, NULL, NULL, 4, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(81, 'SN081', 'PR002', 16, NULL, NULL, 1, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(82, 'SN082', 'RO001', 1, NULL, NULL, 3, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(83, 'SN083', 'LA003', 14, NULL, NULL, 6, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(84, 'SN084', 'FI002', 9, NULL, NULL, 5, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(85, 'SN085', 'PR002', 16, NULL, NULL, 4, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(86, 'SN086', 'LA003', 14, NULL, NULL, 4, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(87, 'SN087', 'PR001', 15, NULL, NULL, 5, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(88, 'SN088', 'SE001', 10, NULL, NULL, 4, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(89, 'SN089', 'LA002', 13, NULL, NULL, 4, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(90, 'SN090', 'FI002', 9, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(91, 'SN091', 'LA003', 14, NULL, NULL, 1, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(92, 'SN092', 'SW002', 4, NULL, NULL, 5, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(93, 'SN093', 'SW003', 5, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(94, 'SN094', 'UP002', 22, NULL, NULL, 4, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(95, 'SN095', 'RO002', 2, NULL, NULL, 5, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(96, 'SN096', 'RO001', 1, NULL, NULL, 2, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(97, 'SN097', 'LA003', 14, NULL, NULL, 1, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(98, 'SN098', 'LA002', 13, NULL, NULL, 2, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(99, 'SN099', 'LA002', 13, NULL, NULL, 2, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(100, 'SN100', 'ST002', 18, NULL, NULL, 1, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(101, 'SN101', 'SW002', 4, NULL, NULL, 2, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(102, 'SN102', 'FI002', 9, NULL, NULL, 5, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(103, 'SN103', 'LA003', 14, NULL, NULL, 1, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(104, 'SN104', 'SW003', 5, NULL, NULL, 6, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(105, 'SN105', 'ST001', 17, NULL, NULL, 3, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(106, 'SN106', 'RO001', 1, NULL, NULL, 2, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(107, 'SN107', 'AC001', 19, NULL, NULL, 5, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(108, 'SN108', 'LA001', 12, NULL, NULL, 1, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(109, 'SN109', 'SW001', 3, NULL, NULL, 3, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(110, 'SN110', 'LA001', 12, NULL, NULL, 4, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(111, 'SN111', 'LA001', 12, NULL, NULL, 3, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(112, 'SN112', 'FI002', 9, NULL, NULL, 6, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(113, 'SN113', 'AC001', 19, NULL, NULL, 3, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(114, 'SN114', 'AC001', 19, NULL, NULL, 6, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(115, 'SN115', 'LA001', 12, NULL, NULL, 3, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(116, 'SN116', 'MO001', 6, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(117, 'SN117', 'LA003', 14, NULL, NULL, 6, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(118, 'SN118', 'ST001', 17, NULL, NULL, 2, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(119, 'SN119', 'PR002', 16, NULL, NULL, 2, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(120, 'SN120', 'AC001', 19, NULL, NULL, 2, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(121, 'SN121', 'MO001', 6, NULL, NULL, 6, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(122, 'SN122', 'RO002', 2, NULL, NULL, 4, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(123, 'SN123', 'RO002', 2, NULL, NULL, 4, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(124, 'SN124', 'SW002', 4, NULL, NULL, 5, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(125, 'SN125', 'PR001', 15, NULL, NULL, 4, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(126, 'SN126', 'SW002', 4, NULL, NULL, 4, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(127, 'SN127', 'SW002', 4, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(128, 'SN128', 'RO002', 2, NULL, NULL, 1, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(129, 'SN129', 'RO001', 1, NULL, NULL, 3, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(130, 'SN130', 'SW003', 5, NULL, NULL, 1, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(131, 'SN131', 'AC001', 19, NULL, NULL, 3, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(132, 'SN132', 'MO002', 7, NULL, NULL, 2, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(133, 'SN133', 'LA002', 13, NULL, NULL, 3, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(134, 'SN134', 'LA002', 13, NULL, NULL, 1, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(135, 'SN135', 'MO001', 6, NULL, NULL, 6, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(136, 'SN136', 'LA003', 14, NULL, NULL, 4, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(137, 'SN137', 'SW003', 5, NULL, NULL, 2, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(138, 'SN138', 'FI002', 9, NULL, NULL, 6, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(139, 'SN139', 'SW001', 3, NULL, NULL, 5, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(140, 'SN140', 'LA001', 12, NULL, NULL, 2, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(141, 'SN141', 'SW003', 5, NULL, NULL, 6, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(142, 'SN142', 'MO002', 7, NULL, NULL, 1, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(143, 'SN143', 'SW002', 4, NULL, NULL, 5, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(144, 'SN144', 'LA001', 12, NULL, NULL, 1, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(145, 'SN145', 'UP002', 22, NULL, NULL, 5, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(146, 'SN146', 'UP001', 21, NULL, NULL, 1, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(147, 'SN147', 'SW002', 4, NULL, NULL, 4, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(148, 'SN148', 'AC001', 19, NULL, NULL, 3, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(149, 'SN149', 'ST002', 18, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(150, 'SN150', 'SW001', 3, NULL, NULL, 3, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(151, 'SN151', 'SW003', 5, NULL, NULL, 5, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(152, 'SN152', 'UP001', 21, NULL, NULL, 2, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(153, 'SN153', 'PR001', 15, NULL, NULL, 5, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(154, 'SN154', 'LA002', 13, NULL, NULL, 5, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(155, 'SN155', 'SW002', 4, NULL, NULL, 6, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(156, 'SN156', 'RO001', 1, NULL, NULL, 4, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(157, 'SN157', 'LA003', 14, NULL, NULL, 2, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(158, 'SN158', 'SW002', 4, NULL, NULL, 1, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(159, 'SN159', 'SE001', 10, NULL, NULL, 3, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(160, 'SN160', 'SW003', 5, NULL, NULL, 6, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(161, 'SN161', 'PR002', 16, NULL, NULL, 5, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(162, 'SN162', 'SE001', 10, NULL, NULL, 3, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(163, 'SN163', 'AC001', 19, NULL, NULL, 2, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(164, 'SN164', 'SW002', 4, NULL, NULL, 1, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(165, 'SN165', 'LA002', 13, NULL, NULL, 4, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(166, 'SN166', 'PR001', 15, NULL, NULL, 4, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(167, 'SN167', 'AC002', 20, NULL, NULL, 6, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(168, 'SN168', 'SE001', 10, NULL, NULL, 5, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(169, 'SN169', 'LA003', 14, NULL, NULL, 3, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(170, 'SN170', 'MO001', 6, NULL, NULL, 1, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(171, 'SN171', 'LA002', 13, NULL, NULL, 4, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(172, 'SN172', 'UP001', 21, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(173, 'SN173', 'MO002', 7, NULL, NULL, 2, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(174, 'SN174', 'AC001', 19, NULL, NULL, 6, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(175, 'SN175', 'PR002', 16, NULL, NULL, 2, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(176, 'SN176', 'MO001', 6, NULL, NULL, 6, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(177, 'SN177', 'AC001', 19, NULL, NULL, 2, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(178, 'SN178', 'SW003', 5, NULL, NULL, 2, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(179, 'SN179', 'LA003', 14, NULL, NULL, 2, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(180, 'SN180', 'AC001', 19, NULL, NULL, 2, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(181, 'SN181', 'FI001', 8, NULL, NULL, 3, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(182, 'SN182', 'MO001', 6, NULL, NULL, 1, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(183, 'SN183', 'UP001', 21, NULL, NULL, 4, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(184, 'SN184', 'RO002', 2, NULL, NULL, 2, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(185, 'SN185', 'SE002', 11, NULL, NULL, 4, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(186, 'SN186', 'MO002', 7, NULL, NULL, 5, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(187, 'SN187', 'ST002', 18, NULL, NULL, 5, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(188, 'SN188', 'UP001', 21, NULL, NULL, 1, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(189, 'SN189', 'SW003', 5, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(190, 'SN190', 'RO002', 2, NULL, NULL, 4, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(191, 'SN191', 'LA002', 13, NULL, NULL, 1, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(192, 'SN192', 'UP002', 22, NULL, NULL, 1, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(193, 'SN193', 'MO001', 6, NULL, NULL, 4, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(194, 'SN194', 'MO002', 7, NULL, NULL, 2, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(195, 'SN195', 'SE002', 11, NULL, NULL, 4, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(196, 'SN196', 'ST002', 18, NULL, NULL, 1, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(197, 'SN197', 'ST002', 18, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(198, 'SN198', 'SW003', 5, NULL, NULL, 5, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(199, 'SN199', 'RO002', 2, NULL, NULL, 5, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(200, 'SN200', 'UP002', 22, NULL, NULL, 6, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(201, 'SN201', 'MO001', 6, NULL, NULL, 4, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(202, 'SN202', 'ST001', 17, NULL, NULL, 3, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(203, 'SN203', 'PR001', 15, NULL, NULL, 2, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(204, 'SN204', 'AC001', 19, NULL, NULL, 1, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(205, 'SN205', 'AC002', 20, NULL, NULL, 3, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(206, 'SN206', 'FI001', 8, NULL, NULL, 6, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(207, 'SN207', 'FI001', 8, NULL, NULL, 3, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(208, 'SN208', 'SW001', 3, NULL, NULL, 4, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(209, 'SN209', 'SE001', 10, NULL, NULL, 6, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(210, 'SN210', 'AC001', 19, NULL, NULL, 3, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(211, 'SN211', 'UP002', 22, NULL, NULL, 4, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(212, 'SN212', 'PR002', 16, NULL, NULL, 1, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(213, 'SN213', 'LA001', 12, NULL, NULL, 6, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(214, 'SN214', 'AC001', 19, NULL, NULL, 4, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(215, 'SN215', 'FI002', 9, NULL, NULL, 5, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(216, 'SN216', 'SE002', 11, NULL, NULL, 3, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(217, 'SN217', 'FI001', 8, NULL, NULL, 5, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(218, 'SN218', 'AC001', 19, NULL, NULL, 1, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(219, 'SN219', 'SE002', 11, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(220, 'SN220', 'ST002', 18, NULL, NULL, 6, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(221, 'SN221', 'LA003', 14, NULL, NULL, 5, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(222, 'SN222', 'SW003', 5, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(223, 'SN223', 'SW002', 4, NULL, NULL, 6, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(224, 'SN224', 'RO001', 1, NULL, NULL, 3, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(225, 'SN225', 'LA003', 14, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(226, 'SN226', 'LA002', 13, NULL, NULL, 4, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(227, 'SN227', 'SW002', 4, NULL, NULL, 1, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(228, 'SN228', 'AC002', 20, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(229, 'SN229', 'UP002', 22, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(230, 'SN230', 'SW003', 5, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(231, 'SN231', 'SE002', 11, NULL, NULL, 6, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(232, 'SN232', 'RO002', 2, NULL, NULL, 6, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(233, 'SN233', 'LA002', 13, NULL, NULL, 1, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(234, 'SN234', 'MO002', 7, NULL, NULL, 2, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(235, 'SN235', 'LA001', 12, NULL, NULL, 5, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(236, 'SN236', 'ST001', 17, NULL, NULL, 6, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(237, 'SN237', 'SW003', 5, NULL, NULL, 5, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(238, 'SN238', 'UP001', 21, NULL, NULL, 5, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(239, 'SN239', 'MO001', 6, NULL, NULL, 4, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(240, 'SN240', 'SW003', 5, NULL, NULL, 6, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(241, 'SN241', 'MO002', 7, NULL, NULL, 5, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(242, 'SN242', 'LA001', 12, NULL, NULL, 5, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(243, 'SN243', 'MO002', 7, NULL, NULL, 1, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(244, 'SN244', 'LA003', 14, NULL, NULL, 5, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(245, 'SN245', 'SW001', 3, NULL, NULL, 4, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(246, 'SN246', 'ST001', 17, NULL, NULL, 2, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(247, 'SN247', 'MO001', 6, NULL, NULL, 4, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(248, 'SN248', 'SE001', 10, NULL, NULL, 5, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(249, 'SN249', 'SW002', 4, NULL, NULL, 6, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(250, 'SN250', 'AC002', 20, NULL, NULL, 1, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(251, 'SN251', 'MO001', 6, NULL, NULL, 6, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(252, 'SN252', 'SW002', 4, NULL, NULL, 1, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(253, 'SN253', 'MO001', 6, NULL, NULL, 1, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(254, 'SN254', 'FI002', 9, NULL, NULL, 4, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(255, 'SN255', 'PR001', 15, NULL, NULL, 2, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(256, 'SN256', 'UP001', 21, NULL, NULL, 2, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(257, 'SN257', 'ST001', 17, NULL, NULL, 5, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(258, 'SN258', 'SW001', 3, NULL, NULL, 3, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(259, 'SN259', 'SE002', 11, NULL, NULL, 5, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(260, 'SN260', 'MO002', 7, NULL, NULL, 1, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(261, 'SN261', 'SE001', 10, NULL, NULL, 1, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(262, 'SN262', 'PR002', 16, NULL, NULL, 1, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(263, 'SN263', 'FI001', 8, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(264, 'SN264', 'PR002', 16, NULL, NULL, 5, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(265, 'SN265', 'SW002', 4, NULL, NULL, 1, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(266, 'SN266', 'AC002', 20, NULL, NULL, 1, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(267, 'SN267', 'SE002', 11, NULL, NULL, 6, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(268, 'SN268', 'RO002', 2, NULL, NULL, 6, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(269, 'SN269', 'PR001', 15, NULL, NULL, 4, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(270, 'SN270', 'SW001', 3, NULL, NULL, 5, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(271, 'SN271', 'FI002', 9, NULL, NULL, 3, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(272, 'SN272', 'LA001', 12, NULL, NULL, 5, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(273, 'SN273', 'LA001', 12, NULL, NULL, 6, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(274, 'SN274', 'SW002', 4, NULL, NULL, 3, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(275, 'SN275', 'MO001', 6, NULL, NULL, 4, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(276, 'SN276', 'MO002', 7, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(277, 'SN277', 'PR002', 16, NULL, NULL, 6, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(278, 'SN278', 'LA003', 14, NULL, NULL, 1, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(279, 'SN279', 'SW002', 4, NULL, NULL, 6, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(280, 'SN280', 'SE002', 11, NULL, NULL, 5, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(281, 'SN281', 'SW001', 3, NULL, NULL, 4, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(282, 'SN282', 'ST001', 17, NULL, NULL, 3, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(283, 'SN283', 'LA001', 12, NULL, NULL, 5, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(284, 'SN284', 'SW001', 3, NULL, NULL, 2, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(285, 'SN285', 'SW002', 4, NULL, NULL, 5, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(286, 'SN286', 'RO002', 2, NULL, NULL, 3, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(287, 'SN287', 'MO001', 6, NULL, NULL, 4, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(288, 'SN288', 'SE001', 10, NULL, NULL, 5, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(289, 'SN289', 'AC002', 20, NULL, NULL, 4, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(290, 'SN290', 'SW002', 4, NULL, NULL, 5, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(291, 'SN291', 'ST001', 17, NULL, NULL, 6, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(292, 'SN292', 'ST002', 18, NULL, NULL, 4, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(293, 'SN293', 'SE001', 10, NULL, NULL, 3, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(294, 'SN294', 'FI001', 8, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(295, 'SN295', 'MO002', 7, NULL, NULL, 2, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(296, 'SN296', 'MO001', 6, NULL, NULL, 1, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(297, 'SN297', 'FI001', 8, NULL, NULL, 1, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(298, 'SN298', 'SW001', 3, NULL, NULL, 3, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(299, 'SN299', 'FI001', 8, NULL, NULL, 4, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(300, 'SN300', 'SW002', 4, NULL, NULL, 2, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(301, 'SN301', 'UP002', 22, NULL, NULL, 2, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(302, 'SN302', 'LA001', 12, NULL, NULL, 1, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(303, 'SN303', 'RO002', 2, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(304, 'SN304', 'LA001', 12, NULL, NULL, 5, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(305, 'SN305', 'LA003', 14, NULL, NULL, 4, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(306, 'SN306', 'SW001', 3, NULL, NULL, 1, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(307, 'SN307', 'LA003', 14, NULL, NULL, 2, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(308, 'SN308', 'SE002', 11, NULL, NULL, 4, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(309, 'SN309', 'AC001', 19, NULL, NULL, 6, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(310, 'SN310', 'UP002', 22, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(311, 'SN311', 'ST002', 18, NULL, NULL, 5, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(312, 'SN312', 'SW003', 5, NULL, NULL, 6, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(313, 'SN313', 'SE002', 11, NULL, NULL, 4, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(314, 'SN314', 'FI002', 9, NULL, NULL, 5, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(315, 'SN315', 'RO001', 1, NULL, NULL, 5, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(316, 'SN316', 'SW002', 4, NULL, NULL, 2, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(317, 'SN317', 'ST002', 18, NULL, NULL, 2, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(318, 'SN318', 'LA002', 13, NULL, NULL, 4, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(319, 'SN319', 'SW002', 4, NULL, NULL, 1, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(320, 'SN320', 'ST001', 17, NULL, NULL, 1, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(321, 'SN321', 'AC002', 20, NULL, NULL, 3, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(322, 'SN322', 'PR002', 16, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(323, 'SN323', 'LA002', 13, NULL, NULL, 3, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(324, 'SN324', 'SW001', 3, NULL, NULL, 5, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(325, 'SN325', 'SW002', 4, NULL, NULL, 1, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(326, 'SN326', 'LA001', 12, NULL, NULL, 6, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(327, 'SN327', 'RO002', 2, NULL, NULL, 3, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(328, 'SN328', 'UP002', 22, NULL, NULL, 4, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(329, 'SN329', 'RO001', 1, NULL, NULL, 4, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(330, 'SN330', 'SW003', 5, NULL, NULL, 3, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(331, 'SN331', 'FI001', 8, NULL, NULL, 1, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(332, 'SN332', 'LA002', 13, NULL, NULL, 3, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(333, 'SN333', 'LA002', 13, NULL, NULL, 5, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(334, 'SN334', 'PR002', 16, NULL, NULL, 6, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(335, 'SN335', 'FI001', 8, NULL, NULL, 6, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(336, 'SN336', 'UP002', 22, NULL, NULL, 3, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(337, 'SN337', 'FI002', 9, NULL, NULL, 5, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(338, 'SN338', 'LA002', 13, NULL, NULL, 2, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(339, 'SN339', 'ST001', 17, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(340, 'SN340', 'MO001', 6, NULL, NULL, 1, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(341, 'SN341', 'PR002', 16, NULL, NULL, 5, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(342, 'SN342', 'ST002', 18, NULL, NULL, 6, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(343, 'SN343', 'RO001', 1, NULL, NULL, 4, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(344, 'SN344', 'MO001', 6, NULL, NULL, 3, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(345, 'SN345', 'UP001', 21, NULL, NULL, 2, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(346, 'SN346', 'RO002', 2, NULL, NULL, 2, 'Recently upgraded', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(347, 'SN347', 'UP002', 22, NULL, NULL, 4, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(348, 'SN348', 'FI002', 9, NULL, NULL, 2, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(349, 'SN349', 'MO002', 7, NULL, NULL, 1, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(350, 'SN350', 'SE002', 11, NULL, NULL, 6, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(351, 'SN351', 'MO001', 6, NULL, NULL, 5, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(352, 'SN352', 'MO002', 7, NULL, NULL, 4, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(353, 'SN353', 'SE001', 10, NULL, NULL, 4, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(354, 'SN354', 'ST001', 17, NULL, NULL, 1, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(355, 'SN355', 'MO002', 7, NULL, NULL, 4, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(356, 'SN356', 'AC001', 19, NULL, NULL, 6, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(357, 'SN357', 'MO002', 7, NULL, NULL, 5, 'Under performance testing', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(358, 'SN358', 'LA001', 12, NULL, NULL, 5, 'Requires firmware update', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(359, 'SN359', 'FI001', 8, NULL, NULL, 3, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37');
INSERT INTO `stocks` (`id`, `serial_number`, `product_code`, `product_id`, `requisition_no`, `erp_number`, `division_id`, `remarks`, `loan_period`, `loan_start_date`, `status`, `created_at`, `updated_at`) VALUES
(360, 'SN360', 'SW003', 5, NULL, NULL, 3, 'Installed in main office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(361, 'SN361', 'FI001', 8, NULL, NULL, 4, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(362, 'SN362', 'ST001', 17, NULL, NULL, 4, 'Pending inspection', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(363, 'SN363', 'UP001', 21, NULL, NULL, 6, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(364, 'SN364', 'LA003', 14, NULL, NULL, 5, 'Deployed in data center', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(365, 'SN365', 'FI001', 8, NULL, NULL, 3, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(366, 'SN366', 'SW002', 4, NULL, NULL, 3, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(367, 'SN367', 'LA003', 14, NULL, NULL, 2, 'Under routine maintenance', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(368, 'SN368', 'FI001', 8, NULL, NULL, 3, 'Located in branch office', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(369, 'SN369', 'FI002', 9, NULL, NULL, 3, 'Ready for deployment', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(370, 'SN370', 'AC002', 20, NULL, NULL, 2, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(371, 'SN371', 'PR002', 16, NULL, NULL, 1, 'For backup operations', NULL, NULL, 'available', '2025-03-26 04:52:37', '2025-03-26 04:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transactions`
--

CREATE TABLE `stock_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `requisition_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('IN','OUT','LOAN','RETURN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IN',
  `handled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_transactions`
--

INSERT INTO `stock_transactions` (`id`, `serial_number`, `product_code`, `product_id`, `requisition_no`, `type`, `handled_by`, `remarks`, `transaction_date`) VALUES
(1, 'SN001', 'UP002', 22, NULL, 'IN', 4, 'Initial stock received - CP1500PFCLCD', '2025-02-27 04:52:37'),
(2, 'SN002', 'SW003', 5, NULL, 'IN', 2, 'New shipment arrived - Dell EMC Networking N1548', '2025-03-10 04:52:37'),
(3, 'SN003', 'LA002', 13, NULL, 'IN', 4, 'Replenishment confirmed - MacBook Pro 13', '2025-03-24 04:52:37'),
(4, 'SN004', 'UP002', 22, NULL, 'IN', 6, 'New shipment arrived - CP1500PFCLCD', '2025-02-25 04:52:37'),
(5, 'SN005', 'SE002', 11, NULL, 'IN', 4, 'Routine restock completed - ProLiant DL380 Gen10', '2025-03-01 04:52:37'),
(6, 'SN006', 'AC002', 20, NULL, 'IN', 5, 'Discrepancy resolved - Aironet 2800', '2025-03-25 04:52:37'),
(7, 'SN007', 'SW001', 3, NULL, 'IN', 7, 'Emergency purchase executed - Cisco Catalyst 2960', '2025-03-25 04:52:37'),
(8, 'SN008', 'LA002', 13, NULL, 'IN', 6, 'Routine restock completed - MacBook Pro 13', '2025-03-01 04:52:37'),
(9, 'SN009', 'MO002', 7, NULL, 'IN', 6, 'Emergency purchase executed - Surfboard SB8200', '2025-03-23 04:52:37'),
(10, 'SN010', 'LA001', 12, NULL, 'IN', 6, 'Routine restock completed - XPS 13', '2025-03-24 04:52:37'),
(11, 'SN011', 'UP002', 22, NULL, 'IN', 3, 'Initial stock received - CP1500PFCLCD', '2025-03-12 04:52:37'),
(12, 'SN012', 'LA001', 12, NULL, 'IN', 6, 'Manual entry for low stock - XPS 13', '2025-03-23 04:52:37'),
(13, 'SN013', 'SE001', 10, NULL, 'IN', 3, 'New shipment arrived - PowerEdge R740', '2025-03-13 04:52:37'),
(14, 'SN014', 'LA002', 13, NULL, 'IN', 3, 'Replenishment confirmed - MacBook Pro 13', '2025-03-08 04:52:37'),
(15, 'SN015', 'ST001', 17, NULL, 'IN', 5, 'Emergency purchase executed - Barracuda 4TB', '2025-03-23 04:52:37'),
(16, 'SN016', 'SE002', 11, NULL, 'IN', 7, 'Replenishment confirmed - ProLiant DL380 Gen10', '2025-03-11 04:52:37'),
(17, 'SN017', 'MO001', 6, NULL, 'IN', 6, 'Initial stock received - Nighthawk CM1200', '2025-03-01 04:52:37'),
(18, 'SN018', 'SE001', 10, NULL, 'IN', 3, 'Delivered to branch office - PowerEdge R740', '2025-03-12 04:52:37'),
(19, 'SN019', 'RO002', 2, NULL, 'IN', 6, 'Routine restock completed - Cisco ISR 4000', '2025-03-21 04:52:37'),
(20, 'SN020', 'AC002', 20, NULL, 'IN', 4, 'New shipment arrived - Aironet 2800', '2025-03-21 04:52:37'),
(21, 'SN021', 'RO002', 2, NULL, 'IN', 2, 'Emergency purchase executed - Cisco ISR 4000', '2025-03-25 04:52:37'),
(22, 'SN022', 'UP002', 22, NULL, 'IN', 3, 'Initial stock received - CP1500PFCLCD', '2025-03-07 04:52:37'),
(23, 'SN023', 'FI002', 9, NULL, 'IN', 7, 'Routine restock completed - PA-220', '2025-03-09 04:52:37'),
(24, 'SN024', 'AC001', 19, NULL, 'IN', 5, 'Discrepancy resolved - UniFi AC Pro', '2025-02-24 04:52:37'),
(25, 'SN025', 'FI002', 9, NULL, 'IN', 2, 'Routine restock completed - PA-220', '2025-03-09 04:52:37'),
(26, 'SN026', 'LA002', 13, NULL, 'IN', 2, 'Replenishment confirmed - MacBook Pro 13', '2025-03-16 04:52:37'),
(27, 'SN027', 'SE001', 10, NULL, 'IN', 7, 'Routine restock completed - PowerEdge R740', '2025-03-25 04:52:37'),
(28, 'SN028', 'MO002', 7, NULL, 'IN', 5, 'Replenishment confirmed - Surfboard SB8200', '2025-03-23 04:52:37'),
(29, 'SN029', 'RO001', 1, NULL, 'IN', 3, 'Manual entry for low stock - MA5600T', '2025-03-04 04:52:37'),
(30, 'SN030', 'SE002', 11, NULL, 'IN', 3, 'New shipment arrived - ProLiant DL380 Gen10', '2025-03-05 04:52:37'),
(31, 'SN031', 'AC002', 20, NULL, 'IN', 6, 'New shipment arrived - Aironet 2800', '2025-03-11 04:52:37'),
(32, 'SN032', 'MO002', 7, NULL, 'IN', 2, 'Initial stock received - Surfboard SB8200', '2025-03-07 04:52:37'),
(33, 'SN033', 'RO002', 2, NULL, 'IN', 4, 'Manual entry for low stock - Cisco ISR 4000', '2025-03-08 04:52:37'),
(34, 'SN034', 'MO001', 6, NULL, 'IN', 2, 'Discrepancy resolved - Nighthawk CM1200', '2025-03-05 04:52:37'),
(35, 'SN035', 'ST002', 18, NULL, 'IN', 5, 'Delivered to branch office - WD Blue 4TB', '2025-03-21 04:52:37'),
(36, 'SN036', 'AC002', 20, NULL, 'IN', 6, 'Manual entry for low stock - Aironet 2800', '2025-03-08 04:52:37'),
(37, 'SN037', 'LA003', 14, NULL, 'IN', 5, 'Delivered to branch office - ThinkPad X1 Carbon', '2025-03-05 04:52:37'),
(38, 'SN038', 'SW002', 4, NULL, 'IN', 6, 'Manual entry for low stock - HP ProCurve 5400', '2025-03-21 04:52:37'),
(39, 'SN039', 'RO002', 2, NULL, 'IN', 3, 'Returned from maintenance - Cisco ISR 4000', '2025-03-16 04:52:37'),
(40, 'SN040', 'MO002', 7, NULL, 'IN', 3, 'Routine restock completed - Surfboard SB8200', '2025-03-08 04:52:37'),
(41, 'SN041', 'LA003', 14, NULL, 'IN', 6, 'Delivered to branch office - ThinkPad X1 Carbon', '2025-03-09 04:52:37'),
(42, 'SN042', 'SW001', 3, NULL, 'IN', 4, 'New shipment arrived - Cisco Catalyst 2960', '2025-03-23 04:52:37'),
(43, 'SN043', 'PR001', 15, NULL, 'IN', 7, 'Emergency purchase executed - OfficeJet Pro 9015', '2025-03-03 04:52:37'),
(44, 'SN044', 'SW002', 4, NULL, 'IN', 3, 'Manual entry for low stock - HP ProCurve 5400', '2025-03-09 04:52:37'),
(45, 'SN045', 'MO002', 7, NULL, 'IN', 6, 'Delivered to branch office - Surfboard SB8200', '2025-03-16 04:52:37'),
(46, 'SN046', 'PR001', 15, NULL, 'IN', 3, 'Initial stock received - OfficeJet Pro 9015', '2025-02-25 04:52:37'),
(47, 'SN047', 'UP001', 21, NULL, 'IN', 4, 'Manual entry for low stock - Back-UPS Pro 1500VA', '2025-02-26 04:52:37'),
(48, 'SN048', 'LA002', 13, NULL, 'IN', 4, 'Discrepancy resolved - MacBook Pro 13', '2025-03-13 04:52:37'),
(49, 'SN049', 'SE002', 11, NULL, 'IN', 6, 'Stock adjustment after audit - ProLiant DL380 Gen10', '2025-03-12 04:52:37'),
(50, 'SN050', 'SW001', 3, NULL, 'IN', 3, 'Discrepancy resolved - Cisco Catalyst 2960', '2025-03-15 04:52:37'),
(51, 'SN051', 'PR002', 16, NULL, 'IN', 7, 'Manual entry for low stock - PIXMA TS9120', '2025-03-24 04:52:37'),
(52, 'SN052', 'UP001', 21, NULL, 'IN', 7, 'Stock adjustment after audit - Back-UPS Pro 1500VA', '2025-03-12 04:52:37'),
(53, 'SN053', 'LA001', 12, NULL, 'IN', 4, 'Delivered to branch office - XPS 13', '2025-03-24 04:52:37'),
(54, 'SN054', 'MO002', 7, NULL, 'IN', 4, 'Initial stock received - Surfboard SB8200', '2025-03-21 04:52:37'),
(55, 'SN055', 'LA001', 12, NULL, 'IN', 7, 'Initial stock received - XPS 13', '2025-02-26 04:52:37'),
(56, 'SN056', 'PR002', 16, NULL, 'IN', 3, 'Discrepancy resolved - PIXMA TS9120', '2025-02-27 04:52:37'),
(57, 'SN057', 'SW003', 5, NULL, 'IN', 3, 'Manual entry for low stock - Dell EMC Networking N1548', '2025-03-07 04:52:37'),
(58, 'SN058', 'AC002', 20, NULL, 'IN', 5, 'Discrepancy resolved - Aironet 2800', '2025-03-19 04:52:37'),
(59, 'SN059', 'UP002', 22, NULL, 'IN', 5, 'Stock adjustment after audit - CP1500PFCLCD', '2025-03-21 04:52:37'),
(60, 'SN060', 'LA001', 12, NULL, 'IN', 5, 'Discrepancy resolved - XPS 13', '2025-03-04 04:52:37'),
(61, 'SN061', 'SE001', 10, NULL, 'IN', 5, 'New shipment arrived - PowerEdge R740', '2025-03-16 04:52:37'),
(62, 'SN062', 'SE001', 10, NULL, 'IN', 7, 'Routine restock completed - PowerEdge R740', '2025-03-05 04:52:37'),
(63, 'SN063', 'SW002', 4, NULL, 'IN', 4, 'Routine restock completed - HP ProCurve 5400', '2025-03-06 04:52:37'),
(64, 'SN064', 'ST002', 18, NULL, 'IN', 5, 'Replenishment confirmed - WD Blue 4TB', '2025-03-18 04:52:37'),
(65, 'SN065', 'LA001', 12, NULL, 'IN', 5, 'Routine restock completed - XPS 13', '2025-03-22 04:52:37'),
(66, 'SN066', 'LA003', 14, NULL, 'IN', 4, 'New shipment arrived - ThinkPad X1 Carbon', '2025-03-10 04:52:37'),
(67, 'SN067', 'PR002', 16, NULL, 'IN', 3, 'Stock adjustment after audit - PIXMA TS9120', '2025-03-21 04:52:37'),
(68, 'SN068', 'UP001', 21, NULL, 'IN', 5, 'Returned from maintenance - Back-UPS Pro 1500VA', '2025-03-12 04:52:37'),
(69, 'SN069', 'RO001', 1, NULL, 'IN', 5, 'Delivered to branch office - MA5600T', '2025-03-03 04:52:37'),
(70, 'SN070', 'ST001', 17, NULL, 'IN', 5, 'Emergency purchase executed - Barracuda 4TB', '2025-03-05 04:52:37'),
(71, 'SN071', 'PR001', 15, NULL, 'IN', 6, 'Discrepancy resolved - OfficeJet Pro 9015', '2025-03-03 04:52:37'),
(72, 'SN072', 'AC002', 20, NULL, 'IN', 7, 'Initial stock received - Aironet 2800', '2025-03-05 04:52:37'),
(73, 'SN073', 'SW002', 4, NULL, 'IN', 2, 'Initial stock received - HP ProCurve 5400', '2025-03-24 04:52:37'),
(74, 'SN074', 'MO002', 7, NULL, 'IN', 3, 'Replenishment confirmed - Surfboard SB8200', '2025-03-24 04:52:37'),
(75, 'SN075', 'ST001', 17, NULL, 'IN', 4, 'Replenishment confirmed - Barracuda 4TB', '2025-02-27 04:52:37'),
(76, 'SN076', 'SE002', 11, NULL, 'IN', 2, 'Manual entry for low stock - ProLiant DL380 Gen10', '2025-03-18 04:52:37'),
(77, 'SN077', 'FI001', 8, NULL, 'IN', 3, 'Returned from maintenance - FortiGate 60F', '2025-03-14 04:52:37'),
(78, 'SN078', 'MO001', 6, NULL, 'IN', 3, 'Discrepancy resolved - Nighthawk CM1200', '2025-03-09 04:52:37'),
(79, 'SN079', 'AC002', 20, NULL, 'IN', 5, 'Routine restock completed - Aironet 2800', '2025-03-20 04:52:37'),
(80, 'SN080', 'RO002', 2, NULL, 'IN', 5, 'Discrepancy resolved - Cisco ISR 4000', '2025-03-01 04:52:37'),
(81, 'SN081', 'PR002', 16, NULL, 'IN', 2, 'Emergency purchase executed - PIXMA TS9120', '2025-03-08 04:52:37'),
(82, 'SN082', 'RO001', 1, NULL, 'IN', 4, 'Delivered to branch office - MA5600T', '2025-03-23 04:52:37'),
(83, 'SN083', 'LA003', 14, NULL, 'IN', 7, 'Returned from maintenance - ThinkPad X1 Carbon', '2025-03-04 04:52:37'),
(84, 'SN084', 'FI002', 9, NULL, 'IN', 6, 'Delivered to branch office - PA-220', '2025-03-09 04:52:37'),
(85, 'SN085', 'PR002', 16, NULL, 'IN', 5, 'Returned from maintenance - PIXMA TS9120', '2025-02-28 04:52:37'),
(86, 'SN086', 'LA003', 14, NULL, 'IN', 5, 'Manual entry for low stock - ThinkPad X1 Carbon', '2025-02-25 04:52:37'),
(87, 'SN087', 'PR001', 15, NULL, 'IN', 6, 'Routine restock completed - OfficeJet Pro 9015', '2025-03-14 04:52:37'),
(88, 'SN088', 'SE001', 10, NULL, 'IN', 5, 'Returned from maintenance - PowerEdge R740', '2025-03-07 04:52:37'),
(89, 'SN089', 'LA002', 13, NULL, 'IN', 5, 'Initial stock received - MacBook Pro 13', '2025-03-09 04:52:37'),
(90, 'SN090', 'FI002', 9, NULL, 'IN', 4, 'Initial stock received - PA-220', '2025-02-28 04:52:37'),
(91, 'SN091', 'LA003', 14, NULL, 'IN', 2, 'New shipment arrived - ThinkPad X1 Carbon', '2025-03-21 04:52:37'),
(92, 'SN092', 'SW002', 4, NULL, 'IN', 6, 'Stock adjustment after audit - HP ProCurve 5400', '2025-03-11 04:52:37'),
(93, 'SN093', 'SW003', 5, NULL, 'IN', 4, 'Initial stock received - Dell EMC Networking N1548', '2025-02-27 04:52:37'),
(94, 'SN094', 'UP002', 22, NULL, 'IN', 5, 'Manual entry for low stock - CP1500PFCLCD', '2025-02-28 04:52:37'),
(95, 'SN095', 'RO002', 2, NULL, 'IN', 6, 'Stock adjustment after audit - Cisco ISR 4000', '2025-02-28 04:52:37'),
(96, 'SN096', 'RO001', 1, NULL, 'IN', 3, 'Emergency purchase executed - MA5600T', '2025-03-23 04:52:37'),
(97, 'SN097', 'LA003', 14, NULL, 'IN', 2, 'New shipment arrived - ThinkPad X1 Carbon', '2025-03-01 04:52:37'),
(98, 'SN098', 'LA002', 13, NULL, 'IN', 3, 'Initial stock received - MacBook Pro 13', '2025-03-13 04:52:37'),
(99, 'SN099', 'LA002', 13, NULL, 'IN', 3, 'Discrepancy resolved - MacBook Pro 13', '2025-03-17 04:52:37'),
(100, 'SN100', 'ST002', 18, NULL, 'IN', 2, 'Routine restock completed - WD Blue 4TB', '2025-03-16 04:52:37'),
(101, 'SN101', 'SW002', 4, NULL, 'IN', 3, 'Stock adjustment after audit - HP ProCurve 5400', '2025-02-24 04:52:37'),
(102, 'SN102', 'FI002', 9, NULL, 'IN', 6, 'Delivered to branch office - PA-220', '2025-03-11 04:52:37'),
(103, 'SN103', 'LA003', 14, NULL, 'IN', 2, 'Initial stock received - ThinkPad X1 Carbon', '2025-03-24 04:52:37'),
(104, 'SN104', 'SW003', 5, NULL, 'IN', 7, 'Emergency purchase executed - Dell EMC Networking N1548', '2025-03-06 04:52:37'),
(105, 'SN105', 'ST001', 17, NULL, 'IN', 4, 'Emergency purchase executed - Barracuda 4TB', '2025-02-27 04:52:37'),
(106, 'SN106', 'RO001', 1, NULL, 'IN', 3, 'Routine restock completed - MA5600T', '2025-02-28 04:52:37'),
(107, 'SN107', 'AC001', 19, NULL, 'IN', 6, 'Delivered to branch office - UniFi AC Pro', '2025-03-02 04:52:37'),
(108, 'SN108', 'LA001', 12, NULL, 'IN', 2, 'Returned from maintenance - XPS 13', '2025-03-10 04:52:37'),
(109, 'SN109', 'SW001', 3, NULL, 'IN', 4, 'Manual entry for low stock - Cisco Catalyst 2960', '2025-03-05 04:52:37'),
(110, 'SN110', 'LA001', 12, NULL, 'IN', 5, 'Stock adjustment after audit - XPS 13', '2025-03-03 04:52:37'),
(111, 'SN111', 'LA001', 12, NULL, 'IN', 4, 'New shipment arrived - XPS 13', '2025-03-12 04:52:37'),
(112, 'SN112', 'FI002', 9, NULL, 'IN', 7, 'Stock adjustment after audit - PA-220', '2025-03-18 04:52:37'),
(113, 'SN113', 'AC001', 19, NULL, 'IN', 4, 'Stock adjustment after audit - UniFi AC Pro', '2025-03-25 04:52:37'),
(114, 'SN114', 'AC001', 19, NULL, 'IN', 7, 'Routine restock completed - UniFi AC Pro', '2025-02-26 04:52:37'),
(115, 'SN115', 'LA001', 12, NULL, 'IN', 4, 'Routine restock completed - XPS 13', '2025-03-25 04:52:37'),
(116, 'SN116', 'MO001', 6, NULL, 'IN', 4, 'Initial stock received - Nighthawk CM1200', '2025-03-13 04:52:37'),
(117, 'SN117', 'LA003', 14, NULL, 'IN', 7, 'Routine restock completed - ThinkPad X1 Carbon', '2025-02-24 04:52:37'),
(118, 'SN118', 'ST001', 17, NULL, 'IN', 3, 'Emergency purchase executed - Barracuda 4TB', '2025-03-03 04:52:37'),
(119, 'SN119', 'PR002', 16, NULL, 'IN', 3, 'Returned from maintenance - PIXMA TS9120', '2025-03-15 04:52:37'),
(120, 'SN120', 'AC001', 19, NULL, 'IN', 3, 'Replenishment confirmed - UniFi AC Pro', '2025-03-25 04:52:37'),
(121, 'SN121', 'MO001', 6, NULL, 'IN', 7, 'Replenishment confirmed - Nighthawk CM1200', '2025-03-23 04:52:37'),
(122, 'SN122', 'RO002', 2, NULL, 'IN', 5, 'Returned from maintenance - Cisco ISR 4000', '2025-03-01 04:52:37'),
(123, 'SN123', 'RO002', 2, NULL, 'IN', 5, 'Discrepancy resolved - Cisco ISR 4000', '2025-03-12 04:52:37'),
(124, 'SN124', 'SW002', 4, NULL, 'IN', 6, 'Initial stock received - HP ProCurve 5400', '2025-03-04 04:52:37'),
(125, 'SN125', 'PR001', 15, NULL, 'IN', 5, 'Discrepancy resolved - OfficeJet Pro 9015', '2025-02-27 04:52:37'),
(126, 'SN126', 'SW002', 4, NULL, 'IN', 5, 'Returned from maintenance - HP ProCurve 5400', '2025-03-02 04:52:37'),
(127, 'SN127', 'SW002', 4, NULL, 'IN', 4, 'Replenishment confirmed - HP ProCurve 5400', '2025-03-11 04:52:37'),
(128, 'SN128', 'RO002', 2, NULL, 'IN', 2, 'Discrepancy resolved - Cisco ISR 4000', '2025-03-21 04:52:37'),
(129, 'SN129', 'RO001', 1, NULL, 'IN', 4, 'Routine restock completed - MA5600T', '2025-02-25 04:52:37'),
(130, 'SN130', 'SW003', 5, NULL, 'IN', 2, 'Replenishment confirmed - Dell EMC Networking N1548', '2025-03-13 04:52:37'),
(131, 'SN131', 'AC001', 19, NULL, 'IN', 4, 'Emergency purchase executed - UniFi AC Pro', '2025-03-07 04:52:37'),
(132, 'SN132', 'MO002', 7, NULL, 'IN', 3, 'Manual entry for low stock - Surfboard SB8200', '2025-03-09 04:52:37'),
(133, 'SN133', 'LA002', 13, NULL, 'IN', 4, 'Stock adjustment after audit - MacBook Pro 13', '2025-03-01 04:52:37'),
(134, 'SN134', 'LA002', 13, NULL, 'IN', 2, 'Emergency purchase executed - MacBook Pro 13', '2025-03-04 04:52:37'),
(135, 'SN135', 'MO001', 6, NULL, 'IN', 7, 'Discrepancy resolved - Nighthawk CM1200', '2025-03-22 04:52:37'),
(136, 'SN136', 'LA003', 14, NULL, 'IN', 5, 'Replenishment confirmed - ThinkPad X1 Carbon', '2025-03-18 04:52:37'),
(137, 'SN137', 'SW003', 5, NULL, 'IN', 3, 'Routine restock completed - Dell EMC Networking N1548', '2025-02-25 04:52:37'),
(138, 'SN138', 'FI002', 9, NULL, 'IN', 7, 'Delivered to branch office - PA-220', '2025-03-21 04:52:37'),
(139, 'SN139', 'SW001', 3, NULL, 'IN', 6, 'Initial stock received - Cisco Catalyst 2960', '2025-03-01 04:52:37'),
(140, 'SN140', 'LA001', 12, NULL, 'IN', 3, 'Emergency purchase executed - XPS 13', '2025-03-25 04:52:37'),
(141, 'SN141', 'SW003', 5, NULL, 'IN', 7, 'Delivered to branch office - Dell EMC Networking N1548', '2025-03-13 04:52:37'),
(142, 'SN142', 'MO002', 7, NULL, 'IN', 2, 'Manual entry for low stock - Surfboard SB8200', '2025-03-25 04:52:37'),
(143, 'SN143', 'SW002', 4, NULL, 'IN', 6, 'Routine restock completed - HP ProCurve 5400', '2025-03-02 04:52:37'),
(144, 'SN144', 'LA001', 12, NULL, 'IN', 2, 'Stock adjustment after audit - XPS 13', '2025-03-02 04:52:37'),
(145, 'SN145', 'UP002', 22, NULL, 'IN', 6, 'Replenishment confirmed - CP1500PFCLCD', '2025-02-25 04:52:37'),
(146, 'SN146', 'UP001', 21, NULL, 'IN', 2, 'Discrepancy resolved - Back-UPS Pro 1500VA', '2025-03-20 04:52:37'),
(147, 'SN147', 'SW002', 4, NULL, 'IN', 5, 'Returned from maintenance - HP ProCurve 5400', '2025-03-20 04:52:37'),
(148, 'SN148', 'AC001', 19, NULL, 'IN', 4, 'Discrepancy resolved - UniFi AC Pro', '2025-03-08 04:52:37'),
(149, 'SN149', 'ST002', 18, NULL, 'IN', 4, 'Emergency purchase executed - WD Blue 4TB', '2025-02-27 04:52:37'),
(150, 'SN150', 'SW001', 3, NULL, 'IN', 4, 'Routine restock completed - Cisco Catalyst 2960', '2025-03-22 04:52:37'),
(151, 'SN151', 'SW003', 5, NULL, 'IN', 6, 'Replenishment confirmed - Dell EMC Networking N1548', '2025-03-17 04:52:37'),
(152, 'SN152', 'UP001', 21, NULL, 'IN', 3, 'Replenishment confirmed - Back-UPS Pro 1500VA', '2025-03-06 04:52:37'),
(153, 'SN153', 'PR001', 15, NULL, 'IN', 6, 'Delivered to branch office - OfficeJet Pro 9015', '2025-03-23 04:52:37'),
(154, 'SN154', 'LA002', 13, NULL, 'IN', 6, 'New shipment arrived - MacBook Pro 13', '2025-03-18 04:52:37'),
(155, 'SN155', 'SW002', 4, NULL, 'IN', 7, 'Replenishment confirmed - HP ProCurve 5400', '2025-03-14 04:52:37'),
(156, 'SN156', 'RO001', 1, NULL, 'IN', 5, 'Delivered to branch office - MA5600T', '2025-03-16 04:52:37'),
(157, 'SN157', 'LA003', 14, NULL, 'IN', 3, 'Delivered to branch office - ThinkPad X1 Carbon', '2025-03-14 04:52:37'),
(158, 'SN158', 'SW002', 4, NULL, 'IN', 2, 'Manual entry for low stock - HP ProCurve 5400', '2025-03-01 04:52:37'),
(159, 'SN159', 'SE001', 10, NULL, 'IN', 4, 'New shipment arrived - PowerEdge R740', '2025-03-03 04:52:37'),
(160, 'SN160', 'SW003', 5, NULL, 'IN', 7, 'Delivered to branch office - Dell EMC Networking N1548', '2025-02-25 04:52:37'),
(161, 'SN161', 'PR002', 16, NULL, 'IN', 6, 'Delivered to branch office - PIXMA TS9120', '2025-03-03 04:52:37'),
(162, 'SN162', 'SE001', 10, NULL, 'IN', 4, 'Routine restock completed - PowerEdge R740', '2025-03-20 04:52:37'),
(163, 'SN163', 'AC001', 19, NULL, 'IN', 3, 'Manual entry for low stock - UniFi AC Pro', '2025-03-19 04:52:37'),
(164, 'SN164', 'SW002', 4, NULL, 'IN', 2, 'Emergency purchase executed - HP ProCurve 5400', '2025-02-27 04:52:37'),
(165, 'SN165', 'LA002', 13, NULL, 'IN', 5, 'Manual entry for low stock - MacBook Pro 13', '2025-02-27 04:52:37'),
(166, 'SN166', 'PR001', 15, NULL, 'IN', 5, 'Stock adjustment after audit - OfficeJet Pro 9015', '2025-03-18 04:52:37'),
(167, 'SN167', 'AC002', 20, NULL, 'IN', 7, 'Stock adjustment after audit - Aironet 2800', '2025-03-20 04:52:37'),
(168, 'SN168', 'SE001', 10, NULL, 'IN', 6, 'Discrepancy resolved - PowerEdge R740', '2025-03-24 04:52:37'),
(169, 'SN169', 'LA003', 14, NULL, 'IN', 4, 'Returned from maintenance - ThinkPad X1 Carbon', '2025-02-27 04:52:37'),
(170, 'SN170', 'MO001', 6, NULL, 'IN', 2, 'Returned from maintenance - Nighthawk CM1200', '2025-03-22 04:52:37'),
(171, 'SN171', 'LA002', 13, NULL, 'IN', 5, 'Discrepancy resolved - MacBook Pro 13', '2025-02-27 04:52:37'),
(172, 'SN172', 'UP001', 21, NULL, 'IN', 4, 'New shipment arrived - Back-UPS Pro 1500VA', '2025-03-23 04:52:37'),
(173, 'SN173', 'MO002', 7, NULL, 'IN', 3, 'Returned from maintenance - Surfboard SB8200', '2025-03-21 04:52:37'),
(174, 'SN174', 'AC001', 19, NULL, 'IN', 7, 'Delivered to branch office - UniFi AC Pro', '2025-03-07 04:52:37'),
(175, 'SN175', 'PR002', 16, NULL, 'IN', 3, 'Emergency purchase executed - PIXMA TS9120', '2025-02-25 04:52:37'),
(176, 'SN176', 'MO001', 6, NULL, 'IN', 7, 'Stock adjustment after audit - Nighthawk CM1200', '2025-03-25 04:52:37'),
(177, 'SN177', 'AC001', 19, NULL, 'IN', 3, 'Stock adjustment after audit - UniFi AC Pro', '2025-03-08 04:52:37'),
(178, 'SN178', 'SW003', 5, NULL, 'IN', 3, 'Emergency purchase executed - Dell EMC Networking N1548', '2025-03-21 04:52:37'),
(179, 'SN179', 'LA003', 14, NULL, 'IN', 3, 'Discrepancy resolved - ThinkPad X1 Carbon', '2025-03-08 04:52:37'),
(180, 'SN180', 'AC001', 19, NULL, 'IN', 3, 'Discrepancy resolved - UniFi AC Pro', '2025-02-26 04:52:37'),
(181, 'SN181', 'FI001', 8, NULL, 'IN', 4, 'Stock adjustment after audit - FortiGate 60F', '2025-03-22 04:52:37'),
(182, 'SN182', 'MO001', 6, NULL, 'IN', 2, 'Initial stock received - Nighthawk CM1200', '2025-02-25 04:52:37'),
(183, 'SN183', 'UP001', 21, NULL, 'IN', 5, 'Initial stock received - Back-UPS Pro 1500VA', '2025-03-18 04:52:37'),
(184, 'SN184', 'RO002', 2, NULL, 'IN', 3, 'Replenishment confirmed - Cisco ISR 4000', '2025-03-05 04:52:37'),
(185, 'SN185', 'SE002', 11, NULL, 'IN', 5, 'New shipment arrived - ProLiant DL380 Gen10', '2025-03-20 04:52:37'),
(186, 'SN186', 'MO002', 7, NULL, 'IN', 6, 'Manual entry for low stock - Surfboard SB8200', '2025-03-19 04:52:37'),
(187, 'SN187', 'ST002', 18, NULL, 'IN', 6, 'New shipment arrived - WD Blue 4TB', '2025-03-24 04:52:37'),
(188, 'SN188', 'UP001', 21, NULL, 'IN', 2, 'Emergency purchase executed - Back-UPS Pro 1500VA', '2025-03-13 04:52:37'),
(189, 'SN189', 'SW003', 5, NULL, 'IN', 4, 'Emergency purchase executed - Dell EMC Networking N1548', '2025-03-22 04:52:37'),
(190, 'SN190', 'RO002', 2, NULL, 'IN', 5, 'Delivered to branch office - Cisco ISR 4000', '2025-03-17 04:52:37'),
(191, 'SN191', 'LA002', 13, NULL, 'IN', 2, 'Stock adjustment after audit - MacBook Pro 13', '2025-03-22 04:52:37'),
(192, 'SN192', 'UP002', 22, NULL, 'IN', 2, 'Emergency purchase executed - CP1500PFCLCD', '2025-03-12 04:52:37'),
(193, 'SN193', 'MO001', 6, NULL, 'IN', 5, 'Replenishment confirmed - Nighthawk CM1200', '2025-02-28 04:52:37'),
(194, 'SN194', 'MO002', 7, NULL, 'IN', 3, 'Routine restock completed - Surfboard SB8200', '2025-03-17 04:52:37'),
(195, 'SN195', 'SE002', 11, NULL, 'IN', 5, 'New shipment arrived - ProLiant DL380 Gen10', '2025-03-22 04:52:37'),
(196, 'SN196', 'ST002', 18, NULL, 'IN', 2, 'Emergency purchase executed - WD Blue 4TB', '2025-02-24 04:52:37'),
(197, 'SN197', 'ST002', 18, NULL, 'IN', 4, 'Initial stock received - WD Blue 4TB', '2025-03-20 04:52:37'),
(198, 'SN198', 'SW003', 5, NULL, 'IN', 6, 'New shipment arrived - Dell EMC Networking N1548', '2025-02-28 04:52:37'),
(199, 'SN199', 'RO002', 2, NULL, 'IN', 6, 'Replenishment confirmed - Cisco ISR 4000', '2025-03-06 04:52:37'),
(200, 'SN200', 'UP002', 22, NULL, 'IN', 7, 'Initial stock received - CP1500PFCLCD', '2025-03-16 04:52:37'),
(201, 'SN201', 'MO001', 6, NULL, 'IN', 5, 'New shipment arrived - Nighthawk CM1200', '2025-03-01 04:52:37'),
(202, 'SN202', 'ST001', 17, NULL, 'IN', 4, 'Initial stock received - Barracuda 4TB', '2025-03-19 04:52:37'),
(203, 'SN203', 'PR001', 15, NULL, 'IN', 3, 'Stock adjustment after audit - OfficeJet Pro 9015', '2025-03-22 04:52:37'),
(204, 'SN204', 'AC001', 19, NULL, 'IN', 2, 'Stock adjustment after audit - UniFi AC Pro', '2025-03-14 04:52:37'),
(205, 'SN205', 'AC002', 20, NULL, 'IN', 4, 'New shipment arrived - Aironet 2800', '2025-03-12 04:52:37'),
(206, 'SN206', 'FI001', 8, NULL, 'IN', 7, 'New shipment arrived - FortiGate 60F', '2025-03-14 04:52:37'),
(207, 'SN207', 'FI001', 8, NULL, 'IN', 4, 'Discrepancy resolved - FortiGate 60F', '2025-03-24 04:52:38'),
(208, 'SN208', 'SW001', 3, NULL, 'IN', 5, 'Returned from maintenance - Cisco Catalyst 2960', '2025-03-21 04:52:38'),
(209, 'SN209', 'SE001', 10, NULL, 'IN', 7, 'New shipment arrived - PowerEdge R740', '2025-02-28 04:52:38'),
(210, 'SN210', 'AC001', 19, NULL, 'IN', 4, 'Manual entry for low stock - UniFi AC Pro', '2025-03-02 04:52:38'),
(211, 'SN211', 'UP002', 22, NULL, 'IN', 5, 'Replenishment confirmed - CP1500PFCLCD', '2025-02-24 04:52:38'),
(212, 'SN212', 'PR002', 16, NULL, 'IN', 2, 'Routine restock completed - PIXMA TS9120', '2025-03-03 04:52:38'),
(213, 'SN213', 'LA001', 12, NULL, 'IN', 7, 'Emergency purchase executed - XPS 13', '2025-03-13 04:52:38'),
(214, 'SN214', 'AC001', 19, NULL, 'IN', 5, 'Emergency purchase executed - UniFi AC Pro', '2025-02-24 04:52:38'),
(215, 'SN215', 'FI002', 9, NULL, 'IN', 6, 'Emergency purchase executed - PA-220', '2025-03-15 04:52:38'),
(216, 'SN216', 'SE002', 11, NULL, 'IN', 4, 'Stock adjustment after audit - ProLiant DL380 Gen10', '2025-03-19 04:52:38'),
(217, 'SN217', 'FI001', 8, NULL, 'IN', 6, 'Stock adjustment after audit - FortiGate 60F', '2025-03-03 04:52:38'),
(218, 'SN218', 'AC001', 19, NULL, 'IN', 2, 'Stock adjustment after audit - UniFi AC Pro', '2025-02-28 04:52:38'),
(219, 'SN219', 'SE002', 11, NULL, 'IN', 4, 'Returned from maintenance - ProLiant DL380 Gen10', '2025-03-22 04:52:38'),
(220, 'SN220', 'ST002', 18, NULL, 'IN', 7, 'Routine restock completed - WD Blue 4TB', '2025-03-11 04:52:38'),
(221, 'SN221', 'LA003', 14, NULL, 'IN', 6, 'Initial stock received - ThinkPad X1 Carbon', '2025-03-22 04:52:38'),
(222, 'SN222', 'SW003', 5, NULL, 'IN', 4, 'Replenishment confirmed - Dell EMC Networking N1548', '2025-03-12 04:52:38'),
(223, 'SN223', 'SW002', 4, NULL, 'IN', 7, 'Routine restock completed - HP ProCurve 5400', '2025-03-07 04:52:38'),
(224, 'SN224', 'RO001', 1, NULL, 'IN', 4, 'New shipment arrived - MA5600T', '2025-03-21 04:52:38'),
(225, 'SN225', 'LA003', 14, NULL, 'IN', 4, 'Replenishment confirmed - ThinkPad X1 Carbon', '2025-03-17 04:52:38'),
(226, 'SN226', 'LA002', 13, NULL, 'IN', 5, 'Returned from maintenance - MacBook Pro 13', '2025-02-28 04:52:38'),
(227, 'SN227', 'SW002', 4, NULL, 'IN', 2, 'Emergency purchase executed - HP ProCurve 5400', '2025-03-22 04:52:38'),
(228, 'SN228', 'AC002', 20, NULL, 'IN', 4, 'New shipment arrived - Aironet 2800', '2025-03-22 04:52:38'),
(229, 'SN229', 'UP002', 22, NULL, 'IN', 4, 'Discrepancy resolved - CP1500PFCLCD', '2025-03-21 04:52:38'),
(230, 'SN230', 'SW003', 5, NULL, 'IN', 4, 'Manual entry for low stock - Dell EMC Networking N1548', '2025-03-07 04:52:38'),
(231, 'SN231', 'SE002', 11, NULL, 'IN', 7, 'Discrepancy resolved - ProLiant DL380 Gen10', '2025-03-05 04:52:38'),
(232, 'SN232', 'RO002', 2, NULL, 'IN', 7, 'Routine restock completed - Cisco ISR 4000', '2025-03-06 04:52:38'),
(233, 'SN233', 'LA002', 13, NULL, 'IN', 2, 'Initial stock received - MacBook Pro 13', '2025-03-20 04:52:38'),
(234, 'SN234', 'MO002', 7, NULL, 'IN', 3, 'New shipment arrived - Surfboard SB8200', '2025-03-12 04:52:38'),
(235, 'SN235', 'LA001', 12, NULL, 'IN', 6, 'Discrepancy resolved - XPS 13', '2025-03-20 04:52:38'),
(236, 'SN236', 'ST001', 17, NULL, 'IN', 7, 'Discrepancy resolved - Barracuda 4TB', '2025-03-25 04:52:38'),
(237, 'SN237', 'SW003', 5, NULL, 'IN', 6, 'Stock adjustment after audit - Dell EMC Networking N1548', '2025-02-26 04:52:38'),
(238, 'SN238', 'UP001', 21, NULL, 'IN', 6, 'Routine restock completed - Back-UPS Pro 1500VA', '2025-03-03 04:52:38'),
(239, 'SN239', 'MO001', 6, NULL, 'IN', 5, 'Delivered to branch office - Nighthawk CM1200', '2025-03-17 04:52:38'),
(240, 'SN240', 'SW003', 5, NULL, 'IN', 7, 'Routine restock completed - Dell EMC Networking N1548', '2025-03-01 04:52:38'),
(241, 'SN241', 'MO002', 7, NULL, 'IN', 6, 'Emergency purchase executed - Surfboard SB8200', '2025-02-24 04:52:38'),
(242, 'SN242', 'LA001', 12, NULL, 'IN', 6, 'Delivered to branch office - XPS 13', '2025-02-28 04:52:38'),
(243, 'SN243', 'MO002', 7, NULL, 'IN', 2, 'Replenishment confirmed - Surfboard SB8200', '2025-03-17 04:52:38'),
(244, 'SN244', 'LA003', 14, NULL, 'IN', 6, 'Replenishment confirmed - ThinkPad X1 Carbon', '2025-03-07 04:52:38'),
(245, 'SN245', 'SW001', 3, NULL, 'IN', 5, 'Manual entry for low stock - Cisco Catalyst 2960', '2025-03-07 04:52:38'),
(246, 'SN246', 'ST001', 17, NULL, 'IN', 3, 'Discrepancy resolved - Barracuda 4TB', '2025-03-19 04:52:38'),
(247, 'SN247', 'MO001', 6, NULL, 'IN', 5, 'Emergency purchase executed - Nighthawk CM1200', '2025-03-24 04:52:38'),
(248, 'SN248', 'SE001', 10, NULL, 'IN', 6, 'Delivered to branch office - PowerEdge R740', '2025-03-21 04:52:38'),
(249, 'SN249', 'SW002', 4, NULL, 'IN', 7, 'New shipment arrived - HP ProCurve 5400', '2025-03-22 04:52:38'),
(250, 'SN250', 'AC002', 20, NULL, 'IN', 2, 'Delivered to branch office - Aironet 2800', '2025-03-25 04:52:38'),
(251, 'SN251', 'MO001', 6, NULL, 'IN', 7, 'Discrepancy resolved - Nighthawk CM1200', '2025-03-11 04:52:38'),
(252, 'SN252', 'SW002', 4, NULL, 'IN', 2, 'Replenishment confirmed - HP ProCurve 5400', '2025-03-22 04:52:38'),
(253, 'SN253', 'MO001', 6, NULL, 'IN', 2, 'Delivered to branch office - Nighthawk CM1200', '2025-03-02 04:52:38'),
(254, 'SN254', 'FI002', 9, NULL, 'IN', 5, 'Routine restock completed - PA-220', '2025-03-05 04:52:38'),
(255, 'SN255', 'PR001', 15, NULL, 'IN', 3, 'New shipment arrived - OfficeJet Pro 9015', '2025-03-03 04:52:38'),
(256, 'SN256', 'UP001', 21, NULL, 'IN', 3, 'Stock adjustment after audit - Back-UPS Pro 1500VA', '2025-03-24 04:52:38'),
(257, 'SN257', 'ST001', 17, NULL, 'IN', 6, 'Delivered to branch office - Barracuda 4TB', '2025-03-13 04:52:38'),
(258, 'SN258', 'SW001', 3, NULL, 'IN', 4, 'Replenishment confirmed - Cisco Catalyst 2960', '2025-03-08 04:52:38'),
(259, 'SN259', 'SE002', 11, NULL, 'IN', 6, 'Stock adjustment after audit - ProLiant DL380 Gen10', '2025-03-21 04:52:38'),
(260, 'SN260', 'MO002', 7, NULL, 'IN', 2, 'Discrepancy resolved - Surfboard SB8200', '2025-03-06 04:52:38'),
(261, 'SN261', 'SE001', 10, NULL, 'IN', 2, 'New shipment arrived - PowerEdge R740', '2025-03-13 04:52:38'),
(262, 'SN262', 'PR002', 16, NULL, 'IN', 2, 'Discrepancy resolved - PIXMA TS9120', '2025-03-20 04:52:38'),
(263, 'SN263', 'FI001', 8, NULL, 'IN', 4, 'Stock adjustment after audit - FortiGate 60F', '2025-03-07 04:52:38'),
(264, 'SN264', 'PR002', 16, NULL, 'IN', 6, 'New shipment arrived - PIXMA TS9120', '2025-03-10 04:52:38'),
(265, 'SN265', 'SW002', 4, NULL, 'IN', 2, 'Discrepancy resolved - HP ProCurve 5400', '2025-03-01 04:52:38'),
(266, 'SN266', 'AC002', 20, NULL, 'IN', 2, 'Routine restock completed - Aironet 2800', '2025-02-27 04:52:38'),
(267, 'SN267', 'SE002', 11, NULL, 'IN', 7, 'Routine restock completed - ProLiant DL380 Gen10', '2025-03-10 04:52:38'),
(268, 'SN268', 'RO002', 2, NULL, 'IN', 7, 'Delivered to branch office - Cisco ISR 4000', '2025-02-24 04:52:38'),
(269, 'SN269', 'PR001', 15, NULL, 'IN', 5, 'Stock adjustment after audit - OfficeJet Pro 9015', '2025-02-28 04:52:38'),
(270, 'SN270', 'SW001', 3, NULL, 'IN', 6, 'Discrepancy resolved - Cisco Catalyst 2960', '2025-03-08 04:52:38'),
(271, 'SN271', 'FI002', 9, NULL, 'IN', 4, 'Replenishment confirmed - PA-220', '2025-03-03 04:52:38'),
(272, 'SN272', 'LA001', 12, NULL, 'IN', 6, 'New shipment arrived - XPS 13', '2025-03-20 04:52:38'),
(273, 'SN273', 'LA001', 12, NULL, 'IN', 7, 'Discrepancy resolved - XPS 13', '2025-03-25 04:52:38'),
(274, 'SN274', 'SW002', 4, NULL, 'IN', 4, 'Stock adjustment after audit - HP ProCurve 5400', '2025-03-19 04:52:38'),
(275, 'SN275', 'MO001', 6, NULL, 'IN', 5, 'Stock adjustment after audit - Nighthawk CM1200', '2025-03-12 04:52:38'),
(276, 'SN276', 'MO002', 7, NULL, 'IN', 4, 'Delivered to branch office - Surfboard SB8200', '2025-02-26 04:52:38'),
(277, 'SN277', 'PR002', 16, NULL, 'IN', 7, 'Stock adjustment after audit - PIXMA TS9120', '2025-03-13 04:52:38'),
(278, 'SN278', 'LA003', 14, NULL, 'IN', 2, 'Discrepancy resolved - ThinkPad X1 Carbon', '2025-03-10 04:52:38'),
(279, 'SN279', 'SW002', 4, NULL, 'IN', 7, 'Returned from maintenance - HP ProCurve 5400', '2025-02-28 04:52:38'),
(280, 'SN280', 'SE002', 11, NULL, 'IN', 6, 'New shipment arrived - ProLiant DL380 Gen10', '2025-03-11 04:52:38'),
(281, 'SN281', 'SW001', 3, NULL, 'IN', 5, 'Discrepancy resolved - Cisco Catalyst 2960', '2025-02-27 04:52:38'),
(282, 'SN282', 'ST001', 17, NULL, 'IN', 4, 'Initial stock received - Barracuda 4TB', '2025-03-05 04:52:38'),
(283, 'SN283', 'LA001', 12, NULL, 'IN', 6, 'Returned from maintenance - XPS 13', '2025-02-27 04:52:38'),
(284, 'SN284', 'SW001', 3, NULL, 'IN', 3, 'Initial stock received - Cisco Catalyst 2960', '2025-03-04 04:52:38'),
(285, 'SN285', 'SW002', 4, NULL, 'IN', 6, 'Emergency purchase executed - HP ProCurve 5400', '2025-03-20 04:52:38'),
(286, 'SN286', 'RO002', 2, NULL, 'IN', 4, 'Manual entry for low stock - Cisco ISR 4000', '2025-03-07 04:52:38'),
(287, 'SN287', 'MO001', 6, NULL, 'IN', 5, 'Manual entry for low stock - Nighthawk CM1200', '2025-03-09 04:52:38'),
(288, 'SN288', 'SE001', 10, NULL, 'IN', 6, 'Returned from maintenance - PowerEdge R740', '2025-03-02 04:52:38'),
(289, 'SN289', 'AC002', 20, NULL, 'IN', 5, 'Discrepancy resolved - Aironet 2800', '2025-03-24 04:52:38'),
(290, 'SN290', 'SW002', 4, NULL, 'IN', 6, 'Initial stock received - HP ProCurve 5400', '2025-03-23 04:52:38'),
(291, 'SN291', 'ST001', 17, NULL, 'IN', 7, 'Emergency purchase executed - Barracuda 4TB', '2025-03-03 04:52:38'),
(292, 'SN292', 'ST002', 18, NULL, 'IN', 5, 'Discrepancy resolved - WD Blue 4TB', '2025-03-14 04:52:38'),
(293, 'SN293', 'SE001', 10, NULL, 'IN', 4, 'Discrepancy resolved - PowerEdge R740', '2025-03-12 04:52:38'),
(294, 'SN294', 'FI001', 8, NULL, 'IN', 4, 'Manual entry for low stock - FortiGate 60F', '2025-03-20 04:52:38'),
(295, 'SN295', 'MO002', 7, NULL, 'IN', 3, 'Routine restock completed - Surfboard SB8200', '2025-03-23 04:52:38'),
(296, 'SN296', 'MO001', 6, NULL, 'IN', 2, 'Returned from maintenance - Nighthawk CM1200', '2025-03-02 04:52:38'),
(297, 'SN297', 'FI001', 8, NULL, 'IN', 2, 'New shipment arrived - FortiGate 60F', '2025-02-24 04:52:38'),
(298, 'SN298', 'SW001', 3, NULL, 'IN', 4, 'Returned from maintenance - Cisco Catalyst 2960', '2025-02-26 04:52:38'),
(299, 'SN299', 'FI001', 8, NULL, 'IN', 5, 'Stock adjustment after audit - FortiGate 60F', '2025-03-03 04:52:38'),
(300, 'SN300', 'SW002', 4, NULL, 'IN', 3, 'Returned from maintenance - HP ProCurve 5400', '2025-03-23 04:52:38'),
(301, 'SN301', 'UP002', 22, NULL, 'IN', 3, 'Replenishment confirmed - CP1500PFCLCD', '2025-03-25 04:52:38'),
(302, 'SN302', 'LA001', 12, NULL, 'IN', 2, 'Manual entry for low stock - XPS 13', '2025-02-25 04:52:38'),
(303, 'SN303', 'RO002', 2, NULL, 'IN', 4, 'Routine restock completed - Cisco ISR 4000', '2025-02-26 04:52:38'),
(304, 'SN304', 'LA001', 12, NULL, 'IN', 6, 'Returned from maintenance - XPS 13', '2025-02-25 04:52:38'),
(305, 'SN305', 'LA003', 14, NULL, 'IN', 5, 'Routine restock completed - ThinkPad X1 Carbon', '2025-03-22 04:52:38'),
(306, 'SN306', 'SW001', 3, NULL, 'IN', 2, 'Routine restock completed - Cisco Catalyst 2960', '2025-03-06 04:52:38'),
(307, 'SN307', 'LA003', 14, NULL, 'IN', 3, 'Discrepancy resolved - ThinkPad X1 Carbon', '2025-03-23 04:52:38'),
(308, 'SN308', 'SE002', 11, NULL, 'IN', 5, 'Manual entry for low stock - ProLiant DL380 Gen10', '2025-03-23 04:52:38'),
(309, 'SN309', 'AC001', 19, NULL, 'IN', 7, 'Delivered to branch office - UniFi AC Pro', '2025-03-13 04:52:38'),
(310, 'SN310', 'UP002', 22, NULL, 'IN', 4, 'Returned from maintenance - CP1500PFCLCD', '2025-03-03 04:52:38'),
(311, 'SN311', 'ST002', 18, NULL, 'IN', 6, 'Discrepancy resolved - WD Blue 4TB', '2025-03-08 04:52:38'),
(312, 'SN312', 'SW003', 5, NULL, 'IN', 7, 'New shipment arrived - Dell EMC Networking N1548', '2025-03-06 04:52:38'),
(313, 'SN313', 'SE002', 11, NULL, 'IN', 5, 'Stock adjustment after audit - ProLiant DL380 Gen10', '2025-03-10 04:52:38'),
(314, 'SN314', 'FI002', 9, NULL, 'IN', 6, 'Delivered to branch office - PA-220', '2025-03-21 04:52:38'),
(315, 'SN315', 'RO001', 1, NULL, 'IN', 6, 'Manual entry for low stock - MA5600T', '2025-03-02 04:52:38'),
(316, 'SN316', 'SW002', 4, NULL, 'IN', 3, 'Returned from maintenance - HP ProCurve 5400', '2025-02-28 04:52:38'),
(317, 'SN317', 'ST002', 18, NULL, 'IN', 3, 'New shipment arrived - WD Blue 4TB', '2025-03-08 04:52:38'),
(318, 'SN318', 'LA002', 13, NULL, 'IN', 5, 'Discrepancy resolved - MacBook Pro 13', '2025-03-18 04:52:38'),
(319, 'SN319', 'SW002', 4, NULL, 'IN', 2, 'Discrepancy resolved - HP ProCurve 5400', '2025-03-04 04:52:38'),
(320, 'SN320', 'ST001', 17, NULL, 'IN', 2, 'Delivered to branch office - Barracuda 4TB', '2025-03-17 04:52:38'),
(321, 'SN321', 'AC002', 20, NULL, 'IN', 4, 'Discrepancy resolved - Aironet 2800', '2025-03-04 04:52:38'),
(322, 'SN322', 'PR002', 16, NULL, 'IN', 4, 'Routine restock completed - PIXMA TS9120', '2025-02-25 04:52:38'),
(323, 'SN323', 'LA002', 13, NULL, 'IN', 4, 'Initial stock received - MacBook Pro 13', '2025-03-23 04:52:38'),
(324, 'SN324', 'SW001', 3, NULL, 'IN', 6, 'Delivered to branch office - Cisco Catalyst 2960', '2025-02-24 04:52:38'),
(325, 'SN325', 'SW002', 4, NULL, 'IN', 2, 'Manual entry for low stock - HP ProCurve 5400', '2025-03-09 04:52:38'),
(326, 'SN326', 'LA001', 12, NULL, 'IN', 7, 'Discrepancy resolved - XPS 13', '2025-03-03 04:52:38'),
(327, 'SN327', 'RO002', 2, NULL, 'IN', 4, 'Stock adjustment after audit - Cisco ISR 4000', '2025-03-03 04:52:38'),
(328, 'SN328', 'UP002', 22, NULL, 'IN', 5, 'Delivered to branch office - CP1500PFCLCD', '2025-03-22 04:52:38'),
(329, 'SN329', 'RO001', 1, NULL, 'IN', 5, 'New shipment arrived - MA5600T', '2025-03-13 04:52:38'),
(330, 'SN330', 'SW003', 5, NULL, 'IN', 4, 'New shipment arrived - Dell EMC Networking N1548', '2025-02-28 04:52:38'),
(331, 'SN331', 'FI001', 8, NULL, 'IN', 2, 'New shipment arrived - FortiGate 60F', '2025-03-23 04:52:38'),
(332, 'SN332', 'LA002', 13, NULL, 'IN', 4, 'Routine restock completed - MacBook Pro 13', '2025-03-11 04:52:38'),
(333, 'SN333', 'LA002', 13, NULL, 'IN', 6, 'Initial stock received - MacBook Pro 13', '2025-03-17 04:52:38'),
(334, 'SN334', 'PR002', 16, NULL, 'IN', 7, 'Replenishment confirmed - PIXMA TS9120', '2025-03-10 04:52:38'),
(335, 'SN335', 'FI001', 8, NULL, 'IN', 7, 'Initial stock received - FortiGate 60F', '2025-03-16 04:52:38'),
(336, 'SN336', 'UP002', 22, NULL, 'IN', 4, 'Routine restock completed - CP1500PFCLCD', '2025-03-16 04:52:38'),
(337, 'SN337', 'FI002', 9, NULL, 'IN', 6, 'New shipment arrived - PA-220', '2025-03-19 04:52:38'),
(338, 'SN338', 'LA002', 13, NULL, 'IN', 3, 'Delivered to branch office - MacBook Pro 13', '2025-03-14 04:52:38'),
(339, 'SN339', 'ST001', 17, NULL, 'IN', 4, 'Manual entry for low stock - Barracuda 4TB', '2025-03-09 04:52:38'),
(340, 'SN340', 'MO001', 6, NULL, 'IN', 2, 'Manual entry for low stock - Nighthawk CM1200', '2025-02-26 04:52:38'),
(341, 'SN341', 'PR002', 16, NULL, 'IN', 6, 'Returned from maintenance - PIXMA TS9120', '2025-03-12 04:52:38'),
(342, 'SN342', 'ST002', 18, NULL, 'IN', 7, 'Discrepancy resolved - WD Blue 4TB', '2025-03-09 04:52:38'),
(343, 'SN343', 'RO001', 1, NULL, 'IN', 5, 'Returned from maintenance - MA5600T', '2025-03-03 04:52:38'),
(344, 'SN344', 'MO001', 6, NULL, 'IN', 4, 'Discrepancy resolved - Nighthawk CM1200', '2025-03-03 04:52:38'),
(345, 'SN345', 'UP001', 21, NULL, 'IN', 3, 'Returned from maintenance - Back-UPS Pro 1500VA', '2025-03-15 04:52:38'),
(346, 'SN346', 'RO002', 2, NULL, 'IN', 3, 'Initial stock received - Cisco ISR 4000', '2025-03-11 04:52:38'),
(347, 'SN347', 'UP002', 22, NULL, 'IN', 5, 'New shipment arrived - CP1500PFCLCD', '2025-03-23 04:52:38'),
(348, 'SN348', 'FI002', 9, NULL, 'IN', 3, 'Delivered to branch office - PA-220', '2025-03-20 04:52:38'),
(349, 'SN349', 'MO002', 7, NULL, 'IN', 2, 'Manual entry for low stock - Surfboard SB8200', '2025-03-15 04:52:38'),
(350, 'SN350', 'SE002', 11, NULL, 'IN', 7, 'New shipment arrived - ProLiant DL380 Gen10', '2025-03-12 04:52:38'),
(351, 'SN351', 'MO001', 6, NULL, 'IN', 6, 'Returned from maintenance - Nighthawk CM1200', '2025-03-17 04:52:38'),
(352, 'SN352', 'MO002', 7, NULL, 'IN', 5, 'Delivered to branch office - Surfboard SB8200', '2025-02-28 04:52:38'),
(353, 'SN353', 'SE001', 10, NULL, 'IN', 5, 'Routine restock completed - PowerEdge R740', '2025-03-24 04:52:38'),
(354, 'SN354', 'ST001', 17, NULL, 'IN', 2, 'Initial stock received - Barracuda 4TB', '2025-03-24 04:52:38'),
(355, 'SN355', 'MO002', 7, NULL, 'IN', 5, 'Routine restock completed - Surfboard SB8200', '2025-03-06 04:52:38'),
(356, 'SN356', 'AC001', 19, NULL, 'IN', 7, 'New shipment arrived - UniFi AC Pro', '2025-03-19 04:52:38'),
(357, 'SN357', 'MO002', 7, NULL, 'IN', 6, 'Routine restock completed - Surfboard SB8200', '2025-03-18 04:52:38'),
(358, 'SN358', 'LA001', 12, NULL, 'IN', 6, 'Routine restock completed - XPS 13', '2025-02-27 04:52:38'),
(359, 'SN359', 'FI001', 8, NULL, 'IN', 4, 'Emergency purchase executed - FortiGate 60F', '2025-03-09 04:52:38'),
(360, 'SN360', 'SW003', 5, NULL, 'IN', 4, 'Stock adjustment after audit - Dell EMC Networking N1548', '2025-03-17 04:52:38'),
(361, 'SN361', 'FI001', 8, NULL, 'IN', 5, 'Replenishment confirmed - FortiGate 60F', '2025-03-15 04:52:38'),
(362, 'SN362', 'ST001', 17, NULL, 'IN', 5, 'Routine restock completed - Barracuda 4TB', '2025-03-01 04:52:38'),
(363, 'SN363', 'UP001', 21, NULL, 'IN', 7, 'Discrepancy resolved - Back-UPS Pro 1500VA', '2025-03-24 04:52:38'),
(364, 'SN364', 'LA003', 14, NULL, 'IN', 6, 'Stock adjustment after audit - ThinkPad X1 Carbon', '2025-03-21 04:52:38'),
(365, 'SN365', 'FI001', 8, NULL, 'IN', 4, 'Delivered to branch office - FortiGate 60F', '2025-02-27 04:52:38'),
(366, 'SN366', 'SW002', 4, NULL, 'IN', 4, 'Routine restock completed - HP ProCurve 5400', '2025-03-14 04:52:38'),
(367, 'SN367', 'LA003', 14, NULL, 'IN', 3, 'Manual entry for low stock - ThinkPad X1 Carbon', '2025-03-08 04:52:38'),
(368, 'SN368', 'FI001', 8, NULL, 'IN', 4, 'Routine restock completed - FortiGate 60F', '2025-03-15 04:52:38'),
(369, 'SN369', 'FI002', 9, NULL, 'IN', 4, 'Emergency purchase executed - PA-220', '2025-03-04 04:52:38'),
(370, 'SN370', 'AC002', 20, NULL, 'IN', 3, 'Delivered to branch office - Aironet 2800', '2025-03-10 04:52:38'),
(371, 'SN371', 'PR002', 16, NULL, 'IN', 2, 'Routine restock completed - PIXMA TS9120', '2025-03-18 04:52:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('Admin','Super Admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `division_id`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@example.com', 'superadmin', '$2y$10$FFUw/IOR314Qlnx9GGFsROgvYsnOF1ClRO84kk76Qx/KBi8KfZYlW', 1, 'Super Admin', '2025-03-26 04:52:36', '2025-03-26 04:52:36'),
(2, 'Admin Kuching', 'admin_kuching@example.com', 'admin_kuching', '$2y$10$GXneWOlxt18NQXZOJkYqFeUK3NSnPd.qYxh7OdbtZMktLsLM7ShS.', 1, 'Admin', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(3, 'Admin Miri', 'admin_miri@example.com', 'admin_miri', '$2y$10$SK901tjkLASXYeZ/xOnf8Ounp/hbmnDt4JZNHl8947lASKpDdz/WK', 2, 'Admin', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(4, 'Admin Samarahan', 'admin_samarahan@example.com', 'admin_samarahan', '$2y$10$c.iYooNph5vQNHOuW4FEoe/D0fFXmnfvrF2NpFWLnyj8SFWhU6Hny', 3, 'Admin', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(5, 'Admin Sri Aman', 'admin_sriaman@example.com', 'admin_sriaman', '$2y$10$CLuUfdJMOTmzvHTFv2.pvuG2MZMJXe9iYNUfzORVfewH5RzzRIzfq', 4, 'Admin', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(6, 'Admin Bintulu', 'admin_bintulu@example.com', 'admin_bintulu', '$2y$10$mJEKl/GJ0XaTKssP0.uu5OJBIhSAgRn9LdZ4v/MNCV3osipecdUJu', 5, 'Admin', '2025-03-26 04:52:37', '2025-03-26 04:52:37'),
(7, 'Admin Kapit', 'admin_kapit@example.com', 'admin_kapit', '$2y$10$DRsNVNLiCwZoT7yR.nyc5O3jmW3SlJ3lgIRNZAiZal4fmH22OvnLO', 6, 'Admin', '2025-03-26 04:52:37', '2025-03-26 04:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `user_product_thresholds`
--

CREATE TABLE `user_product_thresholds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `low_threshold` int(11) NOT NULL,
  `critical_threshold` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_product_thresholds`
--

INSERT INTO `user_product_thresholds` (`id`, `user_id`, `category`, `low_threshold`, `critical_threshold`, `created_at`, `updated_at`) VALUES
(1, 1, 'Router', 10, 3, '2025-03-26 04:52:50', '2025-03-26 04:52:50'),
(2, 1, 'Switch', 10, 3, '2025-03-26 04:52:50', '2025-03-26 04:52:50'),
(3, 1, 'Modem', 10, 3, '2025-03-26 04:52:50', '2025-03-26 04:52:50'),
(4, 1, 'Firewall', 10, 3, '2025-03-26 04:52:50', '2025-03-26 04:52:50'),
(5, 1, 'Server', 10, 3, '2025-03-26 04:52:50', '2025-03-26 04:52:50'),
(6, 1, 'Laptop', 10, 3, '2025-03-26 04:52:50', '2025-03-26 04:52:50'),
(7, 1, 'Printer', 10, 3, '2025-03-26 04:52:50', '2025-03-26 04:52:50'),
(8, 1, 'Storage', 10, 3, '2025-03-26 04:52:50', '2025-03-26 04:52:50'),
(9, 1, 'Access Point', 10, 3, '2025-03-26 04:52:50', '2025-03-26 04:52:50'),
(10, 1, 'UPS', 10, 3, '2025-03-26 04:52:50', '2025-03-26 04:52:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_product_code_unique` (`product_code`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stocks_serial_number_unique` (`serial_number`),
  ADD KEY `stocks_product_id_foreign` (`product_id`),
  ADD KEY `stocks_division_id_foreign` (`division_id`);

--
-- Indexes for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_transactions_product_id_foreign` (`product_id`),
  ADD KEY `stock_transactions_handled_by_foreign` (`handled_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD KEY `users_division_id_foreign` (`division_id`);

--
-- Indexes for table `user_product_thresholds`
--
ALTER TABLE `user_product_thresholds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_product_thresholds_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=372;

--
-- AUTO_INCREMENT for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=372;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_product_thresholds`
--
ALTER TABLE `user_product_thresholds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD CONSTRAINT `stock_transactions_handled_by_foreign` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_transactions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_product_thresholds`
--
ALTER TABLE `user_product_thresholds`
  ADD CONSTRAINT `user_product_thresholds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
