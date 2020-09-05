<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	if(!isset($_SESSION[_session_userid_]))	{
		die();
	}
	$post = $helpers->clearSlashes($_POST);
	echo "<option value='' disabled selected>Select user</option>";

	if($_POST['usertype']=="WL")
	{
		//==================================================
		//@@all retailer list to pay balance@@
		//===================================================
		$mysQuery=$mysqlClass->mysqlQuery("SELECT * FROM `add_cust` WHERE `admin_id` = '".$_SESSION[_session_userid_]."' and usertype = 'WL'");
		while ($row = $mysQuery->fetch(PDO::FETCH_ASSOC))
		{
			echo "<option value='" . $row['id'] . "'>" . $row['cname'] ." (".$row['mobile']. ")"."</option>";
		}	
		
	}

	
?>