-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2025 at 04:13 AM
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
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Huawei', 1, '2025-02-21 07:32:50', '2025-02-21 07:32:50'),
(2, 'HPE', 1, '2025-02-21 07:32:50', '2025-02-21 07:32:50'),
(3, 'Cisco', 1, '2025-02-21 07:32:50', '2025-02-21 07:32:50'),
(4, 'Netgear', 1, '2025-02-21 07:32:50', '2025-02-21 07:32:50'),
(5, 'Arista', 1, '2025-02-21 07:32:50', '2025-03-07 03:03:17'),
(6, 'Juniper', 1, '2025-02-21 07:32:50', '2025-02-21 07:32:50'),
(7, 'HP', 1, '2025-02-21 07:32:50', '2025-02-21 07:32:50'),
(8, 'Brocade', 1, '2025-02-21 07:32:50', '2025-03-05 07:02:54'),
(9, 'Extreme', 1, '2025-02-21 07:32:50', '2025-02-21 07:32:50'),
(10, 'Dell', 1, '2025-02-21 07:32:50', '2025-02-21 07:32:50'),
(11, 'RV340', 1, '2025-02-21 07:32:50', '2025-02-21 07:32:50'),
(12, 'test', 1, '2025-02-21 07:32:50', '2025-03-06 04:33:19'),
(13, 'MA5600T', 1, '2025-02-21 07:32:50', '2025-02-21 07:32:50'),
(17, 'Lenovo', 0, '2025-02-21 07:42:07', '2025-03-07 07:33:39'),
(19, 'Asus', 1, '2025-02-28 03:29:50', '2025-02-28 03:29:50');

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
(1, 'Switch', 1, '2025-02-21 07:35:08', '2025-02-21 07:35:08'),
(2, 'Router', 1, '2025-02-21 07:35:08', '2025-02-21 07:35:08'),
(3, 'Modem', 1, '2025-02-21 07:35:08', '2025-03-07 07:13:12'),
(5, 'PDU', 1, '2025-02-21 07:55:19', '2025-02-21 08:12:12'),
(6, 'Laptop', 1, '2025-02-21 08:12:48', '2025-02-27 06:45:15');

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
(1, 'Kuching', '2025-02-17 07:39:23', '2025-02-17 07:39:23'),
(2, 'Miri', '2025-02-17 07:39:23', '2025-02-17 07:39:23'),
(3, 'Samarahan', '2025-02-17 07:39:23', '2025-02-17 07:39:23'),
(4, 'Sri Aman', '2025-02-17 07:39:23', '2025-02-17 07:39:23'),
(5, 'Bintulu', '2025-02-17 07:39:23', '2025-02-17 07:39:23'),
(6, 'Kapit', '2025-02-17 07:39:23', '2025-02-17 07:39:23');

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
(2, '2025_02_05_115448_create_division_table', 1),
(3, '2025_02_05_115500_create_users_table', 1),
(4, '2025_02_05_115617_create_stocks_table', 1),
(5, '2025_02_05_115726_create_stock_transactions_table', 1),
(6, '2025_02_06_100229_create_stock_updates_table', 2),
(7, '2025_02_07_160534_add_model_column_to_stock_transactions_table', 2),
(8, '2025_02_07_161325_rename_model_to_stock_model_in_stock_transactions_table', 3),
(9, '2025_02_10_094950_add_category_to_stock_transactions_table', 4),
(10, '2025_02_10_111432_add_status_to_stocks_table', 5),
(11, '2025_02_20_114429_create_brands_table', 6),
(12, '2025_02_20_114508_create_categories_table', 7),
(13, '2025_03_19_095349_create_products_table', 8);

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
(2, 'RO001', 'Router', 'Huawei', 'MA5600T', 'active', '2025-03-19 07:00:08', '2025-03-19 07:33:19');

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requisition_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`id`, `serial_number`, `model`, `category`, `brand`, `product_code`, `requisition_no`, `division_id`, `remarks`, `status`, `created_at`, `updated_at`) VALUES
(1, 'SN001', 'MA5600T', 'Switch', 'Huawei', NULL, NULL, 1, 'Ready for deployment', 'IN', '2025-02-17 08:12:36', '2025-02-20 04:17:18'),
(2, 'SN002', 'FlexNetwork 5130', 'Router', 'HPE', NULL, NULL, 5, 'Under routine maintenance', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(3, 'SN003', 'Catalyst 2960', 'Modem', 'Cisco', NULL, NULL, 1, 'Deployed in data center', 'OUT', '2025-02-17 08:12:36', '2025-02-20 08:27:13'),
(4, 'SN004', 'ProSAFE M4300', 'Switch', 'Netgear', NULL, NULL, 2, 'For backup operations', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(5, 'SN005', '7050X', 'Switch', 'Arista', NULL, NULL, 3, 'For backup operations', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(6, 'SN006', 'MX480', 'Switch', 'Juniper', NULL, NULL, 2, 'Deployed in data center', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(7, 'SN007', 'Catalyst 2960', 'Modem', 'Cisco', NULL, NULL, 6, 'Installed in main office', 'LOAN', '2025-02-17 08:12:36', '2025-02-21 00:57:19'),
(8, 'SN008', 'Catalyst 2960', 'Modem', 'Cisco', NULL, NULL, 3, 'Deployed in data center', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(9, 'SN009', 'MA5600T', 'Modem', 'Huawei', NULL, NULL, 5, 'Located in branch office', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(10, 'SN010', 'ProCurve 5400', 'Modem', 'HP', NULL, NULL, 1, 'Ready for deployment', 'OUT', '2025-02-17 08:12:36', '2025-02-20 08:43:15'),
(11, 'SN011', 'Catalyst 2960', 'Modem', 'Cisco', NULL, NULL, 3, 'Under routine maintenance', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(12, 'SN012', 'FlexNetwork 5130', 'Router', 'HPE', NULL, NULL, 2, 'Under routine maintenance', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(13, 'SN013', 'MX480', 'Switch', 'Juniper', NULL, NULL, 5, 'Located in branch office', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(14, 'SN014', 'ICX 6610', 'Switch', 'Brocade', NULL, NULL, 1, 'Deployed in data center', 'OUT', '2025-02-17 08:12:36', '2025-02-20 04:22:33'),
(15, 'SN015', 'Catalyst 2960', 'Switch', 'Cisco', NULL, NULL, 3, 'Installed in main office', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(16, 'SN016', 'Networks Summit X450-G2', 'Router', 'Extreme', NULL, NULL, 1, 'Installed in main office', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(17, 'SN017', 'FlexNetwork 5130', 'Modem', 'HPE', NULL, NULL, 6, 'For backup operations', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(18, 'SN018', 'ProSAFE M4300', 'Modem', 'Netgear', NULL, NULL, 2, 'Recently upgraded', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(19, 'SN019', 'MA5600T', 'Modem', 'Huawei', NULL, NULL, 1, 'Under routine maintenance', 'OUT', '2025-02-17 08:12:36', '2025-02-20 08:35:38'),
(20, 'SN020', 'ProSAFE M4300', 'Router', 'Netgear', NULL, NULL, 3, 'Ready for deployment', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(21, 'SN021', 'EMC Networking N1548', 'Modem', 'Dell', NULL, NULL, 2, 'Ready for deployment', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(22, 'SN022', 'ProSAFE M4300', 'Switch', 'Netgear', NULL, NULL, 2, 'Under routine maintenance', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(23, 'SN023', 'Networks Summit X450-G2', 'Switch', 'Extreme', NULL, NULL, 6, 'For backup operations', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(24, 'SN024', 'Networks Summit X450-G2', 'Switch', 'Extreme', NULL, NULL, 1, 'Deployed in data center', 'OUT', '2025-02-17 08:12:36', '2025-02-18 00:56:27'),
(25, 'SN025', 'MA5600T', 'Switch', 'Huawei', NULL, NULL, 3, 'Located in branch office', 'available', '2025-02-17 08:12:36', '2025-02-18 06:57:19'),
(26, 'SN026', 'MA5600T', 'Switch', 'Huawei', NULL, NULL, 1, 'Ready for deployment - haris', 'OUT', '2025-02-17 08:12:36', '2025-02-20 08:21:59'),
(27, 'SN027', 'EMC Networking N1548', 'Router', 'Dell', NULL, NULL, 4, 'Ready for deployment', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(28, 'SN028', 'MX480', 'Modem', 'Juniper', NULL, NULL, 1, 'Installed in main office', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(29, 'SN029', 'Catalyst 2960', 'Router', 'Cisco', NULL, NULL, 4, 'Installed in main office', 'available', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(30, 'SN030', 'MA5600T', 'Switch', 'Huawei', NULL, NULL, 5, 'Ready for deployment - haris', 'available', '2025-02-17 08:12:36', '2025-02-18 07:25:11'),
(31, 'SN123456783', 'RV340', 'Router', 'RV340', NULL, NULL, 1, 'test', 'OUT', '2025-02-18 03:34:04', '2025-02-18 07:43:33'),
(32, 'test', 'test', 'Router', 'test', NULL, NULL, 2, ',', 'available', '2025-02-18 04:00:11', '2025-02-18 04:00:11'),
(33, 'TEST123', 'MA5600T', 'Router', 'MA5600T', NULL, NULL, 1, 'TEST3', 'available', '2025-02-21 01:19:46', '2025-02-21 01:19:46'),
(34, 'test12345', 'MA5600T', 'Router', 'MA5600T', NULL, NULL, 1, 'test1234', 'available', '2025-02-21 01:34:47', '2025-02-21 01:34:47'),
(35, 'TEST6', 'MA5600T', 'Router', 'MA5600T', NULL, NULL, 1, 'TEST6', 'OUT', '2025-02-21 02:00:01', '2025-02-28 02:23:40'),
(36, 'TEST IN DATABASE', 'MA5600T', 'Router', 'MA5600T', NULL, NULL, 1, 'TEST IN DATABASE', 'OUT', '2025-02-21 02:24:32', '2025-02-24 08:58:23'),
(37, 'in', 'MA5600T', 'Router', 'MA5600T', NULL, NULL, 1, 'in', 'OUT', '2025-02-21 02:45:09', '2025-02-21 03:36:07'),
(38, '1234567', 'MA5600T', 'Router', 'MA5600T', NULL, NULL, 1, '12345678', 'OUT', '2025-02-21 02:45:42', '2025-02-21 03:35:57'),
(39, 'SNT765432', 'MA5600T', 'Router', 'MA5600T', NULL, NULL, 1, '1', 'OUT', '2025-02-21 03:46:43', '2025-02-24 02:26:46'),
(40, 'SN031', 'MA5600T', 'Router', 'MA5600T', NULL, NULL, 1, 'test return', 'OUT', '2025-02-21 07:03:00', '2025-02-28 03:15:46'),
(41, 'SN999', 'Legion', 'Laptop', NULL, NULL, NULL, 1, 'test return modal', 'available', '2025-02-21 08:23:49', '2025-02-28 03:36:08'),
(42, 'SN099', 'MA5600T', 'Router', 'Huawei', '001', '000', 1, 'test return', 'OUT', '2025-02-27 08:44:57', '2025-02-27 08:46:01'),
(43, 'SN777', 'Legion', 'Laptop', 'Lenovo', NULL, NULL, 1, 'test in modal', 'available', '2025-02-28 02:25:11', '2025-02-28 02:25:11'),
(44, 'SN6577', 'Asteroid Destroyer', 'PDU', 'Dell', NULL, NULL, 1, 'test in modal', 'OUT', '2025-02-28 02:42:50', '2025-02-28 02:49:40'),
(45, 'SN9043', 'TUF F15', 'Laptop', 'Asus', NULL, NULL, 1, 'test in modal', 'OUT', '2025-02-28 03:30:47', '2025-02-28 03:31:46'),
(46, 'SN1312423', 'MA5600T', 'Router', 'Huawei', '001', NULL, 2, 'in', 'available', '2025-02-28 07:10:01', '2025-02-28 07:10:01'),
(47, 'SN983421', 'TUF F15', 'Laptop', 'Asus', NULL, NULL, 2, 'in', 'OUT', '2025-02-28 07:10:24', '2025-02-28 07:58:11'),
(48, 'SN981231', 'TUF F15', 'Laptop', 'Asus', NULL, NULL, 2, 'test return', 'available', '2025-02-28 07:10:43', '2025-02-28 08:30:33'),
(49, 'SN981723', 'Legion', 'Laptop', 'Lenovo', NULL, NULL, 2, 'in', 'LOAN', '2025-02-28 07:11:00', '2025-02-28 07:25:19'),
(50, 'SN89011', 'Legion', 'Laptop', 'Dell', NULL, 'REQ-20250307-8763', 1, 'return test', 'available', '2025-03-07 07:15:25', '2025-03-12 02:21:57'),
(51, 'SN099765', 'MA5600T', 'Router', 'Huawei', '001', 'REQ-20250317-2222', 1, 'return test', 'LOAN', '2025-03-17 00:53:48', '2025-03-17 03:10:06'),
(53, 'SN00998765', 'MA5600T', 'Router', 'Huawei', '001', 'REQ-20250317-7228', 1, 'test', 'available', '2025-03-17 03:16:59', '2025-03-17 03:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transactions`
--

CREATE TABLE `stock_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requisition_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('IN','OUT','LOAN') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `handled_by` bigint(20) UNSIGNED NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_transactions`
--

INSERT INTO `stock_transactions` (`id`, `serial_number`, `category`, `brand`, `product_code`, `requisition_no`, `stock_model`, `type`, `handled_by`, `remarks`, `transaction_date`) VALUES
(1, 'SN001', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 2, 'Routine restock completed - Huawei MA5600T', '2025-02-06 08:12:38'),
(2, 'SN002', 'Router', NULL, NULL, NULL, 'HPE FlexNetwork 5130', 'IN', 6, 'Emergency purchase executed - HPE FlexNetwork 5130', '2025-01-30 08:12:38'),
(3, 'SN003', 'Modem', NULL, NULL, NULL, 'Cisco Catalyst 2960', 'IN', 2, 'New shipment arrived - Cisco Catalyst 2960', '2025-01-27 08:12:38'),
(4, 'SN004', 'Switch', NULL, NULL, NULL, 'Netgear ProSAFE M4300', 'IN', 3, 'Emergency purchase executed - Netgear ProSAFE M4300', '2025-01-23 08:12:38'),
(5, 'SN005', 'Switch', NULL, NULL, NULL, 'Arista 7050X', 'IN', 4, 'Routine restock completed - Arista 7050X', '2025-01-29 08:12:38'),
(6, 'SN006', 'Switch', NULL, NULL, NULL, 'Juniper MX480', 'IN', 3, 'Initial stock received - Juniper MX480', '2025-02-02 08:12:38'),
(7, 'SN007', 'Modem', NULL, NULL, NULL, 'Cisco Catalyst 2960', 'IN', 7, 'Initial stock received - Cisco Catalyst 2960', '2025-01-18 08:12:38'),
(8, 'SN008', 'Modem', NULL, NULL, NULL, 'Cisco Catalyst 2960', 'IN', 4, 'Delivered to branch office - Cisco Catalyst 2960', '2025-01-28 08:12:38'),
(9, 'SN009', 'Modem', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 6, 'Initial stock received - Huawei MA5600T', '2025-02-12 08:12:38'),
(10, 'SN010', 'Modem', NULL, NULL, NULL, 'HP ProCurve 5400', 'IN', 2, 'Initial stock received - HP ProCurve 5400', '2025-01-24 08:12:38'),
(11, 'SN011', 'Modem', NULL, NULL, NULL, 'Cisco Catalyst 2960', 'IN', 4, 'Delivered to branch office - Cisco Catalyst 2960', '2025-01-22 08:12:38'),
(12, 'SN012', 'Router', NULL, NULL, NULL, 'HPE FlexNetwork 5130', 'IN', 3, 'New shipment arrived - HPE FlexNetwork 5130', '2025-01-20 08:12:38'),
(13, 'SN013', 'Switch', NULL, NULL, NULL, 'Juniper MX480', 'IN', 6, 'Delivered to branch office - Juniper MX480', '2025-01-24 08:12:38'),
(14, 'SN014', 'Switch', NULL, NULL, NULL, 'Brocade ICX 6610', 'IN', 2, 'Delivered to branch office - Brocade ICX 6610', '2025-02-05 08:12:38'),
(15, 'SN015', 'Switch', NULL, NULL, NULL, 'Cisco Catalyst 2960', 'IN', 4, 'Routine restock completed - Cisco Catalyst 2960', '2025-01-19 08:12:38'),
(16, 'SN016', 'Router', NULL, NULL, NULL, 'Extreme Networks Summit X450-G2', 'IN', 2, 'Delivered to branch office - Extreme Networks Summit X450-G2', '2025-02-01 08:12:38'),
(17, 'SN017', 'Modem', NULL, NULL, NULL, 'HPE FlexNetwork 5130', 'IN', 7, 'New shipment arrived - HPE FlexNetwork 5130', '2025-02-02 08:12:38'),
(18, 'SN018', 'Modem', NULL, NULL, NULL, 'Netgear ProSAFE M4300', 'IN', 3, 'New shipment arrived - Netgear ProSAFE M4300', '2025-02-07 08:12:38'),
(19, 'SN019', 'Modem', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 2, 'Routine restock completed - Huawei MA5600T', '2025-01-24 08:12:38'),
(20, 'SN020', 'Router', NULL, NULL, NULL, 'Netgear ProSAFE M4300', 'IN', 4, 'New shipment arrived - Netgear ProSAFE M4300', '2025-01-27 08:12:38'),
(21, 'SN021', 'Modem', NULL, NULL, NULL, 'Dell EMC Networking N1548', 'IN', 3, 'Emergency purchase executed - Dell EMC Networking N1548', '2025-02-06 08:12:38'),
(22, 'SN022', 'Switch', NULL, NULL, NULL, 'Netgear ProSAFE M4300', 'IN', 3, 'Routine restock completed - Netgear ProSAFE M4300', '2025-02-10 08:12:38'),
(23, 'SN023', 'Switch', NULL, NULL, NULL, 'Extreme Networks Summit X450-G2', 'IN', 7, 'Emergency purchase executed - Extreme Networks Summit X450-G2', '2025-01-31 08:12:38'),
(24, 'SN024', 'Switch', NULL, NULL, NULL, 'Extreme Networks Summit X450-G2', 'IN', 2, 'New shipment arrived - Extreme Networks Summit X450-G2', '2025-01-28 08:12:38'),
(25, 'SN025', 'Router', NULL, NULL, NULL, 'HP ProCurve 5400', 'IN', 4, 'New shipment arrived - HP ProCurve 5400', '2025-02-12 08:12:38'),
(26, 'SN026', 'Modem', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 7, 'Routine restock completed - Huawei MA5600T', '2025-02-12 08:12:38'),
(27, 'SN027', 'Router', NULL, NULL, NULL, 'Dell EMC Networking N1548', 'IN', 5, 'New shipment arrived - Dell EMC Networking N1548', '2025-01-21 08:12:38'),
(28, 'SN028', 'Modem', NULL, NULL, NULL, 'Juniper MX480', 'IN', 2, 'New shipment arrived - Juniper MX480', '2025-01-20 08:12:38'),
(29, 'SN029', 'Router', NULL, NULL, NULL, 'Cisco Catalyst 2960', 'IN', 5, 'Initial stock received - Cisco Catalyst 2960', '2025-01-23 08:12:38'),
(30, 'SN030', 'Router', NULL, NULL, NULL, 'Extreme Networks Summit X450-G2', 'IN', 6, 'New shipment arrived - Extreme Networks Summit X450-G2', '2025-01-22 08:12:38'),
(31, 'SN024', 'Switch', NULL, NULL, NULL, 'Extreme Networks Summit X450-G2', 'OUT', 2, 'Deployed at government office', '2025-02-18 00:56:27'),
(32, 'SN123456783', 'Router', NULL, NULL, NULL, 'RV340', 'IN', 2, 'test', '2025-02-18 03:34:04'),
(33, 'test', 'Router', NULL, NULL, NULL, 'test', 'IN', 2, ',', '2025-02-18 04:00:11'),
(34, 'SN025', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 2, 'Located in branch office', '2025-02-18 06:57:19'),
(35, 'SN025', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 2, 'Located in branch office', '2025-02-18 07:05:18'),
(36, 'SN026', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 2, 'New Shipment Arrived', '2025-02-18 07:06:55'),
(37, 'SN026', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 2, 'Ready for deployment - haris', '2025-02-18 07:21:04'),
(38, 'SN030', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 2, 'Ready for deployment - haris', '2025-02-18 07:25:11'),
(39, 'SN026', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 2, 'Ready for deployment - haris', '2025-02-18 07:28:04'),
(40, 'SN123456783', 'Router', NULL, NULL, NULL, 'RV340', 'OUT', 2, 'test out operation', '2025-02-18 07:43:33'),
(41, 'SN026', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'LOAN', 2, 'Test Loan Function', '2025-02-20 02:16:54'),
(42, 'SN001', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'LOAN', 2, 'Test Loan Function2', '2025-02-20 02:52:30'),
(43, 'SN003', 'Modem', NULL, NULL, NULL, 'Cisco Catalyst 2960', 'LOAN', 2, 'Test Loan 3', '2025-02-20 03:11:44'),
(44, 'SN001', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 2, 'Test return', '2025-02-20 04:17:18'),
(45, 'SN014', 'Switch', NULL, NULL, NULL, 'Brocade ICX 6610', 'OUT', 2, 'test', '2025-02-20 04:22:33'),
(46, 'SN019', 'Modem', NULL, NULL, NULL, 'Huawei MA5600T', 'LOAN', 2, 'Test Loan 019 (Taken by: Sains City One)', '2025-02-20 04:31:47'),
(47, 'SN019', 'Modem', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 2, 'tEST RETURN (Returned to inventory)', '2025-02-20 04:33:00'),
(48, 'SN003', 'Modem', NULL, NULL, NULL, 'Cisco Catalyst 2960', 'IN', 2, 'TEST LOAN (Returned to inventory)', '2025-02-20 04:33:24'),
(49, 'SN026', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 2, 'Testtttttttttttttt (Returned to inventory)', '2025-02-20 07:37:24'),
(50, 'SN026', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'LOAN', 2, 'Test Loan 4 (Taken by: Sains PJ2)', '2025-02-20 07:50:15'),
(51, 'SN026', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'IN', 2, 'Return test 1 (Returned to inventory)', '2025-02-20 07:53:44'),
(52, 'SN026', 'Switch', NULL, NULL, NULL, 'Huawei MA5600T', 'OUT', 2, 'Test Out and Taken by function', '2025-02-20 08:21:59'),
(53, 'SN003', 'Modem', NULL, NULL, NULL, 'Cisco Catalyst 2960', 'OUT', 2, 'test again', '2025-02-20 08:27:13'),
(54, 'SN019', 'Modem', NULL, NULL, NULL, 'Huawei MA5600T', 'OUT', 2, 'tt', '2025-02-20 08:35:38'),
(55, 'SN010', 'Modem', NULL, NULL, NULL, 'HP ProCurve 5400', 'OUT', 2, 'sdcsdcsd (Taken by: Sains PJ2)', '2025-02-20 08:43:15'),
(56, 'SN007', 'Modem', NULL, NULL, NULL, 'Cisco Catalyst 2960', 'LOAN', 7, 'Test Loan By Kapit Division (Taken by: SAINS Kapit)', '2025-02-21 00:57:19'),
(61, 'in', 'Router', 'Huawei', '001', NULL, 'MA5600T', 'IN', 2, 'in', '2025-02-21 02:45:09'),
(62, '1234567', 'Router', 'Huawei', '001', NULL, 'MA5600T', 'IN', 2, '1234', '2025-02-21 02:45:42'),
(63, '1234567', 'Router', 'Huawei', '001', '1234567', 'MA5600T', 'IN', 2, '12345678', '2025-02-21 02:54:10'),
(66, 'SNT765432', 'Router', 'Huawei', '001', 'TEST IN DATABASE', 'MA5600T', 'IN', 2, '1', '2025-02-21 03:46:43'),
(67, 'SN031', 'Router', NULL, NULL, NULL, 'MA5600T', 'IN', 1, 'test', '2025-02-21 07:03:00'),
(68, 'SN031', 'Router', NULL, NULL, NULL, 'MA5600T', 'LOAN', 1, 'test (Taken by: pj1)', '2025-02-21 07:03:23'),
(69, 'SN031', 'Router', NULL, NULL, NULL, 'MA5600T', 'IN', 1, 'test return (Returned to inventory)', '2025-02-21 07:03:35'),
(70, 'SN999', 'Laptop', NULL, NULL, NULL, 'Legion', 'IN', 1, 'test brand and category', '2025-02-21 08:23:49'),
(71, 'SN999', 'Laptop', NULL, NULL, NULL, 'Legion', 'LOAN', 1, 'loaned to other office (Taken by: sebes)', '2025-02-24 02:20:27'),
(72, 'SN031', 'Router', NULL, NULL, NULL, 'MA5600T', 'LOAN', 1, 'loaned to other office (Taken by: risjon)', '2025-02-24 02:24:07'),
(73, 'SNT765432', 'Router', NULL, NULL, NULL, 'MA5600T', 'OUT', 1, 'test out (Taken by: sebes)', '2025-02-24 02:26:46'),
(74, 'TEST IN DATABASE', 'Router', 'MA5600T', NULL, NULL, 'MA5600T', 'OUT', 1, 'test out (Taken by: risjon)', '2025-02-24 08:58:23'),
(76, 'SN099', 'Router', 'Huawei', '001', '000', 'MA5600T', 'IN', 1, 'test remarks', '2025-02-27 08:44:57'),
(77, 'SN099', 'Router', 'Huawei', '001', '000', 'MA5600T', 'LOAN', 1, 'test loan (Taken by: risjon)', '2025-02-27 08:45:28'),
(78, 'SN099', 'Router', 'Huawei', '001', '000', 'MA5600T', 'IN', 1, 'test return (Returned to inventory)', '2025-02-27 08:45:48'),
(79, 'SN099', 'Router', 'Huawei', '001', '000', 'MA5600T', 'OUT', 1, 'test out (Taken by: risjon)', '2025-02-27 08:46:01'),
(80, 'SN031', 'Router', 'MA5600T', NULL, NULL, 'MA5600T', 'LOAN', 1, '123456789 (Taken by: risjon)', '2025-02-28 02:21:59'),
(81, 'TEST6', 'Router', 'MA5600T', NULL, NULL, 'MA5600T', 'OUT', 1, '1235 (Taken by: risjon)', '2025-02-28 02:23:40'),
(82, 'SN777', 'Laptop', 'Lenovo', NULL, NULL, 'Legion', 'IN', 1, 'test in modal', '2025-02-28 02:25:12'),
(83, 'SN031', 'Router', 'MA5600T', NULL, NULL, 'MA5600T', 'IN', 1, 'test return (Returned to inventory)', '2025-02-28 02:25:58'),
(84, 'SN6577', 'PDU', 'Dell', NULL, NULL, 'Asteroid Destroyer', 'IN', 1, 'test in modal', '2025-02-28 02:42:50'),
(85, 'SN6577', 'PDU', 'Dell', NULL, NULL, 'Asteroid Destroyer', 'OUT', 1, 'test (Taken by: test)', '2025-02-28 02:49:40'),
(86, 'SN031', 'Router', 'MA5600T', NULL, NULL, 'MA5600T', 'OUT', 1, 'test', '2025-02-28 03:15:46'),
(87, 'SN9043', 'Laptop', 'Asus', NULL, NULL, 'TUF F15', 'IN', 1, 'test in modal', '2025-02-28 03:30:47'),
(88, 'SN999', 'Laptop', NULL, NULL, NULL, 'Legion', 'IN', 1, 'test return (Returned to inventory)', '2025-02-28 03:31:29'),
(89, 'SN9043', 'Laptop', 'Asus', NULL, NULL, 'TUF F15', 'OUT', 1, 'test out modal (Taken by: risjon)', '2025-02-28 03:31:46'),
(90, 'SN999', 'Laptop', NULL, NULL, NULL, 'Legion', 'LOAN', 1, 'test loan modal (Taken by: risjon)', '2025-02-28 03:35:51'),
(91, 'SN999', 'Laptop', NULL, NULL, NULL, 'Legion', 'IN', 1, 'test return modal (Returned to inventory)', '2025-02-28 03:36:08'),
(92, 'SN1312423', 'Router', 'Huawei', '001', NULL, 'MA5600T', 'IN', 1, 'in', '2025-02-28 07:10:01'),
(93, 'SN983421', 'Laptop', 'Asus', NULL, NULL, 'TUF F15', 'IN', 1, 'in', '2025-02-28 07:10:24'),
(94, 'SN981231', 'Laptop', 'Asus', NULL, NULL, 'TUF F15', 'IN', 1, 'in', '2025-02-28 07:10:43'),
(95, 'SN981723', 'Laptop', 'Lenovo', NULL, NULL, 'Legion', 'IN', 1, 'in', '2025-02-28 07:11:00'),
(96, 'SN981723', 'Laptop', 'Lenovo', NULL, NULL, 'Legion', 'LOAN', 1, 'test (Taken by: test)', '2025-02-28 07:25:19'),
(97, 'SN981231', 'Laptop', 'Asus', NULL, NULL, 'TUF F15', 'LOAN', 1, 'test (Taken by: test)', '2025-02-28 07:56:48'),
(98, 'SN983421', 'Laptop', 'Asus', NULL, NULL, 'TUF F15', 'OUT', 1, 'test out (Taken by: risjon)', '2025-02-28 07:58:11'),
(99, 'SN981231', 'Laptop', 'Asus', NULL, NULL, 'TUF F15', 'IN', 1, 'test (Returned to inventory)', '2025-02-28 08:29:08'),
(100, 'SN981231', 'Laptop', 'Asus', NULL, NULL, 'TUF F15', 'LOAN', 1, 'test loan (Taken by: risjon)', '2025-02-28 08:30:12'),
(101, 'SN981231', 'Laptop', 'Asus', NULL, NULL, 'TUF F15', 'IN', 1, 'test return (Returned to inventory)', '2025-02-28 08:30:33'),
(102, 'SN89011', 'Laptop', 'Dell', NULL, 'REQ-20250307-8763', 'Legion', 'IN', 1, 'test', '2025-03-07 07:15:25'),
(103, 'SN89011', 'Laptop', 'Dell', NULL, 'REQ-20250307-8763', 'Legion', 'LOAN', 1, 'to pj2 (Taken by: sebes)', '2025-03-12 02:21:43'),
(104, 'SN89011', 'Laptop', 'Dell', NULL, 'REQ-20250307-8763', 'Legion', 'IN', 1, 'return test (Returned to inventory)', '2025-03-12 02:21:57'),
(105, 'SN099765', 'Router', 'Huawei', '001', 'REQ-20250317-2222', 'MA5600T', 'IN', 1, 'Stock IN', '2025-03-17 00:53:48'),
(106, 'SN099765', 'Router', 'Huawei', '001', 'REQ-20250317-2222', 'MA5600T', 'LOAN', 1, 'Loaned to MO3 (Taken by: Haris)', '2025-03-17 02:16:42'),
(107, 'SN099765', 'Router', 'Huawei', '001', 'REQ-20250317-2222', 'MA5600T', 'IN', 1, 'Return loaned from MO3 (Returned to inventory)', '2025-03-17 02:17:11'),
(108, 'SN099765', 'Router', 'Huawei', '001', 'REQ-20250317-2222', 'MA5600T', 'LOAN', 1, 'test 1 (Taken by: pj 1)', '2025-03-17 03:01:59'),
(109, 'SN099765', 'Router', 'Huawei', '001', 'REQ-20250317-2222', 'MA5600T', 'IN', 1, 'return test (Returned to inventory)', '2025-03-17 03:02:28'),
(110, 'SN099765', 'Router', 'Huawei', '001', 'REQ-20250317-2222', 'MA5600T', 'LOAN', 1, 'tt (Taken by: pj 1)', '2025-03-17 03:10:06'),
(111, 'SN00998765', 'Router', 'Huawei', '001', 'REQ-20250317-7228', 'MA5600T', 'IN', 1, 'test', '2025-03-17 03:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `stock_updates`
--

CREATE TABLE `stock_updates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_updates`
--

INSERT INTO `stock_updates` (`id`, `stock_id`, `type`, `created_at`, `updated_at`) VALUES
(122, 1, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(123, 2, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(124, 3, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(125, 4, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(126, 5, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(127, 6, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(128, 7, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(129, 8, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(130, 9, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(131, 10, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(132, 11, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(133, 12, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(134, 13, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(135, 14, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(136, 15, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(137, 16, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(138, 17, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(139, 18, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(140, 19, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(141, 20, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(142, 21, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(143, 22, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(144, 23, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(145, 24, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(146, 25, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(147, 26, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(148, 27, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(149, 28, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(150, 29, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(151, 30, 'created', '2025-02-17 08:12:36', '2025-02-17 08:12:36'),
(152, 24, 'updated', '2025-02-18 00:56:27', '2025-02-18 00:56:27'),
(153, 31, 'created', '2025-02-18 03:34:04', '2025-02-18 03:34:04'),
(154, 32, 'created', '2025-02-18 04:00:11', '2025-02-18 04:00:11'),
(155, 25, 'updated', '2025-02-18 06:57:19', '2025-02-18 06:57:19'),
(156, 26, 'updated', '2025-02-18 07:06:55', '2025-02-18 07:06:55'),
(157, 26, 'updated', '2025-02-18 07:21:04', '2025-02-18 07:21:04'),
(158, 30, 'updated', '2025-02-18 07:25:11', '2025-02-18 07:25:11'),
(159, 26, 'updated', '2025-02-18 07:28:04', '2025-02-18 07:28:04'),
(160, 31, 'updated', '2025-02-18 07:43:33', '2025-02-18 07:43:33'),
(161, 26, 'updated', '2025-02-20 02:16:54', '2025-02-20 02:16:54'),
(162, 1, 'updated', '2025-02-20 02:52:30', '2025-02-20 02:52:30'),
(163, 3, 'updated', '2025-02-20 03:11:44', '2025-02-20 03:11:44'),
(164, 1, 'updated', '2025-02-20 04:17:18', '2025-02-20 04:17:18'),
(165, 14, 'updated', '2025-02-20 04:22:33', '2025-02-20 04:22:33'),
(166, 19, 'updated', '2025-02-20 04:31:47', '2025-02-20 04:31:47'),
(167, 19, 'updated', '2025-02-20 04:33:00', '2025-02-20 04:33:00'),
(168, 3, 'updated', '2025-02-20 04:33:24', '2025-02-20 04:33:24'),
(169, 26, 'updated', '2025-02-20 07:37:24', '2025-02-20 07:37:24'),
(170, 26, 'updated', '2025-02-20 07:50:15', '2025-02-20 07:50:15'),
(171, 26, 'updated', '2025-02-20 07:53:44', '2025-02-20 07:53:44'),
(172, 26, 'updated', '2025-02-20 08:21:59', '2025-02-20 08:21:59'),
(173, 3, 'updated', '2025-02-20 08:27:13', '2025-02-20 08:27:13'),
(174, 19, 'updated', '2025-02-20 08:35:38', '2025-02-20 08:35:38'),
(175, 10, 'updated', '2025-02-20 08:43:15', '2025-02-20 08:43:15'),
(176, 7, 'updated', '2025-02-21 00:57:19', '2025-02-21 00:57:19'),
(177, 33, 'created', '2025-02-21 01:19:46', '2025-02-21 01:19:46'),
(178, 34, 'created', '2025-02-21 01:34:47', '2025-02-21 01:34:47'),
(179, 35, 'created', '2025-02-21 02:00:01', '2025-02-21 02:00:01'),
(180, 36, 'created', '2025-02-21 02:24:32', '2025-02-21 02:24:32'),
(181, 37, 'created', '2025-02-21 02:45:09', '2025-02-21 02:45:09'),
(182, 38, 'created', '2025-02-21 02:45:42', '2025-02-21 02:45:42'),
(183, 38, 'updated', '2025-02-21 02:54:10', '2025-02-21 02:54:10'),
(184, 38, 'updated', '2025-02-21 03:35:57', '2025-02-21 03:35:57'),
(185, 37, 'updated', '2025-02-21 03:36:07', '2025-02-21 03:36:07'),
(186, 39, 'created', '2025-02-21 03:46:43', '2025-02-21 03:46:43'),
(187, 40, 'created', '2025-02-21 07:03:00', '2025-02-21 07:03:00'),
(188, 40, 'updated', '2025-02-21 07:03:23', '2025-02-21 07:03:23'),
(189, 40, 'updated', '2025-02-21 07:03:35', '2025-02-21 07:03:35'),
(190, 41, 'created', '2025-02-21 08:23:49', '2025-02-21 08:23:49'),
(191, 41, 'updated', '2025-02-24 02:20:27', '2025-02-24 02:20:27'),
(192, 40, 'updated', '2025-02-24 02:24:07', '2025-02-24 02:24:07'),
(193, 39, 'updated', '2025-02-24 02:26:46', '2025-02-24 02:26:46'),
(194, 36, 'updated', '2025-02-24 08:58:23', '2025-02-24 08:58:23'),
(195, 40, 'updated', '2025-02-27 04:23:41', '2025-02-27 04:23:41'),
(196, 42, 'created', '2025-02-27 08:44:57', '2025-02-27 08:44:57'),
(197, 42, 'updated', '2025-02-27 08:45:28', '2025-02-27 08:45:28'),
(198, 42, 'updated', '2025-02-27 08:45:48', '2025-02-27 08:45:48'),
(199, 42, 'updated', '2025-02-27 08:46:01', '2025-02-27 08:46:01'),
(200, 40, 'updated', '2025-02-28 02:21:59', '2025-02-28 02:21:59'),
(201, 35, 'updated', '2025-02-28 02:23:40', '2025-02-28 02:23:40'),
(202, 43, 'created', '2025-02-28 02:25:12', '2025-02-28 02:25:12'),
(203, 40, 'updated', '2025-02-28 02:25:58', '2025-02-28 02:25:58'),
(204, 44, 'created', '2025-02-28 02:42:50', '2025-02-28 02:42:50'),
(205, 44, 'updated', '2025-02-28 02:49:40', '2025-02-28 02:49:40'),
(206, 40, 'updated', '2025-02-28 03:15:46', '2025-02-28 03:15:46'),
(207, 45, 'created', '2025-02-28 03:30:47', '2025-02-28 03:30:47'),
(208, 41, 'updated', '2025-02-28 03:31:29', '2025-02-28 03:31:29'),
(209, 45, 'updated', '2025-02-28 03:31:46', '2025-02-28 03:31:46'),
(210, 41, 'updated', '2025-02-28 03:35:51', '2025-02-28 03:35:51'),
(211, 41, 'updated', '2025-02-28 03:36:08', '2025-02-28 03:36:08'),
(212, 46, 'created', '2025-02-28 07:10:01', '2025-02-28 07:10:01'),
(213, 47, 'created', '2025-02-28 07:10:24', '2025-02-28 07:10:24'),
(214, 48, 'created', '2025-02-28 07:10:43', '2025-02-28 07:10:43'),
(215, 49, 'created', '2025-02-28 07:11:00', '2025-02-28 07:11:00'),
(216, 49, 'updated', '2025-02-28 07:25:19', '2025-02-28 07:25:19'),
(217, 48, 'updated', '2025-02-28 07:56:48', '2025-02-28 07:56:48'),
(218, 47, 'updated', '2025-02-28 07:58:11', '2025-02-28 07:58:11'),
(219, 48, 'updated', '2025-02-28 08:29:08', '2025-02-28 08:29:08'),
(220, 48, 'updated', '2025-02-28 08:30:12', '2025-02-28 08:30:12'),
(221, 48, 'updated', '2025-02-28 08:30:33', '2025-02-28 08:30:33'),
(222, 50, 'created', '2025-03-07 07:15:25', '2025-03-07 07:15:25'),
(223, 50, 'updated', '2025-03-12 02:21:43', '2025-03-12 02:21:43'),
(224, 50, 'updated', '2025-03-12 02:21:57', '2025-03-12 02:21:57'),
(225, 51, 'created', '2025-03-17 00:53:48', '2025-03-17 00:53:48'),
(226, 51, 'updated', '2025-03-17 02:16:42', '2025-03-17 02:16:42'),
(227, 51, 'updated', '2025-03-17 02:17:11', '2025-03-17 02:17:11'),
(228, 51, 'updated', '2025-03-17 03:01:59', '2025-03-17 03:01:59'),
(229, 51, 'updated', '2025-03-17 03:02:28', '2025-03-17 03:02:28'),
(230, 51, 'updated', '2025-03-17 03:10:06', '2025-03-17 03:10:06'),
(231, 53, 'created', '2025-03-17 03:16:59', '2025-03-17 03:16:59');

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
  `role` enum('Admin','Super Admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Admin',
  `division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `role`, `division_id`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@example.com', 'superadmin', '$2y$10$ACD6oAgPN2tT1Cr33Gwmked8lWpVa7Rxm6BpD2QMQwEqlxBnEb66e', 'Super Admin', 1, '2025-02-17 07:39:23', '2025-02-27 04:21:20'),
(2, 'Admin Kuching', 'admin_kuching@example.com', 'admin_kuching', '$2y$10$0qia83pGquoP0uIEXrMIzOiqta.6pwxil3xLTcNrVWXFmNlBMyVF6', 'Admin', 1, '2025-02-17 07:39:23', '2025-02-17 07:39:23'),
(3, 'Admin Miri', 'admin_miri@example.com', 'admin_miri', '$2y$10$Fx4vyoFtGTx4bdeuEljUaO.r5dXhaIQfz0si5zV6R.hx992Qjg6Ne', 'Admin', 2, '2025-02-17 07:39:23', '2025-02-17 07:39:23'),
(4, 'Admin Samarahan', 'admin_samarahan@example.com', 'admin_samarahan', '$2y$10$9CjQgOiKFIZ2yjzeqooruuJW0jI5prb9gX4sRcPkBvkpKDV0sKi5S', 'Admin', 3, '2025-02-17 07:39:23', '2025-02-17 07:39:23'),
(5, 'Admin Sri Aman', 'admin_sriaman@example.com', 'admin_sriaman', '$2y$10$UAPm3K8arko9/tewb5LYZuDa2HKg10rbHa81MPY5dib8sEVtiMI1K', 'Admin', 4, '2025-02-17 07:39:23', '2025-02-17 07:39:23'),
(6, 'Admin Bintulu', 'admin_bintulu@example.com', 'admin_bintulu', '$2y$10$Uaz.hKOGsBdU0/rRaLl4..Tiaw.TkR7IVB8RfoIa0pVmcr6NnH/li', 'Admin', 2, '2025-02-17 07:39:23', '2025-03-17 03:43:45'),
(7, 'Admin Kapit', 'admin_kapit@example.com', 'admin_kapit', '$2y$10$aOsvRs3CMc8XOAZbV/d3aOZk0SQWXx8l0c/jaytIkGzYNvJdoOjbm', 'Admin', 6, '2025-02-17 07:39:23', '2025-02-17 07:39:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_name_unique` (`name`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`);

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `divisions_name_unique` (`name`);

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
  ADD KEY `stocks_division_id_foreign` (`division_id`);

--
-- Indexes for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_transactions_serial_number_foreign` (`serial_number`),
  ADD KEY `stock_transactions_handled_by_foreign` (`handled_by`);

--
-- Indexes for table `stock_updates`
--
ALTER TABLE `stock_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_updates_stock_id_foreign` (`stock_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_division_id_foreign` (`division_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `stock_updates`
--
ALTER TABLE `stock_updates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD CONSTRAINT `stock_transactions_handled_by_foreign` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_transactions_serial_number_foreign` FOREIGN KEY (`serial_number`) REFERENCES `stocks` (`serial_number`) ON DELETE CASCADE;

--
-- Constraints for table `stock_updates`
--
ALTER TABLE `stock_updates`
  ADD CONSTRAINT `stock_updates_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
