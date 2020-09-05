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
		if(empty($amount) && $amount>0){
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
			$errorMsg = 'Recharge Commission Not Set.';
		}
		
		$ADCommission = $userClass->commission_wl($network,$WL_ID,$amount);
		if(empty($ADCommission['commission_status']) || $ADCommission['commission_status'] != 'SET'){
			$errorMsg = 'Admin Commission Issue, Contact to admin.';
		}
			
		if(!empty($errorMsg)){
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse($errorMsg);
		}
		
		$retaile_commission = $DTRTCommission['rt_commission'];
		$distributor_commission = $DTRTCommission['dt_commission'];
		$master_distributor_commission = $DTRTCommission['md_commission'];
		$wl_commission = $ADCommission['wl_commission'];
		
		$tranAgentId = $helpers->transaction_id_generator('R',2);
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
						'comments'					=>	'Recharge Transaction Pending',
						'tr_type'					=>	'RECHARGE',
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
						'comments'					=>	'Recharge Transaction',
						'tr_type'					=>	'RECHARGE',
						'retailer_id '				=>	$USER_ID
					);
		$rtTran_lastid = $mysqlClass->insertData(" retailer_trans ", $valueRTR);
		if($rtTran_lastid > 0){ } else {
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse('Retailer Transaction error, Contact to support.');
		}
		
		$valueRI = array(
					'transaction_id' 	=> $tranAgentId,
					'agent_trid' 		=> $tranAgentId,
					'mobile' 			=> $number,
					'operator' 			=> $network,
					'amount' 			=> $amount,
					'deducted_amount' 	=> $deducted_amount,
					'commission' 		=> $retaile_commission,
					'user_id' 			=> $USER_ID,
					'rech_type' 		=> $operator_type,
					'date_created' 		=> date("Y-m-d H:i:s"),
					'update_date' 		=> date("Y-m-d H:i:s"),
					'status' 			=> 'PENDING',
					'source' 			=> ''
				);
		$rechInfo_lastid = $mysqlClass->insertData(" recharge_info ", $valueRI);
		if($rechInfo_lastid > 0){ } else {
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse('Recharge info error, Contact to support.');
		}
		
		/* Transaction commit and call api */
		$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
		
		if(in_array($post['operator_type'],['DTH','PREPAID'])){
			$request_param = array(
								'api_access_key' => $api_access_key,
								'number' => $number,
								'amount' => $amount,
								'operator' => $network,
								'clientid' => $tranAgentId,
								'circle_code' => 1,
								'operator_type' => $operator_type
							);
			$transaction_result_json = $helpers->netpaisa_curl($netpaisa_recharge_prepaid, $request_param);
			
			//$transaction_result_json = '{ "data": { "status": "PENDING", "txid": "TEST'.time().'", "clientid": "'.$tranAgentId.'", "number": "'.$number.'", "amount": "'.$amount.'", "opening_balance": "986.11", "closing_balance": "976.19" }, "status_code": "01", "status_msg": "Transaction Pending" }';
			
			$transaction_result  = json_decode($transaction_result_json, true);
		} 
		
		if($post['operator_type']=="POSTPAID"){
			$request_param = array(
								'api_access_key' => $api_access_key,
								'number' => $number,
								'amount' => $amount,
								'operator' => $network,
								'clientid' => $tranAgentId,
								'circle_code' => 1,
								'operator_type' => 'POSTPAID'
							);

			$transaction_result_json = $helpers->netpaisa_curl($netpaisa_recharge_postpaid, $request_param);
			$transaction_result = json_decode($transaction_result_json, true);
		}
		unset($post['token']);
		$valueLog = array(
					'agent_trid' => $tranAgentId,
					'request_data' => json_encode($post),
					'response_data' => $transaction_result_json,
					'request_date' => date("Y-m-d H:i:s")
				);
		$mysqlClass->insertData(" recharge_log ", $valueLog);
		$txid		= ($transaction_result['data']['txid'])? $transaction_result['data']['txid'] : $tranAgentId;
		
		if(isset($transaction_result['status_code'])){
			if($transaction_result['status_code']=='00'){
								
				$valueRIU = array(
							'status' 			=> "SUCCESS",
							'transaction_id' 	=> $txid,
							'update_date' 		=> date("Y-m-d H:i:s")
						);
				$mysqlClass->updateData(" recharge_info ", $valueRIU, " where id = '".$rechInfo_lastid."'");
				
				$valueRTTrU = array(
							'comments' 			=> "Recharge Transaction Successful",
							'transaction_id' 	=> $txid
						);
				$mysqlClass->updateData(" retailer_trans ", $valueRTTrU, " where id = '".$rtTran_lastid."'");
				
				$valueWlTrU = array(
							'comments' 			=> "Recharge Transaction Successful",
							'transaction_id' 	=> $txid
						);
				$mysqlClass->updateData(" wl_trans ", $valueWlTrU, " where id = '".$wlTran_lastid."'");
				
				/* TO CHECK, MASTER DISTRIBUTOR IS EXIST OR NOT. */
				
				$qryDistributorValidate = $mysqlClass->mysqlQuery(" SELECT id,created_by,creator_id FROM `add_cust` WHERE id='$CREATOR_ID' AND usertype='DISTRIBUTOR' ")->fetch(PDO::FETCH_ASSOC);
				$MASTER_DISTRIBUTOR = 0;
				//print_r($qryDistributorValidate);
				if(isset($qryDistributorValidate['id']) && $qryDistributorValidate['created_by']=='DISTRIBUTOR'){
					$masterValidate = $mysqlClass->mysqlQuery(" SELECT id,user_id,is_master_distributor FROM `add_distributer` WHERE user_id='".$qryDistributorValidate['creator_id']."' ")->fetch(PDO::FETCH_ASSOC);
					
					if(isset($masterValidate['id']) && $masterValidate['is_master_distributor']==1){
						$MASTER_DISTRIBUTOR = $masterValidate['user_id'];						
					}
				}
				//print_r($masterValidate);
				
				
				$mysqlClass->mysqlQuery("START TRANSACTION");
				
				/* Master Distributor Commission */
				
				if($MASTER_DISTRIBUTOR > 0){
					$mdt_opening_balance = $userClass->check_user_balance($MASTER_DISTRIBUTOR, " FOR UPDATE");
					$mdt_closing_balance = $userClass->update_wallet_balance_add_amount($MASTER_DISTRIBUTOR,$master_distributor_commission);
					
					$valueMDTR = array(
									'dist_retail_wl_admin_id'	=>	$USER_ID,
									'transaction_id'			=>	$txid,
									'agent_trid'				=>	$tranAgentId,
									'opening_balance'			=>	$mdt_opening_balance,
									'deposits'					=>	$master_distributor_commission,
									'withdrawl'					=>	0,
									'balance'					=>	$mdt_closing_balance,
									'date_created'				=>	date("Y-m-d H:i:s"),
									'created_by'				=>	$USER_ID,
									'creator_type'				=>	'RETAILER',
									'comments'					=>	'Recharge Transaction(Master Distributor)',
									'tr_type'					=>	'RECHARGE',
									'dist_id '					=>	$MASTER_DISTRIBUTOR
								);
					$mdtTran_lastid = $mysqlClass->insertData(" distributor_trans ", $valueMDTR);
				}
				
				
				/* Distributor Commission */
				
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
								'comments'					=>	'Recharge Transaction',
								'tr_type'					=>	'RECHARGE',
								'dist_id '					=>	$CREATOR_ID
							);
				$rtTran_lastid = $mysqlClass->insertData(" distributor_trans ", $valueDTR);
				$mysqlClass->mysqlQuery("COMMIT");
				
				$response['ERROR_CODE'] = 0;
				$response['MESSAGE'] = $transaction_result['status_msg'];
			} 
			else if($transaction_result['status_code']=='02') {
				
				$valueRIU = array(
							'status' 			=> "FAILED",
							'transaction_id' 	=> $txid,
							'update_date' 		=> date("Y-m-d H:i:s")
						);
				$mysqlClass->updateData(" recharge_info ", $valueRIU, " where id = '".$rechInfo_lastid."'");
				
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
						'comments'					=>	'Recharge Refund',
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
						'comments'					=>	'Recharge Refund',
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