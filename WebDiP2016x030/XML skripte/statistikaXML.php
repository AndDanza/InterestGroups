<?php

require '../PHP Klase/baza.class.php';
require '../PHP Klase/korisnik.class.php';
session_start();

$veza = new Baza();
$veza->spojiDB();

$vrstaPodataka = $_GET["stat"];

function DohvatiVrijemePlusPomak($baza)
{
    $sql = "SELECT pomak_vremena FROM `konfiguracija_sustava` WHERE id = (SELECT MAX(id) FROM konfiguracija_sustava)";
    $rez = $baza -> selectDB($sql);
    $pomak = $rez->fetch_array();
    
    return date("Y-m-j H:i:s", ($pomak[0]*60*60) + time());
}

header ("Content-Type:text/xml");

$domXML = new DOMDocument('1.0', 'UTF-8');
/* korijenski tag */
$xmlDatoteka = $domXML->createElement("xml");
/* dodavanje taga u xml datoteku */
$xmlDatoteka = $domXML->appendChild($xmlDatoteka);

switch($vrstaPodataka)
{
    case "potroseni":
        $korisnik = intval($_GET["korisnik"]);
        $sqlString = "SELECT kor.id_korisnik, kor.ime, kor.prezime, kor.korisnicko_ime, kupon.naziv_kupona, "
                        ."kupon.potrebno_bodova, kos.datum_vrijeme_kupnje FROM sadrzaj_kosarice AS kupi "
                    ."JOIN kosarica AS kos ON kos.id_kosarica=kupi.kosarica_id_kosarica "
                    ."JOIN korisnik AS kor ON kor.id_korisnik=kos.korisnik "
                    ."JOIN kupon_clanstva AS kupon ON kupon.id_kupona = kupi.kupon_clanstva_id_kupona "
                    ."WHERE kor.id_korisnik = $korisnik";
        if(isset($_GET["trazi"]))
        {
            $pogled = $_GET["trazi"];
            $pogled = "%".$pogled."%";
            $sqlString .= " AND (kos.datum_vrijeme_kupnje LIKE '$pogled' OR kupon.naziv_kupona LIKE '$pogled')";
        }
        if(isset($_GET["sort"]))
        {
            $sortiraj = $_GET["sort"];
            $sqlString .= " ORDER BY $sortiraj";
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($id, $ime, $prezime, $korIme, $nazivKupon, $bodoviKupon, $kupnjaDan) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("statistika");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('id', $id));
            $trenutniZapis->appendChild($domXML->createElement('ime', $ime));
            $trenutniZapis->appendChild($domXML->createElement('prezime', $prezime));
            $trenutniZapis->appendChild($domXML->createElement('korisnicko_ime', $korIme));
            $trenutniZapis->appendChild($domXML->createElement('naziv_kupona', $nazivKupon));
            $trenutniZapis->appendChild($domXML->createElement('broj_bodova', $bodoviKupon));
            $trenutniZapis->appendChild($domXML->createElement('kupnja', $kupnjaDan));
        }
        break;
    case "sakupljeni":
        $korisnik = intval($_GET["korisnik"]);
        $sqlString = "SELECT kor.id_korisnik, kor.ime, kor.prezime, kor.korisnicko_ime, akcija.naziv_akcije, "
                        ."akcija.broj_bodova, bod.datum_vrijeme_stjecanja "
                    ."FROM log_bodova AS bod "
                    ."JOIN korisnik AS kor ON kor.id_korisnik=bod.korisnik "
                    ."JOIN vrsta_akcije AS akcija ON akcija.id_vrste_akcije=bod.vrsta_akcije "
                    ."WHERE kor.id_korisnik = $korisnik";
        if(isset($_GET["trazi"]))
        {
            $pogled = $_GET["trazi"];
            $pogled = "%".$pogled."%";
            $sqlString .= " AND (bod.datum_vrijeme_stjecanja LIKE '$pogled' OR akcija.naziv_akcije LIKE '$pogled')";
        }
        if(isset($_GET["sort"]))
        {
            $sortiraj = $_GET["sort"];
            $sqlString .= " ORDER BY $sortiraj";
        }
        $rezUpita = $veza->selectDB($sqlString);
        while(list($id, $ime, $prezime, $korIme, $nazivAkcije, $bodoviAkcije, $danAkcije) = $rezUpita->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("statistika");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('id', $id));
            $trenutniZapis->appendChild($domXML->createElement('ime', $ime));
            $trenutniZapis->appendChild($domXML->createElement('prezime', $prezime));
            $trenutniZapis->appendChild($domXML->createElement('korisnicko_ime', $korIme));
            $trenutniZapis->appendChild($domXML->createElement('naziv_akcije', $nazivAkcije));
            $trenutniZapis->appendChild($domXML->createElement('broj_bodova', $bodoviAkcije));
            $trenutniZapis->appendChild($domXML->createElement('dan_akcije', $danAkcije));
        }
        break;
    case "kuponi":
        $podrucje = intval($_GET["pod"]);
        $sadTime = DohvatiVrijemePlusPomak($veza);
        $sqlString = "SELECT `id_kupona`, `naziv_kupona`, `potrebno_bodova` FROM `kupon_clanstva` "
                  ."WHERE `podrucja_interesa` = $podrucje AND `datum_vrijeme_istjecanja` >= '$sadTime' "
                  ."AND `id_kupona` NOT IN (SELECT `kupon_clanstva_id_kupona` FROM `sadrzaj_kosarice`)";
        $rezUpita = $veza->selectDB($sqlString);
        while(list($id_kupona, $nazivK, $bodoviK) = $rezUpita->fetch_array())
        {
            if(!in_array($nazivK, $_SESSION["kosarica"]))
            {
                $trenutniZapis = $domXML->createElement("kupon");
                $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

                $trenutniZapis->appendChild($domXML->createElement('id_kupon', $id_kupona));
                $trenutniZapis->appendChild($domXML->createElement('naziv_kupon', $nazivK));
                $trenutniZapis->appendChild($domXML->createElement('bodovi', $bodoviK));
            }
        }
        break;
    case "kupljeniKuponi":
        $korisnik = unserialize($_SESSION["aktivniKorisnik"]);
        $id = intval($korisnik->get_id());
        $sqlString = "SELECT `id_kupona`, `naziv_kupona`, `datum_vrijeme_izdavanja`, `datum_vrijeme_istjecanja`, "
                    ."`generirani_kod` FROM kupon_clanstva WHERE `id_kupona` "
                    ."IN (SELECT `kupon_clanstva_id_kupona` FROM sadrzaj_kosarice WHERE kosarica_id_kosarica "
                    ."IN (SELECT id_kosarica FROM kosarica WHERE korisnik = $id))";
        $result = $veza ->selectDB($sqlString);
        while(list($id_kupona, $nazivK, $izdanK, $istjeceK, $kodK) = $result->fetch_array())
        {
            $trenutniZapis = $domXML->createElement("kupon");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('id_kupon', $id_kupona));
            $trenutniZapis->appendChild($domXML->createElement('naziv_kupon', $nazivK));
            $trenutniZapis->appendChild($domXML->createElement('izdavanje', $izdanK));
            $trenutniZapis->appendChild($domXML->createElement('istice', $istjeceK));
            $trenutniZapis->appendChild($domXML->createElement('kod', $kodK));
        }
        break;
    case "kosarica":
        $uKosarici = $_SESSION["kosarica"];
        foreach($uKosarici as $kljuc => $vrij)
        {
            $trazi = intval($kljuc);
            $sql = "SELECT `id_kupona`, `naziv_kupona`, `potrebno_bodova`, `datum_vrijeme_izdavanja` "
                  ."FROM `kupon_clanstva` WHERE id_kupona = $trazi";
            $result = $veza ->selectDB($sql);
            list($id_kupona, $nazivK, $bodoviK, $izdanK) = $result->fetch_array();
                        
            $trenutniZapis = $domXML->createElement("kosarica");
            $trenutniZapis = $xmlDatoteka->appendChild($trenutniZapis);

            $trenutniZapis->appendChild($domXML->createElement('id_kupon', $id_kupona));
            $trenutniZapis->appendChild($domXML->createElement('naziv_kupon', $nazivK));
            $trenutniZapis->appendChild($domXML->createElement('bodovi', $bodoviK));
            $trenutniZapis->appendChild($domXML->createElement('izdan', $izdanK));          
        }
        break;
}

echo $domXML->saveXML();

$veza ->zatvoriDB();
