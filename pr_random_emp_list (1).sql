-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 05, 2024 at 11:53 AM
-- Server version: 8.0.31
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bhuracon_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `pr_random_emp_list`
--

DROP TABLE IF EXISTS `pr_random_emp_list`;
CREATE TABLE IF NOT EXISTS `pr_random_emp_list` (
  `eid` int NOT NULL AUTO_INCREMENT,
  `ename` varchar(700) DEFAULT NULL,
  `gender` varchar(500) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` varchar(1000) DEFAULT NULL,
  `native` varchar(500) DEFAULT NULL,
  `designation` varchar(500) DEFAULT NULL,
  `guj_stay` date DEFAULT NULL,
  PRIMARY KEY (`eid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
