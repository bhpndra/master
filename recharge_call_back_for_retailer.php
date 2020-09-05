<?php
	$request_data = (array) json_decode(file_get_contents('php://input'), TRUE);
		require("config.php");
		require("include/lib.php");
		require("api/classes/db_class.php");
		require("api/classes/comman_class.php");
		$helpers = new helper_class();

	$post = $helpers->clearSlashes($request_data);		
	$circle_url = BASE_URL."/api/recharge/get_status.php";
	$post_fields = array("agent_tranid"=>$post['clientid']);
	//print_r($post_fields);
	echo $responseRC = api_curl($circle_url,$post_fields,$headerArray);
?>