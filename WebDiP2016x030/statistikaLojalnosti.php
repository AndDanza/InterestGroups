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
    $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
    $id = intval($korisnik->get_vrsta_korisnika());
    
    if($id !== 1)
    {
        header("refresh:0;url=podrucjaZaKorisnika.php");
    }
}

function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s", ($pomak[0]*60*60) + time());
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
                        echo '<li><a id="meniProfil" href="profil.php"><b>Profil</b></a></li>';
                        $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                        if(intval($korisnik->get_vrsta_korisnika()) === 1)
                        {
                            echo '<li><a href="dnevnik.php"><b>Log sustava</b></a></li>';
                            echo '<li><a href="kontrolaKorisnika.php"><b>Pregled korisnika</b></a></li>';
                            echo '<li><a href="unosPomaka.php"><b>Pomak vremena</b></a></li>';
                            echo '<li><a href="kreirajPodrucje.php"><b>Kreiraj područje</b></a></li>';
                            echo '<li><a href="kreirajKupon.php"><b>Kreiraj kupon</b></a></li>';
                            echo '<li><a style="background: gainsboro;" href="statistikaLojalnosti.php"><b>Statistika lojalnosti</b></a></li>';
                        }
                        if(intval($korisnik->get_vrsta_korisnika()) !== 3)
                        {
                            echo '<li><a href="definirajKupon.php"><b>Definiraj kupon</b></a></li>';
                            echo '<li><a href="dodajDiskusiju.php"><b>Dodaj diskusiju</b></a></li>';
                            echo '<li><a href="pregledKorisnikaModerator.php"><b>Korisnici područja</b></a></li>';
                            echo '<li><a href="obavijesti.php"><b>Obavijesti</b></a></li>';
                        }
                        echo '<li><a href="kuponi.php"><b>Kuponi članstva</b></a></li>';
                        echo '<li><a href="podrucjaZaKorisnika.php"><b>Područja interesa</b></a></li>';
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
            <h2>Pregled statitike lojalnosti</h2>
            <?php
            $baza = new Baza();
            $baza->spojiDB();
            
            $sql = "SELECT id_korisnik, korisnicko_ime FROM korisnik";
            $rez = $baza -> selectDB($sql);
            echo '<select id="korsniciSustava" style="padding: 1px; width: 15%; margin-left: 5%;">';
            while(list($id, $naziv) = $rez->fetch_array())
            {
                echo "<option value='$id'>$naziv</option>";
            }   
            echo '</select>';
               
            $baza ->zatvoriDB();
            ?>
            
            <h3 id="naslovPrijava">Sakupljeni bodovi</h3>
            <label for="brStranicaSakupljeni">Broj redaka u tablici: </label>
            <select class="brojStranica" id="brStranicaSakupljeni">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <p class="podaci" style="float: right;">
                <label for="searchSakupljeni">Pretraži: </label>
                <input id="searchSakupljeni" name="searchSakupljeni" type="text">
            </p>
            
            <table class="tablicaLog" id="tablicaLogSakupljeni">
                <thead>
                    <tr>
                        <th>Ime</th>
                        <th>Prezime</th>
                        <th>Korisničko ime</th>
                        <th>Vrsta akcije</th>
                        <th id="sakupljeniBodovi" style="cursor: pointer;">Broj bodova</th>
                        <th id="sakupljeniDate" style="cursor: pointer;">Datum/vrijeme akcije</th> 
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div style="float: right; margin-top: 2%;" id="paginacijaSakupljeni">
                <a id="krajSakupljeni" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>
                <a id="sljedecaSakupljeni" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>
                <input id="trenSakupljeni" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">
                <a id="prethodniSakupljeni" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>
                <a id="pocetakSakupljeni" style="float: right; margin: 3% 2% 0% 1%">Početak</a>
            </div>
            
            
            <h3 style="clear: right; margin-top: 10%;" id="naslovBaza">Potrošeni bodovi</h3>
            <label for="brStranicaPotroseni">Broj redaka u tablici: </label>
            <select class="brojStranica" id="brStranicaPotroseni">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <p class="podaci" style="float: right;">
                <label for="searchPotroseni">Pretraži: </label>
                <input id="searchPotroseni" name="searchBaza" type="text">
            </p>
            
            <table class="tablicaLog" id="tablicaLogPotroseni">
                <thead>
                    <tr>
                        <th>Ime</th>
                        <th>Prezime</th>
                        <th>Korisničko ime</th>
                        <th id="potroseniKupon" style="cursor: pointer;">Naziv kupona</th>
                        <th>Broj bodova</th>
                        <th id="potroseniDate" style="cursor: pointer;">Datum/vrijeme kupnje</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div style="float: right; margin-top: 2%;" id="paginacijaPotroseni">
                <a id="krajPotroseni" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>
                <a id="sljedecaPotroseni" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>
                <input id="trenStranicaPotroseni" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">
                <a id="prethodniPotroseni" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>
                <a id="pocetakPotroseni" style="float: right; margin: 3% 2% 0% 1%">Početak</a>
            </div>
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
        <script src="jQuery/ajax_statistika.js"></script>
        <script src="jQuery/socijalneMreze.js"></script>
        
    </body>
</html>


