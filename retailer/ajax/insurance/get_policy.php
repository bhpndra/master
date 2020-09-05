<?php
	session_start();
	require("../../../config.php");
	require("../../../include/lib.php");
	require("../../../api/classes/db_class.php");
	require("../../../api/classes/comman_class.php");
  require("../../../api/classes/jwt_encode_decode.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
    $jwtED = new jwt_encode_decode();

if(isset($_SESSION['TOKEN'])){
      $res = $jwtED->decode_token($_SESSION['TOKEN']);
      if(isset($res->USER_ID) && $res->USER_ID > 0){
        $USER_ID  = $res->USER_ID;
        $CREATOR_ID = $res->CREATOR_ID;
        $WL_ID    = $res->WL_ID;
        $ADMIN_ID   = $res->ADMIN_ID;
      } else {
        $helpers->errorResponse("Token Expire");
      }
    } else {
      $helpers->errorResponse("Token not set!");
    }


$columns = " * ";
$res = $mysqlClass->fetchAllData('religare_proposals', $columns, "where user_id='".$USER_ID."' AND payment_status=1  order by religare_proposal_id desc");

if($res){
$result['data']=array('status'=>'success','policies'=>$res);
}
else{ $result['data']=array('status'=>'failed','policies'=>''); }
$someJSON=json_encode($result);
echo $someJSON;
?>
