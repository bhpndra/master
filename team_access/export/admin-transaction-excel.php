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
   
   $sqlQuery = "select * from admin_trans where 1 and `transaction_id`!='' and `transaction_id`!='0'  and admin_id = '".$_SESSION[_session_userid_]."'"; ;
   
   $sqlQuery = $mysqlObj->mysqlQuery($sqlQuery.$filter);	
   $data = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
   
   $fileName = "admin_transaction_".date('Ymd') . ".xls";			
   header("Content-Type: application/vnd.ms-excel");
   header("Content-Disposition: attachment; filename=\"$fileName\"");	
   $showColoumn = false;
   if(!empty($data)) {
   foreach($data as $admin_transaction) {
   if(!$showColoumn) {		 
   echo implode("\t", array_keys($admin_transaction)) . "\n";
   $showColoumn = true;
   }
   echo implode("\t", array_values($admin_transaction)) . "\n";
   }
   }
   exit;  
   }
   else{
   
   }
   ?>