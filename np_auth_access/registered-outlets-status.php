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
		$filter .= " and ol.outlet_status ='".$filterBy['fByStatus']."' ";
	} 	
	if(isset($filterBy['fByOutletid'])&& $filterBy['fByOutletid']!=""){
		$filter .= " and ol.outletid ='".$filterBy['fByOutletid']."' ";
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
                                        <span>All Outlet KYC</span>
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
																<label>Outlet Id</label>
																<div>
																	<input type='text' name='fByOutletid' value="<?php echo @$filterBy['fByOutletid']; ?>"  class='form-control' placeholder="Outlet Id" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Status</label>
												<?php
													if($filterBy['fByStatus']!=""){
														$selected = $filterBy['fByStatus'];
														$$selected = "selected";
														//echo @$PENDING;
													}
												?>
																<div>
																	<select class='form-control' name='fByStatus' >
																													
																		<option value="">All</option>
																		<option <?=@$Pending?> value="Pending">Pending</option>
																		<option <?=@$Approved?> value="Approved">Approved</option>
																		<option <?=@$Reject?> value="Reject">Reject</option>
																	</select>
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="registered-outlets-status.php" class="btn btn-default">Reset</a>
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
                                                        <i class="fa fa-cogs"></i>All Outlet KYC</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">S.No</th>
																	<th style="width: 200px;">Agent Details</th>
																	<th style="width: 300px;">Outlet Info</th>
																	<th scope="col">Address</th>
																	<th scope="col">Outlet Status</th>
																	<th scope="col">Reg. Date</th>
																	<th scope="col">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
<?php
// for Pagination
include_once('classes/pagination.php'); 
$paginate = new pagin();
$pagiConfig = array();
$pagiConfig['current_page'] = $_GET['page'];
$pagiConfig['per_page_items'] = 25;
unset($_GET['page']);
$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?".http_build_query($_GET);
$orderBy = " ORDER BY `id` DESC ";
$sql = "SELECT ol.*, u.name as uname,u.cname,u.mobile as umobile,u.city
 FROM
 `outlet_kyc` as ol,
 add_cust as u
 WHERE
 
 ol.user_id = u.id and
 ol.pan_no !='' and
 ol.outletid !='' and
 sources = 'I'";	
	
	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);	
	
 $i = $pagination['offset'] + 1;
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){
			?>
									<tr>
										<td class="sorting_1"><?php echo $i; ?></td>
										<td>
											<strong>Name : </strong><?php echo $rows["uname"]; ?><br/>
											<strong>Company : </strong><?php echo $rows["cname"]; ?><br/>
											<strong>Mobile : </strong><?php echo $rows["umobile"]; ?><br/>
										
										</td>
										<td>
											<strong>Name : </strong><?php echo $rows["name"]; ?><br/>
											<strong>Email : </strong><?php echo $rows["email"]; ?><br/>
											<strong>Outlet Id : </strong><?php echo $rows["outletid"]; ?><br/>
											<strong>Mobile : </strong><?php echo $rows["phone1"]; ?><br/>
											<strong>PAN : </strong><?php echo $rows["pan_no"]; ?><br/>
											<strong>Aadhaar : </strong><?php echo $rows["aadhaar"]; ?><br/>
										
										</td>
										<td>
											<strong>City : </strong><?php echo $rows["city"]; ?><br/>
											<strong>Address : </strong><?php echo $rows["address"]; ?></td>
										<td><?php echo $rows["outlet_status"]; ?></td>
										<td><?php echo $rows['registration_date']; ?></td>
										<td>
											<a href="javascript:void(0)" onclick="check_outlet_kyc_status('<?=$rows["outletid"]?>','<?=$rows["pan_no"]?>')" class="btn btn-success btn-xs statusmsg push-right">Check Status</a><br/>
											<!--<a href="outlet-kyc-registration.php?outletid=<?=$rows["outletid"]?>&uid=<?=$rows["user_id"]?>" class="btn btn-primary btn-xs statusmsg push-right">Submit KYC</a>-->
										</td>
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
		function check_outlet_kyc_status(outletid,pan_no){
			$.ajax({
                type: 'POST',
                data: {outletid: outletid, pan_no: pan_no},
                cache: false,
                url: 'ajax/outlet_registration_status.php',
                success: function (response)
                {
					 swal({
						title: "KYC Status",
						text: response,
						html: true,
						closeOnConfirm: false
					});
                }
            });
		}
</script>

</body>
</html>
</html>