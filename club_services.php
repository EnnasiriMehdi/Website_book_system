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

if (isset($_POST["set_price"]) && isset($_POST["id"]) && count($_POST)==2)
{
    $key = new fleet($_POST["id"],"",1,"","","","","",",","","");
    $key = $key->get_by_id();
    if ( $key != "Ressource introuvable !") {
        $encode = [$key["instruction_price"], $key["solo_price"]];
    }
    else
    {
        $encode = ["error"];
    }
    echo json_encode($encode);

    exit(0);
}

if (isset($_POST["get_info"]) && isset($_POST["id"]) && count($_POST)==2)
{
    $key = new services($_POST["id"],"",1,"","","","","");
    $ressources = [];
    $ressources = $key->get_by_id();

    echo json_encode(state_to_string_v2($ressources,7));

    exit(0);
}

if (isset($_POST["change_state"]) && isset($_POST["id"]) && count($_POST)==2)
{
    $state = string_to_binary($_POST["change_state"]);

    $key = new services($_POST["id"],"",1,"","","","",$state);

    echo $key ->change_state();

    exit(0);
}

if (isset($_POST["delete_ressource"]) && isset($_POST["id"]) && count($_POST)==2)
{

    $key = new services($_POST["id"],"","50","","","","","");
    echo $key ->delete_ressource();

    exit(0);
}

if (isset($_POST["modif_ressources"]) && isset($_POST["id"]) && count($_POST)==2)
{
    $values = json_decode($_POST["modif_ressources"]);

    $key = new services($_POST["id"],$values[0],$values[1],$values[2],$values[3],$values[4],$values[5],$values[6]);
    echo $key ->modif_ressource();

    exit(0);
}

if (isset($_POST["create_ressources"]) && count($_POST)==1)
{
    $values = json_decode($_POST["create_ressources"]);
    $key = new services("",$values[0],$values[1],$values[2],$values[3],$values[4],$values[5],"");

    echo $key ->create();

    exit(0);
}

include "header_admin.php";

?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div>
        <h1 class="h3 mb-4 text-gray-800 left_block">Prestations</h1><div class="right_block col-10"> <button class="button" onclick="show_create_block()">Créer une prestation</button></div>
    </div>

    <div>
        <div class="left_block col-6">
            <div>
                <?php
                $key = new services("","","50","","","","","",);
                $get_list = $key ->get_list();

                state_to_string($get_list);

                if (!empty($get_list))
                {
                    echo "<div class='table-responsive'>";
                    echo        '<table class="table table-bordered" id="table_ressources" width="100%" cellspacing="0">';
                    echo            '<thead>';
                    echo                '<tr>';
                    echo                '<th></th>';
                    echo                '<th>Intitulé</th>';
                    echo                '<th>Description</th>';
                    echo                '<th>Prix HT</th>';
                    echo                '<th>TVA</th>';
                    echo                '<th>Type</th>';
                    echo                '<th>Avion</th>';
                    echo                '<th>État</th>';
                    echo                '</tr>';
                    echo            '</thead>';
                    echo            '<tfoot>';
                    echo                '<tr>';
                    echo                '<th></th>';
                    echo                '<th>Intitulé</th>';
                    echo                '<th>Description</th>';
                    echo                '<th>Prix HT</th>';
                    echo                '<th>TVA</th>';
                    echo                '<th>Type</th>';
                    echo                '<th>Avion</th>';
                    echo                '<th>État</th>';
                    echo                '</tr>';
                    echo            '</tfoot>';
                    echo            '<tbody>';

                    foreach ($get_list as $res)
                    {
                        echo            '<tr id="tr_'. $res["id"] .'">';
                        echo                '<td><input type="checkbox" id ="checkbox_ressources_' . $res["id"] . '" class="ressources_checkbox" onclick="show_ressources_infos('. $res["id"] . ",7"  .')"></td>';
                        echo                '<td>' . $res["name"] . '</td>';
                        echo                '<td>' . $res["description"] . '</td>';
                        echo                '<td>' . $res["price_DF"] . '</td>';
                        echo                '<td>' . $res["VAT"] . '</td>';
                        echo                '<td>' . $res["type"] . '</td>';
                        echo                '<td>' . $res["id_location"] . '</td>';
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

            <div class="ressources_right_block">Intitulé : <p class = "display_infos right_block"></p></div>
            <div class="ressources_right_block">Description : <p class = "display_infos right_block"></p></div>
            <div class="ressources_right_block">Prix HT : <p class = "display_infos right_block"></p></div>
            <div class="ressources_right_block">TVA : <p class = "display_infos right_block"></p></div>
            <div class="ressources_right_block">Type :<p class = "display_infos right_block"></p></div>
            <div class="ressources_right_block">Avion :<p class = "display_infos right_block"></p></div>
            <div class="ressources_right_block">État :<p class = "display_infos right_block"></p></div>


        </div>

        <div class="right_block col-6 half-ressources" id="modif_block">

            <div class="text-center title h4">MODIFICATIONS</div>

            <div class="ressources_right_block">Intitulé : <input type="text" class="new_ressources right_block"></div>
            <div class="ressources_right_block">Description : <input type="textarea" class="new_ressources right_block"></div>
            <div class="ressources_right_block">Prix HT : <input type="number" step="0.01" class="new_ressources right_block" id="df_modif" onchange="DF_to_VAT('df_modif','vat_modif');protect_ressources_price('modif_plane_0')"></div>
            <div class="ressources_right_block">TVA : <input type="number" step="0.01"class="new_ressources right_block" id="vat_modif" onchange="VAT_to_DF('vat_modif','df_modif');protect_ressources_price('modif_plane_0')"></div>
            <div class="ressources_right_block">Type :<select class="new_ressources right_block">
                <option value="0">Basique</option>
                <option value="1">Aéroclub</option>
                </select></div>
            <div class="ressources_right_block">Avion :<select id="set_price_ressources_1" onchange="set_price_ressources(1,'modif')"class="new_ressources right_block">
                    <option value="0" id="modif_plane_0">Auncun</option>
                <?php
                $fleet = new fleet("","","","","","","","","","","");
                $fleet = $fleet->get_list();

                foreach ($fleet as $res)
                {
                    echo "<option id='plane_". $res["id"] ."'value='" . $res["id"] ."'>". $res["plane_model"] . "</option>";
                }
                ?>
                </select></div>

            <div class="text-center"> <button class="validation-modif_ressources button" id="valid_modif">Valider les modifications</button></div>
        </div>

        <div class="right_block col-6 half-ressources" id="create_block">

            <div class="text-center title h4">Création</div>

            <div class="ressources_right_block">Intitulé : <input type="text" class="create_ressources right_block"></div>
            <div class="ressources_right_block">Description : <input type="textarea" class="create_ressources right_block"></div>
            <div class="ressources_right_block">Prix HT : <input type="number" step="0.01" class="create_ressources right_block" id="df_create" onchange="DF_to_VAT('df_create','vat_create');protect_ressources_price('create_plane_0')"></div>
            <div class="ressources_right_block">TVA : <input type="number" step="0.01"class="create_ressources right_block" id="vat_create" onchange="VAT_to_DF('vat_create','df_create');protect_ressources_price('create_plane_0')"></div>
            <div class="ressources_right_block">Type :<select class="create_ressources right_block">
                <option value="0">Basique</option>
                <option value="1">Aéroclub</option>
            </select></div>
            <div class="ressources_right_block">Avion :<select class="create_ressources right_block" id="set_price_ressources_2" onchange="set_price_ressources(2,'create')">
                    <option value="0" id="create_plane_0">Auncun</option>
                    <?php
                        $fleet = new fleet("","","","","","","","","","","");
                        $fleet = $fleet->get_list();

                        foreach ($fleet as $res)
                        {
                            echo "<option id='plane_". $res["id"] ."'value='" . $res["id"] ."'>". $res["plane_model"] . "</option>";
                        }
                    ?>
                </select></option></div>

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
