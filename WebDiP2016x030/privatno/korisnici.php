<!DOCTYPE html>

<html>
    <head>
        <title>Interesne skipine - Korisnici</title>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="naslov" content="Korisnici">
        <meta name="kljucneRijeci" content="FOI, WebDiP, HTML, CSS">
        <meta name="datum" content="06.05.2017.">
        <meta name="autor" content="anddanzan">
        
        <link rel="stylesheet" type="text/css" href="../CSS/glavniCSS.css">
        <link rel="stylesheet" type="text/css" href="../CSS/formeCSS.css">
        <!-- <link rel="stylesheet" type="text/css" href="CSS/responzivnost.css"> -->
    </head>
    
    <body>
        <header class="zaglavlje">
            <ul>
                <li><b id="naslovSkupine">e-Spajanje interesnih skupina</b></li>
                <?php
                if(!isset($_SESSION["aktivniKorisnik"]))
                {
                    echo '<li class="desnaTipka"><a href="../prijava.php">Prijava</a></li>';
                }
                else
                {
                    echo '<li class="odjava"><a href="odjava.php?odjava=true">Odjava</a></li>';
                }
                ?>
            </ul>
        </header>

        <div class="divMeni">
            <nav>
                <ul class="meni">
                    <li><a href="../prijava.php"><b>Prijava</b></a></li>
                    <li><a href="../registracija.php"><b>Registracija</b></a></li>
                </ul>
            </nav>
        </div>

        <section id="sekcijaKorisnik">
            <h2 id="naslovKorisnik">Podaci o korisnicima sustava</h3>
            <label for="brStranicaKorisnik">Broj redaka u tablici: </label>
            <select class="brojStranica" id="brStranicaKorisnik">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <p class="podaci" style="float: right;">
                <label for="searchKorisnik">Pretraži: </label>
                <input id="searchKorisnik" name="searchKorisnik" type="text">
            </p>
            
            <table class="tablicaLog" id="tablicaKorisnik">
                <thead>
                    <tr>
                        <th>Ime</th>
                        <th id="korisnikPrezime">Prezime</th>
                        <th id="korisnikKorIme">Korisničko ime</th>
                        <th>Email</th>
                        <th>Lozinka</th>
                        <th>Vrsta korisnika</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div style="float: right; margin-top: 2%;" id="paginacijaKorisnik">
                <a id="krajKorisnik" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>
                <a id="sljedecaKorisnik" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>
                <input id="trenStranicaKorisnik" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">
                <a id="prethodniKorisnik" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>
                <a id="pocetakKorisnik" style="float: right; margin: 3% 2% 0% 1%">Početak</a>
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
        <script src="./korisnik_stranicenje.js"></script>
        
    </body>
</html>


