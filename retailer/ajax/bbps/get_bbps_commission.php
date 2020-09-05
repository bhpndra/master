<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
	$helpers = new helper_class();
	
if(isset($_SESSION['TOKEN'])){
	$post = $helpers->clearSlashes($_POST);		
	$trans_url = BASE_URL."/api/recharge/get_recharge_commission.php";
	$post_fields = array(
					"token"=>$_SESSION['TOKEN'],
					"operator_type"=>$post['operator_type'],
					"number"=>$post['number'],
					"amount"=>$post['amount'],
					"circle_code"=>$post['circle_code'],
					"operator"=>$post['operator'],
					);
	$responseRC = api_curl($trans_url,$post_fields,$headerArray);
	$resRC = json_decode($responseRC,true);
	//print_r($resRC);	
	if($resRC['ERROR_CODE']==0){
		$response['status'] = 0;
		$response['msg'] = $resRC['MESSAGE'];
		$response['data'] = $resRC;
	} else {
		if($resRC['MESSAGE']=="You have insufficient balance"){
			$response['status'] = 1;
			$response['msg'] = "Recharge Down, Contact to Admin.";
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
