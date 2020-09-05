<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
 
	require("../../config.php");
	require("../classes/db_class.php");
	require("../classes/comman_class.php");
	require("../classes/jwt_encode_decode.php");
	require("../classes/CustomerDevice.php");	
	require("../classes/AuthChecksum.php");
	
	$helpers = new Helper_class();
	$mysqlObj = new mysql_class();
	$custDevice = new CustomerDevice();	
	$AuthChecksum = new AuthChecksum();
	
	$headers = [];
	foreach ($_SERVER as $name => $value) {
		if (substr($name, 0, 5) == 'HTTP_') {
			$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
		}
	}
		
	$headers_data['auth_token'] = $headers['Authtoken'];
	$headers_data['Logindevice'] = $headers['Logindevice'];  // app or web 
	$headers_data['device_id'] = $headers['Deviceid'];
	$headers_data['device_version'] = $headers['Deviceversion'];
	$headers_data['Appversion'] = $headers['Appversion'];
	
	if( $headers_data['Appversion']>=1 ){
		
		$DefaultSettingQry = $mysqlObj->mysqlQuery("  select Cheksum_OAuth,DeviceCheck,GeoLocationCheck from default_setting ");
		$DefaultSettingData = $DefaultSettingQry->fetch(PDO::FETCH_ASSOC); 

		if( !empty($DefaultSettingData['Cheksum_OAuth']) && $DefaultSettingData['Cheksum_OAuth']==1  ){
			
			$rsp = $AuthChecksum->authenticate_checksum($_POST);		
			$rsp = $AuthChecksum->authcheck($headers_data);
		}
		
	}
	
	
	
	
	if($_SERVER['HTTP_X_API_KEY']==HTTP_X_API_KEY && $_SERVER['HTTP_NETPAISAPASSKEY']==NETPAISAPASSKEY){	
	} else {
		//print_r($_SERVER);
		$helpers->errorResponse("Authorization Invalid !");
	}
	
	if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST)){
		$post = $helpers->clearSlashes($_POST);
		
		if(isset($post['password']) && isset($post['userid'])){
			$hash_pass = $helpers->hashPassword($post['password']);
			$hash_password =  $hash_pass['encrypted'];
			
			//check user account enable/disabled			
			$userAccount = $mysqlObj->countRows("SELECT `id`,`status` FROM `add_cust` where `user`='".$post['userid']."' and `status` = 'DISABLED' ");
			
			if($userAccount > 0){
				$helpers->errorResponse("Account Suspended.");
			}
			else{
				$user = $mysqlObj->fetchRow("`add_cust`","`id`,`usertype`,`user`,`pass`,`wl_id`"," where `user`='".$post['userid']."' and `pass`='".$hash_password."' and `status` = 'ENABLED' ");
				
				//$wlDetail = $mysqlObj->mysqlQuery(" select b.domain,b.user_id from add_cust as a, add_white_label as b where a.id = b.user_id and a.status = 'ENABLED' and b.domain = '".$post['domain']."' ");
				$wlDetail = $mysqlObj->mysqlQuery(" select b.domain,b.user_id from add_cust as a, add_white_label as b where a.id = b.user_id and a.status = 'ENABLED' and a.id = '".$user['wl_id']."'  ");
				$resWL = $wlDetail->fetch(PDO::FETCH_ASSOC);
				
				if($user['pass']==$hash_password && $user['user']==$post['userid']){
					if(isset($resWL) && $resWL['user_id']==$user['wl_id']){
						
						$DeviceCheckFlag=0;
						$GeoLocationFlag=0;
						
						if( !empty($DefaultSettingData['DeviceCheck']) && $DefaultSettingData['DeviceCheck']==1  ){
							$DeviceRsp = $custDevice->CheckIfDeviceExist($post['device_id'],$user['id']);
							$DeviceCheckFlag=1;
						}
						if( !empty($DefaultSettingData['GeoLocationCheck']) && $DefaultSettingData['GeoLocationCheck']==1  ){
							$IsValidDistance = $custDevice->IsFromValidDistanceArea($post['device_id'],$user['id'],$post['geo_location_lat'],$post['geo_location_long']);
							$GeoLocationFlag=1;
						}
						
						
						// If both Device Check and Geo Location flag on 
						
						
						if( $DeviceCheckFlag==1 && $GeoLocationFlag==1 ){
							
							if( $DeviceRsp=== false ){
								
								$msg="Trying to login with different device.";
								$helpers->errorResponse($msg);
								$LoginStatus = "fail";
								
							}else if( $IsValidDistance === false ){
								
								$msg="Trying to login with out of area.";
								$helpers->errorResponse($msg);
								$LoginStatus = "fail";
								
							} else if( $DeviceRsp=== true && $IsValidDistance === true){
							
								$response['ERROR_CODE'] = 0;
								$response['MESSAGE'] = "UserId and Password Valid.";
								$response['DOMAIN'] = $resWL['domain'];
								$LoginStatus = "success";						
								
								$custDevice->UpdateDeviceId($post['device_id'],$user['id']);
								$source = $post['login'];
								
								$auth_token = $AuthChecksum->CreateWebServiceOAuthToken($user['id'], $post['device_id'], $source);
								$response['auth_token'] = $auth_token;
							
							}else{
								$helpers->errorResponse("Something went wrong");
								$LoginStatus = "fail";
							}
							
							
						}
						
						// If Device Check flag is on and Geo Location flag is off 
						
						else if( $DeviceCheckFlag==1 && $GeoLocationFlag==0  ){
							
							if( $DeviceRsp=== true ){
							
								$response['ERROR_CODE'] = 0;
								$response['MESSAGE'] = "UserId and Password Valid.";
								$response['DOMAIN'] = $resWL['domain'];
								$LoginStatus = "success";						
								
								$custDevice->UpdateDeviceId($post['device_id'],$user['id']);
								$source = $post['login'];
								
								$auth_token = $AuthChecksum->CreateWebServiceOAuthToken($user['id'], $post['device_id'], $source);
								$response['auth_token'] = $auth_token;
							
							}else{
								$helpers->errorResponse("Your account has been suspended.Please contact your service provider.");
								$LoginStatus = "fail";
							}
						}
						
						// If Device Check flag is off and Geo Location flag is on 
						
						else if( $DeviceCheckFlag==0 && $GeoLocationFlag==1  ){
							
							if( $IsValidDistance=== true ){							
								$response['ERROR_CODE'] = 0;
								$response['MESSAGE'] = "UserId and Password Valid.";
								$response['DOMAIN'] = $resWL['domain'];
								$LoginStatus = "success";						
								
								$custDevice->UpdateDeviceId($post['device_id'],$user['id']);
								$source = $post['login'];
								
								$auth_token = $AuthChecksum->CreateWebServiceOAuthToken($user['id'], $post['device_id'], $source);
								$response['auth_token'] = $auth_token;
							
							}else{
								$helpers->errorResponse("Your account has been suspended.Please contact your service provider.");
								$LoginStatus = "fail";
							}
						}
						
						// If Both Device Check flag  and Geo Location flag are off 
						
						else{
							
							$response['ERROR_CODE'] = 0;
							$response['MESSAGE'] = "UserId and Password Valid.";
							$response['DOMAIN'] = $resWL['domain'];
							$LoginStatus = "success";						
							
							$custDevice->UpdateDeviceId($post['device_id'],$user['id']);
							$source = $post['login'];
							
							$auth_token = $AuthChecksum->CreateWebServiceOAuthToken($user['id'], $post['device_id'], $source);
							$response['auth_token'] = $auth_token;
						}
						
						
						
					} else {
						$helpers->errorResponse("Invalid User.");
						$LoginStatus = "fail";
					}
				} else {
					$helpers->errorResponse("Invalid username or password.");
					$LoginStatus = "fail";
				}
				
				
				$data = array(
								'user_id'=>$user['id'],
								'method'=>$post['login'],								
								'status'=>$LoginStatus,
								'client_details'=>json_encode($_SERVER),
								'date_time'=>date("Y-m-d H:i:s"),
								'user_type' => $user['usertype'],								
								'mac_address' => $post['mac_id'],
								'device_id' => $post['device_id'],
								'geo_location_lat' => $post['geo_location_lat'],
								'geo_location_long' => $post['geo_location_long'],
							);
					$mysqlObj->insertData("`login_detail`",$data);
			} 
		}
		else {
			$helpers->errorResponse("Username or password empty.");
		}
		
	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	$mysqlObj->close_connection();
	die();
?>