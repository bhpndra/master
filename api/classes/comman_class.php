<?php
	class Helper_class{
		
		 function clearSlashes($request){
				foreach($request as $key => $val){
					if(is_array($val)){
						$request[$key] = $this->clearSlashes($val);
					} else {
						$request[$key] = addslashes(trim($val));
					}					
				}
				return $request;
		 }
		 
		 function fileUpload($FILES,$path,$imgPrifix,$rename=false){
				$allowed_image_extension = array(
					"png",
					"PNG",
					"jpg",
					"JPG",
					"jpeg",
					"JPEG"
				);

				// Get image file extension
				$file_extension = pathinfo($FILES["name"], PATHINFO_EXTENSION);
				$checkImage = getimagesize($FILES["tmp_name"]);
				if (! file_exists($FILES["tmp_name"])) {
					$response = array(
						"type" => "error",
						"message" => "Choose image file to upload."
					);
				} else if (! in_array($file_extension, $allowed_image_extension) || $checkImage === false) {
					$response = array(
						"type" => "error",
						"message" => "Upload valid images. Only PNG and JPEG are allowed."
					);
					echo $result;
				} else {
					if($rename==true){
						$fileName = $imgPrifix.".". $file_extension;
					} else {
						$fileName = basename($FILES["name"]);
					}
					$target = $path . $fileName;
					if (move_uploaded_file($FILES["tmp_name"], $target)) {
						$response = array(
							"type" => "success",
							"message" => "Image uploaded successfully.",
							"filename" => $fileName
						);
					} else {
						$response = array(
							"type" => "error",
							"message" => "Problem in uploading image files."
						);
					}
				}
				return $response;
		 }
		 
		 function random_string( $length = 8 ) {
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%";
			$password = substr( str_shuffle( $chars ), 0, $length );
			return $password;
		}
		 
		function hashPassword($password) {
			$salt = sha1(md5($password)).'em01vZsQ2lB8g0spqrzxmlYVVikash';
			$salt = substr($salt, 5, 12);
			$encrypted = base64_encode(sha1($password . $salt, true) . $salt);
			$hash = array("salt" => $salt, "encrypted" => $encrypted);
			return $hash;
		}
		
		function hashPin($pin) {
			$salt = sha1(md5($pin)).'em01vZsQ2lB8g0spqrzxmlYVVh';
			$salt = substr($salt, 3, 10);
			$encrypted = base64_encode(sha1($pin . $salt, true) . $salt);
			$hash = array("salt" => $salt, "encrypted" => $encrypted);
			return $hash;
		}
		
		function random_api_access_key( $length = 10 ) {
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$salt = 'NPSAPI';
			$rand_access_key = substr( str_shuffle( $chars ), 0, $length );
			$encrypted_rand_access_key = md5(sha1($salt.$rand_access_key));
			return $encrypted_rand_access_key;
		}
		
		function transaction_id_generator($prifix,$length){
			$l = empty($length)? 8 : $length;
			$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$randomString = '';
			for ($i = 0; $i < $l; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			
			$timestamp = time();			
			$txid = $prifix.$timestamp.$randomString;
			return $txid;
		}
		
		function create_durations($createDate){
			date_default_timezone_set('Asia/Kolkata');
			$timestamp = time();
			$date1 = date_create($createDate);
			$currentDate=date_create(date("Y-m-d H:m:s",$timestamp));
			$diff = date_diff($date1,$currentDate);
			
			if($diff->s > 0){
				$duration = $diff->s. " sec";		
			}	
			if($diff->i > 0){
				$duration = ($diff->i > 1) ? $diff->i. " mins" : $diff->i. " min";		
			}
			if($diff->h > 0){
				$duration = ($diff->h > 1) ? $diff->h. " hours" : $diff->h. " hour";		
			}
			if($diff->days > 0){
				$duration = ($diff->days > 1) ? $diff->days. " days" : $diff->days. " day";		
			}
			return $duration; 
		}
		
		function createDate($date,$format = 'Y-m-d'){
			return ((bool)strtotime($date)) ? date_format(date_create($date),$format) : false;
		}
		
		function send_msg($mobile,$msg){
			 $message = str_replace(" ","%20",$message);
				$curl = curl_init();
				$sender_id = "NETPAI";
				$authkey = "PP3Uo5ysAtR7eDQ0";
				curl_setopt_array($curl, array(
					CURLOPT_URL => "https://buzzify.in/V2/http-api.php?apikey=$authkey&senderid=$sender_id&number=$mobile&message=$message&format=json",
					CURLOPT_RETURNTRANSFER => true
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
					$response = "cURL Error #:" . $err;
				} else {
					$response;
				}
				return $response;
		}
		
		
		function send_msg_dynamic($smsParam){
			
			$url = $smsParam['url'];
			$request_type = $smsParam['request_type'];
			unset($smsParam['url']);
			unset($smsParam['request_type']);
			
			
			if($request_type=="GET"){
				$dataString = http_build_query($smsParam);
				$url .= '?'.str_replace(" ","%20",$dataString);
				
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true
				));
				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
					$response = "cURL Error #:" . $err;
				} else {
					$response;
				}
				return $response;
				
			} 
			else if($request_type=="POST"){
				$dataString = http_build_query($smsParam);
				$url .= '?'.$dataString;
								
				   $curl = curl_init();
					curl_setopt_array($curl, array(						
						CURLOPT_URL => $url,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_SSL_VERIFYHOST => 0,
						CURLOPT_SSL_VERIFYPEER => 0,
					));
					$response = curl_exec($curl);
					$err = curl_error($curl);
					curl_close($curl);
					if ($err) {
						$response = "cURL Error #:" . $err;
					} else {
						$response;
					}
					return $response;
				
			}
			else {
				return 'Invalid Parameters';
			}
			 
		}
		
		function validateMobile($phoneNumber) {
			if (preg_match('/^\d{10}$/', $phoneNumber)) { // phone number is valid
				return $phoneNumber;
			} else { // phone number is not valid
				return FALSE;
			}
		}
		
		function netpaisa_curl($url, $post_fields) {
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 120,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $post_fields,
				/*  CURLOPT_HTTPHEADER => array(
					//"Accept: application/json",
					//"Content-Type: application/json"
				), */
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			if ($err) {
				echo "cURL Error #:" . $err;
			} else {
				return $response;
			}
		}
				
		function errorResponse($msg) {
			$res= [];    
			$res['ERROR_CODE'] = 1;
			$res['MESSAGE'] = $msg;
			echo json_encode($res); die();
		}
		
		function alert_message($message,$alertTypeClass){
			/* alertTypeClass  :    alert-danger , alert-success , alert-info , alert-warning , alert-danger   */
			@$msg .= "<div class=\"alert ".$alertTypeClass." alert-dismissable\">";
				$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
							&times;
					  </button>";
				$msg .= $message;
			$msg .= "</div>";	
			return $msg;
		}
		
		function flashAlert_set($alertName,$message){
			  $_SESSION[$alertName] = $message;
		}
		
		function flashAlert_get($alertName){
			if(isset($_SESSION[$alertName])){
				$alert = $_SESSION[$alertName];
				unset($_SESSION[$alertName]);
				return $alert;
			} else {
				return false;
				//return "Not Set";
			}
		}
		function redirect_page($url){
			if(!empty($url)){
				echo "<script> window.location = '".$url."'; </script>";	
				die();
			} else {
				echo "Redirection url missing";
			}			
		}
		
		function checkLogin(){
			if(!isset($_SESSION[_session_userid_]) && empty($_SESSION[_session_userid_])){
				header("location:login.php");
				die();
			}
		}
		 
		 
	}
	
	class Mysql_class{
		//private $db_conn;
		
		public function __construct()
		{
			$database = new Database();
			$db = $database->getConnection();
			$this->db_conn = $db;
		}
		
		// Insert row
		function insertData($table,$dataArray){			
			$fields = array_keys($dataArray);
			$fildvalu = array_values($dataArray);	
			
			$col_list = implode(",", $fields);
			$param_list = implode(",", array_fill(1,count($fields), "?"));
			
			$sql="INSERT into $table ($col_list) VALUES ($param_list)";
			$stmt = $this->db_conn->prepare($sql);
			
			$stmt->execute($fildvalu);
			$error= $stmt->errorInfo();

			if($error[0]!=0){
				echo "Error1: " .$error[2];
			} else {
				return $this->db_conn->lastInsertId();
			}
		}
		
		// Update row
		function updateData($table,$dataArray,$condition){
			$fields = array_keys($dataArray);
			$fildvalu = array_values($dataArray);	
			$data = "";
			foreach($fields as $feild){
				$data .= "`".$feild."`". "= ?, ";
			}
			$data = substr($data, 0, -2);
			$sql="UPDATE $table set $data $condition";
			$stmt = $this->db_conn->prepare($sql);
			
			$stmt->execute($fildvalu);
			$error = $stmt->errorInfo();

			if($error[0]!=0){
				echo "Error1: " .$error[2];
			} else {
				return $stmt->rowCount();
			}
		}
		
		//Count Total Rows
		function countRows($sql){
			$prep_state = $this->db_conn->prepare($sql);
			$prep_state->execute();
			$num = $prep_state->rowCount();
			return $num;
		}
		
		//Fetch All Row
		function fetchAllData($table,$fields,$condition){ 
			$prep_state = $this->db_conn->prepare("SELECT ".$fields." FROM ".$table." ".$condition);
			$prep_state->execute();			
			$row = $prep_state->fetchAll(PDO::FETCH_ASSOC);			
			if($prep_state->errorInfo()[0]!=0){
				echo "Error: " .$prep_state->errorInfo()[2];
			} else {
				return $row;
			}
		} 
		
		//Fetch Single Row
		function fetchRow($table,$fields,$condition){
			$prep_state = $this->db_conn->prepare("SELECT ".$fields." FROM ".$table." ".$condition);
			$prep_state->execute();			
			$row = $prep_state->fetch(PDO::FETCH_ASSOC);			
			if($prep_state->errorInfo()[0]!=0){
				echo "Error: " .$prep_state->errorInfo()[2];
			} else {
				return $row;
			}
		} 
		
		function mysqlQuery($sql){
			$prep_state = $this->db_conn->prepare($sql);
			$prep_state->execute();
			if($prep_state->errorInfo()[0]!=0){
				echo "Error: " .$prep_state->errorInfo()[2];
			} else {
				return $prep_state;
			}			
		}
		
		function close_connection(){
			/* $prep_state = $this->db_conn->prepare('KILL CONNECTION_ID()');
			$prep_state->execute();	 */	
			return false;
		}

	}
?>