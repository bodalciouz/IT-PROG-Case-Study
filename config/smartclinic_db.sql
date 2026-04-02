-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2026 at 08:28 PM
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
-- Database: `smartclinic_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) UNSIGNED NOT NULL COMMENT 'PK for appointments',
  `patient_id` int(11) UNSIGNED NOT NULL COMMENT 'FK users',
  `service_id` int(11) UNSIGNED NOT NULL COMMENT 'FK services',
  `appointment_date` date NOT NULL COMMENT 'Date of the appointment',
  `appointment_time` time NOT NULL COMMENT 'Time of the appointment',
  `status` enum('pending','confirmed','ongoing','completed','missed','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Current appointment status',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Creation date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) UNSIGNED NOT NULL COMMENT 'PK for notifications',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT 'FK users',
  `appointment_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'FK appointments',
  `type` enum('appointment','queue','reminder','other') NOT NULL DEFAULT 'other' COMMENT 'Type of notification',
  `message` text DEFAULT NULL COMMENT 'Notification content',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `queue_id` int(11) UNSIGNED NOT NULL COMMENT 'PK for queue',
  `appointment_id` int(11) UNSIGNED NOT NULL COMMENT 'FK appointments',
  `queue_number` int(11) UNSIGNED NOT NULL COMMENT 'Patient position in queue',
  `status` enum('pending','ongoing','completed','missed','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Current queue status',
  `estimated_wait` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Estimated waiting time in minutes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Queue record creation date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) UNSIGNED NOT NULL COMMENT 'PK for roles',
  `role_name` varchar(50) NOT NULL COMMENT 'Name of roles'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(3, 'Patient'),
(2, 'Staff');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) UNSIGNED NOT NULL COMMENT 'PK for schedules',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT 'FK users',
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL COMMENT 'Available day',
  `start_time` time NOT NULL COMMENT 'Start time',
  `end_time` time NOT NULL COMMENT 'End time',
  `max_patients` int(11) UNSIGNED NOT NULL DEFAULT 10 COMMENT 'Max patients per schedule',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `user_id`, `day_of_week`, `start_time`, `end_time`, `max_patients`, `created_at`) VALUES
(1, 7, 'Monday', '09:00:00', '17:00:00', 10, '2026-03-31 07:40:26');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) UNSIGNED NOT NULL COMMENT 'PK for services',
  `service_name` varchar(100) NOT NULL COMMENT 'Name of the clinic service',
  `description` text DEFAULT NULL COMMENT 'Description of service',
  `estimated_duration` int(11) UNSIGNED NOT NULL DEFAULT 30 COMMENT 'Estimated duration in minutes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `description`, `estimated_duration`) VALUES
(1, 'Special Laboratory', 'Fully Automated Laboratory Services', 30),
(2, 'X-Ray', 'Digital X-Ray Services', 20),
(3, 'Digital Mammography', 'Digital Mammography Services', 45),
(4, 'CT-Scan', 'Computed Tomography Scan', 60),
(5, 'ECG', 'Electrocardiogram', 20),
(6, 'ICG Echocardiography', 'ICG Echocardiography Services', 45),
(7, 'Treadmill Stress Test', 'Cardiac Stress Test', 60),
(8, 'Ambulatory Blood Pressure Monitoring', 'ABPM Services', 30),
(9, 'Holter Monitoring', 'Holter Monitoring Services', 30),
(10, 'Ultrasound', 'Ultrasound Imaging', 30),
(11, 'Vascular Procedure', 'Vascular Procedure Services', 60),
(12, 'Momme Clinic', 'Momme Clinic Services', 30),
(13, 'Multi-Specialty Doctors Clinic', 'Multi-Specialty Consultation', 30),
(14, 'Home Service Ambulatory Care Services', 'Home Service and Ambulatory Care', 60),
(15, 'Mobile On-Site Services', 'Mobile Medical Services', 45),
(16, 'Vaccination', 'Vaccination Services', 15);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) UNSIGNED NOT NULL COMMENT 'PK for users',
  `role_id` int(11) UNSIGNED NOT NULL COMMENT 'FK for users',
  `first_name` varchar(100) NOT NULL COMMENT 'User first name',
  `last_name` varchar(100) NOT NULL COMMENT 'User last name',
  `email` varchar(100) NOT NULL COMMENT 'Unique email address',
  `password` varchar(255) NOT NULL COMMENT 'User password',
  `contact_number` varchar(20) DEFAULT NULL COMMENT 'User contact number',
  `address` text DEFAULT NULL COMMENT 'User address',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Account creation date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `first_name`, `last_name`, `email`, `password`, `contact_number`, `address`, `created_at`) VALUES
(3, 1, 'Admin', 'User', 'admin@smartclinic.com', '$2y$10$nn6Kl4bgSlGE4.aNTG3Q0e/GzICwkzEGjsjiwUgpKyiLkPkeusv76', '09123456789', 'Clinic Address', '2026-03-20 14:35:44'),
(6, 3, 'Carl', 'Crespo', 'carl_crespo@dlsu.edu.ph', '$2y$10$B.fnhjBo5xThzKh.9IiRH.nRLuDP6z3hEB1pGpSJoE1s8G.aKjT0m', '09668730710', 'Manila', '2026-03-30 08:12:38'),
(7, 2, 'Staff', 'User', 'staff@smartclinic.com', '$2y$10$46DI89ujwBaSsXkDGmXxte9X6y4yaqnpnCHMIXV6KMuSZ6gtbRjMS', '09987654321', 'Clinic Staff Room', '2026-03-30 08:27:02'),
(14, 3, 'Angelo', 'Benigno', 'oleg@gmail.com', '$2y$10$VGNIyen85QeyzkIBGa4lF.DOmCY.3vvShGQOd79CyNXFpmmI1RyJu', '012345678', 'Caloocan', '2026-03-31 09:41:50'),
(16, 3, 'ako', 'lang', 'akolangba@gmail.com', '$2y$10$.DxgOTdKJHN.QXpZp6Ax4.yqYPcJjOGb7CyDpa39fbWk/gSoSd5Ti', '0922 727 1919', 'Manila', '2026-04-01 12:25:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`queue_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `queue_number` (`queue_number`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD UNIQUE KEY `service_name` (`service_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for appointments', AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for notifications', AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `queue_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for queue', AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for roles', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for schedules', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for services', AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for users', AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appointments_patient` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appointments_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `queue`
--
ALTER TABLE `queue`
  ADD CONSTRAINT `fk_queue_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `fk_schedules_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
