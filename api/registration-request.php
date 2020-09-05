<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("../config.php");
	require("classes/db_class.php");
	require("classes/comman_class.php");
	require("classes/jwt_encode_decode.php");
	
	$helpers = new Helper_class();
	$mysqlObj = new mysql_class();
	
	if($_SERVER['HTTP_X_API_KEY']==HTTP_X_API_KEY && $_SERVER['HTTP_NETPAISAPASSKEY']==NETPAISAPASSKEY){	
	} else {
		//print_r($_SERVER);
		$helpers->errorResponse("Authorization Invalid !");
	}
	
	if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST)){
		$post = $helpers->clearSlashes($_POST);
		
		$name = $post['name'];
		$mobile = $helpers->validateMobile($post['mobile']);
		$email = $post['email'];
		$cname = $post['cname'];
		$city = $post['city'];
		$state = $post['state'];
		$address = $post['address'];
		$pincode = $post['pin'];
		$DOMAIN_NAME = $post['DOMAIN_NAME'];
        
        $que = $mysqlObj->mysqlQuery("select * from `user_registration_request` WHERE `email`='".$post['email']."' OR `mobile`='".$post['mobile']."'");
        if($que->rowCount()>0){				
			$helpers->errorResponse('Error! User already exist with email/mobile.');
		}
		else{

		if(empty($name)){
			$helpers->errorResponse("Name is missing.");
		}
		if(empty($mobile)){
			$helpers->errorResponse("Number is missing or not valid.");
		}		
		if(empty($email)){
			$helpers->errorResponse("Email is missing.");
		}
		if(empty($cname)){
			$helpers->errorResponse("Company name is missing.");
		}
		if(empty($city)){
			$helpers->errorResponse("City name is missing.");
		}
		if(empty($state)){
			$helpers->errorResponse("State name is missing.");
		}
		if(empty($address)){
			$helpers->errorResponse("Company name is missing.");
		}
		if(empty($pincode)){
			$helpers->errorResponse("Pin code is missing.");
		}
	
		$wl_id = $mysqlObj->mysqlQuery("select user_id from `add_white_label` WHERE `domain`='".$DOMAIN_NAME."' ")->fetch(PDO::FETCH_ASSOC);
		$white_id = $wl_id['user_id'];
		/////////////////// End //////////////////
		
		//// Insert data ////			
			$valueNewUser = array(
					'name' 					=> $post['name'],
					'mobile' 				=> $post['mobile'],
					'email' 				=> $post['email'],
					'cname'	 				=> $post['cname'],
					'city' 					=> $post['city'],
					'user_type' 			=> $post['type'],
					'state' 				=> $post['state'],
					'address' 	    	    => $post['address'],
					'pincode' 				=> $post['pin'],
					'created_on' 		    => date("Y-m-d H:i:s"),
					'created_by' 		    => 'ADMIN',
					'wl_id'					=>  $white_id,						);
			$id = $mysqlObj->insertData('user_registration_request',$valueNewUser);
			if($id > 1){
				$response['ERROR_CODE'] = 0;
				$response['MESSAGE'] = 'Your request is accepted ';				
			 } else {
				$response['ERROR_CODE'] = 1;
				$response['MESSAGE'] = 'Please try again';				
			}

		}
		
	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	$mysqlObj->close_connection();
	die();
?>