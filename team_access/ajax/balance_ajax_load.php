<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);

	if ( isset($post['action']) AND $post['action'] == 'GetClosingBalance' ) {

		$postDMT_netpaisa = $helpers->netpaisa_curl("https://netpaisa.com/nps/api/get_balance.php", array('api_access_key'=>$_SESSION[_session_apiKey_]));
		$dataDMT = json_decode($postDMT_netpaisa, true);
		
		
		
    // print_r($post);
		echo "<table class='table table-bordered' >
				<tr> <th>API Main Balance: </th> <td>".$dataDMT['data']['wallet_balance']."</td> </tr>
				<tr> <th>API AEPS Balance: </th> <td>".$dataDMT['data']['aeps_balance']."</td> </tr>
			</table>";
        die();
        
    }	
?>
