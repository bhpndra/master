<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	include_once('classes/user_class.php'); 
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	$userClass = new user_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	if(isset($filterBy['fByTranid'])&& $filterBy['fByTranid']!=""){
		$filter .= " and (a.transaction_id like '%".$filterBy['fByTranid']."%' or a.agent_trid like '%".$filterBy['fByTranid']."%') ";
	}
	if(isset($filterBy['fByMobile'])&& $filterBy['fByMobile']!=""){
		$filter .= " and a.mobile like '%".$filterBy['fByMobile']."%' ";
	}
	if(isset($filterBy['fBycname'])&& $filterBy['fBycname']!=""){
		$filter .= " and a.cname like '%".$filterBy['fBycname']."%' ";
	}
	if(isset($filterBy['status'])&& $filterBy['status']!=""){
		$filter .= " and a.status = '".$filterBy['status']."' ";
	}	
	
	if(@$filterBy['dateFrom']=="" && @$filterBy['dateTo']==""){
		$date1 = new DateTime('30 days ago');
		$dateFrom = $date1->format('Y-m-d');
		$dateTo = date("Y-m-d");
		$filter .= " and DATE(a.`date_created`) BETWEEN '$dateFrom' AND '$dateTo' ";
	} else {
		$filter .= " and DATE(a.`date_created`) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
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
                                        <span>Recharge</span>
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
																<label>Recharge Mobile Number</label>
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
																<label>Status</label>
																<div class="input-icon">
																	<select class="form-control" name='status'>
																		<option value="">All</option>
<?php
		$sqlQuery = $mysqlObj->mysqlQuery("SELECT status FROM `recharge_info` GROUP by status");	
															while($rowsts = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
?>
																	<option value="<?=$rowsts['status']?>"><?=$rowsts['status']?></option>
															<?php }?>
																		
																	</select>
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="recharge-history.php" class="btn btn-default">Reset</a>
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
                                                        <i class="fa fa-cogs"></i>Recharge</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">S.No</th>
																	<th scope="col">Transaction Id</th>
																	<th scope="col">Agent Id</th>
																	<th scope="col">Type</th>
																	<th scope="col">Mobile</th>
																	<th scope="col">Opening Bal</th>
																	<th scope="col">Amount</th>
																	<th scope="col">Closing Bal</th>
																	<th scope="col">Recharge Type</th>
																	<th scope="col">Time</th>
																	<th scope="col">Status</th>
																	<th scope="col">Msg</th>
																	<th scope="col">Retailer</th>
																	<!--<th scope="col">Disputes</th>-->
                                                                </tr>
                                                            </thead>
                                                            <tbody>
<?php
// for Pagination
include_once('classes/pagination.php'); 
$paginate = new pagin();
$pagiConfig = array();
$pagiConfig['current_page'] = isset($_GET['page']) ? $_GET['page'] : 1;
$pagiConfig['per_page_items'] = 10;
unset($_GET['page']);
$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?".http_build_query($_GET);
$orderBy = " order by a.`id` DESC";
$sql1 = "SELECT a.id FROM `recharge_info` as a left join add_cust as b on a.user_id = b.id where a.`transaction_id`!='' and a.`transaction_id`!='0'  and b.admin_id = '".$_SESSION[_session_userid_]."'";	
$sql = "SELECT a.*,b.name,b.mobile as r_mobile, b.cname FROM `recharge_info` as a left join add_cust as b on a.user_id = b.id where a.`transaction_id`!='' and a.`transaction_id`!='0'  and b.admin_id = '".$_SESSION[_session_userid_]."'";	
	
	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql1.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
 $i = $pagination['offset'] + 1;
 //echo $sql.$filter;
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
			?>
									<tr>
										<td class="sorting_1"><?php echo $i; ?></td>
										<td class=" expand">
											<?php echo $trnsId = ($rows['transaction_id'])? $rows['transaction_id'] : $rows['agent_trid']; ?>
										</td>
										<td><?php echo $rows["agent_trid"]; ?></td>
										<td><?php echo $rows["rech_type"]; ?></td>
										<td><?php echo $rows["mobile"]; ?></td>
										<?php
										$td = $mysqlObj->get_field_data("comments,id,opening_balance","retailer_trans"," where transaction_id = '".$rows["transaction_id"]."'"); 
										?>
										<td><?php echo $td["opening_balance"]; ?></td>
										<td><?php echo $rows['deducted_amount']; ?></td>
										<td><?php echo $rows['amount']; ?></td>
										<td><?php echo $rows["rech_type"]; ?></td>
										<td><?php echo $rows["date_created"]; ?></td>
										<td><?php echo $rows['status'];	?></td>
										<td><?php echo $td['comments'];	?></td>
										<!--<td id="statusCol<?=$rows['agent_trid']?>">
											<?php 
												if($rows['status']=="PENDING"){
											?>
												<a href="javascript:void(0)"  onclick="update_status('<?=$rows['agent_trid']?>')" class="btn btn-warning btn-xs statusmsg push-right">Get Status</a>
											<?php
												} else if($rows['status']=="SUCCESS") {
													echo $rows['status'];
											?>
											<br/><a href="javascript:void(0)"  onclick="check_status('<?=$rows['agent_trid']?>')" class="btn btn-success btn-xs statusmsg push-right">Get Status</a>
											<?php
												} else { echo $rows['status']; }
												
											?>
										
										</td>-->
										
										<td>
										<?php
											//$uRes = $mysqlObj->mysqlQuery("SELECT name,mobile,cname FROM `add_cust` where id = '".$rows["user_id"]."'")->fetch(PDO::FETCH_ASSOC);
											echo $rows["name"]  . "<br/>" ;
											echo $rows["r_mobile"]  . "<br/>" ;
											echo $rows["cname"]  . "<br/>" ;
										?>
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
<script type="text/javascript">
    function update_status(Trid){  //alert(id);
			$("#statusCol"+Trid).html('Wait');
            $.ajax({
                type: "POST",
                cache: false,
                url: "../r_admin/rechargeV1status.php",
                data: {'utrans_id':Trid},
                success: function (response) {
                    var data = JSON.parse(response);
                    console.log(data);
					if(data['status_msg']!="PENDING"){
						 $("#statusCol"+Trid).html(data['status_msg']);
					}
                   
                    /*$("#otlST"+id).html(data['outlet_status']);
                    $("#comm"+id).html(data['comments']); */
                }
            }); 
        } 
		function check_status(Trid){  //alert(id);
				if(confirm("Transaction Already success. Do you want retry ?")){
				$("#statusCol"+Trid).html('WAIT');
					$.ajax({
						type: "POST",
						cache: false,
						url: "../r_admin/recharge_status_check.php",
						data: {'utrans_id':Trid},
						success: function (response) {
							var data = JSON.parse(response);
							console.log(data);
							if(data['status_msg']!="PENDING"){
								 $("#statusCol"+Trid).html(data['status_msg']);
							} else {
								$("#statusCol"+Trid).html(data['status_msg']);
							}
						   
							/*$("#otlST"+id).html(data['outlet_status']);
							$("#comm"+id).html(data['comments']); */
						}
					}); 
				} 
			} 
</script>
</body>
</html>
</html>