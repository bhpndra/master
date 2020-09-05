<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include("../classes/user_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	$userClass = new user_class();
	
	$post = $helpers->clearSlashes($_POST);

	

	if($post['userid']!="")
	{	

		$row = $mysqlClass->get_field_data("*","bankit_outlet_kyc"," where user_id = '".$post['userid']."'");

		echo json_encode($row);

	}
?>