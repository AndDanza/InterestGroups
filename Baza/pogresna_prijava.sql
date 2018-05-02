-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2017 at 03:25 PM
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
-- Table structure for table `pogresna_prijava`
--

CREATE TABLE IF NOT EXISTS `pogresna_prijava` (
  `korisnik` int(11) NOT NULL,
  `datum_vrijeme_pokusaja` datetime NOT NULL,
  `administrator` int(11) DEFAULT NULL,
  `racun_zakljucan` tinyint(4) DEFAULT '1',
  `datum_vrijeme_otkljucavanje` datetime DEFAULT NULL,
  PRIMARY KEY (`korisnik`,`datum_vrijeme_pokusaja`),
  KEY `fk_pogresna_prijava_korisnik2_idx` (`administrator`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pogresna_prijava`
--

INSERT INTO `pogresna_prijava` (`korisnik`, `datum_vrijeme_pokusaja`, `administrator`, `racun_zakljucan`, `datum_vrijeme_otkljucavanje`) VALUES
(2, '2017-03-04 00:00:00', 1, 0, '2017-03-01 00:00:00'),
(3, '2017-03-02 00:00:00', NULL, 1, NULL),
(4, '2017-03-08 00:00:00', 10, 0, '2017-03-25 00:00:00'),
(5, '2017-03-03 00:00:00', 1, 0, '2017-03-26 00:00:00'),
(6, '2017-03-03 00:00:00', 10, 0, '2017-03-11 00:00:00'),
(7, '2017-03-10 00:00:00', NULL, 1, NULL),
(8, '2017-03-03 00:00:00', NULL, 1, NULL),
(9, '2017-03-24 00:00:00', 10, 0, '2017-03-26 00:00:00'),
(11, '2017-03-02 00:00:00', 1, 1, '2017-03-26 00:00:00'),
(12, '2017-03-24 00:00:00', NULL, 0, '2017-03-26 00:00:00');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pogresna_prijava`
--
ALTER TABLE `pogresna_prijava`
  ADD CONSTRAINT `fk_pogresna_prijava_korisnik1` FOREIGN KEY (`korisnik`) REFERENCES `korisnik` (`id_korisnik`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pogresna_prijava_korisnik2` FOREIGN KEY (`administrator`) REFERENCES `korisnik` (`id_korisnik`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
