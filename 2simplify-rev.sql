-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 10 Okt 2018 pada 13.29
-- Versi Server: 10.1.13-MariaDB
-- PHP Version: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `2simplify-rev`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_history`
--

CREATE TABLE `activity_history` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `context` varchar(100) NOT NULL,
  `activity` text NOT NULL,
  `recorded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `activity_history`
--

INSERT INTO `activity_history` (`id`, `user`, `context`, `activity`, `recorded_at`) VALUES
(1, 0, 'Register owner', 'Register user pertama berhasil', '2018-10-10 13:18:06'),
(2, 0, 'Konfirmasi pengguna', 'Konfirmasi pengguna berhasil', '2018-10-10 13:27:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bussiness_entities`
--

CREATE TABLE `bussiness_entities` (
  `id` int(5) NOT NULL,
  `bussiness_entity` varchar(50) NOT NULL,
  `created_by` int(5) NOT NULL,
  `updated_by` int(5) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `bussiness_entities`
--

INSERT INTO `bussiness_entities` (`id`, `bussiness_entity`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'PT', 1, 1, '2017-03-30 10:48:31', '2017-03-30 10:48:31'),
(2, 'CV', 1, 1, '2017-03-30 10:48:31', '2017-03-30 10:48:31'),
(3, 'individu', 1, 1, '2017-03-30 10:48:31', '2017-03-30 10:48:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `companies`
--

CREATE TABLE `companies` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `bussiness_entity` int(11) NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `province` int(11) NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `relationship` int(1) NOT NULL DEFAULT '1',
  `remark` text COLLATE utf8_unicode_ci NOT NULL,
  `logo` int(5) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `currency`
--

CREATE TABLE `currency` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `symbol` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `currency`
--

INSERT INTO `currency` (`id`, `name`, `symbol`) VALUES
(1, 'Rupiah', 'Rp.'),
(2, 'US Dollar', 'US$');

-- --------------------------------------------------------

--
-- Struktur dari tabel `daily_activities`
--

CREATE TABLE `daily_activities` (
  `id` int(11) UNSIGNED NOT NULL,
  `activity` text NOT NULL,
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `default_parameter`
--

CREATE TABLE `default_parameter` (
  `id` int(5) NOT NULL,
  `parameter` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `default_parameter`
--

INSERT INTO `default_parameter` (`id`, `parameter`, `value`) VALUES
(1, 'company', '1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(1, 'direktur'),
(2, 'human resources'),
(3, 'finance'),
(4, 'accounting'),
(5, 'engineering'),
(6, 'administration');

-- --------------------------------------------------------

--
-- Struktur dari tabel `documents`
--

CREATE TABLE `documents` (
  `id` int(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(50) NOT NULL,
  `created_by` int(5) UNSIGNED DEFAULT NULL,
  `updated_by` int(5) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `documents`
--

INSERT INTO `documents` (`id`, `name`, `code`, `created_by`, `updated_by`, `created_at`, `updated_at`, `active`) VALUES
(1, 'Tanda terima', 'tt', 1, 1, '2017-04-10 11:27:30', '2017-11-13 23:14:05', 0),
(2, 'Activity report', 'ar', 1, 1, '2017-04-10 11:29:39', '2017-11-13 23:14:01', 0),
(3, 'Reimburse', 'fr', 1, 1, '2017-04-10 11:52:21', '2017-11-13 23:13:56', 0),
(4, 'Cuti', 'fv', 1, 1, '2017-04-10 11:52:21', '2017-11-13 23:13:50', 0),
(5, 'PO', 'po', 1, 1, '2017-04-10 11:52:21', '2017-04-10 11:52:21', 0),
(6, 'DO', 'do', 1, 1, '2017-04-10 11:52:21', '2017-04-10 11:52:21', 0),
(7, 'BAST', 'ba', 1, 1, '2017-04-10 11:52:21', '2017-04-10 11:52:21', 0),
(8, 'Kontrak perjanjian', 'kp', 1, 1, '2017-04-10 11:52:21', '2017-04-10 11:52:33', 0),
(9, 'Quotation', 'quo', 1, 1, '2017-04-10 11:52:21', '2017-11-13 22:47:04', 0),
(10, 'Project', 'pro', 1, 1, '2017-04-10 11:52:21', '2017-11-13 22:47:04', 1),
(11, 'Receipt', 'rcp', 1, 1, '2017-04-10 11:52:21', '2017-11-13 22:47:04', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `document_attachments`
--

CREATE TABLE `document_attachments` (
  `id` int(11) NOT NULL,
  `attachment` int(11) UNSIGNED NOT NULL COMMENT 'id data taken from table upload_files',
  `document_data` int(11) NOT NULL COMMENT 'id data dari suatu dokumen',
  `description` text NOT NULL,
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `document_data`
--

CREATE TABLE `document_data` (
  `id` int(5) NOT NULL,
  `document` int(5) DEFAULT NULL COMMENT 'jenis dokumen',
  `document_number` int(5) DEFAULT NULL COMMENT 'nomor dokumen berdasarkan jenis dokumennya',
  `asset` int(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `document_notes`
--

CREATE TABLE `document_notes` (
  `id` int(5) NOT NULL,
  `document_data` int(11) NOT NULL COMMENT 'id dari document data',
  `notes` text NOT NULL,
  `created_by` int(5) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `events`
--

CREATE TABLE `events` (
  `id` int(3) UNSIGNED NOT NULL,
  `event` text NOT NULL,
  `event_date` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(3) UNSIGNED NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `form_ar`
--

CREATE TABLE `form_ar` (
  `id` int(11) NOT NULL,
  `project_name` varchar(100) DEFAULT NULL,
  `customer` int(10) UNSIGNED NOT NULL,
  `activity_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activity` text NOT NULL,
  `next_activity` text,
  `target_completed` date DEFAULT NULL,
  `remark` text,
  `pic` varchar(100) NOT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `form_do`
--

CREATE TABLE `form_do` (
  `id` int(5) NOT NULL,
  `po_quo` int(5) UNSIGNED NOT NULL COMMENT 'id of table po_quo',
  `do_date` datetime NOT NULL,
  `do_number` varchar(100) NOT NULL,
  `delivered_by` varchar(50) NOT NULL,
  `received_by` varchar(50) NOT NULL,
  `remark` text,
  `created_by` int(5) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(5) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approved_by` int(3) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `form_po`
--

CREATE TABLE `form_po` (
  `id` int(11) UNSIGNED NOT NULL,
  `doc_date` date NOT NULL,
  `buyer` int(11) NOT NULL,
  `pic_buyer` varchar(50) NOT NULL,
  `supplier` int(11) NOT NULL,
  `pic_supplier` varchar(50) NOT NULL,
  `currency` int(11) NOT NULL,
  `ppn` int(11) NOT NULL,
  `total_discount` int(2) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `acknowledged_by` int(11) NOT NULL,
  `acknowledged_at` datetime NOT NULL,
  `approved_by` int(11) NOT NULL,
  `approved_at` datetime NOT NULL,
  `po_or_quo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:quo, 1:po',
  `remark` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `form_project`
--

CREATE TABLE `form_project` (
  `id` int(5) NOT NULL,
  `po_quo` int(5) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `pic` int(5) NOT NULL,
  `project_status` int(1) NOT NULL DEFAULT '1',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_by` int(5) NOT NULL,
  `updated_by` int(5) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `form_quo`
--

CREATE TABLE `form_quo` (
  `id` int(4) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `quo` int(4) NOT NULL,
  `quo_number` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='terkait ketika create quotation dan revisi quotation';

-- --------------------------------------------------------

--
-- Struktur dari tabel `form_receipt`
--

CREATE TABLE `form_receipt` (
  `id` int(11) UNSIGNED NOT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `receipt_date` date NOT NULL,
  `buyer` int(5) DEFAULT NULL,
  `supplier` int(5) DEFAULT NULL,
  `currency` int(11) NOT NULL,
  `ppn` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remark` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `form_receive`
--

CREATE TABLE `form_receive` (
  `id` int(5) NOT NULL,
  `receive_date` datetime NOT NULL,
  `service_point` int(10) UNSIGNED NOT NULL,
  `requisite` int(1) UNSIGNED NOT NULL,
  `submitted` int(5) UNSIGNED NOT NULL COMMENT 'pihak yang menyerahkan/membuat form tanda terima',
  `received` int(5) UNSIGNED NOT NULL COMMENT 'pihak yang menerima form tanda terima',
  `created_by` int(5) UNSIGNED DEFAULT NULL,
  `updated_by` int(5) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remark` text NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:open, 1:closed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `form_reimburse`
--

CREATE TABLE `form_reimburse` (
  `id` int(11) NOT NULL,
  `submitter` int(10) UNSIGNED DEFAULT NULL,
  `verified_by` int(10) UNSIGNED NOT NULL,
  `verified_at` datetime DEFAULT NULL,
  `approved_by` int(10) UNSIGNED NOT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `paid` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'konfirmasi dari finance person bahwa reimburse tsb sudah dibayar. 0: belum dibayar, 1:sudah dibayar, 2:konfirmasi telah dibayar',
  `send` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:dikirim, 0:disimpan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `form_vacation`
--

CREATE TABLE `form_vacation` (
  `id` int(11) NOT NULL,
  `submitter` int(10) UNSIGNED DEFAULT NULL,
  `day_used` int(11) NOT NULL,
  `requisite` int(10) UNSIGNED NOT NULL,
  `approved_by` int(10) UNSIGNED NOT NULL,
  `verified_by` int(10) UNSIGNED NOT NULL,
  `approved` int(1) NOT NULL DEFAULT '0' COMMENT '0: not yet approved, 1:approved, 2:reject',
  `verified` int(1) NOT NULL DEFAULT '0' COMMENT '0: not yet verified, 1:verified, 2:reject',
  `verified_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remark` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `document` int(11) NOT NULL COMMENT 'document id',
  `document_number` int(11) NOT NULL,
  `already_read` tinyint(1) NOT NULL DEFAULT '0',
  `for_user` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `task_group` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `display_name`, `description`, `task_group`, `created_at`, `updated_at`) VALUES
(1, 'create-user', 'create user', 'this permission allowing to create user', 1, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(2, 'update-user', 'update user', 'this permission allowing to update user', 1, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(3, 'view-user', 'view user', 'this permission allowing to view user', 1, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(4, 'activate-user', 'activate user', 'this permission allowing to activate user', 1, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(5, 'deactivate-user', 'deactivate user', 'this permission allowing to deactivate user', 1, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(6, 'create-partner', 'create partner', 'this permission allowing to create partner', 2, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(7, 'update-partner', 'update partner', 'this permission allowing to update partner', 2, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(8, 'view-partner', 'view partner', 'this permission allowing to view partner', 2, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(9, 'activate-partner', 'activate partner', 'this permission allowing to activate partner', 2, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(10, 'deactivate-partner', 'deactivate partner', 'this permission allowing to deactivate partner', 2, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(11, 'create-receive-letter', 'create receive-letter', 'this permission allowing to create receive-letter', 3, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(12, 'update-receive-letter', 'update receive-letter', 'this permission allowing to update receive-letter', 3, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(13, 'view-receive-letter', 'view receive-letter', 'this permission allowing to view receive-letter', 3, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(14, 'activate-receive-letter', 'activate receive-letter', 'this permission allowing to activate receive-letter', 3, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(15, 'deactivate-receive-letter', 'deactivate receive-letter', 'this permission allowing to deactivate receive-letter', 3, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(16, 'create-vacation', 'create vacation', 'this permission allowing to create vacation', 4, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(17, 'update-vacation', 'update vacation', 'this permission allowing to update vacation', 4, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(18, 'view-vacation', 'view vacation', 'this permission allowing to view vacation', 4, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(19, 'setup-vacation', 'setup vacation', 'this permission allowing to setup vacation', 4, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(20, 'verify-vacation', 'verify vacation', 'this permission allowing to verify vacation', 4, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(21, 'create-activity-report', 'create activity-report', 'this permission allowing to create activity-report', 5, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(22, 'update-activity-report', 'update activity-report', 'this permission allowing to update activity-report', 5, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(23, 'view-activity-report', 'view activity-report', 'this permission allowing to view activity-report', 5, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(24, 'activate-activity-report', 'activate activity-report', 'this permission allowing to activate activity-report', 5, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(25, 'deactivate-activity-report', 'deactivate activity-report', 'this permission allowing to deactivate activity-report', 5, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(26, 'create-reimburse', 'create reimburse', 'this permission allowing to create reimburse', 6, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(27, 'update-reimburse', 'update reimburse', 'this permission allowing to update reimburse', 6, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(28, 'view-reimburse', 'view reimburse', 'this permission allowing to view reimburse', 6, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(29, 'activate-reimburse', 'activate reimburse', 'this permission allowing to activate reimburse', 6, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(30, 'deactivate-reimburse', 'deactivate reimburse', 'this permission allowing to deactivate reimburse', 6, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(32, 'update-product', 'update product', 'this permission allowing to update product', 7, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(33, 'view-product', 'view product', 'this permission allowing to view product', 7, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(34, 'activate-product', 'activate product', 'this permission allowing to activate product', 7, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(35, 'deactivate-product', 'deactivate product', 'this permission allowing to deactivate product', 7, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(36, 'create-stock', 'create stock', 'this permission allowing to create stock', 8, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(37, 'update-stock', 'update stock', 'this permission allowing to update stock', 8, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(38, 'view-stock', 'view stock', 'this permission allowing to view stock', 8, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(39, 'activate-stock', 'activate stock', 'this permission allowing to activate stock', 8, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(40, 'deactivate-stock', 'deactivate stock', 'this permission allowing to deactivate stock', 8, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(41, 'create-data-cassete', 'create data-cassete', 'this permission allowing to create data-cassete', 9, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(42, 'update-data-cassete', 'update data-cassete', 'this permission allowing to update data-cassete', 9, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(43, 'view-data-cassete', 'view data-cassete', 'this permission allowing to view data-cassete', 9, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(44, 'activate-data-cassete', 'activate data-cassete', 'this permission allowing to activate data-cassete', 9, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(45, 'deactivate-data-cassete', 'deactivate data-cassete', 'this permission allowing to deactivate data-cassete', 9, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(46, 'create-data-po', 'create data-po', 'this permission allowing to create data-po', 10, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(47, 'update-data-po', 'update data-po', 'this permission allowing to update data-po', 10, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(48, 'view-data-po', 'view data-po', 'this permission allowing to view data-po', 10, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(49, 'activate-data-po', 'activate data-po', 'this permission allowing to activate data-po', 10, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(50, 'deactivate-data-po', 'deactivate data-po', 'this permission allowing to deactivate data-po', 10, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(51, 'create-data-do', 'create data-do', 'this permission allowing to create data-do', 11, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(52, 'update-data-do', 'update data-do', 'this permission allowing to update data-do', 11, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(53, 'view-data-do', 'view data-do', 'this permission allowing to view data-do', 11, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(54, 'approval-data-do', 'activate data-do', 'this permission allowing to make approval data-do', 11, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(55, 'deactivate-data-do', 'deactivate data-do', 'this permission allowing to deactivate data-do', 11, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(56, 'create-data-quo', 'create data-quo', 'this permission allowing to create data-quo', 12, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(57, 'update-data-quo', 'update data-quo', 'this permission allowing to update data-quo', 12, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(58, 'view-data-quo', 'view data-quo', 'this permission allowing to view data-quo', 12, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(59, 'activate-data-quo', 'activate data-quo', 'this permission allowing to activate data-quo', 12, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(60, 'deactivate-data-quo', 'deactivate data-quo', 'this permission allowing to deactivate data-quo', 12, '2017-02-18 10:16:16', '2017-02-18 10:16:16'),
(61, 'create-asset', 'create asset', 'this permission allowing to create asset', 13, '2017-04-07 03:45:06', '2017-04-07 03:45:06'),
(62, 'update-asset', 'update asset', 'this permission allowing to update asset', 13, '2017-04-07 03:45:06', '2017-04-07 03:45:06'),
(63, 'view-asset', 'view asset', 'this permission allowing to view asset', 13, '2017-04-07 03:45:06', '2017-04-07 03:45:06'),
(64, 'activate-asset', 'activate asset', 'this permission allowing to activate asset', 13, '2017-04-07 03:45:06', '2017-04-07 03:45:06'),
(65, 'deactivate-asset', 'deactivate asset', 'this permission allowing to deactivate asset', 13, '2017-04-07 03:45:06', '2017-04-07 03:45:06'),
(66, 'upload-data', 'upload data', 'upload data to database', 14, '2017-04-20 07:07:33', '2017-04-20 07:07:33'),
(67, 'remove-upload', 'remove upload data', 'remove upload data from database', 14, '2017-04-20 07:07:33', '2017-04-20 07:07:33'),
(68, 'view-activity-history', 'Activity History', 'to view Activity History', 15, '2017-05-19 07:20:34', '2017-05-19 07:20:34'),
(69, 'view-notes', 'View Notes', 'to view all notes', 16, '2017-05-22 07:41:50', '2017-05-22 07:41:50'),
(70, 'create-notes', 'Create Notes', 'to create all notes', 16, '2017-05-22 07:41:50', '2017-05-22 07:41:50'),
(71, 'remove-asset', 'Removing asset', 'this permission allowing to remove asset', 13, '2017-05-24 08:45:00', '2017-05-24 08:45:00'),
(72, 'print-receive-form', 'Print the receive form', 'this permission allowing to user to print the receive form', 17, '2017-05-29 06:03:16', '2017-05-29 06:03:16'),
(73, 'view-attachment', 'view attachment', 'allowing to view the document attachment', 18, '2017-05-30 07:02:33', '2017-05-30 07:02:33'),
(74, 'create-attachment', 'create attachment', 'allowing to create the document attachment', 18, '2017-05-31 07:21:33', '2017-05-31 07:21:33'),
(75, 'print-activity-report', 'Print the activity report', 'this permission allowing to user to print the activity report', 17, '2017-06-20 07:22:16', '2017-06-20 07:22:16'),
(76, 'print-vacation-form', 'Print the vacation form', 'this permission allowing to user to print the vacation form', 17, '2017-06-20 07:27:39', '2017-06-20 07:27:39'),
(77, 'print-reimburse-form', 'Print the reimburse form', 'this permission allowing to user to print the reimburse form', 17, '2017-06-20 07:27:39', '2017-06-20 07:27:39'),
(78, 'print-po', 'Print the purchase order', 'this permission allowing to user to print the purchase order', 17, '2017-06-20 07:27:39', '2017-06-20 07:27:39'),
(79, 'print-do', 'Print the delivery order', 'this permission allowing to user to print the delivery order', 17, '2017-06-20 07:27:39', '2017-06-20 07:27:39'),
(80, 'approval-vacation', 'approval vacation', 'this permission allowing to approve or reject vacation', 4, '2017-07-13 03:35:16', '2017-07-13 03:35:16'),
(81, 'approval-reimburse', 'approval reimburse', 'this permission allowing to approve or reject reimburse form', 3, '2017-07-13 03:35:16', '2017-07-13 03:35:16'),
(82, 'create-product-category', 'create product category', '', 7, '2017-10-23 09:06:48', '2017-10-23 09:06:48'),
(83, 'update-product-category', 'update product category', '', 7, '2017-10-23 09:08:07', '2017-10-23 09:08:07'),
(84, 'view-product-category', 'view product category', '', 7, '2017-10-23 09:08:07', '2017-10-23 09:08:07'),
(88, 'create-product', 'create product', '', 7, '2017-11-01 08:30:20', '2017-11-01 08:30:20'),
(89, 'create-product-vendor', 'create product vendor', '', 7, '2017-11-01 08:30:20', '2017-11-01 08:30:20'),
(90, 'update-product-vendor', 'update product vendor', '', 7, '2017-11-01 08:30:20', '2017-11-01 08:30:20'),
(91, 'print-quo', 'Print the Quotation form', 'this permission allowing to user to print the Quotation form', 17, '2017-12-04 07:55:39', '2017-12-04 07:55:39'),
(92, 'remove-data-quo', 'remove data quo', 'this permission allowing to remove data quo', 12, '2017-12-12 09:17:16', '2017-12-12 09:17:16'),
(93, 'remove-data-po', 'remove data po', 'this permission allowing to remove data po', 10, '2017-12-12 09:18:16', '2017-12-12 09:18:16'),
(94, 'approval-data-quo', 'approval-data quo', 'this permission allowing to give approval related to data quo', 12, '2017-12-13 09:20:16', '2017-12-13 09:20:16'),
(95, 'create-activity', 'Create activity', 'Allow to create activity', 16, '2018-02-09 03:43:40', '2018-02-09 03:43:40'),
(96, 'create-event', 'Create event', 'Allow to create event', 16, '2018-02-09 03:43:40', '2018-02-09 03:43:40'),
(97, 'update-event', 'Update event', 'Allow to update event', 16, '2018-02-09 03:43:40', '2018-02-09 03:43:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `permission_role`
--

CREATE TABLE `permission_role` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data untuk tabel `permission_role`
--

INSERT INTO `permission_role` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(2, 6),
(3, 6),
(1, 7),
(2, 7),
(1, 8),
(2, 8),
(3, 8),
(4, 8),
(1, 9),
(2, 9),
(1, 10),
(2, 10),
(1, 11),
(2, 11),
(3, 11),
(1, 12),
(2, 12),
(1, 13),
(2, 13),
(3, 13),
(4, 13),
(1, 14),
(2, 14),
(1, 15),
(2, 15),
(1, 16),
(2, 16),
(3, 16),
(1, 17),
(2, 17),
(1, 18),
(2, 18),
(3, 18),
(4, 18),
(1, 19),
(2, 19),
(1, 20),
(2, 20),
(1, 21),
(2, 21),
(3, 21),
(1, 22),
(2, 22),
(1, 23),
(2, 23),
(3, 23),
(4, 23),
(1, 24),
(2, 24),
(1, 25),
(2, 25),
(1, 26),
(2, 26),
(3, 26),
(1, 27),
(2, 27),
(1, 28),
(2, 28),
(3, 28),
(4, 28),
(1, 29),
(2, 29),
(1, 30),
(2, 30),
(1, 32),
(2, 32),
(1, 33),
(2, 33),
(3, 33),
(4, 33),
(1, 34),
(2, 34),
(1, 35),
(2, 35),
(1, 36),
(2, 36),
(3, 36),
(1, 37),
(2, 37),
(1, 38),
(2, 38),
(3, 38),
(4, 38),
(1, 39),
(2, 39),
(1, 40),
(2, 40),
(1, 41),
(2, 41),
(3, 41),
(1, 42),
(2, 42),
(1, 43),
(2, 43),
(3, 43),
(4, 43),
(1, 44),
(2, 44),
(1, 45),
(2, 45),
(1, 46),
(2, 46),
(3, 46),
(1, 47),
(2, 47),
(1, 48),
(2, 48),
(3, 48),
(4, 48),
(1, 49),
(2, 49),
(1, 50),
(2, 50),
(1, 51),
(2, 51),
(3, 51),
(1, 52),
(2, 52),
(1, 53),
(2, 53),
(3, 53),
(4, 53),
(1, 54),
(2, 54),
(1, 55),
(2, 55),
(1, 56),
(2, 56),
(3, 56),
(1, 57),
(2, 57),
(1, 58),
(2, 58),
(3, 58),
(4, 58),
(1, 59),
(2, 59),
(1, 60),
(2, 60),
(1, 61),
(2, 61),
(3, 61),
(1, 62),
(2, 62),
(1, 63),
(2, 63),
(3, 63),
(4, 63),
(1, 64),
(2, 64),
(1, 65),
(2, 65),
(1, 66),
(2, 66),
(3, 66),
(1, 67),
(2, 67),
(1, 68),
(1, 69),
(1, 70),
(2, 70),
(1, 71),
(1, 72),
(1, 73),
(1, 74),
(1, 75),
(1, 76),
(1, 77),
(1, 78),
(1, 79),
(2, 80),
(1, 81),
(2, 81),
(1, 82),
(1, 83),
(1, 84),
(1, 88),
(1, 89),
(1, 90),
(1, 91),
(1, 92),
(1, 93),
(1, 94),
(2, 94),
(1, 95),
(1, 96),
(1, 97);

-- --------------------------------------------------------

--
-- Struktur dari tabel `po_product`
--

CREATE TABLE `po_product` (
  `id` int(11) NOT NULL,
  `doc` int(3) UNSIGNED NOT NULL COMMENT 'id from table po_quo',
  `product` int(11) UNSIGNED NOT NULL COMMENT 'product id',
  `quantity` int(11) NOT NULL,
  `price_unit` int(11) NOT NULL,
  `item_discount` int(2) NOT NULL DEFAULT '0',
  `status` int(5) NOT NULL DEFAULT '0' COMMENT '0: not yet approved, 1: approved, 2:reject, 3:need revision'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `po_quo`
--

CREATE TABLE `po_quo` (
  `id` int(11) UNSIGNED NOT NULL,
  `quo` int(11) DEFAULT NULL COMMENT 'id of form_quo',
  `quo_revision` int(5) DEFAULT NULL COMMENT 'id of quo_revision',
  `po` int(11) UNSIGNED DEFAULT NULL COMMENT 'id of form_po',
  `po_number` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `part_number` varchar(50) DEFAULT NULL,
  `category` int(11) NOT NULL COMMENT 'id of product categories',
  `product_vendor` int(5) DEFAULT NULL COMMENT 'id of table product_vendor',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(255) DEFAULT '#',
  `picture` int(5) DEFAULT NULL,
  `created_by` int(3) UNSIGNED NOT NULL,
  `updated_by` int(3) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `part_number`, `category`, `product_vendor`, `name`, `description`, `link`, `picture`, `created_by`, `updated_by`, `created_at`, `updated_at`, `active`) VALUES
(1, 'HSM01', 1, 1, 'ProtectServer v2', 'HSM safenet protectserver v2', 'www.gemalto.com', 9, 1, 1, '2018-10-06 06:38:30', '2018-10-06 07:23:42', 0),
(2, 'HSM02', 1, NULL, 'Luna EFT v2', 'luna eft', NULL, 13, 1, 1, '2018-10-06 07:26:55', '2018-10-06 07:38:08', 0),
(3, 'Lenovo01', 2, NULL, 'Ideapad', 'Lenovo idead 301', '', NULL, 1, 1, '2018-10-09 10:05:48', '2018-10-09 10:05:48', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(5) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `picture` int(5) DEFAULT NULL,
  `created_by` int(5) UNSIGNED DEFAULT NULL,
  `updated_by` int(5) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_vendor`
--

CREATE TABLE `product_vendor` (
  `id` int(5) NOT NULL,
  `category` int(5) NOT NULL COMMENT 'id of table product_categories',
  `vendor` int(5) NOT NULL COMMENT 'id of table vendors'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `product_vendor`
--

INSERT INTO `product_vendor` (`id`, `category`, `vendor`) VALUES
(1, 1, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_item`
--

CREATE TABLE `project_item` (
  `id` int(5) NOT NULL,
  `item_request` int(5) NOT NULL COMMENT 'id of project_item_request',
  `product` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '2' COMMENT '1:in, 2:out'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_item_request`
--

CREATE TABLE `project_item_request` (
  `id` int(5) NOT NULL,
  `project` int(5) NOT NULL,
  `request_date` date NOT NULL,
  `request_number` varchar(100) DEFAULT NULL,
  `requested_by` int(5) NOT NULL,
  `returned_by` int(5) DEFAULT NULL,
  `remark` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `provinces`
--

CREATE TABLE `provinces` (
  `id` int(5) NOT NULL,
  `province` varchar(100) NOT NULL,
  `created_by` int(5) NOT NULL,
  `updated_by` int(5) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `provinces`
--

INSERT INTO `provinces` (`id`, `province`, `created_by`, `updated_by`, `created_at`, `updated_at`, `active`) VALUES
(1, 'aceh', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(2, 'bali', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(3, 'banten', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(4, 'bengkulu', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(5, 'gorontalo', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(6, 'jakarta', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(7, 'jambi', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(8, 'jawa barat', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(9, 'jawa tengah', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(10, 'jawa timur', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(11, 'kalimantan barat', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(12, 'kalimantan selatan', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(13, 'kalimantan tengah', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(14, 'kalimantan timur', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(15, 'kalimantan utara', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(16, 'kepulauan bangka belitung', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(17, 'kepulauan riau', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(18, 'lampung', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(19, 'maluku', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(20, 'maluku utara', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(21, 'nusa tenggara barat', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(22, 'nusa tenggara timur', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(23, 'papua', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(24, 'papua barat', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(25, 'riau', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(26, 'sulawesi barat', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(27, 'sulawesi selatan', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(28, 'sulawesi tengah', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(29, 'sulawesi tenggara', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(30, 'sulawesi utara', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(31, 'sumatera barat', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(32, 'sumatera selatan', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(33, 'sumatera utara', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0),
(34, 'daerah istimewa yogyakarta', 1, 1, '2017-03-30 10:05:50', '2017-03-30 10:05:50', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `quo_product`
--

CREATE TABLE `quo_product` (
  `id` int(11) NOT NULL,
  `quo` int(3) UNSIGNED NOT NULL COMMENT 'id from table form_quo',
  `product` int(11) UNSIGNED NOT NULL COMMENT 'product id',
  `quantity` int(11) NOT NULL,
  `price_unit` int(11) NOT NULL,
  `item_discount` int(2) NOT NULL DEFAULT '0',
  `remark` text,
  `status` int(5) NOT NULL DEFAULT '0' COMMENT '0: not yet approved, 1: approved, 2:reject, 3:need revision',
  `revision` int(4) DEFAULT NULL COMMENT 'id of quo_revision'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `quo_revision`
--

CREATE TABLE `quo_revision` (
  `id` int(4) NOT NULL,
  `form_quo` int(4) NOT NULL COMMENT 'id form quo',
  `revision_number` int(4) NOT NULL,
  `doc_date` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(4) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `receipt_product`
--

CREATE TABLE `receipt_product` (
  `id` int(5) NOT NULL,
  `receipt` int(5) UNSIGNED NOT NULL COMMENT 'id of form_receipt',
  `product` int(5) UNSIGNED NOT NULL,
  `quantity` int(5) NOT NULL,
  `price` int(11) NOT NULL,
  `discount` int(3) DEFAULT '0',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(5) NOT NULL,
  `remark` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `reimburse_detail`
--

CREATE TABLE `reimburse_detail` (
  `id` int(11) NOT NULL,
  `receipt_date` date NOT NULL,
  `requisite` int(11) NOT NULL,
  `cost` varchar(50) DEFAULT NULL,
  `remark` text,
  `document_number` int(11) NOT NULL COMMENT 'id dari reimburse form',
  `approved` int(1) NOT NULL DEFAULT '0' COMMENT '0: not yet approved, 1: approved, 2:reject, 3:need revision'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `relationships`
--

CREATE TABLE `relationships` (
  `id` int(5) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `created_by` int(5) NOT NULL,
  `updated_by` int(5) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `relationships`
--

INSERT INTO `relationships` (`id`, `relationship`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'own', 1, 1, '2017-03-30 10:51:28', '2017-03-30 10:51:28'),
(2, 'partner', 1, 1, '2017-03-30 10:51:28', '2017-03-30 10:51:28'),
(3, 'customer', 1, 1, '2017-04-05 15:45:10', '2017-04-05 15:45:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `requisite`
--

CREATE TABLE `requisite` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `form` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `requisite`
--

INSERT INTO `requisite` (`id`, `name`, `form`) VALUES
(1, 'pinjam', 1),
(2, 'serah terima', 1),
(3, 'cuti tahunan', 4),
(4, 'cuti sakit', 4),
(5, 'cuti melahirkan', 4),
(6, 'cuti menikah', 4),
(7, 'cuti menikahkan', 4),
(8, 'cuti khitanan', 4),
(9, 'cuti baptis', 4),
(10, 'cuti menemani istri melahirkan/istri mengalami keguguran', 4),
(11, 'cuti karena suami/istri, orangtua/mertua, anak/menantu meninggal dunia', 4),
(12, 'cuti karena anggota keluarga dalam satu rumah meninggal dunia', 4),
(13, 'cuti bersama', 4),
(14, 'transport', 3),
(15, 'parkir', 3),
(16, 'makan', 3),
(17, 'tiket tol', 3),
(18, 'lain-lain', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'superadmin', 'Role ini memiliki akses ke semua fitur', '2017-01-25 04:03:17', '2017-01-25 04:03:17'),
(2, 'supervisor', 'supervisor/manager', 'Role ini memiliki akses hampir ke semua fitur kecuali permission untuk manajemen user', '2017-01-25 08:19:17', '2017-01-25 08:19:17'),
(3, 'staff', 'staff', 'Role ini hanya memiliki akses untuk membuat dan membaca data', '2017-02-18 10:31:19', '2017-02-18 10:31:19'),
(4, 'viewer', 'pengguna biasa', 'Role ini hanya memiliki akses untuk membaca data', '2017-02-18 10:31:19', '2017-02-18 10:31:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_user`
--

CREATE TABLE `role_user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_points`
--

CREATE TABLE `service_points` (
  `id` int(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `service_points`
--

INSERT INTO `service_points` (`id`, `name`, `code`) VALUES
(1, 'jakarta', 'SNC-JKT'),
(2, 'medan', 'SNC-JKT'),
(3, 'semarang', 'SNC-SMG'),
(4, 'surabaya', 'SNC-SBY'),
(5, 'makassar', 'SNC-MKS'),
(6, 'kendari', 'SNC-KENDARI');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stocks`
--

CREATE TABLE `stocks` (
  `id` int(5) UNSIGNED NOT NULL,
  `product` int(5) UNSIGNED DEFAULT NULL,
  `quantity` int(5) DEFAULT NULL,
  `stock_relation` int(5) DEFAULT NULL,
  `received_at` date DEFAULT NULL COMMENT 'tanggal stock diterima',
  `send_at` date DEFAULT NULL COMMENT 'tanggal dikirim (stock out)',
  `created_by` int(5) UNSIGNED DEFAULT NULL,
  `updated_by` int(5) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '1:in, 2:out'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_relation`
--

CREATE TABLE `stock_relation` (
  `id` int(5) NOT NULL,
  `document` int(2) NOT NULL COMMENT '6:DO, 10:project, 11:receipt',
  `spec_doc` int(11) NOT NULL COMMENT 'specific document'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `task_groups`
--

CREATE TABLE `task_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(30) CHARACTER SET latin1 NOT NULL,
  `display_name` varchar(30) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `task_groups`
--

INSERT INTO `task_groups` (`id`, `name`, `display_name`) VALUES
(1, 'manage_user', 'manajemen pengguna'),
(2, 'manage_partner', 'manajemen partner/customer'),
(3, 'manage_form_receive_letter', 'manajemen form tanda terima'),
(4, 'manage_form_vacation', 'manajemen form cuti'),
(5, 'manage_activity_report', 'manajemen form activity report'),
(6, 'manage_reimburse', 'manajemen form reimburse'),
(7, 'manage_product', 'manajemen produk'),
(8, 'manage_stock', 'manajemen data stock'),
(9, 'manage_cassete', 'manajemen data cassete'),
(10, 'manage_po', 'manajemen data PO'),
(11, 'manage_do', 'manajemen data DO'),
(12, 'manage_quo', 'manajemen data QUO'),
(13, 'manage_assets', 'manajemen data aset'),
(14, 'upload_data', 'upload data'),
(15, 'activity_history', 'Activity history'),
(16, 'notes', 'Notes'),
(17, 'print_data', 'printing the data'),
(18, 'manage_attachment', 'manage document attachment');

-- --------------------------------------------------------

--
-- Struktur dari tabel `upload_files`
--

CREATE TABLE `upload_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `upload_file` varchar(255) NOT NULL,
  `description` text,
  `file_type` int(1) DEFAULT NULL COMMENT '1:image, 2:audio, 3:video',
  `public` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'value 1: public-->data bisa dibuka oleh pihak manapun',
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department` int(11) NOT NULL,
  `photo` int(4) UNSIGNED DEFAULT NULL,
  `signature` int(4) UNSIGNED DEFAULT NULL,
  `confirmation_link` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(5) NOT NULL,
  `updated_by` int(5) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `vacation_date`
--

CREATE TABLE `vacation_date` (
  `id` int(11) NOT NULL,
  `vacation_date` date NOT NULL,
  `document_number` int(11) NOT NULL COMMENT 'id dari data pada tabel form_vacation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `vacation_per_year`
--

CREATE TABLE `vacation_per_year` (
  `year` int(5) NOT NULL,
  `yearly_v` int(2) NOT NULL COMMENT 'cuti tahunan',
  `birth_v` int(2) NOT NULL COMMENT 'cuti melahirkan',
  `merried_v` int(2) NOT NULL COMMENT 'cuti menikah ',
  `merried_off_v` int(2) NOT NULL COMMENT 'cuti menikahkan',
  `circumcision_v` int(2) NOT NULL COMMENT 'cuti khitanan',
  `baptism_v` int(2) NOT NULL COMMENT 'cuti baptis',
  `accompany_wife` int(2) NOT NULL COMMENT 'cuti menemani istri melahirkan/istri keguguran',
  `close_family_passed_away` int(2) NOT NULL COMMENT 'cuti karena keluarga dekat meninggal dunia',
  `family_passed_away` int(2) NOT NULL COMMENT 'cuti karena keluarga dalam satu rumah meninggal dunia',
  `joint_holiday` int(2) NOT NULL COMMENT 'cuti bersama',
  `remark` text NOT NULL COMMENT 'keterangan tambahan terkait cuti'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(255) DEFAULT '#',
  `created_by` int(3) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(3) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_history`
--
ALTER TABLE `activity_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bussiness_entities`
--
ALTER TABLE `bussiness_entities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daily_activities`
--
ALTER TABLE `daily_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `default_parameter`
--
ALTER TABLE `default_parameter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `document_attachments`
--
ALTER TABLE `document_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachment` (`attachment`),
  ADD KEY `document_data` (`document_data`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `document_data`
--
ALTER TABLE `document_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document` (`document`),
  ADD KEY `asset` (`asset`);

--
-- Indexes for table `document_notes`
--
ALTER TABLE `document_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `form_ar`
--
ALTER TABLE `form_ar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer` (`customer`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `form_do`
--
ALTER TABLE `form_do`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_quo` (`po_quo`);

--
-- Indexes for table `form_po`
--
ALTER TABLE `form_po`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_project`
--
ALTER TABLE `form_project`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po` (`po_quo`);

--
-- Indexes for table `form_quo`
--
ALTER TABLE `form_quo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quo_number` (`quo_number`);

--
-- Indexes for table `form_receipt`
--
ALTER TABLE `form_receipt`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_receive`
--
ALTER TABLE `form_receive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisite` (`requisite`),
  ADD KEY `submitted` (`submitted`),
  ADD KEY `received` (`received`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `service_point` (`service_point`);

--
-- Indexes for table `form_reimburse`
--
ALTER TABLE `form_reimburse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submitter` (`submitter`),
  ADD KEY `verified_by` (`verified_by`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `form_vacation`
--
ALTER TABLE `form_vacation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submitter` (`submitter`),
  ADD KEY `requisite` (`requisite`),
  ADD KEY `approval_by` (`approved_by`),
  ADD KEY `verify_by` (`verified_by`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document` (`document`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`),
  ADD KEY `task_group` (`task_group`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `po_product`
--
ALTER TABLE `po_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product` (`product`),
  ADD KEY `doc` (`doc`);

--
-- Indexes for table `po_quo`
--
ALTER TABLE `po_quo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quo_revision` (`quo_revision`),
  ADD KEY `po` (`po`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `part_number` (`part_number`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `product_vendor`
--
ALTER TABLE `product_vendor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_item`
--
ALTER TABLE `project_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product` (`product`),
  ADD KEY `item_request` (`item_request`);

--
-- Indexes for table `project_item_request`
--
ALTER TABLE `project_item_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project` (`project`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quo_product`
--
ALTER TABLE `quo_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product` (`product`),
  ADD KEY `doc` (`quo`);

--
-- Indexes for table `quo_revision`
--
ALTER TABLE `quo_revision`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receipt_product`
--
ALTER TABLE `receipt_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt` (`receipt`),
  ADD KEY `product` (`product`);

--
-- Indexes for table `reimburse_detail`
--
ALTER TABLE `reimburse_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisite` (`requisite`),
  ADD KEY `document` (`document_number`);

--
-- Indexes for table `relationships`
--
ALTER TABLE `relationships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requisite`
--
ALTER TABLE `requisite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form` (`form`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

--
-- Indexes for table `service_points`
--
ALTER TABLE `service_points`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product` (`product`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `stock_relation` (`stock_relation`);

--
-- Indexes for table `stock_relation`
--
ALTER TABLE `stock_relation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_groups`
--
ALTER TABLE `task_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `upload_files`
--
ALTER TABLE `upload_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `confirmation_link` (`confirmation_link`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `photo` (`photo`),
  ADD KEY `signature` (`signature`);

--
-- Indexes for table `vacation_date`
--
ALTER TABLE `vacation_date`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vacation_per_year`
--
ALTER TABLE `vacation_per_year`
  ADD UNIQUE KEY `year` (`year`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_history`
--
ALTER TABLE `activity_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `bussiness_entities`
--
ALTER TABLE `bussiness_entities`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `daily_activities`
--
ALTER TABLE `daily_activities`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `default_parameter`
--
ALTER TABLE `default_parameter`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `document_attachments`
--
ALTER TABLE `document_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `document_data`
--
ALTER TABLE `document_data`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `document_notes`
--
ALTER TABLE `document_notes`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form_ar`
--
ALTER TABLE `form_ar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form_do`
--
ALTER TABLE `form_do`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form_po`
--
ALTER TABLE `form_po`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form_project`
--
ALTER TABLE `form_project`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form_quo`
--
ALTER TABLE `form_quo`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form_receipt`
--
ALTER TABLE `form_receipt`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form_receive`
--
ALTER TABLE `form_receive`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form_reimburse`
--
ALTER TABLE `form_reimburse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form_vacation`
--
ALTER TABLE `form_vacation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;
--
-- AUTO_INCREMENT for table `po_product`
--
ALTER TABLE `po_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `po_quo`
--
ALTER TABLE `po_quo`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `product_vendor`
--
ALTER TABLE `product_vendor`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `project_item`
--
ALTER TABLE `project_item`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `project_item_request`
--
ALTER TABLE `project_item_request`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `quo_product`
--
ALTER TABLE `quo_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `quo_revision`
--
ALTER TABLE `quo_revision`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `receipt_product`
--
ALTER TABLE `receipt_product`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `reimburse_detail`
--
ALTER TABLE `reimburse_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `relationships`
--
ALTER TABLE `relationships`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `requisite`
--
ALTER TABLE `requisite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `service_points`
--
ALTER TABLE `service_points`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `stock_relation`
--
ALTER TABLE `stock_relation`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `task_groups`
--
ALTER TABLE `task_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `upload_files`
--
ALTER TABLE `upload_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vacation_date`
--
ALTER TABLE `vacation_date`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `daily_activities`
--
ALTER TABLE `daily_activities`
  ADD CONSTRAINT `daily_activities_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
