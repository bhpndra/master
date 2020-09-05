<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
	$helpers = new helper_class();
	
if(isset($_SESSION['TOKEN'])){
	$post = $helpers->clearSlashes($_POST);		
	$trans_url = BASE_URL."/api/bbps/pay_bill.php";
	$post_fields = $post;
	$post_fields["token"] = $_SESSION['TOKEN'];
	$responseRC = api_curl($trans_url,$post_fields,$headerArray);
	$resRC = json_decode($responseRC,true);
	//print_r($resRC);	
	if($resRC['ERROR_CODE']==0){
		$response['status'] = 0;
		$response['msg'] = $resRC['MESSAGE'];
	} else {
		if($resRC['MESSAGE']=="You have insufficient balance"){
			$response['status'] = 1;
			$response['msg'] = "Bill Payment Down, Contact to Admin.";
		} else {
			$response['status'] = 1;
			$response['msg'] = $resRC['MESSAGE'];
		}
	}
} else {
	$response['status'] = 0;
	$response['msg'] = "Wrong Request";
}
echo json_encode($response);
?>
