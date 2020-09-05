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
	$userClass = new User_class();
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
		
		$column = ' * ';
		$res = $mysqlClass->fetchRow('outlet_kyc_bankit', $column, " where user_id = '".$USER_ID."' " );
		
		$request_param = array("api_access_key" => $api_access_key, "mobile" => $res['mobile'], "pan_no" => $res['pan_no']);
		$outlet_json = $helpers->netpaisa_curl($aeps_bankit_details, $request_param);
		$outlet_res = json_decode($outlet_json,true);
		//print_r($outlet_res);
		if(isset($res['user_id']) && $res['user_id'] > 0){
			$outlet_status = ($outlet_res['DATA']['outlet_status'] == 'Approved') ? "APPROVE" : strtoupper($outlet_res['DATA']['outlet_status']);
			if(strtoupper($outlet_res['DATA']['kyc_status']) != strtoupper($res['outlet_kyc']) ||  $outlet_status != strtoupper($res['outlet_status'])){
				$updateOutlet = array(
							"company" => strtoupper($outlet_res['DATA']['company']),
							"address" => strtoupper($outlet_res['DATA']['address']),
							"pincode" => $outlet_res['DATA']['pincode'],
							"city" => strtoupper($outlet_res['DATA']['city']),
							"district" => strtoupper($outlet_res['DATA']['district']),
							"dob" => $outlet_res['DATA']['dob'],
							"aadhaar" => $outlet_res['DATA']['aadhaar_no'],
							"aadhaarimg" => "https://netpaisa.com/nps/apiUser/pan_img/".$outlet_res['DATA']['aadhaar_img'],
							"update_date" => date("Y-m-d H:i:s"),
							"outlet_status" => $outlet_status,
							"comments" => strtoupper($outlet_res['DATA']['comments']),
							"outlet_kyc" => strtoupper($outlet_res['DATA']['kyc_status'])
						);
				$mysqlClass->updateData('outlet_kyc_bankit', $updateOutlet, " where user_id = '".$USER_ID."' and mobile = '".$res['mobile']."' and pan_no = '".$res['pan_no']."' " );
			}
			$resB = $mysqlClass->fetchRow('outlet_kyc_bankit',' * ', " where user_id = '".$USER_ID."' " );
			
		}
		if(!empty($resB) && count($resB)>0){
			unset($resB['panimg']);
			unset($resB['user_id']);
			unset($resB['id']);
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = 'Outlet Found';
			$response['DATA'] = $resB;
			//$response['outlet_res'] = $outlet_res;
		} else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = 'Outlet Not Found';
		}
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>