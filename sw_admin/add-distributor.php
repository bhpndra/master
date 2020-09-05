<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>

<?php
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
$msg = '';

$limit = $mysqlClass->mysqlQuery("select number_of_child_limi from `add_cust` WHERE `id`='".$USER_ID."' and `admin_id`='".$ADMIN_ID."' and usertype = 'WL' ")->fetch(PDO::FETCH_ASSOC);
$limitError = $helpers->alert_message("No Limit to create user !","alert-danger");
if(!empty($limit['number_of_child_limi']) and $limit['number_of_child_limi'] > 0){
	$limitError = '';
}
$number_of_child_limi = $limit['number_of_child_limi'];

$post = $helpers->clearSlashes($_POST);
	if(isset($_POST['add']) && $limitError == ''){
		
		$user_capping = $post['user_capping'];
		
		if(($user_capping + 1) < $number_of_child_limi && $user_capping > 0){
			
			$number_of_child_limi = $number_of_child_limi - $user_capping;
			$hash_pass = $helpers->hashPassword($post['pass']);
			$spin = $helpers->hashPin($post['s_pin']);
					
			$que = $mysqlClass->mysqlQuery("select * from `add_cust` WHERE `email`='".$post['email']."' OR `mobile`='".$post['mobile']."'");
			$resAAK = $mysqlClass->mysqlQuery("select api_access_key from `admin` WHERE `id`='".$ADMIN_ID."' and `api_access_key`!=''" )->fetch(PDO::FETCH_ASSOC);
			
			if($que->rowCount()>0){				
				$msg  = $helpers->alert_message("Error! User already exist with email/mobile.","alert-danger");			
			} else {
				
				if(!empty($resAAK['api_access_key'])){
					if(isset($_FILES['aadhar_file']['name']) && !empty($_FILES['aadhar_file']['name'])){
						$aadharFileUpload = $helpers->fileUpload($_FILES["aadhar_file"],"../uploads/kyc-doc/",$post['aadhar_no']);
						if($aadharFileUpload['type']=="success"){
							$aadhar_file_name = $aadharFileUpload['filename'];
						} else {
							echo "<script> alert('".$aadharFileUpload['message']."'); </script>";
						}
					} else {
						$aadhar_file_name = '';
					}
					
					if(isset($_FILES['pan_file']['name']) && !empty($_FILES['pan_file']['name'])){
						$panFileUpload = $helpers->fileUpload($_FILES["pan_file"],"../uploads/kyc-doc/",$post['pan_no']);
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
						'number_of_child_limi' 	=> $user_capping,
						'api_access_key'	    => $resAAK['api_access_key'],
						'usertype'	    		=> "DISTRIBUTOR",				
						'created_on' 		    => date("Y-m-d H:i:s"),
						'created_by' 		    => 'WL',
						'creator_id' 		    => $USER_ID,
						'wl_id' 		    	=> $USER_ID,
						'admin_id' 		    	=> $ADMIN_ID
					);
				
					$last_id = $mysqlClass->insertData(' add_cust ', $value);
					if($last_id>0){
							$is_master = (isset($post['is_master']) && $post['is_master']==1) ? 1 : 0;
							if($is_master==1){
								$userId = 'MD'.$last_id;								
							} else {
								$userId = 'DT'.$last_id;								
							}
							$dValue = array(						
								'user' => $userId
							);
							$mysqlClass->updateData(' add_cust ', $dValue, " where id = '".$last_id."' ");
							$dValue = array(
								'user_id' => $last_id,
								'status' => 'Active',
								'created_by' => 'WL',
								'creator_id' => $USER_ID,
								'is_master_distributor' => $is_master,
								'package_id' => $post['package']
							);
							$mysqlClass->insertData(' add_distributer ', $dValue);
						
								/* Update User Child limit */
								$updateLimit = $number_of_child_limi- 1;
								$mysqlClass->updateData(' add_cust ', array('number_of_child_limi'=>$updateLimit), " where id = '".$USER_ID."' ");
							
						$userDetailes = "LoginId: ".$userId." Password: ".$post['pass']." PIN: ".$post['s_pin']. " ".DOMAIN_NAME;
						
						$wlDetail = $mysqlClass->mysqlQuery(" select sms_api from add_cust where id = '".$WL_ID."' ");
						$resWL = $wlDetail->fetch(PDO::FETCH_ASSOC);
					
						if($resWL['sms_api']=='CUSTOM'){
							$sms_pack = $mysqlClass->mysqlQuery("select * from `sms_pack` WHERE `user_id`='".$WL_ID."' and `admin_id`='".$ADMIN_ID."'" )->fetch(PDO::FETCH_ASSOC);
						} else {
							$sms_pack = $mysqlClass->mysqlQuery("select * from `sms_pack` WHERE `user_id`='0' and `api_for`='ADMIN' and `admin_id`='".$ADMIN_ID."'" )->fetch(PDO::FETCH_ASSOC);
							if(empty($sms_pack['id'])){
								$sms_pack = $mysqlClass->mysqlQuery("select * from `sms_pack` WHERE `user_id`='0' and `api_for`='ADMIN' and `admin_id`='1'" )->fetch(PDO::FETCH_ASSOC);
							}
						}
						$smsParameters = json_decode($sms_pack['api_parameters'],true);
						$smsParameters[$sms_pack['param_mobile_name']] = $post['mobile'];
						$smsParameters[$sms_pack['param_msg_name']] = $userDetailes;
						$smsParameters['request_type'] = $sms_pack['request_type'];
						$smsParameters['url'] = $sms_pack['url'];
						$sms_api_res =  $helpers->send_msg_dynamic($smsParameters);
						
						$helpers->flashAlert_set('user_details_set',$userDetailes);
						$helpers->flashAlert_set('new_user_set',"New User created successfully.");
						$helpers->redirect_page("all-distributor");
					}
				} else {
					$msg  = $helpers->alert_message("Your API access key not set.","alert-danger");
				}
			}
		} else {
			$msg  = $helpers->alert_message("User Capping error.","alert-danger");
		}
		
	}



?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Add Distributor</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add Distributor</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

		<div class="row">
			<div class="col-md-12">
			<?=($msg!='')? $msg : '';?>
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Add Distributor</h3>

            </div>
			<?php if($limitError==''){ ?>
			<form method="post" enctype="multipart/form-data">
            <div class="card-body row">
              <div class="form-group col-md-4">
                <label for="inputName">Name</label>
                <input type="text"  class="form-control" value="" name="name" required />
              </div>
              <div class="form-group col-md-4">
                <label for="inputName">Mobile</label>
                <input type="tel"  class="form-control" value="" name="mobile" pattern="[5-9]{1}[0-9]{9}" required >
              </div>
              <div class="form-group col-md-4">
                <label for="inputName">Email</label>
                <input type="email"  class="form-control" value="" name="email" required >
              </div>
              <div class="form-group col-md-4">
                <label for="inputName">Password</label>
                <input type="password"  class="form-control" value="" name="pass" id="pass" required >
              </div>
              <div class="form-group col-md-4">
                <label for="inputName">Confirm Password</label>
                <input type="text"  class="form-control" value="" name="cpassword" onblur="checkPassword(this)" required >
              </div>
              <div class="form-group col-md-4">
                <label for="inputName">Login Pin (Min 4 Digit)</label>
                <input type="text"  class="form-control" value="" pattern="[0-9]{4}" name="s_pin" required >
              </div>
              <div class="form-group col-md-4">
                <label for="inputName">Company/Shop Name</label>
                <input type="text"  class="form-control" value="" name="cname" required />
              </div>
              <div class="form-group col-md-4">
                <label for="inputName">City</label>
                <input type="text"  class="form-control" value="" name="city" required />
              </div>
              <div class="form-group col-md-4">
                <label for="inputName">State</label>
                <input type="text"  class="form-control" value="" name="state" required />
              </div>
              <div class="form-group col-md-8">
                <label for="inputName">Address</label>
                <input type="text"  class="form-control" value="" name="address" required />
              </div>
              <div class="form-group col-md-4">
                <label for="inputName">PIN Code</label>
                <input type="text"  class="form-control" value="" name="pin" pattern="[0-9]{6}" required />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">PAN No.</label>
                <input type="text"  class="form-control" value="" name="pan_no" required />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">PAN Image</label>
                <input type="file"  class="form-control" value="" name="pan_file" required />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Addhaar No..</label>
                <input type="text"  class="form-control" value="" name="aadhar_no" pattern="[0-9]{12}" required />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Addhaar Image</label>
                <input type="file"  class="form-control" value="" name="aadhar_file"  required />
              </div>
              <div class="form-group col-md-4">
                <label for="inputName">Package</label>
                <select name="package" class='form-control' required>
					<option value="">Select Package</option>
					<?php
						$utypes = $mysqlClass->fetchAllData("package_list","package_name,id", " WHERE `creator_id` = '".$USER_ID."' and created_by = 'WL'");
						foreach($utypes as $ut){
					?>
						<option value="<?=$ut['id']?>"><?=$ut['package_name']?></option>
					<?php } ?>
				</select>
              </div>
              <div class="form-group col-md-4">
                <label for="inputName">Status</label>
                <select name="status" class='form-control' required>
					<option value="ENABLED">ENABLED</option>
					<option value="DISABLED">DISABLED</option>
				</select>
              </div>			  
              <div class="form-group col-md-4">
                <label for="inputName">User Capping (No.of Child User) </label>
                <input type="text"  class="form-control" value="" name="user_capping" onblur="check_capping(this)" required />
              </div>			  
              <div class="form-group col-md-4">
                <label for="inputName">Is Master Distributor </label>
                <input type="checkbox"  class="ml-4" name="is_master" value='1'  />
              </div>
            </div>
			  <div class="card-footer">
				<button type="submit" name="add" class="btn btn-primary">Add</button>
			  </div>
			</form>
            <!-- /.card-body -->
			<?php } else { echo $limitError; } ?>
          </div>
          <!-- /.card -->
        </div>
        
		</div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
<script>
function checkPassword(e){
	var pass = $("#pass").val();
	var cpass= $(e).val();
	if(pass!=cpass){
		$(e).val('');
		$("#pass").val('');
		alert("Password not match!");
	}
}
function check_capping(e){
	var avbCapping = <?=$number_of_child_limi - 1?>;
	var val = $(e).val();
	if(avbCapping < val){
		$(e).val('');
		alert("Not allow to larger than "+ avbCapping);
	}
}
</script>
</html>
