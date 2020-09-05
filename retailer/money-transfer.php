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
                    <h1 class="m-0 text-dark">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Money Transfer</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <?php include("inc/service_box.php"); ?>
            <?php if(isset($_GET['mobile'])){
                $circle_url = BASE_URL . "/api/dmt/remitter_details.php";
                $post_fields = array("token" => $_SESSION['TOKEN'], "mobile" => $_GET['mobile']);
                $resRIMDET = api_curl($circle_url, $post_fields, $headerArray);
                $remitDetails = json_decode($resRIMDET, true);
                if (isset($remitDetails['ERROR_CODE']) && $remitDetails['ERROR_CODE'] == 0 
					&& isset($_GET['mobile']) && !empty($_GET['mobile']) 
					&& isset($remitDetails['REMITTER_DETAILS']['mobile']) && $remitDetails['REMITTER_DETAILS']['mobile'] == $_GET['mobile']) {
						
                    echo "<script> window.location = 'dmt-remitter-details?mobile=".$_GET['mobile']."'; </script>"; die();
                }
            }
            ?>
			
            <?php  if (isset($_GET['mobile']) && isset($remitDetails['REMITTER_DETAILS']['is_verified']) && $remitDetails['REMITTER_DETAILS']['is_verified'] == 0 && isset($remitDetails['REMITTER_DETAILS']['id'])) { ?>           
            
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card card-info">
                            <div class="card-header">
                              <h3 class="card-title">Verification Pending (<?=$remitDetails['MESSAGE']?>)</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form class="form-horizontal" method="post">
                              <div class="card-body">
                                <div class="form-group row">
                                  <label class="col-sm-3 col-form-label">Enter OTP</label>
                                  <div class="col-sm-9">
                                    <input type="number" min='1' class="form-control" placeholder="Enter OTP" id="otp">
                                    <input type="hidden" min='1' class="form-control" id="mobile" value="<?=$_GET['mobile']?>">
                                    <input type="hidden" min='1' class="form-control" id="remitterid" value="<?=$remitDetails['REMITTER_DETAILS']['id']?>" />
                                  </div>
                                </div>
                              </div>
                              <!-- /.card-body -->
                              <div class="card-footer">
                                <a href="javascript:void(0)" onclick="verify_retmitter()" class="btn btn-info ">Submit</a>
                              </div>
                              <!-- /.card-footer -->
                            </form>
                          </div>
                    </div>
                </div>
            <?php } 
					else if (!isset($_GET['mobile']) && empty($_GET['mobile'])) { 
			?>           
            
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card card-info">
                            <div class="card-header">
                              <h3 class="card-title">Login Remitter Wallet</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form class="form-horizontal" method="get">
                              <div class="card-body">
                                <div class="form-group row">
                                  <label class="col-sm-2 col-form-label">Mobile</label>
                                  <div class="col-sm-10">
                                    <input type="number" min='1' class="form-control" placeholder="Mobile" name="mobile">
                                  </div>
                                </div>
                              </div>
                              <!-- /.card-body -->
                              <div class="card-footer">
                                <button type="submit" class="btn btn-info ">Login In</button>
                              </div>
                              <!-- /.card-footer -->
                            </form>
                          </div>
                    </div>
                </div>
            <?php } 
				else if (isset($remitDetails['ERROR_CODE']) && $remitDetails['ERROR_CODE'] == 1 && isset($remitDetails['MESSAGE']) && $remitDetails['MESSAGE'] =="Remitter Not Found.") { 
			?>
            
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header">
                              <h3 class="card-title">Remitter Wallet Registration</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->							
                        <div class="overlay" id="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                            <form class="form-horizontal" method="post" id="regRemit">
                              <div class="card-body">
                                <div class="form-group row">
                                  <label class="col-sm-2 col-form-label">Mobile</label>
                                  <div class="col-sm-10">
                                    <input type="number" min='1' class="form-control" placeholder="Mobile" id="mobile" value="<?=$_GET['mobile']?>" required />
                                  </div>
                                </div>
                                <div class="form-group row">
                                  <label class="col-sm-2 col-form-label">Name</label>
                                  <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Name" id="name" required />
                                  </div>
                                </div>
                                <div class="form-group row">
                                  <label class="col-sm-2 col-form-label">Surname</label>
                                  <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Surname" id="surname" required />
                                  </div>
                                </div>
                                <div class="form-group row">
                                  <label class="col-sm-2 col-form-label">PinCode</label>
                                  <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Pin Code" id="pincode" required />
                                  </div>
                                </div>
                              </div>
                              <!-- /.card-body -->
                              <div class="card-footer">
                                <a href="javascript:void(0)" onclick="register_retmitter()" class="btn btn-info ">Submit</a>
                                <a href="money-transfer" class="btn btn-default float-right">Cancel</a>
                              </div>
                              <!-- /.card-footer -->
                            </form>
                          </div>
                    </div>
                </div>
                
            <?php } else { echo $remitDetails['MESSAGE']; } ?>
             

        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<?php include("inc/footer.php"); ?>
<script>
    function verify_retmitter() {
        var otp = $("#otp").val();
        var mobile = $("#mobile").val();
        var remitterid = $("#remitterid").val();
        if(otp=='' || mobile=='' || remitterid==''){ alert('Fill all feilds'); return false; }
        $("#loading").css("display", "flex");
        
			$.ajax({
			url: '<?= DOMAIN_NAME ?>retailer/ajax/dmt/remitter-registration-validate.php',
			type: 'POST',
			cache: false,
			data: {remitterid: remitterid, mobile: mobile, otp: otp},
			dataTpe: "json",
			success: function (response)
			{
				//console.log(response);
				var res1 = JSON.parse(response);
				if (res1.status == "0") {
					Swal.fire({
						title: "Registration Successfully.",                                                    
						showCancelButton: false,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Ok",
						closeOnConfirm: false
					}).then((result) => {
						if (result.value) {
							window.location = "dmt-remitter-details?mobile=" + mobile;
						}
					});
				} else {
					Swal.fire({
						title: res1.msg,
						type: "error",
						showCancelButton: false,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Ok",
						closeOnConfirm: false
					});
				}
			}
		});
	}
	
    function register_retmitter() {
        var name = $("#name").val();
        var mobile = $("#mobile").val();
        var pincode = $("#pincode").val();
        var surname = $("#surname").val();
        if(name=='' || mobile=='' || pincode=='' || surname==''){ alert('Fill all feilds'); return false; }
        $("#loading").css("display", "flex");
        
			/* ------------------------------------------------------ */
			$.ajax({
				url: '<?= DOMAIN_NAME ?>retailer/ajax/dmt/remitter-registration.php',
				type: 'POST',
				cache: false,
				data: {name: name, mobile: mobile, pincode: pincode, surname: surname},
				dataTpe: "json",
				success: function (response)
				{
					//console.log(response);
					var res = JSON.parse(response);
					var remitterid = res.remitterid;
					if (res.status == "0" && res.is_verified == "0") {
						Swal.fire({
							title: "Enter OTP",
							input: 'text',
							inputAttributes: {
								autocapitalize: 'off'
							},
							showCancelButton: true,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "Submit",
							closeOnConfirm: false
						}).then((result) => {
							if (result.value) {
								$.ajax({
									url: '<?= DOMAIN_NAME ?>retailer/ajax/dmt/remitter-registration-validate.php',
									type: 'POST',
									cache: false,
									data: {remitterid: remitterid, mobile: mobile, otp: result.value},
									dataTpe: "json",
									success: function (response)
									{
										//console.log(response);
										var res1 = JSON.parse(response);
										if (res1.status == "0") {
											Swal.fire({
												title: "Registration Successfully.",                                                    
												showCancelButton: false,
												confirmButtonColor: "#DD6B55",
												confirmButtonText: "Ok",
												closeOnConfirm: false
											}).then((result) => {
												if (result.value) {
													window.location = "dmt-remitter-details?mobile=" + mobile;
												}
											});
										} else {
											Swal.fire({
												title: res1.msg,
												type: "error",
												showCancelButton: false,
												confirmButtonColor: "#DD6B55",
												confirmButtonText: "Ok",
												closeOnConfirm: false
											});
										}
									}
								});
							}
						});
					} else {
						Swal.fire({
							title: res.msg,
							type: "error",
							showCancelButton: false,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "Ok",
							closeOnConfirm: false
						});
					}
					$("#loading").css("display", "none");
				}
			});
			/* ------------------------------------------------------ */
            $("#loading").css("display", "none");
           
        
    }

</script>
<script>
    $(document).ready(function () {
        $("#loading").css("display", "none");
        $('#beneficiaryList').DataTable({
            lengthChange: false,
            paging: true,
            searching: true,
            ordering: false,
            info: false
        });
    });
</script>

</html>
