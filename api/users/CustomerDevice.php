<?php
/**********************************************************
 Controller Name : CustomerOtp Controller Class

***********************************************************/
class CustomerDevice extends Database{

	public function __construct(){
		parent::getConnection();
	}
	
/** Insert otp*/	
  function saveOtp($table, $data){
	$query="INSERT INTO ".$table." SET ";
	foreach($data as $key=>$val){
	$query .= $key."='".$val."', ";
	}
	$query .=" created_at = NOW()"; 
	// echo $query; exit; 
	$stmt = $this->db_conn->prepare($query);
	$stmt->execute();
	$id = $this->db_conn->lastInsertId();
	return $id; 
  } 

  /*** Update otp flags after success **/
    function update($table, $data,  $user_id, $otp_id=''){
	$query="UPDATE ".$table." SET ";
	foreach($data as $key=>$val){
	$query .= $key."='".$val."', ";
	}
	if($otp_id !=''){
     $query .=" updated_at = NOW() WHERE user_id='".$user_id."' AND id='".$otp_id."'";  
	}else{
	 $query .=" updated_at = NOW() WHERE user_id='".$user_id."'";
	}
	
	$stmt = $this->db_conn->prepare($query);
	$stmt->execute();
	$id = $user_id;
	return $id; 
  }

# Check user OTP
 function checkUserOtp($table, $mobile, $otp){
 	//echo "current time";
 	date_default_timezone_set('Asia/Kolkata');
 	$time1 = date('i', strtotime(date("Y-m-d H:i:s"))); 
 	$query = " SELECT user_id, created_at FROM {$table} WHERE mobile_number='".$mobile."' AND otp='".$otp."' AND is_invoked=0 AND is_expired=0 ORDER BY id DESC LIMIT 0, 1";
 	$stmt = $this->db_conn->prepare($query);
 	$stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty($result)){	
    $time2 = date('i', strtotime($result['created_at'])); 
    $time =  $time1 - $time2;
    if($time < 15){
    $flag = $result['user_id'];	
    }
    else{
    $flag = 0;
    }
    }else{
    $flag = 0;	
    }
    return $flag;
 }

 Function getOtpCreationTime($table, $mobile, $otp){
 	$query = " SELECT created_at FROM {$table} WHERE mobile_number='".$mobile."' AND otp='".$otp."' AND is_invoked=0 AND is_expired=0 ORDER BY id DESC LIMIT 1, 1";
 	$stmt = $this->db_conn->prepare($query);
 	$stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty($result)){
     $dateTime = $result['user_id'];
    }else{
    $dateTime = 0;	
    }
    return $dateTime;
 }

# Get second last id of OTP
 function getSecondLastIdofOTP($table, $user_id){
 	$query = " SELECT id FROM {$table} WHERE user_id='".$user_id."' AND is_invoked=0 AND is_expired=0 ORDER BY id DESC LIMIT 1, 1";
 	$stmt = $this->db_conn->prepare($query);
 	$stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty($result)){
     $flag = $result['id'];
    }else{
    $flag = 0;	
    }
    return $flag;
 }
 
 function WrongAttempt($user_id){
	 
	 error_reporting(E_ALL);
	 ini_set('display_errors',1);
	 
	$query = " SELECT * FROM LoginAttempts WHERE user_id='".$user_id."'  ORDER BY id DESC LIMIT 1";
	
	
 	$stmt = $this->db_conn->prepare($query);
 	$stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
	
    //if(!empty($result)){
	if($stmt->rowCount() > 0) {
		
		
		$rsp = $this->CheckIfWrongAttemptIsMoreThan3($user_id);
		
		if($rsp===false){
			return false;
		}else{
		$attempts = $result["Attempts"]+1;
		$query="UPDATE LoginAttempts SET Attempts='".$attempts."',LastLogin=now() where user_id='".$user_id."' ";				
		$stmt = $this->db_conn->prepare($query);
		$stmt->execute();
		}
	  
    }else{
		
        $query="INSERT INTO LoginAttempts SET user_id='".$user_id."',Attempts=1,LastLogin=now()    ";
		$stmt = $this->db_conn->prepare($query);
		
		$stmt->execute();
		$id = $this->db_conn->lastInsertId();
		return $id; 	
	
	
    }
	 
	
	 
 }
 
 function CheckIfWrongAttemptIsMoreThan3($user_id){
	
	 $query = " SELECT * FROM LoginAttempts WHERE user_id='".$user_id."'  ORDER BY id DESC LIMIT 1";
 	$stmt = $this->db_conn->prepare($query);
 	$stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
 
	if($stmt->rowCount() > 0) {
		
		if($result["Attempts"]>=3){
			
			
			$query="UPDATE add_cust SET status='DISABLED' where id='".$user_id."' ";				
			$stmt = $this->db_conn->prepare($query);
			$stmt->execute();
				
			
			$rsp=false;
		}else{
			$rsp=true;
		}
		
	}else{
		$rsp=false;
	}
	 return $rsp;
 }
 
 
 function UpdateDeviceId($device_id,$user_id){
	 
	$query = " SELECT * FROM installed_app WHERE device_id='".$device_id."'  ORDER BY id DESC LIMIT 1";
 	$stmt = $this->db_conn->prepare($query);
 	$stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
 
	if($stmt->rowCount() > 0) {
		
		$query="UPDATE installed_app SET user_id='$user_id' where device_id='".$device_id."' ";				
		$stmt = $this->db_conn->prepare($query);
		$stmt->execute();
	}
	 
 }
 
 function CheckIfDeviceExist($device_id,$user_id){
	 
	$query = " SELECT * FROM installed_app WHERE user_id='".$user_id."' and status=1  ORDER BY id DESC LIMIT 1";
 	$stmt = $this->db_conn->prepare($query);
 	$stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
 
	if($stmt->rowCount() == 1) {
		
		return false;
	}else{
		return true;
	}
	 
 }
 
 
}


?>