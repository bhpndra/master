<?php
@session_start();
include "include/connect.php";
include "include/jwt.php";
$response = array();
if (! empty($_POST)) {
    
    try {
        
        $user = trim($_POST['user']);
        $pass = trim($_POST['pass']);
        $pin  = trim($_POST['pin']);
        
        $web = "web";
        $success = "success";
        $failure = "failure";
        
        $hash_pass = hashPassword($pass);
        $hash_password =  $hash_pass['encrypted'];
        
        $hash_pin = hashPin($pin);
        $hash_spin =  $hash_pin['encrypted'];
        
        $stmt = $db->prepare("SELECT `user`,`pass`,`id`,`name`,`creator_id` FROM `add_cust` where (user=:username) and (pass=:hash_password) and (security_pin=:pin) ");
        $stmt->bindparam("username", $user);
        $stmt->bindparam("hash_password", $hash_password);
        $stmt->bindparam("pin",  $hash_spin);
        $stmt->execute();
        
        $userdetail = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $ids =    $userdetail['id'];
        $tokenId = base64_encode(random_bytes(32));
        $issuedAt = time();
        $notBefore = $issuedAt + 10;            //Adding 10 seconds
        $expire = $notBefore + 60*60*12;         // Adding 12 Hours
        $serverName = 'http://netpaisa.com'; /// set your domain name
        $secretKey = base64_decode(SECRET_KEY);
        // $dataP = ['user'=>$userdetail['user'], 'jti' => $userdetail['id'], 'nbf' => time(),  'iat'=> time()+432000];
         $dataP = [
                    'iat' => $issuedAt, // Issued at: time when the token was generated
                    'jti' => $tokenId, // Json Token Id: an unique identifier for the token
                    'iss' => $serverName, // Issuer
                    'nbf' => $notBefore, // Not before
                    'exp' => $expire, // Expire
                    'data' => [// Data related to the logged user you can set your required data
                        'id' => $userdetail['id'], // id from the users table
                        'name' => $userdetail['name'], //  name
                    ]
                ];
         $jwt_token = JWT::encode(
            $dataP,
            $secretKey,
            ALGORITHM
        );
    //echo $jwt_token; exit;
        $token = str_replace('"','',$jwt_token);
        $_SESSION["userId"] = $token; 
        
        if ($stmt->rowCount() > 0) {
            
            $stmt_wl = $db->prepare("SELECT `user_id` FROM `add_white_label` where (user_id=:userid) ");
            $stmt_wl->bindparam("userid", $ids);
            $stmt_wl->execute();
            
            if ($stmt_wl->rowCount() > 0) {
                       $stmt3 = $db->prepare("UPDATE threat_attempt SET pin_attempt = 0 WHERE user_id = :userid ");
                       $stmt3->bindparam(":userid", $ids);
                       $stmt3->execute();
            
                $wl_detail   =   $stmt_wl->fetch(PDO::FETCH_ASSOC);
                
                $_SESSION['creator_id']   =  $userdetail['creator_id'];
                $_SESSION['adminuser']   =   $userdetail['name'];
                $_SESSION['adminuserid'] =   $ids;
                
                $_SESSION['startwl'] = time(); // taking now logged in time.
                // Ending a session in 30 minutes from the starting time.
                $_SESSION['expirewl'] = $_SESSION['startwl'] + (30 * 60);
                
                $url = "admin/index1.php";
           
            } else {
                
                $stmt_dr = $db->prepare("SELECT `user_id` FROM `add_distributer` where (user_id=:userid) ");
                $stmt_dr->bindparam("userid", $ids);
                $stmt_dr->execute();
            
                if ($stmt_dr->rowCount() > 0) {
                       $stmt3 = $db->prepare("UPDATE threat_attempt SET pin_attempt = 0 WHERE user_id = :userid ");
                       $stmt3->bindparam(":userid", $ids);
                       $stmt3->execute();
                    
                    $wl_detail   =   $stmt_dr->fetch(PDO::FETCH_ASSOC);
                    
                    $_SESSION["distuserid"]  = $ids;
                    $_SESSION['name']       = $userdetail['name'];
                    
                    $_SESSION['start'] = time(); // taking now logged in time.
                    // Ending a session in 25 minutes from the starting time.
                    $_SESSION['expire'] = $_SESSION['start'] + (25 * 60);
                    $url = "d_admin/index1.php";
                    
                } else {
                  
                    $stmt_rt = $db->prepare("SELECT `user_id` FROM `add_retailer` where (user_id=:userid) ");
                    $stmt_rt->bindparam("userid", $ids);
                    $stmt_rt->execute();
                    
                    if ($stmt_rt->rowCount() > 0) {
                      // $count = $get_count['pin_attempt'] + 1;
                       $stmt3 = $db->prepare("UPDATE threat_attempt SET pin_attempt = 0 WHERE user_id = :userid ");
                       $stmt3->bindparam(":userid", $ids);
                       $stmt3->execute();
                        
                        $_SESSION['userid']      =  $ids;
                        $_SESSION['rname']       = $userdetail['name'];
                        $_SESSION['starts'] = time(); // taking now logged in time.
                        // Ending a session in 35 minutes from the starting time.
                        $_SESSION['expires'] = $_SESSION['starts'] + (35 * 60);
                        $url = "r_admin/index1.php";
                    }
                
                }
            
            }
            
            
           $REMOTE_ADDR = ( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
            // add user login info on login success
            $stmt = $db->prepare("INSERT INTO login_detail(user_id,ip_address,method,status) VALUES(:userid,:serverip,:web,:message)");
            $stmt->bindparam(":userid", $ids);
            $stmt->bindparam(":serverip", $REMOTE_ADDR);
            $stmt->bindparam(":web", $web);
            $stmt->bindparam(":message", $success);
            $stmt->execute();
           
            $response['status'] = 1;
            $response['msg']    = $url;
        } else {
            $REMOTE_ADDR = ( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
            // add user login info on login failure
            $stmt = $db->prepare("INSERT INTO login_detail(user_id,ip_address,method,status) VALUES(:userid, :serverip, :web, :message)");
            $stmt->bindparam(":userid", $userdetail['id']);
            $stmt->bindparam(":serverip", $_SERVER['REMOTE_ADDR']);
            $stmt->bindparam(":web", $web);
            $stmt->bindparam(":message", $message);
            $stmt->execute();
            
            // echo "<script>alert('invalid username or password')</script>";
            $response['status'] = 0;
            $response['msg'] = "Username or password not match!";
        }
    } catch (PDOException $e) {
        // echo "Please check login details.";
  $stmt4 = $db->prepare("SELECT id FROM add_cust where user = :username ");
  $stmt4->bindparam(":username", $user);
  $stmt4->execute();
  $get_id =  $stmt4->fetch(PDO::FETCH_ASSOC);
  $user_id = $get_id['id'] ;
   $ip = get_client_ip_new();
  $stmt1 = $db->prepare("SELECT * FROM threat_attempt where user_id = :userid ");
  $stmt1->bindparam(":userid", $user_id);
  $stmt1->execute();
  $get_count =  $stmt1->fetch(PDO::FETCH_ASSOC);
   if($get_count['pin_attempt'] == 5) {
            $stmt5 = $db->prepare("UPDATE add_cust SET status = 'DISABLED' WHERE id = :userid ");
            $stmt5->bindparam(":userid", $user_id);
            $stmt5->execute();
            $response['status'] = 0;
            $response['msg'] = "Account Blocked!!";
            echo json_encode($response);
            die();
   }
 if(empty($get_count)){
            $stmt2 = $db->prepare("INSERT INTO threat_attempt (pin_attempt,user_id,pin_ip) VALUES (1,:userid,:ip) ");
            $stmt2->bindparam(":userid", $user_id);
             $stmt2->bindparam(":ip", $ip);
            $stmt2->execute();
 } else {
            $count = $get_count['pin_attempt'] + 1;
            $stmt3 = $db->prepare("UPDATE threat_attempt SET pin_attempt = $count, pin_ip = :ip WHERE user_id = :userid ");
            $stmt3->bindparam(":userid", $user_id);
            $stmt3->bindparam(":ip", $ip);
            $stmt3->execute();
  }
        $response['status'] = 0;
        $response['msg'] = "Invalid Pin";
    }
}
else {
    $response['status'] = 0;
    $response['msg'] = "Username or password empty!";
    
}
echo json_encode($response);
?>