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
		//print_r($res); die();
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
		$ifsc = $post['ifsc'];
		$account = $post['account'];
		
		if(empty($remitterid)){
			$helpers->errorResponse("Remitterid is missing.");
		}
		if(empty($mobile)){
			$helpers->errorResponse("Number is missing or not valid.");
		}
		if(empty($ifsc)){
			$helpers->errorResponse("IFSC is missing .");
		}
		if(empty($account)){
			$helpers->errorResponse("Account No. is missing .");
		}
	/////////////////// End //////////////////
			
	//// Transaction start ////	
	$amount = 3;
	$retailsDetails = $mysqlClass->mysqlQuery("Select min_amt_capping from add_cust where id = '".$USER_ID."'")->fetch(PDO::FETCH_ASSOC);
	$min_amt_capping = $retailsDetails['min_amt_capping'];
	$package_id = $retailsDetails['package_id'];
	
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
		
		$tranAgentId = $helpers->transaction_id_generator('D',4);
		$wl_deducted_amount = 	$amount;
		$wl_closing_balance = $userClass->update_wallet_balance_deduct_amount($WL_ID,$wl_deducted_amount);
		
		$valueWLTR = array(
						'ret_dest_wl_admin_id'		=>	$USER_ID,
						'transaction_id'			=>	$tranAgentId,
						'agent_trid'				=>	$tranAgentId,
						'user_type'					=>	'RETAILER',
						'opening_balance'			=>	$wlOB,
						'deposits'					=>	0,
						'withdrawl'					=>	$wl_deducted_amount,
						'balance'					=>	$wl_closing_balance,
						'transaction_amount'		=>	$amount,
						'date_created'				=>	date("Y-m-d H:i:s"),
						'created_by'				=>	$USER_ID,
						'creator_type'				=>	'RETAILER',
						'comments'					=>	'Account Verification - '.$account,
						'tr_type'					=>	'DMT',
						'wluser_id'					=>	$WL_ID
					);
		$wlTran_lastid = $mysqlClass->insertData(" wl_trans ", $valueWLTR);
		if($wlTran_lastid > 0){ } else {
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse('Site Admin Transaction error, Contact to support.');
		}
		
		$deducted_amount = 	$amount;
		$rt_closing_balance = $userClass->update_wallet_balance_deduct_amount($USER_ID,$deducted_amount);
		
		$valueRTR = array(
						'ret_dest_wl_admin_id'		=>	$USER_ID,
						'transaction_id'			=>	$tranAgentId,
						'agent_trid'				=>	$tranAgentId,
						'opening_balance'			=>	$retailerOB,
						'deposits'					=>	0,
						'withdrawl'					=>	$deducted_amount,
						'balance'					=>	$rt_closing_balance,
						'date_created'				=>	date("Y-m-d H:i:s"),
						'created_by'				=>	$USER_ID,
						'creator_type'				=>	'RETAILER',
						'comments'					=>	'Account Verification - '.$account,
						'tr_type'					=>	'DMT',
						'retailer_id '				=>	$USER_ID
					);
		$rtTran_lastid = $mysqlClass->insertData(" retailer_trans ", $valueRTR);
		if($rtTran_lastid > 0){ } else {
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse('Retailer Transaction error, Contact to support.');
		}
		
		$valueDV = array(
					'transaction_id' 	=> $tranAgentId,
					'agent_trid' 		=> $tranAgentId,
					'mobile' 			=> $mobile,
					'amount' 			=> 1,
					'deducted_amount' 	=> $deducted_amount,
					'trans_mode' 		=> 'IMPS',
					'bene_ac' 			=> $account,
					'ifsc_code' 		=> $ifsc,
					'user_id' 			=> $USER_ID,
					'date_created' 		=> date("Y-m-d H:i:s"),
					'update_date' 		=> date("Y-m-d H:i:s"),
					'status' 			=> 'PENDING',
					'api' 				=> 'INSTA'
				);
		$dmtInfo_lastid = $mysqlClass->insertData(" dmt_info ", $valueDV);
		if($dmtInfo_lastid > 0){ } else {
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse('DMT info error, Contact to support.');
		}
		
		/* Transaction commit and call api */
		$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
		
		$request_param = array(
							'api_access_key' => $api_access_key,
							'remittermobile' => $mobile,
							'account' => $account,
							'ifsc' => $ifsc,
							'agentid' => $tranAgentId
						);
		$transaction_result_json = $helpers->netpaisa_curl($account_validate, $request_param);
		//$transaction_result_json = '{"statuscode":"TXN","status":"Transaction Successful","data":{"remarks":"Transaction Successful","bankrefno":"012109192931","ipay_id":"1200430093108QRHPA","benename":"SANDEEP KUMAR GUPTA","locked_amt":0,"charged_amt":"2.18","verification_status":"VERIFIED"},"timestamp":"2020-04-30 09:31:10","ipay_uuid":"32F9E1468F5563680DE8","orderid":"1200430093108QRHPA","environment":"PRODUCTION"}';
		
		$transaction_result = json_decode($transaction_result_json, true);
		
		unset($post['token']);
		$valueLog = array(
					'agent_trid' => $tranAgentId,
					'request_data' => json_encode($post),
					'response_data' => $transaction_result_json,
					'request_date' => date("Y-m-d H:i:s")
				);
		$mysqlClass->insertData(" dmt_log ", $valueLog);
		
		if($transaction_result['statuscode']=='TXN' || $transaction_result['statuscode']=='TUP'){
			
			$txid		= ($transaction_result['data']['ipay_id'])? $transaction_result['data']['ipay_id'] : $tranAgentId;		
			$bankrefno		= ($transaction_result['data']['bankrefno'])? $transaction_result['data']['bankrefno'] : '';
			$benename		= ($transaction_result['data']['benename'])? $transaction_result['data']['benename'] : '';
			$valueRIU = array(
						'status' 			=> "SUCCESS",
						'transaction_id' 	=> $txid,
						'ref_no' 			=> $bankrefno,
						'bene_name' 		=> $benename,
						'update_date' 		=> date("Y-m-d H:i:s")
					);
			$mysqlClass->updateData(" dmt_info ", $valueRIU, " where id = '".$dmtInfo_lastid."'");
			
			$valueRTTrU = array(
						'transaction_id' 	=> $txid
					);
			$mysqlClass->updateData(" retailer_trans ", $valueRTTrU, " where id = '".$rtTran_lastid."'");
			
			$valueWlTrU = array(
						'transaction_id' 	=> $txid
					);
			$mysqlClass->updateData(" wl_trans ", $valueWlTrU, " where id = '".$wlTran_lastid."'");
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = 'Transaction Successful';
			$response['DATA'] = $transaction_result['data'];
		} 
		else if ( $transaction_result['statuscode'] == "IAN" OR $transaction_result['statuscode'] == "RPI" OR $transaction_result['statuscode'] == "UAD" OR $transaction_result['statuscode'] == "IAC" OR $transaction_result['statuscode'] == "IAT" OR $transaction_result['statuscode'] == "IAB" OR $transaction_result['statuscode'] == "DTX" OR $transaction_result['statuscode'] == "ISE" OR $transaction_result['statuscode'] == "RNF" OR $transaction_result['statuscode'] == "RAR" OR $transaction_result['statuscode'] == "IRA" OR $transaction_result['statuscode'] == "SPE" OR $transaction_result['statuscode'] == "SPD" OR $transaction_result['statuscode'] == "ERR" OR $transaction_result['statuscode'] == "FAB" ) {
				
				$valueRIU = array(
							'status' 			=> "FAILED",
							'update_date' 		=> date("Y-m-d H:i:s")
						);
				$mysqlClass->updateData(" dmt_info ", $valueRIU, " where id = '".$dmtInfo_lastid."'");
				
				$tranId = $helpers->transaction_id_generator('REFUND',4);
				/* Retailer Refund */
				$mysqlClass->mysqlQuery("START TRANSACTION");
				$rt_opening_balance = $userClass->check_user_balance($USER_ID, " FOR UPDATE");
				$rt_closing_balance = $userClass->update_wallet_balance_add_amount($USER_ID,$deducted_amount);
				
				$valueRTRF = array(
						'ret_dest_wl_admin_id'		=>	$ADMIN_ID,
						'transaction_id'			=>	$tranId,
						'agent_trid'				=>	$tranId,
						'opening_balance'			=>	$rt_opening_balance,
						'deposits'					=>	$deducted_amount,
						'withdrawl'					=>	0,
						'balance'					=>	$rt_closing_balance,
						'date_created'				=>	date("Y-m-d H:i:s"),
						'created_by'				=>	$ADMIN_ID,
						'creator_type'				=>	'ADMIN',
						'comments'					=>	'Account Verification Failed',
						'tr_type'					=>	'REFUND',
						'refund_id'					=>	$tranAgentId,
						'retailer_id '				=>	$USER_ID
					);
				$rtTran_lastid = $mysqlClass->insertData(" retailer_trans ", $valueRTRF);
				
				/* WL Refund */
				$wl_opening_balance = $userClass->check_user_balance($WL_ID, " FOR UPDATE");
				$wl_closing_balance = $userClass->update_wallet_balance_add_amount($WL_ID,$wl_deducted_amount);
				$valueWLRF = array(
						'ret_dest_wl_admin_id'		=>	$ADMIN_ID,
						'transaction_id'			=>	$tranId,
						'agent_trid'				=>	$tranId,
						'user_type'					=>	'ADMIN',
						'opening_balance'			=>	$wl_opening_balance,
						'deposits'					=>	$wl_deducted_amount,
						'withdrawl'					=>	0,
						'balance'					=>	$wl_closing_balance,
						'commission_surcharge'		=>	0,
						'transaction_amount'		=>	0,
						'date_created'				=>	date("Y-m-d H:i:s"),
						'created_by'				=>	$ADMIN_ID,
						'creator_type'				=>	'ADMIN',
						'comments'					=>	'Account Verification Failed',
						'tr_type'					=>	'REFUND',
						'refund_id'					=>	$tranAgentId,
						'wluser_id'					=>	$WL_ID
					);
				$rtTran_lastid = $mysqlClass->insertData(" wl_trans ", $valueWLRF);
				
				$response['ERROR_CODE'] = 1;
				$response['MESSAGE'] = $transaction_result['data']['remarks'];
				$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			
		} 
		else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = 'Error from server side.';
		}
	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>
		