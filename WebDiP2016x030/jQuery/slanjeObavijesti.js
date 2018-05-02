$(document).ready(function()
{
    $("#obavijestTip").click(function()
    {
        var odabranaObavijest = $("#obavijestTip").find(":selected").val();
        odabranaObavijest = parseInt(odabranaObavijest);
        $("#formObavijesti").empty();
        
        switch(odabranaObavijest)
        {
            case 1:
                PojedinacnaPoruka();
                break;
            case 2:
                PorukaDiskusiji();
                break;
        }  
    });
});

function DohvatiDiskusije()
{
    var odabir = "";
    odabir += "<select class='odabirObav' id='obavijestDisk' name='obavijestDisk'></select>";
    $("#formObavijesti").append(odabir);
    
    $.ajax({
        url: './XML skripte/podrucjaXML.php?tip=diskusijaModeratora',
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("diskusija").each(function ()
            {
                var id = $(this).find("id_diskusije").text();
                var diskusija = $(this).find("naziv").text();
                
                odabir = "<option value='"+id+"'>"+diskusija+"</option>";
                $("#obavijestDisk").append(odabir);
            });
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
}

function DohvatiKorisnike()
{
    var odabir = "";
    odabir += "<select class='odabirObav' id='odabirKor' name='odabirKor'></select>";
    $("#formObavijesti").append(odabir);
    
    $.ajax({
        url: './XML skripte/dohvatiXML.php?podaci=obavijestZaModeratora',
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("korisnik").each(function ()
            {
                var id = $(this).find("id").text();
                var korisnik = $(this).find("korisnicko_ime").text();
                
                odabir = "<option value='"+id+"'>"+korisnik+"</option>";
                $("#odabirKor").append(odabir);
            });
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
}

function PojedinacnaPoruka()
{
    var formaUnos = "";
    
    formaUnos = "<label for='odabirKor'>Odaberite korisnika: </label>";
    $("#formObavijesti").append(formaUnos);
    DohvatiKorisnike();
    formaUnos = "<br><br>";
    formaUnos += "<label for='obavijestDisk'>Odaberite diskusiju: </label>";
    $("#formObavijesti").append(formaUnos);
    DohvatiDiskusije();
    formaUnos = "<br><br>";
    formaUnos += "<label for='tekstObavijest'>Obavijest: </label>";
    formaUnos += "<textarea id='tekstObavijest' name='tekstObavijest' style='margin: 0;'></textarea>";
    formaUnos += "<br>";
    formaUnos += "<input type='submit' name='posaljiPojedinacno' value='Pošalji' style='width: 20%; margin: 2% 0% 2% 32%;'>";
    $("#formObavijesti").append(formaUnos);
}


function PorukaDiskusiji()
{
    var formaUnos = "";
    
    formaUnos = "<label for='obavijestDisk'>Odaberite diskusiju: </label>";
    DohvatiDiskusije();
    formaUnos += "<br><br>";
    formaUnos += "<label for='tekstObavijest'>Obavijest: </label>";
    formaUnos += "<textarea id='tekstObavijest' name='tekstObavijest' style='margin: 0;'></textarea>";
    formaUnos += "<br>";
    formaUnos += "<input type='submit' name='posaljiDiskusiji' value='Pošalji' style='width: 20%; margin: 2% 0% 2% 32%;'>";
    
    $("#formObavijesti").append(formaUnos);
}
