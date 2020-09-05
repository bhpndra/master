<?php
	include_once("classes/nps_db_connection.php");
	include_once("classes/comman_class.php");
	
	if(isset($_POST['login'])){
		$helper = new helper_class();
		$post = $helper->clearSlashes($_POST);
		$mysqlObj = new Mysql_class();
		
		$hash_pass = $helper->hashPassword($post['password']);
        $hash_password =  $hash_pass['encrypted'];
        
        $database = new Database();
		$db = $database->getConnection_2();
		$mysqlObj->db_conn = $db;
			
		$checkUser = $mysqlObj->mysqlQuery("SELECT id,user,pass,usertype,name,access_role FROM add_cust WHERE user='".$post['user']."' AND pass='".$hash_password."' AND usertype='1' ");
		
		if($checkUser->rowCount()>0){
		
			$user = $checkUser->fetch(PDO::FETCH_ASSOC);
			
			if( $user['pass']==$hash_password && $user['user']==$post['user'] ){
			
				$_SESSION[_session_username_]   =   $user['name'];
				$_SESSION[_session_userid_] =   $user['id'];
				$_SESSION[_session_usertype_] =   $user['usertype'];
				$_SESSION['roleAccess'] =   $user['access_role'];
				
				$_SESSION['startsu'] =  time(); // taking now logged in time. 
				// Ending a session in 30 minutes from the starting time.
				$_SESSION['expiresu'] = $_SESSION['startsu'] + (30 * 60);
				
				//add login info
				$values = array(
				"user_id" => $_SESSION[_session_userid_],
				"ip_address" => $_SERVER['REMOTE_ADDR'],
				"method" => "Web",
				"status" => "SUCCESS",
				"user_type" => "ADMIN"
				);
				$mysqlObj->insertData('login_detail', $values);
				
				//echo "<script>alert('log in successful')</script>";	
				echo "<script>window.location.href='index.php'</script>";
				
			}
		} else {
			//add failed info
			$values = array(
			"user_id" => $_SESSION[_session_userid_],
			"ip_address" => $_SERVER['REMOTE_ADDR'],
			"method" => "Web",
			"status" => "FAILURE",
			"user_type" => "ADMIN"
			);
			$mysqlObj->insertData('login_detail', $values);
			echo "<script>alert('invalid username or password')</script>";
		}
	}
	
	
	
?>
<!DOCTYPE html>
<html lang="en" class="body-full-height">
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
	<head>        
        <!-- META SECTION -->
	<title>Admin Login</title>            
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<!-- END META SECTION -->
	
	<!-- CSS INCLUDE -->        
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">	
	<link rel="stylesheet" type="text/css" id="theme" href="new-css/login.style.css"/>
	<!-- EOF CSS INCLUDE -->    
	</head>
	<body>
	<div id="fb-root"></div>

	<div class="login-container">
	
	<div class="login-box animated fadeInDown">
	<div id="status">
	</div>
	<div class="login-body">
	<div class="login-title"><strong>Log In</strong> to your account</div>
	<form  class="form-horizontal" method="POST">
	<div class="form-group">
	<div class="col-md-12">
	<input type="username" required class="form-control" placeholder="user_name" name="user"/>
	</div>
	</div>
	<div class="form-group">
	<div class="col-md-12">
	<input type="password" required class="form-control" placeholder="Password" name="password"/>
	</div>
	</div>
	<div class="form-group">
	<div class="col-md-6">
	<!-- <a href="" class="btn btn-link btn-block">Forgot your password?</a>-->
	</div>
	<div class="col-md-6">
	<button type="submit" class="btn btn-info btn-block" name="login">Log In</button>
	</div>
	</div>
	
	
	
	</form>
	
	
	
	</div>
	
	</div>
	
	</div>
	
	
	</body>
	
	</html>