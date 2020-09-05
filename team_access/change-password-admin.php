<?php
	include_once('inc/head.php');
	include_once('inc/header.php');

      $suser_id = $_SESSION[_session_userid_];
	  $helpers = new helper_class();
	  $mysqlClass = new Mysql_class();
	
	if(isset($_POST['submit']) && !empty($_POST['password']) && !empty($_POST['opassword']))  {
		$post = $helpers->clearSlashes($_POST);	    
	    $hash_pass = md5($post['password']);
		
	    $old_pass = md5($post['opassword']);
					
		$value = array(
			'pass' 	=> $hash_pass,
		);
		$last_id = $mysqlClass->updateData('admin', $value, " where id = '".$suser_id."' and pass = '".$old_pass."'");
		
		if($last_id > 0) {			   
			echo "<script>alert('Password Change Successfully')</script>";
		 }
			else {
			  echo "<script>alert('Old Password Not Match !')</script>";
		}
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
								<span>Update Password</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Update Password </div>
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
										
										<div class="form-group col-md-4">
											<label>Old Password</label>
											<div>
												<input type='text' name='opassword'  class='form-control' placeholder="Enter Old Password" required>
											</div>
										</div>	
										<div class="form-group col-md-4">
											<label>Password</label>
											<div>
												<input type='text' name='password' 	class='form-control' placeholder="Enter New Password"  required>
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
</div>
<?php include_once('inc/footer.php'); ?>
</body>
</html>