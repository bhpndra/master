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
		$filter  .= " and DATE(date_time) BETWEEN '$dateFrom' AND '$dateTo' ";
	} else{
		$filter .= " and DATE(date_time) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
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
            <h1 class="m-0 text-dark">Login Details</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Login Details</li>
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
              <h3 class="card-title">Login Details</h3>
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
                  <th>Time</th>
                  <th>Login From</th>
                  <th>Status</th>
                  <th>IP Address</th>
                  <th>Device</th>
                </tr>
<?php

	$totalRows = $mysqlClass->countRows(" select id from login_detail where user_id = '".$USER_ID."' and user_type = 'DISTRIBUTOR' ".$filter);
	$columns = " date_time, method, status, client_details";
	$query = $mysqlClass->mysqlQuery("select ".$columns." from login_detail where user_id = '".$USER_ID."' and user_type = 'DISTRIBUTOR' ".$filter . " ORDER BY `id` DESC " );
	
?>
                </thead>
                <tbody>
				<?php
					if($totalRows > 0 )	{
						while($res = $query->fetch(PDO::FETCH_ASSOC)){
							$cd = json_decode($res['client_details'], true);
							$res['ip_address'] =  $cd['REMOTE_ADDR'];	
							$res['device'] =  $cd['HTTP_USER_AGENT'];
							unset($res['client_details']);
				?>
				<tr>
				  <td><?=$k+1?></td>
				  <td><?=$res['date_time']?></td>
				  <td><?=strtoupper($res['method'])?></td>
				  <td><?=strtoupper($res['status'])?></td>
				  <td><?=$res['ip_address']?></td>
				  <td><?=$res['device']?></td>			  
				</tr>
				<?php
						}
					} else {
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
				]
    } );
 
    table.buttons().container()
        .appendTo( '#example1_wrapper .col-md-6:eq(0)' );
} );
</script>
</html>
