<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include("../classes/user_class.php");
	
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	$userClass = new user_class();
	
	$post = $helpers->clearSlashes($_REQUEST);
	
	if($post['uid']!="")
	{
		
		$row = "select * from wl_setting_permission where wl_id = '".$post['uid']."' ";
		if($mysqlClass->countRows($row)>0)
		{
			$postArray = array(
							"wl_id"			=> $post['uid'],
							"sms_package"	=> $post['sms_pack_chk'],
							"bulk_sms"		=> $post['bulk_sms_chk'],
							"bulk_email"	=> $post['bulk_email_chk'],
							"payment_gateway"=> $post['payment_gateway_chk'],
							"created_on"	=> date("Y-m-d H:i:s")						
						);
			
			$mysqlClass->updateData("wl_setting_permission", $postArray ," where `wl_id` ='".$post['uid']."' ");
			echo "<div class='col-md-12 alert alert-success'>User Permission Updated Successfully.</div>";
			
		}
		else{
			$postArray = array(
							"wl_id"			=> $post['uid'],
							"sms_package"	=> $post['sms_pack_chk'],
							"bulk_sms"		=> $post['bulk_sms_chk'],
							"bulk_email"	=> $post['bulk_email_chk'],
							"payment_gateway"=> $post['payment_gateway_chk'],
							"created_on"	=> date("Y-m-d H:i:s")						
						);
			
			$mysqlClass->insertData("wl_setting_permission", $postArray);
			echo "<div class='col-md-12 alert alert-success'>User Permission Saved Successfully.</div>";
		}	
	}
	
?>