<?php

require './PHP Klase/baza.class.php';
require './PHP Klase/korisnik.class.php';

session_start();
if(!isset($_SESSION["aktivniKorisnik"]))
{
    header("refresh:0;url=prijava.php");
}
else
{
    $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
    $id = intval($korisnik->get_vrsta_korisnika());
    
    if($id === 3)
    {
        header("refresh:0;url=podrucjaZaKorisnika.php");
    }
}


if(isset($_GET["block"]))
{
    $baza = new Baza();
    $baza ->spojiDB();
    
    $id_korisnik = intval($_GET["block"]);
    $disk = intval($_GET["disk"]);
    
    $sql = "UPDATE `pretplata` SET `zabrana_komentiranja` = 1 WHERE `korisnik` = $id_korisnik AND diskusija = $disk";
    $baza->updateDB($sql);

    ZapisiLogOstalo($baza, $id_korisnik, 'Korisniku zabranjeno komentiranje u diskusiji');
    
    $baza->zatvoriDB();
}
else if(isset($_GET["unblock"]))
{
    $baza = new Baza();
    $baza ->spojiDB();
    
    $id_korisnik = intval($_GET["unblock"]);
    $disk = intval($_GET["disk"]);
    
    $sql = "UPDATE `pretplata` SET `zabrana_komentiranja` = 0 WHERE `korisnik` = $id_korisnik AND diskusija = $disk";
    $baza->updateDB($sql);

    ZapisiLogOstalo($baza, $id_korisnik, 'Korisniku omogućeno komentiranje u diskusiji');
    
    $baza->zatvoriDB();
}


function ZapisiLogOstalo($baza, $korisnik, $opis)
{
    $datumVrijeme = DohvatiVrijemePlusPomak($baza);
    
    $sql = "INSERT INTO `log_aplikacije`(`datum_vrijeme_akcije`, `korisnik_id_korisnik`) "
           ."VALUES ('$datumVrijeme', $korisnik)";
    $baza -> updateDB($sql);
    
    $sql = "INSERT INTO `log_aplikacije_ostalo`(`korisnik`, `datum_vrijeme_akcije`, `opis_radnje`) "
          ."VALUES ($korisnik, '$datumVrijeme', '$opis')";
    $baza -> updateDB($sql);
}

function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s", ($pomak[0]*60*60) + time());
}
?>

<!DOCTYPE html>

<html>
    <head>
        <title>Interesne skipine - Korisnici</title>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="naslov" content="Korisnici">
        <meta name="kljucneRijeci" content="FOI, WebDiP, HTML, CSS">
        <meta name="datum" content="06.05.2017.">
        <meta name="autor" content="anddanzan">
        
        <link rel="stylesheet" type="text/css" href="./CSS/glavniCSS.css">
        <link rel="stylesheet" type="text/css" href="./CSS/formeCSS.css">
        <!-- <link rel="stylesheet" type="text/css" href="CSS/responzivnost.css"> -->
    </head>
    
    <body>
        <header class="zaglavlje">
            <ul>
                <li><b id="naslovSkupine">e-Spajanje interesnih skupina</b></li>
                <li class="odjava"><a href="odjava.php?odjava=true">Odjava</a></li>
                <li id="cart"><a href="kosarica.php"><img src="Slike/kosarica.png" alt="kosarica"></a></li>
            </ul>
        </header>

        <div class="divMeni">
            <nav>
                <ul class="meni">
                    <?php
                    if(isset($_SESSION["aktivniKorisnik"]))
                    {
                        echo '<li><a href="profil.php"><b>Profil</b></a></li>';
                        $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                        if(intval($korisnik->get_vrsta_korisnika()) === 1)
                        {
                            echo '<li><a href="dnevnik.php"><b>Log sustava</b></a></li>';
                            echo '<li><a href="kontrolaKorisnika.php"><b>Pregled korisnika</b></a></li>';
                            echo '<li><a href="unosPomaka.php"><b>Pomak vremena</b></a></li>';
                            echo '<li><a href="kreirajPodrucje.php"><b>Kreiraj područje</b></a></li>';
                            echo '<li><a href="kreirajKupon.php"><b>Kreiraj kupon</b></a></li>';
                            echo '<li><a href="statistikaLojalnosti.php"><b>Statistika lojalnosti</b></a></li>';
                        }
                        if(intval($korisnik->get_vrsta_korisnika()) !== 3)
                        {
                            echo '<li><a href="definirajKupon.php"><b>Definiraj kupon</b></a></li>';
                            echo '<li><a href="dodajDiskusiju.php"><b>Dodaj diskusiju</b></a></li>';
                            echo '<li><a style="background: gainsboro;" href="pregledKorisnikaModerator.php"><b>Korisnici područja</b></a></li>';
                            echo '<li><a href="obavijesti.php"><b>Obavijesti</b></a></li>';
                        }
                        echo '<li><a href="kuponi.php"><b>Kuponi članstva</b></a></li>';
                    }
                    ?>
                    <li><a href="podrucjaZaKorisnika.php"><b>Područja interesa</b></a></li>
                </ul>
            </nav>
        </div>

        <section id="kontrolaPodrucja">
            <h2 id="naslovKontrolaPod">Podaci o korisnicima sustava</h3>
            <label for="brStranicaKontrolaPod">Broj redaka u tablici: </label>
            <select class="brojStranica" id="brStranicaKontrolaPod">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <p class="podaci" style="float: right;">
                <label for="searchKontrolaPod">Pretraži: </label>
                <input id="searchKontrolaPod" name="searchKontrolaPod" type="text">
            </p>
            
            <table class="tablicaLog" id="tablicaKontrolaPod">
                <thead>
                    <tr>
                        <th>Ime</th>
                        <th>Prezime</th>
                        <th id="pregledKorIme">Korisničko ime</th>
                        <th id="pregledDisk">Diskusija</th>
                        <th>Email</th>
                        <th>Kontrola</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div style="float: right; margin-top: 2%;" id="paginacijaKontrolaPod">
                <a id="krajKontrolaPod" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>
                <a id="sljedecaKontrolaPod" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>
                <input id="trenStranicaKontrolaPod" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">
                <a id="prethodniKontrolaPod" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>
                <a id="pocetakKontrolaPod" style="float: right; margin: 3% 2% 0% 1%">Početak</a>
            </div>
        </section>

        <footer id="footerPrijava">
            <p style="text-align: center;">
                Registracija u sustav e-Interesnih skupina<br>
                Vrijeme potrebno za rješavanje aktivnog dokumenta: 15 min<br>
                &copy; 2017 A.Danzante
            </p>
        </footer>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="./jQuery/kontrolaKorisnikaPodrucja.js"></script>
        
    </body>
</html>


