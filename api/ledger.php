<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("../config.php");
	require("classes/db_class.php");
	require("classes/comman_class.php");
	require("classes/user_class.php");
	require("classes/jwt_encode_decode.php");
	$helpers = new Helper_class();
	$mysqlClass = new Mysql_class();
	$jwtED = new jwt_encode_decode();
	
	if($_SERVER['HTTP_X_API_KEY']==HTTP_X_API_KEY && $_SERVER['HTTP_NETPAISAPASSKEY']==NETPAISAPASSKEY){	
	} else {
		//print_r($_SERVER);
		$helpers->errorResponse("Authorization Invalid !");
	}
	
	if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST)){
		
		$post = $helpers->clearSlashes($_POST);
		
		//// Valided Token and Get Token Details ////
		if(isset($post['token'])){
			$res = $jwtED->decode_token($post['token']);
			if(isset($res->USER_ID) && $res->USER_ID > 0){
				$USER_ID 	= $res->USER_ID;
				$CREATOR_ID	= $res->CREATOR_ID;
				$WL_ID 		= $res->WL_ID;
				$ADMIN_ID 	= $res->ADMIN_ID;
			} else {
				$helpers->errorResponse("Token Expire");
			}
		} else {
			$helpers->errorResponse("Token not set!");
		}
		/////////////////// End //////////////////
		

		$filter = '';
		//// Check Parameters Isset ////
		$limit = (isset($post['limit']) && $post['limit'] > 0) ? $post['limit'] : '';
		$offset = (isset($post['offset']) && $post['offset'] > 0) ? $post['offset'] : 0;
		$offset = (isset($post['page']) && $post['page'] > 0) ? $post['page'] * $limit : $offset;
		if(isset($limit) && !empty($limit)){
			$pagin = " limit $offset,$limit"; 
		} else { $pagin = "";  }
		
		if(isset($post['dateFrom']) && isset($post['dateTo'])){
			$dateFrom = $helpers->createDate($post['dateFrom']);
			$dateTo = $helpers->createDate($post['dateTo']);
		}
		
		if(isset($dateFrom) && isset($dateTo) && !empty($dateFrom) && !empty($dateTo)){
			$diff=date_diff(date_create($dateFrom),date_create($dateTo))->days;
			if($diff > 30){
				$helpers->errorResponse('You can fetch only 30 Days recharge history.');
			}
			$filter .= " and (date(date_created) BETWEEN '$dateFrom' AND '$dateTo') ";
		} else {
			if(isset($post['limit']) && $post['limit'] > 0){}else{
				$date1 	  = new DateTime('7 days ago');
				$dateFrom = $date1->format('Y-m-d');
				$dateTo   = date("Y-m-d");
				$filter .= " and (date(date_created) BETWEEN '$dateFrom' AND '$dateTo') ";
			}
		}
				
		$filter .= (isset($post['search']) && $post['search'] != '') ? " and (concat_ws(' ',transaction_id,agent_trid,refund_id,comments) like '%".$post['search']."%') " : '';
		$orderBy = " order by `id` DESC";
		/////////////////// End //////////////////
		
		$columns = " transaction_id, agent_trid, opening_balance, deposits, withdrawl, balance as closing_balance, comments, tr_type, date_created, refund_id ";
		$res = $mysqlClass->fetchAllData('retailer_trans', $columns, " where retailer_id = '".$USER_ID."' ".$filter.$orderBy.$pagin );
		if(count($res)>0){
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = 'Report Found';
			$response['DATA'] = $res;
		} else {
			$response['ERROR_CODE'] = 1;
			$response['MESSAGE'] = 'Transaction Report Not Found';
		}
		

	} else {
		$helpers->errorResponse("Invalid request !");
	}
	echo json_encode($response);
	
	$mysqlClass->close_connection();
	die();
?>