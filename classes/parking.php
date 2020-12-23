<?php




class parking
{
    protected $id;
    protected $name;
    protected $price_DF;
    protected $VAT;
    protected $available_area;
    protected $max_area;
    protected $state;


    /**
     * parking constructor.
     * @param $id
     * @param $name
     * @param $price_DF
     * @param $VAT
     * @param $available_area
     * @param $max_area
     * @param $state
     */
    public function __construct($id="", $name, $price_DF, $VAT, $available_area, $max_area, $state)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price_DF = $price_DF;
        $this->VAT = $price_DF * 0.2;
        $this->available_area = $available_area;
        $this->max_area = $max_area;
        $this->state = $state;
    }

    public function create()
    {
        if ($this->verify_name_exist() == 0) {
            $connect = connectDb();
            $create = $connect->prepare("INSERT INTO parking (name,price_DF,VAT,available_area,max_area,state) VALUES (:name,:price_DF,:VAT,:available_area,:max_area,:state)");
            return $create->execute([":name" => $this->name, ":price_DF" => $this->price_DF, ":VAT" => $this->VAT, ":available_area" => $this->available_area, ":max_area" => $this->max_area, ":state" => $this->state]);
        }
        else
        {
            return "La ressources existe déjà";
        }
    }

    public function verify_name_exist()
    {
        $connect = connectDb();
        $verify = $connect ->prepare("SELECT * from parking where name = :name");
        $verify ->execute([ ":name" => $this->name]);
        return $verify->rowCount();
    }

    public function get_list()
    {
        $connect = connectDb();
        $get_list = $connect -> query("SELECT * FROM parking ");
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
        $get_by_id = $connect ->prepare("SELECT * from parking where id = :id");
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
            $change_state = $connect->prepare("UPDATE parking SET state = 0 WHERE id = :id");
        }
        elseif ($this->state == 0)
        {
            $change_state = $connect->prepare("UPDATE parking SET state = 1 WHERE id = :id");
        }
        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function delete_ressource()
    {
        $connect = connectDb();

        $change_state = $connect->prepare("DELETE FROM parking WHERE id = :id");

        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function modif_ressource()
    {
        $connect = connectDb();

        $modif_ressource = $connect->prepare("UPDATE parking SET name = :name, price_DF = :price_DF, VAT = :VAT, available_area = :available_area, max_area = :max_area WHERE id = :id");
        return $modif_ressource ->execute([ ":id" => $this->id, ":name" => $this->name, ":price_DF" => $this->price_DF, ":VAT" => $this->VAT, ":available_area" => $this->available_area, ":max_area" => $this->max_area ]);
    }
}