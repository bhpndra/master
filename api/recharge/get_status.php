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
	$mysqlClass = new mysql_class();
	$jwtED = new jwt_encode_decode();
	
	if($_SERVER['HTTP_X_API_KEY']==HTTP_X_API_KEY && $_SERVER['HTTP_NETPAISAPASSKEY']==NETPAISAPASSKEY){	
	} else {
		//print_r($_SERVER);
		$helpers->errorResponse("Authorization Invalid !");
	}
	
	if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST)){
		
		$post = $helpers->clearSlashes($_POST);
			
				
		$agent_tranid = $post['agent_tranid'];

		if(empty($agent_tranid)){
			$helpers->errorResponse("AgentTran Id code is missing.");
		}
		
		$sqlRecharge = "Select a.agent_trid, a.operator, a.amount, b.id as userid, b.admin_id, b.wl_id, b.package_id, b.creator_id from recharge_info as a left join add_cust as b on a.user_id = b.id where a.agent_trid = '".$agent_tranid."' and a.status = 'PENDING' ";
			$rechargeDetails = $mysqlClass->mysqlQuery($sqlRecharge)->fetch(PDO::FETCH_ASSOC);
						
			if(!isset($rechargeDetails['agent_trid'])){
				$helpers->errorResponse("Wrong Transaction Request.");
			} else {
				$USER_ID = $rechargeDetails['userid'];
				$WL_ID = $rechargeDetails['wl_id'];
				$ADMIN_ID = $rechargeDetails['admin_id'];
				$CREATOR_ID = $rechargeDetails['creator_id'];
				$tranAgentId = $rechargeDetails['agent_trid'];
				$network = $rechargeDetails['operator'];
				$package_id = $rechargeDetails['package_id'];
				$amount = $rechargeDetails['amount'];
			}
		
		//// Check Admin api_access_key ////
		$apiAccessKey = $userClass->get_api_access_key($ADMIN_ID);
		if(empty($apiAccessKey['api_access_key'])){
			$helpers->errorResponse("Admin API Access Key Not Set.");
		}
		$api_access_key = $apiAccessKey['api_access_key'];
		/////////////////// End //////////////////
		
		
		//// Call netpaisa_curl ////
		$request_param = ['api_access_key' => $api_access_key, 'clientid'=> $agent_tranid];

		$status_result_json = $helpers->netpaisa_curl($netpaisa_status_rechv1, $request_param);
		//$status_result_json = '{"status_code":"02","status_msg":"FAILURE","data":{"clientid":"R15878980712MG7","mobile":"9136653737","amount":"25.00","status":"FAILED"}}';
		//$status_result_json = '{"status_code":"00","status_msg":"SUCCESS","data":{"clientid":"R1594814500Q9","transaction_id":"TEST205903546","mobile":"9716763608","amount":"10.00","status":"SUCCESS"}}';
		$status_result = json_decode($status_result_json, true);
		
		if(isset($status_result['status_code']) && $status_result['status_code']=='00'){
			
			
				$txid = (isset($status_result['data']['transaction_id']) && !empty(isset($status_result['data']['transaction_id']))) ? $status_result['data']['transaction_id'] : $agent_tranid;
								
				$valueRIU = array(
							'status' 			=> "SUCCESS",
							'transaction_id' 	=> $txid,
							'update_date' 		=> date("Y-m-d H:i:s")
						);
				$mysqlClass->updateData(" recharge_info ", $valueRIU, " where agent_trid = '".$tranAgentId."'");
				
				$valueRTTrU = array(
							'comments' 			=> "Recharge Transaction Successful",
							'transaction_id' 	=> $txid
						);
				$mysqlClass->updateData(" retailer_trans ", $valueRTTrU, " where agent_trid = '".$tranAgentId."'");
				
				$valueWlTrU = array(
							'comments' 			=> "Recharge Transaction Successful",
							'transaction_id' 	=> $txid
						);
				$mysqlClass->updateData(" wl_trans ", $valueWlTrU, " where agent_trid = '".$tranAgentId."'");
				
				$DTRTCommission = $userClass->commission_dt_rt($network,$package_id,$amount);
				if(empty($DTRTCommission['commission_status']) || $DTRTCommission['commission_status'] != 'SET'){					
					$distributor_commission = 0;
					$master_distributor_commission = 0;
				} else {					
					$distributor_commission = ($DTRTCommission['dt_commission'] > 0) ? $DTRTCommission['dt_commission'] : 0;
					$master_distributor_commission = ($DTRTCommission['md_commission'] > 0) ? $DTRTCommission['md_commission'] : 0;
					
				}
				
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
				$response['MESSAGE'] = $status_result['status_msg'];
		} 
		else if(isset($status_result['status_code']) && $status_result['status_code']=='02' || ($status_result['status_code']=='ERROR' && $status_result['status_msg']=='Data Not Found !' )){
			
			$sqlRecharge = "Select a.id, a.deducted_amount, a.status, a.agent_trid, b.id as userid, b.admin_id, b.wl_id from recharge_info as a left join add_cust as b on a.user_id = b.id where a.agent_trid = '".$agent_tranid."' and a.status != 'FAILED' ";
			$rechargeDetails = $mysqlClass->mysqlQuery($sqlRecharge)->fetch(PDO::FETCH_ASSOC);
						
			if(!isset($rechargeDetails['agent_trid']) || $rechargeDetails['agent_trid']!=$agent_tranid){
				$helpers->errorResponse("Wrong Transaction.");
			} else {
				$rechInfo_lastid = $rechargeDetails['id'];
				$USER_ID = $rechargeDetails['userid'];
				$WL_ID = $rechargeDetails['wl_id'];
				$ADMIN_ID = $rechargeDetails['admin_id'];
				$deducted_amount = $rechargeDetails['deducted_amount'];
				$tranAgentId = $rechargeDetails['agent_trid'];
			}
						
			$checkRefund = $mysqlClass->fetchRow(" retailer_trans ", " id ", " where refund_id = '".$agent_tranid."' ");
			if(isset($checkRefund['id']) && $checkRefund['id'] > 0){
				$valueRIU = array(
							'status' 			=> "FAILED"
						);
				$mysqlClass->updateData(" recharge_info ", $valueRIU, " where id = '".$rechInfo_lastid."'");
				
				$helpers->errorResponse("Transaction Already Refunded");
			} else {
				$valueRIU = array(
							'status' 			=> "FAILED",
							'update_date' 		=> date("Y-m-d H:i:s")
						);
				$mysqlClass->updateData(" recharge_info ", $valueRIU, " where id = '".$rechInfo_lastid."'");
			}			
				
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
				
				$wlDeductedAmount = $mysqlClass->fetchRow(" wl_trans ", " withdrawl ", " where agent_trid = '".$tranAgentId."' ");
				$wl_deducted_amount = (isset($wlDeductedAmount['withdrawl']) && $wlDeductedAmount['withdrawl'] > 0) ? $wlDeductedAmount['withdrawl'] : 0;
				
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
				$response['MESSAGE'] = 'FAILED';
				$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
				
		}
		else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = isset($status_result['MSG']) ? $status_result['MSG'] : isset($status_result['status_msg']) ? $status_result['status_msg'] : 'Error' ;
			$response['DATA'] = $status_result;
		}
		/////////////////// End //////////////////
		
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	die();
?>