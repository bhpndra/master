<?php
	require("../config.php");
	require_once('../api/classes/db_class.php');
	require_once('../api/classes/comman_class.php');
	$mysqlObj = new mysql_class();
	$helpers = new helper_class();
	
	$post = $helpers->clearSlashes($_POST);
	
	if(isset($_POST)){
		if(isset($post['password']) && isset($post['userid'])){
			$url = BASE_URL."/api/users/login.php";
			$response = $helpers->api_curl($url,$post,$headerArray);
			
		} else {
			$response['status'] = 0;
			$response['msg'] = "<div class='alert alert-warning alert-dismissible'>
										<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
										<strong>Warning!</strong> Username or password empty.
									</div>";
		}
	} else {
		$response['status'] = 0;
		$response['msg'] = "Wrong Request";
	}
	
	echo json_encode($response);
?>