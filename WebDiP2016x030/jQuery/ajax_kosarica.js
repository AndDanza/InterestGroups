/*pregled dostupnih kupona po području*/
function PregledKosarice(kreni, stani, trazi = "")
{
    $("#kosaricaKor").empty();
    var brojac = 0;
    var brojZapisa = 3;
    
    $.ajax({
        url: './XML skripte/statistikaXML.php?stat=kosarica',
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("kosarica").each(function ()
            {
                brojac++;

                if (brojac >= kreni && brojac <= stani)
                {
                    var id = $(this).find("id_kupon").text();
                    var naziv = $(this).find("naziv_kupon").text();
                    var bodovi = $(this).find("bodovi").text();
                    var izdan = $(this).find("izdan").text();

                    var red =  '<figure style="float: left; height: 350px;">';
                    red += '<img src="Slike/kupon.png" alt="kupon" style="height:150px; width:90%; margin-left: 5%;">';
                    red += '<figcaption>';
                    red += "<b>Naziv: </b><br>"+naziv+"<br>";
                    red += "<b>Broj bodova: </b>"+bodovi+"<br>";
                    red += "<b>Datum kreiranja: </b><br>"+izdan+"<br><br>";
                    red += '</figcaption>';
                    red += '<a class="kos" href="kosarica.php?ukloni='+id+'&naziv='+naziv+'"><b style="font-size: 18px;">Ukolni iz košarice</b></a>';
                    red += '</figure>';

                    $("#kosaricaKor").append(red);
                }
            });
            
            if(trazi === "")
            {
                $("#pocetakKosarica").attr("onclick", "PregledKosarice(0, "+brojZapisa+")");
                $("#krajKosarica").attr("onclick", "PregledKosarice("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaKosarica").attr("onclick", "PregledKosarice("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniKosarica").attr("onclick", "PregledKosarice("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakKosarica").attr("onclick", "PregledKosarice(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajKosarica").attr("onclick", "PregledKosarice("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaKosarica").attr("onclick", "PregledKosarice("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniKosarica").attr("onclick", "PregledKosarice("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
            
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranicaKosarica").attr("value", parseInt(stani/brojZapisa));
    
}

$(document).ready(function ()
{
    var brojZapisa = 3;
    PregledKosarice(0, brojZapisa);
});


