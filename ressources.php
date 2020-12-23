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


include "header_admin.php";

?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Ressources du Club</h1>

    <div class="col-12">
        <div class="col-6 left_block">
            <div class="text-center">
                <a href="fuel.php"><button class="ressources_button">Carburant</button></a>
            </div>
            <div class="text-center">
                <a href="parking.php"><button class="ressources_button">Parking</button></a>
            </div>
            <div class="text-center">
                <a href="fleet.php"><button class="ressources_button" >Flotte d'avions</button></a>
            </div>
        </div>

        <div class="col-6 right_block" >
            <div class="text-center">
                <a href="landing_fee.php"><button class="ressources_button">Redevances atterissage</button></a>
            </div>
            <div class="text-center">
                <a href="shed.php"><button class="ressources_button">Abris</button></a>
            </div>
            <div class="text-center">
                <a href="category.php"><button class="ressources_button">Catégorie d'abris</button></a>
            </div>
        </div>


    </div>
    <?php
/*
    $shed = new shed(3,"asssa",10,0.2,50,2, true);
    echo $shed ->create();
    //echo "ici : " . $test;

    print_r($shed->get_list());

    echo "<br>";

    $parking = new parking("","test1",10.5,1.2,150,300,true);
    echo $parking ->create();

    print_r($parking->get_list());

    echo "<br>";

    $fuel = new fuel("","test1",10.5,1.2,50,150,true);
    echo $fuel ->create();

    print_r($fuel->get_list());

    echo "<br>";

    $fleet = new fleet("", "test2",50.2,3.3,150,"AZE123AZE",true);
    echo $fleet->create();

    print_r($fleet->get_list());

    echo "<br>";

    $category = new category("", "test1",5,5,5,5,5,5);
    echo $category->create();

    print_r($category->get_list());

    echo "<br>";

    $landing_fee = new landing_fee("","test1",50,2,true,true,1);
    echo $landing_fee->create();

    print_r($landing_fee->get_list());
*/
    ?>


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
