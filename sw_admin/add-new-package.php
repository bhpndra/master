<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>
<?php
		
	$post = $helpers->clearSlashes($_POST);

	$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
	$WL_ID = $resVT['DATA']['WL_ID'];
	$USER_ID = $resVT['DATA']['USER_ID'];
	$msg = '';

	/* Check package not set commission structure. */
	
	$sql_checkPInfo = "SELECT id,package_name FROM `package_list` where id not IN ( select package_id from package_commission where package_id in (SELECT id FROM `package_list` WHERE `creator_id`='" . $USER_ID . "' and created_for = 'DISTRIBUTOR' group by creator_id )) and `creator_id`='" . $USER_ID . "' and created_for = 'DISTRIBUTOR' limit 1"; 
	$checkPInfo = $mysqlClass->mysqlQuery($sql_checkPInfo)->fetch(PDO::FETCH_ASSOC);

	/* End Check package not set commission structure. */	


	$userInfo = $mysqlClass->mysqlQuery("SELECT package_id,usertype FROM `add_cust` WHERE `id`='" . $USER_ID . "' and admin_id = '".$ADMIN_ID."' ")->fetch(PDO::FETCH_ASSOC);	

	if(isset($_POST['addName'])){
		
		$dValue = array(
						'package_name' => $post['packageName'],
						'creator_id' => $USER_ID,
						'created_by' => $userInfo['usertype'],
						'created_for' => 'DISTRIBUTOR',
						'default_package' => $userInfo['package_id'],
						'created_on' => date("Y-m-d H:i:s")
					);
		$id = $mysqlClass->insertData(' package_list ', $dValue);
		if($id > 1){
			$helpers->flashAlert_set('alert_success',"Package created successfully, Now complete your commission structure.");	
			$helpers->redirect_page("add-new-package");
		}
}


if(isset($_POST['add'])){
	$pid = $checkPInfo['id'];
		foreach($post['operator_code'] as $k1=>$v1){
			$values[$k1] = "({$pid},
							'{$post['operator_code'][$k1]}',
							'{$post['operator_type'][$k1]}',
							'{$post['type'][$k1]}',
							'{$post['md_comm'][$k1]}',
							'{$post['dt_comm'][$k1]}',
							'{$post['rt_comm'][$k1]}'
						)";
			$opt_Type = $post['operator_type'][$k1];
		}
		$queryValues = implode(",",$values);
		
		$commission_package = "INSERT INTO `package_commission`(`package_id`, `network`, `operator_type`, `commission_type`, `md_commission`,`dt_commission`, `rt_commission`) VALUES {$queryValues} " ;
		
		$que = $mysqlClass->mysqlQuery($commission_package);
		
		$alert_success = "Commission structure updated successfully.";
		if($opt_Type == "POSTPAID" || $opt_Type == "PREPAID" || $opt_Type == "DTH"){ $alert_success = 'Recharge Commission structure updated successfully. Please Complete DMT and AEPS Commission Structure'; } 
		if($opt_Type == "AEPS"){ $alert_success = 'AEPS Commission structure updated successfully. Please Complete DMT and Recharge Commission Structure'; } 
		if($opt_Type == "DMT"){ $alert_success = 'DMT Commission structure updated successfully. Please Complete AEPS and Recharge Commission Structure'; }
		
		$helpers->flashAlert_set('alert_success',$alert_success);	
		$pid = base64_encode($pid);
		$helpers->redirect_page("update-package?pid=$pid");

}

$msgSuccess = $helpers->flashAlert_get('alert_success');
$msgError = $helpers->flashAlert_get('alert_error');
if($msgSuccess){
	$msg = $helpers->alert_message($msgSuccess,"alert-success");
}
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Add New Package</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add New Package</li>
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
			<?=($msg!='')? $msg : '';?>
			<div class="col-md-11">
			  <div class="card card-success">
				<div class="card-header">
				  <h3 class="card-title">Add New Package</h3>
					
				</div>
				<div class="card-body">
				<form method="post" >
				  <div class="form-group row">
					<label class="col-sm-3 col-form-label">Package Name</label>
					<input type="text"  class="form-control col-sm-6" value="<?=($checkPInfo['package_name'])? $checkPInfo['package_name'] : ''?>" name="packageName" >
					<?php if(empty($checkPInfo['id'])){ ?>
					 <div class="col-sm-3">
						<button type="submit" name="addName" class="btn btn-primary">Add</button>
					 </div>
					<?php } ?>
				  </div>				  
				</form>
				</div>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->

			</div>
			<?php if(!empty($checkPInfo['id'])){ ?>
			<div class="col-md-11">
			  <div class="card card-info  collapsed-card">
				<div class="card-header">
				  <h3 class="card-title">Manage Recharge Commission</h3>
					<div class="card-tools">
					  <button type="button" class="btn btn-tool" data-card-widget="collapse" autocomplete="off"><i class="fas fa-plus"></i></button>
					</div>
				</div>
				<div class="card-body">
				<form method="post" class="commissionForm">
				<!-- Prepaid Commission -->
				<?php
					$recharge_prepaid_operator = $mysqlClass->fetchAllData(" network "," operator_name,np_operator_code ","  where `operator_type` = 'PREPAID' "); 
				?>
				  <table class="table">	
					<tr>
						<th colspan="6" style="text-align: center;background: #4d4d4d;color: #fff;padding: 5px;">Prepaid Commission</th>
					</tr>				
					<tr>
						<th>Network</th>
						<th>My Commission</th>
						<th>Master Distributor Commission</th>
						<th>Distributor Commission</th>
						<th>Retailer Commission</th>
						<th>Commission Type</th>
					</tr>
					<?php
						foreach($recharge_prepaid_operator as $rop){
							$resWLCommission = $mysqlClass->fetchRow(  "wl_package_commission", " wl_commission,commission_type ", " where package_id = '".$userInfo['package_id']."' and network = '".$rop['np_operator_code']."' ");
					?>
					<tr>
						<td>
							<?=$rop['operator_name']?>
							<input type="hidden" name="operator_code[]" value="<?=$rop['np_operator_code']?>" />
							<input type="hidden" name="operator_type[]" value="PREPAID" />
						</td>
						<td style="color:red">
							<?=$resWLCommission['wl_commission']?> - <?=$resWLCommission['commission_type']?>
						</td>
						<td><input type="text" name="md_comm[]" value="0" /></td>
						<td><input type="text" name="dt_comm[]" value="0" /></td>
						<td><input type="text" name="rt_comm[]" value="<?=$resWLCommission['wl_commission']?>" /></td>
						<td>
							<select name="type[]" >
								<option <?=($resWLCommission['commission_type']=="PERCENT")? 'selected' : '' ?> value="PERCENT">PERCENT</option>
								<option <?=($resWLCommission['commission_type']=="FLAT")? 'selected' : '' ?> value="FLAT">FLAT</option>
							</select>
						</td>
					</tr>
					<? } ?>
				  </table>
				  
				  <!-- Postpaid Commission -->
				<?php
					$recharge_postpaid_operator = $mysqlClass->fetchAllData(" network "," operator_name,np_operator_code ","  where `operator_type` = 'POSTPAID' "); 
				?>
				  <table class="table">	
					<tr>
						<th colspan="6" style="text-align: center;background: #4d4d4d;color: #fff;padding: 5px;">Postpaid Commission</th>
					</tr>				
					<tr>
						<th>Network</th>
						<th>My Commission</th>
						<th>Master Distributor Commission</th>
						<th>Distributor Commission</th>
						<th>Retailer Commission</th>
						<th>Commission Type</th>
					</tr>
					<?php
						foreach($recharge_postpaid_operator as $ro){
							$resWLCommission = $mysqlClass->fetchRow(  "wl_package_commission", " wl_commission,commission_type ", " where package_id = '".$userInfo['package_id']."' and network = '".$ro['np_operator_code']."' ");
					?>
					<tr>
						<td>
							<?=$ro['operator_name']?>
							<input type="hidden" name="operator_code[]" value="<?=$ro['np_operator_code']?>" />
							<input type="hidden" name="operator_type[]" value="POSTPAID" />
						</td>
						<td style="color:red">
							<?=$resWLCommission['wl_commission']?> - <?=$resWLCommission['commission_type']?>
						</td>
						<td><input type="text" name="md_comm[]" value="0" /></td>
						<td><input type="text" name="dt_comm[]" value="0" /></td>
						<td><input type="text" name="rt_comm[]" value="<?=$resWLCommission['wl_commission']?>" /></td>
						<td>
							<select name="type[]" >
								<option <?=($resWLCommission['commission_type']=="PERCENT")? 'selected' : '' ?> value="PERCENT">PERCENT</option>
								<option <?=($resWLCommission['commission_type']=="FLAT")? 'selected' : '' ?> value="FLAT">FLAT</option>
							</select>
						</td>
					</tr>
					<? } ?>
				  </table>
				  
				<!-- DTH Commission -->
				<?php
					$recharge_dth_operator = $mysqlClass->fetchAllData(" network "," operator_name,np_operator_code ","  where `operator_type` = 'DTH' "); 
				?>
				  <table class="table">	
					<tr>
						<th colspan="6" style="text-align: center;background: #4d4d4d;color: #fff;padding: 5px;">DTH Commission</th>
					</tr>				
					<tr>
						<th>Network</th>
						<th>My Commission</th>
						<th>Master Distributor Commission</th>
						<th>Distributor Commission</th>
						<th>Retailer Commission</th>
						<th>Commission Type</th>
					</tr>
					<?php
						foreach($recharge_dth_operator as $rdo){
							$resWLCommission = $mysqlClass->fetchRow(  "wl_package_commission", " wl_commission,commission_type ", " where package_id = '".$userInfo['package_id']."' and network = '".$rdo['np_operator_code']."' ");
					?>
					<tr>
						<td>
							<?=$rdo['operator_name']?>
							<input type="hidden" name="operator_code[]" value="<?=$rdo['np_operator_code']?>" />
							<input type="hidden" name="operator_type[]" value="DTH" />
						</td>
						<td style="color:red">
							<?=$resWLCommission['wl_commission']?> - <?=$resWLCommission['commission_type']?>
						</td>
						<td><input type="text" name="md_comm[]" value="0" /></td>
						<td><input type="text" name="dt_comm[]" value="0" /></td>
						<td><input type="text" name="rt_comm[]" value="<?=$resWLCommission['wl_commission']?>" /></td>
						<td>
							<select name="type[]" >
								<option <?=($resWLCommission['commission_type']=="PERCENT")? 'selected' : '' ?> value="PERCENT">PERCENT</option>
								<option <?=($resWLCommission['commission_type']=="FLAT")? 'selected' : '' ?> value="FLAT">FLAT</option>
							</select>
						</td>
					</tr>
					<? } ?>
				  </table>
				  
				  
				  
				  <div class="card-footer">
					<button type="submit" name="add" class="btn btn-primary">Add</button>
				  </div>
				</form>
				</div>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->

			</div>
			
			<div class="col-md-11">
			  <div class="card card-danger  collapsed-card">
				<div class="card-header">
				  <h3 class="card-title">Manage DMT Commission</h3>
					<div class="card-tools">
					  <button type="button" class="btn btn-tool" data-card-widget="collapse" autocomplete="off"><i class="fas fa-plus"></i></button>
					</div>
				</div>
				<div class="card-body">
				<form method="post" class="commissionForm">
				<!-- DMT Commission -->
				<?php
					$resWLCommDMT = $mysqlClass->fetchRow(  "wl_package_commission", " wl_commission,commission_type ", " where package_id = '".$userInfo['package_id']."' and network = 'DMT' ");
					$dmt_slab = $mysqlClass->fetchAllData(" dmt_slabs "," * ","  where `wl_user_id` = '".$USER_ID."' "); 
				?>
				  <table class="table">	
					<?php if(count($dmt_slab)>0){ ?>
					<tr>
						<th colspan="6" style="text-align: center;background: #4d4d4d;color: #fff;padding: 5px;">DMT Commission (Your Commission <?=$resWLCommDMT['wl_commission']?> - <?=$resWLCommDMT['commission_type']?>)</th>
					</tr>				
					<tr>
						<th>Network</th>
						<th>Master Distributor Commission</th>
						<th>Distributor Commission</th>
						<th>Retailer Commission</th>
						<th>Commission Type</th>
					</tr>
					<?php
						foreach($dmt_slab as $ds){							
					?>
					<tr>
						<td>
							<?=$ds['slab_name']?>
							<input type="hidden" name="operator_code[]" value="<?=$ds['slab_network']?>" />
							<input type="hidden" name="operator_type[]" value="DMT" />
						</td>
						<td><input type="text" name="md_comm[]" value="0" /></td>
						<td><input type="text" name="dt_comm[]" value="0" /></td>
						<td><input type="text" name="rt_comm[]" value="0" /></td>
						<td>
							<select name="type[]" >
								<option value="PERCENT">PERCENT</option>
								<option value="FLAT">FLAT</option>
							</select>
						</td>
					</tr>
					<?php }  ?>
					<?php } else { ?>
					<tr>
						<th colspan="6" style="text-align:center"><a class="btn btn-success" href="manage-dmt-slabs">Create DMT Slabs</a></th>
					</tr>					
					<?php } ?>
				  </table>
				<?php if(count($dmt_slab)>0){ ?>
				  <div class="card-footer">
					<button type="submit" name="add" class="btn btn-primary">Add</button>
				<?php } ?>
				  </div>
				</form>
				</div>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->

			</div>
		

			
			<div class="col-md-11">
			  <div class="card card-warning  collapsed-card">
				<div class="card-header">
				  <h3 class="card-title">Manage AEPS Commission </h3>
					<div class="card-tools">
					  <button type="button" class="btn btn-tool" data-card-widget="collapse" autocomplete="off"><i class="fas fa-plus"></i></button>
					</div>
				</div>
				<div class="card-body">
				<form method="post" class="commissionForm">
				<!-- DMT Commission -->
				<?php
					$resWLCommAEPS = $mysqlClass->fetchRow(  "wl_package_commission", " wl_commission,commission_type ", " where package_id = '".$userInfo['package_id']."' and network = 'AEPS' ");
					$resWLCommAEPSF = $mysqlClass->fetchRow(  "wl_package_commission", " wl_commission,commission_type ", " where package_id = '".$userInfo['package_id']."' and network = 'AEPSF' ");
					$aeps_slab = $mysqlClass->fetchAllData(" aeps_slabs "," * ","  where `wl_user_id` = '".$USER_ID."' "); 
					
				?>
				  <table class="table">	
					<?php if(count($aeps_slab)>0){ ?>
					<tr>
						<th colspan="6" style="text-align: center;background: #4d4d4d;color: #fff;padding: 5px;">AEPS Commission</th>
					</tr>
					<tr>
						<td colspan="6" style="text-align: center;padding: 5px;">Your Commission  (501 to 3000)  <strong><?=$resWLCommAEPS['wl_commission']?> - <?=$resWLCommAEPS['commission_type']?></strong> | (3001 to 10000)  <strong><?=$resWLCommAEPSF['wl_commission']?> - <?=$resWLCommAEPSF['commission_type']?></strong></td>
					</tr>					
					<tr>
						<th>Network</th>
						<th>Master Distributor Commission</th>
						<th>Distributor Commission</th>
						<th>Retailer Commission</th>
						<th>Commission Type</th>
					</tr>
					<?php
						foreach($aeps_slab as $as){							
					?>
					<tr>
						<td>
							<?=$as['slab_name']?>
							<input type="hidden" name="operator_code[]" value="<?=$as['slab_network']?>" />
							<input type="hidden" name="operator_type[]" value="AEPS" />
						</td>
						<td><input type="text" name="md_comm[]" value="0" /></td>
						<td><input type="text" name="dt_comm[]" value="0" /></td>
						<td><input type="text" name="rt_comm[]" value="0" /></td>
						<td>
							<select name="type[]" >
								<option value="PERCENT">PERCENT</option>
								<option value="FLAT">FLAT</option>
							</select>
						</td>
					</tr>
					<?php }  ?>
					<?php } else { ?>
					<tr>
						<th colspan="6" style="text-align:center"><a class="btn btn-success" href="manage-aeps-slabs">Create AEPS Slabs</a></th>
					</tr>					
					<?php } ?>
				  </table>
				<?php if(count($aeps_slab)>0){ ?>
				  <div class="card-footer">
					<button type="submit" name="add" class="btn btn-primary">Add</button>
				<?php } ?>
				  </div>
				</form>
				</div>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->

			</div>
				
			<?php } ?>
		</div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
</html>
