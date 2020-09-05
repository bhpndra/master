<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();

$post = $helpers->clearSlashes($_POST);		
$state_url = BASE_URL."/api/insurance/get_state.php";
$post_fields = array("token"=>$_SESSION['TOKEN']);
$responseRC = api_curl($state_url,$post_fields,$headerArray);
$resRC = json_decode($responseRC,true);
	
if($resRC['ERROR_CODE']==0){
	$state = "<option value='' selected>Select State</option>";
	foreach($resRC['DATA'] as $rc){
		$state .= "<option value='".$rc['statecd']."'>".$rc['statecd']."</option>";
	}
	echo $state;
} else {
	echo "<option value='' selected>State not found</option>";
}
?>
