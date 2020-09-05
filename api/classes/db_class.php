<?php
define("_session_username_","newapiusername");
define("_session_userid_","newapiuserid");
define("_session_usertype_","newapitype");

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors', 1);

class Database
{

    // used to connect to the default database
    private $host = "localhost";
    private $db_name = "netpaisa_reseller_db";
    private $username = "root";
    private $password = "";
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
	/*
	private $host2 = "localhost";
    private $db_name2 = "netpaisa_db";
    private $username2 = "root";
    private $password2 = "";
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
	*/
}
