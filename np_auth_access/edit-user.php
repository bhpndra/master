<?php
include_once('inc/head.php');
include_once('inc/header.php');

include_once('classes/user_class.php');
?>
<?php
	$helpers = new helper_class();
	$post = $helpers->clearSlashes($_POST);
	$mysqlClass = new Mysql_class();
	$userClass = new User_class();
	
	$get = $helpers->clearSlashes($_GET);
	$uid = base64_decode($get['uid']);
	//print_r($post);
	if(isset($_POST['submit'])){
				
		if(isset($_FILES['aadhar_file']['name']) && !empty($_FILES['aadhar_file']['name'])){
			$aadharFileUpload = $helpers->fileUpload($_FILES["aadhar_file"],"../uploads/kyc-doc/wl/",$post['aadhar_no']);
			if($aadharFileUpload['type']=="success"){
				$aadhar_file_name = $aadharFileUpload['filename'];
			} else {
				echo "<script> alert('".$aadharFileUpload['message']."'); </script>";
			}
		} else {
			$aadhar_file_name = $post['old_aadhar_file'];
		}
		
		if(isset($_FILES['pan_file']['name']) && !empty($_FILES['pan_file']['name'])){
			$panFileUpload = $helpers->fileUpload($_FILES["pan_file"],"../uploads/kyc-doc/wl/",$post['pan_no']);
			if($panFileUpload['type']=="success"){
				$pan_file_name = $panFileUpload['filename'];
			} else {
				echo "<script> alert('".$panFileUpload['message']."'); </script>";
			}
		} else {
			$pan_file_name = $post['old_pan_file'];
		}	
			
			$value = array(
				'name' 					=> $post['name'],
				'cname'	 				=> $post['cname'],
				'city' 					=> $post['city'],
				'pin' 					=> $post['pin'],
				'aadhar_no'	    		=> $post['aadhar_no'],
				'pan_no'	    		=> $post['pan_no'],
				'aadhar_file'	    	=> $aadhar_file_name,
				'pan_file'	    		=> $pan_file_name,
				'address' 	    	    => $post['address'],
				'package_id' 	    	=> $post['package'],
				'status' 	    	    => $post['status'],
				'service_access'	    => implode(",",$post['service_access'])
			);

			$last_id = $mysqlClass->updateData(' add_cust ', $value, " where id = '".$uid."'  and admin_id = '".$_SESSION[_session_userid_]."'");
			$dValue = array(						
				'package_id' => $post['package']
			);
			$mysqlClass->updateData(' add_white_label ', $dValue, " where user_id = '".$uid."' ");
	
			
		unset($_POST);
	}
	
	
	$resUser = $mysqlClass->get_field_data(" * ", "`add_cust`", " WHERE `id`='".$uid."' and admin_id = '".$_SESSION[_session_userid_]."'");
	//print_r($resUser);

	$service_access = explode(",", $resUser['service_access']);

	//print_r($service_access);
if(!isset($resUser['id'])){
	echo "<script> window.location = 'all-white-label.php'; </script>";
	die();
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
								<span>Update User</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Update User </div>
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
											<label>Name</label>
											<div>
												<input type='text' name='name'  value="<?=$resUser['name']?>" class='form-control' placeholder="Name" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>User Name</label>
											<div>
												<input type='text' name='user' value="<?=$resUser['user']?>" class='form-control' placeholder="User Name" readonly>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Mobile</label>
											<div>
												<input type='text' name='mobile' value="<?=$resUser['mobile']?>"  class='form-control' placeholder="Mobile" readonly  >
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Email</label>
											<div>
												<input type='email' name='email'  value="<?=$resUser['email']?>" class='form-control' placeholder="Email" readonly>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Company Name</label>
											<div>
												<input type='text' name='cname'  value="<?=$resUser['cname']?>" class='form-control' placeholder="Company Name" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>City</label>
											<div>
												<input type='text' name='city'  value="<?=$resUser['city']?>" class='form-control' placeholder="City" >
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Postal Code</label>
											<div>
												<input type='text' name='pin' value="<?=$resUser['pin']?>" class='form-control' placeholder="Postal Code" >
											</div>
										</div>
										<div class="form-group col-md-8">
											<label>Address</label>
											<div>
												<input type='text' name='address' value="<?=$resUser['address']?>" class='form-control' placeholder="Address" >
											</div>
										</div>
										<div class="form-group col-md-12" >
											<div class="col-md-12"><label>Services (User - Access) </label> </div>
											<div class="col-md-3">
												<div class="form-control">
													<input type="checkbox" name="service_access[]"  value="recharge" <?=(in_array('recharge',$service_access)) ? 'checked' : ''; ?> /> Recharge 
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-control">
													<input type="checkbox" name="service_access[]"  value="aeps" <?=(in_array('aeps',$service_access)) ? 'checked' : ''; ?> /> Aeps 
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-control">
													<input type="checkbox" name="service_access[]"  value="dmt" <?=(in_array('dmt',$service_access)) ? 'checked' : ''; ?> /> Money Transfer
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-control">
													<input type="checkbox" name="service_access[]"  value="bill_payment" <?=(in_array('bill_payment',$service_access)) ? 'checked' : ''; ?> /> Bill Payment
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>Aadhar Number</label>
												<div>
													<input type='text' name='aadhar_no'  class='form-control' placeholder="Aadhar Number" value="<?=$resUser['aadhar_no']?>" >
												</div>
											</div>
											<div class="form-group">
												<label>Aadhar File</label>
												<div>
													<input type='file' name='aadhar_file'  class='form-control' placeholder="GST Number" >
													<br/>
													<img src="../uploads/kyc-doc/wl/<?=$resUser['aadhar_file']?>" height="120px" />
													<input type='hidden' value="<?=$resUser['aadhar_file']?>" name='old_aadhar_file'  class='form-control' />
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>PAN Number</label>
												<div>
													<input type='text' name='pan_no'  class='form-control' placeholder="PAN Number" value="<?=$resUser['pan_no']?>" >
												</div>
											</div>
											<div class="form-group">
												<label>PAN File</label>
												<div>
													<input type='file' name='pan_file'  class='form-control' >
													<br/>
													<img src="../uploads/kyc-doc/wl/<?=$resUser['pan_file']?>" height="120px" />
													<input type='hidden' value="<?=$resUser['pan_file']?>" name='old_pan_file'  class='form-control' />
												</div>
											</div>
										</div>
										
										
										<div class="form-group col-md-4">
											<label>Status</label>
											<div>
												<select name="status" class='form-control' required>
													<option <?php if($resUser['status']=="ENABLED"){ echo "selected"; }  ?> value="ENABLED">ENABLED</option>
													<option <?php if($resUser['status']=="DISABLED"){ echo "selected"; }  ?> value="DISABLED">DISABLED</option>
												</select>
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
														$utypes = $mysqlClass->fetchAllData("package_list","package_name,id", " WHERE `creator_id` = '".$_SESSION[_session_userid_]."' and created_for = 'WL'");
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
	function selectUserType(val){
		if(val==5){
			document.getElementById('domain').innerHTML ="<label>Domain</label><div><input type='text' name='domain'  class='form-control' placeholder='Domain' required ></div>";
		} else {
			document.getElementById('domain').innerHTML ="";
		}
	}
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
