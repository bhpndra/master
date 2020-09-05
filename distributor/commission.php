<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>
<?php


$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
$msg = '';



$qry = $mysqlClass->mysqlQuery("SELECT * FROM `add_cust` WHERE `id`='" . $USER_ID . "' ")->fetch(PDO::FETCH_ASSOC);
echo $package_id =  $qry['package_id'];

$packName = $mysqlClass->mysqlQuery("SELECT * FROM package_list WHERE id='".$package_id."'")->fetch(PDO::FETCH_ASSOC);

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">My Commission</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">My Commission</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">				
					<div class="card card-danger">	
						<div class="card-body">
							<table id="example1" class="table table-bordered">
								<thead>
								<tr>
									<th colspan="6" style="text-align: center;"><?=$packName['package_name']?></th>
								</tr>			
								</thead>
								<tbody>
<!-- AEPS Commission -->
								<tr class="table-success">
									<th colspan="6" style="text-align: center;">AEPS</th>
								</tr>								
								<tr>
									<th>#</th>
									<th>Slab Name</th>
									<th>Operator Type</th>
									<th>Commission Type</th>
									<th>DT Commission</th>                  
									<th>RT Commission</th>                  
								</tr>	
								<?php
								$sql = "SELECT * FROM `package_commission` WHERE package_id='".$package_id."' and operator_type = 'AEPS'";
								$package = $mysqlClass->mysqlQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
									if($mysqlClass->countRows($sql) > 0){
									foreach($package as $k=>$pack){
										$sql_opA = "SELECT slab_name FROM `aeps_slabs` WHERE slab_network='".$pack['network']."' and wl_user_id = '".$WL_ID."'";
										$opAS = $mysqlClass->mysqlQuery($sql_opA)->fetch(PDO::FETCH_ASSOC);
									?>
										<tr>
											<td><?=$k+1?></td>
											<td><?=$opAS['slab_name'];?></td>
											<td><?=$pack['operator_type']?></td>							
											<td><?=$pack['commission_type']?></td>
											<td><?=$pack['dt_commission'];?></td>
											<td><?=$pack['rt_commission'];?></td>
										</tr>
									<?php
										}
									}
									else {
										echo "<tr><td colspan='7'>No Commission details.</td></tr>";
									}
								?>
<!-- DMT Commission -->
								
								<tr class="table-danger">
									<th colspan="6" style="text-align: center;">DMT Surcharge</th>
								</tr>								
								<tr>
									<th>#</th>
									<th>Slab Name</th>
									<th>Operator Type</th>
									<th>Type</th>
									<th>DT Commission</th>                  
									<th>RT Surcharge</th>                 
								</tr>	
								<?php
								$package = $sql = $pack = '';
								$sql = "SELECT * FROM `package_commission` WHERE package_id='".$package_id."' and operator_type = 'DMT'";
								$package = $mysqlClass->mysqlQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
									if($mysqlClass->countRows($sql) > 0){
									foreach($package as $k=>$pack){
										$sql_opD = "SELECT slab_name FROM `dmt_slabs` WHERE slab_network='".$pack['network']."' and wl_user_id = '".$WL_ID."'";
										$opMT = $mysqlClass->mysqlQuery($sql_opD)->fetch(PDO::FETCH_ASSOC);
									?>
										<tr>
											<td><?=$k+1?></td>
											<td><?=$opMT['slab_name'];?></td>
											<td><?=$pack['operator_type']?></td>							
											<td><?=$pack['commission_type']?></td>
											<td><?=$pack['dt_commission'];?></td>
											<td><?=$pack['rt_commission'];?></td>
										</tr>
									<?php
										}
									}
									else {
										echo "<tr><td colspan='7'>No Commission details.</td></tr>";
									}
								?>
<!-- Recharge Commission -->
								
								<tr class="table-warning">
									<th colspan="6" style="text-align: center;">PREPAID Recharge Commission</th>
								</tr>								
								<tr>
									<th>#</th>
									<th>Operator Name</th>
									<th>Operator Type</th>
									<th>Commission Type</th>
									<th>DT Commission</th>                  
									<th>RT Commission</th>                 
								</tr>	
								<?php
								$package = $sql = $pack = '';
								$sql = "SELECT * FROM `package_commission` WHERE package_id='".$package_id."' and operator_type = 'PREPAID'";
								$package = $mysqlClass->mysqlQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
									if($mysqlClass->countRows($sql) > 0){
									foreach($package as $k=>$pack){
										$sql_opP1 = "SELECT operator_name FROM `network` WHERE np_operator_code='".$pack['network']."' ";
										$opP1 = $mysqlClass->mysqlQuery($sql_opP1)->fetch(PDO::FETCH_ASSOC);
									?>
										<tr>
											<td><?=$k+1?></td>
											<td><?=$opP1['operator_name'];?></td>
											<td><?=$pack['operator_type']?></td>							
											<td><?=$pack['commission_type']?></td>
											<td><?=$pack['dt_commission'];?></td>
											<td><?=$pack['rt_commission'];?></td>
										</tr>
									<?php
										}
									}
									else {
										echo "<tr><td colspan='7'>No Commission details.</td></tr>";
									}
								?>
<!-- Recharge Commission -->
								
								<tr class="table-dark">
									<th colspan="6" style="text-align: center;">POSTPAID Recharge Commission</th>
								</tr>								
								<tr>
									<th>#</th>
									<th>Operator Name</th>
									<th>Operator Type</th>
									<th>Commission Type</th>
									<th>DT Commission</th>                  
									<th>RT Commission</th>               
								</tr>	
								<?php
								$package = $sql = $pack = '';
								$sql = "SELECT * FROM `package_commission` WHERE package_id='".$package_id."' and operator_type = 'POSTPAID'";
								$package = $mysqlClass->mysqlQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
									if($mysqlClass->countRows($sql) > 0){
									foreach($package as $k=>$pack){
										$sql_opP2 = "SELECT operator_name FROM `network` WHERE np_operator_code='".$pack['network']."' ";
										$opP2 = $mysqlClass->mysqlQuery($sql_opP2)->fetch(PDO::FETCH_ASSOC);
									?>
										<tr>
											<td><?=$k+1?></td>
											<td><?=$opP2['operator_name'];?></td>
											<td><?=$pack['operator_type']?></td>							
											<td><?=$pack['commission_type']?></td>
											<td><?=$pack['dt_commission'];?></td>
											<td><?=$pack['rt_commission'];?></td>
										</tr>
									<?php
										}
									}
									else {
										echo "<tr><td colspan='7'>No Commission details.</td></tr>";
									}
								?>
<!-- Recharge Commission -->
								
								<tr class="table-info">
									<th colspan="6" style="text-align: center;">DTH Recharge Commission</th>
								</tr>								
								<tr>
									<th>#</th>
									<th>Operator Name</th>
									<th>Operator Type</th>
									<th>Commission Type</th>
									<th>DT Commission</th>                  
									<th>RT Commission</th>                 
								</tr>	
								<?php
								$package = $sql = $pack = '';
								$sql = "SELECT * FROM `package_commission` WHERE package_id='".$package_id."' and operator_type = 'DTH'";
								$package = $mysqlClass->mysqlQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
									if($mysqlClass->countRows($sql) > 0){
									foreach($package as $k=>$pack){
										$sql_opD = "SELECT operator_name FROM `network` WHERE np_operator_code='".$pack['network']."' ";
										$opD = $mysqlClass->mysqlQuery($sql_opD)->fetch(PDO::FETCH_ASSOC);
									?>
										<tr>
											<td><?=$k+1?></td>
											<td><?=$opD['operator_name'];?></td>
											<td><?=$pack['operator_type']?></td>							
											<td><?=$pack['commission_type']?></td>
											<td><?=$pack['dt_commission'];?></td>
											<td><?=$pack['rt_commission'];?></td>
										</tr>
									<?php
										}
									}
									else {
										echo "<tr><td colspan='7'>No Commission details.</td></tr>";
									}
								?>
								
<!-- BBPS Commission -->
								
								<tr class="table-danger">
									<th colspan="6" style="text-align: center;">BBPS Commission</th>
								</tr>								
								<tr>
									<th>#</th>
									<th>Operator Name</th>
									<th>Operator Type</th>
									<th>Commission Type</th>
									<th>DT Commission</th>                  
									<th>RT Commission</th>                
								</tr>	
								<?php
								$package = $sql = $pack = '';
								$sql = "SELECT * FROM `package_commission` WHERE package_id='".$package_id."' and operator_type = 'BILL'";
								$package = $mysqlClass->mysqlQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
									if($mysqlClass->countRows($sql) > 0){
									foreach($package as $k=>$pack){
										$sql_opD = "SELECT operator_name FROM `network` WHERE np_operator_code='".$pack['network']."' ";
										$opD = $mysqlClass->mysqlQuery($sql_opD)->fetch(PDO::FETCH_ASSOC);
									?>
										<tr>
											<td><?=$k+1?></td>
											<td><?=$opD['operator_name'];?></td>
											<td><?=$pack['operator_type']?></td>							
											<td><?=$pack['commission_type']?></td>
											<td><?=$pack['dt_commission'];?></td>
											<td><?=$pack['rt_commission'];?></td>
										</tr>
									<?php
										}
									}
									else {
										echo "<tr><td colspan='7'>No Commission details.</td></tr>";
									}
								?>
								
								
								</tbody>                
							</table>
						</div>
					</div>
					<!-- /.card -->
				</div>        
			</div>			
		</div>
		<!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<?php include("inc/footer.php"); ?>
</html>
