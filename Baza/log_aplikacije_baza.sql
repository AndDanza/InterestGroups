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
-- Table structure for table `log_aplikacije_baza`
--

CREATE TABLE IF NOT EXISTS `log_aplikacije_baza` (
  `korisnik` int(11) NOT NULL,
  `datum_vrijeme_akcije` datetime NOT NULL,
  `upit_nad_bazom` tinytext NOT NULL,
  PRIMARY KEY (`korisnik`,`datum_vrijeme_akcije`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `log_aplikacije_baza`
--

INSERT INTO `log_aplikacije_baza` (`korisnik`, `datum_vrijeme_akcije`, `upit_nad_bazom`) VALUES
(2, '2017-03-15 06:18:11', 'SELECT stanje_bodova FROM `korisnik` WHERE id_korisnik = 2'),
(3, '2017-03-18 07:21:32', 'SELECT stanje_bodova FROM `korisnik` WHERE id_korisnik = 3'),
(4, '2017-03-27 06:00:15', 'SELECT stanje_bodova FROM `korisnik` WHERE id_korisnik = 4'),
(5, '2017-03-27 10:21:36', 'SELECT stanje_bodova FROM `korisnik` WHERE id_korisnik = 5'),
(6, '2017-03-11 12:23:24', 'SELECT stanje_bodova FROM `korisnik` WHERE id_korisnik = 6'),
(7, '2017-03-17 09:14:25', 'SELECT stanje_bodova FROM `korisnik` WHERE id_korisnik = 7'),
(8, '2017-03-17 06:00:18', 'SELECT stanje_bodova FROM `korisnik` WHERE id_korisnik = 8'),
(9, '2017-03-27 15:00:36', 'SELECT stanje_bodova FROM `korisnik` WHERE id_korisnik = 9'),
(11, '2017-03-16 13:00:13', 'SELECT stanje_bodova FROM `korisnik` WHERE id_korisnik = 11'),
(12, '2017-03-16 16:28:35', 'SELECT stanje_bodova FROM `korisnik` WHERE id_korisnik = 12');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log_aplikacije_baza`
--
ALTER TABLE `log_aplikacije_baza`
  ADD CONSTRAINT `fk_log_aplikacije_baza_log_aplikacije1` FOREIGN KEY (`korisnik`, `datum_vrijeme_akcije`) REFERENCES `log_aplikacije` (`korisnik_id_korisnik`, `datum_vrijeme_akcije`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
