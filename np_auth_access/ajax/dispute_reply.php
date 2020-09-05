<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);

	if(isset($post['message']) && !empty($post['message']) && !empty($post['tid']) && !empty($post['did'])){
			
		$data = array(
			'employee_id' => $_SESSION[_session_userid_],
			'employee_name' => 'ADMIN',
			'dispute_id' => $post['did'],
			'comment' => $post['message']
		);
		if($mysqlClass->insertData("disputes_comment", $data)>0){
			if($post['status']=="Close"){
				$data = array(				
					'employee_id' => $_SESSION[_session_userid_],				
					'employee_name' => 'ADMIN',				
					'status' => $post['status'],
				);
				$mysqlClass->updateData("disputes", $data, " where id = '".$post['did']."'");
			}
			echo $post['status'];
		}
	}
?>
<?php
/*
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);

	if(isset($post['message']) && !empty($post['message']) && !empty($post['tid'])){
		$data = array(
			'user_id' => $post['uid'],
			'employee_id' => $_SESSION[_session_userid_],
			'transaction_id' => $post['tid'],
			'message' => $post['message'],
			'status' => $post['status'],
		);		
		if($mysqlClass->insertData("disputes", $data)>0){
			echo $post['status'];
		}
	}
	*/
?>