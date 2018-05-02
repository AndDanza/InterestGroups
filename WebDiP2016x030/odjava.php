<?php

require './PHP Klase/baza.class.php';
require './PHP Klase/korisnik.class.php';

function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s", ($pomak[0]*60*60) + time());
}

function AzurirajLogPrijave($korisnik, $baza)
{
    $datumVrijeme = DohvatiVrijemePlusPomak($baza);
    $id = intval($korisnik -> get_id());
    
    $sql = "UPDATE `log_aplikacije_prijava` SET `datum_vrijeme_odjave` = '$datumVrijeme' "
           ." WHERE korisnik = $id AND `datum_vrijeme_odjave` = '0000-00-00 00:00:00'";
    $baza -> updateDB($sql);
    
    ZapisiLogBaze($baza, $sql, $id, 'log_aplikacije_prijava');
}

function ZapisiLogBaze($baza, $upit, $korisnik, $tablica)
{
    $vrstaUpita =  substr($upit, 0, 6);
    $datumVrijeme = DohvatiVrijemePlusPomak($baza);
    
    $sql = "INSERT INTO `log_aplikacije`(`datum_vrijeme_akcije`, `korisnik_id_korisnik`) "
           ."VALUES ('$datumVrijeme', $korisnik)";
    $baza -> updateDB($sql);
    
    $sql = "INSERT INTO `log_aplikacije_baza`(`korisnik`, `datum_vrijeme_akcije`, `tablica`, `vrsta_upita`) "
          ."VALUES ($korisnik, '$datumVrijeme', '$tablica', '$vrstaUpita')";
    $baza -> updateDB($sql);
}

?>

<!DOCTYPE html>

<html lang="hr">
    <head>
        <title>Aktivacija korisnika</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="naslov" content="Aktivacija korisnika">
        <meta name="kljucneRijeci" content="FOI, WebDiP, HTML, CSS">
        <meta name="datum" content="07.03.2017.">
        <meta name="autor" content="anddanzan">

        <link rel="stylesheet" type="text/css" href="./CSS/glavniCSS.css">
        <link rel="stylesheet" type="text/css" href="./CSS/formeCSS.css">
        <!-- <link rel="stylesheet" type="text/css" href="CSS/responzivnost.css"> -->
    </head>
    <body>
        <header class="zaglavlje">
            <ul>
                <li><b id="naslovSkupine">e-Spajanje interesnih skupina</b></li>
                <li class="desnaTipka"><a href="prijava.php">Prijava</a></li>
            </ul>
        </header>

        <section id="prijava">
            <h2>Odjava iz sustava</h2>
            <?php
                
                $baza = new Baza();
                $baza ->spojiDB();
                
                session_start();
                
                if(isset($_GET["odjava"]) && isset($_SESSION["aktivniKorisnik"]))
                {
                    $odjava = $_GET["odjava"];
                    $korisnik = unserialize($_SESSION["aktivniKorisnik"]);

                    if($odjava == "true")
                    {
                        AzurirajLogPrijave($korisnik, $baza);
                        $ime_prezime = $korisnik->get_ime_prezime();
                        echo "<p><b style='color: #4CAF50; font-size: 35px;'>Odjava korisnika $ime_prezime uspješna.</b></p><br>"
                            ."<p>Za 4 sekundi biti ćete preusmjereni na prijavu, u suprotnom u  odaberite Prijava</p>";
                        unset($_SESSION["aktivniKorisnik"]);
                        session_destroy();
                    }
                    
                    header( "refresh:4;url=prijava.php" );
                }
                else
                    {
                    echo "<p><b style='color: lightsalmon; font-size: 35px;'>Morate biti prijavljeni kako bi odjava bila moguća.</b></p><br>"
                            ."<p>Za 4 sekundi biti ćete preusmjereni na prijavu, u suprotnom u  odaberite Prijava</p>";
                    header( "refresh:4;url=prijava.php" );
                }
                
                $baza ->zatvoriDB();
            ?>
        </section>

        <footer id="footerPrijava">
            <p style="text-align: center;">
                Registracija u sustav e-Interesnih skupina<br>
                Vrijeme potrebno za rješavanje aktivnog dokumenta: 15 min<br>
                &copy; 2017 A.Danzante
            </p>
        </footer>
    </body>
</html>


