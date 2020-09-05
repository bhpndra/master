<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("../../config.php");
	require("../classes/db_class.php");
	require("../classes/comman_class.php");
	require("../classes/jwt_encode_decode.php");
	require("../classes/CustomerDevice.php");
	
	$helpers = new Helper_class();
	$mysqlObj = new mysql_class();
	$custDevice = new 	CustomerDevice();
	
	if($_SERVER['HTTP_X_API_KEY']==HTTP_X_API_KEY && $_SERVER['HTTP_NETPAISAPASSKEY']==NETPAISAPASSKEY){	
	} else {
		//print_r($_SERVER);
		$helpers->errorResponse("Authorization Invalid !");
	}
	
	if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST)){
		$post = $helpers->clearSlashes($_POST);
		
		//check device exist			
			$CheckDeviceExist = $mysqlObj->countRows("SELECT `id`,`status` FROM `installed_app` where `device_id`='".$post['device_id']."' ");
			if($CheckDeviceExist > 0)
			{
				$data = array(						
						'update_date' => date('Y-m-d H:i:s'),
					);
				$mysqlObj->updateData("`installed_app`",$data," where device_id='".$post['device_id']."' ");
				$helpers->errorResponse("Updated !");
			}
			else{
				$data = array(								
								'create_date'=>date("Y-m-d H:i:s"),															
								'mac_address' => $post['mac_id'],
								'device_id' => $post['device_id'],
								'geo_location_lat' => $post['geo_location_lat'],
								'geo_location_long' => $post['geo_location_long'],
								'imei_id'  => $post['imei_id'],
								'device_name'  => $post['device_name'],
								'installation_mode'  => $post['installation_mode'],
								'device_model'  => $post['device_model'],
								'status' => 1
							);
				$mysqlObj->insertData("`installed_app`",$data);
				$helpers->errorResponse("Saved !");
		
			} 
		
			
		
	} else {
		
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	$mysqlObj->close_connection();
	die();
?>