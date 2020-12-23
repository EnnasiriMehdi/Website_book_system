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

$HOURS = [9,10,11,12,13,14,15,16,17,18,19];

$connect = connectDb();

if (!isset($_SESSION["id"]))
{
    header('location: forbiden_page.php');
}


if (isset($_POST["update_request"]) && count($_POST) == 1)
{
    $today = date("Y-m-d");
    $get_request = $connect ->prepare("SELECT * FROM request_line WHERE start_date >= :date");
    $get_request ->execute([ ":date" => $today]);
    echo $get_request->debugDumpParams();
    $get_request = $get_request->fetchAll();



    for ($i = 0; $i < count($get_request); $i++)
    {
        $get_request_state = $connect ->prepare("SELECT state FROM request WHERE id >= :id");
        $get_request_state ->execute([ ":date" =>  $get_request[$i]["id_request"]]);
        $get_request_state ->fetch();

        $test = strtotime($get_request[$i]["start_date"]) - strtotime($today);
        echo $test;
        if ($test < 86400 && $get_request[0] !=0)
        {
            $delete_book = $connect ->prepare("DELETE FROM club_services_book where id = :id");
            $delete_book ->execute([ ":id" => $get_request[$i]["id_book"]]);

            $value = $get_request[$i]["quantity"] * ($get_request[$i]["price_df"] + $get_request[$i]["VAT"]);
            $update_request = $connect ->prepare("UPDATE request SET amount = amount - :value WHERE id = :id");
            $update_request ->execute([ ":id" => $get_request[$i]["id_request"], ":value" => $value]);

            $delete_request_line = $connect ->prepare("DELETE FROM request_line where id = :id");
            $delete_request_line ->execute([ ":id" => $get_request[$i]["id"]]);
        }




    }


    exit(0);
}



if (isset($_POST["encode"]) && count($_POST) == 1 )
{
    $values = json_decode($_POST["encode"]);
    print_r($values);
    $today = $today = date("Y-m-d H:i:s");

    $get_id_order = $connect ->prepare("INSERT INTO request (date, state,id_custo) VALUES ('". $today . "',0,:id)");
    $get_id_order->execute([ ":id" => $_SESSION["id"]]);

    $get_id_order = $connect -> lastInsertId();

    for ($i = 0 ; $i < count($values); $i++)
    {
        $start = mktime(intval($values[$i][2]), 0, 0, date("m", strtotime($values[$i][1])), date("d", strtotime($values[$i][1])), date("y", strtotime($values[$i][1])));
        $end = mktime(intval($values[$i][2]) + $values[$i][3], 0, 0, date("m", strtotime($values[$i][1])), date("d", strtotime($values[$i][1])), date("y", strtotime($values[$i][1])));
        $start = date("Y-m-d H:i:s", $start);
        $end = date("Y-m-d H:i:s", $end);

        $create_line = new club_services_book("", "", $start, $end, "1", 0, 0, $_SESSION["id"], $values[$i][0]);

        $create_line->add_order_line($get_id_order, $values[$i][3]);
    }

    $get_price = $connect -> query("SELECT price_DF,quantity FROM request_line WHERE id_request =".$get_id_order );

    $get_price = $get_price ->fetchAll();
    $amount = 0;
    for($i = 0; $i<count($get_price); $i++)
    {
        $amount = $get_price[$i][0] * $get_price[$i][1];
    }
    $amount = $amount *1.2;

    $set_amount = $connect ->query("UPDATE request SET amount =" . $amount ." WHERE id =". $get_id_order );

    exit(0);
}

if (isset($_POST["get_date"]) && isset($_POST["id"]) && count($_POST) == 2)
{
    $limit_date  = mktime(0, 0, 0, date("m")  , date("d")+30, date("Y"));
    $today = date("Y-m-d");

    $limit_date = date("Y-m-d H:i:s",$limit_date);
    $key = new club_services_book("","","","","","","","",$_POST["id"]);
    $get_list = $key->get_list_by_services();
    $available = [];
    $available_bis = [];

    while (strtotime($today) != strtotime($limit_date))
    {
        if(verify_open_date(strtotime($today)) == "true")
        {
            foreach ($get_list as $res)
            {
                $date = date("Y-m-d",strtotime($res["start_date"]));
                $start =  date("H",strtotime($res["start_date"]));
                $end = date("H",strtotime($res["end_date"]));
                $cpt = 0;
                for ($i = 0; $i<count($HOURS); $i++)
                {
                    if ($HOURS[$i] >= $start && $HOURS[$i] < $end && $today == $date )
                    {
                            for ($j=0; $j<count($available_bis);$j++)
                            {
                                if (isset($available_bis[$j]) && $available_bis[$j] == $HOURS[$i])
                                {
                                    unset($available_bis[$j]);
                                }
                            }
                    }
                    else
                    {
                        $available_bis [] = $HOURS[$i];
                    }
                }
            }
            if (count($available_bis) !=0)
            {
                $available[] = date("d-m-Y", strtotime($today));
            }
            elseif (count($get_list) == 0)
            {
                $available[] = date("d-m-Y", strtotime($today));
            }
        }
        $today = date("Y-m-d", strtotime( $today) + 86400);

        //$available_bis = [];
    }

    echo json_encode($available);

    exit(0);
}

if (isset($_POST["get_hours"]) && isset($_POST["id"]) && count($_POST) == 2)
{
    $today = $_POST["get_hours"];
    $start = mktime(0,0,0,date("m",strtotime($_POST["get_hours"])),date("d",strtotime($_POST["get_hours"])),date("y",strtotime($_POST["get_hours"])));
    $end = mktime(23,59,59,date("m",strtotime($_POST["get_hours"])),date("d",strtotime($_POST["get_hours"])),date("y",strtotime($_POST["get_hours"])));

    $key = new club_services_book("","",$start,$end,"","","","",$_POST["id"]);
    $get_list = $key->get_list_availables_hours();

    $available = [];
    $available_bis = [];

        if(verify_open_date(strtotime($today)) == "true")
        {
            foreach ($get_list as $res)
            {
                $date = date("Y-m-d",strtotime($res["start_date"]));
                $start =  date("H",strtotime($res["start_date"]));
                $end = date("H",strtotime($res["end_date"]));
                $cpt = 0;
                for ($i = 0; $i<count($HOURS); $i++)
                {
                    if ($HOURS[$i] >= $start && $HOURS[$i] < $end && $today == $date )
                    {
                        $available_bis [] = $HOURS[$i];
                    }
                }
            }

        }
        for ($i = 0; $i < count($HOURS);$i ++)
        {
            if (!in_array($HOURS[$i],$available_bis))
            {
                $available [] = $HOURS[$i];
            }
        }

        if (count($available) == 0)
        {
            echo "null";
        }
        else
        {
            echo json_encode($available);
        }
    exit(0);
}

if($_SESSION["role"] === 3)
{
    include "header_admin.php";
}
else
{
    include "header_custo_2.php";
}


?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div>
        <h1 class="h3 mb-4 text-gray-800 left_block">Service du Club</h1><div class="right_block col-10"> <button class="button right_block" id="basket_button" onclick="show_create_block()">Panier</button></div>
    </div>

    <div>
        <div class="aeroclub_services col-12">
            <div>
                <?php
                $key = new services("","","50","","","","","",);
                $get_list = $key ->get_list();

                state_to_string($get_list);

                if (!empty($get_list))
                {
                    echo "<div class='table-responsive'>";
                    echo        '<table class="table table-bordered table_services" id="table_ressources" width="100%" cellspacing="0">';
                    echo            '<thead>';
                    echo                '<tr>';
                    echo                '<th></th>';
                    echo                '<th>Service</th>';
                    echo                '<th>Description</th>';
                    echo                '<th>Prix HT</th>';
                    echo                '<th>TVA</th>';
                    echo                '</tr>';
                    echo            '</thead>';
                    echo            '<tfoot>';
                    echo                '<tr>';
                    echo                '<th></th>';
                    echo                '<th>Service</th>';
                    echo                '<th>Description</th>';
                    echo                '<th>Prix HT</th>';
                    echo                '<th>TVA</th>';
                    echo                '</tr>';
                    echo            '</tfoot>';
                    echo            '<tbody>';

                    foreach ($get_list as $res)
                    {
                        echo            '<tr id="tr_'. $res["id"] .'">';
                        echo                '<td><input style="width: 20px" type="checkbox" id ="checkbox_services_' . $res["id"] . '" class="aeroclub_services" onclick="add_to_basket('. $res["id"] .')"></td>';
                        echo                '<td class="service_' . $res["id"] . '">' . $res["name"] . '</td>';
                        echo                '<td class="service_' . $res["id"] . '">' . $res["description"] . '</td>';
                        echo                '<td class="service_' . $res["id"] . '">' . $res["price_DF"] . '</td>';
                        echo                '<td class="service_' . $res["id"] . '">' . $res["VAT"] . '</td>';
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

        <div id="basket_block" class="col-8">

            <div class="text-center title h5"> PANIER </div>

            <div>
                <table class="table table-bordered" id="basket_table">
                        <tbody class="table table-bordered table_services" id="table_ressources" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Service</th>
                                <th>Prix HT (par heure)</th>
                                <th>TVA</th>
                                <th>Date</th>
                                <th>Horaire</th>
                                <th>Durée</th>
                                <th>Retirer</th>
                            </tr>
                            </thead>
                        </tbody>
                </table>



            </div>

            <div class="text-center"> <div>Montant : <div id="amount_basket" style="display: inline-block">  €</div></div> </div>
            <div class="text-center "> <button class="button" onclick="valid_order()">Valider la demande</button> </div>
            <!-- <div class="text-center"> <button class="button"> Fermer  </button> </div> -->

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
