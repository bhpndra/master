<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">AEPS</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">AEPS</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
<?php 
	$url = BASE_URL . "/api/aeps/outlet-details.php";
	$post_fields = array("token" => $_SESSION['TOKEN']);
	$resAPI_Json = api_curl($url, $post_fields, $headerArray);
	$resDetails = json_decode($resAPI_Json, true);

	$url2 = BASE_URL . "/api/aeps/aeps2-outlet-details.php";
	$post_fields2 = array("token" => $_SESSION['TOKEN']);
	$resAPI_Json2 = api_curl($url2, $post_fields2, $headerArray);
	$resDetails2 = json_decode($resAPI_Json2, true);
?>	    
		<div class="row">
			<div class="col-md-5">				
				<?php if(isset($resDetails['ERROR_CODE']) && $resDetails['ERROR_CODE'] == 0){ ?>
				<div class="info-box "> 
					<div class="row">
						<div class="info-box-content col-8" style="flex: auto;">
							<span class="info-box-text">Hi <strong><?=$resDetails['DATA']['name']?></strong></span>
							<span class="info-box-number">Outlet Id: <?=$resDetails['DATA']['outletid']?></span>						
						</div>
						<div class="info-box-content col-4 text-right" style="flex: auto;">
							<span class="info-box-number">AePS Wallet<br><i class="fas fa-rupee-sign text-success"></i> <?=$resDetails['DATA']['balance']?></span>
						</div>   
						<?php if(isset($resDetails['DATA']['outlet_kyc']) &&  $resDetails['DATA']['outlet_kyc']==1){ ?>
						<div class="col-6">
						  <a href="inst-aeps-process" target="_blank" class="btn btn-sm btn-success">Transaction</a>
						</div>
						<div class="col-6">
						  <a href="aeps-settlement" onclick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=SomeSize,height=SomeSize'); return false;" class="btn btn-sm btn-default float-right">Balance Withdrawal</a>
						</div>
						<?php } else { ?>
						<div class="col-12">
						  <a href="javascript:void(0)" onclick="submit_kyc()" class="btn btn-sm btn-success">Submit KYC</a>
						  <a href="javascript:void(0)" onclick="check_outlet_kyc_status('<?=$resDetails['DATA']['outletid']?>','<?=$resDetails['DATA']['pan_no']?>')" class="btn btn-sm btn-primary ml-2">Check Status</a>
						</div>
						<?php } ?>
					</div>
					<!-- /.info-box-content -->
				</div>
				<?php } ?>
				<?php if(isset($resDetails['ERROR_CODE']) && $resDetails['ERROR_CODE'] != 0){ ?>
				<div class="info-box p-3">
					<div class="col-12 text-center">
					  <button class="btn btn-danger">Outlet not created. Please do agent registration first</button>
					  <button class="btn btn-success mt-2"  data-toggle="modal" data-target="#modal-default">AePS Agent Registration</button>
					</div>
					<!-- /.info-box-content -->
				</div>
      <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Outlet Registration</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action='' role="form" method='post' class="row">
				<div class="form-group col-md-6">
					<label>Mobile Number</label>
					<div>
						<input type='text' name='mobile' id="mobile" value="" class='form-control' placeholder="Mobile Number" required>
					</div>
				</div>
				<div class="form-group col-md-3">
					<label style="opacity: 0;"> Get OTP</label>
					<div>
						<a href="javascript:void(0);" id="getOTP" class="btn btn-primary">Get OTP</a>
					</div>
				</div>
				<div class="form-group col-md-3">
					<label>Enter OTP</label>
					<div>
						<input type='text' name='otp' id="otp" value=""  class='form-control' placeholder="Enter OTP" required>
					</div>
				</div>
				<div class="col-md-12" style="margin-top:-14px; margin-bottom: 10px;" id="otpMessage"></div>		
					<div class="clearfix"></div>
					<div class="form-group col-md-6">
						<label>Name</label>
						<input type="text" required class="form-control" value="" name="name" id="name">
					</div>
					<div class="form-group col-md-6">
						<label>Email Id</label>
						<input type="text" required class="form-control" value="" name="email" id="email">
					</div>
					<div class="form-group col-md-6">
						<label>Company</label>
						<input type="text" required class="form-control" value="" name="company" id="company">
					</div>
					<div class="form-group col-md-6">
						<label>Pan No</label>
						<input type="text" required class="form-control" value="" name="pan_no" id="pan_no">
					</div>
					<div class="form-group col-md-8">
						<label>Address</label>
						<input type="text" required class="form-control" value="" name="address" id="address">
					</div>
					<div class="form-group col-md-4">
						<label>Pincode</label>
						<input type="text" required class="form-control" value="" name="pincode" id="pincode">
					</div>
					</form>
				</div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <input type="submit" name="register_pan" value="Register" id="register_pan" class="btn btn-primary">
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
				<?php } ?>
				
<?php if(isset($resDetails2['ERROR_CODE']) && $resDetails2['ERROR_CODE'] == 1){ ?>	  
		<div class="info-box p-3">
			<div class="col-12 text-center">
			  <button class="btn btn-danger">AEPS 2 Registeration (For Mobile App / Web)</button>
			  <button class="btn btn-success mt-2"  data-toggle="modal" data-target="#modal-aeps2">AEPS 2 Agent Registration</button>
			</div>
			<!-- /.info-box-content -->
		</div>
	 <div class="modal fade" id="modal-aeps2">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">AEPS2 Outlet Registration</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action='' role="form" method='post' class="row">
				<div class="col-md-12" style="margin-top:-14px; margin-bottom: 10px;" id="otpMessage"></div>		
					<div class="clearfix"></div>
					<div class="form-group col-md-6">
						<label>First Name <span style="color:red">*</span></label>
						<input type="text" required class="form-control" value="" name="first_name" id="first_name">
					</div>
					<div class="form-group col-md-6">
						<label>Middle Name</label>
						<input type="text" required class="form-control" value="" name="middle_name" id="middle_name">
					</div>
					<div class="form-group col-md-6">
						<label>Last Name <span style="color:red">*</span></label>
						<input type="text" required class="form-control" value="" name="last_name" id="last_name">
					</div>
					<div class="form-group col-md-6">
						<label>Email Id</label>
						<input type="text" required class="form-control" value="" name="email" id="email2">
					</div>
					<div class="form-group col-md-6">
						<label>Pan No <span style="color:red">*</span></label>
						<input type="text" required class="form-control" value="" name="pan_no" id="pan_no2">
					</div>
					<div class="form-group col-md-6">
						<label>Mobile <span style="color:red">*</span></label>
						<input type="text" required class="form-control" value="" name="mobile" id="mobile2">
					</div>
					</form>
				</div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <input type="submit" name="register_aeps2" value="Register" id="register_aeps2" class="btn btn-primary">
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>			
				
<?php } ?>			
<?php
	if(isset($resDetails2['ERROR_CODE']) && $resDetails2['ERROR_CODE'] == 0){
		if(isset($resDetails2['DATA']['outlet_status']) && $resDetails2['DATA']['outlet_status'] == 'PENDING'){
?>	  
		<div class="info-box p-3">
			<div class="col-12 text-center">
			  <button class="btn btn-warning"  onclick="submit_kyc2()">Submit AEPS2  KYC</button>
			</div>
			<!-- /.info-box-content -->
		</div>
		<?php } else { ?>
		<div class="info-box p-3">
			<div class="col-12 text-center">
			  <button class="btn btn-sm btn-warning mb-2"  onclick="submit_kyc2()">AEPS2  KYC Details</button></br>
<form action="http://45.114.245.112:7081/AEPS/login" method="get" target="_blank">
<?php
		   $url = 'http://45.114.245.112:7081/AEPS/generatetoken';
			//create a new cURL resource
			$ch = curl_init($url);
			//setup request to send json via POST
			$data = array(
				'agentAuthId' => md5('RISEIN TECH PRIVATE LIMITED-RASHI052534'),
				'agentAuthPassword' => md5('gz9lft4fxa'),
				'retailerId' => $resDetails2['DATA']['outletid'],
				'apiId' => '10055'
			);
			$payload = json_encode($data);
			//attach encoded JSON string to the POST fields
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			//set the content type to application/json
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			//return response instead of outputting
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//execute the POST request
			$result = curl_exec($ch);
			curl_close($ch);
			$result_arr = json_decode($result,true);
			//echo "<pre>"; print_r($result_arr); 
			$bankit_token = !empty($result_arr['data']['token'])?$result_arr['data']['token']:'';
		?>
		
		<input type="hidden" name="token" value="<?php echo urldecode($bankit_token); ?>">
		<input class="btn btn-sm btn-success mb-2" type="submit" value="Transaction">
	</form>
		<a href="aeps-settlement" onclick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=SomeSize,height=SomeSize'); return false;" class="btn btn-sm btn-default">Balance Withdrawal</a>
			</div>
			<!-- /.info-box-content -->
		</div>
		<?php } ?>	
<?php } ?>		
				<div class="card card-default">
					<div class="card-header">
					  <h3 class="card-title">INSTRUCTION & DOWNLOAD'S</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<div class="card-body">
						To run the device successfully for Aadhaar Biometric Authentication using fingerprint,
						please follow the respective instructions against each device as under:
						<h5>For Mantra device</h5>
						1. To enable the device for RD Services, please take up with Mantra vendor for support.<br>
						2. For installation, please follow the user manual <a href="https://download.mantratecapp.com/StaticDownload/Mantra_RD_Service_Manual_Windows.pdf" target="_blank">Click Here</a><br>
						3. For verification of RD Service and access to the installed device, please visit
						the link <a href="https://rdtest.aadhaardevice.com" target="_blank">Click Here</a><br>
						4. Click on the 'Discover AVDM' button to refresh the list of available Registered
						Devices with Ready status.<br>
						<br>
						<h5>For Morpho device</h5>
						To enable the device for RD Services, please take up with Morpho vendor for support.<br>
					</div>
				 </div>
			</div>
			<div class="col-md-7">
				<div class="info-box">
					<div class="info-box-content">
						<h3>
							Introduction:
							<img alt="aeps" src="<?=DOMAIN_NAME?>dashboard/dist/img/aeps.png" class="img img-responsive float-right">
						</h3>
						<p>
							AEPS is a bank led model which allows online interoperable financial
							transaction at PoS (Point of Sale / Micro ATM) through the Business Correspondent
							(BC)/Bank Mitra of any bank using the Aadhaar authentication.</p>
						<p>
							The only inputs required for a customer to do a transaction under this scenario
							are:-
							</p><ul>
								<li>IIN (Identifying the Bank to which the customer is associated) </li>
								<li>Aadhaar Number</li>
								<li>Customer Mobile Number</li>
								<li>Fingerprint captured during their enrollment</li>
							</ul>
							<p>
							</p>
							<div class="row">
								<div class="col-md-6">
									<h5>Services Offered:</h5>
									<ul>
										<li>Balance Enquiry </li>
										<li>Cash Withdrawal</li>
										<li>Mini Statement</li>
									</ul>
								</div>
								<div class="col-md-6">
									<h5>Available Operators:</h5>
									<ul>
										<li>118 Banks</li>
										<li>Interoperable</li>
									</ul>
								</div>
							</div>
							<h4>
								Transaction Cost:</h4>
							<ul>
								<li>NIL to customer </li>
								<li>Merchant or BC may get charged or paid based on bank‘s discretion</li>
							</ul>
							<span class="text-orange"><b>Disclaimer:</b> The transaction costs are based on available
								information and may vary based on banks.</span>
							<h4>
								Funds Transfer limit:</h4>
							<ul>
								<li>Banks define limit. No limit for RBI.</li>
							</ul>
							<span class="text-orange"><b>Disclaimer:</b> The funds transfer limits are based on
								available information and may vary based on banks.</span>
						<p></p>
					</div>
				</div>
			</div>
		</div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
<script>
jQuery(document).ready(function () {
    $("#getOTP").click(function (e) {
		var mobile = $("#mobile").val(); 
        if(mobile==''){
			Swal.fire({
                title: "Enter mobile number!",
                type: "error",
                allowEscapeKey: false
            });
			return false;
		}
		$.ajax({
				type: 'POST',
				data: {mobile: mobile, action: 'sendOtp'},
				cache: false,
				url: 'ajax/aeps/get_otp.php',
				dataType: 'html',
				success: function (response){
					var data = JSON.parse(response);
					$("#otpMessage").html("<span style='color:green'>" + data.msg + "</span>");
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
        if( name == '' || email == '' || company == '' || pan_no == '' || pincode == '' || address == '' || agent_userid =='' || otp == '' )
        {
           Swal.fire({
                title: "Please fill all details!",
                type: "error",
                allowEscapeKey: false
            });
        }
        else
        {  
            Swal.fire({
                    title: "Confirm Outlet Information",
                    html: "Mobile : " + mobile + "<br>" + "Name : " + name + "<br>" + "EmailId  : " + email + "<br>" + "Company : " + company + "<br>" + "PAN No : " + pan_no + "<br>" + "Pincode : " + pincode + "<br>" + "Address : " + address,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "green",
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel",
                    closeOnConfirm: false,
                    closeOnCancel: true
            }).then((result) => {
                if (result.value) {
                    Swal.fire({
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
                        url: 'ajax/aeps/aeps_registration_request.php',
                        dataType: 'text',
                        success: function (response)
                        {
                            console.log(response);							
							var data = JSON.parse(response);
                            if (data.status == 0) { 
                                Swal.fire({
                                    title: "Success",
                                    text: "Registeration Outlet Successfully",
                                    type: "success",
                                    closeOnConfirm: false,
                                    timer: 3000
                                }).then((result) => {
									if (result.value) {
										location.reload();
									  }
								});	
                                //window.location = "<?=BASE_URL  ?>"+"r_admin/outlet_pan_status.php";
                            } else { 
                                Swal.fire({
                                    title: 'Registeration Failed',
                                    text: data.msg,
                                    confirmButtonColor: "#2196F3",
                                    type: "error"
                                }).then((result) => {
									if (result.value) {
										location.reload();
									  }
								});	
                            }
                        }
                    });  
                } else {
                    //swal("Cancelled", "Transaction successfully cancelled:)", "error");
                }
            }); 
               
        } 
        
    });        
            
});
function check_outlet_kyc_status(outletid,pan_no){ //alert(outletid);
	$.ajax({
		type: 'POST',
		data: {outletid: outletid, pan_no: pan_no},
		cache: false,
		url: 'ajax/aeps/outlet_registration_status.php',
		success: function (response){
			var data = JSON.parse(response);
			var htmlTxt = "<strong>" + data.msg + "</strong>";
			if(data.status == 1){
				htmlTxt = htmlTxt + "<br/>Reason: " + data.reason;
			}
			Swal.fire({
				title: "KYC Status",
				html: htmlTxt,
				closeOnConfirm: false
			});
		}
	}); 
}
function submit_kyc(){
	var url = '<?=DOMAIN_NAME?>retailer/aeps-kyc-submit' ;
	window.open(url,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=SomeSize,height=SomeSize');
}
</script>
<script>
jQuery(document).ready(function () {
    $("#register_aeps2").click(function (e) {
        e.preventDefault();
        var mobile = $("#mobile2").val();
        var first_name = $("#first_name").val();
        var middle_name = $("#middle_name").val();
        var last_name = $("#last_name").val();
        var email = $("#email2").val();
        var pan_no = $("#pan_no2").val();
        var panData = { first_name: first_name, middle_name: middle_name, last_name: last_name, mobile: mobile, pan_no: pan_no, email: email};
        if( first_name == '' || last_name == '' || pan_no == '' || mobile == '' )
        {
           Swal.fire({
                title: "Please fill all required fields!",
                type: "error",
                allowEscapeKey: false
            });
        }
        else
        {  
            Swal.fire({
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
				url: 'ajax/aeps/aeps2_registration.php',
				dataType: 'text',
				success: function (response)
				{
					console.log(response);							
					var data = JSON.parse(response);
					if (data.status == 0) { 
						Swal.fire({
							title: "Success",
							text: "Registeration Outlet Successfully",
							type: "success",
							closeOnConfirm: false,
							timer: 3000
						}).then((result) => {
							if (result.value) {
								location.reload();
							  }
						});	
						//window.location = "<?=BASE_URL  ?>"+"r_admin/outlet_pan_status.php";
					} else { 
						Swal.fire({
							title: 'Registeration Failed',
							text: data.msg,
							confirmButtonColor: "#2196F3",
							type: "error"
						}).then((result) => {
							if (result.value) {
								location.reload();
							  }
						});	
					}
				}
			}); 
               
        } 
        
    });        
            
});
function check_outlet_kyc_status(outletid,pan_no){ //alert(outletid);
	$.ajax({
		type: 'POST',
		data: {outletid: outletid, pan_no: pan_no},
		cache: false,
		url: 'ajax/aeps/outlet_registration_status.php',
		success: function (response){
			var data = JSON.parse(response);
			var htmlTxt = "<strong>" + data.msg + "</strong>";
			if(data.status == 1){
				htmlTxt = htmlTxt + "<br/>Reason: " + data.reason;
			}
			Swal.fire({
				title: "KYC Status",
				html: htmlTxt,
				closeOnConfirm: false
			});
		}
	}); 
}
function submit_kyc2(){
	var url = '<?=DOMAIN_NAME?>retailer/aeps2-kyc-submit' ;
	window.open(url,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=SomeSize,height=SomeSize');
}
</script>
</html>