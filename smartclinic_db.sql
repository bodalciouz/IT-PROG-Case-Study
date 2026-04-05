-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 05, 2026 at 11:52 AM
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

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `service_id`, `appointment_date`, `appointment_time`, `status`, `created_at`) VALUES
(34, 17, 4, '2026-04-06', '23:12:00', 'pending', '2026-04-04 15:12:49'),
(35, 17, 3, '2026-04-07', '13:16:00', 'missed', '2026-04-04 15:16:24'),
(36, 18, 4, '2026-04-06', '23:12:00', 'pending', '2026-04-04 15:17:40'),
(37, 21, 2, '2026-04-06', '09:00:00', 'confirmed', '2026-04-05 09:51:44'),
(38, 22, 10, '2026-04-06', '09:30:00', 'pending', '2026-04-05 09:51:44'),
(39, 23, 5, '2026-04-06', '10:00:00', 'completed', '2026-04-05 09:51:44'),
(40, 24, 1, '2026-04-06', '10:30:00', 'pending', '2026-04-05 09:51:44'),
(41, 25, 17, '2026-04-06', '11:00:00', 'cancelled', '2026-04-05 09:51:44'),
(42, 26, 13, '2026-04-06', '11:30:00', 'missed', '2026-04-05 09:51:44'),
(43, 21, 13, '2026-04-07', '09:00:00', 'confirmed', '2026-04-05 09:51:44'),
(44, 22, 17, '2026-04-07', '09:30:00', 'pending', '2026-04-05 09:51:44'),
(45, 23, 4, '2026-04-07', '10:00:00', 'ongoing', '2026-04-05 09:51:44'),
(46, 24, 3, '2026-04-07', '10:30:00', 'completed', '2026-04-05 09:51:44'),
(47, 25, 8, '2026-04-07', '11:00:00', 'pending', '2026-04-05 09:51:44'),
(48, 26, 6, '2026-04-07', '11:30:00', 'confirmed', '2026-04-05 09:51:44');

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

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `appointment_id`, `type`, `message`, `created_at`) VALUES
(10, 17, 34, 'appointment', 'Your appointment request for CT-Scan on April 6, 2026 at 11:12 PM has been submitted and is awaiting confirmation.', '2026-04-04 15:12:49'),
(11, 17, 35, 'appointment', 'Your appointment request for Digital Mammography on April 7, 2026 at 1:16 PM has been submitted and is awaiting confirmation.', '2026-04-04 15:16:24'),
(12, 18, 36, 'appointment', 'Your appointment request for CT-Scan on April 6, 2026 at 11:12 PM has been submitted and is awaiting confirmation.', '2026-04-04 15:17:40'),
(13, 21, 37, 'appointment', 'Your X-Ray appointment on April 6, 2026 at 9:00 AM has been confirmed.', '2026-04-05 09:51:44'),
(14, 22, 38, 'appointment', 'Your Ultrasound appointment on April 6, 2026 at 9:30 AM has been submitted and is awaiting confirmation.', '2026-04-05 09:51:44'),
(15, 23, 39, 'queue', 'Your ECG appointment on April 6, 2026 at 10:00 AM has been completed.', '2026-04-05 09:51:44'),
(16, 24, 40, 'appointment', 'Your Special Laboratory appointment on April 6, 2026 at 10:30 AM has been submitted and is awaiting confirmation.', '2026-04-05 09:51:44'),
(17, 25, 41, 'appointment', 'Your Vaccination appointment on April 6, 2026 at 11:00 AM has been cancelled.', '2026-04-05 09:51:44'),
(18, 26, 42, 'queue', 'You missed your Multi-Specialty Doctors Clinic appointment on April 6, 2026 at 11:30 AM.', '2026-04-05 09:51:44'),
(19, 21, 43, 'appointment', 'Your Multi-Specialty Doctors Clinic appointment on April 7, 2026 at 9:00 AM has been confirmed.', '2026-04-05 09:51:44'),
(20, 22, 44, 'appointment', 'Your Vaccination appointment on April 7, 2026 at 9:30 AM has been submitted and is awaiting confirmation.', '2026-04-05 09:51:44'),
(21, 23, 45, 'queue', 'Your CT-Scan appointment on April 7, 2026 at 10:00 AM is now ongoing.', '2026-04-05 09:51:44'),
(22, 24, 46, 'queue', 'Your Digital Mammography appointment on April 7, 2026 at 10:30 AM has been completed.', '2026-04-05 09:51:44'),
(23, 25, 47, 'appointment', 'Your Ambulatory Blood Pressure Monitoring appointment on April 7, 2026 at 11:00 AM has been submitted and is awaiting confirmation.', '2026-04-05 09:51:44'),
(24, 26, 48, 'appointment', 'Your ICG Echocardiography appointment on April 7, 2026 at 11:30 AM has been confirmed.', '2026-04-05 09:51:44');

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

--
-- Dumping data for table `queue`
--

INSERT INTO `queue` (`queue_id`, `appointment_id`, `queue_number`, `status`, `estimated_wait`, `created_at`) VALUES
(12, 34, 1, 'completed', 60, '2026-04-04 15:12:49'),
(13, 35, 1, 'missed', 45, '2026-04-04 15:16:24'),
(14, 36, 2, 'completed', 120, '2026-04-04 15:17:40'),
(15, 37, 3, 'pending', 60, '2026-04-05 09:51:44'),
(16, 38, 4, 'pending', 90, '2026-04-05 09:51:44'),
(17, 39, 5, 'completed', 100, '2026-04-05 09:51:44'),
(18, 40, 6, 'pending', 150, '2026-04-05 09:51:44'),
(19, 41, 7, 'cancelled', 420, '2026-04-05 09:51:44'),
(20, 42, 8, 'missed', 240, '2026-04-05 09:51:44'),
(21, 43, 2, 'pending', 30, '2026-04-05 09:51:44'),
(22, 44, 3, 'pending', 45, '2026-04-05 09:51:44'),
(23, 45, 4, 'ongoing', 180, '2026-04-05 09:51:44'),
(24, 46, 5, 'completed', 225, '2026-04-05 09:51:44'),
(25, 47, 6, 'pending', 180, '2026-04-05 09:51:44'),
(26, 48, 7, 'pending', 270, '2026-04-05 09:51:44');

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
(1, 7, 'Monday', '11:00:00', '17:00:00', 11, '2026-03-31 07:40:26'),
(2, 7, 'Friday', '15:18:00', '23:23:00', 12, '2026-04-04 15:18:39'),
(3, 7, 'Tuesday', '09:00:00', '17:00:00', 10, '2026-04-05 09:51:44'),
(4, 7, 'Wednesday', '09:00:00', '17:00:00', 10, '2026-04-05 09:51:44'),
(5, 19, 'Monday', '08:00:00', '16:00:00', 8, '2026-04-05 09:51:44'),
(6, 19, 'Thursday', '10:00:00', '18:00:00', 8, '2026-04-05 09:51:44'),
(7, 20, 'Tuesday', '08:30:00', '15:30:00', 12, '2026-04-05 09:51:44'),
(8, 20, 'Friday', '09:00:00', '17:00:00', 12, '2026-04-05 09:51:44');

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
(17, 'Vaccination', 'Vaccination Services', 60);

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
(16, 3, 'ako', 'lang', 'akolangba@gmail.com', '$2y$10$.DxgOTdKJHN.QXpZp6Ax4.yqYPcJjOGb7CyDpa39fbWk/gSoSd5Ti', '0922 727 1919', 'Manila', '2026-04-01 12:25:26'),
(17, 3, 'john', 'doe', 'john@yahoo.com', '$2y$10$NsXoMzFg9n9zg/PwZkNDqOAA4ixXjpox7Nmb35wrEQjamfGrfkvUW', '091241234', 'john house', '2026-04-04 15:11:49'),
(18, 3, 'jane', 'doe', 'jane@gmail.com', '$2y$10$PDC4Vm0jk/lnrThA3e/lJePhlMiHlgHgjrd1d9NbucBZXcz7M0O3C', '', '', '2026-04-04 15:12:05'),
(19, 2, 'Maria', 'Santos', 'maria.santos@smartclinic.com', '$2y$10$46DI89ujwBaSsXkDGmXxte9X6y4yaqnpnCHMIXV6KMuSZ6gtbRjMS', '09170000001', 'Staff Office A', '2026-04-05 09:51:44'),
(20, 2, 'Paolo', 'Reyes', 'paolo.reyes@smartclinic.com', '$2y$10$46DI89ujwBaSsXkDGmXxte9X6y4yaqnpnCHMIXV6KMuSZ6gtbRjMS', '09170000002', 'Staff Office B', '2026-04-05 09:51:44'),
(21, 3, 'Mark', 'Dela Cruz', 'mark.delacruz@gmail.com', '$2y$10$PDC4Vm0jk/lnrThA3e/lJePhlMiHlgHgjrd1d9NbucBZXcz7M0O3C', '09181234567', 'Quezon City', '2026-04-05 09:51:44'),
(22, 3, 'Angela', 'Torres', 'angela.torres@gmail.com', '$2y$10$PDC4Vm0jk/lnrThA3e/lJePhlMiHlgHgjrd1d9NbucBZXcz7M0O3C', '09182345678', 'Makati City', '2026-04-05 09:51:44'),
(23, 3, 'Kevin', 'Lim', 'kevin.lim@yahoo.com', '$2y$10$PDC4Vm0jk/lnrThA3e/lJePhlMiHlgHgjrd1d9NbucBZXcz7M0O3C', '09183456789', 'Pasig City', '2026-04-05 09:51:44'),
(24, 3, 'Sofia', 'Navarro', 'sofia.navarro@gmail.com', '$2y$10$PDC4Vm0jk/lnrThA3e/lJePhlMiHlgHgjrd1d9NbucBZXcz7M0O3C', '09184567890', 'Taguig City', '2026-04-05 09:51:44'),
(25, 3, 'Luis', 'Garcia', 'luis.garcia@gmail.com', '$2y$10$PDC4Vm0jk/lnrThA3e/lJePhlMiHlgHgjrd1d9NbucBZXcz7M0O3C', '09185678901', 'Mandaluyong City', '2026-04-05 09:51:44'),
(26, 3, 'Bea', 'Fernandez', 'bea.fernandez@yahoo.com', '$2y$10$PDC4Vm0jk/lnrThA3e/lJePhlMiHlgHgjrd1d9NbucBZXcz7M0O3C', '09186789012', 'San Juan City', '2026-04-05 09:51:44');

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
  MODIFY `appointment_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for appointments', AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for notifications', AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `queue_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for queue', AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for roles', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for schedules', AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for services', AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK for users', AUTO_INCREMENT=27;

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
