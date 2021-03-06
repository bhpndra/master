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
		$date1    = new DateTime('30 days ago');
		$dateFrom = $date1->format('Y-m-d');
		$dateTo   = date("Y-m-d");
		$filter  .= " and DATE(a.settlement_date) BETWEEN '$dateFrom' AND '$dateTo' ";
	} else{
		$filter .= " and DATE(a.settlement_date) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
		$dateFrom = $filterBy['dateFrom'];
		$dateTo = $filterBy['dateTo'];
	}   
	if(isset($filterBy['type'])&& $filterBy['type']!=""){
        $filter .= " and a.status = '".$filterBy['type']."' ";
    }
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">AEPS Bank Settlement Request</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">AEPS Bank Settlement Request</li>
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
						<input type="date" class="form-control" name="dateFrom"  value="<?=$dateFrom?>" />
					  </div>
					  <!-- /.input group -->
					</div>
					<div class="form-group col-md-3">
					  <label>To From:</label>

					  <div class="input-group">
						<div class="input-group-prepend">
						  <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
						</div>
						<input type="date" class="form-control" name="dateTo" value="<?=$dateTo?>" />
					  </div>
					  <!-- /.input group -->
					</div>
					<div class="form-group col-md-3">
					  <label>Type:</label>

					  <div class="input-group">						
						<select class="form-control" name="type" >
						<?php
							if(@$filterBy['type']!=""){
								$selected = str_replace(" ","",$filterBy['type']);
								$$selected = "selected";
							}
						?>
							<option value="">All</option>
							<option <?=$PENDING?> value="PENDING">PENDING</option>
							<option <?=$SUCCESS?> value="SUCCESS">SUCCESS</option>
							<option <?=$FAILED?> value="FAILED">FAILED</option>
							<option <?=$UNDERPROCESS?> value="UNDER PROCESS">UNDER PROCESS</option>
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
				  <h3 class="card-title">AEPS Bank Settlement Request</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body table-responsive">
				  <table id="example1" class="table table-bordered table-striped">
					<thead>
					<tr>
					  <th>#</th>
					  <th>Name</th>
					  <th>UserID</th>
					  <th>Mobile</th>
					  <th>Bank_Name</th>
					  <th>Account_No.</th>
					  <th>A/C Holder Name</th>
					  <th>Amount</th>
					  <th>Transaction Id</th>
					  <th>Group Id</th>
					  <th>Request Date</th>
					  <th>Update Date</th>
					  <th>Status</th>
					</tr>
					</thead>
					<tbody>
<?php
//$retailerIds = " a.user_id in (SELECT id FROM `add_cust` where `wl_id` = '".$WL_ID."' )";
$sql = " select 
		a.id, a.user_id, a.transaction_id, a.amount, a.status, a.account_number, a.account_name, a.ifsc, a.bank_name, a.group_id, a.payment_date, a.settlement_date, b.name, b.user, b.mobile
		from aeps_withdrawl_info as a
		left join add_cust as b on a.user_id = b.id  
		WHERE a.withdrawl_type = 'bank' and b.wl_id = '".$WL_ID."' $filter ORDER BY a.id DESC";
$res = $mysqlClass->mysqlQuery($sql);
foreach($res as $k=>$row){
?>  
					<tr>
					  <td><?=$k+1?></td>
					  <td><?=$row['name']?></td>
					  <td><?=$row['user']?></td>
					  <td><?=$row['mobile']?></td>
					  <td><?=$row['bank_name']?></td>
					  <td><?=$row['account_number']?></td>
					  <td><?=$row['account_name']?></td>
					  <td><?=$row['amount']?></td>
					  <td><?=$row['transaction_id']?></td>
					  <td><?=$row['group_id']?></td>
					  <td><?=$row['settlement_date']?></td>
					  <td><?=$row['payment_date']?></td>
					  <?php 
						if($row['status']=='SUCCESS'){ $badge = 'success'; } else if($row['status']=='PENDING') { $badge = 'warning'; } else if($row['status']=='FAILED') { $badge = 'danger'; } else { $badge = 'info'; }
						
						$currentStatus = str_replace(" ","",$row['status'])."_R";
						$$currentStatus = "selected";
					  ?>
					  <td>
						<span class="badge badge-<?=$badge?>"><?=$row['status']?></span><br/>
						<?php if($row['status']!="SUCCESS"){ ?>
						<select onchange="update_status(this,'<?=$row['id']?>','<?=base64_encode($row['user_id'])?>')">
							<option <?=$PENDING_R?> value="PENDING">PENDING</option>
							<option <?=$SUCCESS_R?> value="SUCCESS">SUCCESS</option>
							<option <?=$UNDERPROCESS_R?> value="UNDER PROCESS">UNDER PROCESS</option>
						</select>
						<?php } else { ?>
						<select>
							<option <?=$SUCCESS_R?> value="SUCCESS">SUCCESS</option>
						</select>
						<?php } ?>
					  </td>
					  <?php $$currentStatus =''; ?>
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
    var table = $('#example1').DataTable( {
        lengthChange: false,
        buttons: [ 'copy', 
				{extend: 'excel', title: '<?=str_replace(".php","",basename($_SERVER['PHP_SELF']))?>'},
				{extend: 'pdf', title: '<?=str_replace(".php","",basename($_SERVER['PHP_SELF']))?>'},
				{extend: 'colvis',  text: 'More'} ],
		columnDefs: [
            {
                targets: [-1],
                visible: true
            }
        ]
    } );
 
    table.buttons().container()
        .appendTo( '#example1_wrapper .col-md-6:eq(0)' );
} );
function update_status(e,id,uid){ 
	var select = e;
	var status = $(select).val();
	
	if(confirm("Are you sure to update status ?")){
		$.ajax({
			type: 'POST',
			data: {uid:uid,id:id,status:status},
			cache: false,
			url: 'ajax/update_aeps_settlement_request.php',
			success: function (response)
			{ 
				res = JSON.parse(response);
				if(res.ERROR_CODE==0){
					$(select).closest('td').children('.badge').html(status);
				}
			}
		});
		
	}
}

</script>
</html>
