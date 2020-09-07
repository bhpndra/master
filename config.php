<?php
define("BASE_URL", "http://localhost/public_html/"); // example https://www.example.com/
define("SITE_TITLE", "NetPaisa"); 
define("HTTP_X_API_KEY", "skg#@!12#09"); 
define("NETPAISAPASSKEY", "snetpaisaSW"); 
define("DOMAIN_NAME", "http://".$_SERVER["SERVER_NAME"]."/public_html/");

$headerArray = array( "x-api-key: skg#@!12#09", "netpaisapasskey: snetpaisaSW" );

define("API_BASE_URL","https://www.netpaisa.com/nps/api/");


//tetetetetet
define("API_BASE_URL","https://www.netpaisa.com/nps/api/");
/* Netpaisa Recharge API Links  */
$netpaisa_get_network = API_BASE_URL."RECHARGEV2/get_network.php";
$netpaisa_circle =  API_BASE_URL."RECHARGEV2/get_circle.php";
$netpaisa_recharge_prepaid =  API_BASE_URL."RECHARGEV2/recharge.php";
$netpaisa_status_rechv1 = API_BASE_URL."RECHARGEV2/get_recharge_status.php";
$netpaisa_recharge_plan =  API_BASE_URL."recharge/get_recharge_plans_v1.php";

$netpaisa_get_network_postpaid =  API_BASE_URL."RECHARGEV1/get_network.php";  /** PostPaid **/
$netpaisa_recharge_postpaid =  API_BASE_URL."RECHARGEV1/recharge.php";  /** PostPaid **/



/* Netpaisa DMT API Links  */
$remitter_details = API_BASE_URL."DMTINSV1.1/remitter_details";
$remitter_registration = API_BASE_URL."DMTINSV1.1/remitter";
$remitter_validate = API_BASE_URL."DMTINSV1.1/remitter_validate";
$beneficiary_register = API_BASE_URL."DMTINSV1.1/beneficiary_register";
$beneficiary_resend_otp = API_BASE_URL."DMTINSV1.1/beneficiary_resend_otp";
$beneficiary_register_validate = API_BASE_URL."DMTINSV1.1/beneficiary_register_validate.php";
$account_validate = API_BASE_URL."DMTINSV1.1/account_validate";
$beneficiary_remove = API_BASE_URL."DMTINSV1.1/beneficiary_remove";
$beneficiary_remove_validate = API_BASE_URL."DMTINSV1.1/beneficiary_remove_validate";
$money_transfer = API_BASE_URL."DMTINSV1.1/transfer";
$bank_details = API_BASE_URL."DMTINSV1.1/bank_details";
$dmt_status = API_BASE_URL."DMTINSV1.1/get_status";


/* Outlet API Base URL */
$pan_otp_req_register = API_BASE_URL."aeps/outlet_registeration_otp";
$outlet_register = API_BASE_URL."aeps/registeration_outlet";
$get_kyc_document = API_BASE_URL."aeps/get_kyc_document";
$aadhar_upload_kyc_document = API_BASE_URL."aeps/aadhar_upload_kyc_document";

$aeps_bankit_registration = API_BASE_URL."aeps/yesbank_aeps_registartion.php";
$aeps_bankit_update = API_BASE_URL."aeps/yesbank_aeps_kyc_update.php";
$aeps_bankit_details = API_BASE_URL."aeps/yesbank_outlet_details.php";

$aeps_tran_status = API_BASE_URL."aeps/get_aeps_status.php";
/* BBPS API Base URL */
$get_operater_status = API_BASE_URL."BBPSV2.1/get_bbps_operator_status.php";
$get_due_bill        = API_BASE_URL."BBPSV2.1/get_due_bill.php";
$pay_bill           = API_BASE_URL."BBPSV2.1/pay_bill.php";
$bill_status           = API_BASE_URL."BBPSV2.1/get_bbps_nps_status.php";


$payout_url         = API_BASE_URL."payout/transaction";

$get_religare_proposal = API_BASE_URL."/nps/api/religare/get_religare_proposal.php";
$religare_pay_bill = API_BASE_URL."/nps/api/religare/transaction.php";
?>