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
		$name = $post['name'];
		$email = $post['email'];
		$company = $post['company'];
		$pan_no = $post['pan_no'];
		$pincode = $post['pincode'];
		$address = $post['address'];
		$otp = $post['otp'];
		if(empty($mobile)){
			$helpers->errorResponse("Number is missing or not valid.");
		}
		if(empty($name)){
			$helpers->errorResponse("Name is missing.");
		}
		if(empty($email)){
			$helpers->errorResponse("Email is missing.");
		}
		if(empty($company)){
			$helpers->errorResponse("Company is missing.");
		}
		if(empty($pan_no)){
			$helpers->errorResponse("Pan No. is missing.");
		}
		if(empty($pincode)){
			$helpers->errorResponse("Pincode is missing.");
		}
		if(empty($address)){
			$helpers->errorResponse("Address is missing.");
		}
		if(empty($otp)){
			$helpers->errorResponse("OTP is missing.");
		}
		/////////////////// End //////////////////
		
		//// Call netpaisa_curl ////
		$request_param = ['api_access_key' => $api_access_key,'mobile'=>$mobile,'name'=>$name,'email'=>$email,'pincode'=>$pincode,'company'=>$company,'pan'=>$pan_no,'address'=>$address,'otp'=>$otp];

		$outlet_json = $helpers->netpaisa_curl($outlet_register, $request_param);
		//$outlet_json = '{"ERR_STATE":0, "IDS":"14##15", "ID_NAME":"Aadhaar / Voter ID ## Photo @ Store ", "DATA":{ "statuscode":"TXN", "status":"Transaction Successful", "outlet_id":"12027", "mobile_number":"9876543210", "email_id":"your_email@gmail.in", "outlet_name":"Company Name", "contact_person":"YOUR NAME", "pan_no":"AZDPG6750C", "kyc_status":"0", "outlet_status":"1"}}';
		$outlet_result = json_decode($outlet_json, true);
		
		if($outlet_result['ERR_STATE']==0 && isset($outlet_result['DATA']['statuscode']) && $outlet_result['DATA']['statuscode']=="TXN"){ 
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = $outlet_result['DATA']['status'];			
			$valueOutlet = array(
							'user_id'		=> $USER_ID,
							'outletid'		=> $outlet_result['DATA']['outlet_id'],
							'name'			=> $outlet_result['DATA']['contact_person'],
							'email'			=> $outlet_result['DATA']['email_id'],
							'mobile'		=> $outlet_result['DATA']['mobile_number'],
							'company'		=> $outlet_result['DATA']['outlet_name'],
							'address'		=> $address,
							'pincode'		=> $pincode,
							'pan_no'		=> $outlet_result['DATA']['pan_no'],
							'registration_date'	=> date("Y-m-d H:i:s"),
							'outlet_status'		=> $outlet_result['DATA']['outlet_status'],
							'outlet_kyc'		=> $outlet_result['DATA']['kyc_status'],
							'status'			=> $outlet_result['DATA']['kyc_status'],
						);
			$mysqlClass->insertData('outlet_kyc',$valueOutlet);
		} else {
			if(isset($outlet_result['ERR_STATE']) && isset($outlet_result['MSG'])){
				//$helpers->errorResponse($outlet_result['MSG']);
				$response['ERROR_CODE'] = 1;
				$response['MESSAGE'] = $outlet_result['MSG'];
				//$response['DATA'] = $outlet_result;
				
				echo json_encode($response); die();
			} else {
				//$helpers->errorResponse('Something went to wrong !');
				$response['ERROR_CODE'] = 1;
				$response['MESSAGE'] ='Something went to wrong !';
				//$response['DATA'] = $outlet_result;
				
				echo json_encode($response); die();
			}
		}

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>