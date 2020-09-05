<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include_once('../inc/apivariable.php');
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);

	if (!empty($post['outletid']) && !empty($post['pan_no'])) {
		$url = 'https://netpaisa.com/nps/api/aeps/get_kyc_document';

		$fields = [
              'api_access_key'        => $api_access_key, 
              'outletid'              => $post['outletid'],
              'pan_no'                => $post['pan_no']
		];
		$pan_register_otp = $helpers->netpaisa_curl($url, $fields);

		$pantdata = json_decode($pan_register_otp, true);

		echo "<strong>KYC Status :</strong>" . $pantdata['APPROVED_STATE']."<br/>";
		
			$APPROVED = $pantdata['RESPONSE']['data']['APPROVED'];			
			
			$reason = array();
			$reason['Business_Address'] = "Business Address Proof is not submited";
			$reason['Aadhaar'] = "Aadhaar is not submited";
			foreach($APPROVED as $k=>$d){
				if(in_array("MANDATORY",$APPROVED[$k])){
					if(in_array("14",$APPROVED[$k])){
						$reason['Aadhaar'] = "";
					}
					if(in_array("23",$APPROVED[$k])){
						$reason['Business_Address'] = "";
					}
				}

			}
		if(!empty($reason['Aadhaar']) || !empty($reason['Business_Address'])){
			echo "<strong>Reason :</strong>" .implode(",<br/>",$reason)."<br/>";
			 
		} else {
			$resOT = $mysqlClass->get_field_data("outlet_status", "outlet_kyc", " WHERE `outletid`='".$post['outletid']."' and `sources`='I'");
			if(isset($resOT['outlet_status']) && $resOT['outlet_status']!="Approved"){
				$mysqlClass->mysqlQuery("UPDATE outlet_kyc SET `outlet_status` ='Approved' WHERE `outletid`='".$post['outletid']."' and `sources`='I'");
			}			
		}
		
		$s = 0;
		$arrS = array();
		foreach($pantdata['RESPONSE']['data']['SCREENING'] as $k=>$v){
			if($pantdata['RESPONSE']['data']['SCREENING'][$k][1]){
				$arrS[$s] = $pantdata['RESPONSE']['data']['SCREENING'][$k][1];
			} else {
				
			}
			$s++;
		}
		if(!empty($arrS)){
			echo   "<strong>Recordes in screening: </strong>" . implode(", ",$arrS);
		} else {
			
		}		
		
		$a = 0;
		$arrR = array();
		foreach($pantdata['RESPONSE']['data']['REQUIRED'] as $k=>$v){
			if($pantdata['RESPONSE']['data']['REQUIRED'][$k][2]){
				if($pantdata['RESPONSE']['data']['REQUIRED'][$k][2] == "MANDATORY"){
					$arrR[$a] = $pantdata['RESPONSE']['data']['REQUIRED'][$k][1];
				}
			} else {
				
			}
			$a++;
		}
		if(!empty($arrR)){
			echo  "<br/><strong>Recordes not Submited/Rejected: </strong>" . implode(", ",$arrR);
		} else {
			
		}
	//print_r($pantdata);
	}
	
?>