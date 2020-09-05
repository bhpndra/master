<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);


	if ( isset($post['action']) AND $post['action'] == 'GetTotalDMTAmtTransferred' ) {
        $transaction_date   = date('Y-m-d');
        //$transaction_date   = "2019-03-29";

        $sql = "SELECT SUM(`amount`) AS total_amount FROM `dmt_info` ";
        $sql .= " WHERE (`status`='SUCCESS' OR `status`='PENDING') AND DATE(`date_created`)='$transaction_date' ";
        $retQuery = $mysqlClass->mysqlQuery($sql);
        $rowRB = $retQuery->fetch(PDO::FETCH_ASSOC);
        $totalAmtTransferred    = ( $rowRB['total_amount'] ) ? $rowRB['total_amount'] : 0;
        echo $totalAmtTransferred;
        die();
        
    }
	if ( isset($post['action']) AND $post['action'] == 'GetTotalDmtAmtDeducted' ) {
        $transaction_date   = date('Y-m-d');
        //$transaction_date   = "2019-03-29";

         $sql = "SELECT SUM(`withdrawl`) AS total_amount FROM `retailer_trans` ";
        $sql .= " WHERE `tr_type`='DMT' AND DATE(`date_created`)='$transaction_date' AND `transaction_id` NOT IN (SELECT `refund_id` FROM `retailer_trans` WHERE `refund_id`!='') ";
        $retQuery = $mysqlClass->mysqlQuery($sql);
        $rowRB = $retQuery->fetch(PDO::FETCH_ASSOC);
        $totalAmtTransferred    = ( $rowRB['total_amount'] ) ? $rowRB['total_amount'] : 0;
        echo $totalAmtTransferred;
        die();
        
    }	
?>
