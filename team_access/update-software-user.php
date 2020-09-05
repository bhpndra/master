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
	$get = $helpers->clearSlashes($_GET);
	$mysqlClass = new Mysql_class();
	//print_r($post);
	if(isset($_POST['submit'])){
		
		$que = $mysqlClass->mysqlQuery("select suser_id from `admin` WHERE (`email`='".$post['email']."' OR `suser_id`='".$post['suser_id']."' OR `mobileno`='".$post['mobileno']."' ) and id != '".$get['id']."'");
		
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
				'name' 				=> $post['name'],
				'email' 				=> $post['email'],
				'address' 				=> $post['address'],
				'cname' 				=> $post['cname'],
				'city' 				=> $post['city'],
				'mobileno' 				=> $post['mobileno'],
				'pin' 				=> $post['pin'],
				'userType' 				=> $post['userType'],
				'package_id' 				=> $post['package'],
				'api_access_key' 				=> $post['api_access_key']
			);
			
			$last_id = $mysqlClass->updateData(' admin ', $value, " where id = '".$get['id']."' ");
			if($last_id>0){

				@$msg .= "<div class=\"alert alert-success alert-dismissable\">";
					$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
								&times;
						  </button>";
					$msg .= "User updated successfully.";
				$msg .= "</div>";
			}
		}
		
		unset($_POST);
	}
	
	$resUser = $mysqlClass->get_field_data(" * ", "`admin`", " WHERE `id`='".$get['id']."' ");
	
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
								<span>Update User User</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Update User User </div>
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
												<?php
													$u_select = $resUser['userType'];
													$$u_select = 'selected';
												?>
													<option value="">Select Type</option>					
														<option <?=@$B2B?> value="B2B">B2B Software</option>
														<option <?=@$RESELLER?> value="RESELLER">Reseller Software</option>
												</select>
											</div>
										</div><div class="form-group col-md-4">
											<label>API Access Key</label>
											<div>
												<input type='text' name='api_access_key'  class='form-control' value="<?=$resUser['api_access_key']?>" placeholder="API Access Key" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Name</label>
											<div>
												<input type='text' name='name'  class='form-control' value="<?=$resUser['name']?>" placeholder="Name" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>User Id (Login Id)</label>
											<div>
												<input type='text' name='suser_id'  class='form-control' value="<?=$resUser['suser_id']?>" placeholder="User Name" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Mobile</label>
											<div>
												<input type='text' name='mobileno'  class='form-control' value="<?=$resUser['mobileno']?>" placeholder="Mobile" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Email</label>
											<div>
												<input type='email' name='email'  class='form-control' value="<?=$resUser['email']?>" placeholder="Email" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Company Name</label>
											<div>
												<input type='text' name='cname'  class='form-control' value="<?=$resUser['cname']?>" placeholder="Company Name" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>City</label>
											<div>
												<input type='text' name='city'  class='form-control' value="<?=$resUser['city']?>" placeholder="City" >
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Postal Code</label>
											<div>
												<input type='text' name='pin'  class='form-control' value="<?=$resUser['pin']?>" placeholder="Postal Code" >
											</div>
										</div>
										<div class="form-group col-md-8">
											<label>Address</label>
											<div>
												<input type='text' name='address'  class='form-control' value="<?=$resUser['address']?>" placeholder="Address" >
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Package</label>
											<div>
											<?php
												$p_select = $resUser['package_id'];
												${"a".$p_select} = 'selected';
											?>
												<select name="package" class='form-control' required>
													<option value="">Select Package</option>
													<?php
														$utypes = $mysqlClass->fetchAllData("package_list","package_name,id", " WHERE `creator_id` = '".$_SESSION[_session_userid_]."' and created_by = 'ADMIN' and created_for = 'SOFTWARE'");
														foreach($utypes as $ut){
													?>
														<option <?=@${"a".$ut['id']}?> value="<?=$ut['id']?>"><?=$ut['package_name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group col-md-12">
											<div>
												<input type="submit" name="submit" class="btn btn-success" value="Update">
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
