/*sakupljeni bodovi za korisnik*/
function DohvatiSakupljeno(kreni, stani, trazi = "")
{
    $("#tablicaLogSakupljeni tbody").empty();
    var brojZapisa = parseInt($("#brStranicaSakupljeni").find(":selected").val());
    var korisnikID = parseInt($("#korsniciSustava").find(":selected").val());
    var brojac = 0;
    var brojBodova = 0;
    var total = 0;
    
    $.ajax({
        url: './XML skripte/statistikaXML.php?stat=sakupljeni&korisnik='+korisnikID+trazi,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("statistika").each(function ()
            {
                brojac++;
                var bodovi = $(this).find("broj_bodova").text();
                total += parseInt(bodovi);
                
                if (brojac >= kreni && brojac <= stani)
                {
                    var ime = $(this).find("ime").text();
                    var prezime = $(this).find("prezime").text();
                    var korIme = $(this).find("korisnicko_ime").text();
                    var akcija = $(this).find("naziv_akcije").text();
                    var datum = $(this).find("dan_akcije").text();

                    var red = "<tr>";
                    red += ("<td>" + ime + "</td>");
                    red += ("<td>" + prezime + "</td>");
                    red += ("<td>" + korIme + "</td>");
                    red += ("<td>" + akcija + "</td>");
                    red += ("<td>" + bodovi + "</td>");
                    brojBodova += parseInt(bodovi);
                    red += ("<td>" + datum + "</td>");
                    red += "</tr>";

                    $("#tablicaLogSakupljeni tbody").append(red);
                }
            });
            
            var red = "<tr><td colspan = '4'>Zbroj sakupljenih bodova za danu stranicu</td><td>" + brojBodova + "</td></tr>";
            red += "<tr><td colspan = '4'>Sveukupno sakupljenih bodova</td><td>" + total + "</td></tr>";
            $("#tablicaLogSakupljeni tbody").append(red);
            
            if(trazi === "")
            {
                $("#pocetakSakupljeni").attr("onclick", "DohvatiSakupljeno(0, "+brojZapisa+")");
                $("#krajSakupljeni").attr("onclick", "DohvatiSakupljeno("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaSakupljeni").attr("onclick", "DohvatiSakupljeno("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniSakupljeni").attr("onclick", "DohvatiSakupljeno("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakSakupljeni").attr("onclick", "DohvatiSakupljeno(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajSakupljeni").attr("onclick", "DohvatiSakupljeno("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaSakupljeni").attr("onclick", "DohvatiSakupljeno("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniSakupljeni").attr("onclick", "DohvatiSakupljeno("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenSakupljeni").attr("value", parseInt(stani/brojZapisa));
    
}

$(document).ready(function ()
{
    var brojZapisa = parseInt($("#brStranicaSakupljeni").find(":selected").val());
    DohvatiSakupljeno(0, brojZapisa);
    
    $("#korsniciSustava").change(function()
    {
        brojZapisa = parseInt($("#brStranicaSakupljeni").find(":selected").val());
        DohvatiSakupljeno(0, brojZapisa);
    });
    
    $("#brStranicaSakupljeni").change(function ()
    {
        brojZapisa = parseInt($("#brStranicaSakupljeni").find(":selected").val());
        DohvatiSakupljeno(0, brojZapisa);
    });
    
    $("#sakupljeniBodovi").click(function()
    {
        brojZapisa = parseInt($("#brStranicaSakupljeni").find(":selected").val());
        var sort = $("#sakupljeniBodovi").attr("onclick");
        
        if(sort === "DESC")
        {
            $("#sakupljeniBodovi").attr("onclick", "ASC");
            DohvatiSakupljeno(0, brojZapisa, '&sort=akcija.broj_bodova DESC');
        }
        else
        {
            $("#sakupljeniBodovi").attr("onclick", "DESC");
            DohvatiSakupljeno(0, brojZapisa, '&sort=akcija.broj_bodova ASC');
        }
    });
    
    $("#sakupljeniDate").click(function()
    {
        brojZapisa = parseInt($("#brStranicaSakupljeni").find(":selected").val());
        var sort = $("#sakupljeniDate").attr("onclick");
        
        if(sort === "DESC")
        {
            $("#sakupljeniDate").attr("onclick", "ASC");
            DohvatiSakupljeno(0, brojZapisa, '&sort=bod.datum_vrijeme_stjecanja DESC');
        }
        else
        {
            $("#sakupljeniDate").attr("onclick", "DESC");
            DohvatiSakupljeno(0, brojZapisa, '&sort=bod.datum_vrijeme_stjecanja ASC');
        }
    });
    
    
    $("#searchSakupljeni").change(function () 
    {
        var pretrazi = $("#searchSakupljeni").val();
        brojZapisa = parseInt($("#brStranicaSakupljeni").find(":selected").val());
        
        if(pretrazi !== "")
        {
            DohvatiSakupljeno(0, brojZapisa,("&trazi="+pretrazi));
        }
        else
        {
            DohvatiSakupljeno(0, brojZapisa);
        }
    });
});




/*potrošeni bodovi za korisnik*/
function DohvatiPotroseno(kreni, stani, trazi = "")
{
    $("#tablicaLogPotroseni tbody").empty();
    var brojZapisa = parseInt($("#brStranicaPotroseni").find(":selected").val());
    var korisnikID = parseInt($("#korsniciSustava").find(":selected").val());
    var brojac = 0;
    var brojBodova = 0;
    var total = 0;
    
    $.ajax({
        url: './XML skripte/statistikaXML.php?stat=potroseni&korisnik='+korisnikID+trazi,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("statistika").each(function ()
            {
                brojac++;
                var bodovi = $(this).find("broj_bodova").text();
                total += parseInt(bodovi);

                if (brojac >= kreni && brojac <= stani)
                {
                    var ime = $(this).find("ime").text();
                    var prezime = $(this).find("prezime").text();
                    var korIme = $(this).find("korisnicko_ime").text();
                    var kupon = $(this).find("naziv_kupona").text();
                    var datum = $(this).find("kupnja").text();

                    var red = "<tr>";
                    red += ("<td>" + ime + "</td>");
                    red += ("<td>" + prezime + "</td>");
                    red += ("<td>" + korIme + "</td>");
                    red += ("<td>" + kupon + "</td>");
                    red += ("<td>" + bodovi + "</td>");
                    brojBodova += parseInt(bodovi);
                    red += ("<td>" + datum + "</td>");
                    red += "</tr>";

                    $("#tablicaLogPotroseni tbody").append(red);
                }
            });
            
            var red = "<tr><td colspan = '4'>Zbroj potrošenih bodova za danu stranicu</td><td>" + brojBodova + "</td></tr>";
            red += "<tr><td colspan = '4'>Sveukupno potrošenih bodova</td><td>" + total + "</td></tr>";
            $("#tablicaLogPotroseni tbody").append(red);
            
            if(trazi === "")
            {
                $("#pocetakPotroseni").attr("onclick", "DohvatiPotroseno(0, "+brojZapisa+")");
                $("#krajPotroseni").attr("onclick", "DohvatiPotroseno("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaPotroseni").attr("onclick", "DohvatiPotroseno("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniPotroseni").attr("onclick", "DohvatiPotroseno("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakPotroseni").attr("onclick", "DohvatiPotroseno(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajPotroseni").attr("onclick", "DohvatiPotroseno("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaPotroseni").attr("onclick", "DohvatiPotroseno("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniPotroseni").attr("onclick", "DohvatiPotroseno("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranicaPotroseni").attr("value", parseInt(stani/brojZapisa));
    
}

$(document).ready(function ()
{
    var brojZapisa = parseInt($("#brStranicaPotroseni").find(":selected").val());
    DohvatiPotroseno(0, brojZapisa);
    
    $("#korsniciSustava").change(function()
    {
        brojZapisa = parseInt($("#brStranicaPotroseni").find(":selected").val());
        DohvatiPotroseno(0, brojZapisa);
    });
    
    $("#brStranicaPotroseni").change(function ()
    {
        brojZapisa = parseInt($("#brStranicaPotroseni").find(":selected").val());
        DohvatiPotroseno(0, brojZapisa);
    });
    
    $("#potroseniKupon").click(function()
    {
        brojZapisa = parseInt($("#brStranicaPotroseni").find(":selected").val());
        var sort = $("#potroseniKupon").attr("onclick");
        
        if(sort === "DESC")
        {
            $("#potroseniKupon").attr("onclick", "ASC");
            DohvatiPotroseno(0, brojZapisa, '&sort=kupon.naziv_kupona DESC');
        }
        else
        {
            $("#potroseniKupon").attr("onclick", "DESC");
            DohvatiPotroseno(0, brojZapisa, '&sort=kupon.naziv_kupona ASC');
        }
    });
    
    $("#potroseniDate").click(function()
    {
        brojZapisa = parseInt($("#brStranicaPotroseni").find(":selected").val());
        var sort = $("#potroseniDate").attr("onclick");
        
        if(sort === "DESC")
        {
            $("#potroseniDate").attr("onclick", "ASC");
            DohvatiPotroseno(0, brojZapisa, '&sort=kos.datum_vrijeme_kupnje DESC');
        }
        else
        {
            $("#potroseniDate").attr("onclick", "DESC");
            DohvatiPotroseno(0, brojZapisa, '&sort=kos.datum_vrijeme_kupnje ASC');
        }
    });
    
    
    $("#searchPotroseni").change(function () 
    {
        var pretrazi = $("#searchPotroseni").val();
        brojZapisa = parseInt($("#brStranicaPotroseni").find(":selected").val());
        
        if(pretrazi !== "")
        {
            DohvatiPotroseno(0, brojZapisa,("&trazi="+pretrazi));
        }
        else
        {
            DohvatiPotroseno(0, brojZapisa);
        }
    });
});