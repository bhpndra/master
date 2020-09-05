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
																<label>Outlet Status</label>
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
																	</select>
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="registered-yesbank-outlets.php" class="btn btn-default">Reset</a>
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
																	<th scope="col">KYC Status</th>
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
 `bankit_outlet_kyc` as ol,
 add_cust as u
 WHERE
 ol.user_id = u.id and
 ol.pan_no !='' and
 ol.outlet_id !='' ";	
	
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
											<strong>Name : </strong><?php echo $rows["fname"]; ?> <?php echo $rows["mname"]; ?> <?php echo $rows["lname"]; ?><br/>
											<strong>Email : </strong><?php echo $rows["email_id"]; ?><br/>
											<strong>Outlet Id : </strong><?php echo $rows["outlet_id"]; ?><br/>
											<strong>Mobile : </strong><?php echo $rows["mobile"]; ?><br/>
											<strong>PAN : </strong><?php echo $rows["pan_no"]; ?><br/>
										
										</td>
										<td id="kycST<?=$rows["user_id"]?>"><?php echo $rows["status"]; ?></td>
										<td id="otlST<?=$rows["user_id"]?>"><?php echo $rows["outlet_status"]; ?></td>
										<td><?php echo $rows['registration_date']; ?></td>
										<td>
											<a href="javascript:void(0)" onclick="get_kyc_details('<?=$rows["user_id"]?>','<?=$rows["outlet_id"]?>')" class="btn btn-success btn-xs statusmsg push-right">View KYC Details</a><br/>
										<?php if(!empty($rows["aadhaar_img"])){ ?>	
											<a target="_blank" href="https://www.netpaisa.com/nps/apiUser/pan_img/<?=$rows["aadhaar_img"]?>" class="btn btn-danger btn-xs statusmsg push-right" >Download Aadhar Image</a><br/>
										<?php } ?>
											<a href="javascript:void(0)" onclick="update_status('<?=$rows["user_id"]?>','<?=$rows["pan_no"]?>','<?=$rows["mobile"]?>')" class="btn btn-warning btn-xs statusmsg push-right">Update Status</a><br/>
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
<div class="modal fade" id="modaluserdetails" tabindex="-1" role="dialog" aria-labelledby="modaluserdetails"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title w-100 font-weight-bold">User Details</h4>
      </div>
      <div class="modal-body mx-3" id="userdetailbox">
	  
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
		 
		function get_kyc_details(uid,outlet_id){  //alert(outlet_id);
			$.ajax({
				type: "POST",
				cache: false,
				url: "ajax/get_banki_kyc_complete_details.php",
				data: {'uid':uid, 'outlet_id':outlet_id},
				success: function (response) {
					$("#modaluserdetails").modal('show');
					$("#userdetailbox").html(response);
				}
			});	
		} 
		function update_status(id,pan,mobile){  //alert(id);
			$.ajax({
				type: "POST",
				cache: false,
				url: "ajax/update_banki_aeps_status.php",
				data: {'id':id, 'pan':pan, 'mobile':mobile},
				success: function (response) {
					var data = JSON.parse(response);
					//console.log(data['kyc_status']);
					$("#kycST"+id).html(data['kyc_status']);
					$("#otlST"+id).html(data['outlet_status']);
					swal({
						title: "Outlet Status",
						text: "Outlet Status : " + data['outlet_status'] + "<br/>KYC Status : " + data['kyc_status'],
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