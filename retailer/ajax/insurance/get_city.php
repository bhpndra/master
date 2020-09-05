<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();

$post = $helpers->clearSlashes($_POST);		
$city_url = BASE_URL."/api/insurance/get_city.php";
$post_fields = array("token"=>$_SESSION['TOKEN'],"statecd"=>$post['statecd']);
$responseRC = api_curl($city_url,$post_fields,$headerArray);
$resRC = json_decode($responseRC,true);
//print_r($resRC);	
if($resRC['ERROR_CODE']==0){
	$city = "<option value='' selected>Select City</option>";
	foreach($resRC['DATA'] as $rc){
		$city .= "<option value='".$rc['citycd']."'>".$rc['citycd']."</option>";
	}
	echo $city;
} else {
	echo "<option value='' selected>City not found</option>";
}
?>
