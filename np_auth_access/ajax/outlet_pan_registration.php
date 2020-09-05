<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include_once('../inc/apivariable.php');
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_REQUEST);

	if (!empty($post['mobile']) && $post['action']=="sendOtp") {
		$mobile = $helpers->validateMobile($post['mobile']);
	
		if($mobile == FALSE) {
			echo "<font color='red'><p align='left'>Please Enter Valid Mobile No</p></font>";
		} else {
			$post_fields = array( "api_access_key" => $api_access_key,"mobile" => $mobile );
			$pan_register_otp = $helpers->netpaisa_curl($pan_otp_req_register, $post_fields);

			$pantdata = json_decode($pan_register_otp, true);
			//print_r( $pantdata);
			//on success
			if ($pantdata['DATA']['statuscode'] == "TXN"){
				echo "<font color='green'>".$pantdata['DATA']['status']."</span>";
			}
		}
	}

	if ( isset($post['agent_userid']) && isset($post['mobile']) && isset($post['name']) && isset($post['email']) && isset($post['company']) && isset($post['pan_no']) && isset($post['pincode']) && isset($post['address']) && isset($post['otp']) && $post['action']=="outlet") {

		$errMsg         = "";
        $agent_userid   = $post['agent_userid'];
        $mobile         = $post['mobile'];
        $name           = $post['name'];
        $email          = $post['email'];
        $company        = $post['company'];
        $pan_no         = strtoupper($post['pan_no']);
        $pincode        = $post['pincode'];
        $address        = $post['address'];
        $otp            = $post['otp'];
		
		if ( empty( $mobile ) ):
            $errMsg = "Please enter mobile no";
        elseif ( $helpers->validateMobile($mobile) == FALSE ):
            $errMsg = "Please enter a valid mobile no";
        
        elseif ( empty( $name ) ):
            $errMsg = "Please enter name";
        elseif ( empty( $email ) ):
            $errMsg = "Please enter emailid";
        elseif ( empty( $company ) ):
            $errMsg = "Please enter company";
        elseif ( empty( $pan_no ) ):
            $errMsg = "Please enter pan_no";  
        elseif ( empty( $pincode ) ):
            $errMsg = "Please enter pincode"; 
        elseif ( empty( $address ) ):
            $errMsg = "Please enter address";
        elseif ( empty( $otp ) ):
            $errMsg = "Please enter otp";     
        else:
            $errMsg = "";
        endif;
		if ( empty( $errMsg ) ) {
			$fields = array(
                    'api_access_key'        => $api_access_key, 
                    'mobile'                => $post['mobile'],
                    'email'                 => $post['email'],
                    'company'               => $post['company'],
                    'name'                  => $post['name'],
                    'pan'                   => $post['pan_no'],
                    'pincode'               => $post['pincode'],
                    'address'               => $post['address'],
                    'otp'                   => $post['otp']
            );
			
			$result = $helpers->netpaisa_curl($outlet_register, $fields);
			$rech_data  = json_decode($result);
			//print_r($rech_data); echo "<br/> ";
			if ( !empty($rech_data) ) {               
                $message    = $rech_data->MSG;
                $err_state  = (int)$rech_data->ERR_STATE;
                $statuscode     = $rech_data->DATA->statuscode;
                $status         = $rech_data->DATA->status;
                $outlet_id      = $rech_data->DATA->outlet_id;
                $mobile_number  = $rech_data->DATA->mobile_number;
                $outlet_name    = $rech_data->DATA->outlet_name;
                $email_id       = $rech_data->DATA->email_id;
                $contact_person = $rech_data->DATA->contact_person;
                $pan_no         = $rech_data->DATA->pan_no;
                $kyc_status     = $rech_data->DATA->kyc_status;
                $outlet_status  = $rech_data->DATA->outlet_status;
				//echo "<br/> Pass to insert";
				if ( $err_state == 0 && $statuscode === 'TXN' ) { //echo "<br/> Condition True<br/>";
					$value = array(
                        "name"              => $name,
                        "company"           => $outlet_name,
                        "address"           => $address,
                        "phone1"            => $mobile_number,
                        "email"             => $email_id,
                        "pan_no"            => $pan_no,
                        "outletid"          => $outlet_id,
                        "outlet_status"     => 'Pending',
                        "registration_date" => date('Y-m-d H:i:s'),
                        "user_id"           => $agent_userid,
                        "sources"           => "I"
                    );
					//print_r($value);
					$last_id = $mysqlClass->insertData("outlet_kyc", $value);
					//echo $last_id;
				} else {
                    echo "Error : ". $message;
                }
			} else {
                echo "Error : "."Invalid Response!!";
            }
		}  else {
            echo "Error : ".$errMsg;
        }
		
	}
?>