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

$post = $helpers->clearSlashes($_POST); 
	if(isset($_POST['add']) ){
			$add_fund_url = BASE_URL . "/api/users/add-fund-request.php";
			unset($post['add']);
			$post_fields = $post;
			$post_fields["token"] = $_SESSION['TOKEN'];
			$receipt = new CURLFile($_FILES['receipt']['tmp_name'], $_FILES['receipt']['type'], $_FILES['receipt']['name']);
			$post_fields['receipt'] = $receipt;
			
			echo $resAF = api_curl($add_fund_url, $post_fields, $headerArray);
			$resAFDetails = json_decode($resAF, true);
			if (isset($resAFDetails['ERROR_CODE']) && $resAFDetails['ERROR_CODE'] == 0) {
				$helpers->flashAlert_set('add_virtual_FA'," Fund request accepted. ");
				$helpers->redirect_page("add-fund");
			} else {
				$helpers->flashAlert_set('add_virtual_FA',$resAFDetails['MESSAGE']);
				$helpers->redirect_page("add-fund");
			}		
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
<?php 
	$urlBankDetails = BASE_URL."/api/wl-bank-list.php";
	$resBanksJson = api_curl($urlBankDetails,$post_fields,$headerArray);
	$resBanks = json_decode($resBanksJson,true);
	if(isset($resBanks['ERROR_CODE']) && $resBanks['ERROR_CODE']==0){
?>
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
						foreach($resBanks['DATA'] as $banks){
							echo "<option value='".$banks['id']."'>".$banks['bank_name']."</option>";
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
	<?php } ?>
			<div class="col-md-6">
			  <div class="card card-dark">
				<div class="card-header">
				  <h3 class="card-title">Bank Details</h3>

				</div>
				<form method="post" enctype="multipart/form-data">
				<div class="card-body row">
<?php 
	if(isset($resBanks['ERROR_CODE']) && $resBanks['ERROR_CODE']==0){
		foreach($resBanks['DATA'] as $banks){
			?>
				<div class="col-12">
				<div class="callout callout-dark">
                  <p>
					<strong>Bank Name: </strong> <?=$banks['bank_name']?><br/>
					<strong>Account: </strong> <?=$banks['account_number']?><br/>
					<strong>IFSC: </strong> <?=$banks['ifsc']?><br/>
					<strong>Branck: </strong> <?=$banks['branch']?>
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
