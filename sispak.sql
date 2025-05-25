-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 25, 2025 at 06:11 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sispak`
--

-- --------------------------------------------------------

--
-- Table structure for table `aturan`
--

CREATE TABLE `aturan` (
  `id` int NOT NULL,
  `penyakit_id` int NOT NULL,
  `gejala_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `aturan`
--

INSERT INTO `aturan` (`id`, `penyakit_id`, `gejala_id`) VALUES
(1, 1, 2),
(2, 1, 3),
(3, 1, 11),
(4, 1, 12),
(5, 1, 13),
(6, 1, 14),
(7, 2, 2),
(8, 2, 9),
(9, 2, 12),
(10, 2, 15),
(11, 2, 19),
(12, 3, 1),
(13, 3, 3),
(14, 3, 4),
(15, 3, 5),
(16, 3, 6),
(17, 3, 7),
(18, 3, 9),
(19, 3, 10),
(20, 3, 16),
(21, 3, 17),
(22, 3, 18),
(23, 3, 20),
(24, 3, 22),
(25, 4, 2),
(26, 4, 3),
(27, 4, 4),
(28, 4, 5),
(29, 4, 6),
(30, 4, 11),
(31, 4, 13),
(32, 4, 22),
(33, 5, 1),
(34, 5, 3),
(35, 5, 4),
(36, 5, 7),
(37, 5, 8),
(38, 5, 9),
(39, 5, 11),
(40, 5, 13),
(41, 5, 16),
(42, 5, 18),
(43, 5, 20),
(44, 5, 21),
(45, 5, 22),
(46, 6, 1),
(47, 6, 3),
(48, 6, 4),
(49, 6, 6),
(50, 6, 7),
(51, 6, 8),
(52, 6, 9),
(53, 6, 10),
(54, 6, 16),
(55, 6, 19),
(56, 6, 20),
(57, 6, 21),
(58, 6, 22),
(59, 6, 23),
(60, 7, 3),
(61, 7, 10),
(62, 7, 18),
(63, 7, 23),
(64, 7, 24);

-- --------------------------------------------------------

--
-- Table structure for table `gejala`
--

CREATE TABLE `gejala` (
  `id` int NOT NULL,
  `kode_gejala` varchar(10) NOT NULL,
  `nama_gejala` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gejala`
--

INSERT INTO `gejala` (`id`, `kode_gejala`, `nama_gejala`) VALUES
(1, 'G01', 'Gusi bengkak'),
(2, 'G02', 'Gigi ngilu'),
(3, 'G03', 'Bau mulut tak sedap'),
(4, 'G04', 'Gusi sakit saat disentuh'),
(5, 'G05', 'Gigi sakit saat makan/minum yang panas atau dingin'),
(6, 'G06', 'Gusi berdarah'),
(7, 'G07', 'Gusi nyeri'),
(8, 'G08', 'Gusi bernanah'),
(9, 'G09', 'Gigi goyang'),
(10, 'G10', 'Penumpukan plak/banyak karang gigi'),
(11, 'G11', 'Gigi berlubang'),
(12, 'G12', 'Gigi patah'),
(13, 'G13', 'Gigi sakit terus menerus'),
(14, 'G14', 'Terlihat noda hitam, coklat atau putih pada permukaan'),
(15, 'G15', 'Gigi tampak kuning'),
(16, 'G16', 'Radang gusi'),
(17, 'G17', 'Gusi mengkilap'),
(18, 'G18', 'Mulut tak sedap/pahit'),
(19, 'G19', 'Gigi renggang'),
(20, 'G20', 'Gusi memerah/keunguan'),
(21, 'G21', 'Gusi yang terdorong maju membuat gigi terlihat panjang/tonggos'),
(22, 'G22', 'Nyeri saat mengunyah'),
(23, 'G23', 'Mulut menjadi kering'),
(24, 'G24', 'Adanya lapisan pada lidah');

-- --------------------------------------------------------

--
-- Table structure for table `penyakit`
--

CREATE TABLE `penyakit` (
  `id` int NOT NULL,
  `kode_penyakit` varchar(10) NOT NULL,
  `nama_penyakit` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `solusi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penyakit`
--

INSERT INTO `penyakit` (`id`, `kode_penyakit`, `nama_penyakit`, `deskripsi`, `solusi`) VALUES
(1, 'P01', 'Karies gigi', 'Karies atau gigi berlubang adalah demineralisasi dari bagian anorganik gigi dengan pelarutan substansi organik yang dikarenakan oleh penyebab multifaktorial.', 'Perawatan gigi berlubang, penambalan gigi, atau perawatan saluran akar jika diperlukan.'),
(2, 'P02', 'Erosi Gigi', 'Erosi gigi adalah kehilangan jaringan gigi secara progresif dan irreversible yang disebabkan karena kimiawi dari asam secara intrinsik maupun ekstrinsik.', 'Mengurangi konsumsi makanan/minuman asam, menggunakan pasta gigi khusus, dan perawatan gigi sensitif.'),
(3, 'P03', 'Gingivitis', 'Gingivitis atau peradangan gusi adalah inflamasi pada mukosa skuamosa atau gingiva atau jaringan lunak sekitar gigi.', 'Pembersihan karang gigi, perbaikan kebersihan mulut, dan penggunaan obat kumur antiseptik.'),
(4, 'P04', 'Pulpitis', 'Pulpitis adalah peradangan pada pulpa gigi yang menimbulkan rasa nyeri. Pulpa adalah bagian gigi yang paling dalam, yang mengandung saraf dan pembuluh darah.', 'Perawatan saluran akar atau pencabutan gigi jika sudah parah.'),
(5, 'P05', 'Abses Gigi', 'Abses adalah rongga patologis yang berisi nanah yang disebabkan oleh infeksi bakteri. Ini merupakan infeksi akut purulen yang berkembang pada bagian apikal gigi.', 'Pemberian antibiotik, drainase abses, dan perawatan saluran akar atau pencabutan gigi.'),
(6, 'P06', 'Periodontitis', 'Periodontitis adalah suatu proses inflamasi yang mempengaruhi struktur penyangga gigi (ligament periodontal), tulang alveolar dan sementum.', 'Pembersihan karang gigi dalam, perawatan periodontal, dan dalam kasus parah mungkin perlu operasi.'),
(7, 'P07', 'Halitosis', 'Halitosis atau bau mulut adalah kondisi yang ditandai dengan aroma napas mulut yang tidak sedap.', 'Perbaikan kebersihan mulut, pembersihan lidah, perawatan gigi berlubang, dan penggunaan obat kumur.');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat`
--

CREATE TABLE `riwayat` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `penyakit_id` int NOT NULL,
  `tanggal` date NOT NULL,
  `gejala` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `riwayat`
--

INSERT INTO `riwayat` (`id`, `user_id`, `penyakit_id`, `tanggal`, `gejala`) VALUES
(1, 2, 1, '2025-05-11', 'G01,G02'),
(2, 2, 1, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(3, 2, 1, '2025-05-11', 'G01,G02,G03,G04,G05,G06,G07,G08,G09,G10,G11,G12,G13,G14,G15,G16,G17,G18,G19,G20,G21,G22,G23,G24'),
(4, 2, 1, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(5, 2, 1, '2025-05-11', 'G01'),
(6, 2, 2, '2025-05-11', 'G01'),
(7, 2, 2, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(8, 2, 1, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(9, 2, 1, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(10, 2, 2, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(11, 2, 1, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(12, 2, 1, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(13, 2, 1, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(15, 2, 1, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(16, 2, 2, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(17, 2, 2, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(18, 2, 1, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(19, 2, 1, '2025-05-11', 'G02,G03,G11,G12,G13,G14'),
(23, 2, 4, '2025-05-11', 'G2,G4'),
(24, 2, 1, '2025-05-11', 'G2,G3,G11,G12,G13,G14'),
(25, 2, 3, '2025-05-11', 'G1,G3,G5,G10'),
(26, 2, 1, '2025-05-15', 'G1,G2,G3'),
(27, 2, 1, '2025-05-15', 'G1,G2,G3'),
(28, 2, 3, '2025-05-15', 'G1,G3,G5,G10'),
(29, 2, 3, '2025-05-15', 'G1,G3,G5,G10'),
(30, 2, 1, '2025-05-15', 'G2,G3,G11,G12,G13,G14'),
(31, 4, 5, '2025-05-17', 'G1,G4,G7,G8,G16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','pakar') NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-05-01 17:02:29'),
(2, 'Admin', 'admin@gmail.com', '$2y$10$.g5Zkr82kYncYV6FM1o.jeeHVBoLbwrdRXg/eOsICuH2VCWVOFNIC', 'admin', '2025-05-01 17:10:54'),
(3, 'Khadziq', 'khadziq@gmail.com', '$2y$10$IQnaaKMa8XIb8DbgvlnHI.vEkQcYYHOK8Ckt8DyLBZC2JJEEiq4BW', 'user', '2025-05-02 09:25:48'),
(4, 'Doni', 'doni@gmail.com', '$2y$10$Dc9e151t0Uot2PTBXMijK.qc/bEL6KkZxQHJGGqydTB1OHixVG/M.', 'pakar', '2025-05-17 13:41:56'),
(5, 'Sia', 'sia@gmail.com', '$2y$10$YxBlHYkGWP9GHWzGvXLwTezkm0qBVPEhASNuXKVBzUvEzVOoqlA9y', 'user', '2025-05-25 04:49:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aturan`
--
ALTER TABLE `aturan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penyakit_id` (`penyakit_id`),
  ADD KEY `gejala_id` (`gejala_id`);

--
-- Indexes for table `gejala`
--
ALTER TABLE `gejala`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_gejala` (`kode_gejala`);

--
-- Indexes for table `penyakit`
--
ALTER TABLE `penyakit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_penyakit` (`kode_penyakit`);

--
-- Indexes for table `riwayat`
--
ALTER TABLE `riwayat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `penyakit_id` (`penyakit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aturan`
--
ALTER TABLE `aturan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `gejala`
--
ALTER TABLE `gejala`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `penyakit`
--
ALTER TABLE `penyakit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `riwayat`
--
ALTER TABLE `riwayat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aturan`
--
ALTER TABLE `aturan`
  ADD CONSTRAINT `aturan_ibfk_1` FOREIGN KEY (`penyakit_id`) REFERENCES `penyakit` (`id`),
  ADD CONSTRAINT `aturan_ibfk_2` FOREIGN KEY (`gejala_id`) REFERENCES `gejala` (`id`);

--
-- Constraints for table `riwayat`
--
ALTER TABLE `riwayat`
  ADD CONSTRAINT `riwayat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `riwayat_ibfk_2` FOREIGN KEY (`penyakit_id`) REFERENCES `penyakit` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
