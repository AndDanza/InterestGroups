/*mogućnost otključavanja i zaključavanja korisnika*/
function DohvatiKorisnikePodrucja(kreni, stani, trazi = "")
{
    $("#tablicaKontrolaPod tbody").empty();
    var brojZapisa = parseInt($("#brStranicaKontrolaPod").find(":selected").val());
    var brojac = 0;
    
    $.ajax({
        url: './XML skripte/dohvatiXML.php?podaci=pregledModeratora'+trazi,
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
                    var id_diskusije = $(this).find("id_diskusije").text();
                    var ime = $(this).find("ime").text();
                    var prezime = $(this).find("prezime").text();
                    var korIme = $(this).find("korisnicko_ime").text();
                    var diskusija = $(this).find("naziv_diskusije").text();
                    var email = $(this).find("email").text();
                    var zabrana = $(this).find("zabrana").text();

                    var red = "<tr>";
                    red += ("<td>" + ime + "</td>");
                    red += ("<td>" + prezime + "</td>");
                    red += ("<td>" + korIme + "</td>");
                    red += ("<td>" + diskusija + "</td>");
                    red += ("<td>" + email + "</td>");
                    
                    if(parseInt(zabrana) === 0)
                    {
                        red += ("<td><a href='pregledKorisnikaModerator.php?block="+id+"&disk="+id_diskusije+"'>Blokiraj</a></td>");
                    }
                    else
                    {
                        red += ("<td><a href='pregledKorisnikaModerator.php?unblock="+id+"&disk="+id_diskusije+"'>Odbokiraj</a></td>");
                    }
                    red += "</tr>";

                    $("#tablicaKontrolaPod tbody").append(red);
                }
            });
            
            if(trazi === "")
            {
                $("#pocetakKontrolaPod").attr("onclick", "DohvatiKorisnikePodrucja(0, "+brojZapisa+")");
                $("#krajKontrolaPod").attr("onclick", "DohvatiKorisnikePodrucja("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaKontrolaPod").attr("onclick", "DohvatiKorisnikePodrucja("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniKontrolaPod").attr("onclick", "DohvatiKorisnikePodrucja("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakKontrolaPod").attr("onclick", "DohvatiKorisnikePodrucja(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajKontrolaPod").attr("onclick", "DohvatiKorisnikePodrucja("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaKontrolaPod").attr("onclick", "DohvatiKorisnikePodrucja("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniKontrolaPod").attr("onclick", "DohvatiKorisnikePodrucja("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranicaKontrolaPod").attr("value", parseInt(stani/brojZapisa));
    
}


$(document).ready(function ()
{
    var brojZapisa = parseInt($("#brStranicaKontrolaPod").find(":selected").val());
    DohvatiKorisnikePodrucja(0, brojZapisa);

    $("#brStranicaKontrolaPod").change(function ()
    {
        brojZapisa = parseInt($("#brStranicaKontrolaPod").find(":selected").val());
        DohvatiKorisnikePodrucja(0, brojZapisa);
    });
    
    
    $("#searchKontrolaPod").change(function () 
    {
        var pretrazi = $("#searchKontrolaPod").val();
        brojZapisa = parseInt($("#brStranicaKontrolaPod").find(":selected").val());
        
        if(pretrazi !== "")
        {
            DohvatiKorisnikePodrucja(0, brojZapisa, ("&trazi="+pretrazi));
        }
        else
        {
            DohvatiKorisnikePodrucja(0, brojZapisa);
        }
    });
    
    $("#pregledKorIme").click(function()
    {
        brojZapisa = parseInt($("#brStranicaKontrolaPod").find(":selected").val());
        var sort = $("#pregledKorIme").attr("onclick");
                
        if(sort === "DESC)")
        {
            $("#pregledKorIme").attr("onclick", "ASC)");
            DohvatiKorisnikePodrucja(0, brojZapisa,'&sort=korisnik.korisnicko_ime DESC');
        }
        else
        {
            $("#pregledKorIme").attr("onclick", "DESC)");
            DohvatiKorisnikePodrucja(0, brojZapisa,'&sort=korisnik.korisnicko_ime ASC');
        }
    });
    
    $("#pregledDisk").click(function()
    {
        brojZapisa = parseInt($("#brStranicaKontrolaPod").find(":selected").val());
        var sort = $("#pregledDisk").attr("onclick");
                
        if(sort === "DESC)")
        {
            $("#pregledDisk").attr("onclick", "ASC");
            DohvatiKorisnikePodrucja(0, brojZapisa,'&sort=&sort=diskusija.naziv_diskusije DESC');
        }
        else
        {
            $("#pregledDisk").attr("onclick", "DESC");
            DohvatiKorisnikePodrucja(0, brojZapisa,'&sort=&sort=diskusija.naziv_diskusije ASC');
        }
    });
});