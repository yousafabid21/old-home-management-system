-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2026 at 01:52 PM
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
-- Database: `old_home_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `event_type` enum('Meal','Medication','Exercise','Social') NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `title`, `description`, `event_type`, `scheduled_at`, `created_by`, `created_at`) VALUES
(1, 'male', '123', 'Meal', '2026-12-31 12:59:00', 1, '2026-01-17 12:39:29');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `family_member_id` int(11) NOT NULL,
  `elderly_id` int(11) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `family_member_id`, `elderly_id`, `appointment_date`, `status`, `notes`, `created_at`) VALUES
(1, 5, 2, '2026-01-24 17:38:00', 'Approved', '1', '2026-01-17 12:39:02');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `donation_date` datetime DEFAULT current_timestamp(),
  `status` enum('Pending','Completed') DEFAULT 'Completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `donor_id`, `amount`, `payment_method`, `message`, `donation_date`, `status`) VALUES
(1, 3, 123.00, 'Credit Card', '', '2026-01-17 17:16:23', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `elderly_members`
--

CREATE TABLE `elderly_members` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `admission_date` date NOT NULL,
  `medical_history` text DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `family_member_id` int(11) DEFAULT NULL,
  `room_no` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `elderly_members`
--

INSERT INTO `elderly_members` (`id`, `name`, `dob`, `gender`, `admission_date`, `medical_history`, `emergency_contact`, `family_member_id`, `room_no`, `created_at`) VALUES
(1, 'Usman', '2026-01-02', 'Male', '2026-01-20', 'asdfdsg', NULL, 2, '123', '2026-01-17 10:47:39'),
(2, 'Usman', '2026-12-31', 'Male', '2026-01-17', 'f', NULL, 5, 'Pending', '2026-01-17 12:38:21');

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL,
  `elderly_id` int(11) NOT NULL,
  `doctor_name` varchar(100) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `checkup_date` date NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`id`, `elderly_id`, `doctor_name`, `diagnosis`, `medications`, `checkup_date`, `notes`) VALUES
(1, 2, 'Ali', 'is', '123', '2026-12-31', 'sdfsd');

-- --------------------------------------------------------

--
-- Table structure for table `staff_p_details`
--

CREATE TABLE `staff_p_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `designation` varchar(50) DEFAULT NULL,
  `shift` varchar(50) DEFAULT NULL,
  `joining_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_p_details`
--

INSERT INTO `staff_p_details` (`id`, `user_id`, `designation`, `shift`, `joining_date`) VALUES
(1, 4, 'Nurse', 'Evening', '2024-12-31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','donor','family') NOT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `contact_no`, `address`, `created_at`) VALUES
(1, 'System Administrator', 'admin@gmail.com', '$2y$10$Ot.yOnTSDO7lXv8PCJX/SO9UGLM.gQBlgD3VsZ1TVdESbBiSC56Pm', 'admin', '1234567890', 'Admin Office', '2026-01-05 07:28:39'),
(2, 'usman', 'Usman@gmail.com', '$2y$10$ii7UYTb5OPBj7hmD8cZczOnMTwDIweZ5rU7cG8EKleCP2ObBOFyVW', 'family', '123', NULL, '2026-01-17 12:14:44'),
(3, 'worker', 'worker@gmail.com', '$2y$10$CUTH/oWwA/fRcCDgzamChO1GzMPgZSYwFcyJ10JyKfOHlnc.pcD.O', 'donor', '123', NULL, '2026-01-17 12:16:06'),
(4, 'Farah', 'farah@gmail.com', '$2y$10$MSi59G85nHn3mLzw.IOa6.CRoEeEzRVqjUPZJNNF8b7FaUHLIkuEe', 'staff', '02359239', NULL, '2026-01-17 12:34:42'),
(5, 'ali', 'ali@gmail.com', '$2y$10$NEQkPTAUvR3VMRWD3dpO9euhMnrRcHc29Bm1dvgZZ7zB5pZkOYFVe', 'family', '123', NULL, '2026-01-17 12:37:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `family_member_id` (`family_member_id`),
  ADD KEY `elderly_id` (`elderly_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `elderly_members`
--
ALTER TABLE `elderly_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `family_member_id` (`family_member_id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `elderly_id` (`elderly_id`);

--
-- Indexes for table `staff_p_details`
--
ALTER TABLE `staff_p_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `elderly_members`
--
ALTER TABLE `elderly_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff_p_details`
--
ALTER TABLE `staff_p_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`family_member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`elderly_id`) REFERENCES `elderly_members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `elderly_members`
--
ALTER TABLE `elderly_members`
  ADD CONSTRAINT `elderly_members_ibfk_1` FOREIGN KEY (`family_member_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`elderly_id`) REFERENCES `elderly_members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_p_details`
--
ALTER TABLE `staff_p_details`
  ADD CONSTRAINT `staff_p_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
