<?php

session_start();

foreach (glob("utils/*.php") as $filename)
{
    require  $filename ;
}

if (!isset($_SESSION["id"]))
{
    header('location: forbiden_page.php');
}

$connect = connectDb();

if( isset($_POST["id_basement"]) && isset($_POST["id"]) && count($_POST) == 2)
{
    $id = get_id_by_get($_POST["id"]);

    $set_role = $connect ->prepare("UPDATE user SET based = :based WHERE id = :id");
    echo $set_role ->execute([ ":id" => $id, ":based" => $_POST["id_basement"] ]);

    exit(0);
}

if( isset($_POST["id_role"]) && isset($_POST["id"]) && count($_POST) == 2)
{
    $id = get_id_by_get($_POST["id"]);

    $set_role = $connect ->prepare("UPDATE user SET role = :role WHERE id = :id");
    echo $set_role ->execute([ ":id" => $id, ":role" => $_POST["id_role"] ]);

    exit(0);
}

if(isset($_POST["id"]) && isset($_POST["lock_type"]) && count($_POST) == 2)
{
    $id = get_id_by_get($_POST["id"]);

    $un_lock_user = $connect -> prepare("UPDATE user SET state = :state WHERE id = :id");
    echo $un_lock_user ->execute([ ":state" => $_POST["lock_type"],
        ":id" => $id ]);

    exit(0);
}

if(isset($_POST["get_acou_group_and_plane"]))
{

    $get_acoustic_group = $connect ->query("SELECT id,name FROM acoustic_coef");
    $get_acoustic_group = $get_acoustic_group -> fetchAll();
    $get_plane_type = $connect -> query("SELECT * FROM plane_type");
    $get_plane_type = $get_plane_type -> fetchAll();

    $array = array();
    $cpt = 0;
    foreach ($get_plane_type as $res)
    {
        $array['plane'][$cpt] = $res["name"];
        $cpt ++;
    }
    $cpt = 0;
    foreach ($get_acoustic_group as $res)
    {
        $array['acoustic'][$cpt] = $res["name"];
        $cpt ++;
    }

    //print_r($array);
    echo json_encode($array);

    exit(0);
}

if (isset($_POST["values"]) && isset($_POST["id"]))
{

    $decode =  json_decode($_POST["values"]);

    $id = get_id_by_get($_POST["id"]);

    $decode = get_id_acoustic($decode);
    $decode = get_id_plane_type($decode);

    $error = [];

    verify_email( $decode[3], $error,$id);
    verify_is_number($decode[6], $error, "Le code postal doit être une valeur numérique");
    verify_is_number($decode[10],$error, "La longueur doit être une valeur numérique ");
    verify_is_number($decode[11], $error, "L'envergure doit être une valeur numérique");
    verify_is_number($decode[12], $error, "Le poids doit être une valeur numérique");

    if(count($error) == 0 )
    {
        $scale = $decode[11] * $decode[10];
        if ($scale > 100 && $decode[12] > 1)
        {
            $category = 1;
        }
        else if ( $scale < 100 && $decode[12] < 1)
        {
            $category = 2;
        }
        else
        {
            $category = 3;
        }
        
        $modif_user = $connect -> prepare("UPDATE user SET firstname = :firstname, lastname = :lastname, birthdate = :birthdate, email = :email,address = :address, city = :city, zip = :zip, plane_model = :plane_model, plane_type = :plane_type, registration = :registration, length = :length , scale = :scale, weight = :weight, acoustic_group = :acoustic_group, category = :category WHERE id = :id ");
        $modif_user -> execute([ ":id" => $id, ":firstname" => $decode[0],
            ":lastname" => $decode[1], ":birthdate" => $decode[2],
            ":email" => $decode[3], ":address" => $decode[4],
            ":city" => $decode[5], ":zip" => $decode[6],
            ":plane_model" => $decode[7], ":plane_type" => $decode[8],
            ":registration" => $decode[9], ":length" => $decode[10],
            ":scale" => $decode[11], ":weight" => $decode[12],
            ":acoustic_group" => $decode[13],":category" => $category ]);

        echo "1";
        // $modif_user ->debugDumpParams();
    }
    else
    {
        echo json_encode($error);
    }
    exit (0);
}

$id = $_GET["id"];

if($_SESSION["role"] == 3)
{
    include "header_custo_2.php";
}
else
{
    include "header_admin.php";
}

?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <?php

    $get_user = $connect -> prepare("SELECT * FROM user WHERE id = :id");
    $get_user -> execute([ ":id" => $id]);
    $get_user = $get_user -> fetch();

    $get_user = integer_to_string_v2($get_user);

    $get_role = $connect -> prepare("SELECT * FROM role");
    $get_role -> execute([ ":id" => $id]);
    $get_role = $get_role -> fetchAll();

    ?>



    <!-- Page Heading -->
    <div class="col-12">
        <h1 class="h2 mb-4 text-gray-800 left_block" >Profil - <?php echo $get_user["role"]. " ". $get_user["based"] . " - ". $get_user["state"] ?>   </h1>
        <button class="right_block button h6" onclick="modify_profil()">Modifier le profil</button>
        <?php

        if($_SESSION["role"] == 1) {
            if ($get_user["state"] == "Actif") {
                echo '<button class="right_block button h6" onclick="lock_profil()">Bloqué le profil</button>';
            } else {
                echo '<button class="right_block button h6" onclick = "unlock_profil()" > Déloqué le profil </button >';
            }

            echo '<button class="right_block button h6" onclick="display_role_n_basement()()">Role & Basement</button>';
        }
        ?>
    </div>
    <hr>

    <div class="col-12 profil_block" id="profil_block">

        <div>
            <div class="col-6 left_block">

                <div class="col-12 h3 title">Informations personnelles</div>
                <hr>
                <div class="col-12 h5">Prénom : <p class="display_infos text"> <?php echo $get_user["firstname"] ?></p> </div>
                <div class="col-12 h5">Nom : <p class="display_infos text"> <?php echo $get_user["lastname"] ?> </p> </div>

                <div class="col-12 h5">Date de naissance : <p class="display_infos date"> <?php echo $get_user["birthdate"] ?> </p> </div>
                <div class="col-12 h5">Email : <p class="display_infos text"> <?php echo $get_user["email"] ?> </p> </div>

                <div class="col-12 h5">Adresse : <p class="display_infos text"> <?php echo $get_user["address"] ?> </p> </div>
                <div class="col-12 h5">Ville : <p class="display_infos text"> <?php echo $get_user["city"] ?> </p> </div>
                <div class="col-12 h5">Code postal : <p class="display_infos text"> <?php echo $get_user["zip"] ?> </p> </div>

            </div>

            <div class="col-6 right_block" >

                <div class="col-12 h3 title" >Informations appareil</div>
                <hr>
                <div class="col-12 h5" >Modèle d'avion : <p class="display_infos text"> <?php echo $get_user["plane_model"] ?> </p> </div>
                <div class="col-12 h5" >Type d'appareil : <p class="display_infos" id="plane_type"> <?php echo $get_user["plane_type"] ?> </p> </div>

                <div class="col-12 h5">Immatriculation : <p class="display_infos text"> <?php echo $get_user["registration"] ?> </p> </div>

                <div class="col-12 h5">Longueur (mètres) : <p class="display_infos digit"> <?php echo $get_user["length"] ?> </p> </div>
                <div class="col-12 h5">Envergure (mètres) : <p class="display_infos digit"> <?php echo $get_user["scale"] ?> </p> </div>
                <div class="col-12 h5">Masse maximale (tonnes) : <p class="display_infos digit"> <?php echo $get_user["weight"] ?> </p> </div>
                <div class="col-12 h5">Groupe accoustique : <p class="display_infos digit"> <?php echo $get_user["acoustic_group"] ?> </p> </div>


            </div>

            <div id="errors" class="error alert alert-danger ">  </div>

            <div class="col-12  h4 validation-button hide" id="validation-button" >
                <button class="button" onclick="valid_modif_profil()">VALIDER LES MODIFICATIONS</button>
                <button class="button" onclick="cancel_modif_profil()"> ANNULER </button>
            </div>


        </div>

    </div>

    <div id="overlay"></div>

    <div class="col-6" id="role_n_basement_block">

        <div class="left_block col-6">
            <div class="h6">Role</div>
            <div>
                <?php

                echo "<div>";
                for ($i = 0; $i < count($get_role); $i++)
                {
                    if ($get_role[$i][1] == $get_user["role"])
                    {
                        echo "<input type='checkbox' class='checkbox_role' id='checkbox_". $get_role[$i][0] ."' onclick='set_role(". $get_role[$i][0] .")' checked> <div>" . $get_role[$i][1] . "</div>";
                    }
                    else {
                        echo "<input type='checkbox' class='checkbox_role' id='checkbox_" . $get_role[$i][0] ."' onclick='set_role(". $get_role[$i][0] .")'> <div>" . $get_role[$i][1] . "</div>";
                    }
                }
                echo "</div>";

                ?>
            </div>
        </div>

        <div class="right_block col-6">
            <div class="h6">Basé</div>
            <div class="validation-button">
                <?php

                echo "<div>";


                if ($get_user["based"] == "basé" )
                {
                    echo "<input type='checkbox' class='checkbox_base' id='checkbox_base_". 1 ."' onclick='set_basement(". 1 .")' checked> <div> Oui </div>";
                    echo "<input type='checkbox' class='checkbox_base' id='checkbox_base_". 0 ."' onclick='set_basement(". 0 .")' > <div> Non</div>";
                }
                else {
                    echo "<input type='checkbox' class='checkbox_base' id='checkbox_base_". 1 ."' onclick='set_basement(". 1 .")' > <div> Oui </div>";
                    echo "<input type='checkbox' class='checkbox_base' id='checkbox_base_". 0 ."' onclick='set_basement(". 0 .")' checked > <div> Non</div>";
                }

                echo "</div>";

                ?>
            </div>
        </div>

        <div class="col-12  h6 validation-button " id="validation-button" >
            <button class="button" onclick="close_role_n_basement()"> FERMER </button>
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
