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
    
    if($id !== 1)
    {
        header("refresh:0;url=podrucjaZaKorisnika.php");
    }
}


if(isset($_GET["block"]))
{
    $baza = new Baza();
    $baza ->spojiDB();
    
    $id_korisnik = intval($_GET["block"]);
    $datum = DohvatiVrijemePlusPomak($baza);
    
    $sql = "UPDATE korisnik SET aktivan_racun = 0 WHERE id_korisnik = $id_korisnik";
    $baza->updateDB($sql);
    
    $sql = "INSERT INTO `pogresna_prijava`(`korisnik`, `datum_vrijeme_pokusaja`, `racun_zakljucan`)"
          ."VALUES ($id_korisnik, '$datum', 1)";
    $baza -> updateDB($sql);

    ZapisiLogOstalo($baza, $id_korisnik, 'Korisnik blokiran');
    
    $baza->zatvoriDB();
}
else if(isset($_GET["unblock"]))
{
    $baza = new Baza();
    $baza ->spojiDB();
    
    $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
    $admin = intval($korisnik->get_id());
    $datum = DohvatiVrijemePlusPomak($baza);
    
    $id_korisnik = intval($_GET["unblock"]);
    
    $sql = "UPDATE korisnik SET aktivan_racun = 1 WHERE id_korisnik = $id_korisnik";
    $baza->updateDB($sql);
    
    $sql = "UPDATE `pogresna_prijava` "
          ."SET `administrator` = $admin,`racun_zakljucan` = 0,`datum_vrijeme_otkljucavanje` = '$datum'"
          ."WHERE `korisnik` = $id_korisnik AND `datum_vrijeme_otkljucavanje` = '0000-00-00 00:00:00'";
    $baza->updateDB($sql);

    ZapisiLogOstalo($baza, $id_korisnik, 'Korisnik odblokiran');
    
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
                        echo '<li><a id="meniProfil" href="profil.php"><b>Profil</b></a></li>';
                        $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                        if(intval($korisnik->get_vrsta_korisnika()) === 1)
                        {
                            echo '<li><a href="dnevnik.php"><b>Log sustava</b></a></li>';
                            echo '<li><a style="background: gainsboro;" href="kontrolaKorisnika.php"><b>Pregled korisnika</b></a></li>';
                            echo '<li><a href="unosPomaka.php"><b>Pomak vremena</b></a></li>';
                            echo '<li><a href="kreirajPodrucje.php"><b>Kreiraj područje</b></a></li>';
                            echo '<li><a href="kreirajKupon.php"><b>Kreiraj kupon</b></a></li>';
                            echo '<li><a href="statistikaLojalnosti.php"><b>Statistika lojalnosti</b></a></li>';
                            echo '<li><a href="definirajKupon.php"><b>Definiraj kupon</b></a></li>';
                            echo '<li><a href="dodajDiskusiju.php"><b>Dodaj diskusiju</b></a></li>';
                            echo '<li><a href="pregledKorisnikaModerator.php"><b>Korisnici područja</b></a></li>';
                            echo '<li><a href="obavijesti.php"><b>Obavijesti</b></a></li>';
                        }
                        echo '<li><a href="kuponi.php"><b>Kuponi članstva</b></a></li>';
                    }
                    ?>
                    <li><a href="podrucjaZaKorisnika.php"><b>Područja interesa</b></a></li>
                </ul>
            </nav>
        </div>

        <section id="kontrolaKorisnik">
            <h2 id="naslovKontrola">Podaci o korisnicima sustava</h3>
            <label for="brStranicaKontrola">Broj redaka u tablici: </label>
            <select class="brojStranica" id="brStranicaKontrola">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <p class="podaci" style="float: right;">
                <label for="searchKontrola">Pretraži: </label>
                <input id="searchKontrola" name="searchKontrola" type="text">
            </p>
            
            <table class="tablicaLog" id="tablicaKontrola">
                <thead>
                    <tr>
                        <th>Ime</th>
                        <th id="kontrolaPrezime">Prezime</th>
                        <th id="kontrolaKorIme">Korisničko ime</th>
                        <th>Email</th>
                        <th>Kontrola</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div style="float: right; margin-top: 2%;" id="paginacijaKontrola">
                <a id="krajKontrola" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>
                <a id="sljedecaKontrola" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>
                <input id="trenStranicaKontrola" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">
                <a id="prethodniKontrola" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>
                <a id="pocetakKontrola" style="float: right; margin: 3% 2% 0% 1%">Početak</a>
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
        <script src="./jQuery/kontrolaKorisnika.js"></script>
        
    </body>
</html>


