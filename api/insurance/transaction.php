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
		$proposalNum= $post['proposalNum'];
		$operator_type = $post['operator_type'];
		$amount = $post['amount'];
		$network = $post['operator'];
		
		if(empty($operator_type) || !in_array($operator_type,['DTH','PREPAID','POSTPAID'])){
			$helpers->errorResponse("Operator type not valid or missing ");
		}
		if(empty($proposalNum)){
			$helpers->errorResponse("Proposal Number is missing or not valid.");
		}
		if(empty($amount) && $amount>0){
			$helpers->errorResponse("Amount is missing or invalid.");
		}
		if(empty($network)){
			$helpers->errorResponse("Operator is missing.");
		}

	/////////////////// End //////////////////
			
	//// Transaction start ////	
	$retailsDetails = $mysqlClass->mysqlQuery("Select min_amt_capping,package_id from add_cust where id = '".$USER_ID."'")->fetch(PDO::FETCH_ASSOC);
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
		
		if(empty($package_id) || $package_id <= 0){
			$errorMsg = 'Package Not set.';
		}
		
		$DTRTCommission = $userClass->commission_dt_rt($network,$package_id,$amount);
		if(empty($DTRTCommission['commission_status']) || $DTRTCommission['commission_status'] != 'SET'){
			$errorMsg = 'Insurance Commission Not Set.';
		}
		
		$ADCommission = $userClass->commission_wl($network,$WL_ID,$amount);
		if(empty($ADCommission['commission_status']) || $ADCommission['commission_status'] != 'SET'){
			$errorMsg = 'Admin Commission Issue, Contact to admin.';
		}
			
		if(!empty($errorMsg)){
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse($errorMsg);
		}

		$mac=strtok(exec('getmac'), ' ');
		$request_param = array(
								'operator_value' => $network,
								'api_access_key' => $api_access_key,
								'operator_name' => "RELIGARE",
								'proposalNum' => $proposalNum,
								'mac' => $mac,
								'amount' => $amount
							);
			$transaction_result_json = $helpers->netpaisa_curl($religare_pay_bill, $request_param);

			$transaction_result  = json_decode($transaction_result_json, true);



					if(isset($transaction_result['status_code'])){
			if($transaction_result['status_code']=='00'){

				$responseRC=json_decode($transaction_result['responseRC']);
				  $post_data = array("payment_status"=>1,
				  "depositDt"=>$responseRC->chequeDDReqResIO->depositDt,
				  "policyNum"=>$responseRC->chequeDDReqResIO->policyNum,
				  "payment_date"=>date("Y-m-d H:i:s")
				  );
				  $last_upid=$mysqlClass->updateData(" religare_proposals ", $post_data, " where proposalNum = '".$post['proposalNum']."'");
				
				$retaile_commission = $DTRTCommission['rt_commission'];
		$distributor_commission = $DTRTCommission['dt_commission'];
		$wl_commission = $ADCommission['wl_commission'];
		
		$tranAgentId = $helpers->transaction_id_generator('R',4);
		$wl_deducted_amount = 	$amount - $wl_commission;
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
						'commission_surcharge'		=>	$wl_commission,
						'transaction_amount'		=>	$amount,
						'date_created'				=>	date("Y-m-d H:i:s"),
						'created_by'				=>	$USER_ID,
						'creator_type'				=>	'RETAILER',
						'comments'					=>	'INSURANCE Transaction',
						'tr_type'					=>	'INSURANCE',
						'wluser_id'					=>	$WL_ID
					);
		$wlTran_lastid = $mysqlClass->insertData(" wl_trans ", $valueWLTR);
		if($wlTran_lastid > 0){ } else {
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse('Site Admin Transaction error, Contact to support.');
		}
		
		$deducted_amount = 	$amount - $retaile_commission;
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
						'comments'					=>	'INSURANCE Transaction',
						'tr_type'					=>	'INSURANCE',
						'retailer_id '				=>	$USER_ID
					);
		$rtTran_lastid = $mysqlClass->insertData(" retailer_trans ", $valueRTR);
		if($rtTran_lastid > 0){ } else {
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse('Retailer Transaction error, Contact to support.');
		}
		
		// Transaction commit and call api 
		$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
		$valueLog = array(
					'agent_trid' => $tranAgentId,
					'proposalNum' => $proposalNum,
					'request_data' => json_encode($post),
					'response_data' => '',
					'request_date' => date("Y-m-d H:i:s")
				);
		$mysqlClass->insertData(" religare_insurance_log ", $valueLog);
		$txid		= ($transaction_result['data']['txid'])? $transaction_result['data']['txid'] : $tranAgentId;

				$valueRTTrU = array(
							'comments' 			=> "Insurance Transaction Successful",
							'transaction_id' 	=> $txid
						);
				$mysqlClass->updateData(" retailer_trans ", $valueRTTrU, " where id = '".$rtTran_lastid."'");
				
				$valueWlTrU = array(
							'comments' 			=> "Insurance Transaction Successful",
							'transaction_id' 	=> $txid
						);
				$mysqlClass->updateData(" wl_trans ", $valueWlTrU, " where id = '".$wlTran_lastid."'");
				
				// Distributor Commission 
				$mysqlClass->mysqlQuery("START TRANSACTION");
				$dt_opening_balance = $userClass->check_user_balance($CREATOR_ID, " FOR UPDATE");
				$dt_closing_balance = $userClass->update_wallet_balance_add_amount($CREATOR_ID,$distributor_commission);
				
				$valueDTR = array(
								'dist_retail_wl_admin_id'	=>	$USER_ID,
								'transaction_id'			=>	$txid,
								'agent_trid'				=>	$tranAgentId,
								'opening_balance'			=>	$dt_opening_balance,
								'deposits'					=>	$distributor_commission,
								'withdrawl'					=>	0,
								'balance'					=>	$dt_closing_balance,
								'date_created'				=>	date("Y-m-d H:i:s"),
								'created_by'				=>	$USER_ID,
								'creator_type'				=>	'RETAILER',
								'comments'					=>	'Insurance Transaction',
								'tr_type'					=>	'INSURANCE',
								'dist_id '					=>	$CREATOR_ID
							);
				$rtTran_lastid = $mysqlClass->insertData(" distributor_trans ", $valueDTR);
				$mysqlClass->mysqlQuery("COMMIT");
				
				$response['ERROR_CODE'] = 0;
				$response['MESSAGE'] = $transaction_result['responseRC'];
			}
			else {
				$responseRC=json_decode($transaction_result['responseRC']);
				$response['ERROR_CODE'] = 2;
				$response['MESSAGE'] = $responseRC->intPolicyDataIO->errorLists[0]->errDescription;
			}
		}
		else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = $transaction_result['responseRC'];
		}
	/*	
	/////////////////// End //////////////////
	*/
					//$response['ERROR_CODE'] = 0;
				//$response['MESSAGE'] = "payment deducted from netpaisa wallet.";
				echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>