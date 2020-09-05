<?php
/*************** 
ERROR_CODE -> 0 - Success / 1 - Error
MESSAGE -> ""
***************/
	require("../../config.php");
	require("../classes/db_class.php");
	require("../classes/comman_class.php");
	require("../classes/user_class.php");
	require("../classes/jwt_encode_decode.php");
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
		$offset = (isset($post['offset']) && $post['offset'] > 0) ? $post['offset'] : 0;
		$limit = (isset($post['limit']) && $post['limit'] > 0) ? $post['limit'] : '';
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
				$helpers->errorResponse('You can fetch only 30 Days history.');
			}
			$filter .= " and (date(settlement_date) BETWEEN '$dateFrom' AND '$dateTo') ";
		} else {
			$date1 	  = new DateTime('7 days ago');
			$dateFrom = $date1->format('Y-m-d');
			$dateTo   = date("Y-m-d");
			$filter .= " and (date(settlement_date) BETWEEN '$dateFrom' AND '$dateTo') ";
		}
		
		if(isset($post['status'])){
			$filter .= " and status = '".$post['status']."' ";
		}
				
		$orderBy = " order by id DESC";
		/////////////////// End //////////////////
		
		$totalRows = $mysqlClass->countRows(" select id from aeps_withdrawl_info where user_id = '".$USER_ID."'  ".$filter);
		
		$columns = " transaction_id, withdrawl_type, amount, status, payment_date, settlement_date, group_id, account_number, bank_name, ifsc, account_name, id";
		$query = $mysqlClass->mysqlQuery("select ".$columns." from aeps_withdrawl_info  where user_id = '".$USER_ID."' ".$filter.$orderBy.$pagin );
		while($res = $query->fetch(PDO::FETCH_ASSOC)){
			$row[] = $res;
		}
		if($totalRows > 0){
			$response['ERROR_CODE'] = 0;
			$response['MESSAGE'] = 'Report Found';
			$response['DATA'] = $row;
			$response['TOTAL_ROW'] = $totalRows;
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