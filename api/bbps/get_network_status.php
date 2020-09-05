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
		
		//// Check Rquired Parameters Isset ////
		if(empty($post['operator_name'])){
			$helpers->errorResponse("Operator Name not valid or missing ");
		}
		if(empty($post['operator_value'])){
			$helpers->errorResponse("Operator Value not valid or missing ");
		}
		/////////////////// End //////////////////
		
		//// Call netpaisa_curl ////
		$request_param = ['api_access_key' => $api_access_key, 'operator_name' => $post['operator_name'], 'operator_value'=> $post['operator_value']];

		$network_result_json = $helpers->netpaisa_curl($get_operater_status, $request_param);
		$network_result = json_decode($network_result_json, true);
		
		if(isset($network_result['data']['data'][0]['is_down']) && $network_result['data']['data'][0]['is_down'] == 0){
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = 'SUCCESS';
		} else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = 'Operator Down';
		}
	
		/////////////////// End //////////////////
		
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	die();
?>