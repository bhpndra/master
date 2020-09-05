<?php

class Vouchers
{

    private $db_conn;
    private $table_name = "vouchers";


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

        $sql = "SELECT * from " . $this->table_name . "  ORDER BY brand_name";

        $prep_state = $this->db_conn->prepare($sql);
        $prep_state->execute();

        return $prep_state;
    }
	
	function getAllWithPagin($from_record_num, $records_per_page)
    {
        $sql = "SELECT * from " . $this->table_name . " ORDER BY brand_name ASC LIMIT ?, ?";
        $prep_state = $this->db_conn->prepare($sql);
        $prep_state->bindParam(1, $from_record_num, PDO::PARAM_INT); 
        $prep_state->bindParam(2, $records_per_page, PDO::PARAM_INT);


        $prep_state->execute();

        return $prep_state;
        $db_conn = NULL;
    }

    function getName()
    {

        $sql = "SELECT brand_name FROM " . $this->table_name . " WHERE id = ? ";
        $prep_state = $this->db_conn->prepare($sql);
        $prep_state->bindParam(1, $this->id); 
        $prep_state->execute();

        $row = $prep_state->fetch(PDO::FETCH_ASSOC);

        $this->name = $row['name'];
    }
	
	function create()
    {
        $sql = "INSERT INTO " . $this->table_name . " SET brand_name = ?, voucher_type = ?, commision_type = ?, margin = ?";

        $prep_state = $this->db_conn->prepare($sql);

        $prep_state->bindParam(1, $this->brand_name);
        $prep_state->bindParam(2, $this->voucher_type);
        $prep_state->bindParam(3, $this->commision_type);
        $prep_state->bindParam(4, $this->margin);

        if ($prep_state->execute()) {
            return true;
        } else {
            return false;
        }

    }
	
	function updateBrand()
    {
        $sql = "UPDATE " . $this->table_name . " SET brand_name = :name, voucher_type = :vtype, commision_type = :ctype, margin = :margin  WHERE id = :id";
        // prepare query
        $prep_state = $this->db_conn->prepare($sql);


        $prep_state->bindParam(':name', $this->brand_name);
        $prep_state->bindParam(':vtype', $this->voucher_type);
        $prep_state->bindParam(':ctype', $this->commision_type);
        $prep_state->bindParam(':margin', $this->margin);
        $prep_state->bindParam(':id', $this->id);

        // execute the query
        if ($prep_state->execute()) {
            return true;
        } else {
            return false;
        }
    }
	
	function getBrand()
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

        $prep_state = $this->db_conn->prepare($sql);
        $prep_state->bindParam(':id', $this->id);
        $prep_state->execute();

        return $prep_state;
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

