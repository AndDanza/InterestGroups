-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2017 at 07:06 PM
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
-- Table structure for table `log_aplikacije_prijava`
--

CREATE TABLE IF NOT EXISTS `log_aplikacije_prijava` (
  `korisnik` int(11) NOT NULL,
  `datum_vrijeme_akcije` datetime NOT NULL,
  `datum_vrijeme_odjave` datetime DEFAULT NULL,
  PRIMARY KEY (`korisnik`,`datum_vrijeme_akcije`),
  KEY `fk_log_aplikacije_prijava_log_aplikacije1_idx` (`korisnik`,`datum_vrijeme_akcije`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `log_aplikacije_prijava`
--

INSERT INTO `log_aplikacije_prijava` (`korisnik`, `datum_vrijeme_akcije`, `datum_vrijeme_odjave`) VALUES
(1, '2017-03-27 08:17:16', '2017-03-27 11:00:00'),
(2, '2017-03-27 06:14:15', '2017-03-27 18:47:29'),
(3, '2017-03-27 06:00:35', '2017-03-27 15:00:00'),
(4, '2017-03-27 05:36:15', '2017-03-27 09:29:09'),
(5, '2017-03-27 07:25:45', '2017-03-27 09:00:00'),
(6, '2017-03-27 18:36:27', '2017-03-27 20:00:00'),
(7, '2017-03-27 07:46:10', '2017-03-27 08:00:00'),
(8, '2017-03-27 08:39:16', '2017-03-27 10:22:20'),
(9, '2017-03-27 09:43:13', '2017-03-27 10:00:00'),
(10, '2017-03-27 11:05:38', '2017-03-27 16:00:00');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log_aplikacije_prijava`
--
ALTER TABLE `log_aplikacije_prijava`
  ADD CONSTRAINT `fk_log_aplikacije_prijava_log_aplikacije1` FOREIGN KEY (`korisnik`, `datum_vrijeme_akcije`) REFERENCES `log_aplikacije` (`korisnik_id_korisnik`, `datum_vrijeme_akcije`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
