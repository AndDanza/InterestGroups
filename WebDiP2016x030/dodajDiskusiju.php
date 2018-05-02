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
    
    if($id !== 1 && $id !== 2)
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


function AzurirajPretplate($baza, $podrucje)
{
    $sql = "SELECT MAX(`id_diskusija`) FROM `diskusija` WHERE podrucja_interesa = $podrucje";
    $rez = $baza->selectDB($sql);
    list($rezultat) = $rez->fetch_array();
    $novaDisk = intval($rezultat);
    $vrijeme = DohvatiVrijemePlusPomak($baza);
    
    $sql = "SELECT korisnik_id_korisnik FROM odabir_podrucja_interesa WHERE podrucja_interesa_id_podrucja = $podrucje";
    $rez = $baza->selectDB($sql);
    
    while(list($user) = $rez->fetch_array())
    {
        $sql = "INSERT INTO `pretplata`(`korisnik`, `diskusija`, `datum_vrijeme_pretplate`) "
              ."VALUES ($user, $novaDisk, '$vrijeme')";
        $baza->updateDB($sql);
    }
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
                            echo '<li><a href="kreirajKupon.php"><b>Kreiraj kupon</b></a></li>';
                            echo '<li><a href="statistikaLojalnosti.php"><b>Statistika lojalnosti</b></a></li>';
                        }
                        if(intval($korisnik->get_vrsta_korisnika()) !== 3)
                        {
                            echo '<li><a href="definirajKupon.php"><b>Definiraj kupon</b></a></li>';
                            echo '<li><a style="background: gainsboro;" href="dodajDiskusiju.php"><b>Dodaj diskusiju</b></a></li>';
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
            
            $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
            $moderator = intval($korisnik->get_id());
            
            if(isset($_POST["unesiDisk"]))
            {
                $odabirPod = intval($_POST["odabirPod"]);
                $pravila = $_POST["pravilaDisk"];
                $nazivDisk = $_POST["nazivDisk"];
                $trajeDo = $_POST["trajanjeDisk"];
                $vrijeme = DohvatiVrijemePlusPomak($baza);

                $sql = "INSERT INTO `diskusija`(`podrucja_interesa`, `naziv_diskusije`, `pravila`, `datum_vrijeme_otvaranja`, datum_vrijeme_zatvaranja) "
                      ."VALUES ($odabirPod, '$nazivDisk', '$pravila', '$vrijeme', '$trajeDo')";
                $baza->updateDB($sql);

                AzurirajPretplate($baza, $odabirPod);

                ZapisiLogOstalo($baza, $moderator, 'Unesena nova diskusija');
                        
                echo "<p><b style='color: green; text-align: left; font-size: 18px;'>Diskusija je dodana</b></p>";
            }
                    
            
            if(isset($_POST["azurirajDisk"]))
            {
                $odabirPod = intval($_POST["odabirPod"]);
                $pravila = $_POST["pravilaDisk"];
                $nazivDisk = $_POST["nazivDisk"];
                $trajeDo = $_POST["trajanjeDisk"];
                $vrijeme = DohvatiVrijemePlusPomak($baza);
                $diskusija = $_POST["idDisk"];

                $sql = "UPDATE `diskusija` "
                      ."SET `podrucja_interesa` = $odabirPod,`naziv_diskusije` = '$nazivDisk',"
                      ."`pravila` = '$pravila',`datum_vrijeme_zatvaranja` = '$trajeDo' "
                      ."WHERE `id_diskusija` = $diskusija";
                $baza->updateDB($sql);

                ZapisiLogOstalo($baza, $moderator, 'Ažurirana diskusija');
                        
                echo "<p><b style='color: green; text-align: left; font-size: 18px;'>Diskusija je ažurirana</b></p>";
            }
                           
            
            if(isset($_GET["delete"]))
            {
                $brisiID = intval($_GET["delete"]);
                
                $sql = "SELECT COUNT(*) FROM `komentari` WHERE diskusija = $brisiID";
                $rez = $baza->selectDB($sql);
                list($br_kom) = $rez->fetch_array();
                
                if($br_kom <= 0)
                {
                    $sql = "DELETE FROM pretplata WHERE diskusija = $brisiID";
                    $baza->updateDB($sql);
                    
                    $sql = "DELETE FROM diskusija WHERE id_diskusija = $brisiID";
                    $baza->updateDB($sql);
                    
                    ZapisiLogOstalo($baza, $moderator, 'Obrisana diskusija');
                    
                    echo "<p><b style='color: green; text-align: left; font-size: 18px;'>Diskusija je obrisana</b></p>";
                }
                else
                {
                    echo "<p><b style='color: lightsalmon; text-align: left; font-size: 18px;'>Ne možete obrisati diskusiju s pretplaćenim korisnicima u njoj.</b></p>";
                }
            }
            
            $baza->zatvoriDB();
            ?>
            <form method="POST" name="novoPodrucje" action="dodajDiskusiju.php" novalidate> 
                <h2 id="naslovDisk">Nova diskusija</h2>
                <p class="podaci">
                    <input id="idDisk" name="idDisk" type="text" hidden>
                    <label for="nazivDisk">Naziv: </label>
                    <input id="nazivDisk" name="nazivDisk" type="text"><br><br>
                    <label for="pravilaDisk">Pravila: </label>
                    <textarea id='pravilaDisk' type='text' name='pravilaDisk'></textarea><br><br>
                    <label for="trajanjeDisk">Datum isteka: </label>
                    <input id="trajanjeDisk" name="trajanjeDisk" type="text" placeholder="yyyy-mm-dd hh:mm:ss"><br><br>
                    <label for="odabirPod">Područje interesa: </label>
                    <select id="odabirPod" name="odabirPod">
                        <?php

                        $baza = new Baza();
                        $baza->spojiDB();

                        $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
                        $vrsta = intval($korisnik->get_vrsta_korisnika());
                        $id_kor = intval($korisnik->get_id());

                        $sql = "SELECT id_podrucja, naziv_podrucja FROM `podrucja_interesa`";
                        if($vrsta === 2)
                        {
                            $sql .= "WHERE `moderator` = $id_kor";
                        }
                        $rez = $baza->selectDB($sql);

                        while(list($id, $pod) = $rez->fetch_array())
                        {
                            echo '<option value="'.$id.'">'.$pod.'</option>';
                        }

                        $baza->zatvoriDB();

                        ?>

                    </select>
                </p>
                <p id="kontrole">
                    <input id="unesiDisk" name="unesiDisk" type="submit" value="Unesi" style="width: 20%;">
                </p>
            </form>
            
            <h3 style="margin-top: 5%;">Pregled diskusija</h3>
            <label for="brStranicaDisk">Broj redaka u tablici: </label>
            <select class="brojStranica" id="brStranicaDisk">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <p class="podaci" style="float: right;">
                <label for="searchDisk">Pretraži: </label>
                <input id="searchDisk" name="searchDisk" type="text">
            </p>
            
            <table class="tablicaLog" id="tablicaLogDisk">
                <thead>
                    <tr>
                        <th id="diskNaziv" style="cursor: pointer;">Naziv</th>
                        <th>Pravila</th>
                        <th>Područje interesa</th>
                        <th id="diskDate" style="cursor: pointer;">Datum vrijeme kreiranja</th>
                        <th >Datum vrijeme zatvaranja</th>
                        <th>Uredi</th>
                        <th>Obriši</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div style="float: right; margin-top: 2%;" id="paginacijaDisk">
                <a id="krajDisk" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>
                <a id="sljedecaDisk" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>
                <input id="trenStranicaDisk" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">
                <a id="prethodniDisk" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>
                <a id="pocetakDisk" style="float: right; margin: 3% 2% 0% 1%">Početak</a>
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
        <script src="jQuery/ajax_diskusije.js"></script>
      
    </body>
</html>
