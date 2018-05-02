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
-- Table structure for table `sadrzaj_kosarice`
--

CREATE TABLE IF NOT EXISTS `sadrzaj_kosarice` (
  `kosarica_id_kosarica` int(11) NOT NULL,
  `kupon_clanstva_id_kupona` int(11) NOT NULL,
  PRIMARY KEY (`kosarica_id_kosarica`,`kupon_clanstva_id_kupona`),
  KEY `fk_kosarica_has_kupon_clanstva_kupon_clanstva1_idx` (`kupon_clanstva_id_kupona`),
  KEY `fk_kosarica_has_kupon_clanstva_kosarica1_idx` (`kosarica_id_kosarica`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sadrzaj_kosarice`
--

INSERT INTO `sadrzaj_kosarice` (`kosarica_id_kosarica`, `kupon_clanstva_id_kupona`) VALUES
(1, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(2, 2),
(3, 3),
(9, 4),
(10, 4);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sadrzaj_kosarice`
--
ALTER TABLE `sadrzaj_kosarice`
  ADD CONSTRAINT `fk_kosarica_has_kupon_clanstva_kosarica1` FOREIGN KEY (`kosarica_id_kosarica`) REFERENCES `kosarica` (`id_kosarica`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_kosarica_has_kupon_clanstva_kupon_clanstva1` FOREIGN KEY (`kupon_clanstva_id_kupona`) REFERENCES `kupon_clanstva` (`id_kupona`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
