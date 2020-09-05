<?php session_start(); ?>
<?php
$server_name = "https://" . $_SERVER['SERVER_NAME']."/"; //die();
	if ($server_name != "https://rozpay.com/") {
		echo '<script>window.location.href="https://rozpay.com/404.html"</script>';
		die();
	}
?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>
<?php
	
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
$filter = '';

function insta_curl($url, $post_fields){

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL            => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING       => "",
		CURLOPT_MAXREDIRS      => 10,
		CURLOPT_TIMEOUT        => 180,
		CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST  => "POST",
		CURLOPT_POSTFIELDS     => $post_fields,
		CURLOPT_HTTPHEADER     => array(
			"Accept: application/json",
			"Content-Type: application/json",
		),
	));

	$response = curl_exec($curl);
	$err      = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
	} else {
		return $response;
	}
}

function generateNumericOTP($n) { 
    $generator = "1357902468"; 
    $result = ""; 
  
    for ($i = 1; $i <= $n; $i++) { 
        $result .= substr($generator, (rand()%(strlen($generator))), 1); 
    } 
    return $result; 
} 
  
// Main program 
$n = 6; 
$otp = generateNumericOTP($n); 
//$_SESSION['otp'] = $otp;
//$_SESSION['time_exp'] = time() + 300;
if(isset($_POST['dataName']) && isset($_POST['dataMobile']) && isset($_POST['dataAC']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
	if($_POST['dataPW']=="rp8742"){
		$post = $helpers->clearSlashes($_POST); 
		$ext_tran = $helpers->transaction_id_generator('RP',3);
		
		$postFields["token"]	= "16034c0633a24d872d7fe58799a17d11"; //Only use for Rozpay token
		$postFields["request"]['sp_key']	= "DPN";
		$postFields["request"]['external_ref']	= $ext_tran;
		$postFields["request"]['credit_account']	= $post['dataAC'];
		$postFields["request"]['credit_amount']	= ($post['dataAmount']);
		$postFields["request"]['ifs_code']	= $post['dataIFSC'];
		$postFields["request"]['bene_name']	= $post['dataName'];
		$postFields["request"]['latitude']	= "27.9929";
		$postFields["request"]['longitude']	= "77.1231";
		$postFields["request"]['endpoint_ip']	= "43.225.193.238";
		$postFields["request"]['alert_mobile']	= $post['dataMobile'];
		$postFields["request"]['alert_email']	= "";
		$postFields["request"]['remarks']	= $post['dataRemark'];
		//echo json_encode($postFields); die();
		$url = "https://www.instantpay.in/ws/payouts/direct";
		$res1 = insta_curl($url, json_encode($postFields));
		$res = json_decode($res1, true);
	}	
	
}


?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">PayOut</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">PayOut</li>
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
			<div class="col-md-6">
			<?php if(isset($res['statuscode']) && $res['statuscode']=='TXN' && $res['statuscode']=='TUP'){ ?>
			<div class="alert alert-info">
			  <strong>RefrenceId :</strong> <?=$res['data']['payout']['credit_refid']?><br/>
			  <strong>Account :</strong> <?=$res['data']['payout']['account']?><br/>
			  <strong>IFSC :</strong> <?=$res['data']['payout']['ifsc']?><br/>
			  <strong>Name :</strong> <?=$res['data']['payout']['name']?><br/>
			  <strong>Transfer Account :</strong> <?=$res['data']['transfer_value']?><br/>
			  <strong>Status :</strong> <?=$res['status']?><br/>
			</div>
			<?php } ?>
						  <div class="card card-success">
				<div class="card-header">
				  <h3 class="card-title">PayOut</h3>

				</div>
				<form method="post" id="pForm" enctype="multipart/form-data">
				<div class="card-body row">
				  <div class="form-group col-md-12">
					<label for="inputName">Bene. Name</label>
					<input type="text" class="form-control" id="dataName" name="dataName" value="" required="">
				  </div>
				  <div class="form-group col-md-12">
					<label for="inputName">Mobile</label>
					<input type="text" class="form-control" id="dataMobile" name="dataMobile" value=""  required="">
				  </div>
				  <div class="form-group col-md-12">
					<label for="inputName">Account</label>
					<input type="text" class="form-control" id="dataAC" name="dataAC" value=""  required="">
				  </div>
				  <div class="form-group col-md-6">
					<label for="inputName">IFSC</label>
					<input type="text" class="form-control" id="dataIFSC" name="dataIFSC" value=""  required="">
				  </div>
				  <div class="form-group col-md-6">
					<label for="inputName">Amount</label>
					<input type="text" class="form-control" id="dataAmount" name="dataAmount" value=""  required="">
				  </div>
				  <div class="form-group col-md-12">
					<label for="inputName">Remark</label>
					<input type="text" class="form-control" id="dataRemark" name="dataRemark" value=""  >
				  </div>
				  <div class="form-group col-md-12">
					<label for="inputName">Transaction Password</label>
					<input type="password" class="form-control" id="dataRemark" name="dataPW" value=""  required="">
				  </div>
				  
				</div>
				  <div class="card-footer">
					<button type="button" onclick="payout(this)" name="add" class="btn btn-primary">PayOut</button>
				  </div>
				</form>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->

			</div>
		</div>
	 </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
<script>
function payout(e){ 
	var select = e;
	var name = $('#dataName').val();
	var mobile = $('#dataMobile').val();
	var account = $('#dataAC').val();
	var amount = $('#dataAmount').val();
	var ifsc = $('#dataIFSC').val();
	var dataPW = $('#dataPW').val();
	var remark = $('#dataRemark').val();
			if(name=='' || mobile=='' || account=='' || amount=='' || ifsc=='' || dataPW==''){
				alert('Some details missing!');
			} else {
				var htmlText = "Name : " + name + "<br>"+"Mobile : " + mobile + "<br>" +"Amount : " + amount + "<br>" + "Account : " + account + "<br>" + "IFSC : " + ifsc;
				Swal.fire({
				  title: "PayOut",
				  html: htmlText,
				  type: "success",
				  showCancelButton: true,
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "Ok",
				  closeOnConfirm: false
				}).then((result) => {
					if (result.value) {						
						$("#pForm").submit();
					}		
				});	
			}
}

</script>

<script type="text/javascript">
/* function disableF5(e) { if ((e.which || e.keyCode) == 116 || (e.which || e.keyCode) == 82) e.preventDefault(); };

$(document).ready(function(){
     $(document).on("keydown", disableF5);
});

var bc = new BroadcastChannel('test_channel');

bc.onmessage = function (ev) { 
    if(ev.data && ev.data.url===window.location.href){
       alert('You cannot open the same page in 2 tabs');
    }
}

bc.postMessage(window.location.href); */
</script>
</html>
