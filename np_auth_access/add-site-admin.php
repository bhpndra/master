<?php
include_once('inc/head.php');
include_once('inc/header.php');

?>
<?php
	$helpers = new helper_class();
	$post = $helpers->clearSlashes($_POST);
	$mysqlClass = new Mysql_class();
	//print_r($post);
	
	if($_SESSION[_session_usertype_]=="B2B"){
		$usersCount = $mysqlClass->countRows("select id from add_cust  where 1 and admin_id = '".$_SESSION[_session_userid_]."' and usertype = 'WL' ");
		if($usersCount > 0){
			echo "<script> window.history.back(); </script>"; die();
		}
	}
	
	
	if(isset($_POST['submit'])){
		
		if (strpos($post['domain'], 'http://') !== false) {
			$post['domain'] = str_replace("http://","https://",$post['domain']);
		}
		if (strpos($post['domain'], 'https://') === false) {
			$post['domain'] = "https://".$post['domain'];
		}
		if(substr($post['domain'], -1)=='/'){
			$post['domain'] = str_replace("www.","",$post['domain']);
		} else {
			$post['domain'] = str_replace("www.","",$post['domain']).'/';
		} 
		$post['domain'] = strtolower($post['domain']);
		
		$hash_pass = $helpers->hashPassword($post['pass']);
		$spin = $helpers->hashPin($post['s_pin']);
				
		//echo "select * from `add_cust` WHERE `email`='".$post['email']."' OR `user`='".$post['user']."' OR `mobile`='".$post['mobile']."'";
		$que = $mysqlClass->mysqlQuery("select * from `add_cust` WHERE `email`='".$post['email']."' OR `user`='".$post['user']."' OR `mobile`='".$post['mobile']."'");
		$resAAK = $mysqlClass->mysqlQuery("select api_access_key from `admin` WHERE `id`='".$_SESSION[_session_userid_]."' and `api_access_key`!=''" )->fetch(PDO::FETCH_ASSOC);
		
		if($que->rowCount()>0){
			
			@$msg .= "<div class=\"alert alert-danger alert-dismissable\">";
				$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
							&times;
					  </button>";
				$msg .= "Error! User already exist with email/user/mobile.";
			$msg .= "</div>";	
			
		} else {
			
			if(!empty($resAAK['api_access_key'])){
				if(isset($_FILES['aadhar_file']['name']) && !empty($_FILES['aadhar_file']['name'])){
					$aadharFileUpload = $helpers->fileUpload($_FILES["aadhar_file"],"../uploads/kyc-doc/wl/",$post['aadhar_no']);
					if($aadharFileUpload['type']=="success"){
						$aadhar_file_name = $aadharFileUpload['filename'];
					} else {
						echo "<script> alert('".$aadharFileUpload['message']."'); </script>";
					}
				} else {
					$aadhar_file_name = '';
				}
				
				if(isset($_FILES['pan_file']['name']) && !empty($_FILES['pan_file']['name'])){
					$panFileUpload = $helpers->fileUpload($_FILES["pan_file"],"../uploads/kyc-doc/wl/",$post['pan_no']);
					if($panFileUpload['type']=="success"){
						$pan_file_name = $panFileUpload['filename'];
					} else {
						echo "<script> alert('".$panFileUpload['message']."'); </script>";
					}
				} else {
					$pan_file_name = '';
				}
				
				$value = array(
					'name' 					=> $post['name'],
					'user' 					=> $post['user'],
					'mobile' 				=> $post['mobile'],
					'pass' 					=> $hash_pass['encrypted'],
					'security_pin' 			=> $spin['encrypted'],
					'email' 				=> $post['email'],
					'cname'	 				=> $post['cname'],
					'city' 					=> $post['city'],
					'state' 				=> $post['state'],
					'pin' 					=> $post['pin'],
					'aadhar_no'	    		=> $post['aadhar_no'],
					'pan_no'	    		=> $post['pan_no'],
					'aadhar_file'	    	=> $aadhar_file_name,
					'pan_file'	    		=> $pan_file_name,
					'address' 	    	    => $post['address'],
					'package_id' 	    	=> $post['package'],
					'status' 	    	    => $post['status'],
					'service_access'	    => implode(",",$post['service_access']),
					'api_access_key'	    => $resAAK['api_access_key'],
					'usertype'	    		=> "WL",				
					'created_on' 		    => date("Y-m-d H:i:sa"),
					'created_by' 		    => 'ADMIN',
					'creator_id' 		    => @$_SESSION[_session_userid_],
					'admin_id' 		    	=> @$_SESSION[_session_userid_],
					'number_of_child_limi'	=> $post['number_of_child']
				);
			
				$last_id = $mysqlClass->insertData(' add_cust ', $value);
				if($last_id>0){
						$dValue = array(						
							'wl_id' => $last_id
						);
						$mysqlClass->updateData(' add_cust ', $dValue, " where id = '".$last_id."' ");
						$dValue = array(
							'user_id' => $last_id,
							'status' => 'Active',
							'created_by' => 'ADMIN',
							'creator_id' => $_SESSION[_session_userid_],
							'package_id' => $post['package'],
							'domain' => $post['domain']
						);
						$mysqlClass->insertData(' add_white_label ', $dValue);
					@$msg .= "<div class=\"alert alert-success alert-dismissable\">";
						$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
									&times;
							  </button>";
						$msg .= "New User created successfully.";
					$msg .= "</div>";
					$valueSetting = array(
						'site_title' => $post['cname'],
						'site_name' => $post['cname'],
						'email' => $post['email'],
						'support_number' => $post['mobile'],
						'address' => $post['address'],
						'user_id' => $last_id,
						'user_type' => 'WL',
						'template' => 1,
						'color_code' => '#0f6fd5',
					);
					$mysqlClass->insertData(' general_settings ', $valueSetting);
					
				}
			} else {
				@$msg .= "<div class=\"alert alert-success alert-dismissable\">";
					$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
								&times;
						  </button>";
					$msg .= "Your API access key not set.";
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
								<span><?=($_SESSION[_session_usertype_]=="B2B")? "Add Site Admin" : "Add New White Label";?></span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> <?=($_SESSION[_session_usertype_]=="B2B")? "Add Site Admin" : "Add New White Label";?> </div>
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
										<div class="form-group col-md-4" id="domain" >
											<label>Domain</label>
											<div><input type='text' name='domain'  class='form-control' placeholder='https://example.com/' required ></div>
										</div>
										<div class="form-group col-md-4">
											<label>Name</label>
											<div>
												<input type='text' name='name'  class='form-control' placeholder="Name" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>User Name</label>
											<div>
												<input type='text' name='user'  class='form-control' placeholder="User Name" required>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Mobile</label>
											<div>
												<input type='text' name='mobile'  class='form-control' placeholder="Mobile" required>
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
											<label>Security Pin</label>
											<div>
												<input type='text' name='s_pin'  class='form-control' placeholder="Security pin" required>
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
											<label>State</label>
											<div>
												<input type='text' name='state'  class='form-control' placeholder="State" >
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
										<div class="form-group col-md-12" style="display:block">
											<div class="col-md-12"><label>Services (User - Access) </label></div>
											<div class="col-md-3">
												<div class="form-control">
													<input type="checkbox" name="service_access[]" checked value="recharge"> Recharge 
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-control">
													<input type="checkbox" name="service_access[]" checked value="aeps"> Aeps 
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-control">
													<input type="checkbox" name="service_access[]" checked value="dmt"> Money Transfer
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-control">
													<input type="checkbox" name="service_access[]" checked value="bill_payment"> Bill Payment
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>Aadhar Number</label>
												<div>
													<input type='text' name='aadhar_no'  class='form-control' placeholder="Aadhar Number" value="" >
												</div>
											</div>
											<div class="form-group">
												<label>Aadhar File</label>
												<div>
													<input type='file' name='aadhar_file'  class='form-control' placeholder="GST Number" >
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>PAN Number</label>
												<div>
													<input type='text' name='pan_no'  class='form-control' placeholder="PAN Number" value="" >
												</div>
											</div>
											<div class="form-group">
												<label>PAN File</label>
												<div>
													<input type='file' name='pan_file'  class='form-control' >
												</div>
											</div>
										</div>
										
										<div class="form-group col-md-4">
											<label>Status</label>
											<div>
												<select name="status" class='form-control' required>
													<option value="ENABLED">ENABLED</option>
													<option value="DISABLED">DISABLED</option>
												</select>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Package</label>
											<div>
												<select name="package" class='form-control' required>
													<option value="">Select Package</option>
													<?php
														$utypes = $mysqlClass->fetchAllData("package_list","package_name,id", " WHERE `creator_id` = '".$_SESSION[_session_userid_]."' and created_by = '".$_SESSION[_session_usertype_]."' and created_for = 'WL'");
														foreach($utypes as $ut){
													?>
														<option value="<?=$ut['id']?>"><?=$ut['package_name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>No. of Child User Limit</label>
											<div>
												<input type='text' name='number_of_child'  class='form-control' placeholder="Number of Child User Limit" value="1000" >
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
