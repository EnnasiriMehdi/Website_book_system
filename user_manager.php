<?php

session_start();

foreach (glob("utils/*.php") as $filename)
{
    require  $filename ;
}

if ($_SESSION["role"] ==3  )
{
    header('location: forbiden_page.php');
}

$connect = connectDb();

if (isset($_POST["encode"]) && isset($_POST["role"]) &&  count($_POST) == 2)
{
    $search_options = json_decode($_POST["encode"]);
    regex_empty_string($search_options);
    //print_r($search_options);
    $search_options[3] = string_to_binary($search_options[3]);
    $search_options[4] = string_to_binary($search_options[4]);
    if ($_POST["role"] == 1)
    {
        $search_user = $connect->prepare("SELECT * FROM user WHERE firstname REGEXP :firstname AND lastname REGEXP :lastname AND `email` REGEXP :email AND based REGEXP :based AND state REGEXP :state AND role = 3");
    }
    else
    {
        $search_user = $connect->prepare("SELECT * FROM user WHERE firstname REGEXP :firstname AND lastname REGEXP :lastname AND `email` REGEXP :email AND based REGEXP :based AND state REGEXP :state AND role != 3");
    }
    $search_user -> execute([ ":firstname" => $search_options[0],
                                ":lastname" => $search_options[1],
                                ":email" => $search_options[2],
                                ":based" => $search_options[3],
                                ":state" => $search_options[4] ]);
    //$search_user ->debugDumpParams();
    $count = $search_user -> rowCount();
    $search_user = $search_user -> fetchAll();
    $search_user = integer_to_string($search_user);

    if ($count == 0)
    {
        echo    "<div class='no-found '><p>Aucun utilisateur n'a été trouvé</p></div>";
    }
    else
    {
        echo "<div class='table-responsive'>";
        echo        '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
        echo            '<thead>';
        echo                '<tr>';
        echo                '<th>Prénom</th>';
        echo                '<th>Nom</th>';
        echo                '<th>Email</th>';
        echo                '<th>Basé</th>';
        echo                '<th>État</th>';
        echo                '<th></th>';
        echo                '</tr>';
        echo            '</thead>';
        echo            '<tfoot>';
        echo                '<tr>';
        echo                '<th>Prénom</th>';
        echo                '<th>Nom</th>';
        echo                '<th>Email</th>';
        echo                '<th>Basé</th>';
        echo                '<th>État</th>';
        echo                '<th></th>';
        echo                '</tr>';
        echo            '</tfoot>';
        echo            '<tbody>';

        foreach ($search_user as $res)
        {
            echo            '<tr>';
            echo                '<td>' . $res["firstname"] . '</td>';
            echo                '<td>' . $res["lastname"] . '</td>';
            echo                '<td>' . $res["email"] . '</td>';
            echo                '<td>' . $res["based"] . '</td>';
            echo                '<td>' . $res["state"] . '</td>';
            echo                '<td><a href="profil.php?id='. $res['id'] . '"><button class="btn btn-warning">Voir le profil</button></a>';
            echo            '<tr>';
        }
        echo            '</tbody>';
        echo        '</table>';
        echo    '</div>';
    }

    exit(0);

}

include "header_admin.php";

?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Utilisateurs</h1>

    <div>

        <div class="search_title">
        <h4 class="h4 text-gray-800 ">Recherche : </h4>
        </div>

        <div class="input-group input-group-sm mb-2 search_bar firstname_search">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inputGroup-sizing-sm">Prénom</span>
            </div>
            <input type="text" class="form-control research_option" >
        </div>

        <div class="input-group input-group-sm mb-2 search_bar lastname_search">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inputGroup-sizing-sm">Nom</span>
            </div>
            <input type="text" class="form-control research_option" >
        </div>

        <div class="input-group input-group-sm mb-2 search_bar email_search">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inputGroup-sizing-sm">Email</span>
            </div>
            <input type="email" class="form-control research_option">
        </div>

        <div class="input-group input-group-sm mb-2 search_bar based_search">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inputGroup-sizing-sm">Basé</span>
            </div>
            <select class="research_option">
                <option value=""></option>
                <option value="Oui">Oui</option>
                <option value="Non">Non</option>
            </select>
        </div>

        <div class="input-group input-group-sm mb-2 search_bar state_search">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inputGroup-sizing-sm">État</span>
            </div>
            <select class="research_option">
                <option value=""></option>
                <option value="Actif">Actif</option>
                <option value="Bloqué">Bloqué</option>
            </select>

        </div>

        <button class="btn btn-primary button_search" onclick="search_user()">Go !</button>

    </div>

</div>
<!-- /.container-fluid -->

<div class="container-fluid" id="block_table">

    <?php

    $search_user = $connect -> query("SELECT id,firstname,lastname,email,based,state FROM user WHERE role = 3 ");
    $count = $search_user -> rowCount();
    $search_user = $search_user -> fetchALL();
    $search_user = integer_to_string($search_user);
    #print_r($search_user);
    if ($count == 0)
    {
        echo    "<div class='no-found '><p>Aucun utilisateur n'a été trouvé</p></div>";
    }
    else
    {
        echo "<div class='table-responsive'>";
        echo        '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
        echo            '<thead>';
        echo                '<tr>';
        echo                '<th>Prénom</th>';
        echo                '<th>Nom</th>';
        echo                '<th>Email</th>';
        echo                '<th>Basé</th>';
        echo                '<th>État</th>';
        echo                '<th></th>';
        echo                '</tr>';
        echo            '</thead>';
        echo            '<tfoot>';
        echo                '<tr>';
        echo                '<th>Prénom</th>';
        echo                '<th>Nom</th>';
        echo                '<th>Email</th>';
        echo                '<th>Basé</th>';
        echo                '<th>État</th>';
        echo                '<th></th>';
        echo                '</tr>';
        echo            '</tfoot>';
        echo            '<tbody>';

        foreach ($search_user as $res)
        {
            echo            '<tr>';
            echo                '<td>' . $res["firstname"] . '</td>';
            echo                '<td>' . $res["lastname"] . '</td>';
            echo                '<td>' . $res["email"] . '</td>';
            echo                '<td>' . $res["based"] . '</td>';
            echo                '<td>' . $res["state"] . '</td>';
            echo                '<td><a href="profil.php?id='. $res['id'] . '"><button class="btn btn-warning">Voir le profil</button></a>';
            echo            '<tr>';
        }
        echo            '</tbody>';
        echo        '</table>';
        echo    '</div>';
    }

    ?>

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
