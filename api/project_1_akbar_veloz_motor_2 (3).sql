-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jun 16, 2025 at 10:35 PM
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
-- Database: `project_1_akbar_veloz_motor_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `address` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `slug`, `address`, `created_at`, `updated_at`, `deleted_at`) VALUES
(46, 'Utama', 'utama', 'Street:  Jl Raya Pintu I TMII Ged Pewayangan Kautaman, Jakarta\r\n\r\nCity:   Jakarta\r\n\r\nState/province/area:    Jakarta\r\n\r\nPhone number:  021-87799510\r\n\r\nZip code:  13560\r\n\r\nCountry calling code:  +62\r\n\r\nCountry:  Indonesia', '2025-06-10 02:25:17', '2025-06-10 05:09:47', NULL),
(47, 'Kedua', 'kedua', 'Street:  Jl Angkasa 1 Halim Perdana Kusumah, Dki Jakarta\r\n\r\nCity:   Dki Jakarta\r\n\r\nState/province/area:    Jakarta\r\n\r\nPhone number:  021-80880070\r\n\r\nZip code:  13610\r\n\r\nCountry calling code:  +62\r\n\r\nCountry:  Indonesia', '2025-06-10 05:10:28', NULL, NULL),
(50, 'Ketika', 'ketika', 'Street:  Jl Kyai Caringin 12 B, Dki Jakarta\r\n\r\nCity:   Dki Jakarta\r\n\r\nState/province/area:    Jakarta\r\n\r\nPhone number:  021-63863440\r\n\r\nZip code:  10150\r\n\r\nCountry calling code:  +62\r\n\r\nCountry:  Indonesia', '2025-06-10 17:32:04', NULL, NULL),
(51, 'Keempat', 'keempat', 'Street:  Jl Raya Rawakuning RT 16/02, Dki Jakarta\r\n\r\nCity:   Dki Jakarta\r\n\r\nState/province/area:    Jakarta\r\n\r\nPhone number:  0-21-480-3658\r\n\r\nZip code:  13950\r\n\r\nCountry calling code:  +62\r\n\r\nCountry:  Indonesia', '2025-06-10 17:32:19', NULL, NULL),
(52, 'Kelima', 'kelima', 'Street:  Jl Jend Sudirman 3603, Sumatera Selatan\r\n\r\nCity:   Sumatera Selatan\r\n\r\nState/province/area:    Palembang\r\n\r\nPhone number:  0-711-36-8462\r\n\r\nZip code:  30253\r\n\r\nCountry calling code:  +62\r\n\r\nCountry:  Indonesia', '2025-06-10 17:32:34', NULL, NULL),
(55, 'Test Soft Delete 3', 'test-soft-delete-3', '-', '2025-06-10 18:13:58', NULL, '2025-06-13 02:53:15'),
(56, 'Test Soft Delete 4', 'test-soft-delete-4', '-', '2025-06-10 18:14:09', NULL, '2025-06-13 02:53:15'),
(57, 'Test Soft Delete 5', 'test-soft-delete-5', '-', '2025-06-10 18:14:20', NULL, NULL),
(63, 'Test Destroy 1', 'test-destroy-1', '-', '2025-06-10 18:22:37', NULL, NULL),
(64, 'Test Destroy 2', 'test-destroy-2', '-', '2025-06-10 18:22:45', NULL, NULL),
(65, 'Test Destroy 3', 'test-destroy-3', '-', '2025-06-10 18:22:50', NULL, NULL),
(66, 'Test Destroy 4', 'test-destroy-4', '-', '2025-06-10 18:22:55', NULL, NULL),
(68, 'Keenam', 'keenam', 'Jlan raya cibogo', '2025-06-13 01:11:49', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Suzuki', 'suzuki', '2025-06-10 01:37:30', NULL, NULL),
(3, 'Yamaha', 'yamaha', '2025-06-10 01:41:17', NULL, NULL),
(7, 'Honda', 'honda', '2025-06-10 03:27:27', NULL, NULL),
(8, 'Kawasaki', 'kawasaki', '2025-06-10 05:15:32', '2025-06-10 05:18:50', NULL),
(9, 'TVS', 'tvs', '2025-06-10 05:22:44', NULL, NULL),
(10, 'Vespa', 'vespa', '2025-06-10 05:23:22', '2025-06-10 05:23:49', NULL),
(14, 'Bentley', 'bentley', '2025-06-10 14:32:36', NULL, NULL),
(15, 'BMW', 'bmw', '2025-06-10 14:32:44', NULL, NULL),
(16, 'Buggati', 'buggati', '2025-06-10 14:32:49', NULL, NULL),
(19, 'Fiat', 'fiat', '2025-06-10 14:33:09', NULL, NULL),
(20, 'Jaguar', 'jaguar', '2025-06-10 14:33:13', NULL, NULL),
(21, 'Lamborgini', 'lamborgini', '2025-06-10 14:33:18', NULL, NULL),
(22, 'Mini Cooper', 'mini-cooper', '2025-06-10 14:33:23', NULL, NULL),
(27, 'Hyundai', 'hyundai', '2025-06-10 14:43:15', NULL, NULL),
(28, 'KIA', 'kia', '2025-06-10 14:43:25', NULL, NULL),
(29, 'Isuzu', 'isuzu', '2025-06-10 14:43:29', NULL, NULL),
(30, 'Mazda', 'mazda', '2025-06-10 14:43:33', NULL, NULL),
(31, 'Mitsubishi', 'mitsubishi', '2025-06-10 14:43:38', NULL, NULL),
(32, 'Subaru', 'subaru', '2025-06-10 14:43:42', NULL, NULL),
(33, 'Toyota', 'toyota', '2025-06-10 14:43:53', NULL, NULL),
(34, 'Wuling', 'wuling', '2025-06-10 14:43:57', NULL, NULL),
(35, 'Chevrolet', 'chevrolet', '2025-06-10 14:44:01', NULL, NULL),
(36, 'Ford', 'ford', '2025-06-10 14:44:06', NULL, NULL),
(37, 'Jeep', 'jeep', '2025-06-10 14:44:11', NULL, NULL),
(38, 'Tesla', 'tesla', '2025-06-10 14:44:16', NULL, NULL),
(46, 'Audi', 'audi', '2025-06-10 17:18:40', '2025-06-12 16:02:50', NULL),
(47, 'Dummy Soft Delete 1', 'dummy-soft-delete-1', '2025-06-10 17:18:54', NULL, '2025-06-11 17:38:04'),
(48, 'Dummy Soft Delete 2', 'dummy-soft-delete-2', '2025-06-10 17:18:57', NULL, '2025-06-11 17:38:03'),
(49, 'Dummy Soft Delete 3', 'dummy-soft-delete-3', '2025-06-10 17:19:00', NULL, '2025-06-11 17:38:03'),
(50, 'Dummy Soft Delete 4', 'dummy-soft-delete-4', '2025-06-10 17:19:03', NULL, '2025-06-11 17:38:02'),
(51, 'Dummy Soft Delete 5', 'dummy-soft-delete-5', '2025-06-10 17:19:07', NULL, '2025-06-11 17:38:01'),
(53, 'Dummy Destroy 2', 'dummy-destroy-2', '2025-06-10 17:19:20', NULL, '2025-06-11 17:38:15'),
(54, 'Dummy Destroy 3', 'dummy-destroy-3', '2025-06-10 17:19:25', NULL, '2025-06-11 17:38:17'),
(55, 'Dummy Destroy 4', 'dummy-destroy-4', '2025-06-10 17:19:32', NULL, '2025-06-11 17:38:05'),
(56, 'Dummy Destroy 5', 'dummy-destroy-5', '2025-06-10 17:19:37', NULL, '2025-06-11 17:38:05'),
(58, 'Test-Create', 'test-create', '2025-06-12 15:44:05', NULL, '2025-06-12 15:44:15');

-- --------------------------------------------------------

--
-- Table structure for table `chat_sessions`
--

CREATE TABLE `chat_sessions` (
  `session_id` varchar(255) NOT NULL,
  `customer_id` bigint NOT NULL,
  `user_id` int DEFAULT NULL,
  `status` enum('open','closed','pending') DEFAULT 'pending',
  `started_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `closed_at` timestamp NULL DEFAULT NULL,
  `last_customer_activity` timestamp NULL DEFAULT NULL,
  `customer_typing` tinyint(1) DEFAULT '0',
  `user_typing` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chat_sessions`
--

INSERT INTO `chat_sessions` (`session_id`, `customer_id`, `user_id`, `status`, `started_at`, `closed_at`, `last_customer_activity`, `customer_typing`, `user_typing`) VALUES
('a4576359-2119-4add-8e09-98960d20a67c', 5, NULL, 'pending', '2025-06-15 17:22:26', NULL, '2025-06-16 22:29:56', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `is_logged_in` tinyint(1) NOT NULL DEFAULT '0',
  `username` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `address` text,
  `registration_method` enum('manual','google','facebook') NOT NULL DEFAULT 'manual',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `slug`, `email`, `phone`, `password`, `google_id`, `facebook_id`, `is_logged_in`, `username`, `picture`, `address`, `registration_method`, `email_verified_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Farhan', '', 'farhan@email.com', '08921', NULL, NULL, NULL, 0, NULL, NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'manual', NULL, '2025-06-09 17:50:15', NULL, NULL),
(2, 'Zaki', 'zaki', 'zacki@gmail.com', '0892112', NULL, NULL, NULL, 0, NULL, NULL, 'qq', 'manual', NULL, '2025-06-13 02:01:19', NULL, NULL),
(3, 'Eful', 'eful', 'eful@gmail.com', '089211223', NULL, NULL, NULL, 0, NULL, NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'manual', NULL, '2025-06-14 00:39:47', NULL, NULL),
(4, 'Givi Boy', 'givi-boy', 'farhantriputrawisnu@gmail.com', NULL, NULL, '116923222700550539651', NULL, 0, 'Givi Boy', NULL, NULL, 'google', NULL, '2025-06-15 03:00:13', '2025-06-15 03:00:13', NULL),
(5, 'Zacki Saeful bahri', 'zacki-saeful-bahri', 'zackisaefulbahri671@gmail.com', NULL, NULL, '102714652928735509122', NULL, 1, 'Zacki Saeful bahri', NULL, NULL, 'google', NULL, '2025-06-15 09:28:11', '2025-06-15 09:28:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `chat_session_id` varchar(255) NOT NULL,
  `sender_id` bigint NOT NULL,
  `sender_type` enum('customer','user','bot') NOT NULL,
  `message_text` text NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read_by_user` tinyint(1) DEFAULT '0',
  `is_read_by_customer` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `chat_session_id`, `sender_id`, `sender_type`, `message_text`, `timestamp`, `is_read_by_user`, `is_read_by_customer`) VALUES
(1, 'a4576359-2119-4add-8e09-98960d20a67c', 5, 'customer', 'Motor dengan Budget Murah!', '2025-06-16 21:31:36', 0, 1),
(2, 'a4576359-2119-4add-8e09-98960d20a67c', 5, 'bot', '<strong>Pilih kendaraan untuk dinegosiasikan:</strong><div class=\"promo-card vehicle-option\" data-id=\"K0305F\">\n<h4>K0305F - Honda CB 150 R Street Fire</h4>\n<img src=\"null\" alt=\"Honda CB 150 R Street Fire\" style=\"max-width:100px; height:auto; border-radius:8px; margin-top:5px;\">\n<p>Harga: Rp300</p>\n</div><div class=\"promo-card vehicle-option\" data-id=\"K0105F\">\n<h4>K0105F - Honda Fortuner</h4>\n<img src=\"../storage/vehicles/vehicle_K0105F/vehicle_photos/photo_6847b327ae170.jpg\" alt=\"Honda Fortuner\" style=\"max-width:100px; height:auto; border-radius:8px; margin-top:5px;\">\n<p>Harga: Rp2.300</p>\n</div>', '2025-06-16 21:31:36', 0, 1),
(3, 'a4576359-2119-4add-8e09-98960d20a67c', 5, 'bot', '\n<strong>Anda memilih Honda Fortuner</strong>\n<div class=\"vehicle-card-chat promo-card\">\n<h4>K0105F - Honda Fortuner</h4>\n<img src=\"../storage/vehicles/vehicle_K0105F/vehicle_photos/photo_6847b327ae170.jpg\" alt=\"Honda Fortuner\" style=\"max-width:150px; height:auto; border-radius:8px; margin-top:5px;\">\n<p><strong>Harga:</strong> Rp2.300</p>\n</div>\n<p>Silakan masukkan penawaran harga Anda (dalam Rp):</p>\n', '2025-06-16 21:31:42', 0, 1),
(4, 'a4576359-2119-4add-8e09-98960d20a67c', 5, 'customer', 'Saya menawarkan Rp2.500 untuk K0105F - Honda Fortuner.', '2025-06-16 21:31:47', 0, 1),
(5, 'a4576359-2119-4add-8e09-98960d20a67c', 5, 'bot', '<strong>Penawaran DITERIMA!</strong><p>Penawaran Anda Rp2.500 untuk K0105F - Honda Fortuner diterima.</p><button class=\"negotiation-btn\" data-action=\"testDrive\" data-vehicle-id=\"K0105F\" data-negotiated-price=\"2500\">Lanjut Test Drive</button><button class=\"negotiation-btn\" data-action=\"continueTransaction\" data-vehicle-id=\"K0105F\" data-negotiated-price=\"2500\">Lanjut Transaksi</button>', '2025-06-16 21:31:47', 0, 1),
(6, 'a4576359-2119-4add-8e09-98960d20a67c', 5, 'bot', 'Penawaran DITERIMA untuk K0105F - Honda Fortuner. Nominal: Rp2.500. Pelanggan diminta Klik \"Lanjut Transaksi\" atau \"Lanjut Test Drive\".', '2025-06-16 21:31:47', 0, 1),
(7, 'a4576359-2119-4add-8e09-98960d20a67c', 5, 'bot', 'Order Transaksi Anda telah berhasil dibuat untuk kendaraan K0105F! Kami akan mengarahkan Anda ke halaman hubungi kami.', '2025-06-16 21:31:49', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint NOT NULL,
  `customer_id` bigint NOT NULL,
  `vehicle_id` char(8) NOT NULL,
  `negotiated_price` decimal(15,2) DEFAULT '0.00',
  `type_order` enum('test_driver','transaction') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` enum('cancelled','proced','finished') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'proced',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `vehicle_id`, `negotiated_price`, `type_order`, `status`, `is_read`, `created_at`, `updated_at`, `deleted_at`) VALUES
(55, 5, 'K0102F', 2000.00, 'transaction', 'proced', 0, '2025-06-16 21:29:04', NULL, NULL),
(56, 5, 'K0105F', 2500.00, 'transaction', 'proced', 0, '2025-06-16 21:31:49', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ktp_scan` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `address_ktp` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `address_domicile` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `name`, `slug`, `nik`, `phone`, `email`, `ktp_scan`, `photo`, `address_ktp`, `address_domicile`, `created_at`, `updated_at`, `deleted_at`) VALUES
(17, 'Agung Hidayat Alamsyah', 'agung-hidayat-alamsyah', '1234234534564567', '123423453456', 'agunghidayatalamsyah@mailinator.com', 'partners/partners_agung-hidayat-alamsyah/ktp/ktp_scan_6849c673d1a36.png', 'partners/partners_agung-hidayat-alamsyah/photo/photo_684899d70f74b.jpg', '-', '-', '2025-06-10 20:47:19', '2025-06-11 18:30:48', NULL),
(29, 'Andi Hermawan', 'andi-hermawan', '1234234534564568', '123423453457', 'andihermawan@mailinator.com', '', '', 'Sunt ex laboriosama', 'Consequuntur nisi pla', '2025-06-12 00:26:52', '2025-06-12 16:58:51', NULL),
(30, 'Wynne Hebert', 'wynne-hebert', '1234234534564565', '8247', 'kuwymosuj@mailinator.com', '', '', 'Consequuntur aut per', 'Voluptate error omni', '2025-06-12 16:24:02', NULL, '2025-06-12 16:43:53');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Owner', '2025-04-15 11:01:19', NULL, NULL),
(2, 'Employe', '2025-05-20 01:32:04', '2025-05-20 23:04:04', NULL),
(8, 'Test-Role', '2025-06-12 18:00:07', '2025-06-12 18:02:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `test_drivers`
--

CREATE TABLE `test_drivers` (
  `id` bigint NOT NULL,
  `order_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `status` enum('cancelled','process','finish','') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'process',
  `result_note` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint NOT NULL,
  `order_id` bigint NOT NULL COMMENT 'Relasi ke order yang dibuat customer',
  `user_id` bigint DEFAULT NULL COMMENT 'Sales/staff yang memproses transaksi',
  `vehicle_price` decimal(15,2) NOT NULL,
  `deal_negotiation` decimal(15,2) DEFAULT '0.00',
  `grand_total` decimal(15,2) NOT NULL COMMENT 'Total harga kendaraan setelah negoisasi',
  `payment_type` enum('tunai','cicilan') NOT NULL COMMENT 'Jenis pembayaran lunas atau kredit',
  `down_payment` decimal(15,2) DEFAULT NULL COMMENT 'Jumlah DP yang dibayar, hanya jika tipe cicilan',
  `amount_paid` decimal(15,2) NOT NULL COMMENT 'Jumlah riil yang dibayar customer (bisa seharga DP atau lunas)',
  `payment_method` enum('cash','transfer','midtrans') NOT NULL COMMENT 'Metode pembayaran yang digunakan',
  `status` enum('pending','paid','dp_paid','failed','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Status terbaru dari transaksi',
  `payment_gateway_ref` varchar(255) DEFAULT NULL COMMENT 'ID/Referensi unik dari payment gateway seperti Midtrans',
  `payment_proof` varchar(255) DEFAULT NULL COMMENT 'Path ke file bukti bayar untuk metode transfer manual',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Tabel untuk mencatat semua transaksi penjualan kendaraan';

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `order_id`, `user_id`, `vehicle_price`, `deal_negotiation`, `grand_total`, `payment_type`, `down_payment`, `amount_paid`, `payment_method`, `status`, `payment_gateway_ref`, `payment_proof`, `created_at`, `updated_at`, `deleted_at`) VALUES
(12, 55, NULL, 2000.00, 0.00, 0.00, 'tunai', NULL, 0.00, 'cash', 'pending', NULL, NULL, '2025-06-16 21:29:04', NULL, NULL),
(13, 56, NULL, 2300.00, 0.00, 0.00, 'tunai', NULL, 0.00, 'cash', 'pending', NULL, NULL, '2025-06-16 21:31:49', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(20) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `username` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` bigint NOT NULL,
  `is_online` tinyint(1) NOT NULL DEFAULT '0',
  `last_activity` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by_role_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `slug`, `phone`, `photo`, `address`, `username`, `password`, `role_id`, `is_online`, `last_activity`, `created_at`, `updated_at`, `deleted_at`, `deleted_by_role_at`) VALUES
(1, 'Rudiger Madrid', 'rudiger-madrid', '081234567890', 'users/user_rudiger-madrid/photo_684adbbf5e6c0.jpg', 'Jl. Merdeka No.123', 'andis', '$2y$10$dlbxvaY4g7iRKLu.qsbZ7OL7/jzdRd102QZE78N9gF1gRQnq2deU.', 1, 1, '2025-06-15 10:33:56', '2025-04-15 11:01:19', '2025-06-12 13:53:03', NULL, NULL),
(3, 'Farhan Ginting', 'farhan-ginting', '089876543111', NULL, 'Subanggg', 'farhan', '$2y$10$j38WMObhIlUYrSI2nMmQ0e2JTwgBTg8F3UAVs9NYVOOREBrp4X7oi', 2, 0, NULL, '2025-05-21 01:57:21', '2025-05-23 03:33:43', NULL, NULL),
(8, 'Zacki Saiful', 'zacki-saiful', '121-4883', NULL, 'Et eum quis sunt aut', 'zaki', '$2y$12$TFqapKevXQrF95r8LLI2EOjJ9n1/1tqNqJ4d/6eOJxAw4CzoniewC', 2, 0, NULL, '2025-06-12 12:13:54', '2025-06-12 12:44:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` char(8) NOT NULL,
  `vehicle_model_id` bigint NOT NULL,
  `type_vehicle` enum('motorcycle','car') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `color` varchar(100) NOT NULL,
  `production_year` date NOT NULL,
  `serial_number` varchar(50) DEFAULT NULL,
  `stnk_deadline` date NOT NULL,
  `type_fuel` enum('gasoline','electric','hybrid') NOT NULL,
  `kilometer` int NOT NULL,
  `cc_engine` int NOT NULL,
  `description` longtext NOT NULL,
  `lowest_price` int NOT NULL,
  `price_displayed` int NOT NULL,
  `status` enum('available','service','test_drive','sold','transaction','on_loan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'available',
  `user_id` bigint NOT NULL,
  `branch_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by_branch_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_model_id`, `type_vehicle`, `color`, `production_year`, `serial_number`, `stnk_deadline`, `type_fuel`, `kilometer`, `cc_engine`, `description`, `lowest_price`, `price_displayed`, `status`, `user_id`, `branch_id`, `created_at`, `updated_at`, `deleted_at`, `deleted_by_branch_at`) VALUES
('K0101F', 21, 'motorcycle', 'Ea in et est velit l', '2014-03-27', '992', '2026-06-08', 'electric', 34, 13, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 500, 600, 'transaction', 1, 46, '2025-06-11 01:18:06', '2025-06-15 07:11:15', NULL, NULL),
('K0102F', 24, 'car', 'Sit ut dolor facere', '2005-08-18', '55', '2025-07-08', 'hybrid', 13, 16, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 1500, 2000, 'transaction', 1, 47, '2025-06-11 01:18:31', '2025-06-15 07:13:58', NULL, NULL),
('K0105F', 1, 'car', 'Gold', '1994-04-20', '713', '2026-11-26', 'gasoline', 1000, 525, 'Earum cum perspiciat', 1800, 2300, 'transaction', 1, 46, '2025-06-10 04:12:50', '2025-06-15 07:14:36', NULL, NULL),
('K0106F', 5, 'motorcycle', 'Merah', '1972-03-21', '893', '1992-09-12', 'gasoline', 85, 4, 'Ducimus impedit ve', 630, 1000, 'available', 1, 47, '2025-06-15 07:02:09', '2025-06-15 07:19:54', NULL, NULL),
('K0305F', 9, 'motorcycle', 'Merah', '2025-06-01', '3', '2026-06-13', 'gasoline', 1, 125, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 200, 300, 'available', 1, 46, '2025-06-13 03:02:10', '2025-06-13 03:44:05', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_documents`
--

CREATE TABLE `vehicle_documents` (
  `id` bigint NOT NULL,
  `vehicle_id` char(8) NOT NULL,
  `stnk` varchar(255) NOT NULL,
  `bpkb` varchar(255) NOT NULL,
  `service_note` varchar(255) NOT NULL,
  `nota` varchar(255) NOT NULL,
  `asuransi` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by_vehicle_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vehicle_documents`
--

INSERT INTO `vehicle_documents` (`id`, `vehicle_id`, `stnk`, `bpkb`, `service_note`, `nota`, `asuransi`, `created_at`, `updated_at`, `deleted_at`, `deleted_by_vehicle_at`) VALUES
(68, 'K0105F', 'vehicles/vehicle_K0105F/vehicle_documents/stnk_6847b413cf27a.pdf', 'vehicles/vehicle_K0105F/vehicle_documents/bpkb_6847b413cf52f.pdf', '', '', '', '2025-06-10 04:26:59', NULL, '2025-06-10 04:27:36', NULL),
(70, 'K0105F', 'vehicles/vehicle_K0105F/vehicle_documents/stnk_6847b825f14ab.pdf', 'vehicles/vehicle_K0105F/vehicle_documents/bpkb_6847b825f1aec.pdf', '', '', '', '2025-06-10 04:44:21', NULL, NULL, NULL),
(72, 'K0101F', 'vehicles/vehicle_K0101F/vehicle_documents/stnk_6848ef4e1fe37.pdf', 'vehicles/vehicle_K0101F/vehicle_documents/bpkb_6848ef4e20208.pdf', '', '', '', '2025-06-11 02:51:58', NULL, '2025-06-11 02:54:01', NULL),
(73, 'K0101F', 'vehicles/vehicle_K0101F/vehicle_documents/stnk_6848efdecb73b.pdf', 'vehicles/vehicle_K0101F/vehicle_documents/bpkb_6848efdecbaaa.pdf', 'vehicles/vehicle_K0101F/vehicle_documents/service_note_6848efdecbdd3.pdf', 'vehicles/vehicle_K0101F/vehicle_documents/nota_6848efdecc0e2.pdf', 'vehicles/vehicle_K0101F/vehicle_documents/asuransi_6848efdecc478.pdf', '2025-06-11 02:54:22', NULL, NULL, NULL),
(74, 'K0102F', 'vehicles/vehicle_K0102F/vehicle_documents/stnk_6848feecce6e1.pdf', 'vehicles/vehicle_K0102F/vehicle_documents/bpkb_6848feecced21.pdf', 'vehicles/vehicle_K0102F/vehicle_documents/service_note_6848feeccf284.pdf', 'vehicles/vehicle_K0102F/vehicle_documents/nota_6848feecd21a5.pdf', '', '2025-06-11 03:58:36', NULL, '2025-06-11 03:58:49', NULL),
(75, 'K0102F', 'vehicles/vehicle_K0102F/vehicle_documents/stnk_6848ff0fcfb45.pdf', 'vehicles/vehicle_K0102F/vehicle_documents/bpkb_6848ff0fcff09.pdf', '', '', '', '2025-06-11 03:59:11', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_loans`
--

CREATE TABLE `vehicle_loans` (
  `id` bigint NOT NULL,
  `partner_id` bigint NOT NULL,
  `vehicle_id` char(8) NOT NULL,
  `user_id` bigint NOT NULL,
  `loan_date` timestamp NOT NULL,
  `return_date` timestamp NOT NULL,
  `note` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` enum('borrowed','returned') NOT NULL DEFAULT 'borrowed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by_partner_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_models`
--

CREATE TABLE `vehicle_models` (
  `id` bigint NOT NULL,
  `brand_id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by_brand_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vehicle_models`
--

INSERT INTO `vehicle_models` (`id`, `brand_id`, `name`, `slug`, `created_at`, `updated_at`, `deleted_at`, `deleted_by_brand_at`) VALUES
(1, 7, 'Fortuner', 'fortuner', '2025-06-10 04:00:57', NULL, NULL, NULL),
(3, 7, 'Beat Series', 'beat-series', '2025-06-10 05:29:26', NULL, NULL, NULL),
(4, 7, 'New Vario 125 eSP', 'new-vario-125-esp', '2025-06-10 05:29:56', NULL, NULL, NULL),
(5, 7, 'Scoopy eSP', 'scoopy-esp', '2025-06-10 05:30:06', NULL, NULL, NULL),
(6, 7, 'New Vario 150 eSP', 'new-vario-150-esp', '2025-06-10 05:30:32', NULL, NULL, NULL),
(7, 7, 'Revo', 'revo', '2025-06-10 05:30:42', NULL, NULL, NULL),
(8, 7, 'New Vario 110 FI esp', 'new-vario-110-fi-esp', '2025-06-10 05:31:31', NULL, NULL, NULL),
(9, 7, 'CB 150 R Street Fire', 'cb-150-r-street-fire', '2025-06-10 05:31:40', NULL, NULL, NULL),
(10, 7, 'CBR 150 R', 'cbr-150-r', '2025-06-10 05:31:48', NULL, NULL, NULL),
(11, 7, 'Supra Series', 'supra-series', '2025-06-10 05:31:57', NULL, NULL, NULL),
(12, 7, 'Sonic 150 R', 'sonic-150-r', '2025-06-10 05:32:11', NULL, NULL, NULL),
(13, 7, 'Verza', 'verza', '2025-06-10 05:32:19', NULL, NULL, NULL),
(14, 3, 'Mio M3 125', 'mio-m3-125', '2025-06-10 05:36:49', NULL, NULL, NULL),
(15, 3, 'N-Maxx', 'n-maxx', '2025-06-10 05:36:58', '2025-06-10 14:18:09', NULL, NULL),
(16, 3, 'New V-ixion', 'new-v-ixion', '2025-06-10 05:37:08', NULL, NULL, NULL),
(17, 3, 'X Ride', 'x-ride', '2025-06-10 05:37:17', NULL, NULL, NULL),
(18, 3, 'MX King', 'mx-king', '2025-06-10 05:37:30', NULL, NULL, NULL),
(19, 3, 'Jupiter Series', 'jupiter-series', '2025-06-10 05:37:41', NULL, NULL, NULL),
(20, 3, 'All New Soul GT', 'all-new-soul-gt', '2025-06-10 05:37:50', NULL, NULL, NULL),
(21, 3, 'Aerox', 'aerox', '2025-06-10 05:37:59', NULL, NULL, NULL),
(22, 3, 'New Yamaha R15', 'new-yamaha-r15', '2025-06-10 05:38:08', NULL, NULL, NULL),
(23, 8, 'KLX 150', 'klx-150', '2025-06-10 05:38:33', NULL, NULL, NULL),
(24, 8, 'Ninja 250', 'ninja-250', '2025-06-10 05:38:44', NULL, NULL, NULL),
(25, 2, 'GSX-S150', 'gsx-s150', '2025-06-10 05:39:08', NULL, NULL, NULL),
(26, 2, 'GSX-R150', 'gsx-r150', '2025-06-10 05:39:18', NULL, NULL, NULL),
(27, 9, 'Dazz', 'dazz', '2025-06-10 05:42:30', NULL, NULL, NULL),
(28, 9, 'Callisto', 'callisto', '2025-06-10 05:42:49', NULL, NULL, NULL),
(29, 9, 'XL100', 'xl100', '2025-06-10 05:42:58', NULL, NULL, NULL),
(30, 9, 'Rockz', 'rockz', '2025-06-10 05:43:11', NULL, NULL, NULL),
(31, 9, 'Max 125 Sport', 'max-125-sport', '2025-06-10 05:43:20', NULL, NULL, NULL),
(32, 9, 'Ronin TD', 'ronin-td', '2025-06-10 05:43:27', NULL, NULL, NULL),
(33, 9, 'Ronin Nimbus', 'ronin-nimbus', '2025-06-10 05:43:35', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_photos`
--

CREATE TABLE `vehicle_photos` (
  `id` bigint NOT NULL,
  `vehicle_id` char(8) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by_vehicle_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vehicle_photos`
--

INSERT INTO `vehicle_photos` (`id`, `vehicle_id`, `photo_path`, `created_at`, `updated_at`, `deleted_at`, `deleted_by_vehicle_at`) VALUES
(79, 'K0105F', 'vehicles/vehicle_K0105F/vehicle_photos/photo_6847b327ae170.jpg', '2025-06-10 04:23:03', NULL, NULL, NULL),
(80, 'K0105F', 'vehicles/vehicle_K0105F/vehicle_photos/photo_6847b332ba24e.jpg', '2025-06-10 04:23:14', NULL, NULL, NULL),
(84, 'K0105F', 'vehicles/vehicle_K0105F/vehicle_photos/photo_6847b8b2b3c24.jpg', '2025-06-10 04:46:42', NULL, NULL, NULL),
(86, 'K0101F', 'vehicles/vehicle_K0101F/vehicle_photos/photo_6848ef5d245bc.jpg', '2025-06-11 02:52:13', NULL, '2025-06-11 02:53:43', NULL),
(87, 'K0101F', 'vehicles/vehicle_K0101F/vehicle_photos/photo_6848ef67b01d4.jpg', '2025-06-11 02:52:23', NULL, NULL, NULL),
(88, 'K0101F', 'vehicles/vehicle_K0101F/vehicle_photos/photo_6848ef7138af1.jpg', '2025-06-11 02:52:33', NULL, NULL, NULL),
(89, 'K0101F', 'vehicles/vehicle_K0101F/vehicle_photos/photo_6848efaca99a5.jpg', '2025-06-11 02:53:32', NULL, NULL, NULL),
(90, 'K0101F', 'vehicles/vehicle_K0101F/vehicle_photos/photo_6848efb2d49bd.jpg', '2025-06-11 02:53:38', NULL, NULL, NULL),
(91, 'K0101F', 'vehicles/vehicle_K0101F/vehicle_photos/photo_6848efbf6d212.jpg', '2025-06-11 02:53:51', NULL, NULL, NULL),
(92, 'K0102F', 'vehicles/vehicle_K0102F/vehicle_photos/photo_6848ff4028493.jpg', '2025-06-11 04:00:00', NULL, NULL, NULL),
(93, 'K0102F', 'vehicles/vehicle_K0102F/vehicle_photos/photo_6848ff4df40d3.jpg', '2025-06-11 04:00:14', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_name_unique` (`name`);

--
-- Indexes for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`),
  ADD UNIQUE KEY `customers_phone_unique` (`phone`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_session_id` (`chat_session_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_vehicles` (`vehicle_id`),
  ADD KEY `fk_orders_customers` (`customer_id`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_drivers`
--
ALTER TABLE `test_drivers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_test_drivers_orders` (`order_id`),
  ADD KEY `fk_test_drivers_users` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transactions_orders` (`order_id`),
  ADD KEY `fk_transactions_users` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `fk_vehicles_user` (`user_id`),
  ADD KEY `fk_vehicles_branch` (`branch_id`),
  ADD KEY `fk_vehicles_to_models` (`vehicle_model_id`);

--
-- Indexes for table `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vehicle_documents_vehicle_id` (`vehicle_id`);

--
-- Indexes for table `vehicle_loans`
--
ALTER TABLE `vehicle_loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vehicle_loans_partner` (`partner_id`),
  ADD KEY `fk_vehicle_loans_user` (`user_id`),
  ADD KEY `fk_vehicle_loans_vehicle_id` (`vehicle_id`);

--
-- Indexes for table `vehicle_models`
--
ALTER TABLE `vehicle_models`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_models_to_brands` (`brand_id`);

--
-- Indexes for table `vehicle_photos`
--
ALTER TABLE `vehicle_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vehicle_photos_vehicle_id` (`vehicle_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `test_drivers`
--
ALTER TABLE `test_drivers`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `vehicle_loans`
--
ALTER TABLE `vehicle_loans`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `vehicle_models`
--
ALTER TABLE `vehicle_models`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `vehicle_photos`
--
ALTER TABLE `vehicle_photos`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customers` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orders_vehicles` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `test_drivers`
--
ALTER TABLE `test_drivers`
  ADD CONSTRAINT `fk_test_drivers_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_test_drivers_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transactions_to_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_transactions_to_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `fk_vehicles_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vehicles_to_models` FOREIGN KEY (`vehicle_model_id`) REFERENCES `vehicle_models` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vehicles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  ADD CONSTRAINT `fk_vehicle_documents_vehicle_id` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_loans`
--
ALTER TABLE `vehicle_loans`
  ADD CONSTRAINT `fk_vehicle_loans_partner` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vehicle_loans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vehicle_loans_vehicle_id` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_models`
--
ALTER TABLE `vehicle_models`
  ADD CONSTRAINT `fk_models_to_brands` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_photos`
--
ALTER TABLE `vehicle_photos`
  ADD CONSTRAINT `fk_vehicle_photos_vehicle_id` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
