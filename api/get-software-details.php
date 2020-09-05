<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("../config.php");
	require("classes/db_class.php");
	require("classes/comman_class.php");
	require("classes/user_class.php");
	require("classes/jwt_encode_decode.php");
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
		

		$filter = '';
		//// Check Parameters Isset ////
		
		$resD = $mysqlClass->mysqlQuery("select site_title,site_name,address,email,support_number,copyright,logo,app_link,color_code from `general_settings` WHERE `user_type`='WL' and `user_id`='".$WL_ID."'" )->fetch(PDO::FETCH_ASSOC);
		$resWL = $mysqlClass->mysqlQuery("select domain from `add_white_label` WHERE  `user_id`='".$WL_ID."'" )->fetch(PDO::FETCH_ASSOC);
		$response= []; 
		if(!empty($resD['logo'])){
			$resD['logo'] = $resWL['domain'].'uploads/logo/'.$resD['logo'];
		} else {
			$resD['logo'] = '';
		}
		$resD['domain'] = $resWL['domain'];
		if(isset($resD) && count($resD)>0){
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = 'SUCCESS';
			$response['DATA'] = $resD;
		} else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = 'FAILED';
		}
		
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>