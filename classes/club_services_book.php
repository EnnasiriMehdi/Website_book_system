<?php


class club_services_book
{
    protected $id;
    protected $name;
    protected $start_date;
    protected $end_date;
    protected $state;
    protected $id_lesson;
    protected $id_former;
    protected $id_custo;
    protected $id_services;

    /**
     * club_services_book constructor.
     * @param $id
     * @param $name
     * @param $start_date
     * @param $end_date
     * @param $state
     * @param $id_lesson
     * @param $id_former
     * @param $id_custo
     * @param $id_services
     */
    public function __construct($id, $name, $start_date, $end_date, $state, $id_lesson, $id_former, $id_custo, $id_services)
    {
        $this->id = $id;
        $this->name = $name;
        $this->start_date =$start_date;
        $this->end_date = $end_date;
        $this->state = $state;
        $this->id_lesson = $id_lesson;
        $this->id_former = $id_former;
        $this->id_custo = $id_custo;
        $this->id_services = $id_services;
    }

    public function create()
    {
        $connect = connectDb();
        $create = $connect ->prepare("INSERT INTO club_services_book (name,start_date,end_date,state,id_lesson,id_former,id_custo,id_services) VALUES (:name,:start_date,:end_date,:state,:id_lesson,:id_former,:id_custo,:id_services)");
        $create ->execute([ ":name" => $this->name, ":start_date" => $this->start_date, ":end_date" => $this->end_date, ":state" => 1, ":id_lesson" => $this->id_lesson, ":id_former" => $this->id_former, ":id_custo" => $this->id_custo,":id_services"=>$this->id_services]);
        return $connect ->lastInsertId();
    }

    public function get_list()
    {
        $connect = connectDb();
        $get_list = $connect ->query("SELECT * FROM club_services_book WHERE state = 1 ORDER BY start_date ASC");
        $get_list = $get_list ->fetchAll();
        return $get_list;
    }

    public function get_by_id()
    {
        $connect = connectDb();
        $get_by_id = $connect ->query("SELECT * FROM club_services_book WHERE id =". $this->id);
        $get_by_id = $get_by_id ->fetchAll();
        return $get_by_id;
    }

    public function get_list_by_services()
    {
        $connect = connectDb();
        $get_list = $connect ->prepare("SELECT * FROM club_services_book WHERE state = 1 AND id_services = :id ORDER BY start_date ASC ");
        $get_list->execute([ ":id" => $this->id_services]);
        $get_list = $get_list ->fetchAll();
        return $get_list;
    }

    public function get_list_availables_hours()
    {
        $start = date("Y-m-d H:i:s",$this->start_date);
        $end = date("Y-m-d H:i:s",$this->end_date);

        $connect = connectDb();
        $get_list = $connect ->prepare("SELECT * FROM club_services_book WHERE state = 1 AND id_services = :id AND start_date between :start_date AND :end_date ORDER BY start_date ASC ");
        $get_list->execute([ ":id" => $this->id_services , ":start_date" => $start,":end_date" =>$end]);
        $get_list = $get_list ->fetchAll();
        return $get_list;
    }

    public function add_order_line($id,$duration)
    {

        $connect = connectDb();
        $get_service = new services($this->id_services,"","","","","","","");
        $get_service = $get_service ->get_by_id();

        $this->setName($get_service["name"]);

        $id_book = $this->create();

        $create_line = $connect ->prepare("INSERT INTO request_line (name, start_date, end_date, quantity, price_DF, VAT, id_request, id_book) VALUES (:name, :start_date, :end_date, :quantity, :price_DF, :VAT, :id_request, :id_book)");
        $create_line ->execute([ ":name" => $get_service["name"], ":start_date" =>$this->start_date , ":end_date" => $this->end_date,
           ":quantity" => $duration, ":price_DF" => $get_service["price_DF"] , ":VAT" => $get_service["VAT"] ,
           ":id_request" => $id , ":id_book" => $id_book]);


    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }











}