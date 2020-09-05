<?php 
session_start(); 
$netpaisa_services=json_decode($_SESSION['netpaisa_services']);
//print_r($_SESSION);exit;
if(!in_array(1,$netpaisa_services)){
	header("location:dashboard.php");
}

?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>
<?php
	require("../api/classes/db_class.php");
	require("../api/classes/comman_class.php");
	$helpers = new Helper_class();
	$mysqlClass = new mysql_class();
?>
<?php
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
$msg = '';


$post = $helpers->clearSlashes($_POST); 
	if(isset($_POST['update_profile'])){
		//echo "<pre />";
		//print_r($_POST);exit;
		unset($_POST['update_profile']);
		
		$IfPanExist = $mysqlClass->mysqlQuery("SELECT * FROM `pan_card_details` WHERE `pan_no`='" . $_POST['pan_no'] . "' ")->fetch(PDO::FETCH_ASSOC);	
	
		if ( !empty($IfPanExist) && count($IfPanExist) > 0) {
		  $msg0  = $helpers->alert_message("Data already exist.","alert-success");
		} else {
		$_POST['user_id']=$USER_ID;
		$res = $mysqlClass->insertData(' pan_card_details ', $_POST);
		if(!empty($res)){			
		  $msg0  = $helpers->alert_message("Pan Card details have saved Successfully.","alert-success");	
		}
		  
	  }
	}
$cities = $mysqlClass->mysqlQuery("SELECT * FROM `cities`")->fetchAll(PDO::FETCH_ASSOC);	
$states = $mysqlClass->mysqlQuery("SELECT * FROM `states`")->fetchAll(PDO::FETCH_ASSOC);	


?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">PAN CARD</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">PAN CARD</li>
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
			<?=(!empty($msg0))? $msg0 : '';?>
          <div class="card card-danger">
            <div class="card-header">
              <h3 class="card-title">PAN CARD</h3>

            </div>
			<form method="post" enctype="multipart/form-data">
			
            <div class="card-body row">
              <div class="form-group col-md-6">
                <label for="inputName">Full Name <span class="astrisk">*</span></label>
                <input type="text"  class="form-control" value="" name="name" pattern="^[a-zA-Z]{4,}(?: [a-zA-Z]+){0,2}$" required />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">User Name <span class="astrisk">*</span></label>
                <input type="text"  class="form-control" value="" name="user_name" pattern="^[a-zA-Z]{4,}(?: [a-zA-Z]+){0,2}$"  required  />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Mobile <span class="astrisk">*</span></label>
                <input type="tel"  class="form-control" value="" name="mobile" pattern="^[0][1-9]\d{9}$|^[1-9]\d{9}$"  required  />
              </div>
			  <div class="form-group col-md-6">
                <label for="inputName">Alternate Mobile <span class="astrisk">*</span></label>
                <input type="tel"  class="form-control" value=""  name="alternate_mobile" pattern="^[0][1-9]\d{9}$|^[1-9]\d{9}$" required  />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Email <span class="astrisk">*</span></label>
                <input type="email"  class="form-control" value="" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required  />
              </div>
              <div class="form-group col-md-6">
				<label for="dob">DOB <span class="astrisk">*</span></label>
				<input type="date" class="form-control" id="dob" placeholder="Enter dob" name="dob" pattern="^(0[1-9]|1[012])[-/.](0[1-9]|[12][0-9]|3[01])[-/.](19|20)\\d\\d$" required >
			  </div>
			   
            
			 
			<div class="form-group col-md-6">
			  <label for="inputName">District <span class="astrisk">*</span></label>
			  <select class="form-control"  style="width: 100%;"  name="city" required>
				<option value="">Select District</option>
				<?php
				foreach($cities as $city_value){
					?>
					<option value="<?= $city_value['id'];  ?>" ><?php echo $city_value['city'];  ?></option>
					<?php
				}
				
				?>
			  </select>
			</div>
			  
              <div class="form-group col-md-6">
			  <label for="inputName">State <span class="astrisk">*</span></label>
			  <select class="form-control"  style="width: 100%;"  name="state" required>
				<option value="">Select State</option>
				<?php
				foreach($states as $state_value){
					?>
					<option value="<?= $state_value['id'];  ?>" ><?php echo $state_value['name'];  ?></option>
					<?php
				}
				
				?>
			  </select>
			</div>
			  
			  
              <div class="form-group col-md-6">
                <label for="inputName">PIN Code <span class="astrisk">*</span></label>
                <input type="text"  class="form-control" value="" name="pin" pattern="^[1-9]{1}[0-9]{2}\\s{0, 1}[0-9]{3}$" required />
              </div>
              <div class="form-group col-md-12">
                <label for="inputName">Address <span class="astrisk">*</span></label>
                <input type="text"  class="form-control" value="" name="address" required />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">PAN No. <span class="astrisk">*</span></label>
                <input type="text"  class="form-control" name="pan_no"  pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" value="" required  />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Addhaar No.. <span class="astrisk">*</span></label>
                <input type="text"  class="form-control" name="aadhar_no" pattern="^[0-9]{10}|[0-9]{12}$"  value="" required />
              </div>
            </div>
			  <div class="card-footer">
				<button type="submit" name="update_profile" class="btn btn-primary">Save</button>
			  </div>
			</form>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        
			

      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
<script>

</script>
</html>
