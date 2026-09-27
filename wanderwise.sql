-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 27, 2026 at 01:39 PM
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
-- Database: `wanderwise`
--

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `destination_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `nature` int(11) DEFAULT NULL,
  `mountains` int(11) DEFAULT NULL,
  `beach` int(11) DEFAULT NULL,
  `adventure` int(11) DEFAULT NULL,
  `peace` int(11) DEFAULT NULL,
  `culture` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`destination_id`, `name`, `nature`, `mountains`, `beach`, `adventure`, `peace`, `culture`) VALUES
(1, 'Sikkim', 5, 5, 0, 4, 5, 3),
(2, 'Goa', 3, 0, 5, 3, 2, 3),
(3, 'Meghalaya', 5, 4, 0, 4, 5, 3),
(4, 'Kerala', 5, 2, 4, 3, 4, 4),
(5, 'Rajasthan', 2, 0, 0, 2, 3, 5),
(6, 'Kashmir', 5, 5, 0, 4, 5, 4),
(7, 'Andaman', 4, 0, 5, 3, 4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `trip_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `travel_date` date DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `experience` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`trip_id`, `user_id`, `destination`, `travel_date`, `budget`, `rating`, `experience`) VALUES
(2, 1, 'Sikkim', '2026-05-15', 25000.00, 5, 'The mountains were beautiful and peaceful. I really enjoyed nature and photography'),
(4, 1, 'Darjeeling', '2026-04-10', 18000.00, 5, 'I loved the peaceful mountains and beautiful scenery.\r\nThe weather was pleasant and I enjoyed photography.'),
(5, 1, 'Goa', '2026-02-15', 22000.00, 3, 'The beaches were beautiful and I enjoyed photography.\r\nHowever, I did not like the crowded nightlife.'),
(6, 1, 'Jaipur', '2026-10-01', 15000.00, 3, 'I enjoyed visiting historical places and learning about the culture.\r\nThe weather was very hot and the trip was less relaxing.'),
(7, 1, 'Manali', '2025-12-20', 28000.00, 5, 'The mountains and nature were amazing.\r\nI loved the peaceful environment and enjoyed adventure activities.'),
(8, 1, 'Thailand', '2026-06-28', 90000.00, 4, 'Marvelous');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`) VALUES
(1, 'Test User', 'test@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`destination_id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`trip_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `destination_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
