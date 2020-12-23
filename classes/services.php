<?php

require_once 'classes/fleet.php';

class services
{
    protected $id;
    protected $name;
    protected $description;
    protected $price_DF;
    protected $VAT;
    protected $type;
    protected $id_location;
    protected $state;
    /**
     * services constructor.
     * @param $id
     * @param $name
     * @param $description
     * @param $price_DF
     * @param $VAT
     * @param $type
     * @param $id_fuel
     * @param $id_category
     * @param $id_parking
     * @param $id_location
     * @param $id_landing
     */
    public function __construct($id, $name, $description, $price_DF, $VAT, $type, $id_location, $state)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price_DF = $price_DF;
        $this->VAT = $VAT;
        $this->type = $type;
        $this->id_location = $id_location;
        $this->state = $state;
    }

    public function create()
    {
        if ($this->verify_name_exist() == 0) {
            $connect = connectDb();
            $create = $connect->prepare("INSERT INTO club_services (name,description,price_DF,VAT,type,id_location,state) VALUES (:name,:description,:price_DF,:VAT,:type,:id_location, 0)");
            $create->execute([":name" => $this->name, ":description" => $this->description, ":price_DF" => $this->price_DF, ":VAT" => $this->VAT, ":type" => $this->type, ":id_location" => $this->id_location ]);
            return $create ->debugDumpParams();
        }
        else
        {
            return "La ressources existe déjà";
        }
    }

    public function verify_name_exist()
    {
        $connect = connectDb();
        $verify = $connect ->prepare("SELECT * from club_services where name = :name");
        $verify ->execute([ ":name" => $this->name]);
        return $verify->rowCount();
    }

    public function get_list()
    {
        $connect = connectDb();
        $get_list = $connect -> query("SELECT * FROM club_services ");
        if($get_list->rowCount() != 0)
        {
            $get_list = $get_list->fetchAll();
            $cpt = 0;
            foreach ($get_list as $res)
            {
                $id_fleet = new fleet($res["id_location"],"","","","","","","","","","");
                $id_fleet = $id_fleet ->get_by_id();
                $get_list[$cpt]["type"] = type_services_string($res["type"]);

                if ($get_list[$cpt]["id_location"] !=0 ) {
                    $get_list[$cpt]["id_location"] = $id_fleet["plane_model"];
                }
                else
                {
                    $get_list[$cpt]["id_location"] = "Aucun";
                }
                $cpt++;
            }

            return $get_list;
        }
        else
        {
            return "Aucune ressources n'a été trouvée";
        }

    }

    public function get_by_id()
    {
        $connect = connectDb();
        $get_by_id = $connect ->prepare("SELECT * from club_services where id = :id");
        $get_by_id ->execute([ ":id" => $this->id]);
        if($get_by_id->rowCount() != 0)
        {
            $get_by_id = $get_by_id->fetch();
            $id_fleet = new fleet($get_by_id["id_location"],"","","","","","","","","","");
            $id_fleet = $id_fleet ->get_by_id();
            $get_by_id[5] = type_services_string($get_by_id[5]);
            if ($get_by_id["id_location"] !=0 ) {

                $get_by_id[6] = $id_fleet["plane_model"];
            }
            else
            {
                $get_by_id[6] = "";
                $get_by_id[6] = type_services_string($get_by_id[6]);
            }


            return $get_by_id;
        }
        else
        {
            return "Ressource introuvable !";
        }
    }

    public function change_state()
    {
        $connect = connectDb();
        if ($this->state == 1)
        {
            $change_state = $connect->prepare("UPDATE club_services SET state = 0 WHERE id = :id");
        }
        elseif ($this->state == 0)
        {
            $change_state = $connect->prepare("UPDATE club_services SET state = 1 WHERE id = :id");
        }
        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function delete_ressource()
    {
        $connect = connectDb();

        $change_state = $connect->prepare("DELETE FROM club_services WHERE id = :id");

        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function modif_ressource()
    {
        $connect = connectDb();

        $modif_ressource = $connect->prepare("UPDATE club_services SET name = :name, description = :description, price_DF = :price_DF, VAT = :VAT, type = :type, id_location = :id_location WHERE id = :id");

        $modif_ressource ->execute([ ":id" => $this->id, ":name" => $this->name, ":description" => $this->description, ":price_DF" => $this->price_DF, ":VAT" => $this->VAT, ":type" => $this->type, ":id_location" => $this->id_location ]);
        return $modif_ressource ->debugDumpParams();
    }

    /**
     * @return mixed
     */
    public function getPriceDF()
    {
        return $this->price_DF;
    }

    /**
     * @return mixed
     */
    public function getVAT()
    {
        return $this->VAT;
    }


}
