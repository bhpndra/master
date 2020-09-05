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
?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Manage Slab DMT/AEPS</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Manage Slab DMT/AEPS</li>
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
			<div class="col-md-10">
			  <div class="card card-default">
				<div class="card-header">
				  <h3 class="card-title">Manage Slab DMT/AEPS</h3>
				  <div class="card-tools">
					  <a href="all-package" class="btn btn-sm btn-success" >Back to All Package</a>
				 </div>
				</div>
				<div class="card-body">
				<form method="post" >
				  <div class="form-group row">
					<label class="col-sm-3 col-form-label">Package Name</label>
					 <div class="col-sm-4">
						<a href="manage-dmt-slabs" name="updateName" class="btn btn-success">Manage DMT Slab</a>
					 </div>
					 <div class="col-sm-4">
						<a href="manage-aeps-slabs" name="updateName" class="btn btn-primary">Manage AEPS Slab</a>
					 </div>
				  </div>				  
				</form>
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
</html>
