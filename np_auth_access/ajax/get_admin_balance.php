<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include("../classes/user_class.php");
		if(!isset($_SESSION[_session_userid_])){
			die();
		}
		$database = new Database();
		$db = $database->getConnection();
		
		$stmt = $db->prepare("SELECT balance FROM `admin` where id = '".$_SESSION[_session_userid_]."'");
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
				
		$currentBal = $row['balance'];
		
	
	echo empty($currentBal)? '[0] ' : '['.$currentBal.'] ';

?>
