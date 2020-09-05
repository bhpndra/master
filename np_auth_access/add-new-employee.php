<?php
include_once('inc/head.php');
include_once('inc/header.php');

?>
<?php
	$helpers = new helper_class();
	$post = $helpers->clearSlashes($_POST);
	$mysqlClass = new Mysql_class();
	//print_r($post);
	if(isset($_POST['submit'])){
		$hash_pass = $helpers->hashPassword($post['password']);
		$spin = $helpers->hashPin($post['security_pin']);
		unset($post['submit']);
		unset($post['password']);
		unset($post['cpassword']);
		unset($post['security_pin']);
		$post['password'] = $hash_pass['encrypted'];
		$post['security_pin'] = $spin['encrypted'];
				
		$que = $mysqlClass->mysqlQuery("select * from `employee` WHERE `email`='".$post['email']."' OR `mobile`='".$post['mobile']."' ");
		
		if($que->rowCount()>0){
			
			@$msg .= "<div class=\"alert alert-danger alert-dismissable\">";
				$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
							&times;
					  </button>";
				$msg .= "Error! Employee already exist with email/mobile.";
			$msg .= "</div>";	
			
		} else {
			

			
			$last_id = $mysqlClass->insertData(' employee ', $post);
			if($last_id>0){
				
				@$msg .= "<div class=\"alert alert-success alert-dismissable\">";
					$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
								&times;
						  </button>";
					$msg .= "New Employee created successfully.";
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
								<span>Add New Employee</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Add New Employee </div>
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
												<select name="usertype" class='form-control' required >
													<option value="">Select Type</option>
													<?php
														$utypes = $mysqlClass->fetchAllData("usertype","name,id", " where type='EMP'");
														foreach($utypes as $ut){
													?>
														<option value="<?=$ut['id']?>"><?=$ut['name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Assign Access Role</label>
											<div>
												<select name="access_role" class='form-control' required >
													<option value="">Select Role</option>
													<?php
														$utypes = $mysqlClass->fetchAllData("support_roles","role_name,id", " ");
														foreach($utypes as $ut){
													?>
														<option value="<?=$ut['id']?>"><?=$ut['role_name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Name</label>
											<div>
												<input type='text' name='name'  class='form-control' placeholder="Name" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Mobile</label>
											<div>
												<input type='text' name='mobile'  class='form-control' placeholder="Mobile" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Email</label>
											<div>
												<input type='email' name='email'  class='form-control' placeholder="Email" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Password</label>
											<div>
												<input type='password' name='password' id='password'  class='form-control' placeholder="Password" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Confirm Password</label>
											<div>
												<input type='password' name='cpassword' onblur="if(this.value!=document.getElementById('password').value){ alert('Password Not Matched'); this.value = '';};"  class='form-control' placeholder="Confirm Password" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Security Pin</label>
											<div>
												<input type='text' name='security_pin'  class='form-control' placeholder="Security pin" required>
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
												<input type='text' name='pincode'  class='form-control' placeholder="Postal Code" >
											</div>
										</div>
										<div class="form-group col-md-8">
											<label>Address</label>
											<div>
												<input type='text' name='address'  class='form-control' placeholder="Address" >
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Status</label>
											<div>
												<select name="status" class='form-control' required>
													<option value="DISABLE">DISABLE</option>
													<option value="ENABLE">ENABLE</option>
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

</body>
</html>
