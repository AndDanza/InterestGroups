<?php

session_start();
require './PHP Klase/baza.class.php';
require './PHP Klase/korisnik.class.php';

function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s", ($pomak[0]*60*60) + time());
}


function AzurirajBodove($baza, $id_korisnika)
{
    $timeSad = DohvatiVrijemePlusPomak($baza);
    
    $sql = "INSERT INTO `log_bodova`(`korisnik`, `datum_vrijeme_stjecanja`, `vrsta_akcije`) "
          ."VALUES ($id_korisnika, '$timeSad', 6)";
    $baza->updateDB($sql);
    
    $sql = "UPDATE `korisnik` SET `stanje_bodova`= stanje_bodova + (SELECT `broj_bodova` FROM `vrsta_akcije` WHERE `id_vrste_akcije` = 6) "
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

<html lang="hr">
    <head>
        <title>Aktivacija korisnika</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="naslov" content="Aktivacija korisnika">
        <meta name="kljucneRijeci" content="FOI, WebDiP, HTML, CSS">
        <meta name="datum" content="07.03.2017.">
        <meta name="autor" content="anddanzan">
        <meta property="og:url" content="<?php "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]?>" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="eInteresne skupine" />
        <meta property="og:description" content="Najbolje mjesto za informacije, kontorlirano i sigurno okruženje za djeljenje informacija" />

        <link rel="stylesheet" type="text/css" href="./CSS/glavniCSS.css">
        <link rel="stylesheet" type="text/css" href="./CSS/formeCSS.css">
        <!-- <link rel="stylesheet" type="text/css" href="CSS/responzivnost.css"> -->
    </head>
    <body>
        <header class="zaglavlje">
            <ul>
                <li><b id="naslovSkupine">e-Spajanje interesnih skupina</b></li>
                <?php
                if(!isset($_SESSION["aktivniKorisnik"]))
                {
                    echo '<li class="desnaTipka"><a href="prijava.php">Prijava</a></li>';
                    
                }
                else
                {
                    echo '<li class="odjava"><a href="odjava.php?odjava=true">Odjava</a></li>';
                    echo '<li style="background: gainsboro;" id="cart"><a href="kosarica.php"><img src="Slike/kosarica.png" alt="kosarica"></a></li>';
                }
                ?>
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
                            echo '<li><a href="pregledKorisnikaModerator.php"><b>Korisnici područja</b></a></li>';
                            echo '<li><a href="obavijesti.php"><b>Obavijesti</b></a></li>';
                        }
                        echo '<li><a href="kuponi.php"><b>Kuponi članstva</b></a></li>';
                    }
                    else
                    {
                        echo '<li><a href="prijava.php"><b>Prijava</b></a></li>';
                        echo '<li><a href="registracija.php"><b>Registracija</b></a></li>';
                    }
                    ?>
                    <li><a href="podrucjaZaKorisnika.php"><b>Područja interesa</b></a></li>
                </ul>
            </nav>
        </div>

        <section id="kuponiSekcija">
            <?php
            $baza= new Baza();
            $baza->spojiDB();
            
            if(!isset($_SESSION["kosarica"]) || count($_SESSION["kosarica"]) <= 0)
            {
                print "<p id='notifikacija'><b style='color: lightsalmon; font-size: 25px;'>Trenutno nema kupona u košarici</b></p>";            
            }
            
            if(isset($_GET["ukloni"]) && isset($_GET["naziv"]))
            {
                $id_kupon = intval($_GET["ukloni"]);
                $nazivKupona = $_GET["naziv"];
                $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                $stanje = intval($korisnik->get_brBodova());
                
                $sql = "SELECT `potrebno_bodova` FROM `kupon_clanstva` "
                  ."WHERE `id_kupona` = $id_kupon";
                $rez = $baza->selectDB($sql);
                list($potrebno) = $rez->fetch_array();
                
                $korisnik -> set_brBodova($potrebno+$stanje);
                
                unset($_SESSION["kosarica"][$id_kupon]);
                print "<p id='notifikacija'><b style='color: lightsalmon; font-size: 25px;'>Kupon $nazivKupona uklonjen</b></p>";
            
                $_SESSION["aktivniKorisnik"] = serialize($korisnik);
            }
            
            if(isset($_POST["potvrdiKupnju"]))
            {
                $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                $id_korisnik = intval($korisnik->get_id());
                $vrijeme = DohvatiVrijemePlusPomak($baza);
                
                $sql = "INSERT INTO `kosarica`(`korisnik`, `datum_vrijeme_kupnje`) "
                      ."VALUES ($id_korisnik,'$vrijeme')";
                $baza->updateDB($sql);
                
                $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                $id_kor = intval($korisnik->get_id());
                $stanje = intval($korisnik->get_brBodova());
                
                $sql = "UPDATE `korisnik` SET `stanje_bodova` = $stanje WHERE `id_korisnik` = $id_kor";
                $baza->updateDB($sql);
                
                $uKosarici = $_SESSION["kosarica"];
                foreach($uKosarici as $kljuc => $vrij)
                {
                    $kupon = intval($kljuc);
                    $sql = "INSERT INTO `sadrzaj_kosarice`(`kosarica_id_kosarica`, `kupon_clanstva_id_kupona`) "
                          ."VALUES ((SELECT id_kosarica FROM kosarica WHERE korisnik = $id_korisnik AND datum_vrijeme_kupnje = '$vrijeme'), $kupon)";
                    $baza->updateDB($sql);
                }
                
                AzurirajBodove($baza, $id_korisnik);
                        
                unset($_SESSION["kosarica"]);
                
            }
            
            echo '<h2>Kuponi članstva</h2>';
            
            if(isset($_SESSION["aktivniKorisnik"]))
            {
                if(isset($_SESSION["kosarica"]))
                {
                    $uKosarici = $_SESSION["kosarica"];
                    
                    echo '<div id="kosaricaKor"></div>';
                    
                    echo '<div style="float: right; margin-top: 2%;" style="clear:left; float:right;">'
                    .'<a id="krajKosarica" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>'
                    .'<a id="sljedecaKosarica" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>'
                    .'<input id="trenStranicaKosarica" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">'
                    .'<a id="prethodniKosarica" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>'
                    .'<a id="pocetakKosarica" style="float: right; margin: 3% 2% 0% 1%">Početak</a>'
                    .'</div>';
                    
                    if(count($uKosarici))
                    {
                        print '<form id="kupiKupon" method="POST" name="kupiKupon" action="kosarica.php" style="clear: left;" novalidate>'
                         .'<p class="podaci">'
                         .'<input id="potvrdiKupnju" name="potvrdiKupnju" type="submit" value="Potvrdi kupnju" style="width: 20%;">'
                         .'</form>';
                    }
                }
            }
            
            $baza->zatvoriDB();
            ?>
        </section>
        
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
        <script src="jQuery/jQuery_skripta.js"></script>
        <script src="jQuery/ajax_kosarica.js"></script>
        <script src="jQuery/socijalneMreze.js"></script>
        
    </body>
</html>


