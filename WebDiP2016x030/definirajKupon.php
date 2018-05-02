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

function DohvatiSelect($id, $vrsta, $baza)
{
    $dropdown = "";
    $sql = "SELECT `id_podrucja`, `naziv_podrucja`, `moderator` FROM `podrucja_interesa`";
    if($vrsta === 2)
    {
        $sql .= " WHERE `moderator` = $id";
    }
    $rez = $baza->selectDB($sql);
    
    $dropdown = '<select id="mogucaPod" name="definirajPod">';
    while(list($id, $pod) = $rez->fetch_array())
    {
        $dropdown .= '<option value="'.$id.'">'.$pod.'</option>';
    }
    $dropdown .= '</select>';
    
    return $dropdown;
}

function GenerirajKod($id)
{
    $novaLozinka = array_merge(range('a', 'z'), range('A', 'Z'));
    shuffle($novaLozinka);
    $od = rand(0, (count($novaLozinka)-15));
    $novaLozinka = substr(implode($novaLozinka), $od, ($od+15));
    
    return $novaLozinka.$id;
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
                            echo '<li><a style="background: gainsboro;" href="definirajKupon.php"><b>Definiraj kupon</b></a></li>';
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

        <section id="kuponiSekcija">
            <?php
            
            $baza = new Baza();
            $baza ->spojiDB();
            
            $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
            $id = intval($korisnik->get_id());
            $vrsta = intval($korisnik->get_vrsta_korisnika());
                
            $dropdown = DohvatiSelect($id, $vrsta, $baza);
            echo '<form method="POST" action="definirajKupon.php" novalidate>';
            echo '<input name="idKupona" type="number" min="1" placeholder="ID" style="width: 5%;">';
            echo $dropdown;
            echo '<input name="brBodova" type="number" min="10" placeholder="Broj bodova">';
            echo '<input name="trajeDo" type="text" placeholder="yyyy-mm-dd hh:mm:ss">';
            echo '<input type="submit" value="Definiraj" name="definiraj">';
            echo '</form>';
                      
            echo '<h2>Nedodjeljeni kuponi</h2>';
            
            if(isset($_POST["definiraj"]))
            {
                $id_kupon = intval($_POST["idKupona"]);
                $brBodova = intval($_POST["brBodova"]);
                $podrucje = intval($_POST["definirajPod"]);
                $trajeDo = $_POST["trajeDo"];
                $generiraniKod = GenerirajKod($id_kupon);
                
                $sql = "UPDATE `kupon_clanstva` "
                        . "SET `administrator` = $id,`generirani_kod` = '$generiraniKod',"
                        . "`podrucja_interesa` = $podrucje,`potrebno_bodova` = $brBodova,`datum_vrijeme_istjecanja` = '$trajeDo' "
                        . "WHERE `id_kupona` = $id_kupon";
                $baza->updateDB($sql);
                                            
                print "<p id='notifikacija'><b style='color: green; font-size: 25px;'>Kupon je uspješno definiran</b></p>";
            }
            
            $sql = "SELECT `id_kupona`, `naziv_kupona`, `datum_vrijeme_izdavanja` FROM `kupon_clanstva` "
                  ."WHERE generirani_kod = -1 AND potrebno_bodova = -1";
            $result = $baza ->selectDB($sql);
            while(list($id_kupona, $nazivK, $izdanK) = $result->fetch_array())
            {
                echo '<figure style="float: left;">';
                echo '<img src="Slike/kupon.png" alt="kupon" style="height:150px; width:90%; margin-left: 5%;">';
                echo '<figcaption>';
                echo "<b>Id: </b>".$id_kupona."<br>";
                echo "<b>Naziv: </b><br>".$nazivK."<br>";
                echo "<b>Datum kreiranja: </b><br>".$izdanK."<br><br>";
                echo '</figcaption>';
                echo '</figure>';
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
        <script src="jQuery/socijalneMreze.js"></script>
        
    </body>
</html>


