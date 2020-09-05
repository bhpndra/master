<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
	$helpers = new helper_class();
	
if(isset($_SESSION['TOKEN'])){
	$post = $helpers->clearSlashes($_POST);		
	$trans_url = BASE_URL."/api/aeps/aeps-payout.php";
	
	$post_fields = $post;
	$post_fields['token'] = $_SESSION['TOKEN'];
	
	echo $responseRC = api_curl($trans_url,$post_fields,$headerArray);
	
} else {
	$response['status'] = 0;
	$response['msg'] = "Wrong Request";	
	echo json_encode($response);
}
?>
