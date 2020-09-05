<?php
//error_reporting(E_ALL);
//ini_set('display_errors',1);


/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("../../config.php");
	require("../classes/db_class.php");
	require("../classes/comman_class.php");
	//require("../classes/jwt_encode_decode.php");
	
	$helpers = new Helper_class();
	$mysqlObj = new mysql_class();
	
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
			
			$user = $mysqlObj->fetchRow("`add_cust`","`user`,`pass`,`wl_id`"," where `user`='".$post['userid']."' and `pass`='".$hash_password."' and `status` = 'ENABLED' ");
			
			//$wlDetail = $mysqlObj->mysqlQuery(" select b.domain,b.user_id from add_cust as a, add_white_label as b where a.id = b.user_id and a.status = 'ENABLED' and b.domain = '".$post['domain']."' ");
			$wlDetail = $mysqlObj->mysqlQuery(" select b.domain,b.user_id from add_cust as a, add_white_label as b where a.id = b.user_id and a.status = 'ENABLED' and a.id = '".$user['wl_id']."'  ");
			$resWL = $wlDetail->fetch(PDO::FETCH_ASSOC);
			
			if($user['pass']==$hash_password && $user['user']==$post['userid']){
				if(isset($resWL) && $resWL['user_id']==$user['wl_id']){
					$response['ERROR_CODE'] = 0;
					$response['MESSAGE'] = "UserId and Password Valid.";
					$response['DOMAIN'] = $resWL['domain'];
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