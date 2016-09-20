-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 11, 2016 at 09:19 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wordpress`
--

-- --------------------------------------------------------

--
-- Table structure for table `waf_bf_cache`
--

CREATE TABLE IF NOT EXISTS `waf_bf_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `tt` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=67 ;

-- --------------------------------------------------------

--
-- Table structure for table `waf_blacklist`
--

CREATE TABLE IF NOT EXISTS `waf_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(16) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf32 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `waf_logs`
--

CREATE TABLE IF NOT EXISTS `waf_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `type` varchar(15) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip` varchar(16) NOT NULL,
  `url` varchar(256) NOT NULL,
  `reason` text NOT NULL,
  `sid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=93 ;

-- --------------------------------------------------------

--
-- Table structure for table `waf_segments`
--

CREATE TABLE IF NOT EXISTS `waf_segments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lvl` tinyint(2) NOT NULL,
  `value` varchar(50) NOT NULL,
  `approved` int(1) NOT NULL DEFAULT '0',
  `use_type` int(1) NOT NULL DEFAULT '0',
  `bf` double NOT NULL DEFAULT '0',
  `code_contains` varchar(50) NOT NULL,
  `code_size` int(11) NOT NULL,
  `code_before` varchar(20) NOT NULL,
  `code_after` varchar(20) NOT NULL,
  `segment_x` varchar(20) NOT NULL,
  `segment_y` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

-- --------------------------------------------------------

--
-- Table structure for table `waf_settings`
--

CREATE TABLE IF NOT EXISTS `waf_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `waf_users`
--

CREATE TABLE IF NOT EXISTS `waf_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `status` int(1) NOT NULL,
  `editor` int(1) NOT NULL,
  `rmn_pass` varchar(32) NOT NULL,
  `bf` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `waf_vars`
--

CREATE TABLE IF NOT EXISTS `waf_vars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `method` varchar(10) NOT NULL,
  `value` text NOT NULL,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sid` int(11) NOT NULL,
  `approved` int(1) NOT NULL DEFAULT '0',
  `use_type` int(1) NOT NULL DEFAULT '0',
  `code_contains` varchar(50) NOT NULL,
  `code_size` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;
