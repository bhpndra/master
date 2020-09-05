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
            <h1 class="m-0 text-dark">DMT Report</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">DMT Report</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
	  
		<div class="row">
			<div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">DMT Report</h3>
            </div>
            <!-- /.card-header -->
			<div class="card-body">
<?php
if(isset($_GET['dateFrom']) && isset($_GET['dateTo'])){
	$post_fields = $_GET;
	$dateFrom = $_GET['dateFrom'];
	$dateTo   = $_GET['dateTo'];
} else {
	$date1 	  = new DateTime('7 days ago');
	$dateFrom = $date1->format('Y-m-d');
	$dateTo   = date("Y-m-d");
}
?>
			<form method="get" class="row">
			  <div class="form-group col-md-4">
				<label>Date From</label>
				<input type="date" class="form-control" placeholder="Date From" name="dateFrom" value="<?=$dateFrom?>" />
			  </div>
			  <div class="form-group col-md-4">
				<label>Date To</label>
				<input type="date" class="form-control" placeholder="Date From" name="dateTo" value="<?=$dateTo?>"/>
			  </div>
			  <div class="form-group col-md-4">
				<label style="opacity:0;display: block;">Button</label>
				<button type="submit" class="btn btn-primary mr-2" >Filter</button>
				<a href="<?=str_replace(".php","",$_SERVER['PHP_SELF'])?>" class="btn btn-dark text-white" >Reset</a>
			  </div>
			</form>
			</div>
            <div class="card-body">
			<div class="table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>#</th>
                  <th>TransactionId</th>
                  <th>OperatorTr. Id</th>
                  <th>Mobile</th>
                  <th>Amount</th>
                  <th>Deducted Amt</th>
                  <th>Bene. Name</th>
                  <th>Bene. Account</th>
                  <th>IFSC</th>
                  <th>Date</th>
                  <th>status</th>
                </tr>
<?php
$circle_url = BASE_URL."/api/dmt/report.php";
$post_fields["token"] = $_SESSION['TOKEN'];
$responseRC = api_curl($circle_url,$post_fields,$headerArray);
$resRC = json_decode($responseRC,true);
?>
                </thead>
                <tbody>
				<?php
					if($resRC['ERROR_CODE']==0 && isset($resRC['DATA']) && count($resRC['DATA']) > 0){
						foreach($resRC['DATA'] as $k=>$row){
				?>
				<tr>
				  <td><?=$k+1?></td>
				  <td><?=$row['agent_trid']?></td>
				  <td><?=$row['transaction_id']?></td>
				  <td><?=$row['mobile']?></td>
				  <td><?=$row['amount']?></td>
				  <td><?=$row['deducted_amount']?></td>
				  <td><?=$row['bene_name']?></td>
				  <td><?=$row['bene_ac']?></td>
				  <td><?=$row['ifsc_code']?></td>
				  <td><?=$row['date_created']?></td>
				  <?php 
					if($row['status']=='SUCCESS'){ $badge = 'success'; } else if($row['status']=='PENDING') { $badge = 'warning'; } else if($row['status']=='FAILED') { $badge = 'danger'; } else { $badge = 'info'; }
				  ?>
				  <td><span class="badge badge-<?=$badge?>"><?=$row['status']?></span> <a href="invoice-print?trans_id=<?=$row['agent_trid']?>&invoice_type=DMT" onclick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=SomeSize,height=SomeSize'); return false;"><i class="fas fa-print"></i></a></td>
				</tr>
				<?php
						}
					} else {
						echo "<tr><td colspan='10'>".$resRC['MESSAGE']."</td></tr>";
					}
				?>
                </tbody>
                <tfoot>

                </tfoot>
              </table>
            </div>
            </div>
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
$(document).ready(function() {
    var table = $('#example1').DataTable( {
        lengthChange: false,
       buttons: [ 'copy', 
				{extend: 'excel', title: '<?=str_replace(".php","",basename($_SERVER['PHP_SELF']))?>'},
				{extend: 'pdf', title: '<?=str_replace(".php","",basename($_SERVER['PHP_SELF']))?>'},
				{extend: 'colvis',  text: 'More'}
				],
		columnDefs: [
            {
                targets: [-2,-3],
                visible: false				
            }
        ]
    } );
 
    table.buttons().container()
        .appendTo( '#example1_wrapper .col-md-6:eq(0)' );
} );
</script>
</html>
