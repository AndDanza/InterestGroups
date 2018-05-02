-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2017 at 07:07 PM
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
-- Table structure for table `log_aplikacije`
--

CREATE TABLE IF NOT EXISTS `log_aplikacije` (
  `datum_vrijeme_akcije` datetime NOT NULL,
  `korisnik_id_korisnik` int(11) NOT NULL,
  PRIMARY KEY (`korisnik_id_korisnik`,`datum_vrijeme_akcije`),
  KEY `fk_log_aplikacije_korisnik1_idx` (`korisnik_id_korisnik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `log_aplikacije`
--

INSERT INTO `log_aplikacije` (`datum_vrijeme_akcije`, `korisnik_id_korisnik`) VALUES
('2017-03-27 08:17:16', 1),
('2017-03-15 06:18:11', 2),
('2017-03-17 06:18:34', 2),
('2017-03-27 06:14:15', 2),
('2017-03-18 07:21:32', 3),
('2017-03-27 06:00:35', 3),
('2017-03-27 09:41:13', 3),
('2017-03-27 05:36:15', 4),
('2017-03-27 06:00:15', 4),
('2017-03-27 11:16:27', 4),
('2017-03-15 09:19:38', 5),
('2017-03-27 07:25:45', 5),
('2017-03-27 10:21:36', 5),
('2017-03-10 18:32:19', 6),
('2017-03-11 12:23:24', 6),
('2017-03-27 18:36:27', 6),
('2017-03-17 09:14:25', 7),
('2017-03-25 10:30:11', 7),
('2017-03-27 07:46:10', 7),
('2017-03-17 06:00:18', 8),
('2017-03-27 08:39:16', 8),
('2017-03-27 11:29:31', 8),
('2017-03-23 10:21:19', 9),
('2017-03-27 09:43:13', 9),
('2017-03-27 15:00:36', 9),
('2017-03-27 11:05:38', 10),
('2017-03-16 13:00:13', 11),
('2017-03-27 17:30:25', 11),
('2017-03-16 16:28:35', 12),
('2017-03-27 06:36:40', 12);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log_aplikacije`
--
ALTER TABLE `log_aplikacije`
  ADD CONSTRAINT `fk_log_aplikacije_korisnik1` FOREIGN KEY (`korisnik_id_korisnik`) REFERENCES `korisnik` (`id_korisnik`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
