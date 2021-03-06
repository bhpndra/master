<?php

session_start();
require("../../../config.php");
require("../../../include/lib.php");
require("../../../api/classes/db_class.php");
require("../../../api/classes/comman_class.php");
$helpers = new helper_class();

if (isset($_SESSION['TOKEN'])) {
    $post = $helpers->clearSlashes($_POST);
    $trans_url = BASE_URL . "/api/dmt/beneficiary_account_verification.php";
    $post_fields = array(
        "token" => $_SESSION['TOKEN'],
        "remitterid" => $post['remitterid'],
        "account" => $post['account'],
        "ifsc" => $post['ifsc'],
        "mobile" => $post['mobile']
    );
    $responseAPI = api_curl($trans_url, $post_fields, $headerArray);
    $resAPI = json_decode($responseAPI, true);
    //print_r($responseAPI);	
    if ($resAPI['ERROR_CODE'] == 0) {
        $response['status'] = 0;
        $response['msg'] = $resAPI['MESSAGE'];
        $response['data'] = $resAPI['DATA'];
    } else {
        $response['status'] = 1;
        $response['msg'] = $resAPI['MESSAGE'];
    }
} else {
    $response['status'] = 0;
    $response['msg'] = "Wrong Request";
}
echo json_encode($response);
?>
