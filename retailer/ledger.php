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
            <h1 class="m-0 text-dark">Ledger</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Ledger</li>
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
              <h3 class="card-title">Ledger</h3>
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
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>#</th>
                  <th>TransactionId</th>
                  <th>Op.Balance</th>
                  <th>Deposit</th>
                  <th>withdrawl</th>
                  <th>Clo.Balance</th>
                  <th>Date</th>
                  <th>Type</th>
                  <th>Comments</th>
                  <th>Refund Id</th>
                </tr>
<?php
$circle_url = BASE_URL."/api/ledger.php";
$post_fields["token"] = $_SESSION['TOKEN'];
$responseRC = api_curl($circle_url,$post_fields,$headerArray);
$resRC = json_decode($responseRC,true);
?>
                </thead>
                <tbody>
				<?php
					if($resRC['ERROR_CODE']==0){
						foreach($resRC['DATA'] as $k=>$row){
				?>
				<tr>
				  <td><?=$k+1?></td>
				  <td><?=$row['agent_trid']?></td>
				  <td><?=$row['opening_balance']?></td>
				  <td><?=($row['deposits']>0)? "<strong>".$row['deposits']."</strong>" : "--";?></td>
				  <td><?=($row['withdrawl']>0)? "<strong>".$row['withdrawl']."</strong>" :  "--";?></td>
				  <td><?=$row['closing_balance']?></td>
				  <td><?=$row['date_created']?></td>
				  <?php 
					if($row['tr_type']=='RECHARGE'){ $badge = 'success'; } else if($row['tr_type']=='DMT') { $badge = 'warning'; } else if($row['tr_type']=='BILL') { $badge = 'danger'; } else { $badge = 'info'; }
				  ?>
				  <td><span class="badge badge-<?=$badge?>"><?=$row['tr_type']?></span></td>
				  <td><?=$row['comments']?></td>
				  <td><?=$row['refund_id']?></td>
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
