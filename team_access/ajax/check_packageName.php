<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include("../classes/user_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	//$userClass = new user_class();
	
	$post = $helpers->clearSlashes($_POST);


	if(isset($post['packageName']) && $post['packageName']!="")
	{	
		$count = $mysqlClass->countRows("select * from package_list where package_name = '".$post['packageName']."' ");
		if($count>0){
			echo "true";
		} else {
			echo ""; //print_r($post);
		}
		
	} 


?>