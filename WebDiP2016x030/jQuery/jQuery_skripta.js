//AJAX provjera korisničkog imena
$(document).ready(function ()
{
    $("#korIme").focusout(function ()
    {       
        $.ajax({
            url: './XML skripte/dohvatiXML.php?podaci=svi_korisnici',
            type: 'GET',
            dataType: 'xml',

            success: function (xml) 
            {
                $(xml).find('korisnicko_ime').each(function ()
                {
                    if($("#korIme").val() === $(this).text())
                    {
                        alert("Korisničko ime već postoji");
                        $("#korIme").addClass("kriviUnos");
                        return false;
                    }
                    else
                    {
                        $("#korIme").removeClass("kriviUnos");
                        return true;
                    }
                });
            },
            error: function()
            {
                alert("Greška prilikom pristupanja serveru!");
            }
        });        
    });
});

//nedozvoljeni znakovi u unosima
function ProvjeraNedozvoljenihZnakova(ulazniTekst)
{
    var valjanUnos = true;
    var znakovi = ["(", ")", "{", "}", "'", "!", "#", '"', "\\", "/"];
    
    for (var i = 0; i < ulazniTekst.length; i++)
    {
        console.log(ulazniTekst[i]);
        if($.inArray(ulazniTekst[i], znakovi) !== -1)
        {
            valjanUnos = false;
        }
    }
    
    return valjanUnos;
}

//veliko početno slovo kod imena i prezimena
function PocetnoSlovo(ulazniTekst)
{
    var valjaniUnos = true;
    console.log("Početno slovo"+ulazniTekst[0]);
    console.log("Veliko početno"+ulazniTekst[0].toUpperCase());
    
    if(ulazniTekst[0] !== ulazniTekst[0].toUpperCase())
    {
        valjaniUnos = false;
    }
    
    return valjaniUnos;
}

//regularni izraz za email
function ProvjeraEmail(ulazniEmail)
{
    var regEx = new RegExp("\\w+\\.{0,1}\\w+\\@(\\w+\\.{1}){1,2}[a-zA-z]{2,4}$");
    
    return regEx.test(ulazniEmail);
}

//pozivanje provjera
$(document).ready(function()
{
    $("#ime").change(function()
    {
        var ime = $("#ime").val();
        
        if(!ProvjeraNedozvoljenihZnakova(ime))
        {
            $("#ime").addClass("kriviUnos");
        }
        else if(!PocetnoSlovo(ime))
        {
            $("#ime").addClass("kriviUnos");
        }
        else
        {
            $("#ime").removeClass("kriviUnos");
        }
    });
    
    $("#prezime").change(function()
    {
        var prezime = $("#prezime").val();
        
        if(!ProvjeraNedozvoljenihZnakova(prezime))
        {
            $("#prezime").addClass("kriviUnos");
        }
        else if(!PocetnoSlovo(prezime))
        {
            $("#prezime").addClass("kriviUnos");
        }
        else
        {
            $("#prezime").removeClass("kriviUnos");
        }
    });
    
    $("#email").change(function()
    {
        var email = $("#email").val();
        
        if(!ProvjeraNedozvoljenihZnakova(email))
        {
            $("#email").addClass("kriviUnos");
        }
        else if(!ProvjeraEmail(email))
        {
            $("#email").addClass("kriviUnos");
        }
        else
        {
            $("#email").removeClass("kriviUnos");
        }
    });

    $("#reLozinka").focus(function()
    {
        if($("#lozinka").val() === "")
        {
            $("#reLozinka").addClass("kriviUnos");
            $("#lozinka").focus();
        }
    });
    
    $("#reLozinka").change(function()
    {
        if($("#reLozinka").val() !== $("#lozinka").val())
        {
            $("#reLozinka").addClass("kriviUnos");
            $("#lozinka").addClass("kriviUnos");
        }
        else
        {
            $("#reLozinka").removeClass("kriviUnos");
            $("#lozinka").removeClass("kriviUnos");
        }
    });
});

//zabrana slanja u slučaju da je forma prazna
$(document).ready(function()
{
    $("#registracija").submit(function(event)
    {
        var ime, prezime, email, lozinka, reLozinka;
        
        ime = $("#ime").val();
        prezime = $("#prezime").val();
        email = $("#email").val();
        lozinka = $("#lozinka").val();
        reLozinka = $("#reLozinka").val();
        
        if(ime === "" || prezime === "" || email === "" || lozinka === "" || reLozinka === "")
        {
            alert("Ne možete predati praznu formu");
            event.preventDefault();
        }
        
        var googleResponse = jQuery('#g-recaptcha-response').val();
        if (!googleResponse) 
        {
            laert("Niste ispunili uvjet 'Ja nisam robot!'");
            event.preventDefault();
        }
    });
});

/*Provjere na prijavi*/
$(document).ready(function ()
{
    $("#prijava").submit(function(event)
    {
        var username, pass;
        
        username = $("#username").val();
        pass = $("#password").val();
        
        if(username === "" || pass === "")
        {
            $("#username").addClass("kriviUnos");
            $("#password").addClass("kriviUnos");
            alert("Ne možete predati praznu formu");
            event.preventDefault();
        }
        else
        {
            $("#username").removeClass("kriviUnos");
            $("#password").removeClass("kriviUnos");
        }
    });
    
    $("#password").focus(function()
    {
        var username = $("#username").val();
        
        if(username === "")
        {
            $("#username").focus();
            $("#username").addClass("kriviUnos");
        }
        else
        {
            $("#username").removeClass("kriviUnos");
        }
    });
});


$(document).ready(function()
{
    $("#prijavaDvaKoraka").submit(function(event)
    {
        var kod = $("#dvaKoraka").val();
        
        if(kod === "")
        {
            $("#dvaKoraka").addClass("kriviUnos");
            alert("Ne možete predati praznu formu");
            event.preventDefault();
        }
        else
        {
            $("#dvaKoraka").removeClass("kriviUnos");
        }
    });
});



/*Uredi profil*/
$(document).ready(function ()
{
    $("#uredi").click(function()
    {
        $("input").removeAttr("disabled");
        $("select").removeAttr("disabled");
        $("#uredi").hide();
        $("#tipkaAzuriraj").removeAttr("hidden");
    });
});

/*Provjera prilikom ažuriranja profila*/
$(document).ready(function ()
{
    var korisnik = $("#korisnicko_ime").val();
    
    $("#korisnicko_ime").focusout(function ()
    {
        $.ajax({
            url: './XML skripte/dohvatiXML.php?podaci=svi_korisnici',
            type: 'POST',
            dataType: 'xml',

            success: function (xml) 
            {
                $(xml).find('korisnicko_ime').each(function ()
                {
                    if($("#korisnicko_ime").val() === $(this).text() && korisnik !== $(this).text())
                    {
                        alert("Korisničko ime već postoji");
                        $("#korisnicko_ime").addClass("kriviUnos");
                        return false;
                    }
                    else
                    {
                        $("#korisnicko_ime").removeClass("kriviUnos");
                        return true;
                    }
                });
            },
            error: function()
            {
                alert("Greška prilikom pristupanja serveru!");
            }
        });        
    });
});


/*popunjavanje href atributa kod resetiranja lozinke*/
$(document).ready(function () 
{
    $("#resetLozinke").click(function()
    {
        var putanja = $(this).attr('href');
        var korisnik = $("#username").val();
        
        $(this).attr("href", (putanja+korisnik));
    });
});


/*uvjeti korištenja za neregistriranog korisnika*/
$(document).ready(function () 
{
    $("#slazemSe").click(function()
    {
        $.cookie("neregistriraniKorisnik", "Uvjeti koristenja", {expires : 3});
        $("#uvjetiKoristenja").hide();
    });
});

