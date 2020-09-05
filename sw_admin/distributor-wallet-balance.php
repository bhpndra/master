<?php 
	session_start(); 
	include("../config.php"); 
	include("../include/lib.php"); 
	include("inc/head.php"); 
	include("inc/nav.php"); 
	include("inc/sidebar.php"); 
	
	$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
	$WL_ID = $resVT['DATA']['WL_ID'];
	$USER_ID = $resVT['DATA']['USER_ID'];
	$msg = '';
	
	$get = $helpers->clearSlashes($_GET); 
	
	$encode_id = $get['id'];
	$id = base64_decode($get['id']);
	
	$mysqlClass->mysqlQuery("START TRANSACTION");
	
	$userInfo = $mysqlClass->mysqlQuery("SELECT * FROM `add_cust` WHERE `id`='" . $id . "' and usertype = 'DISTRIBUTOR'  FOR UPDATE ")->fetch(PDO::FETCH_ASSOC);	
	
	
	$filter="";
	
	if(isset($_POST['dateFrom']) && isset($_POST['dateTo'])){
		$post_fields = $_POST;
		$dateFrom = $_POST['dateFrom'];
		$dateTo   = $_POST['dateTo'];
		$filter .= " and (date(date_created) BETWEEN '$dateFrom' AND '$dateTo') ";
	} 
	else {
		$date1 	  = new DateTime('60 days ago');
		$dateFrom = $date1->format('Y-m-d');
		$dateTo   = date("Y-m-d");
		$filter .= " and (date(date_created) BETWEEN '$dateFrom' AND '$dateTo') ";
	}
		
?>
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">View Wallet Balance</h1>
					</div>
					<!-- /.col -->
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active">View Wallet Balance</li>
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
							  <h3 class="card-title text-danger"><b><?=$userInfo['name'].' ( '. $userInfo['user'].' ) ';?></b> </h3>
							</div>
							<!-- /.card-header -->
							<div class="card-body table-responsive">
							
								<form method="post" class="row">
									
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
										<a href="<?=str_replace(".php","",$_SERVER['PHP_SELF']."?id=".$encode_id)?>" class="btn btn-dark text-white" >Reset</a>
									</div>
								</form>
								<table id="example1" class="table table-bordered table-striped">
									<thead>
										<tr>
											<th>#</th>
											<th>Tran.Id</th>
											<th>Opening</th>
											<th>Deposit</th>
											<th>Withdrawl</th>
											<th>Closing</th>
											<th>Date</th>
											<th>Type</th>
											<th>Comments</th>
											<th>Comm. / SurCharge</th>
											<th>Tran. Amount</th>
											<th>User Details</th>
										</tr>
									</thead>
								<tbody>
								<?php
								$orderBy = " order by `id` DESC";

								$res = $mysqlClass->fetchAllData('distributor_trans', " * ", " where dist_id = '".$id."' ".$filter.$orderBy );
								$k = 1;
								foreach($res as $row){
								?>  
								<tr>						
									<td><?=$k?></td>
									<td><?=$row['transaction_id']?></td>
									<td><?=$row['opening_balance']?></td>
									<td><?=($row['deposits']>0)? "<strong>".$row['deposits']."</strong>" : "--";?></td>
									<td><?=($row['withdrawl']>0)? "<strong>".$row['withdrawl']."</strong>" :  "--";?></td>
									<td><?=$row['balance']?></td>
									<td><?=$row['date_created']?></td>
									<td><?=$row['tr_type']?></td>
									<td><?=$row['comments']?></td>
									<td><?=$row['commission_surcharge']?></td>
									<td><?=$row['transaction_amount']?></td>
									<?php 
										$resPN = $mysqlClass->fetchRow(" add_cust ", " name,user,mobile ", " where id = '".$row['dist_retail_wl_admin_id']."' ");
										$userDetails = "Name: ". $resPN['name'] . ", UserId: " . $resPN['user'] . ", Mobile: " . $resPN['mobile'];						
									?>
								  <td><?=$userDetails?></td>
								</tr>
								<?php $k++;}?>			
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
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /.content -->
	</div>
<?php include("inc/footer.php"); ?>
<script>
$(document).ready(function() {
    var table = $('#example1').DataTable( {
        lengthChange: false,
        buttons: ['colvis' ],
		columnDefs: [
            {
                targets: [-3,-2],
                visible: false
            }
        ]
    } );
 
    table.buttons().container()
        .appendTo( '#example1_wrapper .col-md-6:eq(0)' );
} );
</script>
</html>
