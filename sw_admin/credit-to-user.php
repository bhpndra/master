<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>
<?php
	require("../api/classes/user_class.php");
	$userClass = new user_class();
?>
<?php
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
$user_CR = $helpers->flashAlert_get('user_CR');
$msg = (!empty($user_CR)) ? $helpers->alert_message($user_CR,"alert-success") : '';


$post = $helpers->clearSlashes($_POST); 
	if(isset($_POST['amount']) && $_POST['amount'] > 0 && isset($_POST['user']) && isset($_POST['userType'])){
		$mysqlClass->mysqlQuery("START TRANSACTION");		
			$wl_avlVbal = $mysqlClass->mysqlQuery("SELECT wl_virtual_balance FROM `add_cust` WHERE `id`='" . $USER_ID . "'  FOR UPDATE ")->fetch(PDO::FETCH_ASSOC);	
			
			$wl_v_bal  = $wl_avlVbal['wl_virtual_balance'];
			$amount	= $post['amount'];
			$cr_UserId = $post['user'];
			$transaction_id = $helpers->transaction_id_generator("CR",4);
			
			if ( $wl_v_bal > $amount ) {
				
				if($post['userType']=="DISTRIBUTOR"){					
					$dist_avlVbal = $userClass->check_user_balance($cr_UserId," FOR UPDATE");
					$dist_closingBal = $userClass->update_wallet_balance_add_amount($cr_UserId,$amount);
					
					$dataValueDt = array(
					'dist_retail_wl_admin_id' 	=> $USER_ID,
					'transaction_id' 			=> $transaction_id,
					'agent_trid' 				=> $transaction_id,
					'opening_balance' 			=> $dist_avlVbal,
					'deposits' 					=> $amount,
					'withdrawl' 				=> 0,
					'balance' 					=> $dist_closingBal,
					'date_created' 				=> date("Y-m-d H:i:s"),
					'created_by' 				=> $USER_ID,
					'comments' 					=> $post['comment'],
					'tr_type' 					=> 'CR',
					'dist_id' 					=> $cr_UserId,
					);
					$lastid = $mysqlClass->insertData(" distributor_trans ", $dataValueDt);					
					
				}
				
				if($post['userType']=="RETAILER"){
					$rt_avlVbal = $userClass->check_user_balance($cr_UserId," FOR UPDATE");
					$rt_closingBal = $userClass->update_wallet_balance_add_amount($cr_UserId,$amount);
					
					$dataValueRt = array(
					'ret_dest_wl_admin_id' 		=> $USER_ID,
					'transaction_id' 			=> $transaction_id,
					'agent_trid' 				=> $transaction_id,
					'opening_balance' 			=> $rt_avlVbal,
					'deposits' 					=> $amount,
					'withdrawl' 				=> 0,
					'balance' 					=> $rt_closingBal,
					'date_created' 				=> date("Y-m-d H:i:s"),
					'created_by' 				=> $USER_ID,
					'comments' 					=> $post['comment'],
					'tr_type' 					=> 'CR',
					'retailer_id' 				=> $cr_UserId,
					);
					
					$lastid = $mysqlClass->insertData(" retailer_trans ", $dataValueRt);				
					
				}
				
				if(isset($lastid) && $lastid > 0){
					/* White label transaction */
					$mysqlClass->mysqlQuery("UPDATE `add_cust` set wl_virtual_balance = (wl_virtual_balance - $amount) WHERE `id`='" . $USER_ID . "' and admin_id = '".$ADMIN_ID."' ");
					
					
					$dataValue = array(
						'transaction_id' 	=> $transaction_id,
						'agent_trid' 		=> $transaction_id,
						'dist_ret_id' 		=> $cr_UserId,
						'opening_balance' 	=> $wl_v_bal,
						'deposits' 			=> 0,
						'withdrawl' 		=> $amount,
						'balance' 			=> ($wl_v_bal - $amount),
						'date_created' 		=> date("Y-m-d H:i:s"),
						'created_by' 		=> $USER_ID,
						'comments' 			=> $post['comment'],
						'tr_type' 			=> 'DR',
						'wluser_id' 		=> $USER_ID,
						);
					$mysqlClass->insertData(" wl_virtual_trans ", $dataValue);
					$helpers->flashAlert_set('user_CR'," User credit successfully. Amount: $amount ");
				
				$mysqlClass->mysqlQuery("COMMIT");
					
					$helpers->redirect_page("credit-to-user");
				} else {
					$msg  = $helpers->alert_message("Some thing wrong, Transaction not complete.","alert-danger");
				}
				
					
				
			} else {
				$msg  = $helpers->alert_message("You have insufficient virsual balance to transafer.","alert-danger");
			}
					
	}



?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Credit To User</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Credit To User</li>
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
			<div class="col-md-8">
			<?=($msg!='')? $msg : '';?>
          <div class="card card-danger">
            <div class="card-header">
              <h3 class="card-title">Credit To User</h3>

            </div>
			<form method="post" id="creditForm" >
            <div class="card-body row">
			  <div class="form-group col-md-3">
                <label for="inputName">Select User Type</label>
                <select name="userType" id="userType" onchange="selectUserType(this.value)" class='form-control' required>
					<option selected disabled value="">Select User Type</option>
					<option value="RETAILER">Retailer</option>
					<option value="DISTRIBUTOR">Distributor</option>
				</select>
              </div>
              <div class="form-group col-md-9">
                <label for="inputName">User</label>
                <select name="user" id="user" class='form-control' required>
					<option>Select User</option>
				</select>
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Amount</label>
                <input type="text"  class="form-control" value="" name="amount" id="amount" maxlength="6" onkeyup="convertNumberToWords(this.value)" onblur="convertNumberToWords(this.value);" required />
				<div id="amountInWord" class="error invalid-feedback"></div>
              </div>
              <div class="form-group col-md-6">
                <label for="inputName">Comment</label>
                <input type="text"  class="form-control" value="" name="comment"  >
              </div>  
            </div>
			  <div class="card-footer">
				<button type="submit" name="credit"  class="btn btn-primary">Credit</button>
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
<script>
function convertNumberToWords(amount) { 
    var words = new Array();
    words[0] = '';
    words[1] = 'One';
    words[2] = 'Two';
    words[3] = 'Three';
    words[4] = 'Four';
    words[5] = 'Five';
    words[6] = 'Six';
    words[7] = 'Seven';
    words[8] = 'Eight';
    words[9] = 'Nine';
    words[10] = 'Ten';
    words[11] = 'Eleven';
    words[12] = 'Twelve';
    words[13] = 'Thirteen';
    words[14] = 'Fourteen';
    words[15] = 'Fifteen';
    words[16] = 'Sixteen';
    words[17] = 'Seventeen';
    words[18] = 'Eighteen';
    words[19] = 'Nineteen';
    words[20] = 'Twenty';
    words[30] = 'Thirty';
    words[40] = 'Forty';
    words[50] = 'Fifty';
    words[60] = 'Sixty';
    words[70] = 'Seventy';
    words[80] = 'Eighty';
    words[90] = 'Ninety';
    amount = amount.toString();
    var atemp = amount.split(".");
    var number = atemp[0].split(",").join("");
    var n_length = number.length;
    var words_string = "";
    if (n_length <= 9) {
        var n_array = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
        var received_n_array = new Array();
        for (var i = 0; i < n_length; i++) {
            received_n_array[i] = number.substr(i, 1);
        }
        for (var i = 9 - n_length, j = 0; i < 9; i++, j++) {
            n_array[i] = received_n_array[j];
        }
        for (var i = 0, j = 1; i < 9; i++, j++) {
            if (i == 0 || i == 2 || i == 4 || i == 7) {
                if (n_array[i] == 1) {
                    n_array[j] = 10 + parseInt(n_array[j]);
                    n_array[i] = 0;
                }
            }
        }
        value = "";
        for (var i = 0; i < 9; i++) {
            if (i == 0 || i == 2 || i == 4 || i == 7) {
                value = n_array[i] * 10;
            } else {
                value = n_array[i];
            }
            if (value != 0) {
                words_string += words[value] + " ";
            }
            if ((i == 1 && value != 0) || (i == 0 && value != 0 && n_array[i + 1] == 0)) {
                words_string += "Crores ";
            }
            if ((i == 3 && value != 0) || (i == 2 && value != 0 && n_array[i + 1] == 0)) {
                words_string += "Lakhs ";
            }
            if ((i == 5 && value != 0) || (i == 4 && value != 0 && n_array[i + 1] == 0)) {
                words_string += "Thousand ";
            }
            if (i == 6 && value != 0 && (n_array[i + 1] != 0 && n_array[i + 2] != 0)) {
                words_string += "Hundred and ";
            } else if (i == 6 && value != 0) {
                words_string += "Hundred ";
            }
        }
        words_string = words_string.split("  ").join(" ");
    }
    //return words_string;
	$("#amountInWord").text(words_string);
	$("#amountInWord").css("display","block");

}
$(document).ready(function(){

    $('#creditForm').on('submit', function(e){
        e.preventDefault();
		var form = this;
		var amountinword = $("#amountInWord").html();
		var amount = $("#amount").val();
		var user = $("#user option:selected").html();
		var userid = $("#user").val();
		
		if(amount <= 0){
			return false;
		}
		var textMessage = "<table class='table'> <tr><th>User:</th><td>" + user + "<td></tr> <tr><th>Amount:</th><td>Rs. " + amount + "<td></tr> <tr><th colspan='2' style='text-align:center;color:green'>" + amountinword + "</th></tr></table>";
			
			Swal.fire({
			  title: "Are you sure Credit ?",
			  html: textMessage,
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			  confirmButtonText: "Credit Now",
			  closeOnConfirm: false
			}).then((result) => {
			    if (result.value) {
					form.submit(false);
				  }
			});
		 	
    });		
});

	function selectUserType(val){
		//alert(val);
		$.ajax({
            url: 'ajax/get_retailerdistributor.php',
            cache: false,
            type: 'POST',
            data: {usertype: val },
            success: function (response) {
                $("#user").html(response);
            }
        });
	}
</script>
</html>
