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
		
			$tranDetails = $mysqlClass->mysqlQuery("Select agent_trid,user_id,wl_id,txntype,amount from aeps_info where agent_trid = '".$agent_tranid."' and status = 'PENDING'")->fetch(PDO::FETCH_ASSOC);
			if(isset($tranDetails['agent_trid']) && !empty($tranDetails['agent_trid'])){
				$USER_ID = $tranDetails['user_id'];
				$WL_ID = $tranDetails['wl_id'];
				$txntype = $tranDetails['txntype'];
				
			} else {
				$helpers->errorResponse(" Transaction Not found.");
			}
			
			$retailsDetails = $mysqlClass->mysqlQuery("Select aeps_balance,package_id,creator_id,admin_id,wallet_balance from add_cust where id = '".$USER_ID."' ")->fetch(PDO::FETCH_ASSOC);
			$RTaepsOpeningBal = $retailsDetails['aeps_balance'];
			$package_id_rt = $retailsDetails['package_id'];
			$CREATOR_ID = $retailsDetails['creator_id'];
			$ADMIN_ID = $retailsDetails['admin_id'];
			$rt_opening_balance = $retailsDetails['wallet_balance'];
			
		
		//// Check Admin api_access_key ////
		$apiAccessKey = $userClass->get_api_access_key($ADMIN_ID);
		if(empty($apiAccessKey['api_access_key'])){
			$helpers->errorResponse("Admin API Access Key Not Set.");
		}
		$api_access_key = $apiAccessKey['api_access_key'];
		/////////////////// End //////////////////
		
		
		//// Call netpaisa_curl ////
		$request_param = ['api_access_key' => $api_access_key, 'transaction_id'=> $agent_tranid];

		$status_result_json = $helpers->netpaisa_curl($aeps_tran_status, $request_param);
		
		$status_result = json_decode($status_result_json, true);
		
		if(isset($status_result['status_code']) && $status_result['status_code']=='00'){
				
				$transaction_id = $status_result['data']['transaction_id'];
				$message = $status_result['data']['message'];
				$status = $status_result['data']['status'];
				
				if(isset($status_result['data']['status']) && ($status_result['data']['status']=='Success' ||  $status_result['data']['status']=='SUCCESS')){
					
					/* $valueAEPS = array(
							'transaction_id'		=>	$transaction_id,
							'message'				=>	$message,
							'status'				=>	'SUCCESS'
						);
					$aeps_lastid = $mysqlClass->updateData(" aeps_info ", $valueAEPS, " where agent_trid = '".$agent_tranid."' and status = 'PENDING' and user_id = '".$USER_ID."'" ); */
					
					$response['ERROR_CODE'] = 0;
					$response['MESSAGE'] = "Transaction Success For Update Contact To Support";
					$response['DATA'] = $status_result['data'];
				} else {
					$valueAEPS = array(
							'transaction_id'		=>	$transaction_id,
							'message'				=>	$message,
							'status'				=>	$status
						);
					$aeps_lastid = $mysqlClass->updateData(" aeps_info ", $valueAEPS, " where agent_trid = '".$agent_tranid."' and status = 'PENDING' and user_id = '".$USER_ID."'" );
					
					$response['ERROR_CODE'] = 0;
					$response['MESSAGE'] = $status;
					$response['DATA'] = $status_result['data'];
				}
				
		} 
		else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = isset($status_result['status_msg']) ? $status_result['status_msg'] : 'Error' ;
			
		}
		/////////////////// End //////////////////
		
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	die();
?>