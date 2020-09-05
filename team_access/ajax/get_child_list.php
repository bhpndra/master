<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include("../classes/user_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	$userClass = new user_class();
	
	$post = $helpers->clearSlashes($_POST);
	

	if($post['uid']!="")
	{	
		$userType = $userClass->check_user_type($post['uid']); 
		$rows = $mysqlClass->fetchAllData("add_cust","*"," where creator_id = '".$post['uid']."' ");
//print_r($row);
echo "<ul>";
	foreach($rows as $row){
?>
		<li><?=$row['name']?> - (<?=$row['mobile']?>) - <?=$row['cname']?> - <a href="void:javascript()" onclick="get_child_retailer('<?=$row['id']?>')">#<?=$row['id']?></a></li>
	
<?php	}
echo "</ul>";
	} 
?>