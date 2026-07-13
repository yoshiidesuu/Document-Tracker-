-- phpMyAdmin SQL Dump
-- version 5.2.3deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 07, 2026 at 11:19 AM
-- Server version: 8.4.9-0ubuntu0.26.04.1
-- PHP Version: 8.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `document-tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `arta_settings`
--

CREATE TABLE `arta_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `days` int UNSIGNED DEFAULT NULL,
  `hours` int UNSIGNED DEFAULT NULL,
  `minutes` int UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `creator_id` bigint UNSIGNED NOT NULL,
  `processing_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `qr_value` varchar(255) NOT NULL,
  `barcode_value` varchar(255) NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `access_key` varchar(255) DEFAULT NULL,
  `arta_category` varchar(50) NOT NULL DEFAULT 'simple',
  `notes` text,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `termination_reason` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `arta_setting_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_tracks`
--

CREATE TABLE `document_tracks` (
  `id` bigint UNSIGNED NOT NULL,
  `document_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `released_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_login_attempts`
--

CREATE TABLE `failed_login_attempts` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `receiver_id` bigint UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_05_000001_create_security_logs_table', 1),
(5, '2026_06_05_000002_create_failed_login_attempts_table', 1),
(6, '2026_06_05_000003_create_password_histories_table', 1),
(7, '2026_06_05_000004_add_security_fields_to_users_table', 1),
(8, '2026_06_05_000005_add_profile_fields_to_users_table', 1),
(9, '2026_06_05_001117_create_personal_access_tokens_table', 1),
(10, '2026_06_05_000006_create_roles_tables', 2),
(11, '2026_06_05_000007_create_system_settings_table', 3),
(12, '2026_06_05_000008_create_departments_table', 4),
(13, '2026_06_05_000009_create_offices_table', 5),
(14, '2026_06_05_000010_create_documents_table', 6),
(15, '2026_06_05_000011_add_access_fields_to_documents_table', 7),
(16, '2026_06_05_000011_create_arta_settings_table', 7),
(17, '2026_06_05_000012_add_arta_setting_id_to_documents_table', 7),
(18, '2026_06_05_000012_add_department_office_to_users_table', 7),
(19, '2026_06_05_135911_create_personal_access_tokens_table', 8),
(20, '2026_06_05_140000_create_document_types_table', 9),
(21, '2026_06_06_000001_create_document_tracks_table', 10),
(22, '2026_06_06_000002_add_status_to_documents_table', 11),
(23, '2026_06_06_000003_add_termination_reason_to_documents_table', 12),
(24, '2026_06_07_000001_create_messages_table', 13),
(25, '2026_06_07_000002_add_messages_permission_to_roles', 14),
(26, '2026_06_07_000003_add_email_settings_permission_to_roles', 15),
(27, '2026_06_07_000004_update_email_settings_permission_key', 16),
(28, '2026_06_07_000005_add_activity_logs_permission_to_roles', 17),
(29, '2026_06_07_000006_create_user_activities_table', 18),
(30, '2026_06_07_000007_add_security_logs_permission_to_roles', 18);

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_histories`
--

CREATE TABLE `password_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `permissions` json DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `permissions`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', 'Full system access', '[\"dashboard\", \"dashboard.access\", \"users\", \"users.list\", \"users.view\", \"users.create\", \"users.edit\", \"users.delete\", \"users.ban\", \"users.unban\", \"users.lock\", \"users.unlock\", \"users.force-logout\", \"users.reset-password\", \"users.bulk-delete\", \"users.bulk-ban\", \"users.bulk-unban\", \"users.bulk-lock\", \"users.bulk-unlock\", \"roles\", \"roles.list\", \"roles.create\", \"roles.edit\", \"roles.delete\", \"permissions\", \"permissions.manage\", \"departments\", \"departments.list\", \"departments.view\", \"departments.create\", \"departments.edit\", \"departments.delete\", \"departments.toggle-status\", \"offices\", \"offices.list\", \"offices.view\", \"offices.create\", \"offices.edit\", \"offices.delete\", \"offices.toggle-status\", \"documents\", \"documents.list\", \"documents.view\", \"documents.create\", \"documents.edit\", \"documents.delete\", \"my-documents\", \"documents.my\", \"document-receiving\", \"documents.receive\", \"document-finish\", \"documents.finish\", \"document-terminate\", \"documents.terminate\", \"arta\", \"arta.list\", \"arta.view\", \"arta.create\", \"arta.edit\", \"arta.delete\", \"arta.toggle-status\", \"document-types\", \"document-types.list\", \"document-types.view\", \"document-types.create\", \"document-types.edit\", \"document-types.delete\", \"document-types.toggle-status\", \"settings\", \"settings.access\", \"statistics\", \"statistics.access\", \"my-scanned\", \"documents.my-scanned\", \"documents.reopen\", \"messages\", \"messages.access\", \"messages.send\", \"email-settings.access\", \"email-settings\", \"activity-logs\", \"activity-logs.access\", \"security-logs\", \"security-logs.access\"]', 1, '2026-06-04 18:25:29', '2026-06-07 10:34:35'),
(2, 'Staff', 'staff', 'Standard staff access', '[\"documents.create\", \"documents.view\", \"reports.view\", \"departments\", \"departments.list\", \"departments.view\", \"offices\", \"offices.list\", \"offices.view\", \"documents\", \"documents.list\", \"messages\", \"messages.access\", \"messages.send\", \"email-settings\", \"email-settings.access\", \"activity-logs\", \"activity-logs.access\", \"security-logs\", \"security-logs.access\"]', 1, '2026-06-04 18:25:29', '2026-06-07 10:34:35'),
(3, 'Viewer', 'viewer', 'Read-only access', '[\"documents.view\", \"messages\", \"messages.access\", \"messages.send\", \"email-settings\", \"email-settings.access\", \"activity-logs\", \"activity-logs.access\", \"security-logs\", \"security-logs.access\"]', 1, '2026-06-04 18:25:29', '2026-06-07 10:34:35');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `role_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`role_id`, `user_id`) VALUES
(1, 1),
(1, 8);

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `event` varchar(100) NOT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `request_method` varchar(10) DEFAULT NULL,
  `request_url` varchar(2048) DEFAULT NULL,
  `severity` varchar(20) NOT NULL DEFAULT 'info',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'site_long_name', 'Advanced Document Tracker', '2026-06-05 02:26:47', '2026-06-05 02:26:47'),
(2, 'site_short_name', 'ADTM', '2026-06-05 02:26:47', '2026-06-05 02:26:47'),
(3, 'site_description', 'A secure, military-grade document management system.', '2026-06-05 02:26:47', '2026-06-05 02:26:47'),
(4, 'color_primary', '#940000', '2026-06-05 02:26:47', '2026-06-05 05:24:55'),
(5, 'color_secondary', '#eb0000', '2026-06-05 02:26:47', '2026-06-05 05:25:09'),
(6, 'emails', '[\"admin@gmail.com\"]', '2026-06-05 02:26:47', '2026-06-06 02:10:53'),
(7, 'contacts', '[\"091234567890\"]', '2026-06-05 02:26:47', '2026-06-06 02:10:53'),
(8, 'addresses', '[\"Metro Manila, Philippines\"]', '2026-06-05 02:26:47', '2026-06-06 02:10:53'),
(9, 'site_logo', 'logo.png', '2026-06-05 02:46:38', '2026-06-05 02:46:38'),
(10, 'site_favicon', 'favicon.png', '2026-06-05 02:46:38', '2026-06-05 02:46:38'),
(11, 'document_header_title', 'Advanced Document Tracker', '2026-06-06 02:06:38', '2026-06-06 02:06:38'),
(12, 'document_right_logo', 'logo.png', '2026-06-06 02:06:38', '2026-06-06 02:06:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_hash` varchar(64) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `mfa_secret` text,
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `mfa_recovery_codes_generated_at` timestamp NULL DEFAULT NULL,
  `terms_accepted_at` timestamp NULL DEFAULT NULL,
  `privacy_accepted_at` timestamp NULL DEFAULT NULL,
  `login_count` int NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(2048) DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `office_id` bigint UNSIGNED DEFAULT NULL,
  `age` tinyint UNSIGNED DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `bday` date DEFAULT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `ip` varchar(45) DEFAULT NULL,
  `geolocation` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_hash`, `last_login_at`, `last_login_ip`, `mfa_secret`, `mfa_enabled`, `mfa_recovery_codes_generated_at`, `terms_accepted_at`, `privacy_accepted_at`, `login_count`, `email_verified_at`, `password`, `password_changed_at`, `remember_token`, `profile_picture`, `id_number`, `firstname`, `middlename`, `lastname`, `department_id`, `office_id`, `age`, `gender`, `bday`, `locked`, `banned`, `status`, `ip`, `geolocation`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@gmail.com', 'aaf2a900bb64742a7bf01dccf84ed71805c19081ec80b22454d6231e951aa328', '2026-06-07 10:19:37', '127.0.0.1', NULL, 0, NULL, NULL, NULL, 16, '2026-06-04 16:26:12', '$2y$12$eY77dUhBWdtjTB1HTbvMXukFDxn3OOafba3XumKvJFpEq7yDfJS2G', '2026-06-04 16:26:13', NULL, NULL, 'ADM-0001', 'Admin', NULL, 'User', NULL, NULL, 30, 'prefer-not-to-say', '1996-01-01', 0, 0, 'active', NULL, NULL, '2026-06-04 16:26:13', '2026-06-07 10:19:37');

-- --------------------------------------------------------

--
-- Table structure for table `user_activities`
--

CREATE TABLE `user_activities` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `activity` varchar(100) NOT NULL,
  `description` text,
  `metadata` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arta_settings`
--
ALTER TABLE `arta_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `arta_settings_category_title_unique` (`category`,`title`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_code_unique` (`code`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documents_qr_value_unique` (`qr_value`),
  ADD UNIQUE KEY `documents_barcode_value_unique` (`barcode_value`),
  ADD KEY `documents_creator_id_foreign` (`creator_id`),
  ADD KEY `documents_arta_setting_id_foreign` (`arta_setting_id`);

--
-- Indexes for table `document_tracks`
--
ALTER TABLE `document_tracks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_tracks_document_id_foreign` (`document_id`),
  ADD KEY `document_tracks_user_id_foreign` (`user_id`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_types_code_unique` (`code`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `failed_login_attempts`
--
ALTER TABLE `failed_login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `failed_login_attempts_email_created_at_index` (`email`,`created_at`),
  ADD KEY `failed_login_attempts_ip_address_created_at_index` (`ip_address`,`created_at`),
  ADD KEY `failed_login_attempts_email_index` (`email`),
  ADD KEY `failed_login_attempts_ip_address_index` (`ip_address`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_receiver_id_index` (`sender_id`,`receiver_id`),
  ADD KEY `messages_receiver_id_read_at_index` (`receiver_id`,`read_at`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `offices_code_unique` (`code`);

--
-- Indexes for table `password_histories`
--
ALTER TABLE `password_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_histories_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `password_histories_user_id_index` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`role_id`,`user_id`),
  ADD KEY `role_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `security_logs_created_at_index` (`created_at`),
  ADD KEY `security_logs_event_created_at_index` (`event`,`created_at`),
  ADD KEY `security_logs_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `security_logs_severity_created_at_index` (`severity`,`created_at`),
  ADD KEY `security_logs_user_id_index` (`user_id`),
  ADD KEY `security_logs_event_index` (`event`),
  ADD KEY `security_logs_ip_address_index` (`ip_address`),
  ADD KEY `security_logs_severity_index` (`severity`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_email_hash_unique` (`email_hash`),
  ADD UNIQUE KEY `users_id_number_unique` (`id_number`),
  ADD KEY `users_department_id_foreign` (`department_id`),
  ADD KEY `users_office_id_foreign` (`office_id`);

--
-- Indexes for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_activities_created_at_index` (`created_at`),
  ADD KEY `user_activities_activity_created_at_index` (`activity`,`created_at`),
  ADD KEY `user_activities_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `user_activities_activity_index` (`activity`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arta_settings`
--
ALTER TABLE `arta_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_tracks`
--
ALTER TABLE `document_tracks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_login_attempts`
--
ALTER TABLE `failed_login_attempts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_histories`
--
ALTER TABLE `password_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_activities`
--
ALTER TABLE `user_activities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_arta_setting_id_foreign` FOREIGN KEY (`arta_setting_id`) REFERENCES `arta_settings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `documents_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `document_tracks`
--
ALTER TABLE `document_tracks`
  ADD CONSTRAINT `document_tracks_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_tracks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_office_id_foreign` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD CONSTRAINT `1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
