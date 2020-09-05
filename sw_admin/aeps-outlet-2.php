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
                  <th>Retailer Details</th>
                  <th>Outlet Id</th>
                  <th>PAN</th>
                  <th>Name</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Shop Name</th>
                  <th>Address</th>
                  <th>PIN Code</th>
                  <th>KYC Status</th>
                  <th>Outlet Status</th>
                  <th>Comments</th>
                </tr>
                </thead>
				<tbody>
<?php
//$users = $mysqlClass->fetchAllData("add_cust"," * ", " WHERE admin_id = '".$ADMIN_ID."' and usertype = 'RETAILER' and wl_id = '".$WL_ID."'");
$users = $mysqlClass->mysqlQuery("SELECT b.name,b.user,b.mobile as r_mob, a.* FROM `outlet_kyc_bankit` as a left join add_cust as b on a.user_id = b.id WHERE b.admin_id = '".$ADMIN_ID."' and b.usertype = 'RETAILER' and b.wl_id = '".$WL_ID."'");
while($us = $users->fetch(PDO::FETCH_ASSOC)){
?>  
					<tr>
					   <td  style="white-space: nowrap;">
						<strong>Name: </strong><?=$us['name']?> <br/>
						<strong>Mobile: </strong><?=$us['r_mob']?> <br/>
						<strong>UserID: </strong> <?=$us["user"]?>
					  </td>
					  <td><?=$us['outletid']?></td>
					  <td><?=$us['pan_no']?></td>
					  <td><?=$us['first_name']?> <?=$us['last_name']?> <?=$us['middle_name']?></td>
					  <td><?=$us['mobile']?></td>
					  <td><?=$us['email']?></td>
					  <td><?=$us['company']?></td>
					  <td><?=$us['address']?></td>
					  <td><?=$us['pincode']?></td>					  
					  <td><?=$us['outlet_kyc']?></td>	
					  <td><?=$us['outlet_status']?></td>	
					  <td><?=$us['comments']?></td>	
					  
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
    } );
 
    table.buttons().container()
        .appendTo( '#example1_wrapper .col-md-6:eq(0)' );
} );
</script>
</html>
