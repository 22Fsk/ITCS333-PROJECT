-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2024 at 01:30 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `doctor_schedule`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule`
--

CREATE TABLE `doctor_schedule` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `type` enum('class','office_hour') NOT NULL,
  `course_code` varchar(10) DEFAULT NULL,
  `day` enum('Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `classroom` varchar(50) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `room` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_schedule`
--

INSERT INTO `doctor_schedule` (`id`, `doctor_id`, `type`, `course_code`, `day`, `start_time`, `end_time`, `classroom`, `section`, `room`, `created_at`) VALUES
(1, 1, 'office_hour', '333', 'Tuesday', '05:25:00', '18:35:00', '2048', '4', '', '2024-12-09 23:26:26'),
(2, 1, 'class', '333', 'Monday', '15:30:00', '04:30:00', '2048', '4', '', '2024-12-09 23:27:14'),
(3, 1, 'class', '', 'Monday', '02:30:00', '05:30:00', '', '', '', '2024-12-09 23:28:45'),
(4, 1, 'class', '345', 'Wednesday', '02:55:00', '14:50:00', '755', '2', '', '2024-12-09 23:50:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
