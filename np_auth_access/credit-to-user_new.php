<?php
	include_once('inc/head.php');
	include_once('inc/header.php');
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	include_once("classes/user_class.php");
	$userClass = new user_class();
	$post = $helpers->clearSlashes($_POST);
?>
<?php
if (isset($_POST['userType']) && isset($_POST['user']) && isset($_POST['bankname']) && isset($_POST['amount'])) {
	if($post['userType']=="2"){
		$usertype   = "retailer";  
	}
	if($post['userType']=="3"){
		$usertype   = "distributor";  
	}
	if($post['userType']=="5"){
		$usertype   = "white_label";  
	}
	
	$bankname   = $post['bankname'];
	$retailerid = $post['user'];  
	$comments   = $post['comments'];
	$amount     = $post['amount'];
	$su_avlbal         = $mysqlClass->mysqlQuery("SELECT `balance`,`ret_dest_wl_admin_id`,`date_created` FROM `admin_trans` WHERE `admin_id`='" . $_SESSION[_session_userid_] . "' ORDER BY `trans_id` DESC LIMIT 1 ")->fetch(PDO::FETCH_ASSOC);
	$superadmin_bal    = $su_avlbal['balance'];
	$credit_user_id    = $su_avlbal['ret_dest_wl_admin_id'];
	$transaction_time  = strtotime($su_avlbal['date_created']);
	 
	$current_time          = time();  echo "<br/>";
	 
	$transaction_gap = $current_time - $transaction_time; echo "<br/>";
	
	if ( ($transaction_gap < 300) AND ($credit_user_id == $retailerid ) ) { //300 is the seconds
	   
		echo "<script>alert('Sorry!!. You are doing duplicate transaction!!')</script>";
		
	} else {
	
		if ( $superadmin_bal > $amount ) {
			
			if ($usertype == "retailer") {
				
				$userinfo         = $mysqlClass->mysqlQuery("SELECT `user_id` FROM `add_retailer` WHERE `user_id`='$retailerid' ")->fetch(PDO::FETCH_ASSOC);
				
				if ( $userinfo['user_id'] ) {
					
					$retailer_avlbal = $mysqlClass->mysqlQuery("SELECT `balance` FROM `retailer_trans` WHERE `retailer_id`='$retailerid' ORDER BY `id` DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

					if ($retailer_avlbal['balance']) {
						$nretbalance = $retailer_avlbal['balance'] + $amount;
					} else {
						$nretbalance = $amount;
					}

					$current_balance = $nretbalance;
					$transaction_id = "TR" . $retailerid . time(). mt_rand();

					$value = array(
						'ret_dest_wl_admin_id'          => $retailerid,
						'user_type'                     => $usertype,
						'bank_id'                       => $bankname,
						'deposits'                      => $amount,
						'balance'                       => $nretbalance,
						'date_created'                  => date('Y-m-d H:i:s'),
						'created_by'                    => $_SESSION[_session_userid_],
						'creator_type'                  => 'admin',
						'transaction_id	'               => $transaction_id,
						'comments'                      => $comments,
						'tr_type'                       => "CR",
						'retailer_id'                   => $retailerid
					);

					$last_tran = $mysqlClass->insertData('retailer_trans', $value);

				} else {
					$errorMsg = 'Retailer Not Exist!';
				}
			} 
			
			if ($usertype == "distributor") {

				$userinfo         = $mysqlClass->mysqlQuery("SELECT `user_id` FROM `add_distributer` WHERE `user_id`='$retailerid' " )->fetch(PDO::FETCH_ASSOC);
				
				if ( $userinfo['user_id'] ) {
					$retailer_avlbal = $mysqlClass->mysqlQuery("SELECT `balance` FROM `distributor_trans` WHERE `dist_id`='$retailerid' ORDER BY `id` DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);


					if ($retailer_avlbal['balance']) {

						$nretbalance = $retailer_avlbal['balance'] + $amount;
					} else {
						$nretbalance = $amount;
					}

					$current_balance = $nretbalance;
					$transaction_id = "TR" . $retailerid . time(). mt_rand();

					$value = array(
						'dist_retail_wl_admin_id'       => $retailerid,
						'user_type'                     => $usertype,
						'bank_id'                       => $bankname,
						'deposits'                      => $amount,
						'balance'                       => $nretbalance,
						'date_created'                  => date('Y-m-d H:i:s'),
						'created_by'                    => $_SESSION[_session_userid_],
						'creator_type'                  => 'admin',
						'transaction_id	'               => $transaction_id,
						'comments'                      => $comments,
						'tr_type'                       => "CR",
						'dist_id'                       => $retailerid
					);

					$last_tran = $mysqlClass->insertData('distributor_trans', $value);
				} else {
					$errorMsg = 'Distributor Not Exist!';
				}

			} 
			
			if ($usertype == "white_label") {

				$userinfo         = $mysqlClass->mysqlQuery("SELECT `user_id` FROM `add_white_label` WHERE `user_id`='$retailerid' " )->fetch(PDO::FETCH_ASSOC);
				
				if ( $userinfo['user_id'] ) {

					$whitelbl_avlbal = $mysqlClass->mysqlQuery("SELECT `balance` FROM `wl_trans` WHERE `wluser_id`='$retailerid' ORDER BY `id` DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

					if ($whitelbl_avlbal['balance']) {
						$nwlbalance1 = $whitelbl_avlbal['balance'] + $amount;
					} else {
						$nwlbalance1 = $amount;
					}

					$current_balance = $nretbalance;
					$transaction_id = "TR" . $retailerid . time(). mt_rand();
					$value = array(
						'ret_dest_wl_admin_id'      => $retailerid,
						'bank_id'                   => $bankname,
						'user_type'                 => 'wl',
						'deposits'                  => $amount,
						'balance'                   => $nwlbalance1,
						'date_created'              => date('Y-m-d H:i:s'),
						'created_by'                => $_SESSION[_session_userid_],
						'creator_type'              => 'admin',
						'transaction_id	'           => $transaction_id,
						'comments'                  => $comments,
						'tr_type'                   => "CR",
						'wluser_id'                 => $retailerid
					);

					$last_tran = $mysqlClass->insertData('wl_trans', $value);

				} else {
					$errorMsg = 'White Label Not Exist!';
				}
			} 


			if ( $last_tran ) {

				$userClass->update_wallet_balance_add_amount($retailerid,$amount);

				$su_avlbal = $mysqlClass->mysqlQuery("SELECT `balance` FROM `admin_trans` WHERE `admin_id`='" . $_SESSION[_session_userid_] . "' ORDER BY `trans_id` DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

				$nsubalance = $su_avlbal['balance'] - $amount;

				$value = array(
					'ret_dest_wl_admin_id'          => $retailerid,
					'bank_id'                       => $bankname,
					'user_type'                     => $usertype,
					'withdrawl'                     => $amount,
					'balance'                       => $nsubalance,
					'date_created'                  => date('Y-m-d H:i:s'),
					'created_by'                    => $_SESSION[_session_userid_],
					'creator_type'                  => 'admin',
					'transaction_id	'               => $transaction_id,
					'comments'                      => $comments,
					'tr_type'                       => "DR",
					'admin_id'                      => $_SESSION[_session_userid_]
				);

				if ($mysqlClass->insertData('admin_trans', $value) > 0) {

					$retailer_details = $mysqlClass->get_field_data("mobile", "add_cust", " WHERE `id`='" . $userid . "' ");
					
					$mobile = $retailer_details['mobile'];
					$msg = "Your Net Paisa wallet has been credited by Rs. $amount. ";
					$helpers->send_msg($mobile,$msg);
					
					$closingBal = $userClass->check_user_balance($retailerid,$usertype);
					$openingBal = $userClass->check_transaction_openingBalance($retailerid,$last_tran,$usertype);
					
					$alertMsg .= "<strong>Opening Balance:</strong> ".$openingBal['balance']."<br/>";
					$alertMsg .= "<strong>Credit Amount:</strong> ".$amount."<br/>";
					$alertMsg .= "<strong>Closing Balance:</strong> ".$closingBal['balance']."<br/>";
					echo '<script>
					swal({
							  title: "",
							  text: "'.$alertMsg.'",
							  type: "success",
							  showCancelButton: false,
							  confirmButtonColor: "#DD6B55",
							  confirmButtonText: "Ok",
							  closeOnConfirm: false,
							  html: true
							},
							function(){
							  window.location = "credit-to-user.php";
							});					
					</script>';
					//echo "<script>alert('Amount Credited...')</script>";
					//echo "<script>window.location = 'credit-to-user.php';</script>";
				} else {
					echo "error" . mysqli_error($conn);
				}
			} else {
				echo $errMSG = ( $errorMsg ) ? $errorMsg : "Something went wrong!!";
				//echo "error" . mysqli_error($conn);
			}

		} else {
			echo "You have insufficient balance to transafer";
		}
	}

}
?>


<!-- MAIN PANEL -->
<div class="page-wrapper-row full-height">
	<div class="page-wrapper-middle">
		<!-- BEGIN CONTAINER -->
		<div class="page-container">
			<!-- BEGIN CONTENT -->
			<div class="page-content-wrapper">
				<!-- BEGIN CONTENT BODY -->
				<!-- BEGIN PAGE HEAD-->
				
				<!-- END PAGE HEAD-->
				<!-- BEGIN PAGE CONTENT BODY -->
				<div class="page-content">
					<div class="container">
						<!-- BEGIN PAGE BREADCRUMBS -->
						<ul class="page-breadcrumb breadcrumb">
							<li>
								<a href="index.html">Home</a>
								<i class="fa fa-circle"></i>
							</li>
							<li>
								<span>Credit to user</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Credit to user </div>
							</div>
							<div class="portlet-body">
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12">
								<?php
									if(@$msg!=""){
										echo $msg;
									}
								
								?>														
                                <div class="col-md-12 ">
									<form method ="post" id="creditForm" class="smart-form" action ="" enctype="multipart/form-data">
													
										<div class="form-group col-md-4">
											<label>User Type</label>
											<div>
												<select name="userType" class='form-control' required onchange="selectUserType(this.value)">
													<option value="">Select Type</option>
													<?php
														$utypes = $mysqlClass->fetchAllData("usertype","name,id", " where `id` not in (1,4)");
														foreach($utypes as $ut){
													?>
														<option value="<?=$ut['id']?>"><?=$ut['name']?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>User</label>
											<div>
												<select name="user" class='form-control select2' id="user" required>
													
												</select>
											</div>
										</div>
										<div class="form-group col-md-4">
											<label>Bank Name</label>
											<select name="bankname" class="form-control select2">
												<?php
												$bankList = $mysqlClass->fetchAllData("payment_option","* ", " where 1 ");
													foreach($bankList as $bank){													
												?>
													<option value="<?= $bank['id'] ?>"><?= $bank['bank_name'] ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="form-group col-md-5" >
											<label>Comment</label>
											<div>
												<input type='text' name='comments'  class='form-control' placeholder="Comment" >
											</div>
										</div>
										<div class="form-group col-md-7" >
											<label>Amount</label>
											<div>
												<input type='text' name='amount' maxlength="7" class='form-control' onkeyup="convertNumberToWords(this.value);" onblur="convertNumberToWords(this.value);" id="amount" placeholder="Amount" >
												<p id="amountinword"></p>
											</div>
										</div>
										<div class="form-group col-md-12">
											<div>
												<button type="submit" name="credit" class="btn btn-success" value="Credit">Credit</button>
											</div>
										</div>

									</form>
								</div>
								
								</div>
							</div>
							</div>
						</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include_once('inc/footer.php'); ?>
<script>
	function selectUserType(val){
if(val==2){
	var utype = "retailer";
}
if(val==3){
	var utype = "distributor";
}
if(val==5){
	var utype = "wl";
}		
		$.ajax({
            url: 'ajax/get_retailerdistributor.php',
            cache: false,
            type: 'POST',
            data: {usertype: utype, master: <?php echo $_SESSION[_session_userid_] ?>},
            success: function (response) {
                $("#user").html(response);
            }
        });
	}
</script>
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
			$("#amountinword").html(words_string);
		
	}
	
$(document).ready(function(){
    $('#creditForm').on('submit', function(e){
        e.preventDefault();
		var form = this;
		var amountinword = $("#amountinword").html();
		var amount = $("#amount").val();
		var user = $("#user option:selected").html();
		
		var textMessage = "<table class='table'> <tr><th>User:</th><td>" + user + "<td></tr> <tr><th>Amount:</th><td>Rs. " + amount + "<td></tr> <tr><th colspan='2' style='text-align:center;color:green'>" + amountinword + "</th></tr></table>";
        swal({
		  title: "Are you sure Credit ?",
		  text: textMessage,
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Credit Now",
		  closeOnConfirm: false,
		  html: true
		},
		function(){
		  form.submit(false);
		}); 
		 	
    });	
  });	

</script>

</body>
</html>
