<?php
/**********************************************************
 Controller Name : CustomerOtp Controller Class
 Developer Name : Prabhat Kumar Tiwari

***********************************************************/
class CustomerOtp extends Database{

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
    function update($table, $data,  $user_id, $otp_id=null){
		
		//echo $table."-".$data."-".$user_id."-".$otp_id;exit;
		
		
	$query="UPDATE ".$table." SET ";
	foreach($data as $key=>$val){
	$query .= $key."='".$val."', ";
	}
	if( !empty($otp_id) ){
     $query .=" updated_at = NOW() WHERE user_id='".$user_id."' AND id='".$otp_id."'";  
	}else{
	 $query .=" updated_at = NOW() WHERE user_id='".$user_id."'";
	}
	//echo $query;exit;
	$stmt = $this->db_conn->prepare($query);
	$stmt->execute();
	$id = $user_id;
	return $id; 
  }

# Check user OTP
 function checkUserOtp($table, $mobile, $otp , $user_ip=null ){
 	//echo "current time";
 	date_default_timezone_set('Asia/Kolkata');
 	
 	$query = " SELECT id, user_id, created_at,otp,user_ip FROM {$table} WHERE mobile_number='".$mobile."' AND user_ip='".$user_ip."'   AND is_invoked=0 AND is_expired=0 ORDER BY id DESC LIMIT 1";
 	$stmt = $this->db_conn->prepare($query);
 	$stmt->execute();
	//echo $query;exit;
	
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty($result)){	
    
	
		$d1=$result['created_at'];
		$d2=date("Y-m-d H:i:s");
		
		$ResultOtp=$result['otp'];
		
		if($ResultOtp==$otp){
		
		$datetime1 = strtotime($d1);
		$datetime2 = strtotime($d2);
		$interval  = abs($datetime2 - $datetime1);
		$minutes   = round($interval / 60);
		//echo $d1."-hh-".$d2."=".$minutes;die;
		
		if( $minutes < 15){
				//echo "$mn";
			$flag = $result['id'];	
			
		}
		else{
				
				$updateData = array(
					"is_invoked"=>0,
					"is_expired"=>1,
					"is_expired_at"=>date("Y-m-d H:i:s")
				);	

            $this->update('customer_otps', $updateData ,$result['user_id'], $result['id']);
			 $flag = 0;
		}
	}else{
		$flag=0;
	}
	}else{
		$flag = 0;	
	}
		//var_dump($flag);exit;
		return (int)$flag;
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
 function getSecondLastIdofOTP($table, $user_id,$LastId=null){
 	$query = " SELECT id FROM {$table} WHERE user_id='".$user_id."' AND is_invoked=0 AND is_expired=0  and id!='".$LastId."'  ORDER BY id DESC LIMIT 1";
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
 
 
 function IsLastOtpIsActive( $user_id, $user_ip ){
	 
	 
	$response = false; 
	$query = " SELECT id,is_invoked,is_expired,created_at,user_ip FROM customer_otps WHERE user_id='".$user_id."' and user_ip='".$user_ip."' ORDER BY id DESC LIMIT 1";
//echo $localIP = getHostByName(getHostName());
//exit;
 //	echo "SELECT id,is_invoked,is_expired,created_at FROM customer_otps WHERE user_id='".$user_id."'  and user_ip='".$user_ip."' ORDER BY id DESC LIMIT 1";exit;
	$stmt = $this->db_conn->prepare($query);
 	$stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
	if($stmt->rowCount() > 0) {
		
		$is_expired = $result['is_expired'];
		$RspUser_ip = $result['user_ip'];
		
		if( empty($is_expired)  ){
			
			$d1=$result['created_at'];
			$d2=date("Y-m-d H:i:s");		
		
			$datetime1 = strtotime($d1);
			$datetime2 = strtotime($d2);
			$interval  = abs($datetime2 - $datetime1);
			$minutes   = round($interval / 60);
			//echo $d1."-hh-".$d2."=".$minutes;die;
			
			if( $minutes < 15){
					
				$response = true;	
				
			}
			
		}		
	}
	//var_dump($response);exit;
	return $response;
 }
 
 
 
 
 # Wrong Attempts
 
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
 
 
 
}


?>