<?php

require './PHP Klase/baza.class.php';

function DohvatiPutanju($vrijednost)
{
    $aktivacijskiLink = "";
                        
    $putanja = $_SERVER["PHP_SELF"];
    $server = $_SERVER["HTTP_HOST"];
                      
    $aktivacijskiLink = "http://";
    $aktivacijskiLink .= $server;
    $aktivacijskiLink .= str_replace(basename($putanja), "", $putanja);
    $aktivacijskiLink .= "aktivacija.php?aktiv=";
    $aktivacijskiLink .= $vrijednost;
                        
    return $aktivacijskiLink;
}

function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s", ($pomak[0]*60*60) + time());
}

function ResetirajAktivaciju($baza, $userID)
{
    $sql = "SELECT korisnicko_ime, email FROM korisnik WHERE id_korisnik = $userID";
    $rez = $baza -> selectDB($sql);
    list($korIme, $email) = $rez -> fetch_array();
    
    $aktivacija = sha1(sha1(time())."-".$korIme);
    $datumSad = DohvatiVrijemePlusPomak($baza);

    $sql = "UPDATE `cekanje_aktivacije` "
          ."SET `datum_vrijeme_kreiranja` = '$datumSad',`aktivacijski_kod`= '$aktivacija'"
          ." WHERE korisnik = $userID";
    $baza ->updateDB($sql);

    $salji = DohvatiPutanju($aktivacija);
    $mail_to = $email;
    $mail_from = "aktivirajme@stranica.hr";
    $mail_subject = "Aktivacijski link";
    $mail_body = "Poštovani, \r\nŠaljemo vam aktivacijaki link za vaš račun:\r\n$salji \r\nLijep Pozdrav";

    if (mail($mail_to, $mail_subject, $mail_body, $mail_from) || !$baza->pogreskaDB()) 
    {
        ZapisiLogOstalo($baza, $userID, 'Ponovno izdavanje aktivacijskog linka');
        return true;
    } 
    else
    {
        return false;
    }
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

function AzurirajBodove($baza, $kod)
{
    $timeSad = DohvatiVrijemePlusPomak($baza);
    
    $sql = "INSERT INTO `log_bodova`(`korisnik`, `datum_vrijeme_stjecanja`, `vrsta_akcije`) "
          ."VALUES ((SELECT korisnik FROM cekanje_aktivacije WHERE aktivacijski_kod = '$kod'), '$timeSad', 3)";
    $baza->updateDB($sql);
    
    $sql = "UPDATE `korisnik` SET `stanje_bodova`= stanje_bodova + (SELECT `broj_bodova` FROM `vrsta_akcije` WHERE `id_vrste_akcije` = 3) "
          ."WHERE `id_korisnik` = (SELECT korisnik FROM cekanje_aktivacije WHERE aktivacijski_kod = '$kod')";
    $baza->updateDB($sql);
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
            <h2>Aktivacija računa</h2>
            <?php
                $baza = new Baza();
                $baza ->spojiDB();
                
                if (isset($_GET['aktiv']))
                {
                    $kod = $_GET['aktiv'];
                    
                    $sql = "SELECT datum_vrijeme_kreiranja FROM cekanje_aktivacije "
                          ."WHERE aktivacijski_kod = '$kod' AND kod_iskoristen = 0";
                    
                    $rez = $baza -> selectDB($sql);
                    $ispitaj = $rez -> fetch_array();
                    $trenutnoVrijeme = DohvatiVrijemePlusPomak($baza);
                    $date = $ispitaj["datum_vrijeme_kreiranja"];
                    
                    if((strtotime($date)+18000) >= strtotime($trenutnoVrijeme))
                    {
                        $sql = "UPDATE cekanje_aktivacije SET kod_iskoristen = 1 WHERE aktivacijski_kod = '$kod'";
                        $rez = $baza ->updateDB($sql);
                        
                        $sql = "UPDATE korisnik SET aktivan_racun = 1 WHERE id_korisnik = "
                              ."(SELECT korisnik FROM cekanje_aktivacije WHERE aktivacijski_kod = '$kod')";
                        $rez = $baza ->updateDB($sql);
                        
                        AzurirajBodove($baza, $kod);
                        
                        echo "<p><b style='color: #4CAF50; font-size: 35px;'>Vaš račun je aktiviran</b></p><br>"
                            ."<p>Za 5 sekundi biti ćete preusmjereni na prijavu, u suprotnom u izborniku odaberite Prijava</p>";
                    }
                    else
                    {
                        echo "<p><b style='color: lightsalmon; font-size: 35px;'>"
                            ."Vaš aktivacijski kod je istekao.<br>"
                            ."Novi kod poslan Vam je na e-mail</b></p>";
                        
                        $sql = "SELECT korisnik FROM cekanje_aktivacije WHERE aktivacijski_kod = '$kod'";
                        $rez = $baza -> selectDB($sql);
                        $user = $rez -> fetch_array();
                        
                        ResetirajAktivaciju($baza, $user[0]);
                    }
                }
                
                $baza ->zatvoriDB();
                
                header("refresh:5;url=prijava.php");
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


