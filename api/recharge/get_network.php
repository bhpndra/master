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
		if(empty($post['operator_type']) || !in_array($post['operator_type'],['DTH','PREPAID','POSTPAID'])){
			$helpers->errorResponse("Operator type not valid or missing ");
		}
		/////////////////// End //////////////////
		
		//// Call netpaisa_curl ////
		if(in_array($post['operator_type'],['DTH','PREPAID'])){
			$request_param = ['api_access_key' => $api_access_key, 'operator_type' => $post['operator_type']];

			$network_result_json = $helpers->netpaisa_curl($netpaisa_get_network, $request_param);
			$network_result = json_decode($network_result_json, true);
			
			if($network_result['status_code']=='00'){
				$response['ERROR_CODE'] = 0;
				$response['MESSAGE'] = 'SUCCESS';
				$response['NETWORK'] = $network_result['item'];
			} else {
				$response['ERROR_CODE'] = 1;
				$response['MESSAGE'] = $network_result['msg'];
			}
		}
		
		if($post['operator_type']=="POSTPAID"){
			$request_param = ['api_access_key' => $api_access_key, 'operator_type' => $post['operator_type']];

			$network_result_json = $helpers->netpaisa_curl($netpaisa_get_network_postpaid, $request_param);
			$network_result = json_decode($network_result_json, true);
			
			if($network_result['status_code']=='00'){
				$response['ERROR_CODE'] = 0;
				$response['MESSAGE'] = 'SUCCESS';
				$response['NETWORK'] = $network_result['item'];
			} else {
				$response['ERROR_CODE'] = 1;
				$response['MESSAGE'] = $network_result['msg'];
			}
		}
		/////////////////// End //////////////////
		
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	die();
?>