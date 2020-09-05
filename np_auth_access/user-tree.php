<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	if(isset($filterBy['fByUsername'])&& $filterBy['fByUsername']!=""){
		$filter .= " and ac.name like '%".$filterBy['fByUsername']."%' ";
	}
	if(isset($filterBy['fByUserid'])&& $filterBy['fByUserid']!=""){
		$filter .= " and ac.user like '%".$filterBy['fByUserid']."%' ";
	}
	if(isset($filterBy['fByMobile'])&& $filterBy['fByMobile']!=""){
		$filter .= " and ac.mobile like '%".$filterBy['fByMobile']."%' ";
	}
	if(isset($filterBy['fBycname'])&& $filterBy['fBycname']!=""){
		$filter .= " and ac.cname like '%".$filterBy['fBycname']."%' ";
	}
	if(isset($filterBy['fByUsertype'])&& $filterBy['fByUsertype']!=""){
		$filter .= " and ac.userType ='".$filterBy['fByUsertype']."' ";
	}

	if($filterBy['fBycreateIn']!="" && isset($filterBy['fBycreateIn'])){
		$date1 = new DateTime($filterBy['fBycreateIn'].' days ago');
		$filterBy['dateFrom'] = $dateFrom = $date1->format('Y-m-d');
		$filterBy['dateTo'] = $dateTo = date("Y-m-d");
		$filter .= " and ( STR_TO_DATE(ac.created_on, '%d/%b/%Y') BETWEEN '$dateFrom' AND '$dateTo' ) ";
	} 
	if(isset($filterBy['dateFrom']) && isset($filterBy['dateTo']) && $filterBy['dateFrom']!="" && $filterBy['dateTo']!=""){
		$filter .= " and STR_TO_DATE(ac.created_on, '%d/%b/%Y') BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
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
																<label>Created in </label>
																<div>
																<?php
																	if($filterBy['fBycreateIn']!=""){
																		$selected1 = "s".$filterBy['fBycreateIn'];
																		$$selected1 = "selected";
																	}
																?>	
																	<select class='form-control' onchange="clearDate()" name='fBycreateIn' id="fBycreateIn" >					
																		<option value="">All</option>
																		<option <?=@$s7?> value="7">Last 7 days</option>
																		<option <?=@$s15?> value="15">Last 15 days</option>
																		<option <?=@$s30?> value="30">Last 30 days</option>
																	</select>
																</div>
															</div>
															<div class="form-group col-md-2">
																<label>Created From</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" onclick="changeCreateIn()" value="<?php echo @$filterBy['dateFrom']; ?>" class="form-control" placeholder="" name="dateFrom" id="dateFrom">
																</div>
															</div>
															<div class="form-group col-md-2">
																<label>Created To</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" onclick="changeCreateIn()" value="<?php echo @$filterBy['dateTo']; ?>" class="form-control" placeholder="" name="dateTo" id="dateTo">
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
                                                                    <th scope="col" width="30px">#</th>
                                                                    <th scope="col" width="100px">User Details</th>
                                                                    <th scope="col" width="450px">Tree</th>
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

//$sql = "select * from add_cust as ac, add_white_label as awl where id in (select user_id from add_white_label)";
$sql = "select ac.*,awl.domain from add_cust as ac, add_white_label as awl where ac.id = awl.user_id";

/* $sql = "SELECT id, name, user, mobile, cname, aeps_balance, created_on, 
		CASE WHEN id in (SELECT `add_distributer`.user_id FROM `add_distributer` where `add_distributer`.user_id = `add_cust`.id) THEN 'Distributer'
		WHEN id in (SELECT `add_retailer`.user_id FROM `add_retailer` where `add_retailer`.user_id = `add_cust`.id) THEN 'Retailer'
		WHEN id in (SELECT `add_white_label`.user_id FROM `add_white_label` where `add_white_label`.user_id = `add_cust`.id) THEN 'White Label'
		else '<span style=\'color:red\'>not defind</span>'
		end as userType		
		FROM `add_cust` where 1 "; */
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
										</td>
										<td width="150px;" class="amount" id="userdetailbox<?= $rows['id']; ?>">
											<a href="void:javascript(0)" onclick="get_child('<?= $rows['id']; ?>')">Get Child</a>
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
        <h4 class="modal-title w-100 font-weight-bold">Retailer List</h4>
      </div>
      <div class="modal-body mx-3" id="retailerList">
	  
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
		function get_child(uid){ 
			$.ajax({
				type: "POST",
				cache: false,
				url: "ajax/get_child_list.php",
				data: {'uid':uid,},
				success: function (response) {
					
					$("#userdetailbox"+uid).html(response);
				}
			});	
		} 
		function get_child_retailer(uid){ 
			$.ajax({
				type: "POST",
				cache: false,
				url: "ajax/get_child_list.php",
				data: {'uid':uid,},
				success: function (response) {
					$("#modaluserdetails").modal('show');
					$("#retailerList").html(response);
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