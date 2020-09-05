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
	$mysqlClass = new mysql_class();
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
		$mobile = $helpers->validateMobile($post['mobile']);
		$remitterid = $post['remitterid'];
		$otp = $post['otp'];
		$pincode = $post['pincode'];
		if(empty($mobile)){
			$helpers->errorResponse("Number is missing or not valid.");
		}
		if(empty($remitterid)){
			$helpers->errorResponse("Remitterid is missing.");
		}
		if(empty($otp)){
			$helpers->errorResponse("OTP is missing.");
		}
		/////////////////// End //////////////////
		
		//// Call netpaisa_curl ////
		$request_param = ['api_access_key' => $api_access_key,'mobile'=>$mobile,'otp'=>$otp,'remitterid'=>$remitterid];

		$remitter_details_json = $helpers->netpaisa_curl($remitter_validate, $request_param);
		$remitter_result = json_decode($remitter_details_json, true);
		
		if($remitter_result['statuscode']=='TXN'){ 
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = $remitter_result['status'];
			$response['REMITTER_DETAILS'] = $remitter_result['data']['remitter'];
			
			$valueBene = array(
							'status'			=>1,
						);
			$mysqlClass->updateData('dmt_remitter_wallet',$valueBene, " where remitter_id = '".$remitterid."' and status = '0'");
		} else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = $remitter_result['status'];
			$mysqlClass->mysqlQuery("delete from dmt_remitter_wallet where remitter_id = '".$remitterid."' and status = '0'");
		}
		/////////////////// End //////////////////
		
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	die();
?>