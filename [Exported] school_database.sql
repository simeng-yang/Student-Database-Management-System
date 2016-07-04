-- phpMyAdmin SQL Dump
-- version 4.5.2
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2016 at 12:08 AM
-- Server version: 5.7.12-log
-- PHP Version: 5.6.16
--
-- Author: Simeng Yang

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `absences`
--

DROP TABLE IF EXISTS `absences`;
CREATE TABLE IF NOT EXISTS `absences` (
  `student_id` int(10) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`student_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `name` varchar(30) NOT NULL,
  `class_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`name`, `class_id`) VALUES
('English', 1),
('Calculus', 7),
('Biology', 9),
('Chemistry', 10),
('Physics', 11),
('History', 12),
('Art', 13),
('Gym', 14);

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

DROP TABLE IF EXISTS `scores`;
CREATE TABLE IF NOT EXISTS `scores` (
  `student_id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) UNSIGNED NOT NULL,
  `score` int(11) NOT NULL,
  PRIMARY KEY (`test_id`,`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `email` varchar(60) DEFAULT NULL,
  `street_name` varchar(30) NOT NULL,
  `street_type` varchar(10) NOT NULL,
  `city` varchar(40) NOT NULL,
  `province` varchar(2) DEFAULT NULL,
  `postal_code` varchar(8) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `dob_day` int(11) NOT NULL,
  `dob_month` varchar(10) NOT NULL,
  `dob_year` int(11) NOT NULL,
  `gender` enum('M','F','Oth') NOT NULL,
  `date_entered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `course` varchar(15) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`first_name`, `last_name`, `email`, `street_name`, `street_type`, `city`, `province`, `postal_code`, `phone`, `dob_day`, `dob_month`, `dob_year`, `gender`, `date_entered`, `course`, `student_id`) VALUES
('Aa', 'Aa', 'aaaa@email.com', 'Aaaa', 'Ave', 'Aaaa', 'AB', 'A1A 1A1', '(905) 111-1111', 1, 'JAN', 1990, 'M', '2016-06-09 20:24:35', 'PLF4M & IDP4U', 107),
('Zz', 'Zz', 'ZZZZ@email.com', 'Zzzz', 'Sq', 'Toronto', 'ON', 'L8J 3P9', '(905) 342-9983', 31, 'DEC', 2005, 'M', '2016-06-09 20:25:39', 'PAD2O1', 108),
('Bobby', 'Joester', 'bjoe@email.com', 'Ruralave', 'Pkwy', 'Oakville', 'ON', 'L9C 4B9', '(905) 728-2891', 14, 'AUG', 2004, 'Oth', '2016-06-09 23:39:37', 'PAD3O1', 112),
('Jane', 'Miller', 'jmiller@email.com', 'Treetop', 'Crcl', 'Lonetown', 'ON', 'L8D 3P9', '(905) 762-9103', 14, 'SEP', 1994, 'F', '2016-06-10 20:59:05', 'PAD2O1', 121),
('Denise', 'Stewart', 'dstewart@email.com', 'Municipality', 'Hts', 'Township', 'PE', 'L9C 4K6', '(905) 592-9172', 16, 'OCT', 1996, 'F', '2016-06-10 21:00:36', 'PAD3O1', 123),
('Cynthia', 'Brown', 'cbrown@email.com', 'Korner', 'Pl', 'Porous', 'PE', 'L7S 4H7', '(905) 592-9172', 16, 'OCT', 1996, 'F', '2016-06-10 21:01:51', 'PAD2O1', 124),
('Mark', 'Jones', 'mjones@email.com', 'Wentworth', 'Crct', 'Urapean', 'ON', 'L9C 8Y9', '(905) 728-2891', 14, 'SEP', 2003, 'M', '2016-06-10 21:04:07', 'PAD3O1', 126),
('Ernest', 'Hemingway', 'ehem@email.com', 'Writer', 'Sq', 'Hamilton', 'ON', 'L8J 3P9', '(905) 342-9983', 16, 'OCT', 2005, 'M', '2016-06-10 21:05:25', 'PAD2O1', 127),
('Mark', 'Twain', 'mtwin@email.com', 'Author', 'Dr', 'West-15th', 'AB', 'L9C 4B9', '(905) 574-1712', 14, 'OCT', 2003, 'M', '2016-06-10 21:06:00', 'PAD3O1', 128),
('Jane', 'Austen', 'jaust@email.com', 'Block', 'Cres', 'Toronto', 'ON', 'L7C 4M8', '(905) 342-9983', 11, 'JAN', 2003, 'F', '2016-06-10 21:06:51', 'PAD3O1', 129),
('James', 'Markinham', 'jmark@email.com', 'Just', 'Ave', 'Nowhere', 'ON', 'L8D 3P9', '(905) 762-9103', 17, 'SEP', 1996, 'M', '2016-06-10 21:11:09', 'PAD2O1', 131),
('Simeng', 'Yang', 'syang6648@email.com', 'Somewherest', 'Blvd', 'Hamilton', 'ON', 'L9C 4B9', '(905) 574-1712', 17, 'MAR', 1998, 'M', '2016-06-12 22:34:15', 'PAD3O1', 138);

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

DROP TABLE IF EXISTS `tests`;
CREATE TABLE IF NOT EXISTS `tests` (
  `date` date NOT NULL,
  `type` enum('T','Q') NOT NULL,
  `maxscore` int(11) NOT NULL,
  `class_id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`test_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

DROP TABLE IF EXISTS `user_account`;
CREATE TABLE IF NOT EXISTS `user_account` (
  `userID` int(9) NOT NULL AUTO_INCREMENT,
  `userName` varchar(40) NOT NULL,
  `pass` varchar(40) NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`userID`, `userName`, `pass`) VALUES
(1, 'root', 'password');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
