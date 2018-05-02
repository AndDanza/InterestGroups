/*pregled diskusija*/
function PregledSvihPodrucja(kreni, stani, trazi = "")
{
    $("#tablicaLogPodrucje tbody").empty();
    var brojZapisa = parseInt($("#brStranicaPodrucje").find(":selected").val());
    var brojac = 0;
    
    $.ajax({
        url: './XML skripte/podrucjaXML.php?tip=pregledPodrucja'+trazi,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("podrucje").each(function ()
            {
                brojac++;

                if (brojac >= kreni && brojac <= stani)
                {
                    var ime = $(this).find("ime_korisnika").text();
                    var prezime = $(this).find("prezime_korisnika").text();
                    var korIme = $(this).find("korisnicko_ime").text();
                    var id_podrucje = $(this).find("id_podrucja").text();
                    var naziv = $(this).find("naziv_podrucja").text();

                    var red = "<tr id=podrucje"+id_podrucje+">";
                    red += "<td class='ime"+id_podrucje+"'>" + ime + "</td>";
                    red += "<td class='prezime"+id_podrucje+"'>" + prezime + "</td>";
                    red += "<td class='korIme"+id_podrucje+"'>" + korIme + "</td>";
                    red += "<td class='naziv"+id_podrucje+"'>" + naziv + "</td>";
                    red += "<td style='width: 7%;'>";
                    red += "<a href='#naslovPodrucje' onclick='AzurirajPodrucje("+id_podrucje+")'>Uredi</a></td>";
                    red += "<td style='width: 7%;'>";
                    red += "<a href='kreirajPodrucje.php?delete="+id_podrucje+"' style='color: lightsalmon;'>Obriši</a></td>";
                    red += "</tr>";

                    $("#tablicaLogPodrucje tbody").append(red);
                }
            });
            
            if(trazi === "")
            {
                $("#pocetakPodrucje").attr("onclick", "PregledSvihPodrucja(0, "+brojZapisa+")");
                $("#krajPodrucje").attr("onclick", "PregledSvihPodrucja("+(brojac-brojZapisa)+", "+brojac+")");
                
                $("#sljedecaPodrucje").attr("onclick", "PregledSvihPodrucja("+(stani+1)+", "+(stani+brojZapisa)+")");
                $("#prethodniPodrucje").attr("onclick", "PregledSvihPodrucja("+(kreni-brojZapisa-1)+", "+(kreni-1)+")");
            }
            else
            {
                $("#pocetakPodrucje").attr("onclick", "PregledSvihPodrucja(0, "+brojZapisa+",\""+trazi+"\")");
                $("#krajPodrucje").attr("onclick", "PregledSvihPodrucja("+(brojac-brojZapisa)+", "+brojac+",\""+trazi+"\")");
                
                $("#sljedecaPodrucje").attr("onclick", "PregledSvihPodrucja("+(stani+1)+", "+(stani+brojZapisa)+",\""+trazi+"\")");
                $("#prethodniPodrucje").attr("onclick", "PregledSvihPodrucja("+(kreni-brojZapisa-1)+", "+(kreni-1)+",\""+trazi+"\")");
            }
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
    
    $("#trenStranicaPodrucje").attr("value", parseInt(stani/brojZapisa));
    
}


function AzurirajPodrucje(id)
{
    $("#idPodrucje").attr("value", id);
    $("#nazivPod").attr("value", $("#podrucje"+id).find(".naziv"+id).text());
    var moderator = $("#podrucje"+id).find(".korIme"+id).text();
    $('#odabirMod option:contains("'+moderator+'")').prop('selected', true);
    
    $("#kontrole").empty();
    $("#kontrole").append('<input id="azurirajPodrucje" name="azurirajPodrucje" value="Ažuriraj" style="width: 20%;" type="submit">');
    $("#kontrole").append('<a href="kreirajPodrucje.php">Odustani</a>');
}

$(document).ready(function ()
{
    var brojZapisa = parseInt($("#brStranicaPodrucje").find(":selected").val());
    PregledSvihPodrucja(0, brojZapisa);
    
    $("#brStranicaPodrucje").change(function ()
    {
        brojZapisa = parseInt($("#brStranicaPodrucje").find(":selected").val());
        PregledSvihPodrucja(0, brojZapisa);
    });
    
    $("#podrucjeNaziv").click(function()
    {
        brojZapisa = parseInt($("#brStranicaPodrucje").find(":selected").val());
        var sort = $("#podrucjeNaziv").attr("onclick");
        
        if(sort === "DESC")
        {
            $("#podrucjeNaziv").attr("onclick", "ASC");
            PregledSvihPodrucja(0, brojZapisa,'&sort=pod.naziv_podrucja DESC');
        }
        else
        {
            $("#podrucjeNaziv").attr("onclick", "DESC");
            PregledSvihPodrucja(0, brojZapisa,'&sort=pod.naziv_podrucja ASC');
        }
    });
    
    $("#podrucjeModerator").click(function()
    {
        brojZapisa = parseInt($("#brStranicaPodrucje").find(":selected").val());
        var sort = $("#podrucjeModerator").attr("onclick");
        
        if(sort === "DESC")
        {
            $("#podrucjeModerator").attr("onclick", "ASC");
            PregledSvihPodrucja(0, brojZapisa,'&sort=kor.korisnicko_ime DESC');
        }
        else
        {
            $("#podrucjeModerator").attr("onclick", "DESC");
            PregledSvihPodrucja(0, brojZapisa,'&sort=kor.korisnicko_ime ASC');
        }
    });
    
    
    $("#searchPodrucje").change(function () 
    {
        var pretrazi = $("#searchPodrucje").val();
        brojZapisa = parseInt($("#brStranicaPodrucje").find(":selected").val());
        
        if(pretrazi !== "")
        {
            PregledSvihPodrucja(0, brojZapisa,("&trazi="+pretrazi));
        }
        else
        {
            PregledSvihPodrucja(0, brojZapisa);
        }
    });
});