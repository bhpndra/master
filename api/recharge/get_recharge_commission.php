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
		$number = $post['number'];
		$operator_type = $post['operator_type'];
		$amount = $post['amount'];
		$network = $post['operator'];
		$circle_code = $post['circle_code'];
		
		if(empty($operator_type) || !in_array($operator_type,['DTH','PREPAID','POSTPAID'])){
			$helpers->errorResponse("Operator type not valid or missing ");
		}
		if(empty($number)){
			$helpers->errorResponse("Number is missing or not valid.");
		}
		if(empty($amount) || $amount <= 0){
			$helpers->errorResponse("Amount is missing or invalid.");
		}
		if(empty($network)){
			$helpers->errorResponse("Operator is missing.");
		}
		if(empty($circle_code)){
			$helpers->errorResponse("Circle code is missing.");
		}
	/////////////////// End //////////////////
			
	//// Transaction start ////	
	$retailsDetails = $mysqlClass->mysqlQuery("Select min_amt_capping,package_id from add_cust where id = '".$USER_ID."'")->fetch(PDO::FETCH_ASSOC);
	$min_amt_capping = $retailsDetails['min_amt_capping'];
	$package_id = $retailsDetails['package_id'];
	
	$errorMsg = '';
	
		$retailerOB = $userClass->check_user_balance($USER_ID,"");
		if($retailerOB <= $min_amt_capping + $amount){
			$errorMsg = 'Insufficient Balance!';
		}
		
		$wlOB = $userClass->check_user_balance($WL_ID,"");
		if($wlOB <= $amount + 1){
			$errorMsg = 'Admin Wallet Issue !';
		}
		
		if(empty($package_id) || $package_id <= 0){
			$errorMsg = 'Package Not set.';
		}
		
		$DTRTCommission = $userClass->commission_dt_rt($network,$package_id,$amount);
		if(empty($DTRTCommission['commission_status']) || $DTRTCommission['commission_status'] != 'SET'){
			$errorMsg = 'Recharge Commission Not Set.';
		}
		
		$ADCommission = $userClass->commission_wl($network,$WL_ID,$amount);
		if(empty($ADCommission['commission_status']) || $ADCommission['commission_status'] != 'SET'){
			$errorMsg = 'Admin Commission Issue, Contact to admin.';
		}
			
		if(!empty($errorMsg)){
			$helpers->errorResponse($errorMsg);
		}
		
		$retaile_commission = $DTRTCommission['rt_commission'];
		$distributor_commission = $DTRTCommission['dt_commission'];
		$wl_commission = $ADCommission['wl_commission'];
		
		
		$deducted_amount = 	$amount - $retaile_commission;
		$response['ERROR_CODE'] = 0;
		$response['MESSAGE'] = 'Transaction Allow';
		$response['NUMBER'] = $number;
		$response['NETWORK'] = $network;
		$response['OPERATOR_TYPE'] = $operator_type;
		$response['AMOUNT'] = round($amount,3);
		$response['DEDUCTED_AMT'] = round($deducted_amount,3);	
		$response['COMMISSION'] = round($retaile_commission,3);	
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>