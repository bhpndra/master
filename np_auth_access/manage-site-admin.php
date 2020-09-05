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
	if(isset($filterBy['fByRowId'])&& $filterBy['fByRowId']!=""){
		$filter .= " and id = '".$filterBy['fByRowId']."' ";
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
                                        <span><?=($_SESSION[_session_usertype_]=="B2B")? "All Site Admin" : "All White Label";?></span>
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
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="manage-site-admin.php" class="btn btn-default">Reset</a>
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
                                                        <i class="fa fa-cogs"></i><?=($_SESSION[_session_usertype_]=="B2B")? "All Site Admin" : "All White Label";?></div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" width="30px;">#</th>
                                                                    <th scope="col" width="150px;">User Details</th>
                                                                    <th scope="col" width="150px;">AEPS Bal.</th>
                                                                    <th scope="col" width="150px;">Wallet Bal.</th>
                                                                    <th scope="col" width="150px;">Virtual Bal.</th>
																	<th scope="col" width="150px;">No.of Child Limit</th>
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
$pagiConfig['current_page'] = isset($filterBy['page'])? $filterBy['page'] : 0;
$pagiConfig['per_page_items'] = 50;
unset($_GET['page']);
$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?".http_build_query($_GET);
$sql = "SELECT id, name, user, mobile, cname, aeps_balance, created_on, status, aadhar_file, pan_file, wallet_balance, wl_virtual_balance,number_of_child_limi FROM `add_cust` where 1 and admin_id = '".$_SESSION[_session_userid_]."' and usertype = 'WL' ";
$orderBy = "  ORDER BY `id` DESC";
		
$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
$pagination = $paginate->pagination($pagiConfig);
$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);	
			
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
											
											<a href="../uploads/kyc-doc/wl/<?php echo $rows["aadhar_file"]; ?>" target="_blank">Aadhaar Image</a><br/>
											<a href="../uploads/kyc-doc/wl/<?php echo $rows["pan_file"]; ?>" target="_blank">Pan Image</a><br/>
										</td>
										<td width="150px;" class="amount"> <?= $rows['aeps_balance']; ?></td>
										<td width="150px;" class="amount" ><?php echo $rows['wallet_balance']?></td>
										<td><?php echo $rows['wl_virtual_balance']?></td>
										<td>
											<?php echo $rows['number_of_child_limi']?><br/><br/>
											
											<a href="javascript:void()" onclick="add_child_capping_val('<?= $rows["id"]?>')" title="Increase No.of Child Limit"><button class="btn btn-success btn-xs"> <i class="fa fa-plus" aria-hidden="true"></i> </button></a>&nbsp;&nbsp;&nbsp;
											<a href="javascript:void()" onclick="subtract_child_capping_val('<?= $rows["id"]?>')" title="Decrease No.of Child Limit"><button class="btn btn-danger btn-xs"><i class="fa fa-minus" aria-hidden="true"></i>  </button></a>
										</td>
										<!--code added on 22 february 2020-->
										<td width="150px;" class="amount"> <?= $rows['status']; ?><br><br>
											<?php if($rows['status']=='DISABLED'){?><button onclick="update_record('<?php echo base64_encode($rows['id']);?>');">Activate</button><?php }?>
										</td>
										<!--code end 22 february 2020-->
										<td>
										<a style="color: #8c6c00" href="javascript:void(0)" onclick="get_user_details('<?=$rows['id']?>')" class="btn btn-warning btn-xs">User Full Details</a>&nbsp;
										<a href="edit-user.php?uid=<?=base64_encode($rows['id'])?>" class="btn btn-success btn-xs" >Edit</a>&nbsp;
										<a href="change-password.php?uid=<?=base64_encode($rows['id'])?>" class="btn btn-primary btn-xs" >Change Password</a>&nbsp;
										<a href="change-pin.php?uid=<?=base64_encode($rows['id'])?>" class="btn btn-info btn-xs" >Change Pin</a>&nbsp;
										<a href="javascript:void(0)" onclick="set_wl_permission('<?=$rows['id']?>')" class="btn btn-danger btn-xs" >Permission</a>
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

<!-- SET WL PERMISSION POPUP--!-->

<div class="modal fade" id="modaluserpermission" tabindex="-1" role="dialog" aria-labelledby="modaluserpermission"
  aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h4 class="modal-title w-100 font-weight-bold">SET WL PERMISSION</h4>
			</div>
			<div id="save_permission" class="col-md-12"></div>
			<div class="modal-body mx-3" id="userpermissionbox">
		  
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
		function get_user_details(uid){ 
			$.ajax({
				type: "POST",
				cache: false,
				url: "ajax/get_user_complete_details.php",
				data: {'uid':uid,},
				success: function (response) {
					$("#modaluserdetails").modal('show');
					$("#userdetailbox").html(response);
				}
			});	
		}
		function set_wl_permission(uid){ 
			$.ajax({
				type: "POST",
				cache: false,
				url: "ajax/set_wl_permission.php",
				data: {'uid':uid,},
				success: function (response) {
					$("#modaluserpermission").modal('show');
					$("#userpermissionbox").html(response);
				}
			});	
		}
		function save_permission(uid)
		{	
			sms_pack_chk = $("#sms_pack_chk").prop("checked");
			if(sms_pack_chk == true){sms_pack_chk=1}else{sms_pack_chk=0}
			
			bulk_sms_chk = $("#bulk_sms_chk").prop("checked");
			if(bulk_sms_chk == true){bulk_sms_chk=1}else{bulk_sms_chk=0}
			
			bulk_email_chk = $("#bulk_email_chk").prop("checked");
			if(bulk_email_chk == true){bulk_email_chk=1}else{bulk_email_chk=0}
			
			payment_gateway_chk = $("#payment_gateway_chk").prop("checked");
			if(payment_gateway_chk == true){payment_gateway_chk=1}else{payment_gateway_chk=0}
		
			
			$.ajax({
				type: "POST",
				cache: false,
				url: "ajax/save_wl_permission.php",
				data: {'uid':uid,sms_pack_chk:sms_pack_chk, bulk_sms_chk:bulk_sms_chk, bulk_email_chk:bulk_email_chk,payment_gateway_chk:payment_gateway_chk},
				success: function (response) {
					$("#save_permission").html(response);
				}
			});
		}
		function get_user_hierarchy(uid){ 
			$.ajax({
				type: "POST",
				cache: false,
				url: "ajax/get_user_hierarchy_details.php",
				data: {'uid':uid,},
				success: function (response) {
					$("#modaluserdetails").modal('show');
					$("#userdetailbox").html(response);
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
<!--code added on 22 february 2020-->
<script>
	function update_record(id)
	{
		if(confirm('Are You sure want to enabled the status ?')){
			var status="ENABLED";
			$.ajax
			({
				
				type: 'POST',
						data: {status:status, id:id},
						cache: false,
						url: 'ajax/modify_records.php',
						success: function (response)
						{ 
							location.reload();
						}
			 });
		}
	}
	
	
	function add_child_capping_val(id){
		swal({
			  title: "Enter Number of Child to Increase",
			  text: "",
			  type: "input",
			  showCancelButton: true,
			  closeOnConfirm: true,
			  animation: "slide-from-top",
			  inputPlaceholder: "Enter number"
			},
			function(inputValue){
			  if (inputValue === false) return false;			  
			  if (inputValue === "") {
				swal.showInputError("Please! Enter Number");
				return false
			  } else {
					$.ajax({
						type:"post",
						url:"ajax/increase_child_capping.php",
						data:{val:inputValue,id:id},
						success:function(data){
							alert(data);
							window.location.href='manage-site-admin.php';							
						}
					});
			  }
			});
	}
	
	function subtract_child_capping_val(id){
		swal({
			  title: "Enter Number of Child to Decrease",
			  text: "",
			  type: "input",
			  showCancelButton: true,
			  closeOnConfirm: true,
			  animation: "slide-from-top",
			  inputPlaceholder: "Enter number"
			},
			function(inputValue){
			  if (inputValue === false) return false;			  
			  if (inputValue === "") {
				swal.showInputError("Please! Enter Number");
				return false
			  } else {
					$.ajax({
						type:"post",
						url:"ajax/decrease_child_capping.php",
						data:{val:inputValue,id:id},
						success:function(data){
							alert(data);
							window.location.href='manage-site-admin.php';							
						}
					});
			  }
			});
	}
	
</script>
<!--code end 22 february 2020-->
</body>
</html>
</html>