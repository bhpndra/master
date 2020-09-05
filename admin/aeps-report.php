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
		$date1    = new DateTime('1 days ago');
		$dateFrom = $date1->format('Y-m-d');
		$dateTo   = date("Y-m-d");
		$filter  .= " and DATE(a.date_created) BETWEEN '$dateFrom' AND '$dateTo' ";
	} else{
		$filter .= " and DATE(a.date_created) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
		$dateFrom = $filterBy['dateFrom'];
		$dateTo = $filterBy['dateTo'];
	}   
	if(isset($filterBy['type'])&& $filterBy['type']!=""){
        $filter .= " and a.status = '".$filterBy['type']."' ";
    } else {
		//$filter .= " and a.status = 'SUCCESS' ";
	}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">AEPS Report</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">AEPS Report</li>
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
								$selected = $filterBy['type'];
								$$selected = "selected";
							}
						?>							
							<option  value="">All</option>
							<option <?=$SUCCESS?> value="SUCCESS">SUCCESS</option>
							<option <?=$PENDING?> value="PENDING">PENDING</option>
							<option <?=$FAILED?> value="REFUND">FAILED</option>
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
				  <h3 class="card-title">AEPS Report</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body table-responsive">
				  <table id="example1" class="table table-bordered table-striped">
					<thead>
					<tr>
					  <th>#</th>
					  <th>TransactionId</th>
					  <th>OperatorTr. Id</th>
					  <th>BCID</th>
					  <th>Outlet Id</th>
					  <th>Amount</th>
					  <th>Date</th>					  
					  <th>Status</th>
					  <th>Message</th>
					  <th>Mobile</th>
					  <th>UID</th>
					  <th>Settlement</th>
					  <th>Retailer Details</th>
					</tr>
					</thead>
					<tbody>
<?php
$retailerIds = " `user_id` in (SELECT id FROM `add_cust` where `wl_id` = '".$WL_ID."' GROUP BY  wl_id)";
$columns = " a.transaction_id,
			 a.agent_trid,
			 a.outletid,
			 a.bcid,
			 a.amount,
			 a.date_created,
			 a.status,
			 a.uid,
			 a.mobile,
			 a.message,
			 a.settlement_wl,
			 b.name,
			 b.user	 ";

$sql = "select $columns from aeps_info as a left join add_cust as b on b.id = a.user_id where b.wl_id = '".$WL_ID."' $filter ORDER BY a.id DESC	"; 

$res = $mysqlClass->mysqlQuery($sql);
$k = 0;
while($row = $res->fetch(PDO::FETCH_ASSOC)){
?>  
					<tr>
					  <td><?=$k+1?></td>
					  <td><?=$row['agent_trid']?></td>
					  <td><?=$row['transaction_id']?></td>
					  <td><?=$row['bcid']?></td>
					  <td><?=$row['outletid']?></td>
					  <td><?=$row['amount']?></td>
					  <td><?=$row['date_created']?></td>
					  <?php 
						if($row['status']=='SUCCESS'){ $badge = 'success'; } else if($row['status']=='PENDING') { $badge = 'warning'; } else if($row['status']=='FAILED') { $badge = 'danger'; } else { $badge = 'info'; }
					  ?>
					  <td>
						<span class="badge badge-<?=$badge?>"><?=$row['status']?></span>
						<?php if($row['status']=="PENDING"){ ?>
							<a href="javascript:void()" class="badge badge-success" onclick="get_status('<?=$row["agent_trid"]?>');">Get Status</a><br/>
						<?php } ?>
					 </td>
					  <td><?=$row['message']?></td>
					  <td><?=$row['mobile']?></td>
					  <td><?=$row['uid']?></td>
					  <td><?=$row['settlement_wl']?></td>
					  <td  style="white-space: nowrap;">
						<strong>Name: </strong><?=$row['name']?> <br/>
						<strong>UserID: </strong> <?=$row["user"]?>
					  </td>
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
                          
function get_status(trans_id){
		Swal.fire({
			title: "Please Wait",
			text: "Your request is being processed",
			type: "info",
			allowEscapeKey: false,
			showConfirmButton: false
		});
		$.ajax({
			type: 'POST',
			data: {agent_tranid:trans_id},
			cache: false,
			url: 'ajax/get_aeps_status.php',
			dataType: 'text',
			success: function (response)
			{
				console.log(response);							
				var data = JSON.parse(response);
				if (data.ERROR_CODE == 0) { 
					Swal.fire({
						title: "Status",
						text: 'TRANSACTION ' + data.MESSAGE,
						type: "success",
						closeOnConfirm: false,
						timer: 3000
					}).then((result) => {
						if (result.value) {
							//location.reload();
						  }
					});						
				} else { 
					Swal.fire({
						title: 'Status',
						text: '' + data.MESSAGE,
						confirmButtonColor: "#2196F3",
						type: "error"
					}).then((result) => {
						if (result.value) {
							//location.reload();
						  }
					});	
				}
			}
		});
}
</script>
</html>
