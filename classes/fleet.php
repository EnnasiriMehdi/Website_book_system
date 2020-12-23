<?php


class fleet
{
    protected  $id;
    protected  $plane_model;
    protected  $length;
    protected  $scale;
    protected  $weight;
    protected  $registration;
    protected  $instruction_price;
    protected  $solo_price;
    protected  $school;
    protected  $trip;
    protected  $state;

    /**
     * fleet constructor.
     * @param $id
     * @param $plane_model
     * @param $length
     * @param $scale
     * @param $weight
     * @param $registration
     * @param $state
     */
    public function __construct($id = "", $plane_model, $length, $scale, $weight, $registration, $instruction_price, $solo_price, $school, $trip, $state)
    {
        $this->id = $id;
        $this->plane_model = $plane_model;
        $this->length = $length;
        $this->scale = $scale;
        $this->weight = $weight;
        $this->registration = $registration;
        $this->state = $state;
        $this->instruction_price = $instruction_price;
        $this->solo_price = $solo_price;
        $this->school = $school;
        $this->trip = $trip;
    }

    public function create()
    {
        if ($this->verify_registration_exist() == 0) {
            $connect = connectDb();
            $create = $connect->prepare("INSERT INTO fleet (plane_model,length,scale,weight,registration, instruction_price, solo_price, school, trip, state) VALUES (:plane_model,:length,:scale,:weight,:registration,:instruction_price, :solo_price, :school, :trip,:state)");
            return $create->execute([":plane_model" => $this->plane_model, ":length" => $this->length, ":scale" => $this->scale, ":weight" => $this->weight, ":registration" => $this->registration, ":state" => $this->state, ":instruction_price" => $this->instruction_price, ":solo_price" => $this->solo_price, ":school" => $this->school, ":trip" => $this->trip]);
        }
        else
        {
            return "La ressources existe déjà";
        }
    }

    public function verify_registration_exist()
    {
        $connect = connectDb();
        $verify = $connect ->prepare("SELECT * from fleet where registration = :registration");
        $verify ->execute([ ":registration" => $this->registration]);
        return $verify->rowCount();
    }

    public function get_list()
    {
        $connect = connectDb();
        $get_list = $connect -> query("SELECT * FROM fleet ");
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
        $get_by_id = $connect ->prepare("SELECT * from fleet where id = :id");
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
            $change_state = $connect->prepare("UPDATE fleet SET state = 0 WHERE id = :id");
        }
        elseif ($this->state == 0)
        {
            $change_state = $connect->prepare("UPDATE fleet SET state = 1 WHERE id = :id");
        }
        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function delete_ressource()
    {
        $connect = connectDb();

        $change_state = $connect->prepare("DELETE FROM fleet WHERE id = :id");

        return $change_state ->execute([ ":id" => $this->id]);
    }

    public function modif_ressource()
    {
        $connect = connectDb();

        $modif_ressource = $connect->prepare("UPDATE fleet SET plane_model = :plane_model, length = :length, scale = :scale, weight = :weight, registration = :registration, instruction_price = :instruction_price, solo_price = :solo_price, school = :school, trip = :trip WHERE id = :id");

        return $modif_ressource ->execute([ ":id" => $this->id, ":plane_model" => $this->plane_model, ":length" => $this->length, ":scale" => $this->scale, ":weight" => $this->weight, ":registration" => $this->registration, ":instruction_price" => $this->instruction_price, ":solo_price" => $this->solo_price, ":school" => $this->school, ":trip" => $this->trip ]);
    }

}