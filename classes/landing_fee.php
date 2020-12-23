<?php


class landing_fee
{
    protected  $id;
    protected  $name;
    protected  $price_DF;
    protected  $VAT;
    protected  $based;
    protected  $week;
    protected  $id_plane_type;

    /**
     * landing_fee constructor.
     * @param $id
     * @param $name
     * @param $price_DF
     * @param $VAT
     * @param $based
     * @param $week
     * @param $id_plane_type
     */
    public function __construct($id = "", $name, $price_DF, $VAT, $based, $week, $id_plane_type)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price_DF = $price_DF;
        $this->VAT = $price_DF * 0.2;
        $this->based = $based;
        $this->week = $week;
        $this->id_plane_type = $id_plane_type;
    }

    public function create()
    {
        if ($this->verify_name_exist() == 0) {
            $connect = connectDb();
            $create = $connect->prepare("INSERT INTO landing_fee (name,price_DF,VAT,based,week,id_plane_type) VALUES (:name,:price_DF,:VAT,:based,:week,:id_plane_type)");
            return $create->execute([":name" => $this->name, ":price_DF" => $this->price_DF, ":VAT" => $this->VAT, ":based" => $this->based, ":week" => $this->week, ":id_plane_type" => $this->id_plane_type]);
        }
        else
        {
            return "La ressources existe déjà";
        }
    }

    public function verify_name_exist()
    {
        $connect = connectDb();
        $verify = $connect ->prepare("SELECT * from landing_fee where name = :name");
        $verify ->execute([ ":name" => $this->name]);
        return $verify->rowCount();
    }

    public function get_list()
    {
        $connect = connectDb();
        $get_list = $connect -> query("SELECT * FROM landing_fee ");
        if($get_list->rowCount() != 0)
        {
            $get_list = $get_list->fetchAll();
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
        $get_by_id = $connect ->prepare("SELECT * from landing_fee where id = :id");
        $get_by_id ->execute([ ":id" => $this->id]);
        if($get_by_id->rowCount() != 0)
        {
            $get_by_id = $get_by_id->fetch();

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
            $change_state = $connect->prepare("UPDATE landing_fee SET state = 0 WHERE id = :id");
        }
        elseif ($this->state == 0)
        {
            $change_state = $connect->prepare("UPDATE landing_fee SET state = 1 WHERE id = :id");
        }
        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function delete_ressource()
    {
        $connect = connectDb();

        $change_state = $connect->prepare("DELETE FROM landing_fee WHERE id = :id");

        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function modif_ressource()
    {
        $connect = connectDb();

        $modif_ressource = $connect->prepare("UPDATE landing_fee SET name = :name, price_DF = :price_DF, VAT = :VAT, based = :based, week = :week, id_plane_type = :id_plane_type WHERE id = :id");

        return $modif_ressource ->execute([ ":id" => $this->id, ":name" => $this->name, ":price_DF" => $this->price_DF, ":VAT" => $this->VAT, ":based" => $this->based, ":week" => $this->week, ":id_plane_type" => $this->id_plane_type ]);
    }
}
