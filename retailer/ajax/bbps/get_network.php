<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();

$post = $helpers->clearSlashes($_POST);		
$circle_url = BASE_URL."/api/bbps/get_network.php";
$post_fields = array("token"=>$_SESSION['TOKEN'],"operator_type"=>$post['operator_type']);
$responseRC = api_curl($circle_url,$post_fields,$headerArray);
$resRC = json_decode($responseRC,true);
//print_r($resRC);	
if($resRC['ERROR_CODE']==0){
	$circle = "<option value='' selected>Select Operator</option>";
	foreach($resRC['NETWORK'] as $rc){
		$circle .= "<option value='".$rc['operator_code']."'>".$rc['operator_name']."</option>";
	}
	echo $circle;
} else {
	echo "<option value='' selected>Circle not found</option>";
}
?>
