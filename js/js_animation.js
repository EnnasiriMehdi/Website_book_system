
// register.php

function bth_text_to_date()
{
    var object = document.getElementById("bth");

    object.setAttribute("type", "date");
}

 // profil.php

var stock_profil_block = document.getElementById("profil_block").innerHTML;

function cancel_modif_profil()
{
    var block = document.getElementById("profil_block");

    block.innerHTML = "";
    block.innerHTML = stock_profil_block;
}
function modify_profil()
{
    var array = [];
    var get_acou_group_and_plane = 1;


    var type_data = ["text","text","date","text","text","text","text","text","select","number","number","number","number","select"];
    var b = [];
    var stock;
    var c = document.getElementsByClassName("h5");



    var request = new XMLHttpRequest();

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4 )
        {

            array = JSON.parse(request.responseText);
            array = Object.values(array);


            let button = document.getElementById('validation-button');
            button.style.display = "block";


            for (let i = 0; i<c.length; i++)
            {
                b[i] = c[i].childNodes;
                stock = b[i][1].innerText;

                if (type_data[i] === "select")
                {
                    let element = document.createElement("select");
                    element.setAttribute("class","new");
                    let empty = document.createElement("option");
                    element.appendChild(empty);
                    for (let j = 0; j<array[i%2].length;j++)
                    {
                        let option = document.createElement("option");
                        option.setAttribute("value", array[i%2][j]);
                        option.innerText = array[i%2][j];
                        element.appendChild(option);
                    }
                    c[i].appendChild(element);
                }
                else {
                    let element = document.createElement("input");
                    element.setAttribute("class", "new");
                    element.setAttribute("type", type_data[i]);
                    element.value = stock;
                    c[i].appendChild(element);
                }
                c[i].removeChild(b[i][1]);

                //c[i].appendChild(element);
            }
            console.log(c);
            console.log(b);
        }
    }
    request.open("POST","profil.php?id=1");
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`get_acou_group_and_plane=${get_acou_group_and_plane}`);
}

function display_role_n_basement()
{
    var overlay = document.getElementById("overlay");
    var role_n_base_block = document.getElementById("role_n_basement_block");

    overlay.style.display = "block";
    role_n_base_block.style.display = "block";


}

function close_role_n_basement()
{
    var overlay = document.getElementById("overlay");
    var role_n_base_block = document.getElementById("role_n_basement_block");

    overlay.style.display = "none";
    role_n_base_block.style.display = "none";
}

function show_modif_block()
{
    let modify = document.getElementById("modify");
    let info_block = document.getElementById("info_block");
    let modif_block = document.getElementById("modif_block");
    let create_block = document.getElementById("create_block");

    modify.innerText = "Informations";
    modify.setAttribute("onclick","show_info_block()");
    info_block.style.display = "none";
    modif_block.style.display = "block";
    create_block.style.display = "none";
}

function show_info_block()
{
    let modify = document.getElementById("modify");
    var info_block = document.getElementById("info_block");
    var modif_block = document.getElementById("modif_block");
    let create_block = document.getElementById("create_block");

    modify.innerText = "Modifier";
    modify.setAttribute("onclick","show_modif_block()");
    info_block.style.display = "block";
    modif_block.style.display = "none";
    create_block.style.display = "none";
}

function show_create_block()
{
    let create_block = document.getElementById("create_block");

    create_block.style.display = "block";
}

function close_create_block()
{
    let create_block = document.getElementById("create_block");

    create_block.style.display = "none";
}

function protect_ressources_price(string)
{
    let none = document.getElementById(string);
    none.selected = true ;
}

function DF_to_VAT(string,string_2)
{
    var VAT = document.getElementById(string_2);
    var DF = document.getElementById(string);
    VAT.value = DF.value * 0.2;
    //protect_ressources_price();
}

function VAT_to_DF(string,string_2)
{
    var VAT = document.getElementById(string);
    var DF = document.getElementById(string_2);
    DF.value = VAT.value / 0.2;
    //protect_ressources_price();
}

///    aeroclub_services.php
function remove_from_basket(id)
{
    var tr_to_remove = document.getElementById("table_line_"+id);
    var inactive_checkbox = document.getElementById("checkbox_services_"+id);
    inactive_checkbox.checked = false;
    tr_to_remove.remove();
    update_amount();
}

function update_amount()
{
    var prices = document.getElementsByClassName("price_basket");
    var vat = document.getElementsByClassName("vat_basket");
    var amount = 0;
    var amount_text = document.getElementById("amount_basket");
    amount_text.innerText = "";
    for (let i = 0; i<prices.length; i++)
    {
        amount += parseInt(prices[i].innerText);
        amount += parseInt(vat[i].innerText);
    }
    amount_text.innerText = amount + " €";
}

function update_amount_V2(id)
{
    var price_df = parseInt(document.getElementById("price_basket_"+id).innerText);
    var vat = parseInt(document.getElementById("vat_basket_"+id).innerText);
    var duration = parseInt(document.getElementById("select_duration_"+id).value);

    if (duration === 1)
    {
        return false;
    }
    else
    {
        var amount_text = document.getElementById("amount_basket");
        var amount = parseInt(document.getElementById("amount_basket").innerText);
        amount_text.innerText = "";

        amount = amount + (price_df+vat)*duration - (price_df+vat);
        amount_text.innerText = amount + "€";
    }

}

var DATE = "";

function add_to_basket(id)
{
    var checkbox = document.getElementById("checkbox_services_"+id);
    if (checkbox.checked === false)
    {
        remove_from_basket(id);
        return false;
    }
    var basket_table= document.getElementById("basket_table");
    var get_values = document.getElementsByClassName("service_"+id);
    var remove_check_box = document.createElement("input");
    remove_check_box.setAttribute("type", "checkbox");
    remove_check_box.setAttribute("onclick", "remove_from_basket("+id+")");
    var td_checkbox = document.createElement("td");
    td_checkbox.appendChild(remove_check_box);
    var tr = document.createElement("tr");

    for (let i =0; i<get_values.length;i++)
    {
        if(i != 1) {
            let td = document.createElement("td");
            td.innerText = get_values[i].innerText;

            switch (i) {
                case 2 :
                    td.setAttribute("class", "price_basket");
                    td.setAttribute("id", "price_basket_"+id);
                    break;
                case 3 :
                    td.setAttribute("class", "vat_basket");
                    td.setAttribute("id", "vat_basket_"+id);
                    break;
                default:
            }
            tr.appendChild(td);
        }
    }
    var td = document.createElement("td");
    get_date_available(id,td);
    tr.appendChild(td);
    td = document.createElement("td");
    var hours = document.createElement("select");
    hours.setAttribute("id", "select_hours_"+id);
    td.appendChild(hours);
    tr.appendChild(td);
    td = document.createElement("td");
    var duration = document.createElement("select");
    duration.setAttribute("id", "select_duration_"+id);
    td.appendChild(duration);
    tr.appendChild(td);

    tr.setAttribute("id", "table_line_"+id);
    tr.setAttribute("class", "tr_basket");
    tr.setAttribute("value", id);

    tr.appendChild(td_checkbox);
    basket_table.appendChild(tr);
    update_amount();

}

function get_date_available(id,td)
{

    var path = window.location.pathname;
    var page = path.split("/").pop();
    var get_date = 1;
    var request = new XMLHttpRequest();
    var array = [];

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {
            array = JSON.parse(request.responseText);

            var response = document.createElement("select");
            response.innerHTML="";
            response.setAttribute("id", "select_date_"+id);
            response.setAttribute("onchange","get_hours_available("+id+",this.value)");
            response.setAttribute("class","select_date");
            let option_1 = document.createElement("option");
            response.appendChild(option_1);
            for (let i = 0; i < array.length; i++)
            {
                let option = document.createElement("option");
                option.setAttribute("value", array[i]);

                option.innerText = array[i];
                response.appendChild(option);
            }
            td.appendChild(response);

        }
    }
    request.open("POST", page);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`get_date=${get_date}&id=${id}`);
}

function get_hours_available(id,date)
{
    var path = window.location.pathname;
    var page = path.split("/").pop();

    var request = new XMLHttpRequest();
    var array = [];

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {

            array = JSON.parse(request.responseText);
            console.log("1 " + request.responseText);
            array = Object.values(array);
            let select_hours = document.getElementById("select_hours_"+id);
            select_hours.innerHTML = "";
            select_hours.setAttribute("onchange","get_duration_available("+id+",this.value)");
            select_hours.setAttribute("class","select_hours");
            let option = document.createElement("option");
            option.innerText = "";
            select_hours.appendChild(option);
            for (let i = 0; i<array.length;i++)
            {
                let option = document.createElement("option");
                option.setAttribute("value",array[i]);
                option.innerText = array[i];
                select_hours.appendChild(option);
            }
        }
    }
    request.open("POST", page);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`get_hours=${date}&id=${id}`);
}

function get_duration_available(id,value)
{
    var date = document.getElementById("select_date_"+id).value;
    var path = window.location.pathname;
    var page = path.split("/").pop();
    var request = new XMLHttpRequest();
    var array = [];

    request.onreadystatechange = function ()
    {
        if (request.readyState === 4)
        {
            array = JSON.parse(request.responseText);
            console.log("2 " + request.responseText);
            array = Object.values(array);
            let select_duration = document.getElementById("select_duration_"+id);
            select_duration.innerHTML = "";
            select_duration.setAttribute("class","select_duration");
            select_duration.setAttribute("onchange","update_amount_V2("+id+")");
            let is_value = (element) => element == value;
            let index = array.findIndex(is_value);
            let cpt = 1;
            while (array[index] === array[index+1]-1)
            {
                index ++;
                cpt ++;
            }
            for (let i = 1; i<=cpt;i++)
            {
                let option = document.createElement("option");
                option.setAttribute("value",i);
                option.innerText = i;
                select_duration.appendChild(option);
            }
        }
    }
    request.open("POST", page);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(`get_hours=${date}&id=${id}`);
}