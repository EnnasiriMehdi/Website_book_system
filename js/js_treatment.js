// register.php

function register()
{
    var firstname = document.getElementById("firstname").value;
    var lastname = document.getElementById("lastname").value;
    var email = document.getElementById("email").value;
    var bth = document.getElementById("bth").value;
    var pwd = document.getElementById("pwd").value;
    var conf_pwd = document.getElementById("conf_pwd").value;
    var address = document.getElementById("address").value;
    var city = document.getElementById("city").value;
    var zip = document.getElementById("zip").value;
    var array = [];
    var errors = document.getElementById("errors");
    errors.innerHTML = "";

    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState=== 4) {

            if (request.responseText === '1')
            {
                alert("le compte a bien ete créer");
                window.location.href = "login.php";
            }
            else
                {
                console.log(request.responseText);
                array = JSON.parse(request.responseText);
                array = Object.values(array);
                let error = document.getElementsByClassName("error");
                for (let u = 0; u < error.length; u++)
                {
                        error[u].style.display = "block";
                }
                for (let i = 0; i < array.length; i++)
                {
                    let ul = document.createElement("li");
                    ul.innerText = array[i];
                    errors.appendChild(ul);
                }
            }
        }
    }
    request.open("POST","register.php");
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`firstname=${firstname}&lastname=${lastname}&email=${email}&bth=${bth}&pwd=${pwd}&conf_pwd=${conf_pwd}&address=${address}&city=${city}&zip=${zip}`);
}

function login()
{
    var email = document.getElementById("email").value;
    var pwd = document.getElementById("pwd").value;

    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4 )
        {
            $response = parseInt(request.responseText);

            if (!isNaN($response))
            {
                alert("Connexion réussite");
                window.location.href = "profil.php?id="+request.responseText;
            }
            else
            {
                let error = document.getElementById("errors");
                error.style.display = "block";
                error.style.marginTop = "20px";
                error.innerText = request.responseText;
            }
            console.log(request.responseText);
        }
    }
    request.open("POST","login.php");
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`email=${email}&pwd=${pwd}`);
}

// profil.php

function valid_modif_profil()
{
    var get_new = document.getElementsByClassName("new");
    var values = [];
    var errors = document.getElementById("errors");
    errors.innerHTML = "";
    for (let i=0; i<get_new.length;i++)
    {
        values[i] = get_new[i].value;
    }

    values = JSON.stringify(values);
    var id = window.location.search.substr(1);

    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4 ) {
            console.log(request.responseText);
            if (request.responseText === '1') {
                alert("Les modifications on été prises ne compte");
                location.reload();
            }
            else
            {

                array = JSON.parse(request.responseText);
                array = Object.values(array);
                let error = document.getElementsByClassName("error");
                for (let u = 0; u < error.length; u++)
                {
                    error[u].style.display = "block";
                }
                for (let i = 0; i < array.length; i++)
                {
                    let ul = document.createElement("li");
                    ul.innerText = array[i];
                    errors.appendChild(ul);
                }
            }
        }
    }
    request.open("POST","profil.php");
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`values=${values}&id=${id}`);


}

function lock_profil()
{
    if(confirm("Voulez-vous vraiment bloquer cet utilisateur ? "))
    {
        var id = window.location.search.substr(1);
        var lock_type = 0;

        var request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (request.readyState === 4) {
                alert("L'utilisateur à bien été bloqué");
            }
        }
        request.open("POST", "profil.php");
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        request.send(`lock_type=${lock_type}&id=${id}`);
    }
}


function unlock_profil()
{
    if (confirm("Voulez-vous vraiment débloquer cet utilisateur ? "))
    {
        var id = window.location.search.substr(1);
        var lock_type = 1;

        var request = new XMLHttpRequest();

        request.onreadystatechange = function ()
        {
            if (request.readyState === 4) {
                alert("L'utilisateur à bien été débloqué");
            }
        }
        request.open("POST", "profil.php");
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        request.send(`lock_type=${lock_type}&id=${id}`);
    }
}

function unactive_all_checkboxes(string)
{
    var checkboxes = document.getElementsByClassName(string);
    for (let i = 0; i < checkboxes.length; i++)
    {
        if (checkboxes[i].checked === true)
        {
            checkboxes[i].checked = false;
        }
    }
}
function set_role(id_role) {
    var id = window.location.search.substr(1);
    unactive_all_checkboxes("checkbox_role");
    /*var checkboxes = document.getElementsByClassName("checkbox_role");
    var id = window.location.search.substr(1);
    for (let i = 0; i < checkboxes.length; i++)
    {
        if (checkboxes[i].checked === true)
        {
            checkboxes[i].checked = false;
        }
    }*/
    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {
            if (request.responseText === '1')
            {
                alert("Le role à bien été changé");
                var checkbox = document.getElementById("checkbox_"+id_role);
                checkbox.checked = true;
            }
            else
            {
                alert("Une erreur est survenue");
                location.reload();
            }
        }
    }
    request.open("POST", "profil.php");
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`id_role=${id_role}&id=${id}`);
}

function set_basement(id_basement)
{
    var id = window.location.search.substr(1);
    unactive_all_checkboxes("checkbox_base");

    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {
            if (request.responseText === '1')
            {
                alert("Le basement à bien été changé");
                var checkbox = document.getElementById("checkbox_base_"+id_basement);
                checkbox.checked = true;
            }
            else
            {
                alert("Une erreur est survenue");
                location.reload();
            }
        }
    }
    request.open("POST", "profil.php");
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`id_basement=${id_basement}&id=${id}`);
}

// user_manager.php & staff_manager

function search_user()
{
    var role;
    var path = window.location.pathname;
    var page = path.split("/").pop();

    if (page === "user_manager.php")
    {
        role = 1;
    }
    else
    {
        role = 0;
    }

    var values = document.getElementsByClassName("research_option");
    var encode = [];
    for (let i = 0; i<values.length; i++)
    {
        encode[i] = values[i].value;
    }
    encode = JSON.stringify(encode);

    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {
            let block_table = document.getElementById("block_table");
            block_table.innerHTML = "";
            block_table.innerHTML = request.responseText;
        }
    }
    request.open("POST", "user_manager.php");
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`encode=${encode}&role=${role}`);
}


// all ressources.php

function show_ressources_infos(id,index)
{
    var path = window.location.pathname;
    var page = path.split("/").pop();
    var get_info = 1;
    unactive_all_checkboxes("ressources_checkbox");
    var decode = [];

    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {
            console.log(request.responseText);
            decode = JSON.parse(request.responseText);
            decode = Object.values(decode);
            console.log(decode);
            let checkbox = document.getElementById("checkbox_ressources_" + id);
            checkbox.checked = true;
            let input = document.getElementsByClassName("new_ressources");
            let p = document.getElementsByClassName("display_infos");
            for (let i=0;i<p.length;i++)
            {
                p[i].innerText = decode[i+1];
            }
            console.log(p);
            for (let i = 0; i<input.length;i++)
            {
                input[i].value = decode[i+1];
            }
            console.log("//////////");
            console.log(input);
            console.log(decode);
            let modify = document.getElementById("modify");
            let erase = document.getElementById("delete");
            let un_activate = document.getElementById("un_activate");
            let valid_button = document.getElementById("valid_modif");

            console.log(request.responseText);
            valid_button.setAttribute('onclick', 'modif_ressource('+id+')');
            modify.setAttribute("onclick","show_modif_block()");
            erase.setAttribute("onclick","delete_ressources("+ id +")");
            un_activate.setAttribute('onclick','un_activate('+ id + ',"' + decode[index] +'")');
        }
    }
    request.open("POST", page);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`get_info=${get_info}&id=${id}`);
}


function un_activate(id,change_state)
{
    var path = window.location.pathname;
    var page = path.split("/").pop();
    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {
            if (request.responseText === '1')
            {
                let text = document.getElementById("state_"+id);
                if (text.innerText === "Actif")
                {
                    text.innerText = "Bloqué";
                }
                else
                {
                    text.innerText = "Actif";
                }
                alert("L'état à bien été changé !");
            }
            else
            {
                console.log(request.responseText);
                alert("Une erreur est survenue !");
            }
        }
    }
    request.open("POST", page);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`change_state=${change_state}&id=${id}`);
}

function delete_ressources(id)
{
    if (confirm("Voulez-vous vraiment supprimer cette ressource ?")) {
        var path = window.location.pathname;
        var page = path.split("/").pop();
        var delete_ressource = 1;
        var request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (request.readyState === 4) {
                if (request.responseText === '1') {
                    let table = document.getElementById("table_ressources");
                    let tr = document.getElementById("tr_" + id);
                    tr.remove();

                    alert("La ressource à bien été supprimé");

                } else {
                    alert("Une erreur est survenue !");
                }
            }
        }
        request.open("POST", page);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        request.send(`delete_ressource=${delete_ressource}&id=${id}`);
    }
}

function modif_ressource(id)
{

    var path = window.location.pathname;
    var page = path.split("/").pop();
    var inputs = document.getElementsByClassName("new_ressources");
    var modif_ressources = [];
    for (let i = 0; i<inputs.length;i++)
    {
        modif_ressources[i] = inputs[i].value;
    }
    modif_ressources = JSON.stringify(modif_ressources);

    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {
            if (request.responseText === '1')
            {
                alert("La ressource à bien été modifié");
                location.reload();
            }
            else
            {
                //console.log(request.responseText);
                alert("Une erreur est survenue !");
            }
        }
    }
    request.open("POST", page);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`modif_ressources=${modif_ressources}&id=${id}`);
}

function create_ressources()
{
    var path = window.location.pathname;
    var page = path.split("/").pop();
    var inputs = document.getElementsByClassName("create_ressources");
    var create_ressources = [];
    for (let i = 0; i<inputs.length;i++)
    {
        create_ressources[i] = inputs[i].value;
    }

    console.log(create_ressources);
    create_ressources = JSON.stringify(create_ressources);

    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {
            if (request.responseText === '1')
            {

                alert("La ressource à bien été créer");
                location.reload();
            }
            else
            {

                alert("Une erreur est survenue !");
            }
        }
    }
    request.open("POST", page);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`create_ressources=${create_ressources}`);
}

function set_price_ressources(id_1,string)
{
    var path = window.location.pathname;
    var page = path.split("/").pop();
    var set_price = 1;
    var id = document.getElementById("set_price_ressources_"+id_1).value;

    var price_DF = document.getElementById("df_"+string);
    var tva = document.getElementById("vat_"+string)

    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {
            console.log(request.responseText);

            var price = JSON.parse(request.responseText);
            if (price.length<2)
            {
                return false;
            }
            if (confirm("Avion d'instruction ? "))
            {
                price_DF.value = price[0];
                DF_to_VAT("df_"+string,"vat_"+string);
                return true;
            }
            else if (confirm("Avion solo  ? "))
            {
                price_DF.value = price[1];
                DF_to_VAT("df_"+string,"vat_"+string);
                return true;
            }
            else
            {
                let none = document.getElementById("plane_0");
                none.selected = true ;
            }



        }
    }
    request.open("POST", page);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`id=${id}&set_price=${set_price}`);
}

function valid_order()
{
    var ids = document.getElementsByClassName("tr_basket");
    var dates = document.getElementsByClassName("select_date");
    var hours = document.getElementsByClassName("select_hours");
    var duration = document.getElementsByClassName("select_duration");
    var array = [];

    if ( ids.length === 0 || dates.length === 0 || hours.length === 0 || duration.length === 0 )
    {
        alert("Vous devez remplir tous les champs : date / horaire / durée !");
        return false;
    }

    for(let i = 0; i < ids.length;i++)
    {
        array[i] = new Array(ids.length);
        array[i][0] = ids[i].getAttribute("value");
        array[i][1] = dates[i].value;
        array[i][2] = hours[i].value;
        array[i][3] = duration[i].value;
        if ( ids[i].getAttribute("value") === "" || dates[i].value === "" || hours[i].value === "" || duration[i].value === "")
        {

            alert("Vous devez remplir tous les champs : date / horaire / durée !");
            return false;
        }
    }
    var encode = JSON.stringify(array);
    var path = window.location.pathname;
    var page = path.split("/").pop();
    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {
            alert("La demande à bien effectuer");
            document.location.href="my_order.php";
        }
    }
    request.open("POST", page);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`encode=${encode}`);
}

function valid_custo_order(id)
{
    if(confirm("Valider cette demande vous engage à la payer. Continuer ? "))
    {
    var path = window.location.pathname;
    var page = path.split("/").pop();
    var valid = 1;

    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {
            console.log(request.responseText);
            if (request.responseText === '1') {
                alert('la demande à bien été validé !');
                var tr = document.getElementById("tr_" + id);
                tr.remove();
            }
            else
            {
                alert("Une erreur est survenue, veuillez réessayer plus tard");
            }
        }
    }
    request.open("POST", page);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`valid=${valid}&id=${id}`);
    }
}

function delete_custo_order(id)
{
    if(confirm("Etes vous sur de vouloir annuler cette demande ? "))
    {
        var path = window.location.pathname;
        var page = path.split("/").pop();
        var erase = 1;

        var request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (request.readyState === 4) {
                if (request.responseText === '11') {
                    alert('la demande à bien été annulée !');
                    var tr = document.getElementById("tr_" + id);
                    tr.remove();
                }
                else
                {
                    console.log(request.responseText);
                    alert("Une erreur est survenue, veuillez réessayer plus tard");
                }
            }
        }
        request.open("POST", page);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        request.send(`erase=${erase}&id=${id}`);
    }
}

function pay_invoice(id)
{
    var path = window.location.pathname;
    var page = path.split("/").pop();
    var pay = 1;

    var request = new XMLHttpRequest();

    request.onreadystatechange = function () {
        if (request.readyState === 4) {

            console.log(request.responseText);
        }
    }
    request.open("POST", page);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`pay=${pay}&id=${id}`);

}

function update_request()
{
    var update_request = 1;

    var request = new XMLHttpRequest();

    request.onreadystatechange = function () {
        if (request.readyState === 4) {

            alert("Les demandes ont bien été mises à jour");
        }
    }
    request.open("POST", "aeroclub_services.php");
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`update_request=${update_request}`);

}