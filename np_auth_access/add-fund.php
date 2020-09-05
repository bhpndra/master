<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	if(isset($_POST['submit']) && !empty($_POST['amount'])){
		
		$post = $helpers->clearSlashes($_POST);
		$stmt = $mysqlClass->mysqlQuery("SELECT balance FROM `admin` where `id` = '".$_SESSION[_session_userid_]."'");
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
						
		$currentBal = $row['balance'];
		$new_balance = $currentBal + $post['amount'];
		
		$transId = $helpers->transaction_id_generator("FA",8);
		$data =  array(
			"deposits" => $post['amount'],
			"withdrawl" => 0,
			"opening_balance" => $currentBal,
			"balance" => $new_balance,
			"user_type" => 'ADMIN',
			"creator_type" => $_SESSION[_session_usertype_],
			"wl_id" => 0,
			"transaction_id" => $transId,
			"agent_trid " => $transId,
			"created_by" =>  $_SESSION[_session_userid_],
			"comments" => 'Fund Added',
			"tr_type" => 'CR',
			"admin_id" => $_SESSION[_session_userid_]
		);
		if($mysqlClass->insertData("admin_trans", $data)>0){
			$mysqlClass->mysqlQuery("update `admin` set balance = $new_balance where id = '".$_SESSION[_session_userid_]."' ");
			echo "<script> alert('Amount (Rs. ".$post['amount'].") added successfully'); window.location = 'add-fund.php'; </script>";
		}		
	}

?>
        <div class="page-wrapper-row full-height">
            <div class="page-wrapper-middle">
                <!-- BEGIN CONTAINER -->
                <div class="page-container">
                    <!-- BEGIN CONTENT -->
                    <div class="page-content-wrapper">
                        <!-- BEGIN CONTENT BODY -->
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
                                        <span>Dashboard</span>
                                    </li>
                                </ul>
                                <!-- END PAGE BREADCRUMBS -->
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="portlet box blue">
											<div class="portlet-title">
												<div class="caption">
													<i class="fa fa-cogs"></i> Add Fund </div>
											</div>
											<div class="portlet-body">
												<div class="well">
													<div class="row show-grid">
														<div id="datatable_col_reorder_filter" class="dataTables_filter">
															<form role="form" method='post'>
<?php
$stmt = $mysqlClass->mysqlQuery("SELECT balance FROM `admin` where `id`  = '".$_SESSION[_session_userid_]."' ");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
				
$currentBal = $row['balance'];
		
?>
																<div class="form-group col-md-3">
																	<label>Current Balance</label>
																	<div>
																		<input type='text' value="<?php echo empty($currentBal)? 0 : $currentBal; ?>"  class='form-control' placeholder="Current Balance" readonly>
																	</div>
																</div>

																<div class="form-group col-md-3">
																	<label>Add amount</label>
																	<div>
																		<input type='text' name='amount' class='form-control' placeholder="Amount" required>
																	</div>
																</div>
																<!--<div class="form-group col-md-3">
																	<label>PIN</label>
																	<div>
																		<input type='password' name='pin'  class='form-control' placeholder="PIN" required>
																	</div>
																</div>-->
																<div class="form-group col-md-3">
																	<label style="opacity: 0;"> submit</label>
																	<div>
																		<input type="submit" id="save" name="submit"  class="btn btn-primary" value="Add">
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
            </div>
        </div>
<?php include_once('inc/footer.php'); ?>
</body>
</html>