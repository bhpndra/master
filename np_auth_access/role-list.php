<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			

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
                                        <span>All Roles</span>
                                    </li>
                                </ul>


                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>All Roles</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" width="30px;">#</th>
                                                                    <th scope="col" width="150px;">Role Name</th>
                                                                    <th scope="col" width="150px;">Action</th>
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



$sql = "SELECT * from support_roles ";
$orderBy = "  ORDER BY `role_name`  ASC ";		
	
$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
$pagination = $paginate->pagination($pagiConfig);
$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
	
		
 $i = $pagination['offset'] + 1;
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
			?>
									<tr>
										<td width="30px;"> <?= $i; ?> </td>
										<td width="150px;"> <?= $rows['role_name']; ?> - (#<?= $rows['id']; ?>) </td>
										<td><a href="edit-role.php?id=<?= $rows['id']; ?>"> Edit </a></td>
										
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