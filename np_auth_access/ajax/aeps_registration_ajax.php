<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);

	if($post['row']!="" && $post['status']!="" && $post['action']=="updateStatus")
	{	
		if( $post['status'] == "PENDING"){
			$raepss = 0;
			$outlet_user = $mysqlClass->get_field_data("user_id","`outlet_kyc`"," where id = '".$post['row']."'");
			$dataArray1 = ["aeps_status"=>$raepss];
			$mysqlClass->updateData("`add_retailer`",$dataArray1," where user_id = '".$outlet_user['user_id']."'");
		}
		if( $post['status'] == "APPROVE"){
			$raepss = 1;
			$outlet_user = $mysqlClass->get_field_data("user_id","`outlet_kyc`"," where id = '".$post['row']."'");
			$dataArray1 = ["aeps_status"=>$raepss];
			$mysqlClass->updateData("`add_retailer`",$dataArray1," where user_id = '".$outlet_user['user_id']."'");
		}
		
		
		$dataArray = ["status"=>$post['status']];		
		$mysqlClass->updateData("`outlet_kyc`",$dataArray," where id = '".$post['row']."'");		
		

		echo "Record Updated successfully";
	}
	if($post['row']!="" && $post['status']!="" && $post['action']=="updateWithdrawlStatus")
	{		
		$dataArray = ["status"=>$post['status']];
		$mysqlClass->updateData("`wl_aeps_withdrawl`",$dataArray," where id = '".$post['row']."'");

		echo "Record Updated successfully";
	}
?>