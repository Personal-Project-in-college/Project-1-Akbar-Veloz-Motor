-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 07 Jul 2025 pada 10.59
-- Versi server: 8.0.30
-- Versi PHP: 8.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `personalprojectincollege_project_1_akbar_veloz_motor`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `banks`
--

CREATE TABLE `banks` (
  `id` bigint NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `account_number` varchar(30) NOT NULL,
  `account_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `banners`
--

CREATE TABLE `banners` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `branches`
--

CREATE TABLE `branches` (
  `id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `address` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `branches`
--

INSERT INTO `branches` (`id`, `name`, `slug`, `address`, `created_at`, `updated_at`, `deleted_at`) VALUES
(46, 'Utama', 'utama', 'Street:  Jl Raya Pintu I TMII Ged Pewayangan Kautaman, Jakarta\r\n\r\nCity:   Jakarta\r\n\r\nState/province/area:    Jakarta\r\n\r\nPhone number:  021-87799510\r\n\r\nZip code:  13560\r\n\r\nCountry calling code:  +62\r\n\r\nCountry:  Indonesia', '2025-06-10 02:25:17', '2025-06-10 05:09:47', NULL),
(47, 'Kedua', 'kedua', 'Street:  Jl Angkasa 1 Halim Perdana Kusumah, Dki Jakarta\r\n\r\nCity:   Dki Jakarta\r\n\r\nState/province/area:    Jakarta\r\n\r\nPhone number:  021-80880070\r\n\r\nZip code:  13610\r\n\r\nCountry calling code:  +62\r\n\r\nCountry:  Indonesia', '2025-06-10 05:10:28', NULL, NULL),
(50, 'Ketika', 'ketika', 'Street:  Jl Kyai Caringin 12 B, Dki Jakarta\r\n\r\nCity:   Dki Jakarta\r\n\r\nState/province/area:    Jakarta\r\n\r\nPhone number:  021-63863440\r\n\r\nZip code:  10150\r\n\r\nCountry calling code:  +62\r\n\r\nCountry:  Indonesia', '2025-06-10 17:32:04', NULL, NULL),
(51, 'Keempat', 'keempat', 'Street:  Jl Raya Rawakuning RT 16/02, Dki Jakarta\r\n\r\nCity:   Dki Jakarta\r\n\r\nState/province/area:    Jakarta\r\n\r\nPhone number:  0-21-480-3658\r\n\r\nZip code:  13950\r\n\r\nCountry calling code:  +62\r\n\r\nCountry:  Indonesia', '2025-06-10 17:32:19', NULL, NULL),
(52, 'Kelima', 'kelima', 'Street:  Jl Jend Sudirman 3603, Sumatera Selatan\r\n\r\nCity:   Sumatera Selatan\r\n\r\nState/province/area:    Palembang\r\n\r\nPhone number:  0-711-36-8462\r\n\r\nZip code:  30253\r\n\r\nCountry calling code:  +62\r\n\r\nCountry:  Indonesia', '2025-06-10 17:32:34', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `brands`
--

CREATE TABLE `brands` (
  `id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `brands`
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
(61, 'Daihatsu', 'daihatsu', '2025-06-20 02:30:10', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat_sessions`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `chat_sessions`
--

INSERT INTO `chat_sessions` (`session_id`, `customer_id`, `user_id`, `status`, `started_at`, `closed_at`, `last_customer_activity`, `customer_typing`, `user_typing`) VALUES
('28236143-18a4-4a44-b06f-5e821f43604c', 59, NULL, 'closed', '2025-07-06 10:47:36', NULL, '2025-07-07 08:40:49', 0, 0),
('36a8d58c-c273-44b8-a4b2-99c78d7f6215', 41, NULL, 'pending', '2025-06-23 03:54:55', NULL, '2025-06-23 20:17:28', 0, 0),
('3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, NULL, 'pending', '2025-06-23 21:17:48', NULL, '2025-06-25 03:51:11', 0, 0),
('8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 1, 'closed', '2025-07-05 22:39:20', NULL, NULL, 0, 0),
('c347fe03-35e0-458e-b04e-f9ae27e6a471', 43, NULL, 'closed', '2025-06-25 10:10:19', NULL, NULL, 0, 0),
('dfc94af9-147e-4dcd-9da8-66c397a50284', 2, NULL, 'closed', '2025-07-07 08:42:34', NULL, '2025-07-07 08:42:37', 0, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `is_logged_in` tinyint(1) NOT NULL DEFAULT '0',
  `username` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `address` text,
  `registration_method` enum('manual','google','facebook') NOT NULL DEFAULT 'manual',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_banned` tinyint(1) NOT NULL DEFAULT '0',
  `login_attempts` int NOT NULL DEFAULT '0',
  `last_attempt_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `customers`
--

INSERT INTO `customers` (`id`, `name`, `slug`, `email`, `phone`, `password`, `google_id`, `facebook_id`, `is_logged_in`, `username`, `picture`, `address`, `registration_method`, `email_verified_at`, `is_banned`, `login_attempts`, `last_attempt_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Zaki', 'zaki', 'zacki@gmail.com', '0892112', '$2y$12$lOHqxUcAMXBNbvLIKDN6nuNM448bwDlvBfviVN3vcljmhGm5U9TLS', NULL, NULL, 0, NULL, NULL, 'qq', 'manual', '2025-07-06 10:47:35', 1, 0, NULL, '2025-06-13 02:01:19', '2025-07-07 08:40:35', NULL),
(3, 'Eful', 'eful', 'eful@gmail.com', '089211223', NULL, NULL, NULL, 0, NULL, NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'manual', '2025-07-06 10:47:35', 1, 0, NULL, '2025-06-14 00:39:47', NULL, NULL),
(59, 'Givi Boy', 'givi-boy', 'farhantriputrawisnu@gmail.com', '+62 745634564', NULL, '116923222700550539651', NULL, 0, 'Givi Boy', NULL, 'asfsfsdfsdfs', 'google', NULL, 0, 0, NULL, '2025-07-06 10:47:35', '2025-07-06 10:48:46', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `customer_otps`
--

CREATE TABLE `customer_otps` (
  `id` int NOT NULL,
  `customer_id` bigint NOT NULL,
  `otp_code` varchar(10) NOT NULL,
  `expired_at` datetime NOT NULL,
  `resend_count` int DEFAULT '0',
  `last_sent_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `messages`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `messages`
--

INSERT INTO `messages` (`id`, `chat_session_id`, `sender_id`, `sender_type`, `message_text`, `timestamp`, `is_read_by_user`, `is_read_by_customer`) VALUES
(40, '36a8d58c-c273-44b8-a4b2-99c78d7f6215', 41, 'customer', 'hallo', '2025-06-23 16:10:05', 1, 1),
(41, '36a8d58c-c273-44b8-a4b2-99c78d7f6215', 41, 'customer', 'Motor dengan Budget Murah!', '2025-06-23 16:10:23', 1, 1),
(42, '36a8d58c-c273-44b8-a4b2-99c78d7f6215', 41, 'bot', '<strong>Pilih kendaraan untuk dinegosiasikan:</strong>\n        <div class=\"search-container\">\n            <input type=\"text\" id=\"vehicleSearchInput\" placeholder=\"Cari kendaraan...\">\n            <button id=\"clearSearchButton\" style=\"display:none;\">X</button>\n        </div>\n        <p id=\"noVehicleResultsMessage\" style=\"display:none; color: gray; text-align: center; margin-top: 10px;\">Tidak ada kendaraan ditemukan.</p>\n        <div id=\"negotiableVehicleListContainer\">\n            <div class=\"promo-card vehicle-option\" data-id=\"B33SAM\" data-vehicle-name=\"B33SAM - Bentley Flying Spur\">\n                            <h4>B33SAM - Bentley Flying Spur</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B33SAM/vehicle_photos/photo_6858b7f9b542c.webp\" alt=\"Bentley Flying Spur\" >\n                            <p>Harga: Rp1.695.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B7631NGB\" data-vehicle-name=\"B7631NGB - BMW 320i\">\n                            <h4>B7631NGB - BMW 320i</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B7631NGB/vehicle_photos/photo_6858b3d74a993.webp\" alt=\"BMW 320i\" >\n                            <p>Harga: Rp288.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"D4276ZSF\" data-vehicle-name=\"D4276ZSF - Daihatsu Xenia\">\n                            <h4>D4276ZSF - Daihatsu Xenia</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_D4276ZSF/vehicle_photos/photo_6854c8c0e0fb5.jpg\" alt=\"Daihatsu Xenia\" >\n                            <p>Harga: Rp180.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B9083ESW\" data-vehicle-name=\"B9083ESW - Ford Ranger Double Cabin\">\n                            <h4>B9083ESW - Ford Ranger Double Cabin</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B9083ESW/vehicle_photos/photo_6858b60c3ac40.webp\" alt=\"Ford Ranger Double Cabin\" >\n                            <p>Harga: Rp305.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"T5682THQ\" data-vehicle-name=\"T5682THQ - Honda CR V\">\n                            <h4>T5682THQ - Honda CR V</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_T5682THQ/vehicle_photos/photo_6854cab68e74a.jpg\" alt=\"Honda CR V\" >\n                            <p>Harga: Rp230.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"T4511YQ\" data-vehicle-name=\"T4511YQ - Honda CRF\">\n                            <h4>T4511YQ - Honda CRF</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_T4511YQ/vehicle_photos/photo_6854d9ca31e58.webp\" alt=\"Honda CRF\" >\n                            <p>Harga: Rp63.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B4224TKW\" data-vehicle-name=\"B4224TKW - Honda Fortuner\">\n                            <h4>B4224TKW - Honda Fortuner</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B4224TKW/vehicle_photos/photo_6854c6465966e.jpg\" alt=\"Honda Fortuner\" >\n                            <p>Harga: Rp348.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B6514ZN0\" data-vehicle-name=\"B6514ZN0 - Honda PCX\">\n                            <h4>B6514ZN0 - Honda PCX</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B6514ZN0/vehicle_photos/photo_6854d25b50b4e.webp\" alt=\"Honda PCX\" >\n                            <p>Harga: Rp25.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"D4532TXZ\" data-vehicle-name=\"D4532TXZ - Kawasaki Ninja 250\">\n                            <h4>D4532TXZ - Kawasaki Ninja 250</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_D4532TXZ/vehicle_photos/photo_6854d832e676e.webp\" alt=\"Kawasaki Ninja 250\" >\n                            <p>Harga: Rp50.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B8931SLT\" data-vehicle-name=\"B8931SLT - Lamborgini Gallado\">\n                            <h4>B8931SLT - Lamborgini Gallado</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B8931SLT/vehicle_photos/photo_6854d50bba314.webp\" alt=\"Lamborgini Gallado\" >\n                            <p>Harga: Rp3.399.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"D4444KKK\" data-vehicle-name=\"D4444KKK - Lamborgini URUS\">\n                            <h4>D4444KKK - Lamborgini URUS</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_D4444KKK/vehicle_photos/photo_6854d6bf8a665.webp\" alt=\"Lamborgini URUS\" >\n                            <p>Harga: Rp8.500.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B3566LAA\" data-vehicle-name=\"B3566LAA - Suzuki GSX-R150\">\n                            <h4>B3566LAA - Suzuki GSX-R150</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B3566LAA/vehicle_photos/photo_6854d0064ebc0.webp\" alt=\"Suzuki GSX-R150\" >\n                            <p>Harga: Rp169.850.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B8788KJA\" data-vehicle-name=\"B8788KJA - Wuling Air Ev\">\n                            <h4>B8788KJA - Wuling Air Ev</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B8788KJA/vehicle_photos/photo_6858bae29b0b8.webp\" alt=\"Wuling Air Ev\" >\n                            <p>Harga: Rp158.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B4328HGA\" data-vehicle-name=\"B4328HGA - Yamaha N-Maxx\">\n                            <h4>B4328HGA - Yamaha N-Maxx</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B4328HGA/vehicle_photos/photo_6854cd9bb4857.webp\" alt=\"Yamaha N-Maxx\" >\n                            <p>Harga: Rp23.300.000</p>\n                        </div>\n        </div>\n    ', '2025-06-23 16:10:23', 1, 1),
(43, '36a8d58c-c273-44b8-a4b2-99c78d7f6215', 41, 'bot', '\n<strong>Anda memilih Yamaha N-Maxx</strong>\n<div class=\"vehicle-card-chat promo-card\">\n<h4>B4328HGA - Yamaha N-Maxx</h4>\n<img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B4328HGA/vehicle_photos/photo_6854cd9bb4857.webp\" alt=\"Yamaha N-Maxx\" >\n<p><strong>Harga:</strong> Rp23.300.000</p>\n</div>\n<p>Silakan masukkan penawaran harga Anda (dalam Rp):</p>\n', '2025-06-23 16:10:41', 1, 1),
(44, '36a8d58c-c273-44b8-a4b2-99c78d7f6215', 41, 'customer', 'Saya menawarkan Rp22.000.000 untuk B4328HGA - Yamaha N-Maxx.', '2025-06-23 16:10:49', 1, 1),
(45, '36a8d58c-c273-44b8-a4b2-99c78d7f6215', 41, 'bot', '<strong>Penawaran DITERIMA!</strong><p>Penawaran Anda Rp22.000.000 untuk B4328HGA - Yamaha N-Maxx diterima.</p><button class=\"negotiation-btn\" data-action=\"testDrive\" data-vehicle-id=\"B4328HGA\" data-negotiated-price=\"22000000\">Lanjut Test Drive</button><button class=\"negotiation-btn\" data-action=\"continueTransaction\" data-vehicle-id=\"B4328HGA\" data-negotiated-price=\"22000000\">Lanjut Transaksi</button>', '2025-06-23 16:10:49', 1, 1),
(46, '36a8d58c-c273-44b8-a4b2-99c78d7f6215', 41, 'bot', 'Penawaran DITERIMA untuk B4328HGA - Yamaha N-Maxx. Nominal: Rp22.000.000. Pelanggan diminta Klik \"Lanjut Transaksi\" atau \"Lanjut Test Drive\".', '2025-06-23 16:10:49', 1, 1),
(47, '36a8d58c-c273-44b8-a4b2-99c78d7f6215', 41, 'bot', 'Order Transaksi Anda telah berhasil dibuat untuk kendaraan B4328HGA! Kami akan mengarahkan Anda ke halaman hubungi kami.', '2025-06-23 16:10:59', 1, 1),
(48, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'customer', 'Apa itu Akbar Veloz Motor?', '2025-06-24 21:21:30', 1, 1),
(49, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'bot', 'Akbar Veloz Motor adalah dealer motor terkemuka yang menyediakan berbagai jenis motor berkualitas tinggi. Kami berkomitmen memberikan pelayanan terbaik dan harga kompetitif. Kunjungi website kami untuk info lebih lanjut!', '2025-06-24 21:21:30', 1, 1),
(50, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'customer', 'Rekomendasi Kendaraan Akhir Tahun', '2025-06-24 21:21:32', 1, 1),
(51, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'bot', 'Jangan lewatkan promo khusus akhir tahun kami! Dapatkan diskon menarik, cashback, dan cicilan ringan untuk motor impian Anda. Penawaran terbatas!', '2025-06-24 21:21:32', 1, 1),
(52, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'customer', 'Motor dengan Budget Murah!', '2025-06-24 21:21:37', 1, 1),
(53, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'bot', '<strong>Pilih kendaraan untuk dinegosiasikan:</strong>\n        <div class=\"search-container\">\n            <input type=\"text\" id=\"vehicleSearchInput\" placeholder=\"Cari kendaraan...\">\n            <button id=\"clearSearchButton\" style=\"display:none;\">X</button>\n        </div>\n        <p id=\"noVehicleResultsMessage\" style=\"display:none; color: gray; text-align: center; margin-top: 10px;\">Tidak ada kendaraan ditemukan.</p>\n        <div id=\"negotiableVehicleListContainer\">\n            <div class=\"promo-card vehicle-option\" data-id=\"B33SAM\" data-vehicle-name=\"B33SAM - Bentley Flying Spur\">\n                            <h4>B33SAM - Bentley Flying Spur</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B33SAM/vehicle_photos/photo_6858b7f9b542c.webp\" alt=\"Bentley Flying Spur\" >\n                            <p>Harga: Rp1.695.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B7631NGB\" data-vehicle-name=\"B7631NGB - BMW 320i\">\n                            <h4>B7631NGB - BMW 320i</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B7631NGB/vehicle_photos/photo_6858b3d74a993.webp\" alt=\"BMW 320i\" >\n                            <p>Harga: Rp288.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B9083ESW\" data-vehicle-name=\"B9083ESW - Ford Ranger Double Cabin\">\n                            <h4>B9083ESW - Ford Ranger Double Cabin</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B9083ESW/vehicle_photos/photo_6858b60c3ac40.webp\" alt=\"Ford Ranger Double Cabin\" >\n                            <p>Harga: Rp305.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"T5682THQ\" data-vehicle-name=\"T5682THQ - Honda CR V\">\n                            <h4>T5682THQ - Honda CR V</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_T5682THQ/vehicle_photos/photo_6854cab68e74a.jpg\" alt=\"Honda CR V\" >\n                            <p>Harga: Rp230.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"T4511YQ\" data-vehicle-name=\"T4511YQ - Honda CRF\">\n                            <h4>T4511YQ - Honda CRF</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_T4511YQ/vehicle_photos/photo_6854d9ca31e58.webp\" alt=\"Honda CRF\" >\n                            <p>Harga: Rp63.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B4224TKW\" data-vehicle-name=\"B4224TKW - Honda Fortuner\">\n                            <h4>B4224TKW - Honda Fortuner</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B4224TKW/vehicle_photos/photo_6854c6465966e.jpg\" alt=\"Honda Fortuner\" >\n                            <p>Harga: Rp348.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"D4532TXZ\" data-vehicle-name=\"D4532TXZ - Kawasaki Ninja 250\">\n                            <h4>D4532TXZ - Kawasaki Ninja 250</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_D4532TXZ/vehicle_photos/photo_6854d832e676e.webp\" alt=\"Kawasaki Ninja 250\" >\n                            <p>Harga: Rp50.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B8931SLT\" data-vehicle-name=\"B8931SLT - Lamborgini Gallado\">\n                            <h4>B8931SLT - Lamborgini Gallado</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B8931SLT/vehicle_photos/photo_6854d50bba314.webp\" alt=\"Lamborgini Gallado\" >\n                            <p>Harga: Rp3.399.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"D4444KKK\" data-vehicle-name=\"D4444KKK - Lamborgini URUS\">\n                            <h4>D4444KKK - Lamborgini URUS</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_D4444KKK/vehicle_photos/photo_6854d6bf8a665.webp\" alt=\"Lamborgini URUS\" >\n                            <p>Harga: Rp8.500.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B8788KJA\" data-vehicle-name=\"B8788KJA - Wuling Air Ev\">\n                            <h4>B8788KJA - Wuling Air Ev</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B8788KJA/vehicle_photos/photo_6858bae29b0b8.webp\" alt=\"Wuling Air Ev\" >\n                            <p>Harga: Rp158.000.000</p>\n                        </div>\n        </div>\n    ', '2025-06-24 21:21:37', 1, 1),
(54, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'bot', '\n<strong>Anda memilih Honda CRF</strong>\n<div class=\"vehicle-card-chat promo-card\">\n<h4>T4511YQ - Honda CRF</h4>\n<img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_T4511YQ/vehicle_photos/photo_6854d9ca31e58.webp\" alt=\"Honda CRF\" >\n<p><strong>Harga:</strong> Rp63.000.000</p>\n</div>\n<p>Silakan masukkan penawaran harga Anda (dalam Rp):</p>\n', '2025-06-24 21:21:47', 1, 1),
(55, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'customer', 'Saya menawarkan Rp6.500.000 untuk T4511YQ - Honda CRF.', '2025-06-24 21:22:03', 1, 1),
(56, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'bot', '<strong>Penawaran DITOLAK</strong><p>Maaf, penawaran Rp6.500.000 untuk T4511YQ - Honda CRF terlalu rendah.</p><button class=\"negotiation-btn\" data-action=\"tryAgain\">Coba Lagi</button><button class=\"negotiation-btn\" data-action=\"selectOtherVehicle\">Pilih Kendaraan Lain</button>', '2025-06-24 21:22:03', 1, 1),
(57, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'bot', 'Penawaran DITOLAK untuk T4511YQ - Honda CRF. Nominal: Rp6.500.000. Terlalu rendah.', '2025-06-24 21:22:03', 1, 1),
(58, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'bot', '<strong>Pilih kendaraan untuk dinegosiasikan:</strong>\n        <div class=\"search-container\">\n            <input type=\"text\" id=\"vehicleSearchInput\" placeholder=\"Cari kendaraan...\">\n            <button id=\"clearSearchButton\" style=\"display:none;\">X</button>\n        </div>\n        <p id=\"noVehicleResultsMessage\" style=\"display:none; color: gray; text-align: center; margin-top: 10px;\">Tidak ada kendaraan ditemukan.</p>\n        <div id=\"negotiableVehicleListContainer\">\n            <div class=\"promo-card vehicle-option\" data-id=\"B33SAM\" data-vehicle-name=\"B33SAM - Bentley Flying Spur\">\n                            <h4>B33SAM - Bentley Flying Spur</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B33SAM/vehicle_photos/photo_6858b7f9b542c.webp\" alt=\"Bentley Flying Spur\" >\n                            <p>Harga: Rp1.695.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B7631NGB\" data-vehicle-name=\"B7631NGB - BMW 320i\">\n                            <h4>B7631NGB - BMW 320i</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B7631NGB/vehicle_photos/photo_6858b3d74a993.webp\" alt=\"BMW 320i\" >\n                            <p>Harga: Rp288.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B9083ESW\" data-vehicle-name=\"B9083ESW - Ford Ranger Double Cabin\">\n                            <h4>B9083ESW - Ford Ranger Double Cabin</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B9083ESW/vehicle_photos/photo_6858b60c3ac40.webp\" alt=\"Ford Ranger Double Cabin\" >\n                            <p>Harga: Rp305.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"T5682THQ\" data-vehicle-name=\"T5682THQ - Honda CR V\">\n                            <h4>T5682THQ - Honda CR V</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_T5682THQ/vehicle_photos/photo_6854cab68e74a.jpg\" alt=\"Honda CR V\" >\n                            <p>Harga: Rp230.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"T4511YQ\" data-vehicle-name=\"T4511YQ - Honda CRF\">\n                            <h4>T4511YQ - Honda CRF</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_T4511YQ/vehicle_photos/photo_6854d9ca31e58.webp\" alt=\"Honda CRF\" >\n                            <p>Harga: Rp63.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B4224TKW\" data-vehicle-name=\"B4224TKW - Honda Fortuner\">\n                            <h4>B4224TKW - Honda Fortuner</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B4224TKW/vehicle_photos/photo_6854c6465966e.jpg\" alt=\"Honda Fortuner\" >\n                            <p>Harga: Rp348.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"D4532TXZ\" data-vehicle-name=\"D4532TXZ - Kawasaki Ninja 250\">\n                            <h4>D4532TXZ - Kawasaki Ninja 250</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_D4532TXZ/vehicle_photos/photo_6854d832e676e.webp\" alt=\"Kawasaki Ninja 250\" >\n                            <p>Harga: Rp50.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B8931SLT\" data-vehicle-name=\"B8931SLT - Lamborgini Gallado\">\n                            <h4>B8931SLT - Lamborgini Gallado</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B8931SLT/vehicle_photos/photo_6854d50bba314.webp\" alt=\"Lamborgini Gallado\" >\n                            <p>Harga: Rp3.399.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"D4444KKK\" data-vehicle-name=\"D4444KKK - Lamborgini URUS\">\n                            <h4>D4444KKK - Lamborgini URUS</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_D4444KKK/vehicle_photos/photo_6854d6bf8a665.webp\" alt=\"Lamborgini URUS\" >\n                            <p>Harga: Rp8.500.000.000</p>\n                        </div><div class=\"promo-card vehicle-option\" data-id=\"B8788KJA\" data-vehicle-name=\"B8788KJA - Wuling Air Ev\">\n                            <h4>B8788KJA - Wuling Air Ev</h4>\n                            <img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B8788KJA/vehicle_photos/photo_6858bae29b0b8.webp\" alt=\"Wuling Air Ev\" >\n                            <p>Harga: Rp158.000.000</p>\n                        </div>\n        </div>\n    ', '2025-06-24 21:22:09', 1, 1),
(59, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'bot', '\n<strong>Anda memilih Wuling Air Ev</strong>\n<div class=\"vehicle-card-chat promo-card\">\n<h4>B8788KJA - Wuling Air Ev</h4>\n<img class=\"promo-card-img\" src=\"http://project-1-akbar-veloz-motor.com/storage/vehicles/vehicle_B8788KJA/vehicle_photos/photo_6858bae29b0b8.webp\" alt=\"Wuling Air Ev\" >\n<p><strong>Harga:</strong> Rp158.000.000</p>\n</div>\n<p>Silakan masukkan penawaran harga Anda (dalam Rp):</p>\n', '2025-06-25 03:31:28', 1, 1),
(60, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'customer', 'Saya menawarkan Rp4.323.443 untuk B8788KJA - Wuling Air Ev.', '2025-06-25 03:33:34', 1, 1),
(61, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'bot', '<strong>Penawaran DITOLAK</strong><p>Maaf, penawaran Rp4.323.443 untuk B8788KJA - Wuling Air Ev terlalu rendah.</p><button class=\"negotiation-btn\" data-action=\"tryAgain\">Coba Lagi</button><button class=\"negotiation-btn\" data-action=\"selectOtherVehicle\">Pilih Kendaraan Lain</button>', '2025-06-25 03:33:34', 1, 1),
(62, '3df64cc5-8a6e-4e3f-9f8c-4cda26510a6a', 43, 'bot', 'Penawaran DITOLAK untuk B8788KJA - Wuling Air Ev. Nominal: Rp4.323.443. Terlalu rendah.', '2025-06-25 03:33:34', 1, 1),
(63, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'customer', 'Hallo', '2025-07-05 22:39:48', 1, 1),
(64, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 1, 'user', 'Iya hallo', '2025-07-05 22:40:05', 1, 1),
(65, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'customer', 'Apa itu akbar veloz motor', '2025-07-05 22:41:08', 1, 1),
(66, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'bot', 'Akbar Veloz Motor adalah dealer kendaraan yang siap melayani Anda dengan ramah dan informatif.', '2025-07-05 22:41:10', 1, 1),
(67, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'customer', 'Bagaimana melakukan transaksi di akbar veloz ?', '2025-07-05 22:41:32', 1, 1),
(68, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'bot', 'Untuk melakukan transaksi di Akbar Veloz Motor, ada dua cara yang bisa Anda pilih:\n\n1.  **Melalui Halaman &#039;Hubungi Kami&#039; di Website:** Anda bisa mengunjungi website kami dan mengisi formulir di halaman &#039;Hubungi Kami&#039;. Tim kami akan segera menghubungi Anda.\n\n2.  **Langsung Lewat Chat Ini:** Anda juga bisa langsung memulai negosiasi dengan kami melalui chat ini. Kami siap membantu Anda dalam proses transaksi.', '2025-07-05 22:41:34', 1, 1),
(69, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'customer', 'mulai negoisasi', '2025-07-05 22:41:45', 1, 1),
(70, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'bot', 'Untuk melakukan negosiasi klik opsi Motor dengan Budget Murah!', '2025-07-05 22:41:47', 1, 1),
(71, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'bot', '<strong>Cari motor sesuai budget?</strong><div class=\"promo-card\" data-target=\"budget\"><h4>Motor dengan Budget Murah!</h4><p>Temukan motor sesuai budget Anda</p></div>', '2025-07-05 22:41:47', 1, 1),
(72, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'customer', 'Motor dengan Budget Murah!', '2025-07-05 22:41:51', 1, 1),
(73, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'customer', 'apa itu test drive', '2025-07-05 22:42:07', 1, 1),
(74, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'bot', 'Test drive adalah kesempatan yang diberikan kepada calon pembeli untuk mencoba langsung kendaraan yang diminati sebelum memutuskan untuk membeli. Tujuannya agar Anda bisa merasakan performa, kenyamanan, dan fitur-fitur kendaraan tersebut secara langsung.', '2025-07-05 22:42:09', 1, 1),
(75, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'customer', '1+1', '2025-07-05 22:42:16', 1, 1),
(76, '8004b0a4-7fcd-433d-9672-eb266c25fb0e', 43, 'bot', '2', '2025-07-05 22:42:18', 1, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` bigint NOT NULL,
  `customer_id` bigint NOT NULL,
  `vehicle_id` char(10) NOT NULL,
  `negotiated_price` int DEFAULT '0',
  `type_order` enum('test_driver','transaction') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type_arrival` enum('showroom','home_visit') NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('cancelled','proced','finished') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'proced',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `vehicle_id`, `negotiated_price`, `type_order`, `type_arrival`, `order_date`, `status`, `is_read`, `created_at`, `updated_at`, `deleted_at`) VALUES
(197, 2, 'D4276ZSF', 0, 'transaction', 'showroom', '2025-06-24 00:13:54', 'finished', 0, '2025-06-24 00:13:54', '2025-06-24 00:14:32', NULL),
(202, 59, 'B6514ZN0', 0, 'test_driver', 'showroom', '2025-08-04 13:00:00', 'finished', 0, '2025-07-06 10:48:46', '2025-07-06 10:54:58', NULL),
(203, 59, 'B6514ZN0', 0, 'transaction', 'showroom', '2025-07-06 10:56:52', 'proced', 0, '2025-07-06 10:56:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `partners`
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
  `address_ktp` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address_domicile` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `partners`
--

INSERT INTO `partners` (`id`, `name`, `slug`, `nik`, `phone`, `email`, `ktp_scan`, `photo`, `address_ktp`, `address_domicile`, `created_at`, `updated_at`, `deleted_at`) VALUES
(34, 'Epi Halimah', 'epi-halimah', '9835134534564568', '089876543111', 'epihalimah@gmail.com', 'partners/partners_epi-halimah/ktp/ktp_scan_686b8438da528.png', 'partners/partners_epi-halimah/photo/photo_686b843904b02.jpg', 'Jl. Sukamulya, Padaasih, Kec. Cibogo, Kabupaten Subang, Jawa Barat 41285', 'Jl. Sukamulya, Padaasih, Kec. Cibogo, Kabupaten Subang, Jawa Barat 41285', '2025-07-07 08:24:25', NULL, NULL),
(35, 'Andi Hermawan', 'andi-hermawan', '7183104534564568', '083812543211', 'andihermawan@gmail.com', 'partners/partners_andi-hermawan/ktp/ktp_scan_686b8cf659889.png', 'partners/partners_andi-hermawan/photo/photo_686b8cf6671ec.jpg', 'Kalijati Tim., Kec. Kalijati, Kabupaten Subang, Jawa Barat 41271', 'Kalijati Tim., Kec. Kalijati, Kabupaten Subang, Jawa Barat 41271', '2025-07-07 09:01:42', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Owner', '2025-04-15 11:01:19', NULL, NULL),
(2, 'Employe', '2025-05-20 01:32:04', '2025-05-20 23:04:04', NULL),
(11, 'Karyawan', '2025-06-19 07:55:54', NULL, '2025-06-19 08:02:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_drivers`
--

CREATE TABLE `test_drivers` (
  `id` bigint NOT NULL,
  `order_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `status` enum('cancelled','process','finish') NOT NULL DEFAULT 'process',
  `result_note` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `test_drivers`
--

INSERT INTO `test_drivers` (`id`, `order_id`, `user_id`, `status`, `result_note`, `created_at`, `updated_at`, `deleted_at`) VALUES
(83, 202, 1, 'finish', 'Motor Kece', '2025-07-06 10:48:50', '2025-07-06 10:54:58', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `order_id` bigint NOT NULL COMMENT 'Relasi ke order yang dibuat customer',
  `user_id` bigint DEFAULT NULL COMMENT 'Sales/staff yang memproses transaksi',
  `vehicle_price` bigint NOT NULL,
  `deal_negotiation` bigint DEFAULT NULL,
  `grand_total` bigint DEFAULT NULL COMMENT 'Total harga kendaraan setelah negoisasi',
  `payment_type` enum('tunai','cicilan') NOT NULL COMMENT 'Jenis pembayaran lunas atau kredit',
  `down_payment` bigint DEFAULT NULL COMMENT 'Jumlah DP yang dibayar, hanya jika tipe cicilan',
  `remaining_amount` bigint DEFAULT NULL COMMENT 'Sisa uang yang akan dibayarkan oleh bank',
  `payment_method` enum('cash','transfer','midtrans') NOT NULL COMMENT 'Metode pembayaran yang digunakan',
  `status` enum('pending','paid','dp_paid','failed','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Status terbaru dari transaksi',
  `payment_gateway_ref` varchar(255) DEFAULT NULL COMMENT 'ID/Referensi unik dari payment gateway seperti Midtrans',
  `payment_proof` varchar(255) DEFAULT NULL COMMENT 'Path ke file bukti bayar untuk metode transfer manual',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel untuk mencatat semua transaksi penjualan kendaraan';

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `order_id`, `user_id`, `vehicle_price`, `deal_negotiation`, `grand_total`, `payment_type`, `down_payment`, `remaining_amount`, `payment_method`, `status`, `payment_gateway_ref`, `payment_proof`, `created_at`, `updated_at`, `deleted_at`) VALUES
(66, 197, 1, 180000000, 175000000, 175000000, 'cicilan', 35000000, 140000000, 'cash', 'paid', NULL, 'storage/transactions/transaction_zaki/transaction_20250624_071354/payment_proof_1750724072.jpg', '2025-06-24 00:13:54', '2025-07-06 15:08:03', NULL),
(70, 203, 1, 25000000, 0, NULL, 'tunai', NULL, NULL, 'cash', 'cancelled', NULL, NULL, '2025-07-06 10:56:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` bigint NOT NULL,
  `is_online` tinyint(1) NOT NULL DEFAULT '0',
  `last_activity` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by_role_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `slug`, `phone`, `photo`, `address`, `username`, `password`, `role_id`, `is_online`, `last_activity`, `created_at`, `updated_at`, `deleted_at`, `deleted_by_role_at`) VALUES
(1, 'Rudiger Madrid', 'rudiger-madrid', '081234567890', 'users/user_rudiger-madrid/photo_684adbbf5e6c0.jpg', 'Jl. Merdeka No.123', 'owner', '$2y$12$xzpCLvayI7GU2mXNJ.x6qOkKuRrRU0VhEebeV3Xq.s/1kHeUBVoHa', 1, 1, '2025-07-07 08:43:27', '2025-04-15 11:01:19', '2025-06-19 07:44:44', NULL, NULL),
(3, 'Farhan Ginting', 'farhan-ginting', '089876543111', NULL, 'Cinangsi, Kec. Cibogo, Kabupaten Subang, Jawa Barat 41285', 'farhan', '$2y$12$xzpCLvayI7GU2mXNJ.x6qOkKuRrRU0VhEebeV3Xq.s/1kHeUBVoHa', 2, 0, '2025-06-19 12:03:45', '2025-05-21 01:57:21', '2025-07-07 08:01:55', NULL, NULL),
(8, 'Zacki Saiful', 'zacki-saiful', '086792215721', NULL, 'Cinangsi, Kec. Cibogo, Kabupaten Subang, Jawa Barat 41285', 'zaki', '$2y$12$RL8T82RCU3Q9/9s9XUIwsOPTIbcS20BODwFdW5VJPwZ1DOUCJeWKS', 2, 0, NULL, '2025-06-12 12:13:54', '2025-07-07 08:02:41', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `vehicles`
--

CREATE TABLE `vehicles` (
  `id` char(10) NOT NULL,
  `vehicle_model_id` bigint NOT NULL,
  `type_vehicle` enum('motorcycle','car') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `color` varchar(100) NOT NULL,
  `production_year` date NOT NULL,
  `serial_number` varchar(50) DEFAULT NULL,
  `stnk_deadline` date NOT NULL,
  `type_fuel` enum('gasoline','electric','hybrid') NOT NULL,
  `kilometer` int NOT NULL,
  `cc_engine` int NOT NULL,
  `description` longtext NOT NULL,
  `lowest_price` bigint NOT NULL,
  `price_displayed` bigint NOT NULL,
  `status` enum('available','service','test_drive','sold','transaction','on_loan') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'available',
  `user_id` bigint NOT NULL,
  `branch_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by_branch_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_model_id`, `type_vehicle`, `color`, `production_year`, `serial_number`, `stnk_deadline`, `type_fuel`, `kilometer`, `cc_engine`, `description`, `lowest_price`, `price_displayed`, `status`, `user_id`, `branch_id`, `created_at`, `updated_at`, `deleted_at`, `deleted_by_branch_at`) VALUES
('B33SAM', 56, 'car', 'Abu abu', '2010-06-30', '2SQER44RTEW873251', '2026-06-30', 'gasoline', 15214, 6000, 'Bentley continental Flying Spur 6.0\r\nNik 2010\r\nSTNK PT\r\nKm 15rban\r\nPlat B', 1595000000, 1695000000, 'on_loan', 1, 46, '2025-06-23 02:11:55', '2025-07-06 23:56:47', NULL, NULL),
('B3566LAA', 26, 'motorcycle', 'Merah', '2012-08-31', '8HGFH21RTEW109186', '2026-08-31', 'gasoline', 21253, 600, 'Body mulus 93%\r\nMesin halus dan sehat\r\nBPKB Ready\r\nSTNK Ready\r\nPajak 07-2024\r\nKM Original 22xxx\r\nExhaust Aftermarket Yoshimura Original\r\nExhaust Header Aftermarket Full System Original DD Exhaust Custom Bending\r\nRemap ECU by Iwan CMS\r\nDecal Aftermarket Graphic Factory\r\nVolt Meter Aftermarket WR3 Original\r\nFront Caliper Aftermarket Brembo\r\nBrake Master Aftermarket Brembo Original\r\nClutch Master Aftermarket Accosato\r\nQuick Throttle Aftermarket Active Original\r\nRadiator Hose Aftermarket Samco Original\r\nUnderbond Aftermarket Kemimoto Original\r\nTail Tidy Aftermarket\r\nEngine Switch Aftermarket Leggero Original\r\nHand grip Aftermarket Ariette\r\nChain Aftermarket\r\nCover Radiator Aftermarket Arashi Original\r\nBar End Aftermarket\r\nFront Fender Aftermarket Carbon Kevlar Original\r\nFairing Side Cover Aftermarket Carbon Kevlar Original\r\nTank Cover Aftermarket Carbon Kevlar Original\r\nFrame Cover Aftermarket Carbon Kevlar Original\r\nSwing Arm Cover Aftermarket Carbon Forged Original\r\nArm Tongue Aftermarket Carbon Kevlar Original\r\nChain Cover Aftermarket Carbon Kevlar Original\r\nFront Brake Hose Aftermarket Hel Original\r\nFront Rim Repaint Black\r\nRear Rim Repaint Black\r\nFront Tire Aftermarket Battlax S22\r\nRear Tire Aftermarket Battlax S22\r\nThe rest is still full Original\r\nJust got exterior detailing and semi coating', 149850000, 169850000, 'transaction', 1, 46, '2025-06-20 03:03:27', '2025-06-23 01:48:00', NULL, NULL),
('B4224TKW', 1, 'car', 'Hitam', '2016-01-01', '', '2026-01-01', 'gasoline', 64005, 2755, 'Spesifikasi Kendaraan:\r\n1. STNK dan BPKB asli & sesuai atas nama : Perorangan Tangan Pertama\r\n2. Pajak : Mei 2025\r\n3. Odometer : 64,005 Km\r\n4. Transmisi : Automatic\r\n5. Plat : Ganjil\r\n6. Warna : Hitam\r\n7. Kelengkapan Dokumen : STNK, BPKB, FAKTUR, NIK, BM, BS, KS', 320000000, 348000000, 'on_loan', 1, 46, '2025-06-20 02:23:39', '2025-06-21 06:11:18', NULL, NULL),
('B4328HGA', 15, 'motorcycle', 'Hitam', '2019-09-20', '1HWYS41JASE662586', '2026-09-09', 'gasoline', 23268, 155, '- Unit Gress\r\n- Pajak Hidup\r\n- Surat Lengkap\r\n- Body Mulus\r\n- Kelistrikan Normal\r\n- Garansi Mesin\r\n- Gratis Oli', 21300000, 23300000, 'transaction', 1, 46, '2025-06-20 02:53:08', '2025-06-21 06:11:33', NULL, NULL),
('B6514ZN0', 50, 'motorcycle', 'Putih', '2020-06-09', '5WDFA67JIOP179889', '2026-06-09', 'gasoline', 29051, 150, 'HONDA PCX 150 KEYKESS REMOTE CBS ISS TAHUN 2020 GLOWING SIAP PAKAI\r\n*Surat lengkap BPKB STNK faktur sertifikat\r\n*pajak panjang\r\n*Mesin Bagus CVT Halus\r\n*Motor Gresss Siap Pakai\r\n*Harga Sudah Free Oli Mesin & Oli Gardan\r\n*kelistrikan normal\r\n*body mulus\r\n*full standard pemakaian\r\n*Proses Pajak Atau Balik Nama Bisa Di Bantu', 23000000, 25000000, 'available', 1, 46, '2025-06-20 03:14:26', '2025-07-06 10:54:58', NULL, NULL),
('B7631NGB', 54, 'car', 'Silver', '2015-02-23', '8HGIK51YMCA10371', '2027-02-23', 'gasoline', 71338, 2000, 'Spesifikasi Kendaraan :\r\n– Warna Silver Metalik\r\n– Automatic Triptonik Transmission (Joystick)\r\n– Leather & Electric Seat with Memory\r\n– Interior black original Leather\r\n– Sport Type\r\n– KM.71RB // Record BMW\r\n– Headunit Original BMW\r\n– Steering Audio Control\r\n– AC Digital, SRS Airbag\r\n– Monitor Information Display\r\n– Manual Book Complete\r\n– Spare Key Complete\r\n– Engine 2.0 Liter Turbo\r\n– Pajak Panjang Februari 2026\r\n– Registrasi Nopol B GENAP\r\n– Velg Original Ban Tebal\r\n– Very Good Condition', 278000000, 288000000, 'available', 1, 46, '2025-06-23 01:54:02', NULL, NULL, NULL),
('B8788KJA', 57, 'car', 'Biru Muda Metalik', '2022-09-19', '2YTSD31OKJA980122', '2026-09-19', 'electric', 23251, 1000, '\r\n- Kondisi sangat rapi dan seperti baru\r\n- NIK 2022\r\n- Pajak sangat panjang sampai OKTOBER 2025\r\n- Kilometer 26rb (pemakaian normal sehari2)\r\n- Service Record Wuling lengkap\r\n- Warranty Battery 8 tahun\r\n- Plat B Jakarta\r\n- Tangan pertama dari baru\r\n- Semua fitur & elektrik normal\r\n- NO FAULT\r\n- kaki-kaki normal\r\n- Dijamin tidak bekas tabrak/banjir\r\n- Surat2 lengkap, Kunci serep lengkap\r\n--\r\nKelengkapan\r\n- Velg 14 inch\r\n- Kaca Film Solar guard Black phantom\r\n- Portable Charger\r\n- Converter\r\n- Full LED Headlamp\r\n- Full LED Taillamp\r\n- LED DRL\r\n- Longrange battery 300km\r\n- Electric parking brake + brake hold\r\n- Stability control (ESC)\r\n- Floating double widescreen 10,25 inch\r\n- Keyless smart entry\r\n- Smart start system', 148000000, 158000000, 'available', 1, 46, '2025-06-23 02:24:04', NULL, NULL, NULL),
('B8931SLT', 51, 'car', 'Putih', '2013-12-19', '3DFQH41QHAW423126', '2026-12-19', 'gasoline', 15678, 3000, '| For Sale\r\nPrice 3.099 M\r\nLamborghini Gallardo LP550-2 Coupe\r\n• NIK 2013\r\n• Odo 16.xxx KM\r\n• Tax Bln 03\r\nPerformance :\r\n• 5.2L Naturally Aspirated V10\r\n• 550 Hp, 540 Nm\r\n• 0-100 km/h : 3.9 seconds\r\n• 6-Speed E-Gear Single Clutch Transmission\r\n• Rear-Wheel Drive\r\nExterior :\r\n• White\r\n• 19” Cordelia Wheels Shiny Black\r\n• Brake Calipers Painted Orange\r\n• Superleggera Front Bumper\r\n• Superleggera Side Skirts\r\n• Superleggera Rear Bumper\r\n• Clear Glass Engine Cover\r\n• Dual Quad Exhaust Tips\r\n• Front Axle Lift\r\n• Rear Camera\r\nInterior :\r\n• Leather Interior in Nero Perseus / Contrast Stitching Arancio\r\n• Door Sill Trim Plates w/ Gallardo Logo\r\n• Power Adjustable Heated Leather Seats\r\n• Leather-Wrapped Steering Wheel w/ Paddle Shifters\r\n• Adjustable Driving Modes (Sport/Auto/Corsa)\r\n• Oil Pressure, Oil Temperature & Battery Voltage Gauges\r\n• Dual-Zone Automatic Climate Control\r\n• Integrated Multimedia Display\r\n• AM/FM Radio\r\n• CD Disc Changer System\r\n• Navigation System\r\n• Surround Sound System', 3099000000, 3399000000, 'on_loan', 1, 46, '2025-06-20 03:26:06', '2025-06-21 06:11:53', NULL, NULL),
('B9083ESW', 55, 'car', 'Putih', '2015-08-29', '0HGGH91PFKR140793', '2026-08-29', 'gasoline', 51253, 3000, 'OTR288JT RANGER DC F150 GEN 3 RAPTOR 4X4 2013 DIESEL MANUAL FULL AKSESORIS SUPER ISTIMEWA GRESSSSSSSSSS BANGET ANTIKKKKKK\r\nFORD RANGER DC 4X4 F 150 GEN 3 RAPTOR FACELIFT MANUAL\r\nNew Model All New\r\nTAHUN 2013 4 X 4 DIESEL MANUAL\r\ntipe paling komplit ( RANGER DC F150 Look MANUAL 4X4 DOUBEL CABIN )\r\nFACE LIFT MODEL TERBARU F150 GEN 3 RAPTOR\r\nFull AKSESORIS MAHAL\r\njok kulit interior mewah\r\nLAMPU TEMBAK\r\nFULL BODY KIT F 150 GEN 3 RAPTOR\r\nWINCH OFFROAD SPEAK\r\nBODY MOULDING\r\nLED BAR LAMP\r\nSUPER ANTIK TIDAK ADA DUANYA SE INDONESIA\r\nVELG RACING R 20\r\nOFFROAD SPEAK BAN 35 BESAR\r\nKEREN HABIS\r\nKONDISI SEPERTI BARU\r\nTIDAK ADA DUANYA SE INDONESIA\r\nSILAHKAN LIHAT DAN BUKTIKAN\r\nHarga CASH Super murah\r\n= 318 jt\r\nHarga Paket Kredit Super murah\r\n= 298 jt\r\n- 1000% Full VARIASI MAHAL OFFROAD SPEAK ( PAKAI PRIBADI BUAT MEJENG SAJA ) MOBIL DI PANDANGI SAJA DAN DISELIMUTIN DALAM GARASI )\r\n- Mulus sekali tanpa cacat sedikitpun\r\n- interior bersih lux masih wangi baru\r\n- Allnew F150 GEN 3 TERBARU RAPTOR RANGER DC MANUAL 4 X 4 FACELIFT TERAKHIR\r\nTidak Ada Duanya Se Indonesia\r\n- Perfect Condition\r\n- Km Rendah Super Antik Gresssssss Masih Kaya Mobil Baru\r\n- MANUAL DIESEL responsif dan\r\nbertenaga sekali\r\n- FULL VARIASI MAHAL F150 GEN 3 MODEL TERBARU 2024 RAPTOR KEREN HABIS ( EYE CATCHING ) MENARIK PERHATIAN DI MANA2 SIAP MEJENG\r\n- tipe paling lengkap\r\n- kondisi sangat istimewa\r\n- tidak ada duanya\r\n- barang sangat terawat sekali - interior asli mewah sekali\r\n- AC ND +TV ANDORID MODEL TESLA BLUETOTH YOUTUBE NETFLIK USB +foglaamp+ power window +power sterring+ velg racing BAN OFFROAD 35 MAHAL + jok mewah lux + FULL AKSESORIS MAHAL MOBIL HOBI ( BANYAK DI SELIMUTIN DAN DIPANDANGI MATA DI GARASI DALAM )\r\n- perfect condition\r\n- sangat terawat sekali\r\n- Nik 2012\r\n- superistimewa\r\n- lihat pastisuka\r\n- antik sekali\r\n- siap pakai luar kota\r\n- Plat B JAKARTA\r\n- FAKTUR ASLI JAKARTA\r\n- Stnk Panjang\r\n- barang super istimewa\r\n- kondisi seperti baru\r\n( MOBIL SUPER ISTIMEWA SILAHKAN LIHAT DAN BUKTIKAN )\r\nTIDAK ADA DUANYA SE INDONESIA\r\nLebih cepat lebih bagus lihat unit mobilnya', 298000000, 305000000, 'available', 1, 46, '2025-06-23 02:03:20', '2025-06-23 02:03:42', NULL, NULL),
('D4276ZSF', 48, 'car', 'Putih', '2021-07-01', '1HGBH41JXMN109131', '2026-07-01', 'gasoline', 84355, 1500, 'Keunggulan Unit\r\n1. Pajak : July 2025\r\n2. Odometer : 74.xxx\r\n3. Like New\r\n4. Tangan Pertama\r\n5. Atas nama : Perorangan\r\n6. Plat Genap', 169000000, 180000000, 'transaction', 1, 46, '2025-06-20 02:32:53', '2025-06-20 02:33:42', NULL, NULL),
('D4444KKK', 52, 'car', 'Putih', '2022-07-20', '4QRYS25YXMN132066', '2026-07-20', 'gasoline', 5214, 3000, '- NIK 2022\r\n- Plat B ( Ganjil )\r\n- KM 7.000 ( Low )\r\n- ATPM\r\n- Tangan pertama dari baru\r\n- 4.0L twin turbocharged V8\r\n- 650 Hp, 850 Nm\r\n- 0 - 100 km/h : 3,6 sec\r\n- 8 speed - automatic transmission\r\n- Carbon ceramic brakes\r\n- Drive modes ( anima, strada, Sport, Corsa Neve )\r\n- 360 view cameras\r\nURBAN KIT :\r\n- Wheels 24” NDS2 Forged Wheels\r\n- Carbon Fibre : Front bumper Apron , Front bumper Eyebrow kit, tuning forks, Side Vent, Door Moulding Overlay, Rear Wing, Rear Diffuser', 8300000000, 8500000000, 'available', 1, 46, '2025-06-20 03:34:05', NULL, NULL, NULL),
('D4532TXZ', 24, 'motorcycle', 'Hijau', '2019-05-12', '4ESFG28JRTS102569', '2026-05-12', 'gasoline', 16781, 250, 'Kawasaki ninja new 250fi se mdp keyless 2019\r\nBody mulus kinclong(mantap)\r\nMesin halus bertenaga(mantap)\r\nBpkb,stnk,faktur lengkap\r\nPajak hidup 02-2024\r\nKm 12rb an asli!\r\nModif:\r\nKnalpot fs over racing\r\nFrame slider\r\nBan battlax', 47850000, 50000000, 'available', 1, 46, '2025-06-20 03:40:16', NULL, NULL, NULL),
('T4511YQ', 53, 'motorcycle', 'Merah', '2012-10-27', '7RTYW31JIOP179251', '2026-10-27', 'gasoline', 23098, 250, 'ASSALAMUALAIKUM\r\nFOR SALE\r\n•CRF 250 •\r\nTahun 2012 Bln 9\r\n√Motor terawat\r\n√Surat STNK BPKB\r\n√Pajak ON\r\n√Modif mantaf\r\n√Stater tokcer\r\n√PLAT B\r\n* Siap Gassspolll\r\nSPEC MODIF:\r\n•Knalpot Prospeed\r\n•Stang Renthal\r\n•Handle Zeta\r\n•Footpeg Rfx\r\n•Handle Zeta\r\n•Gear Roz\r\n•Shift level Rfx\r\n•Brake pedal Rfx\r\n•Slang rem Hel\r\n•Rante Aftermarket\r\n•Stoplamp 3 in 1\r\n•Handguard Zeta\r\n•Skid plateaftermarket\r\n•Headlamp Guard aftermarket\r\n•Decal DSgraphics\r\n•dll\r\nModif 10JT++', 59000000, 63000000, 'available', 1, 46, '2025-06-20 03:47:05', NULL, NULL, NULL),
('T5682THQ', 49, 'car', 'Abu', '2016-08-19', '1IWAD41JXKO109156', '2025-08-19', 'gasoline', 104005, 1900, 'Keunggulan Unit :\r\n- Astra Certified\r\n- Bebas Tabrak\r\n- Bebas Banjir\r\n- Kilometer Tidak Putaran\r\n.\r\nBerikut Spesiffikasi\r\n- Dokumen Lengkap a/n : Perorangan\r\n- Odometer : 100,995 km\r\n- Transmisi : AT\r\n- Pajak : 01 May 2025\r\n- Plat : Ganjil\r\n- Warna : Abu\r\n- KN : REX', 214000000, 230000000, 'available', 1, 46, '2025-06-20 02:39:56', '2025-06-21 06:12:11', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `vehicle_documents`
--

CREATE TABLE `vehicle_documents` (
  `id` bigint NOT NULL,
  `vehicle_id` char(10) NOT NULL,
  `stnk` varchar(255) NOT NULL,
  `bpkb` varchar(255) NOT NULL,
  `service_note` varchar(255) NOT NULL,
  `nota` varchar(255) NOT NULL,
  `asuransi` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by_vehicle_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `vehicle_documents`
--

INSERT INTO `vehicle_documents` (`id`, `vehicle_id`, `stnk`, `bpkb`, `service_note`, `nota`, `asuransi`, `created_at`, `updated_at`, `deleted_at`, `deleted_by_vehicle_at`) VALUES
(86, 'B33SAM', 'vehicles/vehicle_B33SAM/vehicle_documents/stnk_686b400499c2e.pdf', 'vehicles/vehicle_B33SAM/vehicle_documents/bpkb_686b40049a075.pdf', '', '', '', '2025-07-07 03:33:24', NULL, '2025-07-07 04:41:48', NULL),
(88, 'B33SAM', 'vehicles/vehicle_B33SAM/vehicle_documents/stnk_686b501ad0295.pdf', 'vehicles/vehicle_B33SAM/vehicle_documents/bpkb_686b501ad06ac.pdf', '', '', '', '2025-07-07 04:42:02', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `vehicle_loans`
--

CREATE TABLE `vehicle_loans` (
  `id` bigint NOT NULL,
  `partner_id` bigint NOT NULL,
  `vehicle_id` char(10) NOT NULL,
  `user_id` bigint NOT NULL,
  `loan_date` timestamp NOT NULL,
  `return_date` timestamp NOT NULL,
  `note` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('borrowed','returned') NOT NULL DEFAULT 'borrowed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by_partner_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `vehicle_loans`
--

INSERT INTO `vehicle_loans` (`id`, `partner_id`, `vehicle_id`, `user_id`, `loan_date`, `return_date`, `note`, `status`, `created_at`, `updated_at`, `deleted_at`, `deleted_by_partner_at`) VALUES
(29, 34, 'B8931SLT', 1, '2025-07-07 01:59:00', '2025-07-08 01:59:00', '', 'borrowed', '2025-07-07 08:59:42', NULL, NULL, NULL),
(30, 35, 'B4224TKW', 1, '2025-07-07 02:01:00', '2025-07-08 02:01:00', '', 'borrowed', '2025-07-07 09:01:53', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `vehicle_models`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `vehicle_models`
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
(33, 9, 'Ronin Nimbus', 'ronin-nimbus', '2025-06-10 05:43:35', NULL, NULL, NULL),
(48, 61, 'Xenia', 'xenia', '2025-06-20 02:30:25', NULL, NULL, NULL),
(49, 7, 'CR V', 'cr-v', '2025-06-20 02:36:47', NULL, NULL, NULL),
(50, 7, 'PCX', 'pcx', '2025-06-20 03:10:49', NULL, NULL, NULL),
(51, 21, 'Gallado', 'gallado', '2025-06-20 03:22:52', NULL, NULL, NULL),
(52, 21, 'URUS', 'urus', '2025-06-20 03:30:57', NULL, NULL, NULL),
(53, 7, 'CRF', 'crf', '2025-06-20 03:42:14', NULL, NULL, NULL),
(54, 15, '320i', '320i', '2025-06-23 01:50:58', NULL, NULL, NULL),
(55, 36, 'Ranger Double Cabin', 'ranger-double-cabin', '2025-06-23 01:59:51', NULL, NULL, NULL),
(56, 14, 'Flying Spur', 'flying-spur', '2025-06-23 02:08:53', '2025-06-23 02:13:08', NULL, NULL),
(57, 34, 'Air Ev', 'air-ev', '2025-06-23 02:21:18', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `vehicle_photos`
--

CREATE TABLE `vehicle_photos` (
  `id` bigint NOT NULL,
  `vehicle_id` char(10) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `is_cover` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by_vehicle_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `vehicle_photos`
--

INSERT INTO `vehicle_photos` (`id`, `vehicle_id`, `photo_path`, `is_cover`, `created_at`, `updated_at`, `deleted_at`, `deleted_by_vehicle_at`) VALUES
(124, 'B4224TKW', 'vehicles/vehicle_B4224TKW/vehicle_photos/photo_6854c6465966e.jpg', 1, '2025-06-19 19:24:06', '2025-06-19 19:24:44', NULL, NULL),
(125, 'B4224TKW', 'vehicles/vehicle_B4224TKW/vehicle_photos/photo_6854c64d80b10.jpg', 0, '2025-06-19 19:24:13', NULL, NULL, NULL),
(126, 'B4224TKW', 'vehicles/vehicle_B4224TKW/vehicle_photos/photo_6854c656f274d.jpg', 0, '2025-06-19 19:24:23', NULL, NULL, NULL),
(127, 'B4224TKW', 'vehicles/vehicle_B4224TKW/vehicle_photos/photo_6854c65f231ec.jpg', 0, '2025-06-19 19:24:31', NULL, NULL, NULL),
(128, 'B4224TKW', 'vehicles/vehicle_B4224TKW/vehicle_photos/photo_6854c666b7571.jpg', 0, '2025-06-19 19:24:38', NULL, NULL, NULL),
(129, 'D4276ZSF', 'vehicles/vehicle_D4276ZSF/vehicle_photos/photo_6854c8c0e0fb5.jpg', 1, '2025-06-19 19:34:41', '2025-06-19 19:35:17', NULL, NULL),
(130, 'D4276ZSF', 'vehicles/vehicle_D4276ZSF/vehicle_photos/photo_6854c8c7f3986.jpg', 0, '2025-06-19 19:34:48', NULL, NULL, NULL),
(131, 'D4276ZSF', 'vehicles/vehicle_D4276ZSF/vehicle_photos/photo_6854c8cf3b682.jpg', 0, '2025-06-19 19:34:55', NULL, NULL, NULL),
(132, 'D4276ZSF', 'vehicles/vehicle_D4276ZSF/vehicle_photos/photo_6854c8d78c5ac.jpg', 0, '2025-06-19 19:35:03', NULL, NULL, NULL),
(133, 'D4276ZSF', 'vehicles/vehicle_D4276ZSF/vehicle_photos/photo_6854c8e10e217.jpg', 0, '2025-06-19 19:35:13', NULL, NULL, NULL),
(134, 'T5682THQ', 'vehicles/vehicle_T5682THQ/vehicle_photos/photo_6854cab68e74a.jpg', 1, '2025-06-19 19:43:02', '2025-06-19 19:43:37', NULL, NULL),
(135, 'T5682THQ', 'vehicles/vehicle_T5682THQ/vehicle_photos/photo_6854cabe33cc7.jpg', 0, '2025-06-19 19:43:10', NULL, NULL, NULL),
(136, 'T5682THQ', 'vehicles/vehicle_T5682THQ/vehicle_photos/photo_6854cac4ebbda.jpg', 0, '2025-06-19 19:43:17', NULL, NULL, NULL),
(137, 'T5682THQ', 'vehicles/vehicle_T5682THQ/vehicle_photos/photo_6854cacc570ce.jpg', 0, '2025-06-19 19:43:24', NULL, NULL, NULL),
(138, 'T5682THQ', 'vehicles/vehicle_T5682THQ/vehicle_photos/photo_6854cad5c25a3.jpg', 0, '2025-06-19 19:43:33', NULL, NULL, NULL),
(139, 'B4328HGA', 'vehicles/vehicle_B4328HGA/vehicle_photos/photo_6854cd9bb4857.webp', 0, '2025-06-19 19:55:23', NULL, NULL, NULL),
(140, 'B4328HGA', 'vehicles/vehicle_B4328HGA/vehicle_photos/photo_6854cda305f89.webp', 0, '2025-06-19 19:55:31', NULL, NULL, NULL),
(141, 'B4328HGA', 'vehicles/vehicle_B4328HGA/vehicle_photos/photo_6854cdaca000e.webp', 0, '2025-06-19 19:55:40', NULL, NULL, NULL),
(142, 'B4328HGA', 'vehicles/vehicle_B4328HGA/vehicle_photos/photo_6854cdb40162c.webp', 0, '2025-06-19 19:55:48', NULL, NULL, NULL),
(143, 'B4328HGA', 'vehicles/vehicle_B4328HGA/vehicle_photos/photo_6854cdbbe821e.webp', 0, '2025-06-19 19:55:56', NULL, NULL, NULL),
(144, 'B3566LAA', 'vehicles/vehicle_B3566LAA/vehicle_photos/photo_6854d0064ebc0.webp', 1, '2025-06-19 20:05:42', '2025-06-19 20:06:16', NULL, NULL),
(145, 'B3566LAA', 'vehicles/vehicle_B3566LAA/vehicle_photos/photo_6854d00e46882.webp', 0, '2025-06-19 20:05:50', NULL, NULL, NULL),
(146, 'B3566LAA', 'vehicles/vehicle_B3566LAA/vehicle_photos/photo_6854d01557a35.webp', 0, '2025-06-19 20:05:57', NULL, NULL, NULL),
(147, 'B3566LAA', 'vehicles/vehicle_B3566LAA/vehicle_photos/photo_6854d01d85ece.webp', 0, '2025-06-19 20:06:05', NULL, NULL, NULL),
(148, 'B3566LAA', 'vehicles/vehicle_B3566LAA/vehicle_photos/photo_6854d02529fbf.webp', 0, '2025-06-19 20:06:13', NULL, NULL, NULL),
(149, 'B6514ZN0', 'vehicles/vehicle_B6514ZN0/vehicle_photos/photo_6854d25b50b4e.webp', 1, '2025-06-19 20:15:39', '2025-06-19 20:16:13', NULL, NULL),
(150, 'B6514ZN0', 'vehicles/vehicle_B6514ZN0/vehicle_photos/photo_6854d2626515a.webp', 0, '2025-06-19 20:15:46', NULL, NULL, NULL),
(151, 'B6514ZN0', 'vehicles/vehicle_B6514ZN0/vehicle_photos/photo_6854d26b911f3.webp', 0, '2025-06-19 20:15:55', NULL, NULL, NULL),
(152, 'B6514ZN0', 'vehicles/vehicle_B6514ZN0/vehicle_photos/photo_6854d2725daff.webp', 0, '2025-06-19 20:16:02', NULL, NULL, NULL),
(153, 'B6514ZN0', 'vehicles/vehicle_B6514ZN0/vehicle_photos/photo_6854d27a1ac7d.webp', 0, '2025-06-19 20:16:10', NULL, NULL, NULL),
(154, 'B8931SLT', 'vehicles/vehicle_B8931SLT/vehicle_photos/photo_6854d50bba314.webp', 1, '2025-06-19 20:27:07', '2025-06-19 20:27:43', NULL, NULL),
(155, 'B8931SLT', 'vehicles/vehicle_B8931SLT/vehicle_photos/photo_6854d5135a40d.webp', 0, '2025-06-19 20:27:15', NULL, NULL, NULL),
(156, 'B8931SLT', 'vehicles/vehicle_B8931SLT/vehicle_photos/photo_6854d51b786e5.webp', 0, '2025-06-19 20:27:23', NULL, NULL, NULL),
(157, 'B8931SLT', 'vehicles/vehicle_B8931SLT/vehicle_photos/photo_6854d523c6e7e.webp', 0, '2025-06-19 20:27:31', NULL, NULL, NULL),
(158, 'B8931SLT', 'vehicles/vehicle_B8931SLT/vehicle_photos/photo_6854d52c964c9.webp', 0, '2025-06-19 20:27:40', NULL, NULL, NULL),
(159, 'D4444KKK', 'vehicles/vehicle_D4444KKK/vehicle_photos/photo_6854d6bf8a665.webp', 1, '2025-06-19 20:34:23', '2025-06-19 20:34:54', NULL, NULL),
(160, 'D4444KKK', 'vehicles/vehicle_D4444KKK/vehicle_photos/photo_6854d6c68f983.webp', 0, '2025-06-19 20:34:30', NULL, NULL, NULL),
(161, 'D4444KKK', 'vehicles/vehicle_D4444KKK/vehicle_photos/photo_6854d6ce02164.webp', 0, '2025-06-19 20:34:38', NULL, NULL, NULL),
(162, 'D4444KKK', 'vehicles/vehicle_D4444KKK/vehicle_photos/photo_6854d6d53c7ac.webp', 0, '2025-06-19 20:34:45', NULL, NULL, NULL),
(163, 'D4444KKK', 'vehicles/vehicle_D4444KKK/vehicle_photos/photo_6854d6dcb3542.webp', 0, '2025-06-19 20:34:52', NULL, NULL, NULL),
(164, 'D4532TXZ', 'vehicles/vehicle_D4532TXZ/vehicle_photos/photo_6854d832e676e.webp', 1, '2025-06-19 20:40:35', '2025-06-19 20:41:07', NULL, NULL),
(165, 'D4532TXZ', 'vehicles/vehicle_D4532TXZ/vehicle_photos/photo_6854d839ef8ec.webp', 0, '2025-06-19 20:40:42', NULL, NULL, NULL),
(166, 'D4532TXZ', 'vehicles/vehicle_D4532TXZ/vehicle_photos/photo_6854d840e99d4.webp', 0, '2025-06-19 20:40:49', NULL, NULL, NULL),
(167, 'D4532TXZ', 'vehicles/vehicle_D4532TXZ/vehicle_photos/photo_6854d8491838e.webp', 0, '2025-06-19 20:40:57', NULL, NULL, NULL),
(168, 'D4532TXZ', 'vehicles/vehicle_D4532TXZ/vehicle_photos/photo_6854d850f043e.webp', 0, '2025-06-19 20:41:05', NULL, NULL, NULL),
(169, 'T4511YQ', 'vehicles/vehicle_T4511YQ/vehicle_photos/photo_6854d9ca31e58.webp', 1, '2025-06-19 20:47:22', '2025-06-19 20:48:02', NULL, NULL),
(170, 'T4511YQ', 'vehicles/vehicle_T4511YQ/vehicle_photos/photo_6854d9d31fd08.webp', 0, '2025-06-19 20:47:31', NULL, NULL, NULL),
(171, 'T4511YQ', 'vehicles/vehicle_T4511YQ/vehicle_photos/photo_6854d9ddee405.webp', 0, '2025-06-19 20:47:42', NULL, NULL, NULL),
(172, 'T4511YQ', 'vehicles/vehicle_T4511YQ/vehicle_photos/photo_6854d9e704921.webp', 0, '2025-06-19 20:47:51', NULL, NULL, NULL),
(173, 'T4511YQ', 'vehicles/vehicle_T4511YQ/vehicle_photos/photo_6854d9ef6c292.webp', 0, '2025-06-19 20:47:59', NULL, NULL, NULL),
(174, 'B7631NGB', 'vehicles/vehicle_B7631NGB/vehicle_photos/photo_6858b3d74a993.webp', 1, '2025-06-23 01:54:31', '2025-06-23 02:04:42', NULL, NULL),
(175, 'B7631NGB', 'vehicles/vehicle_B7631NGB/vehicle_photos/photo_6858b3ddbf5a0.webp', 0, '2025-06-23 01:54:37', NULL, NULL, NULL),
(176, 'B7631NGB', 'vehicles/vehicle_B7631NGB/vehicle_photos/photo_6858b3e440358.webp', 0, '2025-06-23 01:54:44', NULL, NULL, NULL),
(177, 'B7631NGB', 'vehicles/vehicle_B7631NGB/vehicle_photos/photo_6858b3eb1a7ff.webp', 0, '2025-06-23 01:54:51', NULL, NULL, NULL),
(178, 'B7631NGB', 'vehicles/vehicle_B7631NGB/vehicle_photos/photo_6858b3f1b0478.webp', 0, '2025-06-23 01:54:57', NULL, NULL, NULL),
(179, 'B9083ESW', 'vehicles/vehicle_B9083ESW/vehicle_photos/photo_6858b60c3ac40.webp', 1, '2025-06-23 02:03:56', '2025-06-23 02:04:34', NULL, NULL),
(180, 'B9083ESW', 'vehicles/vehicle_B9083ESW/vehicle_photos/photo_6858b6190c219.webp', 0, '2025-06-23 02:04:09', NULL, NULL, NULL),
(181, 'B9083ESW', 'vehicles/vehicle_B9083ESW/vehicle_photos/photo_6858b61ea6d75.webp', 0, '2025-06-23 02:04:14', NULL, NULL, NULL),
(182, 'B9083ESW', 'vehicles/vehicle_B9083ESW/vehicle_photos/photo_6858b627c0349.webp', 0, '2025-06-23 02:04:23', NULL, NULL, NULL),
(183, 'B9083ESW', 'vehicles/vehicle_B9083ESW/vehicle_photos/photo_6858b63021947.webp', 0, '2025-06-23 02:04:32', NULL, NULL, NULL),
(185, 'B33SAM', 'vehicles/vehicle_B33SAM/vehicle_photos/photo_6858b7ffc7236.webp', 0, '2025-06-23 02:12:15', NULL, NULL, NULL),
(186, 'B33SAM', 'vehicles/vehicle_B33SAM/vehicle_photos/photo_6858b80719570.webp', 0, '2025-06-23 02:12:23', NULL, NULL, NULL),
(187, 'B33SAM', 'vehicles/vehicle_B33SAM/vehicle_photos/photo_6858b80d1068c.webp', 0, '2025-06-23 02:12:29', NULL, NULL, NULL),
(188, 'B33SAM', 'vehicles/vehicle_B33SAM/vehicle_photos/photo_6858b813eba45.webp', 0, '2025-06-23 02:12:36', NULL, NULL, NULL),
(189, 'B8788KJA', 'vehicles/vehicle_B8788KJA/vehicle_photos/photo_6858bae29b0b8.webp', 1, '2025-06-23 02:24:34', '2025-06-23 02:24:58', NULL, NULL),
(190, 'B8788KJA', 'vehicles/vehicle_B8788KJA/vehicle_photos/photo_6858bae89e56b.webp', 0, '2025-06-23 02:24:40', NULL, NULL, NULL),
(191, 'B8788KJA', 'vehicles/vehicle_B8788KJA/vehicle_photos/photo_6858baee10e45.webp', 0, '2025-06-23 02:24:46', NULL, NULL, NULL),
(192, 'B8788KJA', 'vehicles/vehicle_B8788KJA/vehicle_photos/photo_6858baf350917.webp', 0, '2025-06-23 02:24:51', NULL, NULL, NULL),
(193, 'B8788KJA', 'vehicles/vehicle_B8788KJA/vehicle_photos/photo_6858baf8b6c44.webp', 0, '2025-06-23 02:24:56', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indeks untuk tabel `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_name_unique` (`name`);

--
-- Indeks untuk tabel `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`),
  ADD UNIQUE KEY `customers_phone_unique` (`phone`);

--
-- Indeks untuk tabel `customer_otps`
--
ALTER TABLE `customer_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indeks untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_session_id` (`chat_session_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_vehicles` (`vehicle_id`),
  ADD KEY `fk_orders_customers` (`customer_id`);

--
-- Indeks untuk tabel `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `test_drivers`
--
ALTER TABLE `test_drivers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_test_drivers_orders` (`order_id`),
  ADD KEY `fk_test_drivers_users` (`user_id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transactions_orders` (`order_id`),
  ADD KEY `fk_transactions_users` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- Indeks untuk tabel `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `fk_vehicles_user` (`user_id`),
  ADD KEY `fk_vehicles_branch` (`branch_id`),
  ADD KEY `fk_vehicles_to_models` (`vehicle_model_id`);

--
-- Indeks untuk tabel `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vehicle_documents_vehicle_id` (`vehicle_id`);

--
-- Indeks untuk tabel `vehicle_loans`
--
ALTER TABLE `vehicle_loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vehicle_loans_partner` (`partner_id`),
  ADD KEY `fk_vehicle_loans_user` (`user_id`),
  ADD KEY `fk_vehicle_loans_vehicle_id` (`vehicle_id`);

--
-- Indeks untuk tabel `vehicle_models`
--
ALTER TABLE `vehicle_models`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_models_to_brands` (`brand_id`);

--
-- Indeks untuk tabel `vehicle_photos`
--
ALTER TABLE `vehicle_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vehicle_photos_vehicle_id` (`vehicle_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `banks`
--
ALTER TABLE `banks`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT untuk tabel `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT untuk tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT untuk tabel `customer_otps`
--
ALTER TABLE `customer_otps`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT untuk tabel `partners`
--
ALTER TABLE `partners`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `test_drivers`
--
ALTER TABLE `test_drivers`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT untuk tabel `vehicle_loans`
--
ALTER TABLE `vehicle_loans`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `vehicle_models`
--
ALTER TABLE `vehicle_models`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT untuk tabel `vehicle_photos`
--
ALTER TABLE `vehicle_photos`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `customer_otps`
--
ALTER TABLE `customer_otps`
  ADD CONSTRAINT `customer_otps_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customers` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orders_vehicles` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `test_drivers`
--
ALTER TABLE `test_drivers`
  ADD CONSTRAINT `fk_test_drivers_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_test_drivers_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transactions_to_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_transactions_to_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `fk_vehicles_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vehicles_to_models` FOREIGN KEY (`vehicle_model_id`) REFERENCES `vehicle_models` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vehicles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  ADD CONSTRAINT `fk_vehicle_documents_vehicle_id` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `vehicle_loans`
--
ALTER TABLE `vehicle_loans`
  ADD CONSTRAINT `fk_vehicle_loans_partner` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vehicle_loans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vehicle_loans_vehicle_id` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `vehicle_models`
--
ALTER TABLE `vehicle_models`
  ADD CONSTRAINT `fk_models_to_brands` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `vehicle_photos`
--
ALTER TABLE `vehicle_photos`
  ADD CONSTRAINT `fk_vehicle_photos_vehicle_id` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
