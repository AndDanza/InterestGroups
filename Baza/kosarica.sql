-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2017 at 03:15 PM
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
-- Table structure for table `kosarica`
--

CREATE TABLE IF NOT EXISTS `kosarica` (
  `id_kosarica` int(11) NOT NULL,
  `korisnik` int(11) NOT NULL,
  `datum_vrijeme_kupnje` datetime NOT NULL,
  PRIMARY KEY (`id_kosarica`),
  KEY `fk_kosarica_korisnik1_idx` (`korisnik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kosarica`
--

INSERT INTO `kosarica` (`id_kosarica`, `korisnik`, `datum_vrijeme_kupnje`) VALUES
(1, 2, '2017-03-26 00:00:00'),
(2, 3, '2017-03-26 00:00:00'),
(3, 4, '2017-03-26 00:00:00'),
(4, 5, '2017-03-26 00:00:00'),
(5, 6, '2017-03-26 00:00:00'),
(6, 7, '2017-03-26 00:00:00'),
(7, 8, '2017-03-26 00:00:00'),
(8, 9, '2017-03-26 00:00:00'),
(9, 11, '2017-03-26 00:00:00'),
(10, 12, '2017-03-26 00:00:00');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kosarica`
--
ALTER TABLE `kosarica`
  ADD CONSTRAINT `fk_kosarica_korisnik1` FOREIGN KEY (`korisnik`) REFERENCES `korisnik` (`id_korisnik`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
