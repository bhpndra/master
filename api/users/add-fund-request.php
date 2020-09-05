<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("../../config.php");
	require("../classes/db_class.php");
	require("../classes/comman_class.php");
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
	
 	//// Check Rquired Parameters Isset ////
		$date = $post['date'];
		$amount = $post['amount'];
		$payment_type = $post['payment_type'];
		$payment_bank = $post['payment_bank'];
		$bank_trn_id = $post['bank_trn_id'];
		$message = $post['message'];
		$mobile = $post['mobile'];
		
		if(empty($mobile)){
			$helpers->errorResponse("Number is missing or not valid.");
		}
		if(empty($amount) || $amount <= 0){
			$helpers->errorResponse("Amount is missing or invalid.");
		}
		if(empty($date)){
			$helpers->errorResponse("Date is missing.");
		}
		if(empty($payment_type)){
			$helpers->errorResponse("Payment Type is missing.");
		}
		if(empty($payment_bank)){
			$helpers->errorResponse("Payment Bank is missing.");
		}
		if(empty($message)){
			$helpers->errorResponse("Message is missing.");
		}
		if(empty($mobile)){
			$helpers->errorResponse("Mobile is missing.");
		}
	/////////////////// End //////////////////
		
		if(isset($_FILES['receipt']['name']) && !empty($_FILES['receipt']['name'])){
			$receiptUpload = $helpers->fileUpload($_FILES["receipt"],"../../uploads/receipt/",'receipt_'.time(),true);
			if($receiptUpload['type']=="success"){
				$receiptName = $receiptUpload['filename'];
			} else {
				$helpers->errorResponse($receiptUpload['message']);
			}
		} else {
			$helpers->errorResponse("Payment receipt missing.");
		}
		
		$valueAF = array(
				'bank'				=>	$payment_bank,				
				'amount'			=>	$amount,				
				'bank_refno'		=>	$bank_trn_id,				
				'mobile'			=>	$mobile,				
				'message'			=>	$message,				
				'status'			=>	'PENDING',				
				'user_id'			=>	$USER_ID,				
				'user_type'			=>	'RETAILER',				
				'admin_id'			=>	$ADMIN_ID,				
				'payment_date'		=>	$date,				
				'deposit_slip'		=>	$receiptName,				
				'request_time'		=>	date("Y-m-d H:i:s")				
			);
		$rtTran_lastid = $mysqlClass->insertData(" payment ", $valueAF);
		if($rtTran_lastid > 0){
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = "Fund request accepted.";
		} else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = "Something wrong.";
		}
 
	} else {
		$helpers->errorResponse("Invalid request !");
	}
 
	echo json_encode($response);
	die();
?>