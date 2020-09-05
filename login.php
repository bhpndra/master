<!DOCTYPE html>
<html lang="en">
<?php include('include_front/head.php');

 ?>
<link rel="stylesheet" type="text/css" id="theme" href="include_front/assist/css/sweetalert.css"/>
<body>
<div class="page-wrapper">

<body>

<div class="page-wrapper">

    <div class="preloader"></div><!-- /.preloader -->
    
    <section class="signin-wrapper min-vh-100 clearfix" style="background-image: url(include_front/assist/images/signup.jpg);">
        <div class="form-block min-vh-100">
        	<div class="login-page-logo"><img src="<?=DOMAIN_NAME?>uploads/logo/<?=$logo?>" alt="Logo"></div>
        	<div id="msg"></div>
            <form method="POST" id="loginSubmit"  >
                <input type="text" placeholder="User name"  id="userid" name="userid" required value='' />
                <input type="password" placeholder="Password"  id="password" name="password" required value='' />
				
                <a href="javascript:void()" onclick="forgotpassword();" class="forgot-password">Forgot password?</a>
                <button type="submit" class="thm-btn">Sign In</button>
                <!-- <p class="sign-up-link">Don't have an account? <a href="#">Sign up</a></p> -->
            </form>
                        
            
            <p class="copy-text">© Copyright 2019 by <a href="#"><?php echo $gene_info['copyright']; ?></a></p>
        </div><!-- /.form-block -->
        <div class="background-block min-vh-100" style="background-image: url(include_front/assist/images/signup.jpg);">
            
        </div><!-- /.background-block -->
    </section><!-- /.signin-wrapper -->
    

</div><!-- /.page-wrapper -->



<script src="include_front/assist/js/jquery.js"></script>
<script src="include_front/assist/js/bootstrap.bundle.min.js"></script>
<script src="include_front/assist/js/jquery.magnific-popup.min.js"></script>
<script src="include_front/assist/js/owl.carousel.min.js"></script>
<script src="include_front/assist/js/isotope.js"></script>
<script src="include_front/assist/js/bootstrap-select.min.js"></script>
<script src="include_front/assist/js/jquery.bxslider.min.js"></script>
<script src="include_front/assist/js/theme.js"></script>
<script src="include_front/assist/js/sweetalert.js"/></script>
<!--<script src="login.js"/></script>-->
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
						swal({
						  title: "Error",
						  text: data.MESSAGE,
						  type: "error"
						});
					}
				}
			});            
        } else {
			//alert('');
		}
    });
	
	function varify_pin(userid,password){
		swal({
			  title: "Enter Your Securty Pin",
			  text: "",
			  type: "input",
			  showCancelButton: true,
			  closeOnConfirm: false,
			  animation: "slide-from-top",
			  inputPlaceholder: "Enter Your Securty Pin"
			},
			function(inputValue){
			  if (inputValue === false) return false;			  
			  if (inputValue === "") {
				swal.showInputError("You need to enter securty Pin!");
				return false
			  } else {
				  $.ajax({
					url :  '<?=DOMAIN_NAME?>api/users/verify_pin.php',
					type:  'POST',
					cache: false,
					data :  {userid:userid, password:password, pin:inputValue, login:'web', domain: '<?=DOMAIN_NAME?>'},
					dataTpe : "json",
					"headers": {
						"x-api-key": "<?=HTTP_X_API_KEY?>",
						"NETPAISAPASSKEY": "<?=NETPAISAPASSKEY?>"
					},
					success:function(response)
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
								if(data.SW_TYPE=="B2B"){
									location.replace('<?=DOMAIN_NAME?>sw_admin/dashboard');
								} else {
									location.replace('<?=DOMAIN_NAME?>admin/dashboard');
								}
							} else {
								swal.showInputError('User Role Not Defined');
							}
						} else {
							swal.showInputError(data.MESSAGE);
						}
					}
				});
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
					"headers": {
						"x-api-key": "<?=HTTP_X_API_KEY?>",
						"NETPAISAPASSKEY": "<?=NETPAISAPASSKEY?>"
					},
					success:function(response)
					{
						//console.log(data);			   
						var data = JSON.parse(response);
						if(parseInt(data.ERROR_CODE) == 0){
							sweetAlert("SMS Sent", data.MESSAGE, "success");
						} else {
							swal.showInputError(data.MESSAGE);
						}
					}
				});
			  }			  
			  
			}); 
		}
</script>
</body>
</html>