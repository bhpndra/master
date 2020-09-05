<?php
include_once('classes/nps_db_connection.php');
include_once('classes/comman_class.php');
ini_set('display_errors', 0); // see an error when they pop up
error_reporting(E_ALL); // report all php errors

if(!isset($_SESSION[_session_userid_]))
{
	header("location: login.php");
	exit;
}
else
{
	$now = time(); // Checking the time now when current page starts.
	
	if ($now > $_SESSION['expiresu']) {
		//session_destroy();
		//header("Location:logout.php");
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Home</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="#" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="Cache-control" content="no-cache">
    <meta http-equiv="Expires" content="-1">
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
	
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />	
    <link href="new-js/easy-autocomplete/easy-autocomplete.css" rel="stylesheet" type="text/css" />
    <link href="new-css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="new-css/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="new-css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="new-css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="new-css/daterangepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="new-css/morris.css" rel="stylesheet" type="text/css" />
    <link href="new-css/fullcalendar.min.css" rel="stylesheet" type="text/css" />
    <link href="new-css/jqvmap.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="new-css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="new-css/plugins.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="new-css/layout.min.css" rel="stylesheet" type="text/css" />
    <link href="new-css/default.min.css" rel="stylesheet" type="text/css" id="style_color" />
    <link href="new-css/custom.min.css" rel="stylesheet" type="text/css" />

    <link href="new-css/jquery.bdt.css" rel="stylesheet" type="text/css" />
    <link href="new-css/jquery.bdt.min.css" rel="stylesheet" type="text/css" />
    <link href="new-css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

    <link rel="shortcut icon" href="#" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js" type="text/javascript"></script>



</head>
