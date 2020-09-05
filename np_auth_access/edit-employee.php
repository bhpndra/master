<?php
include_once('inc/head.php');
include_once('inc/header.php');

?>
<?php
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	$get = $helpers->clearSlashes($_GET);
	if(isset($_POST['update'])){
		$post = $helpers->clearSlashes($_POST);
		unset($post['update']);
	
		$mysqlClass->updateData(' employee ', $post, " where id = '".$get['id']."' ");
		@$msg .= "<div class=\"alert alert-success alert-dismissable\">";
					$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
								&times;
						  </button>";
					$msg .= "Update successfully.";
				$msg .= "</div>";
		unset($_POST);
	}
	if(isset($_POST['updatePinPassword'])){
		$post = $helpers->clearSlashes($_POST);	
		
		unset($post['updatePinPassword']);		
		if(!empty($post['password'])){
			$hash_pass = $helpers->hashPassword($post['password']);
			unset($post['password']);
			$post['password'] = $hash_pass['encrypted'];			
		}
		if(!empty($post['security_pin'])){
			$spin = $helpers->hashPin($post['security_pin']);
			unset($post['security_pin']);
			$post['security_pin'] = $spin['encrypted'];
		}
	
		$mysqlClass->updateData(' employee ', $post, " where id = '".$get['id']."' ");
		echo "<script> alert('Password or Pin Update Successfully.'); </script>";
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
								<span>Update Employee</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Update Employee </div>
							</div>
							<div class="portlet-body">
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12">
								<?php
									if(@$msg!=""){
										echo $msg;
									}
								$res = $mysqlClass->get_field_data("*","employee"," where id = '".$get['id']."' ");
								?>														
                                <div class="col-md-12 ">
									<form method ="post" class="smart-form" action ="" enctype="multipart/form-data">
										<div class="form-group col-md-4">
											<label>User Type</label>
											<div>
												<select name="usertype" class='form-control' required >
													<option disabled value="">Select Type</option>
													<?php
														$utypes = $mysqlClass->fetchAllData("usertype","name,id", " where type='EMP'");
														foreach($utypes as $ut){
													?>
														<option <?php if($res['usertype']==$ut['id']){ echo "selected"; } ?> value="<?=$ut['id']?>"><?=$ut['name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Assign Access Role</label>
											<div>
												<select name="access_role" class='form-control' required >
													<option disabled value="">Select Role</option>
													<?php
														$utypes = $mysqlClass->fetchAllData("support_roles","role_name,id", " ");
														foreach($utypes as $ut){
													?>
														<option <?php if($res['access_role']==$ut['id']){ echo "selected"; } ?> value="<?=$ut['id']?>"><?=$ut['role_name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Name</label>
											<div>
												<input type='text' name='name' value="<?=$res['name']?>" class='form-control' placeholder="Name" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Mobile</label>
											<div>
												<input type='text' name='mobile' value="<?=$res['mobile']?>"  class='form-control' placeholder="Mobile" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Email</label>
											<div>
												<input type='email' name='email' value="<?=$res['email']?>"  class='form-control' placeholder="Email" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>City</label>
											<div>
												<input type='text' name='city' value="<?=$res['city']?>"  class='form-control' placeholder="City" >
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Postal Code</label>
											<div>
												<input type='text' name='pincode' value="<?=$res['pincode']?>"  class='form-control' placeholder="Postal Code" >
											</div>
										</div>
										<div class="form-group col-md-8">
											<label>Address</label>
											<div>
												<input type='text' name='address'  value="<?=$res['address']?>" class='form-control' placeholder="Address" >
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Status</label>
											<div>
												<select name="status" class='form-control' required>
													<option <?php if($res['status']=="DISABLE"){ echo "selected"; } ?> value="DISABLE">DISABLE</option>
													<option  <?php if($res['status']=="ENABLE"){ echo "selected"; } ?> value="ENABLE">ENABLE</option>
												</select>
											</div>
										</div>
										<div class="form-group col-md-12">
											<div>
												<input type="submit" name="update" class="btn btn-success" value="Update Employee">
											</div>
										</div>

									</form>
								</div>
								
								</div>
							</div>
							</div>
						</div>
						
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Update PIN and Password </div>
							</div>
							<div class="portlet-body">
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12">																					
                                <div class="col-md-12 ">
									<form method ="post" class="smart-form" action ="" enctype="multipart/form-data">
										<div class="form-group col-md-4">
											<label>Password</label>
											<div>
												<input type='text' name='password' class='form-control' placeholder="Password" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>PIN</label>
											<div>
												<input type='text' name='security_pin'  class='form-control' placeholder="PIN" >
											</div>
										</div>
										<div class="form-group col-md-12">
											<div>
												<input type="submit" name="updatePinPassword" class="btn btn-success" value="Update PIN And Password">
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
