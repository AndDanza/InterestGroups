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
          ."VALUES ($id_korisnika, '$timeSad', 1)";
    $baza->updateDB($sql);
    
    $sql = "UPDATE `korisnik` SET `stanje_bodova`= stanje_bodova + (SELECT `broj_bodova` FROM `vrsta_akcije` WHERE `id_vrste_akcije` = 1) "
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
                    echo '<li id="cart"><a href="kosarica.php"><img src="Slike/kosarica.png" alt="kosarica"></a></li>';
                }
                ?>
            </ul>
        </header>
        
        <div class="divMeni">
            <nav>
                <ul class="meni">
                    <li><a href="o_autoru.html"><b>O autoru</b></a></li>
                    <li><a href="dokumentacija.html"><b>Dokumentacija</b></a></li>
                    <?php
                    if(isset($_SESSION["aktivniKorisnik"]))
                    {
                        echo '<li><a id="meniProfil" href="profil.php"><b>Profil</b></a></li>';
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
                        echo '<li><a style="background: gainsboro;" href="podrucjaZaKorisnika.php"><b>Područja interesa</b></a></li>';
                    }
                    else
                    {
                        echo '<li><a href="prijava.php"><b>Prijava</b></a></li>';
                        echo '<li><a href="registracija.php"><b>Registracija</b></a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </div>

        <section id="prikazPodrucja">
            <?php
            $baza = new Baza();
            $baza->spojiDB();
                
            if(isset($_POST["posaljiKomentar"]))
            {
                $user = unserialize($_SESSION["aktivniKorisnik"]);
                $korisnik = intval($user->get_id());
                $diskusija = $_GET["disk"];
                $datum = DohvatiVrijemePlusPomak($baza);
                $komentar = $_POST["komentar"];
                $vrsta = intval($user->get_vrsta_korisnika());
                
                if($vrsta === 3)
                {
                    $sql = "SELECT 1 FROM pretplata "
                      ."WHERE korisnik = $korisnik AND diskusija = $diskusija "
                      ."AND zabrana_komentiranja = 0";
                    $rez = $baza -> selectDB($sql);
                    list($zabrana) = $rez->fetch_array();
                }
                else
                {
                    $zabrana = "1";
                }
                
                
                
                if($zabrana === "1")
                {
                    $sql = "INSERT INTO `komentari`(`korisnik`, `diskusija`, `datum_vrijeme_pisanja`, `tekst_komentara`) "
                          ."VALUES ($korisnik, $diskusija, '$datum', '$komentar')";
                    $baza -> updateDB($sql);
                    
                    AzurirajBodove($baza, $korisnik);
                }
                else if($zabrana !== "1")
                {
                    echo "<p><b style='color: lightsalmon; text-align: left; font-size: 18px;'>Nemate pravo komentiranja na diskusiji</b></p>";
                }
            }
            
            if(isset($_POST["azurirajKomentar"]))
            {
                $user = unserialize($_SESSION["aktivniKorisnik"]);
                $korIme = $user->get_kor_ime();
                $tkoPrepravlja = $_POST["tkoPrepravlja"];
                $vrsta = intval($user->get_vrsta_korisnika());
                
                if($korIme === $tkoPrepravlja)
                {
                    $korisnik = intval($user->get_id());
                    $diskusija = $_GET["disk"];
                    $datumVrijeme = $_GET["vrijeme"];
                    $azuriraniKom = $_POST["azuriraniKom"];

                    $sql = "UPDATE `komentari` SET `tekst_komentara`= '$azuriraniKom' "
                           ."WHERE `korisnik` = $korisnik AND `diskusija` = $diskusija AND `datum_vrijeme_pisanja` = '$datumVrijeme'";
                    $baza -> updateDB($sql);
                
                    echo "<p><b style='color: green; text-align: left; font-size: 18px;'>Vaš komentar je izmjenjen</b></p>";
                }
                else
                {
                    echo "<p><b style='color: lightsalmon; text-align: left; font-size: 18px;'>Ne možete prepraviti tuđi komentar.</b></p>";
                }
            }
            
            if(isset($_GET["delete"]))
            {
                $user = unserialize($_SESSION["aktivniKorisnik"]);
                $korIme = $user->get_kor_ime();
                $vrsta = intval($user->get_vrsta_korisnika());
                
                $ulaz = explode(";", $_GET["delete"]);
                $tkoBrise = $ulaz[0];
                $id_diskusije = intval($ulaz[1]);
                $vrijeme = $ulaz[2];
                
                if($korIme === $tkoBrise || $vrsta === 1)
                {
                    $sql = "DELETE FROM `komentari` "
                          ."WHERE `korisnik` = (SELECT id_korisnik FROM korisnik WHERE korisnicko_ime = '$korIme') "
                          ."AND `diskusija` = $id_diskusije AND `datum_vrijeme_pisanja` = '$vrijeme'";
                    $baza->updateDB($sql);
                    
                    echo "<p><b style='color: green; text-align: left; font-size: 18px;'>Vaš komentar je obrisan</b></p>";
                }
                else
                {
                    echo "<p><b style='color: lightsalmon; text-align: left; font-size: 18px;'>Ne možete obrisati tuđi komentar.</b></p>";
                }
            }
            
            if(isset($_SESSION["aktivniKorisnik"]))
            {
                $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                $id = intval($korisnik->get_id());
                $vrsta = intval($korisnik->get_vrsta_korisnika());
                
                echo '<h2>Područja interesa</h2>';
                echo '<select id="odabirPodrucja" name="odabirPodrucja">';
                
                if($vrsta === 3)
                {
                    $sql = "SELECT id_podrucja, naziv_podrucja "
                          ."FROM podrucja_interesa WHERE id_podrucja IN "
                          ."(SELECT podrucja_interesa_id_podrucja FROM odabir_podrucja_interesa "
                          ."WHERE korisnik_id_korisnik = $id AND datum_vrijeme_prekida = '0000-00-00 00:00:00')";
                }
                elseif ($vrsta === 2) 
                {
                    $sql = "SELECT id_podrucja, naziv_podrucja "
                          ."FROM `podrucja_interesa` WHERE moderator = $id";
                }
                else
                {
                    $sql = "SELECT id_podrucja, naziv_podrucja "
                          ."FROM `podrucja_interesa`";
                }
                $rez = $baza -> selectDB($sql);

                while(list($id, $naziv) = $rez->fetch_array())
                {
                    echo "<option value='$id'>$naziv</option>";
                }
                
                echo '</select>';
                
            }
            else
            {
                echo '<h2>Područja interesa i diskusije</h2>';
            }
            
            $baza ->zatvoriDB();
            ?>
        </section>
        
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
        <script src="jQuery/ajax_podrucja.js"></script>
        <script src="jQuery/socijalneMreze.js"></script>
        
    </body>
</html>


