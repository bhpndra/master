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
		$remittermobile = $post['remittermobile'];
		$amount = $post['amount'];
		$beneficiaryid = $post['beneficiaryid'];
		$account = $post['bene_account'];
		$ifsc = $post['bene_ifsc'];
		$bene_name = $post['bene_name'];
		$current_wallet_remaining_limit = $post['current_wallet_remaining_limit'];
		$mode = 'IMPS';
		
		if(empty($remittermobile)){
			$helpers->errorResponse("Remitter Mobile is missing or not valid.");
		}
		if(empty($amount) || $amount <= 0){
			$helpers->errorResponse("Amount is missing or invalid.");
		}
		if(empty($beneficiaryid)){
			$helpers->errorResponse("Beneficiary Id is missing.");
		}
		if(empty($current_wallet_remaining_limit)){
			$helpers->errorResponse("Current wallet Limit is missing.");
		}
		if(empty($account)){
			$helpers->errorResponse("Beneficiary Account is missing.");
		}
		if(empty($ifsc)){
			$helpers->errorResponse("Beneficiary IFSC is missing.");
		}
		if(empty($bene_name)){
			$helpers->errorResponse("Beneficiary Name is missing.");
		}
		if(empty($mode)){
			$helpers->errorResponse("Mode is missing.");
		}
	/////////////////// End //////////////////

	//// Amount Break ////
$trans_count = 1;	
if ( $current_wallet_remaining_limit >= $amount ) {
		$current_wallet = $amount;		
		$rest_remaining_limit = $current_wallet;			
		for ( $i = 1; $i < 6; $i++ ) {

			if ( $rest_remaining_limit > 5000 ) {
				${"trans_".$i} = 5000;
				$rest_remaining_limit = $rest_remaining_limit - ${"trans_".$i};
				$trans_count ++;
				
			} else if ( $rest_remaining_limit == 5000 ) {
				${"trans_".$i} = 5000;
				$rest_remaining_limit = 0;
				break;
				
			} else {
				${"trans_".$i} = $rest_remaining_limit;
				break;
			}			
		}		
	} else {
		$helpers->errorResponse('Amount should not be more than 25000. Your Current wallet limit is '.$current_wallet_remaining_limit);
	}
/////////////////// End //////////////////
$surcharge = 0;
$transStruct = array();
$retailsDetails = $mysqlClass->mysqlQuery("Select min_amt_capping,package_id from add_cust where id = '".$USER_ID."'")->fetch(PDO::FETCH_ASSOC);
$min_amt_capping = $retailsDetails['min_amt_capping'];
$package_id = $retailsDetails['package_id'];

$tranGroupId = $helpers->transaction_id_generator('GP',2);
if(empty($package_id) || $package_id <= 0){
	$helpers->errorResponse('Package Not set.');
}
$errorMsg = '';

//// Calculate Surcharge (Loop Start) ////	
for ( $k = 1; $k <= $trans_count; $k++ ) {	
	$remit_amount = ${"trans_".$k};
	//// Get DTM Slabs ////	
	$slabNetwork = $userClass->get_dmt_slab($WL_ID,$remit_amount);	
	/////////////////// End //////////////////
	

		$DTRTCommission = $userClass->commission_dt_rt($slabNetwork,$package_id,$remit_amount);
		if(empty($DTRTCommission['commission_status']) || $DTRTCommission['commission_status'] != 'SET'){
			$errorMsg = 'DMT Commission Not Set.';
		}
		
		$ADCommission = $userClass->commission_wl_dmt('DMT',$WL_ID,$remit_amount);
		if(empty($ADCommission['commission_status']) || $ADCommission['commission_status'] != 'SET'){
			$errorMsg = 'Admin Commission Issue, Contact to admin.';
		}
		
		$retaile_surcharge = $DTRTCommission['rt_commission'];
		$distributor_commission = $DTRTCommission['dt_commission'];
		$master_distributor_commission = $DTRTCommission['md_commission'];
		$wl_surcharge = $ADCommission['wl_commission'];
		
		$surcharge = $surcharge + $retaile_surcharge;
		$deducted_amount = $remit_amount + $retaile_surcharge;
		$wl_deducted_amount = $remit_amount + $wl_surcharge;
		
	$mysqlClass->mysqlQuery("START TRANSACTION");  ///START TRANSACTION **********	
		$retailerOB = $userClass->check_user_balance($USER_ID, " FOR UPDATE");
		if($retailerOB <= $min_amt_capping + $deducted_amount){
			$errorMsg = 'Insufficient Balance!';
		}
		
		$wlOB = $userClass->check_user_balance($WL_ID, " FOR UPDATE");
		if($wlOB <= $wl_deducted_amount + 1){
			$errorMsg = 'Admin Wallet Issue !';
		}
		
		if(!empty($errorMsg)){			
			if(!empty($transStruct) && count($transStruct) > 0){
				$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
				$response['ERROR_CODE'] = 0;
				$response['MESSAGE'] = 'Transaction Not Complete ('.$errorMsg.')';	
				$response['TRANS_STRUCT'] = $transStruct;
				echo json_encode($response); die();
			} else {
				$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
				$helpers->errorResponse($errorMsg);
			}			
		}
		
		
		$tranAgentId = $helpers->transaction_id_generator('MT',3);
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
						'commission_surcharge'		=>	$wl_surcharge,
						'transaction_amount'		=>	$amount,
						'date_created'				=>	date("Y-m-d H:i:s"),
						'created_by'				=>	$USER_ID,
						'creator_type'				=>	'RETAILER',
						'comments'					=>	'DMT Transaction',
						'tr_type'					=>	'DMT',
						'wluser_id'					=>	$WL_ID
					);
		$wlTran_lastid = $mysqlClass->insertData(" wl_trans ", $valueWLTR);
		if($wlTran_lastid > 0){ } else {
			$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			$helpers->errorResponse('Site Admin Transaction error, Contact to support.');
		}
		
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
						'comments'					=>	'DMT Transaction',
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
					'mobile' 			=> $remittermobile,
					'amount' 			=> $remit_amount,
					'deducted_amount' 	=> $deducted_amount,
					'trans_mode' 		=> 'IMPS',
					'bene_ac' 			=> $account,
					'bene_code' 		=> $beneficiaryid,
					'bene_name' 		=> $bene_name,
					'ifsc_code' 		=> $ifsc,
					'user_id' 			=> $USER_ID,
					'date_created' 		=> date("Y-m-d H:i:s"),
					'update_date' 		=> date("Y-m-d H:i:s"),
					'status' 			=> 'PENDING',
					'txn_group_id' 		=> $tranGroupId,
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
							'remittermobile' => $remittermobile,
							'amount' => $remit_amount,
							'beneficiaryid' => $beneficiaryid,
							'mode' => 'IMPS',
							'agentid' => $tranAgentId
						);
		$transaction_result_json = $helpers->netpaisa_curl($money_transfer, $request_param);
		
		//$transaction_result_json = '{"statuscode":"TXN","status":"Transaction Successful","data":{"ipay_id":"'.time().'AKYKG","ref_no":"'.time().'","opr_id":"'.time().'","name":"'.$bene_name.'","opening_bal":1925759.31,"amount":'.$remit_amount.',"charged_amt":'.$deducted_amount.',"locked_amt":0,"ccf_bank":50,"bank_alias":"'.$ifsc.'"},"timestamp":"'.date("Y-m-d H:i:s").'","ipay_uuid":"'.time().'AKYKG","orderid":"'.time().'AKYKG","environment":"PRODUCTION"}';
		
		//$transaction_result_json = '{"statuscode":"IAB","status":"Error Occurred or DMT is Down, Contact to Admin","data":"","timestamp":"2020-05-01 14:00:16","ipay_uuid":"A24C82079B582A2FAB76","orderid":"","environment":"PRODUCTION"}';
		
		$transaction_result = json_decode($transaction_result_json, true);
		
		unset($post['token']);
		$valueLog = array(
					'agent_trid' => $tranAgentId,
					'request_data' => json_encode($post),
					'response_data' => $transaction_result_json,
					'request_date' => date("Y-m-d H:i:s")
				);
		$mysqlClass->insertData(" dmt_log ", $valueLog);
		
		$transStruct['retailer_struct'][$k-1] = array('surcharge'=>$retaile_surcharge, 'deducted_amt'=>$deducted_amount, 'remit_amount'=> $remit_amount);
		
		$txid		= (isset($transaction_result['data']['ipay_id']))? $transaction_result['data']['ipay_id'] : $tranAgentId;		
		$bankrefno		= (isset($transaction_result['data']['ref_no']))? $transaction_result['data']['ref_no'] : '';
		
		if($transaction_result['statuscode']=='TXN' || $transaction_result['statuscode']=='TUP'){
			$transStruct['retailer_struct'][$k-1]['status'] = $transaction_result['status'];
			$status_txn = ($transaction_result['statuscode']=='TUP') ? 'PENDING' : 'SUCCESS';
			$valueRIU = array(
						'status' 			=> $status_txn,
						'transaction_id' 	=> $txid,
						'ref_no' 			=> $bankrefno,
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

				
			
			
			if($transaction_result['statuscode']=='TXN'){

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
									'comments'					=>	'DMT Commission(Master Distributor)',
									'tr_type'					=>	'DMT',
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
								'comments'					=>	'DMT Commission',
								'tr_type'					=>	'DMT',
								'dist_id '					=>	$CREATOR_ID
							);
				$dtTran_lastid = $mysqlClass->insertData(" distributor_trans ", $valueDTR);
				$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
			}
		} 
		else if ( $transaction_result['statuscode'] == "IAN" OR $transaction_result['statuscode'] == "RPI" OR $transaction_result['statuscode'] == "UAD" OR $transaction_result['statuscode'] == "IAC" OR $transaction_result['statuscode'] == "IAT" OR $transaction_result['statuscode'] == "IAB" OR $transaction_result['statuscode'] == "DTX" OR $transaction_result['statuscode'] == "ISE" OR $transaction_result['statuscode'] == "RNF" OR $transaction_result['statuscode'] == "RAR" OR $transaction_result['statuscode'] == "IRA" OR $transaction_result['statuscode'] == "SPE" OR $transaction_result['statuscode'] == "SPD" OR $transaction_result['statuscode'] == "ERR" OR $transaction_result['statuscode'] == "FAB" ) {
				
				$transStruct['retailer_struct'][$k-1]['status'] = "FAILED";
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
						'comments'					=>	'DMT Failed',
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
						'comments'					=>	'DMT Failed',
						'tr_type'					=>	'REFUND',
						'refund_id'					=>	$tranAgentId,
						'wluser_id'					=>	$WL_ID
					);
				$rtTran_lastid = $mysqlClass->insertData(" wl_trans ", $valueWLRF);
				
				$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
					
		} else {
			/* TRANSACTION PENDING */
		}
	}
/////////////////// End //////////////////	
		
		
		$response['ERROR_CODE'] = 0;
		$response['MESSAGE'] = 'Transaction Success';	
		$response['TRANS_STRUCT'] = $transStruct;	
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>