<?php
class User_class {
		//private $db_conn;
		
		public function __construct()
		{
			$database = new Database();
			$db = $database->getConnection();
			$this->db_conn = $db;
		}
		function check_user_type($id){ //SELECT id, name, user, mobile, cname,
			$sql = "SELECT usertype FROM `add_cust` where id='".$id."' ";
			$prep_state = $this->db_conn->prepare($sql);
			$prep_state->execute();			
			$row = $prep_state->fetch(PDO::FETCH_ASSOC);	
			return $row;
		}
		
		function check_user_balance($id, $forUpdate = ''){
			//echo "SELECT wallet_balance FROM `add_cust` where id = '".$id."' ". $forUpdate;
			$stmt = $this->db_conn->prepare("SELECT wallet_balance FROM `add_cust` where id = '".$id."' ". $forUpdate);
			$stmt->execute();			
			if($stmt->errorInfo()[0]!=0){
				echo "Error: " .$stmt->errorInfo()[2];
				return false;
			} else {
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				return ($row['wallet_balance']>0) ? $row['wallet_balance'] : '0.00';
				
			}
		}
		
		function check_last_5_transaction($id,$userType){
			if(!empty($userType)){
				$userInfo['usertype'] = $userType;
			} else {
				$userInfo = $this->check_user_type($id);
			}
			if($userInfo['usertype']=="DISTRIBUTOR" ){				
				$stmt = $this->db_conn->prepare("SELECT * FROM `distributor_trans` where dist_id = '".$id."' ORDER BY `id`  DESC LIMIT 5");
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
			if($userInfo['usertype']=="RETIALER"){
				$stmt = $this->db_conn->prepare("SELECT * FROM `retailer_trans` where retailer_id = '".$id."' ORDER BY `id`  DESC LIMIT 5");
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
			if($userInfo['usertype']=="WL"){
				$stmt = $this->db_conn->prepare("SELECT * FROM `wl_trans` where wluser_id = '".$id."' ORDER BY `id`  DESC LIMIT 5");
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			}	
			return $rows;
		}
		function check_transaction_openingBalance($userid,$tranRowId,$userType){
			if(!empty($userType)){
				$userInfo['usertype'] = $userType;
			} else {
				$userInfo = $this->check_user_type($userid);
			}
			if($userInfo['usertype']=="DISTRIBUTOR"){				
				$stmt = $this->db_conn->prepare("SELECT balance FROM `distributor_trans` where dist_id = '".$userid."' and id < '".$tranRowId."' ORDER BY `id`  DESC LIMIT 1");
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
			}
			if($userInfo['usertype']=="RETIALER"){
				$stmt = $this->db_conn->prepare("SELECT balance FROM `retailer_trans` where retailer_id = '".$userid."' and id < '".$tranRowId."' ORDER BY `id`  DESC LIMIT 1");
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
			}
			if($userInfo['usertype']=="WL"){
				$stmt = $this->db_conn->prepare("SELECT balance FROM `wl_trans` where wluser_id = '".$userid."' and id < '".$tranRowId."' ORDER BY `id`  DESC LIMIT 1");
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
			}
			if($stmt->rowCount()>0){
				return $row;
			} else {
				return array('balance'=>"0.00");
			}
			
		}


		function update_wallet_balance_add_amount($userid,$amount){
			if (!empty($userid)) {
				$sql = "UPDATE add_cust SET wallet_balance = (wallet_balance + '$amount') WHERE `id`='" . $userid . "'";
				$prep_state = $this->db_conn->prepare($sql);
				$result = $prep_state->execute();
				if($result){
					$selqr = "SELECT wallet_balance FROM add_cust WHERE `id`='" . $userid . "'";
					$stmt = $this->db_conn->prepare($selqr);
					$stmt->execute();
					$rows = $stmt->fetch(PDO::FETCH_ASSOC);
			
					if (is_array($rows) && count($rows) >= 1) {
						return $rows['wallet_balance'];
					} else {
						return false;
					}
					
				} else{ 
					return false;
				}
			
			} else{
				return false;
			}
		}
		
		function update_wallet_balance_deduct_amount($id,$amount) {

			if (!empty($id)) {
				$sql = "UPDATE add_cust SET wallet_balance = (wallet_balance - '$amount') WHERE `id`='" . $id . "'";
				$prep_state = $this->db_conn->prepare($sql);
				$result = $prep_state->execute();
				if($result){
					$selqr = "SELECT wallet_balance FROM add_cust WHERE `id`='" . $id . "'";
					$stmt = $this->db_conn->prepare($selqr);
					$stmt->execute();
					$rows = $stmt->fetch(PDO::FETCH_ASSOC);
			
					if (is_array($rows) && count($rows) >= 1) {
						return $rows['wallet_balance'];
					} else {
						return false;
					}
					
				} else{ 
					return false;
				}
			
			} else{
				return false;
			}

		}
}
?>