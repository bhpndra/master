<?php
/*************************************************************************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
****************************************************************************/
require("../../config.php");
require("../classes/db_class.php");
require("../classes/comman_class.php");
require("../classes/jwt_encode_decode.php");
require("../classes/utility/Otp.php");
require("../classes/CustomerOtp.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;	
require '../vendor/autoload.php';
$helpers = new Helper_class();
$mysqlObj = new mysql_class();
$custOtp = new 	CustomerOtp();
	if($_SERVER['HTTP_X_API_KEY']==HTTP_X_API_KEY && $_SERVER['HTTP_NETPAISAPASSKEY']==NETPAISAPASSKEY){	
	} else {
		$helpers->errorResponse("Authorization Invalid !");
	}
	
	if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST)){
		$post = $helpers->clearSlashes($_POST);
		if(isset($post['email']) && isset($post['mobile'])){
			
			$user = $mysqlObj->fetchRow("`add_cust`"," `id`,`user`,`pass`,`wl_id`, `mobile`, `email`"," where `email`='".$post['email']."' and `status` = 'ENABLED' ");

			$wlDetail = $mysqlObj->mysqlQuery(" select b.domain,b.user_id from add_cust as a, add_white_label as b where a.id = b.user_id and a.status = 'ENABLED' and a.id = '".$user['wl_id']."'  ");
			$resWL = $wlDetail->fetch(PDO::FETCH_ASSOC);
			if($user['email']==$post['email'] && $user['mobile']==$post['mobile']){
				$domain = str_replace("https://", "", $resWL['domain']);
				$domain = rtrim($domain, "/");
				if(preg_match("/localhost/i", $domain)){
				$exploded = explode("/", $domain);
				$domain = end($exploded);
				$fromEmail = "info@".$domain."yopmail.com"; 		
				}else{
				$fromEmail = "info@".$domain;
				}
				$user_ip = Otp::get_client_ip();
				$otp = Otp::generateOtp(OTP_LENGTH);
				$customer_data = array(
				"user_id"=>$user['id'],
				"mobile_number"=>$user['mobile'],
				"otp"=>$otp,
				"is_invoked"=>0, 
				"is_expired"=>0,
				"user_ip"=>$user_ip
				);

			$custOtpId = $custOtp->saveOtp('customer_otps', $customer_data);
			if($custOtpId){
					$secondlastId = $custOtp->getSecondLastIdofOTP('customer_otps', $user['id'],$custOtpId);
					$updateData = array(
					"is_invoked"=>0,
					"is_expired"=>1,
					"is_expired_at"=>date("Y-m-d H:i:s")
					);	

		            $custOtp->update('customer_otps', $updateData, $user['id'], $secondlastId);
					
					
                  	$helpers->send_otp_mobile($user['mobile'],$otp);
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
					$response = array("ERROR_CODE"=>0, "MESSAGE"=>"OTP has sent successfully.");
                  }
			} else {
				$helpers->errorResponse("Invalid email or mobile.");
			}
		} else {
			$helpers->errorResponse("email or mobile empty.");
		}
		
	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	$mysqlObj->close_connection();
	die();
?>