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


	$filterBy = $helpers->clearSlashes($_GET);
	if(@$filterBy['dateFrom']=="" && @$filterBy['dateTo']==""){
		$date1    = new DateTime('30 days ago');
		$dateFrom = $date1->format('Y-m-d');
		$dateTo   = date("Y-m-d");
		$filter  .= " and DATE(p.payment_date) BETWEEN '$dateFrom' AND '$dateTo' ";
	} else{
		$filter .= " and DATE(p.payment_date) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
		$dateFrom = $filterBy['dateFrom'];
		$dateTo = $filterBy['dateTo'];
	}   
	
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Fund Request History</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Fund Request History</li>
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
              <h3 class="card-title">Fund Request History</h3>
            </div>
            <!-- /.card-header -->
			<div class="card-body">
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
                  <th>Bank Name</th>
                  <th>Account</th>
                  <th>Amount</th>
                  <th>Bank Ref.No.</th>
                  <th>Message</th>
                  <th>Status</th>
                  <th>Payment Date</th>
                  <th>Request Time</th>
                  <th>Update Time</th>
                </tr>
				<?php
					$totalRows = $mysqlClass->countRows(" select p.id from payment as p where p.user_id = '".$USER_ID."' and p.user_type = 'DISTRIBUTOR' ".$filter);
					$columns = " b.bank_name, b.account_number , p.amount, p.bank_refno, p.mobile, p.message, p.status, p.deposit_slip, p.request_time, p.payment_date, p.update_time";
					$query = $mysqlClass->mysqlQuery("select ".$columns." from payment as p left join bank_details as b on p.bank = b.id where p.user_id = '".$USER_ID."' and p.user_type = 'DISTRIBUTOR' ".$filter);
					
				?>
                </thead>
                <tbody>
				<?php
				if($totalRows > 0 )
				{
					while($res = $query->fetch(PDO::FETCH_ASSOC)){
				?>
					<tr>
					  <td><?=++$k?></td>
					  <td><?=$res['bank_name']?></td>
					  <td><?=$res['account_number']?></td>
					  <td><?=$res['amount']?></td>
					  <td><?=$res['bank_refno']?></td>
					  <td><?=$res['message']?></td>
					  <td><?=$res['status']?></td>
					  <td><?=$res['payment_date']?></td>
					  <td><?=$res['request_time']?></td>
					  <td><?=$res['update_time']?></td>				  
					</tr>
				<?php
					}
				}
					 else {
						echo "<tr><td colspan='10'>Row Not Found</td></tr>";
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
                targets: [-2,-1],
                visible: false				
            }
        ]
    } );
 
    table.buttons().container()
        .appendTo( '#example1_wrapper .col-md-6:eq(0)' );
} );
</script>
</html>
