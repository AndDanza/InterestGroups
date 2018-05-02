<?php

require './PHP Klase/baza.class.php';
require './PHP Klase/korisnik.class.php';

session_start();
if(isset($_SESSION["aktivniKorisnik"]))
{
    $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
    
    if($korisnik->get_vrsta_korisnika() !== "1")
    {
        header("refresh:0;url=podrucjaZaKorisnika.php");
    }
}
else
{
    header("refresh:0;url=prijava.php");
}

function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s", ($pomak[0]*60*60) + time());
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
        <title>Interesne skipine - Registracija</title>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="naslov" content="Registracija">
        <meta name="kljucneRijeci" content="FOI, WebDiP, HTML, CSS">
        <meta name="datum" content="06.05.2017.">
        <meta name="autor" content="anddanzan">
        
        <script src='https://www.google.com/recaptcha/api.js'></script>
        
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
                    <li><a id="meniProfil" href="profil.php"><b>Profil</b></a></li>
                    <li><a href="dnevnik.php"><b>Log sustava</b></a></li>
                    <li><a href="kontrolaKorisnika.php"><b>Pregled korisnika</b></a></li>
                    <li><a style="background: gainsboro;" href="unosPomaka.php"><b>Pomak vremena</b></a></li>
                    <li><a href="kreirajPodrucje.php"><b>Kreiraj područje</b></a></li>
                    <li><a href="kreirajKupon.php"><b>Kreiraj kupon</b></a></li>
                    <li><a href="statistikaLojalnosti.php"><b>Statistika lojalnosti</b></a></li>
                    <li><a href="definirajKupon.php"><b>Definiraj kupon</b></a></li>
                    <li><a href="dodajDiskusiju.php"><b>Dodaj diskusiju</b></a></li>
                    <li><a href="pregledKorisnikaModerator.php"><b>Korisnici područja</b></a></li>
                    <li><a href="obavijesti.php"><b>Obavijesti</b></a></li>
                    <li><a href="kuponi.php"><b>Kuponi članstva</b></a></li>
                    <li><a href="podrucjaZaKorisnika.php"><b>Područja interesa</b></a></li>
                </ul>
            </nav>
        </div>
    
        <?php
        
        $baza = new Baza();
        $baza -> spojiDB();
        
        echo  '<form id="pomakVremena" method="POST" name="pomakVremena" action="unosPomaka.php" novalidate>';
        
        if(isset($_POST["potvrdiPomak"]))
        {
            $url = "http://barka.foi.hr/WebDiP/pomak_vremena/pomak.php?format=xml";
            $xml = simplexml_load_file($url);
            $pomak = $xml -> vrijeme -> pomak -> brojSati;
            
            $sql = "INSERT INTO `konfiguracija_sustava`(`pomak_vremena`) "
                  ."VALUES ($pomak)";
            $baza->updateDB($sql);
            
            ZapisiLogBaze($baza, $sql, intval($korisnik->get_vrsta_korisnika()), 'konfiguracija_sustava');
            
            echo "<p id='notifikacija'><b style='color: green; font-size: 25px;'>Pomak pohranjen</b></p>";
        }
        
        echo  '<h2>Promjena virtualnog vremena</h2>'
                 .'<input id="potvrdiPomak" name="potvrdiPomak" type="submit" value="Dohvati">'
                . '<a style="color: lightsalmon; margin-left: 2%;" target="_blank" href="http://barka.foi.hr/WebDiP/pomak_vremena/vrijeme.html">Forma unosa pomaka</a>'
              .'</form>';
        
        $baza ->zatvoriDB();
        ?>
    </body>
</html>