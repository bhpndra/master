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
	$helper   = new helper_class();
	$userClass = new user_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";
	$date1 	  = new DateTime('1 days ago');
	$dateTo = $date1->format('Y-m-d');
	
	if(isset($filterBy['dateTo']) && $filterBy['dateTo']!=""){
		$dateTo   = $filterBy['dateTo'];
	} 
	

	if(isset($_GET['ret_export'])){
		$sql = "SELECT ac.`id` ,ac.`status`,ac.`user`,ac.`cname`,rt.balance FROM `add_cust` ac 
		INNER JOIN `retailer_trans` rt ON rt.retailer_id=ac.id 
		WHERE ac.`id` IN (SELECT `user_id` FROM `add_retailer`) AND rt.id IN (SELECT max(rtr.id) FROM `retailer_trans` rtr WHERE DATE(rtr.date_created)<='$dateTo' group by rtr.retailer_id)";
	
		$sqlQuery = $mysqlObj->mysqlQuery($sql);
		$data = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
		switch("export-to-excel") {
		case "export-to-excel" :
			$filename = "retailer_closing_".time().".xls";		 
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"$filename\"");
			ExportFile($data);
			exit();
		default :
			die("Unknown action ");
			break;
		}
		
	}
	
	if(isset($_GET['dist_export'])){
		$sql = "SELECT ac.`id` ,ac.`status`,ac.`user`,ac.`cname`,dt.balance FROM `add_cust` ac 
		INNER JOIN `distributor_trans` dt ON dt.dist_id=ac.id 
		WHERE ac.`id` IN (SELECT `user_id` FROM `add_distributer`) AND dt.id IN (SELECT max(dst.id) FROM `distributor_trans` dst WHERE DATE(dst.date_created)<='$dateTo' group by dst.dist_id)";
	
		$sqlQuery = $mysqlObj->mysqlQuery($sql);
		$data = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
		switch("export-to-excel") {
		case "export-to-excel" :
			$filename = "distributer_closing_".time().".xls";		 
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"$filename\"");
			ExportFile($data);
			exit();
		default :
			die("Unknown action ");
			break;
		}
		
	}	
	
	if(isset($_GET['wl_export'])){
		$sql = "SELECT ac.`id` ,ac.`status`,ac.`user`,ac.`cname`,wlt.balance FROM `add_cust` ac INNER JOIN `wl_trans` wlt ON wlt.wluser_id=ac.id WHERE ac.`id` IN (SELECT `user_id` FROM `add_white_label`) AND wlt.id IN (SELECT max(wl.id) FROM `wl_trans` wl WHERE DATE(wl.date_created)<='$dateTo' group by wl.wluser_id)";
	
		$sqlQuery = $mysqlObj->mysqlQuery($sql);
		$data = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
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