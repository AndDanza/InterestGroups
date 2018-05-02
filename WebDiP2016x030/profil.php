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
}

function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s", ($pomak[0]*60*60) + time());
}

function OdabranoPodrucje($korisnik, $id_podrucja, $baza)
{
    $id_korisnika = intval($korisnik -> get_id());
    $sql = "SELECT podrucja_interesa_id_podrucja AS podrucja FROM odabir_podrucja_interesa "
          ."WHERE korisnik_id_korisnik = $id_korisnika "
          ."AND datum_vrijeme_prekida = '0000-00-00 00:00:00'";
    $rez = $baza -> selectDB($sql);
    
    while(list($id) = $rez -> fetch_array())
    {
        if($id === $id_podrucja)
        {
            return true;
        }
    }
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


function AzurirajKorisnika($baza, $korisnik)      
{
    $id = intval($korisnik->get_id());
    $ime = $_POST["novoIme"];
    $prezime = $_POST["novoPrezime"];
    $korIme = $_POST["korisnicko_ime"];
    $mailic = $_POST["mail"];
    $lozinka = $_POST["pass"];
    $kriptirana_lozinka = sha1(sha1(time())." -- ".$lozinka);
    $koraciPrijave = $_POST["brKoraka"];
    
    $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
    $korisnik->azuriraj_podatke($korIme, $ime, $prezime, $lozinka, $mailic);
    $_SESSION["aktivniKorisnik"] = serialize($korisnik);
    
    $sql = "UPDATE `korisnik` SET `ime`='$ime',`prezime`='$prezime',`korisnicko_ime`='$korIme',"
          ."`email`='$mailic',`lozinka`='$lozinka',`kriptirana_lozinka`='$kriptirana_lozinka',"
          ."`prijava_dva_koraka`='$koraciPrijave' WHERE id_korisnik = $id";
    $baza->updateDB($sql);
    
    ZapisiLogBaze($baza, $sql, $id, 'korisnik');
    
    return $baza->pogreskaDB() ? false : true;
}


function PohraniPodrucje($baza, $value, $korisnik_id)
{
    $vrijeme = DohvatiVrijemePlusPomak($baza);
    $podrucje = intval($value);
            
    $sql = "INSERT INTO `odabir_podrucja_interesa`(`korisnik_id_korisnik`, `podrucja_interesa_id_podrucja`, `datum_vrijeme_odabira`) "
          ."VALUES ($korisnik_id, $podrucje, '$vrijeme')";
    $baza->updateDB($sql);
    
    $sql = "SELECT id_diskusija FROM diskusija WHERE podrucja_interesa = $podrucje ";
    $rez = $baza->selectDB($sql);
    
    while(list($id_diskusije) = $rez->fetch_array())
    {
       $sql = "INSERT INTO `pretplata`(`korisnik`, `diskusija`, `datum_vrijeme_pretplate`) "
          ."VALUES ($korisnik_id, $id_diskusije, '$vrijeme')";
        $baza->updateDB($sql); 
    }
}


function RazlikaPodrucja($podrucje)
{
    $odabranaPodrucja = $_POST["odabirPodrucja"];
    $uPolju = false;
    
    foreach ($odabranaPodrucja as $value)
    {
        if($value === $podrucje)
        {
            $uPolju = true;
        }
    }
    
    if($uPolju)
    {
        return -1;
    }
    else
    {
        return $podrucje;
    }
}

function OdabirPodrucja($baza, $korisnik)
{
    $korisnik_id = intval($korisnik->get_id());
    $odabranaPodrucja = $_POST["odabirPodrucja"];
    
    $sql = "SELECT COUNT(*) FROM odabir_podrucja_interesa WHERE korisnik_id_korisnik = $korisnik_id";
    $rez = $baza -> selectDB($sql);
    list($brPodrucja) = $rez->fetch_array();
    
    if($brPodrucja > 0)
    {
        foreach ($odabranaPodrucja as $value) 
        {
            if(!OdabranoPodrucje($korisnik, $value, $baza))
            {
                PohraniPodrucje($baza, $value, $korisnik_id);
                AzurirajBodove($baza, $korisnik_id);
            }
        }
        
        $sql = "SELECT podrucja_interesa_id_podrucja AS podrucja FROM odabir_podrucja_interesa "
          ."WHERE korisnik_id_korisnik = $korisnik_id "
          ."AND datum_vrijeme_prekida = '0000-00-00 00:00:00'";
        $rez = $baza -> selectDB($sql);
        $vrijeme = DohvatiVrijemePlusPomak($baza);
        
        while(list($spremljenaPodrucja) = $rez->fetch_array())
        {
            if(RazlikaPodrucja($spremljenaPodrucja) !== -1)
            {
                $sql = "UPDATE `odabir_podrucja_interesa` SET `datum_vrijeme_prekida`='$vrijeme' "
                      ."WHERE `korisnik_id_korisnik` = $korisnik_id "
                      ."AND `podrucja_interesa_id_podrucja` = $spremljenaPodrucja";
                $baza->updateDB($sql);
            }
        }
    }
    else
    {
        foreach ($odabranaPodrucja as $value) 
        {
            PohraniPodrucje($baza, $value, $korisnik_id);
            AzurirajBodove($baza, $korisnik_id);
        }
    }
    
    return $baza->pogreskaDB() ? false : true;
}

function AzurirajBodove($baza, $id_korisnika)
{
    $timeSad = DohvatiVrijemePlusPomak($baza);
    
    $sql = "INSERT INTO `log_bodova`(`korisnik`, `datum_vrijeme_stjecanja`, `vrsta_akcije`) "
          ."VALUES ($id_korisnika, '$timeSad', 5)";
    $baza->updateDB($sql);
    
    $sql = "UPDATE `korisnik` SET `stanje_bodova`= stanje_bodova + (SELECT `broj_bodova` FROM `vrsta_akcije` WHERE `id_vrste_akcije` = 5) "
          ."WHERE `id_korisnik` = $id_korisnika";
    $baza->updateDB($sql);
    
    $sql = "SELECT stanje_bodova FROM korisnik WHERE id_korisnik = $id_korisnika";
    $rez = $baza->selectDB($sql);
    list($bodovi) = $rez->fetch_array();
    
    $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
    $korisnik->set_brBodova($bodovi);
    $_SESSION["aktivniKorisnik"] = serialize($korisnik);
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
        <meta property="og:url" content="<?php "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]?>" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="eInteresne skupine" />
        <meta property="og:description" content="Najbolje mjesto za informacije, kontorlirano i sigurno okruženje za djeljenje informacija" />

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
                        echo '<li><a style="background: gainsboro;" href="profil.php"><b>Profil</b></a></li>';
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
                            echo '<li><a href="obavijesti.php"><b>Obavijesti</b></a></li>';
                        }
                        echo '<li><a href="kuponi.php"><b>Kuponi članstva</b></a></li>';
                    }
                    ?>
                    <li><a href="podrucjaZaKorisnika.php"><b>Područja interesa</b></a></li>
                </ul>
            </nav>
        </div>
        
        <form id="korisnikPodaci" method="POST" name="azuriranje" action="profil.php" novalidate>
            <?php
            if(isset($_POST["azuriraj"]))
            {
                $baza = new Baza();
                $baza ->spojiDB();

                $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                
                if(AzurirajKorisnika($baza, $korisnik))
                {
                    print "<p id='notifikacija'><b style='color: green; font-size: 25px;'>Korisnik ažuriran</b></p>";      
                }
                else
                {
                    print "<p id='notifikacija'><b  style='color: lightsalmon; font-size: 25px;'>Došlo je do greške!</b></p>";
                }
                
                
                if(isset($_POST["odabirPodrucja"]))
                {
                    $brPodrucja = count($_POST["odabirPodrucja"]);
                    if($brPodrucja > 0)
                    {
                       if(OdabirPodrucja($baza, $korisnik))
                        {
                            print "<p id='notifikacija'><b style='color: green; font-size: 25px;'>Vaša područja su zabilježena!</b></p>";
                        }
                        else
                        {
                            print "<p id='notifikacija'><b  style='color: lightsalmon; font-size: 25px;'>Došlo je do greške!</b></p>";
                        } 
                    }
                }
                
                $baza ->zatvoriDB();
                
                header("refresh:1;url=profil.php");
            }
            ?>
            <h2>Osobni podaci</h2>
            <p class="podaci">
                <label for="novoIme">Ime:</label>
                <input id="novoIme" name="novoIme" type="text" value="<?php echo $korisnik->get_ime(); ?>" disabled="disabled">
                <label for="novoPrezime">Prezime:</label>
                <input id="novoPrezime" name="novoPrezime" type="text" value="<?php echo $korisnik->get_prezime(); ?>" disabled="disabled">
                <label for="korisnicko_ime">Korisničko ime:</label>
                <input id="korisnicko_ime" name="korisnicko_ime" type="text" value="<?php echo $korisnik->get_kor_ime(); ?>" disabled="disabled">
                <label for="mail">E-mail:</label>
                <input id="mail" name="mail" type="text" value="<?php echo $korisnik->get_email(); ?>" disabled="disabled">
                <label for="pass">Lozinka:</label>
                <input id="pass" name="pass" type="password" value="<?php echo $korisnik->get_lozinka(); ?>" disabled="disabled">
                <label for="rePass">Potvrda lozinke:</label>
                <input id="rePass" name="rePass" type="password" value="<?php echo $korisnik->get_lozinka(); ?>" disabled="disabled">
            </p>    
            <p id="brKoraka">Prijava u dva koraka:</p>
            <label for="brKorakaDa">Da</label>
            <input id="brKorakaDa" name="brKoraka" type="radio" value="1">
            <label for="brKorakaNe">Ne</label>
            <input id="brKorakaNe" name="brKoraka" type="radio" value="0" checked>
            
            <?php
            $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
            $vrsta = intval($korisnik->get_vrsta_korisnika());
                
            if($vrsta === 3)
            {
                echo '<label for="odabirPodrucja" style="margin-left: 10%;">Odabir područja:</label>';
                echo '<select id="odabirPodrucja" name="odabirPodrucja[]" multiple="multiple" disabled>';
                $baza = new Baza();
                $baza->spojiDB();
                
                $sql = "SELECT id_podrucja, naziv_podrucja FROM podrucja_interesa";
                $rez = $baza -> selectDB($sql);
                while(list($id, $naziv) = $rez->fetch_array())
                {
                    echo "<option value='$id'";
                    if(OdabranoPodrucje($korisnik, $id, $baza))
                    {
                        echo "selected='selected'";
                    }
                    echo ">$naziv</option>";
                }
                echo '</select>';
                $baza->zatvoriDB();
            }
            
            ?>
            <label for="brBodova">Stanje bodova:  <?php echo $korisnik->get_brBodova(); ?></label><br>
            <p id="kontrole">
                <input id="uredi" name="uredi" type="button" value="Uredi profil">
                <input id="tipkaAzuriraj" name="azuriraj" type="submit" value="Pošalji" hidden="hidden">
            </p>
        </form>

        <footer id="footerPrijava">
            <div style="text-align: center; padding-bottom: 5px;">
                Područja interesa e-Interesnih skupina<br>
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
        <script src="jQuery/jQuery_skripta.js"></script>
        <script src="jQuery/socijalneMreze.js"></script>
    </body>
</html>