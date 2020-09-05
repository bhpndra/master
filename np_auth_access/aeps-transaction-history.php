<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	include_once('classes/user_class.php'); 
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	$userClass = new user_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	if(isset($filterBy['fByUsername'])&& $filterBy['fByUsername']!=""){
		$filter .= " and u.name like '%".$filterBy['fByUsername']."%' ";
	}
	
	if(isset($filterBy['fByMobile'])&& $filterBy['fByMobile']!=""){
		$filter .= " and u.mobile like '%".$filterBy['fByMobile']."%' ";
	}	
	if(isset($filterBy['fByStatus'])&& $filterBy['fByStatus']!=""){
		$filter .= " and ai.status ='".$filterBy['fByStatus']."' ";
	} 	
	if(isset($filterBy['groupid'])&& $filterBy['groupid']!=""){
		$filter .= " and ai.group_id ='".$filterBy['groupid']."' ";
	} 
	if(@$filterBy['dateFrom']=="" && @$filterBy['dateTo']==""){
		$date1 = new DateTime('30 days ago');
		$dateFrom = $date1->format('Y-m-d');
		$dateTo = date("Y-m-d");
		$filter .= " and DATE(ai.`date_created`) BETWEEN '$dateFrom' AND '$dateTo' ";
	} else {
		$filter .= " and DATE(ai.`date_created`) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
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
                                        <span>All AEPS Transaction</span>
                                    </li>
                                </ul>
		
								<div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>Advance Filter</div>
                                                </div>
												<div class="portlet-body">
												<div class="row">
													<form method="get" >
															<div class="form-group col-md-3">
																<label>Name</label>
																<div>
																	<input type='text' id="fByUsername" name='fByUsername' value="<?php echo @$filterBy['fByUsername']; ?>"  class='form-control' placeholder="Name" >
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
																	<input type="date" value="<?=@$dateFrom?>" class="form-control" placeholder="" name="dateFrom" id="dateFrom">
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label>To</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" value="<?=@$dateTo?>" class="form-control" placeholder="" name="dateTo" id="dateTo">
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label>Group Id</label>
																<div>
																	<input type="text" value="<?php echo @$filterBy['groupid']; ?>" class="form-control" placeholder="" name="groupid" >
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label>Status</label>
																<div>
																	<select class='form-control' name='fByStatus' >
												<?php
													if($filterBy['fByStatus']!=""){
														$selected = $filterBy['fByStatus'];
														$$selected = "selected";
													}
												?>						
																		<option value="">All</option>
																		<option <?=@$PENDING?> value="PENDING">Pending</option>
																		<option <?=@$SUCCESS?> value="SUCCESS">Success</option>
																		<!--<option value="REJECT">Reject</option>-->
																	</select>
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="aeps-transaction-history.php" class="btn btn-default">Reset</a>
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
                                                        <i class="fa fa-cogs"></i>All AEPS Transaction</div>
                                                </div>
                                                <div class="portlet-body">
												<?php if(isset($filterBy['groupid'])&& $filterBy['groupid']!=""){ ?>
												<h4><strong>Total Amount: </strong> <?php echo $totalSettlement['totalSettlement']; ?> 
													<a style="float:right" target="_blank" href="export/aeps-transaction-base-groupid.php?groupid=<?=$filterBy['groupid']?>"><strong>Export</strong></a>
												</h4>
												<?php } ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">S.No</th>
																	<th scope="col">Site Admin Details</th>
																	<th scope="col">Retailer Details</th>
																	<th scope="col">Transaction</th>
																	<th scope="col">Status</th>
																	<th scope="col">Settlement</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
<?php
// for Pagination
include_once('classes/pagination.php'); 
$paginate = new pagin();
$pagiConfig = array();
$pagiConfig['current_page'] = !empty($_GET['page']) ? $_GET['page'] : 1;
$pagiConfig['per_page_items'] = 25;
unset($_GET['page']);
$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?".http_build_query($_GET);
$orderBy = " ORDER BY ai.`id` DESC ";

$sqlC = "select ai.id from aeps_info as ai left join add_cust as a on a.id = ai.user_id left join add_cust as b on b.id = ai.wl_id where a.admin_id = '".$_SESSION[_session_userid_]."'";		
$sql = "select a.name as ret_name,a.mobile as ret_mobile,a.user as ret_userId,b.name as wl_name,b.mobile as wl_mobile,b.user as wl_userId, ai.* from aeps_info as ai left join add_cust as a on a.id = ai.user_id left join add_cust as b on b.id = ai.wl_id where a.admin_id = '".$_SESSION[_session_userid_]."'";		
	
	$pagiConfig['total_rows'] = $mysqlObj->countRows($sqlC.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);	
$i=1;

				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
			?>
									<tr>
										<td class="sorting_1"><?php echo $i; ?></td>
										<td>
											<strong>Name : </strong><?php echo $rows["wl_name"]; ?><br/>
											<strong>Mobile : </strong><?php echo $rows["wl_mobile"]; ?><br/>
											<strong>User.ID : </strong><?php echo $rows["wl_userId"]; ?>
										
										</td>
										<td>
											<strong>Name : </strong><?php echo $rows["ret_name"]; ?><br/>
											<strong>Mobile : </strong><?php echo $rows["ret_mobile"]; ?><br/>
											<strong>User.ID : </strong><?php echo $rows["ret_userId"]; ?>
										
										</td>
										<td>
											<strong>Tran.ID: </strong><?php echo $rows["transaction_id"]; ?><br/>
											<strong>Amount: </strong><?php echo $rows["amount"]; ?><br/>
											<strong>Date: </strong><?php echo $rows["date_created"]; ?>
										</td>
										<td><?php echo $rows['status']; ?></td>
										<td>
											<span style="color:red"><strong>Retailer:</strong> <?php echo $rows['settlement_retailer']; ?><br/></span>
											<span style="color:green"><strong>White Label:</strong> <?php echo $rows['settlement_wl']; ?></span>
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

			
			
		});
		function updateStatus(e){
			if(confirm('Are You sure want update ?')){
				var id = $(e).attr("data-rowId");
				var sts = $(e).val();
				$.ajax({
					type: 'POST',
					data: {status:sts, row:id, action:"updateWithdrawlStatus" },
					cache: false,
					url: 'ajax/aeps_registration_ajax.php',
					success: function (response)
					{ 
						alert(response);
					}
				});
				
			}
		}
</script>

</body>
</html>
</html>