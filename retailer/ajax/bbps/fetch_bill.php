<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();

$post = $helpers->clearSlashes($_POST);		
$circle_url = BASE_URL."/api/bbps/fetch_bill.php";
$post_fields = $post;
$post_fields["token"] = $_SESSION['TOKEN'];
echo $responseRC = api_curl($circle_url,$post_fields,$headerArray);

?>
