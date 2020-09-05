<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	include_once('classes/user_class.php'); 
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	$userClass = new user_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	if($filterBy['status']=="open" || empty($filterBy['status']) ){
		$statusFilter = " and a.is_active like '%Open%'";
		$statusBtnClass1 = "success";
		$statusBtnClass2 = "primary";
	}
	if($filterBy['status']=="close"  ){
		$statusFilter = " and a.is_active like '%close%'";
		$statusBtnClass1 = "primary";
		$statusBtnClass2 = "success";
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
                                        <span>All Tickets</span>
                                    </li>
                                </ul>

                                <!-- END PAGE BREADCRUMBS -->
								
							
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>All Tickets</div>
													<div class="actions">
														<a href="tickets.php?status=open" class="btn btn-<?php echo $statusBtnClass1;?> pull-right">All Open Tickets</a> 
														<a href="tickets.php?status=close" class="btn btn-<?php echo $statusBtnClass2;?> pull-right">All Closed Tickets</a> 
													</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">S.No</th>
																	<th scope="col">User Details</th>
																	<th scope="col">Subject</th>
																	<th scope="col">Message</th>
																	<th scope="col">Date</th>
																	<th scope="col">Status</th>
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
$pagiConfig['per_page_items'] = 10;
unset($_GET['page']);
$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?".http_build_query($_GET);
$orderBy = " ORDER BY a.`id` DESC";
$sql = "SELECT a.user_id,
		a.id,
		a.subject,
		a.message,
		a.is_active,
		a.created_at,
		b.name,
		b.cname,
		b.mobile
FROM `tickets` as a, add_cust as b where a.user_id = b.id ";	

	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$statusFilter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$statusFilter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
 $i = $pagination['offset'] + 1;
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
			?>
									<tr>
										<td class="sorting_1"><?php echo $i; ?></td>
										<td>
											<strong>Name : </strong><?php echo $rows["name"]; ?><br/>
											<strong>Mobile : </strong><?php echo $rows["mobile"]; ?><br/>
											<strong>Company : </strong><?php echo $rows["cname"]; ?>										
										</td>
										<td>
											<?php if($rows["subject"]=='1')
												{
													echo "Billing Enquiry";
												}
												elseif($rows["subject"]=='2')
												{
													echo "Sales Enquiry";
												}
												else
												{
													echo "Technical Support";
												}
											?>
										</td>
										<td>
											<?php echo $rows["message"]; ?>
										</td>
										<td style="width: 90px;"><?php echo $rows['created_at']; ?></td>
										<td style="width: 115px;">
											<span id="tdStatus_<?php echo $rows["id"]; ?>"><?php echo $rows['is_active']; ?></span><br/>
											<span style="color:blue">(<?=$helper->create_durations($rows['created_at'])." ago."?>)</span>
										</td>
										<td style="width: 90px;">
											<a href="javascript:void(0)" onclick="replyAndClose('<?php echo $rows["id"]; ?>')" class='btn btn-success btn-xs statusmsg'>Close/Reply</a><br/>
											<a class='btn btn-primary btn-xs statusmsg' href='tickets-chat.php?<?php echo "tid=".$rows["id"]; ?>' target='_blank'>View Chat</a>
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
<div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title w-100 font-weight-bold">Reply</h4>
      </div>
      <div class="modal-body mx-3">
	  <form id="replyForm" method="post">
        <div class="md-form mb-5">
          <label>Message</label>
          <textarea class="form-control " id="message" required ></textarea>
        </div>

        <div class="md-form mb-4">
		  <input type="checkbox" value="close" class="custom-control-input" id="close" checked>
		  <label class="custom-control-label" for="defaultChecked2">Close Dispute</label>
        </div>
		 <input type="hidden" value=""  id="tid" >
      </div>
      <div class="modal-footer d-flex justify-content-center">
        <span onclick="submitReply()" class="btn btn-primary pull-left">Submit</span>
        <button data-dismiss="modal" aria-label="Close" class="btn btn-danger">Cancel</button>
      </div>
	 </form>
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
		function replyAndClose(tid){
			$("#modalLoginForm").modal('show');
			$("#tid").val(tid);
		}
		function submitReply() {
			var tid = $("#tid").val();
			var message = $("#message").val();
			if ($('#close').is(":checked"))
			{
				var status = "Close";
			} else {
				var status = "Open";
			}
			if(message!=""){
				
				$.ajax({
					type: 'POST',
					data: {tid:tid, message:message, status:status },
					cache: false,
					url: 'ajax/ticket_reply.php',
					success: function (response)
					{ 
						$("#tdStatus_"+tid).html(response);
						$("#modalLoginForm").modal('hide');
					}
				});
				
			}			 
		}
		
</script>

</body>
</html>
</html>