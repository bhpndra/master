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

$columns = " religare_proposal_id ";
$res = $mysqlClass->fetchRow('religare_proposals', $columns, "where user_id!='' order by religare_proposal_id desc ");
if($res){
$guid=$res['religare_proposal_id']+1;
}
else{$guid=1;}		
	//// Check Rquired Parameters Isset ////
		$guid = $guid;
		$birthDt = $post['birthDt'];
		$firstName = $post['firstName'];
		$genderCd = $post['genderCd'];
		$lastName = $post['lastName'];
		$addressLine1Lang1 = $post['addressLine1Lang1'];
    	$addressLine2Lang1 = $post['addressLine2Lang1'];
		$cityCd = $post['cityCd'];
		$pinCode = $post['pinCode'];
		$stateCd = $post['stateCd'];
		$addressLine1Lang1c = $post['addressLine1Lang1c'];
		$addressLine2Lang1c = $post['addressLine2Lang1c'];
		$cityCdc = $post['cityCdc'];
		$pinCodec = $post['pinCodec'];
		$stateCdc = $post['stateCdc'];
		$contactNum = $post['contactNum'];
		$emailAddress = $post['emailAddress'];
		$titleCd = $post['titleCd'];
		$sumInsured = $post['sumInsured'];
		
		if(empty($birthDt) || empty($firstName) || empty($genderCd) || empty($lastName) || empty($addressLine1Lang1) || empty($cityCd) || empty($pinCode) || empty($stateCd) || empty($addressLine1Lang1c) || empty($cityCdc) || empty($pinCodec) || empty($stateCdc) || empty($contactNum) || empty($emailAddress) || empty($titleCd) || empty($sumInsured)){
			$helpers->errorResponse("Please fill all required input data");
		}
	/////////////////// End //////////////////
			
			$request_param = array(
								'guid' => $guid,
								'api_access_key' => $api_access_key,
								'birthDt' => $birthDt,
								'firstName' => $firstName,
								'genderCd' => $genderCd,
								'lastName' => $lastName,
								'addressLine1Lang1' => $addressLine1Lang1,
								'addressLine2Lang1' => $addressLine2Lang1,
								'cityCd' => $cityCd,
								'pinCode' => $pinCode,
								'stateCd' => $stateCd,
								'addressLine1Lang1c' => $addressLine1Lang1c,
								'addressLine2Lang1c' => $addressLine2Lang1c,
								'cityCdc' => $cityCdc,
								'pinCodec' => $pinCodec,
								'stateCdc' => $stateCdc,
								'contactNum' => $contactNum,
								'emailAddress' => $emailAddress,
								'titleCd' => $titleCd,
								'sumInsured' => $sumInsured
							);
			$transaction_result_json = $helpers->netpaisa_curl($get_religare_proposal, $request_param);
			
			//$transaction_result_json = '{ "data": { "status": "PENDING", "txid": "TEST'.time().'", "clientid": "'.$tranAgentId.'", "number": "'.$number.'", "amount": "'.$amount.'", "opening_balance": "986.11", "closing_balance": "976.19" }, "status_code": "00", "status_msg": "Transaction Success" }';
			
			$transaction_result  = json_decode($transaction_result_json, true);
		
		
		
		if(isset($transaction_result['status_code'])){
			if($transaction_result['status_code']=='00'){

				$responseRC=json_decode($transaction_result['responseRC']);
				$post_data = array("titleCd"=>$_POST['titleCd'],
  "user_id"=>$USER_ID,
  "guid"=>$guid,
  "firstName"=>$_POST['firstName'],
  "lastName"=>$_POST['lastName'],
  "genderCd"=>$_POST['genderCd'],
  "birthDt"=>$_POST['birthDt'],
  "contactNum"=>$_POST['contactNum'],
  "emailAddress"=>$_POST['emailAddress'],
  "addressLine1Lang1"=>$_POST['addressLine1Lang1'],
  "addressLine2Lang1"=>$_POST['addressLine2Lang1'],
  "stateCd"=>$_POST['stateCd'],
  "cityCd"=>$_POST['cityCd'],
  "pinCode"=>$_POST['pinCode'],
  "addressLine1Lang1c"=>$_POST['addressLine1Lang1c'],
  "addressLine2Lang1c"=>$_POST['addressLine2Lang1c'],
  "stateCdc"=>$_POST['stateCdc'],
  "cityCdc"=>$_POST['cityCdc'],
  "pinCodec"=>$_POST['pinCodec'],
  "proposalNum"=>$responseRC->intPolicyDataIO->policy->proposalNum
  );
  $rlProp_lastid = $mysqlClass->insertData(" religare_proposals ", $post_data);
				
				
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
			//$response['MESSAGE'] = $transaction_result['responseRC'];
			$response['MESSAGE'] = "Api server not working";
		}
		
	/////////////////// End //////////////////
		
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>