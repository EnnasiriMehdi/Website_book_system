<?php


class category
{
    protected  $id;
    protected  $name;
    protected  $P_M_B_DF;
    protected  $VAT_M_B;
    protected  $P_D_B_DF;
    protected  $VAT_D_B;
    protected  $P_D_NB_DF;
    protected  $VAT_D_NB;

    /**
     * category constructor.
     * @param int $id
     * @param string $name
     * @param float $P_M_B_DF
     * @param float $VAT_M_B
     * @param float $P_D_B_DF
     * @param float $VAT_D_B
     * @param float $P_D_NB_DF
     * @param float $VAT_D_NB
     */
    public function __construct($id = "", $name, $P_M_B_DF, $VAT_M_B, $P_D_B_DF, $VAT_D_B, $P_D_NB_DF, $VAT_D_NB)
    {
        $this->id = $id;
        $this->name = $name;
        $this->P_M_B_DF = $P_M_B_DF;
        $this->VAT_M_B = $VAT_M_B;
        $this->P_D_B_DF = $P_D_B_DF;
        $this->VAT_D_B = $VAT_D_B;
        $this->P_D_NB_DF = $P_D_NB_DF;
        $this->VAT_D_NB = $VAT_D_NB;
    }

    public function create()
    {
        if ($this->verify_name_exist() == 0) {
            $connect = connectDb();
            $create = $connect->prepare("INSERT INTO category (name,P_M_B_DF,VAT_M_B,P_D_B_DF,VAT_D_B,P_D_NB_DF,VAT_D_NB) VALUES (:name,:P_M_B_DF,:VAT_M_B,:P_D_B_DF,:VAT_D_B,:P_D_NB_DF,:VAT_D_NB)");
            return $create->execute([":name" => $this->name, ":P_M_B_DF" => $this->P_M_B_DF, ":VAT_M_B" => $this->VAT_M_B, ":P_D_B_DF" => $this->P_D_B_DF, ":VAT_D_B" => $this->VAT_D_B, ":P_D_NB_DF" => $this->P_D_NB_DF, ":VAT_D_NB" => $this->VAT_D_NB]);
        }
        else
        {
            return "La ressources existe déjà";
        }
    }

    public function verify_name_exist()
    {
        $connect = connectDb();
        $verify = $connect ->prepare("SELECT * from category where name = :name");
        $verify ->execute([ ":name" => $this->name]);
        return $verify->rowCount();
    }

    public function get_list()
    {
        $connect = connectDb();
        $get_list = $connect -> query("SELECT * FROM category ");
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
        $get_by_id = $connect ->prepare("SELECT * from category where id = :id");
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
            $change_state = $connect->prepare("UPDATE category SET state = 0 WHERE id = :id");
        }
        elseif ($this->state == 0)
        {
            $change_state = $connect->prepare("UPDATE category SET state = 1 WHERE id = :id");
        }
        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function delete_ressource()
    {
        $connect = connectDb();

        $change_state = $connect->prepare("DELETE FROM category WHERE id = :id");

        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function modif_ressource()
    {
        $connect = connectDb();

        $modif_ressource = $connect->prepare("UPDATE category SET name = :name, P_M_B_DF = :P_M_B_DF, VAT_M_B = :VAT_M_B, P_D_B_DF = :P_D_B_DF, VAT_D_B = :VAT_D_B, P_D_NB_DF = :P_D_NB_DF, VAT_D_NB = :VAT_D_NB WHERE id = :id");
        return $modif_ressource ->execute([ ":id" => $this->id, ":name" => $this->name, ":P_M_B_DF" => $this->P_M_B_DF, ":VAT_M_B" => $this->VAT_M_B, ":P_D_B_DF" => $this->P_D_B_DF, ":VAT_D_B" => $this->VAT_D_B, ":P_D_NB_DF" => $this->P_D_NB_DF, ":VAT_D_NB" => $this->VAT_D_NB ]);
    }
}