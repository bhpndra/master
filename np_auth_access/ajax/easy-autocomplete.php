<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);

	//lister by  fByUsername
	if($post['searchString']!="" && $post['filterOn']=="fByUsername")
	{		
		$rows = $mysqlClass->fetchAllData("`add_cust`","name","where name like '%".$post['searchString']."%' ");
		echo json_encode($rows);		
	}
	
	//lister by  fByUserid
	if($post['searchString']!="" && $post['filterOn']=="fByUserid")
	{		
		$rows = $mysqlClass->fetchAllData("`add_cust`","user","where user like '%".$post['searchString']."%' ");
		echo json_encode($rows);		
	}
	
	//lister by  fBycname
	if($post['searchString']!="" && $post['filterOn']=="fBycname")
	{		
		$rows = $mysqlClass->fetchAllData("`add_cust`","cname","where cname like '%".$post['searchString']."%' ");
		echo json_encode($rows);		
	}
?>
