<?php
global $conn;
$conn = mysqli_connect("localhost", 'root','',"netpaisa_reseller_db");
function get_page_content($id, $dbc, $user_id) {

    $uname = "SELECT `page_content` FROM `page_content` WHERE `page_id`='$id' and user_id = '$user_id'";
    if ($rows = $dbc->query($uname)) {

        $data = $rows->fetch_assoc();

        return $data;
    } else {
        return false;
    }
}
?>