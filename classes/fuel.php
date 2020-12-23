<?php


class fuel
{
    protected  $id;
    protected  $name;
    protected  $price_DF;
    protected  $VAT;
    protected  $actual_stock;
    protected  $max_stock;
    protected  $state;

    /**
     * fuel constructor.
     * @param $id
     * @param $name
     * @param $price_DF
     * @param $VAT
     * @param $actual_stock
     * @param $max_stock
     * @param $state
     */
    public function __construct($id = "", $name, $price_DF, $VAT, $actual_stock, $max_stock, $state)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price_DF = $price_DF;
        $this->VAT = $VAT;
        $this->actual_stock = $actual_stock;
        $this->max_stock = $max_stock;
        $this->state = $state;
    }



    public function create()
    {
        if ($this->verify_name_exist() == 0) {
            $connect = connectDb();
            $create = $connect->prepare("INSERT INTO fuel (name,price_DF,VAT,actual_stock,max_stock,state) VALUES (:name,:price_DF,:VAT,:actual_stock,:max_stock,:state)");
            return $create->execute([":name" => $this->name, ":price_DF" => $this->price_DF, ":VAT" => $this->VAT, ":actual_stock" => $this->actual_stock, ":max_stock" => $this->max_stock, ":state" => $this->state]);
        }
        else
        {
            return "La ressources existe déjà";
        }
    }

    public function verify_name_exist()
    {
        $connect = connectDb();
        $verify = $connect ->prepare("SELECT * from fuel where name = :name");
        $verify ->execute([ ":name" => $this->name]);
        return $verify->rowCount();
    }

    public function get_list()
    {
        $connect = connectDb();
        $get_list = $connect -> query("SELECT * FROM fuel ");
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
        $get_by_id = $connect ->prepare("SELECT * from fuel where id = :id");
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
            $change_state = $connect->prepare("UPDATE fuel SET state = 0 WHERE id = :id");
        }
        elseif ($this->state == 0)
        {
            $change_state = $connect->prepare("UPDATE fuel SET state = 1 WHERE id = :id");
        }
        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function delete_ressource()
    {
        $connect = connectDb();

        $change_state = $connect->prepare("DELETE FROM fuel WHERE id = :id");

        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function modif_ressource()
    {
        $connect = connectDb();

        $modif_ressource = $connect->prepare("UPDATE fuel SET name = :name, price_DF = :price_DF, VAT = :VAT, actual_stock = :actual_stock, max_stock = :max_stock WHERE id = :id");
        return $modif_ressource ->execute([ ":id" => $this->id, ":name" => $this->name, ":price_DF" => $this->price_DF, ":VAT" => $this->VAT, ":actual_stock" => $this->actual_stock, ":max_stock" => $this->max_stock ]);
    }
}