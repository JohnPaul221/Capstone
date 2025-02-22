    -- phpMyAdmin SQL Dump
    -- version 5.2.1
    -- https://www.phpmyadmin.net/
    --
    -- Host: 127.0.0.1
    -- Generation Time: Feb 22, 2025 at 06:27 AM
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
    -- Table structure for table `courses`
    --

    CREATE TABLE `courses` (
      `id` int(11) NOT NULL,
      `course_name` varchar(100) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    -- --------------------------------------------------------

    --
    -- Table structure for table `students`
    --

    CREATE TABLE `students` (
      `id` int(11) NOT NULL,
      `name` varchar(100) NOT NULL,
      `email` varchar(100) NOT NULL,
      `course_id` int(11) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    --
    -- Indexes for dumped tables
    --

    --
    -- Indexes for table `courses`
    --
    ALTER TABLE `courses`
      ADD PRIMARY KEY (`id`);

    --
    -- Indexes for table `students`
    --
    ALTER TABLE `students`
      ADD PRIMARY KEY (`id`),
      ADD KEY `course_id` (`course_id`);

    --
    -- AUTO_INCREMENT for dumped tables
    --

    --
    -- AUTO_INCREMENT for table `courses`
    --
    ALTER TABLE `courses`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

    --
    -- AUTO_INCREMENT for table `students`
    --
    ALTER TABLE `students`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

    --
    -- Constraints for dumped tables
    --

    --
    -- Constraints for table `students`
    --
    ALTER TABLE `students`
      ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    COMMIT;

    /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
    /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
    /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
