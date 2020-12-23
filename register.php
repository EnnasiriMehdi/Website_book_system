<?php
session_start();

foreach (glob("utils/*.php") as $filename)
{
    require  $filename ;
}

$connect = connectDb();

if (isset($_POST) && count($_POST) == 9 )
{
    $errors = [];
    //print_r($_POST);
    $check = array("firstname","lastname","email","bth","pwd","conf_pwd","address","city","zip");


    foreach ($check as $field)
    {
        if (empty($_POST[$field]))
        {
            $errors[] = "Vous devez remplir tous les champs !";
            echo json_encode($errors);
            exit(0);
        }
    }

    if(isset($_POST['email']))
    {
        $email = $_POST['email'];
        if(filter_var($email,FILTER_VALIDATE_EMAIL))
        {
            $connect = connectDb();
            $queryPrepared = $connect->prepare("SELECT * FROM user WHERE `email` = :email");
            $queryPrepared->execute([":email" =>$email]);
            $res = $queryPrepared->fetchAll();
            if ($queryPrepared->rowCount() != 0 )
            {
                $errors[] = "L'email saisi existe déjà";
            }
        }
        else
        {
            $errors[] = "L'email saisi est incorrect";
        }
    }

    if(isset($_POST['pwd']))
    {
        $pwd = $_POST['pwd'];
        if(strlen($pwd) < 8 || strlen($pwd) > 64 )
        {
            $errors[] = "Le mot de passe doit faire entre 8 et 63 charactères.";
        }
        if ( !preg_match("#[a-z]#", $pwd) )
        {
            $errors[] = "Le mot de passe doit contenir au moins 1 miniscule.";
        }
        if ( !preg_match("#[A-Z]#", $pwd) )
        {
            $errors[] = "Le mot de passe doit contenir au moins 1 majuscule.";
        }
        if ( !preg_match("#[0-9]#", $pwd))
        {
            $errors[] = "Le mot de passe doit contenir au moins 1 chiffre.";
        }
    }

    if(isset($_POST['pwd']) && isset($_POST['conf_pwd']))
    {
        $pwd = $_POST['pwd'];
        $conf_pwd = $_POST['conf_pwd'];
        if ($pwd != $conf_pwd)
        {
            $errors[] = "La confirmation de mot de passe ne correspond pas au mot de passe.";
        }
    }

    if (isset($_POST['bth']) )
    {
        $bth = $_POST['bth'];
        $birthdayExploded = explode("-", $bth);
        $life = time() - strtotime($bth);
        $yearLife = $life/3600/24/365.242;
        if ( $yearLife < 15 ) {
            $errors[] = "Vous devez avoir plus de 15ans pour vous inscrire";
        }
    }

    if (!empty($errors))
    {
        echo json_encode($errors);
    }
    else
    {
        $hash_pwd = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
        $register = $connect -> prepare("INSERT INTO USER (`firstname`,`lastname`,`email`,`address`,`city`,`zip`,`birthdate`,`based`,`role`,`password`,`state`) VALUES (:firstname,:lastname,:email,:address,:city,:zip,:bth,false,3,:password,1)");
        $register = $register -> execute([":firstname" => $_POST["firstname"],
                                            ":lastname" => $_POST["lastname"],
                                            ":email" => $_POST["email"],
                                            ":address" => $_POST["address"],
                                            ":city" => $_POST["city"],
                                            ":zip" => $_POST["zip"],
                                            ":bth" => $_POST["bth"],
                                            ":password" => $hash_pwd ]);

         echo 1;

    }
    exit(0);
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

  <title>SB Admin 2 - Register</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.css" rel="stylesheet">
  <link href="css/AEN.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

  <div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
      <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
          <div class="col-lg-5 d-none d-lg-block bg-register-image"><img src="images/register.jpg"></div>
          <div class="col-lg-7">
            <div class="p-5">
              <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Création de compte </h1>
              </div>

                <div class="form-group row">
                  <div class="col-sm-6 mb-3 mb-sm-0">
                    <input type="text" class="form-control form-control-user" id="firstname" placeholder="Prénom">
                  </div>
                  <div class="col-sm-6">
                    <input type="text" class="form-control form-control-user" id="lastname" placeholder="Nom">
                  </div>
                </div>
                <div class="form-group">
                  <input type="email" class="form-control form-control-user" id="email" placeholder="Adresse mail">
                </div>
                <div class="form-group">
                  <input type="text" class="form-control form-control-user" id="bth" placeholder="Date de naissance" onfocus="bth_text_to_date()">
                </div>
                <div class="form-group row">
                  <div class="col-sm-6 mb-3 mb-sm-0">
                    <input type="password" class="form-control form-control-user" id="pwd" placeholder="Mot de passe">
                  </div>
                  <div class="col-sm-6">
                    <input type="password" class="form-control form-control-user" id="conf_pwd" placeholder="Confirmation mot de passe">
                  </div>
                </div>
                <div class="form-group">
                  <input type="text" class="form-control form-control-user" id="address" placeholder="Adresse">
                </div>
                <div class="form-group row">
                  <div class="col-sm-6 mb-3 mb-sm-0">
                    <input type="text" class="form-control form-control-user" id="city" placeholder="Ville">
                  </div>
                  <div class="col-sm-6">
                    <input type="text" class="form-control form-control-user" id="zip" placeholder="Code postal">
                  </div>
                </div>
                <button class="btn btn-primary btn-user btn-block" onclick="register()">Créer le compte </button>

                <hr class="error">

                <div id="errors" class="error alert alert-danger ">  </div>

            </div>
          </div>
        </div>
      </div>
    </div>

  </div>



  <!-- Custom scripts for all pages-->
  <script src="js/js_animation.js"></script>
  <script src="js/js_treatment.js"></script>
  <script src="js/datapicker_service/datepicker.min.js"></script>

</body>

</html>
