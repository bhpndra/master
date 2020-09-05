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
	if(isset($_GET['policyno']) && $_GET['policyno']!=''){


	$circle_url = "https://apiuat.religarehealthinsurance.com/relinterfacerestful/religare/restful/getPolicyPDFV2";
	$post_fields = '{"intFaveoGetPolicyPDFIO":{"policyNum":"'.$_GET['policyno'].'","ltype":"POLSCHD"}}';

	$headerArray = array( "appId: 516215", "signature: JsnNW921WJDN51CUaadctSNkGDWlXo/28TrIKuKUIhc=", "timestamp: 1568801564676", "agentId: 20008325", 'Content-Type: application/json');
	$responseRC = api_curl($circle_url,$post_fields,$headerArray);

	$responseRC=json_decode($responseRC);
	if($responseRC && $responseRC->responseData->message=='Success'){
	  $post_data = array("payment_status"=>1,
	  "depositDt"=>$responseRC->intFaveoGetPolicyPDFIO->dataPDF,
	  "policyNum"=>$responseRC->chequeDDReqResIO->policyNum,
	  "payment_date"=>date("Y-m-d H:i:s")
	  );

	  //echo $responseRC->intFaveoGetPolicyPDFIO->dataPDF;
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename=POLICY_'.$_GET['policyno'].'.pdf');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');

echo base64_decode($responseRC->intFaveoGetPolicyPDFIO->dataPDF);
	}
	elseif($responseRC && $responseRC->responseData->message=='Failed'){ print_r($responseRC->intFaveoGetPolicyPDFIO->errorLists[0]->errDescription.'. Contact Admin.');}


	} else {
		$response['status'] = 1;
		$response['msg'] = "Invalid request.";
		print_r($response['msg']);
	}
} else {
	$response['status'] = 0;
	$response['msg'] = "Invalid Token.";
	print_r($response['msg']);

}
?>
