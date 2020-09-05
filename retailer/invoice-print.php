<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php
	$urlU = BASE_URL."/api/user-details.php";
	$responseUserDetails = api_curl($urlU,$post_fields,$headerArray);
	$resUDetails = json_decode($responseUserDetails,true);
	if($resUDetails['ERROR_CODE']==0){
		$uDetails = $resUDetails['DATA'];
	}
?>
<?php
	$urlU = BASE_URL."/api/invoice.php";
	$post_fields['trans_agent_id'] = $_GET['trans_id'];
	$post_fields['invoice_type'] = $_GET['invoice_type'];
	$responseInvoice = api_curl($urlU,$post_fields,$headerArray);
	$resInvoice = json_decode($responseInvoice,true);
	if($resInvoice['ERROR_CODE']==0){
		$invoiceDetails = $resInvoice['DATA'];
	} else {
		//echo '<script>self.close();</script>';
	}
	//print_r($invoiceDetails);
?>
<body>
<div class="wrapper">
  <!-- Main content -->
  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-12">
        <h2 class="page-header" style="text-align: center;">
          <img src="<?=($siteDetails['logo'])? $siteDetails['logo'] : BASE_URL.'logo/logo.png'?>" alt="Logo" class="brand-image"
           style="background: rgba(255, 255, 255, 0.73);padding: 7px; height: 65px;">
          <!--<small class="float-right">Date: 2/10/2014</small>-->
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
      <!-- /.col -->
	  <?php 
	  if($resInvoice['ERROR_CODE']==0 && $resInvoice['MESSAGE']=='RECHARGE'){
	  ?>
      <div class="col-sm-6 invoice-col">
        <h3><b>Recharge Details</b></h3>
		<strong>Mobile:</strong> <?=$invoiceDetails['mobile']?><br>
        <strong>Network:</strong> <?=$invoiceDetails['operator_name']?>
      </div>
	  <?php } else if ($resInvoice['ERROR_CODE']==0 && $resInvoice['MESSAGE']=='DMT') { ?>
      <div class="col-sm-6 invoice-col">
        <h3><b>Remitter Details</b></h3>
		<strong>Mobile:</strong> <?=$invoiceDetails[0]['mobile']?>
      </div>
	  <?php } else if ($resInvoice['ERROR_CODE']==0 && $resInvoice['MESSAGE']=='AEPS') { ?>
		<div class="col-sm-6 invoice-col">
			<h3><b>Customer Details</b></h3>
			<strong>Mobile:</strong> <?=$invoiceDetails[0]['mobile']?><br/>
			<strong>Aadhaar:</strong> <?=$invoiceDetails[0]['uid']?>
		  </div>
	  <?php } else if ($resInvoice['ERROR_CODE']==0 && $resInvoice['MESSAGE']=='BBPS') { ?>
		<div class="col-sm-6 invoice-col">
			<h3><b>Customer Details</b></h3>
			<strong>Bill Type:</strong> <?=$invoiceDetails['bill_type']?><br/>
			<strong>Mobile:</strong> <?=$invoiceDetails['customer_mobile']?><br/>
			<strong>Consumer No.:</strong> <?=$invoiceDetails['consumer_number']?>
		  </div>
	  <?php } ?>
      <div class="col-sm-6 invoice-col text-right">
        <address>
          <h3><strong>Retailer Details.</strong></h3>
          <strong>Name:</strong> <?=$uDetails['name']?><br>
          <strong>Mobile:</strong> <?=$uDetails['mobile']?><br>
          <strong>Email:</strong> <?=$uDetails['email']?>
        </address>
      </div>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Table row -->
    <div class="row">
      <div class="col-12 table-responsive">
        <table class="table table-striped table-sm">
		 <?php 
		  if($resInvoice['ERROR_CODE']==0 && $resInvoice['MESSAGE']=='RECHARGE'){
		  ?>
          <thead>
          <tr>
            <th>Qty</th>
            <th>Tran. Id</th>
            <th>Operator Tran. Id</th>
            <th>Mobile</th>
            <th>Amount</th>
            <th>Network</th>
            <th>Recharge Type</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>1</td>
            <td><?=$invoiceDetails['agent_trid']?></td>
            <td><?=$invoiceDetails['transaction_id']?></td>
            <td><?=$invoiceDetails['mobile']?></td>
            <td><?=$invoiceDetails['amount']?></td>
            <td><?=$invoiceDetails['operator_name']?></td>
            <td><?=$invoiceDetails['rech_type']?></td>
            <td><?=$invoiceDetails['date_created']?></td>
            <td><?=$invoiceDetails['status']?></td>
          </tr>
          </tbody>
		  <?php 
			} else if($resInvoice['ERROR_CODE']==0 && $resInvoice['MESSAGE']=='DMT') { 
			$i=1;
		  ?>
          <thead>
          <tr>
            <th>Qty</th>
            <th>Tran. Id</th>
            <th>Operator Tran. Id</th>
            <th>Bank Ref. No</th>
            <th>Amount</th>
            <th>Bene A/C</th>
            <th>Bene Bank</th>
            <th>IFSC</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
          </thead>
          <tbody>
		  <?php 		  
			foreach($invoiceDetails as $d){
		  ?>
          <tr>
            <td><?=$i?></td>
            <td><?=$d['agent_trid']?></td>
            <td><?=$d['transaction_id']?></td>
            <td><?=$d['ref_no']?></td>
            <td><?=$d['amount']?></td>
            <td><?=$d['bene_ac']?></td>
            <td><?=$d['bene_name']?></td>
            <td><?=$d['ifsc_code']?></td>
            <td><?=$d['date_created']?></td>
            <td><?=$d['status']?></td>
          </tr>
			<?php $i++; } ?>
          </tbody>		  
		  <?php } else if($resInvoice['ERROR_CODE']==0 && $resInvoice['MESSAGE']=='AEPS') {  ?>
		   <thead>
          <tr>
			  <th>TransactionId</th>
			  <th>Agent TransId</th>
			  <th>Trans. Type</th>
			  <th>Amount</th>
			  <th>Date</th>
			  <th>Message</th>
			  <th>Status</th>
			  <th>UID</th>
			  <th>Mobile</th>
          </tr>
          </thead>
          <tbody>
		  <?php 		  
			foreach($invoiceDetails as $k=>$d){
		  ?>
          <tr>
             <td><?=$k+1?></td>
			  <td><?=$d['transaction_id']?></td>
			  <td><?=$d['agent_trid']?></td>
			  <td><?=$d['txntype']?></td>
			  <td><?=$d['amount']?></td>
			  <td><?=$d['date_created']?></td>
			  <td><?=$d['message']?></td>
			  <td><?=$d['status']?></td>
			  <td><?=$d['uid']?></td>
			  <td><?=$d['mobile']?></td>
          </tr>
			<?php } ?>
          </tbody>	
		  <?php } else if($resInvoice['ERROR_CODE']==0 && $resInvoice['MESSAGE']=='BBPS'){
		  ?>
          <thead>
          <tr>
            <th>Qty</th>
			  <th>TransactionId</th>
			  <th>Agent TransId</th>
			  <th>Operator Name</th>
			  <th>Consumer No.</th>
			  <th>Mobile.</th>
			  <th>Amount</th>
			  <th>Bill TYpe</th>
			  <th>Status</th>
			  <th>Date</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>1</td>
            <td><?=$invoiceDetails['agent_trid']?></td>
            <td><?=$invoiceDetails['transaction_id']?></td>
            <td><?=$invoiceDetails['operator_name']?></td>
            <td><?=$invoiceDetails['customer_mobile']?></td>
            <td><?=$invoiceDetails['consumer_number']?></td>
            <td><?=$invoiceDetails['amount']?></td>
            <td><?=$invoiceDetails['bill_type']?></td>
            <td><?=$invoiceDetails['status']?></td>
            <td><?=$invoiceDetails['date_created']?></td>
          </tr>
          </tbody>
		  <?php 
			}
			?>
        </table>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->

<script type="text/javascript"> 
  window.addEventListener("load", window.print());
  setTimeout(function(){self.close();},5000);
</script>
</body>
</html>
