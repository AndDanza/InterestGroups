-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2017 at 02:36 PM
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
-- Table structure for table `diskusija`
--

CREATE TABLE IF NOT EXISTS `diskusija` (
  `id_diskusija` int(11) NOT NULL,
  `podrucja_interesa` int(11) NOT NULL,
  `naziv_diskusije` varchar(100) NOT NULL,
  `pravila` tinytext NOT NULL,
  `datum_vrijeme_otvaranja` datetime NOT NULL,
  `datum_vrijeme_zatvaranja` datetime DEFAULT NULL,
  PRIMARY KEY (`id_diskusija`),
  KEY `fk_diskusija_podrucja_interesa1_idx` (`podrucja_interesa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `diskusija`
--

INSERT INTO `diskusija` (`id_diskusija`, `podrucja_interesa`, `naziv_diskusije`, `pravila`, `datum_vrijeme_otvaranja`, `datum_vrijeme_zatvaranja`) VALUES
(1, 1, 'FFZG', 'Svi savjeti vezani uz studiranje na Filozofskom fakultetu u Zagrebu.', '2017-03-26 00:00:00', NULL),
(2, 2, 'Prva HNL', 'Tko će osvojiti ovogodišnji naslov prvaka. Nema vrijeđanja.', '2017-03-26 00:00:00', NULL),
(3, 3, 'Odbojka na pijesku', 'Aktualne noosti uz odbojku na pijesku. Nema vrijeđanja.', '2017-03-03 00:00:00', '2017-03-31 00:00:00'),
(4, 4, 'Westbrook MVP?', 'Kolike su šanse da Westbrook otme MVP naslov bivšem suigraču Hardenu. Nema vrijeđanja.', '2017-03-16 00:00:00', NULL),
(5, 5, 'Vettel otvara pobjedom', 'Komentari na otvaranje nove sezone i pobjedu Ferraria. Nema vrijeđanja.', '2017-03-25 00:00:00', NULL),
(6, 6, 'FOI - preporuke', 'Koliko vas je polagalo informatiku na maturi ? Koliko je FOI zahtjevan ? Nema vrijeđanja.', '2017-03-01 00:00:00', NULL),
(7, 7, 'Kako se pripremate za maturu?', 'Koji su najbolji načini i izvori za učenje. Nema vrijeđanja.', '2017-03-17 00:00:00', NULL),
(8, 8, 'OnePlus3', 'Kakva su mišljenja na nove marke mobitela iz Kine? Nema vrijeđanja.', '2017-03-26 00:00:00', NULL),
(9, 9, 'DELL da ili ne ?', 'Koliko vas je zadovoljno DELL laptopom? Nema vrijeđanja.', '2017-03-26 00:00:00', NULL),
(10, 10, 'COD4 - koliko vas još igra?', 'Koliko vas još igra COD4? Ima li koji dobar server? Nema vrijeđanja.', '2017-03-26 00:00:00', '2017-03-31 00:00:00');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `diskusija`
--
ALTER TABLE `diskusija`
  ADD CONSTRAINT `fk_diskusija_podrucja_interesa1` FOREIGN KEY (`podrucja_interesa`) REFERENCES `podrucja_interesa` (`id_podrucja`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
