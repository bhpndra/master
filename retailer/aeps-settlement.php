<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<body class="hold-transition sidebar-mini-md">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light" style="margin-left: 0px;">
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fas fa-fingerprint"></i> AESP
          <span class="badge badge-danger"><i class="fas fa-rupee-sign"></i> <span id="navAB">0.00</span></span>
        </a>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fas fa-wallet"></i> Balance: 
          <span class="badge badge-warning"><i class="fas fa-rupee-sign"></i>  <span id="navWB">0.00</span></span>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
<?php
	require("../api/classes/db_class.php");
	require("../api/classes/comman_class.php");
	require("../api/classes/user_class.php");
	$helpers = new Helper_class();
	$mysqlClass = new mysql_class();
	$userClass = new user_class();
?>
<?php
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];

$user_CR = $helpers->flashAlert_get('user_CR');
$msg = (!empty($user_CR)) ? $helpers->alert_message($user_CR,"alert-success") : '';

$resBank = $mysqlClass->fetchRow("retailer_bank_details"," * "," where user_id = '".$USER_ID."'");

if(isset($_POST)){
	$post = $helpers->clearSlashes($_POST); 
}
if(isset($_POST['addBank'])){
	$generated_date = date('Y-m-d H:i:s');
	$data = array(
			'bank_name' => $post['bName'],
			'ifsc' => $post['ifsc'],
			'branch' => $post['branch'],
			'account_number' => $post['acNumber'],
			'account_name' => $post['acName'],
			'user_id' => $USER_ID,
			'admin_id' => $ADMIN_ID,
			'date_created' => $generated_date
		);
		$id = $mysqlClass->insertData('retailer_bank_details',$data);
		if($id>0){
			echo "<script>window.location= 'aeps-settlement';</script>"; die();
		}
}



	if(isset($_POST['withdrawal']) && (isset($_POST['totalAmt']) && $_POST['totalAmt']>0)){ 
	
		$retailerTran = $mysqlClass->fetchRow("aeps_withdrawl_info", " settlement_date ", " where user_id ='".$USER_ID."'  ORDER BY  id DESC LIMIT 1");
		$transaction_time   = strtotime($retailerTran['settlement_date']);
        $current_time       = time();
        $transaction_gap    = $current_time - $transaction_time;

		
		if($transaction_gap > 120 ) {
			
			$generated_date = date('Y-m-d H:i:s');
			$transaction_id = $helpers->transaction_id_generator("AEPSSTM",7);
			$group_id = $helpers->transaction_id_generator("GID",8);
			
			$post = $helpers->clearSlashes($_POST);
			
			
			/* *** Settlement type wallet *** */
			if(isset($post['settlement_type']) && $post['settlement_type']=="wallet"){ 
				$totalAmt = $mysqlClass->fetchRow("aeps_info"," sum(amount) as totalWithdrawalAmt "," where user_id = '".$USER_ID."' and id in (".implode(",",$post['settlementId']).")  and settlement_retailer = 'PENDING' and status = 'SUCCESS' and txntype = 'WAP' ");
				$withdrawal_Amount = (float) $totalAmt['totalWithdrawalAmt'];
				if($withdrawal_Amount == (float) $post['totalAmt']){
					
					$mysqlClass->mysqlQuery("START TRANSACTION");		
					$wl_avlVbal = $mysqlClass->mysqlQuery("SELECT wl_virtual_balance FROM `add_cust` WHERE `id`='" . $WL_ID . "'  FOR UPDATE ")->fetch(PDO::FETCH_ASSOC);	
					
					$wl_v_bal  = $wl_avlVbal['wl_virtual_balance'];
					$amount	= $post['amount'];
					
					if ( $wl_v_bal > $withdrawal_Amount ){ 
						
						/* *** Update AEPS Transactions settlement (aeps_info) *** */
						foreach($post['settlementId'] as $row){						
							$Udata = array(
								'settlement_retailer'		=> 'SUCCESS',				
								'rt_group_id'				=> $group_id
							);
							$mysqlClass->updateData('aeps_info',$Udata," where user_id = '".$USER_ID."' and id = '".$row."' ");
						}
						
						/* *** Insert Withdrawal Info *** */
						$data = array(
							'transaction_id'		=> $transaction_id,
							'withdrawl_type'		=> $post['settlement_type'],
							'user_id'				=> $USER_ID,
							'amount'				=> $withdrawal_Amount,
							'status'				=> 'SUCCESS',
							'payment_date'			=> $generated_date,				
							'settlement_date'		=> $generated_date,	
							'group_id'			=> $group_id
						);
						$id = $mysqlClass->insertData('aeps_withdrawl_info',$data);
						
						/* *** Get User Current Balance (Opening Balance) *** */
						
						$rt_avlVbal = $userClass->check_user_balance($USER_ID," FOR UPDATE");
						$rt_closingBal = $userClass->update_wallet_balance_add_amount($USER_ID,$withdrawal_Amount);
						
						$dataValueRt = array(
						'ret_dest_wl_admin_id' 		=> $USER_ID,
						'transaction_id' 			=> $transaction_id,
						'agent_trid' 				=> $transaction_id,
						'opening_balance' 			=> $rt_avlVbal,
						'deposits' 					=> $withdrawal_Amount,
						'withdrawl' 				=> 0,
						'balance' 					=> $rt_closingBal,
						'date_created' 				=> $generated_date,
						'created_by' 				=> $USER_ID,
						'comments' 					=> "AESP Settlement",
						'tr_type' 					=> 'AEPS_STLM',
						'retailer_id' 				=> $USER_ID,
						);
						
						$last_tran = $mysqlClass->insertData(" retailer_trans ", $dataValueRt);
						
						/* *** Get User Current AEPS Balance (Opening Balance) *** */
						$aeps_avlbal = $userClass->check_user_aeps_wallet_balance($USER_ID," FOR UPDATE");
						
						$updateAmt = (float) $aeps_avlbal -  $withdrawal_Amount;
						$userClass->update_aeps_wallet_balance($USER_ID,$updateAmt);
						
						if($last_tran > 0){
							
							$mysqlClass->mysqlQuery("UPDATE `add_cust` set wl_virtual_balance = (wl_virtual_balance - $withdrawal_Amount) WHERE `id`='" . $WL_ID . "' and admin_id = '".$ADMIN_ID."' ");
					
					
							$dataValue = array(
								'transaction_id' 	=> $transaction_id,
								'agent_trid' 		=> $transaction_id,
								'dist_ret_id' 		=> $USER_ID,
								'opening_balance' 	=> $wl_v_bal,
								'deposits' 			=> 0,
								'withdrawl' 		=> $withdrawal_Amount,
								'balance' 			=> ($wl_v_bal - $withdrawal_Amount),
								'date_created' 		=> date("Y-m-d H:i:s"),
								'created_by' 		=> $USER_ID,
								'comments' 			=> "AESP Settlement",
								'tr_type' 			=> 'AEPS_STLM',
								'wluser_id' 		=> $WL_ID,
								);
							$mysqlClass->insertData(" wl_virtual_trans ", $dataValue);
							
							
							/* $alertMsg = "<strong>Opening Balance:</strong> ".$rt_avlVbal."<br/>";
							$alertMsg .= "<strong> Amount:</strong> ".$withdrawal_Amount."<br/>";
							$alertMsg .= "<strong>Closing Balance:</strong> ".$rt_closingBal."<br/>";
							$credited = '<script>
							Swal.fire({
									  title: "Wallet Updated Successfully",
									  html: "'.$alertMsg.'",
									  type: "success",
									  showCancelButton: false,
									  confirmButtonColor: "#DD6B55",
									  confirmButtonText: "Ok",
									  closeOnConfirm: false
									});					
							</script>';
							$helpers->flashAlert_set("walletUpdate",$credited);
							$helpers->redirect_page('aeps-settlement'); */							
						echo '<script> alert("Request accepted."); self.close();</script>'; die();
						}
						
					}
					else {
						echo "<script> alert('Contact to admin !'); </script>";
					}
					$mysqlClass->mysqlQuery("COMMIT");
				}
			}
			
			
			/* *** Settlement type bank *** */
			if(isset($post['settlement_type']) && $post['settlement_type']=="bank"){
				
				
				$totalAmt = $mysqlClass->fetchRow("aeps_info"," sum(amount) as totalWithdrawalAmt "," where user_id = '".$USER_ID."' and id in (".implode(",",$post['settlementId']).")  and settlement_retailer = 'PENDING' and status = 'SUCCESS' and txntype = 'WAP' ");
				$withdrawal_Amount = (float) $totalAmt['totalWithdrawalAmt'];
				
				
				$transactionCharge = 0;
				
				if($transactionCharge < $withdrawal_Amount){
					if($withdrawal_Amount == (float) $post['totalAmt']){
												
						
						/* *** Update AEPS Transactions settlement (aeps_info) *** */
						foreach($post['settlementId'] as $row){						
							$Udata = array(
								'settlement_retailer'		=> 'SUCCESS',				
								'rt_group_id'				=> $group_id
							);
							$mysqlClass->updateData('aeps_info',$Udata," where user_id = '".$USER_ID."' and id = '".$row."' ");
						}
						
						$bankWithdrawlAmount = $withdrawal_Amount - $transactionCharge;
						/* *** Insert Withdrawal Info *** */
						$data = array(
							'transaction_id'		=> $transaction_id,
							'withdrawl_type'		=> $post['settlement_type'],
							'user_id'				=> $USER_ID,
							'amount'				=> $bankWithdrawlAmount,
							'status'				=> 'PENDING',
							'payment_date'			=> $generated_date,				
							'settlement_date'		=> $generated_date,			
							'bank_name'				=> $resBank['bank_name'],			
							'ifsc'					=> $resBank['ifsc'],			
							'account_name'			=> $resBank['account_name'],			
							'account_number'		=> $resBank['account_number'],			
							'group_id'				=> $group_id
						);
						$id = $mysqlClass->insertData('aeps_withdrawl_info',$data);
						
											
						/* *** Get User Current AEPS Balance (Opening Balance) *** */
						$aeps_avlbal = $userClass->check_user_aeps_wallet_balance($USER_ID," FOR UPDATE");
						
						$updateAmt = (float) $aeps_avlbal -  $withdrawal_Amount;
						$userClass->update_aeps_wallet_balance($USER_ID,$updateAmt);
																	
						if($id > 0){							
							/* $credited = '<script>
							swal({
									  title: "Your Request Accepted",
									  text: "",
									  type: "success",
									  showCancelButton: false,
									  confirmButtonColor: "#DD6B55",
									  confirmButtonText: "Ok",
									  closeOnConfirm: false,
									  html: true
									});					
							</script>';
							$helpers->flashAlert_set("walletUpdate",$credited);
							$helpers->redirect_page('aeps-settlement'); */
						}
						echo '<script> alert("Request accepted."); self.close();</script>'; die();
					}
					else {
						echo $withdrawal_Amount ." == ". (float) $post['totalAmt'];
					}
				} else {
					echo '<script type="text/javascript">';
					echo 'setTimeout(function () { swal("Invalid Withdrawal Amount !"," Withdrawal amount accept greater than '.$transactionCharge.'rp  ");';
					echo '}, 1000);</script>';
				}
			}
			
			
			
		} else {
			echo '<script type="text/javascript">';
			echo 'setTimeout(function () { swal("Sorry!!. You are doing duplicate transaction!!","error");';
			echo '}, 1000);</script>';
		}
	}



?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="margin-left: 0px;">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">AEPS Settlement</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">AEPS Settlement</li>
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
			<div class="col-md-12">
			<?=($msg!='')? $msg : '';?>
          <div class="card card-danger">
            <div class="card-header">
              <h3 class="card-title">AEPS Settlement</h3>

            </div>
			<form method="post" class="container">
			<div class="row">					  
				<div class="col-md-5">					  
					<div class="row">					  
					  <div class="col-md-12 form-group pt-5">
						<label>Wallet:
						<input type="radio" name="settlement_type" value="wallet" checked /> &nbsp; &nbsp;</label>
						<label>Bank:
						<input type="radio" name="settlement_type" value="bank" <?=($resBank['id']>0)? '' : 'disabled'?> /></label>
					  </div>
					  <div class="col-6 form-group">
						<input type="text" name="totalAmt" id="totalAmt" value="0" readonly class="form-control">
					  </div>
					  <div class="col-6 form-group">
						<button type="submit" name="withdrawal" id="withdrawal" class="btn btn-primary" style="display:none">Withdrawal</button>
						<button type="button" name="withdrawal" onclick="return confirmation()" class="btn btn-primary">Withdrawal</button>
					  </div>
					</div>
				</div>
				<div class="col-md-1"></div>
				<div class="col-md-6 text-right">
					<div class="row">
						<div class="col-12">
							<h4>Bank Details <?php /* if($resBank['id']>0){ ?> - <a href="edit-bank.php" style="font-size:16px;">Edit Bank</a><?php } */ ?></h4>
							<?php if($resBank['id']>0){ 
								echo "<div class='text-left' style='display: inline-block;'>";
								echo "<strong>Bank Name: </strong>". $resBank['bank_name'] . "<br/>";
								echo "<strong>Bank IFSC: </strong>". $resBank['ifsc'] . "<br/>";
								echo "<strong>Account Num.: </strong>". $resBank['account_number'] . "<br/>";
								echo "<strong>Account Name.: </strong>". $resBank['account_name'] . "<br/>";
								echo "<strong>Branch: </strong>". $resBank['branch'] . "<br/>";
								echo "</div>";
							} else { ?>
								<a href="javascript:void(0)" data-toggle="modal" data-target="#myModal" class="btn btn-success">Add your Bank</a>
							<?php } ?>
							<hr/>
							<h5 style="text-align:right;color:red">Note: Bank Transaction Charges - Rs 0</h5>
						</div>
					</div>
				</div>
			</div>
		<hr/>
		<div class="table-responsive">
		  <table class="table table-bordered table-hover">
			<thead>
			  <tr>
				<th><input type="checkbox" onchange="checkAll(this)"  value="0" /></th>
				<th>TrID</th>
				<th>Amount </th>
				<th>Time</th>
				<th>Status</th>
			  </tr>
			</thead>
			<tbody>
<?php
		
$orderBy = "ORDER BY `id` ASC LIMIT 300 ";
$sql = "select * from aeps_info where `user_id`='".$USER_ID."' and settlement_retailer = 'PENDING' and status = 'SUCCESS' and txntype = 'WAP' and amount > 0 ";

$sqlQuery = $mysqlClass->mysqlQuery($sql.$filter.$orderBy);
				
				while($tran = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
?>
			  <tr>
				<td><input type="checkbox" value="<?php echo $tran['id']; ?>" name="settlementId[]" data-amt="<?php echo $tran['amount']; ?>" class="s_chk" /></td>
				<td><?php echo $tran['transaction_id']; ?></td>
				<td><?php echo $tran['amount']; ?></td>
				<td><?php echo $tran['date_created']; ?></td>
				<td><?php echo $tran['status']; ?></td>
			  </tr>
			  <tr>
			<?php } ?>
			</tbody>
		  </table>
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
  
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
		<h4 class="modal-title">Add Bank</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>       
      </div>
	  <form method="POST" >
		  <div class="modal-body">
			<p><label>Bank Name</label> <input type="text"  name="bName" class="form-control" required ></p>
			<p><label>Bank IFSC</label> <input type="text"  name="ifsc" class="form-control" required ></p>
			<p><label>Account No.</label> <input type="text"  name="acNumber" class="form-control" required ></p>
			<p><label>Account Holder Name.</label> <input type="text"  name="acName" class="form-control" required ></p>
			<p><label>Branch</label> <input type="text"  name="branch" class="form-control" required ></p>
		  </div>
		  <div class="modal-footer">
			<input type="submit" name="addBank" value="Add" class="btn btn-primary float-left"> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
	  </form>
    </div>
  </div>
</div>    
<?php include("inc/footer.php"); ?>

 <script>

 function confirmation(){
	 var settlementType = $("input[name='settlement_type']:checked").val();	

		Swal.fire({
			  title: "Are you sure want settle amount to " + settlementType,
			  html: '',
			  type: "success",
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			  confirmButtonText: "Yes, Process",
			  closeOnConfirm: false
			}).then((isConfirm) => {
				if (isConfirm.value) {
				$('#withdrawal').click();
				Swal.fire(
				  'Please Wait...'
				);
			  } else {					  
				return false;
			  }
			});	
 }
 
$(document).ready(function(){
	 $('input[type="checkbox"][name="settlementId[]"]').change(function() {
		 getCheckValue();
	 }); 
 });
 function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
		 
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             //console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
	 getCheckValue();
 }
	function getCheckValue(){ 		
		var selected = new Array();
		$(".s_chk:checked").each(function() {
		   selected.push($(this).attr('data-amt'));
		});
		var totalAmt = selected.reduce(function(acc, val) { return (parseFloat(acc) + parseFloat(val)).toFixed(2); }, 0);
		$("#totalAmt").val(totalAmt);
	}
 </script>
</html>
