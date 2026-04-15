-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2026 at 06:43 PM
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
-- Database: `lanja_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `announcement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `visibility_scope` enum('Members','Dept Heads','Everyone') NOT NULL,
  `announce_title` varchar(50) NOT NULL,
  `announce_body` text NOT NULL,
  `announce_expiry` datetime NOT NULL,
  `allow_opt_out` tinyint(1) NOT NULL,
  `announce_delivery` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `attend_status` enum('Present','Absent','Late','Excused') NOT NULL,
  `check_in_time` datetime NOT NULL,
  `taken_by` int(11) NOT NULL,
  `taken_at` varchar(100) NOT NULL,
  `notes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `user_id`, `event_id`, `attend_status`, `check_in_time`, `taken_by`, `taken_at`, `notes`) VALUES
(1, 13, 2, 'Absent', '2026-03-04 04:41:27', 18, '2026-03-04 04:41:27', ''),
(2, 1, 2, 'Absent', '2026-03-04 04:41:27', 18, '2026-03-04 04:41:27', ''),
(3, 18, 2, 'Absent', '2026-03-04 04:41:27', 18, '2026-03-04 04:41:27', ''),
(4, 6, 2, 'Absent', '2026-03-04 04:41:28', 18, '2026-03-04 04:41:28', ''),
(5, 17, 2, 'Absent', '2026-03-04 04:41:28', 18, '2026-03-04 04:41:28', ''),
(6, 7, 2, 'Absent', '2026-03-04 04:41:28', 18, '2026-03-04 04:41:28', ''),
(7, 8, 2, 'Absent', '2026-03-04 04:41:28', 18, '2026-03-04 04:41:28', ''),
(8, 9, 2, 'Absent', '2026-03-04 04:41:28', 18, '2026-03-04 04:41:28', ''),
(9, 14, 2, 'Absent', '2026-03-04 04:41:28', 18, '2026-03-04 04:41:28', ''),
(10, 3, 2, 'Absent', '2026-03-04 04:41:28', 18, '2026-03-04 04:41:28', ''),
(11, 16, 2, 'Absent', '2026-03-04 04:41:28', 18, '2026-03-04 04:41:28', ''),
(12, 15, 2, 'Absent', '2026-03-04 04:41:29', 18, '2026-03-04 04:41:29', ''),
(13, 4, 2, 'Absent', '2026-03-04 04:41:30', 18, '2026-03-04 04:41:30', ''),
(14, 2, 2, 'Absent', '2026-03-04 04:41:30', 18, '2026-03-04 04:41:30', ''),
(15, 10, 2, 'Absent', '2026-03-04 04:41:31', 18, '2026-03-04 04:41:31', ''),
(16, 5, 2, 'Absent', '2026-03-04 04:41:31', 18, '2026-03-04 04:41:31', ''),
(17, 11, 2, 'Absent', '2026-03-04 04:41:31', 18, '2026-03-04 04:41:31', ''),
(18, 12, 2, 'Absent', '2026-03-04 04:41:31', 18, '2026-03-04 04:41:31', ''),
(19, 13, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(20, 1, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(21, 18, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(22, 6, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(23, 17, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(24, 7, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(25, 8, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(26, 9, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(27, 14, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(28, 3, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(29, 16, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(30, 15, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(31, 4, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(32, 2, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(33, 10, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(34, 5, 1, 'Absent', '2026-03-01 17:38:01', 18, '2026-03-01 17:38:01', ''),
(35, 11, 1, 'Absent', '2026-03-01 17:38:02', 18, '2026-03-01 17:38:02', ''),
(36, 12, 1, 'Absent', '2026-03-01 17:38:02', 18, '2026-03-01 17:38:02', ''),
(37, 20, 2, 'Absent', '2026-03-04 04:41:28', 18, '2026-03-04 04:41:28', ''),
(38, 19, 2, 'Absent', '2026-03-04 04:41:29', 18, '2026-03-04 04:41:29', '');

-- --------------------------------------------------------

--
-- Table structure for table `auditlog`
--

CREATE TABLE `auditlog` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` enum('Create','Update','Delete','Archive') NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `before_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`before_json`)),
  `after_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`after_json`)),
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auditlog`
--

INSERT INTO `auditlog` (`log_id`, `user_id`, `action`, `entity_type`, `entity_id`, `before_json`, `after_json`, `occurred_at`) VALUES
(1, 18, 'Update', 'attendance', 3, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"Test\"}', '2026-03-01 16:12:35'),
(2, 18, 'Update', 'attendance', 3, '{\"attend_status\":\"Present\",\"notes\":\"Test\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:42'),
(3, 18, 'Update', 'attendance', 1, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:50'),
(4, 18, 'Update', 'attendance', 2, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:50'),
(5, 18, 'Update', 'attendance', 3, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:50'),
(6, 18, 'Update', 'attendance', 4, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:50'),
(7, 18, 'Update', 'attendance', 5, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:50'),
(8, 18, 'Update', 'attendance', 6, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:50'),
(9, 18, 'Update', 'attendance', 7, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:50'),
(10, 18, 'Update', 'attendance', 8, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:50'),
(11, 18, 'Update', 'attendance', 9, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:50'),
(12, 18, 'Update', 'attendance', 10, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:50'),
(13, 18, 'Update', 'attendance', 11, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:50'),
(14, 18, 'Update', 'attendance', 12, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:51'),
(15, 18, 'Update', 'attendance', 13, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:51'),
(16, 18, 'Update', 'attendance', 14, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:51'),
(17, 18, 'Update', 'attendance', 15, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:51'),
(18, 18, 'Update', 'attendance', 16, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:51'),
(19, 18, 'Update', 'attendance', 17, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:51'),
(20, 18, 'Update', 'attendance', 18, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:12:51'),
(21, 18, 'Update', 'attendance', 1, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:55'),
(22, 18, 'Update', 'attendance', 2, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:55'),
(23, 18, 'Update', 'attendance', 3, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:55'),
(24, 18, 'Update', 'attendance', 4, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:55'),
(25, 18, 'Update', 'attendance', 5, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:55'),
(26, 18, 'Update', 'attendance', 6, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:55'),
(27, 18, 'Update', 'attendance', 7, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:55'),
(28, 18, 'Update', 'attendance', 8, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:55'),
(29, 18, 'Update', 'attendance', 9, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:55'),
(30, 18, 'Update', 'attendance', 10, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:56'),
(31, 18, 'Update', 'attendance', 11, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:56'),
(32, 18, 'Update', 'attendance', 12, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:56'),
(33, 18, 'Update', 'attendance', 13, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:56'),
(34, 18, 'Update', 'attendance', 14, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:56'),
(35, 18, 'Update', 'attendance', 15, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:56'),
(36, 18, 'Update', 'attendance', 16, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:56'),
(37, 18, 'Update', 'attendance', 17, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:56'),
(38, 18, 'Update', 'attendance', 18, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:12:56'),
(39, 18, 'Update', 'attendance', 19, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:13:34'),
(40, 18, 'Update', 'attendance', 19, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:24'),
(41, 18, 'Update', 'attendance', 20, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:24'),
(42, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:24'),
(43, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:24'),
(44, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:24'),
(45, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:25'),
(46, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:25'),
(47, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:25'),
(48, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:25'),
(49, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:25'),
(50, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:25'),
(51, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:25'),
(52, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:25'),
(53, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:25'),
(54, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:26'),
(55, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:26'),
(56, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:26'),
(57, 18, 'Create', 'attendance', 1, '[]', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:26'),
(58, 18, 'Update', 'attendance', 19, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:30'),
(59, 18, 'Update', 'attendance', 20, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:30'),
(60, 18, 'Update', 'attendance', 21, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:30'),
(61, 18, 'Update', 'attendance', 22, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:30'),
(62, 18, 'Update', 'attendance', 23, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:31'),
(63, 18, 'Update', 'attendance', 24, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:31'),
(64, 18, 'Update', 'attendance', 25, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:31'),
(65, 18, 'Update', 'attendance', 26, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:31'),
(66, 18, 'Update', 'attendance', 27, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:31'),
(67, 18, 'Update', 'attendance', 28, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:31'),
(68, 18, 'Update', 'attendance', 29, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:31'),
(69, 18, 'Update', 'attendance', 30, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:31'),
(70, 18, 'Update', 'attendance', 31, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:31'),
(71, 18, 'Update', 'attendance', 32, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:32'),
(72, 18, 'Update', 'attendance', 33, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:32'),
(73, 18, 'Update', 'attendance', 34, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:32'),
(74, 18, 'Update', 'attendance', 35, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:32'),
(75, 18, 'Update', 'attendance', 36, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:14:32'),
(76, 18, 'Update', 'attendance', 19, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:35'),
(77, 18, 'Update', 'attendance', 20, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:35'),
(78, 18, 'Update', 'attendance', 21, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:35'),
(79, 18, 'Update', 'attendance', 22, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:35'),
(80, 18, 'Update', 'attendance', 23, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:36'),
(81, 18, 'Update', 'attendance', 24, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:36'),
(82, 18, 'Update', 'attendance', 25, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:36'),
(83, 18, 'Update', 'attendance', 26, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:36'),
(84, 18, 'Update', 'attendance', 27, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:36'),
(85, 18, 'Update', 'attendance', 28, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:36'),
(86, 18, 'Update', 'attendance', 29, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:36'),
(87, 18, 'Update', 'attendance', 30, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:36'),
(88, 18, 'Update', 'attendance', 31, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:36'),
(89, 18, 'Update', 'attendance', 32, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:36'),
(90, 18, 'Update', 'attendance', 33, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:36'),
(91, 18, 'Update', 'attendance', 34, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:37'),
(92, 18, 'Update', 'attendance', 35, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:37'),
(93, 18, 'Update', 'attendance', 36, '{\"attend_status\":\"Present\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '2026-03-01 16:14:37'),
(94, 18, 'Update', 'attendance', 4, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Late\",\"notes\":\"\"}', '2026-03-01 16:21:03'),
(95, 18, 'Update', 'attendance', 6, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Excused\",\"notes\":\"\"}', '2026-03-01 16:21:03'),
(96, 18, 'Update', 'attendance', 8, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Late\",\"notes\":\"\"}', '2026-03-01 16:21:03'),
(97, 18, 'Update', 'attendance', 1, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:55'),
(98, 18, 'Update', 'attendance', 2, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:56'),
(99, 18, 'Update', 'attendance', 4, '{\"attend_status\":\"Late\",\"notes\":\"\"}', '{\"attend_status\":\"Late\",\"notes\":\"Late by 7 minutes\"}', '2026-03-01 16:21:56'),
(100, 18, 'Update', 'attendance', 7, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:56'),
(101, 18, 'Update', 'attendance', 8, '{\"attend_status\":\"Late\",\"notes\":\"\"}', '{\"attend_status\":\"Late\",\"notes\":\"Late by 32 minutes\"}', '2026-03-01 16:21:56'),
(102, 18, 'Update', 'attendance', 9, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:56'),
(103, 18, 'Update', 'attendance', 10, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:56'),
(104, 18, 'Update', 'attendance', 11, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:56'),
(105, 18, 'Update', 'attendance', 12, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:57'),
(106, 18, 'Update', 'attendance', 13, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:57'),
(107, 18, 'Update', 'attendance', 14, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:57'),
(108, 18, 'Update', 'attendance', 15, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:57'),
(109, 18, 'Update', 'attendance', 16, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:57'),
(110, 18, 'Update', 'attendance', 17, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:57'),
(111, 18, 'Update', 'attendance', 18, '{\"attend_status\":\"Absent\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"notes\":\"\"}', '2026-03-01 16:21:57'),
(112, 18, 'Update', 'attendance', 1, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:44'),
(113, 18, 'Update', 'attendance', 2, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-01 16:32:45'),
(114, 18, 'Update', 'attendance', 4, '{\"attend_status\":\"Late\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"Late by 7 minutes\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"Late by 7 minutes\"}', '2026-03-01 16:32:45'),
(115, 18, 'Update', 'attendance', 6, '{\"attend_status\":\"Excused\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:45'),
(116, 18, 'Update', 'attendance', 7, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:45'),
(117, 18, 'Update', 'attendance', 8, '{\"attend_status\":\"Late\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"Late by 32 minutes\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"Late by 32 minutes\"}', '2026-03-01 16:32:45'),
(118, 18, 'Update', 'attendance', 9, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:45'),
(119, 18, 'Update', 'attendance', 10, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:45'),
(120, 18, 'Update', 'attendance', 11, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:45'),
(121, 18, 'Update', 'attendance', 12, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:46'),
(122, 18, 'Update', 'attendance', 13, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:46'),
(123, 18, 'Update', 'attendance', 14, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:46'),
(124, 18, 'Update', 'attendance', 15, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:46'),
(125, 18, 'Update', 'attendance', 16, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:46'),
(126, 18, 'Update', 'attendance', 17, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:46'),
(127, 18, 'Update', 'attendance', 18, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"\"}', '2026-03-01 16:32:46'),
(128, 18, 'Update', 'attendance', 4, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"Late by 7 minutes\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:36:52\",\"notes\":\"\"}', '2026-03-01 16:36:52'),
(129, 18, 'Update', 'attendance', 8, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-01-23 17:21:00\",\"notes\":\"Late by 32 minutes\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:36:52\",\"notes\":\"\"}', '2026-03-01 16:36:52'),
(130, 18, 'Update', 'attendance', 1, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:36:52\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 14:39:00\",\"notes\":\"Arrived 9 minutes late\"}', '2026-03-01 16:37:24'),
(131, 18, 'Update', 'attendance', 1, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 14:39:00\",\"notes\":\"Arrived 9 minutes late\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '2026-03-01 16:37:50'),
(132, 18, 'Update', 'attendance', 1, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:52'),
(133, 18, 'Update', 'attendance', 2, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:52'),
(134, 18, 'Update', 'attendance', 3, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 12:32:00\",\"notes\":\"Arrived 2 minutes late\"}', '2026-03-02 03:36:53'),
(135, 18, 'Update', 'attendance', 4, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:53'),
(136, 18, 'Create', 'attendance', 2, '[]', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:53'),
(137, 18, 'Update', 'attendance', 5, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:53'),
(138, 18, 'Update', 'attendance', 6, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:53'),
(139, 18, 'Update', 'attendance', 7, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:53'),
(140, 18, 'Update', 'attendance', 8, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Late\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:53'),
(141, 18, 'Update', 'attendance', 9, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:53'),
(142, 18, 'Update', 'attendance', 10, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:53'),
(143, 18, 'Update', 'attendance', 11, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:54'),
(144, 18, 'Update', 'attendance', 12, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:54'),
(145, 18, 'Create', 'attendance', 2, '[]', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:54'),
(146, 18, 'Update', 'attendance', 14, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:50\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:54'),
(147, 18, 'Update', 'attendance', 15, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:51\",\"notes\":\"\"}', '{\"attend_status\":\"Excused\",\"check_in_time\":\"2026-03-02 04:36:54\",\"notes\":\"\"}', '2026-03-02 03:36:54'),
(148, 18, 'Update', 'attendance', 16, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:51\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:54'),
(149, 18, 'Update', 'attendance', 17, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:51\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:54'),
(150, 18, 'Update', 'attendance', 18, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-01 17:37:51\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-02 03:36:54'),
(151, 18, 'Update', 'attendance', 1, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '2026-03-02 03:38:13'),
(152, 18, 'Update', 'attendance', 2, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '2026-03-02 03:38:13'),
(153, 18, 'Update', 'attendance', 3, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 12:32:00\",\"notes\":\"Arrived 2 minutes late\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '2026-03-02 03:38:13'),
(154, 18, 'Update', 'attendance', 4, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '2026-03-02 03:38:13'),
(155, 18, 'Update', 'attendance', 37, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '2026-03-02 03:38:13'),
(156, 18, 'Update', 'attendance', 5, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '2026-03-02 03:38:13'),
(157, 18, 'Update', 'attendance', 6, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '2026-03-02 03:38:13'),
(158, 18, 'Update', 'attendance', 7, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '2026-03-02 03:38:13'),
(159, 18, 'Update', 'attendance', 8, '{\"attend_status\":\"Late\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '2026-03-02 03:38:14'),
(160, 18, 'Update', 'attendance', 9, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '2026-03-02 03:38:14'),
(161, 18, 'Update', 'attendance', 10, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '2026-03-02 03:38:14'),
(162, 18, 'Update', 'attendance', 11, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '2026-03-02 03:38:14'),
(163, 18, 'Update', 'attendance', 12, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '2026-03-02 03:38:14'),
(164, 18, 'Update', 'attendance', 38, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '2026-03-02 03:38:14'),
(165, 18, 'Update', 'attendance', 14, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '2026-03-02 03:38:14'),
(166, 18, 'Update', 'attendance', 15, '{\"attend_status\":\"Excused\",\"check_in_time\":\"2026-03-02 04:36:54\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '2026-03-02 03:38:14'),
(167, 18, 'Update', 'attendance', 16, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '2026-03-02 03:38:14'),
(168, 18, 'Update', 'attendance', 17, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:15\",\"notes\":\"\"}', '2026-03-02 03:38:15'),
(169, 18, 'Update', 'attendance', 18, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:15\",\"notes\":\"\"}', '2026-03-02 03:38:15'),
(170, 18, 'Update', 'attendance', 1, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:01'),
(171, 18, 'Update', 'attendance', 2, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:01'),
(172, 18, 'Update', 'attendance', 3, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:01'),
(173, 18, 'Update', 'attendance', 4, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:03:00\",\"notes\":\"\"}', '2026-03-04 03:41:01'),
(174, 18, 'Update', 'attendance', 37, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:01'),
(175, 18, 'Update', 'attendance', 5, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:02'),
(176, 18, 'Update', 'attendance', 7, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:13\",\"notes\":\"\"}', '{\"attend_status\":\"Late\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:02'),
(177, 18, 'Update', 'attendance', 8, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '{\"attend_status\":\"Excused\",\"check_in_time\":\"2026-03-04 04:41:02\",\"notes\":\"\"}', '2026-03-04 03:41:02'),
(178, 18, 'Update', 'attendance', 9, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:02'),
(179, 18, 'Update', 'attendance', 10, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:02'),
(180, 18, 'Update', 'attendance', 11, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:02'),
(181, 18, 'Update', 'attendance', 12, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:02:00\",\"notes\":\"\"}', '2026-03-04 03:41:02'),
(182, 18, 'Update', 'attendance', 38, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:03'),
(183, 18, 'Update', 'attendance', 13, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:03'),
(184, 18, 'Update', 'attendance', 14, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:03'),
(185, 18, 'Update', 'attendance', 15, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:03'),
(186, 18, 'Update', 'attendance', 16, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:14\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:03'),
(187, 18, 'Update', 'attendance', 17, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:15\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 05:05:00\",\"notes\":\"\"}', '2026-03-04 03:41:03'),
(188, 18, 'Update', 'attendance', 18, '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-02 04:38:15\",\"notes\":\"\"}', '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '2026-03-04 03:41:03'),
(189, 18, 'Update', 'attendance', 1, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:27\",\"notes\":\"\"}', '2026-03-04 03:41:27'),
(190, 18, 'Update', 'attendance', 2, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:27\",\"notes\":\"\"}', '2026-03-04 03:41:27'),
(191, 18, 'Update', 'attendance', 3, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:27\",\"notes\":\"\"}', '2026-03-04 03:41:28'),
(192, 18, 'Update', 'attendance', 4, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:03:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:28\",\"notes\":\"\"}', '2026-03-04 03:41:28'),
(193, 18, 'Update', 'attendance', 37, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:28\",\"notes\":\"\"}', '2026-03-04 03:41:28'),
(194, 18, 'Update', 'attendance', 5, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:28\",\"notes\":\"\"}', '2026-03-04 03:41:28'),
(195, 18, 'Update', 'attendance', 7, '{\"attend_status\":\"Late\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:28\",\"notes\":\"\"}', '2026-03-04 03:41:28'),
(196, 18, 'Update', 'attendance', 8, '{\"attend_status\":\"Excused\",\"check_in_time\":\"2026-03-04 04:41:02\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:28\",\"notes\":\"\"}', '2026-03-04 03:41:28'),
(197, 18, 'Update', 'attendance', 9, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:28\",\"notes\":\"\"}', '2026-03-04 03:41:28'),
(198, 18, 'Update', 'attendance', 10, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:28\",\"notes\":\"\"}', '2026-03-04 03:41:28'),
(199, 18, 'Update', 'attendance', 11, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:28\",\"notes\":\"\"}', '2026-03-04 03:41:28'),
(200, 18, 'Update', 'attendance', 12, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:02:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:29\",\"notes\":\"\"}', '2026-03-04 03:41:29'),
(201, 18, 'Update', 'attendance', 38, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:29\",\"notes\":\"\"}', '2026-03-04 03:41:29'),
(202, 18, 'Update', 'attendance', 13, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:30\",\"notes\":\"\"}', '2026-03-04 03:41:30'),
(203, 18, 'Update', 'attendance', 14, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:30\",\"notes\":\"\"}', '2026-03-04 03:41:31'),
(204, 18, 'Update', 'attendance', 15, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:31\",\"notes\":\"\"}', '2026-03-04 03:41:31'),
(205, 18, 'Update', 'attendance', 16, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:31\",\"notes\":\"\"}', '2026-03-04 03:41:31'),
(206, 18, 'Update', 'attendance', 17, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 05:05:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:31\",\"notes\":\"\"}', '2026-03-04 03:41:31'),
(207, 18, 'Update', 'attendance', 18, '{\"attend_status\":\"Present\",\"check_in_time\":\"2026-01-23 00:00:00\",\"notes\":\"\"}', '{\"attend_status\":\"Absent\",\"check_in_time\":\"2026-03-04 04:41:31\",\"notes\":\"\"}', '2026-03-04 03:41:32');

-- --------------------------------------------------------

--
-- Table structure for table `calendarevent`
--

CREATE TABLE `calendarevent` (
  `event_id` int(11) NOT NULL,
  `event_title` varchar(50) NOT NULL,
  `event_desc` text NOT NULL,
  `event_location` varchar(250) NOT NULL,
  `event_date` datetime NOT NULL,
  `recurring` enum('Daily','Weekly','Monthly','Annually') DEFAULT NULL,
  `iterations` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calendarevent`
--

INSERT INTO `calendarevent` (`event_id`, `event_title`, `event_desc`, `event_location`, `event_date`, `recurring`, `iterations`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'First Day of Class', 'The first day of Spring Term begins', 'D2L', '2026-01-20 00:00:00', NULL, NULL, '2026-01-08 19:42:45', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Team Meeting', 'Gather to discuss future developments', 'Zoom', '2026-01-23 12:30:00', '', 15, '2026-01-08 19:48:31', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dept_name` varchar(50) NOT NULL,
  `dept_desc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_id`, `user_id`, `dept_name`, `dept_desc`) VALUES
(1, 17, 'Reporting Department', 'The Reporting Department is responsible for collecting organizing analyzing and consolidating organizational data into accurate reports');

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE `document` (
  `document_id` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `visibility_scope` enum('Members','Dept Heads','Everyone') NOT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `doc_title` varchar(50) NOT NULL,
  `stored_url` varchar(250) NOT NULL,
  `archived` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `formresponse`
--

CREATE TABLE `formresponse` (
  `response_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `form_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`form_response`)),
  `form_status` enum('Pending','Reviewed','Finalized') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `formtemplate`
--

CREATE TABLE `formtemplate` (
  `template_id` int(11) NOT NULL,
  `temp_title` varchar(50) NOT NULL,
  `temp_desc` text NOT NULL,
  `temp_status` enum('Draft','Active','Archived') NOT NULL,
  `form_questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`form_questions`)),
  `form_deadline` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membersuggestion`
--

CREATE TABLE `membersuggestion` (
  `suggestion_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `suggestion_text` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Reviewed','Resolved') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membersuggestion`
--

INSERT INTO `membersuggestion` (`suggestion_id`, `user_id`, `suggestion_text`, `attachment_path`, `status`, `created_at`, `resolved_by`) VALUES
(9, 18, 'Test', NULL, 'Pending', '2026-04-04 02:01:48', NULL),
(10, 18, 'This is a test', NULL, 'Resolved', '2026-04-04 02:01:55', 18);

-- --------------------------------------------------------

--
-- Table structure for table `passwordresettoken`
--

CREATE TABLE `passwordresettoken` (
  `reset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(15) NOT NULL,
  `reset_success` tinyint(1) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `permission_id` int(11) NOT NULL,
  `perm_title` varchar(50) NOT NULL,
  `perm_desc` text NOT NULL,
  `perm_resource` varchar(100) NOT NULL,
  `perm_crud` enum('Create','Read','Update','Delete') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`permission_id`, `perm_title`, `perm_desc`, `perm_resource`, `perm_crud`) VALUES
(1, 'Create User Account', 'Create a new member account by entering required credentials and assigning an initial active status', 'User', 'Create'),
(2, 'Update User Account Status', 'Freeze or reactivate user accounts immediately revoking or restoring login access while preserving data', 'User', 'Update'),
(3, 'View User Accounts', 'View all user accounts and related metadata for monitoring and management', 'User', 'Read'),
(4, 'View User Activity Logs', 'View user engagement metrics including login frequency last login time and site activity', 'User', 'Read'),
(5, 'Receive Message Requests', 'Receive and view membership requests submitted by visitors via contact form', 'Suggestion', 'Read'),
(6, 'Respond to Requests', 'Accept or decline visitor membership requests', 'Suggestion', 'Update'),
(7, 'Cancel Calendar Event', 'Cancel any calendar event or recurring series across the organization', 'Calendar Event', 'Update'),
(8, 'Upload Document', 'Upload documents within size and performance constraints', 'Document', 'Create'),
(9, 'Set Document Visibility', 'Define which roles or departments can view and download a document', 'Document', 'Update'),
(10, 'View Documents', 'View and download documents based on assigned visibility permissions', 'Document', 'Read'),
(11, 'Archive Document', 'Archive documents so they are no longer active but retained for historical reference', 'Document', 'Update'),
(12, 'Create Calendar Event', 'Create calendar events that are visible to selected roles within seconds of posting', 'Calendar Event', 'Create'),
(13, 'Update Calendar Event', 'Edit calendar events according to defined modification permissions', 'Calendar Event', 'Update'),
(14, 'Publish Announcement', 'Create and publish announcements with title body and expiry date', 'Announcement', 'Create'),
(15, 'Update Announcement', 'Edit active announcements while expired announcements auto-hide', 'Announcement', 'Update'),
(16, 'View Announcement Archive', 'Archive any announcement across the organization to preserve history without deletion', 'Announcement', 'Update'),
(17, 'View Reports', 'View monthly aggregated reports with visualizations and qualitative interpretations', 'Form Response', 'Read'),
(18, 'Cancel Department Calendar Event', 'Cancel calendar events they created or in their department only', 'Calendar Event', 'Update'),
(19, 'View Own Profile', 'View personal profile information including name contact details role and available navigation options', 'User', 'Read'),
(20, 'Update Own Profile', 'Update personal profile information such as name email phone number and address', 'User', 'Update'),
(21, 'Submit Form Response', 'Complete and submit a survey or form related to member involvement', 'Form Response', 'Create'),
(22, 'Update Own Form Response', 'Edit a previously submitted survey response before the submission deadline to correct errors', 'Form Response', 'Update'),
(23, 'View Own Form Responses', 'View a list of previously submitted form responses and open individual submissions in read-only mode', 'Form Response', 'Read'),
(24, 'View Member Announcements', 'View published announcements intended for members', 'Announcement', 'Read'),
(25, 'Submit Message Suggestions', 'Visitors and all members can send membership requests via contact form', 'Suggestion', 'Create'),
(26, 'Reset Password', 'Reset account password using a time-limited tokenized email link with enforced password strength rules', 'User', 'Update'),
(27, 'View Calendar Events', 'View all calendar events across roles with consistent formatting', 'Calendar Event', 'Read'),
(28, 'View Announcements', 'View announcements and their current status', 'Announcement', 'Read'),
(29, 'View System Logs', 'View system log entries for monitoring and troubleshooting', 'Audit Log', 'Read'),
(30, 'Export System Logs', 'Generate and export system logs as files for auditing and archival purposes', 'Audit Log', 'Read'),
(31, 'Assign User Roles', 'Assign or change user roles with immediate effect and audit logging of changes', 'User Role', 'Update'),
(32, 'Grant Role Permissions', 'Grant or modify permissions associated with roles to control system access', 'Role Permission', 'Update'),
(33, 'View Performance Metrics', 'View system performance metrics and operational data through an administrative dashboard', 'Audit Log', 'Read'),
(34, 'View System Alerts', 'View system alerts and notifications related to failures or downtime within the past 90 days', 'Audit Log', 'Read');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `role_desc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role_name`, `role_desc`) VALUES
(1, 'President', 'Oversees the entire organization makes final decisions and ensures overall system and organizational integrity'),
(2, 'Department Head', 'Manages department operations reporting and member activities and serves as the primary liaison to the President'),
(3, 'Member', 'Participates in organizational activities submits required forms and engages with events and communications'),
(4, 'Admin', 'Maintains the technical health security and configuration of the system without participating in organizational decision-making');

-- --------------------------------------------------------

--
-- Table structure for table `rolechangelog`
--

CREATE TABLE `rolechangelog` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `old_role_id` int(11) DEFAULT NULL,
  `new_role_id` int(11) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rolechangelog`
--

INSERT INTO `rolechangelog` (`log_id`, `user_id`, `admin_id`, `old_role_id`, `new_role_id`, `changed_at`) VALUES
(1, 18, 18, 4, 2, '2026-03-01 15:37:58'),
(2, 18, 17, 2, 4, '2026-03-01 15:58:08'),
(3, 18, 17, 2, 2, '2026-03-01 15:58:08'),
(4, 18, 17, 2, 3, '2026-03-01 15:58:08'),
(5, 18, 17, 2, 1, '2026-03-01 15:58:09'),
(6, 19, 18, 4, 4, '2026-03-01 16:38:19'),
(7, 19, 18, 4, 2, '2026-03-01 16:38:19'),
(8, 19, 18, 4, 3, '2026-03-01 16:38:20'),
(9, 19, 18, 4, 1, '2026-03-01 16:38:20'),
(10, 20, 18, 4, 4, '2026-03-01 16:38:24'),
(11, 20, 18, 4, 2, '2026-03-01 16:38:24'),
(12, 20, 18, 4, 3, '2026-03-01 16:38:25'),
(13, 20, 18, 4, 1, '2026-03-01 16:38:25'),
(14, 17, 18, 2, 4, '2026-03-04 03:42:38'),
(15, 17, 18, 2, 2, '2026-03-04 03:42:38'),
(16, 17, 18, 2, 3, '2026-03-04 03:42:38'),
(17, 17, 18, 2, 1, '2026-03-04 03:42:38');

-- --------------------------------------------------------

--
-- Table structure for table `rolepermission`
--

CREATE TABLE `rolepermission` (
  `roleperm_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rolepermission`
--

INSERT INTO `rolepermission` (`roleperm_id`, `permission_id`, `role_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(10, 10, 1),
(11, 11, 1),
(12, 12, 1),
(13, 13, 1),
(14, 14, 1),
(15, 15, 1),
(16, 16, 1),
(17, 17, 1),
(18, 8, 2),
(19, 9, 2),
(20, 10, 2),
(21, 11, 2),
(22, 12, 2),
(23, 13, 2),
(24, 14, 2),
(25, 15, 2),
(26, 16, 2),
(27, 17, 2),
(28, 18, 2),
(29, 19, 3),
(30, 20, 3),
(31, 21, 3),
(32, 22, 3),
(33, 23, 3),
(34, 24, 3),
(35, 25, 1),
(36, 26, 1),
(37, 25, 2),
(38, 26, 2),
(39, 25, 3),
(40, 26, 3),
(41, 25, 4),
(42, 26, 4),
(43, 27, 1),
(44, 28, 1),
(45, 27, 2),
(46, 28, 2),
(47, 27, 3),
(48, 28, 3),
(49, 29, 4),
(50, 30, 4),
(51, 31, 4),
(52, 32, 4),
(53, 33, 4),
(54, 34, 4);

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `session_id` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `revoked_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`session_id`, `user_id`, `expires_at`, `created_at`, `last_seen_at`, `revoked_at`) VALUES
('1873052b5d9f5a1039013945fc5a30f40c8464eccd3f5fcff8b53c0924f3b45b', 18, '2026-03-21 23:18:17', '2026-03-21 23:18:12', '0000-00-00 00:00:00', '2026-03-21 23:18:17'),
('2b8a14400a9a79b84d16c17fec88c785e612f95774cf4e96fa3113d095dfaf49', 18, '2026-03-02 03:31:18', '2026-03-02 03:04:51', '0000-00-00 00:00:00', '2026-03-02 03:31:18'),
('3a5357bf4ad649c767848e4298857422c65348e76063845adb94fdfb59ce1ac9', 18, '2026-03-22 00:10:28', '2026-03-21 23:18:27', '0000-00-00 00:00:00', '2026-03-22 00:10:28'),
('46c00544364d2df1eeede92ab8cf9cd17fcae574c8d7a0effe3c77f14e003a38', 18, '2026-04-10 20:11:56', '2026-04-03 14:11:56', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
('6bd16e1321dbe35795150ab0505ea669fb9ca386df787cae89f54bed522562d0', 18, '2026-04-19 21:43:29', '2026-04-12 15:43:29', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
('7d7051dc46533f0a4044ae29b4054b496e4a32935aa20e51fbfbb3f801b4d047', 18, '2026-03-04 03:42:51', '2026-03-04 03:40:31', '0000-00-00 00:00:00', '2026-03-04 03:42:51'),
('90a9b9cabc472641c7920697e4fb1dc401d7e506b4e1bc7b4f63e12a44749ab1', 18, '2026-03-09 08:35:16', '2026-03-02 03:35:16', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
('95d2def7f2b4862c2817ac4ffc093901d929cedef58a410b5c2bb8bb5ff12e2c', 18, '2026-03-02 02:30:46', '2026-03-01 16:15:24', '0000-00-00 00:00:00', '2026-03-02 02:30:46'),
('99335285a34111a3a655501b73c20d76dd6261a315cbb901f0e1ad3bd2a4c003', 18, '2026-03-02 03:02:52', '2026-03-02 02:31:06', '0000-00-00 00:00:00', '2026-03-02 03:02:52'),
('d72d15d2b7c4ee37cf16eeadc693f3b94577744fe8f82d1df828d80c630dbecb', 18, '2026-03-04 03:39:31', '2026-03-04 03:39:28', '0000-00-00 00:00:00', '2026-03-04 03:39:31'),
('da2cab18b3fc3c97cd9fa28456847f30177e1bd2563d5e5b8161b60c1ee156c3', 18, '2026-03-11 08:50:11', '2026-03-04 03:50:11', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
('e8466a4d7a4490f0537d7977dab07b9a81f673946f43ac24d1f9c27ee89a405b', 18, '2026-03-01 16:14:45', '2026-03-01 16:02:13', '0000-00-00 00:00:00', '2026-03-01 16:14:45'),
('ee380e1fa9663ec317412905202628ee4832a0826ff5185d0dc0c06a3289540a', 18, '2026-03-01 15:53:06', '2026-03-01 15:04:50', '0000-00-00 00:00:00', '2026-03-01 15:53:06'),
('f37b510861f48d45abebc4ef28a9876692897678f0b1612deb09aa4593efcb6c', 17, '2026-03-01 16:02:04', '2026-03-01 15:53:27', '0000-00-00 00:00:00', '2026-03-01 16:02:04');

-- --------------------------------------------------------

--
-- Table structure for table `suggestion`
--

CREATE TABLE `suggestion` (
  `suggestion_id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `contact_email` varchar(50) NOT NULL,
  `visitor_msg` text NOT NULL,
  `msg_status` enum('Pending','Reviewed','Finalized') NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_phone` varchar(15) NOT NULL,
  `user_address` varchar(200) NOT NULL,
  `password_hashed` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `joined_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `first_name`, `last_name`, `user_email`, `user_phone`, `user_address`, `password_hashed`, `is_active`, `joined_on`, `last_login`, `last_updated`, `updated_by`) VALUES
(1, 'Carie', 'Baig', 'carie_b0009@email.com', '(217) 555-0101', '101 Maple Grove Drive Springfield, IL 62704', 'a331e2c7bff38160c7d834a8ee01a3a307ea9541', 1, '2022-03-15 12:12:45', '2023-07-21 18:55:12', '0000-00-00 00:00:00', NULL),
(2, 'Aliyah', 'Salah', 'sal11390@email.com', '(608) 555-0102', '245 Oak Valley Road Madison, WI 53711', '588c7f7239f15fd0c45fdb08c0bacd47482965d1', 1, '2021-11-02 20:45:30', '2023-01-18 14:23:50', '0000-00-00 00:00:00', NULL),
(3, 'Kamila', 'Nawaz', 'kamkam13506@email.com', '(919) 555-0103', '389 Pine Hill Lane Raleigh, NC 27607', 'e0b38d39df70f519eec195b5865249e29d208c4d', 1, '2020-06-10 16:05:10', '2022-12-22 23:40:05', '0000-00-00 00:00:00', NULL),
(4, 'Samiya', 'Rizzi', 'srizzi@email.com', '(626) 555-0104', '512 Sunset Ridge Avenue Pasadena, CA 91105', 'd44a646c242a7de4dfd318d92170f9250859f4bf', 1, '2023-01-22 15:20:00', '2023-12-15 16:05:45', '0000-00-00 00:00:00', NULL),
(5, 'Roxanne', 'Sumar', 'rsumar@email.com', '(802) 555-0105', '76 Riverstone Court Burlington, VT 05401', '5fce5e4f146b95ac6417eadee8a74b75b2a3b386', 1, '2021-08-05 18:30:25', '2023-04-03 11:50:12', '0000-00-00 00:00:00', NULL),
(6, 'Mina', 'Hashim', 'minahashim@email.com', '(972) 555-0106', '834 Willow Bend Way Plano, TX 75024', '7a0066282501c336828d003175bdb54661f57353', 1, '2022-02-28 14:12:15', '2023-08-10 01:15:35', '0000-00-00 00:00:00', NULL),
(7, 'Mariam', 'Latif', 'latimari@email.com', '(425) 555-0107', '690 Cedar Ridge Drive Bellevue, WA 98008', '11e2574e9a33efb39ceb3f9d046030d05ea13940', 1, '2020-12-16 03:10:50', '2022-10-30 12:05:25', '0000-00-00 00:00:00', NULL),
(8, 'Nadia', 'Maroon', 'mar00nn@email.com', '(614) 555-0108', '918 Meadowbrook Lane Columbus, OH 43221', '058255ad1f0a570e8175783ad49429107c2c8c06', 1, '2021-05-19 11:45:40', '2023-03-12 23:35:50', '0000-00-00 00:00:00', NULL),
(9, 'Lula', 'Mirza', 'mirza_lu@email.com', '(847) 555-0109', '207 Lakeside Parkway Evanston, IL 60202', '01c0ff841cb2055f53dd1a5f22bf6afd02048ed9', 1, '2023-02-10 18:15:30', '2023-12-01 21:40:20', '0000-00-00 00:00:00', NULL),
(10, 'Inaya', 'Sheikh', 'sheikh2005@email.com', '(480) 555-0110', '561 Highland Park Drive Scottsdale, AZ 85255', '06c72080d5e3115ab95c0ae3af325bafb8b460c0', 1, '2021-09-30 15:25:55', '2023-09-18 14:15:00', '0000-00-00 00:00:00', NULL),
(11, 'Jasmine', 'Taha', 'jazzy0267@email.com', '(919) 555-0111', '744 Brookhaven Road Chapel Hill, NC 27516', '3a5577a3b1766e1ce8913473745a246cbc37d32a', 1, '2022-07-07 21:40:20', '2023-06-12 02:55:10', '0000-00-00 00:00:00', NULL),
(12, 'Malay', 'Usami', 'musami89@email.com', '(707) 555-0112', '1205 Redwood Springs Circle Santa Rosa, CA 95404', '856f259020e565d14dceb7ca94ac130dc596e7ec', 1, '2020-03-22 10:55:10', '2022-11-08 18:10:25', '0000-00-00 00:00:00', NULL),
(13, 'Farah', 'Amin', 'faraha@email.com', '(828) 555-0113', '967 Autumn Crest Lane Asheville, NC 28803', '64211502d6a9ecd3eb8b7c91e9886c8aadf67f1e', 1, '2021-12-26 00:05:45', '2023-08-05 21:30:15', '0000-00-00 00:00:00', NULL),
(14, 'Sofie', 'Nasser', 'sofiebee23@email.com', '(609) 555-0114', '29 Stonegate Boulevard Princeton, NJ 08540', 'e298c8e7d3b7a83af9aae2b189b00b1e13a64277', 1, '2021-01-18 20:30:40', '2023-05-25 16:45:35', '0000-00-00 00:00:00', NULL),
(15, 'Fatima', 'Noore', 'f_noore3342@email.com', '(410) 555-0115', '392 Harbor Point Drive Annapolis, MD 21403', '920b9fb1dbf9c13240bf47f644d3b71d6353b065', 1, '2022-09-05 13:15:25', '2023-10-29 12:05:55', '0000-00-00 00:00:00', NULL),
(16, 'Amara', 'Noore', 'a_noore1207@email.com', '(410) 555-0116', '392 Harbor Point Drive Annapolis, MD 21403', '5772fa800ad99dd0cc3c59cdabdacf8f1f906603', 1, '2022-09-05 13:40:00', '2022-09-20 18:30:45', '0000-00-00 00:00:00', NULL),
(17, 'Zoha', 'K', 'kha27882@email.com', '(406) 555-0117', '155 Golden Meadow Drive Bozeman, MT 59718', '55e25e785d2c1cd969086a905da929ce0cc8bf85', 1, '2026-03-01 15:53:28', '2026-03-01 15:53:28', '0000-00-00 00:00:00', NULL),
(18, 'JJ', 'G', 'gil42134@email.com', '(941) 555-0118', '880 Cypress Hollow Road Sarasota, FL 34232', 'b148bc09cb2817b5becbfd48af7d1db1ca696bb4', 1, '2026-04-12 15:43:29', '2026-04-12 15:43:29', '0000-00-00 00:00:00', NULL),
(19, 'Kah', 'O', 'ong92990@email.com', '(970) 555-0119', '602 Juniper Ridge Lane Fort Collins, CO 80525', '50a7097293554de80ba95b1aef309aab63e7f858', 1, '2022-04-09 14:10:50', '2023-09-22 19:50:25', '0000-00-00 00:00:00', NULL),
(20, 'Shan', 'K', 'kat44977@email.com', '(859) 555-0120', '2173 Bluebird Crossing Lexington, KY 40503', 'd51d2230171c9c68db83deb9480f36c19f491268', 1, '2020-11-14 13:35:20', '2022-12-18 16:45:10', '0000-00-00 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `userrole`
--

CREATE TABLE `userrole` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userrole`
--

INSERT INTO `userrole` (`user_id`, `role_id`) VALUES
(1, 3),
(2, 3),
(3, 3),
(4, 3),
(5, 3),
(6, 3),
(7, 3),
(8, 3),
(9, 3),
(10, 3),
(11, 3),
(12, 3),
(13, 3),
(14, 3),
(15, 3),
(16, 3),
(17, 1),
(17, 2),
(17, 3),
(17, 4),
(18, 1),
(18, 2),
(18, 3),
(18, 4),
(19, 1),
(19, 2),
(19, 3),
(19, 4),
(20, 1),
(20, 2),
(20, 3),
(20, 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `taken_by` (`taken_by`);

--
-- Indexes for table `auditlog`
--
ALTER TABLE `auditlog`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `entity_id` (`entity_id`);

--
-- Indexes for table `calendarevent`
--
ALTER TABLE `calendarevent`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `user_id` (`uploaded_by`);

--
-- Indexes for table `formresponse`
--
ALTER TABLE `formresponse`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `formtemplate`
--
ALTER TABLE `formtemplate`
  ADD PRIMARY KEY (`template_id`);

--
-- Indexes for table `membersuggestion`
--
ALTER TABLE `membersuggestion`
  ADD PRIMARY KEY (`suggestion_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `resolved_by` (`resolved_by`);

--
-- Indexes for table `passwordresettoken`
--
ALTER TABLE `passwordresettoken`
  ADD PRIMARY KEY (`reset_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `rolechangelog`
--
ALTER TABLE `rolechangelog`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `old_role_id` (`old_role_id`),
  ADD KEY `new_role_id` (`new_role_id`);

--
-- Indexes for table `rolepermission`
--
ALTER TABLE `rolepermission`
  ADD PRIMARY KEY (`roleperm_id`),
  ADD KEY `permission_id` (`permission_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `suggestion`
--
ALTER TABLE `suggestion`
  ADD PRIMARY KEY (`suggestion_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- Indexes for table `userrole`
--
ALTER TABLE `userrole`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `auditlog`
--
ALTER TABLE `auditlog`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- AUTO_INCREMENT for table `calendarevent`
--
ALTER TABLE `calendarevent`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `formresponse`
--
ALTER TABLE `formresponse`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `formtemplate`
--
ALTER TABLE `formtemplate`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `membersuggestion`
--
ALTER TABLE `membersuggestion`
  MODIFY `suggestion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `passwordresettoken`
--
ALTER TABLE `passwordresettoken`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rolechangelog`
--
ALTER TABLE `rolechangelog`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `rolepermission`
--
ALTER TABLE `rolepermission`
  MODIFY `roleperm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `suggestion`
--
ALTER TABLE `suggestion`
  MODIFY `suggestion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcement`
--
ALTER TABLE `announcement`
  ADD CONSTRAINT `announcement_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `announcement_ibfk_2` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`taken_by`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`event_id`) REFERENCES `calendarevent` (`event_id`);

--
-- Constraints for table `auditlog`
--
ALTER TABLE `auditlog`
  ADD CONSTRAINT `auditlog_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `department`
--
ALTER TABLE `department`
  ADD CONSTRAINT `department_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `document`
--
ALTER TABLE `document`
  ADD CONSTRAINT `document_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `document_ibfk_2` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`);

--
-- Constraints for table `formresponse`
--
ALTER TABLE `formresponse`
  ADD CONSTRAINT `formresponse_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `formtemplate` (`template_id`),
  ADD CONSTRAINT `formresponse_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `membersuggestion`
--
ALTER TABLE `membersuggestion`
  ADD CONSTRAINT `membersuggestion_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `membersuggestion_ibfk_2` FOREIGN KEY (`resolved_by`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `passwordresettoken`
--
ALTER TABLE `passwordresettoken`
  ADD CONSTRAINT `passwordresettoken_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `rolechangelog`
--
ALTER TABLE `rolechangelog`
  ADD CONSTRAINT `rolechangelog_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `rolechangelog_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `rolechangelog_ibfk_3` FOREIGN KEY (`old_role_id`) REFERENCES `role` (`role_id`),
  ADD CONSTRAINT `rolechangelog_ibfk_4` FOREIGN KEY (`new_role_id`) REFERENCES `role` (`role_id`);

--
-- Constraints for table `rolepermission`
--
ALTER TABLE `rolepermission`
  ADD CONSTRAINT `rolepermission_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`permission_id`),
  ADD CONSTRAINT `rolepermission_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);

--
-- Constraints for table `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `userrole`
--
ALTER TABLE `userrole`
  ADD CONSTRAINT `userrole_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `userrole_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
