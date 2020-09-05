<?php
		include_once('inc/head.php');
		include_once('inc/header.php');
		include_once('classes/user_class.php');
?>
<?php
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	$userClass = new User_class();
	$getid = $helpers->clearSlashes($_GET);
	$getid = base64_decode($getid['uid']);
	$resUser = $mysqlClass->get_field_data(" * ", "`add_cust`", " WHERE `id`='".$getid."' ");
	
		  if (empty($resUser)) {
  	
          
                    echo "<script>alert('Invalid User Id')</script>";
		            echo '<script>window.location.assign("user-list.php")</script>';
		            die();
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
								<span>Update Pin</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Update Pin </div>
							</div>
							<div class="portlet-body">
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12">
								<?php
									if(@$msg!=""){
										echo $msg;
									}
								
								?>	
<?php
	$usertype = $userClass->check_user_type($getid);
$utype = '';
 	if($usertype['usertype']=="white_label"){
		$utype = 5;
	}
	if($usertype['usertype']=="distributer"){
		$utype = 4;
	} 
?>								
                                <div class="col-md-12 ">
									<form method ="post" class="smart-form" action ="" enctype="multipart/form-data">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group col-md-4">
													<label>User Type</label>
													<div>
														<input type='text' value="<?=$usertype['usertype']?>" class='form-control' disabled />
														<input type='hidden' value="<?=$utype?>" name="usertype" class='form-control'  />
													</div>
												</div>
												<div class="form-group col-md-4" id="domain" >
													
												</div>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Name</label>
											<div>
												<input type='text' name='name'  value="<?=$resUser['name']?>" class='form-control' placeholder="Name" readonly>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>User Name</label>
											<div>
												<input type='text' name='user' value="<?=$resUser['user']?>" class='form-control' placeholder="User Name" readonly>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Mobile</label>
											<div>
												<input type='text' name='mobile' value="<?=$resUser['mobile']?>"  class='form-control' placeholder="Mobile" readonly  >
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Pin</label>
											<div>
												<input type='text' name='pin'  class='form-control' placeholder="Enter New Pin">
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Confirm Pin</label>
											<div>
												<input type='text' name='con_pin'  class='form-control' placeholder="Enter Confirm Pin">
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
<?php
	if(isset($_POST['submit']))  {
		$post = $helpers->clearSlashes($_POST);
		if (empty($post['pin']) || !isset($post['pin'])) {
                   
                     echo "<script>alert('Can not empty Pin')</script>";
                     unset($_POST);
                     die();
		}
		if (empty($post['con_pin']) || !isset($post['con_pin'])) {
                   
                     echo "<script>alert('Can not empty confirm Pin')</script>";
                     unset($_POST);
                     die();
		}
		if ($post['pin'] != $post['con_pin']) {
                   
                     echo "<script>alert('Pin Not Match!')</script>";
                     unset($_POST);
                     die();
		}
	    
	    $hash_pass = $helpers->hashPin($post['pin']);
						
			$value = array(
				'security_pin' 	=> $hash_pass['encrypted'],
			);
	$last_id = $mysqlClass->updateData('add_cust', $value, " where id = '".$getid."'");
	if($last_id) {
           
        echo "<script>alert('Pin Change Successfully')</script>";
    	echo '<script>window.location.assign("user-list.php")</script>';
	 }
	 	else {
          echo "<script>alert('Invalid Request')</script>";
		  echo '<script>window.location.assign("user-list.php")</script>';
	}
	}  
?>
</body>
</html>