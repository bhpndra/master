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
	$userObj = new User_class();
	$mysqlObj = new mysql_class();
	$jwtED = new jwt_encode_decode();
	
	if($_SERVER['HTTP_X_API_KEY']==HTTP_X_API_KEY && $_SERVER['HTTP_NETPAISAPASSKEY']==NETPAISAPASSKEY){	
	} else {
		//print_r($_SERVER);
		$helpers->errorResponse("Authorization Invalid !");
	}
	
	if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST)){
		$post = $helpers->clearSlashes($_POST);
		
		if(!isset($_POST['login'])){
			$helpers->errorResponse("Invalid login type");
		}
		if(!isset($_POST['domain'])){
			$helpers->errorResponse("Invalid Domain");
		}
		$mobile = $helpers->validateMobile($post['mobile']);
		if(!isset($mobile) && $mobile != ''){
			$helpers->errorResponse("Invalid Mobile");
		}
		$password = $helpers->random_string();		
		$hash_pass = $helpers->hashPassword($password);
		$hash_password =  $hash_pass['encrypted'];
		
		$spin = rand(1234,9999);
		$hash_pin = $helpers->hashPin($spin);
		$hash_spin =  $hash_pin['encrypted'];
		
		$user = $mysqlObj->fetchRow("`add_cust`","`id`,`mobile`,`user`,`otp_attempt`,`wl_id`"," where `mobile`='".$post['mobile']."' and `otp_attempt` <= 5 ");
		
		$wlDetail = $mysqlObj->mysqlQuery(" select b.domain,b.user_id,a.admin_id,a.sms_api from add_cust as a, add_white_label as b where a.id = b.user_id and a.status = 'ENABLED' and b.domain = '".$post['domain']."' ");
		$resWL = $wlDetail->fetch(PDO::FETCH_ASSOC);
		
		if($user['mobile']==$post['mobile']){
			if($user['otp_attempt'] > 0 and $user['otp_attempt'] <= 5){	
				$otp_attempt = $user['otp_attempt'] - 0;
				if(isset($resWL) && $resWL['user_id']==$user['wl_id']){
					$response['ERROR_CODE'] = 0;
					$response['MESSAGE'] = "Please check your mobile inbox. Login Details sent";
					
					/* Send SMS  */
						//print_r($resWL);
					$txtMsg =  "New Login Details LoginId: ".$user['user']." Pass: ".$password." and Pin: ".$spin." ";
					if($resWL['sms_api']=='CUSTOM'){
						$sms_pack = $mysqlObj->mysqlQuery("select * from `sms_pack` WHERE `user_id`='".$user['wl_id']."' and `admin_id`='".$resWL['admin_id']."'" )->fetch(PDO::FETCH_ASSOC);
					} else {
						$sms_pack = $mysqlObj->mysqlQuery("select * from `sms_pack` WHERE `user_id`='0' and `api_for`='ADMIN' and `admin_id`='".$resWL['admin_id']."'" )->fetch(PDO::FETCH_ASSOC);
						if(empty($sms_pack['id'])){
							$sms_pack = $mysqlObj->mysqlQuery("select * from `sms_pack` WHERE `user_id`='0' and `api_for`='ADMIN' and `admin_id`='1'" )->fetch(PDO::FETCH_ASSOC);
						}
					}
					$smsParameters = json_decode($sms_pack['api_parameters'],true);
					$smsParameters[$sms_pack['param_mobile_name']] = $post['mobile'];
					$smsParameters[$sms_pack['param_msg_name']] = $txtMsg;
					$smsParameters['request_type'] = $sms_pack['request_type'];
					$smsParameters['url'] = $sms_pack['url'];
					$sms_api_res =  $helpers->send_msg_dynamic($smsParameters);
					$response['SMS_API_RES'] = json_decode($sms_api_res,true);					
					/* End Send SMS  */
					$data = array(
								'pass'=>$hash_password,
								'security_pin'=>$hash_spin,
								'otp_attempt'=>$otp_attempt
							);
					$mysqlObj->updateData("`add_cust`",$data, " where id = '".$user['id']."'");				
					//print_r($smsParameters);
				} else {
					$helpers->errorResponse("Invalid App / Domain");
			 	}
			} else {
				$data = array(
							'status' => 'DISABLED'
						);
				$mysqlObj->updateData("`add_cust`",$data, " where id = '".$user['id']."'");	
				$helpers->errorResponse("Your account has been disabled!");
			}			
		} else {			
			$helpers->errorResponse("Mobile number not valid.");
		}		
		
	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	$mysqlObj->close_connection();
	die();
?>