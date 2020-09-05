<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);


	if ( isset($post['rowId']) AND $post['tranId'] != '' ) {
       if ($post['userTyep'] == "RETAILER") {
	   //Opening Balance
			$selqrO = "SELECT `balance` FROM `retailer_trans` WHERE id < (SELECT id FROM retailer_trans WHERE `agent_trid`='".$post['tranId']."' LIMIT 1) && `retailer_id`='".$post['userID']."' ORDER BY id DESC LIMIT 1 ";			
			$retQuery1 = $mysqlClass->mysqlQuery($selqrO);
			$rowOB = $retQuery1->fetch(PDO::FETCH_ASSOC);
			$Opening_bal    = ( $rowOB['balance'] ) ? $rowOB['balance'] : 0;
			
		//withdrawl Balance	
			$selqrC = "SELECT `agent_trid`,`withdrawl`,`retailer_id` FROM `retailer_trans` WHERE `agent_trid`='".$post['tranId']."' && `retailer_id`='".$post['userID']."'";			
			$retQuery2 = $mysqlClass->mysqlQuery($selqrC);
			$rowCB = $retQuery2->fetch(PDO::FETCH_ASSOC);
			$withdrawl    = ( $rowCB['withdrawl'] ) ? $rowCB['withdrawl'] : 0;
		//Current Balance	
			$selqrCB = "SELECT `balance` FROM `retailer_trans` WHERE `agent_trid`='".$post['tranId']."'";
			$retQuery3 = $mysqlClass->mysqlQuery($selqrCB);
			$rowCB = $retQuery3->fetch(PDO::FETCH_ASSOC);
			$CurrentBal    = ( $rowCB['balance'] ) ? $rowCB['balance'] : 0;
			
		}
		
		
?>
<strong>Previous: </strong><?php echo $Opening_bal; ?><br/>
<strong>Withdrawl: </strong><?php echo $withdrawl; ?><br/>
<strong>Current: </strong><?php echo $CurrentBal; ?>
<?php
        die();
        
    }
?>