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
$total_deducted_amount = 0;
$wl_deducted_amount = 0;
$transStruct = array();
$retailsDetails = $mysqlClass->mysqlQuery("Select min_amt_capping,package_id from add_cust where id = '".$USER_ID."'")->fetch(PDO::FETCH_ASSOC);
$min_amt_capping = $retailsDetails['min_amt_capping'];
$package_id = $retailsDetails['package_id'];

if(empty($package_id) || $package_id <= 0){
	$helpers->errorResponse('Package Not set.');
}
$errorMsg = '';

//// Calculate Surcharge (Loop Start) ////	
for ( $k = 1; $k <= $trans_count; $k++ ) {	
	$remit_amounts = ${"trans_".$k};
	//// Get DTM Slabs ////	
	$slabNetwork = $userClass->get_dmt_slab($WL_ID,$remit_amounts);	
	/////////////////// End //////////////////
	

		$DTRTCommission = $userClass->commission_dt_rt($slabNetwork,$package_id,$remit_amounts);
		if(empty($DTRTCommission['commission_status']) || $DTRTCommission['commission_status'] != 'SET'){
			$errorMsg = 'DMT Commission Not Set.';
		}
		
		$ADCommission = $userClass->commission_wl_dmt('DMT',$WL_ID,$remit_amounts);
		if(empty($ADCommission['commission_status']) || $ADCommission['commission_status'] != 'SET'){
			$errorMsg = 'Admin Commission Issue, Contact to admin.';
		}
		
		$retaile_surcharge = $DTRTCommission['rt_commission'];
		$distributor_surcharge = $DTRTCommission['dt_commission'];
		$wl_surcharge = $ADCommission['wl_commission'];
		
		$surcharge = $surcharge + $retaile_surcharge;
		$total_deducted_amount = $total_deducted_amount + $remit_amounts + $retaile_surcharge;
		$wl_deducted_amount = $wl_deducted_amount + $remit_amounts + $wl_surcharge;
		
		$transStruct['retailer_struct'][] = array('surcharge'=>$retaile_surcharge, 'deducted_amt'=>($remit_amounts + $retaile_surcharge), 'remit_amount'=> $remit_amounts );
	}
/////////////////// End //////////////////	
		$retailerOB = $userClass->check_user_balance($USER_ID,"");
		if($retailerOB <= $min_amt_capping + $total_deducted_amount){
			$errorMsg = 'Insufficient Balance!';
		}
		
		$wlOB = $userClass->check_user_balance($WL_ID,"");
		if($wlOB <= $wl_deducted_amount + 1){
			$errorMsg = 'Admin Wallet Issue !';
		}
		
		if(!empty($errorMsg)){
			$helpers->errorResponse($errorMsg);
		}
		
		$response['ERROR_CODE'] = 0;
		$response['MESSAGE'] = 'Transaction Allow';
		$response['AMOUNT'] = $amount;
		$response['DEDUCTED_AMT'] = $total_deducted_amount;	
		$response['SURCHARGE'] = $surcharge;	
		$response['TRANS_STRUCT'] = $transStruct;	
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>