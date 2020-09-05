<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include("../classes/user_class.php");
	
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	$userClass = new user_class();
	
	$post = $helpers->clearSlashes($_REQUEST);
	
	if(isset($post['id']) && $post['id']!=""  && $post['val'] > 0)
	{
			
		$row = "select * from add_cust where wl_id = '".$post['id']."' ";
		$getChildLimit = $mysqlClass->mysqlQuery($row)->fetch(PDO::FETCH_ASSOC);
		$childL  = $getChildLimit['number_of_child_limi'];
		
		if($mysqlClass->countRows($row)>0)
		{
			$postArray = array(
							"number_of_child_limi"	=> ($childL + $post['val'])
						);
			
			$mysqlClass->updateData("add_cust", $postArray ," where `wl_id` ='".$post['id']."' ");
			echo "Child Limit Increased Successfully.";
			
		}
	}
	else{
		echo "Invalid parameters.";
	}
	
?>