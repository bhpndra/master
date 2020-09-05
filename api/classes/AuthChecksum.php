<?php
/**********************************************************
 Controller Name : AuthChecksum Controller Class
 Developer Name : Bhoopendra Kumar 

***********************************************************/
class AuthChecksum extends Database{

	public function __construct(){
		parent::getConnection();
	}
	 
	function authenticate_checksum($request,$package_name){
			
        $source_checkum = $request['checksum'];
        //$package_name = $request['package_name'];
		
        $checksum = ''; // secret key will be unique for all customers

        if (isset($source_checkum) && !empty($source_checkum)) {
			
            $checksum = $source_checkum;
            unset($request['checksum']);
            $post_data = $request;

            $post_data = urldecode(http_build_query($post_data));
			
            //$secret_keys = $this->db2->select('*')->from('st_secret_keys')->get()->result_array();
			
			$query = " SELECT * FROM netpaisa_keys WHERE status=1  ORDER BY id DESC ";
			$stmt = $this->db_conn->prepare($query);
			$stmt->execute();
			$secret_keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
			$Credentials="";
			if($stmt->rowCount() > 0){		
		
                foreach ($secret_keys as $value) {
					
					$key_name=$value['key_name'];
					$key_value=$value['key_value'];
					
					$Credentials .= $key_name."=".$key_value.",";
					
                }
				
				
				$Keys = substr($Credentials,0,-1);
				$Keys = "PackageName=".$package_name.",".$Keys;
				
				$generate_checkum = hash_hmac('sha256', $post_data, $Keys );
				
				if ($checksum == $generate_checkum) {
					//echo "Checksum matched";
					
					$this->checksum_source = $generate_checkum;
					$this->check_token = $generate_checkum;
				}
				
				
				$abc= array(
					"s_key"=>$Keys,
					"params"=>$post_data,
					"generate_checkum"=>$generate_checkum,
					"received_checkum"=>$checksum,
				);
				
				    $sql = "INSERT INTO checksum_history (s_key,params,generate_checkum,received_checkum) 
											VALUES (:s_key, :params, :generate_checkum,:received_checkum )";
					$stmt= $this->db_conn->prepare($sql);
					$result = $stmt->execute($abc);
				
				
            }
			
            if (empty($this->checksum_source)) {
				
				
                $response = [];
                $response['status'] = 'error';
                $response['message'] = 'UN-AUTHORIZED ACCESS';
				header('HTTP/1.1 201 Error');
                header('Content-Type: application/json');
                echo json_encode($response);
                die();
            } else {
                return true;
            }
        } else {
            $response = [];
            $response['status'] = 'error';
            $response['message'] = 'Checksum token missing.';
            header('HTTP/1.1 201 Error');
            header('Content-Type: application/json');
            echo json_encode($response);
            die();
        }
    }


    function authcheck($headers_data = ''){
		
        
		// if (!function_exists('getallheaders')) {
			
				// $headers = [];
				// foreach ($_SERVER as $name => $value) {
					// if (substr($name, 0, 5) == 'HTTP_') {
						// $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
					// }
				// }
				
			 
		// }else{
			// $headers = getallheaders();
		// }
		
	
		
		//print_r($headers);exit;
		
        $auth_token = null;
        $auth_rtn = null;
        $device_id = null;


	//$TokenExist = $this->CheckTokenExistsOnDeviceId( $headers_data['device_id'] );

	if( $_POST['api_mode']=="login"   ){
		
		$auth_rtn['auth_status'] = 'VALID';
		//$auth_rtn['user_id'] = $auth_token_row['user_id'];
		$auth_rtn['error'] = false;
		$auth_rtn['error_msg'] = '';
	}
	else{
		
        if (isset($headers_data['auth_token']) && !empty($headers_data['auth_token'])) {
			
            $auth_token = $headers_data['auth_token'];
            $device_id = $headers_data['device_id'];
            $device_version = $headers_data['device_version'];
			
			
            $this->auth_token = $auth_token;
            $this->device_id = $device_id;

            if ('0' != $this->check_token) {
                $auth_rtn = $this->CheckWebServiceOAuthToken($auth_token, $device_id);
            } else {
                $auth_token_row = $this->GetWebServiceOAuthToken($auth_token, $device_id);
                $auth_rtn['auth_status'] = 'VALID';
                $auth_rtn['user_id'] = $auth_token_row['user_id'];
                $auth_rtn['error'] = false;
                $auth_rtn['error_msg'] = '';
            }

            if (false !== $auth_rtn['error']) {
                $response = [];
                $response['status'] = 'error';
                $response['message'] = 'Login expired! Please re-login.';
                header('HTTP/1.1 201 Error');
                header('Content-Type: application/json');
                echo json_encode($response);
                die();
            } else {
                $this->UpdateWebserviceOAuthTokenTime($auth_token);
                $logged_user_id = $auth_rtn['user_id'];

                return $logged_user_id;
            }
        } else {
            $response = [];
            $response['status'] = 'error';
            $response['message'] = 'Login expired! Please re-login.';
            header('HTTP/1.1 201 Error');
            header('Content-Type: application/json');
            echo json_encode($response);
            die();
        }
		
	}
    }
 
 
     function CheckWebServiceOAuthToken($auth_token, $device_id)
    {
        $response = array();
        if (isset($auth_token) && !empty($auth_token)) {
			
            $auth_token_row = $this->GetWebServiceOAuthToken($auth_token, $device_id);
            if ($auth_token_row != false) {
                if (time() < strtotime("+60 minutes", strtotime($auth_token_row['update_date']))) {
                    $user_id                    = $auth_token_row['user_id'];
                    $response['auth_status']    = "VALID";
                    $response['user_id']        = $user_id;
                    $response['error']          = FALSE;
                    $response['error_msg']      = "";
                } else {
                    $response['auth_status']    = "LOGIN_REQUIRED";
                    $response['error']          = TRUE;
                    $response['error_msg']      = "Login Expired";
                }
            } else {
                $response['auth_status']    = "LOGIN_REQUIRED";
                $response['error']          = TRUE;
                $response['error_msg']      = "Invalid auth token";
            }
        } else {
            $response['auth_status']    = "LOGIN_REQUIRED";
            $response['error']          = TRUE;
            $response['error_msg']      = "OAuth token missing.";
        }

        return $response;
    }
 
 
	function GetWebServiceOAuthToken($auth_token, $device_id){
	  
	  $query = " SELECT * FROM OAuth_tokens WHERE token='$auth_token' and published=1 and device_id='$device_id' ";
	
			$stmt = $this->db_conn->prepare($query);
			$stmt->execute();
			$TokenRow = $stmt->fetch(PDO::FETCH_ASSOC);			
			if($stmt->rowCount() > 0){
				
				if(!empty($TokenRow)){
					return $TokenRow;
				}else{
					return false;
				}
				
			}else{
				return false;
			} 
	 
    }
 
 
   function UpdateWebserviceOAuthTokenTime($auth_token){
		
		$query="UPDATE OAuth_tokens SET update_date='".date("Y-m-d H:i:s")."' WHERE token='".$auth_token."' and published=1  ";						
		$stmt = $this->db_conn->prepare($query);
		$result = $stmt->execute();
		
		 if($result) {						 
			return true;
		} else {
			return false;
		}
		
    }

	
    function CreateWebServiceOAuthToken($user_id, $device_id, $source){
		
        $token_str =  time() . openssl_random_pseudo_bytes(128) . $user_id . $device_id;
        $source = empty($source) ? "" : $source;
        $token = hash("sha256", $token_str);
        while (true) {
            $auth_row = $this->GetWebServiceOAuthToken($token, $device_id);
            if ($auth_row) {
                return $this->CreateWebServiceOAuthToken($user_id, $device_id);
            } else {
                $auth = array(
                    "token"         => $token,
                    "create_date"   => date("Y-m-d H:i:s"),
                    "update_date"   => date("Y-m-d H:i:s"),
                    "published"     => 1,
                    "source"     => $source,
                    "device_id"     => $device_id
                );

                if ($this->CheckTokenExists($user_id, $device_id) === true) {
					
					$query="UPDATE OAuth_tokens SET ";
					foreach($auth as $key=>$val){
					$query .= $key."='".$val."',";
					}
					$query = substr($query,0,-1);
					
					$query .= " WHERE user_id='".$user_id."' and device_id='$device_id'   ";	
					
					$stmt = $this->db_conn->prepare($query);
					$result = $stmt->execute();
					
					 if($result) {						 
                        return $token;
                    } else {
                        return false;
                    }
                } else {
					
                    $auth['user_id'] = $user_id;                   
					$sql = "INSERT INTO OAuth_tokens (token, create_date, update_date,published, source, device_id, user_id) 
											VALUES (:token, :create_date, :update_date, :published, :source, :device_id, :user_id)";
					$stmt= $this->db_conn->prepare($sql);
					$result = $stmt->execute($auth);
					
					 if( $result ) {
                        return $token;
                    } else {
                        return false;
                    }
					
                }
            }
        }
    }
	
	
	function CheckTokenExists($user_id, $device_id){
		 
        if (isset($user_id) && !empty($user_id)) {
			
			$query = " SELECT * FROM OAuth_tokens WHERE user_id='$user_id' and device_id='$device_id' ";
			$stmt = $this->db_conn->prepare($query);
			$stmt->execute();
			$stmt->fetch(PDO::FETCH_ASSOC);	
			
			if($stmt->rowCount() > 0){
				   return true;
            } else {
                return false;
            }
			
        }
		
    }
	
	
	function CheckTokenExistsOnDeviceId($device_id){
		 
        if (isset($user_id) && !empty($user_id)) {
			
			$query = " SELECT * FROM OAuth_tokens WHERE  device_id='$device_id' ";
			$stmt = $this->db_conn->prepare($query);
			$stmt->execute();
			$stmt->fetch(PDO::FETCH_ASSOC);	
			
			if($stmt->rowCount() > 0){
				   return true;
            } else {
                return false;
            }
			
        }
		
    }
 
}


	// $Auth =	new AuthChecksum();
	// $_POST['name']="Bhoopendra";
	// $_POST['fname']="Rambabu";
	// $_POST['checksum']="RamjkskjkjasKAJkjabkjABDbabu";
	// $rsp = $Auth->authenticate_checksum($_POST);
	// print_r($rsp);



?>