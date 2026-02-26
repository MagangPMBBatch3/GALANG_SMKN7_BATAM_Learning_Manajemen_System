-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Agu 2025 pada 09.31
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `progresproject`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `aktivitas`
--

CREATE TABLE `aktivitas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bagian_id` bigint(20) UNSIGNED NOT NULL,
  `no_wbs` varchar(50) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `user_profile_id` bigint(20) UNSIGNED DEFAULT NULL,
  `proyek_id` bigint(20) UNSIGNED DEFAULT NULL,
  `keterangan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('todo','in_progress','completed','cancelled') NOT NULL DEFAULT 'todo',
  `progress` int(11) NOT NULL DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `aktivitas`
--

INSERT INTO `aktivitas` (`id`, `bagian_id`, `no_wbs`, `nama`, `created_at`, `updated_at`, `deleted_at`, `user_profile_id`, `proyek_id`, `keterangan_id`, `status`, `progress`, `start_date`, `end_date`, `priority`, `description`) VALUES
(1, 1, '121212', 'Luhut', '2025-08-11 21:18:18', '2025-08-14 19:53:04', NULL, NULL, NULL, NULL, 'todo', 0, NULL, NULL, 'medium', NULL),
(3, 1, '908978', 'Belajar', '2025-08-14 19:46:00', '2025-08-14 19:53:09', NULL, NULL, NULL, NULL, 'todo', 0, NULL, NULL, 'medium', NULL),
(6, 1, '234', 'dadgdge', '2025-08-14 19:51:06', '2025-08-14 19:51:06', NULL, NULL, NULL, NULL, 'todo', 0, NULL, NULL, 'medium', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `bagian`
--

CREATE TABLE `bagian` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bagian`
--

INSERT INTO `bagian` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'King', '2025-08-11 01:54:29', '2025-08-13 20:53:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jam_kerja`
--

CREATE TABLE `jam_kerja` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_profile_id` bigint(20) UNSIGNED NOT NULL,
  `no_wbs` varchar(50) DEFAULT NULL,
  `kode_proyek` varchar(50) DEFAULT NULL,
  `proyek_id` bigint(20) UNSIGNED DEFAULT NULL,
  `Aktivitas_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jumlah_jam` decimal(5,2) NOT NULL DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `status_id` bigint(20) UNSIGNED DEFAULT NULL,
  `mode_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jam_kerja`
--

INSERT INTO `jam_kerja` (`id`, `user_profile_id`, `no_wbs`, `kode_proyek`, `proyek_id`, `Aktivitas_id`, `tanggal`, `jumlah_jam`, `keterangan`, `status_id`, `mode_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 2, NULL, NULL, 1, 1, '2025-06-07', 6.00, 'qq', 3, 2, '2025-08-17 20:56:17', '2025-08-18 02:01:02', NULL),
(6, 2, NULL, NULL, 1, 3, '2025-08-01', 34.00, 'jhgjhgjnytj', 2, 3, '2025-08-18 01:59:07', '2025-08-18 01:59:22', NULL),
(10, 5, NULL, NULL, 3, 3, '2025-08-02', 3.00, 'bonsai kelapa', 3, 2, '2025-08-18 18:59:01', '2025-08-21 00:22:01', NULL),
(11, 5, NULL, NULL, 1, 1, '2025-08-13', 3.50, 'saya sudah mengerjakannya yeyyyy', 1, 2, '2025-08-18 19:13:15', '2025-08-18 20:47:43', NULL),
(12, 5, NULL, NULL, 3, 3, '2025-08-14', 3.00, 'rrrrrrrawr', 1, 2, '2025-08-18 19:30:45', '2025-08-18 20:43:25', NULL),
(13, 6, NULL, NULL, 3, 1, '2025-08-19', 8.00, 'Telah selesai angkut 8 ton', 1, 2, '2025-08-18 20:53:23', '2025-08-20 21:44:43', NULL),
(14, 7, NULL, NULL, 5, 1, '2025-08-20', 7.00, 'gelap banget', 1, 2, '2025-08-19 19:25:43', '2025-08-20 02:15:00', NULL),
(15, 7, NULL, NULL, 1, 1, '2025-08-20', 2.00, NULL, 3, 2, '2025-08-20 02:14:46', '2025-08-20 02:14:46', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jam_per_tanggal`
--

CREATE TABLE `jam_per_tanggal` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_profile_id` bigint(20) UNSIGNED NOT NULL,
  `proyek_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_pesan`
--

CREATE TABLE `jenis_pesan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jenis_pesan`
--

INSERT INTO `jenis_pesan` (`id`, `nama`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'dari siswa', '2025-08-11 20:29:39', '2025-08-11 20:29:39', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `keterangan`
--

CREATE TABLE `keterangan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bagian_id` bigint(20) UNSIGNED DEFAULT NULL,
  `proyek_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `keterangan`
--

INSERT INTO `keterangan` (`id`, `bagian_id`, `proyek_id`, `tanggal`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2025-08-08', '2025-08-13 23:40:42', '2025-08-19 02:00:34', NULL),
(2, 1, 1, '2025-08-08', '2025-08-13 23:40:43', '2025-08-19 02:00:37', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `lembur`
--

CREATE TABLE `lembur` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_profile_id` bigint(20) UNSIGNED NOT NULL,
  `proyek_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `lembur`
--

INSERT INTO `lembur` (`id`, `user_profile_id`, `proyek_id`, `tanggal`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 6, 4, '2025-08-21', '2025-08-19 19:55:09', '2025-08-19 19:55:09', NULL),
(3, 6, 3, '2025-08-13', '2025-08-19 19:55:23', '2025-08-19 19:55:23', NULL),
(4, 7, 4, '2025-08-20', '2025-08-19 22:57:34', '2025-08-19 22:57:34', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `levels`
--

CREATE TABLE `levels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `levels`
--

INSERT INTO `levels` (`id`, `nama`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'Admin', '2025-08-12 20:34:55', '2025-08-17 18:58:09', NULL),
(6, 'User', '2025-08-19 00:01:56', '2025-08-19 00:01:56', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, 'create_status_table', 2),
(6, 'create_level_table', 3),
(7, 'create_bagian_table', 4),
(8, '2025_08_11_090842_create_proyek_table', 5),
(9, '2025_08_12_021350_create_lembur_table', 6),
(10, '2025_08_12_013206_create_keterangan_table', 7),
(11, '2025_08_11_080552_create_user_profile_table', 8),
(12, '2025_08_12_025303_create_proyek_user_table', 9),
(13, '2025_08_12_030435_create_jam_per_tanggal_table', 10),
(14, '2025_08_12_032154_create_jenis_pesan_table', 11),
(16, '2025_08_12_040933_create_aktivitas_table', 13),
(17, '2025_08_12_042157_create_mode_jam_kerja_table', 14),
(18, '2025_08_12_043133_create_status_jam_kerja_table', 15),
(19, '2025_08_12_043925_create_jam_kerja_table', 16),
(20, '2025_08_13_000000_update_aktivitas_table', 17),
(22, '2025_08_19_075733_add_level_id_to_users_table', 18),
(23, '2025_08_12_033108_create_pesan_table', 19);

-- --------------------------------------------------------

--
-- Struktur dari tabel `mode_jam_kerja`
--

CREATE TABLE `mode_jam_kerja` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `mode_jam_kerja`
--

INSERT INTO `mode_jam_kerja` (`id`, `nama`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'mode serius', '2025-08-11 21:30:24', '2025-08-14 20:19:30', NULL),
(3, 'mode off', '2025-08-11 21:30:41', '2025-08-11 21:30:41', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesan`
--

CREATE TABLE `pesan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengirim_id` bigint(20) UNSIGNED DEFAULT NULL,
  `penerima_id` bigint(20) UNSIGNED DEFAULT NULL,
  `isi` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `tgl_pesan` datetime DEFAULT NULL,
  `jenis_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pesan`
--

INSERT INTO `pesan` (`id`, `pengirim_id`, `penerima_id`, `isi`, `parent_id`, `tgl_pesan`, `jenis_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 6, 6, 'haloo', NULL, '2025-08-20 07:01:53', 1, '2025-08-20 00:01:54', '2025-08-20 00:01:54', NULL),
(3, 6, 6, 'haloo', NULL, '2025-08-20 07:02:15', 1, '2025-08-20 00:02:15', '2025-08-20 00:02:15', NULL),
(4, 6, 5, 'haloww', NULL, '2025-08-20 07:10:27', 1, '2025-08-20 00:10:27', '2025-08-20 00:10:27', NULL),
(5, 6, 5, 'p', NULL, '2025-08-20 07:10:51', 1, '2025-08-20 00:10:52', '2025-08-20 00:10:52', NULL),
(6, 6, 5, 'hai', NULL, '2025-08-20 07:12:58', 1, '2025-08-20 00:12:59', '2025-08-20 20:02:12', '2025-08-20 20:02:12'),
(7, 5, 7, 'halo bahlil, sini dulu', NULL, '2025-08-20 08:08:24', 1, '2025-08-20 01:08:24', '2025-08-20 01:08:24', NULL),
(8, 5, 6, 'haloo', NULL, '2025-08-20 08:20:17', 1, '2025-08-20 01:20:17', '2025-08-20 01:20:17', NULL),
(9, 5, 6, 'woow', NULL, '2025-08-20 09:04:42', 1, '2025-08-20 02:04:42', '2025-08-20 02:04:42', NULL),
(10, 6, 6, 'lop u galangg', NULL, '2025-08-20 09:05:22', 1, '2025-08-20 02:05:22', '2025-08-20 02:05:22', NULL),
(11, 7, 5, 'woi', NULL, '2025-08-20 09:18:10', 1, '2025-08-20 02:18:10', '2025-08-20 02:18:10', NULL),
(12, 5, 6, 'p', NULL, '2025-08-21 03:01:29', 1, '2025-08-20 20:01:30', '2025-08-20 20:01:30', NULL),
(13, 6, 8, 'jangan ganggu Galang', NULL, '2025-08-21 03:51:17', 1, '2025-08-20 20:51:18', '2025-08-20 20:51:18', NULL),
(14, 5, 7, 'p', NULL, '2025-08-21 04:35:21', 1, '2025-08-20 21:35:21', '2025-08-20 21:35:21', NULL),
(15, 7, 5, 'p', NULL, '2025-08-21 04:38:14', 1, '2025-08-20 21:38:15', '2025-08-20 21:38:15', NULL),
(16, 6, 5, 'halooo', NULL, '2025-08-21 05:35:34', 1, '2025-08-20 22:35:34', '2025-08-20 22:35:34', NULL),
(17, 5, 6, 'iya kenapa', NULL, '2025-08-21 05:35:58', 1, '2025-08-20 22:35:58', '2025-08-20 22:35:58', NULL),
(18, 5, 6, 'p', NULL, '2025-08-21 05:51:30', 1, '2025-08-20 22:51:30', '2025-08-20 22:51:30', NULL),
(19, 6, 5, 'galanggh', NULL, '2025-08-21 05:55:30', 1, '2025-08-20 22:55:30', '2025-08-20 22:55:30', NULL),
(20, 5, 6, 'iya', NULL, '2025-08-21 05:56:09', 1, '2025-08-20 22:56:10', '2025-08-20 22:56:10', NULL),
(21, 5, 6, 'hai', NULL, '2025-08-21 05:57:10', 1, '2025-08-20 22:57:11', '2025-08-20 22:57:11', NULL),
(22, 5, 6, 'p', NULL, '2025-08-21 06:00:27', 1, '2025-08-20 23:00:27', '2025-08-20 23:00:27', NULL),
(23, 5, 6, 'p', NULL, '2025-08-21 06:05:30', 1, '2025-08-20 23:05:30', '2025-08-20 23:05:30', NULL),
(24, 5, 9, 'p', NULL, '2025-08-21 06:11:51', 1, '2025-08-20 23:11:51', '2025-08-20 23:11:51', NULL),
(25, 5, 12, 'p', NULL, '2025-08-21 06:12:06', 1, '2025-08-20 23:12:06', '2025-08-20 23:12:06', NULL),
(26, 5, 7, 'iya', NULL, '2025-08-21 06:12:30', 1, '2025-08-20 23:12:31', '2025-08-20 23:12:31', NULL),
(27, 13, 5, 'permisi pak selamat siang, saya baru masuk pak, jadi saya harus apa dulu', NULL, '2025-08-21 06:23:06', 1, '2025-08-20 23:23:06', '2025-08-20 23:23:06', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `proyek`
--

CREATE TABLE `proyek` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(50) DEFAULT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `nama_sekolah` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `proyek`
--

INSERT INTO `proyek` (`id`, `kode`, `nama`, `tanggal`, `nama_sekolah`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '123', 'Kartel', '2025-08-12', 'SMKN 7 Batam', '2025-08-20 02:15:38', '2025-08-18 23:37:40', NULL),
(3, '444', 'Bandar', '2025-08-20', 'SMKN 7 Batam', '2025-08-20 02:17:18', '2025-08-20 01:25:18', NULL),
(4, '0087', 'Pengihitaman', '2025-08-20', 'SMKN 7 Batam', '2025-08-19 19:14:03', '2025-08-19 19:14:03', NULL),
(5, '00873', 'Energi gelap', '2025-08-20', 'SMKN 7 Batam', '2025-08-19 19:15:20', '2025-08-19 19:15:20', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `proyek_user`
--

CREATE TABLE `proyek_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proyek_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_profile_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `statuses`
--

CREATE TABLE `statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `statuses`
--

INSERT INTO `statuses` (`id`, `nama`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Selesai', '2025-08-11 00:30:28', '2025-08-12 21:33:59', NULL),
(2, 'Belum', '2025-08-12 21:18:53', '2025-08-12 21:28:06', '2025-08-12 21:28:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `status_jam_kerja`
--

CREATE TABLE `status_jam_kerja` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `status_jam_kerja`
--

INSERT INTO `status_jam_kerja` (`id`, `nama`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Selesai', '2025-08-11 21:38:30', '2025-08-18 19:41:48', NULL),
(2, 'Belum Selesai', '2025-08-11 21:38:37', '2025-08-18 19:41:49', NULL),
(3, 'Pending', '2025-08-14 20:34:20', '2025-08-14 20:34:20', NULL),
(4, 'Disetujui', '2025-08-14 20:34:26', '2025-08-14 20:34:26', NULL),
(5, 'Ditolak', '2025-08-14 20:34:32', '2025-08-14 20:34:32', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL,
  `level_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`, `level_id`) VALUES
(2, 'Galang', 'galang88@gmail.com', NULL, '$2y$10$JGMHjoxwVFc7GXc.A5Zng.6u2dFS2OgJbRgR0wCpkU81JZJqaAFym', NULL, '2025-08-12 02:15:35', '2025-08-12 02:15:35', NULL, 3),
(3, 'Udin', 'udin12@gmail.com', NULL, '88888888', NULL, '2025-08-12 23:42:51', '2025-08-12 23:42:51', NULL, NULL),
(4, 'burhan', 'burhan7@gmail.com', NULL, '88888888', NULL, '2025-08-12 23:47:43', '2025-08-12 23:47:43', NULL, NULL),
(5, 'budi', 'budiono@gmail.com', NULL, '88888888', NULL, '2025-08-14 21:10:47', '2025-08-14 21:10:47', NULL, NULL),
(6, 'budiono siregar', 'siregar@gmail.com', NULL, '12345678', NULL, '2025-08-14 21:13:45', '2025-08-14 21:13:45', NULL, NULL),
(7, 'Luhut', 'luhut@gmil.com', NULL, '99999999', NULL, '2025-08-14 21:16:40', '2025-08-14 21:16:40', NULL, NULL),
(11, 'HuTao', 'hutao@gmail.com', NULL, '$2y$10$Wns3FLaAu35mAle0j2HN8eIyR2/k6u.nB.CjchpPghHjmzX6Wri8.', NULL, '2025-08-14 21:56:00', '2025-08-19 18:45:15', NULL, 3),
(12, 'bahlil', 'bahlil@gmail.com', NULL, '$2y$10$Rupba549/WJtE4OxPPMIUutyfoKJzSV5C5bnFCaR5T8aeRBQ5SNre', NULL, '2025-08-15 00:27:31', '2025-08-15 00:27:31', NULL, NULL),
(13, 'bahlil x asep', 'bahlasep@gmail.com', NULL, '$2y$10$r.cC9vRAIiY5AroKe4JOGOgZmGIt2A.6Tn72hWxO/ZTAtumQL89py', NULL, '2025-08-15 00:28:43', '2025-08-15 00:28:43', NULL, 6),
(14, 'Rusdi', 'Rusdi@gmail.com', NULL, '$2y$10$Vza/vPBBbgWr5CI9kRlwDeG0P6l0cHatkkddo4FcZyy3JoG1cNqfS', NULL, '2025-08-15 02:32:28', '2025-08-15 02:32:28', NULL, NULL),
(16, 'Jamal', 'jamal@gmail.com', NULL, '$2y$10$wxJQV4CuR.EYgWwKGzsKyeRkX6Ya3/5CEIsgOoL479ouRH/HDXsAq', NULL, '2025-08-17 18:55:28', '2025-08-17 18:55:28', NULL, NULL),
(17, 'hhhh', 'hh@gmail.com', NULL, '$2y$10$M9AJrFuyRYvlz3FUP/Nn7epUR2Lf8vvIRXzUsCEdK5GZhFQSJTY1m', NULL, '2025-08-19 01:58:19', '2025-08-19 01:58:19', NULL, 3),
(19, 'Furina', 'furina@gmail.com', NULL, '$2y$10$9jMspFyF7KuN5A3dAuonjOJlhSh3K0NgkQAB.BoxXBhwwon5sPPfW', NULL, '2025-08-19 02:02:40', '2025-08-20 19:32:38', '2025-08-20 19:32:38.000000', 6),
(20, 'lutpi', 'lutpi@gmail.com', NULL, '$2y$10$pj.CPBoBlBGg4cE.9U8ot.YqKd2KIOrkRlhcjX3xVedwxIjiEeM/K', NULL, '2025-08-19 18:44:28', '2025-08-19 18:44:28', NULL, 3),
(21, 'Luhut', 'luhut@gmail.com', NULL, '$2y$10$qU2rHTmmKbwnmJAg7jXJJORQxCl3K0tsNyzn4roMP4s.rc.tqt4I.', NULL, '2025-08-20 23:21:19', '2025-08-20 23:21:19', NULL, 6),
(22, 'sambo', 'sambo@gmail.com', NULL, '$2y$10$esq3rl3VZYHw3V2KG8Gb6Oh7mUXjWm6X0hh2AoFf6TQpULHDweGZm', NULL, '2025-08-21 00:19:09', '2025-08-21 00:19:09', NULL, 6);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_profile`
--

CREATE TABLE `user_profile` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nrp` varchar(20) DEFAULT NULL,
  `alamat` varchar(225) DEFAULT NULL,
  `foto` varchar(250) DEFAULT NULL,
  `level_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bagian_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `user_profile`
--

INSERT INTO `user_profile` (`id`, `user_id`, `nama_lengkap`, `nrp`, `alamat`, `foto`, `level_id`, `status_id`, `bagian_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 4, 'Burhan alJAMAL', '12121', 'sss', '/storage/foto/TjoQnuIbstV8Yqi4qzfm3KCgWbFjWVIL8N7XIzsK.jpg', NULL, 1, 1, '2025-08-13 02:24:35', '2025-08-13 20:45:50', NULL),
(3, 3, 'Burhan al', '45345', 'ggg', '/storage/foto/EyWxMI6f9YLMSqPAz3OfgRGZt9mEzkbO2W7X7EMz.jpg', 3, 1, 1, '2025-08-13 02:31:01', '2025-08-13 20:46:01', NULL),
(5, 2, 'Galang', '78789', 'Batam', '/storage/foto/hpeoIl4E7hkGYEjA0ggfeo8WDYojJ7QkxNWaKowO.jpg', NULL, 1, 1, '2025-08-13 20:42:34', '2025-08-20 19:30:26', NULL),
(6, 11, 'HuTao', '0980', 'Batam (Satu Rumah)', '/storage/foto/9Zv9YQanjZA6EFcuYeR69Lwww93qTcLEHXKH0XvN.jpg', 3, 1, 1, '2025-08-14 21:56:01', '2025-08-14 21:57:10', NULL),
(7, 13, 'bahlil x asep', '2323213', 'Ngawi', '/storage/foto/mem7pNT6U1GSvEiPC1zQqk1Cd6lsWN6Fyw8E20G6.png', NULL, 1, 1, '2025-08-15 00:28:43', '2025-08-15 00:34:04', NULL),
(8, 14, 'Rusdi', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-15 02:32:29', '2025-08-15 02:32:29', NULL),
(9, 16, 'Jamal', '7567567', 'batam', '/storage/foto/pT0ZKklqEJeyRHRxOC8cRfe3Lwne1brxSZwuzCpE.jpg', NULL, 1, 1, '2025-08-17 18:55:28', '2025-08-17 18:58:52', NULL),
(10, 17, 'hhhh', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-19 01:58:20', '2025-08-19 01:58:20', NULL),
(11, 19, 'Furina', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-19 02:02:41', '2025-08-19 02:02:41', NULL),
(12, 20, 'lutpi', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-19 18:44:29', '2025-08-19 18:44:29', NULL),
(13, 21, 'Luhut', '6777', 'batam', '/storage/foto/wih8PFnzfDkG9glWIFuqS1vvHmUR97fP9SqUCAX7.jpg', NULL, NULL, NULL, '2025-08-20 23:21:20', '2025-08-20 23:22:10', NULL),
(14, 22, 'sambo', NULL, NULL, NULL, 6, NULL, NULL, '2025-08-21 00:19:10', '2025-08-21 00:19:10', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aktivitas_bagian_id_foreign` (`bagian_id`),
  ADD KEY `aktivitas_user_profile_id_foreign` (`user_profile_id`),
  ADD KEY `aktivitas_proyek_id_foreign` (`proyek_id`),
  ADD KEY `aktivitas_keterangan_id_foreign` (`keterangan_id`);

--
-- Indeks untuk tabel `bagian`
--
ALTER TABLE `bagian`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jam_kerja`
--
ALTER TABLE `jam_kerja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jam_kerja_user_profile_id_foreign` (`user_profile_id`),
  ADD KEY `jam_kerja_proyek_id_foreign` (`proyek_id`),
  ADD KEY `jam_kerja_aktivitas_id_foreign` (`Aktivitas_id`),
  ADD KEY `jam_kerja_status_id_foreign` (`status_id`),
  ADD KEY `jam_kerja_mode_id_foreign` (`mode_id`);

--
-- Indeks untuk tabel `jam_per_tanggal`
--
ALTER TABLE `jam_per_tanggal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jam_per_tanggal_user_profile_id_foreign` (`user_profile_id`),
  ADD KEY `jam_per_tanggal_proyek_id_foreign` (`proyek_id`);

--
-- Indeks untuk tabel `jenis_pesan`
--
ALTER TABLE `jenis_pesan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `keterangan`
--
ALTER TABLE `keterangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `keterangan_bagian_id_foreign` (`bagian_id`),
  ADD KEY `keterangan_proyek_id_foreign` (`proyek_id`);

--
-- Indeks untuk tabel `lembur`
--
ALTER TABLE `lembur`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lembur_user_profile_id_foreign` (`user_profile_id`),
  ADD KEY `lembur_proyek_id_foreign` (`proyek_id`);

--
-- Indeks untuk tabel `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `levels_nama_unique` (`nama`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `mode_jam_kerja`
--
ALTER TABLE `mode_jam_kerja`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `pesan`
--
ALTER TABLE `pesan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesan_pengirim_id_foreign` (`pengirim_id`),
  ADD KEY `pesan_penerima_id_foreign` (`penerima_id`),
  ADD KEY `pesan_jenis_id_foreign` (`jenis_id`);

--
-- Indeks untuk tabel `proyek`
--
ALTER TABLE `proyek`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `proyek_user`
--
ALTER TABLE `proyek_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proyek_user_proyek_id_foreign` (`proyek_id`),
  ADD KEY `proyek_user_user_profile_id_foreign` (`user_profile_id`);

--
-- Indeks untuk tabel `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `statuses_nama_unique` (`nama`);

--
-- Indeks untuk tabel `status_jam_kerja`
--
ALTER TABLE `status_jam_kerja`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_level_id_foreign` (`level_id`);

--
-- Indeks untuk tabel `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_profile_user_id_foreign` (`user_id`),
  ADD KEY `user_profile_level_id_foreign` (`level_id`),
  ADD KEY `user_profile_status_id_foreign` (`status_id`),
  ADD KEY `user_profile_bagian_id_foreign` (`bagian_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `aktivitas`
--
ALTER TABLE `aktivitas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `bagian`
--
ALTER TABLE `bagian`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jam_kerja`
--
ALTER TABLE `jam_kerja`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `jam_per_tanggal`
--
ALTER TABLE `jam_per_tanggal`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `jenis_pesan`
--
ALTER TABLE `jenis_pesan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `keterangan`
--
ALTER TABLE `keterangan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `lembur`
--
ALTER TABLE `lembur`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `levels`
--
ALTER TABLE `levels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `mode_jam_kerja`
--
ALTER TABLE `mode_jam_kerja`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pesan`
--
ALTER TABLE `pesan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `proyek`
--
ALTER TABLE `proyek`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `proyek_user`
--
ALTER TABLE `proyek_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `status_jam_kerja`
--
ALTER TABLE `status_jam_kerja`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD CONSTRAINT `aktivitas_bagian_id_foreign` FOREIGN KEY (`bagian_id`) REFERENCES `bagian` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aktivitas_keterangan_id_foreign` FOREIGN KEY (`keterangan_id`) REFERENCES `keterangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aktivitas_proyek_id_foreign` FOREIGN KEY (`proyek_id`) REFERENCES `proyek` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aktivitas_user_profile_id_foreign` FOREIGN KEY (`user_profile_id`) REFERENCES `user_profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jam_kerja`
--
ALTER TABLE `jam_kerja`
  ADD CONSTRAINT `jam_kerja_aktivitas_id_foreign` FOREIGN KEY (`Aktivitas_id`) REFERENCES `aktivitas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jam_kerja_mode_id_foreign` FOREIGN KEY (`mode_id`) REFERENCES `mode_jam_kerja` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jam_kerja_proyek_id_foreign` FOREIGN KEY (`proyek_id`) REFERENCES `proyek` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jam_kerja_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `status_jam_kerja` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jam_kerja_user_profile_id_foreign` FOREIGN KEY (`user_profile_id`) REFERENCES `user_profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jam_per_tanggal`
--
ALTER TABLE `jam_per_tanggal`
  ADD CONSTRAINT `jam_per_tanggal_proyek_id_foreign` FOREIGN KEY (`proyek_id`) REFERENCES `proyek` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jam_per_tanggal_user_profile_id_foreign` FOREIGN KEY (`user_profile_id`) REFERENCES `user_profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `keterangan`
--
ALTER TABLE `keterangan`
  ADD CONSTRAINT `keterangan_bagian_id_foreign` FOREIGN KEY (`bagian_id`) REFERENCES `bagian` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `keterangan_proyek_id_foreign` FOREIGN KEY (`proyek_id`) REFERENCES `proyek` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `lembur`
--
ALTER TABLE `lembur`
  ADD CONSTRAINT `lembur_proyek_id_foreign` FOREIGN KEY (`proyek_id`) REFERENCES `proyek` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `lembur_user_profile_id_foreign` FOREIGN KEY (`user_profile_id`) REFERENCES `user_profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pesan`
--
ALTER TABLE `pesan`
  ADD CONSTRAINT `pesan_jenis_id_foreign` FOREIGN KEY (`jenis_id`) REFERENCES `jenis_pesan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pesan_penerima_id_foreign` FOREIGN KEY (`penerima_id`) REFERENCES `user_profile` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pesan_pengirim_id_foreign` FOREIGN KEY (`pengirim_id`) REFERENCES `user_profile` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `proyek_user`
--
ALTER TABLE `proyek_user`
  ADD CONSTRAINT `proyek_user_proyek_id_foreign` FOREIGN KEY (`proyek_id`) REFERENCES `proyek` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `proyek_user_user_profile_id_foreign` FOREIGN KEY (`user_profile_id`) REFERENCES `user_profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_level_id_foreign` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `user_profile_bagian_id_foreign` FOREIGN KEY (`bagian_id`) REFERENCES `bagian` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_profile_level_id_foreign` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_profile_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_profile_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
