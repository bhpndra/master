<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	include_once('classes/user_class.php'); 
	$mysqlObj = new mysql_class();
	$helper   = new helper_class();
	$userClass = new user_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	if(isset($filterBy['fByTranid'])&& $filterBy['fByTranid']!=""){
		$filter .= " and transaction_id = '".$filterBy['fByTranid']."' ";
	}
	if(isset($filterBy['fByMobile'])&& $filterBy['fByMobile']!=""){
		$filter .= " and mobile = '".$filterBy['fByMobile']."' ";
	}
	if(isset($filterBy['fByUserid'])&& $filterBy['fByUserid']!=""){
		$filter .= " and user like '%".$filterBy['fByUserid']."%' ";
	}	
	if(isset($filterBy['fByUsername'])&& $filterBy['fByUsername']!=""){
		$filter .= " and name like '%".$filterBy['fByUsername']."%' ";
	}
	
	if($filterBy['dateFrom']=="" && $filterBy['dateTo']==""){
		$date1 	  = new DateTime('7 days ago');
		$dateFrom = $date1->format('Y-m-d');
		$dateTo   = date("Y-m-d");
		$filter  .= " and DATE(`date_created`) BETWEEN '$dateFrom' AND '$dateTo' ";
	} else{
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
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="retailer-wallet-details.php" class="btn btn-default">Reset</a>
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
																	<th scope="col">Transaction Id</th>
																	<th scope="col">Transaction Type</th>
																	<th scope="col">Opening</th>
																	<th scope="col">Withdrawl</th>
																	<th scope="col">Balance</th>
																	<th scope="col">Difference</th>
																	<th scope="col">Date & Time</th>
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

$createSql = "CREATE TEMPORARY TABLE retailsTrans (SELECT 
	id,
	transaction_id,
	(SELECT balance FROM `retailer_trans` WHERE id < a.id and retailer_id = a.retailer_id order by id DESC limit 1) as openingBalance,
	withdrawl,
	balance,
	balance - ((SELECT balance FROM `retailer_trans` WHERE id < a.id and retailer_id = a.retailer_id order by id DESC limit 1) - withdrawl) as diff,
	date_created,
	retailer_id
	
FROM 
	`retailer_trans` as a
WHERE `date_created` BETWEEN '2019-05-24 00:00:00.000000' AND '2019-05-25 00:00:00.000000' and withdrawl > 0); 
";	
$mysqlObj->mysqlQuery($createSql);

$sql = " SELECT * from retailsTrans where 1 ";
$pagiConfig['total_rows'] = $mysqlObj->countRows($sql);

$pagination = $paginate->pagination($pagiConfig);

$sqlQuery1 = $mysqlObj->mysqlQuery($sql.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);	
			
 	$i = $pagination['offset'] + 1;
				while($rows = $sqlQuery1->fetch(PDO::FETCH_ASSOC)){ 
				

				
			?>
									<tr>
										<td><?php echo $rows["transaction_id"]; ?></td>
										<td><?php echo $rows["tr_type"]; ?></td>
										<td><?php echo $rows["openingBalance"]; ?></td>
										<td><?php echo $rows["withdrawl"]; ?></td>
										<td><?php echo $rows["balance"]; ?></td>
										<td><?php echo $rows["diff"]; ?></td>
										<td><?php //echo round($diffOWC, 2);  ?></td>
										<td><?php echo $rows["date_created"]; ?></td>																		
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