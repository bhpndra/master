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
$get = $helpers->clearSlashes($_GET); 
$uid = base64_decode($_GET['uid']);
 
	if(isset($_POST['update_profile'])){
		$que = $mysqlClass->mysqlQuery("select id from `add_cust` WHERE (`email`='".$post['email']."' OR `mobile`='".$post['mobile']."') and id != $uid ");
		if($que->rowCount()>0){	
			$msg0  = $helpers->alert_message("Mobile number or email id already used in another account.","alert-danger");
		} else {
			$value = array(
					'name' 					=> $post['name'],				
					'cname'	 				=> $post['cname'],
					'email' 				=> $post['email'],
					'mobile' 				=> $post['mobile'],
					'city' 					=> $post['city'],
					'state' 				=> $post['state'],
					'pin' 					=> $post['pin'],				
					'address' 	    	    => $post['address']
				);
			$row = $mysqlClass->updateData(' add_cust ', $value, " where id = '".$uid."' and creator_id = '".$USER_ID."' and wl_id = '".$WL_ID."' and usertype = 'RETAILER' ");
			if($row > 0 ){
				$msg0  = $helpers->alert_message("Profile Update Successfully.","alert-success");
			}
		}
	}


	
$userDetail = $mysqlClass->mysqlQuery("SELECT * FROM `add_cust` WHERE `id`='" . $uid . "' and wl_id = '".$WL_ID."' and usertype = 'RETAILER'")->fetch(PDO::FETCH_ASSOC);	

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
            </div>
			  <div class="card-footer">
				<button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
			  </div>
			</form>
            <!-- /.card-body -->
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
