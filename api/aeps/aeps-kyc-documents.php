<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("../../config.php");
	require("../classes/db_class.php");
	require("../classes/comman_class.php");
	require("../classes/user_class.php");
	require("../classes/jwt_encode_decode.php");
	$helpers = new Helper_class();
	$mysqlClass = new Mysql_class();
	$userClass = new User_class();
	$jwtED = new jwt_encode_decode();
	
	if($_SERVER['HTTP_X_API_KEY']==HTTP_X_API_KEY && $_SERVER['HTTP_NETPAISAPASSKEY']==NETPAISAPASSKEY){	
	} else {
		//print_r($_SERVER);
		$helpers->errorResponse("Authorization Invalid !");
	}
	
	if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST)){
		
		$post = $helpers->clearSlashes($_POST);
		
		//// Valided Token and Get Token Details ////
		if(isset($post['token'])){
			$res = $jwtED->decode_token($post['token']);
			if(isset($res->USER_ID) && $res->USER_ID > 0){
				$USER_ID 	= $res->USER_ID;
				$CREATOR_ID	= $res->CREATOR_ID;
				$WL_ID 		= $res->WL_ID;
				$ADMIN_ID 	= $res->ADMIN_ID;
			} else {
				$helpers->errorResponse("Token Expire");
			}
		} else {
			$helpers->errorResponse("Token not set!");
		}
		/////////////////// End //////////////////

		//// Check Admin api_access_key ////
		$apiAccessKey = $userClass->get_api_access_key($ADMIN_ID);
		if(empty($apiAccessKey['api_access_key'])){
			$helpers->errorResponse("Admin API Access Key Not Set.");
		}
		$api_access_key = $apiAccessKey['api_access_key'];
		/////////////////// End //////////////////
		
		//// Check Rquired Parameters Isset ////
		$pan_no = $post['pan_no'];
		$outletid = $post['outletid'];
		if(empty($pan_no)){
			$helpers->errorResponse("Pan No. is missing.");
		}
		if(empty($outletid)){
			$helpers->errorResponse("Outlet Id is missing.");
		}
		/////////////////// End //////////////////
		
		//// Call netpaisa_curl ////
		$request_param = ['api_access_key' => $api_access_key,'outletid'=>$outletid,'pan_no'=>$pan_no];

		$outlet_json = $helpers->netpaisa_curl($get_kyc_document, $request_param);
		
		$outlet_result = json_decode($outlet_json, true);
		
		if($outlet_result['ERR_STATE']==0 && isset($outlet_result['APPROVED_STATE'])){ 
			if($outlet_result['APPROVED_STATE']=="Completed."){
				$response['ERROR_CODE'] = 0;
				$response['MESSAGE'] = $outlet_result['APPROVED_STATE'];			
				$valueOutlet = array(								
								'outlet_kyc'		=> 1,
								'status'			=> 1
							);
				$mysqlClass->updateData('outlet_kyc',$valueOutlet, " where user_id = '".$USER_ID."' and  outletid = '".$outletid."'");
			} 
			else {
				$APPROVED = isset($outlet_result['RESPONSE']['data']['APPROVED']) ? $outlet_result['RESPONSE']['data']['APPROVED'] : array();
				$SCREENING = isset($outlet_result['RESPONSE']['data']['SCREENING']) ? $outlet_result['RESPONSE']['data']['SCREENING'] : array();
				
				$reason['Aadhaar'] = "Aadhaar is not submited";
				$screening['Aadhaar'] = "";
				
				foreach($APPROVED as $k=>$d){
					if(in_array("MANDATORY",$APPROVED[$k])){
						if(in_array("14",$APPROVED[$k])){
							$reason['Aadhaar'] = "";
						}
					}
				}
				
				foreach($SCREENING as $k=>$d){
					if(in_array("MANDATORY",$SCREENING[$k])){
						if(in_array("14",$SCREENING[$k])){
							$screening['Aadhaar'] = "Aadhaar On Screening";
						}
					}
				}
				$response['ERROR_CODE'] = 1;
				$response['MESSAGE'] = $outlet_result['APPROVED_STATE'];
				$response['REASON'] = $reason;
				$response['SCREENING'] = $screening;
			}
		}
		else {
			$helpers->errorResponse('Something went to wrong !');
		}

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>