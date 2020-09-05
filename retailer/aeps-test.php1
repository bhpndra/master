<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>
<?php
	require("../api/classes/db_class.php");
	require("../api/classes/comman_class.php");
	$helpers = new Helper_class();
	$mysqlClass = new mysql_class();
?>
<?php
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
$msg = '';


$post = $helpers->clearSlashes($_POST); 
	if(isset($_POST['update_profile'])){
		$agent_id = "DUMAGTID".time();
		$tranid = "DUMMYTRAN".time();
		$values = array(
				"user_id"           => 1, 
				"outletid"          => $post['outletid'],
				"transaction_id"    => $agent_id,
				"agent_trid"        => $agent_id,
				"terminalid"        => 1,
				"bcid"              => time(),
				"txntype"           => 'WAP',
				"message"           => "INITIATE",
				"txn_status"        => "PENDING",
				"amount"            => $post['amount'],
				"tran_amount"       => $post['amount'],
				"mobile"      		=> $post['mobile'],
				"uid"	      		=> $post['uid'],
				"bank_iin"          => '198.162.0.1',
				"transaction_date"  => date('Y-m-d H:i:s')
			);
		
		
		$webhook_url = DOMAIN_NAME."aeps_web_hook.php";
		$ch = curl_init( $webhook_url );
		# Setup request to send json via POST.
		$payload = json_encode(  $values );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		# Return response instead of printing.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		# Send request.
		echo $result = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($result,true);
		
		if($res['MESSAGE']=="Transaction Success"){
			
			$updateValues = array(
								"status" => 'SUCCESS',
								"amount" => $post['amount'],
								"message" => "Transaction Success",
								"agent_trid" => $agent_id,
								"transaction_id" => $tranid
							);
			
			$transaction_url = DOMAIN_NAME."aeps_call_back.php";

			$ch1 = curl_init( $transaction_url );
			# Setup request to send json via POST.
			$payload = json_encode(  $updateValues );
			curl_setopt( $ch1, CURLOPT_POSTFIELDS, $payload );
			curl_setopt( $ch1, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			# Return response instead of printing.
			curl_setopt( $ch1, CURLOPT_RETURNTRANSFER, true );
			# Send request.
			echo $result = curl_exec($ch1);
			curl_close($ch1);
		}
		
	}


$userDetail = $mysqlClass->mysqlQuery("SELECT * FROM `outlet_kyc` WHERE `user_id`='" . $USER_ID . "' ")->fetch(PDO::FETCH_ASSOC);	

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">AEPS Test</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">AEPS Test</li>
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
			<div class="col-md-6">
			<?=($msg0!='')? $msg0 : '';?>
          <div class="card card-danger">
            <div class="card-header">
              <h3 class="card-title">AEPS Test</h3>

            </div>
			<form method="post" enctype="multipart/form-data">
            <div class="card-body row">

              <div class="form-group col-md-6">
                <label for="inputName">Outlet Id</label>
                <input type="text"  class="form-control" value="<?=$userDetail['outletid']?>" name="outletid" required readonly />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Mobile</label>
                <input type="tel"  class="form-control" value="9876543210" name="mobile" />
              </div>   
              <div class="form-group col-md-6">
                <label for="inputName">Amount</label>
                <input type="number"  class="form-control" value="" name="amount"  min="100" max="10000" />
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Addhaar No..</label>
                <input type="text"  class="form-control" value="xxxx-xxxx-1234" name="uid"  />
              </div>
            </div>
			  <div class="card-footer">
				<button type="submit" name="update_profile" class="btn btn-primary">Process Test</button>
			  </div>
			</form>
            <!-- /.card-body -->
          </div>
          </div>
          <!-- /.card -->
        </div>
        
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>

</html>
