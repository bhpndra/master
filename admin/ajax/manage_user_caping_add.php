<?php
session_start();
require ("../../config.php");
require ("../../include/lib.php");
require ("../../api/classes/db_class.php");
require ("../../api/classes/comman_class.php");
$helpers = new helper_class();
$mysqlClass = new Mysql_class();

if (isset($_SESSION['TOKEN']))
{

    $url = BASE_URL . "/api/users/get_token_details.php";
    $post_fields = array(
        "token" => $_SESSION['TOKEN']
    );
    $responseVT = api_curl($url, $post_fields, $headerArray);
    $resVT = json_decode($responseVT, true);
    if ($resVT['ERROR_CODE'] == 1)
    {
        $helpers->errorResponse("SESSION EXPIRED!");
    }
}
else
{
    $helpers->errorResponse("SESSION EXPIRED!");
}

$ADMIN_ID = $resVT['DATA']['ADMIN_ID']; // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];

//==================================================
//@@all retailer list to pay balance@@
//===================================================
$post = $helpers->clearSlashes($_POST);

$admin_no_of_limit = $mysqlClass->mysqlQuery(" SELECT `number_of_child_limi` FROM `add_cust` WHERE `id`='" . $WL_ID . "' and usertype = 'WL'  ")->fetch(PDO::FETCH_ASSOC);

$ad_number_of_child_limit = $admin_no_of_limit['number_of_child_limi'];
$e_limit = $post['e_limit'];
if($e_limit <= 0){
	$helpers->errorResponse("Value is not less than 0");
}

if ($ad_number_of_child_limit < 1) {
	$helpers->errorResponse("You have not sufficent child credit limit!");
}

if ($ad_number_of_child_limit > $e_limit) {

    $a_c_limit = $ad_number_of_child_limit - $e_limit;

    $rowU = $mysqlClass->mysqlQuery(" UPDATE add_cust SET number_of_child_limi='" . $a_c_limit . "' WHERE id='" . $WL_ID . "' and usertype = 'WL' ");

    if ($rowU > 0){
        $dis_c_child_limit = $mysqlClass->mysqlQuery(" SELECT `number_of_child_limi` FROM `add_cust` WHERE `id`='" . $post['dis_id'] . "' and usertype = 'DISTRIBUTOR'")->fetch(PDO::FETCH_ASSOC);
        $dis_c_child_limit_tdata = $dis_c_child_limit['number_of_child_limi'] + $e_limit;
        $mysqlClass->mysqlQuery(" UPDATE add_cust SET number_of_child_limi='" . $dis_c_child_limit_tdata . "' WHERE id='" . $post['dis_id'] . "' and usertype = 'DISTRIBUTOR' ");

        $response['ERROR_CODE'] = 0;
        $response['MESSAGE'] = $e_limit . " child limit added succufully.";
        $response['UPDATED_LIMIT'] = $dis_c_child_limit_tdata;
        echo json_encode($response);
        die();
    }
    else
    {
        $response['ERROR_CODE'] = 1;
        $response['MESSAGE'] = "FAILED";
        echo json_encode($response);
        die();
    }
}

?>
