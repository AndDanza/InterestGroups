-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2017 at 03:38 PM
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
-- Table structure for table `log_bodova`
--

CREATE TABLE IF NOT EXISTS `log_bodova` (
  `korisnik` int(11) NOT NULL,
  `datum_vrijeme_stjecanja` datetime NOT NULL,
  `vrsta_akcije` int(11) NOT NULL,
  PRIMARY KEY (`korisnik`,`datum_vrijeme_stjecanja`),
  KEY `fk_log_bodova_vrsta_akcije1_idx` (`vrsta_akcije`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `log_bodova`
--

INSERT INTO `log_bodova` (`korisnik`, `datum_vrijeme_stjecanja`, `vrsta_akcije`) VALUES
(5, '2017-03-31 00:00:00', 1),
(6, '2017-03-27 00:00:00', 2),
(12, '2017-03-24 00:00:00', 2),
(5, '2017-03-24 00:00:00', 3),
(12, '2017-03-23 00:00:00', 3),
(6, '2017-03-15 00:00:00', 4),
(7, '2017-03-27 00:00:00', 5),
(8, '2017-03-14 00:00:00', 5),
(9, '2017-03-27 00:00:00', 6),
(11, '2017-03-25 00:00:00', 6);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log_bodova`
--
ALTER TABLE `log_bodova`
  ADD CONSTRAINT `fk_log_bodova_korisnik1` FOREIGN KEY (`korisnik`) REFERENCES `korisnik` (`id_korisnik`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_log_bodova_vrsta_akcije1` FOREIGN KEY (`vrsta_akcije`) REFERENCES `vrsta_akcije` (`id_vrste_akcije`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
