<?php

if(!isset($_SERVER["HTTPS"]))
{
    $putanja = $_SERVER["PHP_SELF"];
    $server = $_SERVER["HTTP_HOST"];
                      
    $aktivacijskiLink = "https://";
    $aktivacijskiLink .= $server;
    $aktivacijskiLink .= $putanja;
    
    header("refresh:0;url=$aktivacijskiLink");
}

require './PHP Klase/baza.class.php';
require './PHP Klase/korisnik.class.php';

global $prijavaOK;
global $sakrijFormu;

session_start();

function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s", ($pomak[0]*60*60) + time());
}

//kreiranje objekta tipa korisnik.class.php i pohrana u sesiju te zapis u dnevnike
function AktivirajSesiju($baza, $user, $vrsta)
{
    $prijavljeniKorisnik = new Korisnik();
    $vrijeme = DohvatiVrijemePlusPomak($baza);
            
    switch($vrsta)
    {
        case 1:
            $sql = "SELECT id_korisnik, ime, prezime, lozinka, tip_korisnika, email, stanje_bodova FROM korisnik"
                  ." WHERE korisnicko_ime = '$user'";
            $rez = $baza-> selectDB($sql);
            $red = $rez -> fetch_array();
            $prijavljeniKorisnik -> set_podaci($red["id_korisnik"], $user, $red["ime"], $red["prezime"], $red["lozinka"], $red["tip_korisnika"], $red["email"], $vrijeme);
            $prijavljeniKorisnik->set_brBodova($red["stanje_bodova"]);
            break;
        case 2:
            $sql = "SELECT id_korisnik, korisnicko_ime, ime, prezime, lozinka, tip_korisnika, email, stanje_bodova FROM korisnik"
              ." WHERE id_korisnik = (SELECT korisnik FROM prijava_dva_koraka WHERE jednokratni_kod = '$user')";
            $rez = $baza-> selectDB($sql);
            $red = $rez -> fetch_array();
            $prijavljeniKorisnik -> set_podaci($red["id_korisnik"], $red["korisnicko_ime"], $red["ime"], $red["prezime"], $red["lozinka"], $red["tip_korisnika"], $red["email"], $vrijeme);
            $prijavljeniKorisnik->set_brBodova($red["stanje_bodova"]);
            break;
    }
    
    $_SESSION["aktivniKorisnik"] = serialize($prijavljeniKorisnik);
    LogPrijava($prijavljeniKorisnik, $baza);
}

//pohrana korisnika u talbicu pogrešna prijava
function KrivaPrijava($baza, $korIme)
{
    if(!isset($_SESSION[$korIme]))
    {
        $_SESSION[$korIme] = 0;
    }
    
    $_SESSION[$korIme]++;

    if($_SESSION[$korIme] >= 3)
    {
        $datum = DohvatiVrijemePlusPomak($baza);
        
        $sql = "INSERT INTO `pogresna_prijava`(`korisnik`, `datum_vrijeme_pokusaja`, `racun_zakljucan`)"
               ."VALUES ((SELECT id_korisnik FROM korisnik WHERE korisnicko_ime = '$korIme'), '$datum', 1)";
        $baza -> updateDB($sql);
        
        $sql = "UPDATE korisnik SET `aktivan_racun` = 0 WHERE korisnicko_ime = '$korIme'";
        $baza->updateDB($sql);
        
        $sql = "SELECT id_korisnik FROM korisnik WHERE korisnicko_ime = '$korIme'";
        $rez = $baza ->selectDB($sql);
        $korID = $rez -> fetch_array();
    
        ZapisiLogBaze($baza, $sql, $korID[0], 'pogresna_prijava');
    }
}


//provjera ako se korisnik možprijaviti (je li u tablici pogrešna prijava)
//0 - provjera nije uredu, 1 - provjera uredu, -1 - već u bazi pogrešnih prijava
function ProvjeraPrijave($baza)
{
    $prijavaUredu = 0;

    if (!empty($_POST["username"]) && !empty($_POST["password"])) 
    {
        $korIme = $_POST["username"];
        
        $sql = "SELECT 1 FROM `pogresna_prijava` WHERE korisnik = "
                . "(SELECT id_korisnik FROM korisnik WHERE korisnicko_ime = '$korIme') AND racun_zakljucan = 1 "
              ."UNION "
              ."SELECT 1 FROM korisnik WHERE korisnicko_ime = '$korIme' AND aktivan_racun = 0";
        $rez = $baza ->selectDB($sql);
        $zakljucan = $rez -> fetch_array();
        
        if($zakljucan[0] != 1)
        {
            $sql = "SELECT lozinka "
                    . "FROM  `korisnik` "
                    . "WHERE korisnicko_ime = '$korIme'";
            $rezultat = $baza->selectDB($sql);
            
            if ($rezultat) 
            {
                $row = $rezultat->fetch_array();
                if ($row["lozinka"] == $_POST["password"]) 
                {
                    $prijavaUredu = 1;
                }
            }
        }
        else
        {
            $prijavaUredu = -1;
        }
    }

    return $prijavaUredu;
}


//izdavanje petominutnog koda za prijavu
function RukovanjeJednokratnimKodom($baza)
{
    $korIme = $_POST["username"];
    $jednokratniKod = sha1(sha1(time())."-".$korIme);
    echo $jednokratniKod;
    
    $sql = "SELECT id_korisnik, email  FROM `korisnik` WHERE korisnicko_ime = '$korIme'";
    $rez = $baza ->selectDB($sql);
    list($korID, $mail) = $rez -> fetch_array();
    $datumSad = DohvatiVrijemePlusPomak($baza);
    
    $sql ="INSERT INTO `prijava_dva_koraka`(`korisnik`, `datum_vrijeme_izdavanja_koda`, `jednokratni_kod`, `iskoristen`)"
         ."VALUES ((SELECT `id_korisnik` FROM `korisnik` WHERE korisnicko_ime = '$korIme'), '$datumSad', '$jednokratniKod', 0)";
    $baza -> updateDB($sql);
    
    ZapisiLogBaze($baza, $sql, $korID[0], 'prijava_dva_koraka');
    
    $mail_to = $mail;
    $mail_from = "aktivirajme@stranica.hr";
    $mail_subject = "Jednokratni kod";
    $mail_body = "Poštovani, Šaljemo vam jednokratni kod za prijavu: $jednokratniKod Lijep Pozdrav";

    if (mail($mail_to, $mail_subject, $mail_body, $mail_from)) 
    {
        ZapisiLogOstalo($baza, $korID[0], 'Izdan jednokratni kod korisniku.');
        return true;
    } 
    else 
    {
        return false;
    }
}


//uz provjeru brišu se svi kodovi korisnika koje nije uspio iskoristiti
function ProvjeriJednokratniKod($baza, $uneseniKod)
{
    $prijavaProsla = true;
    
    $sql ="SELECT datum_vrijeme_izdavanja_koda, korisnik FROM `prijava_dva_koraka` "
         ."WHERE jednokratni_kod = '$uneseniKod' AND iskoristen = 0";
    $rez = $baza ->selectDB($sql);
    
    if($rez == null)
    {
        return false;
    }
    else
    {
        list($vrijeme, $korisnik) = $rez -> fetch_array();
        
        if(strtotime(DohvatiVrijemePlusPomak($baza)) <= (strtotime($vrijeme)+300))
        {
            $sql ="UPDATE `prijava_dva_koraka` SET iskoristen = 1 WHERE jednokratni_kod = '$uneseniKod'";
            $baza -> updateDB($sql);
            
            $sql = "SELECT korisnicko_ime FROM korisnik WHERE id_korisnik = $korisnik";
            $rez = $baza -> selectDB($sql);
            $korIme = $rez -> fetch_array();
                    
            PostaviCookiePrijave($korIme[0]);
            
            $prijavaProsla = true;
        }
        else
        {
            $prijavaProsla = false;
        }
    }
    
    $sql ="DELETE FROM `prijava_dva_koraka` WHERE korisnik = $korisnik AND iskoristen = 0";
    $baza -> updateDB($sql);
    
    return $prijavaProsla;
}


//pamćenje zadnje uspješne prijave
function PostaviCookiePrijave($korisnik)
{
    $baza = new Baza();
    $baza ->spojiDB();
    
    if(!isset($_COOKIE["zadnjaPrijava"]))
    {
        setcookie("zadnjaPrijava", $korisnik, (strtotime(DohvatiVrijemePlusPomak($baza))+(86400 * 30)));
        ZapisiLogOstalo($baza, $korisnik, 'Kreiran cookie za novu prijavu na računalu korisnika.');
    }
    else 
    {
        setcookie("zadnjaPrijava", $korisnik, (strtotime(DohvatiVrijemePlusPomak($baza))+(86400 * 30)));
        unset($_COOKIE["zadnjaPrijava"]);
        
        setcookie("zadnjaPrijava", $korisnik, (strtotime(DohvatiVrijemePlusPomak($baza))+(86400 * 30)));
        ZapisiLogOstalo($baza, $korisnik, 'Kreiran cookie za novu prijavu na računalu korisnika.');
    }
    
    $baza ->zatvoriDB();
}


function NovaLozinka($baza)
{
    $novaLozinka = array_merge(range('a', 'z'), range('A', 'Z'));
    shuffle($novaLozinka);
    $od = rand(0, (count($novaLozinka)-15));
    $novaLozinka = substr(implode($novaLozinka), $od, ($od+15));
    $korisnik = $_GET["reset"];
    $kriptirana_lozinka = sha1(sha1(time())." -- ".$novaLozinka);
        
    $sql = "UPDATE `korisnik` SET `lozinka` = '$novaLozinka',`kriptirana_lozinka`= '$kriptirana_lozinka'"
          ."WHERE korisnicko_ime = '$korisnik'";
    $baza -> updateDB($sql);
    
    $sql = "SELECT id_korisnik, email FROM korisnik WHERE korisnicko_ime = '$korisnik'";
    $rez = $baza ->selectDB($sql);
    list($korID, $mail_to) = $rez -> fetch_array();
    
    ZapisiLogBaze($baza, $sql, $korID, 'korisnik');

    $mail_from = "reset.lozinke@stranica.hr";
    $mail_subject = "Lozinka resetirana";
    $mail_body = "Poštovani, \r\nŠaljemo vam novu lozinku za vaš račun:\r\n$novaLozinka \r\nLijep Pozdrav";

    if (mail($mail_to, $mail_subject, $mail_body, $mail_from) || !$baza->pogreskaDB()) 
    {
        ZapisiLogOstalo($baza, $korID, 'Poslan mail korisniku za novu lozinku.');
        return true;
    }
    else
    {
        return false;
    }
}


function LogPrijava($korisnik, $baza)
{
    $datumVrijeme = $korisnik->get_prijavljen_od();
    $id = $korisnik->get_id();
    $sql = "INSERT INTO `log_aplikacije`(`datum_vrijeme_akcije`, `korisnik_id_korisnik`) "
           ."VALUES ('$datumVrijeme', $id)";
    $baza -> updateDB($sql);
    
    $sql = "INSERT INTO `log_aplikacije_prijava`(`korisnik`, `datum_vrijeme_akcije`) "
            . "VALUES ($id, '$datumVrijeme')";
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


function AzurirajBodove($baza)
{
    $timeSad = DohvatiVrijemePlusPomak($baza);
    $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
    $id_korisnika = intval($korisnik->get_id());
    
    $sql = "INSERT INTO `log_bodova`(`korisnik`, `datum_vrijeme_stjecanja`, `vrsta_akcije`) "
          ."VALUES ($id_korisnika, '$timeSad', 4)";
    $baza->updateDB($sql);
    
    $sql = "UPDATE `korisnik` SET `stanje_bodova`= stanje_bodova + (SELECT `broj_bodova` FROM `vrsta_akcije` WHERE `id_vrste_akcije` = 4) "
          ."WHERE `id_korisnik` = $id_korisnika";
    $baza->updateDB($sql);
    
    $sql = "SELECT stanje_bodova FROM korisnik WHERE id_korisnik = $id_korisnika";
    $rez = $baza->selectDB($sql);
    list($bodovi) = $rez->fetch_array();
    
    $korisnik->set_brBodova($bodovi);
    $_SESSION["aktivniKorisnik"] = serialize($korisnik);
}


?>

<!DOCTYPE html>

<html lang="hr">
    <head>
        <title>Interesne skipine - Prijava</title>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="naslov" content="Prijava">
        <meta name="kljucneRijeci" content="FOI, WebDiP, HTML, CSS">
        <meta name="datum" content="06.05.2017.">
        <meta name="autor" content="anddanzan">
        <meta property="og:url" content="<?php "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]?>" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="eInteresne skupine" />
        <meta property="og:description" content="Najbolje mjesto za informacije, kontorlirano i sigurno okruženje za djeljenje informacija" />

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
        
        <?php
        
        if(isset($_POST["posaljiPrijavu"]))
        {
            $baza = new Baza();
            $baza->spojiDB();
            $korIme = $_POST["username"];
            
            $prijavaOK = ProvjeraPrijave($baza);
            if ($prijavaOK == 1) 
            {
                $sql = "SELECT 1 FROM  `korisnik` "
                      ."WHERE korisnicko_ime = '$korIme' AND prijava_dva_koraka = 1";
                $rezultat = $baza->selectDB($sql);
                $koristiKod = $rezultat -> fetch_array();            

                if($koristiKod[0] == 1)
                {
                    RukovanjeJednokratnimKodom($baza);
                    print '<form id="prijavaDvaKoraka" method="POST" name="prijavaDva" action="prijava.php" novalidate>'
                         .'<p id="notifikacija"><b style="color: green; font-size: 25px;">Prijava u jedan korak uspješna</b></p>';
                    print '<h2>Jednokratni kod</h2>'
                         .'<p class="podaci">'
                         .'<input id="korak2korisnik" name="korak2korisnik" type="text" value="';
                    echo $korIme;
                    print '" hidden>'
                         .'<label for="dvaKoraka">Lozinka:</label>'
                         .'<input id="dvaKoraka" name="dvaKoraka" type="password" required="required">'
                         .'</p>'
                         .'<input id="provjeraKoda" name="potvrdi" type="submit" value="Pošalji">'
                         .'</form>';
                }
                else
                {
                    PostaviCookiePrijave($korIme);
                    AktivirajSesiju($baza, $korIme, 1);
                    AzurirajBodove($baza);
                    $sakrijFormu = false;
                    header("refresh:1;url=profil.php");
                }
            }
            else if($prijavaOK == 0)
            {
                KrivaPrijava($baza, $korIme);
            }
            
            $baza ->zatvoriDB();
        }
        
        ?>
        
        <form id="prijava" method="POST" name="prijava" action="prijava.php" 
            <?php if(isset($_POST["posaljiPrijavu"]) && ($prijavaOK == 1) && $sakrijFormu){echo "hidden";}?> novalidate="">
            <?php
            if(isset($_GET["reset"]))
            {
                $baza = new Baza();
                $baza->spojiDB();
                
                if(NovaLozinka($baza))
                {
                    print "<p id='notifikacija'><b style='color:goldenrod; font-size: 25px;'>Nova lozinka Vam je poslana na mail</b></p>";
                }
                
                $baza->zatvoriDB();
            }
        
            if(isset($_POST["posaljiPrijavu"]))
            {
                if($prijavaOK == 1)
                {
                    print "<p id='notifikacija'><b style='color: green; font-size: 25px;'>Prijava u jedan korak uspješna</b></p>";
                }
                else if($prijavaOK == 0)
                {
                    print "<p id='notifikacija'><b  style='color: lightsalmon; font-size: 25px;'>Prijava neuspješna</b></p>";
                }
                else
                {
                    print "<p id='notifikacija'><b  style='color:goldenrod; font-size: 25px;'>Vaš račun je blokiran.<br>Morate čekati da Vas admin odblokira.</b></p>";
                }   
            }
            
            if(isset($_POST["potvrdi"]))
            {
                $baza = new Baza();
                $baza->spojiDB();
                
                $kod = $_POST["dvaKoraka"];
                $korisnicko_ime = $_POST["korak2korisnik"];
                if(ProvjeriJednokratniKod($baza, $kod))
                {
                    print '<p id="notifikacija"><b style="color: green; font-size: 25px;">Drugi korak prijave uspješan</b></p>';
                    AktivirajSesiju($baza, $kod, 2);
                    AzurirajBodove($baza);
                    header("refresh:1;url=profil.php");
                    $sakrijFormu = true;
                }
                else
                {
                    KrivaPrijava($baza, $korisnicko_ime);
                    print '<p id="notifikacija"><b style="color: lightsalmon; font-size: 25px;">Uneseni jednokratni kod nije valjan. Ponovite prijavu</b></p>';
                }
                
                $baza ->zatvoriDB();
            }
            
            ?>
            <h2>Prijava</h2>
            <p class="podaci">
                <label for="username">Korisničko ime:</label>
                <input id="username" name="username" type="text" value="<?php if(isset($_COOKIE["zadnjaPrijava"])){echo $_COOKIE["zadnjaPrijava"];} ?>" required="required">
            </p>
            <p class="podaci">
                <label for="password">Lozinka:</label>
                <input id="password" name="password" type="password" required="required">
            </p>
            <p id="kontrole">
                <input id="posaljiPrijavu" name="posaljiPrijavu" type="submit" value="Pošalji">
                <br><br>
                <a class="link" href="registracija.php">Registracija</a>
                <a class="link" href="podrucjaZaKorisnika.php">Preskoči</a>
                <br><br>
                <a class="link" id="resetLozinke" href="prijava.php?reset=" style="color: lightsalmon;">Zaboravili ste lozinku?</a>
            </p>
        </form>
        
        <?php
        
        if(!isset($_SESSION["aktivniKorisnik"]) && !isset($_COOKIE["neregistriraniKorisnik"]))
        {
            echo "<p id='uvjetiKoristenja'>Ova stranica koristi kolačiće za neregistrirane korisnike. "
           ."Prihvaćanjem ovog kolačića prihvaćate uvjete korištenje stranica"
           ."<br><input id='slazemSe' type='button' value='Slažem se'></p>";
        }
        
        ?>
        
        <footer id="footerPrijava">
            <div style="text-align: center; padding-bottom: 5px;">
                Prijava u sustav e-Interesnih skupina<br>
                <div class="fb-share-button" data-layout="button_count" data-size="small" data-mobile-iframe="true">
                   <a class="fb-xfbml-parse-ignore" target="_blank" 
                      href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse">Share
                   </a>
                </div>
                <a class="twitter-share-button"
                    href="https://twitter.com/intent/tweet">
                Tweet</a><br>
                &copy; 2017 A.Danzante
            </div>
        </footer>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
        <script src="jQuery/jQuery_skripta.js"></script>
        <script src="jQuery/socijalneMreze.js"></script>
      
    </body>
</html>
