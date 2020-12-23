<?php


class shed
{
    protected  $id;
    protected  $name;
    protected  $length;
    protected  $scale;
    protected  $max_weight;
    protected  $id_category;
    protected  $state;

    /**
     * shed constructor.
     * @param int $id
     * @param string $name
     * @param float $length
     * @param float $scale
     * @param float $max_weight
     * @param float $id_category
     */
    public function __construct( $id = "",  $name,  $length,  $scale,  $max_weight,  $id_category, $state)
    {
        $this->id = $id;
        $this->name = $name;
        $this->length = $length;
        $this->scale = $scale;
        $this->max_weight = $max_weight;
        $this->id_category = $id_category;
        $this->state = $state;
    }

    public function create()
    {
        if ($this->verify_name_exist() == 0) {
            $connect = connectDb();
            $create = $connect->prepare("INSERT INTO shed (name,length,scale,max_weight,state,id_category) VALUES (:name,:length,:scale,:max_weight,:state,:id_category)");

            return $create->execute([":name" => $this->name, ":length" => $this->length, ":scale" => $this->scale, ":max_weight" => $this->max_weight, ":state" => $this->state, ":id_category" => $this->id_category]);
        }
        else
        {
            return "La ressources existe déjà";
        }
    }

    public function verify_name_exist()
    {
        $connect = connectDb();
        $verify = $connect ->prepare("SELECT * from shed where name = :name");
        $verify ->execute([ ":name" => $this->name]);
        return $verify->rowCount();
    }

    public function get_list()
    {
        $connect = connectDb();
        $get_list = $connect -> query("SELECT * FROM shed ");
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
        $get_by_id = $connect ->prepare("SELECT * from shed where id = :id");
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
            $change_state = $connect->prepare("UPDATE shed SET state = 0 WHERE id = :id");
        }
        elseif ($this->state == 0)
        {
            $change_state = $connect->prepare("UPDATE shed SET state = 1 WHERE id = :id");
        }
        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function delete_ressource()
    {
        $connect = connectDb();

        $change_state = $connect->prepare("DELETE FROM shed WHERE id = :id");

        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function modif_ressource()
    {
        $connect = connectDb();

        $modif_ressource = $connect->prepare("UPDATE shed SET name = :name, length = :length, scale = :scale, max_weight = :max_weight, id_category = :id_category WHERE id = :id");
        return $modif_ressource ->execute([ ":id" => $this->id, ":name" => $this->name, ":length" => $this->length, ":scale" => $this->scale, ":max_weight" => $this->max_weight, ":id_category" => $this->id_category ]);
    }
}