<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);

	if($post['status']!="" && $post['id']!="" && $post['agent_id']!="")
	{	
		if( $post['status'] == "PENDING"){
			$dataArray1 = ["status"=>'PENDING'];
			$mysqlClass->updateData("`dmt_info`",$dataArray1," where dmt_id = '".$post['id']."' and agent_id = '".$post['agent_id']."'");
			echo "Record Updated successfully";
		}
		if( $post['status'] == "SUCCESS"){
			$dataArray1 = ["status"=>'SUCCESS'];
			$mysqlClass->updateData("`dmt_info`",$dataArray1," where dmt_id = '".$post['id']."' and agent_id = '".$post['agent_id']."'");
			echo "Record Updated successfully";
		}	
		if( $post['status'] == "REFUNDED"){
			$dataArray1 = ["status"=>'REFUNDED'];
			$mysqlClass->updateData("`dmt_info`",$dataArray1," where dmt_id = '".$post['id']."' and agent_id = '".$post['agent_id']."'");
			echo "Record Updated successfully";
		}		
		if( $post['status'] == "FAILED"){
			$dataArray1 = ["status"=>'FAILED'];
			$mysqlClass->updateData("`dmt_info`",$dataArray1," where dmt_id = '".$post['id']."' and agent_id = '".$post['agent_id']."'");
			echo "Record Updated successfully";
		}	

		
	}
?>