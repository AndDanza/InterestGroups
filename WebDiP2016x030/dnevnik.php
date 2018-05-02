<?php

session_start();
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

?>

<!DOCTYPE html>

<html>
    <head>
        <title>Interesne skipine - Dnevnici rada</title>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="naslov" content="Korisnici">
        <meta name="kljucneRijeci" content="FOI, WebDiP, HTML, CSS">
        <meta name="datum" content="06.05.2017.">
        <meta name="autor" content="anddanzan">
        
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
                    <?php
                    if(isset($_SESSION["aktivniKorisnik"]))
                    {
                        echo '<li><a id="meniProfil" href="profil.php"><b>Profil</b></a></li>';
                        $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                        if(intval($korisnik->get_vrsta_korisnika()) === 1)
                        {
                            echo '<li><a style="background: gainsboro;" href="dnevnik.php"><b>Log sustava</b></a></li>';
                            echo '<li><a href="kontrolaKorisnika.php"><b>Pregled korisnika</b></a></li>';
                            echo '<li><a href="unosPomaka.php"><b>Pomak vremena</b></a></li>';
                            echo '<li><a href="kreirajPodrucje.php"><b>Kreiraj područje</b></a></li>';
                            echo '<li><a href="kreirajKupon.php"><b>Kreiraj kupon</b></a></li>';
                            echo '<li><a href="statistikaLojalnosti.php"><b>Statistika lojalnosti</b></a></li>';
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

        <section id="sekcijaKorisnik">
            <h2>Dnevnici rada sustava</h2>
            <h3 id="naslovPrijava">Log prijava u sustav</h3>
            <label for="brStranicaPrijava">Broj redaka u tablici: </label>
            <select class="brojStranica" id="brStranicaPrijava">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <p class="podaci" style="float: right;">
                <label for="search">Pretraži: </label>
                <input id="search" name="search" type="text">
            </p>
            
            <table class="tablicaLog" id="tablicaLogPrijava">
                <thead>
                    <tr>
                        <th>Ime</th>
                        <th>Prezime</th>
                        <th id="prijavaKorIme">Korisničko ime</th>
                        <th id="prijavaDate">Datum/vrijeme prijave</th>
                        <th>Datum/vrijeme odjave</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div style="float: right; margin-top: 2%;" id="paginacijaPrijave">
                <a id="krajPrijava" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>
                <a id="sljedecaPrij" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>
                <input id="trenStranica" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">
                <a id="prethodniPrij" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>
                <a id="pocetakPrijava" style="float: right; margin: 3% 2% 0% 1%">Početak</a>
            </div>
            
            
            <h3 style="clear: right; margin-top: 10%;" id="naslovBaza">Log pristupa bazi podataka</h3>
            <label for="brStranicaBaze">Broj redaka u tablici: </label>
            <select class="brojStranica" id="brStranicaBaza">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <p class="podaci" style="float: right;">
                <label for="searchBaza">Pretraži: </label>
                <input id="searchBaza" name="searchBaza" type="text">
            </p>
            
            <table class="tablicaLog" id="tablicaLogBaza">
                <thead>
                    <tr>
                        <th>Ime</th>
                        <th>Prezime</th>
                        <th id="bazaKorIme">Korisničko ime</th>
                        <th id="bazaDate">Datum/vrijeme pristupa</th>
                        <th>Vrsta upita</th>
                        <th>Tablica</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div style="float: right; margin-top: 2%;" id="paginacijaBaze">
                <a id="krajBaze" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>
                <a id="sljedecaBaze" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>
                <input id="trenStranicaBaze" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">
                <a id="prethodniBaze" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>
                <a id="pocetakBaze" style="float: right; margin: 3% 2% 0% 1%">Početak</a>
            </div>
            
            
            <h3 style="clear: right; margin-top: 10%;" id="naslovOstalo">Log ostalih podataka</h3>
            <label for="brStranicaOstalo">Broj redaka u tablici: </label>
            <select class="brojStranica" id="brStranicaOstalo">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <p class="podaci" style="float: right;">
                <label for="searchOstalo">Pretraži: </label>
                <input id="searchOstalo" name="searchOstalo" type="text">
            </p>
            
            <table class="tablicaLog" id="tablicaLogOstalo">
                <thead>
                    <tr>
                        <th>Ime</th>
                        <th>Prezime</th>
                        <th id="ostaloKorIme">Korisničko ime</th>
                        <th id="ostaloDate">Datum/vrijeme pristupa</th>
                        <th>Opis radnje</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div style="float: right; margin-top: 2%;" id="paginacijaOstalo">
                <a id="krajOstalo" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>
                <a id="sljedecaOstalo" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>
                <input id="trenStranicaOstalo" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">
                <a id="prethodniOstalo" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>
                <a id="pocetakOstalo" style="float: right; margin: 3% 2% 0% 1%">Početak</a>
            </div>
        </section>

        <footer id="footerPrijava">
            <p style="text-align: center;">
                Registracija u sustav e-Interesnih skupina<br>
                Vrijeme potrebno za rješavanje aktivnog dokumenta: 15 min<br>
                &copy; 2017 A.Danzante
            </p>
        </footer>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="./jQuery/ajax_stranicenje.js"></script>
        
    </body>
</html>

