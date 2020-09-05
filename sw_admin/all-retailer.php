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

$user_details = $helpers->flashAlert_get('user_details_set');
$msg = $helpers->flashAlert_get('new_user_set');
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">All Retailer</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">All Retailer</li>
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
			<?php 
				if($msg){
					echo $helpers->alert_message($msg,"alert-success");
				} 
				if($user_details){
					echo $helpers->alert_message($user_details,"alert-info");
				}
			?>
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">All Retailer</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Name</th>
                  <th>Login Id</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Shop Name</th>
                  <th>Wallet Balance</th>
                  <th>AEPS Balance</th>
                  <th>Bal. Capping</th>
                  <th>Distributor</th>
                  <th>Address</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                </thead>
				<tbody>
<?php
$users = $mysqlClass->fetchAllData("add_cust"," * ", " WHERE admin_id = '".$ADMIN_ID."' and usertype = 'RETAILER' and wl_id = '".$WL_ID."'");
foreach($users as $us){
?>  
					<tr>
						<td><?=$us['name']?></td>
						<td><?=$us['user']?></td>
						<td><?=$us['mobile']?></td>
						<td><?=$us['email']?></td>
						<td><?=$us['cname']?></td>
						<td><?=$us['wallet_balance']?><br>							
							<a href="retailer-wallet-balance?id=<?= base64_encode($us['id']);?>" target="_blank" class="btn btn-primary btn-xs" title="View Wallet Balance detail"><i class="fa fa-eye" aria-hidden="true"></i> Wallet</a>
						</td>
						<td><?=$us['aeps_balance']?><br>
							<a href="retailer-aeps-balance?id=<?= base64_encode($us['id']);?>" target="_blank" class="btn btn-success btn-xs" title="View Wallet Balance detail"><i class="fa fa-eye" aria-hidden="true"></i> AEPS</a>
						</td>
						<td><?=$us['min_amt_capping']?></td>
						<?php $dist = $mysqlClass->fetchRow("add_cust"," name, cname ", " WHERE id = '".$us['creator_id']."' and usertype = 'DISTRIBUTOR' "); ?>
						<td><?=$dist['name']?> (<?=$dist['cname']?>)</td>
						<td>
							<?=$us['address']?> 
							<br/><strong>City: </strong><?=$us['city']?> 
							<br/><strong>State: </strong><?=$us['state']?> 
							<br/><strong>Pin: </strong><?=$us['pin']?>
						</td>
						<td><?=$us['status']?></td>
						<td>
							<a href="edit-retailer?uid=<?=base64_encode($us['id'])?>"  class="btn btn-sm btn-dark mb-1" style="min-width:100px;"><i class="fas fa-edit"></i> Update</a><br/>
							<a href="edit-retailer?uid=<?=base64_encode($us['id'])?>" class="btn btn-sm btn-primary mb-1"><i class="fas fa-edit"></i> Password</a><br/>
							<a href="edit-retailer?uid=<?=base64_encode($us['id'])?>" class="btn btn-sm btn-danger mb-1"><i class="fas fa-edit"></i> Pin</a><br/>
						</td>
					</tr>
<?php }?>              
				
                </tbody>
                <tfoot>

                </tfoot>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
          </div>

		</div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
<script>
$(document).ready(function() {
    var table = $('#example1').DataTable( {
        lengthChange: false,
        buttons: ['colvis' ],
		columnDefs: [
            {
                targets: [-3],
                visible: false
            }
        ]
    } );
 
    table.buttons().container()
        .appendTo( '#example1_wrapper .col-md-6:eq(0)' );
} );
</script>
</html>
