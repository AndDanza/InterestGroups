<?php

require '../PHP Klase/baza.class.php';
header ("Content-Type:text/xml");

$veza = new Baza();
$veza->spojiDB();

$vrstaPodataka = $_GET["log"];

$domXML = new DOMDocument('1.0', 'UTF-8');
/* korijenski tag */
$xmlDatoteka = $domXML->createElement("xml");
/* dodavanje taga u xml datoteku */
$xmlDatoteka = $domXML->appendChild($xmlDatoteka);

switch($vrstaPodataka)
{
    case "prijava":
        $sqlString = "SELECT kor.ime AS ime, kor.prezime AS prezime, kor.korisnicko_ime AS korIme, log.datum_vrijeme_akcije AS prijava, log.datum_vrijeme_odjave AS odjava "
                    ."FROM `log_aplikacije_prijava` as log "
                    ."JOIN korisnik as kor ON kor.id_korisnik = log.korisnik";
        if(isset($_GET["trazi"]))
        {
            $pogled = $_GET["trazi"];
            $pogled = "%".$pogled."%";
            $sqlString .= " WHERE kor.korisnicko_ime LIKE '$pogled' OR log.datum_vrijeme_akcije LIKE '$pogled'";
        }
        if(isset($_GET["sort"]))
        {
            $sortiraj = $_GET["sort"];
            $sqlString .= " ORDER BY $sortiraj";
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($ime, $prezime, $korIme, $prijava, $odjava) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("log");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('ime', $ime));
            $trenutniZapis->appendChild($domXML->createElement('prezime', $prezime));
            $trenutniZapis->appendChild($domXML->createElement('korisnicko_ime', $korIme));
            $trenutniZapis->appendChild($domXML->createElement('prijava', $prijava));
            $trenutniZapis->appendChild($domXML->createElement('odjava', $odjava));
        }
        break;
    case "baza":
        $sqlString = "SELECT kor.ime, kor.prezime, kor.korisnicko_ime, log.datum_vrijeme_akcije, log.tablica, "
                    ."log.vrsta_upita "
                    ."FROM `log_aplikacije_baza` as log "
                    ."JOIN korisnik as kor ON kor.id_korisnik = log.korisnik";
        if(isset($_GET["trazi"]))
        {
            $pogled = $_GET["trazi"];
            $pogled = "%".$pogled."%";
            $sqlString .= " WHERE kor.korisnicko_ime LIKE '$pogled' OR log.vrsta_upita LIKE '$pogled' "
                    . "OR log.datum_vrijeme_akcije LIKE '$pogled'";
        }
        if(isset($_GET["sort"]))
        {
            $sortiraj = $_GET["sort"];
            $sqlString .= " ORDER BY $sortiraj";
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($ime, $prezime, $korIme, $pristup, $tablica, $vrsta) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("log");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('ime', $ime));
            $trenutniZapis->appendChild($domXML->createElement('prezime', $prezime));
            $trenutniZapis->appendChild($domXML->createElement('korisnicko_ime', $korIme));
            $trenutniZapis->appendChild($domXML->createElement('datum_vrijeme', $pristup));
            $trenutniZapis->appendChild($domXML->createElement('tablica', $tablica));
            $trenutniZapis->appendChild($domXML->createElement('vrsta_upita', $vrsta));
        }
        break;
    case "ostalo":
        $sqlString = "SELECT kor.ime AS ime, kor.prezime AS prezime, kor.korisnicko_ime AS korIme, "
                    ."log.datum_vrijeme_akcije AS akcija, log.opis_radnje "
                    ."FROM `log_aplikacije_ostalo` as log "
                    ."JOIN korisnik as kor ON kor.id_korisnik = log.korisnik";
        if(isset($_GET["trazi"]))
        {
            $pogled = $_GET["trazi"];
            $pogled = "%".$pogled."%";
            $sqlString .= " WHERE kor.korisnicko_ime LIKE '$pogled' OR log.datum_vrijeme_akcije LIKE '$pogled' "
                    . "OR log.opis_radnje LIKE '$pogled'";
        }
        if(isset($_GET["sort"]))
        {
            $sortiraj = $_GET["sort"];
            $sqlString .= " ORDER BY $sortiraj";
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($ime, $prezime, $korIme, $akcija, $radnja) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("log");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('ime', $ime));
            $trenutniZapis->appendChild($domXML->createElement('prezime', $prezime));
            $trenutniZapis->appendChild($domXML->createElement('korisnicko_ime', $korIme));
            $trenutniZapis->appendChild($domXML->createElement('akcija', $akcija));
            $trenutniZapis->appendChild($domXML->createElement('radnja', $radnja));
        }
        break;
}

echo $domXML->saveXML();

$veza ->zatvoriDB();
