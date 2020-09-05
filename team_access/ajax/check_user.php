<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include("../classes/user_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	//$userClass = new user_class();
	
	$post = $helpers->clearSlashes($_POST);


	if($post['pin']!="" && $post['check']=="checkPin")
	{	
		$pin = $helpers->hashPin($post['pin']);
		$count = $mysqlClass->countRows("select * from add_cust where security_pin = '".$pin['encrypted']."' and id = '".$_SESSION[_session_userid_]."'");
		if($count>0){
			echo "true";
		} else {
			echo "Wrong PIN !"; //print_r($post);
		}
		
	} 


?>