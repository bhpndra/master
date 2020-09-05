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
		$filter .= " and awi.status ='".$filterBy['fByStatus']."' ";
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
                                        <span>All AEPS withdrawl</span>
                                    </li>
                                </ul>

                                <!-- END PAGE BREADCRUMBS -->
								
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
																		<option <?=@$APPROVED?> value="APPROVED">Approve</option>
																		<!--<option value="REJECT">Reject</option>-->
																	</select>
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="aeps-bank-withdrawl.php" class="btn btn-default">Reset</a>
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
                                                        <i class="fa fa-cogs"></i>All AEPS withdrawl</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">S.No</th>
																	<th scope="col">User Details</th>
																	<th scope="col">Bank Details</th>
																	<th scope="col">Group Id</th>
																	<th scope="col">Transaction Id</th>
																	<th scope="col">Amount</th>
																	<th scope="col">Date</th>
																	<th scope="col">Status</th>
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
$orderBy = " ORDER BY awi.`id` DESC ";
$sql = "SELECT 
		awi.bank_name,
		awi.ifsc,
		awi.account_name,
		awi.account_number,
		awi.transaction_id,
		awi.amount,
		awi.status,
		awi.payment_date,
		awi.group_id,
        awi.id,
		u.name,
		u.cname,
		u.mobile,
        u.user
		
		FROM `wl_aeps_withdrawl` as awi, add_cust as u 
		where 1 and awi.user_id = u.id and awi.withdrawl_type = 'BANK' and u.admin_id = '".$_SESSION[_session_userid_]."' ";	
	
	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
 $i = $pagination['offset'] + 1;
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
			?>
									<tr>
										<td class="sorting_1"><?php echo $i; ?></td>
										<td>
											<strong>Name : </strong><?php echo $rows["name"]; ?><br/>
											<strong>Company : </strong><?php echo $rows["cname"]; ?><br/>
											<strong>Mobile : </strong><?php echo $rows["mobile"]; ?><br/>
											<strong>User Id : </strong><?php echo $rows["user"]; ?><br/>
											<!--<a href="aeps-complete-details.php?uid=<?php echo $rows["uid"]; ?>" target="_blank">Get Complete Details</a>-->
										
										</td>
										<td>
											<strong>Account Name : </strong><?php echo $rows["account_name"]; ?><br/>
											<strong>Bank Name : </strong><?php echo $rows["bank_name"]; ?><br/>
											<strong>IFSC : </strong><?php echo $rows["ifsc"]; ?><br/>
											<strong>A/C Name : </strong><?php echo $rows["account_number"]; ?><br/>
										
										</td>
										<!--<td><a href="aeps-transaction-history.php?groupid=<?php echo $rows["group_id"]; ?>" target="_blank" ><?php echo $rows["group_id"]; ?></td>-->
										<td><a href="#" target="_blank" ><?php echo $rows["group_id"]; ?></td>
										<td><?php echo $rows["transaction_id"]; ?></td>
										<td><?php echo $rows["amount"]; ?></td>
										<td><?php echo $rows['payment_date']; ?></td>
										<td>
											
											<select data-rowId="<?php echo $rows['id'];?>" onchange="updateStatus(this)">
												<option <?php echo ($rows['status']=="PENDING")? "selected": "" ;?> value="PENDING">Pending</option>
												<option <?php echo ($rows['status']=="SUCCESS")? "selected": "";?> value="SUCCESS">Success</option>
												<!--<option <?php echo ($rows['status']=="REJECT")? "selected": "";?> value="REJECT">Reject</option>-->
											</select>
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