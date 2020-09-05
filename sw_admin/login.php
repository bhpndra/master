<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("../include/connect.php"); ?>

<?php
	$server_name = DOMAIN_NAME;
	/* $error = '';
	if(isset($_POST['submit'])){
		$login_fields = array("userid"=>$_POST['userid'],"password"=>$_POST['password'],"domain"=>$server_name,"login"=>"web");
		$urlLogin = BASE_URL."/api/users/login.php";
		$responseUserDetails = api_curl($urlLogin,$login_fields,$headerArray);
		$resULDetails = json_decode($responseUserDetails,true);	
		if(isset($resULDetails) && $resULDetails['ERROR_CODE']==1){
			$error = $resULDetails['MESSAGE'];
		}
	}

	if(isset($_POST['submitPin'])){
		$login_fields2 = array("userid"=>$_POST['userid'],"password"=>$_POST['password'],"pin"=>$_POST['spin'],"domain"=>$server_name,"login"=>"web");
		$urlVerifyPin = BASE_URL."/api/users/verify_pin.php";
		$responseUVDetails = api_curl($urlVerifyPin,$login_fields2,$headerArray);
		$resUVDetails = json_decode($responseUVDetails,true);
		
		if(isset($resUVDetails) && $resUVDetails['ERROR_CODE']==0){
			if($resUVDetails['USER_TYPE']=='WL'){  
						//session_start();
						//$_SESSION['USERTYPE'] = 'B2B';
						print_r($resUVDetails);
			} else {
				print_r($resUVDetails);
			}			
		} else {
			$error = $resUVDetails['MESSAGE'];
		}
	} */
	/****************************
	 * get user id (white label)
	 * ***************************/
	$userqr = mysqli_query($conn, "SELECT * FROM `add_white_label` WHERE `domain`='$server_name'");
	if (mysqli_num_rows($userqr) > 0) {
		$users = mysqli_fetch_array($userqr);
		$wl_id = $users['user_id'];
	} else {
		echo '<script>window.location.href="../404.html"</script>';
		die();
	}
	/*********************************
	 * general settings
	 * *********************************/
	$generalqr = mysqli_query($conn, "SELECT logo,site_name,site_title FROM `general_settings` WHERE `user_id`='" . $wl_id . "' && `user_type`='WL'");
	if (mysqli_num_rows($generalqr) > 0) {
		$gene_info = mysqli_fetch_array($generalqr);
		
		$site_title   = $gene_info['site_title'];
		$site_name   = $gene_info['site_name'];
		$logo         = DOMAIN_NAME. 'uploads/logo/'.$gene_info['logo'];
		
	}
	/*********************************************
	 * get white label user email and contact no
	 * ********************************************/

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title><?=$site_title?> | Dashboard</title>

  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/sweetalert2/sweetalert2.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <link rel="stylesheet" href="<?=DOMAIN_NAME?>dashboard/dist/css/style.css">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
	<img src="<?=$logo?>" style="width: 100px;" /><br/>
    <a href="<?=DOMAIN_NAME?>"><b><?=$site_name?></b></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">B2B Software Admin Login</p>
	   <form action="" method="post" id="loginSubmit">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="userid" id="userid" value="" placeholder="User Id">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" id="password" value="" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">           
              <label for="remember">
                Forgot Password
              </label>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" name="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->
<script src="<?=DOMAIN_NAME?>dashboard/plugins/jquery/jquery.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?=DOMAIN_NAME?>dashboard/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?=DOMAIN_NAME?>dashboard/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=DOMAIN_NAME?>dashboard/dist/js/adminlte.min.js"></script>
<script>
$(document).ready(function(){
    $('#loginSubmit').on('submit', function(e){
        e.preventDefault();
        var userid = $('#userid').val();
        var password = $('#password').val();
        if (userid!='' && password != '') {
			$.ajax({
				url :  '<?=DOMAIN_NAME?>api/users/login.php',
				type:  'POST',
				cache: false,
				data :  {userid:userid, password:password, login:'web', domain: '<?=DOMAIN_NAME?>'},
				dataTpe : "json",
				"headers": {
					"x-api-key": "<?=HTTP_X_API_KEY?>",
					"NETPAISAPASSKEY": "<?=NETPAISAPASSKEY?>"
				},
				success:function(response)
				{
					console.log(data);			   
					var data = JSON.parse(response);
					if(parseInt(data.ERROR_CODE) == 0 && data.MESSAGE == "UserId and Password Valid."){
						varify_pin(userid,password);
					} else {
						Swal.fire(data.MESSAGE);
					}
				}
			});            
        } else {
			//alert('');
		}
    });
	
	function varify_pin(userid,password){
		 Swal.fire({
				title: "Enter Your Securty Pin",
				input: 'password',
				inputAttributes: {
					autocapitalize: 'off'
				},
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Submit",
				closeOnConfirm: false,
				inputValidator: (value) => {
					return !value && 'You need to write something!'
				  }
			}).then((result) => { 
				if (result.value) { //alert(result.value);
					$.ajax({
						url: '<?=DOMAIN_NAME?>api/users/verify_pin.php',
						type: 'POST',
						cache: false,
						data: {userid:userid, password:password, pin:result.value, login:'web', domain: '<?=DOMAIN_NAME?>'},
						dataTpe: "json",
						"headers": {
							"x-api-key": "<?=HTTP_X_API_KEY?>",
							"NETPAISAPASSKEY": "<?=NETPAISAPASSKEY?>"
						},
						success: function (response)
						{
							console.log(data);			   
							var data = JSON.parse(response);
							if(parseInt(data.ERROR_CODE) == 0){
								if(data.USER_TYPE=="RETAILER"){
									location.replace('<?=DOMAIN_NAME?>retailer/dashboard');
								}
								else if(data.USER_TYPE=="DISTRIBUTOR"){
									location.replace('<?=DOMAIN_NAME?>distributor/dashboard');
								}
								else if(data.USER_TYPE=="WL"){
									location.replace('<?=DOMAIN_NAME?>sw_admin/dashboard');
								} else {
									swal.showInputError('User Role Not Defined');
								}
							} else {
								Swal.fire(data.MESSAGE);
							}
						}
					});
				} else {
					Swal.fire('Enter Valid PIN)')
				}
			});
		
			$(".sweet-alert > fieldset").find("input").attr("type", "password");
	}
});
function forgotpassword(){ //alert();
			swal({
			  title: "Enter Your Mobile Number",
			  text: "",
			  type: "input",
			  showCancelButton: true,
			  closeOnConfirm: false,
			  confirmButtonColor: "#ed7d00",
			  animation: "slide-from-top",
			  inputPlaceholder: "Enter Your Mobile Number"
			},
			function(inputValue){
			  if (inputValue === false) return false;			  
			  if (inputValue === "") {
				swal.showInputError("You need to enter mobile number!");
				return false
			  } else {
				  $.ajax({
					url :  '<?=DOMAIN_NAME?>api/users/login_forgot.php',
					type:  'POST',
					cache: false,
					data :  {mobile:inputValue,domain: '<?=DOMAIN_NAME?>', login:'web'},
					dataTpe : "json",
					"headers": {
						"x-api-key": "<?=HTTP_X_API_KEY?>",
						"NETPAISAPASSKEY": "<?=NETPAISAPASSKEY?>"
					},
					success:function(response)
					{
						//console.log(data);			   
						var data = JSON.parse(response);
						if(parseInt(data.ERROR_CODE) == 0){
							sweetAlert("SMS Sent", data.msg, "success");
						} else {
							swal.showInputError(data.MESSAGE);
						}
					}
				});
			  }			  
			  
			}); 
		}
</script>
</html>
