<?php
session_start(); 
include("../config.php");
include("../include/lib.php");  
include("inc/head.php"); 
include("inc/nav.php"); 
include("inc/sidebar.php");

require("../api/classes/db_class.php");
require("../api/classes/comman_class.php");
require("../api/classes/user_class.php");
$helpers = new Helper_class();
$mysqlClass = new mysql_class();
$userClass = new user_class();

$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];

$resRasPay = $mysqlClass->fetchRow(" wl_payment_gateway ", " * ", " WHERE wl_id='$WL_ID' AND payment_gateway_name='RAZORPAY'");
if(empty($resRasPay)){
	echo "PG configration issue."; die();
}

$keyId = $resRasPay['merchant_id'];
$keySecret = $resRasPay['merchant_key'];
$settlement_type = $resRasPay['settlement_type'];

require('razorpay/razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

$error = "Payment Failed";

if (empty($_POST['razorpay_payment_id']) === false)
{
$post = $helpers->clearSlashes($_POST);	

    $api = new Api($keyId, $keySecret);

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $post['razorpay_payment_id'],
            'razorpay_signature' => $post['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}


$razorpayOrder = $api->order->fetch($_SESSION['razorpay_order_id']);

if(isset($razorpayOrder->id) && $razorpayOrder->status =="paid" ){
		$payment_code = addslashes($razorpayOrder->receipt);	
		$razorpay_payment_id = addslashes($razorpayOrder->id);	
		$getUserId = $mysqlClass->fetchRow(" `online_fund_request_details` ", " user_id,amount,payable_amount ", "  where payment_code = '".$payment_code."' and status = 0 " );
		if(!empty($getUserId)){
			$userid = $getUserId['user_id'];
			$amount = $getUserId['amount'];
			$payable_amount = $getUserId['payable_amount'];
		}
				
				$value = array(
					'status' => 1
				);
				if($mysqlClass->updateData('online_fund_request_details', $value,   " where payment_code = '".$payment_code."'" )){			
					$value5 = array(
							'response' => json_encode((array)$razorpayOrder),					
							'order_id' => $payment_code					
						);
					if(isset($userid) && $userid > 0 && $userid == $USER_ID){
						if ( $mysqlClass->insertData('payment_gateway_log', $value5) ) {
							
							if($settlement_type=="INSTANT"){
								$transaction_id = $helpers->transaction_id_generator("CR",4);
								
									//@@@get retailer available balance@@@
									$mysqlClass->mysqlQuery("START TRANSACTION");
									
									$rt_avlVbal = $userClass->check_user_balance($USER_ID," FOR UPDATE");
									$rt_closingBal = $userClass->update_wallet_balance_add_amount($USER_ID,$amount);
									
									$dataValueRt = array(
													'ret_dest_wl_admin_id' 		=> $USER_ID,
													'transaction_id' 			=> $payment_code,
													'agent_trid' 				=> $transaction_id,
													'opening_balance' 			=> $rt_avlVbal,
													'deposits' 					=> $amount,
													'withdrawl' 				=> 0,
													'balance' 					=> $rt_closingBal,
													'date_created' 				=> date("Y-m-d H:i:s"),
													'created_by' 				=> $USER_ID,
													'comments' 					=> 'Wallet Refill with RAZORPAY',
													'tr_type' 					=> 'CR',
													'retailer_id' 				=> $USER_ID
													);
									
									$lastid = $mysqlClass->insertData(" retailer_trans ", $dataValueRt);
									
									$mysqlClass->mysqlQuery("COMMIT");	
								} 
							else {
									$dataValueRt = array(
													'user_id' 					=> $USER_ID,
													'transaction_id' 			=> $payment_code,
													'amount' 					=> $amount,
													'payment_date' 				=> date("Y-m-d H:i:s"),
													'user_type' 				=> 'RETAILER',
													'admin_id' 					=> $ADMIN_ID
													);
									
									$lastid = $mysqlClass->insertData(" online_payment_request ", $dataValueRt);
								}
							}
						} else {
							
							$getUserId = $mysqlClass->fetchRow(" `online_fund_request_details` ", " user_id,amount,payable_amount FROM "," where payment_code = '".$payment_code."' and status = 1 " );
							$amount = $getUserId['amount'];
							$payable_amount = $getUserId['payable_amount'];
						}
					}
}
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

		<div class="row justify-content-center">
								<div class="col-md-5 col-md-offset-4">
									<!-- BEGIN SAMPLE FORM PORTLET-->
									
									<!-- END SAMPLE FORM PORTLET-->
									
									
									
									<!-- BEGIN SAMPLE TABLE PORTLET-->
									<div class="portlet box blue">
										<div class="portlet-title">
											<div class="caption" style="text-align:center;width:100%">Online fund request Status</div>
											
										</div>
										<div class="portlet-body">
											<div class="table-scrollable">
<?php
	if ($success === true){
?>
												<table class="table table-bordered">
													<tr>
														<th>Receipt No:</th>
														<td><?=$payment_code?></td>
													</tr>
													<tr>
														<th>Amount:</th>
														<td><?=$amount?></td>
													</tr>
													<tr>
														<th>Total Amount:</th>
														<td><?=$payable_amount?></td>
													</tr>
													<tr>
														<th>Status:</th>
														<td><?=strtoupper($razorpayOrder->status)?></td>
													</tr>
												</table>
	<?php } else {  /* ?>
												<table class="table table-bordered">
													<tr>
														<th>Receipt No:</th>
														<td><?=$ORDER_ID?></td>
													</tr>
													<tr>
														<th>Amount:</th>
														<td><?=$_POST['amount']?></td>
													</tr>
													<tr>
														<th>Charge:</th>
														<td><?=$pg_charge + ($pg_charge * 18/100)?></td>
													</tr>
													
													<tr>
														<th>Total Amount:</th>
														<td><?=$pay_amount?></td>
													</tr>
												</table>
	<?php */ } ?>	

											</div>
											<br/>

											
										</div>
									</div>
								</div>
							</div>
						
	  </div>
	</div>
  </div>
  
<?php include("inc/footer.php"); ?>