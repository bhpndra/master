<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>
<?php

	
	include_once("../api/classes/user_class.php");
	$userClass = new user_class();
?>
<?php
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
$msg = $helpers->flashAlert_get('add_main_BA');


$post = $helpers->clearSlashes($_POST); 
	if(isset($_POST['amount']) && $_POST['amount'] > 0 && isset($_POST['add']) ){
		$amount = $post['amount'];
		$bankname = '';
		$comments = '';
		$stmt = $mysqlClass->mysqlQuery("SELECT balance FROM `admin` where `id` = '".$ADMIN_ID."'");
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
						
		$currentAdminBal = $row['balance'];
		$new_admin_balance = $currentAdminBal + $amount;
		
		$transId = $helpers->transaction_id_generator("FA",8);
		$dataAdmin =  array(
			"deposits" => $amount,
			"withdrawl" => 0,
			"opening_balance" => $currentAdminBal,
			"balance" => $new_admin_balance,
			"user_type" => 'ADMIN',
			"creator_type" => $ADMIN_ID,
			"wl_id" => 0,
			"transaction_id" => $transId,
			"agent_trid " => $transId,
			"created_by" =>  $ADMIN_ID,
			"comments" => 'Fund Added',
			"tr_type" => 'CR',
			"admin_id" => $ADMIN_ID
		);
		
		if($mysqlClass->insertData("admin_trans", $dataAdmin)>0){
			$mysqlClass->mysqlQuery("update `admin` set balance = '".$new_admin_balance."' where id = '".$ADMIN_ID."' ");
			if ( $new_admin_balance >= $amount ) {
				
				$mysqlClass->mysqlQuery("START TRANSACTION");

				$whitelbl_avlbal = $userClass->check_user_balance($USER_ID, " FOR UPDATE");

				if ($whitelbl_avlbal) {
					$nwlbalance1 = $whitelbl_avlbal + $amount;
				} else {
					$nwlbalance1 = $amount;
				}

				
				$transaction_id = "TR" . $USER_ID . time(). mt_rand();
				$value = array(
					'ret_dest_wl_admin_id'      => $USER_ID,
					'bank_id'                   => $bankname,
					'user_type'                 => 'WL',
					'opening_balance'           => $whitelbl_avlbal,
					'deposits'                  => $amount,
					'balance'                   => $nwlbalance1,
					'date_created'              => date('Y-m-d H:i:s'),
					'created_by'                => $ADMIN_ID,
					'creator_type'              => 'ADMIN',
					'transaction_id	'           => $transaction_id,
					'comments'                  => $comments,
					'tr_type'                   => "CR",
					'wluser_id'                 => $USER_ID
				);

				$last_tran = $mysqlClass->insertData('wl_trans', $value);


				if ( $last_tran > 0 ) {

					$userClass->update_wallet_balance_add_amount($USER_ID,$amount);

					$su_avlbal = $mysqlClass->mysqlQuery("SELECT `balance` FROM `admin` WHERE `id`='" . $ADMIN_ID . "' and userType = 'B2B' ")->fetch(PDO::FETCH_ASSOC);

					$nsubalance = $su_avlbal['balance'] - $amount;

					$value = array(
						'wl_id'          				=> $USER_ID,
						'bank_id'                       => $bankname,
						'user_type'                     => 'WL',
						'opening_balance'           	=> $su_avlbal['balance'],
						'withdrawl'                     => $amount,
						'balance'                       => $nsubalance,
						'date_created'                  => date('Y-m-d H:i:s'),
						'created_by'                    => $ADMIN_ID,
						'creator_type'                  => 'ADMIN',
						'transaction_id	'               => $transaction_id,
						'comments'                      => $comments,
						'tr_type'                       => "DR",
						'admin_id'                      => $ADMIN_ID
					);

					if ($mysqlClass->insertData('admin_trans', $value) > 0) {
						
						$mysqlClass->mysqlQuery("UPDATE `admin` set `balance` = (balance - $amount)  WHERE `id`='" . $ADMIN_ID . "' and userType = 'B2B' ");
						
						$helpers->flashAlert_set('add_main_BA', ' Amount ' .$amount. ' added successfully.');
						
					} 
				} 
				else {
					echo $errMSG = ( $errorMsg ) ? $errorMsg : "Something went wrong!!";
					//echo "error" . mysqli_error($conn);
				}
				$mysqlClass->mysqlQuery("COMMIT");
			} 
			else {
				echo "You have insufficient balance to transafer";
			}
			
			
			$helpers->redirect_page("add-main-balance");
		}
	}

$userDetail = $mysqlClass->mysqlQuery("SELECT wallet_balance FROM `add_cust` WHERE `id`='" . $USER_ID . "' ")->fetch(PDO::FETCH_ASSOC);	

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Add Main Balance</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add Main Balance</li>
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
				  <h3 class="card-title">Add Main Balance</h3>

				</div>
				<form method="post" enctype="multipart/form-data">
				<div class="card-body row">
				  <div class="form-group col-md-6">
					<label for="inputName">Current Fund</label>
					<input type="text"  class="form-control" value="<?=($userDetail['wallet_balance']) ? $userDetail['wallet_balance'] : '0.00'?>" readonly >
				  </div>
				  <div class="form-group col-md-6">
					<label for="inputName">Addd</label>
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
