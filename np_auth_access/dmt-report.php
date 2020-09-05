<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	include_once('classes/user_class.php'); 
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	$userClass = new user_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	if(isset($filterBy['fBySearch'])&& $filterBy['fBySearch']!=""){
		$filter .= " and (concat_ws(' ',d.transaction_id,d.agent_trid,d.mobile,d.bene_code,d.bene_name,d.bene_ac,d.ifsc_code) like '%".$filterBy['fBySearch']."%') ";
	}			
	if(isset($filterBy['fBytransid'])&& $filterBy['fBytransid']!=""){
		$filter .= " and d.`transaction_id`= '".$filterBy['fBytransid']."' ";
	}
	if(isset($filterBy['fBystatus']) && $filterBy['fBystatus']!=""){
		$filter .= " and d.status = '".$filterBy['fBystatus']."'  ";
	}
	if(@$filterBy['dateFrom']=="" && @$filterBy['dateTo']==""){
		$date1 = new DateTime('30 days ago');
		$dateFrom = $date1->format('Y-m-d');
		$dateTo = date("Y-m-d");
		$filter .= " and ( DATE(d.`date_created`) BETWEEN '$dateFrom' AND '$dateTo' ) ";
	} else {
		$filter .= " and DATE(d.`date_created`) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
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
                                        <span>All DMT</span>
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
																<label>Search</label>
																<div>
																	<input type='text' id="fBySearch" name='fBySearch' value="<?php echo @$filterBy['fBySearch']; ?>"  class='form-control' placeholder="Search Text" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Transaction Id</label>
																<div>
																	<input type='text' id="fBytransid" name='fBytransid' value="<?php echo @$filterBy['fBytransid']; ?>"  class='form-control' placeholder="Transaction Id" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Status</label>
																<div>
																	<select class='form-control' name='fBystatus' >
																		<option <?php if(@$filterBy['fBystatus']==""){ echo "selected";} ?> value="All">All Report</option>
																		<option <?php if(@$filterBy['fBystatus']=="SUCCESS"){ echo "selected";} ?> value="SUCCESS">Success</option>
																		<option <?php if(@$filterBy['fBystatus']=="PENDING"){ echo "selected";} ?> value="PENDING">Pending</option>
																		<option <?php if(@$filterBy['fBystatus']=="FAILED"){ echo "selected";} ?> value="FAILED">Failed</option>
																	</select>
																</div>
															</div>
															<div class="form-group col-md-2">
																<label>From</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" value="<?=$dateFrom?>" class="form-control" placeholder="" name="dateFrom" id="dateFrom">
																</div>
															</div>
															<div class="form-group col-md-2">
																<label>To</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" value="<?=$dateTo?>" class="form-control" placeholder="" name="dateTo" id="dateTo">
																</div>
															</div>
															
															<div class="form-group col-md-2">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="dmt-report.php" class="btn btn-default">Reset</a>
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
                                                        <i class="fa fa-cogs"></i>All DMT</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">S.No</th>
																	<th scope="col">Transaction Details</th>
																	<th scope="col">Amount Details</th>
																	<th scope="col">Agent Details</th>
																	<th scope="col">Bene. Details</th>
																	<th scope="col" style="width: 93px;">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
<?php
// for Pagination
include_once('classes/pagination.php'); 
$paginate = new pagin();
$pagiConfig = array();
$pagiConfig['current_page'] = isset($_GET['page']) ? $_GET['page'] : 1;
$pagiConfig['per_page_items'] = 25;
unset($_GET['page']);
$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?".http_build_query($_GET);
$orderBy = " ORDER BY id DESC ";

$sql1 = "SELECT d.id  FROM `dmt_info` as d left join add_cust as b on d.user_id = b.id WHERE b.admin_id = '".$_SESSION[_session_userid_]."' ";	
$sql = "SELECT d.*,b.cname,b.name,b.mobile as r_mobile,b.usertype  FROM `dmt_info` as d left join add_cust as b on d.user_id = b.id WHERE  b.admin_id = '".$_SESSION[_session_userid_]."' ";	
	
	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql1.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
 $i = $pagination['offset'] + 1;
 //echo $sql.$filter;
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
				
				//$user = $mysqlObj->get_field_data('name,cname,mobile,usertype','add_cust'," where id='".$rows["user_id"]."'");
			?>
									<tr>
										<td class="sorting_1"><?php echo $i; ?></td>
										<td><strong>Tr. ID: </strong><?php echo $rows["transaction_id"]; ?><br/>
										<strong>AgentTran. ID: </strong><?php echo $rows["agent_trid"]; ?><br/>
											<strong>Amount: </strong><?php echo $rows["amount"]; ?><br/>
											<strong>Date: </strong><?php echo $rows["date_created"]; ?>
										</td>
										<td id="balDetails<?php echo $rows["id"]; ?>">
											<a href="javascript:void(0)" onclick="getBalanceDetails('<?php echo $rows["transaction_id"]; ?>','<?php echo $rows["usertype"]; ?>','<?php echo $rows["user_id"]; ?>','<?php echo $rows["id"]; ?>')">Get Balance Details</a>
										</td>
										<td>
											<strong>Id: </strong><?php echo $rows["user_id"]; ?><br/>
											<strong>Shop: </strong><?php echo $rows["cname"]; ?><br/>
											<strong>Retailer: </strong><?php echo $rows["name"]; ?><br/>
											<strong>Mobile: </strong><?php echo $rows["r_mobile"]; ?><br/>
															
										</td>
										<td>
											<strong>Code: </strong><?php echo $rows["bene_code"]; ?><br/><strong>Name : </strong><?php echo $rows["bene_name"]; ?><br/>
											<strong>A/C : </strong><?php echo $rows["bene_ac"]; ?><br/>
											<strong>IFSC : </strong><?php echo $rows["ifsc_code"]; ?><br/>
											<strong>Mobile: </strong><?php echo $rows["mobile"]; ?>												
										</td>
										<td id="tcol<?php echo $rows["id"]; ?>">
											<?php echo $rows['status']; ?><br/>
											<?php if($rows['status']=="Transaction Under Process" || $rows['status']=="Pending" || $rows['status']=='Transaction Pending' || $rows['status']=="PENDING"){ ?>
											<a href="javascript:void(0)" onclick="change_status('<?php echo $rows["id"]; ?>','<?php echo $rows["user_id"]; ?>')" class="btn btn-success btn-xs statusmsg" style="margin-bottom:5px;">Change Status</a>
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

			
			
		});
		function getBalanceDetails(tranId,userTyep,userID,rowId){
			$.ajax({
				type: 'POST',
				data: {tranId:tranId, userTyep:userTyep, userID:userID, rowId:rowId  },
				cache: false,
				url: 'ajax/get_dmt_balance_details.php',
				success: function (response)
				{ 
					$("#balDetails"+rowId).html(response);
				}
			});
		}
		function change_status(val,user_id){
			var span = '<select id="swalSelect"><option value="">Select Status</option><option value="SUCCESS">Success</option><option value="PENDING">Pending</option><option value="REFUNDED">Refunded</option><option value="FAILED">Failed</option></select>';			

			swal({
			  title: "Select Status",
			  text: span,
			  type: "info",
			  html: true,
			  showCancelButton: true,
			  closeOnConfirm: false,
			  showLoaderOnConfirm: true,
			},
			function(){
			  /* setTimeout(function(){
				swal("Ajax request finished!");
			  }, 2000); */  
			  var sel = $('#swalSelect :selected').val();
			  if(sel!=''){
				  //alert(sel);
				  $.ajax({
						type: "POST",
						cache: false,
						url: "ajax/dmt_transaction_status.php",
						data: {'status':sel,'id':val, 'user_id':user_id},
						success: function (response) {
							swal(response, "", "success");
						}
					});
			  } else {
				  swal.showInputError("You need to select status!");
			  }
			});
		}
</script>

</body>
</html>
</html>