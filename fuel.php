<?php

session_start();

foreach (glob("utils/*.php") as $filename)
{
    require  $filename ;
}
foreach (glob("classes/*.php") as $filename)
{
    require_once  $filename ;
}

if ($_SESSION["role"] ==3 || $_SESSION["role"] == 2  )
{
    header('location: forbiden_page.php');
}
$connect = connectDb();

if (isset($_POST["get_info"]) && isset($_POST["id"]) && count($_POST)==2)
{
    $key = new fuel($_POST["id"],"","","","","","");
    $ressources = [];
    $ressources = $key->get_by_id();

    echo json_encode(state_to_string_v2($ressources,6));

    exit(0);
}

if (isset($_POST["change_state"]) && isset($_POST["id"]) && count($_POST)==2)
{
    $state = string_to_binary($_POST["change_state"]);
    $key = new fuel($_POST["id"],"","","","","",$state);

    echo $key ->change_state();

    exit(0);
}

if (isset($_POST["delete_ressource"]) && isset($_POST["id"]) && count($_POST)==2)
{

    $key = new fuel($_POST["id"],"","","","","","");
    echo $key ->delete_ressource();

    exit(0);
}

if (isset($_POST["modif_ressources"]) && isset($_POST["id"]) && count($_POST)==2)
{
    $values = json_decode($_POST["modif_ressources"]);
    $key = new fuel($_POST["id"],$values[0],$values[1],$values[2],$values[3],$values[4],"");
    echo $key ->modif_ressource();

    exit(0);
}

if (isset($_POST["create_ressources"]) && count($_POST)==1)
{
    $values = json_decode($_POST["create_ressources"]);
    $key = new fuel("",$values[0],$values[1],$values[2],$values[3],$values[4],0);
    echo $key ->create();

    exit(0);
}

include "header_admin.php";

?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div>
        <h1 class="h3 mb-4 text-gray-800 left_block">Carburant</h1><div class="right_block col-10"> <button class="button" onclick="show_create_block()">Créer un carburant</button></div>
    </div>

    <div>
        <div class="left_block col-6">
            <div>
                <?php
                    $key = new fuel("","","","","","","");
                    $get_list = $key ->get_list();
                    state_to_string($get_list);
                    if (!empty($get_list))
                    {
                        echo "<div class='table-responsive'>";
                        echo        '<table class="table table-bordered" id="table_ressources" width="100%" cellspacing="0">';
                        echo            '<thead>';
                        echo                '<tr>';
                        echo                '<th></th>';
                        echo                '<th>Carburant</th>';
                        echo                '<th>Prix HT</th>';
                        echo                '<th>TVA</th>';
                        echo                '<th>Stock actuel</th>';
                        echo                '<th>Stock maximal</th>';
                        echo                '<th>État</th>';
                        echo                '</tr>';
                        echo            '</thead>';
                        echo            '<tfoot>';
                        echo                '<tr>';
                        echo                '<th></th>';
                        echo                '<th>Carburant</th>';
                        echo                '<th>Prix HT</th>';
                        echo                '<th>TVA</th>';
                        echo                '<th>Stock actuel</th>';
                        echo                '<th>Stock maximal</th>';
                        echo                '<th>État</th>';
                        echo                '</tr>';
                        echo            '</tfoot>';
                        echo            '<tbody>';

                        foreach ($get_list as $res)
                        {
                            echo            '<tr id="tr_'. $res["id"] .'">';
                            echo                '<td><input type="checkbox" id ="checkbox_ressources_' . $res["id"] . '" class="ressources_checkbox" onclick="show_ressources_infos('. $res["id"] . ",6" .')"></td>';
                            echo                '<td>' . $res["name"] . '</td>';
                            echo                '<td>' . $res["price_DF"] . '</td>';
                            echo                '<td>' . $res["VAT"] . '</td>';
                            echo                '<td>' . $res["actual_stock"] . '</td>';
                            echo                '<td>' . $res["max_stock"] . '</td>';
                            echo                '<td id="state_'. $res["id"] .'">' . $res["state"] . '</td>';
                            echo            '<tr>';
                        }
                        echo            '</tbody>';
                        echo        '</table>';
                        echo    '</div>';
                    }

                    //print_r($get_list);

                ?>
            </div>

            <div>
                <div class="col-4 left_block text-center">
                    <button class="ressources_button_bis alert-primary" id="modify">Modifier</button>
                </div>

                <div class="col-4 right_block text-center">
                    <button class="ressources_button_bis alert-danger" id="delete" >Supprimer</button>
                </div>

                <div class="col-4 right_block text-center">
                    <button class="ressources_button_bis alert-warning" id="un_activate">Activer/Désactiver</button>
                </div>
            </div>

        </div>

        <div class="right_block col-6 half-ressources" id="info_block">

            <div class="text-center title h4">INFORMATIONS</div>

            <div class="ressources_right_block">Carburant : <p class = "display_infos right_block"></p></div>
            <div class="ressources_right_block">Prix HT : <p class = "display_infos right_block"></p></div>
            <div class="ressources_right_block">TVA : <p class = "display_infos right_block"></p></div>
            <div class="ressources_right_block">Stock actuel :<p class = "display_infos right_block"></p></div>
            <div class="ressources_right_block">Stock maximal :<p class = "display_infos right_block"></p></div>
            <div class="ressources_right_block">État :<p class = "display_infos right_block"></p></div>


        </div>

        <div class="right_block col-6 half-ressources" id="modif_block">

            <div class="text-center title h4">MODIFICATIONS</div>

            <div class="ressources_right_block">Carburant : <input type="text" class="new_ressources right_block"></div>
            <div class="ressources_right_block">Prix HT : <input type="number" step="0.01" class="new_ressources right_block" id="df_modif" onchange="DF_to_VAT('df_modif','vat_modif')"></div>
            <div class="ressources_right_block">TVA : <input type="number" step="0.01"class="new_ressources right_block" id="vat_modif" onchange="VAT_to_DF('vat_modif','df_modif')"></div>
            <div class="ressources_right_block">Stock actuel :<input type="number" step="0.01"class="new_ressources right_block"></div>
            <div class="ressources_right_block">Stock maximal :<input type="number" step="0.01"class="new_ressources right_block"></div>

            <div class="text-center"> <button class="validation-modif_ressources button" id="valid_modif">Valider les modifications</button></div>
        </div>

        <div class="right_block col-6 half-ressources" id="create_block">

            <div class="text-center title h4">Création</div>

            <div class="ressources_right_block">Carburant : <input type="text" class="create_ressources right_block"></div>
            <div class="ressources_right_block">Prix HT : <input type="number" step="0.01" class="create_ressources right_block" id="df_create" onchange="DF_to_VAT('df_create','vat_create')"></div>
            <div class="ressources_right_block">TVA : <input type="number" step="0.01"class="create_ressources right_block" id="vat_create" onchange="VAT_to_DF('vat_create','df_create')"></div>
            <div class="ressources_right_block">Stock actuel :<input type="number" step="0.01"class="create_ressources right_block"></div>
            <div class="ressources_right_block">Stock maximal :<input type="number" step="0.01"class="create_ressources right_block"></div>

            <div class="text-center"> <button class="validation-modif_ressources button" id="valid_create" onclick="create_ressources()">Valider la création </button></div>
            <div class="text-center"> <button class="validation-modif_ressources button" onclick="close_create_block()">Fermer</button></div>
        </div>

    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Your Website 2020</span>
        </div>
    </div>
</footer>
<!-- End of Footer -->



<!-- Custom scripts for all pages-->
<script src="js/js_animation.js"></script>
<script src="js/js_treatment.js"></script>

</body>

</html>
