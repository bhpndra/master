<?php
include_once('inc/head.php');
include_once('inc/header.php');

if($_SESSION[_session_usertype_]!="ADMIN" && $_SESSION[_session_userid_]!=1){
	echo "<script> window.location = 'index.php'; </script>"; die();
}
?>
<?php
	$helpers = new helper_class();
	$post = $helpers->clearSlashes($_POST);
	$mysqlClass = new Mysql_class();
	//print_r($post);
	if(isset($_POST['submit'])){
		$hash_pass = md5($post['pass']);
		
		$que = $mysqlClass->mysqlQuery("select suser_id from `admin` WHERE `email`='".$post['email']."' OR `suser_id`='".$post['suser_id']."' OR `mobileno`='".$post['mobileno']."'");
		
		if($que->rowCount()>0){
			
			@$msg .= "<div class=\"alert alert-danger alert-dismissable\">";
				$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
							&times;
					  </button>";
				$msg .= "Error! User already exist with Email/User id/Mobile.";
			$msg .= "</div>";	
			
		} else {
			
			
			$value = array(
				'suser_id' 				=> $post['suser_id'],
				'pass' 					=> $hash_pass,
				'name' 				=> $post['name'],
				'email' 				=> $post['email'],
				'address' 				=> $post['address'],
				'cname' 				=> $post['cname'],
				'city' 				=> $post['city'],
				'mobileno' 				=> $post['mobileno'],
				'pin' 				=> $post['pin'],
				'userType' 				=> $post['userType'],
				'api_access_key' 				=> $post['api_access_key'],
				'package_id' 				=> $post['package'],
				'create_on' 				=> date("Y-m-d H:i:sa")
			);
			
			$last_id = $mysqlClass->insertData(' admin ', $value);
			if($last_id>0){

				@$msg .= "<div class=\"alert alert-success alert-dismissable\">";
					$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
								&times;
						  </button>";
					$msg .= "New User created successfully.";
				$msg .= "</div>";
			}
		}
		
		unset($_POST);
	}
?>
<!-- MAIN PANEL -->
<div class="page-wrapper-row full-height">
	<div class="page-wrapper-middle">
		<!-- BEGIN CONTAINER -->
		<div class="page-container">
			<!-- BEGIN CONTENT -->
			<div class="page-content-wrapper">
				<!-- BEGIN CONTENT BODY -->
				<!-- BEGIN PAGE HEAD-->
				
				<!-- END PAGE HEAD-->
				<!-- BEGIN PAGE CONTENT BODY -->
				<div class="page-content">
					<div class="container">
						<!-- BEGIN PAGE BREADCRUMBS -->
						<ul class="page-breadcrumb breadcrumb">
							<li>
								<a href="index.html">Home</a>
								<i class="fa fa-circle"></i>
							</li>
							<li>
								<span>Add New User</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Add New User </div>
							</div>
							<div class="portlet-body">
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12">
								<?php
									if(@$msg!=""){
										echo $msg;
									}
								
								?>														
                                <div class="col-md-12 ">
									<form method ="post" class="smart-form" action ="" enctype="multipart/form-data">
										<div class="form-group col-md-4">
											<label>User Type</label>
											<div>
												<select name="userType" class='form-control' required onchange="selectUserType(this.value)">
													<option value="">Select Type</option>					
														<option value="B2B">B2B Software</option>
														<option value="RESELLER">Reseller Software</option>
												</select>
											</div>
										</div><div class="form-group col-md-4">
											<label>API Access Key</label>
											<div>
												<input type='text' name='api_access_key'  class='form-control' placeholder="API Access Key" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Name</label>
											<div>
												<input type='text' name='name'  class='form-control' placeholder="Name" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>User Id (Login Id)</label>
											<div>
												<input type='text' name='suser_id'  class='form-control' placeholder="User Name" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Mobile</label>
											<div>
												<input type='text' name='mobileno'  class='form-control' placeholder="Mobile" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Password</label>
											<div>
												<input type='password' name='pass' id="pass" class='form-control' placeholder="Password" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Confirm Password</label>
											<div>
												<input type='password' name='cpass' id='cpass' onblur="checkConfirmPassword(this);"  class='form-control' placeholder="Confirm Password" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Email</label>
											<div>
												<input type='email' name='email'  class='form-control' placeholder="Email" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Company Name</label>
											<div>
												<input type='text' name='cname'  class='form-control' placeholder="Company Name" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>City</label>
											<div>
												<input type='text' name='city'  class='form-control' placeholder="City" >
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Postal Code</label>
											<div>
												<input type='text' name='pin'  class='form-control' placeholder="Postal Code" >
											</div>
										</div>
										<div class="form-group col-md-8">
											<label>Address</label>
											<div>
												<input type='text' name='address'  class='form-control' placeholder="Address" >
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Package</label>
											<div>
												<select name="package" class='form-control' required>
													<option value="">Select Package</option>
													<?php
														$utypes = $mysqlClass->fetchAllData("package_list","package_name,id", " WHERE `creator_id` = '".$_SESSION[_session_userid_]."' and created_by = 'ADMIN' and created_for = 'SOFTWARE'");
														foreach($utypes as $ut){
													?>
														<option value="<?=$ut['id']?>"><?=$ut['package_name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group col-md-12">
											<div>
												<input type="submit" name="submit" class="btn btn-success" value="Add">
											</div>
										</div>

									</form>
								</div>
								
								</div>
							</div>
							</div>
						</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include_once('inc/footer.php'); ?>
<script>

	function checkConfirmPassword(confirm){
		var pass = document.getElementById('pass').value;
		if(confirm.value != pass){
				alert("Password not match !");
				document.getElementById('pass').value = '';
				confirm.value = '';
		}
	}
</script>
</body>
</html>
