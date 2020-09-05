<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();

$post = $helpers->clearSlashes($_POST);		
$circle_url = BASE_URL."/api/recharge/report.php";
$post_fields = array("token"=>$_SESSION['TOKEN'],"limit"=>$post['limit']);
$responseRC = api_curl($circle_url,$post_fields,$headerArray);
$resRC = json_decode($responseRC,true);
//print_r($resRC);	
if($resRC['ERROR_CODE']==0){
	$res['data'] = $resRC['DATA'];
	echo json_encode($res);
} else {
	//echo "<option value='' selected>Circle not found</option>";
}
?>
