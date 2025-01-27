-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 25, 2024 at 11:18 AM
-- Server version: 8.0.39-cll-lve
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `videosharing`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'deneme 1'),
(2, 'deneme 2'),
(3, 'deneme 3'),
(4, 'deneme 4'),
(5, 'deneme 5'),
(6, 'deneme 6'),
(7, 'deneme 7'),
(8, 'deneme 8'),
(9, 'deneme 9'),
(10, 'deneme 10'),
(11, 'deneme 11'),
(12, 'deneme 12'),
(13, 'deneme 13'),
(14, 'deneme 14'),
(15, 'deneme 15'),
(16, 'deneme 16'),
(17, 'deneme 17'),
(18, 'deneme 18'),
(19, 'deneme 19'),
(20, 'deneme 20'),
(21, 'deneme 21'),
(22, 'deneme 22'),
(23, 'deneme 23'),
(24, 'deneme 24'),
(25, 'deneme 25'),
(26, 'deneme 26'),
(27, 'deneme 27'),
(28, 'deneme 28'),
(29, 'deneme 29'),
(30, 'deneme 30'),
(31, 'deneme 31'),
(32, 'deneme 32'),
(33, 'deneme 33'),
(34, 'deneme 34'),
(35, 'deneme 35'),
(36, 'deneme 36');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`) VALUES
(1, 'deneme1'),
(2, 'deneme2'),
(3, 'deneme3'),
(4, 'deneme4'),
(5, 'deneme5'),
(6, 'deneme6'),
(7, 'deneme7'),
(8, 'deneme8'),
(9, 'deneme9'),
(10, 'deneme10'),
(11, 'deneme11'),
(12, 'deneme12'),
(13, 'deneme13'),
(14, 'deneme14'),
(15, 'deneme15'),
(16, 'deneme16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(3, 'kirmizi', '$2y$10$OeP7PlWNmWyBHcQMMbOTNOGjxzvT8TsJHgiJQ98hM3scj/k7coPAy');

-- --------------------------------------------------------

--
-- Table structure for table `users_online`
--

CREATE TABLE `users_online` (
  `session_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_online`
--

INSERT INTO `users_online` (`session_id`, `last_activity`) VALUES
('', '2024-12-25 21:14:45');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `thumbnail_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `video_url` text COLLATE utf8mb4_general_ci NOT NULL,
  `video_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hakkinda` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `duration` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `title`, `thumbnail`, `thumbnail_url`, `video_url`, `video_file`, `hakkinda`, `created_at`, `duration`) VALUES
(124, 'deneme', 'test', 'test', 'test', 'test', 'test', '2024-12-24 20:40:48', '9:57');

-- --------------------------------------------------------

--
-- Table structure for table `video_categories`
--

CREATE TABLE `video_categories` (
  `video_id` int NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_categories`
--

INSERT INTO `video_categories` (`video_id`, `category_id`) VALUES
(124, 2),
(125, 2),
(126, 2),
(127, 2),
(126, 4),
(124, 6),
(125, 6),
(126, 6),
(127, 6),
(124, 8),
(124, 9),
(125, 9),
(126, 9),
(127, 9),
(124, 10),
(125, 10),
(126, 10),
(127, 10),
(124, 11),
(125, 11),
(126, 11),
(127, 11),
(124, 14),
(125, 14),
(126, 14),
(127, 14),
(124, 15),
(127, 15),
(124, 16),
(125, 16),
(126, 16),
(127, 16),
(124, 17),
(125, 17),
(126, 17),
(127, 17),
(124, 18),
(127, 18),
(126, 20),
(124, 21),
(125, 21),
(126, 21),
(127, 21),
(126, 22),
(124, 23),
(125, 23),
(126, 23),
(127, 23),
(124, 24),
(125, 24),
(126, 24),
(127, 24),
(125, 26),
(127, 26),
(124, 28),
(126, 28),
(127, 28),
(124, 29),
(125, 29),
(126, 29),
(127, 29),
(124, 30),
(125, 30),
(126, 30),
(127, 30),
(124, 31),
(125, 31),
(126, 31),
(127, 31),
(125, 35),
(127, 35),
(124, 36),
(125, 36),
(126, 36);

-- --------------------------------------------------------

--
-- Table structure for table `video_tags`
--

CREATE TABLE `video_tags` (
  `video_id` int NOT NULL,
  `tag_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_tags`
--

INSERT INTO `video_tags` (`video_id`, `tag_id`) VALUES
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(124, 2),
(125, 2),
(126, 2),
(127, 2),
(124, 3),
(125, 3),
(126, 3),
(127, 3),
(124, 4),
(125, 4),
(126, 4),
(127, 4),
(124, 5),
(125, 5),
(126, 5),
(127, 5),
(124, 6),
(125, 6),
(126, 6),
(127, 6),
(124, 7),
(125, 7),
(126, 7),
(127, 7),
(124, 8),
(125, 8),
(126, 8),
(127, 8),
(124, 9),
(125, 9),
(126, 9),
(127, 9),
(124, 10),
(125, 10),
(126, 10),
(127, 10),
(124, 11),
(125, 11),
(126, 11),
(127, 11),
(124, 12),
(125, 12),
(126, 12),
(127, 12),
(124, 13),
(125, 13),
(126, 13),
(127, 13),
(124, 14),
(125, 14),
(126, 14),
(127, 14),
(124, 15),
(125, 15),
(126, 15),
(127, 15),
(124, 16),
(125, 16),
(126, 16),
(127, 16);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `users_online`
--
ALTER TABLE `users_online`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video_categories`
--
ALTER TABLE `video_categories`
  ADD PRIMARY KEY (`video_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `video_tags`
--
ALTER TABLE `video_tags`
  ADD PRIMARY KEY (`video_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `video_categories`
--
ALTER TABLE `video_categories`
  ADD CONSTRAINT `video_categories_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `video_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `video_tags`
--
ALTER TABLE `video_tags`
  ADD CONSTRAINT `video_tags_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `video_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
