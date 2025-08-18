-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 15, 2025 at 08:48 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ksp_lms_lama`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `registration_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `signature_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paraf_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `jabatan_id` bigint UNSIGNED DEFAULT NULL,
  `jabatan_full` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `directorate_id` bigint UNSIGNED DEFAULT NULL,
  `division_id` bigint UNSIGNED DEFAULT NULL,
  `superior_id` bigint UNSIGNED DEFAULT NULL,
  `golongan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `registration_id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `phone`, `address`, `signature_path`, `paraf_path`, `role`, `is_active`, `profile_picture`, `remember_token`, `created_at`, `updated_at`, `jabatan_id`, `jabatan_full`, `department_id`, `directorate_id`, `division_id`, `superior_id`, `golongan`, `nik`) VALUES
(1, 'ADM01', 'Administrator', 'admin@admin.com', '2025-06-23 04:55:06', '$2y$10$TRhzLAV1wP.IjrpLAe6T6eeg2uw9J3fNwlc/xF/KlZklkeWpcKSd6', NULL, NULL, '082121212121', NULL, NULL, NULL, 'admin', 1, NULL, 'b0KlSFqz7aDQudhyzTHVAQp1lhE98MgSmlqWET4scah78nwe5lLJyZrzQopG', '2025-06-23 04:55:06', '2025-06-23 04:55:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'DIR001', 'Dewi Director', 'TEST@GMAIL.COM', NULL, '$2y$10$DjeExht8y6bI2lMEYJP1UOmGqacRPa4jL.hf1A/NjiBDBa3pX6iR2', NULL, NULL, '08111111001', 'Jl. Direksi No.1', NULL, NULL, 'admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-11 07:45:04', 1, NULL, NULL, 1, NULL, NULL, '00', '3173000000000001'),
(49, 'ASDIR001', 'Arief Assistant', 'asdir@gmail.com', NULL, '$2y$10$BhayvALSwTi.vUXmICMK9eYkNoZCYzCQubfU7zwSrKN8z8MZ.9rHO', NULL, NULL, '08111111002', 'Jl. HC No.1', NULL, NULL, 'admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-11 07:45:20', 2, NULL, NULL, 1, NULL, 48, '01', '3173000000000002');
