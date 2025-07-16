-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 16, 2025 at 04:04 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ksp-lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pdf',
  `letter_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classifications`
--

CREATE TABLE `classifications` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classifications`
--

INSERT INTO `classifications` (`id`, `code`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, '0', 'ADMIN', 'Hanya untuk Admin', '2025-06-23 05:03:06', '2025-06-23 05:03:06');

-- --------------------------------------------------------

--
-- Table structure for table `configs`
--

CREATE TABLE `configs` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `configs`
--

INSERT INTO `configs` (`id`, `code`, `value`, `created_at`, `updated_at`) VALUES
(1, 'default_password', 'admin', NULL, NULL),
(2, 'page_size', '50', NULL, NULL),
(3, 'app_name', 'Aplikasi Surat Menyurat', NULL, NULL),
(4, 'institution_name', '404nfid', NULL, NULL),
(5, 'institution_address', 'Jl. Padat Karya', NULL, NULL),
(6, 'institution_phone', '082121212121', NULL, NULL),
(7, 'institution_email', 'admin@admin.com', NULL, NULL),
(8, 'language', 'id', NULL, NULL),
(9, 'pic', 'M. Iqbal Effendi', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `directorate_id` bigint UNSIGNED NOT NULL,
  `division_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `directorate_id`, `division_id`, `created_at`, `updated_at`) VALUES
(1, 'Corporate Secretary', 1, NULL, NULL, NULL),
(2, 'Legal & Compliance', 1, NULL, NULL, NULL),
(3, 'Internal Audit', 1, NULL, NULL, NULL),
(4, 'Business Development', 1, NULL, NULL, NULL),
(5, 'Engineering Planning', 2, NULL, NULL, NULL),
(6, 'Project Control', 2, NULL, NULL, NULL),
(7, 'Security Fire & SHE ', 2, NULL, NULL, NULL),
(8, 'Finance', 3, NULL, NULL, NULL),
(9, 'Accounting', 3, NULL, NULL, NULL),
(10, 'Procurement', 3, NULL, NULL, NULL),
(11, 'IT & Management System', 3, NULL, NULL, NULL),
(12, 'Human Capital', 3, NULL, NULL, NULL),
(13, 'Marketing Industrial Estate & Housing', 2, 1, NULL, NULL),
(14, 'Industrial Estate & Housing', 2, 1, NULL, NULL),
(15, 'Building Management & Office Rent', 2, 1, NULL, NULL),
(16, 'Real Estate', 2, 1, NULL, NULL),
(17, 'Golf & Sport Center ', 2, 1, NULL, NULL),
(18, 'Executive Marketing & Sales Hotel', 2, 2, NULL, NULL),
(19, 'Front Office', 2, 2, NULL, NULL),
(20, 'Housekeeping', 2, 2, NULL, NULL),
(21, 'Food & Beverage', 2, 2, NULL, NULL),
(22, 'Executive Chef', 2, 2, NULL, NULL),
(23, 'Engineering', 2, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `directorates`
--

CREATE TABLE `directorates` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `directorates`
--

INSERT INTO `directorates` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'President', NULL, NULL),
(2, 'Operation', NULL, NULL),
(3, 'Human Capital & Finance', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dispositions`
--

CREATE TABLE `dispositions` (
  `id` bigint UNSIGNED NOT NULL,
  `to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_date` date NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `letter_status` bigint UNSIGNED NOT NULL,
  `letter_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `directorate_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `divisions`
--

INSERT INTO `divisions` (`id`, `name`, `directorate_id`, `created_at`, `updated_at`) VALUES
(1, 'Commercial Property', 2, NULL, NULL),
(2, 'Hotel', 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jabatans`
--

CREATE TABLE `jabatans` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jabatans`
--

INSERT INTO `jabatans` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Director', NULL, NULL),
(2, 'Assistant Director', NULL, NULL),
(3, 'General Manager', NULL, NULL),
(4, 'Executive Assistant', NULL, NULL),
(5, 'Manager', NULL, NULL),
(6, 'Superintendent', NULL, NULL),
(7, 'Senior Engineer', NULL, NULL),
(8, 'Senior Officer', NULL, NULL),
(9, 'Supervisor', NULL, NULL),
(10, 'Engineer', NULL, NULL),
(11, 'Officer', NULL, NULL),
(12, 'Foreman', NULL, NULL),
(13, 'Junior Engineer', NULL, NULL),
(14, 'Junior Officer', NULL, NULL),
(15, 'Hotel Staff', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `letters`
--

CREATE TABLE `letters` (
  `id` bigint UNSIGNED NOT NULL,
  `reference_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nomor Surat',
  `agenda_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `letter_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'incoming' COMMENT 'Surat Masuk (incoming)/Surat Keluar (outgoing)',
  `classification_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `letters`
--

INSERT INTO `letters` (`id`, `reference_number`, `agenda_number`, `from`, `to`, `letter_date`, `received_date`, `description`, `note`, `type`, `classification_code`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '0-1', '0-1-1', 'ADMIN', NULL, '2025-06-23', '2025-06-24', 'oklahoma', 'test keterangan', 'incoming', '0', 1, '2025-06-23 05:04:28', '2025-06-23 05:04:28');

-- --------------------------------------------------------

--
-- Table structure for table `letter_statuses`
--

CREATE TABLE `letter_statuses` (
  `id` bigint UNSIGNED NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2022_12_05_081849_create_configs_table', 1),
(7, '2022_12_05_083409_create_letter_statuses_table', 1),
(8, '2022_12_05_083945_create_classifications_table', 1),
(9, '2022_12_05_084544_create_letters_table', 1),
(10, '2022_12_05_092303_create_dispositions_table', 1),
(11, '2022_12_05_093329_create_attachments_table', 1),
(12, '2025_06_26_035947_create_surat_pengajuan_training_example_table', 2),
(13, '2025_06_30_141517_create_directorates_table', 3),
(14, '2025_06_30_141601_create_departments_table', 3),
(15, '2025_06_30_141613_create_jabatans_table', 3),
(17, '2025_07_01_112438_create_training_participants_table', 5),
(20, '2025_07_01_135025_add_directorate_id_to_users_table', 7),
(21, '2025_07_01_112419_create_surat_pengajuan_pelatihan_table', 8),
(22, '2025_07_07_115052_create_divisions_table', 9),
(23, '2025_07_07_115138_update_users_and_departments_with_division_and_jabatan_full', 10),
(24, '2025_07_07_172406_add_signature_and_paraf_to_users_table', 11),
(26, '2025_07_09_104744_create_surat_pengajuan_pelatihans_table', 12),
(27, '2025_07_09_105015_create_training_participants_table', 13),
(28, '2025_07_09_110122_create_surat_pengajuan_pelatihan_signatures_and_parafs_table', 14),
(29, '2025_07_11_115559_add_golongan_to_users_table', 15),
(33, '2025_07_15_120446_create_surat_tugas_pelatihans_table', 16);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `signature_and_parafs`
--

CREATE TABLE `signature_and_parafs` (
  `id` bigint UNSIGNED NOT NULL,
  `registration_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `signature_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paraf_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `signature_and_parafs`
--

INSERT INTO `signature_and_parafs` (`id`, `registration_id`, `signature_path`, `paraf_path`, `created_at`, `updated_at`) VALUES
(1, 'ADM01', 'storage/signatures/ADM01.png', 'storage/parafs/ADM01.png', '2025-07-08 03:29:25', '2025-07-08 05:03:16'),
(2, 'MAN019', 'storage/signatures/MAN019.png', 'storage/parafs/MAN019.png', '2025-07-08 10:09:43', '2025-07-08 10:09:54'),
(3, 'EA001', 'storage/signatures/EA001.png', 'storage/parafs/EA001.png', '2025-07-10 08:12:34', '2025-07-10 08:12:43');

-- --------------------------------------------------------

--
-- Table structure for table `surat_pengajuan_pelatihans`
--

CREATE TABLE `surat_pengajuan_pelatihans` (
  `id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `kode_pelatihan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kompetensi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi` enum('Perusahaan','Didalam Kota','Diluar Kota','Diluar Negeri') COLLATE utf8mb4_unicode_ci NOT NULL,
  `instruktur` enum('Internal','Eksternal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sifat` enum('Seminar','Kursus','Sertifikasi','Workshop') COLLATE utf8mb4_unicode_ci NOT NULL,
  `kompetensi_wajib` enum('Wajib','Tidak Wajib') COLLATE utf8mb4_unicode_ci NOT NULL,
  `materi_global` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_pelatihan_ksp` enum('Termasuk','Tidak Termasuk') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `durasi` int NOT NULL,
  `tempat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `penyelenggara` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `biaya` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `per_paket_or_orang` enum('Paket','Orang') COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `surat_pengajuan_pelatihans`
--

INSERT INTO `surat_pengajuan_pelatihans` (`id`, `created_by`, `kode_pelatihan`, `kompetensi`, `judul`, `lokasi`, `instruktur`, `sifat`, `kompetensi_wajib`, `materi_global`, `program_pelatihan_ksp`, `tanggal_mulai`, `tanggal_selesai`, `durasi`, `tempat`, `penyelenggara`, `biaya`, `per_paket_or_orang`, `keterangan`, `created_at`, `updated_at`) VALUES
(2, 1, 'Pelatihan 1', 'Bahasa Inggris', 'Pelatihan TES TOEFL KSP 1', 'Diluar Kota', 'Eksternal', 'Seminar', 'Wajib', 'Audio Visual Bahasa Inggris', 'Termasuk', '2025-07-10', '2025-07-11', 2, 'Jakarta', 'LIA', '6000000', 'Orang', 'Test', '2025-07-09 04:44:57', '2025-07-09 04:44:57'),
(3, 1, 'Pelatihan 2', 'Bahasa Inggris', 'Pelatihan TES TOEFL KSP 2', 'Perusahaan', 'Internal', 'Kursus', 'Wajib', 'Bahasa Inggris', 'Termasuk', '2025-07-24', '2025-07-26', 3, 'Jakarta', 'LIA', '4500000', 'Paket', 'Test', '2025-07-09 08:50:59', '2025-07-09 08:50:59'),
(5, 1, 'Pelatihan 3', 'Bahasa Inggris', 'Pelatihan TES TOEFL KSP 3', 'Diluar Kota', 'Eksternal', 'Sertifikasi', 'Wajib', 't', 'Termasuk', '2025-07-21', '2025-07-22', 2, 'Jakarta', 'LIA', '5000000', 'Orang', 'test', '2025-07-09 09:53:43', '2025-07-09 09:53:43');

-- --------------------------------------------------------

--
-- Table structure for table `surat_pengajuan_pelatihan_signatures_and_parafs`
--

CREATE TABLE `surat_pengajuan_pelatihan_signatures_and_parafs` (
  `id` bigint UNSIGNED NOT NULL,
  `pelatihan_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `kode_pelatihan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `round` int UNSIGNED NOT NULL DEFAULT '1',
  `sequence` int UNSIGNED NOT NULL,
  `type` enum('paraf','signature') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `signed_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `surat_pengajuan_pelatihan_signatures_and_parafs`
--

INSERT INTO `surat_pengajuan_pelatihan_signatures_and_parafs` (`id`, `pelatihan_id`, `user_id`, `kode_pelatihan`, `registration_id`, `round`, `sequence`, `type`, `status`, `signed_at`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, 2, 52, 'Pelatihan 1', 'EA001', 1, 1, 'paraf', 'approved', '2025-07-15 05:54:30', NULL, '2025-07-09 04:44:57', '2025-07-15 05:54:30'),
(2, 2, 51, 'Pelatihan 1', 'GM002', 1, 2, 'signature', 'approved', '2025-07-15 07:04:42', NULL, '2025-07-09 04:44:58', '2025-07-15 07:04:42'),
(3, 3, 52, 'Pelatihan 2', 'EA001', 1, 1, 'paraf', 'rejected', '2025-07-10 10:25:32', 'ganti peserta nya ya', '2025-07-09 08:50:59', '2025-07-10 10:25:32'),
(4, 3, 50, 'Pelatihan 2', 'GM001', 1, 2, 'signature', 'rejected', '2025-07-10 10:25:32', 'Auto rejected due to earlier rejection', '2025-07-09 08:50:59', '2025-07-10 10:25:32'),
(5, 5, 52, 'Pelatihan 3', 'EA001', 1, 1, 'paraf', 'approved', '2025-07-10 08:20:02', NULL, '2025-07-09 09:53:43', '2025-07-10 08:20:02'),
(6, 5, 51, 'Pelatihan 3', 'GM002', 1, 2, 'paraf', 'pending', NULL, NULL, '2025-07-09 09:53:43', '2025-07-09 09:53:43'),
(7, 5, 48, 'Pelatihan 3', 'DIR001', 1, 3, 'signature', 'pending', NULL, NULL, '2025-07-09 09:53:43', '2025-07-09 09:53:43'),
(8, 5, 64, 'Pelatihan 3', 'MAN012', 1, 4, 'signature', 'pending', NULL, NULL, '2025-07-09 09:53:43', '2025-07-09 09:53:43'),
(9, 5, 77, 'Pelatihan 3', 'DIR003', 1, 5, 'signature', 'pending', NULL, NULL, '2025-07-09 09:53:43', '2025-07-09 09:53:43');

-- --------------------------------------------------------

--
-- Table structure for table `surat_pengajuan_training_example`
--

CREATE TABLE `surat_pengajuan_training_example` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `training_date` date NOT NULL,
  `submitted_by` bigint UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `surat_pengajuan_training_example`
--

INSERT INTO `surat_pengajuan_training_example` (`id`, `title`, `description`, `training_date`, `submitted_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 'TES SURAT', 'HALO', '2025-06-28', 1, 'pending', '2025-06-26 05:09:05', '2025-06-26 05:09:05');

-- --------------------------------------------------------

--
-- Table structure for table `surat_tugas_pelatihans`
--

CREATE TABLE `surat_tugas_pelatihans` (
  `id` bigint UNSIGNED NOT NULL,
  `pelatihan_id` bigint UNSIGNED NOT NULL,
  `kode_pelatihan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `tempat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_pelatihan` date NOT NULL,
  `durasi` int UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_accepted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `surat_tugas_pelatihans`
--

INSERT INTO `surat_tugas_pelatihans` (`id`, `pelatihan_id`, `kode_pelatihan`, `judul`, `tanggal`, `tempat`, `tanggal_pelatihan`, `durasi`, `created_by`, `status`, `is_accepted`, `created_at`, `updated_at`) VALUES
(1, 2, 'Pelatihan 1', 'Pelatihan TES TOEFL KSP 1', '2025-07-15', 'Jakarta', '2025-07-10', 2, 51, 'draft', 0, '2025-07-15 07:04:42', '2025-07-15 07:04:42');

-- --------------------------------------------------------

--
-- Table structure for table `surat_tugas_pelatihan_signatures_and_parafs`
--

CREATE TABLE `surat_tugas_pelatihan_signatures_and_parafs` (
  `id` bigint UNSIGNED NOT NULL,
  `surat_tugas_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` enum('paraf','signature') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sequence` int NOT NULL,
  `round` int NOT NULL DEFAULT '1',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training_participants`
--

CREATE TABLE `training_participants` (
  `id` bigint UNSIGNED NOT NULL,
  `pelatihan_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `kode_pelatihan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan_id` bigint UNSIGNED NOT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `superior_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `training_participants`
--

INSERT INTO `training_participants` (`id`, `pelatihan_id`, `user_id`, `kode_pelatihan`, `registration_id`, `jabatan_id`, `department_id`, `superior_id`, `created_at`, `updated_at`) VALUES
(3, 2, 71, 'Pelatihan 1', 'MAN019', 5, 19, NULL, '2025-07-09 04:44:57', '2025-07-09 04:44:57'),
(4, 2, 72, 'Pelatihan 1', 'MAN020', 5, 20, NULL, '2025-07-09 04:44:57', '2025-07-09 04:44:57'),
(5, 3, 71, 'Pelatihan 2', 'MAN019', 5, 19, NULL, '2025-07-09 08:50:59', '2025-07-09 08:50:59'),
(6, 3, 72, 'Pelatihan 2', 'MAN020', 5, 20, NULL, '2025-07-09 08:50:59', '2025-07-09 08:50:59'),
(7, 5, 72, 'Pelatihan 3', 'MAN020', 5, 20, NULL, '2025-07-09 09:53:43', '2025-07-09 09:53:43'),
(8, 5, 71, 'Pelatihan 3', 'MAN019', 5, 19, NULL, '2025-07-09 09:53:43', '2025-07-09 09:53:43');

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
  `golongan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `registration_id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `phone`, `address`, `signature_path`, `paraf_path`, `role`, `is_active`, `profile_picture`, `remember_token`, `created_at`, `updated_at`, `jabatan_id`, `jabatan_full`, `department_id`, `directorate_id`, `division_id`, `superior_id`, `golongan`, `nik`) VALUES
(1, 'ADM01', 'Administrator', 'admin@admin.com', '2025-06-23 04:55:06', '$2y$10$TRhzLAV1wP.IjrpLAe6T6eeg2uw9J3fNwlc/xF/KlZklkeWpcKSd6', NULL, NULL, '082121212121', NULL, NULL, NULL, 'admin', 1, NULL, 'jzyCVYXuzwZVgP3uJqwOYCtzvi8UESKwBlV0E5EwYeqwSHVla4F40LNrtjmz', '2025-06-23 04:55:06', '2025-06-23 04:55:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'DIR001', 'Dewi Director', 'TEST@GMAIL.COM', NULL, '$2y$10$DjeExht8y6bI2lMEYJP1UOmGqacRPa4jL.hf1A/NjiBDBa3pX6iR2', NULL, NULL, '08111111001', 'Jl. Direksi No.1', NULL, NULL, 'admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-11 07:45:04', 1, NULL, NULL, 1, NULL, NULL, '00', '3173000000000001'),
(49, 'ASDIR001', 'Arief Assistant', 'asdir@gmail.com', NULL, '$2y$10$BhayvALSwTi.vUXmICMK9eYkNoZCYzCQubfU7zwSrKN8z8MZ.9rHO', NULL, NULL, '08111111002', 'Jl. HC No.1', NULL, NULL, 'admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-11 07:45:20', 2, NULL, NULL, 1, NULL, 48, '01', '3173000000000002'),
(50, 'GM001', 'Gita GM CP', NULL, NULL, '$2y$10$.0rbg1iVgUFkDZKfec3uUOV2jIyujakIsd2iRCNs4z4BJjYuPY/YS', NULL, NULL, '08111111003', 'Jl. Komersial No.1', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-07 06:13:17', 3, NULL, NULL, 2, 1, NULL, NULL, '3173000000000003'),
(51, 'GM002', 'Hendra GM Hotel', 'generalmanagerhotel@corp.com', NULL, '$2y$10$y9ehnPn2JhnDWAD89WIH3eMfX0peWjQHg9F3rR3BV4xSB54WKMu7K', NULL, NULL, '08111111004', 'Jl. Hotel No.1', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-10 04:20:12', 3, NULL, NULL, 2, 2, NULL, NULL, '3173000000000004'),
(52, 'EA001', 'Ela Exec Hotel', 'executivemanagerhotel@corp.com', NULL, '$2y$10$ocz.K7VJKg/fHs3fZ8UIvuXZ3Zn01WmC7nr8c.3CBrQ9NXBj6bhXO', NULL, NULL, '08111111005', 'Jl. Hotel No.2', NULL, NULL, 'upper_staff', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-10 06:46:23', 4, NULL, NULL, 2, 2, NULL, NULL, '3173000000000005'),
(53, 'MAN001', 'Manager Corporate Secretary', NULL, NULL, '$2y$10$BPXAUrYPstRm8dUHJORz2uAxATNF18CaFJDyJddrc4N8cOs1DRM46', NULL, NULL, '08111110101', 'Jl. Corporate Secretary', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-07 06:13:17', 5, NULL, 1, 1, NULL, NULL, NULL, '31730000000000006'),
(54, 'MAN002', 'Manager Legal & Compliance', NULL, NULL, '$2y$10$o5Uug4Bejtvanp8BoQrx/ORISMYJm2KUzaHsA0hEZlMl37t970vle', NULL, NULL, '08111110102', 'Jl. Legal & Compliance', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-07 06:13:17', 5, NULL, 2, 1, NULL, NULL, NULL, '31730000000000007'),
(55, 'MAN003', 'Manager Internal Audit', NULL, NULL, '$2y$10$p9uf4MHU1TtjS8aPTp3.W.tBPd25l5FZu9BhpAT40q17PmteZIF3K', NULL, NULL, '08111110103', 'Jl. Internal Audit', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-07 06:13:17', 5, NULL, 3, 1, NULL, NULL, NULL, '31730000000000008'),
(56, 'MAN004', 'Manager Business Development', NULL, NULL, '$2y$10$LDMChuWGb8UPepZgWSCSN.AYq6PXD8wXy4xV8rqkXqYpWDA7fjtZ6', NULL, NULL, '08111110104', 'Jl. Business Development', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-07 06:13:17', 5, NULL, 4, 1, NULL, NULL, NULL, '31730000000000009'),
(57, 'MAN005', 'Manager Engineering Planning', NULL, NULL, '$2y$10$4vrl7/Q8WSzD.1YJF3gJU.qilZ62Dj4FyPJ.s79dLh/2mJox1eTvC', NULL, NULL, '08111110105', 'Jl. Engineering Planning', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-07 06:13:17', 5, NULL, 5, 2, NULL, NULL, NULL, '31730000000000010'),
(58, 'MAN006', 'Manager Project Control', NULL, NULL, '$2y$10$Tzb2vPDaz6UlbUu0FobaLOwNov0WCRkY4r56i1iZ.ryiqhY4cAcqy', NULL, NULL, '08111110106', 'Jl. Project Control', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-07 06:13:17', 5, NULL, 6, 2, NULL, NULL, NULL, '31730000000000011'),
(59, 'MAN007', 'Manager Security Fire & SHE Manager', NULL, NULL, '$2y$10$kcdEjIvPognCLrnnAUr2seJbqwUD5UNiDukYygAbce8m/yG5FGWxy', NULL, NULL, '08111110107', 'Jl. Security Fire & SHE Manager', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-07 06:13:17', 5, NULL, 7, 2, NULL, NULL, NULL, '31730000000000012'),
(60, 'MAN008', 'Manager Finance', NULL, NULL, '$2y$10$XbRfK1th2hr6iztyfFbCQO0M/QvXdq1QHiF6zett0TU9qjJ/dkv5q', NULL, NULL, '08111110108', 'Jl. Finance', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:17', '2025-07-07 06:13:17', 5, NULL, 8, 3, NULL, NULL, NULL, '31730000000000013'),
(61, 'MAN009', 'Manager Accounting', 'asdadsadas@gmail.com', NULL, '$2y$10$Jjbt8kRVLJ.Huv48DUx8sO.AAK/J4CNagow/9KonICWM15VYF9tyO', NULL, NULL, '08111110109', 'Jl. Accounting', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 07:56:08', 5, NULL, 9, 3, NULL, NULL, NULL, '31730000000000014'),
(62, 'MAN010', 'Manager Procurement', NULL, NULL, '$2y$10$Lap8KDPWENhEYj2dtpsRLef2zZVMMdnqG4canekeYxEQz.D1sPnNW', NULL, NULL, '08111110110', 'Jl. Procurement', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 10, 3, NULL, NULL, NULL, '31730000000000015'),
(63, 'MAN011', 'Manager IT & Management System', NULL, NULL, '$2y$10$NlxAP7IgUjYfLn3wfWmDu.1HTHkLHSG2k8N5HaTRBXMqYEjg/ms42', NULL, NULL, '08111110111', 'Jl. IT & Management System', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 11, 3, NULL, NULL, NULL, '31730000000000016'),
(64, 'MAN012', 'Manager Human Capital', NULL, NULL, '$2y$10$sdP3TNYsN33lHnlwK8l0YeuMVbOABwn1uME72dQO.UBpDZU.JS1Oe', NULL, NULL, '08111110112', 'Jl. Human Capital', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 12, 3, NULL, NULL, NULL, '31730000000000017'),
(65, 'MAN013', 'Manager Marketing Industrial Estate & Housing', NULL, NULL, '$2y$10$zPwHqiiSDuNgyomJ8DNR8e8pdKJWOxT/9aJFkfpOsSrISaB7Hsdn6', NULL, NULL, '08111110113', 'Jl. Marketing Industrial Estate & Housing', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 13, 2, 1, NULL, NULL, '31730000000000018'),
(66, 'MAN014', 'Manager Industrial Estate & Housing', NULL, NULL, '$2y$10$jO/13m9za4icdXdsxF0/wOz.H16UhtGlentUFIDtPBNs6y4zTQl1i', NULL, NULL, '08111110114', 'Jl. Industrial Estate & Housing', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 14, 2, 1, NULL, NULL, '31730000000000019'),
(67, 'MAN015', 'Manager Building Management & Office Rent', NULL, NULL, '$2y$10$NrkyOgOhtg0n2IPKiMvH/O4qlE2hZ.xl.acIo6uxwC.haQzHpg1bi', NULL, NULL, '08111110115', 'Jl. Building Management & Office Rent', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 15, 2, 1, NULL, NULL, '31730000000000020'),
(68, 'MAN016', 'Manager Real Estate', NULL, NULL, '$2y$10$O5cjG7HmtjFCF1TI7hrmN.iIysgw/vj9DNiOLKDA.VrrcN06Q03yy', NULL, NULL, '08111110116', 'Jl. Real Estate', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 16, 2, 1, NULL, NULL, '31730000000000021'),
(69, 'MAN017', 'Manager Golf & Sport Center Manager', NULL, NULL, '$2y$10$A5sTYwa2Z9cxN.DPw3H63enco5EcAd4q9fw91KD0jLWQN/tReYwEa', NULL, NULL, '08111110117', 'Jl. Golf & Sport Center Manager', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 17, 2, 1, NULL, NULL, '31730000000000022'),
(70, 'MAN018', 'Manager Executive Marketing & Sales Hotel', NULL, NULL, '$2y$10$abZZs2jq3IrKMoVWVTPsM.6B5.ilb1TIs3r0FESPYfoRcY7l78r8.', NULL, NULL, '08111110118', 'Jl. Executive Marketing & Sales Hotel', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 18, 2, 2, NULL, NULL, '31730000000000023'),
(71, 'MAN019', 'Manager Front Office', 'managerfrontoffice@manager.com', NULL, '$2y$10$JRVFx8HaQmTdkWzdsN9q8O/GPLkv2CntW9c0JndAzhccNAOQrolYq', NULL, NULL, '08111110119', 'Jl. Front Offices', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 09:08:36', 5, NULL, 19, 2, NULL, NULL, NULL, '31730000000000024'),
(72, 'MAN020', 'Manager Housekeeping', NULL, NULL, '$2y$10$6Y5DWYpZA.CpuHCBp2v0DOyZO3KT6GSHS/tL2a8jYknb4FYY19SGe', NULL, NULL, '08111110120', 'Jl. Housekeeping', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 20, 2, 2, NULL, NULL, '31730000000000025'),
(73, 'MAN021', 'Manager Food & Beverage', NULL, NULL, '$2y$10$4Cf4SI.b8IS4kNIKJlxBRepqMZE1qpVH0rxmPGmMdd1qZ29GhhUgK', NULL, NULL, '08111110121', 'Jl. Food & Beverage', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 21, 2, 2, NULL, NULL, '31730000000000026'),
(74, 'MAN022', 'Manager Executive Chef', NULL, NULL, '$2y$10$tHJ2JvTxWgEJGk/EKIP7quiYsYKR2/liuuGFZvzc7cpAZrp1HHmq2', NULL, NULL, '08111110122', 'Jl. Executive Chef', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 22, 2, 2, NULL, NULL, '31730000000000027'),
(75, 'MAN023', 'Manager Engineering', NULL, NULL, '$2y$10$qEbq2obOJAsBI7cQReqz5unfF33/BCe/AsNRyPboBQsR//0NSML72', NULL, NULL, '08111110123', 'Jl. Engineering', NULL, NULL, 'department_admin', 1, NULL, NULL, '2025-07-07 06:13:18', '2025-07-07 06:13:18', 5, NULL, 23, 2, 2, NULL, NULL, '31730000000000028'),
(77, 'DIR003', 'Jojo Director', 'directorhcfinance@director.com', NULL, '$2y$10$0qnLpeS0GriLBSNsb/e2DOLQo91ikgB1pDbpc8aHHnTHJ3n722Ul6', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 1, NULL, NULL, '2025-07-09 08:48:46', '2025-07-09 08:48:46', 1, 'DIREKTUR HUMAN CAPITAL & FINANCE', NULL, 3, NULL, NULL, NULL, NULL),
(550, '2000021', 'DENY KUNTADI', '2000021@yourdomain.com', NULL, '$2y$10$QvCx8qeZFIyU6pod8lVxNuW6HnTNNshqjWS2ihg/ZwGunMzlS8o6i', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 8, 'COR. COM SENIOR OFFICER', NULL, NULL, NULL, NULL, '3', NULL),
(551, '2000042', 'SUGENG RAHARDJO', '2000042@yourdomain.com', NULL, '$2y$10$7YJnWJBzfnvOV7.YkXnkXOKUIA/opjw39NbmLrFKprOkkOA/YyXHq', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 5, 'BUILDING MANAGEMENT &OFFICE RENT MANAGER', 15, 2, 1, NULL, '2', NULL),
(552, '2000045', 'MOKHAMAD  HASIM', '2000045@yourdomain.com', NULL, '$2y$10$ldzgmBzdzDsut.kBVgAt6uZkjx12aMwyVWV3XarNVRsHeeWYicg1K', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 5, 'HUMAN CAPITAL MANAGER', 12, 3, NULL, NULL, '2', NULL),
(553, '2000046', 'ARY JULIA HARINI', '2000046@yourdomain.com', NULL, '$2y$10$YRmMP6/nYE2zaZdNwQgz4evt40EvegfOH3rbkDO0BTc617YGWKSae', NULL, NULL, NULL, 'PCI BLOK E25 NO. 06 RT 002 RW 007 KEL. KEDALEMAN CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 7, 'ENGINEERING PLANNING SENIOR ENGINEER', 5, 2, NULL, NULL, '3', NULL),
(554, '2000048', 'DEDI IRWAN. S', '2000048@yourdomain.com', NULL, '$2y$10$rCwE.PzW4fyW3dDarG8.Qe1oghBE2rP3ugJ1K33NiWZ0oXculOYq6', NULL, NULL, NULL, 'JL. ARGA PATUHA BLOK D6 NO. 3 RT 013 RW 004 KEL. KOTASARI CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', NULL, 'FINANCIAL & OPERATION SENIOR AUDITOR', 8, 3, NULL, NULL, '3', NULL),
(555, '2000052', 'SLAMET EKO', '2000052@yourdomain.com', NULL, '$2y$10$4.aD5.hKijLvyMhJ7NcRrOBR7izSzcZ2FxzQ/4rR4v9vt9oIT9Epy', NULL, NULL, NULL, 'KOMP BPP BLOK E5 NO. 7 RT 015 RW 003 DS. PELAMUNAN SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 6, 'FIRE SUPERINTENDENT', 7, 2, NULL, NULL, '3', NULL),
(556, '2000053', 'NURIL MUSTAROKHAH', '2000053@yourdomain.com', NULL, '$2y$10$ROYlvsY7n7XgyumVDcfEfee567zMLo84d.2AkQvF7j1xai7iC83Tq', NULL, NULL, NULL, 'PERUM LEMBAH BAJA SEJAHTERA BLOK E NO. 7 RT 006 RW 001 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 11, 'MANAGEMENT SYSTEM OFFICER', 11, 3, NULL, NULL, '4', NULL),
(557, '2000055', 'ELLY YULIUS MINDARA', '2000055@yourdomain.com', NULL, '$2y$10$8mdCRBUUBe8nL0/y1vq0B.ZhajsfTdxdWU.hBKCgPONukYADDbJKy', NULL, NULL, NULL, 'GRIYA PERMATA ASRI BLOK E2 NO. 7 RT 002 RW 004 KEL. DALUNG SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 8, 'MANAGEMENT SYSTEM SENIOR OFFICER', 11, 3, NULL, NULL, '3', NULL),
(558, '2000063', 'BAMBAM IBRAHIM', '2000063@yourdomain.com', NULL, '$2y$10$CK5exnqXzo.k.7ObPf0CLeHmJrgGCmOVboEnpkf.6UdS0ZeIbecay', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 5, 'INTERNAL AUDIT MANAGER', 3, 1, NULL, NULL, '2', NULL),
(559, '2000064', 'ANNA LESTIANA', '2000064@yourdomain.com', NULL, '$2y$10$Me4UWthu.rqXjUH6y1pvou0et4AAQiKBLQYVS8451Co24pDIzdMbu', NULL, NULL, NULL, 'PCI BLOK E25 NO. 4 RT 002 RW 007 KEL. KEDALEMAN KEC. CIBEBER CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 5, 'PROJECT CONTROL MANAGER', 6, 2, NULL, NULL, '2', NULL),
(560, '2000067', 'FATHULLAH', '2000067@yourdomain.com', NULL, '$2y$10$LOt1Mw07ynAGw0h8WvMgwutHnkFiKwSkFmZUALFLwVOOF4Z0Nr0FG', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', NULL, 'QUALITY ASSURANCE SENIOR AUDITOR', 3, 1, NULL, NULL, '3', NULL),
(561, '2000069', 'SITI NUROHMATUN', '2000069@yourdomain.com', NULL, '$2y$10$xLgefkw1169rWCJ7Uxwma.m6oHNVublYtBwOLZ0Xh4.7WDd29IK9a', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 8, 'PROCUREMENT SENIOR OFFICER', 10, 3, NULL, NULL, '3', NULL),
(562, '2000070', 'MUHAMMAD ZAKKI', '2000070@yourdomain.com', NULL, '$2y$10$8NRBQp8HK8lqBAea2r1VROtvvbMD4IgW7Qwn6MH4iI1bll6VI8tJy', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 5, 'PROCUREMENT MANAGER', 10, 3, NULL, NULL, '2', NULL),
(563, '2000073', 'NANA STIANA', '2000073@yourdomain.com', NULL, '$2y$10$gb/sehCDOTlRuZBCcqsL1OCu99dO7.clvSnhJnFu5hwGYZv3J/7UC', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 8, 'SALES IE & HOUSING SENIOR OFFICER', 13, 2, 1, NULL, '3', NULL),
(564, '2000075', 'AGUNG LAKSONO NUGROHO', '2000075@yourdomain.com', NULL, '$2y$10$f7SV3cL87cg1hUAiYCyBUOuTDoq00biBVrne9FpPj5EQ2IP1iYfja', NULL, NULL, NULL, 'VILLA PERMATA HIJAU J1/9 RT 002 RW 007 DESA SERDANG SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 5, 'GOLF & SPORT CENTER MANAGER', 17, 2, 1, NULL, '2', NULL),
(565, '2000084', 'ANDI AZIS. US', '2000084@yourdomain.com', NULL, '$2y$10$T9QmUiuUioLN4uWtw4F2DO0eBhZud1OaXDODytIiEQ9r0XwRECxgG', NULL, NULL, NULL, 'KOMP TAMAN CILEGON INDAH BLOK J 12 NO. 33 RT 004 RW 005 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 9, 'SECURITY SECTION HOUSING AREA SUPERVISOR', 7, 2, NULL, NULL, '4', NULL),
(566, '2000087', 'JOKO SUPRIANTO', '2000087@yourdomain.com', NULL, '$2y$10$9HZKVu7gi3GoFhQYZbtBeOp5t0Qlue5l7UFZIWbgnq4gZok7Jcktm', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:32:59', '2025-07-14 08:32:59', 6, 'BUILDING & OFFICE OPERATION SUPERINTEN', 15, 2, 1, NULL, '3', NULL),
(567, '2000088', 'M. SYAFEI', '2000088@yourdomain.com', NULL, '$2y$10$Tqa3/f1iR7uZ33oq99k6FuPYJLpd/WGwS/N5WooXx4QgDOYXEj./2', NULL, NULL, NULL, 'KP. SAMPANG II RT 003 RW 001 KEL. TERUMBU KEC. KASEMEN SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 10, 'PROJECT CONTROL ENGINEER', 6, 2, NULL, NULL, '4', NULL),
(568, '2000090', 'AYEP SOFYAN', '2000090@yourdomain.com', NULL, '$2y$10$rJUPLCdMln3/nqOb8IQFZOIkzmDUfMwxR05MVHbpn0x/LNzME7cBy', NULL, NULL, NULL, 'LINK CIBERKO RT 002 RW 003 KEL. KALITIMBANG KEC. CIBEBER CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 9, 'MECH./ELEC./CIVIL MAINT. SUPERVISOR', 17, 2, 1, NULL, '4', NULL),
(569, '2000093', 'SRI DEWI NUGRAHA', '2000093@yourdomain.com', NULL, '$2y$10$5OUggkGVR8btkGglUKa3NOK6uzBenBLItAQVTdKvyZ/67GuJor4Ni', NULL, NULL, NULL, 'TAMAN KRAKATAU BLOK G23 NO. 37 RT 005 RW 007 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 11, 'MARKETING IE & HOUSING OFFICER', 13, 2, 1, NULL, '4', NULL),
(570, '2000094', 'HERMAN AJI', '2000094@yourdomain.com', NULL, '$2y$10$WN.xq9Av33rbr8gfXCQmouAC5KHrum/9oxKsqNHucs0nFMHCqOCKi', NULL, NULL, NULL, 'PERUM BUMI RAKATA ASRI CLUSTER 6 BLOK F1 NO. 4 RT 006 RW 007 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 11, 'ESTATE MANAGEMENT AREA  OFFICER', 14, 2, 1, NULL, '4', NULL),
(571, '2000095', 'SUSAEDI', '2000095@yourdomain.com', NULL, '$2y$10$py6rWvli8YtVESfZRj651.IGQQMPyMffZ5AQFqf8HdDr8cTzuYOl2', NULL, NULL, NULL, 'KOMP. METRO VILLA BLOK C2 NO. 7 RT 004 RW 006 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 4, 'EXECUTIVE ASSISTANT MANAGEMENT HOTEL', NULL, NULL, NULL, NULL, '2', NULL),
(572, '2000097', 'RIZKIE WULANSARI', '2000097@yourdomain.com', NULL, '$2y$10$TbP64boyVZtgB92U5OOSW.CHhQ7CEjLUxXVnRs44pESpWk.LAprG.', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', NULL, 'QUALITY ASSURANCE AUDITOR', 3, 1, NULL, NULL, '4', NULL),
(573, '2000099', 'YONI HESTY PRABOWO', '2000099@yourdomain.com', NULL, '$2y$10$f85uE3w5i.IxeWWs07UrzObt8OVW12Q7lXqb6J4Sw/aSEnToVrTSC', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 5, 'HOUSEKEEPING MANAGER', 20, 2, 2, NULL, '3', NULL),
(574, '2000100', 'AGUS JOHANSYAH', '2000100@yourdomain.com', NULL, '$2y$10$T2B/ulo3MNf35W44RIcNeeLCDC9O2Wduw7ygxiF.AtTQ7xlB8KiFG', NULL, NULL, NULL, 'KOMP BUMI RAKATA ASRI BLOK A8 NO. 16 RT 001 RW 007 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 9, 'CIVIL & HOUSEKEEPING SUPERVISOR', 15, 2, 1, NULL, '4', NULL),
(575, '2000102', 'SATIBI', '2000102@yourdomain.com', NULL, '$2y$10$M3KDHDdhJnBFWRSeTSzHkusoJbSt4mYvhwc6CcT2eVlFxMQYYSaqq', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 8, 'PROCUREMENT SENIOR OFFICER', 10, 3, NULL, NULL, '3', NULL),
(576, '2000104', 'TB. BENNY KURNIAWAN', '2000104@yourdomain.com', NULL, '$2y$10$G/WMdk.337cRx975OiUEaeE6/gpK9k1glGeHoQvLCx6qgXR/jzbfK', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 5, 'IT & MANAGEMENT SYSTEM MANAGER', 11, 3, NULL, NULL, '2', NULL),
(577, '2000105', 'SLAMET SUHARTONO', '2000105@yourdomain.com', NULL, '$2y$10$HHOghyVUK2Ssb3SYg4NOE.7tdkmWrfPYx7Hn7VEHt8aQqVjkFemVq', NULL, NULL, NULL, 'PERMATA BANJAR ASRI BLOK A7 NO. 10 RT 002 RW 009 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 6, 'GOLF MAINTENANCE SUPERINTENDENT', 17, 2, 1, NULL, '3', NULL),
(578, '2000106', 'VALDY ROESMAHYONO', '2000106@yourdomain.com', NULL, '$2y$10$uhlwQ1FIpYGtKXCDmoTDLeA4VXIm1JhZaVA5q8MamdHtqhcofd3oy', NULL, NULL, NULL, 'BBS III BLOK E6 NO. 10 RT 017 RW 009 KEL. CIWADUK KEC. CILEG CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 5, 'MARKETING IE & HOUSING MANAGER', 13, 2, 1, NULL, '2', NULL),
(579, '2000107', 'HENDI RUSTADI', '2000107@yourdomain.com', NULL, '$2y$10$JZVdn/Hwvjzj5/gMW9bd3OFhZ4hKCil73Qn1sh4BeKBIrQWXpA.bW', NULL, NULL, NULL, 'BUMI RAKATA ASRI BLOK A9-10 CLUSTER 1 RT 001 RW 007 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 5, 'FINANCE  MANAGER', 8, 3, NULL, NULL, '2', NULL),
(580, '2000108', 'HANDI HARIVAN', '2000108@yourdomain.com', NULL, '$2y$10$cd3lkWbuuo1gUPEnOI6ebuKdfcydycrq15rq1NFQB5oMgKBs1L5oC', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 5, 'ENGINEERING & BUSINESS DEV. MANAGER', 23, 2, 2, NULL, '2', NULL),
(581, '2000109', 'VERDI WIANSYAH', '2000109@yourdomain.com', NULL, '$2y$10$HqYKgw99TcAT1h8S5iqoFuGMx2TIE54TV0WsaawKzHG20yEVwoISO', NULL, NULL, NULL, 'JL. BUKIT LOTUS NO. 28 BUKIT PALEM RT 006 RW 010 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 8, 'MARKETING IE & HOUSING SENIOR OFFICER', 13, 2, 1, NULL, '3', NULL),
(582, '2000111', 'LARASATI', '2000111@yourdomain.com', NULL, '$2y$10$DeGMvmgAGh/iT/mvMkVFDe.QC8//sHVGFCNQGd0x/UozqayLjYQxu', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 11, 'CORPORATE COMMUNICATION OFFICER', 1, 1, NULL, NULL, '4', NULL),
(583, '2000114', 'YAYAD SURADI', '2000114@yourdomain.com', NULL, '$2y$10$9hFb9/B9pR.ykjTY/rEBZe6FAJc4BkpXHhfcjMEMh0gfe5ok4CDJ6', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 14, 'ESTATE MANAGEMENT AREA  JUNIOR OFFICER', 14, 2, 1, NULL, '5', NULL),
(584, '2000116', 'NURYASIN MUSTOFA KASTOERI', '2000116@yourdomain.com', NULL, '$2y$10$0NGyVICr.jDe8duKhN8PK.fDqdwC4H/vquRLbgCXmm9Fx1bRi9.IC', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:00', '2025-07-14 08:33:00', 6, 'COMMERCIAL PURCHASING SUPERINTENDENT', 10, 3, NULL, NULL, '3', NULL),
(585, '2000119', 'CUCU NURDIN', '2000119@yourdomain.com', NULL, '$2y$10$zDj..avCbhs.nVHgzkHlG.YTELFhIWqtCAdjBB/4ETFkjpDfvTjN.', NULL, NULL, NULL, 'KOMP. BUMI RAKATA ASRI BLOK G6 NO. 11 RT 007 RT 007 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 15, 'CHIEF STEWARD', 22, 2, 2, NULL, '4', NULL),
(586, '2000120', 'YOHA ARISTIAN', '2000120@yourdomain.com', NULL, '$2y$10$D6YYyfJ6uYTJQKVYy9VzmuJPB4AVusuamjyhwk5k4wDyqImbkuRd.', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', NULL, 'FINANCIAL & OPERATION SENIOR AUDITOR', 3, 1, NULL, NULL, '3', NULL),
(587, '2000122', 'KHAERUDIN', '2000122@yourdomain.com', NULL, '$2y$10$xVAE7L6ylCzYYvIbM255xevJEoSo7QK9xuMc0d9BUAK0eoylhl9qW', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 9, 'SECURITY SECTION BUILDING AREA SPV', 15, 2, 1, NULL, '4', NULL),
(588, '2000130', 'HAMAMI A', '2000130@yourdomain.com', NULL, '$2y$10$m8Y5U3adP1RG93pDwKiHn.shty4hWIz..a1BT6Yc2WQjDIFgODtfq', NULL, NULL, NULL, 'KP. LARANGAN RT 005 RW 002 DESA HARJATANI SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 9, 'SECURITY INDUSTRIAL AREA SUPERVISOR', 7, 2, NULL, NULL, '4', NULL),
(589, '2000132', 'WAWAN SETIAWAN', '2000132@yourdomain.com', NULL, '$2y$10$EcG/Y.dIlCVoCcMWWVCDnez.evdTIzrLHV2fiFqwsh0JSxplekN/2', NULL, NULL, NULL, 'LINK SUMAMPIR TIMUR RT 005 RW 004 KEL. KEBONDALEM CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 9, 'HOUSEKEEPING FASILITIES AREA SUPERVISOR', 20, 2, 2, NULL, '5', NULL),
(590, '2000137', 'MAD SAFE\'I', '2000137@yourdomain.com', NULL, '$2y$10$c2is/DEvsFFq4BnODtJDG.UdDzOp80J6RVpvE.e4.BX/99lyJc44K', NULL, NULL, NULL, 'LINK BEBULAK TIMUR RT 002 RW 006 KEL. KEBON DALEM CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', NULL, 'CLUB MANAGEMENT GROUP', 17, 2, 1, NULL, '5', NULL),
(591, '2000138', 'FATUROJI', '2000138@yourdomain.com', NULL, '$2y$10$04W6mMYHDKaI/F6FjtVAB.9qrjqyOP1onK8ASUz05zkFJP1tAjw.2', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 14, 'HOUSING MANAGEMENT JUNIOR OFFICER', 14, 2, 1, NULL, '5', NULL),
(592, '2000140', 'BACHTIAR SOLEH', '2000140@yourdomain.com', NULL, '$2y$10$W1mFau9OXS4uEK/Vmk5WHezGt.QzF23aSXnE/I.VUrGR56t.rriE2', NULL, NULL, NULL, 'LINK CIRIU RT 002 RW 003 KEL. SAMANGRAYA KEC. CITANGKIL CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 5, 'INDUSTRIAL ESTATE & HOUSING MANAGER', 14, 2, 1, NULL, '2', NULL),
(593, '2000142', 'AAN SUHAEMI', '2000142@yourdomain.com', NULL, '$2y$10$/yKimwQLLN0C.yNkcnV6AeYCYAkrdoqJiV1grZEWnzJPba1IrMEGi', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 7, 'PROJECT CONTROL SENIOR ENGINEER', 6, 2, NULL, NULL, '3', NULL),
(594, '2000143', 'AMPERA, US.', '2000143@yourdomain.com', NULL, '$2y$10$QNa8ZADs0BxrLUWoL2vbUevYmKvNXQi3DFNr2ceIOGGW1nt0zILru', NULL, NULL, NULL, 'LINK SUMAMPIR TIMUR RT 004 RW 004 KEL. KEBON DALEM CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 12, 'MECHANICAL & ELECTRIC FOREMAN', 17, 2, 1, NULL, '5', NULL),
(595, '2000144', 'SUHARNO', '2000144@yourdomain.com', NULL, '$2y$10$8yxrBCKbqewbql7b8GJhaeiFFSGJBRKFq0T7ImBniYnRssF13JPCm', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 8, 'WAREHOUSE & COM BUILDING SENIOR OFFICER', 14, 2, 1, NULL, '3', NULL),
(596, '2000145', 'IRFAN HARTAJI', '2000145@yourdomain.com', NULL, '$2y$10$jjjrUwdixkHQEJpk5/nPf.wkNX8L96pGSm89SCw49fMDs0XJECAq6', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 9, 'HOUSEKEEPING FASILITIES AREA SUPERVISOR', 20, 2, 2, NULL, '5', NULL),
(597, '2000146', 'DIKI MEISANO SYAEFUDIN', '2000146@yourdomain.com', NULL, '$2y$10$VaJOewsJKcVHU28u/6sIlOZTDVGPEpQL3XIbZUR/lB79qq8OP418e', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 6, 'SALES & MARKETING RE SUPERINTENDENT', 16, 2, 1, NULL, '3', NULL),
(598, '2000150', 'ARIFA NORMA DEWI', '2000150@yourdomain.com', NULL, '$2y$10$ySF9gApHlgdxQ6KLeATlxe1QuL79arO/BMiklQDxUWeg.1LAfr20W', NULL, NULL, NULL, 'JL. SANTANI NO. 6A KOMP KS RT 002 RW 001 KEL. KOTABUMI CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 5, 'CORPORATE SECRETARY MANAGER', 1, 1, NULL, NULL, '2', NULL),
(599, '2000154', 'WISNU SASONGKO PUTRO', '2000154@yourdomain.com', NULL, '$2y$10$uxZCPFNk1dDQKo005bDZ1uVr/6cygQoOuUHnmDHt7PB6YojBjaN3a', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 5, 'REAL ESTATE MANAGER', 16, 2, 1, NULL, '2', NULL),
(600, '2000162', 'ANTOMI PURNAWAN', '2000162@yourdomain.com', NULL, '$2y$10$MH1BF0wm9nljmPlyz0EYhOj58eITx51wkyvNeOUM98JZRzOuHYCJy', NULL, NULL, NULL, 'TAMAN PURI INDAH BLOK D14 NO. 8 RT 005 RW 017 KEL. SERANG SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 6, 'SPORT CLUB SUPERINTENDENT', 17, 2, 1, NULL, '3', NULL),
(601, '2000164', 'YUNI WITRIANI', '2000164@yourdomain.com', NULL, '$2y$10$fkHjgelnBMuXcVKOfyHV4ucOYb2YUtc9wlneo4pP71WV9aN1vVXn6', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 11, 'PROCUREMENT OFFICER', 10, 3, NULL, NULL, '4', NULL),
(602, '2000166', 'GUNADI', '2000166@yourdomain.com', NULL, '$2y$10$YnJae7jk6mJwIqxJSIHgFOlRTXNlhrfQc6YuTEM9X.5I51ERsxmUC', NULL, NULL, NULL, 'LINK. PENGAIRAN BARU RT 006 RW 008 KEL. KOTABUMI CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 6, 'SECURITY SUPERINTENDENT', 12, 3, NULL, NULL, '3', NULL),
(603, '2000167', 'SAHIDI', '2000167@yourdomain.com', NULL, '$2y$10$zOPLgiC02QHWSTPvpjEnEuyWl6dUKhdq1aHQOU14r49MJRJ8Fy3A.', NULL, NULL, NULL, 'LINK. KP BARU RT 001 RW 003 KEL KEBON DALEM CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:01', '2025-07-14 08:33:01', 14, 'ESTATE MANAGEMENT AREA  JUNIOR OFFICER', 14, 2, 1, NULL, '5', NULL),
(604, '2000168', 'NURDIN', '2000168@yourdomain.com', NULL, '$2y$10$JfwzIel07TxZkS90iNHAaeLPM8WQGPodAbDkKxRFXA1lnmvWKudO.', NULL, NULL, NULL, 'LINK. SUMAMPIR TIMUR RT 005 RW 004 KEL. KEBON DALEM CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 14, 'ESTATE MANAGEMENT AREA  JUNIOR OFFICER', 14, 2, 1, NULL, '5', NULL),
(605, '2000169', 'WAHYUDI', '2000169@yourdomain.com', NULL, '$2y$10$2LfORezI7mI/4N21Y0BgaOt3ftj1qwy.R6byH58bTc1wFXHvN4U02', NULL, NULL, NULL, 'LINK. WARUNG JUET RT 003 RW 002 KEL. SAMANGRAYA CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 11, 'LICENCES OFFICER', 2, 1, NULL, NULL, '4', NULL),
(606, '2000171', 'DONNY FAIZAL', '2000171@yourdomain.com', NULL, '$2y$10$T6teO2mwfq4vcofsfqh6KebvPwIuvuagPTw6b6deFznoa4d5HcBeC', NULL, NULL, NULL, 'LINK. METRO CENDANA BLOK M.1 NO 12 RT 002 RW 009 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 11, 'INFORMATION TECHNOLOGY OFFICER', 11, 3, NULL, NULL, '4', NULL),
(607, '2000172', 'AJUDIN ISRO', '2000172@yourdomain.com', NULL, '$2y$10$CgobJBPYbSB7LbnPbIQVsuyA4qTZeNy/dnwKWZmRLx6TEOlNaX2.O', NULL, NULL, NULL, 'LINK PENGAIRAN BARU RT 006 RW 008 KEL. KOTABUMI CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 11, 'ESTATE MANAGEMENT AREA  OFFICER', 14, 2, 1, NULL, '4', NULL),
(608, '2000173', 'HADIAN HERDIANSYAH', '2000173@yourdomain.com', NULL, '$2y$10$jWR1JlLyxP93lNb/S81LVu.33M6uJwvkWh4JebWi.fOnxAD/4KBaq', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 9, 'SECURITY INDUSTRIAL AREA SUPERVISOR', 7, 2, NULL, NULL, '4', NULL),
(609, '2000176', 'SAMSUL NANDAR', '2000176@yourdomain.com', NULL, '$2y$10$Rfw2vLP8R4IUE9YNh5Pao.Rn8NqID0qc55uOVAPcjvU1fVglUX52e', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 14, 'HOUSING MANAGEMENT JUNIOR OFFICER', 14, 2, 1, NULL, '5', NULL),
(610, '2000177', 'HENI MULYANI', '2000177@yourdomain.com', NULL, '$2y$10$6OoIFK6u2SOYlyLHiwP4huVWsgy8ZYcWq4N9EdAmwRWyw98.fR33q', NULL, NULL, NULL, 'TAMAN KRAKATAU BLOK G26 NO. 9 RT 003 RW 007 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 6, 'FUNDING & COLLECTION SUPERINTENDENT', 8, 3, NULL, NULL, '3', NULL),
(611, '2000179', 'SALAHUDIN', '2000179@yourdomain.com', NULL, '$2y$10$H7GlCCyJ.IRHuNw07KZnVO/H47tZeV07xacCc.qafrxAlnkkgDwBi', NULL, NULL, NULL, 'KP. SIRIH RT 008 RW 001 DS. KAMASAN KEC. CINANGKA SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 6, 'GOLF CLUB SUPERINTENDENT', 17, 2, 1, NULL, '3', NULL),
(612, '2000181', 'JEANI ROSDIANI', '2000181@yourdomain.com', NULL, '$2y$10$7zFWc0pY.2b516ndIByNvOqoA7MMapv1JFIPL.Zl7bt/nqzhwcSsq', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 11, 'SALES IE & HOUSING OFFICER', 13, 2, 1, NULL, '4', NULL),
(613, '2000183', 'MUSTAUFIK HIDAYATULLOH', '2000183@yourdomain.com', NULL, '$2y$10$wdrfgIoUS.7h20TAt5x93uFLCkI8tGp0.4kkGw1T7A5KBuKvWg/Gi', NULL, NULL, NULL, 'LINK KRENCENG RT 003 RW 004 KEL. KEBONSARI KEC. CITANGKIL CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 9, 'SECURITY SECTION PATROL SUPERVISOR', 7, 2, NULL, NULL, '4', NULL),
(614, '2000185', 'EDI YANTO', '2000185@yourdomain.com', NULL, '$2y$10$R1a3pd.wrETERd90WfxvKOALxTvGS.YMnzolRVn3PsAHtL9uotmXG', NULL, NULL, NULL, 'LINK BENTOLA RT 004 RW 001 KEL. BULAKAN KEC. CIBEBER CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 10, 'PROJECT CONTROL ENGINEER', 6, 2, NULL, NULL, '4', NULL),
(615, '2000186', 'SALMAN', '2000186@yourdomain.com', NULL, '$2y$10$HvYTnG4JyJp0zUKr8jTWcegMYAr7LOSpwzDh8HeVsCSeP8MBdTQKW', NULL, NULL, NULL, 'PERUM BUMI CIBEBER KENCANA BLOK D1 NO. 17 RT 001 RW 008 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 5, 'ENGINEERING MANAGER', 5, 2, NULL, NULL, '3', NULL),
(616, '2000187', 'AGUS WAHYUDI', '2000187@yourdomain.com', NULL, '$2y$10$sK4klqqGDegeuND75cJTn.6sRJTHqLNHHe/d9md9tK8LSrN7XKmCK', NULL, NULL, NULL, 'KOMP BUMI RAKATA ASRI CLUDTRE 7 BLOK G10 NO. 01 RT 007 RW 00 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', NULL, 'DUTY  MANAGER', 19, 2, 2, NULL, '4', NULL),
(617, '2000188', 'BUDI HARYADI', '2000188@yourdomain.com', NULL, '$2y$10$ZxnsHdHqzG5t8TXbS13IseVexsrSrlcf2mKiW1Q2AUOfE3nqBIFu.', NULL, NULL, NULL, 'KOMP. METRO MEDITERANIA BLOK B 10 NO. 8 RT 002 RW 010 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 9, 'DRIVING RANGE SUPERVISOR', 17, 2, 1, NULL, '4', NULL),
(618, '2000192', 'MUHAMMAD YUNUS', '2000192@yourdomain.com', NULL, '$2y$10$aOXYa2sUCsQ.xyhrfqW58uxB/giUCNaC8CIoFl86m27LeTUE9GqMa', NULL, NULL, NULL, 'METRO CILEGON GRAND CENDANA BLOK N17 NO. 02 RT 003 RW 010 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 9, 'LAUNDRY SUPERVISOR', 20, 2, 2, NULL, '5', NULL),
(619, '2000193', 'RULI KASRUDIN', '2000193@yourdomain.com', NULL, '$2y$10$47b34ENv5VlKNeLk9Uf3D.tYi9Et79wYywZxSV/dHrzJE644ZV7SG', NULL, NULL, NULL, 'JL. CIMANUK RAYA NO. 11 RT 007 RW 002 KEL. BAKTIJAYA DEPOK', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', NULL, 'THE SUROSOWAN RESTAURANT MANAGER', 21, 2, 2, NULL, '4', NULL),
(620, '2000194', 'SUPRIADI', '2000194@yourdomain.com', NULL, '$2y$10$NaQz1iNMf7I.nBhUCtaSFuiyr3SkAC17rs1ABbztb66G0HbZzQwHC', NULL, NULL, NULL, 'LINK TEGAL PADANG RT 003 RW 003 KEL. KEBON DALEM CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 9, 'HUMAN CAPITAL REMUNERATION SUPERVISOR', 12, 3, NULL, NULL, '4', NULL),
(621, '2000196', 'SUWANTO', '2000196@yourdomain.com', NULL, '$2y$10$VyPGLlFkuk0AZsb4EVkWTOGAM0iwtJHo2yX2xlScnlVdWBLJwor2m', NULL, NULL, NULL, 'TAMAN KRAKATAU BLOK G19 NO, 29 RT 004 RW 007 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:02', '2025-07-14 08:33:02', 9, 'RECREACTION & KRA. SPORT SUPERVISOR', 17, 2, 1, NULL, '4', NULL),
(622, '2000198', 'IVAN YULIAN', '2000198@yourdomain.com', NULL, '$2y$10$hThEoFe2Twyiep2luI648OQckNDXXYet1jPUgqbPtCGS3qBwDqcF2', NULL, NULL, NULL, 'TAMAN KRAKATAU BLOK I8 NO 23 RT 003 RW 006 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 5, 'FRONT OFFICE MANAGER', 19, 2, 2, NULL, '3', NULL),
(623, '2000199', 'NANANG MULYANA', '2000199@yourdomain.com', NULL, '$2y$10$mMmXdr63JlCmuwAZP24O/uOxYG2tLfk5ZTEO82eg7WWS3ZLCCxZrC', NULL, NULL, NULL, 'KAV BLOK J LINK PALAS RT 016 RW 001 KEL. BENDUNGAN CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 9, 'HOUSEKEEPING FASILITIES AREA SUPERVISOR', 20, 2, 2, NULL, '5', NULL),
(624, '2000200', 'RATIMAH', '2000200@yourdomain.com', NULL, '$2y$10$VFvZ2Up2UlVws.cSjOVLfu0XZyD3PhyFAaISidKHBcOfMJROR1Zne', NULL, NULL, NULL, 'TAMAN RAYA CILEGON BLOK D2 NO. 26 RT 004 RW 005 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 8, 'GENERAL AFFAIR SENIOR OFFICER', 12, 3, NULL, NULL, '3', NULL),
(625, '2000201', 'SACHRUL PURNAMA', '2000201@yourdomain.com', NULL, '$2y$10$oA2AIPU3jo3ly3ynRTeCB.Q7NKfm0Qe4Vj.6v0M4BvumFmB/s9hyG', NULL, NULL, NULL, 'JL. PAGARSIH GG. MASTABIR NO. 168/89 RT 005 RW 009 BANDUNG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 11, 'ESTATE MANAGEMENT AREA  OFFICER', 14, 2, 1, NULL, '4', NULL),
(626, '2000202', 'YAYAN JULIANSYAH', '2000202@yourdomain.com', NULL, '$2y$10$5Jf5HZklaCFavYwB/w2cf.arqqrp8kt52eCu3yzuhGkOkwuBACoOy', NULL, NULL, NULL, 'KOMP VILLA ASRI BLOK B4 NO. 03 RT 016 RW 001 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 11, 'GENERAL AFFAIR OFFICER', 12, 3, NULL, NULL, '4', NULL),
(627, '2000204', 'SUPRIYANTO', '2000204@yourdomain.com', NULL, '$2y$10$gdjTXYwAmZRk74QNQcj.4uOMiW2oW6kutkX6OqZSNas0sWfBzAX1q', NULL, NULL, NULL, 'KOMP TAMAN WARNASARI INDAH FWA 108 NO. 02 RT 002 RW 006 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', NULL, 'ASSISTANT HOUSEKEEPING', 20, 2, 2, NULL, '4', NULL),
(628, '2000206', 'SUHENDAR', '2000206@yourdomain.com', NULL, '$2y$10$FLj.IHq3Nywisg0pRZKKjewZMy1luu7hikfuwldg..IMoZ./lTBUG', NULL, NULL, NULL, 'LINK KALIGANDU BUJANG BOROS RT 014 RW 006 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 9, 'HOUSEKEEPING HOTEL AREA SUPERVISOR', 20, 2, 2, NULL, '5', NULL),
(629, '2000211', 'SARTIKA', '2000211@yourdomain.com', NULL, '$2y$10$2Ye6YPnYiRalRSJpe1CqY.n/6VC.gS5Hvt6ICYcHiZleTJfJGtlpG', NULL, NULL, NULL, 'JL. PEMBANGUNAN NO. 25 RT 004 RW 002 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 5, 'LEGAL & COMPLIANCE MANAGER', 2, 1, NULL, NULL, '2', NULL),
(630, '2000213', 'PANJI BUANA PRATAMA', '2000213@yourdomain.com', NULL, '$2y$10$OznmtAnGZ5RoF81IXo03lunSMw2BawTIvXrMc.WXp0TKuqPz/EzVC', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 7, 'ENGINEERING PLANNING SENIOR ENGINEER', 5, 2, NULL, NULL, '3', NULL),
(631, '2000216', 'SILVIANITA PERMANDA', '2000216@yourdomain.com', NULL, '$2y$10$WKHz4ZVnxPO.IH/PwsdjfugU2GPTNTgbxeG1wCukcfZASJ.Ni5b4O', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 11, 'HUMAN CAPITAL ORG. & DEV.  OFFICER', 12, 3, NULL, NULL, '4', NULL),
(632, '2000217', 'FIRLY DIANANDA', '2000217@yourdomain.com', NULL, '$2y$10$4Rfzbhh6lGYMQlOiW2DZOehrPDMjWXMl7.DEe1f84cjb8zeTvsHgi', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', NULL, 'HUMAN CAPITAL REMUNERATION SUPERINTENDEN', 12, 3, NULL, NULL, '3', NULL),
(633, '2000218', 'FARAH DINAH HANDRIANI', '2000218@yourdomain.com', NULL, '$2y$10$aOXx7SCnv6q368LcILXfL.qp7iN5cojIXSAsnnAHqW7pJXOMdSmqW', NULL, NULL, NULL, 'KOMP DAMPKAR JL GUNUNG KUPAK NO 10 RT RW 001 006 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 8, 'HUMAN CAPITAL ORG. & DEV. SENIOR OFFICER', 12, 3, NULL, NULL, '3', NULL),
(634, '2000223', 'YULIA C', '2000223@yourdomain.com', NULL, '$2y$10$7aDxr0q9/brz.oXvTO0Iu.h8D8heoPzpwTxzEc1ZhkrC4EXf1/fQK', NULL, NULL, NULL, 'LINK JOMBANG TANGSI RT 001 RW 002 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 6, 'FINANCIAL ACCOUNTING SUPERINTENDENT', 8, 3, NULL, NULL, '3', NULL),
(635, '2000225', 'WAHYUKA NUR CAHYA', '2000225@yourdomain.com', NULL, '$2y$10$4wm0r/titqlKS6wGHgSLEOCzxgXtr4NmEDFjRUmKMt26gINBDivXe', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', NULL, 'SAFETY, HEALTH & ENVIRONMENT SENIOR OFFI', 7, 2, NULL, NULL, '3', NULL),
(636, '2000226', 'LAZUARDI ARSY', '2000226@yourdomain.com', NULL, '$2y$10$RbMSEaVqwR5Kcwoz0aP8QuLLvRauvFFt91Hka2rIi2sfyxbXWdAp6', NULL, NULL, NULL, 'DUKUH BEDAGAN RT 002 RW 001 DESA PULUNG KEC. PULUNG PONOROGO', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 8, 'INFORMATION TECHNOLOGY SENIOR OFFICER', 11, 3, NULL, NULL, '3', NULL),
(637, '2000227', 'SITI BALQIS ARROHMAH', '2000227@yourdomain.com', NULL, '$2y$10$egDLFc7y8OTeOOJfTPVJHuosfi.6lVoKK4qYLiWKpk/KQ.ZHnIfAu', NULL, NULL, NULL, 'KOMP. BUMI MUKTI BLOK A6 NO 3-4 RT 003 RW 009 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 11, 'CORPORATE COMMUNICATION OFFICER', 1, 1, NULL, NULL, '4', NULL),
(638, '2000229', 'MUCHAMMAD FACHRI MAULANA', '2000229@yourdomain.com', NULL, '$2y$10$nh/pUIZStNtNvXzAudTOcOuScLgW1muV6lxHUbSh3EmOjH4v40w/y', NULL, NULL, NULL, 'JL. SURATAN AMD NO. 3 RT 004 RW 004 KEL. KRANGGAN MOJOKERTO', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 5, 'ACCOUNTING  MANAGER', 9, 3, NULL, NULL, '2', NULL),
(639, '2000230', 'IRVAN HANAFI', '2000230@yourdomain.com', NULL, '$2y$10$rPBU5M41KYyuXJtLRxlt/e/3R2jVCja.aZZzcgJFABZCx8dHYnPKO', NULL, NULL, NULL, 'KP. CEPERSARI RT 004 RW 005 KEL. SRONDOL KULON SEMARANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 10, 'PROJECT CONTROL ENGINEER', 6, 2, NULL, NULL, '4', NULL),
(640, '2000231', 'RATU NIA NIHLATUN NISA', '2000231@yourdomain.com', NULL, '$2y$10$i9uqCaRbT8gmsXyxt7CPGOXnyo4UrwcQybMGEnzezOWPH7vgfHyQi', NULL, NULL, NULL, 'KOMP. DEPAG BLOK J NO. 12 RT 002 RW 007 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:03', '2025-07-14 08:33:03', 11, 'ESTATE MANAGEMENT AREA  OFFICER', 14, 2, 1, NULL, '4', NULL),
(641, '2000233', 'RAHMATIKA', '2000233@yourdomain.com', NULL, '$2y$10$4T5NN0OBoyHqz8L5iTxWJ.lvzav8ca3paf7yaC4WLRrKnARINoPJC', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 8, 'BUSINESS DEVELOPMENT SENIOR OFFICER', 4, 1, NULL, NULL, '3', NULL),
(642, '2000234', 'NIA AZIZAH', '2000234@yourdomain.com', NULL, '$2y$10$EWxOIKTLyJXj2lOHfpaT5uUrXbpJ6nDxgCwxfpT/y6cocQpylVkzm', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 14, 'COM. DEV & CSR JUNIOR OFFICER', 1, 1, NULL, NULL, '5', NULL),
(643, '2000235', 'JON HERI', '2000235@yourdomain.com', NULL, '$2y$10$S0Ay/W.o0qO3VAHJYQv0COhzpHu1fQxNL5Nz3zvMQJgdzg6ZwwTA6', NULL, NULL, NULL, 'RT 006/006 WARNASARI CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 11, 'PROCUREMENT OFFICER', 10, 3, NULL, NULL, '4', NULL),
(644, '2000236', 'TRI SEPTIAWATI WIRDANINGSIH', '2000236@yourdomain.com', NULL, '$2y$10$na7w7OXnWhpKXRBA99RiKe351crlNtd13QfEuvFFGU9ZaLT2jCy.q', NULL, NULL, NULL, 'KERTA BANJARSARI LEBAK', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 9, 'FUNDING & COLLECTION SUPERVISOR', 8, 3, NULL, NULL, '4', NULL),
(645, '2002043', 'ARDIAN', '2002043@yourdomain.com', NULL, '$2y$10$Zxq5..djdI/FqpTTUPs5COug4/.wyGs65/w4pQ/8/JiBb6FkWFBEK', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 12, 'SECURITY SECTION BUILDING AREA FOREMAN', 15, 2, 1, NULL, '5', NULL),
(646, '2002048', 'ENDANG MANSYUR', '2002048@yourdomain.com', NULL, '$2y$10$CqdHTwurlu4297QKtNvdmeHbBEc8cIRV9BWqa1RyPDCM6EaozWub2', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 9, 'CIVIL & HOUSEKEEPING SUPERVISOR', 15, 2, 1, NULL, '4', NULL),
(647, '2002049', 'PAINO', '2002049@yourdomain.com', NULL, '$2y$10$y1Q0MgFLejFseBXcw8Cbv.N9ExARgJySxboWHkQurKdPuub0g9YVC', NULL, NULL, NULL, 'JL. H UMAR KRUKUT RT 002 RW 001 KEL. KRUKUT KEC LIMO DEPOK', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 14, 'MARKETING IE & HOUSING JUNIOR OFFICER', 13, 2, 1, NULL, '5', NULL),
(648, '2100200', 'INA KURNIAWATI AMELIA', '2100200@yourdomain.com', NULL, '$2y$10$I8TsVIyvJCoKzKAQsFa4cuT7Rk5BkikAsyaXVCYUr4163T5sIlw/a', NULL, NULL, NULL, 'PERUM ARGA BAJA PURA JL. ARGA MERBABU BLOK B2 NO 11 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 9, 'HUMAN CAPITAL REMUNERATION SUPERVISOR', 12, 3, NULL, NULL, '4', NULL),
(649, '2100202', 'GUMILAR SUGANDI', '2100202@yourdomain.com', NULL, '$2y$10$hLlCDXWQtocy.9wUNbX0H.8AJ1vx.TnTromY0NrbQB.jThqY3/Ijm', NULL, NULL, NULL, 'KOMP. ARGA BAJA PURA JL ARGA RAUNG BLOK B3 NO 27 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 3, 'COMMERCIAL PROPERTY GENERAL MANAGER', NULL, NULL, NULL, NULL, '1', NULL),
(650, '2100214', 'BAY BOWIE', '2100214@yourdomain.com', NULL, '$2y$10$7/i/GEPWZBrR4nXju9FT5e6eHjEpAtXB1X/o2CMaWeK7/dVSAXbam', NULL, NULL, NULL, 'KP. LEBAK SENGGE RT RW 004 001 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 11, 'INFORMATION TECHNOLOGY OFFICER', 11, 3, NULL, NULL, '4', NULL),
(651, '2100256', 'ADITYA DARMAWAN', '2100256@yourdomain.com', NULL, '$2y$10$C47UyqTFuiN/rWuWRPzGNOZ5vwlyoGJgpLbBn1FXs6JfHFUMdMXGy', NULL, NULL, NULL, 'PERUMAHAN BUMI RAKATA ASRI CLUSTER 5 BLOK E2 NO. 29 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 10, 'ENGINEERING PLANNING ENGINEER', 5, 2, NULL, NULL, '4', NULL),
(652, '2110501', 'MASLIANDRI', '2110501@yourdomain.com', NULL, '$2y$10$E9HGzNGEAAyLgcAkTZzPtujlOn5yeuHYx1OtjIR76f7u/P2qupktq', NULL, NULL, NULL, 'KOMP. D-FLAT KS. JL. TEKUKUR NO. 55 RT 004 RW 005 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 11, 'MNTC FIRE, SFTY EQUIPMENT & FIRE INSPCTR', 7, 2, NULL, NULL, '4', NULL),
(653, '2110670', 'LUKMAN HAKIM', '2110670@yourdomain.com', NULL, '$2y$10$.C/CDAHIQNERI7FNG20e8eDnNu.RpHHZecgO3oqFuNzMchdtD2uAu', NULL, NULL, NULL, 'LINK WERI RT 001 RW 002 KEL. KEBONSARI KEC. CITANGKIL CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 8, 'HOUSING MANAGEMENT SENIOR OFFICER', 14, 2, 1, NULL, '3', NULL),
(654, '2110922', 'SYAMSUL MA\'ARIF', '2110922@yourdomain.com', NULL, '$2y$10$ezzCZ3nOKKr4J/KEO0bti.c5gBWiQ.uFTEdeResEHXsZmRyLoUGAW', NULL, NULL, NULL, 'PURI HIJAU REGENCY RT RW 005 002 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 8, 'COM. DEV & CSR SENIOR OFFICER', 1, 1, NULL, NULL, '3', NULL),
(655, '2111267', 'CUCU LASUBAN', '2111267@yourdomain.com', NULL, '$2y$10$Me9BafgDxPAgIB4tAVYfMub39hUMswgiKw8No1XvUmGLxl/6p/6NW', NULL, NULL, NULL, 'LINK ACING BARU RT 001 RW 007 KEL. MASIGIT KEC. JOMBANG CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 9, 'FIRE & DISASTER PREVENTION SUPERVISOR', 7, 2, NULL, NULL, '4', NULL),
(656, '2111268', 'HERI HERYANA', '2111268@yourdomain.com', NULL, '$2y$10$oeY4iHiQegD2a.syojeTROVR.nCKzw49V2U3Gs53VbBh7xUfrzcN.', NULL, NULL, NULL, 'PURI KRAKATAU HIJAU BLOK E4 NO. 20 RT 014 RW 006 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 12, 'FIRE & DISASTER PREVENTION FOREMAN', 7, 2, NULL, NULL, '5', NULL),
(657, '2111305', 'MUHLAS', '2111305@yourdomain.com', NULL, '$2y$10$8OE.rlvjGxMdIpoL8ACUOu3F7suHlNlUnnrQWLHf7UAH4DpsoeEbK', NULL, NULL, NULL, 'LINK GUNUNG WATU RT RW 004 001 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', NULL, 'MECH./ELEC./CIVIL MAINT. GROUP', 15, 2, 1, NULL, '5', NULL),
(658, '2111460', 'ANDIKA BRANIDEL', '2111460@yourdomain.com', NULL, '$2y$10$s5paRwNxubQ5.hyU22miDujeQT.c9kYrGJoHwhRhv.fBCeL6fcIxq', NULL, NULL, NULL, 'KOMP. IKIP BLOK A NO. 4 RT 002 RW 014 JAKARTA TIMUR', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 8, 'ESTATE MANAGEMENT AREA SENIOR OFFICER', 14, 2, 1, NULL, '3', NULL),
(659, '2112079', 'RIDWAN FIRMANSYAH', '2112079@yourdomain.com', NULL, '$2y$10$/GcMZ3F1tuQ1xdAeAMhOyeKCY76C9eyXTYWhDOaBUS2RO4O9JT7lG', NULL, NULL, NULL, 'DUNGUSCARIANG NO. 163/79 RT RW 009 007 KOTA BANDUNG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:04', '2025-07-14 08:33:04', 9, 'ENGINEERING SUPERVISOR', 23, 2, 2, NULL, '5', NULL),
(660, '2112298', 'EKA IRAWAN, ST', '2112298@yourdomain.com', NULL, '$2y$10$YEEs5iIvB6e7JZ9SesgqD.DkpNtOBIctp1d2UBV4Z4vs4FWo9FcK2', NULL, NULL, NULL, 'JL. MORSE NO. 04 RT RW 001 001 KEL KOTABUMI CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 5, 'SECURITY, FIRE & SHE MANAGER', 7, 2, NULL, NULL, '2', NULL),
(661, '2112478', 'KHILMAN', '2112478@yourdomain.com', NULL, '$2y$10$PyX57dTvnDHk3UQyRpBIN.wr2Qu2nwXEINl7HF3iCBhMnVQIV7ctK', NULL, NULL, NULL, 'PERUM. TAMAN PESONA BLOK. AA4 NO.4 RT RW 001 010 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 12, 'MECHANICAL & ELECTRIC FOREMAN', 15, 2, 1, NULL, '5', NULL),
(662, '2112533', 'MOHAMAD FIKRI NAUFAL', '2112533@yourdomain.com', NULL, '$2y$10$ss4j0i2CZzCGkYh.9zg1DuN0HyTa.cQxRP.LquJ9Kli60hzwGl5l2', NULL, NULL, NULL, 'TAMAN KRAKATAU ZONA BRUXELS BLOK B2 NO. 25  RT RW 001 008 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 13, 'PROJECT CONTROL JUNIOR ENGINEER', 6, 2, NULL, NULL, '5', NULL),
(663, '2112559', 'ALAN ACHMAD KURNIAWAN', '2112559@yourdomain.com', NULL, '$2y$10$hzV1g6wZkpQvaeRhMZKMROIs55PWga5sLZqfMuqQlbaEs4z9iwQVC', NULL, NULL, NULL, 'LINK JOMBANG WETAN RT RW 005 005 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 14, 'ESTATE MANAGEMENT AREA  JUNIOR OFFICER', 14, 2, 1, NULL, '5', NULL),
(664, '2112727', 'JAMAN FADRI', '2112727@yourdomain.com', NULL, '$2y$10$Upd3d0F2QtU0mKHp6dWjxObgW5aCUu3o.IJXm1PR3gL0mSXz5ZqwK', NULL, NULL, NULL, 'LINK CIKERUT RT RW 005 004 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 14, 'HOUSING MANAGEMENT JUNIOR OFFICER', 14, 2, 1, NULL, '5', NULL),
(665, '2113222', 'M. PRASETYO PERMADI', '2113222@yourdomain.com', NULL, '$2y$10$CeQ9iuJ8Fv/Sqh0fiPVR4eTDyk8cpL.wezSC99HHTDhwZABuLm4Ty', NULL, NULL, NULL, 'PERUM PONDOK GOLF ASRI JL ASRI 12 BLOK C1 NO.17 RT RW 001 00 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 9, 'SWIMMING POOL SUPERVISOR', 17, 2, 1, NULL, '4', NULL);
INSERT INTO `users` (`id`, `registration_id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `phone`, `address`, `signature_path`, `paraf_path`, `role`, `is_active`, `profile_picture`, `remember_token`, `created_at`, `updated_at`, `jabatan_id`, `jabatan_full`, `department_id`, `directorate_id`, `division_id`, `superior_id`, `golongan`, `nik`) VALUES
(666, '2113517', 'YOGA TRY KANDIDAT', '2113517@yourdomain.com', NULL, '$2y$10$KXkciV5waWFko7M5tCMzkex.KPgycAxUSXDHFJ3DpF0yyc67zFsP6', NULL, NULL, NULL, 'KAVLING CIGODAG RT RW 005 001 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 10, 'ENGINEERING PLANNING ENGINEER', 5, 2, NULL, NULL, '4', NULL),
(667, '2400001', 'ARRY SETYARTO', '2400001@yourdomain.com', NULL, '$2y$10$dfpZEjnjOosLi/h7ul7ES.wubVk5ob8HBJhak1bS5myJzcaCvScjK', NULL, NULL, NULL, 'JL. MERPATI LBS NO 22 KAV BLOK H CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 2, 'ASSISTANT TO PRESIDENT DIRECTOR', NULL, NULL, NULL, NULL, '3', NULL),
(668, '2400002', 'HARY RAHAYU', '2400002@yourdomain.com', NULL, '$2y$10$1eYon9IVDoxbm/S9rURPJ.NEtEYqoPe4k5vwmy4TsdPpCORVT9j..', NULL, NULL, NULL, 'JL. MARS TIMUR VI NO 01 RT RW 006 007 BANDUNG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 8, 'LICENCES SENIOR OFFICER', 2, 1, NULL, NULL, '3', NULL),
(669, '2400003', 'ACHMAD INDRA PRASASTIAWAN', '2400003@yourdomain.com', NULL, '$2y$10$m8UXyfXHwfC/CArcLI./ZuExS7IJhl9PHoiaLRi4aY3sVpZ5c/7Qq', NULL, NULL, NULL, 'KOMP PEJATEN MAS ESTATE BLOK C11 NO 08 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 6, 'CONSTRUCTION MANAGEMENT AREA SUPERTINTEN', 16, 2, 1, NULL, '3', NULL),
(670, '2400004', 'HERMAWAN KRISNA PUTRA', '2400004@yourdomain.com', NULL, '$2y$10$8E9ngNRZA69QkyoPV/xKVe5sBPPAzOUT0bgCNN1je5mJSEpisZbgi', NULL, NULL, NULL, 'BUMI RAKATA ASRI CLUSTER RGH BLOK D2 NO 12 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 6, 'TAX INSURANCE & INVOICING SUPERINTENDENT', 8, 3, NULL, NULL, '3', NULL),
(671, '2400005', 'MUTHIA AZALIA', '2400005@yourdomain.com', NULL, '$2y$10$CEtjF.G/8ISzvenDKvCdYe07UiE6cJxmvRfwXhaXEU4/w1H5B8LYO', NULL, NULL, NULL, 'JL. BAJA III NO 02 CILEGON RT RW 002 004 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 9, 'SALES SUPERVISOR', 16, 2, 1, NULL, '4', NULL),
(672, '2400006', 'DWI SUGENG UTOMO', '2400006@yourdomain.com', NULL, '$2y$10$s3XR57FyuPNwy2OnyhxQ5umDmwf9UuRSF3O9mRLv7.fb8TXOx4dNy', NULL, NULL, NULL, 'JL. TB. ISMAIL BBS II BLOK I-1 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 11, 'LEGAL OFFICER', 2, 1, NULL, NULL, '4', NULL),
(673, '2400007', 'REKIY PUTRA', '2400007@yourdomain.com', NULL, '$2y$10$uuDR3L29WSRSjO/VWr2czuElHHpmh7nWk46kjb.a9eARYVjyeGnpi', NULL, NULL, NULL, 'PERUMAHAN BUMI RAKATA ASRI CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 2, 'ASSISTANT TO HC & FINANCE DIRECTOR', NULL, NULL, NULL, NULL, '4', NULL),
(674, '2400008', 'NUR AMANIYAH', '2400008@yourdomain.com', NULL, '$2y$10$M68iSCN4mXFGBQFGkVdquuUp5lHNRzpxlMpXCxyWxvFXwPa2logUu', NULL, NULL, NULL, 'LINK. CIORA TENGAH RT RW 02 01 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 12, 'FUNDING & COLLECTION FOREMAN', 8, 3, NULL, NULL, '5', NULL),
(675, '2400009', 'MAFTUHI', '2400009@yourdomain.com', NULL, '$2y$10$qyqWXv2jCYa5.xH6O9o65.rm07cSI.Vc4ANj7nhe9PKj7BO/gJrVC', NULL, NULL, NULL, 'LINK. KARANG TENGAH RT/RW 002/004 KEL. KEDALEMAN CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 11, 'HUMAN CAPITAL ORG. & DEV.  OFFICER', 12, 3, NULL, NULL, '4', NULL),
(676, '2400010', 'ARI ASTIANA', '2400010@yourdomain.com', NULL, '$2y$10$VW0SbCYpeho/UPD8ECTdlumenwlSQMcbUd3DXH/olw6ofPzs53Q/K', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 14, 'DOCUMENT CONTROL JUNIOR OFFICER', 2, 1, NULL, NULL, '5', NULL),
(677, '2400011', 'SATWIKA NARAHUTAMA', '2400011@yourdomain.com', NULL, '$2y$10$Xh02BHvT3AD8gZAW9zhBOeQW2i9BdgGkadCHCSfsVjsIWOMuUPz4W', NULL, NULL, NULL, 'KOMP. GSI BLOK H5 NO. 06 RT/RW 004/007 MARGATANI SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:05', '2025-07-14 08:33:05', 11, 'ESTATE MANAGEMENT AREA  OFFICER', 14, 2, 1, NULL, '4', NULL),
(678, '2400012', 'DIANA NUR AZIIZAH', '2400012@yourdomain.com', NULL, '$2y$10$G4euBRZi009jMAsJoklNwOAwaZ/UY6CNFAUT48NtiA8dy665tEXdu', NULL, NULL, NULL, 'KOMP. PLN LEBAK GEDE RT/RW 03/09 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 9, 'FINANCIAL ACCOUNTING SUPERVISOR', 9, 3, NULL, NULL, '4', NULL),
(679, '2400013', 'IMAM', '2400013@yourdomain.com', NULL, '$2y$10$iQ1N40MuFod67kva9XigQeN59nSHNShCNTYi5D13qQ5TestCazpGS', NULL, NULL, NULL, 'KP. JAHA RT/RW 20/05 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 9, 'TAX, INSURANCE & INVOICING SUPERVISOR', 8, 3, NULL, NULL, '4', NULL),
(680, '2400014', 'FAJAR SURYANTO', '2400014@yourdomain.com', NULL, '$2y$10$fH9MXRtjpKtntPz/l/4bf.CEhKj8ZPv7ESW1DZ5GvmaaMI.vKxyaW', NULL, NULL, NULL, 'JL. DANAU LIMBOTO RT/RW 20/04 BENDUNGAN HILIR JAKARTA', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 14, 'CORPORATE COMMUNICATION JUNIOR OFFICER', 1, 1, NULL, NULL, '5', NULL),
(681, '2400015', 'RAHMAT IAS', '2400015@yourdomain.com', NULL, '$2y$10$9MAXVdByzukQtpnKvAhmcOoU3NMzQNqAEBzEVkQ453kQ9SdJwzagG', NULL, NULL, NULL, 'BUKIT CIMANGGU CITY BLOK Y3 NO 3 BOGOR', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 7, 'PROJECT CONTROL SENIOR ENGINEER', 6, 2, NULL, NULL, '3', NULL),
(682, '2400016', 'EVY ZULFIKAR HILMAN', '2400016@yourdomain.com', NULL, '$2y$10$vtBYptwjfasickBw62VKI.D2CoORgiSN3VwpYvFx9OzjQWwVvP3Sm', NULL, NULL, NULL, '/E10/1 RT 001/009 MALAKA SARI JAKARTA TIMUR', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 2, 'ASSISTANT TO OPERATION DIRECTOR', NULL, NULL, NULL, NULL, '4', NULL),
(683, '2400017', 'RADEN REZA GANESHA P', '2400017@yourdomain.com', NULL, '$2y$10$Vm5KvnmfSBL4qwC8TY1ODe6rsgdOMjBgkJhHTYUejHwL/9IGp4bTG', NULL, NULL, NULL, 'JL. PENDOWO BLOK B RT 001/016 DEPOK', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 11, 'SALES & MARKETING GOLF & SC OFFICER', 17, 2, 1, NULL, '4', NULL),
(684, '2400018', 'LAURENTIUS DWIKI ADI NARAHARYYA', '2400018@yourdomain.com', NULL, '$2y$10$29qhJAvOJB198crEiuHb4eo/pdlLFBMiyLLMeOgRoWngskmfaqZqa', NULL, NULL, NULL, 'GG ANGGREK I NO 11 TEGAL', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 10, 'ENGINEERING PLANNING ENGINEER', 5, 2, NULL, NULL, '4', NULL),
(685, '2400019', 'ELVIN RILDA FERNANDO', '2400019@yourdomain.com', NULL, '$2y$10$c5qsDtEPVTPq7.u1ox69A.7H.y0TxBWYpSBMHt7ywG4Wc4okt1x1O', NULL, NULL, NULL, 'JALAN SATYA I NO 128 RT 001/003 DKI JAKARTA', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 12, 'FINANCIAL ACCOUNTING FOREMAN', 9, 3, NULL, NULL, '5', NULL),
(686, '2400020', 'GUSNETY MUMTAZ', '2400020@yourdomain.com', NULL, '$2y$10$rlq1CQQJjJ8T6nfokvU0w.KClrwrsT7Ugr0r14RVuUOsAyVOSKbda', NULL, NULL, NULL, 'KOMP. TAMAN KRAKATAU BLOK E18 NO. 07 RT RW 05 08 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 12, 'TAX, INSURANCE & INVOICING FOREMAN', 8, 3, NULL, NULL, '5', NULL),
(687, '2400021', 'TIWIK ZUHRIAH', '2400021@yourdomain.com', NULL, '$2y$10$cRJRwMaZg2sQ.9ycm0I2iuqZIpHOp9IV4zosVkO2q1uLv2WLhAsZy', NULL, NULL, NULL, 'KOMP. BUMI LANGGENG BLOK 34 NO 12 RT RW 001 022 BANDUNG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 11, 'MANAGEMENT SYSTEM OFFICER', 11, 3, NULL, NULL, '4', NULL),
(688, '2500010', 'RURY ILHAM', '2500010@yourdomain.com', NULL, '$2y$10$1v4hMsl6SNSllGcZ.YrHz.gLFBkojXGkR5y.Fd0dVsEzP3J9yymjG', NULL, NULL, NULL, 'RT 004/001 TANGERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 3, 'HOTEL GENERAL MANAGER', NULL, NULL, NULL, NULL, '1', NULL),
(689, '2500018', 'KIKI MARZUKI', '2500018@yourdomain.com', NULL, '$2y$10$EbqJyTmvUcYSx8loEw05/OKUJtrWGizYQpbKBu42Uo1U.547XEt3i', NULL, NULL, NULL, 'RT/RW 002/011 SUMUR PECUNG SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 9, 'FOOperational & BEVERAGES SUPERVISOR', 21, 2, 2, NULL, '4', NULL),
(690, '2600001', 'IIM ABDUL HAKIM', '2600001@yourdomain.com', NULL, '$2y$10$HkRMMUmLOq5x5JQ3IurpPeXY/T8GerIZ58GX9thM1AtGBRBpsGYCu', NULL, NULL, NULL, 'DUSUN CIGEMBONG RT RW 001 002 SUMEDANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', NULL, 'THE SUROSOWAN RESTAURANT SOUS CHEF', 22, 2, 2, NULL, '4', NULL),
(691, '2600002', 'WAGIYO', '2600002@yourdomain.com', NULL, '$2y$10$u8t685TNfUDs46CRegjGCuCbxpe9yx5tQq8vphaEQM23PqkRjx3N.', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', NULL, 'OPERATIONAL TECHNICIAN', 23, 2, 2, NULL, '6', NULL),
(692, '2600003', 'ADE FARID', '2600003@yourdomain.com', NULL, '$2y$10$7mLgVijxmbrSgQAH37P9SO/t5owQfSOqk0i3KRdtwfjOfmJrd/Aem', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 9, 'FRONT OFFICE SUPERVISOR', 19, 2, 2, NULL, '5', NULL),
(693, '2600005', 'IRWAN MAULANA HIDAYAT', '2600005@yourdomain.com', NULL, '$2y$10$YYOD8VzXIHrQSaI/66OZkuLob7RIxju.6v3uiAboXv1RVgzM2bBBm', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', NULL, 'BANQUET SOUS CHEF', 22, 2, 2, NULL, '4', NULL),
(694, '2600011', 'EKO WAHYUDI', '2600011@yourdomain.com', NULL, '$2y$10$BPFvL12QVZ7WTi0uXoMvzeLRntxFBitfGRV4rg91kFFExwvWDM3/2', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', 9, 'HOUSEKEEPING HOTEL AREA SUPERVISOR', 20, 2, 2, NULL, '5', NULL),
(695, '2600017', 'DEDEN HIDAYAT', '2600017@yourdomain.com', NULL, '$2y$10$RKm1hr2s13TEaCa1bsCPtOrdddeFflzi3RefRvPcZfcVjITmuRmoa', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:06', '2025-07-14 08:33:06', NULL, 'ROOM & FLOOR ATTENDANT', 20, 2, 2, NULL, '6', NULL),
(696, '2600019', 'SOHARI', '2600019@yourdomain.com', NULL, '$2y$10$rMxagldzwALYeSIkpmyzL.gwt9Kkh.qi14SmeCNVM8wwugXqOilVq', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'ROOM & FLOOR ATTENDANT', 20, 2, 2, NULL, '6', NULL),
(697, '2600021', 'WAHID', '2600021@yourdomain.com', NULL, '$2y$10$haOvymNlcN/wj0TN2h7afu9JrhC7mfP6NQwmTj6ll4CYBVsnJzNCq', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'COOK THE KAIBON', 22, 2, 2, NULL, '6', NULL),
(698, '2600022', 'ADI HENDRIANA', '2600022@yourdomain.com', NULL, '$2y$10$lIXXaS6Jrs2VQAwlNoGCNOB4nJ3LjQF0RjMHL3BMjhyFy9oKsRUPa', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'LAUNDRY', 20, 2, 2, NULL, '6', NULL),
(699, '2600023', 'IDA MARYANA', '2600023@yourdomain.com', NULL, '$2y$10$AIf02Svlwh5t/fQbshCvBe0k/CP7RDPMPklllWbhw2H/d/HUnc1Ie', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'COOK BANQUET', 22, 2, 2, NULL, '6', NULL),
(700, '2600024', 'M. NURMANSYAH', '2600024@yourdomain.com', NULL, '$2y$10$ubLEH70jC0ObFYmXJvnwdONfjHxir6WieFYj/4fvi3lGv61UYZvDS', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'ROOM & FLOOR ATTENDANT', 20, 2, 2, NULL, '6', NULL),
(701, '2600025', 'MISYAIR', '2600025@yourdomain.com', NULL, '$2y$10$g0rpINBnrflN1waEa0iL2.r91dagUgBLyAq32EDWTh34d34sUemAW', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'CIVIL TECHNICIAN', 23, 2, 2, NULL, '6', NULL),
(702, '2600027', 'JUHENDI RIZQI', '2600027@yourdomain.com', NULL, '$2y$10$7AI8Xi.ZrFCqqXNuXKV0Q.Mp9G6dSIUnRe1eWnweDIbGTAxlKg53m', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'THE KAIBON WAITER/SS', 21, 2, 2, NULL, '6', NULL),
(703, '2600028', 'HARRY FUZAKI', '2600028@yourdomain.com', NULL, '$2y$10$snhwvgGvWfvwM4va84FiTuVDCD6xZpt.v/dUy/cyewaAXAF7zBBaO', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', 9, 'ENGINEERING SUPERVISOR', 23, 2, 2, NULL, '5', NULL),
(704, '2600029', 'SYAFIIN', '2600029@yourdomain.com', NULL, '$2y$10$fy8HtL8UEWSQgJTX8rK3eu1T9u7oCnBoMpKL3U9ZotHlKd60oIeX.', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', 9, 'ENGINEERING SUPERVISOR', 23, 2, 2, NULL, '5', NULL),
(705, '2600031', 'FERDIANSYAH TAFTAZANI', '2600031@yourdomain.com', NULL, '$2y$10$Jw/6hsqhjIag8lnmA3y/Ne31wamUW7Xt0hvNk.dAIV.OTsV0VPvkG', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', 12, 'COMMERCIAL PURCHASING FOREMAN', 10, 3, NULL, NULL, '5', NULL),
(706, '2600035', 'MUHAMAD SAMSUDIN', '2600035@yourdomain.com', NULL, '$2y$10$LoWOm82LgRQh9nRCCmFynuQTfqqpqLITlL97aPqKaHMTmHHqxDBN2', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'SALES MANAGEMENT HOTEL', 18, 2, 2, NULL, '4', NULL),
(707, '2600043', 'TRI HANDOKO', '2600043@yourdomain.com', NULL, '$2y$10$Emqva6FCTKaWLxXW2Clfju14ZgmjAJNm5AjsH27ck.o3t0Jl2kbti', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'BANQUET OPERATIONAL MANAGER', 21, 2, 2, NULL, '4', NULL),
(708, '2600044', 'IMAM SAFEI', '2600044@yourdomain.com', NULL, '$2y$10$37Z5OLsyeSr4gTayovzbgeCL1irfSUg.jZCVk87y3UJ29RUGShqga', NULL, NULL, NULL, 'DUSUN 2 KURNIA MATARAM RT RW 006 002 LAMPUNG TENGAH', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'THE SUROSOWAN RESTAURANT MANAGER', 21, 2, 2, NULL, '4', NULL),
(709, '2600046', 'RADEN ANDI WIDODO', '2600046@yourdomain.com', NULL, '$2y$10$Xb2hYqIarjywcJaxDyr.jevgjEv1aTuyMfi9b0MYkIhmM0vj6TWS6', NULL, NULL, NULL, 'PERUMAHAN BUKIT CILEGON ASRI BLOK KC39 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'THE KAIBON WAITER/SS', 21, 2, 2, NULL, '6', NULL),
(710, '2600050', 'CHINTAMI RISTIANA', '2600050@yourdomain.com', NULL, '$2y$10$EP3kOoz2iPgbF5VwrEXdve9KyU2S4ggAVjK2dTHZlfTc.czknRly.', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'RESERVATION/OPERATOR', 19, 2, 2, NULL, '6', NULL),
(711, '2600054', 'SULIS TAOVIK VAOZI', '2600054@yourdomain.com', NULL, '$2y$10$5WwJqgjPdl1p9/GkE8vNwOfnkPnVWTZuqWCb81e1IgKMkouVWOgeS', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'ROOM & FLOOR ATTENDANT', 20, 2, 2, NULL, '6', NULL),
(712, '2600055', 'SUPENDI', '2600055@yourdomain.com', NULL, '$2y$10$FGsAZzW0TRfXhV90YN3Oyum1IkwhiXIUXQG3WBdCytDJeDuazjCha', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'OPERATIONAL TECHNICIAN', 23, 2, 2, NULL, '6', NULL),
(713, '2600057', 'MUHAMMAD IQBAL', '2600057@yourdomain.com', NULL, '$2y$10$mDSoGscdNLg2kpXO6DqbCOqDr1jaJEvbdI.xLcOu1v4hlhg1n9oT6', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'THE KAIBON WAITER/SS', 21, 2, 2, NULL, '6', NULL),
(714, '2600063', 'ASEP ROHMAT', '2600063@yourdomain.com', NULL, '$2y$10$3/Jfzj8Uzqo7pz7n/OFKsuN4vcdbffx0ys5yaaCydHc3Ka.EcBfYC', NULL, NULL, NULL, 'DUSUN SUMBER AGUNG RT/RW 019/002 KOTA MADIUN', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:07', '2025-07-14 08:33:07', NULL, 'COOK BANQUET', 22, 2, 2, NULL, '6', NULL),
(715, '2600067', 'AANG KUSRONI', '2600067@yourdomain.com', NULL, '$2y$10$GfHEJKu/CF6Ma5jZip8N3e7Oup3EFCX.1P8jcJlH3zgGye/8aVvSG', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', 9, 'THE KAIBON RESTAURANT SUPERVISOR', 21, 2, 2, NULL, '5', NULL),
(716, '2600079', 'SAHRUDIN', '2600079@yourdomain.com', NULL, '$2y$10$UZaAOvAq2o1CYm6UohN2ieHe/y9Ka7wfvcqjZZHxNYxWwS9Wcdg2i', NULL, NULL, NULL, 'Kp. Kedungsoka RT/RW 007/002 Kel. Kedungsoka Kec. Pulo Ampel Serang', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'THE KAIBON WAITER/SS', 21, 2, 2, NULL, '6', NULL),
(717, '2600081', 'SANWANI', '2600081@yourdomain.com', NULL, '$2y$10$G.PGOcnnHM82o5LkufGa1uWJryqsJpALZNMhx9rixuJQGFIS31ZCG', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'OPERATIONAL SUPPORT GOLF', 17, 2, 1, NULL, '6', NULL),
(718, '2600088', 'RENI RAHMAWATI', '2600088@yourdomain.com', NULL, '$2y$10$YqSWeiKUtcGUYDrMeWVvcuhXwOLkwdXsPVgyyTSsHz47JfKjLIt9y', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', 12, 'FUNDING & COLLECTION FOREMAN', 8, 3, NULL, NULL, '5', NULL),
(719, '2600095', 'JAHRUDIN', '2600095@yourdomain.com', NULL, '$2y$10$2ZEN7zP8wPC6A6N6GgNIhenp1zaD3v/a1iNTXtGkpupdZJbWscYYK', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'COOK BANQUET', 22, 2, 2, NULL, '6', NULL),
(720, '2600099', 'VENA INDARIANI', '2600099@yourdomain.com', NULL, '$2y$10$ccrE5wg/TsRfG6nKxG6Vb.JuRUXhDKDUqMZTXLB2vehzSYZPVQ2s.', NULL, NULL, NULL, 'LINK KADIPATEN RT RW 001 002 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'COOK PASTRY', 22, 2, 2, NULL, '6', NULL),
(721, '2600100', 'ABDUL RAHMANSYAH', '2600100@yourdomain.com', NULL, '$2y$10$7g1WhLhUByjL6ZxwJ7Uo.uzWI1zlNx90QlbKC5LsV0hyrPKo0NnRu', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'COOK THE SUROSOWAN RESTAURANT', 22, 2, 2, NULL, '6', NULL),
(722, '2600105', 'SAPTONO', '2600105@yourdomain.com', NULL, '$2y$10$t/PB7az0uUU2NZZsQ17niOgq2klOry1IOlY.Oi0izPh2671ipatlK', NULL, NULL, NULL, 'KP. CICADAS RT001/004 PANDEGLANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'COOK THE KAIBON', 22, 2, 2, NULL, '6', NULL),
(723, '2600109', 'MOHAMMAD TAUFIK HIDAYAT', '2600109@yourdomain.com', NULL, '$2y$10$mYtEWt0UqMngScWN/nn63..XqEFem8REgDj13tPmxpE09qh79WsYa', NULL, NULL, NULL, 'GRIYA MUKTI ASRI 15 NO 15 RT 001/005 CIREBON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', 9, 'FRONT OFFICE SUPERVISOR', 19, 2, 2, NULL, '5', NULL),
(724, '2600111', 'IMAM EKA YASA', '2600111@yourdomain.com', NULL, '$2y$10$ASXHtwPZz1pSX9QSUKjDxe9mVq0l6ZRKgiruydSfTBNNg595kS.mC', NULL, NULL, NULL, 'RT023/005 CIWEDUS CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', 14, 'INFORMATION TECHNOLOGY JUNIOR OFFICER', 11, 3, NULL, NULL, '5', NULL),
(725, '2600112', 'INDRA AGUSTIAN', '2600112@yourdomain.com', NULL, '$2y$10$K1gjxBzbRKjwVpX2Jt4fY.gBvP8BChaaQDikzw9booJXo.oVVN28q', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'FB ADMIN', 21, 2, 2, NULL, '6', NULL),
(726, '2600113', 'ALI ROHMAN', '2600113@yourdomain.com', NULL, '$2y$10$Pssy6SqzhWh/HNgPZskwTeLkTWWpvvF6.n9F916CWPpazfdifMjNC', NULL, NULL, NULL, 'CILEGON CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'STEWARD STAFF', 22, 2, 2, NULL, '6', NULL),
(727, '2600115', 'SUROHIM', '2600115@yourdomain.com', NULL, '$2y$10$Ntu/BGNkSe5a0.d4vcxvF.5NSzXo0nBO4bv32PpSLyWqBOPaVM4/u', NULL, NULL, NULL, 'KEL. SUKABARES WARINGINKURUNG SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'COOK THE KAIBON', 22, 2, 2, NULL, '6', NULL),
(728, '2600117', 'MAR\'I MUAMAR', '2600117@yourdomain.com', NULL, '$2y$10$H2CXqC/VkBZMbMXx/9mlFeRBlbm77gYAwhVvt3IpSPzefnPFckiNq', NULL, NULL, NULL, 'Jl Kh Syamil Noo 48 A. Kav Blok F Kavling Cilegon', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'ROOM & FLOOR ATTENDANT', 20, 2, 2, NULL, '6', NULL),
(729, '2600118', 'TAUFIK HIDAYAH', '2600118@yourdomain.com', NULL, '$2y$10$hpdKePKRDcdVPV1hBpdD4eh8VU/oEYuUSxUC6YG.X5cLFxY0rU9KG', NULL, NULL, NULL, 'RT004/007 GEMBOR PERIUK TANGERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'CDP THE SUROSOWAN RESTAURANT', 22, 2, 2, NULL, '5', NULL),
(730, '2600119', 'SUHENDRA', '2600119@yourdomain.com', NULL, '$2y$10$z7hdOlM7k3.BHCvRwI88fOW.oM05G3jYZMAW5lbNV3Kzo9WEjwheG', NULL, NULL, NULL, 'RTA37 NO 08 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', 9, 'HOUSEKEEPING HOTEL AREA SUPERVISOR', 20, 2, 2, NULL, '5', NULL),
(731, '2600120', 'ANGGUN MARATUS SOLIKHA', '2600120@yourdomain.com', NULL, '$2y$10$cBGAlcx6NUc.osVAsJi1keT/DYKipW/niPFPf54NfnJSRmPXd35Za', NULL, NULL, NULL, 'BATANG HARI LAMPUNG TIMUR LAMPUNG TIMUR', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'E-COMMERCE', 18, 2, 2, NULL, '6', NULL),
(732, '2600121', 'DEDE KURNIADI', '2600121@yourdomain.com', NULL, '$2y$10$z1EQNR/p43hYVDDpABCdMOXfW1a7T/vyk5uST6ckbaZk6pyrmK7Oy', NULL, NULL, NULL, 'NLOK C1 NO 15 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'THE SUROSOWAN WAITER/SS', 21, 2, 2, NULL, '6', NULL),
(733, '2600122', 'AGUS PUJIANTO', '2600122@yourdomain.com', NULL, '$2y$10$d/wU5BMz7F1As1EXg0laJ.xHPE5kaRrzuYZmZmjyTN8YeWo0xzmD6', NULL, NULL, NULL, 'KEC NEGERI KATON PROV LAMPUNG KAB. PESAWARAN', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:08', '2025-07-14 08:33:08', NULL, 'FRONT DESK AGENT', 19, 2, 2, NULL, '6', NULL),
(734, '2600123', 'SANDY EKA HADIPUTRA', '2600123@yourdomain.com', NULL, '$2y$10$yhEas8.HVLAKLtz4b/xj8.7YTRixjEVE0V9GU0nPRfPseLQBkg/gK', NULL, NULL, NULL, 'TERAS BOYOLALI BOYOLALI', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', 5, 'FOOperational & BEVERAGE MANAGER', 21, 2, 2, NULL, '3', NULL),
(735, '2600124', 'FREDERIK MANUEL NAINGGOLAN', '2600124@yourdomain.com', NULL, '$2y$10$8eULncjtA34ufxOibnN1xOLJa2fnw1GqtLKf9Vw0Xjpiq3oyXks/6', NULL, NULL, NULL, 'BAHAR KARANG TENGAH TANGERANG TANGERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', 5, 'DUTY  MANAGER', 19, 2, 2, NULL, '4', NULL),
(736, '2600129', 'KEVIN RAMADHAN', '2600129@yourdomain.com', NULL, '$2y$10$HSaQzl/VKDMAr3XK.a6/U.8pEKf1fgJv4xDCVcbkx6HlA8gG1Iu22', NULL, NULL, NULL, 'DESA CIBOGOR KOTA BOGOR', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', 9, 'BAR SUPERVISOR', 21, 2, 2, NULL, '5', NULL),
(737, '2600135', 'NOVAL RIZKI', '2600135@yourdomain.com', NULL, '$2y$10$v0UD3XIW/iWQb3s/c44kD.mYd2f/drv/1basGUPGqqxH57Rl9P8Xm', NULL, NULL, NULL, 'KP. TANJUNG HARAPAN RT 001/006 KEL. PEGADINGAN SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', NULL, 'FRONT DESK AGENT', 19, 2, 2, NULL, '6', NULL),
(738, '2600137', 'HARY YUNISA', '2600137@yourdomain.com', NULL, '$2y$10$ymr.j/n/tFBQRHnK7CKiK.32elcT6aKY5ZGZWppuNAY0ML1Crkr9u', NULL, NULL, NULL, 'JL. DR SETIABUDI GG MANGGIS I NO 32 TANGERANG SELATAN', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', 12, 'PR MARCOM HOTEL FOREMAN', 18, 2, 2, NULL, '5', NULL),
(739, '2600138', 'GAGAS ALFIANA', '2600138@yourdomain.com', NULL, '$2y$10$DfPHDsGBdtWaWwnnwa8zouRNHb1uvJcNbWfsk7dag/4yQCyrEnJUW', NULL, NULL, NULL, 'KP. GAGEUNDANG RT RW 003 004 CIANJUR', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', 9, 'THE SUROSOWAN RESTAURANT SUPERVISOR', 21, 2, 2, NULL, '5', NULL),
(740, '2600139', 'NAISYA RIZKINA DARAJAT', '2600139@yourdomain.com', NULL, '$2y$10$h8x5H1Acaap1AwkEC1T/ieue0zDNfcD8BS9MeGWX/B6qE/Aiqer4i', NULL, NULL, NULL, 'KALORAN PENA RT RW 001 007 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', NULL, 'SALES REPRESENTATIVE HOTEL', 18, 2, 2, NULL, '5', NULL),
(741, '2600141', 'RIKI SUHENDRIK', '2600141@yourdomain.com', NULL, '$2y$10$Q0d2AvXpRSFxytuCgauMseLrVBrlofwtfyuknYCLF6nRXEqU5.9Ly', NULL, NULL, NULL, 'JL. IMAM BONJOL GG LAKSANA NO 20 LK I BANDAR LAMPUNG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', NULL, 'THE KAIBON WAITER/SS', 21, 2, 2, NULL, '6', NULL),
(742, '2600144', 'LUCKY AGUSMAN SALIM', '2600144@yourdomain.com', NULL, '$2y$10$WUB6lNRqIOkf463WQGRrIengqQa7KeBqAWDhanQmFiwQEaK/ambqe', NULL, NULL, NULL, 'DEPOK MAHARAJA BLOK R 3 NO. 15 DEPOK', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', NULL, 'EXECUTIVE CHEF', 22, 2, 2, NULL, '3', NULL),
(743, '2600145', 'YUDA FERYANDI', '2600145@yourdomain.com', NULL, '$2y$10$PdlEfanykORVaIDaZ.v4jOL3QSycsvtsa8oERU38z5JMLeWAk9SZu', NULL, NULL, NULL, 'JL SUNAN GIRI LINK CILODAN RT RW 017 005 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', NULL, 'ROOM & FLOOR ATTENDANT', 20, 2, 2, NULL, '6', NULL),
(744, '2600146', 'AHMAD HUSNAENI', '2600146@yourdomain.com', NULL, '$2y$10$y6XGJ2P0Y2vnVSuwuHEvbOyv8vA85L417.l9VbtjFoDksgOdVt3Ze', NULL, NULL, NULL, 'LINK CIRIU TENGAH RT RW 002 003 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', NULL, 'OPERATIONAL TECHNICIAN', 23, 2, 2, NULL, '6', NULL),
(745, '2600147', 'MUAHMMAD RIZKY', '2600147@yourdomain.com', NULL, '$2y$10$gKYzwKch5I.F2GJ3HupSLOJmrUxMdiAWgnCx0S1Gtj/BuDmkBPzdW', NULL, NULL, NULL, 'PEDONGKELAN RT RW 016 013 DKI', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', NULL, 'THE SUROSOWAN RESTAURANT SOUS CHEF', 22, 2, 2, NULL, '4', NULL),
(746, '2600148', 'KHRISNA DEWA SAKTI', '2600148@yourdomain.com', NULL, '$2y$10$mXm/Y8Tbft3wo9F09mnLAO.sOaJwjqL0xJ.CKtLNe5O/csRlATTKG', NULL, NULL, NULL, 'PR. KRAPYAK PERMAI NO. 53 RT RW 002 011 KLATEN', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', NULL, 'CDP THE KAIBON RESTAURANT', 22, 2, 2, NULL, '5', NULL),
(747, '2600149', 'LILI KUSNADI', '2600149@yourdomain.com', NULL, '$2y$10$v3QpM1mE9cVeQwwq8HBSu.NpLtR1Qak9yNpN0VBHO0y2s4videKpe', NULL, NULL, NULL, 'LINK. SUKAJAYA RT 001 RW 006 KEL. KEBONSARI KEC. CITANGKIL CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', 9, 'SECURITY SECTION COMMERCIAL SUPERVISOR', 7, 2, NULL, NULL, '4', NULL),
(748, '2600150', 'RATNO SETIAWAN', '2600150@yourdomain.com', NULL, '$2y$10$62LLqWGkiYP2VyAa//ubb.ukYWAf9g3z6v6uq75TpdRx1sAPQnpTa', NULL, NULL, NULL, 'LEBAK INDAH GRIYA ASRI BLOK E5/26 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', NULL, 'OPERATIONAL SUPPORT GOLF', 17, 2, 1, NULL, '6', NULL),
(749, '2600151', 'IMAM MUHAMAD RAMADHAN', '2600151@yourdomain.com', NULL, '$2y$10$lSunYDJWVG2wad2e4jVTmeMgNOXUkt3ZVDhWCM7c6eYGB/LMO2sF2', NULL, NULL, NULL, 'KP. SINDANGLAKA RT RW 002 007 CIANJUR', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', NULL, 'OPERATIONAL SUPPORT GOLF', 17, 2, 1, NULL, '6', NULL),
(750, '2600152', 'BUDI SENTOSO', '2600152@yourdomain.com', NULL, '$2y$10$LnvG7Nevhl2pa2c/e7LgVejOiKuMDO5hoT3giMGTWs6uPTh9TP6XK', NULL, NULL, NULL, 'DUSUN VIII RT RW 008 010 KEL SUKA JAWA LAMPUNG TENGAH', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', NULL, 'OPERATIONAL SUPPORT GOLF', 17, 2, 1, NULL, '6', NULL),
(751, '2600153', 'ARBI BATTI NURAHMAN PRAMUDJI', '2600153@yourdomain.com', NULL, '$2y$10$ctYaz4tPnEcieyXEaOUVvOv2XrcNbZwAJbqEnKq6jCvkBUdoJLUiS', NULL, NULL, NULL, 'KOMP METRO GARDEN BLOK F1 NO 05 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:09', '2025-07-14 08:33:09', NULL, 'BARISTA', 21, 2, 2, NULL, '6', NULL),
(752, '2600154', 'INTAN NADE WINATA', '2600154@yourdomain.com', NULL, '$2y$10$z67AHZsYGimIoXrxaOYe9.RZXZ33GFXryiJOWj34dt6PMAfWhNIEK', NULL, NULL, NULL, 'LINK PEGANTUNGAN BARU RT RW 003 014 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'FRONT DESK AGENT', 19, 2, 2, NULL, '6', NULL),
(753, '2600155', 'IIM HERMAWAN', '2600155@yourdomain.com', NULL, '$2y$10$M19jgvl5BTy3w1yYoznpr.Yhq5bekNBBPFWp0Fx5N.pRYPH369wWK', NULL, NULL, NULL, 'KP CIBADAK RT RW 001 004 ALASWANGI MENES PANDEGLANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'COOK THE SUROSOWAN RESTAURANT', 22, 2, 2, NULL, '6', NULL),
(754, '2600156', 'JAYANTI INDAH PUTRI', '2600156@yourdomain.com', NULL, '$2y$10$T7NoHGIfvqdGhS9oCjwZduz6PrONCeMjlvDa5z8XLzC1UTiJRV.Am', NULL, NULL, NULL, 'LINK KALIGANDU BUJANG BOROS CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'COOK THE SUROSOWAN RESTAURANT', 22, 2, 2, NULL, '6', NULL),
(755, '2600157', 'MULYA JATIASMARA', '2600157@yourdomain.com', NULL, '$2y$10$527G/fwa9lVJ9LPRrhBwNeaUvRwVZZl0EPpYPG04hRGB/zZ4.Nb7q', NULL, NULL, NULL, 'CILENDEK TIMUR RT RW 002 005 BOGOR', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'CDP THE SUROSOWAN RESTAURANT', 22, 2, 2, NULL, '5', NULL),
(756, '2600158', 'PURBOYO ANANDA', '2600158@yourdomain.com', NULL, '$2y$10$kv0oKOTlyrvMaTPSTpar..lhmxBSw5C9hP6nqNrg6.LVTaz9mOoLK', NULL, NULL, NULL, 'JL ANTAREJA NO. 15 RT RW 004 006 JAKARTA TIMUR', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', 9, 'THE KAIBON RESTAURANT SUPERVISOR', 22, 2, 2, NULL, '5', NULL),
(757, '2600159', 'JUHANDA', '2600159@yourdomain.com', NULL, '$2y$10$h7Z1V6bRk8cygdoeQdjf9enBn7aHM8jbRGsbEsvRadaq3sNf5T/N6', NULL, NULL, NULL, 'KP SINDANGSARI RT RW 005 011 TASIKMALAYA', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'PASTRY CHEF', 22, 2, 2, NULL, '4', NULL),
(758, '2600160', 'KURNIAWAN YUDHO R', '2600160@yourdomain.com', NULL, '$2y$10$AccO07L4HtJwOQnZuo1FzOuhkrueDiCNY5JTWPwky48XXVPJ2TlxW', NULL, NULL, NULL, 'PERUM PERSADA BANTEN BLOK TC.8 NO.7 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'SALES MANAGEMENT HOTEL', 18, 2, 2, NULL, '4', NULL),
(759, '2600161', 'HENDRO SETIO', '2600161@yourdomain.com', NULL, '$2y$10$.uB3HVpsB.4LwuzRf3tsg.MVPwxbQCAW31QHhiBr/P0ni1KcalZw2', NULL, NULL, NULL, 'JL RUSA IV NO 2 RT RW 004 009 TANGERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'THE KAIBON RESTAURANT SOUS CHEF', 22, 2, 2, NULL, '4', NULL),
(760, '2900004', 'KHANSA KAMILA', '2900004@yourdomain.com', NULL, '$2y$10$v3Drif2VTRJ/0byB7T1pv.7Oxo.Iki0h6OnARX4xKP6oBe4nvA2hG', NULL, NULL, NULL, 'PCI BLOK C 47 NO 02 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'SALES AGENT', 16, 2, 1, NULL, '6', NULL),
(761, '2900005', 'SUHERMAT', '2900005@yourdomain.com', NULL, '$2y$10$yYp2f3jSqLj4nnk63N4y6ub/DBXbwAmTtdiSS2XWYLXI5bNuWF/w6', NULL, NULL, NULL, 'JL KH AGUS SALIM LINK LUWUNG SAWO CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'SALES COORDINATOR', 16, 2, 1, NULL, '5', NULL),
(762, '2900010', 'AHMAD SIDIQ SULAEMAN', '2900010@yourdomain.com', NULL, '$2y$10$ZYkqTJl0uUM3emCvf.cXuOuLPJpAHOK32XSgLFzV0sSEq0MOOlQye', NULL, NULL, NULL, 'LINK. KALANG ANYAR RT 004/001 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'SALES AGENT', 16, 2, 1, NULL, '6', NULL),
(763, '2900012', 'IR. ANDI ISVANDIAR MULUK', '2900012@yourdomain.com', NULL, '$2y$10$t/WpsKOpje2MPuvhROkEEuLzJERiFA0iFGSYv1VaNyR9lRfAyxhL2', NULL, NULL, NULL, 'JL. DAMAI IV / 19 RT RW 007 005 JAKARTA', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'STRATEGIC BUSINESS INITIATIVE GROUP', 4, 1, NULL, NULL, '2', NULL),
(764, '2900013', 'IVA NURJANAH', '2900013@yourdomain.com', NULL, '$2y$10$0E21UdI8wLQubcymJoEPj.X79qdBqUcgbEUCeeQe34.OLOpC13Mwe', NULL, NULL, NULL, 'JL. KH. DJAMHARI NO. 44 KAUJON BUAH GEDE RT 04/03 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'SALES REPRESENTATIVE HOTEL', 18, 2, 2, NULL, '5', NULL),
(765, '2900014', 'MUHAMMAD SANJAYA', '2900014@yourdomain.com', NULL, '$2y$10$HaCLSoT/m687zj6SzNuzju9Sh4Mj78AlBtIX/CDCougTD7Lk0o4j2', NULL, NULL, NULL, 'PERUM LEBAKWANA GRIYA ASRI SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', 12, 'OPERATION & SERVICE AREA FOREMAN', 16, 2, 1, NULL, '5', NULL),
(766, '2900016', 'WISNU ADHI PRABOWO', '2900016@yourdomain.com', NULL, '$2y$10$k4sug.wY7cnbs0ZdEOotl.qvsDm5XSX5AGA6kBbjAmpIYwIah5dYC', NULL, NULL, NULL, 'SUKMAJAYA JOMBANG CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', 11, 'LEGAL OFFICER', 2, 1, NULL, NULL, '4', NULL),
(767, '2900017', 'MUHAMMAD IRFANSYAH POHAN', '2900017@yourdomain.com', NULL, '$2y$10$bOWbGaVWO0ZHhJ851L6KPe16qKVIgblqLKIa1DaZ7gGdyYY..L/W6', NULL, NULL, NULL, 'JL. S. SAMBAS IV NO. 08 RT RW 02 05 JAKARTA SELATAN', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', 2, 'ASSISTANT TO HC & FINANCE DIRECTOR', NULL, NULL, NULL, NULL, '4', NULL),
(768, '2900018', 'INDRA LESMANA', '2900018@yourdomain.com', NULL, '$2y$10$DaL8R/YiEDt5AjW95XVLnOzdzo/39I7s8/R4yKHzFtiYl5fhNK30a', NULL, NULL, NULL, 'TAMAN BANTEN LESTARI BLOK J 3 C NOMOR 15 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', NULL, 'SALES AGENT', 16, 2, 1, NULL, '6', NULL),
(769, '2900020', 'SAEFULLOH', '2900020@yourdomain.com', NULL, '$2y$10$Xcp6ewLHWv8ST9OuJULDl.N0cEGyKa3Q/xdlEinMzg2Skky98lzBO', NULL, NULL, NULL, 'LINK KUBANG WATES RT RW 001 008 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', 12, 'FUNDING & COLLECTION FOREMAN', 8, 3, NULL, NULL, '5', NULL),
(770, '2900023', 'RAMDHANY YUDIASARI ADI PUTRI', '2900023@yourdomain.com', NULL, '$2y$10$cYLdXj8zRNEhdiAoRNB.T.PEJbLMZu/6sdPsQwVCfi3vrfJdY9b1.', NULL, NULL, NULL, 'JL. MORSE NO 10 KOMP KS RT RW 002 001 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:10', '2025-07-14 08:33:10', 9, 'TAX, INSURANCE & INVOICING SUPERVISOR', 8, 3, NULL, NULL, '4', NULL),
(771, '2900024', 'NURIYATUL HIDAYAH', '2900024@yourdomain.com', NULL, '$2y$10$Cn3NWQJq9kB/zFHABjhY1.vdWKxjJ33ihfjvFE.OrlWtib0KjReUW', NULL, NULL, NULL, 'CLUSTER MUTIARA KORELET 1 BLOK L NO. 05 TANGERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', 9, 'FUNDING & COLLECTION SUPERVISOR', 8, 3, NULL, NULL, '4', NULL),
(772, '2900026', 'KARINA ADIKUSUMANINGTYAS', '2900026@yourdomain.com', NULL, '$2y$10$t8uGx5XGqFZtbtBT4BOQnevN5HqNns1rgcZc8PbTw7X.rIhGTnHAi', NULL, NULL, NULL, 'JL. RAYA GANDUL RT RW 007/008 DEPOK', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', 11, 'SAFETY, HEALTH & ENVIRONMENT OFFICER', 7, 2, NULL, NULL, '4', NULL),
(773, '2900027', 'SEILA DELFINA HARIANJA', '2900027@yourdomain.com', NULL, '$2y$10$TSpw1Gc1FAuyLQcJgYaZhOl/hNOMkPVoIV9sdcP4OJ3bBW2paifHK', NULL, NULL, NULL, 'TAMAN TRIDAYA INDAH F.23/3 RT RW 007/010 BEKASI', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', 11, 'SAFETY, HEALTH & ENVIRONMENT OFFICER', 7, 2, NULL, NULL, '4', NULL),
(774, '2900028', 'ALIFFIAN GUSMA HENDRA', '2900028@yourdomain.com', NULL, '$2y$10$zqksvVlIT5Stq2gPCUULZe6HtoU.VXDrhDN1ZW8p.OJHRjdV3s65i', NULL, NULL, NULL, 'PS. TAMPANG JORONG PASIA BINTUNGAN DESA AIR GADANG SUMATERA BARAT', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', 11, 'BUSINESS DEVELOPMENT OFFICER', NULL, NULL, NULL, NULL, '4', NULL),
(775, '2900029', 'IRFAN FAUZAN', '2900029@yourdomain.com', NULL, '$2y$10$PknrNZ2LQQNHZtPy97GjluF3Hit5./eGXt1PDQhKlQtvTSUSvtFey', NULL, NULL, NULL, 'KOMPLEK PEMDA JL. RASAMALA NO. 16 RANGKASBITUNG LEBAK', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', 12, 'FUNDING & COLLECTION FOREMAN', 8, 3, NULL, NULL, '5', NULL),
(776, '2900030', 'RIDWAN SATRIA WICAKSANA', '2900030@yourdomain.com', NULL, '$2y$10$e0YVgYc3NPafiqBg79aVluBi2a6F6cgDggo39tOO9/MCI9tdHAH1q', NULL, NULL, NULL, 'JL. PUCANG TAMA X NO 8 MRANGGEN DEMAK MRANGGEN', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', 14, 'INFORMATION TECHNOLOGY JUNIOR OFFICER', 11, 3, NULL, NULL, '5', NULL),
(777, '2900031', 'YASMIN YUMNAA TSUROYYA', '2900031@yourdomain.com', NULL, '$2y$10$B8HiB40LhyauCK/mPRIBGOo3jwD9iP7gTAgqEzMAJbIscNGZ0C1YG', NULL, NULL, NULL, 'VILLA PERMATA HIJAU CLUSTER INTAN BLOK N1 NO 7 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', 9, 'FINANCIAL ACCOUNTING SUPERVISOR', 9, 3, NULL, NULL, '4', NULL),
(778, '2900032', 'ISMA NAHDIA', '2900032@yourdomain.com', NULL, '$2y$10$u1Z/XTaAgySBwl0vo/zvNOj8HMH3MTII5mQablT78ypkd0ERopqim', NULL, NULL, NULL, 'PURNAKARYA RAYA 15 KEL GEDANGANAK KABUPATEN SEMARANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', 11, 'ESTATE MANAGEMENT AREA  OFFICER', 14, 2, 1, NULL, '4', NULL),
(779, '2900033', 'INDAH INDRIYANI', '2900033@yourdomain.com', NULL, '$2y$10$DYWVjDUMeHL0FtODihq0t.qaFYLT6Wj5YNUEcGiaZiJqcSFDsJYGC', NULL, NULL, NULL, 'TAMAN KRAKATAU BLOK H.7 NO.1 SERANG', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', NULL, 'SALES AGENT', 16, 2, 1, NULL, '6', NULL),
(780, '2900034', 'HARYADI', '2900034@yourdomain.com', NULL, '$2y$10$DLTKxcZalHEIuOibJLeg5eavK7W55O13q1del5SJw7a62lEpcsKqK', NULL, NULL, NULL, 'LINK KUBANG WELUT RT RW 001 004 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', NULL, 'MECH./ELEC./CIVIL MAINT. GROUP', 17, 2, 1, NULL, '5', NULL),
(781, '2900035', 'RIZWAN RAMADHAN WIBIKSANA', '2900035@yourdomain.com', NULL, '$2y$10$IjruWPk6TVvsq5lIxm9TR.0O27iJwxMSVv7w7lzQrlwIdV.LrVgra', NULL, NULL, NULL, 'KOMP BBS II JL KECUBUNG V NO. 07 CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', NULL, 'ADMINISTRATION SUPPORT RE', NULL, NULL, NULL, NULL, '6', NULL),
(782, '2900036', 'KARTIKA APRILIANI', '2900036@yourdomain.com', NULL, '$2y$10$zC0xYYmolBeQb2R31ckAwObvJ8yrcrk6QHQF2PB05tcdYfQ2AaQHy', NULL, NULL, NULL, 'LINK KUBANG WELUT RT RW 005 004 KEL SEMANGRAYA CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', 11, 'SALES & MARKETING GOLF & SC OFFICER', 17, 2, 1, NULL, '4', NULL),
(783, '2900037', 'ABDUL LUTHFI MADJID', '2900037@yourdomain.com', NULL, '$2y$10$Nv3HvGcas77IFlPnw.m8SeX6G3y0uhjQmguL5v2YeGbztU1qwVNQi', NULL, NULL, NULL, 'KOMP PURI TANJUNG SARI NO 41 MEDAN', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', 9, 'COURSE MAINTENANCE SUPERVISOR', 17, 2, 1, NULL, '4', NULL),
(784, '2900038', 'ANUNG NUGROHO', '2900038@yourdomain.com', NULL, '$2y$10$Q8AlOPdSSJIxasxNJ.2d5OZtRyHiHYMPfvFAZyrvQ79VAQ842XCMG', NULL, NULL, NULL, 'JL. ARJUNA NO 139 KAV BLOK J CILEGON', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', 9, 'COURSE MAINTENANCE SUPERVISOR', 17, 2, 1, NULL, '4', NULL),
(785, '2900039', 'ABDUL RIFAI', '2900039@yourdomain.com', NULL, '$2y$10$0qLBrKkv2Zfd6wQShTDlB.j8RJ0hcQXZdsgihCRuxNqW/L5yK1.Cq', NULL, NULL, NULL, 'JL NANGKA TJ BARAT RT RW 003 006 JAKARTA SELATAN', NULL, NULL, 'staff', 1, NULL, NULL, '2025-07-14 08:33:11', '2025-07-14 08:33:11', 9, 'CLUB MANAGEMENT SUPERVISOR', 17, 2, 1, NULL, '4', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachments_letter_id_foreign` (`letter_id`),
  ADD KEY `attachments_user_id_foreign` (`user_id`);

--
-- Indexes for table `classifications`
--
ALTER TABLE `classifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `classifications_code_unique` (`code`);

--
-- Indexes for table `configs`
--
ALTER TABLE `configs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `configs_code_unique` (`code`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_name_unique` (`name`),
  ADD KEY `departments_directorate_id_foreign` (`directorate_id`),
  ADD KEY `departments_division_id_foreign` (`division_id`);

--
-- Indexes for table `directorates`
--
ALTER TABLE `directorates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `directorates_name_unique` (`name`);

--
-- Indexes for table `dispositions`
--
ALTER TABLE `dispositions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dispositions_letter_status_foreign` (`letter_status`),
  ADD KEY `dispositions_letter_id_foreign` (`letter_id`),
  ADD KEY `dispositions_user_id_foreign` (`user_id`);

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `divisions_name_unique` (`name`),
  ADD KEY `divisions_directorate_id_foreign` (`directorate_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jabatans`
--
ALTER TABLE `jabatans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jabatans_name_unique` (`name`);

--
-- Indexes for table `letters`
--
ALTER TABLE `letters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `letters_reference_number_unique` (`reference_number`),
  ADD KEY `letters_classification_code_foreign` (`classification_code`),
  ADD KEY `letters_user_id_foreign` (`user_id`);

--
-- Indexes for table `letter_statuses`
--
ALTER TABLE `letter_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `signature_and_parafs`
--
ALTER TABLE `signature_and_parafs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `surat_pengajuan_pelatihans`
--
ALTER TABLE `surat_pengajuan_pelatihans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `surat_pengajuan_pelatihans_kode_pelatihan_unique` (`kode_pelatihan`),
  ADD KEY `surat_pengajuan_pelatihans_created_by_foreign` (`created_by`);

--
-- Indexes for table `surat_pengajuan_pelatihan_signatures_and_parafs`
--
ALTER TABLE `surat_pengajuan_pelatihan_signatures_and_parafs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_signatures_and_parafs_pelatihan` (`pelatihan_id`),
  ADD KEY `surat_pengajuan_pelatihan_signatures_and_parafs_user_id_foreign` (`user_id`);

--
-- Indexes for table `surat_pengajuan_training_example`
--
ALTER TABLE `surat_pengajuan_training_example`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `surat_tugas_pelatihans`
--
ALTER TABLE `surat_tugas_pelatihans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surat_tugas_pelatihans_pelatihan_id_foreign` (`pelatihan_id`),
  ADD KEY `surat_tugas_pelatihans_created_by_foreign` (`created_by`);

--
-- Indexes for table `surat_tugas_pelatihan_signatures_and_parafs`
--
ALTER TABLE `surat_tugas_pelatihan_signatures_and_parafs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_participants`
--
ALTER TABLE `training_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `training_participants_pelatihan_id_foreign` (`pelatihan_id`),
  ADD KEY `training_participants_user_id_foreign` (`user_id`),
  ADD KEY `training_participants_jabatan_id_foreign` (`jabatan_id`),
  ADD KEY `training_participants_department_id_foreign` (`department_id`),
  ADD KEY `training_participants_superior_id_foreign` (`superior_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_directorate_id_foreign` (`directorate_id`),
  ADD KEY `users_division_id_foreign` (`division_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classifications`
--
ALTER TABLE `classifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `configs`
--
ALTER TABLE `configs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `directorates`
--
ALTER TABLE `directorates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dispositions`
--
ALTER TABLE `dispositions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jabatans`
--
ALTER TABLE `jabatans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `letters`
--
ALTER TABLE `letters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `letter_statuses`
--
ALTER TABLE `letter_statuses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `signature_and_parafs`
--
ALTER TABLE `signature_and_parafs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `surat_pengajuan_pelatihans`
--
ALTER TABLE `surat_pengajuan_pelatihans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `surat_pengajuan_pelatihan_signatures_and_parafs`
--
ALTER TABLE `surat_pengajuan_pelatihan_signatures_and_parafs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `surat_pengajuan_training_example`
--
ALTER TABLE `surat_pengajuan_training_example`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `surat_tugas_pelatihans`
--
ALTER TABLE `surat_tugas_pelatihans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `surat_tugas_pelatihan_signatures_and_parafs`
--
ALTER TABLE `surat_tugas_pelatihan_signatures_and_parafs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `training_participants`
--
ALTER TABLE `training_participants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=786;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_letter_id_foreign` FOREIGN KEY (`letter_id`) REFERENCES `letters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_directorate_id_foreign` FOREIGN KEY (`directorate_id`) REFERENCES `directorates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `departments_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dispositions`
--
ALTER TABLE `dispositions`
  ADD CONSTRAINT `dispositions_letter_id_foreign` FOREIGN KEY (`letter_id`) REFERENCES `letters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dispositions_letter_status_foreign` FOREIGN KEY (`letter_status`) REFERENCES `letter_statuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dispositions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `divisions`
--
ALTER TABLE `divisions`
  ADD CONSTRAINT `divisions_directorate_id_foreign` FOREIGN KEY (`directorate_id`) REFERENCES `directorates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `letters`
--
ALTER TABLE `letters`
  ADD CONSTRAINT `letters_classification_code_foreign` FOREIGN KEY (`classification_code`) REFERENCES `classifications` (`code`),
  ADD CONSTRAINT `letters_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `surat_pengajuan_pelatihans`
--
ALTER TABLE `surat_pengajuan_pelatihans`
  ADD CONSTRAINT `surat_pengajuan_pelatihans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `surat_pengajuan_pelatihan_signatures_and_parafs`
--
ALTER TABLE `surat_pengajuan_pelatihan_signatures_and_parafs`
  ADD CONSTRAINT `fk_signatures_and_parafs_pelatihan` FOREIGN KEY (`pelatihan_id`) REFERENCES `surat_pengajuan_pelatihans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `surat_pengajuan_pelatihan_signatures_and_parafs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `surat_tugas_pelatihans`
--
ALTER TABLE `surat_tugas_pelatihans`
  ADD CONSTRAINT `surat_tugas_pelatihans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `surat_tugas_pelatihans_pelatihan_id_foreign` FOREIGN KEY (`pelatihan_id`) REFERENCES `surat_pengajuan_pelatihans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training_participants`
--
ALTER TABLE `training_participants`
  ADD CONSTRAINT `training_participants_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `training_participants_jabatan_id_foreign` FOREIGN KEY (`jabatan_id`) REFERENCES `jabatans` (`id`),
  ADD CONSTRAINT `training_participants_pelatihan_id_foreign` FOREIGN KEY (`pelatihan_id`) REFERENCES `surat_pengajuan_pelatihans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `training_participants_superior_id_foreign` FOREIGN KEY (`superior_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `training_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_directorate_id_foreign` FOREIGN KEY (`directorate_id`) REFERENCES `directorates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
