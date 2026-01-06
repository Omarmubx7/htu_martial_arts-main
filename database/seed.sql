-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Dec 29, 2025 at 10:47 PM
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
-- Database: `htu_martial_arts`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `booking_date` date NOT NULL DEFAULT current_timestamp(),
  `status` enum('confirmed','cancelled') DEFAULT 'confirmed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `martial_art` varchar(50) NOT NULL,
  `age_group` varchar(20) DEFAULT 'Adult',
  `is_kids_class` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`, `day_of_week`, `start_time`, `end_time`, `martial_art`, `age_group`, `is_kids_class`) VALUES
(1, 'Jiu-jitsu', 'Monday', '06:00:00', '07:30:00', 'Jiu-jitsu', 'Adult', 0),
(2, 'Muay Thai', 'Monday', '08:00:00', '10:00:00', 'Muay Thai', 'Adult', 0),
(3, 'Private Tuition', 'Monday', '10:30:00', '12:00:00', 'Private', 'All', 0),
(4, 'Open Mat', 'Monday', '13:00:00', '14:30:00', 'Jiu-jitsu', 'Adult', 0),
(5, 'Kids Jiu-jitsu', 'Monday', '15:00:00', '17:00:00', 'Jiu-jitsu', 'Kids', 1),
(6, 'Karate', 'Monday', '17:30:00', '19:00:00', 'Karate', 'Adult', 0),
(7, 'Jiu-jitsu', 'Monday', '19:00:00', '21:00:00', 'Jiu-jitsu', 'Adult', 0),
(8, 'Karate', 'Tuesday', '06:00:00', '07:30:00', 'Karate', 'Adult', 0),
(9, 'Private Tuition', 'Tuesday', '08:00:00', '10:00:00', 'Private', 'All', 0),
(10, 'Private Tuition', 'Tuesday', '10:30:00', '12:00:00', 'Private', 'All', 0),
(11, 'Open Mat', 'Tuesday', '13:00:00', '14:30:00', 'Jiu-jitsu', 'Adult', 0),
(12, 'Kids Judo', 'Tuesday', '15:00:00', '17:00:00', 'Judo', 'Kids', 1),
(13, 'Muay Thai', 'Tuesday', '17:30:00', '19:00:00', 'Muay Thai', 'Adult', 0),
(14, 'Judo', 'Tuesday', '19:00:00', '21:00:00', 'Judo', 'Adult', 0),
(15, 'Beginner Self-Defence', 'Tuesday', '18:00:00', '19:00:00', 'Self-Defence', 'Adult', 0),
(16, 'Judo', 'Wednesday', '06:00:00', '07:30:00', 'Judo', 'Adult', 0),
(17, 'Private Tuition', 'Wednesday', '08:00:00', '10:00:00', 'Private', 'All', 0),
(18, 'Private Tuition', 'Wednesday', '10:30:00', '12:00:00', 'Private', 'All', 0),
(19, 'Open Mat', 'Wednesday', '13:00:00', '14:30:00', 'Jiu-jitsu', 'Adult', 0),
(20, 'Kids Karate', 'Wednesday', '15:00:00', '17:00:00', 'Karate', 'Kids', 1),
(21, 'Judo', 'Wednesday', '17:30:00', '19:00:00', 'Judo', 'Adult', 0),
(22, 'Jiu-jitsu', 'Wednesday', '19:00:00', '21:00:00', 'Jiu-jitsu', 'Adult', 0),
(23, 'Jiu-jitsu', 'Thursday', '06:00:00', '07:30:00', 'Jiu-jitsu', 'Adult', 0),
(24, 'Private Tuition', 'Thursday', '08:00:00', '10:00:00', 'Private', 'All', 0),
(25, 'Private Tuition', 'Thursday', '10:30:00', '12:00:00', 'Private', 'All', 0),
(26, 'Open Mat', 'Thursday', '13:00:00', '14:30:00', 'Jiu-jitsu', 'Adult', 0),
(27, 'Kids Jiu-jitsu', 'Thursday', '15:00:00', '17:00:00', 'Jiu-jitsu', 'Kids', 1),
(28, 'Jiu-jitsu', 'Thursday', '17:30:00', '19:00:00', 'Jiu-jitsu', 'Adult', 0),
(29, 'Karate', 'Thursday', '19:00:00', '21:00:00', 'Karate', 'Adult', 0),
(30, 'Advanced Self-Defence', 'Thursday', '19:00:00', '20:00:00', 'Self-Defence', 'Adult', 0),
(31, 'Muay Thai', 'Friday', '06:00:00', '07:30:00', 'Muay Thai', 'Adult', 0),
(32, 'Jiu-jitsu', 'Friday', '08:00:00', '10:00:00', 'Jiu-jitsu', 'Adult', 0),
(33, 'Private Tuition', 'Friday', '10:30:00', '12:00:00', 'Private', 'All', 0),
(34, 'Open Mat', 'Friday', '13:00:00', '14:30:00', 'Jiu-jitsu', 'Adult', 0),
(35, 'Kids Judo', 'Friday', '15:00:00', '17:00:00', 'Judo', 'Kids', 1),
(36, 'Muay Thai', 'Friday', '17:30:00', '19:00:00', 'Muay Thai', 'Adult', 0),
(37, 'Private Tuition', 'Friday', '19:00:00', '21:00:00', 'Private', 'All', 0),
(38, 'Private Tuition', 'Saturday', '08:00:00', '10:00:00', 'Private', 'All', 0),
(39, 'Judo', 'Saturday', '10:30:00', '12:00:00', 'Judo', 'Adult', 0),
(40, 'Karate', 'Saturday', '13:00:00', '14:30:00', 'Karate', 'Adult', 0),
(41, 'Muay Thai', 'Saturday', '15:00:00', '17:00:00', 'Muay Thai', 'Adult', 0),
(42, 'Private Tuition', 'Sunday', '08:00:00', '10:00:00', 'Private', 'All', 0),
(43, 'Karate', 'Sunday', '10:30:00', '12:00:00', 'Karate', 'Adult', 0),
(44, 'Judo', 'Sunday', '13:00:00', '14:30:00', 'Judo', 'Adult', 0),
(45, 'Jiu-jitsu', 'Sunday', '15:00:00', '17:00:00', 'Jiu-jitsu', 'Adult', 0),
(46, 'Muay Thai Conditioning', 'Sunday', '06:00:00', '07:30:00', 'Muay Thai', 'Adult', 0),
(47, 'Kids Karate', 'Sunday', '09:00:00', '10:30:00', 'Karate', 'Kids', 1);

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialty` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructors`
--

INSERT INTO `instructors` (`id`, `name`, `specialty`, `bio`, `image_url`) VALUES
(1, 'Ali Mohammed', 'Gym Owner / Head Martial Arts Coach', 'Coaches in all martial arts. 4th Dan Blackbelt judo, 3rd Dan Blackbelt jiu-jitsu, 1st Dan Blackbelt karate. Accredited Muay Thai coach.', NULL),
(2, 'Sarah Saleh', 'Assistant Martial Arts Coach', '5th Dan karate.', NULL),
(3, 'Fares Qasem', 'Assistant Martial Arts Coach', '2nd Dan Blackbelt jiu-jitsu, 1st Dan Blackbelt judo. Accredited Muay Thai coach.', NULL),
(4, 'Maen Mohanad', 'Assistant Martial Arts Coach', '3rd Dan Blackbelt karate.', NULL),
(5, 'Reem Emad', 'Karate Instructor', 'Karate instructor emphasizing kata, discipline, and self-defence basics. Accredited in traditional Shotokan style.', NULL),
(6, 'Jana Qader', 'Fitness Coach', 'BSc in Physiotherapy, MSc in Sports Science.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `sessions_per_week` int(11) DEFAULT NULL,
  `allowed_arts` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memberships`
--

INSERT INTO `memberships` (`id`, `type`, `price`, `description`, `sessions_per_week`, `allowed_arts`) VALUES
(1, 'Basic', 25.00, '1 martial art - 2 sessions per week - monthly fee', 2, 1),
(2, 'Intermediate', 35.00, '1 martial art - 3 sessions per week - monthly fee', 3, 1),
(3, 'Advanced', 45.00, 'Any 2 martial arts - 5 sessions per week - monthly fee', 5, 2),
(4, 'Elite', 60.00, 'Unlimited classes', NULL, NULL),
(5, 'Junior Membership', 25.00, 'Can attend all-kids martial arts sessions', NULL, NULL),
(6, 'Private Tuition', 15.00, 'Per hour martial arts tuition', NULL, NULL),
(7, 'Beginners\' Self-Defence', 180.00, 'Six-week beginners self-defence course (2 x 1-hour session per week)', 2, NULL),
(8, 'Use of fitness room', 6.00, 'Per visit', NULL, NULL),
(9, 'Personal fitness training', 35.00, 'Per hour with a fitness coach', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `membership_type_id` int(11) DEFAULT NULL,
  `membership_id` int(11) DEFAULT NULL,
  `chosen_martial_art` varchar(50) DEFAULT NULL,
  `sessions_remaining` int(11) DEFAULT 0,
  `sessions_used_this_week` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `role` enum('admin','member') NOT NULL DEFAULT 'member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `membership_type_id`, `membership_id`, `chosen_martial_art`, `sessions_remaining`, `sessions_used_this_week`, `created_at`, `is_admin`, `role`) VALUES
(1, 'Admin User', 'admin@htu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'All', 0, 0, '2025-12-29 18:16:22', 1, 'admin'),
(2, 'Basic Joe', 'basic@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Judo', 0, 0, '2025-12-29 18:16:22', 0, 'member'),
(3, 'Self Defence Sarah', 'sarah@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Self-Defence', 0, 0, '2025-12-29 18:16:22', 0, 'member'),
(4, 'omar mubaidin', 'omarmub@gmail.com', '$2y$10$mjab3NQHKdDKRrmmMsxp2OgAvqZ9mVCd4Uy/EhartBACqqMIDfFIe', NULL, 4, '0', 0, 1, '2025-12-29 21:14:04', 0, 'member');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`),
  ADD KEY `fk_class` (`class_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_membership` (`membership_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_membership` FOREIGN KEY (`membership_type_id`) REFERENCES `memberships` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
