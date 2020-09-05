<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("../config.php");
	require("classes/db_class.php");
	require("classes/comman_class.php");
	require("classes/user_class.php");
	require("classes/jwt_encode_decode.php");
	$helpers = new Helper_class();
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
		$trans_agent_id = $post['trans_agent_id'];
		$invoice_type = $post['invoice_type'];
		if(!isset($invoice_type) || empty($invoice_type)){
			$helpers->errorResponse('Invoice type is missing.');
		}
		$filter  = '';
		if(!isset($trans_agent_id) || empty($trans_agent_id)){
			$helpers->errorResponse('Transaction_Agent_id is missing.');
		} else {
			
			if($invoice_type=="RECHARGE"){
				$filter = " and (concat_ws(' ',transaction_id,agent_trid) like '%".$trans_agent_id."%') ";
			
				$columns = " transaction_id, agent_trid, mobile, operator, amount, deducted_amount, commission, rech_type, date_created, status ";
				$resRech = $mysqlClass->fetchRow('recharge_info', $columns, " where user_id = '".$USER_ID."' ".$filter );
				
				if(is_array($resRech) && count($resRech)>0){
					$network = $mysqlClass->fetchRow('network', ' operator_name' , "  where np_operator_code = '".$resRech['operator']."' ");
					$resRech['operator_name'] = $network['operator_name'];
					$response['ERROR_CODE'] = 0;
					$response['MESSAGE'] = 'RECHARGE';
					$response['DATA'] = $resRech;
				} else {
					$response['ERROR_CODE'] = 1;
					$response['MESSAGE'] = 'RECHARGE Invoice Not Found';
				}
			} else if($invoice_type=="DMT") {
				$filter = " and (concat_ws(' ',transaction_id,agent_trid) like '%".$trans_agent_id."%') ";
			
				$columns = " transaction_id, agent_trid, ref_no, mobile, amount, txn_group_id, deducted_amount, bene_code, bene_ac, bene_name, ifsc_code, date_created, status ";
				$resMTG = $mysqlClass->fetchRow('dmt_info', ' txn_group_id ', " where user_id = '".$USER_ID."' ".$filter );
				if(!empty($resMTG['txn_group_id'])){
					$resMT = $mysqlClass->fetchAllData('dmt_info', $columns, " where user_id = '".$USER_ID."' and txn_group_id = '".$resMTG['txn_group_id']."' " );
				} else {
					$resMT = $mysqlClass->fetchAllData('dmt_info', $columns, " where user_id = '".$USER_ID."' ".$filter );
				}
				//print_r($resMT);

				if(is_array($resMT) && count($resMT)>0){
					$response['ERROR_CODE'] = 0;
					$response['MESSAGE'] = 'DMT';
					$response['DATA'] = $resMT;
				} else {
					$response['ERROR_CODE'] = 1;
					$response['MESSAGE'] = 'DMT Invoice Not Found';
				}
			} else if($invoice_type=="AEPS") {
				$filter = " and (concat_ws(' ',transaction_id,agent_trid) like '%".$trans_agent_id."%') ";
			
				$columns = " transaction_id, agent_trid, txntype, amount, message, status, date_created, uid, mobile ";
				
				$resMT = $mysqlClass->fetchAllData('aeps_info', $columns, " where user_id = '".$USER_ID."' ".$filter );
				
				//print_r($resMT);

				if(is_array($resMT) && count($resMT)>0){
					$response['ERROR_CODE'] = 0;
					$response['MESSAGE'] = 'AEPS';
					$response['DATA'] = $resMT;
				} else {
					$response['ERROR_CODE'] = 1;
					$response['MESSAGE'] = 'AEPS Invoice Not Found';
				}
			} else if($invoice_type=="BBPS"){
				$filter = " and (concat_ws(' ',transaction_id,agent_trid) like '%".$trans_agent_id."%') ";
			
				$columns = " transaction_id, agent_trid, operator_name, customer_mobile, consumer_number, amount, deducted_amount, commission, bill_type, date_created, status ";
				$resRech = $mysqlClass->fetchRow('billpayment_info', $columns, " where user_id = '".$USER_ID."' ".$filter );
				
				if(is_array($resRech) && count($resRech)>0){
					$response['ERROR_CODE'] = 0;
					$response['MESSAGE'] = 'BBPS';
					$response['DATA'] = $resRech;
				} else {
					$response['ERROR_CODE'] = 1;
					$response['MESSAGE'] = 'BBPS Invoice Not Found';
				}
			}
		}

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>