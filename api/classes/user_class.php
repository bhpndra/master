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
			/* $sql = "SELECT 
			CASE WHEN id in (SELECT `add_distributer`.user_id FROM `add_distributer` where `add_distributer`.user_id = `add_cust`.id) THEN 'distributor'
			WHEN id in (SELECT `add_retailer`.user_id FROM `add_retailer` where `add_retailer`.user_id = `add_cust`.id) THEN 'retailer'
			WHEN id in (SELECT `add_white_label`.user_id FROM `add_white_label` where `add_white_label`.user_id = `add_cust`.id) THEN 'wl'
			else 'not_define'
			end as userType		
			FROM `add_cust` where id='".$id."' "; */
			$sql = "SELECT usertype	FROM `add_cust` where id='".$id."' ";
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
				
		function check_last_5_transaction($id){			
			$stmt = $this->db_conn->prepare("SELECT * FROM `retailer_trans` where retailer_id = '".$id."' ORDER BY `id`  DESC LIMIT 5");
			$stmt->execute();
			if($stmt->errorInfo()[0]!=0){
				echo "Error: " .$stmt->errorInfo()[2];
				return false;
			} else {
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $rows;
			}
		}

		
		function check_user_aeps_wallet_balance($id,  $forUpdate = ''){
			$stmt = $this->db_conn->prepare("SELECT aeps_balance FROM `add_cust` where id = '".$id."' ".$forUpdate);
			$stmt->execute();			
			if($stmt->errorInfo()[0]!=0){
				echo "Error: " .$stmt->errorInfo()[2];
				return false;
			} else {
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				return ($row['aeps_balance']>0) ? $row['aeps_balance'] : '0.00';
				
			}
		}
		
		function update_aeps_wallet_balance($userid,$amount){
			if (!empty($userid)) {
				$sql = "UPDATE add_cust SET aeps_balance = '$amount' WHERE `id`='" . $userid . "'";
				$prep_state = $this->db_conn->prepare($sql);
				$result = $prep_state->execute();
				return true;
			} else{
				return false;
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
		
		function get_sms_pack_details($userid,$packFor){
			if (!empty($userid) && !empty($packFor)) {
				if($packFor=="WL"){
					$stmt = $this->db_conn->prepare("select * from `sms_pack` WHERE `user_id`='".$userid."' and `api_for`='WL'");
					$stmt->execute();			
					if($stmt->errorInfo()[0]!=0){
						echo "Error: " .$stmt->errorInfo()[2];
						return false;
					} else {
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						return $row;
						
					}
				}
				if($packFor=="ADMIN"){
					$stmt = $this->db_conn->prepare("select * from `sms_pack` WHERE `admin_id`='".$userid."' and `api_for`='ADMIN'");
					$stmt->execute();			
					if($stmt->errorInfo()[0]!=0){
						echo "Error: " .$stmt->errorInfo()[2];
						return false;
					} else {
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						return $row;
						
					}
				}
				
			} else{
				return false;
			}
		}
		
		function commission_dt_rt($network,$packageId,$amount){
			if (!empty($network) && !empty($packageId) && !empty($amount)) {
					$stmt = $this->db_conn->prepare("SELECT `package_id`,`dt_commission`,`rt_commission`,`commission_type`,`md_commission` FROM package_commission WHERE network='".$network."' && package_id='".$packageId."'");
					$stmt->execute();
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					
					if(!empty($row['package_id'])){
						$dt_commission       = isset ( $row['dt_commission'] ) ? $row['dt_commission'] : 0;
						$rt_commission       = isset ( $row['rt_commission'] ) ? $row['rt_commission'] : 0;						
						$md_commission       = isset ( $row['md_commission'] ) ? $row['md_commission'] : 0;						
						$commission_type      = isset ( $row['commission_type'] ) ? $row['commission_type'] : '';

						if($commission_type == 'FLAT') {
							$distributor_commission        	= $dt_commission;
							$retailer_commission    		= $rt_commission;
							$master_commission    		= $md_commission;
						} else if($commission_type == 'PERCENT') {
							$distributor_commission   = round( ($amount * $dt_commission/100), 2);
							$retailer_commission   = round( ($amount * $rt_commission/100), 2);
							$master_commission   = round( ($amount * $md_commission/100), 2);
						} else {
							$distributor_commission     = 0;
							$retailer_commission    = 0;
							$master_commission    = 0;
						}
						$commission_status    = 'SET';
					}
					else {
						$distributor_commission = 0;
						$retailer_commission    = 0;
						$master_commission    = 0;
						$commission_status    = 'NOT SET';
					}

						$data = array(
								'dt_commission' => $distributor_commission,
								'rt_commission' => $retailer_commission,
								'md_commission' => $master_commission,
								'commission_status' => $commission_status
							);
						return $data;
					} else {
						return false;
					}
			}
			
		function commission_wl($network,$wl_id,$amount){
			if (!empty($network) && !empty($wl_id) && !empty($amount)) {
					$stmtWL = $this->db_conn->prepare("SELECT `package_id` FROM add_cust WHERE id='".$wl_id."' && usertype='WL'");
					$stmtWL->execute();
					$rowWL = $stmtWL->fetch(PDO::FETCH_ASSOC);
					if(empty($rowWL['package_id'])){
						return false;
					}
					
					
					$stmt = $this->db_conn->prepare("SELECT `package_id`, `wl_commission`,`commission_type` FROM wl_package_commission WHERE network='".$network."' && package_id='".$rowWL['package_id']."'");
					$stmt->execute();
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					
					if(!empty($row['package_id'])){
						$wl_commission       = isset ( $row['wl_commission'] ) ? $row['wl_commission'] : 0;					
						$commission_type      = isset ( $row['commission_type'] ) ? $row['commission_type'] : '';

						if($commission_type == 'FLAT') {
							$whitelabel_commission        	= $wl_commission;
						} else if($commission_type == 'PERCENT') {
							$whitelabel_commission   = round( ($amount * $wl_commission/100), 2);
						} else {
							$whitelabel_commission     = 0;
						}
						$commission_status    = 'SET';
					}
					else {
						$whitelabel_commission     = 0;
						$commission_status    = 'NOT SET';
					}

						$data = array(
								'wl_commission' => $whitelabel_commission,
								'commission_status' => $commission_status
							);
						return $data;
					} else {
						return false;
					}
			}	
		
			function commission_wl_dmt($network,$wl_id,$amount){
				if (!empty($network) && !empty($wl_id) && !empty($amount)) {
					$stmtWL = $this->db_conn->prepare("SELECT `package_id` FROM add_cust WHERE id='".$wl_id."' && usertype='WL'");
					$stmtWL->execute();
					$rowWL = $stmtWL->fetch(PDO::FETCH_ASSOC);
					if(empty($rowWL['package_id'])){
						return false;
					}
					
					
					$stmt = $this->db_conn->prepare("SELECT `package_id`, `wl_commission`,`commission_type` FROM wl_package_commission WHERE network like '%".$network."%' && package_id='".$rowWL['package_id']."'");
					$stmt->execute();
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					
					if(!empty($row['package_id'])){
						$wl_commission       = isset ( $row['wl_commission'] ) ? $row['wl_commission'] : 0;					
						$commission_type      = isset ( $row['commission_type'] ) ? $row['commission_type'] : '';

						if($commission_type == 'FLAT') {
							$whitelabel_commission        	= $wl_commission;
							$gst = 0;
							$tds = 0;
						} else if($commission_type == 'PERCENT') {
							/* GST and TDS Start */
							$gst = 18;
							$tds = 5;
							$commitAmount = ($amount<1000)? 1000 : $amount;
							$f48 = round(((($commitAmount/100)/1.18) - $wl_commission),3);
							$gst = round($f48*0.18,3);
							$tds = round($f48*0.05,3);
							$whitelabel_commission = round((($commitAmount/100)-$f48)+$tds,2);
							/* GST and TDS End */	
						} else {
							/* GST and TDS Start */
							$amount = 5000;
							$gst = 18;
							$tds = 5;
							$commitAmount = ($amount<1000)? 1000 : $amount;
							$f48 = round(((($commitAmount/100)/1.18) - $wl_commission),3);
							$gst = round($f48*0.18,3);
							$tds = round($f48*0.05,3);
							$whitelabel_commission = round((($commitAmount/100)-$f48)+$tds,2);
							/* GST and TDS End */
						}
						$commission_status    = 'SET';
					}
					else {
						return false;
					}

						$data = array(
								'wl_commission' => $whitelabel_commission,
								'tds' => $tds,
								'gst' => $gst,
								'commission_status' => $commission_status
							);
						return $data;
					} else {
						return false;
					}
			}
		
		function get_api_access_key($admin){
			if (!empty($admin)) {
					$stmt = $this->db_conn->prepare("SELECT api_access_key,userType FROM `admin` where id = '".$admin."'");
					$stmt->execute();			
					if($stmt->errorInfo()[0]!=0){
						echo "Error: " .$stmt->errorInfo()[2];
						return false;
					} else {
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						return (!empty($row)) ? $row : false;
						
					}
			} else {
				return false;
			}
		}
		
		function get_dmt_slab($wl_id,$amount){
			if (!empty($wl_id)) {
					$network = 'NIR';
					$stmt = $this->db_conn->prepare("SELECT amount_from,amount_to,slab_network FROM `dmt_slabs` where wl_user_id = '".$wl_id."'");
					$stmt->execute();			
					if($stmt->errorInfo()[0]!=0){
						echo "Error: " .$stmt->errorInfo()[2];
						return false;
					} else {
						$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
						if(count($row) > 0){
							foreach($row as $k=>$v){
								if($amount >= $v['amount_from'] && $amount <= $v['amount_to']){
									$network = $v['slab_network'];
									break;
								}
							}
							return $network;
						}
						
					}
			} else {
				return false;
			}
		}
		
		
		function get_aeps_slab($wl_id,$amount){
			if (!empty($wl_id)) {
					$network = 'NIR';
					$stmt = $this->db_conn->prepare("SELECT amount_from,amount_to,slab_network FROM `aeps_slabs` where wl_user_id = '".$wl_id."'");
					$stmt->execute();			
					if($stmt->errorInfo()[0]!=0){
						echo "Error: " .$stmt->errorInfo()[2];
						return false;
					} else {
						$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
						if(count($row) > 0){
							foreach($row as $k=>$v){
								if($amount >= $v['amount_from'] && $amount <= $v['amount_to']){
									$network = $v['slab_network'];
									break;
								}
							}
							return $network;
						}
						
					}
			} else {
				return false;
			}
		}
}
?>