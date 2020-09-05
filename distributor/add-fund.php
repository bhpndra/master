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


if(isset($_POST['add']) ){
	$post = $helpers->clearSlashes($_POST); 
	//// Check Rquired Parameters Isset ////
	$date = $post['date'];
	$amount = $post['amount'];
	$payment_type = $post['payment_type'];
	$payment_bank = $post['payment_bank'];
	$bank_trn_id = $post['bank_trn_id'];
	$message = $post['message'];
	$mobile = $post['mobile'];
	
	if(empty($mobile)){
		$helpers->errorResponse("Number is missing or not valid.");
	}
	if(empty($amount) || $amount <= 0){
		$helpers->errorResponse("Amount is missing or invalid.");
	}
	if(empty($date)){
		$helpers->errorResponse("Date is missing.");
	}
	if(empty($payment_type)){
		$helpers->errorResponse("Payment Type is missing.");
	}
	if(empty($payment_bank)){
		$helpers->errorResponse("Payment Bank is missing.");
	}
	if(empty($message)){
		$helpers->errorResponse("Message is missing.");
	}
	if(empty($mobile)){
		$helpers->errorResponse("Mobile is missing.");
	}
	//
	//File upload--
	if(isset($_FILES['receipt']['name']) && !empty($_FILES['receipt']['name'])){
		$receiptUpload = $helpers->fileUpload($_FILES["receipt"],"../uploads/receipt/",'receipt_'.time(),true);
		if($receiptUpload['type']=="success"){
			$receiptName = $receiptUpload['filename'];
		} else {
			$helpers->errorResponse($receiptUpload['message']);
		}
	}else{
		$helpers->errorResponse("Payment receipt missing.");
	}
	
		$valueAF = array(
			'bank'				=>	$payment_bank,				
			'amount'			=>	$amount,				
			'bank_refno'		=>	$bank_trn_id,				
			'mobile'			=>	$mobile,				
			'message'			=>	$message,				
			'status'			=>	'PENDING',				
			'user_id'			=>	$USER_ID,				
			'user_type'			=>	'DISTRIBUTOR',				
			'admin_id'			=>	$ADMIN_ID,				
			'payment_date'		=>	$date,				
			'deposit_slip'		=>	$receiptName,				
			'request_time'		=>	date("Y-m-d H:i:s")				
		);
		
		$rtTran_lastid = $mysqlClass->insertData(" payment ", $valueAF);
			
		$helpers->flashAlert_set('add_virtual_FA'," Fund request accepted. ");
		//$helpers->redirect_page("add-fund");
				
	}

$userDetail = $mysqlClass->mysqlQuery("SELECT mobile FROM `add_cust` WHERE `id`='" . $USER_ID . "' ")->fetch(PDO::FETCH_ASSOC);	
$msg = $helpers->flashAlert_get('add_virtual_FA');

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Add Fund</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add Fund</li>
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
				
				<?=($msg!='')? $helpers->alert_message($msg,'alert-info') : '';?>
				
			  <div class="card card-success">
				<div class="card-header">
				  <h3 class="card-title">Add Fund Request</h3>

				</div>
				<form method="post" enctype="multipart/form-data">
				<div class="card-body row">				  
				  <div class="form-group col-md-6">
					<label for="inputName">Amount</label>
					<input type="text"  class="form-control" value="" name="amount" required >
				  </div>				  
				  <div class="form-group col-md-6">
					<label for="inputName">Payment Date</label>
					<input type="date"  class="form-control" value="" name="date" required >
				  </div>				  
				  <div class="form-group col-md-6">
					<label for="inputName">Payment Type</label>
					<select  class="form-control select2" name="payment_type" required >
						<option value="NEFT">NEFT</option>
						<option value="RTGS">RTGS</option>
						<option value="IMPS">IMPS</option>
						<option value="TPT">THIRD PARTY TRANSFER</option>
						<option value="CDM">CDM DEPOSITE</option>
						<option value="CASH">CASH COUNTER DEPOSITE</option>
					</select>
				  </div>				  
				  <div class="form-group col-md-6">
						
					<label for="inputName">Payment Bank</label>
					<select  class="form-control select2" name="payment_bank" required >
						<option value='' disabled selected>Select Bank</option>
					<?php
						$banks = $mysqlClass->fetchAllData(" bank_details ", "*" , " WHERE `user_id`='".$WL_ID."' and `admin_id`='".$ADMIN_ID."'" );
						foreach($banks as $bank){
							echo "<option value='".$bank['id']."'>".$bank['bank_name']."</option>";
						}
					?>						
					</select>
				  </div>				  
				  <div class="form-group col-md-6">
					<label for="inputName">Refrence No. / Bank Tr. Id</label>
					<input type="text"  class="form-control" value="" name="bank_trn_id" required >
				  </div>				  
				  <div class="form-group col-md-6">
					<label for="inputName">Receipt</label>
					<input type="file"  class="form-control" value="" name="receipt" required >
				  </div>				  
				  <div class="form-group col-md-7">
					<label for="inputName">Message</label>
					<input type="text"  class="form-control" value="" name="message" required >
				  </div>				  
				  <div class="form-group col-md-5">
					<label for="inputName">Mobile</label>
					<input type="text"  class="form-control" value="<?=isset($userDetail['mobile']) ? $userDetail['mobile'] : ''?>" name="mobile" required readonly>
				  </div>					  
				</div>
				  <div class="card-footer">
					<button type="submit" name="add" class="btn btn-primary">Add Request</button>
				  </div>
				</form>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->

			</div>
	
			<div class="col-md-6">
			  <div class="card card-dark">
				<div class="card-header">
				  <h3 class="card-title">Bank Details</h3>

				</div>
				<form method="post" enctype="multipart/form-data">
				<div class="card-body row">
<?php 
	if( count($banks) >0 ){
		foreach($banks as $bank){
			?>
				<div class="col-12">
				<div class="callout callout-dark">
                  <p>
					<strong>Bank Name: </strong> <?=$bank['bank_name']?><br/>
					<strong>Account: </strong> <?=$bank['account_number']?><br/>
					<strong>IFSC: </strong> <?=$bank['ifsc']?><br/>
					<strong>Branck: </strong> <?=$bank['branch']?>
				  </p>
                </div>
                </div>
			<?php
		}
	} else {
			?>
				<div class="col-12">
				<div class="callout callout-danger">
                  <h3>
					Contact to Admin. Bank details not available
				  </h3>
                </div>
                </div>
			<?php
	}
?>
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
