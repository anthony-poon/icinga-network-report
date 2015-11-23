-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 24, 2015 at 04:38 PM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ido_reporting`
--

-- --------------------------------------------------------

--
-- Table structure for table `report_avail_acknowledge`
--

CREATE TABLE IF NOT EXISTS `report_avail_acknowledge` (
  `acknowledge_id` int(11) NOT NULL AUTO_INCREMENT,
  `time_start` datetime NOT NULL,
  `time_end` datetime NOT NULL,
  `reason` text,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`acknowledge_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_avail_acknowledge_mapping`
--

CREATE TABLE IF NOT EXISTS `report_avail_acknowledge_mapping` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `acknowledge_id` int(11) NOT NULL,
  `avail_obj_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_avail_mapping`
--

CREATE TABLE IF NOT EXISTS `report_avail_mapping` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `avail_obj_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `timestamps` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_avail_obj_definition`
--

CREATE TABLE IF NOT EXISTS `report_avail_obj_definition` (
  `avail_obj_id` int(11) NOT NULL AUTO_INCREMENT,
  `display_name` text NOT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`avail_obj_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_group_definition`
--

CREATE TABLE IF NOT EXISTS `report_group_definition` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `timestamps` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid_2` (`uid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_group_mapping`
--

CREATE TABLE IF NOT EXISTS `report_group_mapping` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `service_uid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_perfdata`
--

CREATE TABLE IF NOT EXISTS `report_perfdata` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `host_object_id` int(11) NOT NULL,
  `service_object_id` int(11) NOT NULL,
  `status_update_time` datetime NOT NULL,
  `output` text NOT NULL,
  `perfdata` text NOT NULL,
  `current_state` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=103306 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_perf_object_definition`
--

CREATE TABLE IF NOT EXISTS `report_perf_object_definition` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `display_name` text,
  `parser_class` text,
  `include_in_excel` tinyint(1) NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
