<?php
/*************************************************************************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
****************************************************************************/
require("../../config.php");
require("../classes/db_class.php");
require("../classes/user_class.php");
require("../classes/comman_class.php");
require("../classes/jwt_encode_decode.php");
require("../classes/utility/Otp.php");
require("../classes/CustomerOtp.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;	
require '../vendor/autoload.php';
$userObj = new User_class();
$helpers = new Helper_class();
$mysqlObj = new mysql_class();
$jwtED = new jwt_encode_decode();
$custOtp = new 	CustomerOtp();
	if($_SERVER['HTTP_X_API_KEY']==HTTP_X_API_KEY && $_SERVER['HTTP_NETPAISAPASSKEY']==NETPAISAPASSKEY){	
	} else {
		$helpers->errorResponse("Authorization Invalid !");
	}
	
	if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST)){
		$post = $helpers->clearSlashes($_POST);
		
		if(isset($post['password']) && isset($post['userid'])){
			$hash_pass = $helpers->hashPassword($post['password']);
			$hash_password =  $hash_pass['encrypted'];
			$user = $mysqlObj->fetchRow("`add_cust`"," `user`,`pass`,`security_pin`,`id`,`name`, `email`, `mobile`,  `service_access`,`wl_id`,`admin_id`,`creator_id`"," where `user`='".$post['userid']."' and `pass`='".$hash_password."' and `status` = 'ENABLED' ");
			//$wlDetail = $mysqlObj->mysqlQuery(" select b.domain,b.user_id from add_cust as a, add_white_label as b where a.id = b.user_id and a.status = 'ENABLED' and b.domain = '".$post['domain']."' ");
			$wlDetail = $mysqlObj->mysqlQuery(" select b.domain,b.user_id from add_cust as a, add_white_label as b where a.id = b.user_id and a.status = 'ENABLED' and a.id = '".$user['wl_id']."'  ");
			$resWL = $wlDetail->fetch(PDO::FETCH_ASSOC);
			$isOtp = 1;
			$current_date = date('Y-m-d');
			if($user['pass']==$hash_password && $user['user']==$post['userid']){
				if(isset($resWL) && $resWL['user_id']==$user['wl_id']){
              $loginDetailsQuery = $mysqlObj->mysqlQuery(" SELECT DATE(logInTime) AS loginDate FROM customer_otps WHERE user_id='".$user['id']."' AND is_invoked = 1 ORDER BY id DESC LIMIT 0, 1"); 
             $loginDetails = $loginDetailsQuery->fetch(PDO::FETCH_ASSOC);
              if(!empty($loginDetails)){
              	if($current_date == $loginDetails['loginDate']){
              	 $isOtp = 0;
                // No need OTP  redirect to dashboard
              		$response['ERROR_CODE'] = 0;
					$response['MESSAGE'] = "Login Success";
					$response['IS_OTP_REQUIRED'] =$isOtp;
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
              	}else{
               // Need OTP before redirecting to dashboard
              	    $response['ERROR_CODE'] = 0;
					$response['MESSAGE'] = "UserId and Password Valid.";
					$response['DOMAIN'] = $resWL['domain'];
					$response['USER_EMAIL']=$user['email'];
					$response['USER_MOBILE']=$user['mobile'];
					$response['IS_OTP_REQUIRED'] =$isOtp;

		################ Gemerate OTP save into table and send it on registered email and mobile #############
					$user_ip = Otp::get_client_ip();
					$otp = Otp::generateOtp(OTP_LENGTH);
					
					$hash_otp = $helpers->hashPin($otp);
					$hash_otp =  $hash_otp['encrypted'];
					$customer_data = array(
						"user_id"=>$user['id'],
						"mobile_number"=>$user['mobile'],
						"otp"=>$hash_otp,
						"is_invoked"=>0, 
						"is_expired"=>0,
						"user_ip"=>$user_ip
					);					
                 $custOtpId = $custOtp->saveOtp('customer_otps', $customer_data); 
                  if($custOtpId){
                  	$domain = str_replace("https://", "", $resWL['domain']);
                  	$domain = rtrim($domain, "/");
                  	if(preg_match("/localhost/i", $domain)){
                  		$exploded = explode("/", $domain);
                  		$domain = end($exploded);
                       	$fromEmail = "info@".$domain."yopmail.com"; 		
                  	}else{
                  		$fromEmail = "info@".$domain;
                  	}
                  
                  	 //$helpers->send_otp_mobile($user['mobile'],$otp);
                     $mail = new PHPMailer(true);
					try {
					//Server settings
					$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      
					$mail->isSMTP();                                       
					$mail->Host       = MAIL_HOST;                    
					$mail->SMTPAuth   = true;                                   
					$mail->Username   = MAIL_USERNAME;                     
					$mail->Password   = MAIL_PASSWORD;                             
					$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
					$mail->Port       = MAIL_PORT; 
					$mail->SMTPDebug = false;                                   
					//Recipients
					$mail->setFrom($fromEmail, 'One Time Password');
					$mail->addAddress($user['email'], '');                   
					//$mail->addReplyTo('info@netpaisa.com', 'Information');
					// Content
					$mail->isHTML(true);                                  
					$mail->Subject = 'Net Paisa One Time Password';
					$mail->Body    = '<p>Dear Customer,<br></p>
					<p>The One Time Password (OTP) for your netPaisa panel to authenticate your device/ browser for a secured website login is '.$otp.'. The OTP is valid for 15 mins or one successful attempt, whichever is earlier. Please do not share with anyone.<br><br><br></p>
					<p><strong>Warm Regards,</strong><br>Net Paisa<p>
					';
					//$mail->AltBody = '';

					$mail->send();
					// echo 'Message has been sent';
					} catch (Exception $e) {
					//echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
					}

                  }
                 ################ End code  ###########################  
              }
				
              }else{
               // Need OTP before redirecting to dashboard
              	    $response['ERROR_CODE'] = 0;
					$response['MESSAGE'] = "UserId and Password Valid.";
					$response['DOMAIN'] = $resWL['domain'];
					$response['USER_EMAIL']=$user['email'];
					$response['USER_MOBILE']=$user['mobile'];
					$response['IS_OTP_REQUIRED'] =$isOtp;

		################ Gemerate OTP save into table and send it on registered email and mobile #############
					$user_ip = Otp::get_client_ip();
					$otp = Otp::generateOtp(OTP_LENGTH);
					$hash_otp = $helpers->hashPin($otp);
					$hash_otp =  $hash_otp['encrypted'];
					$customer_data = array(
						"user_id"=>$user['id'],
						"mobile_number"=>$user['mobile'],
						"otp"=>$hash_otp,
						"is_invoked"=>0, 
						"is_expired"=>0,
						"user_ip"=>$user_ip
					);					
                 $custOtpId = $custOtp->saveOtp('customer_otps', $customer_data); 
                  if($custOtpId){
                  	//$helpers->send_otp_mobile($user['mobile'],$otp);
                     $mail = new PHPMailer(true);
					try {
					//Server settings
					$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      
					$mail->isSMTP();                                       
					$mail->Host       = MAIL_HOST;                    
					$mail->SMTPAuth   = true;                                   
					$mail->Username   = MAIL_USERNAME;                     
					$mail->Password   = MAIL_PASSWORD;                             
					$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
					$mail->Port       = MAIL_PORT; 
					$mail->SMTPDebug = false;                                   
					//Recipients
					$mail->setFrom('no-reply@reseller.netpaisa.com', 'OTP');
					$mail->addAddress($user['email'], '');                   
					//$mail->addReplyTo('info@netpaisa.com', 'Information');
					// Content
					$mail->isHTML(true);                                  
					$mail->Subject = 'Net Paisa One Time Password';
					$mail->Body    = '<p>Dear Customer,<br></p>
					<p>The One Time Password (OTP) for your netPaisa panel to authenticate your device/ browser for a secured website login is '.$otp.'. The OTP is valid for 15 mins or one successful attempt, whichever is earlier. Please do not share with anyone.<br><br><br></p>
					<p><strong>Warm Regards,</strong><br>Net Paisa<p>
					';
					//$mail->AltBody = '';

					$mail->send();
					// echo 'Message has been sent';
					} catch (Exception $e) {
					//echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
					}

                  }
                 ################ End code  ###########################  
              }	        
				} else {
					$helpers->errorResponse("Invalid User.");
				}
			} else {
				$helpers->errorResponse("Invalid username or password.");
			}
		} else {
			$helpers->errorResponse("Username or password empty.");
		}
		
	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	$mysqlObj->close_connection();
	die();
?>