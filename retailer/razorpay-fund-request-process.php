<?php
session_start(); 
include("../config.php");
include("../include/lib.php");  
include("inc/head.php"); 
include("inc/nav.php"); 
include("inc/sidebar.php"); 
require('razorpay/razorpay-php/Razorpay.php');

use Razorpay\Api\Api;

function searchForType($id, $array) {
   foreach ($array as $key => $val) {
       if ($val['type'] == $id) {
           return $key;
       } 
   }
   return null;
}

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
$payment_env = $resRasPay['payment_env'];
$settlement_type = $resRasPay['settlement_type'];
$payment_mode_details = $resRasPay['payment_mode'];
$paymentModes = json_decode($payment_mode_details,true);

$api = new Api($keyId, $keySecret);


$post = $helpers->clearSlashes($_POST); 

$r = searchForType($post['paymode'],$paymentModes); 

if(!isset($r) || $r === null){
	echo "PG mode type issue."; die();
}
$mode_config = array(
				'upi'=>["name"=>"UPI","instruments" => array(["method"=>"upi"])], 
				'wal'=>["name"=>"Wallet","instruments" => array(["method"=>"wallet"])], 
				'nb'=>["name"=>"Net Banking","instruments" => array(["method"=>"netbanking"])], 
				'dc'=>["name"=>"Debit Cards","instruments" => array(["method"=>"card","types"=> ["debit"]])], 
				'cc'=>["name"=>"Credit Cards","instruments" => array(["method"=>"card","types"=> ["credit"]])]
				);
$mode = $paymentModes[$r]['type'];
$charge_type = $paymentModes[$r]['charge_type'];
$charge_value = $paymentModes[$r]['charge_value'];

//echo "<pre>"; print_r($_POST); die();


$pg = $post['pg'];
$amount = $post['amount'];
$pamount = $post['pamount'];

//print_r($_POST);
if($pg=='RAZORPAY'){			
	if($charge_type=="FLAT"){
		$pg_charge = $charge_value;
	} else {
		$fee = $amount * $charge_value/100; 
		$pg_charge = $fee + ($fee * 18/100); //alert(parseFloat(pg_charge));
	}	
	$payableAmount = $amount + $pg_charge; 
	
} else if($pg=='Paygate'){ 			
	if($charge_type=="FLAT"){
		$pg_charge = $charge_value;
	} else {
		$fee = $amount * $charge_value/100; 
		$pg_charge = $fee + ($fee * 18/100); //alert(parseFloat(pg_charge));
	}	
	$payableAmount = $amount + $pg_charge; 
} else {	
	$amount = 0;
}

$pay_amount = round($payableAmount,3,PHP_ROUND_HALF_UP);
if(($_POST['amount'] >= $_POST['pamount']) || ($pay_amount!=round($_POST['pamount'],3,PHP_ROUND_HALF_UP)) || !is_numeric($_POST['pamount'])){
	echo "<br/> Error. ". $pay_amount . "!=". round($_POST['pamount'],3,PHP_ROUND_HALF_UP);
	die();
} 
else {
$pay_amount = round($payableAmount,2,PHP_ROUND_HALF_UP);	
	/* #################################### Paytm payment gateway code ######################################## */
	$checkSum = "";
	$paramList = array();
	
	$ORDER_ID = $USER_ID. rand(1,9).time();
	
	$CUST_ID = $USER_ID;

//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders
//
$orderData = [
    'receipt'         => $ORDER_ID,
    'amount'          => $pay_amount * 100, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$displayCurrency = 'INR';

$displayAmount = $amt = $orderData['amount'];

$resUser = $mysqlClass->mysqlQuery("SELECT name,user,email,mobile,address FROM `add_cust` where id = '".$CUST_ID."'  " )->fetch(PDO::FETCH_ASSOC);

$data = [
    "key"               => $keyId,
    "amount"            => $amt,
    "name"              => $resUser['name'],
    "description"       => $resUser['user'],
    "image"             => "https://s29.postimg.org/r6dj1g85z/daft_punk.jpg",
    "prefill"           => [
    "name"              => $resUser['name'],
    "email"             => $resUser['email'],
    "contact"           => $resUser['mobile'],
    ],
    "notes"             => [
    "address"           => $resUser['address'],
    "merchant_order_id" => "12312321",
    "shopping_order_id" => $ORDER_ID
    ],
    "theme"             => [
    "color"             => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
	"config" => [
	"display"	=> 	[
	'blocks'=> [
	'other' => $mode_config[$mode]
	],
	"sequence" => ["block.other"],
	"preferences"	=>	["show_default_blocks" => false ] // true / false
	]
	]
];

/* $data = [
    "key"               => $keyId,
    "amount"            => $amount,
    "name"              => "DJ Tiesto",
    "description"       => "Tron Legacy",
    "image"             => "https://s29.postimg.org/r6dj1g85z/daft_punk.jpg",
    "prefill"           => [
    "name"              => "Daft Punk",
    "email"             => "customer@merchant.com",
    "contact"           => "9999999999",
    ],
    "notes"             => [
    "address"           => "Hello World",
    "merchant_order_id" => "12312321",
    ],
    "theme"             => [
    "color"             => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
];  */

if ($displayCurrency !== 'INR')
{
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);

	
	$value5 = array(
			'amount'  => $amount,
			'payable_amount'  => $payableAmount,
			'status'  => 0,
			'order_id'  => $data['order_id'],
			'payment_code'  => $ORDER_ID,
			'user_id'  => $USER_ID					
		);			
	if ($mysqlClass->insertData('online_fund_request_details', $value5) ) { } else {
		echo "<strong>Error:</strong> Some thing wrong ";
		die();
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
											<div class="caption" style="text-align:center;width:100%">Online fund request Confirmation</div>
											
										</div>
										<div class="portlet-body">
											<div class="table-scrollable">
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
														<td><?=$pg_charge ?></td>
													</tr>
													
													<tr>
														<th>Total Amount:</th>
														<td><?=$pay_amount?></td>
													</tr>
													<tr>
														<td colspan="2">
														<button class="btn btn-success" id="rzp-button1">Pay with Razorpay</button>
<form action="razorpay-verify.php" method="POST" name="razorpayform">
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
  <!--<script
    src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="<?php echo $data['key']?>"
    data-amount="<?php echo $data['amount']?>"
    data-currency="INR"
    data-name="<?php echo $data['name']?>"
    data-image="<?php echo $data['image']?>"
    data-description="<?php echo $data['description']?>"
    data-prefill.name="<?php echo $data['prefill']['name']?>"
    data-prefill.email="<?php echo $data['prefill']['email']?>"
    data-prefill.contact="<?php echo $data['prefill']['contact']?>"
    data-notes.shopping_order_id="3456"
    data-order_id="<?php echo $data['order_id']?>"
    <?php if ($displayCurrency !== 'INR') { ?> data-display_amount="<?php echo $data['display_amount']?>" <?php } ?>
    <?php if ($displayCurrency !== 'INR') { ?> data-display_currency="<?php echo $data['display_currency']?>" <?php } ?>
  >
  </script>-->
  
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
  var options = <?php echo $json?>;

options.handler = function (response){
    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
    document.getElementById('razorpay_signature').value = response.razorpay_signature;
    document.razorpayform.submit();
};
/* 
// Boolean whether to show image inside a white frame. (default: true)
options.theme.image_padding = false;

options.modal = {
    ondismiss: function() {
        console.log("This code runs when the popup is closed");
    },
    // Boolean indicating whether pressing escape key 
    // should close the checkout form. (default: true)
    escape: true,
    // Boolean indicating whether clicking translucent blank
    // space outside checkout form should close the form. (default: false)
    backdropclose: false
}; */

var rzp = new Razorpay(options);  
  
document.getElementById('rzp-button1').onclick = function(e){
    rzp.open();
    e.preventDefault();
}
</script>
  <!-- Any extra fields to be submitted with the form but not sent to Razorpay -->
</form>														
														</td>
													</tr>
												</table>
												

											</div>
											<br/>

											
										</div>
									</div>
								</div>
		</div>
						
	  </div>
	</div>
  </div>
			
<?php
	}
?>

<?php include("inc/footer.php"); ?>