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

if (isset($_POST["pay"]) && isset($_POST["id"]) && count($_POST) == 2)
{
    $key = new user($_SESSION["id"]);
    $key ->pay_invoice($_POST["id"]);

    exit(0);
}


include "header_custo_2.php";

?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div>
        <h1 class="h3 mb-4 text-gray-800 left_block">Mes factures</h1><div class="right_block col-10"> <button class="button right_block" id="basket_button" onclick="show_create_block()">Panier</button></div>
    </div>


    <div>
        <div class="aeroclub_services col-12">
            <div>
                <?php
                $key = new user($_SESSION["id"]);
                $get_list = $key ->get_invoice();

                state_to_string_v4($get_list);

                if (!empty($get_list))
                {
                    echo "<div class='table-responsive'>";
                    echo        '<table class="table table-bordered table_services" id="table_ressources" width="100%" cellspacing="0">';
                    echo            '<thead>';
                    echo                '<tr>';
                    echo                '<th>Numéro de demande</th>';
                    echo                '<th>Date</th>';
                    echo                '<th>Montant TTC</th>';
                    echo                '<th>État</th>';
                    echo                '<th></th>';
                    echo                '</tr>';
                    echo            '</thead>';
                    echo            '<tfoot>';
                    echo                '<tr>';
                    echo                '<th>Numéro de demande</th>';
                    echo                '<th>Date</th>';
                    echo                '<th>Montant TTC</th>';
                    echo                '<th>État</th>';
                    echo                '<th></th>';
                    echo                '</tr>';
                    echo            '</tfoot>';
                    echo            '<tbody>';

                    foreach ($get_list as $res)
                    {
                        echo            '<tr id="tr_'. $res["id"] .'">';
                        echo                '<td class="service_' . $res["id"] . '">' . $res["id"] . '</td>';
                        echo                '<td class="service_' . $res["id"] . '">' . date("d-m-Y",strtotime($res["date"])) . '</td>';
                        echo                '<td class="service_' . $res["id"] . '">' . $res["amount"] . '</td>';
                        echo                '<td class="service_' . $res["id"] . '">' . $res["state"] . '</td>';
                        if($res["state"] == "Payé")
                        {
                            echo    "<td><a href='invoice_pdf.php?id=". $res["id"]."' class='btn btn-info'>Facture</a></td>";
                        }
                        else
                        {
                            echo            '<td> <button class="button" onclick="pay_invoice('. $res["id"] .')">Payer</button> </td>';
                        }
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
