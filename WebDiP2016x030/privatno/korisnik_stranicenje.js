/*pregled korisnika sustava zaključan sa htaccess*/
function DohvatiKorisnike(kreni, stani, trazi = "")
{
    $("#tablicaKorisnik tbody").empty();
    var brojZapisa = parseInt($("#brStranicaKorisnik").find(":selected").val());
    var brojac = 0;
    
    $.ajax({
        url: '../XML skripte/dohvatiXML.php?podaci=svi_korisnici'+trazi,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {            
            $(xml).find("korisnik").each(function ()
            {
                brojac++;

                if (brojac >= kreni && brojac <= stani)
                {
                    var ime = $(this).find("ime").text();
                    var prezime = $(this).find("prezime").text();
                    var korIme = $(this).find("korisnicko_ime").text();
                    var email = $(this).find("email").text();
                    var lozinka = $(this).find("lozinka").text();
                    var vrsta = $(this).find("naziv_tipa").text();

                    var red = "<tr>";
                    red += ("<td>" + ime + "</td>");
                    red += ("<td>" + prezime + "</td>");
                    red += ("<td>" + korIme + "</td>");
                    red += ("<td>" + email + "</td>");
                    red += ("<td>" + lozinka + "</td>");
                    red += ("<td>" + vrsta + "</td>");
                    red += "</tr>";

                    $("#tablicaKorisnik tbody").append(red);
                }
            });
            
            if(trazi === "")
            {
                $("#pocetakKorisnik").attr("onclick", "DohvatiKorisnike(0, "+brojZapisa+")");
                $("#krajKorisnik").attr("onclick", "DohvatiKorisnike("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaKorisnik").attr("onclick", "DohvatiKorisnike("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniKorisnik").attr("onclick", "DohvatiKorisnike("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakKorisnik").attr("onclick", "DohvatiKorisnike(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajKorisnik").attr("onclick", "DohvatiKorisnike("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaKorisnik").attr("onclick", "DohvatiKorisnike("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniKorisnik").attr("onclick", "DohvatiKorisnike("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranicaKorisnik").attr("value", parseInt(stani/brojZapisa));
    
}


$(document).ready(function ()
{
    var brojZapisa = parseInt($("#brStranicaKorisnik").find(":selected").val());
    DohvatiKorisnike(0, brojZapisa);

    $("#brStranicaKorisnik").change(function ()
    {
        brojZapisa = parseInt($("#brStranicaKorisnik").find(":selected").val());
        DohvatiKorisnike(0, brojZapisa);
    });
    
    $("#searchKorisnik").change(function () 
    {
        var pretrazi = $("#searchKorisnik").val();
        brojZapisa = parseInt($("#brStranicaKorisnik").find(":selected").val());
        
        if(pretrazi !== "")
        {
            DohvatiKorisnike(0, brojZapisa, ("&trazi="+pretrazi));
        }
        else
        {
            DohvatiKorisnike(0, brojZapisa);
        }
    });
    
    $("#korisnikKorIme").click(function()
    {
        brojZapisa = parseInt($("#brStranicaKorisnik").find(":selected").val());
        var sort = $("#korisnikKorIme").attr("onclick");
                
        if(sort === "DohvatiKorisnike(0, "+brojZapisa+",('&sort=kor.korisnicko_ime DESC'))")
        {
            $("#korisnikKorIme").attr("onclick", "DohvatiKorisnike(0, "+brojZapisa+",('&sort=korisnicko_ime ASC'))");
        }
        else
        {
            $("#korisnikKorIme").attr("onclick", "DohvatiKorisnike(0, "+brojZapisa+",('&sort=korisnicko_ime DESC'))");
        }
    });
    
    $("#korisnikPrezime").click(function()
    {
        brojZapisa = parseInt($("#brStranicaKorisnik").find(":selected").val());
        var sort = $("#korisnikPrezime").attr("onclick");
                
        if(sort === "DohvatiLogOstalo(0, "+brojZapisa+",('&sort=log.datum_vrijeme_akcije DESC'))")
        {
            $("#korisnikPrezime").attr("onclick", "DohvatiKorisnike(0, "+brojZapisa+",('&sort=prezime ASC'))");
        }
        else
        {
            $("#korisnikPrezime").attr("onclick", "DohvatiKorisnike(0, "+brojZapisa+",('&sort=prezime DESC'))");
        }
    });
});