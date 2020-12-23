<?php

session_start();

foreach (glob("utils/*.php") as $filename)
{
    require  $filename ;
}

$connect = connectDb();

if (isset($_POST["email"]) && isset($_POST["pwd"]) && count($_POST) == 2)
{
    $log = $connect -> prepare("SELECT * FROM user where `email` = :email");
    $log -> execute([ ":email" => $_POST["email"]]);
    $count = $log ->rowCount();
    $result = $log -> fetch();

    if ($count == 0 )
    {
        echo "L'email n'existe pas ou le mot de passe ne correspond pas !";
        exit(0);
    }
    else
    {
        if (password_verify($_POST["pwd"], $result["password"] ))
        {
            $_SESSION["auth"] = true;
            $_SESSION["email"] = $result["email"];
            $_SESSION["id"] = $result["id"];
            $_SESSION["lastname"] = $result["lastname"];
            $_SESSION["firstname"] = $result["firstname"];
            $_SESSION["birthdate"] = $result["birthdate"];
            $_SESSION["address"] = $result["address"];
            $_SESSION["city"] = $result["city"];
            $_SESSION["zip"] = $result["zip"];

            $_SESSION["plane_type"] = $result["plane_type"];
            $_SESSION["acoustic_group"] = $result["acoustic_group"];

            $_SESSION["length"] = $result["length"];
            $_SESSION["weight"] = $result["weight"];
            $_SESSION["scale"] = $result["scale"];

            $_SESSION["registration"] = $result["registration"];

            $_SESSION["role"] = $result["role"];
            $_SESSION["state"] = $result["state"];
            $_SESSION["based"] = $result["based"];

            echo   $_SESSION["id"] ;

            exit(0);
        }
        else
        {
            echo "L'email n'existe pas ou le mot de passe ne correspond pas !";
            exit(0);
        }
    }
    exit (0);
}



?>



<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>SB Admin 2 - Login</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.css" rel="stylesheet">
  <link href="css/AEN.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block bg-login-image"><img class="bg-login-image" src="images/login.jpg" style="width:120% "></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Je me connecte !</h1>
                  </div>
                    <div class="form-group">
                      <input type="email" class="form-control form-control-user" id="email" aria-describedby="emailHelp" placeholder="Adresse mail">
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" id="pwd" placeholder="Mot de passe">
                    </div>

                    <button  class="btn btn-primary btn-user btn-block" onclick="login()">Connexion</button>

                    <div id="errors" class="error alert alert-danger ">  </div>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="register.php">Creer un compte</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- Custom scripts for all pages-->
  <script src="js/js_animation.js"></script>
  <script src="js/js_treatment.js"></script>

</body>

</html>
