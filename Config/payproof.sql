-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2025 at 01:30 AM
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
-- Database: `payproof`
--

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `course_id` varchar(100) NOT NULL,
  `year_level` varchar(50) NOT NULL,
  `selected_subjects` text NOT NULL,
  `upon_enrollment` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `contact`, `course_id`, `year_level`, `selected_subjects`, `upon_enrollment`, `created_at`) VALUES
(90, 'john paul', 'Francia12', 'magayanes', 'jp3850375@gmail.com', '09934113909', 'BSCS', 'First Year', 'Euthenics 2,Computer Programming 2 (Lab),Computer Programming 2 (Lec),Math in the Modern World,National Service Training Program 2', 1200.00, '2025-03-28 13:22:00'),
(91, 'john paul', 'Francia', 'magayanes', 'jp3850375@gmail.com', '09934113906', 'BSCS', 'First Year', 'Euthenics 2,Computer Programming 2 (Lab),Computer Programming 2 (Lec),Math in the Modern World', 1000.00, '2025-03-28 13:50:48'),
(92, 'john paul', 'Francia12', 'magayanes', 'jp3850375@gmail.com', '09934113909', 'BSCS', 'First Year', 'Computer Programming 2 (Lec),Math in the Modern World,National Service Training Program 2,PATHFIT 2,Ethics,Discrete Structure 1', 2000.00, '2025-03-28 13:53:05'),
(93, 'john paul', 'Francia12', 'magayanes', 'jp3850375@gmail.com', '09934113909', 'BSCS', 'First Year', 'Computer Programming 2 (Lec),Math in the Modern World,National Service Training Program 2,PATHFIT 2,Ethics,Discrete Structure 1', 2000.00, '2025-03-28 13:59:09'),
(94, 'john paul', 'ssdsa', 'magayanes', 'jp3850375@gmail.com', '09123456789', 'BSCS', 'First Year', 'Euthenics 2,Computer Programming 2 (Lab),Computer Programming 2 (Lec),Math in the Modern World,National Service Training Program 2,PATHFIT 2,Ethics', 1000.00, '2025-03-28 14:00:24'),
(95, 'john paul', 'ssdsa', 'magayanes', 'jp3850375@gmail.com', '09123456789', 'BSCS', 'First Year', 'Euthenics 2,Computer Programming 2 (Lab),Computer Programming 2 (Lec),Math in the Modern World,National Service Training Program 2,PATHFIT 2,Ethics', 1000.00, '2025-03-28 14:05:23');

-- --------------------------------------------------------

--
-- Table structure for table `tuition_payment`
--

CREATE TABLE `tuition_payment` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tuition_payment`
--

INSERT INTO `tuition_payment` (`id`, `student_id`, `amount`) VALUES
(47, 94, 1000.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tuition_payment`
--
ALTER TABLE `tuition_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `tuition_payment`
--
ALTER TABLE `tuition_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tuition_payment`
--
ALTER TABLE `tuition_payment`
  ADD CONSTRAINT `tuition_payment_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
