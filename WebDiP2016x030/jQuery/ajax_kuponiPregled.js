/*pregled dostupnih kupona po području*/
function DostupniKuponi(kreni, stani, trazi = "")
{
    $("#dostupniKuponiKor").empty();
    var pod = parseInt($("#odabirPodrucja").find(":selected").val());
    var brojac = 0;
    var brojZapisa = 3;
    
    $.ajax({
        url: './XML skripte/statistikaXML.php?stat=kuponi&pod='+pod,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("kupon").each(function ()
            {
                brojac++;

                if (brojac >= kreni && brojac <= stani)
                {
                    var id = $(this).find("id_kupon").text();
                    var naziv = $(this).find("naziv_kupon").text();
                    var bodovi = $(this).find("bodovi").text();

                    var red = '<figure style="float: left; height: 300px;">';
                    red += '<img src="Slike/kupon.png" alt="kupon" style="height:150px; width:90%; margin-left: 5%;">';
                    red += '<figcaption>';
                    red += "<b>Naziv: </b><br><a href='kuponi.php?pregledaj=$id_kupona'>"+naziv+"</a><br>";
                    red += "<b>Potrebno bodova: </b>"+bodovi+"<br>";
                    red += '</figcaption>';
                    red += '<a class="kos" href="kuponi.php?idKupon='+id+'&nazivKupon='+naziv+'"><b style="font-size: 18px;">Dodaj u košaricu</b></a>';
                    red += '</figure>';

                    $("#dostupniKuponiKor").append(red);
                }
            });
            
            if(trazi === "")
            {
                $("#pocetakKuponKor").attr("onclick", "DostupniKuponi(0, "+brojZapisa+")");
                $("#krajKuponKor").attr("onclick", "DostupniKuponi("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaKuponKor").attr("onclick", "DostupniKuponi("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniKuponKor").attr("onclick", "DostupniKuponi("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakKuponKor").attr("onclick", "DostupniKuponi(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajKuponKor").attr("onclick", "DostupniKuponi("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaKuponKor").attr("onclick", "DostupniKuponi("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniKuponKor").attr("onclick", "DostupniKuponi("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
            
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranicaKuponKor").attr("value", parseInt(stani/brojZapisa));
    
}

/*pregled kupljenih kupona korisnika*/
function KupljeniKuponi(kreni, stani, trazi = "")
{
    $("#kupljeniKuponiKor").empty();
    var brojac = 0;
    var brojZapisa = 3;
    
    $.ajax({
        url: './XML skripte/statistikaXML.php?stat=kupljeniKuponi',
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("kupon").each(function ()
            {
                brojac++;

                if (brojac >= kreni && brojac <= stani)
                {
                    var id = $(this).find("id_kupon").text();
                    var naziv = $(this).find("naziv_kupon").text();
                    var izdan = $(this).find("izdavanje").text();
                    var istice = $(this).find("istice").text();
                    var kod = $(this).find("kod").text();
                    

                    var red = '<figure style="float: left; height: 380px; width: 30%;">';
                    red += '<img src="Slike/kupon.png" alt="kupon" style="height:150px; width:90%; margin-left: 5%;">';
                    red += '<figcaption>';
                    red += "<b>Naziv: </b><br>"+naziv+"<br>";
                    red += "<b>Datum kreiranja: </b><br>"+izdan+"<br>";
                    red += "<b>Datum istjecanja: </b><br>"+istice+"<br>";
                    red += "<b>Kod kupona: </b><br>"+kod+"<br>";
                    red += '</figcaption>';
                    red += '</figure>';

                    $("#kupljeniKuponiKor").append(red);
                }
            });
            
            if(trazi === "")
            {
                $("#pocetakKuponKupljeni").attr("onclick", "KupljeniKuponi(0, "+brojZapisa+")");
                $("#krajKuponKupljeni").attr("onclick", "KupljeniKuponi("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaKuponKupljeni").attr("onclick", "KupljeniKuponi("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniKuponKupljeni").attr("onclick", "KupljeniKuponi("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakKuponKupljeni").attr("onclick", "KupljeniKuponi(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajKuponKupljeni").attr("onclick", "KupljeniKuponi("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaKuponKupljeni").attr("onclick", "KupljeniKuponi("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniKuponKupljeni").attr("onclick", "KupljeniKuponi("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
            
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranicaKuponKuponKupljeni").attr("value", parseInt(stani/brojZapisa));
    
}

$(document).ready(function ()
{
    var brojZapisa = 3;
    DostupniKuponi(0, brojZapisa);
    
    $("#odabirPodrucja").change(function ()
    {
        DostupniKuponi(0, brojZapisa);
    });
    
    KupljeniKuponi(0, brojZapisa-1);
});