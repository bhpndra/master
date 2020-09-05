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
   
   $sqlQuery = "select a.name, a.user, a.mobile,a.cname,r.* from distributor_trans r INNER JOIN add_cust a  ON r.dist_id=a.id  where 1 and a.admin_id = '".$_SESSION[_session_userid_]."' ";
   
   $sqlQuery = $mysqlObj->mysqlQuery($sqlQuery.$filter);	
   $data = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
   
   
   $fileName = "distributer_transaction_".date('Ymd') . ".xls";			
   header("Content-Type: application/vnd.ms-excel");
   header("Content-Disposition: attachment; filename=\"$fileName\"");	
   $showColoumn = false;
   if(!empty($data)) {
   foreach($data as $distributer_transaction) {
   if(!$showColoumn) {		 
   echo implode("\t", array_keys($distributer_transaction)) . "\n";
   $showColoumn = true;
   }
   echo implode("\t", array_values($distributer_transaction)) . "\n";
   }
   }
   exit;  
   }
   else{
   
   }
   ?>