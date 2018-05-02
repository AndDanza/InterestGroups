-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2017 at 03:38 PM
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
-- Table structure for table `korisnik`
--

CREATE TABLE IF NOT EXISTS `korisnik` (
  `id_korisnik` int(11) NOT NULL,
  `tip_korisnika` int(11) NOT NULL,
  `ime` varchar(45) DEFAULT NULL,
  `prezime` varchar(45) DEFAULT NULL,
  `korisnicko_ime` varchar(70) NOT NULL,
  `email` varchar(45) NOT NULL,
  `lozinka` varchar(45) NOT NULL,
  `kriptirana_lozinka` varchar(45) NOT NULL,
  `prijava_dva_koraka` tinyint(4) NOT NULL DEFAULT '0',
  `aktivan_racun` tinyint(4) NOT NULL DEFAULT '1',
  `stanje_bodova` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_korisnik`),
  KEY `fk_korisnik_tip_korisnika_idx` (`tip_korisnika`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `korisnik`
--

INSERT INTO `korisnik` (`id_korisnik`, `tip_korisnika`, `ime`, `prezime`, `korisnicko_ime`, `email`, `lozinka`, `kriptirana_lozinka`, `prijava_dva_koraka`, `aktivan_racun`, `stanje_bodova`) VALUES
(1, 1, 'Andrea', 'Danzante', 'anddanzan', 'anddanzan@foi.hr', 'mojasifra', 'kriptirana sifra', 1, 1, 0),
(2, 2, 'Pero', 'Perić', 'perica154', 'perperic@foi.hr', 'imasifru', 'kriptirana sifra', 1, 1, 0),
(3, 2, 'Anica', 'Anić', 'ana_ana', 'anaanic@foi.hr', 'anicasifra', 'kriptirana sifra', 1, 1, 0),
(4, 2, 'Jura', 'Juric', 'jurinho', 'jurjuric@foi.hr', 'krademsve', 'kriptirana sifra', 1, 1, 0),
(5, 3, NULL, NULL, 'ne_da_ime', 'nekimail@gmail.hr', 'vrlodugasifra', 'kriptirana sifra', 0, 1, 25),
(6, 3, 'Tomislav', 'Karamarko', 'theGazda', 'gazda@hdz.hr', 'nisamuzeo', 'kriptirana sifra', 1, 0, 25),
(7, 3, 'Tihomir', 'Orešković', 'dođohvidjeh', 'tihoresk@vlada.hr', 'airCanada', 'kriptirana sifra', 1, 1, 15),
(8, 3, 'Ivica', 'Garminšparten', 'ivicaG', 'ivicaG@gmail.hr', 'garminfrik', 'kriptirana sifra', 1, 1, 15),
(9, 3, 'Jadranka', 'Kosor', 'oRkos', 'jadkosor@gmail.hr', 'jadrankakosor', 'kriptirana sifra', 1, 1, 30),
(10, 1, 'Domagoj', 'Domagojević', 'ukrainac', 'domagoj@gmail.hr', 'ukraina', 'kriptirana sifra', 0, 1, 0),
(11, 3, NULL, NULL, 'splićo', 'andrijaJarak@gmail.com', '18949', 'kriptirana loznka', 1, 0, 30),
(12, 3, NULL, NULL, 'maliMarko', 'makorZG@gmail.com', '99191961', 'kriptirana lozinka', 0, 0, 40);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `korisnik`
--

--ALTER TABLE `korisnik`
  --ADD CONSTRAINT `fk_korisnik_tip_korisnika` FOREIGN KEY (`tip_korisnika`) REFERENCES `tip_korisnika` (`id_tip_korisnika`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
