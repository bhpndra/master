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
$filter = '';

	$filterBy = $helpers->clearSlashes($_POST);
	if(@$filterBy['dateFrom']=="" && @$filterBy['dateTo']==""){
		$date1    = new DateTime('0 days ago');
		$dateFrom = $date1->format('Y-m-d');
		$dateTo   = date("Y-m-d");
		$filter  .= " and DATE(date_created) BETWEEN '$dateFrom' AND '$dateTo' ";
	} else{
		$filter .= " and DATE(date_created) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
		$dateFrom = $filterBy['dateFrom'];
		$dateTo = $filterBy['dateTo'];
	}   
	if(isset($filterBy['type'])&& $filterBy['type']!=""){
        $filter .= " and tr_type = '".$filterBy['type']."' ";
    }
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Wallet Report</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Wallet Report</li>
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
				  <h3 class="card-title">Filter</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body">
				  <form method="post" class="row">
					<div class="form-group col-md-3">
					  <label>Date From:</label>

					  <div class="input-group">
						<div class="input-group-prepend">
						  <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
						</div>
						<input type="date" class="form-control" name="dateFrom"  value="<?=$dateFrom?>" max="" />
					  </div>
					  <!-- /.input group -->
					</div>
					<div class="form-group col-md-3">
					  <label>To From:</label>

					  <div class="input-group">
						<div class="input-group-prepend">
						  <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
						</div>
						<input type="date" class="form-control" name="dateTo" value="<?=$dateTo?>" min="<?=$dateFrom?>" />
					  </div>
					  <!-- /.input group -->
					</div>
					<div class="form-group col-md-3">
					  <label>Type:</label>

					  <div class="input-group">						
						<select class="form-control" name="type" >
						<?php
							if(@$filterBy['type']!=""){
								$selected = $filterBy['type'];
								$$selected = "selected";
							}
						?>
							<option value="">All</option>
							<option <?=$RECHARGE?> value="RECHARGE">RECHARGE</option>
							<option <?=$DMT?> value="DMT">DMT</option>
							<option <?=$REFUND?> value="REFUND">REFUND</option>
						</select>
					  </div>
					  <!-- /.input group -->
					</div>
					<div class="form-group col-md-3">
					  <label style="opacity:0">Buttons</label>

					  <div class="input-group">						
						<input type="submit" name="filter" value="Filter" class="btn btn-success mr-1" />
						<a href="<?=str_replace(".php","",basename($_SERVER['PHP_SELF']))?>" class="btn btn-warning" >Reset</a>
					  </div>
					  <!-- /.input group -->
					</div>
				  </form>
				</div>
				<!-- /.card-body -->
			  </div>
			  <div class="card">
				<div class="card-header">
				  <h3 class="card-title">Wallet Report</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body table-responsive">
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
					  <th>Refunded Id</th>
					  <th>User Details</th>
					</tr>
					</thead>
					<tbody>
<?php
$res = $mysqlClass->fetchAllData("wl_trans"," * ", " WHERE `wluser_id` = '".$USER_ID."' $filter ORDER BY `id` DESC");
foreach($res as $k=>$row){
?>  
					<tr>
					  <td><?=$k+1?></td>
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
					  <td><?=$row['refund_id']?></td>
					  <?php 
							$resPN = $mysqlClass->fetchRow(" add_cust ", " name,user,mobile ", " where id = '".$row['ret_dest_wl_admin_id']."' and usertype = '".$row['user_type']."' ");
							if(!empty($resPN)){								
								$userDetails = "<strong>Name: </strong>". $resPN['name'] . ", <br/><strong>UserId: </strong>" . $resPN['user'] . ", <br/><strong>Mobile: </strong>" . $resPN['mobile'];
							} else {
								$userDetails = "";
							}						
   					  ?>					  
					  <td  style="white-space: nowrap;"><?=$userDetails?></td>
					</tr>
<?php } ?>  					
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
	$("input[name='dateFrom'").change(function() {
		var dateFrom = $(this).val(); //alert(dateFrom);
		$("input[name='dateTo'").val(dateFrom);
		$("input[name='dateTo'").attr("min",dateFrom);
	});
	
    var table = $('#example1').DataTable( {
        lengthChange: false,
        buttons: [ 'copy', 
				{extend: 'excel', title: '<?=str_replace(".php","",basename($_SERVER['PHP_SELF']))?>'},
				{extend: 'pdf', title: '<?=str_replace(".php","",basename($_SERVER['PHP_SELF']))?>'},
				{extend: 'colvis',  text: 'More'} ],
		
    } );
 
    table.buttons().container()
        .appendTo( '#example1_wrapper .col-md-6:eq(0)' );
} );
</script>
</html>
