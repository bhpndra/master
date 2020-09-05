<?php
	session_start();
	require("../../config.php");
	require("../../include/lib.php");
	require("../../api/classes/db_class.php");
	require("../../api/classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();

if(isset($_SESSION['TOKEN'])){
	$url = BASE_URL."/api/users/get_token_details.php";
	$post_fields = array("token"=>$_SESSION['TOKEN']);
	$responseVT = api_curl($url,$post_fields,$headerArray);
	$resVT = json_decode($responseVT,true);
	if($resVT['ERROR_CODE']==1){
		$helpers->errorResponse("SESSION EXPIRED!");
	}
} else {
	$helpers->errorResponse("SESSION EXPIRED!");
}
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
		
$resBal = $mysqlClass->mysqlQuery("select wallet_balance,aeps_balance,wl_virtual_balance from `add_cust` WHERE `id`='".$USER_ID."' and `wl_id`='".$WL_ID."'" )->fetch(PDO::FETCH_ASSOC);
$vb = (!empty($resBal['wl_virtual_balance']))? $resBal['wl_virtual_balance'] : '0.00';
$wb = (!empty($resBal['wallet_balance']))? $resBal['wallet_balance'] : '0.00';
$ab = (!empty($resBal['aeps_balance']))? $resBal['aeps_balance'] : '0.00';
$res= [];    
$res['ERROR_CODE'] = 0;
$res['MESSAGE'] = 'SUCCESS';
$res['BALANCE'] = array("AEPS"=>$ab,"WALLET"=>$wb,"VIRTUAL"=>$vb);
echo json_encode($res); die();

	

?>
