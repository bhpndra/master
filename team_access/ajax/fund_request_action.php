<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include("../classes/user_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	$userClass = new user_class();
	
	$post = $helpers->clearSlashes($_POST);
	$confirm_bank_refno = $mysqlClass->get_field_data('confirm_bank_refno','payment'," where confirm_bank_refno = '".$post['referenceId']."' ");
	
	if(!empty($confirm_bank_refno['confirm_bank_refno'])){
		echo 'Reference No. Duplicate'; die();
	}
	if($post['action']!="" && ($post['action']=="Cancel" || $post['action']=="Close" ))
	{	
		$dataArray = array(
					"status"=>$post['action'],
					"emp_name_id"=>"ADMIN_#".$_SESSION[_session_userid_],
					"update_time"=>date("Y-m-d h:i:s"),
					"confirm_bank_refno"=>$post['referenceId']
					);
		$mysqlClass->updateData("`payment`",$dataArray," where id = '".$post['frid']."'");
		echo 'true';		
	}
	if($post['action']!="" && $post['action']=="approveWithReferenceId" && $post['referenceId']!="")
	{	
		$dataArray = array(
					"status"=>'approve',
					"emp_name_id"=>"ADMIN_#".$_SESSION[_session_userid_],
					"update_time"=>date("Y-m-d h:i:s"),
					"confirm_bank_refno"=>$post['referenceId']
					);
		$mysqlClass->updateData("`payment`",$dataArray," where id = '".$post['frid']."'");
		echo 'true';		
	} 
	/* if($post['action']!="" && $post['action']=="approve")
	{	
		$row = $mysqlClass->get_field_data("user_id,amount","`payment`","where id = '".$post['frid']."' ");
		$userInfo = $userClass->check_user_type($row['user_id']);
		
		if($userInfo['userType']!='not_define' && !empty($userInfo['userType'])){
		
			$trans_id = $helpers->transaction_id_generator("FR");
			$adminBal = $userClass->update_admin_balance($row['amount'],'Withdrawl',$trans_id);

			if($adminBal>0){
				if($userInfo['userType']=="distributer"){
					$userBal = $userClass->update_user_balance('distributor_trans',$row['user_id'],$row['amount'],'Deposits',$trans_id);
					
				}
				if($userInfo['userType']=="retailer"){
					$userBal = $userClass->update_user_balance('retailer_trans',$row['user_id'],$row['amount'],'Deposits',$trans_id);	
					
				}
				if($userInfo['userType']=="white_label"){
					$userBal = $userClass->update_user_balance('wl_trans',$row['user_id'],$row['amount'],'Deposits',$trans_id);
					
				}
				if($userBal>0){
					$dataArray = ["status"=>"Success"];
					$mysqlClass->updateData("`payment`",$dataArray," where id = '".$post['frid']."'");
					
					$data = array(
						'employee_id' => $_SESSION[_session_userid_],
						'payment_id' => $post['frid'],
						'ip_address' => $_SERVER['SERVER_ADDR']
					);
					$mysqlClass->insertData("fund_request_accept_by", $data);
					
					echo 'true';
				}				
			}
		}
				
	}  */

?>
