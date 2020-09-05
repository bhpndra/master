<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();

$post = $helpers->clearSlashes($_POST);		
$circle_url = BASE_URL."/api/recharge/get_plan.php";
$post_fields = array("token"=>$_SESSION['TOKEN'],"circle_code"=>$post['circle_code'],"operator"=>$post['operator']);
echo $responseRC = api_curl($circle_url,$post_fields,$headerArray);

?>
