<?php
	session_start();
	require("../../config.php");
	require("../../include/lib.php");
	require("../../api/classes/db_class.php");
	require("../../api/classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();

if(isset($_SESSION['TOKEN'])){
	$url = BASE_URL."/api/users/get_token_details.php";
	$post_fields = array("token"=>$_SESSION['TOKEN']);
	$responseVT = api_curl($url,$post_fields,$headerArray);
	$resVT = json_decode($responseVT,true);
	if($resVT['ERROR_CODE']==1){
		$helpers->errorResponse("SESSION EXPIRED!");
	}
} else {
	$helpers->errorResponse("SESSION EXPIRED!");
}

$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];

$post = $helpers->clearSlashes($_POST); 
if(isset($post['switchVal'])  && ($post['switchVal']=='DEFAULT' || $post['switchVal']=='CUSTOM')){		
	$res = $mysqlClass->mysqlQuery("select sms_api from `add_cust` WHERE `id`='".$USER_ID."' and `wl_id`='".$WL_ID."' and `admin_id`='".$ADMIN_ID."'" )->fetch(PDO::FETCH_ASSOC);
	if($res['sms_api']!=$post['switchVal']){
		$data = array("sms_api"=>$post['switchVal']);
		$rowU = $mysqlClass->updateData(" add_cust ", $data , " WHERE `id`='".$USER_ID."' and `wl_id`='".$WL_ID."' and `admin_id`='".$ADMIN_ID."' " );
		if($rowU > 0){
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = 'SUCCESS';
			echo json_encode($response); die();
		}
	} else { 
		$response['ERROR_CODE'] = 0;
		$response['MESSAGE'] = 'SUCCESS';
		echo json_encode($response); die();
	}

}
	

?>
