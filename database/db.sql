-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Oct 09, 2025 at 07:17 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dental_clinic`
--

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `document_type` enum('prescription','partner_share','backup','invoice','expense') NOT NULL,
  `expense_type` enum('fixed','variable','one_time') DEFAULT NULL,
  `expense_category` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `recurrence` enum('monthly','quarterly','yearly','one_time') DEFAULT NULL,
  `next_due_date` date DEFAULT NULL,
  `document_code` varchar(20) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `content` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `partner_name` varchar(100) DEFAULT NULL,
  `partner_phone` varchar(20) DEFAULT NULL,
  `partner_email` varchar(100) DEFAULT NULL,
  `share_percentage` decimal(5,2) DEFAULT NULL,
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `share_amount` decimal(10,2) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `diagnosis` text DEFAULT NULL,
  `medicine_instructions` text DEFAULT NULL,
  `status` enum('active','inactive','completed','cancelled') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `document_type`, `expense_type`, `expense_category`, `amount`, `recurrence`, `next_due_date`, `document_code`, `patient_id`, `service_id`, `title`, `content`, `file_path`, `file_size`, `partner_name`, `partner_phone`, `partner_email`, `share_percentage`, `period_start`, `period_end`, `share_amount`, `paid_amount`, `diagnosis`, `medicine_instructions`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'partner_share', NULL, NULL, NULL, NULL, NULL, 'PT000001', NULL, NULL, 'Dr. Mehdi Karimi', NULL, NULL, NULL, 'Dr. Mehdi Karimi', '09121112233', 'mehdi@email.com', 30.00, '2024-01-01', NULL, NULL, 0.00, NULL, NULL, 'active', 1, '2025-10-03 07:33:55', '2025-10-03 07:33:55'),
(2, 'partner_share', NULL, NULL, NULL, NULL, NULL, 'PT000002', NULL, NULL, 'Dr. Zahra Hosseini', NULL, NULL, NULL, 'Dr. Zahra Hosseini', '09121112234', 'zahra.h@email.com', 20.00, '2024-01-01', NULL, NULL, 0.00, NULL, NULL, 'active', 1, '2025-10-03 07:33:55', '2025-10-03 07:33:55'),
(3, 'backup', NULL, NULL, NULL, NULL, NULL, 'BK109338', NULL, NULL, 'backup_2025-10-03_19-57-38.sql', NULL, 'C:\\xampp\\htdocs\\Teeth\\teeth/backups/backup_2025-10-03_19-57-38.sql', 31842, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'active', 1, '2025-10-03 09:27:38', '2025-10-03 09:27:38'),
(4, 'partner_share', NULL, NULL, NULL, NULL, NULL, 'PT860467', NULL, NULL, 'Mir Naiem', NULL, NULL, NULL, 'Mir Naiem', '0799900990', 'rkhatibi2003@gmail.com', 15.00, '2025-10-04', '2028-01-04', NULL, 0.00, NULL, NULL, 'active', 1, '2025-10-04 03:47:23', '2025-10-04 03:47:23'),
(5, 'backup', NULL, NULL, NULL, NULL, NULL, 'BK821741', NULL, NULL, 'backup_2025-10-04_14-19-16.sql', NULL, 'C:\\xampp\\htdocs\\Teeth\\teeth/backups/backup_2025-10-04_14-19-16.sql', 37110, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'active', 1, '2025-10-04 03:49:16', '2025-10-04 03:49:16'),
(6, 'expense', 'variable', 'گاز', 200.00, 'one_time', '2025-10-04', 'EXP-20251004-2751', NULL, NULL, 'ندارد', 'ندارد', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'active', 1, '2025-10-04 05:52:52', '2025-10-04 05:52:52'),
(7, 'expense', 'fixed', 'تعمیرات', 2222.00, 'one_time', '2025-10-06', 'EXP-20251006-1819', NULL, NULL, '222', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'active', 1, '2025-10-06 22:18:19', '2025-10-06 22:18:19');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `medicine_code` varchar(20) NOT NULL,
  `medicine_name` varchar(100) NOT NULL,
  `medicine_name_en` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `unit` varchar(20) NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 10,
  `expiry_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `supplier_phone` varchar(20) DEFAULT NULL,
  `supplier_email` varchar(100) DEFAULT NULL,
  `supplier_address` text DEFAULT NULL,
  `sale_patient_id` int(11) DEFAULT NULL,
  `sale_code` varchar(20) DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `sale_quantity` int(11) DEFAULT NULL,
  `sale_unit_price` decimal(10,2) DEFAULT NULL,
  `sale_total_price` decimal(10,2) DEFAULT NULL,
  `movement_type` enum('purchase','sale','adjustment') DEFAULT NULL,
  `movement_date` date DEFAULT NULL,
  `movement_quantity` int(11) DEFAULT NULL,
  `movement_notes` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `medicine_code`, `medicine_name`, `medicine_name_en`, `category`, `manufacturer`, `unit`, `purchase_price`, `sale_price`, `stock_quantity`, `min_stock_level`, `expiry_date`, `description`, `supplier_name`, `supplier_phone`, `supplier_email`, `supplier_address`, `sale_patient_id`, `sale_code`, `sale_date`, `sale_quantity`, `sale_unit_price`, `sale_total_price`, `movement_type`, `movement_date`, `movement_quantity`, `movement_notes`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'M849115', 'ییی', 'yy', 'دیدی', 'ww', 'Tube', 2000.00, 3000.00, 10, 10, '2025-10-31', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-10-03 17:20:24', '2025-10-03 17:20:24'),
(2, 'M000001', 'آموکسی سیلین 500', 'Amoxicillin 500mg', 'آنتی بیوتیک', 'داروسازی ابوریحان', 'Box', 150000.00, 200000.00, 50, 10, '2025-12-31', NULL, 'شرکت پخش دارو', '02188776655', 'supplier1@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(3, 'M000002', 'ایبوپروفن 400', 'Ibuprofen 400mg', 'مسکن', 'داروسازی سبحان', 'Box', 80000.00, 120000.00, 100, 20, '2025-10-31', NULL, 'شرکت پخش دارو', '02188776655', 'supplier1@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(4, 'M000003', 'متروندازول 250', 'Metronidazole 250mg', 'آنتی بیوتیک', 'داروسازی جابر', 'Box', 100000.00, 150000.00, 30, 10, '2025-11-30', NULL, 'شرکت پخش دارو', '02188776655', 'supplier1@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(5, 'M000004', 'دهانشویه کلرهگزیدین', 'Chlorhexidine Mouthwash', 'بهداشتی', 'شرکت بهداشتی پارس', 'Bottle', 50000.00, 80000.00, 80, 15, '2026-06-30', NULL, 'توزیع کالای بهداشتی', '02188776656', 'supplier2@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(6, 'M000005', 'ژل فلوراید', 'Fluoride Gel', 'بهداشتی', 'شرکت دندانپزشکی ایران', 'Tube', 120000.00, 180000.00, 40, 10, '2026-03-31', NULL, 'توزیع کالای بهداشتی', '02188776656', 'supplier2@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(7, 'M000006', 'سفیکسیم 400', 'Cefixime 400mg', 'آنتی بیوتیک', 'داروسازی فارابی', 'Box', 200000.00, 280000.00, 25, 10, '2025-09-30', NULL, 'شرکت پخش دارو', '02188776655', 'supplier1@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(8, 'M000007', 'ژل بی حسی موضعی', 'Topical Anesthetic Gel', 'بی حس کننده', 'شرکت دندانپزشکی ایران', 'Tube', 150000.00, 220000.00, 60, 15, '2026-01-31', NULL, 'توزیع کالای بهداشتی', '02188776656', 'supplier2@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(9, 'M000008', 'آسپرین 80', 'Aspirin 80mg', 'مسکن', 'داروسازی رازی', 'Box', 30000.00, 50000.00, 5, 10, '2025-08-31', NULL, 'شرکت پخش دارو', '02188776655', 'supplier1@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(10, 'M468746', 'متادول', 'Metadol', 'ندارد', 'www', 'Box', 1000.00, 3000.00, 20, 10, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-10-04 03:46:18', '2025-10-04 03:46:18'),
(11, 'SUP-842810', 'Supplier: شرکت رنجبر', NULL, NULL, NULL, 'N/A', 0.00, 0.00, 0, 10, NULL, NULL, 'شرکت رنجبر', '0799899789', 'rkhatibi2003@gmail.com', 'کراچی پاکستان', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, '2025-10-04 04:14:56', '2025-10-04 04:14:56'),
(12, 'SUP-232510', 'Supplier: شرکت رنجبر2', NULL, NULL, NULL, 'N/A', 0.00, 0.00, 0, 10, NULL, NULL, 'شرکت رنجبر2', '089999922', 'rkhatibi2003@gmail.com', '22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, '2025-10-06 22:17:12', '2025-10-06 22:17:12'),
(13, 'SUP-013002', 'Supplier: شرکت رنجبر212', NULL, NULL, NULL, 'N/A', 0.00, 0.00, 0, 10, NULL, NULL, 'شرکت رنجبر212', '079099787', 'rkhatibi2003@gmail.com', 'هرات', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, '2025-10-08 13:10:25', '2025-10-08 13:10:25');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `patient_code` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_code`, `first_name`, `last_name`, `age`, `gender`, `phone`, `email`, `address`, `medical_history`, `allergies`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'P000002', 'فاطمه', 'رضایی', 28, 'female', '09131234568', 'fatemeh@email.com', 'تهران، خیابان انقلاب', NULL, NULL, NULL, 1, '2025-10-03 07:32:31', '2025-10-03 07:32:31'),
(3, 'P000003', 'علی', 'محمدی', 42, 'male', '09131234569', NULL, 'تهران، خیابان آزادی', 'فشار خون بالا', NULL, NULL, 1, '2025-10-03 07:32:31', '2025-10-03 07:32:31'),
(4, 'P000004', 'زهرا', 'کریمی', 25, 'female', '09131234570', 'zahra@email.com', 'تهران، خیابان شریعتی', NULL, 'آسپرین', NULL, 1, '2025-10-03 07:32:31', '2025-10-03 07:32:31'),
(5, 'P000005', 'حسین', 'حسینی', 50, 'male', '09131234571', NULL, 'تهران، خیابان کریمخان', 'بیماری قلبی', NULL, NULL, 1, '2025-10-03 07:32:31', '2025-10-03 07:32:31'),
(6, 'P000006', 'مریم', 'نوری', 33, 'female', '09131234572', 'maryam@email.com', 'تهران، خیابان سعادت آباد', NULL, NULL, NULL, 1, '2025-10-03 07:32:31', '2025-10-03 07:32:31'),
(7, 'P000007', 'رضا', 'صادقی', 45, 'male', '09131234573', NULL, 'تهران، خیابان نیاوران', NULL, NULL, NULL, 1, '2025-10-03 07:32:31', '2025-10-03 07:32:31'),
(8, 'P000008', 'سارا', 'موسوی', 30, 'female', '09131234574', 'sara@email.com', 'تهران، خیابان فرمانیه', '\r\nDeprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated in C:\\xampp\\htdocs\\Teeth\\teeth\\patients\\edit.php on line 108', '\r\nDeprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated in C:\\xampp\\htdocs\\Teeth\\teeth\\patients\\edit.php on line 117', '\r\nDeprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated in C:\\xampp\\htdocs\\Teeth\\teeth\\patients\\edit.php on line 126', 1, '2025-10-03 07:32:31', '2025-10-04 14:21:58'),
(9, 'P215401', 'کریم شاه', 'کریمی', 20, 'male', '0728958423', 'rkhatibi2003@gmail.com', 'هرات', 'ندارد', 'شب نمیدانم', 'دد', 1, '2025-10-03 07:35:31', '2025-10-03 07:35:31'),
(10, 'P999041', 'شریف احمد', 'شریفی', 23, 'male', '0728958423', 'rkhatibi2003@gmail.com', 'هرات شهر کهنه', 'درد دندان', 'آلرژی به آلو', 'بخشش لازم نیست اعدامش کنید', 1, '2025-10-04 03:23:03', '2025-10-04 03:23:03');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `payment_type` enum('service','medicine','salary','withdrawal','expense') NOT NULL,
  `expense_category` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','installment','loan') NOT NULL,
  `payment_date` date NOT NULL,
  `installment_number` int(11) DEFAULT 1,
  `total_installments` int(11) DEFAULT 1,
  `due_date` date DEFAULT NULL,
  `month_year` varchar(7) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `paid_date` date DEFAULT NULL,
  `status` enum('pending','paid','overdue','partial') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `patient_id`, `service_id`, `medicine_id`, `staff_id`, `payment_type`, `expense_category`, `amount`, `payment_method`, `payment_date`, `installment_number`, `total_installments`, `due_date`, `month_year`, `paid_amount`, `paid_date`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 2, 11, NULL, NULL, 'service', NULL, 1000000.00, 'cash', '2024-01-18', 1, 1, NULL, NULL, 1000000.00, '2024-01-18', 'paid', NULL, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(4, 3, 12, NULL, NULL, 'service', NULL, 1500000.00, 'cash', '2024-01-22', 1, 1, NULL, NULL, 1500000.00, '2024-01-22', 'paid', NULL, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(5, 4, 13, NULL, NULL, 'service', NULL, 2800000.00, 'installment', '2024-01-25', 1, 1, NULL, NULL, 0.00, NULL, 'pending', NULL, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(6, 5, 14, NULL, NULL, 'service', NULL, 800000.00, 'cash', '2024-01-28', 1, 1, NULL, NULL, 800000.00, '2024-01-28', 'paid', NULL, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(7, 6, 15, NULL, NULL, 'service', NULL, 3000000.00, 'loan', '2024-02-01', 1, 1, NULL, NULL, 0.00, NULL, 'pending', NULL, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(8, 7, 16, NULL, NULL, 'service', NULL, 900000.00, 'cash', '2024-02-05', 1, 1, NULL, NULL, 900000.00, '2024-02-05', 'paid', NULL, 1, '2025-10-03 07:33:34', '2025-10-03 07:33:34'),
(9, 10, 21, NULL, NULL, 'service', NULL, 300000.00, 'cash', '2025-10-04', 1, 1, NULL, NULL, 300000.00, '2025-10-04', 'paid', '', 1, '2025-10-04 03:33:05', '2025-10-04 03:33:05'),
(10, 8, 23, NULL, NULL, 'service', NULL, 1000000.00, 'loan', '2025-10-04', 1, 1, NULL, NULL, 1000000.00, '2025-10-04', 'paid', '', 1, '2025-10-04 03:52:44', '2025-10-04 03:52:44'),
(11, 10, 21, NULL, NULL, 'service', NULL, 100000.00, 'cash', '2025-10-04', 1, 1, NULL, NULL, 100000.00, '2025-10-04', 'paid', '', 1, '2025-10-04 04:22:18', '2025-10-04 04:22:18'),
(14, 2, NULL, NULL, 6, 'withdrawal', NULL, 500.00, 'cash', '2025-10-04', 1, 1, NULL, '2025-10', 500.00, '2025-10-04', 'paid', 'پول کرایه موتر ', 1, '2025-10-04 06:02:33', '2025-10-04 06:02:33');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `service_name` varchar(100) NOT NULL,
  `service_name_en` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `tooth_number` varchar(10) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `final_price` decimal(10,2) DEFAULT NULL,
  `dentist_id` int(11) DEFAULT NULL,
  `status` enum('template','pending','completed','cancelled') DEFAULT 'template',
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `patient_id`, `service_name`, `service_name_en`, `description`, `base_price`, `category`, `service_date`, `tooth_number`, `quantity`, `unit_price`, `total_price`, `discount`, `final_price`, `dentist_id`, `status`, `notes`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 'ترمیم دندان', 'Tooth Filling', 'ترمیم و پر کردن دندان', 500000.00, 'restorative', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, NULL, '2025-10-03 16:39:06', '2025-10-03 16:39:06'),
(2, NULL, 'کشیدن دندان', 'Tooth Extraction', 'کشیدن دندان ساده', 300000.00, 'surgery', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, NULL, '2025-10-03 16:39:06', '2025-10-03 16:39:06'),
(3, NULL, 'عصب کشی', 'Root Canal', 'درمان ریشه دندان', 1500000.00, 'endodontics', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, NULL, '2025-10-03 16:39:06', '2025-10-03 16:39:06'),
(4, NULL, 'جرمگیری', 'Scaling', 'پاکسازی جرم دندان', 400000.00, 'preventive', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, NULL, '2025-10-03 16:39:06', '2025-10-03 16:39:06'),
(5, NULL, 'ارتودنسی', 'Orthodontics', 'درمان ارتودنسی', 20000000.00, 'orthodontics', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, NULL, '2025-10-03 16:39:06', '2025-10-03 16:39:06'),
(6, NULL, 'ایمپلنت', 'Dental Implant', 'کاشت ایمپلنت دندان', 15000000.00, 'surgery', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, NULL, '2025-10-03 16:39:06', '2025-10-03 16:39:06'),
(7, NULL, 'بلیچینگ', 'Teeth Whitening', 'سفید کردن دندان', 2000000.00, 'cosmetic', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, NULL, '2025-10-03 16:39:06', '2025-10-03 16:39:06'),
(8, NULL, 'روکش دندان', 'Dental Crown', 'روکش کردن دندان', 3000000.00, 'restorative', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, NULL, '2025-10-03 16:39:06', '2025-10-03 16:39:06'),
(9, NULL, 'ایمپلیمنت درجه دو', '2nd implement', 'ثثثث', 1233.00, 'دیدی', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, 1, '2025-10-03 17:18:32', '2025-10-03 17:18:32'),
(10, NULL, 'عصب کشی', 'Root Canal', 'درمان ریشه دندان', 5000000.00, 'endodontics', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, 1, '2025-10-03 07:33:05', '2025-10-03 07:33:05'),
(11, NULL, 'ترمیم دندان', 'Tooth Filling', 'پر کردن دندان', 1500000.00, 'restorative', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, 1, '2025-10-03 07:33:05', '2025-10-03 07:33:05'),
(12, NULL, 'کشیدن دندان', 'Tooth Extraction', 'خارج کردن دندان', 800000.00, 'surgery', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, 1, '2025-10-03 07:33:05', '2025-10-03 07:33:05'),
(13, NULL, 'جرم گیری', 'Scaling', 'پاکسازی جرم دندان', 1000000.00, 'preventive', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, 1, '2025-10-03 07:33:05', '2025-10-03 07:33:05'),
(14, NULL, 'بلیچینگ', 'Teeth Whitening', 'سفید کردن دندان', 3000000.00, 'cosmetic', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, 1, '2025-10-03 07:33:05', '2025-10-03 07:33:05'),
(15, NULL, 'ایمپلنت', 'Dental Implant', 'کاشت دندان', 15000000.00, 'surgery', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, 1, '2025-10-03 07:33:05', '2025-10-03 07:33:05'),
(16, NULL, 'روکش دندان', 'Crown', 'روکش سرامیکی', 8000000.00, 'restorative', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, 1, '2025-10-03 07:33:05', '2025-10-03 07:33:05'),
(17, NULL, 'ارتودنسی', 'Orthodontics', 'تنظیم دندان', 25000000.00, 'orthodontics', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, 1, '2025-10-03 07:33:05', '2025-10-03 07:33:05'),
(21, 10, 'جرمگیری', 'Scaling', NULL, 400000.00, 'preventive', '2025-10-04', '', 1, 400000.00, 400000.00, 0.00, 400000.00, 4, 'completed', '', 1, 1, '2025-10-04 03:30:19', '2025-10-04 03:30:19'),
(22, NULL, 'پاک کاری', 'cleaning', 'ندارد', 550.00, 'ندارد', NULL, NULL, 1, NULL, NULL, 0.00, NULL, NULL, 'template', NULL, 1, 1, '2025-10-04 03:42:52', '2025-10-04 03:42:52'),
(23, 8, 'جرم گیری', 'Scaling', NULL, 1000000.00, 'preventive', '2025-10-04', '', 1, 1000000.00, 1000000.00, 0.00, 1000000.00, 4, 'completed', '', 1, 1, '2025-10-04 03:52:23', '2025-10-04 03:52:23');

-- --------------------------------------------------------

--
-- Table structure for table `system`
--

CREATE TABLE `system` (
  `id` int(11) NOT NULL,
  `record_type` enum('setting','activity_log') NOT NULL,
  `setting_key` varchar(50) DEFAULT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(20) DEFAULT 'text',
  `description` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system`
--

INSERT INTO `system` (`id`, `record_type`, `setting_key`, `setting_value`, `setting_type`, `description`, `user_id`, `action`, `table_name`, `record_id`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 'setting', 'clinic_name', 'کاشت دندان خطیبی', 'text', 'نام مرکز', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-03 16:39:06', '2025-10-08 20:48:43'),
(2, 'setting', 'clinic_address', 'هرات سی متره نبش جاده', 'text', 'آدرس مرکز', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-03 16:39:06', '2025-10-08 20:48:43'),
(3, 'setting', 'clinic_phone', '0799908000', 'text', 'تلفن مرکز', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-03 16:39:06', '2025-10-08 20:48:43'),
(4, 'setting', 'clinic_email', 'rkhati33bi2ss@gmail.com', 'email', 'ایمیل مرکز', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-03 16:39:06', '2025-10-08 20:48:43'),
(5, 'setting', 'currency', 'افغانی', 'text', 'واحد پول', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-03 16:39:06', '2025-10-08 20:48:43'),
(6, 'setting', 'language', 'fa', 'text', 'زبان پیشفرض', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-03 16:39:06', '2025-10-08 20:48:43'),
(7, 'setting', 'low_stock_alert', '10', 'number', 'حد هشدار موجودی کم', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-03 16:39:06', '2025-10-08 20:48:43'),
(8, 'setting', 'expiry_alert_days', '30', 'number', 'روزهای هشدار انقضا', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-03 16:39:06', '2025-10-08 20:48:43'),
(9, 'activity_log', NULL, NULL, 'text', 'User logged out', 1, 'logout', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 16:55:29', '2025-10-03 16:55:29'),
(10, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 16:55:42', '2025-10-03 16:55:42'),
(11, 'activity_log', NULL, NULL, 'text', 'Created service template: ایمپلیمنت درجه دو', 1, 'create', 'services', 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 17:18:32', '2025-10-03 17:18:32'),
(12, 'activity_log', NULL, NULL, 'text', 'Created medicine: ییی', 1, 'create', 'medicines', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 17:20:24', '2025-10-03 17:20:24'),
(13, 'setting', 'clinic_name_fa', 'قادری', 'text', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-03 17:24:52', '2025-10-03 19:30:56'),
(14, 'activity_log', NULL, NULL, 'text', 'Updated system settings', 1, 'update', 'settings', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 17:24:52', '2025-10-03 17:24:52'),
(15, 'activity_log', NULL, NULL, 'text', 'User logged out', 1, 'logout', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 17:26:21', '2025-10-03 17:26:21'),
(16, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 17:26:30', '2025-10-03 17:26:30'),
(17, 'activity_log', NULL, NULL, 'text', 'Created user: student', 1, 'create', 'users', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 17:29:50', '2025-10-03 17:29:50'),
(18, 'activity_log', NULL, NULL, 'text', 'User logged out', 1, 'logout', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 17:29:55', '2025-10-03 17:29:55'),
(19, 'activity_log', NULL, NULL, 'text', 'User logged in', 2, 'login', 'users', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 17:29:59', '2025-10-03 17:29:59'),
(20, 'activity_log', NULL, NULL, 'text', 'User logged out', 2, 'logout', 'users', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 17:30:12', '2025-10-03 17:30:12'),
(21, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 07:24:21', '2025-10-03 07:24:21'),
(31, 'activity_log', NULL, NULL, 'text', 'Created patient: کریم شاه کریمی', 1, 'create', 'patients', 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 07:35:31', '2025-10-03 07:35:31'),
(32, 'activity_log', NULL, NULL, 'text', 'Deleted patient: محمد احمدی', 1, 'delete', 'patients', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 07:36:11', '2025-10-03 07:36:11'),
(33, 'activity_log', NULL, NULL, 'text', 'Updated system settings', 1, 'update', 'settings', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 07:39:01', '2025-10-03 07:39:01'),
(34, 'activity_log', NULL, NULL, 'text', 'Updated system settings', 1, 'update', 'settings', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 07:39:18', '2025-10-03 07:39:18'),
(35, 'activity_log', NULL, NULL, 'text', 'Updated system settings', 1, 'update', 'settings', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 09:00:56', '2025-10-03 09:00:56'),
(36, 'activity_log', NULL, NULL, 'text', 'Created user: manger', 1, 'create', 'users', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 09:12:15', '2025-10-03 09:12:15'),
(37, 'activity_log', NULL, NULL, 'text', 'User logged out', 1, 'logout', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 09:12:36', '2025-10-03 09:12:36'),
(38, 'activity_log', NULL, NULL, 'text', 'User logged in', 3, 'login', 'users', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 09:12:42', '2025-10-03 09:12:42'),
(39, 'activity_log', NULL, NULL, 'text', 'User logged out', 3, 'logout', 'users', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 09:13:04', '2025-10-03 09:13:04'),
(40, 'activity_log', NULL, NULL, 'text', 'User logged in', 2, 'login', 'users', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 09:13:11', '2025-10-03 09:13:11'),
(41, 'activity_log', NULL, NULL, 'text', 'User logged out', 2, 'logout', 'users', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 09:13:15', '2025-10-03 09:13:15'),
(42, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 09:13:20', '2025-10-03 09:13:20'),
(43, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0', '2025-10-03 09:20:52', '2025-10-03 09:20:52'),
(44, 'activity_log', NULL, NULL, 'text', 'Updated system settings', 1, 'update', 'settings', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0', '2025-10-03 09:22:23', '2025-10-03 09:22:23'),
(45, 'activity_log', NULL, NULL, 'text', 'Created database backup: backup_2025-10-03_19-57-38.sql', 1, 'create', 'documents', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 09:27:38', '2025-10-03 09:27:38'),
(46, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '10.10.10.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-10-03 09:46:02', '2025-10-03 09:46:02'),
(47, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '10.10.10.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-10-03 09:53:57', '2025-10-03 09:53:57'),
(48, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:20:13', '2025-10-04 03:20:13'),
(49, 'activity_log', NULL, NULL, 'text', 'Created patient: شریف احمد شریفی', 1, 'create', 'patients', 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:23:03', '2025-10-04 03:23:03'),
(50, 'activity_log', NULL, NULL, 'text', 'Created user: رامین شاه', 1, 'create', 'users', 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:29:32', '2025-10-04 03:29:32'),
(51, 'activity_log', NULL, NULL, 'text', 'Provided service to patient ID: 10', 1, 'create', 'services', 21, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:30:19', '2025-10-04 03:30:19'),
(52, 'activity_log', NULL, NULL, 'text', 'Created payment for patient ID: 10', 1, 'create', 'payments', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:33:05', '2025-10-04 03:33:05'),
(53, 'activity_log', NULL, NULL, 'text', 'Created service template: پاک کاری', 1, 'create', 'services', 22, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:42:52', '2025-10-04 03:42:52'),
(54, 'activity_log', NULL, NULL, 'text', 'Created medicine: متادول', 1, 'create', 'medicines', 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:46:18', '2025-10-04 03:46:18'),
(55, 'activity_log', NULL, NULL, 'text', 'Created partner: Mir Naiem', 1, 'create', 'documents', 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:47:23', '2025-10-04 03:47:23'),
(56, 'activity_log', NULL, NULL, 'text', 'Created database backup: backup_2025-10-04_14-19-16.sql', 1, 'create', 'documents', 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:49:16', '2025-10-04 03:49:16'),
(57, 'activity_log', NULL, NULL, 'text', 'Updated system settings', 1, 'update', 'settings', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:49:54', '2025-10-04 03:49:54'),
(58, 'activity_log', NULL, NULL, 'text', 'Updated patient: سارا موسوی', 1, 'update', 'patients', 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:51:58', '2025-10-04 03:51:58'),
(59, 'activity_log', NULL, NULL, 'text', 'Provided service to patient ID: 8', 1, 'create', 'services', 23, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:52:23', '2025-10-04 03:52:23'),
(60, 'activity_log', NULL, NULL, 'text', 'Created payment for patient ID: 8', 1, 'create', 'payments', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:52:44', '2025-10-04 03:52:44'),
(61, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 04:01:58', '2025-10-04 04:01:58'),
(62, 'activity_log', NULL, NULL, 'text', 'Created supplier: شرکت رنجبر', 1, 'create', 'suppliers', 11, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 04:14:56', '2025-10-04 04:14:56'),
(63, 'activity_log', NULL, NULL, 'text', 'Created payment for patient ID: 10', 1, 'create', 'payments', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 04:22:18', '2025-10-04 04:22:18'),
(64, 'setting', 'expense_categories', 'کرایه,برق,آب,گاز,اینترنت,تلفن,نظافت,تعمیرات,سایر', 'text', 'دسته‌بندی مصارف', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-04 05:19:13', '2025-10-04 05:19:13'),
(65, 'setting', 'salary_payment_day', '1', 'number', 'روز پرداخت معاش (روز ماه)', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-04 05:19:13', '2025-10-04 05:19:13'),
(66, 'activity_log', NULL, NULL, 'text', 'Created staff: Rafe Ahmad Khatebi', 1, 'create_staff', 'users', 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 05:27:12', '2025-10-04 05:27:12'),
(67, 'activity_log', NULL, NULL, 'text', 'Created expense: ندارد', 1, 'create_expense', 'documents', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 05:52:52', '2025-10-04 05:52:52'),
(68, 'activity_log', NULL, NULL, 'text', 'Paid salary for month 2025-10', 1, 'pay_salary', 'payments', 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 05:54:32', '2025-10-04 05:54:32'),
(69, 'activity_log', NULL, NULL, 'text', 'Deleted user ID: 5', 1, 'delete_user', 'users', 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 06:00:30', '2025-10-04 06:00:30'),
(70, 'activity_log', NULL, NULL, 'text', 'Created staff: وحید وحیدی', 1, 'create_staff', 'users', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 06:01:59', '2025-10-04 06:01:59'),
(71, 'activity_log', NULL, NULL, 'text', 'Staff withdrawal: 500', 1, 'add_withdrawal', 'payments', 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 06:02:33', '2025-10-04 06:02:33'),
(72, 'activity_log', NULL, NULL, 'text', 'Updated system settings', 1, 'update', 'settings', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 06:29:16', '2025-10-04 06:29:16'),
(73, 'activity_log', NULL, NULL, 'text', 'User logged out', 1, 'logout', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 07:21:31', '2025-10-04 07:21:31'),
(74, 'activity_log', NULL, NULL, 'text', 'User logged in', 2, 'login', 'users', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 07:21:35', '2025-10-04 07:21:35'),
(75, 'activity_log', NULL, NULL, 'text', 'User logged out', 2, 'logout', 'users', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 07:21:58', '2025-10-04 07:21:58'),
(76, 'activity_log', NULL, NULL, 'text', 'User logged in', 3, 'login', 'users', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 07:22:02', '2025-10-04 07:22:02'),
(77, 'activity_log', NULL, NULL, 'text', 'User logged out', 3, 'logout', 'users', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 07:22:17', '2025-10-04 07:22:17'),
(78, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 07:22:26', '2025-10-04 07:22:26'),
(79, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 07:40:07', '2025-10-04 07:40:07'),
(80, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-06 21:54:46', '2025-10-06 21:54:46'),
(81, 'activity_log', NULL, NULL, 'text', 'Created supplier: شرکت رنجبر2', 1, 'create', 'suppliers', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-06 22:17:12', '2025-10-06 22:17:12'),
(82, 'activity_log', NULL, NULL, 'text', 'Created expense: 222', 1, 'create_expense', 'documents', 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-06 22:18:19', '2025-10-06 22:18:19'),
(83, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-08 13:02:41', '2025-10-08 13:02:41'),
(84, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-08 13:05:56', '2025-10-08 13:05:56'),
(85, 'activity_log', NULL, NULL, 'text', 'Created supplier: شرکت رنجبر212', 1, 'create', 'suppliers', 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-08 13:10:25', '2025-10-08 13:10:25'),
(86, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-08 14:05:19', '2025-10-08 14:05:19'),
(87, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-08 21:47:38', '2025-10-08 21:47:38'),
(88, 'activity_log', NULL, NULL, 'text', 'Updated system settings', 1, 'update', 'settings', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-08 21:48:43', '2025-10-08 21:48:43'),
(89, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 11:14:48', '2025-10-09 11:14:48'),
(90, 'activity_log', NULL, NULL, 'text', 'User logged in', 1, 'login', 'users', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 21:18:39', '2025-10-09 21:18:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','dentist','secretary','accountant') NOT NULL,
  `monthly_salary` decimal(10,2) DEFAULT 0.00,
  `salary_currency` varchar(20) DEFAULT 'افغانی',
  `hire_date` date DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_staff` tinyint(1) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `password_changed_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `role`, `monthly_salary`, `salary_currency`, `hire_date`, `job_title`, `is_active`, `is_staff`, `last_login`, `failed_login_attempts`, `locked_until`, `password_changed_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$ZKkdGMDYNrAZ5DQcIkwT4eN0Iyg3p3RPbilwonDNQxgItTWpFDV6q', 'مدیر سیستم', NULL, NULL, 'admin', 0.00, 'افغانی', NULL, NULL, 1, 0, NULL, 0, NULL, '2025-10-03 16:39:06', '2025-10-03 16:39:06', '2025-10-03 16:56:17'),
(2, 'student', '$2y$10$n5MkYZVTP9EhVjvz/FdF1uerTWazBf1nhOPkKa2qnrXkedq7b.CU2', 'Rafe Ahmad Khatebi', 'rkhatibi2003@gmail.com', '0728958423', 'secretary', 0.00, 'افغانی', NULL, NULL, 1, 0, NULL, 0, NULL, '2025-10-03 17:29:50', '2025-10-03 17:29:50', '2025-10-03 17:29:50'),
(3, 'manger', '$2y$10$lAJkPffthu5hQ/10Q0Ybw.csggX2ntjVek3ISwFJvXsP0PjyEx9cm', 'Rafe Ahmad Khatebi', 'rkhatibi2003@gmail.com', '0728958423', 'accountant', 0.00, 'افغانی', NULL, NULL, 1, 0, NULL, 0, NULL, '2025-10-03 09:12:15', '2025-10-03 09:12:15', '2025-10-03 09:12:15'),
(4, 'رامین شاه', '$2y$10$7NNlUmhW.n4VuPqEcIOxZuXm7rV3D6iQoWtd9o4JGkKGj.4Jnw/ym', 'سروشی', 'rkhatibi2003@gmail.com', '0728958423', 'dentist', 0.00, 'افغانی', NULL, NULL, 1, 0, NULL, 0, NULL, '2025-10-04 03:29:32', '2025-10-04 03:29:32', '2025-10-04 03:29:32'),
(6, 'staff_1759582919', '$2y$10$.8xCd9PUQvQeLwoQd/c5u.b/LeamM1ouG3weZwkmF/JdoEdFRLrsa', 'وحید وحیدی', 'rkhatibi2003@gmail.com', '0728958423', 'secretary', 7000.00, 'افغانی', '2025-10-04', 'کارمند', 1, 1, NULL, 0, NULL, '2025-10-04 06:01:59', '2025-10-04 06:01:59', '2025-10-04 06:01:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_code` (`document_code`),
  ADD KEY `idx_document_type_code` (`document_type`,`document_code`),
  ADD KEY `idx_patient_type` (`patient_id`,`document_type`),
  ADD KEY `idx_service_type` (`service_id`,`document_type`),
  ADD KEY `idx_partner_period` (`partner_name`,`period_start`,`period_end`),
  ADD KEY `idx_status_type` (`status`,`document_type`),
  ADD KEY `idx_created_date_type` (`created_at`,`document_type`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_expense_type` (`expense_type`,`status`),
  ADD KEY `idx_expense_category` (`expense_category`),
  ADD KEY `idx_next_due` (`next_due_date`,`status`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `medicine_code` (`medicine_code`),
  ADD KEY `idx_medicine_code` (`medicine_code`),
  ADD KEY `idx_medicine_name_active` (`medicine_name`,`is_active`),
  ADD KEY `idx_category_active` (`category`,`is_active`),
  ADD KEY `idx_stock_alert` (`stock_quantity`,`min_stock_level`),
  ADD KEY `idx_expiry_alert` (`expiry_date`,`is_active`),
  ADD KEY `idx_sale_date` (`sale_date`,`sale_patient_id`),
  ADD KEY `idx_movement_date` (`movement_date`,`movement_type`),
  ADD KEY `idx_supplier_name` (`supplier_name`),
  ADD KEY `sale_patient_id` (`sale_patient_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patient_code` (`patient_code`),
  ADD KEY `idx_patient_code` (`patient_code`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_name_phone` (`first_name`,`last_name`,`phone`),
  ADD KEY `idx_created_date` (`created_at`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_date` (`patient_id`,`payment_date`),
  ADD KEY `idx_payment_method_date` (`payment_method`,`payment_date`),
  ADD KEY `idx_payment_type_date` (`payment_type`,`payment_date`),
  ADD KEY `idx_status_due` (`status`,`due_date`),
  ADD KEY `idx_installments` (`payment_method`,`installment_number`,`total_installments`),
  ADD KEY `idx_overdue_payments` (`status`,`due_date`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_staff_month` (`staff_id`,`month_year`),
  ADD KEY `idx_expense_category` (`expense_category`,`payment_date`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_service_name_active` (`service_name`,`is_active`),
  ADD KEY `idx_category_active` (`category`,`is_active`),
  ADD KEY `idx_patient_date` (`patient_id`,`service_date`),
  ADD KEY `idx_dentist_date` (`dentist_id`,`service_date`),
  ADD KEY `idx_status_date` (`status`,`service_date`),
  ADD KEY `idx_template_services` (`status`,`is_active`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `system`
--
ALTER TABLE `system`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_setting` (`setting_key`,`record_type`),
  ADD KEY `idx_record_type` (`record_type`),
  ADD KEY `idx_setting_key` (`setting_key`),
  ADD KEY `idx_user_action` (`user_id`,`action`),
  ADD KEY `idx_table_record` (`table_name`,`record_id`),
  ADD KEY `idx_activity_date` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username_active` (`username`,`is_active`),
  ADD KEY `idx_role_active` (`role`,`is_active`),
  ADD KEY `idx_login_attempts` (`failed_login_attempts`,`locked_until`),
  ADD KEY `idx_staff_active` (`is_staff`,`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `system`
--
ALTER TABLE `system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `documents_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `medicines`
--
ALTER TABLE `medicines`
  ADD CONSTRAINT `medicines_ibfk_1` FOREIGN KEY (`sale_patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `medicines_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_5` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `services_ibfk_2` FOREIGN KEY (`dentist_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `services_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `system`
--
ALTER TABLE `system`
  ADD CONSTRAINT `system_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
