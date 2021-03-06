<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<body class="hold-transition sidebar-mini-md">
<div class="wrapper">
<?php
	require("../api/classes/db_class.php");
	require("../api/classes/comman_class.php");
	require("../api/classes/user_class.php");
	$helpers = new Helper_class();
	$mysqlClass = new mysql_class();
	$userClass = new user_class();
?>
<?php
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];

	$url = BASE_URL . "/api/aeps/aeps2-outlet-details.php";
	$post_fields = array("token" => $_SESSION['TOKEN']);
	$resAPI_Json = api_curl($url, $post_fields, $headerArray);
	$resDetails = json_decode($resAPI_Json, true);

	if(!isset($resDetails['ERROR_CODE']) && $resDetails['ERROR_CODE'] != 0){
		echo '<script>self.close();</script>'; die();
	}	
	$outletDetails = $resDetails['DATA'];
	
	
	  if (isset($_POST['submit'])) {
		  $post = $helpers->clearSlashes($_POST); 
            $update_kyc2 = BASE_URL . "/api/aeps/aeps2-kyc-update.php";
			unset($post['submit']);
			$post_fields = $post;
			$post_fields["token"] = $_SESSION['TOKEN'];
			$aadhaar_img = new CURLFile($_FILES['aadhaar_img']['tmp_name'], $_FILES['aadhaar_img']['type'], $_FILES['aadhaar_img']['name']);
			$post_fields['aadhaar_img'] = $aadhaar_img;
			
			$resAF = api_curl($update_kyc2, $post_fields, $headerArray);
			$resAFDetails = json_decode($resAF, true);
			if (isset($resAFDetails['ERROR_CODE']) && $resAFDetails['ERROR_CODE'] == 0) {
				//$helpers->flashAlert_set('add_virtual_FA'," Fund request accepted. ");
				$helpers->redirect_page("aeps2-kyc-submit");
			} else {
				//$helpers->flashAlert_set('add_virtual_FA',$resAFDetails['MESSAGE']);
				//$helpers->redirect_page("add-fund");
			}	
     }

	
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="margin-left: 0px;">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Submit AEPS 2 KYC</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Submit AEPS 2 KYC</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

		<div class="row justify-content-center">
			<div class="col-md-8">
			<?=($msg!='')? $msg : '';?>
          <div class="card card-danger">
            <div class="card-header">
              <h3 class="card-title">Submit AEPS 2 KYC</h3>

            </div>
			<form method="post" id="creditForm" enctype="multipart/form-data" >
            <div class="card-body row">
				<div class="form-group col-md-3">
					<label>Outlet Id </label>
					<input type="text"  class="form-control" value="<?=isset($outletDetails['outletid']) ? $outletDetails['outletid'] : '' ?>" readonly />
					<input type="hidden"  class="form-control" value="<?=isset($outletDetails['outletid']) ? $outletDetails['outletid'] : '' ?>" name="outlet_id"  />
				</div>
				<div class="form-group col-md-3">
					<label>Mobile </label>
					<input type="text"  class="form-control" value="<?=isset($outletDetails['mobile']) ? $outletDetails['mobile'] : '' ?>" readonly />
					<input type="hidden"  class="form-control" value="<?=isset($outletDetails['mobile']) ? $outletDetails['mobile'] : '' ?>" name="mobile"  />
				</div>
				<div class="form-group col-md-3">
					<label>Pan No </label>
					<input type="text"  class="form-control" value="<?=isset($outletDetails['pan_no']) ? $outletDetails['pan_no'] : '' ?>" readonly />
					<input type="hidden"  class="form-control" value="<?=isset($outletDetails['pan_no']) ? $outletDetails['pan_no'] : '' ?>" name="pan_no"  />
				</div>
				<div class="form-group col-md-3">
					<label>Shop Name <span style="color:red">*</span></label>
					<input type="text" required class="form-control" value="<?=isset($outletDetails['company']) ? $outletDetails['company'] : '' ?>" name="shop_name" id="shop_name">
				</div>
				<div class="form-group col-md-3">
					<label>First Name <span style="color:red">*</span></label>
					<input type="text" required class="form-control" value="<?=isset($outletDetails['first_name']) ? $outletDetails['first_name'] : '' ?>" name="first_name" id="first_name">
				</div>
				<div class="form-group col-md-3">
					<label>Middle Name</label>
					<input type="text" required class="form-control" value="<?=isset($outletDetails['middle_name']) ? $outletDetails['middle_name'] : '' ?>" name="middle_name" id="middle_name">
				</div>
				<div class="form-group col-md-3">
					<label>Last Name <span style="color:red">*</span></label>
					<input type="text" required class="form-control" value="<?=isset($outletDetails['last_name']) ? $outletDetails['last_name'] : '' ?>" name="last_name" id="last_name">
				</div>
				<div class="form-group col-md-3">
					<label>Date Of Birth <span style="color:red">*</span></label>
					<input type="date" required class="form-control" value="<?=isset($outletDetails['dob']) ? $outletDetails['dob'] : '' ?>" name="dob" id="dob">
				</div>
				<div class="form-group col-md-3">
					<label>Email Id <span style="color:red">*</span></label>
					<input type="text" required class="form-control" value="<?=isset($outletDetails['email']) ? $outletDetails['email'] : '' ?>" name="email" id="email">
				</div>
				<div class="form-group col-md-9">
					<label>Address <span style="color:red">*</span></label>
					<input type="text" required class="form-control" value="<?=isset($outletDetails['address']) ? $outletDetails['address'] : '' ?>" name="address" id="address">
				</div>
				<div class="form-group col-md-3">
					<label>PIN Code <span style="color:red">*</span></label>
					<input type="text" required class="form-control" value="<?=isset($outletDetails['pincode']) ? $outletDetails['pincode'] : '' ?>" name="pincode" id="pincode">
				</div>
				<div class="form-group col-md-3">
					<label>City <span style="color:red">*</span></label>
					<input type="text" required class="form-control" value="<?=isset($outletDetails['city']) ? $outletDetails['city'] : '' ?>" name="city" id="city">
				</div>
				<div class="form-group col-md-3">
					<label>District <span style="color:red">*</span></label>
					<input type="text" required class="form-control" value="<?=isset($outletDetails['district']) ? $outletDetails['district'] : '' ?>" name="district" id="district">
				</div>
				<div class="form-group col-md-3">
					<label>State <span style="color:red">*</span></label>
					<input type="text" required class="form-control" value="<?=isset($outletDetails['state']) ? $outletDetails['state'] : '' ?>" name="state" id="state">
				</div>
				<div class="form-group col-md-3">
					<label>KYC Status</label>
					<input type="text"  class="form-control" value="<?=isset($outletDetails['outlet_kyc']) ? $outletDetails['outlet_kyc'] : '' ?>" readonly >
				</div>
				<div class="form-group col-md-3">
					<label>Outlet Status</label>
					<input type="text"  class="form-control" value="<?=isset($outletDetails['outlet_status']) ? $outletDetails['outlet_status'] : '' ?>" readonly />
				</div>
				<div class="form-group col-md-6">
					<label>Comments</label>
					<input type="text"  class="form-control" value="<?=isset($outletDetails['comments']) ? $outletDetails['comments'] : '' ?>" readonly />
				</div>				
				<div class="form-group col-md-4">
					<label>Aadhaar No.</label>
					<input type="text"  class="form-control" value="<?=isset($outletDetails['aadhaar']) ? $outletDetails['aadhaar'] : '' ?>" name="aadhaar_no" />
				</div>				
				<div class="form-group col-md-4">
					<label>Aadhaar Image (JPEG).</label>
					<input type="file"  class="form-control" value="<?=isset($outletDetails['aadhaarimg']) ? $outletDetails['aadhaarimg'] : '' ?>" name="aadhaar_img" />
				</div>			
				<div class="form-group col-md-4">					
					<img src="<?=isset($outletDetails['aadhaarimg']) ? $outletDetails['aadhaarimg'] : '' ?>" alt="Aadhaar Image" style="width:200px" />
				</div>
			
            <!-- /.card-body -->
          </div>
		  
				<?php if(isset($outletDetails['outlet_kyc']) && ($outletDetails['outlet_kyc'] == 'SUCCESS' || $outletDetails['outlet_status'] == 'APPROVE')) { } else { ?>	
					  <div class="card-footer">
						<button type="submit" name="submit"  class="btn btn-primary">Submit</button>
					  </div>
				<?php } ?>
			</form>
          <!-- /.card -->
        </div>
        
		</div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php //include("inc/footer.php"); ?>	
</html>
