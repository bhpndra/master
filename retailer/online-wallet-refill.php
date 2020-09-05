<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>
<?php
	require("../api/classes/db_class.php");
	require("../api/classes/comman_class.php");
	$helpers = new Helper_class();
	$mysqlClass = new mysql_class();


$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Add Fund (Online Wallet Refill)</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add Fund (Online Wallet Refill)</li>
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
<?php
$sqlRasPay = $mysqlClass->fetchRow(" wl_payment_gateway ", " * ", " WHERE wl_id='$WL_ID' AND payment_gateway_name='RAZORPAY'");
	$sqlRasPay['payment_mode'];

	if(isset($sqlRasPay['payment_mode']) && isset($sqlRasPay['merchant_id']) && isset($sqlRasPay['merchant_key'])){
?>
			<div class="col-md-6">
			<?=($msg!='')? $helpers->alert_message($msg,'alert-info') : '';?>
			  <div class="card card-success">
				<div class="card-header">
				  <h3 class="card-title">Add Fund  (Online Wallet Refill)</h3>

				</div>
				<form method="post" id='form_id' >
				<div class="card-body row">				  
				  <div class="form-group col-md-6">
					<label for="inputName">Payment Gateway</label>
					<select  class="form-control" name="pg" id="pg" required >
						<option value="" selected disabled>Select Payment Gateway</option>
						<option value="RAZORPAY">Razorpay</option>
					</select>
				  </div>				  
				  <div class="form-group col-md-6" style="display:block">

					<label for="inputName">Payment Bank</label>
					<select  class="form-control" name="paymode" required id="paymode" onchange="clear_input()" >
<?php	
	
	$rasPayMode = json_decode($sqlRasPay['payment_mode'],true);				
						foreach($rasPayMode as $pm){
							if($pm['status']=='ACTIVE'){
								echo "<option datamdr='".$pm['charge_value']."' datamdrt='".$pm['charge_type']."' value='".$pm['type']."'>".$pm['type_name']."</option>";
							}
						}
?>						
					</select>
				  </div>				  
				  <div class="form-group col-md-6">
					<label for="inputName">Amount</label>
					<input type="text"  class="form-control" value="" name="amount" onblur="calculate_payAmount(this)" id="amount" required >
				  </div>				  
				  <div class="form-group col-md-6">
					<label for="inputName">Payable Amount</label>
					<input type="text"  class="form-control" value="0.00" name="pamount" id="pamount" required readonly>
				  </div>				  
				</div>
				  <div class="card-footer">
					<a href="javascript:void(0)" onclick="payNow()" name="add" class="btn btn-primary">Process Now</a>
				  </div>
				</form>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->

			</div>
	<?php } ?>
		</div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
<script>
	
	function clear_input(){		
		document.getElementById("amount").value = '';
		document.getElementById("pamount").value = '';
	}
	function calculate_payAmount(e){
		var paymentGateway = document.getElementById("pg").value;
		var pg_mode_select = document.getElementById("paymode");
		var mode_charge = $('option:selected', pg_mode_select).attr('datamdr');
		var mode_type =$('option:selected', pg_mode_select).attr('datamdrt');
		var mode =$('option:selected', pg_mode_select).val();
		//alert(mode_type );
		if(paymentGateway=='RAZORPAY'){			
			if(mode_type=="FLAT"){
				var pg_charge = mode_charge;
			} else {
				var fee = (parseFloat(e.value) * mode_charge/100); 
				var pg_charge = parseFloat(fee) + (parseFloat(fee) * 18/100); //alert(parseFloat(pg_charge));
			}
			
			var payableAmount = parseFloat(e.value) + parseFloat(pg_charge); 
			document.getElementById("pamount").value = payableAmount.toFixed(3);
		} else if(paymentGateway=='Paygate'){ 			
			if(mode_type=="FLAT"){
				var pg_charge = mode_charge;
			} else {
				var fee = (parseFloat(e.value) * mode_charge/100); 
				var pg_charge = parseFloat(fee) + (parseFloat(fee) * 18/100); //alert(parseFloat(pg_charge));
			}
			
			var payableAmount = parseFloat(e.value) + parseFloat(pg_charge); 
			document.getElementById("pamount").value = payableAmount.toFixed(3);
		} else {
			alert('Select Payment gateway!');
			e.value = '';
		}
	} 
	function payNow(){
		var paymentGateway = document.getElementById("pg").value;
		var amount = document.getElementById("amount").value;
		var pamount = document.getElementById("pamount").value;
		if(paymentGateway=='RAZORPAY'){
			if(amount > 0){
				
				document.getElementById('form_id').action = 'razorpay-fund-request-process.php';
				document.getElementById("form_id").submit(); 
			} else {
				alert('Enter Amount greater than 0 ');
			}
		} 
		else {
			alert('Select Payment gateway!');
		}
	}
</script>
</html>
