<?php

@session_start();

include "include/connect.php";

$response = array();



if (! empty($_POST)) {

    

    try {

        

        $user = trim($_POST['user']);

        $pass = trim($_POST['pass']);

        $web = "web";

        $success = "success";

        $failure = "failure";

        

        $status ="ENABLED";

        

        //$hash_password = hash('md5', $pass); // Password encryption

        

        $hash_pass = hashPassword($pass);

        $hash_password =  $hash_pass['encrypted'];

        $stmt11 = $db->prepare("SELECT user,pass,id,name,status FROM add_cust WHERE (user=:username)");
        $stmt11->bindparam("username", $user);
        $stmt11->execute();
        $userdetail1 = $stmt11->fetch(PDO::FETCH_ASSOC);


            if($userdetail1['status'] == 'DISABLED' ) {
                    
                    $response['status'] = 0;
                    $response['msg'] = "Your account has been deactivated!. Please contact to support";
                    echo json_encode($response);
                    die();


                 }

        
        if(!empty($userdetail1) && $userdetail1['status'] == 'ENABLED' ) {  
             
                 $id1 =  $userdetail1['id'];
                 $ip = get_client_ip_new();

                 $stmt1 = $db->prepare("SELECT * FROM threat_attempt where user_id = :userid ");
                 $stmt1->bindparam(":userid", $id1);
                 $stmt1->execute();
                 $get_count =  $stmt1->fetch(PDO::FETCH_ASSOC);



                  if($get_count['password_attempt'] == 5) {


                    $stmt5 = $db->prepare("UPDATE add_cust SET status = 'DISABLED' WHERE id = :userid ");
                    $stmt5->bindparam(":userid", $id1);
                    $stmt5->execute();

                    $response['status'] = 0;
                    $response['msg'] = "Your account has been deactivated!. Please contact to support";
                    echo json_encode($response);
                    die();


                 }




                     if(empty($get_count)) {

                                $stmt2 = $db->prepare("INSERT INTO threat_attempt (password_attempt,user_id,password_ip) VALUES (1,:userid,:ip) ");
                                $stmt2->bindparam(":userid", $id1);
                                $stmt2->bindparam(":ip", $ip);
                                $stmt2->execute();
                     } else {

                                $count = $get_count['password_attempt'] + 1;
                                $stmt3 = $db->prepare("UPDATE threat_attempt SET password_attempt = $count , password_ip = :ip WHERE user_id = :userid ");
                                $stmt3->bindparam(":userid", $id1);
                                $stmt3->bindparam(":ip", $ip);
                                $stmt3->execute();

                      }
             



        }




                if(empty($userdetail1)) { 
 
                       $server_details = json_encode($_SERVER);

                       $user_ip = get_client_ip_new();
                       $stmt4 = $db->prepare("SELECT * FROM ip_threat WHERE (user_ip=:user_ip)");
                       $stmt4->bindparam("user_ip", $user_ip);
                       $stmt4->execute();
                       $ipdetail = $stmt4->fetch(PDO::FETCH_ASSOC);

                        if($ipdetail['attempt'] >= 15) {

                                    $stmt5 = $db->prepare("UPDATE ip_threat SET status = 'DEACTIVATED' WHERE user_ip = :user_ip ");
                                    $stmt5->bindparam("user_ip", $user_ip);
                                    $stmt5->execute();
                                    $response['status'] = 0;
                                    $response['msg'] = "Your IP Address Blocked";
                                    echo json_encode($response);
                                    die();
                        }

                        if(empty($ipdetail)) {

                                 $stmt6 = $db->prepare("INSERT INTO ip_threat (user_ip, attempt, server_details) VALUES (:user_ip, 1,:server_details)");
                                 $stmt6->bindparam("user_ip", $user_ip);
                                 $stmt6->bindparam("server_details", $server_details);
                                 $stmt6->execute();

                        } else {

                                 $count = $ipdetail['attempt'] + 1;
                                 
                                 $stmt7 = $db->prepare("UPDATE ip_threat SET attempt = $count, server_details = :server_details  WHERE user_ip = :user_ip");
                                 $stmt7->bindparam("user_ip", $user_ip);
                                 $stmt7->bindparam("server_details", $server_details);
                                 $stmt7->execute();

                        }
                      
        }




        

        $stmt = $db->prepare("SELECT user,pass,id,name,status FROM add_cust WHERE (user=:username) AND (pass=:hash_password) ");

        $stmt->bindparam("username", $user);

        $stmt->bindparam("hash_password", $hash_password);

        $stmt->execute();

        

        $userdetail = $stmt->fetch(PDO::FETCH_ASSOC);

    

        $current_status = $userdetail['status'];

       

        if ($stmt->rowCount() > 0 && $current_status==$status) {

         

            /*

            $_SESSION['userid'] = $userdetail['id'];

            $_SESSION['rname'] = $userdetail['name'];

            

            $_SESSION['starts'] = time(); // taking now logged in time.

                                          // Ending a session in 35 minutes from the starting time.

            $_SESSION['expires'] = $_SESSION['starts'] + (35 * 60);

            

            */

            

            // add user login info on login success

          /*  $stmt = $db->prepare("INSERT INTO login_detail(user_id,ip_address,method,status) VALUES(:userid,:serverip,:web,:message)");

            $stmt->bindparam(":userid", $userdetail['id']);

            $stmt->bindparam(":serverip", $_SERVER['REMOTE_ADDR']);

            $stmt->bindparam(":web", $web);

            $stmt->bindparam(":message", $success);

            $stmt->execute();

            */

            

            /*

             * echo '<script type="text/javascript">

             * $(document).ready(function(){

             * swal({

             * title: "Please Wait",

             * text: "Your request is being processed",

             * type: "success",

             * allowEscapeKey : false,

             * showConfirmButton : false

             * },

             * function(){

             * //event to perform on click of ok button of sweetalert

             *

             * });

             * });

             * </script>';

             *

             * echo "<meta http-equiv='refresh' content='1;url=r_admin/mobile_recharge.php'>";

             */


            $stmt3 = $db->prepare("UPDATE threat_attempt SET password_attempt = 0 WHERE user_id = :userid ");
            $stmt3->bindparam(":userid", $id1);
            $stmt3->execute();

            $response['status'] = 1;

            $response['msg'] = "Please Wait";

        } else {

            // add user login info on login failure

          /*  $stmt = $db->prepare("INSERT INTO login_detail(user_id,ip_address,method,status) VALUES(:userid,:serverip,:web,:message)");

            $stmt->bindparam(":userid", $userdetail['id']);

            $stmt->bindparam(":serverip", $_SERVER['REMOTE_ADDR']);

            $stmt->bindparam(":web", $web);

            $stmt->bindparam(":message", $message);

            $stmt->execute();

            */

            

            // echo "<script>alert('invalid username or password')</script>";

            

            if($current_status=="DISABLED" )

            {

              $response['status'] = 0;

              $response['msg'] = "Your account has been deactivated!. Please contact to support";  

            }

            else

            {

            $response['status'] = 0;

            $response['msg'] = "Invalid username or password!";

            }

        }

    } catch (PDOException $e) {

        // echo "Please check login details.";

        $response['status'] = 0;

        $response['msg'] = "Please check login details!";

    }

}

else {

    $response['status'] = 0;

    $response['msg'] = "Username or password empty!";

    

}

echo json_encode($response);

?>