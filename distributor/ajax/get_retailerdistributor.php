<?php
	session_start();
	require("../../config.php");
	require("../../include/lib.php");
	require("../../api/classes/db_class.php");
	require("../../api/classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();

if(isset($_SESSION['TOKEN'])){
	$url = BASE_URL."/api/users/get_token_details.php";
	$post_fields = array("token"=>$_SESSION['TOKEN']);
	$responseVT = api_curl($url,$post_fields,$headerArray);
	$resVT = json_decode($responseVT,true);
	if($resVT['ERROR_CODE']==1){
		$helpers->errorResponse("SESSION EXPIRED!");
	}
} else {
	$helpers->errorResponse("SESSION EXPIRED!");
}
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
		

	echo "<option value='' disabled selected>Select user</option>";
	if($_POST['usertype']=="RETAILER")
	{
		
		//==================================================
		//@@all retailer list to pay balance@@
		//===================================================
		
		$mysQuery = $mysqlClass->mysqlQuery("SELECT cname,name,mobile,id FROM `add_cust` WHERE 1 and `creator_id` = '".$USER_ID."' and `wl_id`='".$WL_ID."' and admin_id = '".$ADMIN_ID."' and usertype = 'RETAILER' ");
		while ($row = $mysQuery->fetch(PDO::FETCH_ASSOC))
		{
			echo "<option value='" . $row['id'] . "'>" . $row['name'] . " - " . $row['cname'] ." (".$row['mobile']. ")"."</option>";
		}	
		
		
	}
	
	
	if($_POST['usertype']=="DISTRIBUTOR")
	{
		//==================================================
		//@@all retailer list to pay balance@@
		//===================================================
		$mysQuery = $mysqlClass->mysqlQuery("SELECT cname,name,mobile,id FROM `add_cust` WHERE 1 and `creator_id` = '".$USER_ID."' and `wl_id`='".$WL_ID."' and admin_id = '".$ADMIN_ID."' and usertype = 'DISTRIBUTOR' ");
		while ($row = $mysQuery->fetch(PDO::FETCH_ASSOC))
		{
			echo "<option value='" . $row['id'] . "'>" . $row['name'] . " - " . $row['cname'] ." (".$row['mobile']. ")"."</option>";
		}	
	}	

?>
