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
		$filter .= " and (concat_ws(' ',d.topup_trid,d.agent_trid,d.mobile,d.bene_code,d.bene_name,d.bene_ac,d.ifsc_code) like '%".$filterBy['fBySearch']."%') ";
	}			
	if(isset($filterBy['fBytransid'])&& $filterBy['fBytransid']!=""){
		$filter .= " and d.`topup_trid`= '".$filterBy['fBytransid']."' ";
	}
	if($filterBy['fBystatus']==""){
		$filter .= " and (d.status like '%Transaction Pending%' or d.status like '%Pending%'  or d.status like '%Transaction Under Process%') ";
	}
	if($filterBy['dateFrom']=="" && $filterBy['dateTo']==""){
		$date1 = new DateTime('30 days ago');
		$dateFrom = $date1->format('Y-m-d');
		$dateTo = date("Y-m-d");
		$filter .= " and ( DATE(d.`date`) BETWEEN '$dateFrom' AND '$dateTo' ) ";
	} else {
		$filter .= " and DATE(d.`date`) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
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
                                        <span>All DMT Pending</span>
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
																		<option <?php if(@$filterBy['fBystatus']==""){ echo "selected";} ?> value="">Pending</option>
																		<option <?php if(@$filterBy['fBystatus']=="All"){ echo "selected";} ?> value="All">All Report</option>
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
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="dmt1-pending-report.php" class="btn btn-default">Reset</a>
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
                                                        <i class="fa fa-cogs"></i>All DMT Pending</div>
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
																	<th scope="col" style="width: 93px;">API</th>
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
$orderBy = " ORDER BY dmt_id DESC ";
//$sql = "SELECT d.*,u.cname FROM `dmt_info` as d, `add_cust` as u WHERE d.`topup_trid`!='' AND ( DATE(`date`) BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() ) and u.id = d.agent_id";	
$sql = "SELECT d.*  FROM `in_mt_info` as d WHERE 1 ";	
	
	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
 $i = $pagination['offset'] + 1;
 //echo $sql.$filter;
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
				
				$user = $mysqlObj->get_field_data('name,cname,mobile','add_cust'," where id='".$rows["agent_id"]."'");
			?>
									<tr>
										<td class="sorting_1"><?php echo $i; ?></td>
										<td><strong>Tr. ID: </strong><?php echo $rows["topup_trid"]; ?><br/>
										<strong>AgentTran. ID: </strong><?php echo $rows["agent_trid"]; ?><br/>
											<strong>Amount: </strong><?php echo $rows["amount"]; ?><br/>
											<strong>Date: </strong><?php echo $rows["date"]; ?>
										</td>
										<td id="balDetails<?php echo $rows["dmt_id"]; ?>">
											<a href="javascript:void(0)" onclick="getBalanceDetails('<?php echo $rows["agent_trid"]; ?>','RETAILER','<?php echo $rows["agent_id"]; ?>','<?php echo $rows["dmt_id"]; ?>')">Get Balance Details</a>
										</td>
										<td>
											<strong>Id: </strong><?php echo $rows["agent_id"]; ?><br/>
											<strong>Shop: </strong><?php echo $user["cname"]; ?><br/>
											<strong>Retailer: </strong><?php echo $user["name"]; ?><br/>
											<strong>Mobile: </strong><?php echo $user["mobile"]; ?><br/>
															
										</td>
										<td>
											<strong>Code: </strong><?php echo $rows["bene_code"]; ?><br/><strong>Name : </strong><?php echo $rows["bene_name"]; ?><br/>
											<strong>A/C : </strong><?php echo $rows["bene_ac"]; ?><br/>
											<strong>IFSC : </strong><?php echo $rows["ifsc_code"]; ?><br/>
											<strong>Mobile: </strong><?php echo $rows["mobile"]; ?>												
										</td>
										<td id="tcol<?php echo $rows["dmt_id"]; ?>">
											<?php echo $rows['status']; ?><br/>
											
										</td>
										<td><?php echo $rows['api']; ?></td>
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
		function getBalanceDetails(tranId,userTyep,userID,rowId){
			$.ajax({
				type: 'POST',
				data: {tranId:tranId, userTyep:userTyep, userID:userID, rowId:rowId  },
				cache: false,
				url: 'ajax/get_indo_nepal_balance_details.php',
				success: function (response)
				{ 
					$("#balDetails"+rowId).html(response);
				}
			});
		}
		
</script>

</body>
</html>
</html>