<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	include_once('classes/user_class.php'); 
	$mysqlObj = new mysql_class();
	$helper   = new helper_class();
	$userClass = new user_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			

	
	$date1 	  = new DateTime('1 days ago');
	$dateTo = $date1->format('Y-m-d');	
	if(isset($filterBy['dateTo']) && $filterBy['dateTo']!=""){
		$dateTo   = $filterBy['dateTo'];
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
                                        <span>All Payments</span>
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
																<label>Date</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" value="<?=$dateTo?>" class="form-control" placeholder="" name="dateTo" id="dateTo">
																</div>
															</div>
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="retailer-daily-report.php" class="btn btn-default">Reset</a>
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
                                                       <i class="fa fa-cogs"></i>All Payments
                                                   </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
																	<th scope="col">Current Date Opening</th>
																	<th scope="col">Withdrawl</th>
																	<th scope="col">Total Credit</th>
																	<th scope="col">Commission</th>
																	<th scope="col">Current Balance</th>
																	<th scope="col">User Details</th>
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
$orderBy = " order by rt.`date_created` DESC";
$sql = "SELECT
			ac.`id`,
			ac.`status`,
			ac.`name`,
			ac.`user`,
			ac.`mobile`,
			ac.`cname`,
			rt.balance as currentBalance,
			rt.id as transRowId
		FROM `add_cust` as ac 
		INNER JOIN `retailer_trans` as rt ON rt.retailer_id = ac.id 
		WHERE 
			ac.`id` IN (SELECT `user_id` FROM `add_retailer`)
			AND rt.id IN (SELECT max(rtr.id) FROM `retailer_trans` rtr WHERE DATE(rtr.date_created)<='$dateTo' group by rtr.retailer_id)
		";	
	
//echo $sql.$filter.$orderBy;

	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);						
 	$i = $pagination['offset'] + 1;
		while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
					
		$todayOpening = $mysqlObj->mysqlQuery("SELECT balance FROM `retailer_trans` where retailer_id = '".$rows["id"]."' and date(`date_created`) < '$dateTo' ORDER BY `id`  DESC LIMIT 1 ")->fetch(PDO::FETCH_ASSOC);
			
		$totalUses = $mysqlObj->mysqlQuery("SELECT SUM(withdrawl) as used FROM `retailer_trans` WHERE `transaction_id` NOT IN (SELECT `refund_id` FROM `retailer_trans` rt WHERE rt.`refund_id`!='') and tr_type!='DR' and tr_type!='CR' and date(`date_created`) = '$dateTo' and retailer_id='".$rows["id"]."' ")->fetch(PDO::FETCH_ASSOC);
			
		$totalDeposit = $mysqlObj->mysqlQuery("SELECT SUM(`deposits`) as credit FROM `retailer_trans` WHERE (tr_type='CR' or tr_type like '%AEPS Balance Settlement!%') and date(`date_created`) = '$dateTo' and `transaction_id` NOT IN (SELECT `refund_id` FROM `retailer_trans` WHERE `refund_id`!='') and retailer_id='".$rows["id"]."' ")->fetch(PDO::FETCH_ASSOC);
		
		$totalCommission = $mysqlObj->mysqlQuery("SELECT sum(earn_comm) as commission  FROM `retailer_commission` WHERE `retailer_id` = '".$rows["id"]."' and date(created_on) = '$dateTo' ")->fetch(PDO::FETCH_ASSOC);
				
?>
									<tr>
										<td><?php echo $todayOpening['balance']; ?></td>
										<td><?php echo $totalUses["used"]; ?></td>
										<td><?php echo empty($totalDeposit["credit"]) ? 0.00 : $totalDeposit["credit"]; ?></td>
										<td><?php echo $totalCommission["commission"]; ?></td>
										<td><?php echo $rows["currentBalance"]; ?></td>
										<td>
											<strong>ID: 	 </strong><?php echo $rows["id"]; ?><br/>
											<strong>Name: 	 </strong><?php echo $rows["name"]; ?><br/>
											<strong>User id: </strong><?php echo $rows["user"]; ?><br/>
											<strong>Mobile:  </strong><?php echo $rows["mobile"]; ?><br/>
											<strong>Shop Name: </strong><?php echo $rows["cname"]; ?><br/>
										</td>																			
									</tr>
				<?php
						}
				?>
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
			//   fByUsername *************************************************
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