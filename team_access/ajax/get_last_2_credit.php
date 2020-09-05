<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
if(!isset($_SESSION[_session_userid_]))
{
	die();
}		
	$post = $helpers->clearSlashes($_POST);

	$sqlQuery1 = $mysqlClass->mysqlQuery("SELECT withdrawl,date_created FROM `admin_trans` where  wl_id = '".$post['user_id']."' and tr_type = 'DR'  ORDER BY `id` DESC limit 3");
	echo "<table class='table' style='background: #f7ffd3'><tr><th colspan='3' style='text-align: center;'>Last 3 Credit to User</th></tr>";
	echo "<tr><th>Amount</th><th>Time</th></tr>";
	$Lrows = '';
	while($Lrows = $sqlQuery1->fetch(PDO::FETCH_ASSOC)){
		echo "<tr>";
			echo "<td>".$Lrows['withdrawl']."</td>";
			echo "<td>".$Lrows['date_created']."</td>";
		echo "</tr>";	
	}
	echo "</table>";
	
?>