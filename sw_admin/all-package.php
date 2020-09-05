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
            <h1 class="m-0 text-dark">All Package</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">All Package</li>
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
              <h3 class="card-title">All Package</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Package Name</th>
                  <th>Create On</th>
                  <th>Action</th>
                </tr>
                </thead>
				<tbody>
<?php
$packages = $mysqlClass->fetchAllData("package_list"," * ", " where creator_id = '".$USER_ID."' and created_for = 'DISTRIBUTOR'");
foreach($packages as $pk){
?>  
					<tr>
					  <td><?=$pk['package_name']?></td>
					  <td><?=$pk['created_on']?></td>
					  <td><a href="update-package?pid=<?=base64_encode($pk['id'])?>" class="btn btn-sm btn-success">Edit</a></td>
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
        buttons: ['colvis' ]
    } );
 
    table.buttons().container()
        .appendTo( '#example1_wrapper .col-md-6:eq(0)' );
} );
</script>
</html>
