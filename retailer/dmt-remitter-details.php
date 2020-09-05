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
            <?php if(isset($_GET['mobile'])){
                $circle_url = BASE_URL . "/api/dmt/remitter_details.php";
                $post_fields = array("token" => $_SESSION['TOKEN'], "mobile" => $_GET['mobile']);
                $resRIMDET = api_curl($circle_url, $post_fields, $headerArray);
                $remitDetails = json_decode($resRIMDET, true);
            }
			if(!isset($remitDetails['REMITTER_DETAILS']['mobile']) || $remitDetails['REMITTER_DETAILS']['mobile'] != $_GET['mobile']){
				 echo "<script> window.location = 'money-transfer'; </script>"; die();
			}
            ?>

            <?php
            if (isset($remitDetails['ERROR_CODE']) && $remitDetails['ERROR_CODE'] == 0 && isset($_GET['mobile']) && !empty($_GET['mobile'])) {
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">              
                            <div class="info-box-content col-6">
                                <span class="info-box-text">Hi <strong><?= $remitDetails['REMITTER_DETAILS']['name'] ?></strong></span>
                                <span class="info-box-number">Mobile: <?= $remitDetails['REMITTER_DETAILS']['mobile'] ?></span>
                                <a href="money-transfer" class="btn btn-sm btn-default" >Exit Wallet &nbsp;<i class="fas fa-external-link-alt" style="opacity: 0.6;"></i></a>
                            </div>              
                            <div class="info-box-content col-6 text-right">
                                <span class="info-box-number">Wallet Limit<br/><i class="fas fa-rupee-sign text-success"></i> 25000</span>
                                <span class="info-box-number">Remaining Limit<br/><i class="fas fa-rupee-sign text-success"></i> <?= $remitDetails['REMITTER_DETAILS']['remaininglimit'] ?></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <div class="card card-warning" id="addBeneBox" style="display:none">
                            <div class="card-header">
                                <h3 class="card-title">Add Beneficiary</h3>
                                <div class="card-tools">
                                    <a href="javascript:void(0)" onclick="beneList()" class="btn btn-sm btn-primary text-white" >Beneficiary List</a>
                                </div>
                            </div>
                            <div class="overlay loading" ><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                            <div class="card-body">                                
								<div class="form-group">
								  <label>Remmiter Mobile .:</label>
									<input type="text" class="form-control" maxlength="10" id="mobile" readonly value="<?= $remitDetails['REMITTER_DETAILS']['mobile']?>" />
									<input type="hidden" class="form-control" id="remitterid" value="<?= $remitDetails['REMITTER_DETAILS']['id']?>"/>
								  <!-- /.input group -->
								</div>
								
								<div class="form-group">
								  <label>Account No.:</label>
									<input type="text" class="form-control" id="account" />
								  <!-- /.input group -->
								</div>
<?php 
		$bankdetail_url = BASE_URL . "/api/dmt/bank_details.php";
		$postfields = array("token" => $_SESSION['TOKEN']);
		$resBanks = api_curl($bankdetail_url, $postfields, $headerArray);
		$banksDetails = json_decode($resBanks, true);
?>			
								<div class="form-group">
								  <label>Select Bank:</label>
								  <select class="form-control select2" style="width: 100%;" id="bank" onchange="selectBank(this)" >
									<option selected="selected">Select Bank</option>
									<?php
										if(isset($banksDetails['DATA']) && count($banksDetails['DATA']) > 0){
											foreach($banksDetails['DATA'] as $bank){
												$disabled = ($bank['is_down']==1) ? 'disabled' : '';
												echo "<option ".$disabled." value='".$bank['branch_ifsc']."' data-name='".$bank['bank_name']."'>".$bank['bank_name'] ."- (".$bank['bank_sort_name'].")"."</option>";
											}
										}
									?>
								  </select>
								</div>
								
								<div class="form-group" id="ifscMannualBox" style="display:none">
								  <label>IFSC.:</label>
									<input type="text" class="form-control" id="ifscMannual" />
								  <!-- /.input group -->
								</div>
								
								<div class="row">							
									<div class="form-group col-9">
									  <label>Account Holder Name.:</label>
										<input type="text" class="form-control" id="name" />
									  <!-- /.input group -->
									</div>	
									<div class="form-group col-3">
									  <label style="opacity:0">:</label>
										<a href="javascript:void(0)" onclick="get_bene_name(this)" class="form-control btn btn-primary" >Get Name</a>
									  <!-- /.input group -->
									</div>	
								</div>	
                            </div>
							  <div class="card-footer">
								<button type="submit" onclick="add_beneficiary(this)" class="btn btn-success">Add Beneficiary</button>
							  </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
						<div class="card card-danger" id="beneListBox" >
                            <div class="card-header">
                                <h3 class="card-title">Beneficiary List</h3>
                                <div class="card-tools">
                                    <a href="javascript:void(0)" onclick="addBene()" class="btn btn-sm btn-dark" ><i class="fas fa-plus"></i> Add Beneficiary</a>
                                </div>
                            </div>
                            <div class="overlay loading" ><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                            <div class="card-body">
                                <div class="table-responsive1">
                                    <table  class="table"  id="beneficiaryList">
                                        <thead>
                                        <th></th>
                                        <th></th>
                                        </thead>

                                        <tbody>
                                            <?php
                                            foreach ($remitDetails['BENEFICIARY'] as $row_b) {
                                                ?>
                                                <tr data-name="<?= $row_b['name'] ?>" data-account="<?= $row_b['account'] ?>" data-ifsc="<?= $row_b['ifsc'] ?>" data-bank="<?= $row_b['bank'] ?>" data-benefiId="<?= $row_b['id'] ?>" data-remitterId="<?= $remitDetails['REMITTER_DETAILS']['id'] ?>" data-remaininglimit="<?= $remitDetails['REMITTER_DETAILS']['remaininglimit'] ?>" data-remitterMobile="<?= $remitDetails['REMITTER_DETAILS']['mobile'] ?>">
                                                    <td>
                                                        <strong><?= $row_b['name'] ?></strong>
														<button type="button" class="btn btn-sm btn-danger btnTrash" onclick="remove_beneficiary(this)"><i class="fas fa-trash-alt"></i></button><br/>
                                                        <span class="text-small ">A/C No.: <?= $row_b['account'] ?></span><br/>
                                                        <span class="text-small ">IFSC: <?= $row_b['ifsc'] ?></span><br/>
                                                        <span class="text-small ">Bank: <?= $row_b['bank'] ?></span><br/>
                                                        <span class="text-small text-success">Last Success: <?= $row_b['last_success_date'] ?></span><br/>
                                                    </td>
                                                    <td class="text-right">
                                                        <input type="number" max="5000" min="11" class="amount" name="amount"/><br/>
                                                        <button type="button" class="btn btn-sm btn-primary mt-1" onclick="transefer_money(this)">Transfer</button>
                                                        <br/>
                                                        <?php if ($row_b['status'] == 1) { ?>
                                                            <a href="#" class="text-success"><i class="fas fa-check-square mt-1"></i> Verified</a>
                                                        <?php } else { ?>
                                                            <a href="#" class="text-danger"  onclick="verify_beneficiary(this)"><i class="fas fa-exclamation mt-1"></i> Click To Verify</a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            <!-- /.card-body -->
                        </div>
							<div class="card-footer">
								<div class="alert alert-info text-small">
									<b><i class="fa fa-warning"></i>&nbsp;Note:</b> Please don't refresh browser and
									don't press back button until transaction were complete. Kindly wait until popup
									appears. कृपया ब्राउज़र को रीफ़्रेश न करें और लेन-देन पूरा होने तक वापस बटन न दबाएँ।
									पॉपअप दिखाई देने तक कृपया प्रतीक्षा करें।
								</div>
							</div>
                      </div>
					</div>
                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header border-transparent">
                                <h3 class="card-title">Latest Money Transfer</h3>					
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table  class="table m-0 text-small">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Amount</th>
                                                <th>A/C</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $dmt_url = BASE_URL . "/api/dmt/report.php";                                        
                                        $post_fields = array("token" => $_SESSION['TOKEN'], "limit" => 10, 'mobile' => $remitDetails['REMITTER_DETAILS']['mobile']);
                                        $responseRC = api_curl($dmt_url, $post_fields, $headerArray);
                                        $resRC = json_decode($responseRC, true);
                                        ?>
                                        <tbody>
                                            <?php
                                            if ($resRC['ERROR_CODE'] == 0 && isset($resRC['DATA']) && count($resRC['DATA'])>0) {
                                                foreach ($resRC['DATA'] as $row) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $row['agent_trid'] ?><br/>
														<a class="text-small" href="javascript:void(0)" ><?= $row['ref_no'] ?></a>
														</td>
                                                        <td><?= $row['amount'] ?></td>
                                                        <td><?= $row['bene_ac'] ?><br/>
														<span class="text-small" ><?= $row['bene_name'] ?></span>
														</td>
                                                        <?php
                                                        if ($row['status'] == 'SUCCESS') {
                                                            $badge = 'success';
                                                        } else if ($row['status'] == 'PENDING') {
                                                            $badge = 'warning';
                                                        } else if ($row['status'] == 'FAILED') {
                                                            $badge = 'danger';
                                                        } else {
                                                            $badge = 'info';
                                                        }
                                                        ?>
                                                        <td><span class="badge badge-<?= $badge ?>"><?= $row['status'] ?></span></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer clearfix">
                                <a href="dmt-report" class="btn btn-sm btn-secondary float-right">View All Transaction</a>
                            </div>
                            <!-- /.card-footer -->
                        </div>
                    </div>

                </div>
            <?php } ?>     

        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<?php include("inc/footer.php"); ?>
<script>
	function beneList() {
        $("#beneListBox").css("display","block");
        $("#addBeneBox").css("display","none");
    }
	function addBene() {
        $("#beneListBox").css("display","none");
        $("#addBeneBox").css("display","block");
    }
	function get_bene_name(e){
		var btn = e;
		$(btn).attr("onclick","");
        var mobile = $("#mobile").val();
        var account = $("#account").val();
        var ifsc  = $("#bank").find("option:selected").val();
        var remitterid  = $("#remitterid").val();
		 
        if(remitterid=='' || mobile=='' || account=='' || ifsc==''){ alert('Fill Account No., Mobile and Select Bank'); return false; }
		verify_beneficiary(btn,remitterid,mobile,account,ifsc,'getname');
	}
    function verify_beneficiary(btn,remitterid,mobile,account,ifsc,btnType) {
		
        $(".loading").css("display", "flex");
		Swal.fire({
            title: 'Are you sure ?',
            html: 'Beneficiary_Verification Charge are Rs. 3.00<br /><b>Note:</b> <i class="fas fa-rupee"></i> 1.00 will be credited back to beneficiary account.',
            type: "success",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes Get Name",
            closeOnConfirm: false
        }).then((result) => {
            if (result.value) {
			/* ------------------------------------------------------ */
			$.ajax({
				url: '<?= DOMAIN_NAME ?>retailer/ajax/dmt/beneficiary_account_validate.php',
				type: 'POST',
				cache: false,
				data: {remitterid: remitterid, mobile: mobile,account: account,ifsc: ifsc},
				dataTpe: "json",
				success: function (response)
				{
					//console.log(response);
					var res = JSON.parse(response);
					if (res.status == "0") {
						if(res.data.benename!=''){
							if(btnType=='getname'){
								$("#name").val(res.data.benename);
								$(".loading").css("display", "none");
							}
						} else {
							Swal.fire({
								title: res.msg,
								type: "error",
								showCancelButton: false,
								confirmButtonColor: "#DD6B55",
								confirmButtonText: "Ok",
								closeOnConfirm: false
							});
							$(".loading").css("display", "none");
							return false;
						}
					} else {
						Swal.fire({
							title: res.msg,
							type: "error",
							showCancelButton: false,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "Ok",
							closeOnConfirm: false
						});
						$(".loading").css("display", "none");
						return false;
					}					
				}
			});
			/* ------------------------------------------------------ */
			} else {
				$(btn).attr("onclick","verify_beneficiary(this)");
                $(".loading").css("display", "none");
            }
		});
    }
	
	function selectBank(e){
		var se = $(e).val(); //alert(se);
		if(se==''){
			$('#ifscMannualBox').css('display','block');
		} else {
			$('#ifscMannual').val('');
			$('#ifscMannualBox').css('display','none');
		}
	}
	
	function add_beneficiary(e) {
        var mobile = $("#mobile").val();
        var name = $("#name").val();
        var account = $("#account").val();
        var ifsc  = $("#bank").find("option:selected").val();
        var account_name  = $("#bank").find("option:selected").attr('data-name');
        var remitterid  = $("#remitterid").val();
		
		if(ifsc==''){
			var ifsc  = $('#ifscMannual').val();
		}
		
        if(name=='' || mobile=='' || account=='' || ifsc==''){ alert('Fill all feilds'); return false; }
        $(".loading").css("display", "flex");
		
		/* ------------------------------------------------------ */
			$.ajax({
				url: '<?= DOMAIN_NAME ?>retailer/ajax/dmt/beneficiary_register.php',
				type: 'POST',
				cache: false,
				data: {remitterid: remitterid, mobile: mobile, account: account, ifsc: ifsc, name: name, account_name : account_name, remitterid: remitterid },
				dataTpe: "json",
				success: function (response)
				{
					//console.log(response);
					var res = JSON.parse(response);
					if (res.status == "0") {
						if(res.beneficiary_status==0){
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
                                        url: '<?= DOMAIN_NAME ?>retailer/ajax/dmt/beneficiary_remove_validate.php',
                                        type: 'POST',
                                        cache: false,
                                        data: {remitterid: remitterid, beneficiaryid: res.beneficiary_id, otp: result.value},
                                        dataTpe: "json",
                                        success: function (response)
                                        {
                                            //console.log(response);
                                            var res1 = JSON.parse(response);
                                            if (res1.status == "0") {
                                                Swal.fire({
                                                    title: res1.msg,                                                    
                                                    showCancelButton: false,
                                                    confirmButtonColor: "#DD6B55",
                                                    confirmButtonText: "Ok",
                                                    closeOnConfirm: false
                                                }).then((result) => {
                                                    if (result.value) {
                                                        location.reload();
                                                    }
                                                });
                                            } else {
                                                Swal.fire({
                                                    title: "Error.",
                                                    html: res1.msg,
                                                    type: "error",
                                                    showCancelButton: false,
                                                    confirmButtonColor: "#DD6B55",
                                                    confirmButtonText: "Ok",
                                                    closeOnConfirm: false
                                                });
                                            }
                                            $(".loading").css("display", "none");
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
							location.reload();
							return false;
						}
					} else {
						Swal.fire({
							title: res.msg,
							type: "error",
							showCancelButton: false,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "Ok",
							closeOnConfirm: false
						});
						$(".loading").css("display", "none");
						return false;
					}					
				}
			});
			/* ------------------------------------------------------ */
		
    }
	
    function remove_beneficiary(e) {
        $(".loading").css("display", "flex");
        var btn = e;
        var name = $(btn).closest("tr").attr('data-name');
        var account = $(btn).closest("tr").attr('data-account');
        var remitterid = $(btn).closest("tr").attr('data-remitterId');
        var beneficiaryid = $(btn).closest("tr").attr('data-benefiId');
        var htmlText = "<strong>Name: </strong>" + name + "<br/><strong>A/C no.: </strong>" + account;
        Swal.fire({
            title: 'Are you sure to delete ?',
            html: htmlText,
            type: "success",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes Delete",
            closeOnConfirm: false
        }).then((result) => {
            if (result.value) {
                /* ------------------------------------------------------ */
                $.ajax({
                    url: '<?= DOMAIN_NAME ?>retailer/ajax/dmt/beneficiary_remove.php',
                    type: 'POST',
                    cache: false,
                    data: {remitterid: remitterid, beneficiaryid: beneficiaryid},
                    dataTpe: "json",
                    success: function (response)
                    {
                        //console.log(response);
                        var res = JSON.parse(response);
                        if (res.status == "0") {
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
                                        url: '<?= DOMAIN_NAME ?>retailer/ajax/dmt/beneficiary_remove_validate.php',
                                        type: 'POST',
                                        cache: false,
                                        data: {remitterid: remitterid, beneficiaryid: beneficiaryid, otp: result.value},
                                        dataTpe: "json",
                                        success: function (response)
                                        {
                                            //console.log(response);
                                            var res1 = JSON.parse(response);
                                            if (res1.status == "0") {
                                                Swal.fire({
                                                    title: "Beneficiaty Delete Successfully.",                                                    
                                                    showCancelButton: false,
                                                    confirmButtonColor: "#DD6B55",
                                                    confirmButtonText: "Ok",
                                                    closeOnConfirm: false
                                                }).then((result) => {
                                                    if (result.value) {
                                                        $(btn).closest("tr").css('display','none');
                                                    }
                                                });
                                            } else {
                                                Swal.fire({
                                                    title: "Error.",
                                                    html: res1.msg,
                                                    type: "error",
                                                    showCancelButton: false,
                                                    confirmButtonColor: "#DD6B55",
                                                    confirmButtonText: "Ok",
                                                    closeOnConfirm: false
                                                });
                                            }
                                            $(".loading").css("display", "none");
                                        }
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                title: "Error.",
                                html: res.msg,
                                type: "error",
                                showCancelButton: false,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Ok",
                                closeOnConfirm: false
                            });
                        }
                        $(".loading").css("display", "none");
                    }
                });
                /* ------------------------------------------------------ */
                $(".loading").css("display", "none");
            } else {
                $(".loading").css("display", "none");
            }
        });
        //$(".loading").css("display","flex");
    }

	function transefer_money(e){
		//$(".loading").css("display", "flex");
        var btn = e;
        var remittermobile = $(btn).closest("tr").attr('data-remitterMobile');
        var beneficiaryid = $(btn).closest("tr").attr('data-benefiId');
        var bene_name = $(btn).closest("tr").attr('data-name');
        var bene_account = $(btn).closest("tr").attr('data-account');
        var bene_ifsc = $(btn).closest("tr").attr('data-ifsc');
        var current_wallet_remaining_limit = $(btn).closest("tr").attr('data-remaininglimit');
        var amount = $(btn).closest("tr").find("input[name='amount']").val();
		if(parseInt(amount) > parseInt(current_wallet_remaining_limit)){
			alert("You have remaining limit Rs." + current_wallet_remaining_limit);
			return false;
		}
		if(parseInt(amount) <= 0){
			alert("Minimum amount Accept  Rs. 11" );
			return false;
		}
		$.ajax({
			url :  '<?=DOMAIN_NAME?>retailer/ajax/dmt/get_surcharge.php',
			type:  'POST',
			cache: false,
			data :  {remittermobile:remittermobile,beneficiaryid:beneficiaryid,amount:amount,current_wallet_remaining_limit:current_wallet_remaining_limit},
			dataTpe : "json",
			success:function(response)
			{
				//console.log(response);
				var res = JSON.parse(response);
				if(res.status=="0") {
					/* ############## Transaction details ############## */					
					var row = res.structure;
					var td_row = '<table class="table table-bordered table-sm">';
					td_row = td_row + "<tr><th>Amount</th><th>Deducted Amount</th><th>Surcharge</th></tr>";
					for (var i = 0; i < row.length; ++i){
						td_row = td_row + "<tr><td>" + row[i]['remit_amount'] + "</td><td>" + row[i]['deducted_amt'] + "</td><td>" + row[i]['surcharge'] + "</td></tr>";
					}
					var td_row = td_row + '</table>';
					var htmlText = td_row + "<strong>Bene. Name</strong> : " + bene_name + "<br/><strong>Total Amount</strong> : " + res.amount + "<br>"+"<strong>Total Deducted Amount</strong> : " + res.total_deducted_amt + "<br>" +"<strong>Total Surcharge</strong> : " + res.surcharge;
					/* ############## End ################ */
					Swal.fire({
					  title: res.msg,
					  html: htmlText,
					  type: "success",
					  showCancelButton: true,
					  confirmButtonColor: "#DD6B55",
					  confirmButtonText: "Transfer Now",
					  closeOnConfirm: false
					}).then((result) => {
						if (result.value) {
					/* ------------------------------------------------------ */
						$.ajax({
							url :  '<?=DOMAIN_NAME?>retailer/ajax/dmt/transfer.php',
							type:  'POST',
							cache: false,
							data :  {remittermobile:remittermobile,beneficiaryid:beneficiaryid,amount:amount,current_wallet_remaining_limit:current_wallet_remaining_limit,bene_account:bene_account,bene_ifsc:bene_ifsc,bene_name:bene_name},
							dataTpe : "json",
							success:function(response)
							{
								//console.log(response);
								var res = JSON.parse(response);
								if(res.status=="0") {
									
									/* ############## Transaction details ############## */					
									var row = res.structure;
									var table = '<table class="table table-bordered table-sm">';
									table = table + "<tr><th>Amount</th><th>Deducted Amount</th><th>Surcharge</th><th>Status</th></tr>";
									for (var i = 0; i < row.length; ++i){
										table = table + "<tr><td>" + row[i]['remit_amount'] + "</td><td>" + row[i]['deducted_amt'] + "</td><td>" + row[i]['surcharge'] + "</td><td>" + row[i]['status'] + "</td></tr>";
									}
									var table = table + '</table>';
									var htmlText = table;
									/* ############## End ################ */
									
									Swal.fire({
									  title: res.msg,
									  html: htmlText,
									  type: "success",
									  showCancelButton: false,
									  confirmButtonColor: "#DD6B55",
									  confirmButtonText: "Ok",
									  closeOnConfirm: false
									}).then((result) => {
										if (result.value) {
											location.reload();
										  }
									});	
								} else {
									Swal.fire({
									  title: "Error.",
									  html: res.msg,
									  type: "error",
									  showCancelButton: false,
									  confirmButtonColor: "#DD6B55",
									  confirmButtonText: "Ok",
									  closeOnConfirm: false
									}).then((result) => {
										if (result.value) {
											location.reload();
										  }
									});	
								}
								$("#loading").css("display","none");
							}
						});
					/* ------------------------------------------------------ */
						} else {
							$("#loading").css("display","none");
						}
					});	
				} else {
					Swal.fire({
					  title: "Error.",
					  html: res.msg,
					  type: "error",
					  showCancelButton: false,
					  confirmButtonColor: "#DD6B55",
					  confirmButtonText: "Ok",
					  closeOnConfirm: false
					}).then((result) => {
						if (result.value) {
							location.reload();
						  }
					});	
				}
				$("#loading").css("display","none");
			}
		});
	}


</script>
<script>
    $(document).ready(function () {
        $(".loading").css("display", "none");
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
