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
		
		//// Check Admin api_access_key ////
		$apiAccessKey = $userClass->get_api_access_key($ADMIN_ID);
		if(empty($apiAccessKey['api_access_key'])){
			$helpers->errorResponse("Admin API Access Key Not Set.");
		}
		$api_access_key = $apiAccessKey['api_access_key'];
		/////////////////// End //////////////////
		
		//// Check Rquired Parameters Isset ////
		$remitterid = $post['remitterid'];
		$beneficiaryid = $post['beneficiaryid'];
		$otp = $post['otp'];
		
		if(empty($remitterid)){
			$helpers->errorResponse("Remitterid is missing.");
		}
		if(empty($otp)){
			$helpers->errorResponse("OTP is missing.");
		}
		if(empty($beneficiaryid)){
			$helpers->errorResponse("beneficiaryid is missing.");
		}
		/////////////////// End //////////////////
		
		
		//// Call netpaisa_curl ////
		$request_param = ['api_access_key' => $api_access_key,'remitterid'=>$remitterid,'otp'=>$otp,'beneficiaryid'=>$beneficiaryid];

		$res_json = $helpers->netpaisa_curl($beneficiary_register_validate, $request_param);
		$res_result = json_decode($res_json, true);
		
		 if($res_result['statuscode']=='TXN'){ 
		 	$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = $res_result['status'];
			$valueBene = array(
							'status' => 1,
						);
			$mysqlClass->updateData('beneficiary',$valueBene, " where beneficiary_code = '".$beneficiaryid."' and status = '0'");
		} else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = $res_result['status'];
		}
		/////////////////// End //////////////////
		
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	$mysqlClass->close_connection();
	die();
?>