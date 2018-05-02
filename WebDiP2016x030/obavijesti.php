<?php

require './PHP Klase/korisnik.class.php';
require './PHP Klase/baza.class.php';

session_start();
if(!isset($_SESSION["aktivniKorisnik"]))
{
    header("refresh:0;url=prijava.php");
}
else
{
    $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
    $vrsta = intval($korisnik->get_vrsta_korisnika());
    if($vrsta === 3)
    {
        header("refresh:0;url=profil.php");
    }
}

function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s", ($pomak[0]*60*60) + time());
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


function SaljiMail($user, $poruka, $baza)
{
    $sql = "SELECT email FROM korisnik WHERE id_korisnik = $user";
    $rez = $baza->selectDB($sql);
    
    
    list($mail_to) = $rez->fetch_array();
    $mail_from = "aktivirajme@stranica.hr";
    $mail_subject = "Obavijest";

    if (mail($mail_to, $mail_subject, $poruka, $mail_from)) 
    {
        return true;
    } 
    else 
    {
        return false;
    }
}

?>

<!DOCTYPE html>

<html>
    <head>
        <title>Interesne skipine - Profil</title>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="naslov" content="Prijava">
        <meta name="kljucneRijeci" content="FOI, WebDiP, HTML, CSS">
        <meta name="datum" content="06.05.2017.">
        <meta name="autor" content="anddanzan">

        <link rel="stylesheet" type="text/css" href="CSS/glavniCSS.css">
        <link rel="stylesheet" type="text/css" href="CSS/formeCSS.css">
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
                        if(intval($korisnik->get_id()) === 1)
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
                            echo '<li><a href="pregledKorisnikaModerator.php"><b>Korisnici područja</b></a></li>';
                            echo '<li><a style="background: gainsboro;" href="obavijesti.php"><b>Obavijesti</b></a></li>';
                        }
                        echo '<li><a href="kuponi.php"><b>Kuponi članstva</b></a></li>';
                    }
                    ?>
                    <li><a href="podrucjaZaKorisnika.php"><b>Područja interesa</b></a></li>
                </ul>
            </nav>
        </div>
        
        <section id="slanjeObavijesti">
            <?php
            $baza = new Baza();
            $baza->spojiDB();
            
            if(isset($_POST["posaljiPojedinacno"]))
            {
                $korisnik = intval($_POST["odabirKor"]);
                $poruka = $_POST["tekstObavijest"];
                $diskusija = intval($_POST["obavijestDisk"]);
                $timeSad = DohvatiVrijemePlusPomak($baza);

                $sql = "INSERT INTO `obavijesti`(`korisnik`, `diskusija`, `datum_vrijeme_slanja`, `poruka`) "
                      ."VALUES ($korisnik, $diskusija, '$timeSad','$poruka')";
                $baza->updateDB($sql);

                if(SaljiMail($korisnik, $poruka, $baza))
                {
                    ZapisiLogOstalo($baza, $korisnik, 'Poslana obavijest');
                }
                
                echo "<p><b style='color: green; text-align: left; font-size: 18px;'>Obavijest poslana odabranom korisniku.</b></p>";
            }


            if(isset($_POST["posaljiDiskusiji"]))
            {
                $diskusija = intval($_POST["obavijestDisk"]);
                $timeSad = DohvatiVrijemePlusPomak($baza);
                $poruka = $_POST["tekstObavijest"];

                $sql = "SELECT korisnik, korisnik.email FROM pretplata "
                      ."JOIN korisnik ON korisnik.id_korisnik=pretplata.korisnik "
                      ."WHERE diskusija = $diskusija";
                $rez = $baza->selectDB($sql);

                while(list($korisnik, $mail) = $rez->fetch_array())
                {
                    $sql = "INSERT INTO `obavijesti`(`korisnik`, `diskusija`, `datum_vrijeme_slanja`, `poruka`) "
                          ."VALUES ($korisnik, $diskusija, '$timeSad','$poruka')";
                    $baza->updateDB($sql);

                    SaljiMail($korisnik, $poruka, $baza);
                }
                
                echo "<p><b style='color: green; text-align: left; font-size: 18px;'>Obavijest poslana pretplatnicima diskusije.</b></p>";
            }

            $baza->zatvoriDB();
            ?>
            <h2>Slanje obavijesti</h2>
            <label for="obavijestTip">Vrsta obavijesti</label>
            <select id="obavijestTip" name="obavijestTip">
                <option value="-1">--Odaberite vrstu obavijesti--</option>
                <option value="1">Pojedinačna</option>
                <option value="2">Pretplaćeni na diskusiju</option>
            </select>
            <br><br>
            <form id="formObavijesti" method="POST" action="obavijesti.php" style="margin-top: 3%;" novalidate>
                
            </form>
        </section>

        <footer id="footerPrijava">
            <p style="text-align: center;">
                Prijava u sustav e-Interesnih skupina<br>
                Vrijeme potrebno za rješavanje aktivnog dokumenta: 20 min<br>
                &copy; 2017 A.Danzante
            </p>
        </footer>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="jQuery/slanjeObavijesti.js"></script>
    </body>
</html>