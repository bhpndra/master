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
		$id = $post['id'];
		$gid = $post['gid'];
		$mode = $post['mode'];
		
		if(empty($id)){
			$helpers->errorResponse("Row Id is missing or not valid.");
		}
		if(empty($gid)){
			$helpers->errorResponse("Group Id is missing.");
		}
		if(empty($mode)){
			$helpers->errorResponse("Mode is missing.");
		}
	/////////////////// End //////////////////
	
	$checkPayout = $mysqlClass->fetchRow(" payout_info "," id "," where group_id = '".$gid."' and status != 'FAILED' ");
	if(isset($checkPayout['id']) && $checkPayout['id'] > 0 ){
		$helpers->errorResponse("Not Allow to Payout (Group Id Already Used).");
	}
	
	
	//// Transaction start ////	
	$aepsSettlDetails = $mysqlClass->mysqlQuery("Select * from aeps_withdrawl_info where user_id = '".$USER_ID."' and id = '".$id."' and group_id = '".$gid."' and status = 'PENDING' and withdrawl_type = 'bank' ")->fetch(PDO::FETCH_ASSOC);
	if(isset($aepsSettlDetails['id']) && $aepsSettlDetails['id'] > 0) {
		$amount = $aepsSettlDetails['amount'];
		$userDetails = $mysqlClass->fetchRow(" add_cust "," mobile "," where id = '".$USER_ID."' ");
		$mobile = $userDetails['mobile'];
		$ifsc = $aepsSettlDetails['ifsc'];
		$bank_name = $aepsSettlDetails['bank_name'];
		$account_name = $aepsSettlDetails['account_name'];
		$account_number = $aepsSettlDetails['account_number'];
	} else{
		$helpers->errorResponse("Not Allow to Payout.");
	}
	
	
	$errorMsg = '';
$charge_amount = 0;
if($mode=='IMPS'){
	//$mode = 'DPN';
	if($resST_Details['amount'] >= 50000){
		$charge_amount = 10;
	} else if($resST_Details['amount'] >= 25000){
		$charge_amount = 6;
	} else {
		$charge_amount = 5;
	}
	
} else if($mode=='RTGS') {
	//$mode = 'CPN';
	$charge_amount = 20;
} else {
	//$mode = 'BPN';
	$charge_amount = 4;	
}
	
	$mysqlClass->mysqlQuery("START TRANSACTION");  ///START TRANSACTION **********
	
		
		$wlOB = $userClass->check_user_balance($WL_ID, " FOR UPDATE");
		if($wlOB <= $amount + $charge_amount + 1){
			$errorMsg = 'Admin Wallet Issue !';
		}

			
		if(!empty($errorMsg)){
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse($errorMsg);
		}
	
	
		
		$tranAgentId = $helpers->transaction_id_generator('AS',3);
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
						'commission_surcharge'		=>	$charge_amount,
						'transaction_amount'		=>	$amount,
						'date_created'				=>	date("Y-m-d H:i:s"),
						'created_by'				=>	$USER_ID,
						'creator_type'				=>	'RETAILER',
						'comments'					=>	'AEPS Settlement Using Payout',
						'tr_type'					=>	'PAYOUT',
						'wluser_id'					=>	$WL_ID
					);
					//print_r($valueWLTR);
		$wlTran_lastid = $mysqlClass->insertData(" wl_trans ", $valueWLTR);
		if($wlTran_lastid > 0){ } else {
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse('Site Admin Transaction error, Contact to support.');
		}
		
				
		/* Transaction commit */
		$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
		
		$valueRIU = array(
					'status' 			=> 'SUCCESS',
					'payment_date' 		=> date("Y-m-d H:i:s")
				);
		$mysqlClass->updateData(" aeps_withdrawl_info ", $valueRIU, " where user_id = '".$USER_ID."' and id = '".$id."' and group_id = '".$gid."' and status = 'PENDING' and withdrawl_type = 'bank' ");
		
		$transfer_amount = $amount - $charge_amount;
		$valueRI = array(
					'transaction_id' 	=> $tranAgentId,
					'agent_trid' 		=> $tranAgentId,
					'group_id' 			=> $gid,
					'account_number' 	=> $account_number,
					'mode' 				=> $mode,
					'ifsc_code' 		=> $ifsc,
					'amount' 			=> $transfer_amount,
					'user_id' 			=> $USER_ID,
					'date_created' 		=> date("Y-m-d H:i:s"),
					'update_date' 		=> date("Y-m-d H:i:s"),
					'status' 			=> 'PENDING',
					'source' 			=> ''
				);
		$payoutInfo_lastid = $mysqlClass->insertData(" payout_info ", $valueRI);
		
		$request_param = array(
								'api_access_key' 	=> $api_access_key,
								'account_number' 	=> $account_number,
								'name'				=> $account_name,
								'mobile'			=> $mobile,
								'ifsc_code'			=> $ifsc,
								'mode'				=> $mode,
								'remarks'			=> 'AEPS Settlement Amt: ' . $amount,
								'agent_id'			=> $tranAgentId,
								'amount'			=> $transfer_amount
							);

		$transaction_result_json = $helpers->netpaisa_curl($payout_url, $request_param);
		//$transaction_result_json = '{"status_code": "00","status_msg": "Transaction Successful","data":{"external_ref":"'.$tranAgentId.'","ipay_id":"DUMMy'.time().'","transfer_value":"'.$transfer_amount.'","type_pricing":"CHARGE","commercial_value":"1.1800","value_tds":"0.0000","ccf":"0.00","vendor_ccf":"0.00","charged_amt":"11.18","payout":{"credit_refid":"00","account":"919716763608","ifsc":"PYTM0123456","name":"Sandeep Kumar Gupta"}}}';
		$transaction_result = json_decode($transaction_result_json, true);
		
		unset($post['token']);
		$valueLog = array(
					'agent_trid' => $tranAgentId,
					'request_data' => json_encode($post),
					'response_data' => $transaction_result_json,
					'request_date' => date("Y-m-d H:i:s")
				);
		$mysqlClass->insertData(" payout_log ", $valueLog);
		$txid		= ($transaction_result['data']['ipay_id'])? $transaction_result['data']['ipay_id'] : $tranAgentId;
		$status		= $transaction_result['status_msg'];
		
		if(isset($transaction_result['status_code'])){
			if($transaction_result['status_code']=='00'){
								
				$valueRIU = array(
							'status' 			=> $status,
							'transaction_id' 	=> $txid,
							'update_date' 		=> date("Y-m-d H:i:s")
						);
				$mysqlClass->updateData(" payout_info ", $valueRIU, " where id = '".$payoutInfo_lastid."'");
				$valueWLU = array(
							'transaction_id' 	=> $txid
						);
				$mysqlClass->updateData(" wl_trans ", $valueWLU, " where id = '".$wlTran_lastid."'");
		
				$response['ERROR_CODE'] = 0;
				$response['MESSAGE'] = $status;
			} 
			else if($transaction_result['status_code']=='01') {
				
				$valueRIU = array(
							'status' 			=> "FAILED",
							'transaction_id' 	=> $txid,
							'update_date' 		=> date("Y-m-d H:i:s")
						);
				$mysqlClass->updateData(" payout_info ", $valueRIU, " where id = '".$payoutInfo_lastid."'");
				
				$valueRIU1 = array(
					'status' 			=> 'PENDING',
					'payment_date' 		=> date("Y-m-d H:i:s")
				);
				$mysqlClass->updateData(" aeps_withdrawl_info ", $valueRIU1, " where user_id = '".$USER_ID."' and id = '".$id."' and group_id = '".$gid."' and status = 'SUCCESS' and withdrawl_type = 'bank' ");
				
				$tranId = $helpers->transaction_id_generator('REFUND',4);
				/* Retailer Refund */
				$mysqlClass->mysqlQuery("START TRANSACTION");
				
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
						'comments'					=>	'Payout Refund',
						'tr_type'					=>	'REFUND',
						'refund_id'					=>	$tranAgentId,
						'wluser_id'					=>	$WL_ID
					);
				$rtTran_lastid = $mysqlClass->insertData(" wl_trans ", $valueWLRF);
				
				$response['ERROR_CODE'] = 1;
				$response['MESSAGE'] = $transaction_result['status_msg'];
				$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			}
			else {
				$response['ERROR_CODE'] = 1;
				$response['MESSAGE'] = 'Transaction Pending';
			}
		}
		else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = $transaction_result['status_msg'];
		}
		
	/////////////////// End //////////////////
		
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>