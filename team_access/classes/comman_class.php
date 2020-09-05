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
		 
		 function fileUpload($FILES,$path,$imgPrifix){
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

				if (! file_exists($FILES["tmp_name"])) {
					$response = array(
						"type" => "error",
						"message" => "Choose image file to upload."
					);
				} else if (! in_array($file_extension, $allowed_image_extension)) {
					$response = array(
						"type" => "error",
						"message" => "Upload valiid images. Only PNG and JPEG are allowed."
					);
					echo $result;
				} else {
					$fileName = $imgPrifix."_".time() ."_". basename($FILES["name"]);
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
			$encrypted_rand_access_key = md5(sha1($salt.$chars));
			return $encrypted_rand_access_key;
		}
		
		function transaction_id_generator($prifix,$length){
			$l = empty($length)? 8 : $length;
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
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
		
		function send_msg($mobile,$msg){
			$sender_id = "NETPAI";
			$authkey = "206718AZOObhQBa5abdd115";

			$curl = curl_init();

			curl_setopt_array($curl, array(
				// CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?sender=$sender_id&route=4&mobiles=$mobile&authkey=$authkey&country=0&message=$message",
				CURLOPT_URL => "http://control.msg91.com/api/sendhttp.php?authkey=$authkey&mobiles=$mobile&message=$msg&sender=$sender_id&route=4",
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
		function valueSetForInsert($a){
			return "'".$a."'";
		}
		function insertData($table,$dataArray){
			
			$fields = array_keys($dataArray);
			$values = array_map(array($this,'valueSetForInsert'),array_values($dataArray));
			
			$sql = "INSERT INTO ".$table." (".implode(",",$fields).") values(".implode(",",$values).")";
			$stmt= $this->db_conn->prepare($sql);
			$stmt->execute();
			if($stmt->errorInfo()[0]!=0){
				echo "Error: " .$stmt->errorInfo()[2];
			} else {
				return $this->db_conn->lastInsertId();
			}
		}
		
		// Update row
		function valueSetForUpdate($k,$v){
			return $k." = '".$v."'";
		}
		function updateData($table,$dataArray,$condition){
			//$this->db_conn->setAttribute(array(PDO::MYSQL_ATTR_FOUND_ROWS=>TRUE)); 
			$fields = array_keys($dataArray);
			$values = array_values($dataArray);
			$data = array_map(array($this,'valueSetForUpdate'),$fields,$values);
			
			$sql = "UPDATE ".$table." set " .implode(",",$data) ." ".$condition;
			$stmt= $this->db_conn->prepare($sql);
			$stmt->execute();
			if($stmt->errorInfo()[0]!=0){
				echo "Error: " .$stmt->errorInfo()[2];
			} else {
				return $stmt->rowCount();
			}
		}
				
			
		function countRows($sql){
			$prep_state = $this->db_conn->prepare($sql);
			$prep_state->execute();
			$num = $prep_state->rowCount();
			return $num;
		}
		
		function get_field_data($fieldName,$tableName,$condition){
			$prep_state = $this->db_conn->prepare("SELECT ".$fieldName." FROM ".$tableName." ".$condition);
			$prep_state->execute();			
			$row = $prep_state->fetch(PDO::FETCH_ASSOC);			
			if($prep_state->errorInfo()[0]!=0){
				echo "Error: " .$prep_state->errorInfo()[2];
			} else {
				return $row;
			}
		}

		
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
		
		function mysqlQuery($sql){
			$prep_state = $this->db_conn->prepare($sql);
			$prep_state->execute();
			if($prep_state->errorInfo()[0]!=0){
				echo "Error: " .$prep_state->errorInfo()[2];
			} else {
				return $prep_state;
			}			
		}

	}
?>