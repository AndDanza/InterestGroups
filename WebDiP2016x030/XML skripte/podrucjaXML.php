<?php

require '../PHP Klase/baza.class.php';
require '../PHP Klase/korisnik.class.php';
session_start();

header ("Content-Type:text/xml");

$veza = new Baza();
$veza->spojiDB();

$vrstaPodataka = $_GET["tip"];

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
    case "pregledPodrucja":
        $sqlString = "SELECT kor.ime, kor.prezime, kor.korisnicko_ime, pod.id_podrucja, pod.naziv_podrucja "
                    ."FROM `podrucja_interesa` AS pod "
                    ."JOIN korisnik AS kor ON kor.id_korisnik= pod.moderator";
        if(isset($_GET["trazi"]))
            {
                $pogled = $_GET["trazi"];
                $pogled = "%".$pogled."%";
                $sqlString .= " WHERE (kor.korisnicko_ime LIKE '$pogled' OR pod.naziv_podrucja LIKE '$pogled')";
            }
        if(isset($_GET["sort"]))
        {
            $sortiraj = $_GET["sort"];
            $sqlString .= " ORDER BY $sortiraj";
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($ime, $prezime, $korIme, $id_podrucja, $nazivPod) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("podrucje");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('ime_korisnika', $ime));
            $trenutniZapis->appendChild($domXML->createElement('prezime_korisnika', $prezime));
            $trenutniZapis->appendChild($domXML->createElement('korisnicko_ime', $korIme));
            $trenutniZapis->appendChild($domXML->createElement('id_podrucja', $id_podrucja));
            $trenutniZapis->appendChild($domXML->createElement('naziv_podrucja', $nazivPod));
        }
        break;
    case "podrucja":
        if(isset($_SESSION["aktivniKorisnik"]))
        {
            $korisink = $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
            $vrsta = intval($korisnik->get_vrsta_korisnika());
            $id_mod = intval($korisnik->get_id());
        }
        
        $sqlString = "SELECT pod.id_podrucja, pod.naziv_podrucja FROM podrucja_interesa AS pod ";
        if(isset($_SESSION["aktivniKorisnik"]))
        {
            if($vrsta === 2)
            {
                $sqlString .= "WHERE pod.moderator = $id_mod";
            }
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($id, $naziv) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("podrucje");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('id_podrucja', $id));
            $trenutniZapis->appendChild($domXML->createElement('naziv_podrucja', $naziv));
        }
        break;
    case "diskusijeNereg":
       $id = $_GET["id"];
       $sqlString = "SELECT dis .id_diskusija, dis .naziv_diskusije, dis .pravila, dis .datum_vrijeme_otvaranja, "
                        ."(SELECT COUNT(*) FROM komentari WHERE diskusija=dis .id_diskusija) AS brKom "
                    ."FROM `diskusija` AS dis WHERE dis.podrucja_interesa = $id "
                    ."ORDER BY brKom DESC "
                    ."LIMIT 0,3";
        $rezUpita = $veza->selectDB($sqlString);
        while(list($id, $naziv, $pravila, $vrijeme) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("diskusija");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('id_diskusije', $id));
            $trenutniZapis->appendChild($domXML->createElement('naziv', $naziv));
            $trenutniZapis->appendChild($domXML->createElement('pravila', $pravila));
            $trenutniZapis->appendChild($domXML->createElement('otvorena', $vrijeme));
        }
        break;
    case "diskusija":
        $id = $_GET["id"];
        $vrijeme = DohvatiVrijemePlusPomak($veza);
        $sqlString = "SELECT id_diskusija, `naziv_diskusije`,`pravila`,`datum_vrijeme_otvaranja` , datum_vrijeme_zatvaranja "
            . "FROM `diskusija` WHERE `podrucja_interesa` = $id AND datum_vrijeme_zatvaranja >= '$vrijeme'";
        $rezUpita = $veza->selectDB($sqlString);
        while(list($diskID, $naziv, $pravila, $vrijeme, $kraj) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("diskusija");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);
            
            $trenutniZapis->appendChild($domXML->createElement('id_diskusije', $diskID));
            $trenutniZapis->appendChild($domXML->createElement('naziv', $naziv));
            $trenutniZapis->appendChild($domXML->createElement('pravila', $pravila));
            $trenutniZapis->appendChild($domXML->createElement('otvorena', $vrijeme));
            $trenutniZapis->appendChild($domXML->createElement('zatvorena', $kraj));
        }
        break;
    case "diskusijaModeratora":
        $korisink = $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
        $id_mod = intval($korisnik->get_id());
        $vrsta = intval($korisnik->get_vrsta_korisnika());
        
        if($vrsta === 2)
        {
            $sqlString = "SELECT id_diskusija, `naziv_diskusije`,`pravila`,`datum_vrijeme_otvaranja`, pod.naziv_podrucja, datum_vrijeme_zatvaranja  "
                        ."FROM `diskusija` "
                        ."JOIN podrucja_interesa AS pod ON pod.id_podrucja = diskusija.podrucja_interesa "
                        ."WHERE `podrucja_interesa` IN (SELECT `id_podrucja` FROM podrucja_interesa WHERE `moderator` = $id_mod) ";
            if(isset($_GET["trazi"]))
            {
                $pogled = $_GET["trazi"];
                $pogled = "%".$pogled."%";
                $sqlString .= " AND (naziv_diskusije LIKE '$pogled' OR datum_vrijeme_otvaranja LIKE '$pogled' "
                        . "OR pravila LIKE '$pogled' OR pod.naziv_podrucja LIKE '$pogled')";
            }
        }
        else if($vrsta === 1)
        {
            $sqlString = "SELECT id_diskusija, `naziv_diskusije`,`pravila`,`datum_vrijeme_otvaranja`, pod.naziv_podrucja, datum_vrijeme_zatvaranja  "
                        ."FROM `diskusija` "
                        ."JOIN podrucja_interesa AS pod ON pod.id_podrucja = diskusija.podrucja_interesa ";
            if(isset($_GET["trazi"]))
            {
                $pogled = $_GET["trazi"];
                $pogled = "%".$pogled."%";
                $sqlString .= " AND (naziv_diskusije LIKE '$pogled' OR datum_vrijeme_otvaranja LIKE '$pogled' "
                        . "OR pravila LIKE '$pogled' OR pod.naziv_podrucja LIKE '$pogled')";
            }
        }
        if(isset($_GET["sort"]))
        {
            $sortiraj = $_GET["sort"];
            $sqlString .= " ORDER BY $sortiraj";
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($diskID, $naziv, $pravila, $vrijeme, $pod, $zatvoreno) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("diskusija");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);
            
            $trenutniZapis->appendChild($domXML->createElement('id_diskusije', $diskID));
            $trenutniZapis->appendChild($domXML->createElement('naziv', $naziv));
            $trenutniZapis->appendChild($domXML->createElement('pravila', $pravila));
            $trenutniZapis->appendChild($domXML->createElement('datumVrijeme', $vrijeme));
            $trenutniZapis->appendChild($domXML->createElement('podrucje', $pod));
            $trenutniZapis->appendChild($domXML->createElement('zatvoreno', $zatvoreno));
        }
        break;
    case "komentar":
        $id = $_GET["id"];
        $sqlString = "SELECT kor.korisnicko_ime, `datum_vrijeme_pisanja`, `tekst_komentara` "
                    ."FROM komentari AS kom JOIN korisnik AS kor ON kor.id_korisnik=kom.korisnik "
                    ."WHERE `diskusija` = $id "
                    ."ORDER BY `datum_vrijeme_pisanja` DESC";
        $rezUpita = $veza->selectDB($sqlString);
        while(list($user, $vrijeme, $tekst) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("komentar");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);
            
            $trenutniZapis->appendChild($domXML->createElement('user', $user));
            $trenutniZapis->appendChild($domXML->createElement('datumVrijeme', $vrijeme));
            $trenutniZapis->appendChild($domXML->createElement('diskusija', $id));
            $trenutniZapis->appendChild($domXML->createElement('tekst', $tekst));
        }
        break;
}

echo $domXML->saveXML();

$veza ->zatvoriDB();
