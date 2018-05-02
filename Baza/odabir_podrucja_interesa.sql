-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2017 at 02:32 PM
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
-- Table structure for table `odabir_podrucja_interesa`
--

CREATE TABLE IF NOT EXISTS `odabir_podrucja_interesa` (
  `korisnik_id_korisnik` int(11) NOT NULL,
  `podrucja_interesa_id_podrucja` int(11) NOT NULL,
  `datum_vrijeme_odabira` datetime NOT NULL,
  `datum_vrijeme_prekida` datetime DEFAULT NULL,
  PRIMARY KEY (`korisnik_id_korisnik`,`podrucja_interesa_id_podrucja`,`datum_vrijeme_odabira`),
  KEY `fk_korisnik_has_podrucja_interesa_podrucja_interesa1_idx` (`podrucja_interesa_id_podrucja`),
  KEY `fk_korisnik_has_podrucja_interesa_korisnik1_idx` (`korisnik_id_korisnik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `odabir_podrucja_interesa`
--

INSERT INTO `odabir_podrucja_interesa` (`korisnik_id_korisnik`, `podrucja_interesa_id_podrucja`, `datum_vrijeme_odabira`, `datum_vrijeme_prekida`) VALUES
(5, 1, '2017-03-26 09:30:13', NULL),
(5, 2, '2017-03-26 09:30:13', NULL),
(6, 1, '2017-03-26 09:30:13', NULL),
(6, 3, '2017-03-26 09:30:13', NULL),
(7, 1, '2017-03-26 09:30:13', NULL),
(7, 4, '2017-03-26 09:30:13', NULL),
(8, 1, '2017-03-26 09:30:13', NULL),
(8, 3, '2017-03-26 09:30:13', NULL),
(8, 6, '2017-03-26 09:30:13', NULL),
(9, 1, '2017-03-26 09:30:13', NULL),
(9, 3, '2017-03-26 09:30:13', NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `odabir_podrucja_interesa`
--
ALTER TABLE `odabir_podrucja_interesa`
  ADD CONSTRAINT `fk_korisnik_has_podrucja_interesa_korisnik1` FOREIGN KEY (`korisnik_id_korisnik`) REFERENCES `korisnik` (`id_korisnik`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_korisnik_has_podrucja_interesa_podrucja_interesa1` FOREIGN KEY (`podrucja_interesa_id_podrucja`) REFERENCES `podrucja_interesa` (`id_podrucja`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
