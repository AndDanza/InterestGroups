/*mogućnost otključavanja i zaključavanja korisnika*/
function DohvatiKorisnike(kreni, stani, trazi = "")
{
    $("#tablicaKontrola tbody").empty();
    var brojZapisa = parseInt($("#brStranicaKontrola").find(":selected").val());
    var brojac = 0;
    
    $.ajax({
        url: './XML skripte/dohvatiXML.php?podaci=blokirani'+trazi,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {            
            $(xml).find("korisnik").each(function ()
            {
                brojac++;

                if (brojac >= kreni && brojac <= stani)
                {
                    var id = $(this).find("id").text();
                    var ime = $(this).find("ime").text();
                    var prezime = $(this).find("prezime").text();
                    var korIme = $(this).find("korisnicko_ime").text();
                    var email = $(this).find("email").text();
                    var aktiv = $(this).find("aktiv").text();

                    var red = "<tr>";
                    red += ("<td>" + ime + "</td>");
                    red += ("<td>" + prezime + "</td>");
                    red += ("<td>" + korIme + "</td>");
                    red += ("<td>" + email + "</td>");
                    console.log(ime + " "+prezime+" - "+aktiv);
                    if(parseInt(aktiv) === 1)
                    {
                        red += ("<td><a href='kontrolaKorisnika.php?block="+id+"'>Blokiraj</a></td>");
                    }
                    else
                    {
                        red += ("<td><a href='kontrolaKorisnika.php?unblock="+id+"'>Odbokiraj</a></td>");
                    }
                    
                    red += "</tr>";

                    $("#tablicaKontrola tbody").append(red);
                }
            });
            
            if(trazi === "")
            {
                $("#pocetakKontrola").attr("onclick", "DohvatiKorisnike(0, "+brojZapisa+")");
                $("#krajKontrola").attr("onclick", "DohvatiKorisnike("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaKontrola").attr("onclick", "DohvatiKorisnike("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniKontrola").attr("onclick", "DohvatiKorisnike("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakKontrola").attr("onclick", "DohvatiKorisnike(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajKontrola").attr("onclick", "DohvatiKorisnike("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaKontrola").attr("onclick", "DohvatiKorisnike("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniKontrola").attr("onclick", "DohvatiKorisnike("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranicaKontrola").attr("value", parseInt(stani/brojZapisa));
    
}


$(document).ready(function ()
{
    var brojZapisa = parseInt($("#brStranicaKontrola").find(":selected").val());
    DohvatiKorisnike(0, brojZapisa);

    $("#brStranicaKontrola").change(function ()
    {
        var brojZapisa = parseInt($("#brStranicaKontrola").val());
        DohvatiKorisnike(0, brojZapisa);
    });
    
    
    $("#searchKontrola").change(function () 
    {
        var pretrazi = $("#searchKontrola").val();
        brojZapisa = parseInt($("#brStranicaKontrola").find(":selected").val());
        
        if(pretrazi !== "")
        {
            DohvatiKorisnike(0, brojZapisa, ("&trazi="+pretrazi));
        }
        else
        {
            DohvatiKorisnike(0, brojZapisa);
        }
    });
    
    $("#kontrolaKorIme").click(function()
    {
        var sort = $("#kontrolaKorIme").attr("onclick");
        brojZapisa = parseInt($("#brStranicaKontrola").find(":selected").val());
        
        if(sort === "DohvatiKorisnike(0, "+brojZapisa+",('&sort=korisnicko_ime DESC'))")
        {
            $("#kontrolaKorIme").attr("onclick", "DohvatiKorisnike(0, "+brojZapisa+",('&sort=korisnicko_ime ASC'))");
        }
        else
        {
            $("#kontrolaKorIme").attr("onclick", "DohvatiKorisnike(0, "+brojZapisa+",('&sort=korisnicko_ime DESC'))");
        }
    });
    
    $("#kontrolaPrezime").click(function()
    {
        var sort = $("#kontrolaPrezime").attr("onclick");
        brojZapisa = parseInt($("#brStranicaKontrola").find(":selected").val());
        
        if(sort === "DohvatiKorisnike(0, "+brojZapisa+",('&sort=log.datum_vrijeme_akcije DESC'))")
        {
            $("#kontrolaPrezime").attr("onclick", "DohvatiKorisnike(0, "+brojZapisa+",('&sort=prezime ASC'))");
        }
        else
        {
            $("#kontrolaPrezime").attr("onclick", "DohvatiKorisnike(0, "+brojZapisa+",('&sort=prezime DESC'))");
        }
    });
});