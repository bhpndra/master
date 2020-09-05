<?php
include_once('inc/head.php');
include_once('inc/header.php');

?>
<?php
	$helpers = new helper_class();
	$post = $helpers->clearSlashes($_POST);
	$mysqlClass = new Mysql_class();
	
	if(isset($_POST['submit'])){
		
		$dValue = array(
						'package_name' => $post['packageName'],
						'created_by' => 'ADMIN',
						'creator_id' => $_SESSION[_session_userid_]
					);
		$id = $mysqlClass->insertData(' package_list ', $dValue);

/* ******************************* INSERT prepaid Values ******************************** */
		$valuePrepaid = array();
		foreach($post['prepaid_operator_code'] as $k1=>$v1){
			$valuePrepaid[$k1] = "({$id},
							'{$post['prepaid_operator_code'][$k1]}',
							'PERCENT',
							'{$post['prepaid_my_commission'][$k1]}',
							'{$post['prepaid_my_profit'][$k1]}',
							'{$post['prepaid_wl_commission'][$k1]}',
							'{$post['prepaid_wl_profit'][$k1]}',
							'{$post['prepaid_master_dist_profit'][$k1]}',
							'{$post['prepaid_dist_profit'][$k1]}',
							'{$post['prepaid_retailer_profit'][$k1]}'
						)";
		}
		
/* ******************************* INSERT postpaid Values ******************************** */
		$valuePostpaid = array();
		foreach($post['postpaid_operator_code'] as $k2=>$v2){
			$valuePostpaid[$k2] = "({$id},
							'{$post['postpaid_operator_code'][$k2]}',
							'{$post['postpaid_commission_type'][$k2]}',
							'{$post['postpaid_my_commission'][$k2]}',
							'{$post['postpaid_my_profit'][$k2]}',
							'{$post['postpaid_wl_commission'][$k2]}',
							'{$post['postpaid_wl_profit'][$k2]}',
							'{$post['postpaid_master_dist_profit'][$k2]}',
							'{$post['postpaid_dist_profit'][$k2]}',
							'{$post['postpaid_retailer_profit'][$k2]}'
						)";
		}
		
/* ******************************* INSERT dth Values ******************************** */
		$valueDTH = array();
		foreach($post['dth_operator_code'] as $k3=>$v3){
			$valueDTH[$k3] = "({$id},
							'{$post['dth_operator_code'][$k3]}',
							'PERCENT',
							'{$post['dth_my_commission'][$k3]}',
							'{$post['dth_my_profit'][$k3]}',
							'{$post['dth_wl_commission'][$k3]}',
							'{$post['dth_wl_profit'][$k3]}',
							'{$post['dth_master_dist_profit'][$k3]}',
							'{$post['dth_dist_profit'][$k3]}',
							'{$post['dth_retailer_profit'][$k3]}'
						)";
		}

/* ******************************* AEPS Commission Values ******************************** */		
		$valueAEPS = array();
		foreach($post['aeps_operator_code'] as $k4=>$v4){
			$valueAEPS[$k4] = "({$id},
							'{$post['aeps_operator_code'][$k4]}',
							'{$post['aeps_commission_type'][$k4]}',
							'{$post['aeps_my_commission'][$k4]}',
							'{$post['aeps_my_profit'][$k4]}',
							'{$post['aeps_wl_commission'][$k4]}',
							'{$post['aeps_wl_profit'][$k4]}',
							'{$post['aeps_master_dist_profit'][$k4]}',
							'{$post['aeps_dist_profit'][$k4]}',
							'{$post['aeps_retailer_profit'][$k4]}'
						)";
		}

/* ******************************* BBPS Values ******************************** */			
		$valueBBPS = array();
		foreach($post['bbps_operator_code'] as $k4=>$v4){
			$valueBBPS[$k4] = "({$id},
							'{$post['bbps_operator_code'][$k4]}',
							'PERCENT',
							'{$post['bbps_my_commission'][$k4]}',
							'{$post['bbps_my_profit'][$k4]}',
							'{$post['bbps_wl_commission'][$k4]}',
							'{$post['bbps_wl_profit'][$k4]}',
							'{$post['bbps_master_dist_profit'][$k4]}',
							'{$post['bbps_dist_profit'][$k4]}',
							'{$post['bbps_retailer_profit'][$k4]}'
						)";
		}

/* ******************************* Surcharge Values ******************************** */		
		$valueDMT = array();
		foreach($post['dmt_operator_code'] as $k5=>$v5){
			$valueDMT[$k5] = "({$id},
							'{$post['dmt_operator_code'][$k5]}',
							'{$post['dmt_commission_type'][$k5]}',
							'{$post['dmt_my_commission'][$k5]}',
							'{$post['dmt_my_profit'][$k5]}',
							'{$post['dmt_wl_commission'][$k5]}',
							'{$post['dmt_wl_profit'][$k5]}',
							'{$post['dmt_master_dist_profit'][$k5]}',
							'{$post['dmt_dist_profit'][$k5]}',
							'{$post['dmt_retailer_profit'][$k5]}'
						)";
		}
		
		$values = array_merge($valuePrepaid,$valuePostpaid,$valueDTH,$valueAEPS,$valueBBPS,$valueDMT);
				
		$queryValues = implode(",",$values);
		
		$commission_package = "INSERT INTO `package_commission_new`(`pid`, `network`, `commission_type`,`my_commission`,`my_profit`,`wl_commission`,`wl_profit`,`master_dist_profit`,`dist_profit`,`retailer_profit`) VALUES {$queryValues} " ;
		
		$que = $mysqlClass->mysqlQuery($commission_package);
		if($que){
			echo "<script> alert('New Package created successfully.'); window.location = 'add-new-commission.php' </script>";
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
													<input type='text' onblur="check_packageName(this)" name='packageName' class='form-control' placeholder="Package Name " required>
													<p style="color:red;margin-bottom: 0px;" id="packageName" ></p>
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
													?>
													<tr>
														<td>
															<?=$rowPrepaid['operator_name']?>
															<input type="hidden" name="prepaid_operator_code[]" value="<?=$rowPrepaid['np_operator_code']?>" readonly style="border:none" />
														</td>
														<td><input type="text" name="prepaid_my_commission[]" value="<?=$rowPrepaid['commission']?>" readonly style="border:none" /></td>
														<td><input type="text" name="prepaid_my_profit[]" value="0" required /></td>
														<td><input type="text" name="prepaid_wl_commission[]" value="<?=$rowPrepaid['commission']?>" required /></td>
														<td><input type="text" name="prepaid_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="prepaid_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="prepaid_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="prepaid_retailer_profit[]" value="0" required /></td>
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
													?>
													<tr>
														<td>
															<?=$rowPostpaid['operator_name']?>
															<select name="postpaid_commission_type[]">
																<option value="PERCENT">PERCENT</option>
																<option value="FLAT">FLAT</option>
															</select>
															<input type="hidden" name="postpaid_operator_code[]" value="<?=$rowPostpaid['np_operator_code']?>" readonly style="border:none" />
														</td>
														<td><input type="text" name="postpaid_my_commission[]" value="<?=$rowPostpaid['commission']?>" readonly style="border:none" /></td>
														<td><input type="text" name="postpaid_my_profit[]" value="0" required /></td>
														<td><input type="text" name="postpaid_wl_commission[]" value="<?=$rowPostpaid['commission']?>" value="0" required /></td>
														<td><input type="text" name="postpaid_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="postpaid_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="postpaid_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="postpaid_retailer_profit[]" value="0" required /></td>
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
													?>
													<tr>
														<td>
															<?=$rowDTH['operator_name']?>
															<input type="hidden" name="dth_operator_code[]" value="<?=$rowDTH['np_operator_code']?>" readonly style="border:none" />
														</td>
														<td><input type="text" name="dth_my_commission[]" value="<?=$rowDTH['commission']?>" readonly style="border:none" /></td>
														<td><input type="text" name="dth_my_profit[]" value="0" required /></td>
														<td><input type="text" name="dth_wl_commission[]" value="<?=$rowDTH['commission']?>" required /></td>
														<td><input type="text" name="dth_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="dth_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="dth_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="dth_retailer_profit[]" value="0" required /></td>
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
														<td><input type="text" name="commission" value="0" required /></td>
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
														<td>
															AEPS (500-999)
															<input type="hidden" name="aeps_operator_code[]" value="AEPS1" readonly style="border:none" />
														</td>
														<td>
															<input type="text" name="aeps_my_commission[]" value="0.32" readonly style="border:none" />
														</td>
														<td><input type="text" name="aeps_my_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="aeps_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_retailer_profit[]" value="0" required /></td>
													</tr>
													<input type="hidden" name="aeps_commission_type[]" value="PERCENT" readonly style="border:none" />
													<tr>
														<td>
															AEPS(1000 - 1499) 
															<input type="hidden" name="aeps_operator_code[]" value="AEPS2" readonly style="border:none" />
														</td>
														<td>
															<input type="text" name="aeps_my_commission[]" value="0.32" readonly style="border:none" />
														</td>
														<td><input type="text" name="aeps_my_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="aeps_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_retailer_profit[]" value="0" required /></td>
													</tr>
													<input type="hidden" name="aeps_commission_type[]" value="PERCENT" readonly style="border:none" />													
													<tr>
														<td>
															AEPS(1500 - 1999)  
															<input type="hidden" name="aeps_operator_code[]" value="AEPS3" readonly style="border:none" />
														</td>
														<td>
															<input type="text" name="aeps_my_commission[]" value="0.32" readonly style="border:none" />
														</td>
														<td><input type="text" name="aeps_my_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="aeps_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_retailer_profit[]" value="0" required /></td>
													</tr>
													<input type="hidden" name="aeps_commission_type[]" value="PERCENT" readonly style="border:none" />												
													<tr>
														<td>
															AEPS(2000 - 2999)  
															<input type="hidden" name="aeps_operator_code[]" value="AEPS4" readonly style="border:none" />
														</td>
														<td>
															<input type="text" name="aeps_my_commission[]" value="0.32" readonly style="border:none" />
														</td>
														<td><input type="text" name="aeps_my_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="aeps_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_retailer_profit[]" value="0" required /></td>
													</tr>
													<input type="hidden" name="aeps_commission_type[]" value="PERCENT" readonly style="border:none" />												
													<tr>
														<td>
															AEPS(3000 - Above) Flat  
															<input type="hidden" name="aeps_operator_code[]" value="AEPS5" readonly style="border:none" />
														</td>
														<td>
														<input type="text" name="aeps_my_commission[]" value="11" readonly style="border:none" />														
														</td>
														<td><input type="text" name="aeps_my_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="aeps_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="aeps_retailer_profit[]" value="0" required /></td>
													</tr>
													<input type="hidden" name="aeps_commission_type[]" value="FLAT" readonly style="border:none" />
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
														<td>
															Electricity <!--<font color="red"> [Surcharge ] </font>-->
															<input type="hidden" name="bbps_operator_code[]" value="ELECTR" readonly style="border:none" />
														</td>
														<td><input type="text" name="bbps_my_commission[]" value="0.60" readonly style="border:none" /></td>
														<td><input type="text" name="bbps_my_profit[]" value="0" required /></td>
														<td><input type="text" name="bbps_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="bbps_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="bbps_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="bbps_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="bbps_retailer_profit[]" value="0" required /></td>
													</tr>
													<tr>
														<td>
															Gas <!--<font color="red"> [Surcharge ] </font>-->
															<input type="hidden" name="bbps_operator_code[]" value="GAS" readonly style="border:none" />
														</td>
														<td><input type="text" name="bbps_my_commission[]" value="0.60" readonly style="border:none" /></td>
														<td><input type="text" name="bbps_my_profit[]" value="0" required /></td>
														<td><input type="text" name="bbps_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="bbps_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="bbps_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="bbps_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="bbps_retailer_profit[]" value="0" required /></td>
													</tr>													
													<tr>
														<td>
															Insurance <!--<font color="red"> [Surcharge ] </font>-->
															<input type="hidden" name="bbps_operator_code[]" value="INSUR" readonly style="border:none" />
														</td>
														<td><input type="text" name="bbps_my_commission[]" value="0" readonly style="border:none" /></td>
														<td><input type="text" name="bbps_my_profit[]" value="0" required /></td>
														<td><input type="text" name="bbps_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="bbps_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="bbps_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="bbps_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="bbps_retailer_profit[]" value="0" required /></td>
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
														<td>
															DMT (11 - 1000)
															<select name="dmt_commission_type[]">
																<option value="PERCENT">PERCENT</option>
																<option value="FLAT">FLAT</option>
															</select>
															<input type="hidden" name="dmt_operator_code[]" value="dmt1" readonly style="border:none" />
														</td>
														<td><input type="text" name="dmt_my_commission[]" value="5.75" readonly style="border:none" /></td>
														<td><input type="text" name="dmt_my_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="dmt_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_retailer_profit[]" value="0" required /></td>
													</tr>
													<tr>
														<td>
															DMT(1001 - 2000)
															<select name="dmt_commission_type[]">
																<option value="PERCENT">PERCENT</option>
																<option value="FLAT">FLAT</option>
															</select>
															<input type="hidden" name="dmt_operator_code[]" value="dmt2" readonly style="border:none" />
														</td>
														<td><input type="text" name="dmt_my_commission[]" value="7.70" readonly style="border:none" /></td>
														<td><input type="text" name="dmt_my_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="dmt_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_retailer_profit[]" value="0" required /></td>
													</tr>
													<tr>
														<td>
															DMT(2001 - 3000)
															<select name="dmt_commission_type[]">
																<option value="PERCENT">PERCENT</option>
																<option value="FLAT">FLAT</option>
															</select>
															<input type="hidden" name="dmt_operator_code[]" value="dmt3" readonly style="border:none" />
														</td>
														<td><input type="text" name="dmt_my_commission[]" value="9.65" readonly style="border:none" /></td>
														<td><input type="text" name="dmt_my_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="dmt_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_retailer_profit[]" value="0" required /></td>
													</tr>
													<tr>
														<td>
															DMT(3001 - 4000)
															<select name="dmt_commission_type[]">
																<option value="PERCENT">PERCENT</option>
																<option value="FLAT">FLAT</option>
															</select>
															<input type="hidden" name="dmt_operator_code[]" value="dmt4" readonly style="border:none" />
														</td>
														<td><input type="text" name="dmt_my_commission[]" value="11.60" readonly style="border:none" /></td>
														<td><input type="text" name="dmt_my_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="dmt_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_retailer_profit[]" value="0" required /></td>
													</tr>
													<tr>
														<td>
															DMT(4001 - 5000)
															<select name="dmt_commission_type[]">
																<option value="PERCENT">PERCENT</option>
																<option value="FLAT">FLAT</option>
															</select>
															<input type="hidden" name="dmt_operator_code[]" value="dmt5" readonly style="border:none" />
														</td>
														<td><input type="text" name="dmt_my_commission[]" value="13.55" readonly style="border:none" /></td>
														<td><input type="text" name="dmt_my_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_wl_commission[]" value="0" required /></td>
														<td><input type="text" name="dmt_wl_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_master_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_dist_profit[]" value="0" required /></td>
														<td><input type="text" name="dmt_retailer_profit[]" value="0" required /></td>
													</tr>
													<?php
														/* end DMT Surcharge */
													?>
												</tbody>
											 </table>
										</div>
										<div class="form-group">
											<input type="submit" name="submit" onclick="return checkPackage()" class="btn btn-primary pull-right" value="Add Commission"/>
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

	function check_packageName(pn){
		var packageName = pn.value; 
		$.ajax({
				type: "POST",
				cache: false,
				url: "ajax/check_packageName.php",
				data: {'packageName':packageName},				
				success: function (response) { 					
					if(response == "true"){
						$("#packageName").html("Package name already used");
						$(pn).val('');
					} else {
						$("#packageName").html("");
					}
				}
			});
	}

$(document).ready(function(){
	/* *********** Check when set commission (on blur)  ************* */
	$("#packageTable tr td input").blur(function(event) {
		var c_input = this;
		var row = $(this).closest("tr");
		var inputs = row.find('input');

		var my_commission = inputs[1].value;
		
		var commi_distribute = parseFloat(inputs[2].value) + parseFloat(inputs[3].value) + parseFloat(inputs[4].value) + parseFloat(inputs[5].value) + parseFloat(inputs[6].value) + parseFloat(inputs[7].value);
		if(parseFloat(my_commission) == my_commission && parseFloat(commi_distribute) == commi_distribute){
			if(parseFloat(my_commission) < parseFloat(commi_distribute)){
				$(row).css("background-color", "#ff0000");	
				$(c_input).val('');
				alert("Invalid Commission set!");
			} else {
				$(row).css("background-color", "transparent");
			}
		} else {
			$(row).css("background-color", "#ff0000");	
			$(c_input).val('0');
			alert("Invalid Commission set!");
		}
	});
});
	
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
				return false;
			}
		  /* }); */			  
		});
		
		if(error===true){
			  alert("Invalid Commission set!  Please check highlighted rows");
			  return false;
		  } else {
			  return true;
		  }
	}
</script>
</body>
</html>
