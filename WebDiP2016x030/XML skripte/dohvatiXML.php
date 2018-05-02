<?php

require '../PHP Klase/baza.class.php';
require '../PHP Klase/korisnik.class.php';
session_start();

header ("Content-Type:text/xml");

$veza = new Baza();
$veza->spojiDB();

$vrstaPodataka = $_GET["podaci"];

function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s", ($pomak[0]*60*60) + time());
}

$domXML = new DOMDocument('1.0', 'UTF-8');
/* korijenski tag */
$xmlDatoteka = $domXML->createElement("xml");
/* dodavanje taga u xml datoteku */
$xmlDatoteka = $domXML->appendChild($xmlDatoteka);

switch($vrstaPodataka)
{
    case "svi_korisnici":
        $sqlString = "SELECT id_korisnik, ime, prezime, korisnicko_ime, email, lozinka, aktivan_racun, tip.naziv_tipa "
            . "FROM korisnik "
            . "JOIN tip_korisnika AS tip ON tip.id_tip_korisnika=korisnik.tip_korisnika";
        if(isset($_GET["trazi"]))
        {
            $pogled = $_GET["trazi"];
            $pogled = "%".$pogled."%";
            $sqlString .= " WHERE korisnik.ime LIKE '$pogled' OR korisnik.prezime LIKE '$pogled'";
        }
        if(isset($_GET["sort"]))
        {
            $sortiraj = $_GET["sort"];
            $sqlString .= " ORDER BY $sortiraj";
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($id, $ime, $prezime, $korIme, $email, $lozinka, $aktiv, $vrsta) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("korisnik");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('id', $id));
            $trenutniZapis->appendChild($domXML->createElement('ime', $ime));
            $trenutniZapis->appendChild($domXML->createElement('prezime', $prezime));
            $trenutniZapis->appendChild($domXML->createElement('korisnicko_ime', $korIme));
            $trenutniZapis->appendChild($domXML->createElement('email', $email));
            $trenutniZapis->appendChild($domXML->createElement('lozinka', $lozinka));
            $trenutniZapis->appendChild($domXML->createElement('aktiv', $aktiv));
            $trenutniZapis->appendChild($domXML->createElement('naziv_tipa', $vrsta));
        }
        break;
    case "blokirani":
        $sqlString = "SELECT id_korisnik, ime, prezime, korisnicko_ime, email, aktivan_racun FROM korisnik "
                    ."WHERE id_korisnik NOT IN (SELECT korisnik FROM cekanje_aktivacije WHERE kod_iskoristen = 0)";
        if(isset($_GET["trazi"]))
        {
            $pogled = $_GET["trazi"];
            $pogled = "%".$pogled."%";
            $sqlString .= " AND (korisnik.ime LIKE '$pogled' OR korisnik.prezime LIKE '$pogled' "
                         ."OR korisnik.korisnicko_ime LIKE '$pogled')";
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($id, $ime, $prezime, $korIme, $email, $aktiv) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("korisnik");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('id', $id));
            $trenutniZapis->appendChild($domXML->createElement('ime', $ime));
            $trenutniZapis->appendChild($domXML->createElement('prezime', $prezime));
            $trenutniZapis->appendChild($domXML->createElement('korisnicko_ime', $korIme));
            $trenutniZapis->appendChild($domXML->createElement('email', $email));
            $trenutniZapis->appendChild($domXML->createElement('aktiv', $aktiv));
        }
        break;
    case "pregledModeratora":
        $korisink = $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
        $id_mod = intval($korisnik->get_id());
        $vrsta = intval($korisnik->get_vrsta_korisnika());
        
        if($vrsta === 2)
        {
            $sqlString = "SELECT korisnik.id_korisnik, korisnik.ime, korisnik.prezime, korisnik.korisnicko_ime, "
                  . "diskusija.naziv_diskusije, korisnik.email, pretplata.zabrana_komentiranja, diskusija.id_diskusija "
                    ."FROM `pretplata` "
                    ."JOIN korisnik ON korisnik.id_korisnik=pretplata.korisnik "
                    ."JOIN diskusija ON diskusija.id_diskusija = pretplata.diskusija "
                    ."WHERE diskusija IN (SELECT id_diskusija FROM diskusija WHERE podrucja_interesa "
                  . "IN (SELECT id_podrucja FROM podrucja_interesa WHERE moderator = $id_mod))";
            if(isset($_GET["trazi"]))
            {
                $sqlString .= " AND"; 
            }
        }
        else if ($vrsta === 1)
        {
            $sqlString = "SELECT korisnik.id_korisnik, korisnik.ime, korisnik.prezime, korisnik.korisnicko_ime, "
                    . "diskusija.naziv_diskusije, korisnik.email, pretplata.zabrana_komentiranja, diskusija.id_diskusija "
                    ."FROM `pretplata`"
                    ."JOIN korisnik ON korisnik.id_korisnik=pretplata.korisnik "
                    ."JOIN diskusija ON diskusija.id_diskusija = pretplata.diskusija";
            if(isset($_GET["trazi"]))
            {
                $sqlString .= " WHERE"; 
            }
        }
        if(isset($_GET["trazi"]))
        {
            $pogled = $_GET["trazi"];
            $pogled = "%".$pogled."%";
            $sqlString .= " (korisnik.ime LIKE '$pogled' OR diskusija.naziv_diskusije LIKE '$pogled' "
                         ."OR korisnik.korisnicko_ime LIKE '$pogled')";
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($id, $ime, $prezime, $korIme, $diskusija, $email, $zabrana, $id_diskusije) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("korisnik");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('id', $id));
            $trenutniZapis->appendChild($domXML->createElement('ime', $ime));
            $trenutniZapis->appendChild($domXML->createElement('prezime', $prezime));
            $trenutniZapis->appendChild($domXML->createElement('korisnicko_ime', $korIme));
            $trenutniZapis->appendChild($domXML->createElement('naziv_diskusije', $diskusija));
            $trenutniZapis->appendChild($domXML->createElement('email', $email));
            $trenutniZapis->appendChild($domXML->createElement('zabrana', $zabrana));
            $trenutniZapis->appendChild($domXML->createElement('id_diskusije', $id_diskusije));
        }
        break;
    case "obavijestZaModeratora":
        $korisink = $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
        $id_mod = intval($korisnik->get_id());
        $vrsta = intval($korisnik->get_vrsta_korisnika());
        
        if($vrsta === 2)
        {
          $sqlString = "SELECT id_korisnik, korisnicko_ime FROM korisnik "
                      ."WHERE id_korisnik IN (SELECT korisnik_id_korisnik FROM odabir_podrucja_interesa "
                        ."WHERE podrucja_interesa_id_podrucja IN (SELECT id_podrucja FROM podrucja_interesa "
                        ."WHERE moderator = $id_mod))"; 
        }
        else if ($vrsta === 1)
        {
            $sqlString = "SELECT id_korisnik, korisnicko_ime FROM korisnik "; 
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($id, $korIme) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("korisnik");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('id', $id));
            $trenutniZapis->appendChild($domXML->createElement('korisnicko_ime', $korIme));
        }
        break;
    case "kuponi":
        $sqlString = "SELECT `id_kupona`, `naziv_kupona`, `datum_vrijeme_izdavanja` "
                    ."FROM `kupon_clanstva` "
                    ."WHERE generirani_kod = -1 AND `datum_vrijeme_istjecanja` = '0000-00-00 00:00:00'"; 
        if(isset($_GET["trazi"]))
        {
            $pogled = $_GET["trazi"];
            $pogled = "%".$pogled."%";
            $sqlString .= " AND (naziv_kupona LIKE '$pogled' OR datum_vrijeme_izdavanja LIKE '$pogled')";
        }
        if(isset($_GET["sort"]))
        {
            $sortiraj = $_GET["sort"];
            $sqlString .= " ORDER BY $sortiraj";
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($id, $naziv, $datum) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("kupon");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('id_kupon', $id));
            $trenutniZapis->appendChild($domXML->createElement('naziv', $naziv));
            $trenutniZapis->appendChild($domXML->createElement('kreiranje', $datum));
        }
        break;
}

echo $domXML->saveXML();

$veza ->zatvoriDB();
