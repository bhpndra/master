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
		$column = ' user_id, outletid, name, email, mobile, company, address, pincode, pan_no, aadhaar, outlet_status, outlet_kyc, aadhaarimg ';
		$res = $mysqlClass->fetchRow('outlet_kyc', $column, " where user_id = '".$USER_ID."' " );
		if(isset($res['user_id']) && $res['user_id'] > 0){
			$resB = $mysqlClass->fetchRow('outlet_kyc',' aeps_balance ', " where id = '".$USER_ID."' " );
			$res['balance'] = (!empty($resB['aeps_balance']))? $resB['aeps_balance'] : '0.00';
		}
		if(!empty($res) && count($res)>0){
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = 'Outlet Found';
			$response['DATA'] = $res;
		} else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = 'Transaction Report Not Found';
		}
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>