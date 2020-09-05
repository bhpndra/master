<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php include_once('classes/user_class.php'); ?>
<?php
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	$userClass = new User_class();
	//echo $helper->hashPin('1234')['encrypted'];

	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	 if(isset($filterBy['fByUsername'])&& $filterBy['fByUsername']!=""){
		$filter .= " and a.name like '%".$filterBy['fByUsername']."%' ";
	}
	if(isset($filterBy['fByUserid'])&& $filterBy['fByUserid']!=""){
		$filter .= " and a.user like '%".$filterBy['fByUserid']."%' ";
	}
	if(isset($filterBy['fByMobile'])&& $filterBy['fByMobile']!=""){
		$filter .= " and p.mobile like '%".$filterBy['fByMobile']."%' ";
	}
	if(isset($filterBy['fBycname'])&& $filterBy['fBycname']!=""){
		$filter .= " and a.cname like '%".$filterBy['fBycname']."%' ";
	}
	if(@$filterBy['dateFrom']=="" && @$filterBy['dateTo']==""){
		$date1 = new DateTime('7 days ago');
		$dateFrom = $date1->format('Y-m-d');
		$dateTo = date("Y-m-d");
		$filter .= " and DATE(p.`request_time`) BETWEEN '$dateFrom' AND '$dateTo' ";
	} else {
		$filter .= " and DATE(p.`request_time`) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
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
                                        <span>All Pending Fund Request</span>
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
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="fund-request.php" class="btn btn-default">Reset</a>
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
                                                        <i class="fa fa-cogs"></i>All Pending Fund Request
                                                    </div>
                                                    <form action="export/fund-request-history-excel.php?filter=<?=$filter;?>" method="post">					
                                                        <button type="submit" id="dataExport" name="dataExport" value="Export to excel" class="btn btn-info pull-right" style="margin:3px 5px 0px 0px;">Export in Excel</button>
                                                     </form>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" width="30px;">#</th>
                                                                    <th scope="col" >User</th>
                                                                    <th scope="col" width="150px;">Amount</th>
                                                                    <th scope="col" width="90px;">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
<?php
// for Pagination
include_once('classes/pagination.php'); 
$paginate = new pagin();
$pagiConfig = array();
$pagiConfig['current_page'] = isset($_GET['page']) ? $_GET['page'] : 1;
$pagiConfig['per_page_items'] = 50;
unset($_GET['page']);
$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?".http_build_query($_GET);
$orderBy = " order by `id` DESC";
$sql = "SELECT p.mobile,
		p.amount,
		p.payment_type,
		p.status,
		p.user_id,
		p.bank,
		p.id,
		p.request_time,
		p.update_time,
		p.deposit_slip,
		p.bank_refno,
		a.name,
		a.cname
		FROM `payment` as p, `add_cust` as a  where 1 and p.user_id = a.id  and p.status != 'Pending' and p.admin_id = '".$_SESSION[_session_userid_]."' and p.user_type = 'WL'";	
	
	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
 $i = $pagination['offset'] + 1;
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
			?>
									<tr>
										<td width="50px;" class="topup_trid"><?= $i; ?> </td>
										<td class="topup_trid">
											<strong>Name:</strong> <?= $rows['name']; ?><br/>
											<strong>Mobile:</strong> <?= $rows['mobile']; ?><br/>
											<strong>Id:</strong> <?= $rows['user_id']; ?><br/>
											<hr/>
<?php
	$bankDetails = $mysqlObj->get_field_data(" * ", 'bank_details', " where admin_id = '".$_SESSION[_session_userid_]."' and id = '".$rows['bank']."'");
?>
											<strong>A/C No.:</strong> <?= $bankDetails['account_number']; ?><br/>
											<strong>Bank To:</strong> <?= $bankDetails['bank_name']; ?><br/>
											<strong>Ref No.:</strong> <?= $rows['bank_refno']; ?><br/>
											<strong>Payment Type:</strong> <?= $rows['payment_type']; ?><br/>
											<strong>Request Time:</strong> <?= $rows['request_time']; ?><br/>
											<strong>Payment(Credit) Time:</strong> <?= $rows['update_time']; ?><br/>
											
											<hr/>
<?php
	$sqlQuery1 = $mysqlObj->mysqlQuery("SELECT withdrawl,date_created FROM `admin_trans` where  wl_id = '".$rows['user_id']."' and tr_type = 'DR'  ORDER BY `id` DESC limit 5");
	echo "<table class='table' style='background: #f7ffd3'><tr><th colspan='3' style='text-align: center;'>Last 5 Credit to User</th></tr>";
	echo "<tr><th>Amount</th><th>Reuest Time</th></tr>";
	$Lrows = '';
	while($Lrows = $sqlQuery1->fetch(PDO::FETCH_ASSOC)){
		echo "<tr>";
			echo "<td>".$Lrows['withdrawl']."</td>";
			echo "<td>".$Lrows['date_created']."</td>";
		echo "</tr>";	
	}
	echo "</table>";
?>	
										</td>
										<td width="150px;" class="amount"> <?= $rows['amount']; ?><br/>
										<?php if(!empty($rows['deposit_slip'])){  ?>
											<a href="../uploads/receipt/<?=$rows['deposit_slip']; ?>" target="_blank">View Slip</a>
										<?php } ?>
										</td>
										
										<td width="100px;" class="amount"  id="status<?= $rows['id']; ?>">
										
											<?php if($rows['status']=="PENDING") { ?>
													<a href="javascript:void(0)" class="btn btn-danger btn-xs statusmsg"  >Pending</a>
											<?php } else if($rows['status']=="Cancel") { ?>
													<a href="#" class="btn btn-warning btn-xs statusmsg" >Declined</a>
											<?php } else { ?>
													<a href="#" class="btn btn-success btn-xs statusmsg" >Success</a>
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
<script>
		function fund_approve_referenceid(id){
			swal({
			  title: "Enter Bank Transfer ReferenceId",
			  text: "",
			  type: "input",
			  inputType: "text",
			  inputPlaceholder: 'Enter ReferenceId here',
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			  confirmButtonText: "Yes, Approve!",
			  cancelButtonText: "No!",
			  closeOnConfirm: false,
			  closeOnCancel: true,
			  html: true,
			  showLoaderOnConfirm: true
			},
			function(inputValue){
				if (inputValue === false) return false;  
				if (inputValue === "") {
					swal.showInputError("You need to write something!");
					return false
				}
				
				if (inputValue) {  //alert(inputValue);
					$.ajax({
						type: "POST",
						cache: false,
						url: "ajax/fund_request_action.php",
						data: {'frid':id,'action':'approveWithReferenceId','referenceId':inputValue},
						success: function (response) { //alert(response);
							if(response=="true"){
								$("#action"+id).html(' ');
								$("#status"+id).html('<a href="#" class="btn btn-success btn-xs statusmsg" >Success</a>');
								swal("Approved!", "", "success");
							} else {
								swal("Request Failed!", response, "error");
							}
						}
					});
			  				
			  } 
			});
		}
/*		function fund_approve(id,amt){
			swal({
			  title: "Are you sure?",
			  text: "INR "+amt+" refund to user.<br/> Please enter your PIN",
			  type: "input",
			  inputType: "password",
			  inputPlaceholder: 'Enter PIN here',
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			  confirmButtonText: "Yes, Approve!",
			  cancelButtonText: "No!",
			  closeOnConfirm: false,
			  closeOnCancel: true,
			  html: true,
			  showLoaderOnConfirm: true
			},
			function(inputValue){
				if (inputValue === false) return false;  
				if (inputValue === "") {
					swal.showInputError("You need to write something!");
					return false
				}
				
				if (inputValue) {  //alert(inputValue);
					$.ajax({
						type: "POST",
						cache: false,
						url: "ajax/check_user.php",
						data: {'pin':inputValue,'check':'checkPin'},
						success: function (response) { //alert(response);
							if(response=="true"){								
								$.ajax({
									type: "POST",
									cache: false,
									url: "ajax/fund_request_action.php",
									data: {'frid':id,'action':'approve'},
									success: function (response) { //alert(response);
										if(response=="true"){
											$("#action"+id).html(' ');
											$("#status"+id).html('<a href="#" class="btn btn-success btn-xs statusmsg" >Success</a>');
											swal("Approved!", "", "success");
										} else {
											swal("Request Failed!", "", "error");
										}
									}
								});
							} else {
								swal(response, "", "error");
							}
						}
					});
			  				
			  } 
			});
		}
*/
		
		function fund_cancel(id){
			swal({
			  title: "Are you sure?",
			  text: "Decline this request",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			  confirmButtonText: "Yes, Cancel !",
			  cancelButtonText: "No!",
			  closeOnConfirm: false,
			  closeOnCancel: true,
			  showLoaderOnConfirm: true
			},
			function(isConfirm){ 
			  if (isConfirm) { 
					$.ajax({
						type: "POST",
						cache: false,
						url: "ajax/fund_request_action.php",
						data: {'frid':id,'action':'REJECT'},
						success: function (response) {
							$("#action"+id).html(' ');
							$("#status"+id).html('<a href="#" class="btn btn-warning btn-xs statusmsg" >Canceled</a>');
							swal("Canceled!", "", "success");
						}
					});
			  				
			  } 
			});
		}
		
</script>
</body>
</html>
</html>