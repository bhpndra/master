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
	$mysqlClass = new Mysql_class();
	$userClass = new User_class();
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
		$mobile = $helpers->validateMobile($post['mobile']);
		$pan_no = strtoupper($post['pan_no']);
		$email = $post['email'];
		$first_name = $post['first_name'];
		$middle_name = $post['middle_name'];
		$last_name = $post['last_name'];
		if(empty($mobile)){
			$helpers->errorResponse("Number is missing or not valid.");
		}
		if(empty($first_name)){
			$helpers->errorResponse("First Name is missing.");
		}
		if(empty($last_name)){
			$helpers->errorResponse("Last Name is missing.");
		}
		if(empty($email)){
			$helpers->errorResponse("Email is missing.");
		}
		if(empty($pan_no)){
			$helpers->errorResponse("Pan No. is missing.");
		}
		/////////////////// End //////////////////
		
		//// Call netpaisa_curl ////
		$request_param = array('api_access_key' => $api_access_key,
						'mobile'=>$mobile,
						'first_name'=>$first_name,
						'middle_name'=>$middle_name,
						'last_name'=>$last_name,
						'email'=>$email,
						'pan_no'=>$pan_no,
						);

		$outlet_json = $helpers->netpaisa_curl($aeps_bankit_registration, $request_param);
		//$outlet_json = '{"ERR_STATE":0, "IDS":"14##15", "ID_NAME":"Aadhaar / Voter ID ## Photo @ Store ", "DATA":{ "statuscode":"TXN", "status":"Transaction Successful", "outlet_id":"12027", "mobile_number":"9876543210", "email_id":"your_email@gmail.in", "outlet_name":"Company Name", "contact_person":"YOUR NAME", "pan_no":"AZDPG6750C", "kyc_status":"0", "outlet_status":"1"}}';
		$outlet_result = json_decode($outlet_json, true);
		
		if($outlet_result['ERR_STATE']==0 && isset($outlet_result['outlet_id']) && $outlet_result['outlet_id']!=""){ 
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = $outlet_result['MSG'];			
			$valueOutlet = array(
							'user_id'		=> $USER_ID,
							'outletid'		=> $outlet_result['outlet_id'],
							'first_name'	=> $first_name,
							'middle_name'	=> $middle_name,
							'last_name'		=> $last_name,
							'email'			=> $email,
							'mobile'		=> $mobile,
							'company'		=> '',
							'address'		=> '',
							'pincode'		=> '',
							'pan_no'		=> $pan_no,
							'registration_date'	=> date("Y-m-d H:i:s"),
							'update_date'	=> date("Y-m-d H:i:s"),
							'outlet_status'		=> 'PENDING',
							'outlet_kyc'		=> 'PENDING',
							'status'			=> 'SUCCESS',
						);
			$mysqlClass->insertData('outlet_kyc_bankit',$valueOutlet);
		} else {
			if(isset($outlet_result['MSG'])){
				$helpers->errorResponse($outlet_result['MSG']);
			} else {
				$helpers->errorResponse('Something went to wrong !');
			}
		}

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>