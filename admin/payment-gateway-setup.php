<?php 
	session_start(); 
	include("../config.php");
	include("../include/lib.php");
	include("inc/head.php"); 
	include("inc/nav.php");
	include("inc/sidebar.php");

	
?>
<!-- Bootstrap Color Picker -->
<link rel="stylesheet" href="<?=BASE_URL?>/dashboard/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">

<?php
	if($pagePerms['payment_gateway'] == 1){		
		echo "<script>alert('Error: Page not found.')</script>";					
		$helpers->redirect_page("dashboard");		
	}
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];

$msg = $helpers->flashAlert_get('wl_payment_setup_fa');

//Save details for Razorpay payment gateway

	
	if(isset($_POST['razorpayBtn'])){
		$post = $helpers->clearSlashes($_POST); // form data clearslashes	
		//print_r($post); die();
		//create json array of Payment Mode
		$payment_mode = array(
					0	=>  array(
								"type"	=>	"cc",
								"type_name" => "Credit Card",
								"charge_type"	=> $post['cc_mdr'],
								"charge_value"	=> $post['cc_value'],
								"status"	=> $post['cc_status']								
							),
					1	=>  array(
								"type"	=>	"dc",
								"type_name" => "Debit card",
								"charge_type"	=> $post['dc_mdr'],
								"charge_value"	=> $post['dc_value'],
								"status"	=> $post['dc_status']								
							),
					2	=>  array(
								"type"	=>	"nb",
								"type_name" => "Net Banking",
								"charge_type"	=> $post['nb_mdr'],
								"charge_value"	=> $post['nb_value'],
								"status"	=> $post['nb_status']								
							),
					3	=>  array(
								"type"	=>	"wal",
								"type_name" => "Wallet",
								"charge_type"	=> $post['wal_mdr'],
								"charge_value"	=> $post['wal_value'],
								"status"	=> $post['wal_status']								
							),
					4	=>  array(
								"type"	=>	"upi",
								"type_name" => "UPI",
								"charge_type"	=> $post['upi_mdr'],
								"charge_value"	=> $post['upi_value'],
								"status"	=> $post['upi_status']								
							)							
					);
					
				
		//-------------
		$values = array(
					"merchant_id"			=> $post['mid'],
					"merchant_key"			=> $post['mkey'],
					"settlement_type"		=> $post['settlement_type'],
					"payment_mode"			=> json_encode($payment_mode),
					"payment_gateway_name"	=> "RAZORPAY",
					"payment_env"			=> "PROD",
					"wl_id" 				=> $WL_ID,
					"admin_id" 	 			=> $ADMIN_ID,
					"date_created"			=> date("Y-m-d H:i:s")
					);
					
		//query to check setup details are already in table
		$sql = "SELECT * FROM wl_payment_gateway WHERE wl_id='$WL_ID' AND payment_gateway_name='RAZORPAY' ";
		
		if($mysqlClass->countRows($sql) > 0 )
		{
			$mysqlClass->updateData(' wl_payment_gateway ', $values, " WHERE wl_id='$WL_ID' AND admin_id='$ADMIN_ID' AND payment_gateway_name='RAZORPAY' ");		
			$msg0  = $helpers->flashAlert_set("wl_payment_setup_fa","Razorpay Payment Setup Updated Successfully.");
			$sdd = "post";
		}
		else{
			$mysqlClass->insertData(' wl_payment_gateway ', $values);		
			$msg0  = $helpers->flashAlert_set("wl_payment_setup_fa","Razorpay Payment Setup Successfully.");
		}
		$helpers->redirect_page("payment-gateway-setup");
	}

	
	// Save paytm form data
	
	if(isset($_POST['paytmBtn'])){
		
		$post = $helpers->clearSlashes($_POST); // form data clearslashes	
		
		//create json array of Payment Mode
		$payment_mode = array(
					0	=>  array(
								"type"	=>	"cc",
								"type_name" => "Credit Card",
								"charge_type"	=> $post['cc_mdr'],
								"charge_value"	=> $post['cc_value'],
								"status"	=> $post['cc_status']								
							),
					1	=>  array(
								"type"	=>	"dc",
								"type_name" => "Debit card",
								"charge_type"	=> $post['dc_mdr'],
								"charge_value"	=> $post['dc_value'],
								"status"	=> $post['dc_status']								
							),
					2	=>  array(
								"type"	=>	"nb",
								"type_name" => "Net Banking",
								"charge_type"	=> $post['nb_mdr'],
								"charge_value"	=> $post['nb_value'],
								"status"	=> $post['nb_status']								
							),
					3	=>  array(
								"type"	=>	"wal",
								"type_name" => "Wallet",
								"charge_type"	=> $post['wal_mdr'],
								"charge_value"	=> $post['wal_value'],
								"status"	=> $post['wal_status']								
							),
					4	=>  array(
								"type"	=>	"upi",
								"type_name" => "UPI",
								"charge_type"	=> $post['upi_mdr'],
								"charge_value"	=> $post['upi_value'],
								"status"	=> $post['upi_status']								
							)							
					);
			
		$pay_env = (isset($post['pay_env']) && $post['pay_env'] == 'TEST') ? $post['pay_env'] : 'PROD'; //default value is PROD
		
		
		//-------------
		$values = array(
					"merchant_id"			=> $post['mid'],
					"merchant_key"			=> $post['mkey'],
					"settlement_type"		=> $post['settlement_type'],
					"payment_mode"			=> json_encode($payment_mode),
					"payment_gateway_name"	=> "PAYTM",
					"payment_env"			=> $pay_env,
					"wl_id" 				=> $WL_ID,
					"admin_id" 	 			=> $ADMIN_ID,
					"date_created"			=> date("Y-m-d H:i:s")
					);
		
		//query to check setup details are already in table
		$sql = "SELECT * FROM wl_payment_gateway WHERE wl_id='$WL_ID' AND payment_gateway_name='PAYTM' ";
		
		if($mysqlClass->countRows($sql) > 0 )
		{
			$mysqlClass->updateData(' wl_payment_gateway ', $values, " WHERE wl_id='$WL_ID' AND admin_id='$ADMIN_ID' AND payment_gateway_name='PAYTM' ");		
			$msg0  = $helpers->flashAlert_set("wl_payment_setup_fa","Paytm Payment Setup Updated Successfully.");
		}
		else{
			$res = $mysqlClass->insertData(' wl_payment_gateway ', $values);	
			$msg0  = $helpers->flashAlert_set("wl_payment_setup_fa","Paytm Payment Setup Successfully.");
		}
		$helpers->redirect_page("payment-gateway-setup");
	}
	
	// fetch data for Razorpay payment gateway.
	
	$sqlRasPay = $mysqlClass->fetchRow(" wl_payment_gateway ", " * ", " WHERE wl_id='$WL_ID' AND payment_gateway_name='RAZORPAY'");
	$rasPayMode = json_decode($sqlRasPay['payment_mode'],true);
	//echo  $sqlRasPay['payment_mode'];
	// fetch data for Paytm payment gateway.
	
	$sqlPaytm = $mysqlClass->fetchRow(" wl_payment_gateway ", " * ", " WHERE wl_id='$WL_ID' AND payment_gateway_name='PAYTM'");
	$rasPaytmMode = json_decode($sqlPaytm['payment_mode'],true);
	
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Payment Gateway Setup</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Payment Gateway Setup</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

		<div class="row justify-content-center">
			<div class="col-md-10">
				<?php
					if($msg){
						echo $helpers->alert_message($msg,"alert-success");
					}
				?>
			  	<div class="card card-warning collapsed-card">

					<!--RAZORPAY PAYMENT GATEWAY SETUP--!-->
					
					<div class="card-header">
						<h3 class="card-title"><img src="<?=DOMAIN_NAME?>uploads/razorpay.png"> &nbsp; (Razorpay Payment Gateway Setup)</h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse" autocomplete="off"><i class="fas fa-plus"></i></button>
						</div>
					</div>					
					<div class="card-body">
						<form role="form" autocomplete="off" method="post" class="commissionForm">
							<div class="row">								
								<table class="table">	
									<tr>
										<th colspan="5" style="text-align: center;background: #4d4d4d;color: #fff;padding: 5px;">Payment Mode</th>
									</tr>				
									<tr>
										<th>MODE</th>
										<th>MDR</th>
										<th>VALUE</th>
										<th>STATUS</th>
									</tr>
									<tr>
										<td><label>Credit Card</label></td>
										<td>
											<select name="cc_mdr"  >
												<option value="PERCENT" <?if($rasPayMode[0]['charge_type'] == "PERCENT"){echo "selected";}?>>PERCENT</option>
												<option value="FLAT" <?if($rasPayMode[0]['charge_type'] == "FLAT"){echo "selected";}?>>FLAT</option>
											</select>
										</td>
										<td width="20%"><input type="text" name="cc_value" actocomplete="off" value="<?=($rasPayMode[0]['charge_value']) ? $rasPayMode[0]['charge_value'] : 1; ?>" ></td>
										<td>
											<select name="cc_status"  >
												<option value="ACTIVE" <?if($rasPayMode[0]['status'] == "ACTIVE"){echo "selected";}?>>ACTIVE</option>
												<option value="DEACTIVE" <?if($rasPayMode[0]['status'] == "DEACTIVE"){echo "selected";}?>>DEACTIVE</option>
											</select>
										</td>
									</tr>
									<tr>
										<td><label>Debit Card</label></td>
										<td>
											<select name="dc_mdr"  >
												<option value="PERCENT" <?if($rasPayMode[1]['charge_type'] == "PERCENT"){echo "selected";}?>>PERCENT</option>
												<option value="FLAT" <?if($rasPayMode[1]['charge_type'] == "FLAT"){echo "selected";}?>>FLAT</option>
											</select>
										</td>
										<td><input type="text" name="dc_value" actocomplete="off" value="<?=($rasPayMode[1]['charge_value']) ? $rasPayMode[1]['charge_value'] : 1; ?>" ></td>
										<td>
											<select name="dc_status"  >
												<option value="ACTIVE" <?if($rasPayMode[1]['status'] == "ACTIVE"){echo "selected";}?>>ACTIVE</option>
												<option value="DEACTIVE" <?if($rasPayMode[1]['status'] == "DEACTIVE"){echo "selected";}?>>DEACTIVE</option>
											</select>
										</td>
									</tr>
									<tr>
										<td><label>Net Banking</label></td>
										<td>
											<select name="nb_mdr"  >
												<option value="PERCENT" <?if($rasPayMode[2]['charge_type'] == "PERCENT"){echo "selected";}?>>PERCENT</option>
												<option value="FLAT" <?if($rasPayMode[2]['charge_type'] == "FLAT"){echo "selected";}?>>FLAT</option>
											</select>
										</td>
										<td><input type="text" name="nb_value" actocomplete="off" value="<?=($rasPayMode[2]['charge_value']) ? $rasPayMode[2]['charge_value'] : 1; ?>" ></td>
										<td>
											<select name="nb_status"  >
												<option value="ACTIVE" <?if($rasPayMode[2]['status'] == "ACTIVE"){echo "selected";}?>>ACTIVE</option>
												<option value="DEACTIVE" <?if($rasPayMode[2]['status'] == "DEACTIVE"){echo "selected";}?>>DEACTIVE</option>
											</select>
										</td>
									</tr>
									<tr>
										<td><label>Wallet</label></td>
										<td>
											<select name="wal_mdr"  >
												<option value="PERCENT" <?if($rasPayMode[3]['charge_type'] == "PERCENT"){echo "selected";}?>>PERCENT</option>
												<option value="FLAT" <?if($rasPayMode[3]['charge_type'] == "FLAT"){echo "selected";}?>>FLAT</option>
											</select>
										</td>
										<td><input type="text" name="wal_value" actocomplete="off" value="<?=($rasPayMode[3]['charge_value']) ? $rasPayMode[3]['charge_value'] : 1; ?>"></td>
										<td>
											<select name="wal_status"  >
												<option value="ACTIVE" <?if($rasPayMode[3]['status'] == "ACTIVE"){echo "selected";}?>>ACTIVE</option>
												<option value="DEACTIVE" <?if($rasPayMode[3]['status'] == "DEACTIVE"){echo "selected";}?>>DEACTIVE</option>
											</select>
										</td>
									</tr>
									<tr>
										<td><label>UPI</label></td>
										<td>
											<select name="upi_mdr"  >
												<option value="PERCENT" <?if($rasPayMode[4]['charge_type'] == "PERCENT"){echo "selected";}?>>PERCENT</option>
												<option value="FLAT" <?if($rasPayMode[4]['charge_type'] == "FLAT"){echo "selected";}?>>FLAT</option>
											</select>
										</td>
										<td><input type="text" name="upi_value" actocomplete="off" value="<?=($rasPayMode[4]['charge_value']) ? $rasPayMode[4]['charge_value'] : 1; ?>"  ></td>
										<td>
											<select name="upi_status"  >
												<option value="ACTIVE" <?if($rasPayMode[4]['status'] == "ACTIVE"){echo "selected";}?>>ACTIVE</option>
												<option value="DEACTIVE" <?if($rasPayMode[4]['status'] == "DEACTIVE"){echo "selected";}?>>DEACTIVE</option>
											</select>
										</td>
									</tr>
									<!--<tr>
										<td><label>Payment</label></td>
										<td>
											<select name="common_mdr"  >
												<option value="PERCENT" <?if($rasPayMode[0]['charge_type'] == "PERCENT"){echo "selected";}?>>PERCENT</option>
												<option value="FLAT" <?if($rasPayMode[0]['charge_type'] == "FLAT"){echo "selected";}?>>FLAT</option>
											</select>
										</td>
										<td><input type="text" name="common_value" actocomplete="off" value="<?=$rasPayMode[0]['charge_value']?>"  ></td>
										<td>
											<select name="common_status"  >
												<option value="ACTIVE" <?if($rasPayMode[0]['status'] == "ACTIVE"){echo "selected";}?>>ACTIVE</option>
												<option value="DEACTIVE" <?if($rasPayMode[0]['status'] == "DEACTIVE"){echo "selected";}?>>DEACTIVE</option>
											</select>
										</td>
									</tr>-->
								</table>
								<div class="col-md-12 mb-2" style="text-align: center;background: #4d4d4d;color: #fff;padding: 5px; font-weight:bold">Merchant details</div>
								<div class="col-md-4 form-group">
									<label class="label">Key ID</label>
									<input type="text" name="mid"   required placeholder="Merchant-ID" style="width:200px" value="<?=$sqlRasPay['merchant_id'];?>"/>
								</div>
								<div class="col-md-4 form-group">
									<label class="label">Key Secret</label>
									<input type="text" name="mkey"   required placeholder="Merchant Key" style="width:200px" value="<?=$sqlRasPay['merchant_key'];?>"/>
								</div>
								<div class="col-md-4 form-group">
									<label class="label">Settlement</label><br/>
									Instant: <input type="radio" name="settlement_type"   required value="INSTANT" <?=($sqlRasPay['settlement_type'] == 'INSTANT') ? 'checked' : '' ;?> /> &nbsp; &nbsp;
									Mannual: <input type="radio" name="settlement_type"   required value="MANNUAL" <?=($sqlRasPay['settlement_type'] == 'MANNUAL') ? 'checked' : '' ;?>/>
								</div>																	
							</div>		
							
							<div>
								<button type="submit" name="razorpayBtn" class="btn btn-success">Submit</button>
							</div>
						</form>
					</div>					
				</div>
				
				<div class="card card-danger collapsed-card"  style="display:none">

					<!--PAYTM PAYMENT GATEWAY SETUP--!-->
					
					<div class="card-header">
						<h3 class="card-title" ><img src="<?=DOMAIN_NAME?>uploads/paytm.png"> &nbsp;(Paytm Payment Gateway Setup)</h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse" autocomplete="off"><i class="fas fa-plus"></i></button>
						</div>
					</div>					
					<div class="card-body">
						<form role="form" autocomplete="off" method="post" class="commissionForm">
							<div class="row">								
								<div class="card-body row">
									<label class="col-sm-3 col-form-label">Paytm Environment</label>
									<div class="col-sm-9 pt-1">					
										<input type="checkbox" name="pay_env" value="TEST" onChange="checkboxSwichVal(this.value)" data-bootstrap-switch data-off-color="warning" data-on-color="success" data-on-text="TEST" data-off-text="PROD" data-size="small" <?=($sqlPaytm['payment_env']=== "TEST") ? "checked='checked'" : ''; ?>/>
									</div>
								</div>
								<table class="table">	
									<tr>
										<th colspan="5" style="text-align: center;background: #4d4d4d;color: #fff;padding: 5px;">Payment Mode</th>
									</tr>				
									<tr>
										<th>MODE</th>
										<th>MDR</th>
										<th>VALUE</th>
										<th>STATUS</th>
									</tr>
									<tr>
										<td><label>Credit Card</label></td>
										<td>
											<select name="cc_mdr"  >
												<option value="PERCENT" <?if($rasPaytmMode[0]['charge_type'] == "PERCENT"){echo "selected";}?>>PERCENT</option>
												<option value="FLAT" <?if($rasPaytmMode[0]['charge_type'] == "FLAT"){echo "selected";}?>>FLAT</option>
											</select>
										</td>
										<td width="20%"><input type="text" name="cc_value" actocomplete="off" value="<?=$rasPaytmMode[0]['charge_value']?>" ></td>
										<td>
											<select name="cc_status"  >
												<option value="ACTIVE" <?if($rasPaytmMode[0]['status'] == "ACTIVE"){echo "selected";}?>>ACTIVE</option>
												<option value="DEACTIVE" <?if($rasPaytmMode[0]['status'] == "DEACTIVE"){echo "selected";}?>>DEACTIVE</option>
											</select>
										</td>
									</tr>
									<tr>
										<td><label>Debit Card</label></td>
										<td>
											<select name="dc_mdr"  >
												<option value="PERCENT" <?if($rasPaytmMode[1]['charge_type'] == "PERCENT"){echo "selected";}?>>PERCENT</option>
												<option value="FLAT" <?if($rasPaytmMode[1]['charge_type'] == "FLAT"){echo "selected";}?>>FLAT</option>
											</select>
										</td>
										<td><input type="text" name="dc_value" actocomplete="off" value="<?=$rasPaytmMode[1]['charge_value']?>" ></td>
										<td>
											<select name="dc_status"  >
												<option value="ACTIVE" <?if($rasPaytmMode[1]['status'] == "ACTIVE"){echo "selected";}?>>ACTIVE</option>
												<option value="DEACTIVE" <?if($rasPaytmMode[1]['status'] == "DEACTIVE"){echo "selected";}?>>DEACTIVE</option>
											</select>
										</td>
									</tr>
									<tr>
										<td><label>Net Banking</label></td>
										<td>
											<select name="nb_mdr"  >
												<option value="PERCENT" <?if($rasPaytmMode[2]['charge_type'] == "PERCENT"){echo "selected";}?>>PERCENT</option>
												<option value="FLAT" <?if($rasPaytmMode[2]['charge_type'] == "FLAT"){echo "selected";}?>>FLAT</option>
											</select>
										</td>
										<td><input type="text" name="nb_value" actocomplete="off" value="<?=$rasPaytmMode[2]['charge_value']?>" ></td>
										<td>
											<select name="nb_status"  >
												<option value="ACTIVE" <?if($rasPaytmMode[2]['status'] == "ACTIVE"){echo "selected";}?>>ACTIVE</option>
												<option value="DEACTIVE" <?if($rasPaytmMode[2]['status'] == "DEACTIVE"){echo "selected";}?>>DEACTIVE</option>
											</select>
										</td>
									</tr>
									<tr>
										<td><label>Wallet</label></td>
										<td>
											<select name="wal_mdr"  >
												<option value="PERCENT" <?if($rasPaytmMode[3]['charge_type'] == "PERCENT"){echo "selected";}?>>PERCENT</option>
												<option value="FLAT" <?if($rasPaytmMode[3]['charge_type'] == "FLAT"){echo "selected";}?>>FLAT</option>
											</select>
										</td>
										<td><input type="text" name="wal_value" actocomplete="off" value="<?=$rasPaytmMode[3]['charge_value']?>"></td>
										<td>
											<select name="wal_status"  >
												<option value="ACTIVE" <?if($rasPaytmMode[3]['status'] == "ACTIVE"){echo "selected";}?>>ACTIVE</option>
												<option value="DEACTIVE" <?if($rasPaytmMode[3]['status'] == "ACTIVE"){echo "selected";}?>>DEACTIVE</option>
											</select>
										</td>
									</tr>
									<tr>
										<td><label>UPI</label></td>
										<td>
											<select name="upi_mdr"  >
												<option value="PERCENT" <?if($rasPaytmMode[4]['charge_type'] == "PERCENT"){echo "selected";}?>>PERCENT</option>
												<option value="FLAT" <?if($rasPaytmMode[4]['charge_type'] == "FLAT"){echo "selected";}?>>FLAT</option>
											</select>
										</td>
										<td><input type="text" name="upi_value" actocomplete="off" value="<?=$rasPaytmMode[4]['charge_value']?>"  ></td>
										<td>
											<select name="upi_status"  >
												<option value="ACTIVE" <?if($rasPaytmMode[4]['status'] == "ACTIVE"){echo "selected";}?>>ACTIVE</option>
												<option value="DEACTIVE" <?if($rasPaytmMode[4]['status'] == "DEACTIVE"){echo "selected";}?>>DEACTIVE</option>
											</select>
										</td>
									</tr>
								</table>
								<div class="col-md-12 mb-2" style="text-align: center;background: #4d4d4d;color: #fff;padding: 5px; font-weight:bold">Merchant details</div>
								<div class="col-md-6 form-group">
									<label class="label">Merchant ID</label>
									<input type="text" name="mid"   required placeholder="Merchant-ID" style="width:200px" value="<?=$sqlPaytm['merchant_id']?>"/>
								</div>
								<div class="col-md-6 form-group">
									<label class="label">Merchant Key</label>
									<input type="text" name="mkey"   required placeholder="Merchant Key" style="width:200px" value="<?=$sqlPaytm['merchant_key']?>"/>
								</div>							
							</div>							
							<div>
								<button type="submit" name="paytmBtn" class="btn btn-success">Submit</button>
							</div>
						</form>
					</div>		
					
				</div>
			<!-- /.card -->
			</div>
		</div>

      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
<!-- bootstrap color picker -->
<script src="<?=BASE_URL?>dashboard/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script>
$(document).ready(function(){
    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });
});
$(document).ready(function(){
    $(document).on('click', '.btn-add', function(e)
    { //alert();
        e.preventDefault();

        var controlForm = $('.controls'),
            currentEntry = $(this).parents('.entry:first'),
            newEntry = $(currentEntry.clone()).appendTo(controlForm);

        newEntry.find('input').val('');
        controlForm.find('.entry:not(:last) .btn-add')
            .removeClass('btn-add').addClass('btn-remove')
            .removeClass('btn-success').addClass('btn-danger')
            .html('<span class="fas fa-minus"></span>');
    });
	$(document).on('click', '.btn-remove', function(e)
    {
		$(this).parents('.entry:first').remove();

		e.preventDefault();
		return false;
	});
});

function checkboxSwichVal(switchVal)
{
	//alert(switchVal);
}
function sms_switch(e){
	var swit = e;
	if ($(swit).prop('checked')==true){ 
        //alert('Default');
		updateAPI('TEST');
		$('#apiForm').css('display','none');
    } else {
		updateAPI('PRO');
		$('#apiForm').css('display','block');
	}
}
function updateAPI(switchVal){
	$.ajax({
		type: 'POST',
		data: {switchVal:switchVal},
		cache: false,
		url: 'ajax/sms_switch_update.php',
		success: function (response)
		{ 
			
		}
	});
}
</script>
</html>
