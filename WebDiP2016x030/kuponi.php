<?php

session_start();
require './PHP Klase/baza.class.php';
require './PHP Klase/korisnik.class.php';

if(!isset($_SESSION["aktivniKorisnik"]))
{
    header("refresh:0;url=prijava.php");
}
else 
{
    if(!isset($_SESSION["kosarica"]))
    {
        $_SESSION["kosarica"] = array();
    }
}


function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s", ($pomak[0]*60*60) + time());
}

if(isset($_GET["pregledaj"]))
{
    $baza = new Baza();
    $baza->spojiDB();
    
    echo '<div id="pregledKupona">';
    echo '<a style="float:right;" href="kuponi.php"><b style="color: red;">ZATVORI</b></a><br>';
    echo '<img src="Slike/kupon.png" alt="kupon" style="height:70px; width:20%; margin-left: 5%; margin-top: 4%;">';
    
    $id_kupon = intval($_GET["pregledaj"]);
    $sql = "SELECT pdf_opis_slika, `naziv_kupona`, `potrebno_bodova`, `datum_vrijeme_izdavanja` "
          ."FROM kupon_clanstva WHERE id_kupona = $id_kupon";
    $rez = $baza->selectDB($sql);
    list($pdfKupon, $nazivKupon, $bodoviK, $datumIzdavanja) = $rez->fetch_array();
    
    echo '<p style="float: left; color: #555;">';
    echo "<b>Naziv: </b>".$nazivKupon."<br>";
    echo "<b>Broj bodova: </b>".$bodoviK."<br>";
    echo "<b>Datum kreiranja: </b><br>".$datumIzdavanja."<br><br>";    
    echo '</p>';
    
    echo '<object data="data:application/pdf;base64,';
    echo base64_encode($pdfKupon);
    echo '" type="application/pdf" class="objectPregled"></object>';

    echo '</div>';
    
    $baza->zatvoriDB();
}


function ProvjeriKupon($baza, $kod)
{
    $kuponOK = true;
    
    $sql = "SELECT 1 FROM `kupon_clanstva` WHERE `generirani_kod` = '$kod'";
    $rez = $baza->selectDB($sql);
    list($odgovor) = $rez->fetch_array();
    
    if($odgovor !== "1")
    {
        $kuponOK = false;
    }
    
    return$kuponOK;
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
                        echo '<li><a style="background: gainsboro;" href="kuponi.php"><b>Kuponi članstva</b></a></li>';
                    }
                    ?>
                    <li><a href="podrucjaZaKorisnika.php"><b>Područja interesa</b></a></li>
                </ul>
            </nav>
        </div>

        <section id="kuponiSekcija">
            <?php
            $baza = new Baza();
            $baza ->spojiDB();
            
            if(isset($_GET["idKupon"]) && isset($_GET["nazivKupon"]))
            {
                $id_kupon = intval($_GET["idKupon"]);
                $nazivKupona = $_GET["nazivKupon"];
                $stanje = intval($korisnik->get_brBodova());
                
                $sql = "SELECT `potrebno_bodova` FROM `kupon_clanstva` "
                      ."WHERE `id_kupona` = $id_kupon";
                $rez = $baza->selectDB($sql);
                list($potrebno) = $rez->fetch_array();
                
                if($potrebno < $stanje)
                {
                    $_SESSION["kosarica"][$id_kupon] = $nazivKupona;
                    print "<p id='notifikacija'><b style='color: green; font-size: 25px;'>Kupon $nazivKupona dodan u košaricu</b></p>";
                    $korisnik->set_brBodova($stanje-$potrebno);
                    $_SESSION["aktivniKorisnik"] = serialize($korisnik);
                }
                else
                {
                    print "<p id='notifikacija'><b style='color: lightsalmon; font-size: 25px;'>Nemate dovoljno bodova za kupon</b></p>";
                }
            }
            $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
            $id = intval($korisnik->get_id());
            $vrsta = intval($korisnik->get_vrsta_korisnika());
            
            echo '<h2 id="dostupniKuponi">Dostupni kuponi članstva</h2>';
            
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
            $result = $baza ->selectDB($sql);
            $rez = $baza -> selectDB($sql);
            echo '<select id="odabirPodrucja" name="odabirPodrucja">';
            while(list($id_pod, $naziv) = $rez->fetch_array())
            {
                echo "<option value='$id_pod'>$naziv</option>";
            }
            echo '</select>';
            
            echo '<div id="dostupniKuponiKor"></div>';
            
            echo '<div style="float: right; margin-top: 2%;" style="clear:left; float:right;">'
                    .'<a id="krajKuponKor" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>'
                    .'<a id="sljedecaKuponKor" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>'
                    .'<input id="trenStranicaKuponKor" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">'
                    .'<a id="prethodniKuponKor" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>'
                    .'<a id="pocetakKuponKor" style="float: right; margin: 3% 2% 0% 1%">Početak</a>'
                .'</div>';
            
            echo '<h2 style="clear: both;">Dosad kupljeni kuponi</h2>';
            
            echo '<div style="clear: both;" id="kupljeniKuponiKor"></div>';
            
            echo '<div style="float: right; margin-top: 2%;" style="clear:left; float:right;">'
                    .'<a id="krajKuponKupljeni" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>'
                    .'<a id="sljedecaKuponKupljeni" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>'
                    .'<input id="trenStranicaKuponKupljeni" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">'
                    .'<a id="prethodniKuponKupljeni" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>'
                    .'<a id="pocetakKuponKupljeni" style="float: right; margin: 3% 2% 0% 1%">Početak</a>'
                .'</div>';
            
            if($vrsta !== 3)
            {
                echo '<h2 style="clear: both;">Provjera kupona</h2>';
                
                echo '<form method="POST" action="kuponi.php" novalidate style="float: left; width: 50%;">';
                echo '<input name="generiraniKod" type="text" style="width: 60%;" placeholder="Generirani kod">';
                echo '<input type="submit" value="Provjeri" name="provjeri">';
                echo '</form>';
            }
            
            if(isset($_POST["provjeri"]))
            {
                $kod = $_POST["generiraniKod"];
                    
                if(ProvjeriKupon($baza, $kod))
                {
                    print "<p id='notifikacija'><b style='float: left; color: green; font-size: 25px;'>Uneseni kod kupona je valjan</b></p>";
                }
                else
                {
                    print "<p id='notifikacija'><b style='float: left; color: color: lightsalmon; font-size: 25px;'>Uneseni kod kupona je neispravan!</b></p>";
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
        <script src="jQuery/ajax_kuponiPregled.js"></script>
        <script src="jQuery/socijalneMreze.js"></script>
        
    </body>
</html>


