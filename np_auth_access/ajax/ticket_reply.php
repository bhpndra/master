<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);

	if(isset($post['message']) && !empty($post['message']) && !empty($post['tid'])){
		$data = array(
			'employee_id' => $_SESSION[_session_userid_],
			'employee_name' => $_SESSION[_session_username_]."_ADMIN",
			'ticket_id' => $post['tid'],
			'comment' => $post['message']
		);
		if($mysqlClass->insertData("ticket_comments", $data)>0){
			if($post['status']=="Close"){
				$data = array(
				'employee_id' => $_SESSION[_session_userid_],
				'employee_name' => $_SESSION[_session_username_]."_ADMIN",
				'is_active' => $post['status']
				);
				$mysqlClass->updateData("tickets", $data, " where id = '".$post['tid']."' " );
				echo "Close";
			}
		}		
	}
?>