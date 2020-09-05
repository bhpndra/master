<?php
include_once('../classes/nps_db_connection.php');
include_once('../classes/comman_class.php');
ini_set('display_errors', 0); // see an error when they pop up
error_reporting(E_ALL); // report all php errors

if(!isset($_SESSION[_session_userid_]))
{
	header("location: login.php");
	exit;
}

	include_once('../classes/user_class.php'); 
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	$userClass = new user_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	
	if(isset($filterBy['groupid'])&& $filterBy['groupid']!=""){
		$data = $mysqlObj->mysqlQuery("SELECT * FROM `aeps_info` where group_id = '".$filterBy['groupid']."'")->fetchAll(PDO::FETCH_ASSOC);
	
//print_r($data);	
		switch("export-to-excel") {
		case "export-to-excel" :
			$filename = "whitelabel_closing_".time().".xls";		 
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"$filename\"");
			ExportFile($data);
			exit();
		default :
			die("Unknown action ");
			break;
		}
	}

function ExportFile($records) {
	$heading = false;
		if(!empty($records))
		  foreach($records as $row) {
			if(!$heading) {
			  // display field/column names as a first row
			  echo implode("\t", array_keys($row)) . "\n";
			  $heading = true;
			}
			echo implode("\t", array_values($row)) . "\n";
		  }
		exit;
}	

	
?>