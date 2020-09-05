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


/*if(isset($_SESSION['TOKEN'])){
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
    }*/

      $post = $helpers->clearSlashes($_POST); 

          $birthDt = $post['birthDt'];
    $firstName = $post['firstName'];
    $genderCd = $post['genderCd'];
    $lastName = $post['lastName'];
    $addressLine1Lang1 = $post['addressLine1Lang1'];
    $addressLine2Lang1 = $post['addressLine2Lang1'];
    $cityCd = $post['cityCd'];
    $pinCode = $post['pinCode'];
    $stateCd = $post['stateCd'];
    $addressLine1Lang1c = $post['addressLine1Lang1c'];
    $addressLine2Lang1c = $post['addressLine2Lang1c'];
    $cityCdc = $post['cityCdc'];
    $pinCodec = $post['pinCodec'];
    $stateCdc = $post['stateCdc'];
    $contactNum = $post['contactNum'];
    $emailAddress = $post['emailAddress'];
    $titleCd = $post['titleCd'];
    $sumInsured = $post['sumInsured'];

  $trans_url = BASE_URL."/api/insurance/get_religare_proposal.php";
  $post_fields = array(
          "token"=>$_SESSION['TOKEN'],
          'birthDt' => $birthDt,
          'firstName' => $firstName,
          'genderCd' => $genderCd,
          'lastName' => $lastName,
          'addressLine1Lang1' => $addressLine1Lang1,
          'addressLine2Lang1' => $addressLine2Lang1,
          'cityCd' => $cityCd,
          'pinCode' => $pinCode,
          'stateCd' => $stateCd,
          'addressLine1Lang1c' => $addressLine1Lang1c,
          'addressLine2Lang1c' => $addressLine2Lang1c,
          'cityCdc' => $cityCdc,
          'pinCodec' => $pinCodec,
          'stateCdc' => $stateCdc,
          'contactNum' => $contactNum,
          'emailAddress' => $emailAddress,
          'titleCd' => $titleCd,
          'sumInsured' => $sumInsured
          );
  $responseRC = api_curl($trans_url,$post_fields,$headerArray);
  $resRC = json_decode($responseRC,true);

if($resRC['ERROR_CODE']==0){
  $rm=json_decode($resRC['MESSAGE']);
  $result['data']=array('status'=>'success','msg'=>$rm);
}
elseif($resRC['ERROR_CODE']==2){
  $rm=json_decode($resRC['MESSAGE']);
  $result['data']=array('status'=>'failed','msg'=>$resRC['MESSAGE']);
}
else{
  //$rm=json_decode($resRC['MESSAGE']);
  $result['data']=array('status'=>'failed','msg'=>$resRC['MESSAGE']);
}

  $someJSON=json_encode($result);
        echo $someJSON;
}


?>
