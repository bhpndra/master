<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
	$helpers = new helper_class();
	
if(isset($_SESSION['TOKEN'])){
	$post = $helpers->clearSlashes($_POST);		
	$trans_url = BASE_URL."/api/dmt/transfer.php";
	$post_fields = array(
					"token"=>$_SESSION['TOKEN'],
					"remittermobile"=>$post['remittermobile'],
					"beneficiaryid"=>$post['beneficiaryid'],
					"bene_account"=>$post['bene_account'],
					"bene_ifsc"=>$post['bene_ifsc'],
					"bene_name"=>$post['bene_name'],
					"amount"=>$post['amount'],
					"current_wallet_remaining_limit"=>$post['current_wallet_remaining_limit']
					);
	$responseAPI = api_curl($trans_url,$post_fields,$headerArray);
	$resAPI = json_decode($responseAPI,true);
	//print_r($responseAPI);	
	if($resAPI['ERROR_CODE']==0){
		$response['status'] = 0;
		$response['msg'] = $resAPI['MESSAGE'];
		$response['structure'] = $resAPI['TRANS_STRUCT']['retailer_struct'];
	} else {
		if($resRC['MESSAGE']=="Error: You have insufficient balance!"){
			$response['status'] = 1;
			$response['msg'] = "DMT Down, Contact to Admin.";
		} else {
			$response['status'] = 1;
			$response['msg'] = $resAPI['MESSAGE'];
		}
	}
} else {
	$response['status'] = 0;
	$response['msg'] = "Wrong Request";
}
echo json_encode($response);
?>
