<?php
   include_once('../classes/nps_db_connection.php');
   include_once('../classes/comman_class.php');
   ini_set('display_errors', 0); // see an error when they pop up
   error_reporting(E_ALL); // report all php errors
   
   include_once('../classes/user_class.php'); 
   $mysqlObj = new mysql_class();
   $helper   = new helper_class();
   $userClass = new user_class();
   
   $filter = $_GET['filter'];
   
   if(isset($_POST["dataExport"])){
   
   $orderBy =" ORDER BY b.`id` DESC";
   $sqlQuery = "SELECT 
			a.wl_id,
			a.transaction_id,
			a.date_created,
			a.withdrawl,
			a.balance,
			a.comments,
			b.name,
			b.cname,
			b.user,
			b.usertype,
			b.mobile
		FROM `admin_trans` as a, `add_cust` as b WHERE `tr_type`='DR' and b.id = a.wl_id  and b.admin_id = '".$_SESSION[_session_userid_]."' " ;
   
   $sqlQuery = $mysqlObj->mysqlQuery($sqlQuery.$filter);	
   $data = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
   
   $fileName = "fund_transfer_statements_".date('Ymd') . ".xls";			
   header("Content-Type: application/vnd.ms-excel");
   header("Content-Disposition: attachment; filename=\"$fileName\"");	
   $showColoumn = false;
   if(!empty($data)) {
    foreach($data as $fund_transfer_statements) {
   if(!$showColoumn) {		 
     echo implode("\t", array_keys($fund_transfer_statements)) . "\n";
     $showColoumn = true;
   }
   echo implode("\t", array_values($fund_transfer_statements)) . "\n";
    }
   }
   exit;  
   }
   else{
   
   }
   ?>