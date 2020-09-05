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
		$operator_name = $post['operator_name'];
		$operator_value = $post['operator_value'];
		$operator_type = $post['operator_type'];
		$consumer_number = $post['consumer_number'];
		$customer_mobile = $post['customer_mobile'];
		$mac = '00-21-85-A2-09-71';
		if(empty($operator_name)){
			$helpers->errorResponse("Operator Name not valid or missing ");
		}
		if(empty($operator_value)){
			$helpers->errorResponse("Operator Value not valid or missing ");
		}
		if(empty($operator_type)){
			$helpers->errorResponse("Operator Type not valid or missing ");
		}
		if(empty($consumer_number)){
			$helpers->errorResponse("Consumer Number not valid or missing ");
		}
		if(empty($customer_mobile)){
			$helpers->errorResponse("Consumer Mobile not valid or missing ");
		}
		/////////////////// End //////////////////
		
		$billType = array(5=>"Electricity Bill",6=>"Gas Bill",10=>"Water Bill");
		$amount = 0.13;
		$errorMsg = '';
		$mysqlClass->mysqlQuery("START TRANSACTION");  ///START TRANSACTION **********
		
		$retailerOB = $userClass->check_user_balance($USER_ID, " FOR UPDATE");
		if($retailerOB <= $min_amt_capping + $amount){
			$errorMsg = 'Insufficient Balance!';
		}
		
		$wlOB = $userClass->check_user_balance($WL_ID, " FOR UPDATE");
		if($wlOB <= $amount + 1){
			$errorMsg = 'Admin Wallet Issue !';
		}
		
		if(!empty($errorMsg)){
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse($errorMsg);
		}
		
		
		$tranAgentId = $helpers->transaction_id_generator('B',3);
		$wl_closing_balance = $userClass->update_wallet_balance_deduct_amount($WL_ID,$amount);
		
		$valueWLTR = array(
						'ret_dest_wl_admin_id'		=>	$USER_ID,
						'transaction_id'			=>	$tranAgentId,
						'agent_trid'				=>	$tranAgentId,
						'user_type'					=>	'RETAILER',
						'opening_balance'			=>	$wlOB,
						'deposits'					=>	0,
						'withdrawl'					=>	$amount,
						'balance'					=>	$wl_closing_balance,
						'commission_surcharge'		=>	0,
						'transaction_amount'		=>	$amount,
						'date_created'				=>	date("Y-m-d H:i:s"),
						'created_by'				=>	$USER_ID,
						'creator_type'				=>	'RETAILER',
						'comments'					=>	'Fetch Bill',
						'tr_type'					=>	'BILL',
						'wluser_id'					=>	$WL_ID
					);
		$wlTran_lastid = $mysqlClass->insertData(" wl_trans ", $valueWLTR);
		if($wlTran_lastid > 0){ } else {
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse('Site Admin Transaction error, Contact to support.');
		}
		
		
		$rt_closing_balance = $userClass->update_wallet_balance_deduct_amount($USER_ID,$amount);
		
		$valueRTR = array(
						'ret_dest_wl_admin_id'		=>	$USER_ID,
						'transaction_id'			=>	$tranAgentId,
						'agent_trid'				=>	$tranAgentId,
						'opening_balance'			=>	$retailerOB,
						'deposits'					=>	0,
						'withdrawl'					=>	$amount,
						'balance'					=>	$rt_closing_balance,
						'date_created'				=>	date("Y-m-d H:i:s"),
						'created_by'				=>	$USER_ID,
						'creator_type'				=>	'RETAILER',
						'comments'					=>	'Fetch Bill',
						'tr_type'					=>	'BILL',
						'retailer_id '				=>	$USER_ID
					);
		$rtTran_lastid = $mysqlClass->insertData(" retailer_trans ", $valueRTR);
		if($rtTran_lastid > 0){ } else {
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse('Retailer Transaction error, Contact to support.');
		}
		
	
		/* Transaction commit and call api */
		$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
		
		//// Call netpaisa_curl ////
		$request_param = array(
								'api_access_key' 	=> $api_access_key,
								'operator_name' 	=> $operator_name,
								'operator_value'	=> $operator_value,
								'operator_type'		=> $operator_type,
								'consumer_number'	=> $consumer_number,
								'customer_mobile'	=> $customer_mobile,
								'agent_id'			=> $tranAgentId,
								'mac'				=> $mac
							);

		$fetch_result_json = $helpers->netpaisa_curl($get_due_bill, $request_param);
		//$fetch_result_json = '{ "status_code": "00", "status_msg": "SUCCESS", "data": { "statuscode": "TXN", "status": "Transaction Successful", "dueamount": "100", "duedate": "01-01-9999", "customername": "SHRI UDAY SINGH", "billnumber": "678477913662", "billdate": "01-01-0001", "billperiod": "NA", "reference_id":91 } }';
		$fetch_result = json_decode($fetch_result_json, true);
		
		if(isset($fetch_result['status_code']) && $fetch_result['status_code'] == '00'){
			
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = 'SUCCESS';
			$response['DATA'] = $fetch_result['data'];
		} else {
			
			$tranId = $helpers->transaction_id_generator('REFUND',4);
			/* Retailer Refund */
			$mysqlClass->mysqlQuery("START TRANSACTION");
			$rt_opening_balance = $userClass->check_user_balance($USER_ID, " FOR UPDATE");
			$rt_closing_balance = $userClass->update_wallet_balance_add_amount($USER_ID,$amount);
			
			$valueRTRF = array(
					'ret_dest_wl_admin_id'		=>	$ADMIN_ID,
					'transaction_id'			=>	$tranId,
					'agent_trid'				=>	$tranId,
					'opening_balance'			=>	$rt_opening_balance,
					'deposits'					=>	$amount,
					'withdrawl'					=>	0,
					'balance'					=>	$rt_closing_balance,
					'date_created'				=>	date("Y-m-d H:i:s"),
					'created_by'				=>	$ADMIN_ID,
					'creator_type'				=>	'ADMIN',
					'comments'					=>	'Bill Fetch Refund',
					'tr_type'					=>	'REFUND',
					'refund_id'					=>	$tranAgentId,
					'retailer_id '				=>	$USER_ID
				);
			$rtTran_lastid = $mysqlClass->insertData(" retailer_trans ", $valueRTRF);
			
			/* WL Refund */
			$wl_opening_balance = $userClass->check_user_balance($WL_ID, " FOR UPDATE");
			$wl_closing_balance = $userClass->update_wallet_balance_add_amount($WL_ID,$amount);
			$valueWLRF = array(
					'ret_dest_wl_admin_id'		=>	$ADMIN_ID,
					'transaction_id'			=>	$tranId,
					'agent_trid'				=>	$tranId,
					'user_type'					=>	'ADMIN',
					'opening_balance'			=>	$wl_opening_balance,
					'deposits'					=>	$amount,
					'withdrawl'					=>	0,
					'balance'					=>	$wl_closing_balance,
					'commission_surcharge'		=>	0,
					'transaction_amount'		=>	0,
					'date_created'				=>	date("Y-m-d H:i:s"),
					'created_by'				=>	$ADMIN_ID,
					'creator_type'				=>	'ADMIN',
					'comments'					=>	'Bill Fetch Refund',
					'tr_type'					=>	'REFUND',
					'refund_id'					=>	$tranAgentId,
					'wluser_id'					=>	$WL_ID
				);
			$rtTran_lastid = $mysqlClass->insertData(" wl_trans ", $valueWLRF);
			
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = $fetch_result['status_msg'];
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			
		}
	
		/////////////////// End //////////////////
		
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	die();
?>