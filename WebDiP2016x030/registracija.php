<?php

require './reCaptcha/recaptchalib.php';
require './PHP Klase/baza.class.php';

global $registracijaOK;

if(isset($_SERVER["HTTPS"]))
{
    $putanja = $_SERVER["PHP_SELF"];
    $server = $_SERVER["HTTP_HOST"];
                      
    $aktivacijskiLink = "http://";
    $aktivacijskiLink .= $server;
    $aktivacijskiLink .= $putanja;
                       
    header("refresh:0;url=$aktivacijskiLink");
}

function reCAPTCHA()
{
    $privatekey = "6LfegB8UAAAAALqDSj1HzEb3VyKXuvORtEq-rerD";
    $resp = recaptcha_check_answer ($privatekey,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);

    if (!$resp->is_valid) 
    {
        return false;
    } 
    else 
    {
        return true;
    }
}


function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s",($pomak[0]*60*60) + time());
}

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

function ZapisiKorisnika($baza)
{
    $ime = $_POST["ime"];
    $prezime = $_POST["prezime"];
    $korIme = $_POST["korIme"];
    $mailic = $_POST["email"];
    $lozinka = $_POST["lozinka"];
    $kriptirana_lozinka = sha1(sha1(time())." -- ".$lozinka);
    $koraciPrijave = $_POST["dvaKoraka"];
    
    $sqlUnos = "INSERT INTO "
              ."`korisnik`(`tip_korisnika`, `ime`, `prezime`, `korisnicko_ime`, `email`, `lozinka`, `kriptirana_lozinka`, `prijava_dva_koraka`, `aktivan_racun`, `stanje_bodova`) "
            . "VALUES (3,'$ime','$prezime','$korIme','$mailic','$lozinka','$kriptirana_lozinka',$koraciPrijave, 0, 0)";
    $baza ->updateDB($sqlUnos);

    return $baza->pogreskaDB() ? false : true;
}

function PripermiAktivaciju($baza)
{
    $korIme = $_POST["korIme"];
    $aktivacija = sha1(sha1(time())."-".$korIme);

    $sql = "SELECT 1 FROM cekanje_aktivacije WHERE aktivacijski_kod = '$aktivacija'";
    $rez = $baza ->selectDB($sql);
    
    $rezultat = $rez -> fetch_array();
    
    if($rezultat[0] != 1)
    {
        $sql = "SELECT id_korisnik FROM korisnik WHERE korisnicko_ime = '$korIme'";
        $rez = $baza ->selectDB($sql);

        $korID = $rez -> fetch_array();
        $datumSad = DohvatiVrijemePlusPomak($baza);

        $sql = "INSERT INTO `cekanje_aktivacije`(`datum_vrijeme_kreiranja`, `korisnik`, `kod_iskoristen`, `aktivacijski_kod`)"
                . "VALUES ('$datumSad', $korID[0], 0, '$aktivacija')";
        $baza ->updateDB($sql);
        
        
        if($baza -> pogreskaDB())
        {
            return false;
        }

        $salji = DohvatiPutanju($aktivacija);
        $mail_to = $_POST["email"];
        $mail_from = "aktivirajme@stranica.hr";
        $mail_subject = "Aktivacijski link";
        $mail_body = "Poštovani, \r\nŠaljemo vam aktivacijaki link za vaš račun:\r\n$salji \r\nLijep Pozdrav";

        if (mail($mail_to, $mail_subject, $mail_body, $mail_from) || !$baza->pogreskaDB()) 
        {
            ZapisiLogBaze($baza, $sql, $korID[0], 'cekanje_aktivacije');
            return true;
        } 
        else 
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}


function RegistrirajKorisnika($baza)
{
    $baza->selectDB("START TRANSACTION");
    
    $korisnikZapisan = ZapisiKorisnika($baza);
    $aktivacijaSpremna = PripermiAktivaciju($baza);
    
    if($korisnikZapisan && $aktivacijaSpremna)
    {
        $baza->selectDB("COMMIT");
        return true;
    }
    else
    {
        $baza->selectDB("ROLLBACK");
        return false;
    }
}


if(isset($_POST["posalji"]))
{
    $baza = new Baza();
    $baza ->spojiDB();
    
    if(reCAPTCHA()== false)
    {
        $registracijaOK = false;
    }
    else
    {
        $registracijaOK = RegistrirajKorisnika($baza);
    }
    
    $baza -> zatvoriDB();
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
        <link rel="stylesheet" type="text/css" href="CSS/responzivnost.css">
    </head>

    <body>
        <header class="zaglavlje">
            <ul>
                <li><b id="naslovSkupine">e-Spajanje interesnih skupina</b></li>
            </ul>
        </header>        

        <form id="registracija" method="POST" name="registracija" action="registracija.php" novalidate>
            <?php
            
            if(isset($_POST["posalji"]))
            {
                print $registracijaOK === true ?
                        "<p id='notifikacija'><b style='color: green; font-size: 25px;'>Registracija uspješna</b></p>"
                        :
                        "<p id='notifikacija'><b  style='color: lightsalmon; font-size: 25px;'>Došlo je do greške!</b></p>";
            }
            ?>
            <h2 id="idRegistracijaH2">Registracija</h2>
            <p class="podaci">
                <label for="ime">Ime:</label>
                <input id="ime" name="ime" type="text">
                <label for="prezime">Prezime:</label>
                <input id="prezime" name="prezime" type="text">
                <label for="korIme">Korisničko ime:</label>
                <input id="korIme" name="korIme" type="text" required="required">
                <label for="email">E-mail:</label>
                <input id="email" name="email" type="text" required="required">
                <label for="lozinka">Lozinka:</label>
                <input id="lozinka" name="lozinka" type="password" required="required">
                <label for="reLozinka">Potvrda lozinke:</label>
                <input id="reLozinka" name="reLozinka" type="password" required="required">
            </p>
            <p id="radioDvaKoraka">Prijava u dva koraka:</p>
            <label for="dvaKorakaDa">Da</label>
            <input id="dvaKorakaDa" name="dvaKoraka" type="radio" value="1" checked="checked">
            <label for="dvaKorakaNe">Ne</label>
            <input id="dvaKorakaNe" name="dvaKoraka" type="radio" value="0">
            
            <!-- reCaptcha unos -->
            <?php
            require_once('./reCaptcha/recaptchalib.php');
            $publickey = "6LfegB8UAAAAANUT8y9cQTP25V7Q09U7dZqfLCOY"; // you got this from the signup page
            echo recaptcha_get_html($publickey);
            ?>
            
            <p id="kontrole">
                <input id="tipkaSend" name="posalji" type="submit" value="Pošalji">
                <br><br>
                <a class="link" href="prijava.phpp">Prijavite se</a>
                <a class="link" href="podrucjaZaKorisnika.php">Preskoči</a>
                <br><br>
            </p>
        </form>

        <footer id="footerPrijava">
            <p style="text-align: center;">
                Registracija u sustav e-Interesnih skupina<br>
                Vrijeme potrebno za rješavanje aktivnog dokumenta: 15 min<br>
                &copy; 2017 A.Danzante
            </p>
        </footer>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="jQuery/jQuery_skripta.js"></script>
        
    </body>
</html>
