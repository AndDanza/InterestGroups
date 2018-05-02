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
-- Table structure for table `log_aplikacije_ostalo`
--

CREATE TABLE IF NOT EXISTS `log_aplikacije_ostalo` (
  `korisnik` int(11) NOT NULL,
  `datum_vrijeme_akcije` datetime NOT NULL,
  `opis_radnje` tinytext NOT NULL,
  PRIMARY KEY (`korisnik`,`datum_vrijeme_akcije`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `log_aplikacije_ostalo`
--

INSERT INTO `log_aplikacije_ostalo` (`korisnik`, `datum_vrijeme_akcije`, `opis_radnje`) VALUES
(2, '2017-03-17 06:18:34', 'Prva opomena za korisnika navedenog u logu.'),
(3, '2017-03-27 09:41:13', 'Moderator nije umurio korisnike u raspravi.'),
(4, '2017-03-27 11:16:27', 'Moderator nije bio aktivan u zadnje vrijeme te nije kontrolirao raspravu.'),
(5, '2017-03-15 09:19:38', 'Poslao preko maila zamolbu za mjesto moderatora.'),
(6, '2017-03-10 18:32:19', 'Prva opomena za vrijeđanja na diskusijama'),
(7, '2017-03-25 10:30:11', 'Najlojalniji korisnik ovog mjeseca'),
(8, '2017-03-27 11:29:31', 'Trenutno ima najviše komentara'),
(9, '2017-03-23 10:21:19', 'Uplatio donaciju na ime stranice'),
(11, '2017-03-27 17:30:25', 'Odlučio privremeni deaktivirati račun.'),
(12, '2017-03-27 06:36:40', 'Odlučio privremeni deaktivirati račun.');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log_aplikacije_ostalo`
--
ALTER TABLE `log_aplikacije_ostalo`
  ADD CONSTRAINT `fk_log_aplikacije_ostalo_log_aplikacije1` FOREIGN KEY (`korisnik`, `datum_vrijeme_akcije`) REFERENCES `log_aplikacije` (`korisnik_id_korisnik`, `datum_vrijeme_akcije`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
