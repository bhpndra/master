<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	
	include("classes/user_class.php");
	$userClass = new user_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	if(isset($filterBy['fByTranid'])&& $filterBy['fByTranid']!=""){
		$filter .= " and a.transaction_id like '%".$filterBy['fByTranid']."%' ";
	}		
	if(isset($filterBy['fByUsername'])&& $filterBy['fByUsername']!=""){
		$filter .= " and b.name like '%".$filterBy['fByUsername']."%' ";
	}
	if(isset($filterBy['fByUserid'])&& $filterBy['fByUserid']!=""){
		$filter .= " and b.user like '%".$filterBy['fByUserid']."%' ";
	}
	if(isset($filterBy['fByMobile'])&& $filterBy['fByMobile']!=""){
		$filter .= " and b.mobile like '%".$filterBy['fByMobile']."%' ";
	}
	if(isset($filterBy['fBycname'])&& $filterBy['fBycname']!=""){
		$filter .= " and b.cname like '%".$filterBy['fBycname']."%' ";
	}
	if($filterBy['dateFrom']=="" && $filterBy['dateTo']==""){
		$date1 = new DateTime('7 days ago');
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
                                        <span>All Fund Transfer Statement</span>
                                    </li>
                                </ul>

                                <!-- END PAGE BREADCRUMBS -->
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
															<div class="form-group col-md-3">
																<label>Transaction Id</label>
																<div>
																	<input type='text' id="fByTranid" name='fByTranid' value="<?php echo @$filterBy['fByTranid']; ?>"  class='form-control' placeholder="Transaction Id" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Name</label>
																<div>
																	<input type='text' id="fByUsername" name='fByUsername' value="<?php echo @$filterBy['fByUsername']; ?>"  class='form-control' placeholder="Name" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Username</label>
																<div>
																	<input type='text' id="fByUserid" name='fByUserid' value="<?php echo @$filterBy['fByUserid']; ?>"  class='form-control' placeholder="Username" >
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label>Mobile Number</label>
																<div>
																	<input type='text' name='fByMobile' value="<?php echo @$filterBy['fByMobile']; ?>"  class='form-control' placeholder="Mobile Number" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Company Name</label>
																<div>
																	<input type='text' id="fBycname" name='fBycname' value="<?php echo @$filterBy['fBycname']; ?>"  class='form-control' placeholder="Company Name" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>From</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" value="<?=$dateFrom?>" class="form-control" placeholder="" name="dateFrom" id="dateFrom">
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label>To</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" value="<?=$dateTo?>" class="form-control" placeholder="" name="dateTo" id="dateTo">
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="distributor-fund-transfer-report.php" class="btn btn-default">Reset</a>
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
                                                        <i class="fa fa-cogs"></i>All Fund Transfer Statement</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" width="30px;">#</th>
                                                                    <th scope="col" width="150px;">Transaction Details</th>
                                                                    <th scope="col" width="180px;">Distributor</th>
                                                                    <th scope="col" width="180px;">Receiver</th>
                                                                    <th scope="col" width="95px;">Withdrawl</th>
                                                                    <th scope="col" width="95px;">Amount</th>
                                                                    <th scope="col" width="150px;">Remark</th>
                                                                    <th scope="col" width="150px;">Retailer List</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
<?php
// for Pagination
include_once('classes/pagination.php'); 
$paginate = new pagin();

$pagiConfig = array();
$pagiConfig['current_page'] = $_GET['page'];
$pagiConfig['per_page_items'] = 50;
unset($_GET['page']);
$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?".http_build_query($_GET);

$orderBy =" ORDER BY a.`id` DESC";
$sql = "SELECT 
			a.dist_retail_wl_admin_id,
			a.transaction_id,
			a.date_created,
			a.withdrawl,
			a.balance,
			a.comments,
			b.id as dis_id,
			b.name,
			b.cname,
			b.user,
			b.mobile,
			c.id as rec_id,
			c.name as rec_name,
			c.cname as rec_cname,
			c.user as rec_user,
			c.mobile as rec_mobile
		FROM `distributor_trans` as a, `add_cust` as b, `add_cust` as c WHERE `tr_type`='DR' and c.id = a.dist_retail_wl_admin_id and b.id = a.dist_id ";	

	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
			
 $i = $pagination['offset'] + 1;
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
			?>
									<tr>
										<td width="30px;"> <?= $i; ?> </td>
										<td><strong>ID:</strong> <?= $rows['transaction_id']; ?><br/>
											<strong>Date: </strong> <?= $rows['date_created']; ?>
										</td>
										<td>
											<strong>Name:</strong> <?= $rows['name']; ?><br/>
											<strong>Mobile:</strong> <?= $rows['mobile']; ?><br/>
											<strong>Username:</strong> <?= $rows['user']; ?><br/>
											<strong>Shop Name:</strong> <?= $rows['cname']; ?><br/>
											<strong>User Id:</strong> <?= $rows['dis_id']; ?>
										</td>
										<td>
											<strong>Name:</strong> <?= $rows['rec_name']; ?><br/>
											<strong>Mobile:</strong> <?= $rows['rec_cname']; ?><br/>
											<strong>Username:</strong> <?= $rows['rec_user']; ?><br/>
											<strong>Shop Name:</strong> <?= $rows['rec_mobile']; ?><br/>
											<strong>User Id:</strong> <?= $rows['rec_id']; ?>
										</td>
										<td> <?= $rows['withdrawl']; ?></td>
										<td> <?= $rows['balance']; ?></td>
										<td> <?= $rows['comments']; ?></td>
										<td>
											<?php
												/*if($user['userType']=="distributer" || $user['userType']=="white_label"){
													echo "<a href='view-child-user.php?uid=".$rows['dist_retail_wl_admin_id']."' target='_blank'> View Child Users </a>";
												}*/
											?>
<?php if($user['userType']=="retailer"){ ?>
	<a style="color: #484a47;" href="view-all-retailer-transactions.php?uid=<?=$rows['dist_retail_wl_admin_id']?>" target="_blank">All Transactions</a>
<?php } ?>
<?php if($user['userType']=="distributer"){ ?>
	<a style="color: #484a47;" href="view-all-distributer-transactions.php?uid=<?=$rows['dist_retail_wl_admin_id']?>" target="_blank">All Transactions</a><br/>
	<a style="color: #347d01;" href="view-all-distributer-transactions.php?uid=<?=$rows['dist_retail_wl_admin_id']?>&fBytransType=CR" target="_blank">All Credit</a><br/>
	<a style="color: #7d1f01;" href="view-all-distributer-transactions.php?uid=<?=$rows['dist_retail_wl_admin_id']?>&fBytransType=DR" target="_blank">All Debit</a><br/>
	<a href='view-child-user.php?uid=<?=$rows['dist_retail_wl_admin_id']?>' target='_blank'> View Child Users </a>
<?php } ?>
<?php if($user['userType']=="white_label"){ ?>
	<a style="color: #484a47;" href="view-all-whitelabel-transactions.php?uid=<?=$rows['dist_retail_wl_admin_id']?>" target="_blank">All Transactions</a><br/>
	<a style="color: #347d01;" href="view-all-whitelabel-transactions.php?uid=<?=$rows['dist_retail_wl_admin_id']?>&fBytransType=CR" target="_blank">All Credit</a><br/>
	<a style="color: #7d1f01;" href="view-all-whitelabel-transactions.php?uid=<?=$rows['dist_retail_wl_admin_id']?>&fBytransType=DR" target="_blank">All Debit</a><br/>
	<a href='view-child-user.php?uid=<?=$rows['dist_retail_wl_admin_id']?>' target='_blank'> View Child Users </a>
<?php } ?>
										</td>
										
										<?php $i++; } ?>
									</tr>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                
												<?php echo $pagination['pagination']; ?>
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
	<script src="new-js/easy-autocomplete/jquery.easy-autocomplete.min.js" type="text/javascript"></script>
<script>
	$(document).ready(function(){
			//   fByUserid *************************************************
			var options = {
			url: function(phrase) {
				return "ajax/easy-autocomplete.php";
			},
			getValue: function(element) { //console.log(JSON.stringify(element))
				return element.name;				
			},
			ajaxSettings: {
				dataType: "json",
				method: "POST",
				data: {
					dataType: "json"
				}
			},
			preparePostData: function(data) { 
				data.searchString = $("#fByUsername").val();  
				data.filterOn = 'fByUsername';  //alert(JSON.stringify(data));
				return data;
			},
			requestDelay: 400
			};
			$("#fByUsername").easyAutocomplete(options);
			
			//   fByUserid ***********************************************
			var optionsForUserid = {
			url: function(phrase) {
				return "ajax/easy-autocomplete.php";
			},
			getValue: function(element) { 
				return element.user;				
			},
			ajaxSettings: {
				dataType: "json",
				method: "POST",
				data: {
					dataType: "json"
				}
			},
			preparePostData: function(data) { 
				data.searchString = $("#fByUserid").val();  
				data.filterOn = 'fByUserid';
				return data;
			},
			requestDelay: 400
			};
			$("#fByUserid").easyAutocomplete(optionsForUserid);	
			
			//   fBycname ***********************************************
			var optionsForCname = {
			url: function(phrase) {
				return "ajax/easy-autocomplete.php";
			},
			getValue: function(element) { 
				return element.cname;				
			},
			ajaxSettings: {
				dataType: "json",
				method: "POST",
				data: {
					dataType: "json"
				}
			},
			preparePostData: function(data) { 
				data.searchString = $("#fBycname").val();  
				data.filterOn = 'fBycname';
				return data;
			},
			requestDelay: 400
			};
			$("#fBycname").easyAutocomplete(optionsForCname);	
			
			
		});
</script>
</body>
</html>
</html>