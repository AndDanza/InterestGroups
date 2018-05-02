/*pregled diskusija*/
function PregledDiskusija(kreni, stani, trazi = "")
{
    $("#tablicaLogDisk tbody").empty();
    var brojZapisa = parseInt($("#brStranicaDisk").find(":selected").val());
    var brojac = 0;
    
    $.ajax({
        url: './XML skripte/podrucjaXML.php?tip=diskusijaModeratora'+trazi,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("diskusija").each(function ()
            {
                brojac++;

                if (brojac >= kreni && brojac <= stani)
                {
                    var id = $(this).find("id_diskusije").text();
                    var naziv = $(this).find("naziv").text();
                    var podrucje = $(this).find("podrucje").text();
                    var pravila = $(this).find("pravila").text();
                    var datum = $(this).find("datumVrijeme").text();
                    var istek = $(this).find("zatvoreno").text();

                    var red = "<tr id=disk"+id+">";
                    red += "<td class='naziv"+id+"'>" + naziv + "</td>";
                    red += "<td class='pravila"+id+"'>" + pravila + "</td>";
                    red += "<td class='pod"+id+"'>" + podrucje + "</td>";
                    red += "<td class='datum"+id+"'>" + datum + "</td>";
                    red += "<td class='istek"+id+"'>" + istek + "</td>";
                    red += "<td style='width: 7%;'><a href='#naslovDisk' onclick='AzurirajDisk("+id+")'>Uredi</a></td>";
                    red += "<td style='width: 7%;'><a href='dodajDiskusiju.php?delete="+id+"' style='color: lightsalmon;'>Obriši</a></td>";
                    red += "</tr>";

                    $("#tablicaLogDisk tbody").append(red);
                }
            });
            
            if(trazi === "")
            {
                $("#pocetakDisk").attr("onclick", "PregledDiskusija(0, "+brojZapisa+")");
                $("#krajSakupljeni").attr("onclick", "PregledDiskusija("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaDisk").attr("onclick", "PregledDiskusija("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniDisk").attr("onclick", "PregledDiskusija("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakDisk").attr("onclick", "PregledDiskusija(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajDisk").attr("onclick", "PregledDiskusija("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaDisk").attr("onclick", "PregledDiskusija("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniDisk").attr("onclick", "PregledDiskusija("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranicaDisk").attr("value", parseInt(stani/brojZapisa));
    
}


function AzurirajDisk(id)
{
    $("#idDisk").attr("value", id);
    $("#nazivDisk").attr("value", $("#disk"+id).find(".naziv"+id).text());
    $("#pravilaDisk").html($("#disk"+id).find(".pravila"+id).text());
    var podrucje = $("#disk"+id).find(".pod"+id).text();
    $('#odabirPod option:contains("'+podrucje+'")').prop('selected', true);
    $("#trajanjeDisk").attr("value", $("#disk"+id).find(".istek"+id).text());
    
    $("#kontrole").empty();
    $("#kontrole").append('<input id="azurirajDisk" name="azurirajDisk" value="Ažuriraj" style="width: 20%;" type="submit">');
    $("#kontrole").append('<a href="dodajDiskusiju.php">Odustani</a>');
}

$(document).ready(function ()
{
    var brojZapisa = parseInt($("#brStranicaDisk").find(":selected").val());
    PregledDiskusija(0, brojZapisa);
    
    $("#brStranicaDisk").change(function ()
    {
        brojZapisa = parseInt($("#brStranicaDisk").find(":selected").val());
        PregledDiskusija(0, brojZapisa);
    });
    
    $("#diskNaziv").click(function()
    {
        brojZapisa = parseInt($("#brStranicaDisk").find(":selected").val());
        var sort = $("#diskNaziv").attr("onclick");
        
        if(sort === "DESC")
        {
            $("#diskNaziv").attr("onclick", "ASC");
            PregledDiskusija(0, brojZapisa,'&sort=naziv_diskusije DESC');
        }
        else
        {
            $("#diskNaziv").attr("onclick", "DESC");
            PregledDiskusija(0, brojZapisa,'&sort=naziv_diskusije ASC');
        }
    });
    
    $("#diskDate").click(function()
    {
        brojZapisa = parseInt($("#brStranicaDisk").find(":selected").val());
        var sort = $("#diskDate").attr("onclick");
        
        if(sort === "DESC")
        {
            $("#diskDate").attr("onclick", "ASC");
            PregledDiskusija(0, brojZapisa,'&sort=datum_vrijeme_otvaranja DESC');
        }
        else
        {
            $("#diskDate").attr("onclick", "DESC");
            PregledDiskusija(0, brojZapisa,'&sort=datum_vrijeme_otvaranja ASC');
        }
    });
    
    
    $("#searchDisk").change(function () 
    {
        var pretrazi = $("#searchDisk").val();
        brojZapisa = parseInt($("#brStranicaDisk").find(":selected").val());
        
        if(pretrazi !== "")
        {
            PregledDiskusija(0, brojZapisa,("&trazi="+pretrazi));
        }
        else
        {
            PregledDiskusija(0, brojZapisa);
        }
    });
});