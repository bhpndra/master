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
	require("../classes/CustomerOtp.php");
	
	$helpers = new Helper_class();
	$userObj = new User_class();
	$mysqlObj = new mysql_class();
	$jwtED = new jwt_encode_decode();
	$custOtp = new 	CustomerOtp();
	
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
		
		if(isset($post['password']) && isset($post['userid']) && isset($post['otp'])){
			$hash_pass = $helpers->hashPassword($post['password']);
			$hash_password =  $hash_pass['encrypted'];
			
			$user = $mysqlObj->fetchRow("`add_cust`","`user`,`pass`,`security_pin`,`id`,`name`, `mobile`, `service_access`,`wl_id`,`admin_id`,`creator_id` "," where `user`='".$post['userid']."' and `pass`='".$hash_password."'");
			if(empty($post['otp'])){
			$helpers->errorResponse("Please enter OTP.");	
			}			
			if(!is_numeric($post['otp'])){
				$helpers->errorResponse("OTP must be integer value.");	
				$custOtp->WrongAttempt($user['id']);
			}
            if(strlen($post['otp'])!= OTP_LENGTH){
               $helpers->errorResponse("OTP length must be ".OTP_LENGTH." digits.");
			   $custOtp->WrongAttempt($user['id']);
            }
            
			$user_ip = $_SERVER['REMOTE_ADDR'];
			
			/**Check user OTP **/
		$checkOtp = $custOtp->checkUserOtp("customer_otps", $user['mobile'], $post['otp'],$user_ip  );
		//var_dump($checkOtp);exit;

			if($checkOtp!==0){
				$updateData = array(
					"is_invoked"=>1,
					"is_expired"=>1,
					"is_expired_at"=>date("Y-m-d H:i:s")
				);	

            $custOtp->update('customer_otps', $updateData, $user['id'],$checkOtp);

			$wlDetail = $mysqlObj->mysqlQuery(" select b.domain,b.user_id from add_cust as a, add_white_label as b where a.id = b.user_id and a.status = 'ENABLED' and b.domain = '".$post['domain']."' ");
			$resWL = $wlDetail->fetch(PDO::FETCH_ASSOC);
			
			if($user['pass']==$hash_password && $user['user']==$post['userid']){
			
				if(isset($resWL) && $resWL['user_id']==$user['wl_id']){
					
					$CheckIfDenied = $custOtp->CheckIfWrongAttemptIsMoreThan3($user['id']);
					if($CheckIfDenied===true){
					
					
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


				}
				else {
					$helpers->errorResponse("Access Denied.");
				}				
					
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
				$helpers->errorResponse("Please enter Valid OTP !");
				//$custOtp->WrongAttempt($user['id']);
			}
		}else{
			
			
			$rsp=$custOtp->WrongAttempt($user['id']);
			if($rsp===false){
				$helpers->errorResponse("WrongAttemp");
			}else{
				$helpers->errorResponse("Please enter Valid OTP !");
			}
			
		}
		} else {
			$helpers->errorResponse("Username/password/Pin empty.");			
		}
		
	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	$mysqlObj->close_connection();
	die();
?>