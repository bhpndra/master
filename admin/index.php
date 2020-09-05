<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php
if(isset($_SESSION['TOKEN'])){
	$url = BASE_URL."/api/users/check_token.php";
	$post_fields = array("token"=>$_SESSION['TOKEN']);
	$responseVT = api_curl($url,$post_fields,$headerArray);
	$resVT = json_decode($responseVT,true);
	if($resVT['ERROR_CODE']==1){
		header("location:../logout.php"); die();
	} else {
		header("location:dashboard"); die();
	}
} else {
	header("location:../logout.php"); die();
}
?>
