<?php
	include_once('inc/head.php');
	include_once('inc/header.php');
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	include_once("classes/user_class.php");
	$userClass = new user_class();
	$post = $helpers->clearSlashes($_POST);
?>
<?php
if(!empty($post['bankitOutletId']) && !empty($post['userassign']) && !empty($post['credit'])){
	$uid = $post['userassign'];
	$oid = $post['bankitOutletId'];
	$update = array('user_id' => $uid);
	$status = $mysqlClass->updateData('outlet_kyc',$update,"WHERE id='$oid'");
	if ($status) {
	    echo "<script>alert('Record updated successfully')</script>";
	} else {
	    echo "<script>alert('Error in Query')</script>";
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
								<span>Credit to user</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Assign Outlet</div>
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
									<form method ="post" id="creditForm" class="smart-form" action ="" enctype="multipart/form-data">
													
										<div class="form-group col-md-4">
											<label>Select Outlet</label>
											<div>
												<select name="bankitOutletId" class='form-control select2' required>
													<option value="">Select Outlet</option>
													<?php
														$utypes = $mysqlClass->fetchAllData("outlet_kyc"," * ", " where `user_id` ='0'");
														
														foreach($utypes as $ut){
															
													?>
														<option value="<?=$ut['id']?>"><?=$ut['outletid']?> (<?=$ut['pan_no']?>) - <?=$ut['name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										
										<div class="form-group col-md-4">
											<label>User not assign</label>
											<div>
												<select name="userassign" class='form-control select2' required>
													<option value="">Select user</option>
													<?php
														$add_cust = $mysqlClass->fetchAllData("add_cust","id,mobile,user,cname", " where id not in(SELECT user_id FROM `outlet_kyc` WHERE user_id!='')");
														
														foreach($add_cust as $ac){
															
													?>
														<option value="<?=$ac['id']?>"><?=$ac['outlet_id']?> (<?=$ac['mobile']?>) - <?=$ac['user']?> (<?=$ac['cname']?>)</option>
													<?php } ?>
												</select>
											</div>
										</div>
										
										<div class="form-group col-md-12">
											<div>
												<button type="submit" name="credit" class="btn btn-success" value="Assign">Assign Outlet id</button>
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
