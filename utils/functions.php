<?php

date_default_timezone_set('Europe/Paris');

function connectDb()
{
    try{

        $connect = new PDO(DB_DRIVER.":host=".DB_HOST.";dbname=".DB_NAME.";port=".DB_PORT,DB_USER,DB_PASSWORD,array (PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
    }
    catch(Exception $e){

        die("Erreur SQL " . $e->getMessage());

    }
    return $connect;
}

// integer to string table user

function integer_to_string($array)
{
    $cpt = 0;
    foreach ($array as $res)
    {
        $array[$cpt]['based'] = ($res['based'] == 1) ? 'Oui' : 'Non';
        $array[$cpt]['state'] = ($res['state'] == 1) ? 'Actif' : 'Bloqué';
        $cpt ++;
    }
    return $array;
}

function state_to_string(&$array)
{
    $cpt = 0;

    foreach ($array as $res) {
        $array[$cpt]['state'] = ($res['state'] == 1) ? 'Actif' : 'Bloqué';
        $cpt++;
    }
}

function state_to_string_v3(&$array)
{
    $cpt = 0;

    foreach ($array as $res) {
        $array[$cpt]['state'] = ($res['state'] == 1) ? 'Validée' : 'En attente de validation';
        $cpt++;
    }
}

function state_to_string_v4(&$array)
{
    $cpt = 0;

    foreach ($array as $res) {
        $array[$cpt]['state'] = ($res['state'] == 1) ? 'Payé' : 'En attente de paiement';
        $cpt++;
    }
}

function usage_to_string($array)
{
    $array[8] = ($array[8] == 1) ? 'Oui' : 'Non';
    $array[9] = ($array[9] == 1) ? 'Oui' : 'Non';

    return $array;
}
function state_to_string_v2($array,$index)
{
        $array[$index] = ($array[$index] == 1) ? 'Actif' : 'Bloqué';

    return $array;
}

function integer_to_string_v2($array)
{
    $connect = connectDb();

    $get_role = $connect ->query("SELECT * FROM role");
    $get_role = $get_role -> fetchAll();

    foreach ($get_role as $res)
    {
        if ($array["role"] == $res["id"])
        {
            $array["role"] = $res["name"];
        }
    }


    $array['based'] = ($array['based'] == 1) ? 'basé' : 'non basé';
    $array['state'] = ($array['state'] == 1) ? 'Actif' : 'Bloqué';

    return $array;
}

function get_id_acoustic($array)
{
    $connect = connectDb();
    $get_acoustic_group = $connect -> query("SELECT * FROM acoustic_coef");
    $get_acoustic_group = $get_acoustic_group -> fetchall();
    $cpt = 0;

    foreach ($array as $res)
    {
        for ($i = 0; $i<count($get_acoustic_group); $i++)
        {
            if ($array[13] == $get_acoustic_group[$i][1])
            {
                $array[13] = $get_acoustic_group[$i][0];
                return $array;
            }
        }
        $cpt ++;
    }
    $array[13] = 1;

    return $array;
}

function get_id_plane_type($array)
{
    $connect = connectDb();
    $get_pane_type = $connect -> query("SELECT * FROM plane_type");
    $get_pane_type = $get_pane_type -> fetchall();
    $cpt = 0;

    foreach ($array as $res)
    {
        for ($i = 0; $i<count($get_pane_type); $i++)
        {
            if ($array[8] == $get_pane_type[$i][1])
            {
                $array[8] = $get_pane_type[$i][0];
                return $array;
            }
        }
        $cpt ++;
    }
    $array[8] = 3;

    return $array;
}

function verify_string()
{

}

function verify_email($email,&$errors,$id  = "none")
{
    if(filter_var($email,FILTER_VALIDATE_EMAIL)) {
        if ($id != "none") {

            $connect = connectDb();
            $queryPrepared = $connect->prepare("SELECT email FROM user WHERE `id` = :id");
            $queryPrepared->execute([":id" => $id]);
            if ($res = $queryPrepared->fetch())
            {
                if ($email == $res [0])
                {
                }
                else
                    {
                    $connect = connectDb();
                    $queryPrepared = $connect->prepare("SELECT * FROM user WHERE `email` = :email");
                    $queryPrepared->execute([":email" => $email]);
                    $res = $queryPrepared->fetchAll();
                    if ($queryPrepared->rowCount() != 0)
                    {
                        $errors[] = "L'email saisi existe déjà";
                    }

                }
            }
        }
    }
    else
    {
        $errors[] = "L'email saisi est incorrect";
    }
}

function verify_is_number(&$number, &$errors, $string)
{
    if(!is_numeric($number))
    {
        if ( $number != "")
        {
            $errors[] = $string;
        }
        $number = 0;
    }
}

function get_id_by_get($value)
{
    $id = [];
    $array = str_split($value);
    for ($i = 0; $i < count($array); $i++) {
        if (is_numeric($array[$i])) {
            $id [] = $array[$i];
        }
    }

    return implode($id);
}

function string_to_binary($value)
{
    switch ($value)
    {
        case "Oui" :
            return 1;
        case "Non":
            return 0;
        case "Actif":
            return 1;
        case "Bloqué":
            return 0;
        default:
            return ".*";
    }
}

function type_services_string($value)
{
    switch ($value)
    {
        case "" :
            return "Aucun";
        case 0:
            return "Basique";
        case 1:
            return "AéroClub";
        default:
            return "N/A";
    }
}

function regex_empty_string(&$array)
{
    for ($i = 0; $i < count($array); $i++)
    {
        if (empty($array[$i]))
        {
            $array[$i] = ".*";
        }
    }
}

function verify_open_date($value)
{
    $start_season = mktime(0, 0, 0, 4  , 15 , date("Y", $value) );
    $end_season =   mktime(0, 0, 0, 10  , 15 , date("Y", $value) );

    $year = date("Y", $value);

    $value_y_d_m = date("Y-d-m", $value);

    $easterDate = easter_date($year);
    $easterDay = date('j', $easterDate);
    $easterMonth = date('n', $easterDate);
    $easterYear = date('Y', $easterDate);
    $holidays = array(
        date("Y-d-m",mktime(0, 0, 0, 1, 1, $year)),// 1er janvier
        date("Y-d-m",mktime(0, 0, 0, 5, 1, $year)),// Fete du travail
        date("Y-d-m",mktime(0, 0, 0, 5, 8, $year)),// Victoire des allies
        date("Y-d-m",mktime(0, 0, 0, 7, 14, $year)),// Fete nationale
        date("Y-d-m",mktime(0, 0, 0, 8, 15, $year)),// Assomption
        date("Y-d-m",mktime(0, 0, 0, 11, 1, $year)),// Toussaint
        date("Y-d-m",mktime(0, 0, 0, 11, 11, $year)),// Armistice
        date("Y-d-m",mktime(0, 0, 0, 12, 25, $year)),// Noel

        date("Y-d-m",mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear)),// Lundi de paques
        date("Y-d-m",mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear)),// Ascension
        date("Y-d-m",mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear)), // Pentecote
    );

    foreach ($holidays as $res)
    {
        if ($value_y_d_m == $res)
        {
            return "true";
        }
    }

    $value_str = date("l", $value);
    $value_str = strtolower($value_str);

    if ( $value < $start_season || $value > $end_season )
    {
        if(($value_str == "saturday" ) || ($value_str == "sunday"))
        {
            return "true";
        }
        else
        {
        return "false";
        }
    }


    return "true";

}