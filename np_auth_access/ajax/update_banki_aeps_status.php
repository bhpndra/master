<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include("../classes/user_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	$userClass = new user_class();
	
	$post = $helpers->clearSlashes($_POST);
	

	if($post['id']!="" && $post['pan']!="")
	{	
		$value = array(
			'mobile' => $post['mobile'],
			'pan_no' => $post['pan'],
			'user_id' => $post['id'],
			'api_access_key' => "263aa8f8d4b50c37629bfa038c298d54",
		);
		//print_r($value);
		$np_yesbank_aeps_outlet_details = "https://www.digitalmoneysoftech.in/api/ws/aeps/yesbank_outlet_details.php";
		$np_response = $helpers->netpaisa_curl($np_yesbank_aeps_outlet_details,$value);
		$dec_np_response = json_decode($np_response, true);
		 
$data['outlet_status'] = $dec_np_response['DATA']['outlet_status'];
$data['kyc_status'] = $dec_np_response['DATA']['kyc_status'];
		echo json_encode($data, true);
	} 
?>