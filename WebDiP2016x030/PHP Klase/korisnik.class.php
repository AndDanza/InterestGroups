<?php


class Korisnik {
    private $id;
    private $kor_ime;
    private $ime;
    private $prezime;
    private $lozinka;
    private $email;
    private $prijavljen_od;
    private $vrsta_korisnika;
    private $broj_bodova;

    public function Korisnik() {
        
    }

    public function set_podaci($id, $p_kor_ime, $p_ime, $p_prezime, $p_lozinka, $p_vrsta_korisnika, $p_email, $p_vrijeme) {
        $this->id = $id;
        $this->kor_ime = $p_kor_ime;
        $this->ime = $p_ime;
        $this->prezime = $p_prezime;
        $this->lozinka = $p_lozinka;
        $this->vrsta_korisnika = $p_vrsta_korisnika;
        $this->prijavljen_od = $p_vrijeme;
        $this->email = $p_email;
    }
    
    public function azuriraj_podatke($p_kor_ime, $p_ime, $p_prezime, $p_lozinka, $p_email)
    {
        $this->kor_ime = $p_kor_ime;
        $this->ime = $p_ime;
        $this->prezime = $p_prezime;
        $this->lozinka = $p_lozinka;
        $this->email = $p_email;
    }
    
    public function set_brBodova($bodovi)
    {
        $this->broj_bodova = $bodovi;
    }
    
    public function get_brBodova()
    {
        return $this->broj_bodova;
    }
    
    public function get_id() {
        return $this->id;
    }
    
    public function get_ime() {
        return $this->ime;
    }
    
    public function get_prezime() {
        return $this->prezime;
    }

    public function get_kor_ime() {
        return $this->kor_ime;
    }

    public function get_ime_prezime() {
        return $this->ime . " " . $this->prezime;
    }

    public function get_prijavljen_od() {
        return $this->prijavljen_od;
    }
    
    public function get_email() {
        return $this->email;
    }
    
    public function get_lozinka() {
        return $this->lozinka;
    }
    
    public function get_vrsta_korisnika() {
        return $this->vrsta_korisnika;
    }
}

