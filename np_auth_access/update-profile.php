<?php
	// include required files
	include_once('inc/head.php');
	include_once('inc/header.php');

	// objects creation
	$helpers = new helper_class();
	$post = $helpers->clearSlashes($_POST);
	$mysqlClass = new Mysql_class();
	
	if(isset($_POST['submit']))
	{		
		$resAAK = $mysqlClass->mysqlQuery("select api_access_key from `admin` WHERE `id`='".$_SESSION[_session_userid_]."' and `api_access_key`!=''" )->fetch(PDO::FETCH_ASSOC);		
			
		if(!empty($resAAK['api_access_key'])){
			
			if(isset($_FILES['logo']['name']) && !empty($_FILES['logo']['name'])){
				
				$logo = $helpers->fileUpload($_FILES["logo"],"../uploads/logo/","admin");
				if($logo['type']=="success"){
					$logo_file_name = $logo['filename'];
				} else {
					echo "<script> alert('".$logo['message']."'); </script>";
				}
				
			} else {
				$logo_file_name = '';
			}				
			
			$value = array(
				'logo'	  => $logo_file_name					
			);
			$mysqlClass->updateData(' admin ', $value, " where id = '".$_SESSION[_session_userid_]."' ");
			
			@$msg .= "<div class=\"alert alert-success alert-dismissable\">";
				$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
							&times;
					  </button>";
				$msg .= "Logo uploaded Successfully.";
			$msg .= "</div>";				
		}		
		unset($_POST);
		echo "<script>alert('Logo uploaded Successfully.');
					window.location.href='update-profile.php';
			</script>";
	}
	
?>
<!-- MAIN PANEL -->
<div class="page-wrapper-row full-height" onLoad="setLogo()">
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
								<span>Update Profile</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Update Profile </div>
							</div>
							<div class="portlet-body">
								<div class="row">
									<div class="col-sm-12 col-md-12 col-lg-12">
									
										<?php if(@$msg!=""){ echo $msg;	} ?>		
											
										<form method ="post" class="smart-form" action ="" enctype="multipart/form-data">				
											<div class="col-md-4">
												<div class="form-group">
													<label>Upload Logo</label>
													<div>
														<input type='file' name='logo' class='form-control' >
													</div>
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
	function setLogo()
	{
		alert("fsfdf");
	}
</script>
</body>
</html>
