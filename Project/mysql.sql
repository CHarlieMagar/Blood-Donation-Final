-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2025 at 08:49 PM
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
-- Database: `mysql`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_credentials`
--

CREATE TABLE `admin_credentials` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_credentials`
--

INSERT INTO `admin_credentials` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `blood_requests`
--

CREATE TABLE `blood_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` int(25) NOT NULL,
  `email` varchar(100) NOT NULL,
  `blood_required` varchar(100) NOT NULL,
  `blood_type` varchar(10) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blood_requests`
--

INSERT INTO `blood_requests` (`id`, `name`, `phone`, `email`, `blood_required`, `blood_type`, `message`) VALUES
(1, 'Utshav Bhatta', 2147483647, 'utshav42@gmail.com', 'medical', '', 'Thank You'),
(2, 'Sujal Kunwar', 2147483647, 'sujal.kunwar@patancollege.edu.np', 'surgery', '', 'okie'),
(4, 'Jenish', 2147483647, 'zenismaharjan54@gmail.com', 'surgery', '', 'ok'),
(5, 'Bidur', 2147483647, 'bidur@gmail.com', 'surgery', '', 'dafsf');

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `blood_group` varchar(5) NOT NULL,
  `mobile_no` int(15) NOT NULL,
  `age` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`id`, `name`, `gender`, `blood_group`, `mobile_no`, `age`, `email`, `address`) VALUES
(6, 'Zenish', 'Male', 'O-', 2147483647, 50, 'zenismaharjan504@gmail.com', 'kirtipur'),
(8, 'Bidur', 'Male', 'B+', 987, 87, 'bidur@gmail.com', 'adsf'),
(9, 'Zenish', '', '', 2147483647, 50, 'zenismaharjan5@gmail.com', 'kirtipur'),
(13, 'Hari Thapa', 'Male', 'A-', 2147483647, 18, 'Hellosushant12@gmail.com', 'Lalitpur'),
(17, 'Siya Rana', 'Female', 'B+', 2147483647, 20, 'thamanaga@gmail.com', 'Kathmandu');

-- --------------------------------------------------------

--
-- Table structure for table `emergency`
--

CREATE TABLE `emergency` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` int(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `blood_required` varchar(50) NOT NULL,
  `blood_type` varchar(10) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency`
--

INSERT INTO `emergency` (`id`, `name`, `phone`, `email`, `blood_required`, `blood_type`, `message`, `created_at`) VALUES
(1, 'Jenish', 2147483647, 'zenismaharjan504@gmail.com', '', '', 'ok', '2024-12-03 06:33:21'),
(2, 'hRam', 2147483647, 'hellonepal12@gmail.com', '', '', 'ok', '2024-12-03 06:40:36'),
(4, 'Bidur ', 0, 'bidur@gmail.com', '', '', '        ', '2024-12-03 07:38:38'),
(5, 'Sushant Thapa Magar', 2147483647, 'thapasushant772@gmail.com', 'surgery', 'B+', 'dafsf', '2024-12-03 09:41:38'),
(6, 'Jenish Maharjan', 2147483647, 'jenishmaharjan666@gmail.com', 'accident', 'B+', 'need blood', '2024-12-04 08:17:23');

-- --------------------------------------------------------

--
-- Stand-in structure for view `user`
-- (See below for the actual view)
--
CREATE TABLE `user` (
);

-- --------------------------------------------------------

--
-- Structure for view `user`
--
DROP TABLE IF EXISTS `user`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user`  AS SELECT `global_priv`.`Host` AS `Host`, `global_priv`.`User` AS `User`, if(json_value(`global_priv`.`Priv`,'$.plugin') in ('mysql_native_password','mysql_old_password'),ifnull(json_value(`global_priv`.`Priv`,'$.authentication_string'),''),'') AS `Password`, if(json_value(`global_priv`.`Priv`,'$.access') & 1,'Y','N') AS `Select_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 2,'Y','N') AS `Insert_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 4,'Y','N') AS `Update_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 8,'Y','N') AS `Delete_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 16,'Y','N') AS `Create_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 32,'Y','N') AS `Drop_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 64,'Y','N') AS `Reload_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 128,'Y','N') AS `Shutdown_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 256,'Y','N') AS `Process_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 512,'Y','N') AS `File_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 1024,'Y','N') AS `Grant_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 2048,'Y','N') AS `References_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 4096,'Y','N') AS `Index_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 8192,'Y','N') AS `Alter_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 16384,'Y','N') AS `Show_db_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 32768,'Y','N') AS `Super_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 65536,'Y','N') AS `Create_tmp_table_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 131072,'Y','N') AS `Lock_tables_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 262144,'Y','N') AS `Execute_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 524288,'Y','N') AS `Repl_slave_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 1048576,'Y','N') AS `Repl_client_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 2097152,'Y','N') AS `Create_view_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 4194304,'Y','N') AS `Show_view_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 8388608,'Y','N') AS `Create_routine_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 16777216,'Y','N') AS `Alter_routine_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 33554432,'Y','N') AS `Create_user_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 67108864,'Y','N') AS `Event_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 134217728,'Y','N') AS `Trigger_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 268435456,'Y','N') AS `Create_tablespace_priv`, if(json_value(`global_priv`.`Priv`,'$.access') & 536870912,'Y','N') AS `Delete_history_priv`, elt(ifnull(json_value(`global_priv`.`Priv`,'$.ssl_type'),0) + 1,'','ANY','X509','SPECIFIED') AS `ssl_type`, ifnull(json_value(`global_priv`.`Priv`,'$.ssl_cipher'),'') AS `ssl_cipher`, ifnull(json_value(`global_priv`.`Priv`,'$.x509_issuer'),'') AS `x509_issuer`, ifnull(json_value(`global_priv`.`Priv`,'$.x509_subject'),'') AS `x509_subject`, cast(ifnull(json_value(`global_priv`.`Priv`,'$.max_questions'),0) as unsigned) AS `max_questions`, cast(ifnull(json_value(`global_priv`.`Priv`,'$.max_updates'),0) as unsigned) AS `max_updates`, cast(ifnull(json_value(`global_priv`.`Priv`,'$.max_connections'),0) as unsigned) AS `max_connections`, cast(ifnull(json_value(`global_priv`.`Priv`,'$.max_user_connections'),0) as signed) AS `max_user_connections`, ifnull(json_value(`global_priv`.`Priv`,'$.plugin'),'') AS `plugin`, ifnull(json_value(`global_priv`.`Priv`,'$.authentication_string'),'') AS `authentication_string`, 'N' AS `password_expired`, elt(ifnull(json_value(`global_priv`.`Priv`,'$.is_role'),0) + 1,'N','Y') AS `is_role`, ifnull(json_value(`global_priv`.`Priv`,'$.default_role'),'') AS `default_role`, cast(ifnull(json_value(`global_priv`.`Priv`,'$.max_statement_time'),0.0) as decimal(12,6)) AS `max_statement_time` FROM `global_priv` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_credentials`
--
ALTER TABLE `admin_credentials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `blood_requests`
--
ALTER TABLE `blood_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emergency`
--
ALTER TABLE `emergency`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_credentials`
--
ALTER TABLE `admin_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blood_requests`
--
ALTER TABLE `blood_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `donors`
--
ALTER TABLE `donors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `emergency`
--
ALTER TABLE `emergency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
