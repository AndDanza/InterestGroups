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


?>

<!DOCTYPE html>

<html lang="hr">
    <head>
        <title>Interesne skipine - Novo područje</title>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="naslov" content="Prijava">
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
                        echo '<li><a href="profil.php"><b>Profil</b></a></li>';
                        $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                        if(intval($korisnik->get_vrsta_korisnika()) === 1)
                        {
                            echo '<li><a href="dnevnik.php"><b>Log sustava</b></a></li>';
                            echo '<li><a href="kontrolaKorisnika.php"><b>Pregled korisnika</b></a></li>';
                            echo '<li><a href="unosPomaka.php"><b>Pomak vremena</b></a></li>';
                            echo '<li><a style="background: gainsboro;" href="kreirajPodrucje.php"><b>Kreiraj područje</b></a></li>';
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
        
        <section id="novoPodrucje">
            <?php
            $baza = new Baza();
            $baza->spojiDB();
            
            if(isset($_POST["unesiPod"]))
            {
                $moderator = intval($_POST["odabirMod"]);
                $nazivPod = $_POST["nazivPod"];

                $sql = "INSERT INTO `podrucja_interesa`(`naziv_podrucja`, `moderator`) "
                        . "VALUES ('$nazivPod' ,$moderator)";
                $baza->updateDB($sql);

                $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                $id_kor = intval($korisnik->get_id());
                ZapisiLogOstalo($baza, $id_kor, 'Uneseno novo područje interesa');
                
                echo "<p><b style='color: green; text-align: left; font-size: 18px;'>Područje interesa uneseno</b></p>";
            }

            if(isset($_POST["azurirajPodrucje"]))
            {
                $moderator = intval($_POST["odabirMod"]);
                $nazivPod = $_POST["nazivPod"];
                $idPodrucja = intval($_POST["idPodrucje"]);

                $sql = "UPDATE `podrucja_interesa` "
                      ."SET `naziv_podrucja` = '$nazivPod',`moderator` = $moderator"
                      ." WHERE `id_podrucja` = $idPodrucja";
                $baza->updateDB($sql);

                ZapisiLogOstalo($baza, $moderator, 'Ažurirano područje');
                        
                echo "<p><b style='color: green; text-align: left; font-size: 18px;'>Područje interesa je ažurirano</b></p>";
            }
                           
            
            if(isset($_GET["delete"]))
            {
                $brisiID = intval($_GET["delete"]);
                
                $sql = "SELECT COUNT(*) FROM `diskusija` WHERE `podrucja_interesa` = $brisiID";
                $rez = $baza->selectDB($sql);
                list($br_diskusija) = $rez->fetch_array();
                
                if($br_diskusija <= 0)
                {
                    $sql = "DELETE FROM podrucja_interesa WHERE id_podrucja = $brisiID";
                    $baza->updateDB($sql);
                    
                    ZapisiLogOstalo($baza, $moderator, 'Obrisano područje');
                    
                    echo "<p><b style='color: green; text-align: left; font-size: 18px;'>Područje interesa je obrisano</b></p>";
                }
                else
                {
                    echo "<p><b style='color: lightsalmon; text-align: left; font-size: 18px;'>Ne možete obrisati područje interesa s diskusijama.</b></p>";
                }
            }
            
            $baza->zatvoriDB();
            
            ?>
            <form method="POST" name="novoPodrucje" action="kreirajPodrucje.php" novalidate> 
                <h2 id="naslovPodrucje">Novo područje</h2>
                <p class="podaci">
                    <input id="idPodrucje" name="idPodrucje" type="text" hidden>
                    <label for="nazivPod">Naziv područja:</label>
                    <input id="nazivPod" name="nazivPod" type="text">
                </p>
                <p class="podaci">
                    <label for="odabirMod">Moderator:</label>
                    <select id="odabirMod" name="odabirMod">
                        <?php

                        $baza = new Baza();
                        $baza->spojiDB();

                        $sql = "SELECT id_korisnik, korisnicko_ime FROM korisnik WHERE tip_korisnika = 2";
                        $rez = $baza->selectDB($sql);

                        while(list($id, $kor) = $rez->fetch_array())
                        {
                            echo '<option value="'.$id.'">'.$kor.'</option>';
                        }

                        $baza->zatvoriDB();

                        ?>
                    </select>
                </p>
                <p id="kontrole">
                    <input id="unesiPod" name="unesiPod" type="submit" value="Unesi" style="width: 20%;">
                </p>
            </form>
            
            <h3 style="margin-top: 10%;">Pregled područja interesa</h3>
            <label for="brStranicaPodrucje">Broj redaka u tablici: </label>
            <select class="brojStranica" id="brStranicaPodrucje">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <p class="podaci" style="float: right;">
                <label for="searchPodrucje">Pretraži: </label>
                <input id="searchPodrucje" name="searchPodrucje" type="text">
            </p>
            
            <table class="tablicaLog" id="tablicaLogPodrucje">
                <thead>
                    <tr>
                        <th>Ime</th>
                        <th>Prezime</th>
                        <th id="podrucjeModerator" style="cursor: pointer;">Korisničko ime</th>
                        <th id="podrucjeNaziv" style="cursor: pointer;">Područje interesa</th>
                        <th>Uredi</th>
                        <th>Obriši</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div style="float: right; margin-top: 2%;" id="paginacijaPodrucje">
                <a id="krajPodrucje" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>
                <a id="sljedecaPodrucje" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>
                <input id="trenStranicaPodrucje" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">
                <a id="prethodniPodrucje" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>
                <a id="pocetakPodrucje" style="float: right; margin: 3% 2% 0% 1%">Početak</a>
            </div>
        </section>
        
        <footer id="footerPrijava">
            <div style="text-align: center; padding-bottom: 5px;">
                Prijava u sustav e-Interesnih skupina<br>
                &copy; 2017 A.Danzante
            </div>
        </footer>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="jQuery/jQuery_skripta.js"></script>
        <script src="jQuery/ajax_pregledPodrucja.js"></script>
      
    </body>
</html>
