<?php
/* $host = explode(".",$_SERVER['HTTP_HOST']);
	if($_SERVER['HTTP_HOST'] != "dmt.netpaisa.com"){
		if(preg_match('/www/', $_SERVER['HTTP_HOST'])){
		}
		else{
			if(count($host) > 2){ 
				if(in_array('co',$host) || (in_array('in',$host) || in_array('net',$host))){ 
					header("location:https://www.".$_SERVER['HTTP_HOST']);
				}
			} else {
				header("location:https://www.".$_SERVER['HTTP_HOST']);
			}
		}
		
	} */
	$host = explode(".",$_SERVER['HTTP_HOST']);	
	/*if(preg_match('/www/', $_SERVER['HTTP_HOST'])){
		header("location:https://". str_replace("www.","",$_SERVER['HTTP_HOST']) );
	} else {
	
	}
	*/
	
	if(preg_match('/www/', $_SERVER['HTTP_HOST'])){
		header("location:https://". str_replace("www.","",$_SERVER['HTTP_HOST']) );
	} else {
	
	}
	
	$app_download = "";
	session_start();
	require_once "config.php";
	require_once "include/connect.php";
	/**
	 * *****************************************************
	 * ******* Domain Verify for White Label *******
	 * ******************************************************
	 */
	$selqr = "SELECT * FROM `add_white_label`";
	$rows = mysqli_query($conn, $selqr);
	while ($data = mysqli_fetch_array($rows)) {
		$wl_domain[] = $data['domain'];
	}
	//print_r($wl_domain);
	$server_name = "http://" . $_SERVER['SERVER_NAME']."/public_html/"; //die();
	//$server_name = "http://" . $_SERVER['SERVER_NAME']."/netpaisa_new/"; //die();
	if (! in_array($server_name, $wl_domain)) {
		echo '<script>window.location.href="404.html"</script>';
		die();
	}
	
	/****************************
	 * get user id (white label)
	 * ***************************/
	$userqr = mysqli_query($conn, "SELECT * FROM `add_white_label` WHERE `domain`='$server_name'");
	if (mysqli_num_rows($userqr) > 0) {
		$users = mysqli_fetch_array($userqr);
		$wl_id = $users['user_id'];
	}
	/*********************************
	 * general settings
	 * *********************************/
	$generalqr = mysqli_query($conn, "SELECT * FROM `general_settings` WHERE `user_id`='" . $wl_id . "' && `user_type`='WL'");
	if (mysqli_num_rows($generalqr) > 0) {
		$gene_info = mysqli_fetch_array($generalqr);
		
		$site_title   = $gene_info['site_title'];
		$site_name   = $gene_info['site_name'];
		$copyright    = $gene_info['copyright'];
		$meta_desc    = $gene_info['meta_desc'];
		$logo         = $gene_info['logo'];
		$supportEmail = $gene_info['email'];
		$app_link	  = empty($gene_info['app_link']) ?  '#' : $gene_info['app_link'];
		$colorCode	  = isset($gene_info['color_code']) ? $gene_info['color_code'] : '#0f6fd5';
		$template	  = isset($gene_info['template']) ? $gene_info['template'] : 1;
		$email	  = isset($gene_info['email']) ? $gene_info['email'] : 1;
		$support_number	  = isset($gene_info['support_number']) ? $gene_info['support_number'] : 1;
		$address	  = isset($gene_info['address']) ? $gene_info['address'] : 'C-276, Noida';
	}
	/*********************************************
	 * get white label user email and contact no
	 * ********************************************/
	 
	
?>
<head>
    <meta charset="UTF-8"/>
    <title><?=$site_title?></title>
    <!-- mobile responsive meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- depdency stylesheet -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,300i,400,400i,600,600i,700,700i,800,800i%7CCovered+By+Your+Grace" rel="stylesheet">
	
    <link rel="stylesheet" type="text/css" href="include_front/assist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="include_front/assist/css/animate.css">
    <link rel="stylesheet" type="text/css" href="include_front/assist/css/magnific-popup.css">
    <link rel="stylesheet" type="text/css" href="include_front/assist/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="include_front/assist/css/hover-min.css">
    <link rel="stylesheet" type="text/css" href="include_front/assist/plugins/icon/style.css">
    <link rel="stylesheet" type="text/css" href="include_front/assist/plugins/bands-icon/style.css">
    <link rel="stylesheet" type="text/css" href="include_front/assist/css/owl.carousel.css">
    <link rel="stylesheet" type="text/css" href="include_front/assist/css/owl.theme.default.min.css">
    <link rel="stylesheet" type="text/css" href="include_front/assist/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="include_front/assist/css/jquery.bxslider.min.css">
    <!-- main template stylesheet -->
    <link rel="stylesheet" href="include_front/assist/css/style.css">
    <link rel="stylesheet" href="include_front/assist/css/responsive.css">
</head>