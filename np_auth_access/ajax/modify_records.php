<?php

	include("../classes/nps_db_connection.php");

	include("../classes/comman_class.php");

	//include("../classes/user_class.php");

	$helpers = new helper_class();

	$mysqlClass = new Mysql_class();

	//$userClass = new user_class();

	$post = $helpers->clearSlashes($_POST);

	$pas=0;

	$pin=0;

	$dataArray = ["status"=>$post['status']];

	$password = ["password_attempt"=>$pas];

	$pins = ["pin_attempt"=>$pin];

	$mysqlClass->updateData("`add_cust`",$dataArray," where id = '".base64_decode($post['id'])."'");

	$mysqlClass->updateData("`threat_attempt`",$password," where user_id = '".base64_decode($post['id'])."'");


	$mysqlClass->updateData("`threat_attempt`",$pins," where user_id = '".base64_decode($post['id'])."'");
	

?>