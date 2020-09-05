<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("../../config.php");
	require("../classes/db_class.php");
	require("../classes/comman_class.php");
	require("../classes/jwt_encode_decode.php");
	$helpers = new Helper_class();
	$mysqlClass = new Mysql_class();
	$jwtED = new jwt_encode_decode();
	
	if($_SERVER['HTTP_X_API_KEY']==HTTP_X_API_KEY && $_SERVER['HTTP_NETPAISAPASSKEY']==NETPAISAPASSKEY){	
	} else {
		//print_r($_SERVER);
		$helpers->errorResponse("Authorization Invalid !");
	}
	
	if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST)){
		
		$post = $helpers->clearSlashes($_POST);
		
		//// Valided Token and Get Token Details ////
		if(isset($post['token'])){
			$res = $jwtED->decode_token($post['token']);
			if(isset($res->USER_ID) && $res->USER_ID > 0){
				$USER_ID 	= $res->USER_ID;
				$CREATOR_ID	= $res->CREATOR_ID;
				$WL_ID 		= $res->WL_ID;
				$ADMIN_ID 	= $res->ADMIN_ID;
			} else {
				$helpers->errorResponse("Token Expire");
			}
		} else {
			$helpers->errorResponse("Token not set!");
		}
		/////////////////// End //////////////////
		

		$network_result = $mysqlClass->fetchAllData('instanetwork', " operator_name,operator_code ", "where operator_type = '".$post['operator_type']."'  order by operator_name asc");

		$response['ERROR_CODE'] = 0;
		$response['MESSAGE'] = 'SUCCESS';
		$response['NETWORK'] = $network_result;

	
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	die();
?>