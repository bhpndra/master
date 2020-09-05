<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>
<?php
error_reporting(0);
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
$msg = '';

if(isset($_POST)){
	$post = $helpers->clearSlashes($_POST);	
} 
$get = $helpers->clearSlashes($_GET); 
$uid = base64_decode($_GET['uid']);
 
	if(isset($_POST['update_profile'])){
		$que = $mysqlClass->mysqlQuery("select id from `add_cust` WHERE (`email`='".$post['email']."' OR `mobile`='".$post['mobile']."') and id != $uid ");
		if($que->rowCount()>0){	
			$msg0  = $helpers->alert_message("Mobile number or email id already used in another account.","alert-danger");
		} else {
			$minAmtCapping = ($post['minAmtCapping'] > 0) ? $post['minAmtCapping'] : 1;
			$value = array(
					'name' 					=> $post['name'],				
					'cname'	 				=> $post['cname'],
					'email' 				=> $post['email'],
					'mobile' 				=> $post['mobile'],
					'city' 					=> $post['city'],
					'state' 				=> $post['state'],
					'pin' 					=> $post['pin'],				
					'status' 				=> $post['status'],				
					'service_access' 		=> implode(",",$post['services']),	
					'netpaisa_services' 	=> json_encode($post['services_new']),		
					'min_amt_capping' 		=> $minAmtCapping,				
					'address' 	    	    => $post['address']
				);
			$row = $mysqlClass->updateData(' add_cust ', $value, " where id = '".$uid."' and wl_id = '".$WL_ID."' and usertype = 'RETAILER' ");
			if($row > 0 ){
				$msg0  = $helpers->alert_message("Profile Update Successfully.","alert-success");
			}
		}
	}


	if(isset($_POST['update_password'])){
		$hash_pass = $helpers->hashPassword($post['npass']);
		$value = array(
				'pass' 	=> $hash_pass['encrypted']
			);
		$e_row = $mysqlClass->updateData(' add_cust ', $value, " where id = '".$uid."' and wl_id = '".$WL_ID."' and usertype = 'RETAILER' ");
		if($e_row > 0){
			$msg1  = $helpers->alert_message("Password Update Successfully.","alert-success");
		} else {
			$msg1  = $helpers->alert_message("Old Password not match.","alert-danger");
		}
	}

	if(isset($_POST['update_pin'])){
		$spin = $helpers->hashPin($post['npin']);
		$value = array(
				'security_pin' => $spin['encrypted']
			);
		$e_row = $mysqlClass->updateData(' add_cust ', $value, " where id = '".$uid."' and wl_id = '".$WL_ID."' and usertype = 'RETAILER' ");
		if($e_row > 0){
			$msg1  = $helpers->alert_message("PIN Update Successfully.","alert-success");
		} else {
			$msg1  = $helpers->alert_message("Old PIN not match.","alert-danger");
		}
	}
	
	if(isset($_POST['move_to_distributor'])){
		$que = $mysqlClass->mysqlQuery("select creator_id,package_id,id from `add_cust` WHERE 1 and id = '".$post['distributor']."' ");
		if($que->rowCount()>0){	
			$res = $que->fetch(PDO::FETCH_ASSOC);
			$value1 = array(
					'creator_id' => $post['distributor'],
					'package_id' => $res['package_id']
				);
			$row = $mysqlClass->updateData(' add_retailer ', $value1, " where user_id = '".$uid."'  ");
			
			$value2 = array(
					'creator_id' => $post['distributor'],
					'package_id' => $res['package_id']
				);
			$row = $mysqlClass->updateData(' add_cust ', $value2, " where id = '".$uid."' and wl_id = '".$WL_ID."' and usertype = 'RETAILER' ");
			if($row > 0 ){
				$msg0  = $helpers->alert_message("Update Successfully.","alert-success");
			}
		}
	}

	
	
$userDetail = $mysqlClass->mysqlQuery("SELECT * FROM `add_cust` WHERE `id`='" . $uid . "' and wl_id = '".$WL_ID."' and usertype = 'RETAILER'")->fetch(PDO::FETCH_ASSOC);	
$NetPaisaServices = $mysqlClass->mysqlQuery("select id,name from `netpaisa_services` WHERE status=1");
		
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Profile</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Profile</li>
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
			<div class="col-md-6">
			<?=($msg0!='')? $msg0 : '';?>
          <div class="card card-dark">
            <div class="card-header">
              <h3 class="card-title">Profile</h3>

            </div>
			<form method="post" enctype="multipart/form-data">
            <div class="card-body row">
              <div class="form-group col-md-6">
                <label for="inputName">Name</label>
                <input type="text"  class="form-control" value="<?=$userDetail['name']?>" name="name" required />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">User</label>
                <input type="tel"  class="form-control" value="<?=$userDetail['user']?>" required readonly />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Mobile</label>
                <input type="tel"  class="form-control" value="<?=$userDetail['mobile']?>" name="mobile" required  />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Email</label>
                <input type="email"  class="form-control" value="<?=$userDetail['email']?>" name="email" required  />
              </div>
              
              <div class="form-group col-md-6">
                <label for="inputName">Company</label>
                <input type="text"  class="form-control" value="<?=$userDetail['cname']?>" name="cname" required />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">City</label>
                <input type="text"  class="form-control" value="<?=$userDetail['city']?>" name="city" required />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">State</label>
                <input type="text"  class="form-control" value="<?=$userDetail['state']?>" name="state" required />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">PIN Code</label>
                <input type="text"  class="form-control" value="<?=$userDetail['pin']?>" name="pin" pattern="[0-9]{6}" required />
              </div>
              <div class="form-group col-md-12">
                <label for="inputName">Address</label>
                <input type="text"  class="form-control" value="<?=$userDetail['address']?>" name="address" required />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">PAN No.</label>
                <input type="text"  class="form-control" value="<?=$userDetail['pan_no']?>" readonly />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Addhaar No..</label>
                <input type="text"  class="form-control" value="<?=$userDetail['aadhar_no']?>" readonly />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Min. Amount Capping</label>
                <input type="text"  class="form-control" value="<?=$userDetail['min_amt_capping']?>" name="minAmtCapping" />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Status</label>
                <select  class="form-control" name="status" >
					<option <?=($userDetail['status']=="ENABLED") ? 'Selected' : '' ; ?> value="ENABLED">ENABLED</option>
					<option <?=($userDetail['status']=="DISABLED") ? 'Selected' : '' ; ?> value="DISABLED">DISABLED</option>
				</select>
              </div>
			  <?php 
			  $services = explode(",",$userDetail['service_access']); 
			  $netpaisa_services = json_decode(empty($userDetail['netpaisa_services'])?0:$userDetail['netpaisa_services']);
			 // print_r($netpaisa_services);exit;
			  ?>
			  <div class="col-md-4">
				<div class="input-group">
				  <div class="input-group-prepend">
					<span class="input-group-text">
					  <input type="checkbox" name="services[]" value="aeps" <?=(in_array('aeps',$services)) ? 'checked' : ''; ?> />
					</span>
				  </div>
				  <input type="text" readonly value="AEPS" class="form-control">
				</div>
				<!-- /input-group -->
			  </div>
			  <div class="col-md-4">
				<div class="input-group">
				  <div class="input-group-prepend">
					<span class="input-group-text">
					  <input type="checkbox" name="services[]" value="recharge"  <?=(in_array('recharge',$services)) ? 'checked' : ''; ?> />
					</span>
				  </div>
				  <input type="text" readonly value="RECHARGE" class="form-control">
				</div>
				<!-- /input-group -->
			  </div>
			  <div class="col-md-4">
				<div class="input-group">
				  <div class="input-group-prepend">
					<span class="input-group-text">
					  <input type="checkbox" name="services[]" value="dmt"  <?=(in_array('dmt',$services)) ? 'checked' : ''; ?> />
					</span>
				  </div>
				  <input type="text" readonly value="DMT" class="form-control">
				</div>
				<!-- /input-group -->
			  </div>
			  <div class="col-md-4">
				<div class="input-group">
				  <div class="input-group-prepend">
					<span class="input-group-text">
					  <input type="checkbox" name="services[]" value="bbps"  <?=(in_array('bbps',$services)) ? 'checked' : ''; ?> />
					</span>
				  </div>
				  <input type="text" readonly value="BBPS" class="form-control">
				</div>
				<!-- /input-group -->
			  </div>
			  <div class="col-md-4">
				<div class="input-group">
				  <div class="input-group-prepend">
					<span class="input-group-text">
					  <input type="checkbox" name="services[]" value="payout"  <?=(in_array('payout',$services)) ? 'checked' : ''; ?> />
					</span>
				  </div>
				  <input type="text" readonly value="Payout" class="form-control">
				</div>
				<!-- /input-group -->
			  </div>
			  
			  <?php
			  foreach($NetPaisaServices as $ListServices){
				  ?>
				  
			<div class="col-md-4">
				<div class="input-group">
				  <div class="input-group-prepend">
					<span class="input-group-text">
					  <input type="checkbox" name="services_new[]" value="<?= $ListServices['id']; ?>"  <?=(in_array($ListServices['id'],$netpaisa_services)) ? 'checked' : ''; ?> />
					</span>
				  </div>
				  <input type="text" readonly value="<?= $ListServices['name']; ?>" class="form-control">
				</div>
				<!-- /input-group -->
			  </div>
				  
				  
				  <?php
			  }
			  ?>
			  
			  
            </div>
			  <div class="card-footer">
				<button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
			  </div>
			</form>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        
			
			<div class="col-md-6">
			<div class="row">
			<?=($msg1!='')? $msg1 : '';?>
			  <div class="card card-success">
				<div class="card-header">
				  <h3 class="card-title">Change Password</h3>

				</div>
				<form method="post" >
				<div class="card-body row">
				  <div class="form-group col-md-6">
					<label for="inputName">Password</label>
					<input type="password"  class="form-control" value="" name="npass" id="npass" required >
				  </div>
				  <div class="form-group col-md-6">
					<label for="inputName">Confirm Password</label>
					<input type="password"  class="form-control" value="" name="cpassword" onblur="checkPassword(this)" required >
				  </div>
				  
				</div>
				  <div class="card-footer">
					<button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
				  </div>
				</form>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->

			  <div class="card card-warning">
				<div class="card-header">
				  <h3 class="card-title">Change Login PIN</h3>

				</div>
				<form method="post" enctype="multipart/form-data">
				<div class="card-body row">
				  <div class="form-group col-md-6">
					<label for="inputName">Pin</label>
					<input type="password"  class="form-control" value="" name="npin" id="npin" required >
				  </div>
				  <div class="form-group col-md-6">
					<label for="inputName">Confirm Pin</label>
					<input type="password"  class="form-control" value=""  onblur="checkPin(this)" required >
				  </div>
				  
				</div>
				  <div class="card-footer">
					<button type="submit" name="update_pin" class="btn btn-primary">Update Login Pin</button>
				  </div>
				</form>

			  <!-- /.card -->
			</div>
			
			<div class="card card-danger">
				<div class="card-header">
				  <h3 class="card-title">Move to Distributor</h3>

				</div>
				<form method="post" enctype="multipart/form-data">
				<div class="card-body row">
				  <div class="form-group col-md-12">
					<label for="inputName">Distributor</label>
					
					<select name="distributor" class='form-control' required>
						<option value="">Select Distributor</option>
						<?php
							//$utypes = $mysqlClass->mysqlQuery("add_cust","name,id", " WHERE `creator_id` = '".$WL_ID."' and created_by = 'WL'");
							$DT_sql =  $mysqlClass->mysqlQuery("select a.name,a.id,b.is_master_distributor from add_cust as a left join add_distributer as b on a.id = b.user_id where a.creator_id = '".$WL_ID."' and a.created_by = 'WL' or a.creator_id in ( select id from add_cust where `creator_id` = '".$WL_ID."' and created_by = 'WL'  ) and a.usertype = 'DISTRIBUTOR' ");
							while($ut = $DT_sql->fetch(PDO::FETCH_ASSOC)){
						?>
							<option <?=($userDetail['creator_id']==$ut['id']) ? 'selected' : ''?> value="<?=$ut['id']?>"><?=$ut['name']?> - <?=($ut['is_master_distributor']==0)? 'DT' : 'MD'?></option>
						<?php } ?>
					</select>
				  </div>
				 				  
				</div>
				  <div class="card-footer">
					<button type="submit" name="move_to_distributor" class="btn btn-primary">Update Package</button>
				  </div>
				</form>

			  <!-- /.card -->
			</div>
        
			</div>
			</div>
		</div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
<script>
function checkPassword(e){
	var pass = $("#npass").val();
	var cpass= $(e).val();
	if(pass!=cpass){
		$(e).val('');
		$("#npass").val('');
		alert("Confirm Password not match!");
	}
}

function checkPin(e){
	var pass = $("#npin").val();
	var cpass= $(e).val();
	if(pass!=cpass){
		$(e).val('');
		$("#npin").val('');
		alert("Confirm PIN not match!");
	}
}
</script>
</html>
