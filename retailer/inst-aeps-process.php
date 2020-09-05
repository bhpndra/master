<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include('../include/connect.php'); ?>
<?php
if(isset($_SESSION['TOKEN'])){
	$url = BASE_URL."/api/users/get_token_details.php";
	$post_fields = array("token"=>$_SESSION['TOKEN']);
	$responseVT = api_curl($url,$post_fields,$headerArray);
	$resVT = json_decode($responseVT,true);
	if($resVT['ERROR_CODE']==1){
		header("location:../logout.php"); die();
	}
	if($resVT['ERROR_CODE']==0 && $resVT['DATA']['USER_TYPE']!='RETAILER'){
		header("location:../logout.php"); die();
	}
} else {
	header("location:../logout.php"); die();
}		
	//echo "select * from outlet_kyc WHERE `user_id`='" . $resVT['DATA']['USER_ID'] . "' ";
	$sel    = mysqli_query($conn, "select * from outlet_kyc WHERE `user_id`='" . $resVT['DATA']['USER_ID'] . "' ");
	$c      = mysqli_fetch_array($sel);
	$outletid      = $c["outletid"];
	$outlet_status = $c["outlet_kyc"];
	$outlet_pan    = $c["pan_no"];
	if ( $outlet_status == 1 && !empty($outlet_pan) && !empty($outletid))	{
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>AEPS - </title>
  <!-- Bootstrap Core CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
<style>
.masthead {
  min-height: 30rem;
  position: relative;
  display: table;
  width: 100%;
  height: auto;
  padding-top: 4rem;
  padding-bottom: 8rem;
  background: -webkit-gradient(linear, left top, right top, from(rgba(255, 255, 255, 0.1)), to(rgba(255, 255, 255, 0.1))), url("https://www.netpaisa.com/aeps/img/bg.jpeg");
  background: linear-gradient(90deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.1) 100%), url("https://www.netpaisa.com/aeps/img/bg.jpeg");
  background-position: center center;
  background-repeat: no-repeat;
  background-size: cover;
}
.masthead h1 {
  font-size: 2rem;
  margin: 0;
  padding: 0;
  	background: rgba(00,00,00,0.7);
	color: #fff;
	padding: 35px 30px;
	border-radius: 15px
}
@media (min-width: 992px) {
  .masthead {
    height: 100vh;
  }
  .masthead h1 {
	font-size: 4.5rem;
	background: rgba(00,00,00,0.7);
	color: #fff;
	padding: 35px 30px;
	border-radius: 15px
  }
}
.btn-primary:hover, .btn-primary:focus, .btn-primary:active {
  background-color: #155d74 !important;
  border-color: #155d74 !important;
}
.masthead img{
	background: #fff;
	padding: 15px;
	border-radius: 10px;
	width: 260px;
	margin-bottom: 15px;
}
</style>
</head>
<body id="page-top">
  <!-- Header -->
  <header class="masthead d-flex">
    <div class="container text-center my-auto">
		<img style=" width: 500px;  height: auto;" src="https://www.netpaisa.com/aeps/img/aeps-logo.png" />
      
      <br/>
	  <span id="transationBtn"></span>
    </div>
    <div class="overlay"></div>
  </header>
<?php
	$parameterList = [];
	$parameterList["ALLOWED_SERVICES"]  = "WAP,BAP,SAP";
	$parameterList["APP_ID"] = "124";
	$parameterList["BLOCKED_SERVICES"]  = "";
	$parameterList["PAN"] = $outlet_pan;
	//print_r($parameterList);
	$checkSumString = "";
	foreach($parameterList as $key => $val){
		if($checkSumString == ""){
			$checkSumString .= $key.":".$val;
		}else{
			$checkSumString .= "|".$key.":".$val;
		}
	}
	$key = "cecc22c929ac677fe257629c560d0e6d6be4ece5b8fdb365b6933754086684d4";
	$plaintext = $checkSumString;
	$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
	$iv = "1234567812345678";
	$ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
	$hmac = hash_hmac("sha256", $ciphertext_raw, $key, $as_binary=true);
	$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
	$parameterList["CHECKSUMHASH"] = $ciphertext;
?>
<script> 
	let value = encodeURIComponent(JSON.stringify(<?= json_encode($parameterList) ?>));
	var form = document.createElement("form");
	form.setAttribute('method',"POST");
	form.setAttribute('id',"formAeps");
	//form.setAttribute('target',"_blank");
	//form.setAttribute('action',"https://aeps.netpaisa.com");
	form.setAttribute('action',"https://www.rupey.in");
	//form.setAttribute('action',"http://www.google.com/");
	var input = document.createElement("input"); //input element, text	
	input.setAttribute('type',"hidden");
	input.setAttribute('name',"params");
	input.setAttribute('value',value);
	var submitButton = document.createElement("input"); //input element, Submit button
	submitButton.setAttribute('class',"btn btn-small btn-primary");
	submitButton.setAttribute('target',"_blank");
	submitButton.setAttribute('type',"submit");
	submitButton.setAttribute('value',"Continue to Transaction");
	submitButton.setAttribute('class',"btn btn-primary btn-xl js-scroll-trigger");
	form.appendChild(input);
	form.appendChild(submitButton);
	document.getElementById("transationBtn").appendChild(form);
	
	window.onload = function(){
	  document.forms['formAeps'].submit();
	}
</script>
</body>
</html>
<?php } else { ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>AEPS - </title>
  <!-- Bootstrap Core CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
<style>
.masthead {
  min-height: 30rem;
  position: relative;
  display: table;
  width: 100%;
  height: auto;
  padding-top: 4rem;
  padding-bottom: 8rem;
  background: -webkit-gradient(linear, left top, right top, from(rgba(255, 255, 255, 0.1)), to(rgba(255, 255, 255, 0.1))), url("https://www.netpaisa.com/aeps/img/bg.jpeg");
  background: linear-gradient(90deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.1) 100%), url("https://www.netpaisa.com/aeps/img/bg.jpeg");
  background-position: center center;
  background-repeat: no-repeat;
  background-size: cover;
}
.masthead h1 {
  font-size: 2rem;
  margin: 0;
  padding: 0;
  	background: rgba(00,00,00,0.7);
	color: #fff;
	padding: 35px 30px;
	border-radius: 15px
}
@media (min-width: 992px) {
  .masthead {
    height: 100vh;
  }
  .masthead h1 {
	font-size: 4.5rem;
	background: rgba(00,00,00,0.7);
	color: #fff;
	padding: 35px 30px;
	border-radius: 15px
  }
}
.btn-primary:hover, .btn-primary:focus, .btn-primary:active {
  background-color: #155d74 !important;
  border-color: #155d74 !important;
}
.masthead img{
	background: #fff;
	padding: 15px;
	border-radius: 10px;
	width: 260px;
	margin-bottom: 15px;
}
</style>
</head>
<body id="page-top">
  <!-- Header -->
  <header class="masthead d-flex">
    <div class="container text-center my-auto">
		<img style=" width: 500px;  height: auto;" src="https://www.netpaisa.com/aeps/img/aeps-logo.png" /><br/>
		<a class="btn btn-danger btn-xl js-scroll-trigger" href="dashboard">Outlet Pending - Back to Dashboard</a>
      <br/>
	  <span id="transationBtn"></span>
    </div>
    <div class="overlay"></div>
  </header>
</body>
</html>
<?php } ?>