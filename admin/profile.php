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


$post = $helpers->clearSlashes($_POST); 
	if(isset($_POST['update_profile'])){
		$value = array(
				'name' 					=> $post['name'],				
				'cname'	 				=> $post['cname'],
				'city' 					=> $post['city'],
				'state' 				=> $post['state'],
				'pin' 					=> $post['pin'],				
				'address' 	    	    => $post['address']
			);
		$mysqlClass->updateData(' add_cust ', $value, " where id = '".$USER_ID."' and wl_id = '".$WL_ID."' and  admin_id = '".$ADMIN_ID."' ");
		$msg0  = $helpers->alert_message("Profile Update Successfully.","alert-success");
	}


	if(isset($_POST['update_password'])){
		$ohash_pass = $helpers->hashPassword($post['opass']);
		$hash_pass = $helpers->hashPassword($post['npass']);
		$value = array(
				'pass' 	=> $hash_pass['encrypted']
			);
		$e_row = $mysqlClass->updateData(' add_cust ', $value, " where id = '".$USER_ID."' and wl_id = '".$WL_ID."' and  admin_id = '".$ADMIN_ID."' and pass = '".$ohash_pass['encrypted']."' ");
		if($e_row > 0){
			$msg1  = $helpers->alert_message("Password Update Successfully.","alert-success");
		} else {
			$msg1  = $helpers->alert_message("Old Password not match.","alert-danger");
		}
	}

	if(isset($_POST['update_pin'])){
		$ospin = $helpers->hashPin($post['o_pin']);
		$spin = $helpers->hashPin($post['npin']);
		$value = array(
				'security_pin' => $spin['encrypted']
			);
		$e_row = $mysqlClass->updateData(' add_cust ', $value, " where id = '".$USER_ID."' and wl_id = '".$WL_ID."' and  admin_id = '".$ADMIN_ID."' and security_pin = '".$ospin['encrypted']."' ");
		if($e_row > 0){
			$msg1  = $helpers->alert_message("PIN Update Successfully.","alert-success");
		} else {
			$msg1  = $helpers->alert_message("Old PIN not match.","alert-danger");
		}
	}	
$userDetail = $mysqlClass->mysqlQuery("SELECT * FROM `add_cust` WHERE `id`='" . $USER_ID . "' ")->fetch(PDO::FETCH_ASSOC);	

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
          <div class="card card-danger">
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
                <input type="tel"  class="form-control" value="<?=$userDetail['mobile']?>" required readonly />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Email</label>
                <input type="email"  class="form-control" value="<?=$userDetail['email']?>" required readonly />
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
			<div class="col-md-12">
			  <div class="card card-success">
				<div class="card-header">
				  <h3 class="card-title">Change Password</h3>

				</div>
				<form method="post" >
				<div class="card-body row">
				  <div class="form-group col-md-4">
					<label for="inputName">Old Password</label>
					<input type="password"  class="form-control" value="" name="opass" id="opass" required >
				  </div>
				  <div class="form-group col-md-4">
					<label for="inputName">Password</label>
					<input type="password"  class="form-control" value="" name="npass" id="npass" required >
				  </div>
				  <div class="form-group col-md-4">
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
			</div>
			  <!-- /.card -->
			<div class="col-md-12">
			  <div class="card card-warning">
				<div class="card-header">
				  <h3 class="card-title">Change Login PIN</h3>

				</div>
				<form method="post" enctype="multipart/form-data">
				<div class="card-body row">
				  <div class="form-group col-md-4">
					<label for="inputName">Old Pin</label>
					<input type="password"  class="form-control" value=""  name="o_pin" required >
				  </div>
				  <div class="form-group col-md-4">
					<label for="inputName">Pin</label>
					<input type="password"  class="form-control" value="" name="npin" id="npin" required >
				  </div>
				  <div class="form-group col-md-4">
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
			</div>
				<div class="col-md-12">
					<div class="card card-info">
						<div class="card-header">
							<h3 class="card-title">Number of Child Limit</h3>
						</div>
						
						<div class="card-body row">
							<div class="form-group col-md-12">
								<label for="inputName">No. of Child Limit</label>
								<input type="text" class="form-control" value="<?= $userDetail['number_of_child_limi']?>" readonly >
							</div>
														
						</div>
						
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
