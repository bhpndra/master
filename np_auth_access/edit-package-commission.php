<?php
include_once('inc/head.php');
include_once('inc/header.php');

?>
<?php
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	
	$post = $helpers->clearSlashes($_POST);
	$get = $helpers->clearSlashes($_GET);
	
	
	$resPN = $mysqlClass->get_field_data(" id, package_name ", "package_list", " where id = '".$get['id']."' ");
	if(!isset($resPN['id']) || empty($resPN['id'])){
		die();
	}
	
	if(isset($_POST['submit'])){

		$dValue = array( 'package_name' => $post['packageName'] );
		$mysqlClass->updateData(' package_list ', $dValue, " where id = '".$get['id']."' ");

/* ******************************* Update prepaid Values ******************************** */
		$valuePrepaid = array();
		foreach($post['prepaid_operator_code'] as $k1=>$v1){
			$valuePrepaid = array(
								'commission_type' => 'PERCENT',
								'my_commission' => $post['prepaid_my_commission'][$k1],
								'my_profit' => $post['prepaid_my_profit'][$k1],
								'wl_commission' => $post['prepaid_wl_commission'][$k1],
								'wl_profit' => $post['prepaid_wl_profit'][$k1],
								'master_dist_profit' => $post['prepaid_master_dist_profit'][$k1],
								'dist_profit' => $post['prepaid_dist_profit'][$k1],
								'retailer_profit' => $post['prepaid_retailer_profit'][$k1]
							);
			$mysqlClass->updateData(' package_commission_new ', $valuePrepaid, " where pid = '{$get['id']}' and network = '{$post['prepaid_operator_code'][$k1]}' ");
		}

/* ******************************* Update postpaid Values ******************************** */
		$valuePostpaid = array();
		foreach($post['postpaid_operator_code'] as $k2=>$v2){

			$valuePostpaid = array(
								'commission_type' => $post['postpaid_commission_type'][$k2],
								'my_commission' => $post['postpaid_my_commission'][$k2],
								'my_profit' => $post['postpaid_my_profit'][$k2],
								'wl_commission' => $post['postpaid_wl_commission'][$k2],
								'wl_profit' => $post['postpaid_wl_profit'][$k2],
								'master_dist_profit' => $post['postpaid_master_dist_profit'][$k2],
								'dist_profit' => $post['postpaid_dist_profit'][$k2],
								'retailer_profit' => $post['postpaid_retailer_profit'][$k2]
							);
			$mysqlClass->updateData(' package_commission_new ', $valuePostpaid, " where pid = '{$get['id']}' and network = '{$post['postpaid_operator_code'][$k2]}' ");
		}

		
/* ******************************* Update dth Values ******************************** */
		$valueDTH = array();
		foreach($post['dth_operator_code'] as $k3=>$v3){

			$valueDTH = array(
								'commission_type' => 'PERCENT',
								'my_commission' => $post['dth_my_commission'][$k3],
								'my_profit' => $post['dth_my_profit'][$k3],
								'wl_commission' => $post['dth_wl_commission'][$k3],
								'wl_profit' => $post['dth_wl_profit'][$k3],
								'master_dist_profit' => $post['dth_master_dist_profit'][$k3],
								'dist_profit' => $post['dth_dist_profit'][$k3],
								'retailer_profit' => $post['dth_retailer_profit'][$k3]
							);
			$mysqlClass->updateData(' package_commission_new ', $valueDTH, " where pid = '{$get['id']}' and network = '{$post['dth_operator_code'][$k3]}' ");
		}
		

/* ******************************* Update AEPS Commission Values ******************************** */		
		$valueAEPS = array();
		foreach($post['aeps_operator_code'] as $k4=>$v4){

			$valueAEPS = array(
								
								'my_commission' => $post['aeps_my_commission'][$k4],
								'my_profit' => $post['aeps_my_profit'][$k4],
								'wl_commission' => $post['aeps_wl_commission'][$k4],
								'wl_profit' => $post['aeps_wl_profit'][$k4],
								'master_dist_profit' => $post['aeps_master_dist_profit'][$k4],
								'dist_profit' => $post['aeps_dist_profit'][$k4],
								'retailer_profit' => $post['aeps_retailer_profit'][$k4]
							);
			$mysqlClass->updateData(' package_commission_new ', $valueAEPS, " where pid = '{$get['id']}' and network = '{$post['aeps_operator_code'][$k4]}' ");
		}
		

/* ******************************* Update BBPS Values ******************************** */			
		$valueBBPS = array();
		foreach($post['bbps_operator_code'] as $k5=>$v5){

			$valueBBPS = array(
								'commission_type' => 'PERCENT',
								'my_commission' => $post['bbps_my_commission'][$k5],
								'my_profit' => $post['bbps_my_profit'][$k5],
								'wl_commission' => $post['bbps_wl_commission'][$k5],
								'wl_profit' => $post['bbps_wl_profit'][$k5],
								'master_dist_profit' => $post['bbps_master_dist_profit'][$k5],
								'dist_profit' => $post['bbps_dist_profit'][$k5],
								'retailer_profit' => $post['bbps_retailer_profit'][$k5]
							);
			$mysqlClass->updateData(' package_commission_new ', $valueBBPS, " where pid = '{$get['id']}' and network = '{$post['bbps_operator_code'][$k5]}' ");			
		}

/* ******************************* Update Surcharge Values ******************************** */		
		$valueDMT = array();
		foreach($post['dmt_operator_code'] as $k6=>$v6){
			$valueDMT = array(
								'commission_type' => $post['dmt_commission_type'][$k6],
								'my_commission' => $post['dmt_my_commission'][$k6],
								'my_profit' => $post['dmt_my_profit'][$k6],
								'wl_commission' => $post['dmt_wl_commission'][$k6],
								'wl_profit' => $post['dmt_wl_profit'][$k6],
								'master_dist_profit' => $post['dmt_master_dist_profit'][$k6],
								'dist_profit' => $post['dmt_dist_profit'][$k6],
								'retailer_profit' => $post['dmt_retailer_profit'][$k6]
							);
			$mysqlClass->updateData(' package_commission_new ', $valueDMT, " where pid = '{$get['id']}' and network = '{$post['dmt_operator_code'][$k6]}' ");	
		}

		@$msg .= "<div class=\"alert alert-success alert-dismissable\">";
				$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
							&times;
					  </button>";
				$msg .= "Package update successfully.";
			$msg .= "</div>";

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
								<span>Add New Commission Package</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Add New Commission Package </div>
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
													<input type='text' name='packageName' class='form-control' value="<?=$resPN['package_name']?>" placeholder="Package Name " required>
												</div>
											</div>											
										</div>
										<br/>
										<div class="table-responsive packageCommission">
                                             <table class="table " id="packageTable">
												<tbody>
													<tr>
														<th colspan="8" style="background:#5b6874;text-align: center;color: #fff;">Prepaid Commission</th>
													</tr>
													<tr>
														<th>Network</th>
														<th>OPR Comm [%]</th>
														<th>My Profit [%]</th>
														<th>WL Comm [%]</th>
														<th>WL Profit [%]</th>
														<th>M Dist Profit [%]</th>
														<th>Dist Profit [%]</th>
														<th>Retail Profit [%]</th>
													</tr>
						<?php
							/* Start prepaid */
							$sqPrepaid = $mysqlClass->mysqlQuery("SELECT * FROM `network_new` WHERE `operator_type`='Prepaid' ");
							while($rowPrepaid = $sqPrepaid->fetch(PDO::FETCH_ASSOC)){
							
								$resPrepaid = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = '".$rowPrepaid['np_operator_code']."' ");
						?>
						<tr>
							<td>
								<?=$rowPrepaid['operator_name']?>
								<input type="hidden" name="prepaid_operator_code[]" value="<?=$rowPrepaid['np_operator_code']?>" readonly style="border:none" />
							</td>
							<td><input type="text" name="prepaid_my_commission[]" value="<?=$rowPrepaid['commission']?>" readonly style="border:none" /></td>
							<td><input type="text" name="prepaid_my_profit[]" value="<?=$resPrepaid['my_profit']?>" required /></td>
							<td><input type="text" name="prepaid_wl_commission[]" value="<?=$resPrepaid['wl_commission']?>" required /></td>
							<td><input type="text" name="prepaid_wl_profit[]" value="<?=$resPrepaid['wl_profit']?>" required /></td>
							<td><input type="text" name="prepaid_master_dist_profit[]" value="<?=$resPrepaid['master_dist_profit']?>" required /></td>
							<td><input type="text" name="prepaid_dist_profit[]" value="<?=$resPrepaid['dist_profit']?>" required /></td>
							<td><input type="text" name="prepaid_retailer_profit[]" value="<?=$resPrepaid['retailer_profit']?>" required /></td>
						</tr>
						<?php
							}
							/* end prepaid */
						?>	
						
						<tr>
							<th colspan="8" style="background:#5b6874;text-align: center;color: #fff;">Postpaid Commission</th>
						</tr>
						<?php
							/* Start postpaid */
							$sqPostpaid = $mysqlClass->mysqlQuery("SELECT * FROM `network_new` WHERE `operator_type`='Postpaid' ");
							while($rowPostpaid = $sqPostpaid->fetch(PDO::FETCH_ASSOC)){
								
								$resPostpaid = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = '".$rowPostpaid['np_operator_code']."' ");
						?>
						<tr>
							<td>
								<?=$rowPostpaid['operator_name']?>
								<select name="postpaid_commission_type[]">
									<option <?php if($resPostpaid['commission_type']=="PERCENT"){ echo "selected"; } ?> value="PERCENT">PERCENT</option>
									<option <?php if($resPostpaid['commission_type']=="FLAT"){ echo "selected"; } ?> value="FLAT">FLAT</option>
								</select>
								<input type="hidden" name="postpaid_operator_code[]" value="<?=$rowPostpaid['np_operator_code']?>" readonly style="border:none" />
							</td>
							<td><input type="text" name="postpaid_my_commission[]" value="<?=$rowPostpaid['commission']?>" readonly style="border:none" /></td>
							<td><input type="text" name="postpaid_my_profit[]" value="<?=$resPostpaid['my_profit']?>" required /></td>
							<td><input type="text" name="postpaid_wl_commission[]" value="<?=$resPostpaid['wl_commission']?>" required /></td>
							<td><input type="text" name="postpaid_wl_profit[]" value="<?=$resPostpaid['wl_profit']?>" required /></td>
							<td><input type="text" name="postpaid_master_dist_profit[]" value="<?=$resPostpaid['master_dist_profit']?>" required /></td>
							<td><input type="text" name="postpaid_dist_profit[]" value="<?=$resPostpaid['dist_profit']?>" required /></td>
							<td><input type="text" name="postpaid_retailer_profit[]" value="<?=$resPostpaid['retailer_profit']?>" required /></td>
						</tr>
						<?php
							}
							/* end postpaid */
						?>
						
						<tr>
							<th colspan="8" style="background:#5b6874;text-align: center;color: #fff;">DTH Commission</th>
						</tr>
						<?php
							/* Start DTH */
							$sqDTH = $mysqlClass->mysqlQuery("SELECT * FROM `network_new` WHERE `operator_type`='dth' ");
							while($rowDTH = $sqDTH->fetch(PDO::FETCH_ASSOC)){
								$resDTH = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = '".$rowDTH['np_operator_code']."' ");
						?>
						<tr>
							<td>
								<?=$rowDTH['operator_name']?>
								<input type="hidden" name="dth_operator_code[]" value="<?=$rowDTH['np_operator_code']?>" readonly style="border:none" />
							</td>
							<td><input type="text" name="dth_my_commission[]" value="<?=$rowDTH['commission']?>" readonly style="border:none" /></td>
							<td><input type="text" name="dth_my_profit[]" value="<?=$resDTH['my_profit']?>" required /></td>
							<td><input type="text" name="dth_wl_commission[]" value="<?=$resDTH['wl_commission']?>" required /></td>
							<td><input type="text" name="dth_wl_profit[]" value="<?=$resDTH['wl_profit']?>" required /></td>
							<td><input type="text" name="dth_master_dist_profit[]" value="<?=$resDTH['master_dist_profit']?>" required /></td>
							<td><input type="text" name="dth_dist_profit[]" value="<?=$resDTH['dist_profit']?>" required /></td>
							<td><input type="text" name="dth_retailer_profit[]" value="<?=$resDTH['retailer_profit']?>" required /></td>
						</tr>
						<?php
							}
							/* end DTH */
						?>
						<tr>
							<th colspan="8" style="background:#5b6874;text-align: center;color: #fff;">Device Commission</th>
						</tr>
						<?php
							/* Start Device */
							$sqMPOS = $mysqlClass->mysqlQuery("SELECT * FROM `network_new` WHERE `operator_type`='MPOS' ");
							while($rowMPOS = $sqMPOS->fetch(PDO::FETCH_ASSOC)){
						?>
						<tr>
							<td>
								<?=$rowMPOS['operator_name']?>
								<input type="hidden" name="commission" value="<?=$rowMPOS['np_operator_code']?>" readonly style="border:none" />
							</td>
							<td><input type="text" name="commission" value="<?=$rowMPOS['commission']?>" readonly style="border:none" /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="<?=$rowMPOS['commission']?>" required /></td>
						</tr>
						<?php
							}
							/* end Device */
						?>
						<tr>
							<th colspan="8" style="background:#5b6874;text-align: center;color: #fff;">TRAVELS  Commission</th>
						</tr>
						<?php
							/* Start TRAVELS */
							$sqTRAVEL = $mysqlClass->mysqlQuery("SELECT * FROM `network_new` WHERE `operator_type`='TRAVEL' ");
							while($rowTRAVEL = $sqTRAVEL->fetch(PDO::FETCH_ASSOC)){
						?>
						<tr>
							<td>
								<?=$rowTRAVEL['operator_name']?>
								<input type="hidden" name="commission" value="<?=$rowTRAVEL['np_operator_code']?>" readonly style="border:none" />
							</td>
							<td><input type="text" name="commission" value="<?=$rowTRAVEL['commission']?>" readonly style="border:none" /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="<?=$rowTRAVEL['commission']?>" required /></td>
						</tr>
						<?php
							}
							/* end TRAVELS */
						?>
						<tr>
							<th colspan="8" style="background:#5b6874;text-align: center;color: #fff;">BroadBand  Commission</th>
						</tr>
						<?php
							/* Start BroadBand */
							$sqBroadBand = $mysqlClass->mysqlQuery("SELECT * FROM `network_new` WHERE `operator_type`='BROADBAND' ");
							while($rowBroadBand = $sqBroadBand->fetch(PDO::FETCH_ASSOC)){
						?>
						<tr>
							<td>
								<?=$rowBroadBand['operator_name']?>
								<input type="hidden" name="commission" value="<?=$rowBroadBand['commission']?>" readonly style="border:none" />
							</td>
							<td><input type="text" name="commission" value="<?=$rowBroadBand['commission']?>" readonly style="border:none" /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="0" required /></td>
							<td><input type="text" name="commission" value="<?=$rowBroadBand['commission']?>" required /></td>
						</tr>
						<?php
							}
							/* end BroadBand */
						?>
					<tr>
						<th colspan="8" style="background:#5b6874;text-align: center;color: #fff;">AEPS  Commission</th>
					</tr>
					<?php
						/* Start AEPS */
					?>
					<tr>
					<?php
						$resAEPS1 = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'AEPS1' ");
					?>
						<td>
							AEPS (500-999)
							<input type="hidden" name="aeps_operator_code[]" value="AEPS1" readonly style="border:none" />
						</td>
						<td><input type="text" name="aeps_my_commission[]" value="<?=$resAEPS1['my_commission']?>" readonly style="border:none" /></td>
						<td><input type="text" name="aeps_my_profit[]" value="<?=$resAEPS1['my_profit']?>" required /></td>
						<td><input type="text" name="aeps_wl_commission[]" value="<?=$resAEPS1['wl_commission']?>" required /></td>
						<td><input type="text" name="aeps_wl_profit[]" value="<?=$resAEPS1['wl_profit']?>" required /></td>
						<td><input type="text" name="aeps_master_dist_profit[]" value="<?=$resAEPS1['master_dist_profit']?>" required /></td>
						<td><input type="text" name="aeps_dist_profit[]" value="<?=$resAEPS1['dist_profit']?>" required /></td>
						<td><input type="text" name="aeps_retailer_profit[]" value="<?=$resAEPS1['retailer_profit']?>" required /></td>
					</tr>
					<tr>
					<?php
						$resAEPS2 = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'AEPS2' ");
					?>
						<td>
							AEPS(1000 - 1499) 
							<input type="hidden" name="aeps_operator_code[]" value="AEPS2" readonly style="border:none" />
						</td>
						<td><input type="text" name="aeps_my_commission[]" value="<?=$resAEPS2['my_commission']?>" readonly style="border:none" /></td>
						<td><input type="text" name="aeps_my_profit[]" value="<?=$resAEPS2['my_profit']?>" required /></td>
						<td><input type="text" name="aeps_wl_commission[]" value="<?=$resAEPS2['wl_commission']?>" required /></td>
						<td><input type="text" name="aeps_wl_profit[]" value="<?=$resAEPS2['wl_profit']?>" required /></td>
						<td><input type="text" name="aeps_master_dist_profit[]" value="<?=$resAEPS2['master_dist_profit']?>" required /></td>
						<td><input type="text" name="aeps_dist_profit[]" value="<?=$resAEPS2['dist_profit']?>" required /></td>
						<td><input type="text" name="aeps_retailer_profit[]" value="<?=$resAEPS2['retailer_profit']?>" required /></td>
					</tr>													
					<tr>
					<?php
						$resAEPS3 = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'AEPS3' ");
					?>
						<td>
							AEPS(1500 - 1999)  
							<input type="hidden" name="aeps_operator_code[]" value="AEPS3" readonly style="border:none" />
						</td>
						<td><input type="text" name="aeps_my_commission[]" value="<?=$resAEPS3['my_commission']?>" readonly style="border:none" /></td>
						<td><input type="text" name="aeps_my_profit[]" value="<?=$resAEPS3['my_profit']?>" required /></td>
						<td><input type="text" name="aeps_wl_commission[]" value="<?=$resAEPS3['wl_commission']?>" required /></td>
						<td><input type="text" name="aeps_wl_profit[]" value="<?=$resAEPS3['wl_profit']?>" required /></td>
						<td><input type="text" name="aeps_master_dist_profit[]" value="<?=$resAEPS3['master_dist_profit']?>" required /></td>
						<td><input type="text" name="aeps_dist_profit[]" value="<?=$resAEPS3['dist_profit']?>" required /></td>
						<td><input type="text" name="aeps_retailer_profit[]" value="<?=$resAEPS3['retailer_profit']?>" required /></td>
					</tr>												
					<tr>
					<?php
						$resAEPS4 = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'AEPS4' ");
					?>
						<td>
							AEPS(2000 - 2999)  
							<input type="hidden" name="aeps_operator_code[]" value="AEPS4" readonly style="border:none" />
						</td>
						<td><input type="text" name="aeps_my_commission[]" value="<?=$resAEPS4['my_commission']?>" readonly style="border:none" /></td>
						<td><input type="text" name="aeps_my_profit[]" value="<?=$resAEPS4['my_profit']?>" required /></td>
						<td><input type="text" name="aeps_wl_commission[]" value="<?=$resAEPS4['wl_commission']?>" required /></td>
						<td><input type="text" name="aeps_wl_profit[]" value="<?=$resAEPS4['wl_profit']?>" required /></td>
						<td><input type="text" name="aeps_master_dist_profit[]" value="<?=$resAEPS4['master_dist_profit']?>" required /></td>
						<td><input type="text" name="aeps_dist_profit[]" value="<?=$resAEPS4['dist_profit']?>" required /></td>
						<td><input type="text" name="aeps_retailer_profit[]" value="<?=$resAEPS4['retailer_profit']?>" required /></td>
					</tr>												
					<tr>
					<?php
						$resAEPS5 = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'AEPS5' ");
					?>
						<td>
							AEPS(3000 - Above)  
							<input type="hidden" name="aeps_operator_code[]" value="AEPS5" readonly style="border:none" />
						</td>
						<td><input type="text" name="aeps_my_commission[]" value="<?=$resAEPS5['my_commission']?>" readonly style="border:none" /></td>
						<td><input type="text" name="aeps_my_profit[]" value="<?=$resAEPS5['my_profit']?>" required /></td>
						<td><input type="text" name="aeps_wl_commission[]" value="<?=$resAEPS5['wl_commission']?>" required /></td>
						<td><input type="text" name="aeps_wl_profit[]" value="<?=$resAEPS5['wl_profit']?>" required /></td>
						<td><input type="text" name="aeps_master_dist_profit[]" value="<?=$resAEPS5['master_dist_profit']?>" required /></td>
						<td><input type="text" name="aeps_dist_profit[]" value="<?=$resAEPS5['dist_profit']?>" required /></td>
						<td><input type="text" name="aeps_retailer_profit[]" value="<?=$resAEPS5['retailer_profit']?>" required /></td>
					</tr>
					<?php
						/* end AEPS */
					?>
													
					<tr>
						<th colspan="8" style="background:#5b6874;text-align: center;color: #fff;">BBPS  Commission</th>
					</tr>
					<?php
						/* Start BBPS */
					?>
					<tr>
					<?php
						$resELECTR = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'ELECTR' ");
					?>
						<td>
							Electricity <!--<font color="red"> [Surcharge ] </font>-->
							<input type="hidden" name="bbps_operator_code[]" value="ELECTR" readonly style="border:none" />
						</td>
						<td><input type="text" name="bbps_my_commission[]" value="<?=$resELECTR['my_commission']?>" readonly style="border:none" /></td>
						<td><input type="text" name="bbps_my_profit[]" value="<?=$resELECTR['my_profit']?>" required /></td>
						<td><input type="text" name="bbps_wl_commission[]" value="<?=$resELECTR['wl_commission']?>" required /></td>
						<td><input type="text" name="bbps_wl_profit[]" value="<?=$resELECTR['wl_profit']?>" required /></td>
						<td><input type="text" name="bbps_master_dist_profit[]" value="<?=$resELECTR['master_dist_profit']?>" required /></td>
						<td><input type="text" name="bbps_dist_profit[]" value="<?=$resELECTR['dist_profit']?>" required /></td>
						<td><input type="text" name="bbps_retailer_profit[]" value="<?=$resELECTR['retailer_profit']?>" required /></td>
					</tr>
					<tr>
					<?php
						$resGAS = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'GAS' ");
					?>
						<td>
							Gas <!--<font color="red"> [Surcharge ] </font>-->
							<input type="hidden" name="bbps_operator_code[]" value="GAS" readonly style="border:none" />
						</td>
						<td><input type="text" name="bbps_my_commission[]" value="<?=$resGAS['my_commission']?>" readonly style="border:none" /></td>
						<td><input type="text" name="bbps_my_profit[]" value="<?=$resGAS['my_profit']?>" required /></td>
						<td><input type="text" name="bbps_wl_commission[]" value="<?=$resGAS['wl_commission']?>" required /></td>
						<td><input type="text" name="bbps_wl_profit[]" value="<?=$resGAS['wl_profit']?>" required /></td>
						<td><input type="text" name="bbps_master_dist_profit[]" value="<?=$resGAS['master_dist_profit']?>" required /></td>
						<td><input type="text" name="bbps_dist_profit[]" value="<?=$resGAS['dist_profit']?>" required /></td>
						<td><input type="text" name="bbps_retailer_profit[]" value="<?=$resGAS['retailer_profit']?>" required /></td>
					</tr>													
					<tr>
					<?php
						$resINSUR = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'INSUR' ");
					?>
						<td>
							Insurance <!--<font color="red"> [Surcharge ] </font>-->
							<input type="hidden" name="bbps_operator_code[]" value="INSUR" readonly style="border:none" />
						</td>
						<td><input type="text" name="bbps_my_commission[]" value="0" readonly style="border:none" /></td>
						<td><input type="text" name="bbps_my_profit[]" value="<?=$resINSUR['my_profit']?>" required /></td>
						<td><input type="text" name="bbps_wl_commission[]" value="<?=$resINSUR['wl_commission']?>" required /></td>
						<td><input type="text" name="bbps_wl_profit[]" value="<?=$resINSUR['wl_profit']?>" required /></td>
						<td><input type="text" name="bbps_master_dist_profit[]" value="<?=$resINSUR['master_dist_profit']?>" required /></td>
						<td><input type="text" name="bbps_dist_profit[]" value="<?=$resINSUR['dist_profit']?>" required /></td>
						<td><input type="text" name="bbps_retailer_profit[]" value="<?=$resINSUR['retailer_profit']?>" required /></td>
					</tr>
					<?php
						/* end BBPS */
					?>
				</tbody>
			 </table>
					
			<table class="table " id="">
				<tbody>
					<tr>
						<th colspan="8" style="background:#620522;text-align: center;color: #fff;">DMT  Surcharge</th>
					</tr>
					<tr>
						<th>Network</th>
						<th>OPR Comm </th>
						<th>My Profit </th>
						<th>WL Surchr </th>
						<th>WL Profit </th>
						<th>M Dist Profit </th>
						<th>Dist Profit </th>
						<th>Retail Profit </th>
					</tr>
					<?php
						/* Start DMT Surcharge */
					?>
					<tr>
					<?php
						$resDMT1 = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'dmt1' ");
					?>
						<td>
							DMT (11 - 1000)
							<select name="dmt_commission_type[]">
								<option <?php if($resDMT1['commission_type']=="PERCENT"){ echo "selected"; } ?> value="PERCENT">PERCENT</option>
								<option <?php if($resDMT1['commission_type']=="FLAT"){ echo "selected"; } ?> value="FLAT">FLAT</option>
							</select>
							<input type="hidden" name="dmt_operator_code[]" value="dmt1" readonly style="border:none" />
						</td>
						<td><input type="text" name="dmt_my_commission[]" value="<?=$resDMT1['my_commission']?>" readonly style="border:none" /></td>
						<td><input type="text" name="dmt_my_profit[]" value="<?=$resDMT1['my_profit']?>" required /></td>
						<td><input type="text" name="dmt_wl_commission[]" value="<?=$resDMT1['wl_commission']?>" required /></td>
						<td><input type="text" name="dmt_wl_profit[]" value="<?=$resDMT1['wl_profit']?>" required /></td>
						<td><input type="text" name="dmt_master_dist_profit[]" value="<?=$resDMT1['master_dist_profit']?>" required /></td>
						<td><input type="text" name="dmt_dist_profit[]" value="<?=$resDMT1['dist_profit']?>" required /></td>
						<td><input type="text" name="dmt_retailer_profit[]" value="<?=$resDMT1['retailer_profit']?>" required /></td>
					</tr>
					<tr>
					<?php
						$resDMT2 = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'dmt2' ");
					?>
						<td>
							DMT(1001 - 2000)
							<select name="dmt_commission_type[]">
								<option <?php if($resDMT2['commission_type']=="PERCENT"){ echo "selected"; } ?> value="PERCENT">PERCENT</option>
								<option <?php if($resDMT2['commission_type']=="FLAT"){ echo "selected"; } ?> value="FLAT">FLAT</option>
							</select>
							<input type="hidden" name="dmt_operator_code[]" value="dmt2" readonly style="border:none" />
						</td>
						<td><input type="text" name="dmt_my_commission[]" value="<?=$resDMT2['my_commission']?>" readonly style="border:none" /></td>
						<td><input type="text" name="dmt_my_profit[]" value="<?=$resDMT2['my_profit']?>" required /></td>
						<td><input type="text" name="dmt_wl_commission[]" value="<?=$resDMT2['wl_commission']?>" required /></td>
						<td><input type="text" name="dmt_wl_profit[]" value="<?=$resDMT2['wl_profit']?>" required /></td>
						<td><input type="text" name="dmt_master_dist_profit[]" value="<?=$resDMT2['master_dist_profit']?>" required /></td>
						<td><input type="text" name="dmt_dist_profit[]" value="<?=$resDMT2['dist_profit']?>" required /></td>
						<td><input type="text" name="dmt_retailer_profit[]" value="<?=$resDMT2['retailer_profit']?>" required /></td>
					</tr>
					<tr>
					<?php
						$resDMT3 = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'dmt3' ");
					?>
						<td>
							DMT(2001 - 3000)
							<select name="dmt_commission_type[]">
								<option <?php if($resDMT3['commission_type']=="PERCENT"){ echo "selected"; } ?> value="PERCENT">PERCENT</option>
								<option <?php if($resDMT3['commission_type']=="FLAT"){ echo "selected"; } ?> value="FLAT">FLAT</option>
							</select>
							<input type="hidden" name="dmt_operator_code[]" value="dmt3" readonly style="border:none" />
						</td>
						<td><input type="text" name="dmt_my_commission[]" value="<?=$resDMT3['my_commission']?>" readonly style="border:none" /></td>
						<td><input type="text" name="dmt_my_profit[]" value="<?=$resDMT3['my_profit']?>" required /></td>
						<td><input type="text" name="dmt_wl_commission[]" value="<?=$resDMT3['wl_commission']?>" required /></td>
						<td><input type="text" name="dmt_wl_profit[]" value="<?=$resDMT3['wl_profit']?>" required /></td>
						<td><input type="text" name="dmt_master_dist_profit[]" value="<?=$resDMT3['master_dist_profit']?>" required /></td>
						<td><input type="text" name="dmt_dist_profit[]" value="<?=$resDMT3['dist_profit']?>" required /></td>
						<td><input type="text" name="dmt_retailer_profit[]" value="<?=$resDMT3['retailer_profit']?>" required /></td>
					</tr>
					<tr>
					<?php
						$resDMT4 = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'dmt4' ");
					?>
						<td>
							DMT(3001 - 4000)
							<select name="dmt_commission_type[]">
								<option <?php if($resDMT4['commission_type']=="PERCENT"){ echo "selected"; } ?> value="PERCENT">PERCENT</option>
								<option <?php if($resDMT4['commission_type']=="FLAT"){ echo "selected"; } ?> value="FLAT">FLAT</option>
							</select>
							<input type="hidden" name="dmt_operator_code[]" value="dmt4" readonly style="border:none" />
						</td>
						<td><input type="text" name="dmt_my_commission[]" value="<?=$resDMT4['my_commission']?>" readonly style="border:none" /></td>
						<td><input type="text" name="dmt_my_profit[]" value="<?=$resDMT4['my_profit']?>" required /></td>
						<td><input type="text" name="dmt_wl_commission[]" value="<?=$resDMT4['wl_commission']?>" required /></td>
						<td><input type="text" name="dmt_wl_profit[]" value="<?=$resDMT4['wl_profit']?>" required /></td>
						<td><input type="text" name="dmt_master_dist_profit[]" value="<?=$resDMT4['master_dist_profit']?>" required /></td>
						<td><input type="text" name="dmt_dist_profit[]" value="<?=$resDMT4['dist_profit']?>" required /></td>
						<td><input type="text" name="dmt_retailer_profit[]" value="<?=$resDMT4['retailer_profit']?>" required /></td>
					</tr>
					<tr>
					<?php
						$resDMT5 = $mysqlClass->get_field_data( " * ", "package_commission_new", " where pid = '".$get['id']."' and network = 'dmt5' ");
					?>
						<td>
							DMT(4001 - 5000)
							<select name="dmt_commission_type[]">
								<option <?php if($resDMT5['commission_type']=="PERCENT"){ echo "selected"; } ?> value="PERCENT">PERCENT</option>
								<option <?php if($resDMT5['commission_type']=="FLAT"){ echo "selected"; } ?> value="FLAT">FLAT</option>
							</select>
							<input type="hidden" name="dmt_operator_code[]" value="dmt5" readonly style="border:none" />
						</td>
						<td><input type="text" name="dmt_my_commission[]" value="<?=$resDMT5['my_commission']?>" readonly style="border:none" /></td>
						<td><input type="text" name="dmt_my_profit[]" value="<?=$resDMT5['my_profit']?>" required /></td>
						<td><input type="text" name="dmt_wl_commission[]" value="<?=$resDMT5['wl_commission']?>" required /></td>
						<td><input type="text" name="dmt_wl_profit[]" value="<?=$resDMT5['wl_profit']?>" required /></td>
						<td><input type="text" name="dmt_master_dist_profit[]" value="<?=$resDMT5['master_dist_profit']?>" required /></td>
						<td><input type="text" name="dmt_dist_profit[]" value="<?=$resDMT5['dist_profit']?>" required /></td>
						<td><input type="text" name="dmt_retailer_profit[]" value="<?=$resDMT5['retailer_profit']?>" required /></td>
					</tr>
					<?php
						/* end DMT Surcharge */
					?>
												</tbody>
											 </table>
										</div>
										<div class="form-group">
											<input type="submit" name="submit" onclick="return checkPackage()" class="btn btn-primary pull-right" value="Update Commission"/>
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
<script>
$(document).ready(function(){
	/* *********** Check error commission row  ************* */
	$('#packageTable tr').each(function() {
		
	  var inputs = $(this).find('input'), row = this;
	  inputs.each(function() {
		  
		var my_commission = inputs[1].value;
		
		var commi_distribute = parseFloat(inputs[2].value) + parseFloat(inputs[3].value) + parseFloat(inputs[4].value) + parseFloat(inputs[5].value) + parseFloat(inputs[6].value) + parseFloat(inputs[7].value);
		
		if(parseFloat(my_commission) < parseFloat(commi_distribute)){
			$(row).css("background-color", "#ff0000");
		}
	  });

	});
	
	/* *********** Check when set commission (on blur)  ************* */
	$("#packageTable tr td input").blur(function(event) {
		var c_input = this;
		var row = $(this).closest("tr");
		var inputs = row.find('input');
		if(inputs[1]===this){
			
		} else {
			var my_commission = inputs[1].value;		
			var commi_distribute = parseFloat(inputs[2].value) + parseFloat(inputs[3].value) + parseFloat(inputs[4].value) + parseFloat(inputs[5].value) + parseFloat(inputs[6].value) + parseFloat(inputs[7].value);
			if(parseFloat(my_commission) == my_commission && parseFloat(commi_distribute) == commi_distribute){
				if(parseFloat(my_commission) < parseFloat(commi_distribute)){
					$(row).css("background-color", "#ff0000");	
					$(c_input).val('0');
					alert("Invalid Commission set!");
				} else {
					$(row).css("background-color", "transparent");
				}
			} else {
				$(row).css("background-color", "#ff0000");	
				$(c_input).val('0');
				alert("Invalid Commission set!");
			}
		}
	});
	
});

	/* *********** On submit button  ************* */
	function checkPackage(){ 
		error = false;
		
		$('#packageTable tr').each(function() {
		  var inputs = $(this).find('input'), row = this;
		  /* inputs.each(function() { */
			var my_commission = inputs[1].value;
			
			var commi_distribute = parseFloat(inputs[2].value) + parseFloat(inputs[3].value) + parseFloat(inputs[4].value) + parseFloat(inputs[5].value) + parseFloat(inputs[6].value) + parseFloat(inputs[7].value);
			if(parseFloat(my_commission) < parseFloat(commi_distribute)){
				$(row).css("background-color", "#ff0000");
				error = true;				
			}
		  /* }); */			  
		});
		
		if(error===true){
			  alert("Invalid Commission set! Please check highlighted rows");
			  return false;
		  } else {
			  return true;
		  }
	}
</script>
</body>
</html>
