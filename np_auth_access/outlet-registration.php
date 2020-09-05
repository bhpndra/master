<?php
	include_once('inc/head.php'); 
	include_once('inc/header.php');
	$mysqlObj = new mysql_class();
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
                                        <span> Outlet Registration </span>
                                    </li>
                                </ul>
                                <!-- END PAGE BREADCRUMBS -->
<?php
	if (isset($_POST['sendOtp'])){										
		$post = $helpers->clearSlashes($_POST);
		$mobile_no = $post['mobile'];
		
		if (!empty($mobile_no)) {
			$mobile = $helpers->validateMobile($mobile_no);
		
			if($mobile == FALSE) {
				$mobileError = "<font color='red'><p align='left'>Please Enter Valid Mobile No</p></font>";
			} else {
				$post_fields = array( "api_access_key" => $api_access_key,"mobile" => $mobile );
				$pan_register_otp = $helpers->netpaisa_curl($pan_otp_req_register, $post_fields);

				$pantdata = json_decode($pan_register_otp, true);
				//print_r( $pantdata);
				//on success
				if ($pantdata['DATA']['statuscode'] == "TXN"){
					echo "<script>
					$(document).ready(function(){
						$('#myModal1').modal('show');
						});
					</script>";	
					echo '<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<!-- Modal content-->
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
									<h4 class="modal-title">OTP CONFIRMATION</h4>
								</div>
								<form method="POST" >

									<div class="modal-body">
										<div class="row">

											<div class="col-md-12">
												<input type="text" required name="otp" id="otp" placeholder="Enter OTP" class="form-control" autocomplete="off">
											</div>

										</div>
									</div>

									<div class="modal-footer">
										<button type="submit" name="confirm_outlet_register_otp" id="confirm_outlet_register_otp" class="btn btn-primary">Verify OTP</button>
										<button type="button" class="btn btn-secondary" data-dismiss="modal" id="close_otp_modal">Close</button>
									</div>

								</form>
							</div>
						</div>
					</div>';
				}
				else{
					echo '<script type="text/javascript">';
					echo 'setTimeout(function () { swal("Error!","'.$pantdata['MSG'].'","error");';
					echo '}, 1000);</script>';
				}    
			}
		}
	} 
?>
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="portlet box blue">
											<div class="portlet-title">
												<div class="caption">
													<i class="fa fa-cogs"></i>  Outlet Registration  </div>
											</div>
											<div class="portlet-body">
													<div class="well">
													<div class="row show-grid">
														<div id="datatable_col_reorder_filter" class="dataTables_filter">
															<form action='' role="form" method='post'>
																<div class="col-md-12"><h3>Outlet Pan Registration</h3></div>
																<div class="form-group col-md-6">
																	<label>Select User</label>
<?php
$sql = "SELECT id, name, mobile FROM `add_cust` WHERE id not in (SELECT a.user_id FROM `add_retailer`  as a, `outlet_kyc` as b where a.user_id = b.user_id and b.sources = 'I') and status = 'ENABLED' and id in (SELECT user_id from add_retailer)";
$sqlQuery = $mysqlObj->mysqlQuery($sql);
?>
																	<div>
																		<select class="form-control select2" id="userid" >
																			<option value="">Select User</option>
																			<?php	while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){  ?>
																				<option value="<?=$rows['id']?>"><?=$rows['name']?> - (<?=$rows['mobile']?>)</option>
																			<?php } ?>
																		</select>
																	</div>
																</div>
																<div class="form-group col-md-3">
																	<label>Mobile Number</label>
																	<div>
																		<input type='text' name='mobile' id="mobile"  class='form-control' placeholder="Mobile Number" required>
																	</div>
																</div>
																<div class="form-group col-md-1">
																	<label style="opacity: 0;"> Get OTP</label>
																	<div>
																		<a href="javascript:void(0);" id="getOTP" class="btn btn-primary">Get OTP</a>
																	</div>
																</div>
																<div class="form-group col-md-2">
																	<label>Enter OTP</label>
																	<div>
																		<input type='text' name='otp' id="otp"  class='form-control' placeholder="Enter OTP" required>
																	</div>
																</div>
																<div class="col-md-12" style="margin-top:-14px; margin-bottom: 10px;" id="otpMessage"></div>		
											<div class="clearfix"></div>
                                            <div class="form-group col-md-6">
                                                <label>Name</label>
                                                <input type="text" required class="form-control" name="name" id="name">
                                            </div>
                                            
                                            <div class="form-group col-md-6">
                                                <label>Email Id</label>
                                                <input type="text" required class="form-control" name="email" id="email">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label>Company</label>
                                                <input type="text" required class="form-control" name="company" id="company">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label>Pan No</label>
                                                <input type="text" required class="form-control" name="pan_no" id="pan_no">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label>Pincode</label>
                                                <input type="text" required class="form-control" name="pincode" id="pincode">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label>Address</label>
                                                <input type="text" required class="form-control" name="address" id="address">
                                            </div>

                                            <div class="clearfix"></div>
                                            <hr>
                                            <div class="col-md-6">
                                                <input type="submit" name="register_pan" value="Register" id="register_pan" class="btn btn-primary"> 
                                            </div>
                                    
															</form>
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
                </div>
            </div>
        </div>
<?php include_once('inc/footer.php'); ?>
<script>
jQuery(document).ready(function () {

    $("#getOTP").click(function (e) {
        
		var mobile = $("#mobile").val(); 
		$.ajax({
				type: 'POST',
				data: {mobile: mobile, action: 'sendOtp'},
				cache: false,
				url: 'ajax/outlet_pan_registration.php',
				dataType: 'html',
				success: function (response){
					$("#otpMessage").html(response);
				}
		});	
    });

    $("#register_pan").click(function (e) {
        e.preventDefault();
        var mobile = $("#mobile").val();
        var name = $("#name").val();
        var email = $("#email").val();
        var company = $("#company").val();
        var pan_no = $("#pan_no").val();
        var pincode = $("#pincode").val();
        var address = $("#address").val();
        var otp = $("#otp").val();
        var agent_userid = $("#userid").val();

        var panData = { name: name, email: email, company: company, pan_no: pan_no, pincode: pincode, address: address, otp: otp, mobile: mobile, agent_userid: agent_userid, action: 'outlet'};

        if( name == '' || email == '' || company == '' || pan_no == '' || pincode == '' || address == '' || agent_userid =='' )
        {
            swal({
                title: "Please fill all details!",
                type: "error",
                html: true,
                allowEscapeKey: false
            });
        }
        else
        {  
            swal({
                    title: "Confirm Outlet Information",
                    text: "Mobile : " + mobile + "<br>" + "Name : " + name + "<br>" + "EmailId  : " + email + "<br>" + "Company : " + company + "<br>" + "PAN No : " + pan_no + "<br>" + "Pincode : " + pincode + "<br>" + "Address : " + address,
                    type: "warning",
                    html: true,
                    showCancelButton: true,
                    confirmButtonColor: "green",
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel",
                    closeOnConfirm: false,
                    closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {

                    swal({
                        title: "Please Wait",
                        text: "Your request is being processed",
                        type: "info",
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });

                    $.ajax({
                        type: 'POST',
                        data: panData,
                        cache: false,
                        url: 'ajax/outlet_pan_registration.php',
                        dataType: 'html',
                        success: function (response)
                        {
                            console.log(response)
                            if (response.search("Error") < 1)
                            { 
                                swal({
                                    title: "Success",
                                    text: "Registeration Outlet Successfully",
                                    type: "success",
                                    html: true,
                                    closeOnConfirm: false,
                                    timer: 3000
                                });

                                //window.location = "<?=BASE_URL  ?>"+"r_admin/outlet_pan_status.php";

                            } else { 

                                swal({
                                    title: 'Registeration Failed',
                                    text: response,
                                    confirmButtonColor: "#2196F3",
                                    html: true,
                                    type: "error"
                                });
                            }
                        },
                        error: function ()
                        {
                            swal({
                                title: response,
                                type: "error",
                                html: true,
                                allowEscapeKey: false
                            });
                        }


                    });  
                } else {
                    swal("Cancelled", "Transaction successfully cancelled:)", "error");
                }

            }); 
               
        } 
        
    });        
            

              

});
</script>
</body>
</html>