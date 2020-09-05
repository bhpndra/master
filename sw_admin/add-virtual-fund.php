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
$msg = $helpers->flashAlert_get('add_virtual_FA');


$post = $helpers->clearSlashes($_POST); 
	if(isset($_POST['amount']) && $_POST['amount'] > 0 && isset($_POST['add']) ){
		$mysqlClass->mysqlQuery("START TRANSACTION");		
			$wl_avlVbal = $mysqlClass->mysqlQuery("SELECT wl_virtual_balance FROM `add_cust` WHERE `id`='" . $USER_ID . "'  FOR UPDATE ")->fetch(PDO::FETCH_ASSOC);	
			
			$amount	= $post['amount'];
			$mysqlClass->mysqlQuery("UPDATE `add_cust` set wl_virtual_balance = (wl_virtual_balance + $amount) WHERE `id`='" . $USER_ID . "' and admin_id = '".$ADMIN_ID."' ");
			
			$transaction_id = $helpers->transaction_id_generator("VAF",4);
			$dataValue = array(
				'transaction_id' 	=> $transaction_id,
				'agent_trid' 		=> $transaction_id,
				'opening_balance' 	=> $wl_avlVbal['wl_virtual_balance'],
				'deposits' 			=> $amount,
				'withdrawl' 		=> 0,
				'balance' 			=> ($wl_avlVbal['wl_virtual_balance'] + $amount),
				'date_created' 		=> date("Y-m-d H:i:s"),
				'created_by' 		=> $USER_ID,
				'comments' 			=> 'ADD FUND',
				'tr_type' 			=> 'ADD FUND',
				'wluser_id' 		=> $USER_ID,
				);
			$mysqlClass->insertData(" wl_virtual_trans ", $dataValue);
		$mysqlClass->mysqlQuery("COMMIT");
		
			$helpers->flashAlert_set('add_virtual_FA'," Balance added successfully. ");
			$helpers->redirect_page("add-virtual-fund");
	}

$userDetail = $mysqlClass->mysqlQuery("SELECT wl_virtual_balance FROM `add_cust` WHERE `id`='" . $USER_ID . "' ")->fetch(PDO::FETCH_ASSOC);	

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Add Virtual Fund</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add Virtual Fund</li>
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

			<div class="col-md-6">
			<?=($msg!='')? $helpers->alert_message($msg,'alert-success') : '';?>
			  <div class="card card-success">
				<div class="card-header">
				  <h3 class="card-title">Add Virtual Fund</h3>

				</div>
				<form method="post" enctype="multipart/form-data">
				<div class="card-body row">
				  <div class="form-group col-md-6">
					<label for="inputName">Current Fund</label>
					<input type="text"  class="form-control" value="<?=($userDetail['wl_virtual_balance']) ? $userDetail['wl_virtual_balance'] : '0.00'?>" readonly >
				  </div>
				  <div class="form-group col-md-6">
					<label for="inputName">Add</label>
					<input type="text"  class="form-control" value="" name="amount" required >
				  </div>
				  
				</div>
				  <div class="card-footer">
					<button type="submit" name="add" class="btn btn-primary">Add</button>
				  </div>
				</form>
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
