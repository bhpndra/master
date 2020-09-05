<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	//include("../classes/user_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	//$userClass = new user_class();
	$post = $helpers->clearSlashes($_POST);
	
	if(isset($post['submit']) && $post['submit']=='add'){
		unset($post['submit']);
		$dataArray = $post;
		$mysqlClass->insertData("`network`",$dataArray);
	}

	
	if(isset($post['submit']) && $post['submit']=='update'){
		$row_id = $post['row_id'];
		unset($post['row_id']);
		unset($post['submit']);
		$dataArray = $post;
		$mysqlClass->updateData("`network`",$dataArray," where id = '".$row_id."'");
	}	
	
?>