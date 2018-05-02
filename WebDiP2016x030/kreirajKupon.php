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
        <title>Interesne skipine - Nova diskusija</title>

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
                            echo '<li><a href="kreirajPodrucje.php"><b>Kreiraj područje</b></a></li>';
                            echo '<li><a style="background: gainsboro;" href="kreirajKupon.php"><b>Kreiraj kupon</b></a></li>';
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
        
        <section id="kreirajKupon">
            <form enctype="multipart/form-data" name="kreirajKupon" action="kreirajKupon.php" method="POST" novalidate>
                <?php
                
                $baza = new Baza();
                $baza->spojiDB();
                
                
                if(isset($_POST["pohraniKupon"]) && $_FILES['uploadKupon']['size'] > 0)
                {
                    $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                    $id = intval($korisnik->get_id());
                    $naziv = $_POST["nazivKupon"];
                    $vrijeme = DohvatiVrijemePlusPomak($baza);

                    $nazivDat = $_FILES['uploadKupon']['name'];
                    $tmpNaziv  = $_FILES['uploadKupon']['tmp_name'];
                    $velicinaDat = $_FILES['uploadKupon']['size'];
                    $tipDat = $_FILES['uploadKupon']['type'];

                    $fp = fopen($tmpNaziv, 'r');
                    $sadrzaj = fread($fp, filesize($tmpNaziv));
                    $sadrzaj = addslashes($sadrzaj);
                    fclose($fp);

                    $sql = "INSERT INTO `kupon_clanstva`(`administrator`, `naziv_kupona`, `pdf_opis_slika`, `datum_vrijeme_izdavanja`) "
                          ."VALUES ($id, '$naziv', '$sadrzaj', '$vrijeme')";
                    $baza->updateDB($sql);

                    echo "<p id='notifikacija'><b style='color: green; font-size: 25px;'>Kupon pohranjen</b><br>"
                            ."<p id='dat' style='font-size: 18px;'>Učitali ste ".$nazivDat." veličine ".($velicinaDat/1024)."KB</p></p>";

                    ZapisiLogOstalo($baza, $id, 'Kreiran kupon');                    
                }
                
                if(isset($_POST["azurirajKupon"]))
                {
                    $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                    $id = intval($korisnik->get_id());
                    
                    $naziv = $_POST["nazivKupon"];
                    $id_kupona = intval($_POST["idKupon"]);
                    
                    if($_FILES['uploadKupon']['size'] > 0)
                    {
                        $nazivDat = $_FILES['uploadKupon']['name'];
                        $tmpNaziv  = $_FILES['uploadKupon']['tmp_name'];
                        $velicinaDat = $_FILES['uploadKupon']['size'];
                        $tipDat = $_FILES['uploadKupon']['type'];

                        $fp = fopen($tmpNaziv, 'r');
                        $sadrzaj = fread($fp, filesize($tmpNaziv));
                        $sadrzaj = addslashes($sadrzaj);
                        fclose($fp);
                        
                        $sql = "UPDATE `kupon_clanstva` SET `naziv_kupona` = '$naziv', pdf_opis_slika = '$sadrzaj' "
                          ."WHERE `id_kupona` = $id_kupona";
                        $baza->updateDB($sql);
                        
                        echo "<p id='notifikacija'><b style='color: green; font-size: 18px;'>Svi podaci o kuponu ažurirani</b></p>";
                    }
                    else
                    {
                       $sql = "UPDATE `kupon_clanstva` SET `naziv_kupona` = '$naziv' "
                          ."WHERE `id_kupona` = $id_kupona";
                        $baza->updateDB($sql); 
                        
                        echo "<p id='notifikacija'><b style='color: green; font-size: 18px;'>Ažurirano ime kupona</b></p>";
                    }
                    
                    
                    ZapisiLogOstalo($baza, $id, 'Ažuriran kupon');       

                    

                }
                
                if(isset($_GET["delete"]))
                {
                    $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                    $id_korisnika = intval($korisnik->get_id());
                    $id = intval($_GET["delete"]);
                    
                    $sql = "DELETE FROM `kupon_clanstva` WHERE `id_kupona` = $id";
                    $baza->updateDB($sql);
                    
                    ZapisiLogOstalo($baza, $id_korisnika, 'Kupon obrisan');     
                }
                
                $baza->zatvoriDB();
                ?>
                <h2>Kreiraj kupon </h2>
                <p class="podaci">
                    <input id="idKupon" name="idKupon" type="text" hidden>
                    <label for="nazivKupon">Naziv: </label>
                    <input id="nazivKupon" name="nazivKupon" type="text"><br><br>
                    <label for="uploadKupon" style="margin-top: 2%;">Kupon: </label>
                    <input type="file" id="uploadKupon" name="uploadKupon">
                </p>
                </p>
                <p id="kontrole">
                    <input id="pohraniKupon" name="pohraniKupon" type="submit" value="Pohrani" style="width: 20%;">
                </p>
            </form>
            
            <h3 style="margin-top: 10%;">Pregled kreiranih kupona</h3>
            <label for="brStranicaKupon">Broj redaka u tablici: </label>
            <select class="brojStranica" id="brStranicaKupon">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <p class="podaci" style="float: right;">
                <label for="searchKupon">Pretraži: </label>
                <input id="searchKupon" name="searchKupon" type="text">
            </p>
            
            <table class="tablicaLog" id="tablicaLogKupon">
                <thead>
                    <tr>
                        <th>Naziv</th>
                        <th>Datum vrijeme kreiranja</th>
                        <th>Uredi</th>
                        <th>Obriši</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div style="float: right; margin-top: 2%;" id="paginacijaKupon">
                <a id="krajKupon" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>
                <a id="sljedecaKupon" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>
                <input id="trenStranicaKupon" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">
                <a id="prethodniKupon" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>
                <a id="pocetakKupon" style="float: right; margin: 3% 2% 0% 1%">Početak</a>
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
        <script src="jQuery/ajax_kupon.js"></script>
      
    </body>
</html>
