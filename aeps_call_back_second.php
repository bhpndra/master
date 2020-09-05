<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("config.php");
	require("api/classes/db_class.php");
	require("api/classes/comman_class.php");
	require("api/classes/user_class.php");
	$helpers = new Helper_class();
	$userClass = new user_class();
	$mysqlClass = new Mysql_class();
	
	$request_data = (array) json_decode(file_get_contents('php://input'), TRUE);

	
	if(isset($request_data)){
		
		$post = $helpers->clearSlashes($request_data);
		
		
	//// Check Rquired Parameters Isset ////
		$agent_trid = $post['agent_trid'];
		$transaction_id = $post['transaction_id'];
		$message = $post['message'];
		$txn_status = strtoupper($post['txn_status']);
		$amount = $post['amount'];
		$transaction_date = empty($post['transaction_date']) ? date("Y-m-d H:i:s") : $post['transaction_date'];
		$aadhaar_no = $post['aadhaar_no'];
		$mobile_no = $post['mobile_no'];
		$outletid = $post['outletid'];
		$bcid = $post['bcid'];
		$txntype = strtoupper($post['txntype']);
		
		if(empty($agent_trid)){
			$helpers->errorResponse("Agent Transaction Id is missing or not valid.");
		}
		if(empty($amount) || $amount <= 0){
			$helpers->errorResponse("Amount is missing or invalid.");
		}
		if(empty($transaction_id)){
			$helpers->errorResponse("Transaction Id code is missing.");
		}
		if(empty($txn_status)){
			$helpers->errorResponse("Status is missing.");
		}
	/////////////////// End //////////////////
	
	$outletDetails = $mysqlClass->mysqlQuery("Select user_id from outlet_kyc_bankit where outletid = '".$outletid."' ")->fetch(PDO::FETCH_ASSOC);
	
	if(!isset($outletDetails['user_id']) || $outletDetails['user_id'] <= 0 || empty($outletDetails['user_id'])){
		$helpers->errorResponse("Outlet not found.");
	}
	
	$valueLog = array(
				'agent_trid' => $agent_trid,
				'api_response' => json_encode($request_data),
				'callback_key' => 'CALLBACK_BANKIT',
				'log_date' => date("Y-m-d H:i:s")
			);
	$mysqlClass->insertData(" aeps_log ", $valueLog);
	

	$userDetails = $mysqlClass->mysqlQuery("Select id,wl_id from add_cust where id = '".$outletDetails['user_id']."' ")->fetch(PDO::FETCH_ASSOC);
	if(isset($userDetails['id']) && !empty($userDetails['id'])){
		$USER_ID = $userDetails['id'];
		$WL_ID = $userDetails['wl_id'];
	} else {
		$helpers->errorResponse(" User Not found.");
	}
	
	
	//// Transaction start ////	
		if(!empty($USER_ID) && !empty($WL_ID) && !empty($txntype) && $txntype == 'CREDIT' && $txn_status == 'SUCCESS'){ $txntype = 'WAP';
			$mysqlClass->mysqlQuery("START TRANSACTION");  ///START TRANSACTION **********
			
			$retailsDetails = $mysqlClass->mysqlQuery("Select aeps_balance,package_id,creator_id,admin_id,wallet_balance from add_cust where id = '".$USER_ID."'  FOR UPDATE")->fetch(PDO::FETCH_ASSOC);
			$RTaepsOpeningBal = $retailsDetails['aeps_balance'];
			$package_id_rt = $retailsDetails['package_id'];
			$CREATOR_ID = $retailsDetails['creator_id'];
			$ADMIN_ID = $retailsDetails['admin_id'];
			$rt_opening_balance = $retailsDetails['wallet_balance'];
						
			$wlDetails = $mysqlClass->mysqlQuery("Select aeps_balance,package_id,wallet_balance from add_cust where id = '".$WL_ID."'  FOR UPDATE")->fetch(PDO::FETCH_ASSOC);
			$WLaepsOpeningBal = $wlDetails['aeps_balance'];
			$package_id_wl = $wlDetails['package_id'];
			$wl_opening_balance = $wlDetails['wallet_balance'];
						
						
			/* Get RT & DT AEPS Commission */			
			$slabNetwork = $userClass->get_aeps_slab($WL_ID,$amount);	
			
			if($slabNetwork!="NIR" && $slabNetwork != ''){
				$DTRTCommission = $userClass->commission_dt_rt($slabNetwork,$package_id_rt,$amount);
				if(empty($DTRTCommission['commission_status']) || $DTRTCommission['commission_status'] != 'SET'){
					$retaile_commission = 0;
					$distributor_commission = 0;
				}
				$retaile_commission = $DTRTCommission['rt_commission'];
				$distributor_commission = $DTRTCommission['dt_commission'];
				$master_distributor_commission = $DTRTCommission['md_commission'];
			} else {
				$retaile_commission = 0;
				$distributor_commission = 0;
				$master_distributor_commission = 0;
			}
						
			/* Get WL AEPS Commission */
			if($amount > 500 && $amount <= 3000){
				$wl_network = 'AEPS';
			} else if ($amount > 3000){
				$wl_network = 'AEPSF';
			} else {
				$wl_network = '';
			}
			
			if($wl_network!=''){
				$aeps_wl_Comm = $mysqlClass->mysqlQuery("Select commission_type,wl_commission from wl_package_commission where package_id = '".$package_id_wl."' and  network = '".$wl_network."'")->fetch(PDO::FETCH_ASSOC);
				$wl_commission_type = $aeps_wl_Comm['commission_type'];
				$wlCommission = $aeps_wl_Comm['wl_commission'];
				
				if($wl_commission_type == 'FLAT') {
					$wl_commission = $wlCommission;
				} else if($wl_commission_type == 'PERCENT') {
					$wl_commission   = round( ($amount * $wlCommission/100), 2);
				} else {
					$wl_commission = 0;
				}
				
			} else {
				$wl_commission = 0;
			}
			
			/* echo $wl_commission."<br/>";
			echo $distributor_commission."<br/>";
			echo $retaile_commission."<br/>";  */
			
			/* Update AEPS Transaction */
			$valueAEPS = array(
					'user_id'				=>	$USER_ID,
					'wl_id'					=>	$WL_ID,
					'outletid'				=>	$outletid,
					'transaction_id'		=>	$transaction_id,
					'agent_trid'			=>	$agent_trid,
					'terminalid'			=>	'',
					'bcid'					=>	$bcid,
					'txntype'				=>	$txntype,
					'amount'				=>	$amount,
					'bank_iin'				=>	'',
					'message'				=>	$message,
					'status'				=>	$txn_status,
					'uid'					=>	$aadhaar_no,
					'mobile'				=>	$mobile_no,
					'date_created'			=>	$transaction_date
				);
			$aeps_lastid = $mysqlClass->insertData(" aeps_info ", $valueAEPS);
			if($aeps_lastid > 0){ 
			
				/* Update RT AEPS Wallet Balance */
				$rtAEPSClosing = $amount + $RTaepsOpeningBal;				
				$rt_closing_balance = $rt_opening_balance + $retaile_commission;
				$RTvalueABalU = array("aeps_balance" => $rtAEPSClosing, "wallet_balance"=>$rt_closing_balance);				
				$mysqlClass->updateData(" add_cust ", $RTvalueABalU, " where id = '".$USER_ID."' " );
				
				/* Update WL AEPS Wallet Balance */
				$wlAEPSClosing = $amount + $WLaepsOpeningBal;		
				$wl_closing_balance = $wl_opening_balance + $wl_commission;
				$WLvalueABalU = array("aeps_balance" => $wlAEPSClosing, "wallet_balance"=>$wl_closing_balance);
				$mysqlClass->updateData(" add_cust ", $WLvalueABalU, " where id = '".$WL_ID."' " );
				
								
				/* RT Commission */				
				$valueRTRF = array(
						'ret_dest_wl_admin_id'		=>	$ADMIN_ID,
						'transaction_id'			=>	$transaction_id,
						'agent_trid'				=>	$agent_trid,
						'opening_balance'			=>	$rt_opening_balance,
						'deposits'					=>	$retaile_commission,
						'withdrawl'					=>	0,
						'balance'					=>	$rt_closing_balance,
						'date_created'				=>	date("Y-m-d H:i:s"),
						'created_by'				=>	$ADMIN_ID,
						'creator_type'				=>	'ADMIN',
						'comments'					=>	'AEPS Commission',
						'tr_type'					=>	'AEPS',
						'refund_id'					=>	'',
						'retailer_id '				=>	$USER_ID
					);
				$rtTran_lastid = $mysqlClass->insertData(" retailer_trans ", $valueRTRF); 
				
				/* WL Commission */
				
				$valueWLRF = array(
						'ret_dest_wl_admin_id'		=>	$ADMIN_ID,
						'transaction_id'			=>	$transaction_id,
						'agent_trid'				=>	$agent_trid,
						'user_type'					=>	'ADMIN',
						'opening_balance'			=>	$wl_opening_balance,
						'deposits'					=>	$wl_commission,
						'withdrawl'					=>	0,
						'balance'					=>	$wl_closing_balance,
						'commission_surcharge'		=>	0,
						'transaction_amount'		=>	0,
						'date_created'				=>	date("Y-m-d H:i:s"),
						'created_by'				=>	$ADMIN_ID,
						'creator_type'				=>	'ADMIN',
						'comments'					=>	'AEPS Commission',
						'tr_type'					=>	'AEPS',
						'refund_id'					=>	'',
						'wlUSER_ID'					=>	$WL_ID
					);
				$rtTran_lastid = $mysqlClass->insertData(" wl_trans ", $valueWLRF);
				
				
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
				
				/* Master Distributor Commission */
				
				if($MASTER_DISTRIBUTOR > 0){
					$mdt_opening_balance = $userClass->check_user_balance($MASTER_DISTRIBUTOR, " FOR UPDATE");
					$mdt_closing_balance = $userClass->update_wallet_balance_add_amount($MASTER_DISTRIBUTOR,$master_distributor_commission);
					
					$valueMDTR = array(
									'dist_retail_wl_admin_id'	=>	$USER_ID,
									'transaction_id'			=>	$transaction_id,
									'agent_trid'				=>	$agent_trid,
									'opening_balance'			=>	$mdt_opening_balance,
									'deposits'					=>	$master_distributor_commission,
									'withdrawl'					=>	0,
									'balance'					=>	$mdt_closing_balance,
									'date_created'				=>	date("Y-m-d H:i:s"),
									'created_by'				=>	$USER_ID,
									'creator_type'				=>	'RETAILER',
									'comments'					=>	'AEPS Commission(Master Distributor)',
									'tr_type'					=>	'AEPS',
									'dist_id '					=>	$MASTER_DISTRIBUTOR
								);
					$mdtTran_lastid = $mysqlClass->insertData(" distributor_trans ", $valueMDTR);
				}
				
				
				/* DT Commission */
				$dt_opening_balance = $userClass->check_user_balance($CREATOR_ID, " FOR UPDATE");
				$dt_closing_balance = $userClass->update_wallet_balance_add_amount($CREATOR_ID,$distributor_commission);
				
				$valueDTR = array(
								'dist_retail_wl_admin_id'	=>	$USER_ID,
								'transaction_id'			=>	$transaction_id,
								'agent_trid'				=>	$agent_trid,
								'opening_balance'			=>	$dt_opening_balance,
								'deposits'					=>	$distributor_commission,
								'withdrawl'					=>	0,
								'balance'					=>	$dt_closing_balance,
								'date_created'				=>	date("Y-m-d H:i:s"),
								'created_by'				=>	$USER_ID,
								'creator_type'				=>	'RETAILER',
								'comments'					=>	'AEPS Commission',
								'tr_type'					=>	'AEPS',
								'dist_id '					=>	$CREATOR_ID
							);
				$dtTran_lastid = $mysqlClass->insertData(" distributor_trans ", $valueDTR);
				
				$response['ERROR_CODE'] = 0;
				$response['MESSAGE'] = "Transaction Update Success";
			
			} else {
				$helpers->errorResponse('Transaction error, Contact to support.');
			}			
			
			$mysqlClass->mysqlQuery("COMMIT");
		} 
		else {
			$valueAEPS = array(
					'user_id'				=>	$USER_ID,
					'wl_id'					=>	$WL_ID,
					'outletid'				=>	$outletid,
					'transaction_id'		=>	$transaction_id,
					'agent_trid'			=>	$agent_trid,
					'terminalid'			=>	'',
					'bcid'					=>	$bcid,
					'txntype'				=>	$txntype,
					'amount'				=>	$amount,
					'bank_iin'				=>	'',
					'message'				=>	$message,
					'status'				=>	$txn_status,
					'uid'					=>	$aadhaar_no,
					'mobile'				=>	$mobile_no,
					'date_created'			=>	$transaction_date
				);
			$aeps_lastid = $mysqlClass->insertData(" aeps_info ", $valueAEPS);
		}
	} else {
		$helpers->errorResponse('Request error');
	}
	
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>