-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2017 at 02:31 PM
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
-- Table structure for table `prijava_dva_koraka`
--

CREATE TABLE IF NOT EXISTS `prijava_dva_koraka` (
  `korisnik` int(11) NOT NULL,
  `datum_vrijeme_izdavanja_koda` datetime NOT NULL,
  `jednokratni_kod` varchar(20) NOT NULL,
  PRIMARY KEY (`korisnik`,`datum_vrijeme_izdavanja_koda`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `prijava_dva_koraka`
--

INSERT INTO `prijava_dva_koraka` (`korisnik`, `datum_vrijeme_izdavanja_koda`, `jednokratni_kod`) VALUES
(1, '2017-03-01 00:00:00', '1548'),
(1, '2017-03-17 00:00:00', '7845'),
(1, '2017-03-30 00:00:00', '465'),
(2, '2017-03-09 00:00:00', '7886'),
(2, '2017-03-09 00:19:00', '4569'),
(3, '2017-03-10 00:00:00', '4878'),
(6, '2017-03-11 00:00:00', '18819'),
(7, '2017-03-26 00:00:00', '45645'),
(7, '2017-03-31 00:00:00', 'ddsddfs'),
(8, '2017-03-26 00:00:00', '4865');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `prijava_dva_koraka`
--
ALTER TABLE `prijava_dva_koraka`
  ADD CONSTRAINT `fk_prijava_dva_koraka_korisnik1` FOREIGN KEY (`korisnik`) REFERENCES `korisnik` (`id_korisnik`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
