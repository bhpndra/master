<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	include_once('classes/user_class.php'); 
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	$userClass = new user_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	if(isset($filterBy['fByTranid'])&& $filterBy['fByTranid']!=""){
		$filter .= " and transaction_id like '%".$filterBy['fByTranid']."%' ";
	}
	if(isset($filterBy['fBytransType'])&& $filterBy['fBytransType']!=""){
		$filter .= " and tr_type = '".$filterBy['fBytransType']."' ";
	}
	if($filterBy['dateFrom']=="" && $filterBy['dateTo']==""){
		$date1 = new DateTime('30 days ago');
		$dateFrom = $date1->format('Y-m-d');
		$dateTo = date("Y-m-d");
		$filter .= " and DATE(`date_created`) BETWEEN '$dateFrom' AND '$dateTo' ";
	} else {
		$filter .= " and DATE(`date_created`) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
		$dateFrom = $filterBy['dateFrom'];
		$dateTo = $filterBy['dateTo'];
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
                                        <span>User Transactions</span>
                                    </li>
                                </ul>

                                <!-- END PAGE BREADCRUMBS -->
								<div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-user"></i>User Details</div>
                                                </div>
												<div class="portlet-body">
												<div class="row">
<?php
$sql = "SELECT id, name, user, mobile, cname, aeps_balance,
		CASE WHEN id in (SELECT `add_distributer`.user_id FROM `add_distributer` where `add_distributer`.user_id = `add_cust`.id) THEN 'Distributer'
		WHEN id in (SELECT `add_retailer`.user_id FROM `add_retailer` where `add_retailer`.user_id = `add_cust`.id) THEN 'Retailer'
		WHEN id in (SELECT `add_white_label`.user_id FROM `add_white_label` where `add_white_label`.user_id = `add_cust`.id) THEN 'White Label'
		else '<span style=\'color:red\'>not defind</span>'
		end as userType		
		FROM `add_cust` where id=".$filterBy['uid'];
		$userDetails = $mysqlObj->mysqlQuery($sql)->fetch(PDO::FETCH_ASSOC);

?>
													<table class="table">
														<tr>
															<th scope="col" width="150px;">Name</th>
															<th scope="col" width="150px;">Username</th>
															<th scope="col" width="150px;">Mobile</th>
															<th scope="col" width="150px;">Role</th>
															<th scope="col" width="150px;">Company</th>
															<th scope="col" width="150px;">Balance</th>
														</tr>
														<tr>
															<td width="150px;"> <?= $userDetails['name']; ?> </td>
															<td width="150px;" class="topup_trid"><?= $userDetails['user']; ?> </td>
															<td width="150px;" class="mobile"> <?= $userDetails['mobile']; ?></td>
															<td width="150px;" class="amount"> <?= $userDetails['userType']; ?></td>
															<td width="150px;" class="amount"> <?= $userDetails['cname']; ?></td>
															<td width="150px;" class="amount" onclick="getBalance(this,'<?= $userDetails['id']; ?>')"> <a href="javascript:void(0)">Get Balance</a></td>
														</tr>
													</table>
												</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
								<div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>AEPS Details</div>
                                                </div>
												<div class="portlet-body">
												
												<div class="row">
													<table class="table">
														<tr>
															<th scope="col" width="150px;">Total AEPS</th>
															<th scope="col" width="150px;">Total Withdrawl</th>
															<th scope="col" width="150px;">Total Bank Withdrawl</th>
															<th scope="col" width="150px;">Total Wallet Withdrawl</th>
															<th scope="col" width="150px;">Pending Settlements</th>
															<th scope="col" width="150px;">Current AEPS Balance</th>
														</tr>
														<tr>
														<?php														
															$totalAeps = $mysqlObj->mysqlQuery("SELECT sum(amount) as amt  FROM `aeps_info` WHERE `user_id` = '".$filterBy['uid']."' and (txntype = 'Credit' or txntype = 'WAP' or txntype = 'CW') and status = 'SUCCESS' and amount > 0")->fetch(PDO::FETCH_ASSOC);
														?>
															<td width="150px;"> <?= $totalAeps['amt']; ?> </td>
														<?php														
															$totalWAeps = $mysqlObj->mysqlQuery("SELECT sum(amount) as amt  FROM `aeps_withdrawl_info` WHERE `user_id` = '".$filterBy['uid']."' and withdrawl_type in ('BANK','WALLET')")->fetch(PDO::FETCH_ASSOC);
														?>
															<td width="150px;" class="topup_trid"><?= $totalWAeps['amt']; ?> </td>
														<?php														
															$totalBWAeps = $mysqlObj->mysqlQuery("SELECT sum(amount) as amt  FROM `aeps_withdrawl_info` WHERE `user_id` = '".$filterBy['uid']."' and withdrawl_type = 'BANK'")->fetch(PDO::FETCH_ASSOC);
														?>
															<td width="150px;" class="topup_trid"><?= $totalBWAeps['amt']; ?> </td>
														<?php														
															$totalWWAeps = $mysqlObj->mysqlQuery("SELECT sum(amount) as amt  FROM `aeps_withdrawl_info` WHERE `user_id` = '".$filterBy['uid']."' and withdrawl_type = 'WALLET'")->fetch(PDO::FETCH_ASSOC);
														?>														
															<td width="150px;" class="topup_trid"><?= $totalWWAeps['amt']; ?> </td>
														<?php														
															$totalPAeps = $mysqlObj->mysqlQuery("SELECT sum(amount) as amt FROM `aeps_info` WHERE `user_id` = '".$filterBy['uid']."' and status = 'SUCCESS' and amount > 1 and settlement = 'PENDING'")->fetch(PDO::FETCH_ASSOC);
														?>
															<td width="150px;" class="amount"> <?= $totalPAeps['amt']; ?></td>
															<td width="150px;" class="amount"> <?= $userDetails['aeps_balance']; ?></td>
														</tr>
													</table>
												</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
								 
								
                                <!-- BEGIN PAGE CONTENT INNER -->

							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php include_once('inc/footer.php'); ?>
	<script src="new-js/easy-autocomplete/jquery.easy-autocomplete.min.js" type="text/javascript"></script>


</body>
</html>
</html>