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



if(isset($post['row_id']) &&  isset($post['status_id'])){	

	$data = array("status" => $post['status_id'] );
	$rowU = $mysqlClass->updateData(" pan_card_details ", $data , " WHERE `id`='".$post['row_id']."'  " );
	if($rowU > 0){
		$response['ERROR_CODE'] = 0;
		$response['MESSAGE'] = 'SUCCESS';
		echo json_encode($response); die();
	} else { 
		$response['ERROR_CODE'] = 1;
		$response['MESSAGE'] = " Error ";
		echo json_encode($response); die();
	}
	
} else {
	$response['ERROR_CODE'] = 1;
	$response['MESSAGE'] = "SOMETHING WRONG";
	echo json_encode($response); die();
}
	

?>
