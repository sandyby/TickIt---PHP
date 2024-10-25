-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 25, 2024 at 03:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webpro_uts_lab`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(10) UNSIGNED NOT NULL,
  `list_id` int(10) UNSIGNED NOT NULL,
  `description` varchar(100) NOT NULL,
  `due_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `list_id`, `description`, `due_date`, `status`, `created_at`) VALUES
(10, 4, 'Osu', '2024-10-25 10:53:35', 1, '2024-10-25 10:43:52'),
(11, 4, 'wsdfcsdfv', '2024-10-31 10:44:00', 0, '2024-10-25 10:44:04'),
(12, 4, 'q', '2024-10-25 11:46:17', 0, '2024-10-25 10:46:17'),
(14, 6, 'Palembang', '2024-10-25 11:49:31', 0, '2024-10-25 10:53:51'),
(15, 8, 'MTK', '2024-10-25 11:48:37', 1, '2024-10-25 11:48:34'),
(16, 6, 'Batam', '2024-10-25 11:49:33', 0, '2024-10-25 11:49:21');

-- --------------------------------------------------------

--
-- Table structure for table `lists`
--

CREATE TABLE `lists` (
  `list_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lists`
--

INSERT INTO `lists` (`list_id`, `user_id`, `title`, `created_at`) VALUES
(4, 1, 'Gamingggg', '2024-10-25 10:11:51'),
(6, 1, 'Vacation', '2024-10-25 10:53:27'),
(8, 1, 'Sekolah', '2024-10-25 11:48:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(128) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verification_code` varchar(64) NOT NULL,
  `isVerified` bit(1) DEFAULT b'0',
  `verified_at` timestamp NULL DEFAULT NULL,
  `reset_password_token` varchar(64) DEFAULT NULL,
  `reset_password_token_expiry_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `created_at`, `verification_code`, `isVerified`, `verified_at`, `reset_password_token`, `reset_password_token_expiry_date`) VALUES
(1, 'RafiGhadhanfar', 'rafighadhanfarmuhammad@gmail.com', '$2y$10$82CrCQntq2EsOXKAU9i5P.Oocosrmaa33KyVKHpE3cjLBn.WWYqvW', '2024-10-24 10:25:40', '1bb424d94edec5c5e44ef7b22775609af14c67e03756ec03095cd64bf5f7e311', b'1', '2024-10-25 07:12:10', NULL, NULL),
(2, 'Archie68', 'archie.evilbears.twitch@gmail.com', '$2y$10$mF0daRdoge3V0lqaFMv3Su9bGcJiVRvfz.sDe1YP47AVInGmCiFdS', '2024-10-25 11:18:52', '51af98cb0f00bb442d68645eda1e6aedd0431a8742d277140d150eef2175d74a', b'1', '2024-10-25 11:21:15', NULL, NULL),
(3, 'sandyby56', 'sandyby56@gmail.com', '$2y$10$fyugQQ9JTKFS9DLJdoOL2e1.4EpN/Fe24d/72G5KL5ivX6Mw/HBvO', '2024-10-25 12:38:30', 'b51f12519f7908101d2ebf78158dcd7a65374363655183a18079ec7fc6bde810', b'1', '2024-10-25 12:39:14', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `list_id` (`list_id`);

--
-- Indexes for table `lists`
--
ALTER TABLE `lists`
  ADD PRIMARY KEY (`list_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `verification_code` (`verification_code`),
  ADD UNIQUE KEY `reset_password_token` (`reset_password_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `lists`
--
ALTER TABLE `lists`
  MODIFY `list_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`list_id`) REFERENCES `lists` (`list_id`) ON DELETE CASCADE;

--
-- Constraints for table `lists`
--
ALTER TABLE `lists`
  ADD CONSTRAINT `lists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
