<?php

class Vouchers_type
{

    private $db_conn;
    private $table_name = "voucher_type";


    public $id;
    public $name;

    public function __construct()
    {
        $database = new Database();
		$db = $database->getConnection();
        $this->db_conn = $db;
    }


    function getAll()
    {

        $sql = "SELECT * from " . $this->table_name . "  ORDER BY voucher_type_name";

        $prep_state = $this->db_conn->prepare($sql);
        $prep_state->execute();

        return $prep_state;
    }

    function getName()
    {

        $sql = "SELECT voucher_type_name FROM " . $this->table_name . " WHERE id = ? ";
        $prep_state = $this->db_conn->prepare($sql);
        $prep_state->bindParam(1, $this->id); 
        $prep_state->execute();

        $row = $prep_state->fetch(PDO::FETCH_ASSOC);

        $this->name = $row['voucher_type_name'];
    }
	
	function create()
    {
        $sql = "INSERT INTO " . $this->table_name . " SET voucher_type_name = ?";

        $prep_state = $this->db_conn->prepare($sql);

        $prep_state->bindParam(1, $this->voucher_type_name);

        if ($prep_state->execute()) {
            return true;
        } else {
            return false;
        }

    }
	
	function update()
    {
        $sql = "UPDATE " . $this->table_name . " SET voucher_type_name = :name  WHERE id = :id";
        // prepare query
        $prep_state = $this->db_conn->prepare($sql);


        $prep_state->bindParam(':name', $this->voucher_type_name);
        $prep_state->bindParam(':id', $this->id);

        // execute the query
        if ($prep_state->execute()) {
            return true;
        } else {
            return false;
        }
    }
	
	function getVoucher()
    {
        $sql = "SELECT voucher_type_name FROM " . $this->table_name . " WHERE id = :id";

        $prep_state = $this->db_conn->prepare($sql);
        $prep_state->bindParam(':id', $this->id);
        $prep_state->execute();

        $row = $prep_state->fetch(PDO::FETCH_ASSOC);

        $this->voucher_type_name = $row['voucher_type_name'];
    }
	
    function delete()
    {
        $sql = "DELETE FROM " . $this->table_name . " WHERE id = :id ";

        $prep_state = $this->db_conn->prepare($sql);
        $prep_state->bindParam(':id', $this->id);

        if ($prep_state->execute(array(":id" => $_GET['id']))) {
            return true;
        } else {
            return false;
        }
    }
}

