<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<!-- Bootstrap Color Picker -->
<link rel="stylesheet" href="<?=BASE_URL?>/dashboard/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>

<?php
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
$msg = '';


$post = $helpers->clearSlashes($_POST); 
	if(isset($_POST['update_settings'])){
			if(isset($_FILES['logo']['name']) && !empty($_FILES['logo']['name'])){
				$fileName = "logo_".$USER_ID;
				$logoUpload = $helpers->fileUpload($_FILES["logo"],"../uploads/logo/",$fileName,true);
				if($logoUpload['type']=="success"){
					$upload_logo = $logoUpload['filename'];
				} else {
					echo "<script> alert('".$logoUpload['message']."'); </script>";
				}
			} else {
				$upload_logo = $post['oldLogo'];
			}
		$values = array(
					"site_title" => $post['sitetitle'],
					"meta_desc"  => $post['meta'],
					"copyright"  => $post['copyright'],
					"logo"       => $upload_logo,
					"ip_address" => $_SERVER['REMOTE_ADDR'],
					"user_type" => "WL",
					"facebook_url" => $post['facebook'],
					"twitter_url"  => $post['twitter'],
					"linkedin_url" => $post['linkedin'],
					"color_code" => $post['colorCode'],
					"email" => $post['email'],
					"support_number" => $post['support_number'],
					"address" => $post['address'],
					"site_name" => $post['site_name'],
					"app_link" => $post['applink']
					);
		$mysqlClass->updateData(' general_settings ', $values, " WHERE `user_id`='".$USER_ID."' and `user_type`='WL'  ");
		$msg0  = $helpers->alert_message("Setting Update Successfully.","alert-success");
	}


$general_set = $mysqlClass->mysqlQuery("SELECT * FROM `general_settings` WHERE `user_id`='".$USER_ID."' AND `user_type`='WL'")->fetch(PDO::FETCH_ASSOC);	
	//general settings info
	$site_title=$general_set['site_title'];
	$email = $general_set['email'];
	$meta_desc = $general_set['meta_desc'];
	$copyright = $general_set['copyright'];
	$logo = $general_set['logo'];
	$facebook = $general_set['facebook_url'];
	$twitter = $general_set['twitter_url'];
	$linkedin = $general_set['linkedin_url'];
	$applink = $general_set['app_link'];
	$colorCode = $general_set['color_code'];
	$support_number = $general_set['support_number'];
	$email = $general_set['email'];
	$site_name = $general_set['site_name'];
	$address = $general_set['address'];
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Setting</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Setting</li>
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
			<?=($msg0!='')? $msg0 : '';?>
			  <div class="card card-info">
				<div class="card-header">
				  <h3 class="card-title">Setting</h3>
				  <div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
                 </div>
				</div>				
				<div class="card-body">
				<form method ="post" class="row" action ="" enctype="multipart/form-data">
					<div class="col-md-4 form-group">
						<label class="label">Site Title:*</label>
						<input type="text" name="sitetitle" class="form-control" value="<?php echo($site_title) ?$site_title : ''; ?>" required="required">
					</div>
					<div class="col-md-4 form-group">
						<label class="label">Site Name:*</label>
						<input type="text" name="site_name" class="form-control" value="<?php echo($site_name) ?$site_name : ''; ?>" required="required">
					</div>
																	
					<div class="col-md-4 form-group">
						<label class="label">Meta Description:*</label>
						<input type="text" name="meta" class="form-control" value="<?php echo($meta_desc) ?$meta_desc : ''; ?>" required="required">	
					</div>												
					<div class="col-md-4 form-group">
						<label class="label">Copyright:*</label>
						<input type="text" name="copyright" class="form-control" value="<?php echo($copyright) ?$copyright : ''; ?>" required="required">
					</div>

					<div class="col-md-4 form-group">
						<label class="label">Facebook URL:</label>
						<input type="text" name="facebook" class="form-control" value="<?php echo($facebook) ?$facebook : ''; ?>">
					</div>

					<div class="col-md-4 form-group">
						<label class="label">Twitter URL:</label>
						<input type="text" name="twitter" class="form-control" value="<?php echo($twitter) ?$twitter : ''; ?>">	
					</div>

															
															<!--row-->

						<div class="col-md-4 form-group">
							<label class="label">Linkedin URL:</label>
							<input type="text" name="linkedin" class="form-control"  value="<?php echo($linkedin) ?$linkedin : ''; ?>">
						</div>
						<div class="col-md-4 form-group">
							<label class="label">App Link:</label>
							<input type="text" name="applink" class="form-control"  value="<?php echo($applink) ?$applink : ''; ?>">
						</div>
						<div class="col-md-4 form-group">
							<label class="label">Support Email:</label>
							<input type="text" name="email" class="form-control"  value="<?php echo($email) ?$email : ''; ?>">
						</div>
						<div class="col-md-3 form-group">
							<label class="label">Support Number:</label>
							<input type="text" name="support_number" class="form-control"  value="<?php echo($support_number) ?$support_number : ''; ?>">
						</div>
						<div class="col-md-3 form-group">
							<label class="label">Theme Color:</label>
							<input type="text" name="colorCode" class="form-control my-colorpicker1" id="full-popover" data-color-format="hex" value="<?php echo($colorCode) ?$colorCode : '#1f6e30'; ?>" >
						</div>
						<div class="col-md-6 form-group">
							<label class="label">Address:</label>
							<input type="text" name="address" class="form-control" value="<?php echo($address) ?$address : ''; ?>"  />
						</div>
						<div class="col-md-4 form-group">
							<label class="label">Upload Logo:*</label>
							<input type="hidden" name="oldLogo" class="form-control" value="<?php echo $logo; ?>" />
							<input type="file" name="logo" class="form-control" <?php echo ($logo == ""?"required":""); ?>>
							<?php if($logo != "") { ?>
							<img height="50" width="50" src="<?=BASE_URL?>uploads/logo/<?php echo $logo; ?>" />
							<?php } ?>
						</div>	
						<div class="col-md-4 form-group">
							<label class="label" style="opacity: 0;">fasdf</label>
							<button type="submit" name="update_settings" class="btn btn-success form-control">
								Update Settings
							</button>
						</div>
					</form>
				</div>
			  </div>
          <!-- /.card -->
			</div>
		</div>

      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
<!-- bootstrap color picker -->
<script src="<?=BASE_URL?>dashboard/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script>
$(document).ready(function(){
    //Colorpicker
    $('.my-colorpicker1').colorpicker();
});
</script>
</html>
