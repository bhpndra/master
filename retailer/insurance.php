<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>
<?php 
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
    $retailer_id = $USER_ID;
	$txnid           =  time().$USER_ID;
    $partnerleadid   =  "{ 'NetPaisa' $txnid}";
    $partneragentid  =  "{ 'NetPaisa' $retailer_id}";
?>
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
                        <li class="breadcrumb-item active">Insurance</li>
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
<?php
$url = BASE_URL."/api/insurance/process_insurance.php";
$responseSiteDetails = api_curl($url,$post_fields,$headerArray);
$insurUrl = json_decode($responseSiteDetails,true);
?>		
               <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card card-info">
                            <div class="card-header">
                              <h3 class="card-title">Insurance</h3>
                            </div>
                            <div class="card-body">
                            <!-- /.card-header -->
							<form method="post" name="insurance_form" action="" class="form-group row justify-content-center">
								
								<div class="col4-box2" style="text-align: center; clear: both;">
								<a href="<?=$insurUrl['URL']?>" target="_blank" class="btn btn-success">Continue</a>
								</div>
							</form>
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
	window.load = window.open('<?=$insurUrl["URL"]?>','_blank');
</script>
</html>