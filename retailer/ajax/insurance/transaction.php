<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
if(isset($_SESSION['TOKEN'])){
	$post = $helpers->clearSlashes($_POST);		
	$trans_url = BASE_URL."/api/insurance/transaction.php";
	$post_fields = array(
					"token"=>$_SESSION['TOKEN'],
					"operator_type"=>"POSTPAID",
					"proposalNum"=>$post['proposalNum'],
					"amount"=>$post['amount'],
					"operator"=>"RELIGARE",
					);
	$responseRC = api_curl($trans_url,$post_fields,$headerArray);
	$resRC = json_decode($responseRC,true);

	if($resRC['ERROR_CODE']==0){

	$response['status'] = 0;
	$response['relpayment'] = json_decode($resRC['MESSAGE']);
	$response['msg'] = $resRC['MESSAGE'];
	} else {
		if($resRC['MESSAGE']=="You have insufficient balance"){
			$response['status'] = 1;
			$response['msg'] = "Insurance Down, Contact to Admin.";
		} else {
			$response['status'] = 1;
			$response['msg'] = $resRC['MESSAGE'];
		}
	}
} else {
	$response['status'] = 1;
	$response['msg'] = "Wrong Request";
}
echo json_encode($response);
?>
