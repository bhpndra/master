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
   
   $orderBy =" order by `id` DESC";
   $sqlQuery = "SELECT p.mobile,
		p.amount,
		p.payment_type,
		p.status,
		p.user_id,
		p.bank,
		p.id,
		p.request_time,
		p.update_time,
		p.deposit_slip,
		p.bank_refno,
		a.name,
		a.cname
		FROM `payment` as p, `add_cust` as a  where 1 and p.user_id = a.id  and p.status != 'Pending' and p.admin_id = '".$_SESSION[_session_userid_]."' and p.user_type = 'WL'";
   
   $sqlQuery = $mysqlObj->mysqlQuery($sqlQuery.$filter);	
   $data = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
   
   $fileName = "fund_request_history_".date('Ymd') . ".xls";			
   header("Content-Type: application/vnd.ms-excel");
   header("Content-Disposition: attachment; filename=\"$fileName\"");	
   $showColoumn = false;
   if(!empty($data)) {
    foreach($data as $fund_request_history) {
   if(!$showColoumn) {		 
     echo implode("\t", array_keys($fund_request_history)) . "\n";
     $showColoumn = true;
   }
   echo implode("\t", array_values($fund_request_history)) . "\n";
    }
   }
   exit;  
   }
   else{
   
   }
   ?>