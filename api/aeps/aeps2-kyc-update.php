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

	 function defineVariable($var){
		return !empty($var) ? $var : '';
	 }
	
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
		$outlet_id = $post['outlet_id'];
		if(empty($mobile)){
			$helpers->errorResponse("Number is missing or not valid.");
		}
		if(empty($pan_no)){
			$helpers->errorResponse("Pan No. is missing.");
		}
		if(empty($outlet_id)){
			$helpers->errorResponse("Outlet Id. is missing.");
		}
		/////////////////// End //////////////////
$postdata['api_access_key'] = $api_access_key;
$postdata['first_name'] = defineVariable($post['first_name']);
$postdata['middle_name'] = defineVariable($post['middle_name']);	
$postdata['last_name'] = defineVariable($post['last_name']);
$postdata['mobile'] = defineVariable($post['mobile']);
$postdata['email'] = defineVariable($post['email']);		
$postdata['pan_no'] = defineVariable($post['pan_no']);
$postdata['outlet_id'] = $outlet_id;

$postdata['company'] = defineVariable($post['shop_name']);	
$postdata['pincode'] = defineVariable($post['pincode']);	
$postdata['address'] = defineVariable($post['address']);	
$postdata['district'] = defineVariable($post['district']);	
$postdata['state'] = defineVariable($post['state']);	
$postdata['city'] = defineVariable($post['city']);
$postdata['aadhaar_no'] = defineVariable($post['aadhaar_no']);
$postdata['dob'] = defineVariable($post['dob']);
$postdata['shop_name'] = defineVariable($post['shop_name']);
$postdata['shop_in_code'] = defineVariable($post['pincode']);
$postdata['shop_district'] = defineVariable($post['district']);
$postdata['shop_state'] = defineVariable($post['state']);
$postdata['shop_city'] = defineVariable($post['city']);
$postdata['shop_area'] = defineVariable($post['city']);
$postdata['alter_mobile'] = defineVariable($post['mobile']);
$postdata['shop_address'] = defineVariable($post['address']);
$postdata['local_address'] = defineVariable($post['address']);
$postdata['local_area'] = defineVariable($post['city']);
$postdata['local_pin'] = defineVariable($post['pincode']);
$postdata['local_district'] = defineVariable($post['district']);
$postdata['local_state'] = defineVariable($post['state']);
$postdata['local_city'] = defineVariable($post['city']);

foreach($postdata as $k=>$kv){
	if(empty($kv)){
		unset($postdata[$k]);
	}
}
	if (isset($_FILES['aadhaar_img'])){
	 $aadhaar_img = new CURLFile($_FILES['aadhaar_img']['tmp_name'], $_FILES['aadhaar_img']['type'], $_FILES['aadhaar_img']['name']);
	 $postdata['aadhaar_img'] = $aadhaar_img;
	}
		
		//// Call netpaisa_curl ////


		$outlet_json = $helpers->netpaisa_curl($aeps_bankit_update, $postdata);
		//$outlet_json = '{"ERR_STATE":0, "IDS":"14##15", "ID_NAME":"Aadhaar / Voter ID ## Photo @ Store ", "DATA":{ "statuscode":"TXN", "status":"Transaction Successful", "outlet_id":"12027", "mobile_number":"9876543210", "email_id":"your_email@gmail.in", "outlet_name":"Company Name", "contact_person":"YOUR NAME", "pan_no":"AZDPG6750C", "kyc_status":"0", "outlet_status":"1"}}';
		$outlet_result = json_decode($outlet_json, true);
		
		if($outlet_result['ERR_STATE']==0 && isset($outlet_result['outlet_id']) && $outlet_result['outlet_id']!=""){ 
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = $outlet_result['MSG'];			
			$aadhaarimg = isset($outlet_result['updated_data']['aadhaar_img'])? $outlet_result['updated_data']['aadhaar_img'] : '';			
			$valueOutlet = array(
							'first_name'	=> defineVariable($post['first_name']),
							'middle_name'	=> defineVariable($post['middle_name']),
							'last_name'		=> defineVariable($post['last_name']),
							'email'			=> defineVariable($post['email']),
							'mobile'		=> $mobile,
							'company'		=> defineVariable($post['shop_name']),
							'address'		=> defineVariable($post['address']),
							'pincode'		=> defineVariable($post['pincode']),
							'city'			=> defineVariable($post['city']),
							'state'			=> defineVariable($post['state']),
							'district'		=> defineVariable($post['district']),
							'dob'			=> defineVariable($post['dob']),
							'pan_no'		=> $pan_no,
							'aadhaar'		=> defineVariable($post['aadhaar_no']),
							'aadhaarimg'		=> $aadhaarimg,
							'update_date'	=> date("Y-m-d H:i:s")
						);
			foreach($valueOutlet as $k=>$kv){
				if(empty($kv)){
					unset($valueOutlet[$k]);
				}
			}
			$mysqlClass->updateData('outlet_kyc_bankit',$valueOutlet, " where user_id = '".$USER_ID."' and outletid = '".$outlet_id."' ");
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