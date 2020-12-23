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

if (!isset($_SESSION["id"]))
{
    header('location: forbiden_page.php');
}

$connect = connectDb();

if (isset($_POST["valid"]) && isset($_POST["id"]) && count($_POST) == 2)
{
    $key = new user($_SESSION["id"]);
    $key ->valid_order($_POST["id"]);

    exit(0);
}


if (isset($_POST["erase"]) && isset($_POST["id"]) && count($_POST) == 2)
{

    $key = new user($_SESSION["id"]);
    $key ->delete_request($_POST["id"]);

    exit(0);
}

include "header_custo_2.php";

?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div>
        <h1 class="h3 mb-4 text-gray-800 left_block">Mes demandes</h1><div class="right_block col-10"> <button class="button right_block" id="basket_button" onclick="show_create_block()">Panier</button></div>
    </div>


    <div>
        <div class="aeroclub_services col-12">
            <div>
                <?php
                $key = new user($_SESSION["id"]);
                $get_list = $key ->get_resa();

                state_to_string_v3($get_list);

                if (!empty($get_list))
                {
                    echo "<div class='table-responsive'>";
                    echo        '<table class="table table-bordered table_services" id="table_ressources" width="100%" cellspacing="0">';
                    echo            '<thead>';
                    echo                '<tr>';
                  echo                '<th>Numéro de réservation</th>';
                    echo                '<th>Préstation</th>';
                    echo                '<th>Date et heure départ</th>';
                    echo                '<th>Date et heure fin</th>';
                    echo                '</tr>';
                    echo            '</thead>';
                    echo            '<tfoot>';
                    echo                '<tr>';
                    echo                '<th>Numéro de réservation</th>';
                    echo                '<th>Préstation</th>';
                    echo                '<th>Date et heure départ</th>';
                    echo                '<th>Date et heure fin</th>';
                    echo                '</tr>';
                    echo            '</tfoot>';
                    echo            '<tbody>';

                    foreach ($get_list as $res)
                    {
                        echo            '<tr id="tr_'. $res["id"] .'">';
                        echo                '<td class="service_' . $res["id"] . '">' . $res["id"] . '</td>';
                        echo                '<td class="service_' . $res["id"] . '">' . $res["name"] . '</td>';
                        echo                '<td class="service_' . $res["id"] . '">' . date("d-m-Y H:i",strtotime($res["start_date"])) . '</td>';
                        echo                '<td class="service_' . $res["id"] . '">' . date("d-m-Y H:i",strtotime($res["end_date"])) . '</td>';

                        echo            '<tr>';
                    }
                    echo            '</tbody>';
                    echo        '</table>';
                    echo    '</div>';
                }

                //print_r($get_list);

                ?>
            </div>

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
