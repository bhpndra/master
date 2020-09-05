<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	if(isset($filterBy['fByUsername'])&& $filterBy['fByUsername']!=""){
		$filter .= " and name like '%".$filterBy['fByUsername']."%' ";
	}
	if(isset($filterBy['fByUserid'])&& $filterBy['fByUserid']!=""){
		$filter .= " and user like '%".$filterBy['fByUserid']."%' ";
	}
	if(isset($filterBy['fByMobile'])&& $filterBy['fByMobile']!=""){
		$filter .= " and mobile like '%".$filterBy['fByMobile']."%' ";
	}
	if(isset($filterBy['fBycname'])&& $filterBy['fBycname']!=""){
		$filter .= " and cname like '%".$filterBy['fBycname']."%' ";
	}
	if(isset($filterBy['fByUsertype'])&& $filterBy['fByUsertype']!=""){
		$filter .= " and userType ='".$filterBy['fByUsertype']."' ";
	}
	if(isset($filterBy['uid'])&& $filterBy['uid']!=""){
		$filter .= "and creator_id = '".$filterBy['uid']."' ";
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
													<input type="hidden" value="<?=$filterBy['uid']?>" name="uid" />
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
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="view-child-user.php?uid=<?=$filterBy['uid']?>" class="btn btn-default">Reset</a>
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
                                                                    <th scope="col" width="150px;">Name</th>
                                                                    <th scope="col" width="150px;">Username</th>
                                                                    <th scope="col" width="150px;">Mobile</th>
                                                                    <th scope="col" width="150px;">Role</th>
                                                                    <th scope="col" width="150px;">Company</th>
                                                                    <th scope="col" width="150px;">Balance</th>
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



$sql = "SELECT id, name, user, mobile, cname,
		CASE WHEN id in (SELECT `add_distributer`.user_id FROM `add_distributer` where `add_distributer`.user_id = `add_cust`.id) THEN 'Distributer'
		WHEN id in (SELECT `add_retailer`.user_id FROM `add_retailer` where `add_retailer`.user_id = `add_cust`.id) THEN 'Retailer'
		WHEN id in (SELECT `add_white_label`.user_id FROM `add_white_label` where `add_white_label`.user_id = `add_cust`.id) THEN 'White Label'
		else '<span style=\'color:red\'>not defind</span>'
		end as userType		
		FROM `add_cust` where 1  ";
$orderBy = "  ORDER BY `add_cust`.`id` DESC";		
		

	
	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
	
		
 $i = $pagination['offset'] + 1;
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
			?>
									<tr>
										<td width="30px;"> <?= $i; ?> </td>
										<td width="150px;"> <?= $rows['name']; ?> - (#<?= $rows['id']; ?>) </td>
										<td width="150px;" class="topup_trid"><?= $rows['user']; ?> </td>
										<td width="150px;" class="mobile"> <?= $rows['mobile']; ?></td>
										<td width="150px;" class="amount"> <?= $rows['userType']; ?></td>
										<td width="150px;" class="amount"> <?= $rows['cname']; ?></td>
										<td width="150px;" class="amount" onclick="getBalance(this,'<?= $rows['id']; ?>')"> <a href="javascript:void(0)">Get Balance</a></td>
										<td width="150px;" class="amount">
										<a style="color: #661d00;" href="javascript:void(0)" onclick="getLast5Transaction('<?php echo $rows['id']?>')">Last 5 Transaction</a>
										<br/>
										<?php if($rows['userType']=="Retailer"){ ?>
											<a style="color: #347d01;" href="view-all-retailer-transactions.php?uid=<?=$rows['id']?>" target="_blank">All Transactions</a>
										<?php } ?>
										<?php if($rows['userType']=="Distributer"){ ?>
											<a style="color: #347d01;" href="view-all-distributer-transactions.php?uid=<?=$rows['id']?>" target="_blank">All Transactions</a><br/>
											<a href='view-child-user.php?uid=<?=$rows['id']?>' target='_blank'> View Child Users </a>
										<?php } ?>
										<?php if($rows['userType']=="White Label"){ ?>
											<a style="color: #347d01;" href="view-all-whitelabel-transactions.php?uid=<?=$rows['id']?>" target="_blank">All Transactions</a><br/>
											<a href='view-child-user.php?uid=<?=$rows['id']?>' target='_blank'> View Child Users </a>
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