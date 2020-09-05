<?php
define("_session_username_","adminuser");
define("_session_userid_","adminid");
define("_session_usertype_","admintype");
define("_session_apiKey_","api_access_key");
function checkFilesType($FILES){
    foreach($FILES as $key => $val){
        if($FILES[$key]['name']!=""){
            $ext = pathinfo($FILES[$key]['name'], PATHINFO_EXTENSION);
                if($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'PNG' || $ext == 'JPEG'){
                $trueval = true;
            } else {
                die();
            }
        }
    }
}
if(isset($_FILES)){
    checkFilesType($_FILES);
}
session_start();
error_reporting(E_ALL);
ini_set("display_errors",0);
class Database
{

    // used to connect to the default database
    private $host = "10.10.2.133";
    private $db_name = "netpaisa_reseller_db";
    private $username = "netpaisa_reseller_user";
    private $password = 'bhhjaf@kfJFTbE8NmEb123';
    public $db_conn;

    // get the database default connection
    public function getConnection()
    {
        $this->db_conn = null;

        try {
            $this->db_conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        } catch (PDOException $exception) {
            echo "Database Connection Error: " . $exception->getMessage();
        }
        return $this->db_conn;
    }
	
	
	// used to connect to the Second database
	private $host2 = "10.10.2.133";
    private $db_name2 = "netpaisa_reseller_db";
    private $username2 = "netpaisa_reseller_user";
    private $password2 = 'bhhjaf@kfJFTbE8NmEb123';
    public $db_conn2;

    // get the database Second connection
    public function getConnection_2()
    {
        $this->db_conn2 = null;

        try {
            $this->db_conn2 = new PDO("mysql:host=" . $this->host2 . ";dbname=" . $this->db_name2, $this->username2, $this->password2);
        } catch (PDOException $exception) {
            echo "Database Connection Error: " . $exception->getMessage();
        }
        return $this->db_conn2;
    } 
}
