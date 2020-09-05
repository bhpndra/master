<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();

	
function insta_curl($url, $post_fields){

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL            => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING       => "",
		CURLOPT_MAXREDIRS      => 10,
		CURLOPT_TIMEOUT        => 180,
		CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST  => "POST",
		CURLOPT_POSTFIELDS     => $post_fields,
		CURLOPT_HTTPHEADER     => array(
			"Accept: application/json",
			"Content-Type: application/json",
		),
	));

	$response = curl_exec($curl);
	$err      = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
	} else {
		return $response;
	}
}

if(isset($_SESSION['TOKEN'])){
	$url = BASE_URL."/api/users/get_token_details.php";
	$post_fields = array("token"=>$_SESSION['TOKEN']);
	$responseVT = api_curl($url,$post_fields,$headerArray);
	$resVT = json_decode($responseVT,true);
	if($resVT['ERROR_CODE']==1){
		$helpers->errorResponse("SESSION EXPIRED!");
	}
} else {
	$helpers->errorResponse("SESSION EXPIRED!");
}

$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];

$post = $helpers->clearSlashes($_POST); 
if(isset($post['status']) &&  isset($post['id']) && isset($post['uid'])){	

	/* $wlDetail = $mysqlClass->mysqlQuery(" select sms_api from add_cust where id = '".$WL_ID."' ");
	$resWL = $wlDetail->fetch(PDO::FETCH_ASSOC);

	if($resWL['sms_api']=='CUSTOM'){
		$sms_pack = $mysqlClass->mysqlQuery("select * from `sms_pack` WHERE `user_id`='".$WL_ID."' and `admin_id`='".$ADMIN_ID."'" )->fetch(PDO::FETCH_ASSOC);
	} else {
		$sms_pack = $mysqlClass->mysqlQuery("select * from `sms_pack` WHERE `user_id`='0' and `api_for`='ADMIN' and `admin_id`='".$ADMIN_ID."'" )->fetch(PDO::FETCH_ASSOC);
		if(empty($sms_pack['id'])){
			$sms_pack = $mysqlClass->mysqlQuery("select * from `sms_pack` WHERE `user_id`='0' and `api_for`='ADMIN' and `admin_id`='1'" )->fetch(PDO::FETCH_ASSOC);
		}
	}
	$smsParameters = json_decode($sms_pack['api_parameters'],true);
	$smsParameters[$sms_pack['param_mobile_name']] = $post['mobile'];
	$smsParameters[$sms_pack['param_msg_name']] = $userDetailes;
	$smsParameters['request_type'] = $sms_pack['request_type'];
	$smsParameters['url'] = $sms_pack['url'];
	$sms_api_res =  $helpers->send_msg_dynamic($smsParameters); */
	
	$id = base64_decode($post['uid']);
	$FR_status = $mysqlClass->mysqlQuery("SELECT status FROM `aeps_withdrawl_info` WHERE `id`='".$post['id']."' and `user_id`='".$id."' ")->fetch(PDO::FETCH_ASSOC);
	if($FR_status!="SUCCESS"){
			
				
$ext_tran = $helpers->transaction_id_generator('RP',3);
	
	$postFields["token"]	= "16034c0633a24d872d7fe58799a17d11";
	$postFields["request"]['sp_key']	= "DPN";
	$postFields["request"]['external_ref']	= $ext_tran;
	$postFields["request"]['credit_account']	= $post['account'];
	$postFields["request"]['credit_amount']	= ($post['amount'] - 10);
	$postFields["request"]['ifs_code']	= $post['ifsc'];
	$postFields["request"]['bene_name']	= $post['name'];
	$postFields["request"]['latitude']	= "27.9929";
	$postFields["request"]['longitude']	= "77.1231";
	$postFields["request"]['endpoint_ip']	= "43.225.193.238";
	$postFields["request"]['alert_mobile']	= $post['mobile'];
	$postFields["request"]['alert_email']	= "";
	$postFields["request"]['remarks']	= "Test";
	//echo json_encode($postFields); die();
	$url = "https://www.instantpay.in/ws/payouts/direct";
	$res = insta_curl($url, json_encode($postFields));
	//echo $res;
	$res1 = json_decode($res,true);
	if($res1['statuscode']=="TXN" || $res1['statuscode']=="TUP"){
		$data = array("status" => $post['status'], "payment_date" => date('Y-m-d H:i:s'), "refid" => $res1['data']['payout']['credit_refid'] );
		$rowU = $mysqlClass->updateData(" aeps_withdrawl_info ", $data , " WHERE `id`='".$post['id']."' and `user_id`='".$id."' " );
	}
				
		$response['ERROR_CODE'] = 0;
		$response['MESSAGE'] = 'SUCCESS';
		$response['DATA'] = $res1;
		echo json_encode($response); die();
			
	} else {
		$response['ERROR_CODE'] = 1;
		$response['MESSAGE'] = "NOT ALLOW TO UPDATE.";
		echo json_encode($response); die();
	}

	
} else {
	$response['ERROR_CODE'] = 1;
	$response['MESSAGE'] = "SOMETHING WRONG";
	echo json_encode($response); die();
}
	

?>
