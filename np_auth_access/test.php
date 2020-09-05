<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	
	$database1 = new Database();
	$db_1 = $database1->getConnection();
	
	$database2 = new Database();
	$db_2 = $database2->getConnection_2();
?>
<?php
		/*
		* display today's Distributor balance
		*/
		//$mysqlObj->db_conn  = $db_1; /* overlap connecation */
		//$mysqlObj->db_conn  = $db_2; /* overlap connecation */
		
		$ret_total_balance = 0;
		$retQuery = $mysqlObj->mysqlQuery("SELECT `id` FROM `add_cust` WHERE `status`='ENABLED' AND `id` IN (SELECT `user_id` FROM `add_retailer`)");
		while ($retRows = $retQuery->fetch(PDO::FETCH_ASSOC)) {
		
//echo $retRows['id']." ---- ";
			$baldistQuery = $mysqlObj->mysqlQuery("SELECT `balance` FROM `retailer_trans` WHERE  `retailer_id`='".$retRows['id']."' ORDER BY `id` DESC LIMIT 1");
//echo $baldistRows['balance']." <br/>";		
			$baldistRows = $baldistQuery->fetch(PDO::FETCH_ASSOC);
			$ret_total_balance = $ret_total_balance + $baldistRows['balance'];
	
		}
		
		echo $ret_total_balance;
?>