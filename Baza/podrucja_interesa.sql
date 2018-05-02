-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2017 at 02:29 PM
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
-- Table structure for table `podrucja_interesa`
--

CREATE TABLE IF NOT EXISTS `podrucja_interesa` (
  `id_podrucja` int(11) NOT NULL,
  `naziv_podrucja` varchar(100) NOT NULL,
  `moderator` int(11) NOT NULL,
  PRIMARY KEY (`id_podrucja`),
  KEY `fk_podrucja_interesa_korisnik1_idx` (`moderator`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `podrucja_interesa`
--

INSERT INTO `podrucja_interesa` (`id_podrucja`, `naziv_podrucja`, `moderator`, `izgled_stranice_id`) VALUES
(1, 'FFZG', 3, 1),
(2, 'Nogomet', 4, 2),
(3, 'Odbojka', 3,3),
(4, 'NBA', 2, 3),
(5, 'Formula1', 3, 2),
(6, 'FOI', 2, 1),
(7, 'Srednje škole', 3, 3),
(8, 'Mobiteli', 4, 2),
(9, 'Laptopi', 3, 1),
(10, 'Računalne igre', 2, 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `podrucja_interesa`
--
ALTER TABLE `podrucja_interesa`
  ADD CONSTRAINT `fk_podrucja_interesa_korisnik1` FOREIGN KEY (`moderator`) REFERENCES `korisnik` (`id_korisnik`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
