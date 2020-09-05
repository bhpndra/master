<?php
if(isset($_SESSION['TOKEN'])){
	$post_fields = array("token"=>$_SESSION['TOKEN']);
	if(($_SESSION['SESSION_TIME'] + 60) <= time()){
		$url = BASE_URL."/api/users/get_token_details.php";		
		$responseVT = api_curl($url,$post_fields,$headerArray);
		$resVT = json_decode($responseVT,true);
		if($resVT['ERROR_CODE']==1){
			header("location:../logout.php"); die();
		}
		$_SESSION['SESSION_TIME'] = $_SESSION['SESSION_TIME'] + 60;
	} else {
		$resVT['DATA'] = json_decode($_SESSION['TOKEN_DETAIL'],true);
	}
	if($resVT['DATA']['USER_TYPE']!='WL'){
		header("location:../logout.php"); die();
	}
} else {
	header("location:../logout.php"); die();
}
$url = BASE_URL."/api/get-software-details.php";
$responseSiteDetails = api_curl($url,$post_fields,$headerArray);
$resSTDetails = json_decode($responseSiteDetails,true);
if($resSTDetails['ERROR_CODE']==0){
	$siteDetails = $resSTDetails['DATA'];
}

	require("../api/classes/db_class.php");
	require("../api/classes/comman_class.php");
	require("../api/classes/user_class.php");
	$helpers = new Helper_class();
	$mysqlClass = new mysql_class();
	$userClass = new user_class();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title><?=($siteDetails['site_title'])? $siteDetails['site_title'] : SITE_TITLE?> | Dashboard</title>

  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/sweetalert2/sweetalert2.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/dist/css/style.css">  
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/summernote/summernote-bs4.css">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>