-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Okt 2023 pada 13.31
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.1.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `olams`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `attendances`
--

CREATE TABLE `attendances` (
  `attendance_id` int(10) UNSIGNED NOT NULL,
  `user_id` tinyint(3) UNSIGNED NOT NULL,
  `division_id` tinyint(3) UNSIGNED NOT NULL,
  `reason` text NOT NULL,
  `type` enum('Sick','National') NOT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `finish_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `attendances`
--

INSERT INTO `attendances` (`attendance_id`, `user_id`, `division_id`, `reason`, `type`, `start_date`, `finish_date`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(3, 1, 1, '', 'National', '2023-10-18 06:16:33', '2023-10-21 06:16:33', '2023-10-18 06:16:17', 1, NULL, NULL),
(4, 2, 4, '', 'National', '2023-10-18 06:17:01', '2023-10-21 06:17:01', '2023-10-18 06:18:02', 1, NULL, NULL),
(5, 3, 1, '', 'National', '2023-10-18 06:17:01', '2023-10-21 06:17:01', '2023-10-18 06:18:02', 1, NULL, NULL),
(8, 4, 1, '', 'National', '2023-10-18 06:19:47', '2023-10-21 06:19:47', '2023-10-18 06:20:04', 1, NULL, NULL),
(9, 5, 1, '', 'National', '2023-10-18 06:20:18', '2023-10-21 06:20:18', '2023-10-18 06:20:43', 1, NULL, NULL),
(10, 6, 1, '', 'National', '2023-10-18 06:21:35', '2023-10-21 06:21:35', '2023-10-18 06:22:09', 1, NULL, NULL),
(11, 7, 1, '', 'National', '2023-10-18 06:21:35', '2023-10-21 06:21:35', '2023-10-18 06:22:09', 1, NULL, NULL),
(12, 8, 1, '', 'National', '2023-10-18 06:22:20', '2023-10-21 06:22:20', '2023-10-18 06:22:58', 1, NULL, NULL),
(13, 9, 1, '', 'National', '2023-10-18 06:22:20', '2023-10-21 06:22:20', '2023-10-18 06:22:58', 0, NULL, NULL),
(16, 10, 1, '', 'National', '2023-10-18 06:23:58', '2023-10-21 06:23:58', '2023-10-18 06:24:40', 1, NULL, NULL),
(17, 11, 1, '', 'National', '2023-10-18 06:23:58', '2023-10-21 06:23:58', '2023-10-18 06:24:40', 1, NULL, NULL),
(18, 12, 5, '', 'Sick', '2023-10-18 06:25:05', '2023-10-20 06:25:05', '2023-10-18 06:27:31', 1, NULL, NULL),
(19, 13, 3, '', 'Sick', '2023-10-18 06:25:05', '2023-10-20 06:25:05', '2023-10-18 06:27:31', 1, NULL, NULL),
(20, 14, 2, '', 'Sick', '2023-10-18 06:25:05', '2023-10-20 06:25:05', '2023-10-18 06:27:31', 1, NULL, NULL),
(21, 15, 2, '', 'Sick', '2023-10-18 06:25:05', '2023-10-20 06:25:05', '2023-10-18 06:27:31', 1, NULL, NULL),
(22, 16, 1, '', 'Sick', '2023-10-18 06:25:05', '2023-10-20 06:25:05', '2023-10-18 06:27:31', 1, NULL, NULL),
(23, 16, 6, 'Sakit', 'Sick', '2023-10-18 18:00:00', '2023-10-20 17:00:00', '2023-10-20 07:28:47', 2, '2023-10-20 10:51:40', 2),
(24, 20, 6, 'lebaran', 'National', '2023-10-19 11:01:00', '2023-10-20 22:01:00', '2023-10-20 07:59:17', 2, '2023-10-20 10:39:08', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `duty_overtimes`
--

CREATE TABLE `duty_overtimes` (
  `duty_overtime_id` int(10) UNSIGNED NOT NULL,
  `user_id` tinyint(3) UNSIGNED NOT NULL,
  `project_id` tinyint(3) UNSIGNED NOT NULL,
  `division_id` tinyint(3) UNSIGNED NOT NULL,
  `lead_count` tinyint(3) UNSIGNED NOT NULL,
  `customer_count` tinyint(3) UNSIGNED NOT NULL,
  `note` text DEFAULT NULL,
  `status` enum('Approved','Pending') NOT NULL DEFAULT 'Pending',
  `approved_by` tinyint(3) UNSIGNED NOT NULL,
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `duty_overtimes`
--

INSERT INTO `duty_overtimes` (`duty_overtime_id`, `user_id`, `project_id`, `division_id`, `lead_count`, `customer_count`, `note`, `status`, `approved_by`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, 17, 1, 6, 2, 0, NULL, 'Pending', 1, 17, '2023-10-18 07:10:44', NULL, NULL),
(2, 18, 2, 6, 2, 5, NULL, 'Pending', 1, 18, '2023-10-18 07:10:44', NULL, NULL),
(3, 19, 3, 6, 1, 3, NULL, 'Pending', 1, 19, '2023-10-18 07:10:44', NULL, NULL),
(4, 20, 4, 6, 1, 0, NULL, 'Pending', 1, 20, '2023-10-18 07:10:44', NULL, NULL),
(5, 17, 3, 6, 2, 4, NULL, 'Pending', 1, 17, '2023-10-18 07:12:44', NULL, NULL),
(6, 18, 3, 6, 2, 4, NULL, 'Pending', 1, 18, '2023-10-18 07:12:44', NULL, NULL),
(7, 19, 2, 6, 2, 2, NULL, 'Pending', 1, 19, '2023-10-18 07:15:37', NULL, NULL),
(8, 20, 2, 6, 2, 2, NULL, 'Pending', 1, 20, '2023-10-18 07:15:37', NULL, NULL),
(9, 17, 4, 6, 4, 4, NULL, 'Pending', 1, 17, '2023-10-18 07:15:37', NULL, NULL),
(10, 18, 4, 6, 4, 4, NULL, 'Pending', 1, 18, '2023-10-18 07:15:37', NULL, NULL),
(11, 19, 4, 6, 4, 4, NULL, 'Pending', 2, 19, '2023-10-18 07:15:37', '2023-10-24 06:18:48', NULL),
(13, 18, 1, 6, 1, 2, NULL, 'Pending', 1, 18, '2023-10-16 07:16:07', NULL, NULL),
(14, 18, 3, 6, 2, 3, NULL, 'Pending', 1, 18, '2023-10-10 07:16:07', NULL, NULL),
(16, 17, 1, 6, 2, 1, 'Rancangan Design Ruangan', 'Approved', 2, 18, '2023-10-24 08:10:30', '2023-10-26 12:28:01', 2),
(19, 17, 2, 6, 2, 0, 'Lanjut Minggu Depan', 'Approved', 2, 17, '2023-10-26 12:18:42', '2023-10-26 12:26:28', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `leaves`
--

CREATE TABLE `leaves` (
  `leaves_id` tinyint(3) UNSIGNED NOT NULL,
  `user_id` tinyint(3) UNSIGNED NOT NULL,
  `division_id` tinyint(3) UNSIGNED NOT NULL,
  `reason` text NOT NULL,
  `category` enum('Annual','Pregnancy','Important Reason','Extended') NOT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `finish_date` timestamp NULL DEFAULT NULL,
  `status` enum('Approved','Pending','Rejected') NOT NULL,
  `status_updated_at` timestamp NULL DEFAULT NULL,
  `status_updated_by` tinyint(3) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `leaves`
--

INSERT INTO `leaves` (`leaves_id`, `user_id`, `division_id`, `reason`, `category`, `start_date`, `finish_date`, `status`, `status_updated_at`, `status_updated_by`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 2, 4, '', 'Important Reason', '2023-10-17 06:41:26', '2023-10-23 06:41:53', 'Approved', NULL, NULL, '2023-10-18 06:41:00', 1, '2023-10-27 06:44:12', NULL),
(2, 3, 1, '', 'Important Reason', '2023-10-23 06:41:53', '2023-10-24 06:41:53', 'Approved', NULL, NULL, '2023-10-18 06:41:00', 1, NULL, NULL),
(3, 4, 1, '', 'Important Reason', '2023-10-18 06:25:05', '2023-10-20 06:25:05', 'Pending', NULL, NULL, '2023-10-18 06:41:29', 1, NULL, NULL),
(4, 5, 1, '', 'Important Reason', '2023-10-18 06:25:05', '2023-10-25 06:41:53', 'Approved', NULL, NULL, '2023-10-18 06:42:26', 1, NULL, NULL),
(5, 6, 1, '', 'Important Reason', '2023-10-24 06:41:53', '2023-10-26 06:41:53', 'Pending', NULL, NULL, '2023-10-18 06:42:26', 1, NULL, NULL),
(6, 7, 1, '', 'Important Reason', '2023-10-25 06:41:53', '2023-10-26 06:41:53', 'Approved', NULL, NULL, '2023-10-18 06:43:35', 1, NULL, NULL),
(7, 8, 1, '', 'Extended', NULL, NULL, 'Rejected', NULL, NULL, '2023-10-18 06:43:35', 1, NULL, NULL),
(8, 9, 1, '', 'Extended', NULL, NULL, 'Rejected', NULL, NULL, '2023-10-18 06:44:31', 1, NULL, NULL),
(9, 10, 1, '', 'Extended', NULL, NULL, 'Rejected', NULL, NULL, '2023-10-18 06:44:31', 1, NULL, NULL),
(10, 11, 1, '', 'Annual', NULL, NULL, 'Pending', NULL, NULL, '2023-10-18 06:45:48', 1, NULL, NULL),
(11, 12, 5, '', 'Annual', NULL, NULL, 'Pending', '0000-00-00 00:00:00', NULL, '2023-10-18 06:45:48', 1, '2023-10-27 06:44:12', NULL),
(12, 13, 3, '', 'Annual', NULL, NULL, 'Approved', NULL, NULL, '2023-10-18 06:46:59', 1, NULL, NULL),
(13, 14, 2, '', 'Extended', NULL, NULL, 'Approved', NULL, NULL, '2023-10-18 06:46:59', 1, NULL, NULL),
(14, 15, 2, '', 'Important Reason', NULL, NULL, 'Approved', NULL, NULL, '2023-10-18 06:47:47', 1, NULL, NULL),
(15, 16, 1, '', 'Pregnancy', NULL, NULL, 'Approved', NULL, NULL, '2023-10-18 06:47:47', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_basic_salaries`
--

CREATE TABLE `m_basic_salaries` (
  `basic_salary_id` tinyint(3) UNSIGNED NOT NULL,
  `user_id` tinyint(3) UNSIGNED NOT NULL,
  `total_basic_salary` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `update_by` tinyint(3) UNSIGNED DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `m_basic_salaries`
--

INSERT INTO `m_basic_salaries` (`basic_salary_id`, `user_id`, `total_basic_salary`, `created_at`, `created_by`, `update_by`, `update_at`) VALUES
(1, 2, 6000000, '2023-10-18 07:23:15', 1, NULL, NULL),
(2, 3, 10000000, '2023-10-18 07:23:15', 1, NULL, NULL),
(3, 4, 5000000, '2023-10-18 07:27:32', 1, NULL, NULL),
(4, 5, 5000000, '2023-10-18 07:27:32', 1, NULL, NULL),
(5, 6, 5000000, '2023-10-18 07:27:32', 1, NULL, NULL),
(6, 7, 5000000, '2023-10-18 07:27:32', 1, NULL, NULL),
(7, 8, 5000000, '2023-10-18 07:27:32', 1, NULL, NULL),
(8, 9, 5000000, '2023-10-18 07:27:32', 1, NULL, NULL),
(9, 10, 5000000, '2023-10-18 07:27:32', 1, NULL, NULL),
(10, 11, 5000000, '2023-10-18 07:27:32', 1, NULL, NULL),
(11, 12, 5000000, '2023-10-18 07:27:32', 1, NULL, NULL),
(12, 13, 5000000, '2023-10-18 07:27:32', 1, NULL, NULL),
(13, 14, 6000000, '2023-10-18 07:27:32', 1, NULL, NULL),
(14, 15, 5000000, '2023-10-18 07:27:32', 1, NULL, NULL),
(15, 16, 7000000, '2023-10-18 07:27:32', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_divisions`
--

CREATE TABLE `m_divisions` (
  `division_id` tinyint(3) UNSIGNED NOT NULL,
  `division_name` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `m_divisions`
--

INSERT INTO `m_divisions` (`division_id`, `division_name`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 'Software Development', '2023-10-18 05:39:14', 1, NULL, NULL),
(2, 'General Affair	', '2023-10-18 05:39:14', 1, NULL, NULL),
(3, 'Manage Service Provider', '2023-10-18 05:39:38', 1, NULL, NULL),
(4, 'Office Administration', '2023-10-18 05:39:38', 1, NULL, NULL),
(5, 'Digital Marketing', '2023-10-18 05:41:19', 1, NULL, NULL),
(6, 'Rancang Mebel', '2023-10-18 07:00:41', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_projects`
--

CREATE TABLE `m_projects` (
  `project_id` tinyint(3) UNSIGNED NOT NULL,
  `project_name` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` tinyint(3) UNSIGNED DEFAULT NULL,
  `is_deleted` enum('Y','N') DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `m_projects`
--

INSERT INTO `m_projects` (`project_id`, `project_name`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`, `is_deleted`) VALUES
(1, 'Rumah Sakit Harapan', '2023-10-18 05:43:50', 1, '2023-10-19 11:07:26', NULL, NULL, NULL, 'N'),
(2, 'Gopal', '2023-10-18 05:43:50', 1, '2023-10-19 11:07:30', NULL, NULL, NULL, 'N'),
(3, 'EFS', '2023-10-18 05:44:09', 1, '2023-10-19 13:29:52', 2, NULL, NULL, 'N'),
(4, 'MAP', '2023-10-18 05:44:09', 1, '2023-10-19 11:07:42', NULL, NULL, NULL, 'N');

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_roles`
--

CREATE TABLE `m_roles` (
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `updated_by` tinyint(3) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `m_roles`
--

INSERT INTO `m_roles` (`role_id`, `name`, `created_at`, `created_by`, `updated_by`, `updated_at`) VALUES
(1, 'User', '2023-10-18 05:04:24', 1, NULL, NULL),
(2, 'Leader', '2023-10-18 05:05:08', 1, NULL, NULL),
(3, 'Admin', '2023-10-18 05:05:08', 1, NULL, NULL),
(4, 'Supervisor', '2023-10-18 05:05:08', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `overtimes`
--

CREATE TABLE `overtimes` (
  `overtime_id` tinyint(3) UNSIGNED NOT NULL,
  `user_id` tinyint(3) UNSIGNED NOT NULL,
  `project_id` tinyint(3) UNSIGNED NOT NULL,
  `divisi_id` tinyint(3) UNSIGNED NOT NULL,
  `type` enum('Normal','Urgent','Business Trip') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_nopad_ci DEFAULT NULL,
  `start_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `finish_date` timestamp NULL DEFAULT NULL,
  `category` enum('Weekday','Weekend') DEFAULT NULL,
  `effective_time` tinyint(3) UNSIGNED DEFAULT NULL,
  `reason` text NOT NULL,
  `submitted_by_admin` tinyint(3) UNSIGNED DEFAULT NULL,
  `sent_by_admin` timestamp NULL DEFAULT NULL,
  `checked_by_leader` tinyint(3) UNSIGNED DEFAULT NULL,
  `checked_by_leader_at` timestamp NULL DEFAULT NULL,
  `status` enum('Approved','Pending','Rejected') NOT NULL DEFAULT 'Pending',
  `status_updated_at` timestamp NULL DEFAULT NULL,
  `status_updated_by` tinyint(3) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `overtimes`
--

INSERT INTO `overtimes` (`overtime_id`, `user_id`, `project_id`, `divisi_id`, `type`, `start_date`, `finish_date`, `category`, `effective_time`, `reason`, `submitted_by_admin`, `sent_by_admin`, `checked_by_leader`, `checked_by_leader_at`, `status`, `status_updated_at`, `status_updated_by`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 3, 2, 1, 'Normal', '2023-10-18 07:34:32', NULL, 'Weekday', NULL, 'Debbuging', NULL, NULL, NULL, NULL, 'Approved', NULL, NULL, '2023-10-18 07:34:32', 3, NULL, NULL),
(2, 4, 3, 1, 'Normal', '2023-10-18 07:35:38', NULL, 'Weekday', NULL, 'Refisi Tampilan Dashboard', NULL, NULL, NULL, NULL, 'Approved', NULL, NULL, '2023-10-18 07:35:38', 4, '2023-10-18 07:36:53', NULL),
(3, 5, 1, 1, 'Normal', '2023-10-18 07:36:28', NULL, 'Weekday', NULL, 'Debbuging', NULL, NULL, NULL, NULL, 'Pending', NULL, NULL, '2023-10-18 07:36:28', 5, NULL, NULL),
(4, 6, 2, 1, 'Normal', '2023-10-18 07:38:43', NULL, 'Weekend', NULL, 'Refisi Tampilan Profile', NULL, NULL, NULL, NULL, 'Rejected', '2023-10-26 11:17:00', 1, '2023-10-18 07:38:43', 6, '2023-10-26 11:17:00', NULL),
(5, 9, 4, 1, 'Urgent', '2023-10-18 07:38:43', '2023-10-18 16:54:59', 'Weekday', NULL, 'Deployment', 2, '2023-10-24 03:38:34', 3, '2023-10-24 02:38:10', 'Pending', NULL, NULL, '2023-10-18 07:38:43', 9, '2023-10-24 03:38:34', 2),
(6, 3, 2, 1, 'Normal', '2023-10-22 13:00:00', '2023-10-22 16:00:00', 'Weekend', NULL, 'Debbuging', 2, '2023-10-23 13:00:24', NULL, NULL, 'Approved', NULL, NULL, '2023-10-23 11:58:49', 2, '2023-10-23 13:00:24', 2),
(7, 7, 1, 1, 'Normal', '2023-10-24 13:00:00', '2023-10-24 16:00:00', 'Weekday', NULL, 'Memperbaiki Error', NULL, NULL, NULL, NULL, 'Pending', NULL, NULL, '2023-10-24 02:44:28', 2, NULL, NULL),
(8, 8, 1, 1, 'Normal', '2023-10-24 13:00:00', '2023-10-24 16:00:00', 'Weekend', NULL, 'Revisi Code', NULL, NULL, 3, '2023-10-24 12:24:42', 'Approved', '2023-10-24 12:26:52', 1, '2023-10-24 02:56:07', 2, '2023-10-24 12:26:52', 3),
(11, 9, 1, 1, 'Normal', '2023-10-24 13:00:00', '2023-10-24 16:00:00', 'Weekday', NULL, 'Deployment', NULL, NULL, NULL, NULL, 'Pending', NULL, NULL, '2023-10-24 09:36:16', 9, NULL, NULL),
(17, 9, 1, 1, 'Normal', '2023-10-24 09:43:00', '2023-10-24 13:43:00', 'Weekday', NULL, 'Deploy', NULL, NULL, NULL, NULL, 'Approved', '2023-10-24 12:02:49', 1, '2023-10-24 09:44:00', 9, '2023-10-24 12:02:49', NULL),
(18, 9, 1, 1, 'Normal', '2023-10-24 09:44:00', '2023-10-24 13:44:00', 'Weekday', NULL, 'Deploy', NULL, NULL, 3, '2023-10-25 06:44:37', 'Approved', '2023-10-24 12:00:56', 1, '2023-10-24 09:44:31', 9, '2023-10-25 06:44:37', 3),
(19, 9, 1, 1, 'Urgent', '2023-10-24 10:16:00', '2023-10-24 16:16:00', 'Weekend', NULL, 'Deploy', NULL, NULL, 3, '2023-10-24 13:04:12', 'Pending', NULL, NULL, '2023-10-24 10:16:35', 9, '2023-10-24 13:04:12', 3),
(27, 5, 3, 1, 'Urgent', '2023-10-25 03:37:00', '2023-10-25 13:37:00', 'Weekday', NULL, 'test Reject', NULL, NULL, NULL, NULL, 'Approved', '2023-10-26 03:13:40', 1, '2023-10-25 03:37:41', 3, '2023-10-26 03:13:40', NULL),
(30, 10, 4, 1, 'Urgent', '2023-10-25 13:12:00', '2023-10-25 16:12:00', 'Weekday', NULL, 'Deployment', 2, '2023-10-25 07:08:35', NULL, NULL, 'Rejected', '2023-10-25 06:44:07', 3, '2023-10-25 06:12:32', 11, '2023-10-25 07:08:35', 2),
(31, 10, 4, 1, 'Normal', '2023-10-25 06:45:00', '2023-10-25 13:45:00', 'Weekday', 0, 'check Submit', 2, '2023-10-25 06:56:14', 3, '2023-10-25 07:15:39', 'Pending', NULL, NULL, '2023-10-25 06:45:35', 11, '2023-10-25 11:53:54', 9),
(34, 10, 1, 1, 'Normal', '2023-10-26 02:07:00', '2023-10-26 07:07:00', 'Weekday', 2, 'Deployment', NULL, NULL, NULL, NULL, 'Approved', '2023-10-26 02:50:11', 1, '2023-10-26 02:08:01', 10, '2023-10-26 03:50:32', 10),
(35, 10, 2, 1, 'Normal', '2023-10-26 04:09:00', '2023-10-26 07:09:00', 'Weekend', 3, 'Deployment', NULL, NULL, NULL, NULL, 'Approved', '2023-10-26 03:51:11', 1, '2023-10-26 02:09:45', 10, '2023-10-26 03:51:39', 10),
(36, 10, 4, 1, 'Normal', '2023-10-26 13:17:00', '2023-10-26 16:00:00', 'Weekday', NULL, 'Deploy', NULL, NULL, NULL, NULL, 'Pending', NULL, NULL, '2023-10-26 11:18:13', 10, NULL, NULL),
(37, 10, 4, 1, 'Normal', '2023-10-27 13:00:00', '2023-10-27 16:00:00', 'Weekday', NULL, 'Code Error', NULL, NULL, NULL, NULL, 'Pending', NULL, NULL, '2023-10-27 03:23:44', 10, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `overtimes_histories`
--

CREATE TABLE `overtimes_histories` (
  `overtime_history_id` tinyint(3) UNSIGNED NOT NULL,
  `overtime_id` tinyint(3) UNSIGNED NOT NULL,
  `user_id` tinyint(3) UNSIGNED NOT NULL,
  `status` enum('Approved','Pending','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `overtimes_histories`
--

INSERT INTO `overtimes_histories` (`overtime_history_id`, `overtime_id`, `user_id`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 1, 3, 'Approved', '2023-10-18 07:45:21', 1, NULL, NULL),
(2, 2, 4, 'Approved', '2023-10-18 07:45:21', 1, NULL, NULL),
(3, 3, 5, 'Pending', '2023-10-18 07:46:22', 1, NULL, NULL),
(4, 4, 6, 'Pending', '2023-10-18 07:46:22', 1, NULL, NULL),
(5, 5, 9, 'Approved', '2023-10-18 07:47:12', 3, NULL, NULL),
(6, 18, 9, 'Approved', '2023-10-24 09:44:31', 9, '2023-10-24 12:24:30', 3),
(7, 19, 9, 'Approved', '2023-10-24 10:16:35', 9, '2023-10-24 12:12:01', 3),
(15, 27, 5, 'Pending', '2023-10-25 03:37:41', 3, NULL, NULL),
(34, 30, 10, 'Pending', '2023-10-25 06:12:32', 11, NULL, NULL),
(35, 30, 10, 'Approved', '2023-10-25 06:43:09', 10, '2023-10-25 06:43:09', 3),
(36, 30, 10, 'Rejected', '2023-10-25 06:44:07', 10, '2023-10-25 06:44:07', 3),
(37, 18, 9, 'Approved', '2023-10-25 06:44:37', 9, '2023-10-25 06:44:37', 3),
(38, 31, 10, 'Pending', '2023-10-25 06:45:35', 11, NULL, NULL),
(39, 31, 10, 'Approved', '2023-10-25 06:45:46', 10, '2023-10-25 06:45:46', 3),
(40, 31, 10, NULL, '2023-10-25 06:56:14', 10, '2023-10-25 06:56:14', 2),
(41, 30, 10, NULL, '2023-10-25 07:08:35', 10, '2023-10-25 07:08:35', 2),
(42, 31, 10, 'Approved', '2023-10-25 07:10:04', 10, '2023-10-25 07:10:04', 3),
(53, 34, 10, 'Pending', '2023-10-26 02:08:01', 10, NULL, NULL),
(54, 35, 10, 'Pending', '2023-10-26 02:09:45', 10, NULL, NULL),
(55, 34, 10, 'Approved', '2023-10-26 02:50:11', 10, '2023-10-26 02:50:11', 1),
(56, 27, 5, 'Approved', '2023-10-26 03:13:40', 5, '2023-10-26 03:13:40', 1),
(57, 35, 10, 'Approved', '2023-10-26 03:51:11', 10, '2023-10-26 03:51:11', 1),
(58, 4, 6, 'Rejected', '2023-10-26 11:17:00', 6, '2023-10-26 11:17:00', 1),
(59, 36, 10, 'Pending', '2023-10-26 11:18:13', 10, NULL, NULL),
(60, 37, 10, 'Pending', '2023-10-27 03:23:44', 10, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` tinyint(3) UNSIGNED NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(254) NOT NULL,
  `email` varchar(64) NOT NULL,
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` tinyint(3) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `name`, `username`, `password`, `email`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
(1, 4, 'Ahmad Bahtiarsyah', 'bahtiar', '$2y$12$5xQWIJEIM1Zk/tEL.CYbKedaHUYbbHQrRJ6Y2m49Oo4coca/ervvG', 'bahtiar@example.com', 1, '2023-10-18 05:18:30', NULL, NULL),
(2, 3, 'Karin Oktiara', 'karin', '$2y$12$/yh7xWYcapJ46NkxpctodesMcLe9nRIpAHLlMWpO0.d7neKIJxofa', 'karin@example.com', 1, '2023-10-18 05:18:30', NULL, NULL),
(3, 2, 'Iyu Priatna', 'iyu', '$2y$12$APcX4fwyVMlT2HoCzVJhPu.rE3VsM2r11cgnkFYfFBcEs5b39v3cq', 'iyu@example.com', 1, '2023-10-18 05:19:46', NULL, '2023-10-27 07:57:08'),
(4, 1, 'Nur Fauzi', 'fauzi', '$2y$12$qLrE9ZAty1zJeCqSyzyq0eKGdm64AtxfLkJLG3gwgVGGcAtXnbwUq', 'fauzi@example.com', 1, '2023-10-18 05:25:18', NULL, NULL),
(5, 1, 'Yosi Yuniar', 'yosi', '$2y$12$vo3Z1aTf.IbEJc.CiiRiNOiZi9vwuizJITV2bx6dFG3yC5i/WpTmK', 'yosi@example.com', 1, '2023-10-18 05:24:13', NULL, NULL),
(6, 1, 'Tita Aprilianti', 'tita', '$2y$12$TlgdUfYsyFW/x3nr22qL.e8.uvh3cCzW6xPoVQVdGQ8DBA87UwHe2', 'tita@example.com', 1, '2023-10-18 05:24:13', NULL, NULL),
(7, 1, 'Tito Shadam', 'tito', '$2y$12$VpF/OTQySJknm7uSq/BtNOfIlKpjB1Cvktswwyr8uyuHV5xpnctRi', 'tito@example.com', 1, '2023-10-18 05:26:56', NULL, NULL),
(8, 1, 'Virgianto Eko', 'eko', '$2y$12$AQSypZrdplpm5hvJSSAY/edksAw8Zf2r/Im0TLmrtMougNe/6jyZG', 'eko@example.com', 1, '2023-10-18 05:26:56', NULL, NULL),
(9, 1, 'Muhammad Bara Aksayeth', 'bara', '$2y$12$8kvYfRZdiPuVTghGgLv5euRuCJ9zHQPcLxsYfNJf9DVe/Hcu1W1LG', 'bara@example.com', 1, '2023-10-18 05:29:05', NULL, NULL),
(10, 1, 'Marino Imola', 'marino', '$2y$12$PtvMvymnX88ZEL6ECf5R8.OBfKIcYtxHlucF6F06BbvjK1ZZy.EZm', 'marino@example.com', 1, '2023-10-18 05:29:05', NULL, NULL),
(11, 1, 'Cryan Fajri', 'cryan', '$2y$12$9Elzi2qCQIWmGXMYPnZzQegpYgb0t6XGBhQdbYRCA4AxHUdmqM.qm', 'cryan@example.com', 1, '2023-10-18 05:32:17', NULL, NULL),
(12, 1, 'Mega Murdiana', 'mega', '$2y$12$QRgTU3k2heEBW4PBQH4rvOYgDJ72u81tQ/cDxkXRWRHA8gHXUV8na', 'mega@example.com', 1, '2023-10-18 05:32:17', NULL, NULL),
(13, 1, 'Subhan Abdullah', 'subhan', '$2y$12$QxiZEcHwXPh71r2cNXQe4uAMvs9fEH5ERHnn3upSNBv3bFtdnIYzO', 'subhan@example.com', 1, '2023-10-18 05:34:17', NULL, NULL),
(14, 1, 'Andi Baskoro', 'andi', '$2y$12$cpYHKk1ZPPIhJlrHgGah2.5vx7uCfXwr/YmTcXoiEBAByhyxpMt0m', 'andi@example.com', 1, '2023-10-18 05:34:17', NULL, NULL),
(15, 1, 'Heri Gunawan', 'heri', '$2y$12$d1SWg0JiVCgMxNZ2vfRkjeolPG1JoUhj0PZYBqWxmmcyrhEmNZDs6', 'heri@example.com', 1, '2023-10-18 05:35:38', NULL, NULL),
(16, 1, 'Anggraeni', 'eni', '$2y$12$yeDcmktrYOC0p8PknUEFie52jAouV5TbEpWP5T8nfn8NEHZ762Obq', 'eni@example.com', 1, '2023-10-18 05:35:38', NULL, NULL),
(17, 1, 'Hafiz ', 'hafiz', '$2y$12$Ikz0Mij1xW5f3oE2K/zT1u.Xsv33K8pMcLYtlU6862iNf7KByCgny', 'hafiz@example.com', 1, '2023-10-18 06:53:39', NULL, NULL),
(18, 1, 'Edo', 'edo', '$2y$12$UXqupYQx6Eu6Tg1koDUmnO5II9wpj0zfEx658i61ZT8XMp4A/yZt6', 'edo@example.com', 1, '2023-10-18 07:02:51', NULL, NULL),
(19, 1, 'Rendy', 'rendy', '$2y$12$rSTQq2rQe/UwNtPGPlpmj.X5EM.CFI6izcu6txRNhL1arTscOFn62', 'rendy@example.com', 1, '2023-10-18 07:02:51', NULL, NULL),
(20, 1, 'Puspa', 'puspa', '$2y$12$MioQTTjBRC7DSP5Swm1a7.P8hr3AagILvMgPBQy95AfVhqWFOv2Z6', 'puspa@example.com', 1, '2023-10-18 07:03:54', NULL, NULL),
(23, 2, 'Andi Admin', 'andiAdmin', '$2y$10$eoK1H8PerdNe1FY353Cu7OLjcx3OwpYq9U8t230Gy3B5W3mdh5iJ2', 'andiadmin@example.com', 0, '2023-10-27 03:38:34', NULL, NULL),
(25, 1, 'UserTest', 'userr', '$2y$10$w6Sg/08pSBhN3iOKvc.NUeF5u9mjIpzYd/V1wbxg9r1aPxeD37O7e', 'userTest@example.com', 0, '2023-10-27 07:58:12', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `division_id` (`division_id`);

--
-- Indeks untuk tabel `duty_overtimes`
--
ALTER TABLE `duty_overtimes`
  ADD PRIMARY KEY (`duty_overtime_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `division_id` (`division_id`);

--
-- Indeks untuk tabel `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`leaves_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `division_id` (`division_id`);

--
-- Indeks untuk tabel `m_basic_salaries`
--
ALTER TABLE `m_basic_salaries`
  ADD PRIMARY KEY (`basic_salary_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `m_divisions`
--
ALTER TABLE `m_divisions`
  ADD PRIMARY KEY (`division_id`);

--
-- Indeks untuk tabel `m_projects`
--
ALTER TABLE `m_projects`
  ADD PRIMARY KEY (`project_id`);

--
-- Indeks untuk tabel `m_roles`
--
ALTER TABLE `m_roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indeks untuk tabel `overtimes`
--
ALTER TABLE `overtimes`
  ADD PRIMARY KEY (`overtime_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `divisi_id` (`divisi_id`);

--
-- Indeks untuk tabel `overtimes_histories`
--
ALTER TABLE `overtimes_histories`
  ADD PRIMARY KEY (`overtime_history_id`),
  ADD KEY `overtime_id` (`overtime_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `attendances`
--
ALTER TABLE `attendances`
  MODIFY `attendance_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `duty_overtimes`
--
ALTER TABLE `duty_overtimes`
  MODIFY `duty_overtime_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `leaves`
--
ALTER TABLE `leaves`
  MODIFY `leaves_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `m_basic_salaries`
--
ALTER TABLE `m_basic_salaries`
  MODIFY `basic_salary_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `m_divisions`
--
ALTER TABLE `m_divisions`
  MODIFY `division_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `m_projects`
--
ALTER TABLE `m_projects`
  MODIFY `project_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `overtimes`
--
ALTER TABLE `overtimes`
  MODIFY `overtime_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `overtimes_histories`
--
ALTER TABLE `overtimes_histories`
  MODIFY `overtime_history_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_ibfk_2` FOREIGN KEY (`division_id`) REFERENCES `m_divisions` (`division_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `attendances_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ketidakleluasaan untuk tabel `duty_overtimes`
--
ALTER TABLE `duty_overtimes`
  ADD CONSTRAINT `duty_overtimes_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `m_projects` (`project_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `duty_overtimes_ibfk_3` FOREIGN KEY (`division_id`) REFERENCES `m_divisions` (`division_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `duty_overtimes_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ketidakleluasaan untuk tabel `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_ibfk_2` FOREIGN KEY (`division_id`) REFERENCES `m_divisions` (`division_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `leaves_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ketidakleluasaan untuk tabel `m_basic_salaries`
--
ALTER TABLE `m_basic_salaries`
  ADD CONSTRAINT `m_basic_salaries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ketidakleluasaan untuk tabel `overtimes`
--
ALTER TABLE `overtimes`
  ADD CONSTRAINT `overtimes_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `m_projects` (`project_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `overtimes_ibfk_3` FOREIGN KEY (`divisi_id`) REFERENCES `m_divisions` (`division_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `overtimes_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ketidakleluasaan untuk tabel `overtimes_histories`
--
ALTER TABLE `overtimes_histories`
  ADD CONSTRAINT `overtimes_histories_ibfk_1` FOREIGN KEY (`overtime_id`) REFERENCES `overtimes` (`overtime_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `overtimes_histories_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `m_roles` (`role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
