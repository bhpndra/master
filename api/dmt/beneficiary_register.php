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
		$mobile = $helpers->validateMobile($post['mobile']);
		$remitterid = $post['remitterid'];
		$name = $post['name'];
		$ifsc = $post['ifsc'];
		$account = $post['account'];
		$account_name = $post['account_name'];
		
		if(empty($remitterid)){
			$helpers->errorResponse("Remitterid is missing.");
		}
		if(empty($mobile)){
			$helpers->errorResponse("Number is missing or not valid.");
		}
		if(empty($name)){
			$helpers->errorResponse("Name is missing .");
		}
		if(empty($account_name)){
			$helpers->errorResponse("Account Name is missing .");
		}
		if(empty($ifsc)){
			$helpers->errorResponse("IFSC is missing .");
		}
		if(empty($account)){
			$helpers->errorResponse("Account No. is missing .");
		}
		/////////////////// End //////////////////
		
		
		//// Call netpaisa_curl ////
		$request_param = ['api_access_key' => $api_access_key,'mobile'=>$mobile,'remitterid'=>$remitterid,'name'=>$name,'ifsc'=>$ifsc,'account'=>$account];

		$res_json = $helpers->netpaisa_curl($beneficiary_register, $request_param);
		$res_result = json_decode($res_json, true);
		
		 if($res_result['statuscode']=='TXN'){ 
		 
			//// Checking BENEFICIARY in table exist or not ////
			$resBene = $mysqlClass->fetchRow('beneficiary'," beneficiary_code "," where account = '".$account."' and ifsc = '".$ifsc."' ");
			if(!isset($resBene['beneficiary_code'])){
				$valueBene = array(
								'name'				=> $name,
								'account'			=> $account,
								'bank'				=>$account_name,
								'ifsc'				=>$ifsc,
								'beneficiary_code'	=>$res_result['data']['beneficiary']['id'],
								'api'	=>'INSTA',
								'status'	=>$res_result['data']['beneficiary']['status'],
							);
				$mysqlClass->insertData('beneficiary',$valueBene);
			}
			/////////////////// End //////////////////
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = $res_result['status'];
			$response['REMITTER_ID'] = $res_result['data']['remitter']['id'];
			$response['BENEFICIARY'] = $res_result['data']['beneficiary'];
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