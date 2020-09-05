<?php
	// header('Access-Control-Allow-Origin: *');
	// header('Content-type: application/json');
	
	require_once '../../include/connect.php';

	// $postdata = json_decode( file_get_contents( 'php://input' ), true );
	$postdata = $_POST;
	
	if ( $postdata == false ) {
		$response['statuscode']  	= "ERR";
		$response['status']     	= "Invalid request!";
		$response['data']     		= "";
		echo json_encode($response);
		die();
	}
	
	if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
		$retailer_ip = $_SERVER['HTTP_ORIGIN'];
	}
	else if (array_key_exists('HTTP_REFERER', $_SERVER)) {
		$retailer_ip = $_SERVER['HTTP_REFERER'];
	} else {
		$retailer_ip = $_SERVER['REMOTE_ADDR'];
	}

	$api_access_key 	= validateInput($postdata['api_access_key']);
	$account   			= validateInput($postdata['account']);

	// echo '<pre>';
	// print_r($_POST);
	// echo '</pre>';
	
	if ( $api_access_key == '' ) {
		$response['statuscode']  	= "ERR";
		$response['status']     	= "Unauthorized access. Api access key is missing!";
		$response['data']     		= "";
		echo json_encode($response);
		die();
	}
	
	if ( $retailer_ip == '' ) {
		$response['statuscode']  	= "ERR";
		$response['status']     	= "Unauthorized access. Server ip is invalid!";
		$response['data']     		= "";
		echo json_encode($response);
		die();
	}
	
	$retailer_data = is_api_retailer_valid($api_access_key,$retailer_ip);
	
	if ( $retailer_data == false ) {
		$response['statuscode']  	= "ERR";
		$response['status']     	= "Unauthorized access!";
		$response['data']     		= "";
		echo json_encode($response);
		die();
	}
	
	$value = array(
		'request'  			=> json_encode($_REQUEST),
		'server_details'  	=> json_encode($_SERVER),
		'api_access_key'	=> $api_access_key,
		'retailer_ip' 		=> $retailer_ip,
		'request_date' 		=> date('Y-m-d H:i:s'),
	);

	$insert_id = insert('remitter_log', $value, $conn);
	
	if ( $insert_id == false ) {
		$response['statuscode']  	= "ERR";
		$response['status']     	= "Connection Error!";
		$response['data']     		= "";
		echo json_encode($response);
		die();
	}

	#{"token": "{{token}}","request": {"account": "{{account}}"}}
	$arr = array("token" =>$insta_token, "request"=> array("account"=> $account));
	$post_fields = json_encode($arr);
	
	$remit_contents  = insta_curl($get_bank_details, $post_fields);
	$data = json_decode($remit_contents,true);
	unset($data[0]);
	
	if ( !empty($data) ) {
		$values = array(
			'response' => $remit_contents,
		);
	
		update_qry('remitter_log', $values, 'id', $insert_id, $conn);

		echo $remit_contents;
		die();

	} else {

		$response['statuscode']  	= "ERR";
		$response['status']     	= "Request timeout!";
		$response['data']     		= "";
		
	}
	
	echo json_encode($response);
	die();
?>