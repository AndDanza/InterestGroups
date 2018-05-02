-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2017 at 02:25 PM
-- Server version: 5.5.54
-- PHP Version: 5.4.45-0+deb7u7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `WebDiP2016x030`
--

-- --------------------------------------------------------

--
-- Table structure for table `vrsta_akcije`
--

CREATE TABLE IF NOT EXISTS `vrsta_akcije` (
  `id_vrste_akcije` int(11) NOT NULL,
  `naziv_akcije` varchar(100) NOT NULL,
  `broj_bodova` int(11) NOT NULL,
  PRIMARY KEY (`id_vrste_akcije`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vrsta_akcije`
--

INSERT INTO `vrsta_akcije` (`id_vrste_akcije`, `naziv_akcije`, `broj_bodova`) VALUES
(1, 'Komentar', 5),
(2, '3 ili više komentara na diskusiji', 20),
(3, 'Prva prijava', 20),
(4, 'Prijava u sustav', 5),
(5, 'Kupnja prvog kupona', 15),
(6, 'Kupnja 3 ili više kupona', 30);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
