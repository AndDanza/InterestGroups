/*LOG PRIJAVA*/
function DohvatiLogPrijava(kreni, stani, trazi = "")
{
    $("#tablicaLogPrijava tbody").empty();
    var brojZapisa = parseInt($("#brStranicaPrijava").find(":selected").val());
    var brojac = 0;
    
    
    $.ajax({
        url: './XML skripte/dnevnikXML.php?log=prijava'+trazi,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("log").each(function ()
            {
                brojac++;

                if (brojac >= kreni && brojac <= stani)
                {
                    var ime = $(this).find("ime").text();
                    var prezime = $(this).find("prezime").text();
                    var korIme = $(this).find("korisnicko_ime").text();
                    var prijava = $(this).find("prijava").text();
                    var odjava = $(this).find("odjava").text();

                    var red = "<tr>";
                    red += ("<td>" + ime + "</td>");
                    red += ("<td>" + prezime + "</td>");
                    red += ("<td>" + korIme + "</td>");
                    red += ("<td>" + prijava + "</td>");
                    red += ("<td>" + odjava + "</td>");
                    red += "</tr>";

                    $("#tablicaLogPrijava tbody").append(red);
                }
            });
            
            if(trazi === "")
            {
                $("#pocetakPrijava").attr("onclick", "DohvatiLogPrijava(0, "+brojZapisa+")");
                $("#krajPrijava").attr("onclick", "DohvatiLogPrijava("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaPrij").attr("onclick", "DohvatiLogPrijava("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniPrij").attr("onclick", "DohvatiLogPrijava("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakPrijava").attr("onclick", "DohvatiLogPrijava(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajPrijava").attr("onclick", "DohvatiLogPrijava("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaPrij").attr("onclick", "DohvatiLogPrijava("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniPrij").attr("onclick", "DohvatiLogPrijava("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranica").attr("value", parseInt(stani/brojZapisa));
    
}

$(document).ready(function ()
{
    var brojZapisa = parseInt($("#brStranicaPrijava").find(":selected").val());
    DohvatiLogPrijava(0, brojZapisa);

    $("#brStranicaPrijava").change(function ()
    {
        brojZapisa = parseInt($("#brStranicaPrijava").find(":selected").val());
        DohvatiLogPrijava(0, brojZapisa);
    });
    
    $("#prijavaKorIme").click(function()
    {
        brojZapisa = parseInt($("#brStranicaPrijava").find(":selected").val());
        
        var sort = $("#prijavaKorIme").attr("onclick");
        
        if(sort === "DESC")
        {
            $("#prijavaKorIme").attr("onclick", "ASC");
            DohvatiLogPrijava(0, brojZapisa,'&sort=kor.korisnicko_ime DESC');
        }
        else
        {
            $("#prijavaKorIme").attr("onclick", "DESC");
            DohvatiLogPrijava(0, brojZapisa,'&sort=kor.korisnicko_ime ASC');
        }
    });
    
    $("#prijavaDate").click(function()
    {
        brojZapisa = parseInt($("#brStranicaPrijava").find(":selected").val());
        var sort = $("#prijavaDate").attr("onclick");
        
        if(sort === "DESC")
        {
            $("#prijavaDate").attr("onclick", "ASC");
            DohvatiLogPrijava(0, brojZapisa,'&sort=log.datum_vrijeme_akcije DESC');
        }
        else
        {
            $("#prijavaDate").attr("onclick", "DESC");
            DohvatiLogPrijava(0, brojZapisa,'&sort=log.datum_vrijeme_akcije ASC');
        }
    });
    
    
    $("#search").change(function () 
    {
        var pretrazi = $("#search").val();
        brojZapisa = parseInt($("#brStranicaPrijava").find(":selected").val());
        
        if(pretrazi !== "")
        {
            DohvatiLogPrijava(0, brojZapisa,("&trazi="+pretrazi));
        }
        else
        {
            DohvatiLogPrijava(0, brojZapisa);
        }
    });
});


/*LOG BAZA*/
function DohvatiLogBaze(kreni, stani, trazi = "")
{
    $("#tablicaLogBaza tbody").empty();
    var brojZapisa = parseInt($("#brStranicaBaza").find(":selected").val());
    var brojac = 0;
    
    $.ajax({
        url: './XML skripte/dnevnikXML.php?log=baza'+trazi,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("log").each(function ()
            {
                brojac++;

                if (brojac >= kreni && brojac <= stani)
                {
                    var ime = $(this).find("ime").text();
                    var prezime = $(this).find("prezime").text();
                    var korIme = $(this).find("korisnicko_ime").text();
                    var datumVrijeme = $(this).find("datum_vrijeme").text();
                    var vrstaUpita = $(this).find("vrsta_upita").text();
                    var tablica = $(this).find("tablica").text();
                    
                    var red = "<tr>";
                    red += ("<td>" + ime + "</td>");
                    red += ("<td>" + prezime + "</td>");
                    red += ("<td>" + korIme + "</td>");
                    red += ("<td>" + datumVrijeme + "</td>");
                    red += ("<td>" + vrstaUpita + "</td>");
                    red += ("<td>" + tablica + "</td>");
                    red += "</tr>";

                    $("#tablicaLogBaza tbody").append(red);
                }
            });
            
            if(trazi === "")
            {
                $("#pocetakBaze").attr("onclick", "DohvatiLogBaze(0, "+brojZapisa+")");
                $("#krajBaze").attr("onclick", "DohvatiLogBaze("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaBaze").attr("onclick", "DohvatiLogBaze("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniBaze").attr("onclick", "DohvatiLogBaze("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakBaze").attr("onclick", "DohvatiLogBaze(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajBaze").attr("onclick", "DohvatiLogBaze("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaBaze").attr("onclick", "DohvatiLogBaze("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniBaze").attr("onclick", "DohvatiLogBaze("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranicaBaze").attr("value", parseInt(stani/brojZapisa));
    
}


$(document).ready(function ()
{
    var brojZapisa = parseInt($("#brStranicaBaza").find(":selected").val());
    DohvatiLogBaze(0, brojZapisa);

    $("#brStranicaBaza").change(function ()
    {
        brojZapisa = parseInt($("#brStranicaBaza").find(":selected").val());
        DohvatiLogBaze(0, brojZapisa);
    });
    
    $("#bazaKorIme").click(function()
    {
        brojZapisa = parseInt($("#brStranicaBaza").find(":selected").val());
        var sort = $("#bazaKorIme").attr("onclick");
        
        if(sort === "DESC")
        {
            $("#bazaKorIme").attr("onclick", "ASC");
            DohvatiLogBaze(0, brojZapisa,'&sort=kor.korisnicko_ime DESC');
        }
        else
        {
            $("#bazaKorIme").attr("onclick", "DESC");
            DohvatiLogBaze(0, brojZapisa,'&sort=kor.korisnicko_ime ASC');
        }
    });
    
    $("#bazaDate").click(function()
    {
        brojZapisa = parseInt($("#brStranicaBaza").find(":selected").val());
        var sort = $("#bazaDate").attr("onclick");
        
        if(sort === "DESC")
        {
            $("#bazaDate").attr("onclick", "ASC");
            DohvatiLogBaze(0, brojZapisa,'&sort=log.datum_vrijeme_akcije DESC');
        }
        else
        {
            $("#bazaDate").attr("onclick", "DESC");
            DohvatiLogBaze(0, brojZapisa,'&sort=log.datum_vrijeme_akcije ASC');
        }
    });
    
    $("#searchBaza").change(function () 
    {
        var pretrazi = $("#searchBaza").val();
        brojZapisa = parseInt($("#brStranicaBaza").find(":selected").val());
        
        if(pretrazi !== "")
        {
            DohvatiLogBaze(0, brojZapisa, ("&trazi="+pretrazi));
        }
        else
        {
            DohvatiLogBaze(0, brojZapisa);
        }
    });
});



/*LOG OSTALO*/
function DohvatiLogOstalo(kreni, stani, trazi = "")
{
    $("#tablicaLogOstalo tbody").empty();
    var brojZapisa = parseInt($("#brStranicaOstalo").find(":selected").val());
    var brojac = 0;
    
    $.ajax({
        url: './XML skripte/dnevnikXML.php?log=ostalo'+trazi,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {            
            $(xml).find("log").each(function ()
            {
                brojac++;

                if (brojac >= kreni && brojac <= stani)
                {
                    var ime = $(this).find("ime").text();
                    var prezime = $(this).find("prezime").text();
                    var korIme = $(this).find("korisnicko_ime").text();
                    var akcija = $(this).find("akcija").text();
                    var radnja = $(this).find("radnja").text();

                    var red = "<tr>";
                    red += ("<td>" + ime + "</td>");
                    red += ("<td>" + prezime + "</td>");
                    red += ("<td>" + korIme + "</td>");
                    red += ("<td>" + akcija + "</td>");
                    red += ("<td>" + radnja + "</td>");
                    red += "</tr>";

                    $("#tablicaLogOstalo tbody").append(red);
                }
            });
            
            if(trazi === "")
            {
                $("#pocetakOstalo").attr("onclick", "DohvatiLogOstalo(0, "+brojZapisa+")");
                $("#krajOstalo").attr("onclick", "DohvatiLogOstalo("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaOstalo").attr("onclick", "DohvatiLogOstalo("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniOstalo").attr("onclick", "DohvatiLogOstalo("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakOstalo").attr("onclick", "DohvatiLogOstalo(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajOstalo").attr("onclick", "DohvatiLogOstalo("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaOstalo").attr("onclick", "DohvatiLogOstalo("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniOstalo").attr("onclick", "DohvatiLogOstalo("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranicaOstalo").attr("value", parseInt(stani/brojZapisa));
    
}


$(document).ready(function ()
{
    var brojZapisa = parseInt($("#brStranicaOstalo").find(":selected").val());
    DohvatiLogOstalo(0, brojZapisa);

    $("#brStranicaOstalo").change(function ()
    {
        brojZapisa = parseInt($("#brStranicaOstalo").find(":selected").val());
        DohvatiLogOstalo(0, brojZapisa);
    });
    
    
    $("#searchOstalo").change(function () 
    {
        var pretrazi = $("#searchOstalo").val();
        brojZapisa = parseInt($("#brStranicaOstalo").find(":selected").val());
        
        if(pretrazi !== "")
        {
            DohvatiLogOstalo(0, brojZapisa, ("&trazi="+pretrazi));
        }
        else
        {
            DohvatiLogOstalo(0, brojZapisa);
        }
    });
    
    $("#ostaloKorIme").click(function()
    {
        brojZapisa = parseInt($("#brStranicaOstalo").find(":selected").val());
        var sort = $("#ostaloKorIme").attr("onclick");
                
        if(sort === "DESC")
        {
            $("#ostaloKorIme").attr("onclick", "ASC");
            DohvatiLogOstalo(0, brojZapisa,'&sort=kor.korisnicko_ime DESC');
        }
        else
        {
            $("#ostaloKorIme").attr("onclick", "DESC");
            DohvatiLogOstalo(0, brojZapisa,'&sort=kor.korisnicko_ime ASC');
        }
    });
    
    $("#ostaloDate").click(function()
    {
        brojZapisa = parseInt($("#brStranicaOstalo").find(":selected").val());
        var sort = $("#ostaloDate").attr("onclick");
                
        if(sort === "DESC")
        {
            $("#ostaloDate").attr("onclick", "ASC");
            DohvatiLogOstalo(0, brojZapisa,'&sort=log.datum_vrijeme_akcije DESC');
        }
        else
        {
            $("#ostaloDate").attr("onclick", "DESC");
            DohvatiLogOstalo(0, brojZapisa,'&sort=log.datum_vrijeme_akcije ASC');
        }
    });
});
