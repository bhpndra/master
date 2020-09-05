<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include_once('../inc/apivariable.php');
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);

	$ADMIN_ID = $_SESSION[_session_userid_];	

	if(isset($post['switchVal'])  && ($post['switchVal']=='DEFAULT' || $post['switchVal']=='CUSTOM')){		
		$res = $mysqlClass->mysqlQuery("select sms_api from `add_cust` WHERE `admin_id`='".$ADMIN_ID."'" )->fetch(PDO::FETCH_ASSOC);
		if($res['sms_api']!=$post['switchVal']){
			$data = array("sms_api"=>$post['switchVal']);
			$rowU = $mysqlClass->updateData(" add_cust ", $data , " WHERE `admin_id`='".$ADMIN_ID."' " );
			if($rowU > 0){
				$response['ERROR_CODE'] = 0;
				$response['MESSAGE'] = 'SUCCESS';
				echo json_encode($response); die();
			}
		} else { 
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = 'SUCCESS';
			echo json_encode($response); die();
		}

	}
	

?>
