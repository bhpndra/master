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
$sql = "SELECT id, name, user, mobile, cname,
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
                                                        <i class="fa fa-cogs"></i>Filter</div>
                                                </div>
												<div class="portlet-body">
												<div class="row">
													<form method="get" >
													<input type="hidden" value="<?=$_GET['uid']?>" name="uid" />
															<div class="form-group col-md-3">
																<label>Transaction Id</label>
																<div>
																	<input type='text' id="fByTranid" name='fByTranid' value="<?php echo @$filterBy['fByTranid']; ?>"  class='form-control' placeholder="Transaction Id" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Transaction Type</label>
												<?php
													if($filterBy['fBytransType']!=""){
														$selected = $filterBy['fBytransType'];
														$$selected = "selected";
														//echo @$PENDING;
													}
												?>
																<div>
																	<select class='form-control' name="fBytransType">
																		<option value="">All</option>
																<?php
																	$trType = $mysqlObj->mysqlQuery("SELECT DISTINCT tr_type FROM `retailer_trans` where tr_type != '' and tr_type != 'AEPS WITHDRAWL' 
ORDER BY `retailer_trans`.`tr_type` ASC");
																	while($rows = $trType->fetch(PDO::FETCH_ASSOC)){
																?>
																		<option <?=@$$rows['tr_type']?> value="<?=$rows['tr_type']?>"><?=$rows['tr_type']?></option>
																<?php
																	}
																?>
																	</select>
																</div>
															</div>
															
															<div class="form-group col-md-2">
																<label>From</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" value="<?=$dateFrom?>" class="form-control" placeholder="" name="dateFrom" id="dateFrom">
																</div>
															</div>
															
															<div class="form-group col-md-2">
																<label>To</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" value="<?=$dateTo?>" class="form-control" placeholder="" name="dateTo" id="dateTo">
																</div>
															</div>
															
															<div class="form-group col-md-2">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="view-all-retailer-transactions.php?uid=<?=$_GET['uid']?>" class="btn btn-default">Reset</a>
																</div>
															</div>
														</form>
												</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
								 
								
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>User Transactions</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:20px" scope="col">S.No</th>
																	<th scope="col">Transaction</th>
																	<th>Opening</th>
																	<th>Diposits</th>
																	<th>Withdrawl</th>
																	<th>Closing</th>
																	<th scope="col">Msg</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
<?php
// for Pagination
include_once('classes/pagination.php'); 
$paginate = new pagin();
$pagiConfig = array();
$pagiConfig['current_page'] = $_GET['page'];
$pagiConfig['per_page_items'] = 10;
unset($_GET['page']);
$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?".http_build_query($_GET);
$orderBy = " order by `id` DESC";
$sql = "SELECT * FROM `retailer_trans` where 1 and tr_type != 'AEPS WITHDRAWL' and `retailer_id`='".$filterBy['uid']."'  ";	
	
	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
 $i = $pagination['offset'] + 1;
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
			?>
									<tr>
										<td class="sorting_1"><?php echo $i; ?></td>
										<td class=" expand">
											<strong>Id:</strong> <?php echo $rows["transaction_id"]; ?><br/>
											<strong>Date:</strong> <?php echo $rows["date_created"]; ?><br/>
											<strong>Type:</strong> <?php echo $rows["tr_type"]; ?><br/>
										</td>
										<?php
											$opbl = $userClass->check_transaction_openingBalance($rows['retailer_id'],$rows['id'],"retailer");
											
										?>
										<td><?php echo $opbl["balance"]; ?></td>
										<td><?php echo $rows["deposits"]; ?></td>
										<td>
											<?php echo $rows["withdrawl"]; ?>
										</td>
										<td><?php echo $rows["balance"]; ?></td>

										<td><?php echo $rows['comments'];	?></td>

										<?php $i++; } ?>
										
									</tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                
												<?php echo $pagination['pagination']; ?>
												</div>
                                            </div>
                                        </div><?php echo implode(";",$ret); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php include_once('inc/footer.php'); ?>
	<script src="new-js/easy-autocomplete/jquery.easy-autocomplete.min.js" type="text/javascript"></script>
<script>
		function getBalance(e,id){
			$(e).html('Get Balance <i class="fa fa-refresh fa-spin" style=""></i>');
			$.ajax({
				type: "POST",
				cache: false,
				url: "ajax/get_user_balance.php",
				data: {'uid':id,},
				success: function (response) {
					$(e).html(response+" <a href='javascript:void(0)'><i class='fa fa-refresh'></i></a>");					
				}
			});			
		}
		function getLast5Transaction(uid){
			 
			$.ajax({
				type: "POST",
				cache: false,
				url: "ajax/get_last_5_transaction.php",
				data: {'uid':uid,},
				dataType:"json",
				success: function (response) { 
					if(response){
						var len = response.length;
						var trans = "<div class='table-responsive'><table class='table table-bordered'>";
						trans += "<tr><th>Date</th><th>Transaction Id</th><th>Balance</th><th>Withdrawl</th><th>Deposits</th></tr>";
						if(len > 0){
							for(var i=0;i<len;i++){								
								trans += "<tr><td>"+response[i].date_created+"</td><td>"+response[i].transaction_id+"</td><td>"+response[i].balance+"</td><td>"+response[i].withdrawl+"</td><td>"+response[i].deposits+"</td></tr>";
							}
							trans += "</table></div>";
							if(trans != ""){
								swal({
								  title: "Last 5 Transactions",
								  text: trans,
								  html: true
								});
							}
						}
					}
					
				}
			});			
		}
</script>

</body>
</html>
</html>