/*pregled kupona*/
function PregledKupona(kreni, stani, trazi = "")
{
    $("#tablicaLogKupon tbody").empty();
    var brojZapisa = parseInt($("#brStranicaKupon").find(":selected").val());
    var brojac = 0;
    
    $.ajax({
        url: './XML skripte/dohvatiXML.php?podaci=kuponi'+trazi,
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
                    var naziv = $(this).find("naziv").text();
                    var datum = $(this).find("kreiranje").text();

                    var red = "<tr id=kupon"+id+">";
                    red += "<td class='naziv"+id+"'>" + naziv + "</td>";
                    red += "<td class='datum"+id+"'>" + datum + "</td>";
                    red += "<td style='width: 7%;'><a href='#naslovDisk' onclick='AzurirajKupon("+id+")'>Uredi</a></td>";
                    red += "<td style='width: 7%;'><a href='kreirajKupon.php?delete="+id+"' style='color: lightsalmon;'>Obriši</a></td>";
                    red += "</tr>";

                    $("#tablicaLogKupon tbody").append(red);
                }
            });
            
            if(trazi === "")
            {
                $("#pocetakKupon").attr("onclick", "PregledKupona(0, "+brojZapisa+")");
                $("#krajKupon").attr("onclick", "PregledKupona("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaKupon").attr("onclick", "PregledKupona("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniKupon").attr("onclick", "PregledKupona("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakKupon").attr("onclick", "PregledKupona(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajKupon").attr("onclick", "PregledKupona("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaKupon").attr("onclick", "PregledDiskusija("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniKupon").attr("onclick", "PregledDiskusija("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranicaKupon").attr("value", parseInt(stani/brojZapisa));
    
}


function AzurirajKupon(id)
{
    $("#idKupon").attr("value", id);
    $("#nazivKupon").attr("value", $("#kupon"+id).find(".naziv"+id).text());
    
    $("#kontrole").empty();
    $("#kontrole").append('<input id="azurirajKupon" name="azurirajKupon" value="Ažuriraj" style="width: 20%;" type="submit">');
    $("#kontrole").append('<a href="kreirajKupon.php">Odustani</a>');
}

$(document).ready(function ()
{
    var brojZapisa = parseInt($("#brStranicaKupon").find(":selected").val());
    PregledKupona(0, brojZapisa);
    
    $("#brStranicaKupon").change(function ()
    {
        brojZapisa = parseInt($("#brStranicaKupon").find(":selected").val());
        PregledKupona(0, brojZapisa);
    });
    
    
    $("#searchKupon").change(function () 
    {
        var pretrazi = $("#searchKupon").val();
        brojZapisa = parseInt($("#brStranicaKupon").find(":selected").val());
        
        if(pretrazi !== "")
        {
            PregledKupona(0, brojZapisa,("&trazi="+pretrazi));
        }
        else
        {
            PregledKupona(0, brojZapisa);
        }
    });
});