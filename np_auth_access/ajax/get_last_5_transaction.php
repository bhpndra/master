<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include("../classes/user_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	$userClass = new user_class();
	
	$post = $helpers->clearSlashes($_POST);


	if($post['uid']!="")
	{	
		$last5 = $userClass->check_last_5_transaction($post['uid'],$userType='');
		
	} 
	echo json_encode($last5);

?>