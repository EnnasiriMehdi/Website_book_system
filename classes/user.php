<?php


class user
{
    protected $id;

    /**
     * user constructor.
     * @param $key
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function get_order()
    {
        $connect = connectDb();
        $get_order = $connect ->query("SELECT * FROM request WHERE id_custo =". $this->id);
        $get_order = $get_order->fetchAll();

        return $get_order;
    }

    public function get_resa()
    {
        $connect = connectDb();
        $get_order = $connect ->query("SELECT * FROM club_services_book WHERE state = 1 AND id_custo =". $this->id);
        $get_order = $get_order->fetchAll();

        return $get_order;
    }

    public function get_invoice()
    {
        $connect = connectDb();
        $get_order = $connect ->query("SELECT * FROM invoice WHERE id_custo =". $this->id);
        $get_order = $get_order->fetchAll();

        return $get_order;
    }

    public function delete_request($id_bis)
    {
        $connect = connectDb();

        $get_book_ids = $connect ->prepare("SELECT id_book FROM request_line WHERE id_request = :id");
        $get_book_ids ->execute([ ":id" => $id_bis]);
        $get_book_ids = $get_book_ids ->fetchAll();

        for ($i = 0; $i < count($get_book_ids); $i ++)
        {
            $delete_book = $connect ->query("DELETE FROM club_services_book WHERE id =".$get_book_ids[$i][0]);

        }

        $delete_request_line = $connect ->prepare("DELETE FROM request_line WHERE id_request=:id");
        echo $delete_request_line ->execute([ ":id" => $id_bis]);


        $delete_request = $connect ->prepare("DELETE FROM request WHERE id = :id");
        echo $delete_request->execute([ ":id" => $id_bis]);


    }

    public function valid_order($id_bis)
    {
        $connect = connectDb();
        $valid_order = $connect ->prepare("UPDATE request SET state = 1 WHERE id = :id_bis ");
        $valid_order ->execute([ ":id_bis" => $id_bis]);

        $this->get_line_order($id_bis);
    }

    private function get_line_order($id_bis)
    {
        $connect = connectDb();
        $get_list = $connect ->prepare("SELECT * FROM request_line WHERE id_request = :id");
        $get_list ->execute([":id" => $id_bis]);

        $this->create_invoice($get_list->fetchAll());
    }

    private function create_invoice($array)
    {

        $today = $today = date("Y-m-d");
        $connect = connectDb();
        $create_invoice = $connect ->prepare("INSERT INTO invoice (date, state,id_custo) VALUES ('". $today . "',0,:id)");

        $create_invoice->execute([ ":id" => $_SESSION["id"]]);


        $last_invoice = $connect -> lastInsertId();

        $key = new club_services_book("","","","","","","","","");

        $key ->setId($array[0]["id_book"]);
        $id_service = $key ->get_by_id();

        $amount = 0;

        for ( $i = 0; $i < count($array); $i++)
        {
            $key ->setId($array[$i]["id_book"]);
            $id_service = $key ->get_by_id();

            $create_invoice_line = $connect ->prepare("INSERT INTO invoice_line (name, start_date, end_date,quantity,price_DF, VAT, invoice, service) VALUES (:name, :start_date, :end_date,:quantity,:price_DF, :VAT, :invoice, :service)");
            $create_invoice_line->execute([ ":name" => $array[$i]["name"], ":start_date" => $array[$i]["start_date"], ":end_date" =>$array[$i]["end_date"] , ":quantity" => $array[$i]["quantity"] , ":price_DF" => $array[$i]["price_df"] , ":VAT" => $array[$i]["VAT"] , ":invoice" => $last_invoice , ":service"=> $id_service[0]["id_services"]]);

            $amount = $amount + $array[$i]["price_df"]*$array[$i]["quantity"];
        }

        $amount = $amount*1.2;

        $set_invoice_amount = $connect->query("UPDATE invoice SET amount =". $amount ." WHERE id=".$last_invoice);

        echo 1;
    }

    public function pay_invoice($invoice)
    {
        $connect = connectDb();

        $get_invoice_line = $connect ->prepare("SELECT * FROM invoice_line WHERE invoice = :id ORDER BY start_date ASC ");
        $get_invoice_line ->execute([":id" => $invoice]);
        $get_invoice_line = $get_invoice_line ->fetchAll();

        if (strtotime($get_invoice_line[0]["start_date"]) < strtotime(date("Y-m-d")))
        {

            $set_fee = $connect ->prepare("INSERT INTO invoice_line (name,price_DF, VAT, invoice) VALUES (:name,:price_DF, :VAT, :invoice)");
            $set_fee ->execute( [":name" => "frais de dossier", ":price_DF" => 25.83, ":VAT" => 25.83*0.2, ":invoice"=> $invoice]);

            $get_invoice_line = $connect ->prepare("SELECT * FROM invoice_line WHERE invoice = :id ORDER BY start_date ASC ");
            $get_invoice_line ->execute([":id" => $invoice]);
            $get_invoice_line = $get_invoice_line ->fetchAll();

            $update_invoice_amount = $connect->prepare("UPDATE invoice SET amount = amount + 25.83*1.2 WHERE id = :id");
            $update_invoice_amount ->execute([":id" => $invoice]);

        }

        print_r($get_invoice_line);

        for ($i = 0; $i < count($get_invoice_line); $i ++)
        {
            $save_history = $connect ->prepare("INSERT INTO sales_history_aeroclub (name,price_DF,VAT,date,id_custo,id_invoice,id_invoice_line) VALUES (:name,:price_DF,:VAT,:date,:id_custo,:id_invoice,:id_invoice_line)");
            $save_history ->execute([ ":name" => $get_invoice_line[$i]["name"] ,":price_DF" => $get_invoice_line[$i]["price_DF"] ,":VAT" => $get_invoice_line[$i]["VAT"] ,":date" => date("Y-m-d H:i:s")  ,":id_custo" => $_SESSION["id"] ,":id_invoice" => $get_invoice_line[$i]["invoice"] ,":id_invoice_line" => $get_invoice_line[$i]["id"] ]);
        }





        $pay_order = $connect ->prepare("UPDATE invoice SET state = 1 WHERE id=:id");
        $pay_order ->execute([ ":id" => $invoice]);


    }
}

