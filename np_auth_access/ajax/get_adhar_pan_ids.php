<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include_once('../inc/apivariable.php');
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);

	if (!empty($post['outletid']) && !empty($post['pan_no'])) {
		$url = 'https://netpaisa.com/nps/api/aeps/get_kyc_pan_aadhar_id';

		$fields = [
              'api_access_key'        => $api_access_key, 
              'outletid'              => $post['outletid'],
              'pan_no'                => $post['pan_no']
		];
		$pan_register_otp = $helpers->netpaisa_curl($url, $fields);

		
		$data = json_decode($pan_register_otp, true);
		$s = 0;
		$arrS = array();
		foreach($data['DATA']['data']['SCREENING'] as $k=>$v){
			if($data['DATA']['data']['SCREENING'][$k][1]){
				$arrS[$s] = $data['DATA']['data']['SCREENING'][$k][1];
			} else {
				//$data['screening'.$i] = '';
			}
			$s++;
		}
		if(!empty($arrS)){
			$data['screening'] = implode(",",$arrS);
		} else {
			$data['screening'] = '';
		}
		
		
		$a = 0;
		$arrA = array();
		foreach($data['DATA']['data']['APPROVED'] as $k=>$v){
			if($data['DATA']['data']['APPROVED'][$k][1]){
				$arrA[$a] = $data['DATA']['data']['APPROVED'][$k][1];
			} else {
				//$data['screening'.$i] = '';
			}
			$a++;
		}
		if(!empty($arrA)){
			$data['approved'] = implode(",",$arrA);
		} else {
			$data['approved'] = '';
		}
	
		echo json_encode($data, true);
	}
	
	
?>