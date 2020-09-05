<?php
include_once('inc/head.php');
include_once('inc/header.php');

?>
<?php
	include_once('classes/user_class.php'); 
	$helpers = new helper_class();
	
	$post = $helpers->clearSlashes($_POST);
	$get = $helpers->clearSlashes($_GET);
	$pid = base64_decode($get['id']);
	
	$mysqlClass = new Mysql_class();

	$resPN = $mysqlClass->get_field_data(" id, package_name, default_package ", "package_list", " where id = '".$pid."' and created_by = '".$_SESSION[_session_usertype_]."'");
	if(!isset($resPN['id']) || empty($resPN['id'])){
		die();
	}

	
	if(isset($_POST['submit'])){
		
		$dValue = array( 'package_name' => $post['packageName'] );
		$mysqlClass->updateData(' package_list ', $dValue, " where id = '".$pid."' and creator_id = '".$_SESSION[_session_userid_]."' ");

		
		foreach($post['row_id'] as $k1=>$v1){
			if(!empty($v1)){
				$value = array(
									'wl_commission' => $post['wl_comm'][$k1],
									'commission_type' => $post['wl_comm_type'][$k1]
								);
				$mysqlClass->updateData(' wl_package_commission ', $value, " where package_id = '{$resPN['id']}' and network = '{$post['operator_code'][$k1]}' and id = '{$v1}'");
			}
		}
		
		echo "<script> alert('Package update successfully.'); window.location = 'update-wl-user-package.php?id=".base64_encode($pid)."' </script>";
		
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
								<span>Update User Package</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Update User Package </div>
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
										<input type="hidden" value="<?=$resAdminPackage['package_id']?>" readonly name="default_package" />
										<div class="row">
											<div class="form-group">
												<label class="control-label col-md-2">Package Name </label>
												<div class="col-md-4">
													<input type='text' name='packageName' class='form-control' value="<?=$resPN['package_name']?>" placeholder="Package Name " required>
													<p style="color:red;margin-bottom: 0px;" id="packageName" ></p>
												</div>
											</div>											
										</div>
										<br/>
										<div class="table-responsive packageCommission">
											<?php $operator_type = $mysqlClass->fetchAllData(" network "," operator_type ","  group by `operator_type` "); ?>
                                             <table class="table " id="packageTable">
												<tbody>
													<?php foreach($operator_type as $ot){  $opt = $ot['operator_type']; ?>
													<tr>
														<th colspan="8" style="background:#5b6874;text-align: center;color: #fff;"><?=$opt?> Commission</th>
													</tr>
													<tr>
														<th>Network</th>
														<th>Admin Commission (Software)</th>
														<th>WL User Commission</th>
													</tr>
													<?php
														/* Start prepaid */
														$sqPrepaid = $mysqlClass->mysqlQuery("SELECT * FROM `network` WHERE `operator_type`='".$opt."' ");
														while($rowPrepaid = $sqPrepaid->fetch(PDO::FETCH_ASSOC)){
															
															$resWLCommission = $mysqlClass->get_field_data( " * ", "wl_package_commission", " where package_id = '".$pid."' and network = '".$rowPrepaid['np_operator_code']."' ");
															
															$resAdminCommission = $mysqlClass->get_field_data( " * ", "admin_package_commission", " where package_id = '".$resPN['default_package']."' and network = '".$rowPrepaid['np_operator_code']."' ");
													?>
													<tr>
														<td>
															<?=$rowPrepaid['operator_name']?>
															<input type="hidden" name="operator_code[]" value="<?=$rowPrepaid['np_operator_code']?>" readonly style="border:none" />
															<input type="hidden" name="row_id[]" value="<?=$resWLCommission['id']?>" readonly style="border:none" />
															
														</td>
														
														<td><input type="text" value="<?=$resAdminCommission['commission']?>" readonly style="border: none;"   /> - <?=$resAdminCommission['commission_type']?></td>
														<td>
															<input type="text" name="wl_comm[]" value="<?=$resWLCommission['wl_commission']?>" required   />
															<select name="wl_comm_type[]">
																<option <?=($resWLCommission['commission_type']=="PERCENT")? 'selected' : '' ?> value='PERCENT'>PERCENT</option>
																<option <?=($resWLCommission['commission_type']=="FLAT")? 'selected' : '' ?> value='FLAT'>FLAT</option>
															</select>	
														</td>
													</tr>
													<?php
														}
													}
													?>
												</tbody>
												</table>
											</div>
										<div class="form-group">
											<input type="submit" name="submit" onclick="return checkPackage()" class="btn btn-primary pull-right" value="Update Package"/>
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
