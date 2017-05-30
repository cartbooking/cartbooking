-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 23, 2017 at 03:58 PM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sydney`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `shift_id` int(6) NOT NULL,
  `date` date NOT NULL,
  `overseer_id` int(6) NOT NULL DEFAULT '0',
  `pioneer_id` int(6) NOT NULL DEFAULT '0',
  `pioneer_b_id` int(6) NOT NULL DEFAULT '0',
  `confirmed` varchar(1) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `full` varchar(1) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `recorded` varchar(1) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `placements` int(3) NOT NULL DEFAULT '0',
  `videos` int(3) NOT NULL DEFAULT '0',
  `requests` int(3) NOT NULL DEFAULT '0',
  `comments` varchar(1500) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `experience` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `overseer_id` (`overseer_id`),
  KEY `pioneer_id` (`pioneer_id`),
  KEY `pioneer_b_id` (`pioneer_b_id`),
  KEY `confirmed` (`confirmed`),
  KEY `full` (`full`),
  KEY `recorded` (`recorded`),
  KEY `shift_id` (`shift_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8065 ;

-- --------------------------------------------------------

--
-- Table structure for table `bookings_archive`
--

CREATE TABLE IF NOT EXISTS `bookings_archive` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `month` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `year` int(4) NOT NULL,
  `confirmed` int(4) NOT NULL,
  `unconfirmed` int(4) NOT NULL,
  `placements` int(5) NOT NULL,
  `videos` int(4) NOT NULL,
  `requests` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `experiences`
--

CREATE TABLE IF NOT EXISTS `experiences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `overseer_id` int(6) NOT NULL,
  `experience` varchar(1500) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=178 ;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `volunteers` int(1) NOT NULL,
  `centre` varchar(100) NOT NULL DEFAULT '0',
  `path` varchar(500) NOT NULL DEFAULT '0',
  `markers` varchar(500) NOT NULL DEFAULT '0',
  `description` varchar(500) NOT NULL DEFAULT '',
  `zoom` int(3),
  `capacity` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `pioneers`
--

CREATE TABLE IF NOT EXISTS `pioneers` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `last_name` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `gender` varchar(1) COLLATE latin1_general_ci NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `congregation` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `phone` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `spouse_id` int(6) NOT NULL,
  `inactive` varchar(1) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gender` (`gender`),
  KEY `phone` (`phone`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=597 ;

CREATE TABLE IF NOT EXISTS `relationships` (
  `publisher_id_1` INT NOT NULL,
  `publisher_id_2` INT NOT NULL,
  PRIMARY KEY (publisher_id_1, publisher_id_2),
  FOREIGN KEY (publisher_id_1) REFERENCES pioneers(id) ON DELETE CASCADE ,
  FOREIGN KEY (publisher_id_2) REFERENCES pioneers(id) ON DELETE CASCADE
);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE IF NOT EXISTS `shifts` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `location_id` int(3) NOT NULL,
  `day` int(1) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `location_id` (`location_id`),
  KEY `day` (`day`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=175 ;

-- --------------------------------------------------------

--
-- Table structure for table `shifts_archive`
--

CREATE TABLE IF NOT EXISTS `shifts_archive` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `location_id` int(3) NOT NULL,
  `day` int(1) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `location_id` (`location_id`),
  KEY `day` (`day`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=76 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


SELECT l.name, count(1) as count, EXTRACT(YEAR_MONTH FROM b.date) as month,
  sum(placements) as placements, sum(videos) as videos, sum(requests) as requests
FROM locations l LEFT JOIN shifts s ON l.id = s.location_id
INNER JOIN bookings b ON s.id = b.shift_id
GROUP BY l.name, EXTRACT(YEAR_MONTH FROM date)
ORDER BY l.name, EXTRACT(YEAR_MONTH FROM date);
