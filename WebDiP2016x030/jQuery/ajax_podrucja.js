/*ISPIS PODRUČJA NEREGISTRIRANOG KORISNIKA*/
function DohvatiPodrucja()
{
    $.ajax({
        url: './XML skripte/podrucjaXML.php?tip=podrucja',
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("podrucje").each(function ()
            {
                var id = $(this).find("id_podrucja").text();
                var naziv = $(this).find("naziv_podrucja").text();

                var red = "<div class='divPodrucja'>";
                                                                                                 //id = id_podrucja, dis+id=id diva u koji idu diskusije, pod+id = id tipke
                red += ("<input id='pod"+id+"' type='button' class='tipkaPodrucja' value='"+naziv+"' onclick='DohvatiDiskusije("+id+",\"dis"+id+"\",\"pod"+id+"\")'>");
                red += ("<div class='divDiskusija' id='dis"+id+"'></div>");
                red += "</div>";
                
                $("#prikazPodrucja").append(red);

            });
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
}

//na prvi klik ispisuju se diskusije i postavlja se funkcija ičisti sve
//na drugi klik čisti se div sa diskusijama i vraća se funkcija
function Ocisti(id, div, idInput)
{
    //id = id podrucja, div = div u koji se spremaju diskusije, idInput je tipka podrucja
    $("#"+div).empty();
    $("#"+idInput).attr("onclick","DohvatiDiskusije("+id+",\""+div+"\",\""+idInput+"\")");
}


function DohvatiDiskusije(id, div, idInput)
{
    $("#"+idInput).attr("onclick","Ocisti("+id+",\""+div+"\",\""+idInput+"\")");
    
    var red = "<table>";
    red += "<colgroup><col style='width: 22%;'></colgroup>";
    red += "<colgroup><col style='width: 55%;'></colgroup><thead>";
    red += "<tr><th>Naziv diskusije</th>";
    red += "<th>Pravila ponašanja</th>";
    red += "<th>Datum i vrijeme početka</th></tr>";
    red += "</thead><tbody></tbody></table>";
    $("#"+div).append(red);
    
    $.ajax({
        url: './XML skripte/podrucjaXML.php?tip=diskusijeNereg&id='+id,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("diskusija").each(function ()
            {
                var naziv = $(this).find("naziv").text();
                var pravila = $(this).find("pravila").text();
                var vrijeme = $(this).find("otvorena").text();
                
                red = "<tr><td>"+naziv+"</td>";
                red += "<td>"+pravila+"</td>";
                red += "<td>"+vrijeme+"</td></tr>";
                
                $("#"+div+" table tbody").append(red);
            });
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
}


//ako je postavljena mogućnost profil u izborniku znači da je korisnik prijavljen
$(document).ready(function ()
{
    if($("#meniProfil").length > 0)
    {
        var odabranoPod = $("#odabirPodrucja").find(":selected").val();
        DohvatiDiskusijeRegistrirani(odabranoPod);
        
        $("#odabirPodrucja").change(function()
        {
            odabranoPod = $("#odabirPodrucja").find(":selected").val();
            DohvatiDiskusijeRegistrirani(odabranoPod);
        });
    }
    else
    {
        DohvatiPodrucja();
    }
});



/*ISPIS DISKUSIJA ZA DANO PODRUČJE - REGISTRIRANI KORISNIK*/
function DohvatiDiskusijeRegistrirani(odabranoPod)
{
    $("#prikazPodrucja div").empty();
    
    $.ajax({
        url: './XML skripte/podrucjaXML.php?tip=diskusija&id='+odabranoPod,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("diskusija").each(function ()
            {
                var id = $(this).find("id_diskusije").text();
                var naziv = $(this).find("naziv").text();
                var pravila = $(this).find("pravila").text();
                
                var red = "<div class='divPodrucja'>";
                                                                          //id = id diskusije, kom+id = kom1 npr. div u koji spremaju se komentari, disk+id = dis1 id tipke za prikaz komentara
                red += ("<input id='disk"+id+"' type='button'  style='width: 45%;' class='tipkaPodrucja' value='"+naziv+"' onclick='DohvatiKomentare("+id+",\"kom"+id+"\", \"disk"+id+"\", 0, 5)'>");
                red += ("<p><b  style='color: lightsalmon; text-align: left; font-size: 18px;'>"+pravila+"</p>");
                red += ("<div class='divDiskusija' id='kom"+id+"'></div>");
                red += "</div>";
                
                $("#prikazPodrucja").append(red);

            });
        },
        error: function ()
        {
            alert("Greška prilikom dohvaćanja diskusija.");
        }
    });
}


function DohvatiKomentare(id_diskusije, div, idInput, kreni, stani)
{
    //id_diskusije, div = div u koji pohranjujem komentare, idInput = tipka na kojoj se nalazi diskusije
    $("#"+idInput).attr("onclick","OcistiKomentare("+id_diskusije+", \""+div+"\", \""+idInput+"\")");
    
    $("#"+div).empty();
    
    var red = "<table><thead>";
    red += "<tr style='width: 15%;'><th>Korisnik</th>";
    red += "<th style='padding: 1%;'>Komentar</th>";
    red += "<th style='width: 7%;'>Datum i vrijeme objave</th>";
    red += "<th style='width: 7%;'></th></tr>";
    red += "<th></th></tr>";
    red += "</thead><tbody></tbody></table>";
    $("#"+div).append(red);
    
    red = "<input class='dodajKom' type='button' onclick='UnosKomentara(\""+div+"\","+id_diskusije+")' value='Dodaj komentar'>";
    red += '<div style="float: right; margin: 2% 1% 2%;" id="paginacija'+div+'">';
    red += '<a id="kraj'+div+'" style="float: right; margin: 3% 2% 0% 1%">Kraj</a>';
    red += '<a id="sljedeca'+div+'" style="float: right; margin: 3% 2% 0% 1%">Sljedeća</a>';
    red += '<input id="tren'+div+'" style="width: 10%; float: right; margin: 1.5% 2% 0% 1%;" disabled type="text">';
    red += '<a id="prethodni'+div+'" style="float: right; margin: 3% 2% 0% 1%">Prethodna</a>';
    red += '<a id="pocetak'+div+'" style="float: right; margin: 3% 2% 0% 1%">Početak</a>';
    red += '</div>'; 
    $("#"+div).append(red);
    
    var brojac = 0;
    
    $.ajax({
        url: './XML skripte/podrucjaXML.php?tip=komentar&id='+id_diskusije,
        type: 'GET',
        dataType: 'xml',

        success: function (xml)
        {
            $(xml).find("komentar").each(function ()
            {
                brojac++;
                
                if (brojac >= kreni && brojac <= stani)
                {
                    var user = $(this).find("user").text();
                    var vrijeme = $(this).find("datumVrijeme").text();
                    var tekst = $(this).find("tekst").text();
                    var diskusija = $(this).find("diskusija").text();

                    var izraz = user+diskusija+vrijeme;
                    izraz = izraz.replace(":", "");
                    izraz = izraz.replace(" ", "");
                    izraz = izraz.replace("-", "");
                    izraz = izraz.replace("-", "");
                    izraz = izraz.replace(":", "");
                
                    red = "<tr id='"+izraz+"'><td style='width: 15%;'>"+user+"</td>";
                    red += "<td class='komTekst' style='padding: 1%;'>"+tekst+"</td>";
                    red += "<td style='width: 15%; padding: 1%;'>"+vrijeme+"</td>";
                    red += "<td style='width: 7%;'><a onclick='AzurirajKomentar(\""+user+"\","+diskusija+",\""+vrijeme+"\",\""+div+"\")'>Uredi</a></td>";
                    red += "<td style='width: 7%;'><a style='color: lightsalmon;' href='podrucjaZaKorisnika.php?delete="+user+";"+diskusija+";"+vrijeme+"'>Obriši</a></td></tr>";
                    
                    $("#"+div+" table tbody").append(red);
                }
            });
            
            $("#pocetak"+div).attr("onclick", "DohvatiKomentare("+id_diskusije+", \""+div+"\", \""+idInput+"\",0, 5)");
            $("#kraj"+div).attr("onclick", "DohvatiKomentare("+id_diskusije+", \""+div+"\", \""+idInput+"\","+(brojac-5)+", "+brojac+")");
                
            $("#sljedeca"+div).attr("onclick", "DohvatiKomentare("+id_diskusije+", \""+div+"\", \""+idInput+"\","+(stani+1)+", "+(stani+5)+")");
            $("#prethodni"+div).attr("onclick", "DohvatiKomentare("+id_diskusije+", \""+div+"\", \""+idInput+"\","+(kreni-5-1)+", "+(kreni-1)+")");
        },
        error: function ()
        {
            alert("Greška prilikom pristupanja serveru!");
        }
    });
   
    $("#tren"+div).attr("value", parseInt(stani/5));
}

function UnosKomentara(div, diskusija)
{
    $(".dodajKom").hide();
    
    var red = "<tr><td colspan='3'><form id='komentiraj' method='POST' name='komentiraj' action=\"podrucjaZaKorisnika.php?disk="+diskusija+"\" novalidate>";
    red += "<textarea id='komentar' type='text' name='komentar'></textarea>";
    red += "<input id='posaljiKomentar' type='submit' name='posaljiKomentar' value='Komentiraj'>";
    red += "</form></td></tr>";
                
   $("#"+div+" table tbody").append(red);
}

function AzurirajKomentar(user, diskusija, vrijeme, div)
{
    $(".dodajKom").hide();
    
    var izraz = user+diskusija+vrijeme;
    izraz = izraz.replace(":", "");
    izraz = izraz.replace(" ", "");
    izraz = izraz.replace("-", "");
    izraz = izraz.replace("-", "");
    izraz = izraz.replace(":", "");
    $("#"+izraz).hide();
    
    var tekst = $("#"+izraz).find(".komTekst").text();
    
    var red = "<tr><td colspan='3'>";
    red += "<form id='azurirajKom' method='POST' name='azurirajKom' action=\"podrucjaZaKorisnika.php?disk="+diskusija+"&vrijeme="+vrijeme+"\" novalidate>";
    red += "<input name='tkoPrepravlja' value='"+user+"' hidden>";
    red += "<textarea id='azuriraniKom' type='text' name='azuriraniKom'>"+tekst+"</textarea>";
    red += "<input id='azurirajKomentar' type='submit' name='azurirajKomentar' value='Uredi'>";
    red += "</form></td></tr>";
    
    $("#"+div+" table tbody").append(red);
}

function OcistiKomentare(id, div, idInput)
{
    $("#"+div).empty();
    $("#"+idInput).attr("onclick","DohvatiKomentare("+id+",\"kom"+id+"\", \"disk"+id+"\")");
}
