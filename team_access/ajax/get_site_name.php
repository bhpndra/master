<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include_once('../inc/apivariable.php');
	
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);

	if (!empty($post['user_id'])) {
		
		$data = $mysqlClass->mysqlQuery("SELECT site_title, user_id, site_name FROM `general_settings` WHERE user_id='".$post['user_id']."' ")->fetch(PDO::FETCH_ASSOC);	
		
		echo "<strong>Site-name: </strong>". $data['site_name'];		
	}
	
	
?>