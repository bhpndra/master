<?php
include_once('inc/head.php');
include_once('inc/header.php');

if($_SESSION[_session_usertype_]!="ADMIN" && $_SESSION[_session_userid_]!=1){
	echo "<script> window.location = 'index.php'; </script>"; die();
}
?>
<?php
	include_once('classes/user_class.php'); 
	$helpers = new helper_class();
	$post = $helpers->clearSlashes($_POST);
	$mysqlClass = new Mysql_class();
	
	if(isset($_POST['submit'])){
		
		$dValue = array(
						'package_name' => $post['packageName'],
						'creator_id' => 1,
						'created_by' => 'ADMIN',
						'created_for' => 'SOFTWARE',
						'created_on' => date("Y-m-d H:i:s")
					);
		$id = $mysqlClass->insertData(' package_list ', $dValue);

/* ******************************* INSERT prepaid Values ******************************** */
		$values = array();
		foreach($post['operator_code'] as $k1=>$v1){
			$values[$k1] = "({$id},
							'{$post['operator_code'][$k1]}',
							'{$post['sw_comm_type'][$k1]}',
							'{$post['sw_comm'][$k1]}'
						)";
		}
		$queryValues = implode(",",$values);
		
		$commission_package = "INSERT INTO `admin_package_commission`(`package_id`, `network`, `commission_type`, `commission`) VALUES {$queryValues} " ;
		
		$que = $mysqlClass->mysqlQuery($commission_package);
		if($que){
			echo "<script> alert('New Package created successfully.'); window.location = 'create-package.php' </script>";
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
								<span>Add Software Package</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Add Software Package </div>
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
										<div class="row">
											<div class="form-group">
												<label class="control-label col-md-2">Package Name </label>
												<div class="col-md-4">
													<input type='text' onblur="check_packageName(this)" name='packageName' class='form-control' placeholder="Package Name " required>
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
														<th>Our Commission</th>
														<th>Software Commission</th>
													</tr>
													<?php
														/* Start prepaid */
														$sqPrepaid = $mysqlClass->mysqlQuery("SELECT * FROM `network` WHERE `operator_type`='".$opt."' ");
														while($rowPrepaid = $sqPrepaid->fetch(PDO::FETCH_ASSOC)){
													?>
													<tr>
														<td>
															<?=$rowPrepaid['operator_name']?>
															<input type="hidden" name="operator_code[]" value="<?=$rowPrepaid['np_operator_code']?>" readonly style="border:none" />
															<input type="hidden" name="operator_type[]" value="<?=$opt?>" readonly style="border:none" />
														</td>
														<td><input type="text" value="<?=$rowPrepaid['commission']?>" readonly style="border: none;"   /> - <?=$rowPrepaid['commission_type']?></td>
														<td>
															<input type="text" name="sw_comm[]" value="0" required   />
															<select name="sw_comm_type[]">
																<option value='PERCENT'>PERCENT</option>
																<option value='FLAT'>FLAT</option>
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
											<input type="submit" name="submit" onclick="return checkPackage()" class="btn btn-primary pull-right" value="Create New Package"/>
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
