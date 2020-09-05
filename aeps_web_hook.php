<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("config.php");
	require("api/classes/db_class.php");
	require("api/classes/comman_class.php");
	require("api/classes/user_class.php");
	$helpers = new Helper_class();
	$userClass = new user_class();
	$mysqlClass = new Mysql_class();
	
	$request_data = (array) json_decode(file_get_contents('php://input'), TRUE);

	
	if(isset($request_data)){
		
		$post = $helpers->clearSlashes($request_data);
		
		
	//// Check Rquired Parameters Isset ////
		$agent_trid = $post['agent_trid'];
		$outletid = $post['outletid'];
		$transaction_id = $post['transaction_id'];
		$terminalid = $post['terminalid'];
		$bcid = $post['bcid'];
		$txntype = $post['txntype'];
		$message = $post['message'];
		$txn_status = $post['txn_status'];
		$amount = $post['amount'];
		$bank_iin = $post['bank_iin'];
		$uid = $post['uid'];
		$mobile = $post['mobile'];
		$transaction_date = $post['transaction_date'];
		
		if(empty($agent_trid)){
			$helpers->errorResponse("Agent Transaction Id is missing or not valid.");
		}
		if(empty($amount) || $amount <= 0){
			$helpers->errorResponse("Amount is missing or invalid.");
		}
		if(empty($outletid)){
			$helpers->errorResponse("Outlet Id is missing.");
		}
		if(empty($transaction_id)){
			$helpers->errorResponse("Transaction Id code is missing.");
		}
	/////////////////// End //////////////////

	$tranDetails = $mysqlClass->mysqlQuery("Select agent_trid from aeps_info where agent_trid = '".$agent_trid."'")->fetch(PDO::FETCH_ASSOC);
	if(isset($tranDetails['agent_trid']) && !empty($tranDetails['agent_trid'])){
		$helpers->errorResponse("Duplicate transaction.");
	}
	
	$outletDetails = $mysqlClass->mysqlQuery("Select user_id from outlet_kyc where outletid = '".$outletid."'")->fetch(PDO::FETCH_ASSOC);
	if(isset($outletDetails['user_id']) && !empty($outletDetails['user_id'])){
		$user_id = $outletDetails['user_id'];
	} else {
		$helpers->errorResponse("Outlet id Not Found");
	}
	
	$retailerDetails = $mysqlClass->mysqlQuery("Select wl_id from add_cust where id = '".$user_id."'")->fetch(PDO::FETCH_ASSOC);
	if(isset($retailerDetails['wl_id']) && !empty($retailerDetails['wl_id'])){
		$wl_id = $retailerDetails['wl_id'];
	} else {
		$helpers->errorResponse("Site Admin id Not Found");
	}
	
	$valueLog = array(
				'agent_trid' => $agent_trid,
				'api_response' => json_encode($request_data),
				'callback_key' => 'WEBHOOK',
				'log_date' => date("Y-m-d H:i:s")
			);
	$mysqlClass->insertData(" aeps_log ", $valueLog);
	
	
	$valueAEPS = array(
					'user_id'				=>	$user_id,
					'wl_id'					=>	$wl_id,
					'outletid'				=>	$outletid,
					'transaction_id'		=>	$agent_trid,
					'agent_trid'			=>	$agent_trid,
					'terminalid'			=>	$terminalid,
					'bcid'					=>	$bcid,
					'txntype'				=>	$txntype,
					'amount'				=>	$amount,
					'bank_iin'				=>	$bank_iin,
					'message'				=>	$message,
					'status'				=>	$txn_status,
					'uid'					=>	$uid,
					'mobile'					=>	$mobile,
					'date_created'			=>	$transaction_date
				);
	$aeps_lastid = $mysqlClass->insertData(" aeps_info ", $valueAEPS);
	if($aeps_lastid > 0){ 
		$response['ERROR_CODE'] = 0;
		$response['MESSAGE'] = "Transaction Success";
	} else {
		$helpers->errorResponse('Transaction error, Contact to support.');
	}
	
	} else {
		$helpers->errorResponse('Request error');
	}
	
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>