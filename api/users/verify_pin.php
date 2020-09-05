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
		
		if(isset($post['password']) && isset($post['userid']) && isset($post['pin'])){
			$hash_pass = $helpers->hashPassword($post['password']);
			$hash_password =  $hash_pass['encrypted'];
			
			$hash_pin = $helpers->hashPin($post['pin']);
			$hash_spin =  $hash_pin['encrypted'];
			
			$user = $mysqlObj->fetchRow("`add_cust`","`user`,`pass`,`security_pin`,`id`,`name`,`service_access`,`wl_id`,`admin_id`,`creator_id`,`netpaisa_services` "," where `user`='".$post['userid']."' and `pass`='".$hash_password."' and `security_pin`='".$hash_spin."' ");
			//echo $hash_spin;
			
			$wlDetail = $mysqlObj->mysqlQuery(" select b.domain,b.user_id from add_cust as a, add_white_label as b where a.id = b.user_id and a.status = 'ENABLED' and b.domain = '".$post['domain']."' ");
			$resWL = $wlDetail->fetch(PDO::FETCH_ASSOC);
			
			if($user['pass']==$hash_password && $user['security_pin']==$hash_spin && $user['user']==$post['userid']){
			
				if(isset($resWL) && $resWL['user_id']==$user['wl_id']){
					$response['ERROR_CODE'] = 0;
					$response['MESSAGE'] = "Login Success";
					
					$response['USER_NAME'] = $user['name'];				
					$response['SERVICES'] = $user['service_access'];
					
					$userType = $userObj->check_user_type($user['id']);				
					$response['USER_TYPE'] = $userType['usertype'];
					
					$JWTDATA['USER_ID'] = $user['id'];
					$JWTDATA['WL_ID'] = $user['wl_id'];
					$JWTDATA['ADMIN_ID'] = $user['admin_id'];
					$JWTDATA['CREATOR_ID'] = $user['creator_id'];
					$JWTDATA['USER_TYPE'] = $userType['usertype'];
					$token = $jwtED->encode_token($JWTDATA);
					if($post['login']=="web"){ 
					$response['TEST'] = $post['login'];
					
						session_start();
						$_SESSION['TOKEN'] = $token;
						$_SESSION['USER_NAME'] = $user['name'];				
						$_SESSION['SERVICES'] = $user['service_access'];
						$_SESSION['netpaisa_services'] = $user['netpaisa_services'];
						$_SESSION['SESSION_TIME'] = time();
						if($response['USER_TYPE']!='RETAILER'){
							$_SESSION['TOKEN_DETAIL'] = json_encode($JWTDATA);
						}
						if($response['USER_TYPE']=='WL'){
							$swType = $mysqlObj->fetchRow("`admin`"," userType ","  WHERE `id`='".$user['admin_id']."'" );
							$response['SW_TYPE'] = $swType['userType'];
						}
						
						
					}
					else{
						$response['TOKEN'] = $token;
					}
					
					$data = array(
								'user_id'=>$user['id'],
								'method'=>$post['login'],
								'status'=>'success',
								'client_details'=>json_encode($_SERVER),
								'date_time'=>date("Y-m-d H:i:s"),
								'user_type' => $userType['usertype']
							);
					$mysqlObj->insertData("`login_detail`",$data);				
					
				} else {
					$helpers->errorResponse("Invalid User.");
				}
							
			} else {
				$data = array(
							'user_id'=>'',
							'method'=>'web',
							'client_details'=>json_encode($_SERVER),
							'date_time'=>date("Y-m-d H:i:s"),
							'status'=>'failed'
						);
				$mysqlObj->insertData("`login_detail`",$data);
				$helpers->errorResponse("Invalid Login Pin !");
			}
		} else {
			$helpers->errorResponse("Username/password/Pin empty.");			
		}
		
	} else {
		$helpers->errorResponse("Invalid request !");
	}
	//print_r($_SESSION);
	echo json_encode($response);
	$mysqlObj->close_connection();
	//die();
?>