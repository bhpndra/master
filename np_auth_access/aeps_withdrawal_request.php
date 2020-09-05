<?php

	// error_reporting(E_ALL);
	// ini_set('display_errors',1);

	require_once 'session_variable.php';
	require_once '../../include/connect.php';
	
	/************************************************************/
	/*              ---      Token Decode      ---              */
	/************************************************************/
	
	define('SECRET_KEY','RECHARGE-DMT-ROCK2017') ;
	define('ALGORITHM', 'HS512');
	$secretKey = base64_decode(SECRET_KEY);
	
	$data = str_replace('"','',$_POST['user_token']);    //token retrieved
	
	// Here we will transform this array into JWT:
	$jwt = JWT::decode(
	$data,      //Data to be encoded in the JWT
	$secretKey, // The signing key
	ALGORITHM 
	); 
	
	$tokenid = $jwt->jti;

	//echo "<pre>";print_r($_POST);
	
	//==================================================
	//@ add aeps account details TABLE: 'aeps_withdrawl'
	//==================================================
	if(isset($_POST['agent_id']) && isset($_POST['user_token']) && isset($_POST['withdrawal_type']) && isset($_POST['total_amount']) && !empty($tokenid)):
	
		//get form data 
		
		$agent_id    			= validateInput($_POST['agent_id']);
		$user_token  			= validateInput($_POST['user_token']);
		$withdrawl_type  		= validateInput($_POST['withdrawal_type']);
		$total_amount  			= validateInput($_POST['total_amount']);
		$transaction_id     	= "AEPSTR" . $agent_id. mt_rand();
		$aeps_withdrawl_data 	= json_decode($_POST['data'],true);
		$usertype           	= 'retailer';  //like retailer 

		$response     = array();
		$error        = "";
		$info  	      = array();
		$data         = array();
		$aeps_data    = array();
		$aeps_amount  = 0.00;
		$aeps_txnid   = array();
		
		$errMsg  = "";
		
		if( empty( $agent_id ) ):
		
			$errMsg  = "Please Enter Retailer Id";

		elseif( empty( $withdrawl_type ) ):

			$errMsg  = "Please Enter Withdrawl Type";
			
		elseif( empty( $total_amount ) ):

			$errMsg  = "Please Enter Total Amount";	
		
		elseif( empty( $tokenid) ):
		
			$errMsg  = "Token Not Valid";

		else:
		
			$errMsg ="";
		
		endif;
		
		
		
		if( empty( $errMsg ) ){

			if($withdrawl_type == 'bank'){ 
				
				$selRetailer_id = mysqli_query($conn,"select * from `aeps_withdrawl` WHERE `retailer_id`='".$agent_id."'");
        
				if ( mysqli_num_rows($selRetailer_id) <= 0 ) {
					$response['status']  = 0;
					$response['msg']   = "Please Add Bank Acccount.!";
					echo json_encode($response);
					die;
				}

				if( $total_amount > 7 ) { 
					
						for ( $a = 0; $a < count( $aeps_withdrawl_data ) ; $a++ ) {
							
							$txn_id = $aeps_withdrawl_data[$a]['transaction_id'];

							$txn_amnt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT transaction_id,amount,settlement FROM `aeps_info` WHERE `transaction_id`='$txn_id' LIMIT 1"));

							if($txn_amnt['settlement'] == 'SUCCESS') {

								//response
								$response['status']  = 0;
								$response['msg']   = "AEPS Transaction Already Settled!";
								$response['transaction_id'] =  $txn_amnt['transaction_id'];
								echo json_encode($response);
								die;

							}						
							
							$aeps_amount += (float) $txn_amnt['amount'];
							
							array_push($aeps_txnid, $aeps_withdrawl_data[$a]['transaction_id']);
							
						}

						
						if ( (float)$aeps_amount == (float)$total_amount ) { 
							
							$current_aeps_bal = get_aeps_balance($agent_id);  // Retailer Current Balance
							$txn_group_id 	= "GPID".generate_random_string(8);
							
							if ( (float)$current_aeps_bal >= (float)$aeps_amount ) {
								
								if ( count( $aeps_txnid ) > 0 ) {
							
									for ( $at = 0; $at < count( $aeps_txnid ); $at++ ) {
							
										$data_apes = array(
											"message"           => "WITHDRAWL",
											"settlement"        => "SUCCESS",
											"group_id"          => $txn_group_id,
											"settlement_date"   => date('Y-m-d H:i:s')
										);

										
			
										$transaction_ids = "'".$aeps_txnid[$at]."'";
										
										update_qry( 'aeps_info', $data_apes, 'transaction_id', $transaction_ids, $conn );
									
									}
									
									$aeps_bal = (float) $current_aeps_bal - (float) $aeps_amount;  // After Withdrawl AEPS Balance
									
									$data_add_cust = array( 
										"aeps_balance" => $aeps_bal
									);

									$closing_balance = $aeps_bal;
									
									update_qry( 'add_cust', $data_add_cust, 'id', $agent_id , $conn );
									
									$aeps_amount = $aeps_amount - 7;
									
									$data_pay_money_info = array( 
										"transaction_id"    => "PM".time().strtoupper(generate_random_string(8)),
										"withdrawl_type"    => strtoupper($withdrawl_type),
										"user_id"           => $agent_id,
										"amount"            => (float) $aeps_amount,
										"status"            => "PENDING",
										"opening_balance"	=> $current_aeps_bal,
										"closing_balance"	=> $closing_balance,
										"group_id"          => $txn_group_id,
										"payment_date"      => date('Y-m-d H:i:s'),
										"settlement_txn_id" => $transaction_id,
										"settlement_date"   => date('Y-m-d H:i:s')
									);
									
									insert('aeps_withdrawl_info', $data_pay_money_info, $conn );	
									
									//response
									$response['status']  = 1;
									$response['opening_balance']  = $current_aeps_bal;
									$response['closing_balance']  = $closing_balance;
									$response['msg']   = "AEPS Amount Withdrawled By Bank!";	
									
								} else {
									
									//response
									$response['status']  = 0;
									$response['msg']   = "AEPS Amount Not Settled!";									
									
								}
			
							} else {

								//response
								$response['status']  = 0;
								$response['msg']   = "AEPS Amount Not Sufficient!";
									
							}
			
						} else {
			
							//response
							$response['status']  = 0;
							$response['msg']   = "AEPS Unmatched Amount!";							
							
						}
						
				} else {
					//response
					$response['status']  = 0;
					$response['msg']   = "Bank Transaction Failed!";	
				}
				
				
			} else {

				$admin_id      = 1;
				
				$admin_balance = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `admin_trans` WHERE `admin_id`='$admin_id' ORDER BY `admin_trans`.`trans_id` DESC LIMIT 1"));
				
				$superadmin_bal     = $admin_balance['balance'];
				$credit_user_id     = $admin_balance['ret_dest_wl_admin_id'];
				$transaction_time   = strtotime($admin_balance['date_created']);
				$current_time       = time();
				$transaction_gap    = $current_time - $transaction_time;
						
				for ( $a = 0; $a < count( $aeps_withdrawl_data ) ; $a++ ) {	
					
					$txn_id = $aeps_withdrawl_data[$a]['transaction_id'];

					$txn_amnt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT transaction_id,amount,settlement FROM `aeps_info` WHERE `transaction_id`='$txn_id' LIMIT 1"));

					if($txn_amnt['settlement'] == 'SUCCESS'){

						//response
						$response['status']  = 0;
						$response['msg']   = "AEPS Transaction Already Settled!";
						$response['transaction_id'] =  $txn_amnt['transaction_id'];
						echo json_encode($response);
						die;

					}
					
					$aeps_amount += (float) $txn_amnt['amount'];
					
					array_push($aeps_txnid, $aeps_withdrawl_data[$a]['transaction_id']);
					
				}
				
				//echo "<pre>"; print_r($aeps_txnid);				
					
				if ( (float)$aeps_amount == (float)$total_amount ) {
					
					$current_aeps_bal = get_aeps_balance($agent_id);  // Retailer Current Balance
					$txn_group_id 	= "GPID".generate_random_string(8);
						
					if ( (float)$current_aeps_bal >= (float)$aeps_amount ) {
							
							if ( count( $aeps_txnid ) > 0 ) {

								$sql_retailer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `retailer_trans` WHERE `retailer_id`='$agent_id' ORDER BY `id` DESC LIMIT 1"));
				
								//check avalbl bal
								if ( $sql_retailer['balance'] ) {
									//update balance
									$nretbalance = $sql_retailer['balance'] + $aeps_amount;
									
								} else {
									$nretbalance = $aeps_amount;
									
								}
					
								$current_balance = $nretbalance; 
								//@@generater unique transaction ID
								$transaction_id = "AEPSTR" . $agent_id. mt_rand();
								
								$value = array(
									'ret_dest_wl_admin_id'  => $agent_id,
									'user_type'             => $usertype,
									'deposits'              => $aeps_amount,
									'balance'               => $nretbalance,
									'date_created'          => date('Y-m-d H:i:s'),
									'created_by'            => 1,
									'creator_type'          => 'admin',
									'transaction_id'        => $transaction_id,
									'comments'              => 'AEPS Amount Withdrawled By Wallet',
									'tr_type'               => "AEPS Balance Settlement!",
									'retailer_id'           => $agent_id
								);

								// echo "<pre>"; print_r($aeps_txnid);
								
							$last_tran = insert('retailer_trans',$value, $conn);
								
							if( $last_tran ){

									update_agent_wallet_balance_on_comm($agent_id,$aeps_amount);
									
									$deduct_balance = $superadmin_bal - $aeps_amount;
					
									$values_admin = array(
										'ret_dest_wl_admin_id'          => $agent_id,
										'user_type'                     => $usertype,
										'withdrawl'                     => $aeps_amount,
										'balance'                       => $deduct_balance,
										'date_created'                  => date('Y-m-d H:i:s'),
										'created_by'                    => 1,
										'creator_type'                  => 'admin',
										'transaction_id'                => $transaction_id,
										'comments'                      => "AEPS Settled By Retailer",
										'tr_type'                       => "AEPS Balance Settlement!",
										'admin_id'                      => 1
									);

									//echo "<pre>";print_r($values_admin);
									
									$last_admin_tran = insert('admin_trans',$values_admin, $conn); 
					
									for ( $at = 0; $at < count( $aeps_txnid ); $at++ ) {
							
										$data_apes = array(
											"message"           => "WITHDRAWL",
											"settlement"        => "SUCCESS",
											"group_id"          => $txn_group_id,
											"settlement_date"   => date('Y-m-d H:i:s')
										);

										//echo "<pre>";print_r($values_admin);
			
										$transaction_id_aeps = "'".$aeps_txnid[$at]."'";
			
										update_qry( 'aeps_info', $data_apes, 'transaction_id', $transaction_id_aeps, $conn );
									
									}
									
									
									$aeps_bal = (float) $current_aeps_bal - (float) $aeps_amount;  // After Withdrawl AEPS Balance
									
									$data_add_cust = array( 
										"aeps_balance" => $aeps_bal
									);
									
									update_qry( 'add_cust', $data_add_cust, 'id', $agent_id , $conn );

									$closing_balance = $aeps_bal;
									
									$data_pay_money_info = array( 
										"transaction_id"    => "PM".time().strtoupper(generate_random_string(8)),
										"withdrawl_type"    => strtoupper($withdrawl_type),
										"user_id"           => $agent_id,
										"amount"            => (float) $aeps_amount,
										"status"            => "APPROVED",
										"opening_balance"	=> $current_aeps_bal,
										"closing_balance"	=> $closing_balance,
										"group_id"          => $txn_group_id,
										"payment_date"      => date('Y-m-d H:i:s'),
										"settlement_txn_id" => $transaction_id,
										"settlement_date"   => date('Y-m-d H:i:s')
									);
									
									insert('aeps_withdrawl_info', $data_pay_money_info, $conn );
									
									
									
									//response
									$response['status']  = 1;
									$response['opening_balance']  = $current_aeps_bal;
									$response['closing_balance']  = $closing_balance;
									$response['msg']   = "AEPS Amount Withdrawled By Wallet!";	
								
		
							} else {
								
								//response
								$response['status']  = 0;
								$response['msg']   = "AEPS Amount Not Settled!";
								
								
							}
		
						} else {

								//response
								$response['status']  = 0;
								$response['msg']   = "AEPS Amount Not Sufficient!";
								
						}
	
					} else {

						//response
						$response['status']  = 0;
						$response['msg']   = "AEPS Amount Not Sufficient!";
						
						
					}
				}
				else{
						//response
						$response['status']  = 0;
						$response['msg']   = "AEPS Unmatched Amount!!";
				}
				
			}
		
		} else {

			$response['status']  = 0;
			$response['msg']     = "Error: ".$errMsg;
			echo json_encode($response);
			die();

		}
		
		echo json_encode($response);
		die();

	else:
		
		$response['status']  = 0;
		$response['msg']     = "Error: Invalid Params";
		echo json_encode($response);
		die();
		
	
	endif;
	
?>