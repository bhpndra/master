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
		
		$sqlDMT = "Select a.agent_trid, a.amount, a.deducted_amount, b.id as userid, b.admin_id, b.wl_id, b.creator_id from dmt_info as a left join add_cust as b on a.user_id = b.id where a.agent_trid = '".$agent_tranid."' and a.status = 'PENDING' ";
			$dmtDetails = $mysqlClass->mysqlQuery($sqlDMT)->fetch(PDO::FETCH_ASSOC);
						
			if(!isset($dmtDetails['agent_trid'])){
				$helpers->errorResponse("Wrong Transaction Request.");
			} else {
				$USER_ID = $dmtDetails['userid'];
				$WL_ID = $dmtDetails['wl_id'];
				$ADMIN_ID = $dmtDetails['admin_id'];
				$CREATOR_ID = $dmtDetails['creator_id'];
				$tranAgentId = $dmtDetails['agent_trid'];
				$deducted_amount = $dmtDetails['deducted_amount'];
				$amount = $dmtDetails['amount'];
			}
		
		//// Check Admin api_access_key ////
		$apiAccessKey = $userClass->get_api_access_key($ADMIN_ID);
		if(empty($apiAccessKey['api_access_key'])){
			$helpers->errorResponse("Admin API Access Key Not Set.");
		}
		$api_access_key = $apiAccessKey['api_access_key'];
		/////////////////// End //////////////////
		
		
		//// Call netpaisa_curl ////
		$request_param = ['api_access_key' => $api_access_key, 'agent_trid'=> $agent_tranid];

		$status_result_json = $helpers->netpaisa_curl($dmt_status, $request_param);
		//$status_result_json = '{"statuscode":"ERR_AKM","Unauthorized access. API access key is missing."}';
		//$status_result_json = '{"statuscode":"ERR_ATM","transaction_id is invalid"}';
		//$status_result_json = '{"statuscode":"ERR_ACI","status":"Unauthorized access. API access key invalid / Invalid requested server ip."}';
		//$status_result_json = '{"statuscode":"ERR_DNF","status":"Data Not Found !"}';
		//$status_result_json = '{"statuscode":"TXN","status":"Data Found Successfully","data":{"ipay_id":"1200628114011ESZUY","ref_no":"018011832101","opr_id":"018011832101","amount":"2000.00","status":"Transaction Successful","date":"2020-06-28 11:40:13"}}';
		$status_result = json_decode($status_result_json, true);
		
		
		if(isset($status_result['statuscode']) && $status_result['statuscode']=='TXN'){
			/* If Transaction Success  */
			$res_status =  strtoupper($status_result['data']['status']);
			if($res_status == strtoupper('Transaction Successful') || $res_status == 'SUCCESS'){
				
				$txid = $status_result['data']['ipay_id'];
				$bankrefno = $status_result['data']['ref_no'];
				
				$status_txn = 'SUCCESS';
				$valueRIU = array(
							'status' 			=> $status_txn,
							'transaction_id' 	=> $txid,
							'ref_no' 			=> $bankrefno,
							'update_date' 		=> date("Y-m-d H:i:s")
						);
				$mysqlClass->updateData(" dmt_info ", $valueRIU, " where agent_trid = '".$agent_tranid."' ");
				
				$valueRTTrU = array(
							'transaction_id' 	=> $txid
						);
				$mysqlClass->updateData(" retailer_trans ", $valueRTTrU, " where agent_trid = '".$agent_tranid."' ");
				
				$valueWlTrU = array(
							'transaction_id' 	=> $txid
						);	
				$mysqlClass->updateData(" wl_trans ", $valueWlTrU, " where agent_trid = '".$agent_tranid."' ");
				
				
				
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

				$master_distributor_commission = 0;
				$distributor_commission = 0;
				
				$mysqlClass->mysqlQuery("START TRANSACTION");

				/* Master Distributor Commission */
				
				if($MASTER_DISTRIBUTOR > 0){
					$mdt_opening_balance = $userClass->check_user_balance($MASTER_DISTRIBUTOR, " FOR UPDATE");
					$mdt_closing_balance = $userClass->update_wallet_balance_add_amount($MASTER_DISTRIBUTOR,$master_distributor_commission);
					
					$valueMDTR = array(
									'dist_retail_wl_admin_id'	=>	$USER_ID,
									'transaction_id'			=>	$txid,
									'agent_trid'				=>	$agent_tranid,
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
								'agent_trid'				=>	$agent_tranid,
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
				
				
				
				$response['ERROR_CODE'] = 0;
				$response['MESSAGE'] = 'TRANSACTION SUCCESS';
			}
			else if($res_status == 'FAILED' || $res_status == 'REFUND' || $res_status == 'REFUNDED' || $res_status == 'FAILURE'){
				
				
				
				$valueRIU = array(
							'status' 			=> "FAILED",
							'update_date' 		=> date("Y-m-d H:i:s")
						);
				$mysqlClass->updateData(" dmt_info ", $valueRIU, " where agent_trid = '".$agent_tranid."' ");
				
								
				$tranId = $helpers->transaction_id_generator('REFUND',4);
				$mysqlClass->mysqlQuery("START TRANSACTION");
				
				$checkRetRef = $mysqlClass->fetchRow(" retailer_trans ", ' id ', " where refund_id = '".$agent_tranid."' ");
				if(isset($checkRetRef['id']) && $checkRetRef['id'] > 0 ){ } else {
					/* Retailer Refund */					
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
							'refund_id'					=>	$agent_tranid,
							'retailer_id '				=>	$USER_ID
						);
					$rtTran_lastid = $mysqlClass->insertData(" retailer_trans ", $valueRTRF);
				}
				
				$checkWlRef = $mysqlClass->fetchRow(" wl_trans ", ' id ', " where refund_id = '".$agent_tranid."' ");				
				if(isset($checkWlRef['id']) && $checkWlRef['id'] > 0 ){ } else {
					/* WL Refund */
					$wlTran = $mysqlClass->fetchRow(" wl_trans ", ' withdrawl ', " where agent_trid = '".$agent_tranid."' ");
					$wl_deducted_amount = (isset($wlTran['withdrawl'])) ? $wlTran['withdrawl'] : 0;
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
							'refund_id'					=>	$agent_tranid,
							'wluser_id'					=>	$WL_ID
						);
					$rtTran_lastid = $mysqlClass->insertData(" wl_trans ", $valueWLRF);
				}
				
				$mysqlClass->mysqlQuery("COMMIT");   ///COMMIT #############
				
				
				$response['ERROR_CODE'] = 1;
				$response['MESSAGE'] = 'TRANSACTION FAILED';
			} else {
				$response['ERROR_CODE'] = 1;
				$response['MESSAGE'] = 'TRANSACTION PENDING';
			}
			

		} 
		else if ( isset($status_result['statuscode']) && $status_result['statuscode']=='ERR_DNF' ) {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = $status_result['status'];
					
		} else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = 'Response Error! Contact to admin';			
		}
		//$response['DATAJSON'] = $status_result_json;
		/////////////////// End //////////////////
		
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	die();
?>