-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2022 at 02:43 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `todolist`
--

-- --------------------------------------------------------

--
-- Table structure for table `subtasks`
--

CREATE TABLE `subtasks` (
  `Id` int(255) NOT NULL,
  `subTaskId` varchar(20) NOT NULL,
  `taskId` varchar(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `createdDate` date NOT NULL,
  `createdTime` time NOT NULL,
  `dueDate` date NOT NULL,
  `state` varchar(9) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `subtasks`
--

INSERT INTO `subtasks` (`Id`, `subTaskId`, `taskId`, `title`, `createdDate`, `createdTime`, `dueDate`, `state`, `deleted_at`) VALUES
(8, 'STsk1667495823730', 'Tsk1667492308714', 'Complete the Delete a row API', '2022-11-03', '06:17:03', '2022-11-04', 'Pending', '2022-11-03 19:59:27'),
(9, 'STsk1667505637777', 'Tsk1667492308714', 'Complete the Update API', '2022-11-03', '09:00:37', '2022-11-15', 'Pending', '2022-11-03 04:27:35'),
(10, 'STsk1667508359911', 'Tsk1667492308714', 'Complete the Marking API', '2022-11-03', '09:45:59', '2022-11-08', 'Completed', '2022-11-04 00:23:47'),
(11, 'STsk1667514865228', 'Tsk1667514786168', 'Test the Create API', '2022-11-03', '11:34:25', '2022-11-01', 'Pending', '2022-11-04 07:27:17'),
(12, 'STsk1667514913471', 'Tsk1667514786168', 'Test the Update API', '2022-11-03', '11:35:13', '2022-11-18', 'Completed', NULL),
(13, 'STsk1667515055443', 'Tsk1667514786168', 'Test the Delete API', '2022-11-03', '11:37:35', '2022-11-23', 'Completed', '2022-11-04 00:25:46');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `Id` int(255) NOT NULL,
  `taskId` varchar(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `createdDate` date NOT NULL,
  `createdTime` time NOT NULL,
  `dueDate` date NOT NULL,
  `state` varchar(9) NOT NULL,
  `subTasks` int(20) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`Id`, `taskId`, `title`, `createdDate`, `createdTime`, `dueDate`, `state`, `subTasks`, `deleted_at`) VALUES
(1, 'Tsk1667491', 'Task 1', '2022-11-03', '02:18:28', '2022-11-09', 'Pending', 0, '2022-11-03 04:08:16'),
(2, 'Tsk1667491578613', 'Task 2', '2022-11-03', '03:18:28', '2022-11-27', 'Completed', 0, NULL),
(3, 'Tsk166749172618', 'Task 3', '2022-11-03', '04:18:28', '2022-11-15', 'Completed', 0, NULL),
(4, 'Tsk1667492308714', 'Complete the To Do list API', '2022-11-03', '05:18:28', '2022-11-04', 'Completed', 0, NULL),
(5, 'Tsk1667514786168', 'Test the To Do list API', '2022-11-03', '11:33:06', '2022-11-05', 'Pending', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `subtasks`
--
ALTER TABLE `subtasks`
  MODIFY `Id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `Id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
