-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Jan 2024 pada 02.09
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
  `type` enum('Sick','National Holiday') NOT NULL,
  `start_date` date DEFAULT NULL,
  `finish_date` date DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `attendances`
--

INSERT INTO `attendances` (`attendance_id`, `user_id`, `division_id`, `reason`, `type`, `start_date`, `finish_date`, `foto`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(43, 28, 1, 'sakit', 'Sick', '2024-01-19', '2024-01-22', 'images/sick/Sick leave userTest 2024-01-19 to 2024-01-22.jpg', '2024-01-22 06:06:10', 30, NULL, NULL),
(44, 28, 1, 'lebaran', 'National Holiday', '2024-01-26', '2024-01-28', '', '2024-01-22 06:06:42', 30, NULL, NULL),
(45, 28, 1, 'sakit', 'Sick', '2024-01-24', '2024-01-25', 'images/sick/Sick leave userTest 2024-01-24 to 2024-01-25.jpeg', '2024-01-22 08:10:27', 30, NULL, NULL);

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
  `start_date` timestamp NULL DEFAULT current_timestamp(),
  `finish_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `time_duty_overtime` tinyint(3) UNSIGNED NOT NULL,
  `status` enum('Approved','Pending') NOT NULL DEFAULT 'Pending',
  `approved_by` tinyint(3) UNSIGNED DEFAULT NULL,
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `submitted_by_admin` tinyint(3) UNSIGNED DEFAULT NULL,
  `sent_by_admin` timestamp NULL DEFAULT NULL,
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

INSERT INTO `leaves` (`leaves_id`, `user_id`, `division_id`, `reason`, `category`, `start_date`, `finish_date`, `submitted_by_admin`, `sent_by_admin`, `status`, `status_updated_at`, `status_updated_by`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(31, 28, 1, 'Liburan', 'Annual', '2024-01-17 17:00:00', '2024-01-18 17:00:00', 30, '2024-01-17 14:26:18', 'Approved', '2024-01-17 14:26:32', 27, '2024-01-17 14:25:59', 28, '2024-01-17 14:26:32', NULL),
(32, 28, 1, 'liburan', 'Annual', '2024-01-18 02:47:00', '2024-01-19 02:47:00', 30, '2024-01-18 02:48:23', 'Approved', '2024-01-18 02:48:35', 27, '2024-01-18 02:48:00', 28, '2024-01-18 02:48:35', NULL),
(33, 28, 1, 'liburan', 'Annual', '2024-01-22 03:32:00', '2024-01-30 03:32:00', 30, '2024-01-18 03:32:49', 'Approved', '2024-01-18 03:33:02', 27, '2024-01-18 03:32:26', 28, '2024-01-18 03:33:02', NULL),
(34, 28, 1, 'liburan', 'Annual', '2024-02-05 03:33:00', '2024-02-09 03:33:00', NULL, NULL, 'Pending', NULL, NULL, '2024-01-18 03:33:54', 28, NULL, NULL);

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
(21, 28, 2000000, '2024-01-17 14:38:55', 28, NULL, NULL);

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
(7, 'IT Consultant', '2024-01-16 13:29:06', 26, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_projects`
--

CREATE TABLE `m_projects` (
  `project_id` tinyint(3) UNSIGNED NOT NULL,
  `project_name` varchar(64) NOT NULL,
  `start_date` date DEFAULT current_timestamp(),
  `finish_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) UNSIGNED NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` tinyint(3) UNSIGNED DEFAULT NULL,
  `is_deleted` enum('Y','N') DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(70, 28, 4, 1, 'Normal', '2024-01-17 14:22:00', '2024-01-17 16:22:00', 'Weekday', 2, 'debugging', 30, '2024-01-17 14:23:29', 31, '2024-01-17 14:23:00', 'Approved', '2024-01-17 14:23:44', 27, '2024-01-17 14:22:30', 28, '2024-01-17 14:24:10', 28),
(71, 28, 4, 1, 'Normal', '2024-01-18 01:42:00', '2024-01-18 14:42:00', 'Weekend', 6, 'melakukan revisi', 30, '2024-01-18 02:44:33', 31, '2024-01-18 02:43:45', 'Approved', '2024-01-18 02:45:27', 27, '2024-01-18 02:42:42', 28, '2024-01-18 02:46:29', 28),
(72, 28, 3, 1, 'Normal', '2024-01-18 03:38:00', '2024-01-18 09:45:00', 'Weekday', 15, 'bugging', 30, '2024-01-18 03:40:02', 31, '2024-01-18 03:39:42', 'Approved', '2024-01-18 03:40:21', 27, '2024-01-18 03:39:24', 28, '2024-01-18 03:43:10', 28),
(74, 28, 2, 1, 'Urgent', '2024-01-18 03:51:00', '2024-01-18 07:55:00', 'Weekday', NULL, 'urgent', NULL, NULL, NULL, NULL, 'Rejected', '2024-01-18 03:51:39', 31, '2024-01-18 03:51:28', 28, '2024-01-18 03:51:39', NULL),
(75, 28, 3, 1, 'Business Trip', '2024-01-21 11:27:00', '2024-01-21 15:00:00', 'Weekend', 8, 'debugging', 30, '2024-01-21 09:05:01', 31, '2024-01-21 09:04:14', 'Approved', '2024-01-21 09:05:36', 27, '2024-01-21 08:28:15', 28, '2024-01-21 09:10:02', 28),
(76, 28, 4, 1, 'Urgent', '2024-01-27 02:00:00', '2024-01-28 14:00:00', 'Weekend', 8, 'server down', NULL, NULL, NULL, NULL, 'Approved', '2024-01-21 08:54:54', 31, '2024-01-21 08:53:06', 28, '2024-01-21 08:59:46', 28);

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
(140, 70, 28, 'Pending', '2024-01-17 14:22:30', 28, NULL, NULL),
(141, 70, 31, 'Approved', '2024-01-17 14:23:00', 31, '2024-01-17 14:23:00', 31),
(142, 70, 30, 'Approved', '2024-01-17 14:23:29', 30, '2024-01-17 14:23:29', 30),
(143, 70, 27, 'Approved', '2024-01-17 14:23:44', 27, '2024-01-17 14:23:44', 27),
(144, 71, 28, 'Pending', '2024-01-18 02:42:42', 28, NULL, NULL),
(145, 71, 31, 'Approved', '2024-01-18 02:43:45', 31, '2024-01-18 02:43:45', 31),
(146, 71, 30, 'Approved', '2024-01-18 02:44:33', 30, '2024-01-18 02:44:33', 30),
(147, 71, 27, 'Approved', '2024-01-18 02:45:27', 27, '2024-01-18 02:45:27', 27),
(148, 72, 28, 'Pending', '2024-01-18 03:39:24', 28, NULL, NULL),
(149, 72, 31, 'Approved', '2024-01-18 03:39:42', 31, '2024-01-18 03:39:42', 31),
(150, 72, 30, 'Approved', '2024-01-18 03:40:02', 30, '2024-01-18 03:40:02', 30),
(151, 72, 27, 'Approved', '2024-01-18 03:40:21', 27, '2024-01-18 03:40:21', 27),
(154, 74, 28, 'Pending', '2024-01-18 03:51:28', 28, NULL, NULL),
(155, 74, 31, 'Rejected', '2024-01-18 03:51:39', 31, '2024-01-18 03:51:39', 31),
(156, 75, 28, 'Pending', '2024-01-21 08:28:15', 28, NULL, NULL),
(157, 76, 28, 'Pending', '2024-01-21 08:53:06', 28, NULL, NULL),
(158, 76, 31, 'Approved', '2024-01-21 08:54:54', 31, '2024-01-21 08:54:54', 31),
(159, 75, 31, 'Approved', '2024-01-21 09:04:14', 31, '2024-01-21 09:04:14', 31),
(160, 75, 30, 'Approved', '2024-01-21 09:05:01', 30, '2024-01-21 09:05:01', 30),
(161, 75, 27, 'Approved', '2024-01-21 09:05:36', 27, '2024-01-21 09:05:36', 27);

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
(27, 4, 'Supervisor', 'supervisor', '$2y$10$/sdWNUQ4w2HsE.EYb6rzmOZaNHJ31wib0Y4RBnYsrTHSoo3ieLWWq', 'supervisor1@gmail.com', 2, '2024-01-16 13:07:43', NULL, NULL),
(28, 1, 'userTest', 'userTest', '$2y$10$QCzrdQckZ2Prcy7dA4jItu.ypFEBi970aFFINErcKjje61AeL0REC', 'userTest@gmail.com', 26, '2024-01-16 13:34:41', NULL, NULL),
(30, 3, 'Admin', 'admin', '$2y$10$ARj7aKnOSYVrsJU/143J/ujQr/QH/1YMAOXgS4otgNAzcICGUfUdG', 'admin@gmail.com', 26, '2024-01-16 13:39:39', NULL, NULL),
(31, 2, 'Leader', 'leader', '$2y$10$ByXNQIFlVdOmcfriWPoFRujP1y0O/G0dd78hizBOGRFlDf3iPpMyG', 'leader@gmail.com', 26, '2024-01-16 13:40:13', NULL, NULL),
(32, 1, 'Fazri Al Fauzi', 'fazri', '$2y$10$HcmtoJaYfYBoijLs5gVof.z26cZJ8QO.eN6tc3yE/YJOaENOjhC9.', 'fazrial39@gmail.com', 30, '2024-01-17 14:44:29', NULL, NULL),
(33, 1, 'Akbar Albarokah', 'akbar', '$2y$10$S3.w7yMQA4Xqo6CZXV9bYOwaHy6djcnTKUSkawTUHtmwaqOhEOBK.', 'akbaralbarokah0@gmail.com', 30, '2024-01-17 14:44:53', NULL, NULL);

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
  MODIFY `attendance_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `duty_overtimes`
--
ALTER TABLE `duty_overtimes`
  MODIFY `duty_overtime_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT untuk tabel `leaves`
--
ALTER TABLE `leaves`
  MODIFY `leaves_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT untuk tabel `m_basic_salaries`
--
ALTER TABLE `m_basic_salaries`
  MODIFY `basic_salary_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `m_divisions`
--
ALTER TABLE `m_divisions`
  MODIFY `division_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `m_projects`
--
ALTER TABLE `m_projects`
  MODIFY `project_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `overtimes`
--
ALTER TABLE `overtimes`
  MODIFY `overtime_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT untuk tabel `overtimes_histories`
--
ALTER TABLE `overtimes_histories`
  MODIFY `overtime_history_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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
