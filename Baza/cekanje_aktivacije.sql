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
-- Table structure for table `cekanje_aktivacije`
--

CREATE TABLE IF NOT EXISTS `cekanje_aktivacije` (
  `datum_vrijeme_slanja_linka` datetime NOT NULL,
  `korisnik` int(11) NOT NULL,
  `link_iskoristen` tinyint(4) NOT NULL DEFAULT '0',
  `link_aktivacije` varchar(100) NOT NULL,
  PRIMARY KEY (`datum_vrijeme_slanja_linka`,`korisnik`),
  KEY `fk_cekanje_aktivacije_korisnik1_idx` (`korisnik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cekanje_aktivacije`
--

INSERT INTO `cekanje_aktivacije` (`datum_vrijeme_slanja_linka`, `korisnik`, `link_iskoristen`, `link_aktivacije`) VALUES
('2017-03-01 00:00:00', 3, 1, 'http://nekilink/aktiviraj'),
('2017-03-01 00:00:00', 4, 1, 'http://nekilink/aktiviraj'),
('2017-03-02 00:00:00', 9, 1, 'http://nekilink.com/aktiviraj'),
('2017-03-02 09:24:21', 6, 1, 'http://nekilink.com/aktiviraj'),
('2017-03-03 00:00:00', 5, 1, 'http://nekilink.com/aktiviraj'),
('2017-03-11 15:22:16', 8, 1, 'http://nekilink.com/aktiviraj'),
('2017-03-26 00:00:00', 7, 1, 'http://nekilink.com/aktiviraj'),
('2017-03-26 00:00:00', 10, 1, 'http://nekilink.com/aktiviraj'),
('2017-03-26 10:22:33', 11, 0, 'http://nekilink.com/aktiviraj'),
('2017-03-26 13:00:10', 12, 0, 'http://nekilink.com/aktiviraj');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cekanje_aktivacije`
--
ALTER TABLE `cekanje_aktivacije`
  ADD CONSTRAINT `fk_cekanje_aktivacije_korisnik1` FOREIGN KEY (`korisnik`) REFERENCES `korisnik` (`id_korisnik`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
