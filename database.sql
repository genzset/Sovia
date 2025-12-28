-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 28 Des 2025 pada 12.08
-- Versi server: 8.4.3
-- Versi PHP: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `database`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `status_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `feedback`
--

CREATE TABLE `feedback` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `message`, `created_at`, `status`) VALUES
(1, 2, 'bagus banget webnya minn', '2025-12-18 15:35:40', 'read');

-- --------------------------------------------------------

--
-- Struktur dari tabel `followers`
--

CREATE TABLE `followers` (
  `id` int NOT NULL,
  `follower_id` int NOT NULL,
  `following_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `statuses`
--

CREATE TABLE `statuses` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `tribe_id` int DEFAULT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `likes` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `statuses`
--

INSERT INTO `statuses` (`id`, `user_id`, `tribe_id`, `content`, `image`, `likes`, `created_at`) VALUES
(3, 2, NULL, 'Mclaren lu warna apa boss??', 'mclaren tr.jpg', 0, '2025-12-16 02:17:10'),
(4, 2, NULL, 'Zoom out, supermegacycle nya belum mulai.', 'gbtctimothy.jpeg', 0, '2025-12-16 02:19:49'),
(5, 3, NULL, 'Iziinnn', 'nicegang.jpg', 0, '2025-12-16 02:22:24'),
(6, 4, NULL, 'Dakwaan kasus Haji Halim disebut berubah dan melompati alur KUHAP. Dari urusan administrasi ditarik ke pidana, padahal lahan dikuasai dan dilegalkan lebih 20 tahun tanpa sanggahan negara. Jika begitu, yang patut dievaluasi siapa sebenarnya lalai mengawasi.\r\n', 'G8M3rE7aoAAWdxm.jpg', 1, '2025-12-16 02:24:50'),
(7, 5, NULL, 'Saya melakukan kunjungan kenegaraan ke Moskow untuk bertemu dengan Presiden Federasi Rusia, Yang Mulia Vladimir Putin.\r\n', 'G72uM3UaAAAOqNi.jpg', 0, '2025-12-16 02:30:46'),
(8, 5, 1, 'GM', 'gm pb.jpeg', 2, '2025-12-16 02:35:37'),
(9, 6, NULL, 'GM!', 'G8QZPpxa4AEJBHA.jpg', 0, '2025-12-16 02:42:26'),
(10, 6, NULL, 'Aliran dana masuk ke ETF Bitcoin terlihat melemah dalam beberapa minggu terakhir. Hal ini menahan kenaikan harga setelah Bitcoin sempat jatuh dari rekor tertinggi sekitar $126.000 pada Oktober ke level mendekati $80.000 bulan lalu.\r\n\r\nSaat ini, Bitcoin kemungkinan besar akan terus berada dalam fase konsolidasi dengan rentang yang cukup lebar antara $80.000 dan $100.000. Apakah kita sudah masuk ke bear market?', NULL, 1, '2025-12-16 02:43:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tribes`
--

CREATE TABLE `tribes` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT 'default_tribe.png',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `tribes`
--

INSERT INTO `tribes` (`id`, `name`, `image`, `created_by`, `created_at`) VALUES
(1, 'INDONESIA', 'bendera indonesia.png', 5, '2025-12-16 02:31:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tribe_members`
--

CREATE TABLE `tribe_members` (
  `id` int NOT NULL,
  `tribe_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `joined_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `tribe_members`
--

INSERT INTO `tribe_members` (`id`, `tribe_id`, `user_id`, `joined_at`) VALUES
(1, 1, 5, '2025-12-16 02:31:51'),
(2, 1, 6, '2025-12-16 02:46:17'),
(3, 1, 7, '2025-12-16 03:01:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user',
  `avatar` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `avatar`) VALUES
(2, 'Timothy Ronaldd', '$2y$10$Z0BHwOxlSbXKjb1iL6hWledvDz3UWv7WF0AgQGgPIo/TGttcpfgWK', 'user', '1765851395_1761472727_Ac.jpg'),
(3, 'niceguymo', '$2y$10$ePWrNLymkF4rHAy2XJQCFeRawvAMrTAFe2AJN8vCI2V8iV81Hh51e', 'user', '1765851628_nicegang.jpg'),
(4, 'Ferry Irwandi', '$2y$10$xUqshqwkjjmX8ave9lEwy.pXO2IEVEfmCFFNGT0ySPVTtMR.JMhnu', 'user', '1765851794_1765242354_ferryirwandi.jpg'),
(5, 'Prabowo Subianto', '$2y$10$2VJdLUNxcXweVRy153LHKeMyiXu.CXUgKd6ewX7dU3D64375dK3P2', 'user', '1765852165_1761491707_prbw.jpeg'),
(6, 'Coinvestasi', '$2y$10$Zy1TJkVxEIwZ2wASrZgJjOWX2So02EVy0aboCR8JwFmecPNJ231a.', 'user', '1765852885_anfqzVul_400x400.jpg'),
(7, 'Chillguy', '$2y$10$Ixd9czaG.X4Gazxn8cjgVOcKAzCDHaZaUwGknuNvxatggl.idjWFK', 'user', '1765854025_chillguy.jpg'),
(8, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'default.png'),
(9, 'user', '$2y$10$acpOyriAGSwjrXNwZOIMZeYKvEMsV7pPZb/kFOr9ZYT4CkqMU.O2i', 'user', '1766923609_images.jpg');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `follower_id` (`follower_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Indeks untuk tabel `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tribe_id` (`tribe_id`);

--
-- Indeks untuk tabel `tribes`
--
ALTER TABLE `tribes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `tribe_members`
--
ALTER TABLE `tribe_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tribe_id` (`tribe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `tribes`
--
ALTER TABLE `tribes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tribe_members`
--
ALTER TABLE `tribe_members`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `statuses`
--
ALTER TABLE `statuses`
  ADD CONSTRAINT `statuses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `statuses_ibfk_2` FOREIGN KEY (`tribe_id`) REFERENCES `tribes` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tribes`
--
ALTER TABLE `tribes`
  ADD CONSTRAINT `tribes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tribe_members`
--
ALTER TABLE `tribe_members`
  ADD CONSTRAINT `tribe_members_ibfk_1` FOREIGN KEY (`tribe_id`) REFERENCES `tribes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tribe_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
