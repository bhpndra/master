<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	if(isset($filterBy['fByUsername'])&& $filterBy['fByUsername']!=""){
		$filter .= " and a.name like '%".$filterBy['fByUsername']."%' ";
	}
	if(isset($filterBy['fByRowId'])&& $filterBy['fByRowId']!=""){
		$filter .= " and a.id = '".$filterBy['fByRowId']."' ";
	}
	if(isset($filterBy['fByUserid'])&& $filterBy['fByUserid']!=""){
		$filter .= " and a.user like '%".$filterBy['fByUserid']."%' ";
	}
	if(isset($filterBy['fByMobile'])&& $filterBy['fByMobile']!=""){
		$filter .= " and a.mobile like '%".$filterBy['fByMobile']."%' ";
	}
	if(isset($filterBy['fBycname'])&& $filterBy['fBycname']!=""){
		$filter .= " and a.cname like '%".$filterBy['fBycname']."%' ";
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
                                        <span>All Users</span>
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
																<label>UserId</label>
																<div>
																	<input type='text' id="fByRowId" name='fByRowId' value="<?php echo @$filterBy['fByRowId']; ?>"  class='form-control' placeholder="Id" >
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
															
															<div class="form-group col-md-2">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="user-list.php" class="btn btn-default">Reset</a>
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
                                                        <i class="fa fa-cogs"></i>All Users</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" width="30px;">#</th>
                                                                    <th scope="col" width="150px;">User Details</th>
                                                                    <th scope="col" width="150px;">Recharge</th>
                                                                    <th scope="col" width="150px;">AEPS</th>
                                                                    <th scope="col" width="150px;">DMT (Money Transfer)</th>
                                                                    <th scope="col" width="150px;">Bill Payments</th>
                                                                    <!--code added on 22 february 2020-->
                                                                    <th scope="col" width="150px;">Status</th>
                                                                    <!--code end 22 february 2020-->
                                                                    <th scope="col" width="150px;">Details</th>
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
$sql = "SELECT a.id, a.name, a.user, a.mobile, a.cname, a.created_on, a.status, a.aadhar_file, a.pan_file, a.service_access, b.domain 				
		FROM `add_cust` as a, `add_white_label` as b where 1 and a.id = b.user_id ";
$orderBy = "  ORDER BY `id` DESC";		
	
if($filterBy['fByUsertype']==""){
	
	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
	
} else {
	
	$mysqlObj->mysqlQuery("CREATE TEMPORARY TABLE tem_users (".$sql.")");
	
	$pagiConfig['total_rows'] = $mysqlObj->countRows("SELECT * from tem_users where 1	".$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery("SELECT * from tem_users where 1	".$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);
	
}			
 $i = $pagination['offset'] + 1;
 $allow = "<span style='color:green'>Allow</span>";
 $notAllow = "<span style='color:red'>Not Allow</span>";
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
			?>
									<tr>
										<td width="30px;"> <?= $i; ?> </td>
										<td width="250px;">
											<strong>Name:</strong> <?= $rows['name']; ?> - (#<?= $rows['id']; ?>)<br/>
											<strong>Username:</strong> <?= $rows['user']; ?><br/>
											<strong>Mobile:</strong> <?= $rows['mobile']; ?><br/>
											<strong>Company:</strong> <?= $rows['cname']; ?><br/>
											<strong>Domain:</strong> <?= $rows['domain']; ?><br/>
											
											<?php if($rows['userType']!="White Label"){ ?>
											<a href="../kyc-doc/<?php echo $rows["aadhar_file"]; ?>" target="_blank">Aadhaar Image</a><br/>
											<a href="../kyc-doc/<?php echo $rows["pan_file"]; ?>" target="_blank">Pan Image</a><br/>
											<?php } else { ?>
											<a href="../kyc-doc/wl/<?php echo $rows["aadhar_file"]; ?>" target="_blank">Aadhaar Image</a><br/>
											<a href="../kyc-doc/wl/<?php echo $rows["pan_file"]; ?>" target="_blank">Pan Image</a><br/>
											<?php } ?>
										</td>
										<?php $services = explode(',',$rows["service_access"]); ?>
										<td width="150px;" class="amount"> <?=(in_array('recharge',$services))? $allow : $notAllow ?> </td>
										<td width="150px;" class="amount"> <?=(in_array('aeps',$services))? $allow : $notAllow ?> </td>
										<td width="150px;" class="amount"> <?=(in_array('dmt',$services))? $allow : $notAllow ?> </td>
										<td width="150px;" class="amount"> <?=(in_array('bill_payment',$services))? $allow : $notAllow ?> </td>
										<td width="150px;" class="amount">											
										<a href="edit-user.php?uid=<?=$rows['id']?>" class="btn btn-success btn-xs" >Edit</a>
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
		function clearDate(){ 
			$("#dateFrom").val('');
			$("#dateTo").val(''); 
		}
		function changeCreateIn(){ //alert();
			var option  = $("#fBycreateIn").html();
			$("#fBycreateIn").html(option.replace('selected', ''));
		}
		 

</script>
</body>
</html>
</html>