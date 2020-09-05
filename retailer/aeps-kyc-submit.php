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

	$url = BASE_URL . "/api/aeps/outlet-details.php";
	$post_fields = array("token" => $_SESSION['TOKEN']);
	$resAPI_Json = api_curl($url, $post_fields, $headerArray);
	$resDetails = json_decode($resAPI_Json, true);

	if(!isset($resDetails['ERROR_CODE']) && $resDetails['ERROR_CODE'] != 0){
		echo '<script>self.close();</script>'; die();
	}	
	if(isset($resDetails['DATA'])){
		$outletDetails = $resDetails['DATA'];
		
		$urlKYC = BASE_URL . "/api/aeps/aeps-kyc-documents.php";
		$postKYC["token"] = $_SESSION['TOKEN'];
		$postKYC["pan_no"] = $outletDetails['pan_no'];
		$postKYC["outletid"] = $outletDetails['outletid'];
		$resAPIO_Json = api_curl($urlKYC, $postKYC, $headerArray);
		$resODetails = json_decode($resAPIO_Json, true);
		
	}
	
	
	  if (isset($_POST['submit'])) {
                        $post = $helpers->clearSlashes($_POST);
                        $outletid = $outletDetails['outletid'];  //outletid
                        $pan_no = $outletDetails['pan_no'];  //outletid
                        $user_id = $USER_ID; // retailer_id  
                        $aadhaar_no = $post['aadhaar_number']; // retailer aadhaar_number
                        $aadhaar_id = 14; // api response aadhaarid
                        $aadhaarimage_name = $_FILES['aadhaarimage']['name'];
                        $aadhaarimage_type = $_FILES['aadhaarimage']['type'];
                        $aadhaarimage_tmp_name = $_FILES['aadhaarimage']['tmp_name'];
                        $aadhaarimage_error = $_FILES['aadhaarimage']['error'];
                        $aadhaarimage_size = $_FILES['aadhaarimage']['size'];

                        $apiAccessKey = $userClass->get_api_access_key($ADMIN_ID);
						if(empty($apiAccessKey['api_access_key'])){
							$helpers->errorResponse("Admin API Access Key Not Set.");
						}
						$api_access_key = $apiAccessKey['api_access_key'];

                        if($aadhaarimage_name != '' && $aadhaarimage_type != '' && $aadhaarimage_tmp_name != '' && $aadhaarimage_error != 4 && $aadhaarimage_size != 0){

                                if ( $aadhaarimage_size < 200000 ) {    

                                    $target_dir = "../uploads/aeps_kyc/";
                                    $aadhar_file_name = '';
                                   
                                    if (isset($_FILES['aadhaarimage']['name'])) {
                                       $aadharUpload = $helpers->fileUpload($_FILES['aadhaarimage'],$target_dir,$outletid,true);
										if($aadharUpload['type']=="success"){
											$aadhar_file_name = $aadharUpload['filename'];
										}
                                    } 
//echo BASE_URL . "uploads/aeps_kyc/" .  $aadhar_file_name;
                                    $img_encoded_url = base64_encode(file_get_contents( BASE_URL . "uploads/aeps_kyc/" .  $aadhar_file_name));
									if(!isset($img_encoded_url) || empty($aadhar_file_name)){
										$helpers->errorResponse("Image Upload Error !");
									}
                                    //The data you want to send via POST
                                    $fields = [
                                            'api_access_key'        => $api_access_key, 
                                            'outletid'              => $outletid,
                                            'pan_no'                => $pan_no,
                                            'aadhar_no'             => $aadhaar_no,
                                            'aadharid'              => $aadhaar_id,
                                            'aadharfilename'        => 'data:image/jpeg;base64,'. $img_encoded_url
										];                         
                                    $result = $helpers->netpaisa_curl($aadhar_upload_kyc_document, $fields);
                                    
                                    $rech_data  = json_decode($result,true);
									
                                    if(!empty($rech_data)) {
                                        
                                        //Response
                                        $status = (int)$rech_data['ERR_STATE'];
                                        $message = $rech_data['MSG'];

                                        if($status == 0) {                                            
                                            $mysqlClass->mysqlQuery("UPDATE outlet_kyc SET `aadhaar` ='$aadhaar_no', `aadhaarimg`= '$aadhar_file_name' WHERE `user_id`='$user_id' and `sources`='I'");                                            
                                            echo "<script>alert(' Message : " . $message . "'); self.close();</script>";
                                        }  else {
                                            echo "<script>alert(' Message : " . $message . "'); self.close();</script>";
                                        }
                                    }
                                } else {
                                    echo "<script>alert('Please select each image with size : 200KB')</script>";
                                }
                          
                        } else {
                            echo "<script>alert('Select Aadhar Image!!')</script>";    
                        } 
                        
                        if($aadhaar_no == ''){
                            echo "<script>alert('Input Aadhar Number!!')</script>"; 
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
            <h1 class="m-0 text-dark">Submit AEPS KYC</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Submit AEPS KYC</li>
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
              <h3 class="card-title">Submit AEPS KYC</h3>

            </div>
			<form method="post" id="creditForm" enctype="multipart/form-data" >
            <div class="card-body row">
			  <div class="form-group col-md-3">
                <label for="inputName">Name</label>
                <input type="text"  class="form-control" value="<?=$outletDetails['name']?>" disabled>
              </div>
              <div class="form-group col-md-3">
                <label for="inputName">Mobile</label>
                <input type="text"  class="form-control" value="<?=$outletDetails['mobile']?>" disabled>
              </div>
              <div class="form-group col-md-3">
                <label for="inputName">Email</label>
                <input type="text"  class="form-control" value="<?=$outletDetails['email']?>" disabled>
              </div>
              <div class="form-group col-md-3">
                <label for="inputName">Company</label>
                <input type="text"  class="form-control" value="<?=$outletDetails['company']?>" disabled>
              </div>
              <div class="form-group col-md-9">
                <label for="inputName">Address</label>
                <input type="text"  class="form-control" value="<?=$outletDetails['address']?>" disabled>
              </div>
              <div class="form-group col-md-3">
                <label for="inputName">PIN</label>
                <input type="text"  class="form-control" value="<?=$outletDetails['pincode']?>" disabled>
              </div> 
              <div class="form-group col-md-3">
                <label for="inputName">PAN</label>
                <input type="text"  class="form-control" value="<?=$outletDetails['pan_no']?>" disabled>
              </div>  
              <div class="form-group col-md-4">
                <label for="inputName">Aadhaar No.</label>
                <input type="text" name="aadhaar_number" class="form-control" value="<?=$outletDetails['aadhaar']?>" >
              </div>	  
              <div class="form-group col-md-4">
                <label for="inputName" >Aadhaar Image.</label>
<?php 
		if(isset($resODetails['SCREENING']['Aadhaar']) && !empty($resODetails['SCREENING']['Aadhaar'])){  
			echo "<br/><strong style='color:green'>" . $resODetails['SCREENING']['Aadhaar'] . "</strong>" ; 
		}
		else { 
?>
                <input type="file" name="aadhaarimage" class="form-control"  >
<?php } ?>
              </div>

            </div>
<?php if(isset($resODetails['SCREENING']['Aadhaar']) && !empty($resODetails['SCREENING']['Aadhaar'])) { } else { ?>	
			  <div class="card-footer">
				<button type="submit" name="submit"  class="btn btn-primary">Submit</button>
			  </div>
<?php } ?>
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
<?php //include("inc/footer.php"); ?>	
</html>
