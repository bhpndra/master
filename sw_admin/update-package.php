<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>
<?php
	
	
	$post = $helpers->clearSlashes($_POST);
	$get = $helpers->clearSlashes($_GET);

	$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
	$WL_ID = $resVT['DATA']['WL_ID'];
	$USER_ID = $resVT['DATA']['USER_ID'];
	$msg = '';

	$pid = base64_decode($get['pid']);
	$sql_checkPInfo = "SELECT id,package_name FROM `package_list` where id = '".$pid."' and creator_id = $USER_ID and created_for = 'DISTRIBUTOR'"; 
	$checkPInfo = $mysqlClass->mysqlQuery($sql_checkPInfo)->fetch(PDO::FETCH_ASSOC);
	if(!isset($checkPInfo['id'])){ $helpers->redirect_page("add-new-package"); }

	/* White Label Package */
	$userInfo = $mysqlClass->mysqlQuery("SELECT package_id,usertype FROM `add_cust` WHERE `id`='" . $USER_ID . "' and admin_id = '".$ADMIN_ID."' ")->fetch(PDO::FETCH_ASSOC);	

if(isset($_POST['updateName'])){
		
		$dValue = array(
						'package_name' => $post['packageName']
					);
		$id = $mysqlClass->updateData(' package_list ', $dValue, " where id = '".$pid."' and creator_id = $USER_ID and created_for = 'DISTRIBUTOR' ");
		if($id > 0){
			$helpers->flashAlert_set('alert_success',"Package Name update successfully");	
			$helpers->redirect_page("update-package?pid=".$get['pid']);
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
		}
		$queryValues = implode(",",$values);
		
		$commission_package = "INSERT INTO `package_commission`(`package_id`, `network`, `operator_type`, `commission_type`, `md_commission`,`dt_commission`, `rt_commission`) VALUES {$queryValues} " ;
		
		$que = $mysqlClass->mysqlQuery($commission_package);

		$helpers->flashAlert_set('alert_success',"Commission structure updated successfully.");	
		$pid = base64_encode($pid);
		$helpers->redirect_page("update-package?pid=$pid");

}


if(isset($_POST['update'])){
	$pid = $checkPInfo['id'];
		foreach($post['row_id'] as $k1=>$v1){
			$value = array(
						'md_commission' => $post['md_comm'][$k1],
						'dt_commission' => $post['dt_comm'][$k1],
						'rt_commission' => $post['rt_comm'][$k1],
						'commission_type' => $post['type'][$k1]
					);
			$mysqlClass->updateData(' package_commission ', $value, " where package_id = '".$pid."' and id = '".$v1."' and network = '".$post['operator_code'][$k1]."'" );
		}
		
		$helpers->flashAlert_set('alert_success',"Commission structure updated successfully.");	
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
            <h1 class="m-0 text-dark">Update Package</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Update Package</li>
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
			  <div class="card card-default">
				<div class="card-header">
				  <h3 class="card-title">Update Package</h3>
				  <div class="card-tools">
					  <a href="all-package" class="btn btn-sm btn-success" >Back to All Packages</a>
				 </div>
				</div>
				<div class="card-body">
				<form method="post" >
				  <div class="form-group row">
					<label class="col-sm-2 col-form-label">Package Name</label>
					<input type="text"  class="form-control col-sm-7" value="<?=($checkPInfo['package_name'])? $checkPInfo['package_name'] : ''?>" name="packageName" >
					 <div class="col-sm-3">
						<button type="submit" name="updateName" class="btn btn-primary pull-right">Update Package Name</button>
					 </div>
				  </div>				  
				</form>
				</div>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->
			</div>
			<?php
				
				$check_operators = $mysqlClass->fetchAllData(" package_commission "," operator_type "," where package_id = '".$pid."' group by `operator_type` ");
				$pkOpt = array();
				foreach($check_operators as $chkOpt){
					$pkOpt[] = $chkOpt['operator_type'];
				}
				//print_r($pkOpt);
			?>
			
			<?php if(!empty($checkPInfo['id'])){ ?>
			<div class="col-md-11">
			  <div class="card card-info  collapsed-card">
				<div class="card-header">
				
				  <h3 class="card-title">Manage Recharge Commission
				  <?php
					if(!in_array('PREPAID',$pkOpt) || !in_array('POSTPAID',$pkOpt) || !in_array('DTH',$pkOpt)){
						echo "<span style='color:red;background: #fff;padding: 5px;'>(Recharge Commission Not set.)</span>"; 
						$rechargeBTN = 'add';
					}
					?>
				  </h3>
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
							
							if(in_array('PREPAID',$pkOpt)){
								$resPrepaid_pc = $mysqlClass->fetchRow(  "package_commission", "  md_commission,dt_commission,rt_commission,commission_type,id ", " where package_id = '".$pid."' and network = '".$rop['np_operator_code']."' and operator_type = 'PREPAID' ");
							}
					?>
					<tr>
						<td>
							<?=$rop['operator_name']?>
							<input type="hidden" name="operator_code[]" value="<?=$rop['np_operator_code']?>" />
							<input type="hidden" name="operator_type[]" value="PREPAID" />
							<?=(isset($resPrepaid_pc['id']) && $resPrepaid_pc['id'] > 0)? "<input type='hidden' name='row_id[]' value='".$resPrepaid_pc['id']."' />" : ''?>
						</td>
						<td style="color:red">
							<?=$resWLCommission['wl_commission']?> - <?=$resWLCommission['commission_type']?>
						</td>
						<td><input type="text" name="md_comm[]" value="<?=($resPrepaid_pc['md_commission'])? $resPrepaid_pc['md_commission'] : 0;?>" /></td>
						<td><input type="text" name="dt_comm[]" value="<?=($resPrepaid_pc['dt_commission'])? $resPrepaid_pc['dt_commission'] : 0;?>" /></td>
						<td><input type="text" name="rt_comm[]" value="<?=($resPrepaid_pc['rt_commission'])? $resPrepaid_pc['rt_commission'] : 0;?>" /></td>
						<td>
							<select name="type[]" >
								<option <?=(@$resPrepaid_pc['commission_type']=="PERCENT")? 'selected' : '' ?> value="PERCENT">PERCENT</option>
								<option <?=(@$resPrepaid_pc['commission_type']=="FLAT")? 'selected' : '' ?> value="FLAT">FLAT</option>
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
							
							if(in_array('POSTPAID',$pkOpt)){
								$resPostpaid_pc = $mysqlClass->fetchRow(  "package_commission", " md_commission,dt_commission,rt_commission,commission_type,id ", " where package_id = '".$pid."' and network = '".$ro['np_operator_code']."' and operator_type = 'POSTPAID' ");
							}
					?>
					<tr>
						<td>
							<?=$ro['operator_name']?>
							<input type="hidden" name="operator_code[]" value="<?=$ro['np_operator_code']?>" />
							<input type="hidden" name="operator_type[]" value="POSTPAID" />
							<?=(isset($resPostpaid_pc['id']) && $resPostpaid_pc['id'] > 0)? "<input type='hidden' name='row_id[]' value='".$resPostpaid_pc['id']."' />" : ''?>
						</td>
						<td style="color:red">
							<?=$resWLCommission['wl_commission']?> - <?=$resWLCommission['commission_type']?>
						</td>
						<td><input type="text" name="md_comm[]" value="<?=($resPostpaid_pc['md_commission'])? $resPostpaid_pc['md_commission'] : 0;?>" /></td>
						<td><input type="text" name="dt_comm[]" value="<?=($resPostpaid_pc['dt_commission'])? $resPostpaid_pc['dt_commission'] : 0;?>" /></td>
						<td><input type="text" name="rt_comm[]" value="<?=($resPostpaid_pc['rt_commission'])? $resPostpaid_pc['rt_commission'] : 0;?>" /></td>
						<td>
							<select name="type[]" >
								<option <?=(@$resPostpaid_pc['commission_type']=="PERCENT")? 'selected' : '' ?> value="PERCENT">PERCENT</option>
								<option <?=(@$resPostpaid_pc['commission_type']=="FLAT")? 'selected' : '' ?> value="FLAT">FLAT</option>
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
							
							if(in_array('DTH',$pkOpt)){
								$resDth_pc = $mysqlClass->fetchRow(  "package_commission", " md_commission,dt_commission,rt_commission,commission_type,id ", " where package_id = '".$pid."' and network = '".$rdo['np_operator_code']."' and operator_type = 'DTH' ");
							}
					?>
					<tr>
						<td>
							<?=$rdo['operator_name']?>
							<input type="hidden" name="operator_code[]" value="<?=$rdo['np_operator_code']?>" />
							<input type="hidden" name="operator_type[]" value="DTH" />
							<?=(isset($resDth_pc['id']) && $resDth_pc['id'] > 0)? "<input type='hidden' name='row_id[]' value='".$resDth_pc['id']."' />" : ''?>
						</td>
						<td style="color:red">
							<?=$resWLCommission['wl_commission']?> - <?=$resWLCommission['commission_type']?>
						</td>
						<td><input type="text" name="md_comm[]" value="<?=($resDth_pc['md_commission'])? $resDth_pc['md_commission'] : 0;?>" /></td>
						<td><input type="text" name="dt_comm[]" value="<?=($resDth_pc['dt_commission'])? $resDth_pc['dt_commission'] : 0;?>" /></td>
						<td><input type="text" name="rt_comm[]" value="<?=($resDth_pc['rt_commission'])? $resDth_pc['rt_commission'] : 0;?>" /></td>
						<td>
							<select name="type[]" >
								<option <?=(@$resDth_pc['commission_type']=="PERCENT")? 'selected' : '' ?> value="PERCENT">PERCENT</option>
								<option <?=(@$resDth_pc['commission_type']=="FLAT")? 'selected' : '' ?> value="FLAT">FLAT</option>
							</select>
						</td>
					</tr>
					<? } ?>
				  </table>
				  
				  
				  <div class="card-footer">
					<button type="submit" name="<?=(isset($rechargeBTN))? $rechargeBTN : 'update'?>" class="btn btn-primary"><?=(isset($rechargeBTN))? $rechargeBTN : 'update'?></button>
				  </div>
				</form>
				</div>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->

			</div>
			
			<div class="col-md-11">
			  <div class="card card-success  collapsed-card">
				<div class="card-header">
				
				  <h3 class="card-title">Manage BBPS Commission
				  <?php
					if(!in_array('BILL',$pkOpt) ){
						echo "<span style='color:red;background: #fff;padding: 5px;'>(BBPS Commission Not set.)</span>"; 
						$bbpsBTN = 'add';
					}
					?>
				  </h3>
					<div class="card-tools">
					  <button type="button" class="btn btn-tool" data-card-widget="collapse" autocomplete="off"><i class="fas fa-plus"></i></button>
					</div>
				</div>
				<div class="card-body">
				<form method="post" class="commissionForm">
				<!-- Prepaid Commission -->
				<?php
					$recharge_prepaid_operator = $mysqlClass->fetchAllData(" network "," operator_name,np_operator_code ","  where `operator_type` = 'BILL' "); 
				?>
				  <table class="table">	
					<tr>
						<th colspan="6" style="text-align: center;background: #4d4d4d;color: #fff;padding: 5px;">BBPS Commission</th>
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
							
							if(in_array('BILL',$pkOpt)){
								$resBill_pc = $mysqlClass->fetchRow(  "package_commission", " md_commission,dt_commission,rt_commission,commission_type,id ", " where package_id = '".$pid."' and network = '".$rop['np_operator_code']."' and operator_type = 'BILL' ");
							}
					?>
					<tr>
						<td>
							<?=$rop['operator_name']?>
							<input type="hidden" name="operator_code[]" value="<?=$rop['np_operator_code']?>" />
							<input type="hidden" name="operator_type[]" value="BILL" />
							<?=(isset($resBill_pc['id']) && $resBill_pc['id'] > 0)? "<input type='hidden' name='row_id[]' value='".$resBill_pc['id']."' />" : ''?>
						</td>
						<td style="color:red">
							<?=$resWLCommission['wl_commission']?> - <?=$resWLCommission['commission_type']?>
						</td>
						<td><input type="text" name="md_comm[]" value="<?=($resBill_pc['md_commission'])? $resBill_pc['md_commission'] : 0;?>" /></td>
						<td><input type="text" name="dt_comm[]" value="<?=($resBill_pc['dt_commission'])? $resBill_pc['dt_commission'] : 0;?>" /></td>
						<td><input type="text" name="rt_comm[]" value="<?=($resBill_pc['rt_commission'])? $resBill_pc['rt_commission'] : 0;?>" /></td>
						<td>
							<select name="type[]" >
								<option <?=(@$resBill_pc['commission_type']=="PERCENT")? 'selected' : '' ?> value="PERCENT">PERCENT</option>
								<option <?=(@$resBill_pc['commission_type']=="FLAT")? 'selected' : '' ?> value="FLAT">FLAT</option>
							</select>
						</td>
					</tr>
					<? } ?>
				  </table>

				  <div class="card-footer">
					<button type="submit" name="<?=(isset($bbpsBTN))? $bbpsBTN : 'update'?>" class="btn btn-primary"><?=(isset($bbpsBTN))? $bbpsBTN : 'update'?></button>
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
				  <h3 class="card-title">Manage DMT Commission
				  <?php
					if(!in_array('DMT',$pkOpt)){ 
						echo "<span style='color:red;background: #fff;padding: 5px;'>(DMT Commission Not set.)</span>";
						$dmtBTN = 'add';
					}
					?>
				  </h3>
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
							
							if(in_array('DMT',$pkOpt)){
								$resDmt_pc = $mysqlClass->fetchRow(  "package_commission", " md_commission,dt_commission,rt_commission,commission_type,id ", " where package_id = '".$pid."' and network = '".$ds['slab_network']."' and operator_type = 'DMT' ");
							}						
					?>
					<tr>
						<td>						
							<?=$ds['slab_name']?>
							<input type="hidden" name="operator_code[]" value="<?=$ds['slab_network']?>" />
							<input type="hidden" name="operator_type[]" value="DMT" />
							<?=(isset($resDmt_pc['id']) && $resDmt_pc['id'] > 0)? "<input type='hidden' name='row_id[]' value='".$resDmt_pc['id']."' />" : ''?>
						</td>
						<td><input type="text" name="md_comm[]" value="<?=($resDmt_pc['md_commission'])? $resDmt_pc['md_commission'] : 0;?>" /></td>
						<td><input type="text" name="dt_comm[]" value="<?=($resDmt_pc['dt_commission'])? $resDmt_pc['dt_commission'] : 0;?>" /></td>
						<td><input type="text" name="rt_comm[]" value="<?=($resDmt_pc['rt_commission'])? $resDmt_pc['rt_commission'] : 0;?>" /></td>
						<td>
							<select name="type[]" >
								<option <?=(@$resDmt_pc['commission_type']=="PERCENT")? 'selected' : '' ?> value="PERCENT">PERCENT</option>
								<option <?=(@$resDmt_pc['commission_type']=="FLAT")? 'selected' : '' ?> value="FLAT">FLAT</option>
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
					<button type="submit" name="<?=(isset($dmtBTN))? $dmtBTN : 'update'?>" class="btn btn-primary"><?=(isset($dmtBTN))? $dmtBTN : 'update'?></button>
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
				  <h3 class="card-title">Manage AEPS Commission
				  <?php
					if(!in_array('AEPS',$pkOpt)){ 
						echo "<span style='color:red;background: #fff;padding: 5px;'>(AEPS Commission Not set.)</span>";
						$aepsBTN = 'add';
					}
					?>
				  </h3>
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
							
							if(in_array('AEPS',$pkOpt)){
								$resAeps_pc = $mysqlClass->fetchRow(  "package_commission", " md_commission,dt_commission,rt_commission,commission_type,id ", " where package_id = '".$pid."' and network = '".$as['slab_network']."' and operator_type = 'AEPS' ");
							}												
					?>
					<tr>
						<td>
							<?=$as['slab_name']?>
							<input type="hidden" name="operator_code[]" value="<?=$as['slab_network']?>" />
							<input type="hidden" name="operator_type[]" value="AEPS" />
							<?=(isset($resAeps_pc['id']) && $resAeps_pc['id'] > 0)? "<input type='hidden' name='row_id[]' value='".$resAeps_pc['id']."' />" : ''?>
						</td>
						<td><input type="text" name="md_comm[]" value="<?=($resAeps_pc['md_commission'])? $resAeps_pc['md_commission'] : 0;?>" /></td>
						<td><input type="text" name="dt_comm[]" value="<?=($resAeps_pc['dt_commission'])? $resAeps_pc['dt_commission'] : 0;?>" /></td>
						<td><input type="text" name="rt_comm[]" value="<?=($resAeps_pc['rt_commission'])? $resAeps_pc['rt_commission'] : 0;?>" /></td>
						<td>
							<select name="type[]" >
								<option <?=(@$resAeps_pc['commission_type']=="PERCENT")? 'selected' : '' ?> value="PERCENT">PERCENT</option>
								<option <?=(@$resAeps_pc['commission_type']=="FLAT")? 'selected' : '' ?> value="FLAT">FLAT</option>
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
					<button type="submit" name="<?=(isset($aepsBTN))? $aepsBTN : 'update'?>" class="btn btn-primary"><?=(isset($aepsBTN))? $aepsBTN : 'update'?></button>
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
