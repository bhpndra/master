<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("../../config.php");
	require("../classes/db_class.php");
	require("../classes/comman_class.php");
	require("../classes/user_class.php");
	require("../classes/jwt_encode_decode.php");
	$helpers = new Helper_class();
	$mysqlClass = new Mysql_class();
	$userClass = new user_class();
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
		
		//// Check Admin api_access_key ////
		$apiAccessKey = $userClass->get_api_access_key($ADMIN_ID);
		if(empty($apiAccessKey['api_access_key'])){
			$helpers->errorResponse("Admin API Access Key Not Set.");
		}
		$api_access_key = $apiAccessKey['api_access_key'];
		/////////////////// End //////////////////
		
		$mobile = $helpers->validateMobile($post['mobile']);
		if(empty($mobile)){
			$helpers->errorResponse("Mobile is missing or not valid.");
		}
		

		$request_param = ['api_access_key' => $api_access_key, 'mobile' => $mobile];

		$result_json = $helpers->netpaisa_curl($pan_otp_req_register, $request_param);
		//$result_json = '{"DATA":{"statuscode":"TXN","status":"OTP Sent successfully","mobile_number":"9716763608"},"ERR_STATE":0,"MSG":"OTP Sent successfully"}';
		
		$result_arr = json_decode($result_json, true);
		
		if($result_arr['ERR_STATE']=='0'){
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = $result_arr['MSG'];
		} else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = $result_arr['MSG'];
		}

		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>