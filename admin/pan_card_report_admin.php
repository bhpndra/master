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
		$filter  .= " and DATE(a.create_date) BETWEEN '$dateFrom' AND '$dateTo' ";
	} else{
		$filter .= " and DATE(a.create_date) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
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
            <h1 class="m-0 text-dark">Pan Card Report</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Pan Card Report</li>
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
							<option value="">All</option>
							<?php
							$res_status = $mysqlClass->fetchAllData('netpaisa_services_status',"id,name","");
							foreach($res_status  AS $status_value){
								$StatusArray[$status_value['id']]=$status_value['name'];
								?>
								<option value="<?= $status_value['id']; ?>"><?= $status_value['name']; ?></option>
								<?php
							}
		
							?>
							
							
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
				  <h3 class="card-title">Pan Card Report</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body table-responsive">
				  <table id="example1" class="table table-bordered table-striped">
					<thead>
					<tr>
					  <th>#</th>                
					  <th>Name</th>
					  <th>user name</th>
					  <th>Mobile</th>
					  <th>Alternate Mobile</th>
					  <th>Email</th>
					  <th>Dob</th>
					  <th>City</th>
					  <th>State</th>
					  <th>PIN</th>
					  <th>Address</th>
					  <th>Pan NO</th>
					  <th>Aadhar NO</th>
					  <th>Status</th>
					  <th>Create Date</th>
					</tr>
					</thead>
					<tbody>
<?php
$res_status = $mysqlClass->fetchAllData('netpaisa_services_status',"id,name","");
		foreach($res_status  AS $status_value){
			$StatusArray[$status_value['id']]=$status_value['name'];
		}
	
		$resRC['services_status'] = $StatusArray;

$sql = " Select a.*	from pan_card_details as a left join add_cust as b on b.id = a.user_id where b.wl_id = '".$WL_ID."' $filter ORDER BY a.id DESC	";

$res = $mysqlClass->mysqlQuery($sql);
$k = 0;
while($row = $res->fetch(PDO::FETCH_ASSOC)){
?>  
				<tr>
				  <td><?=$k+1?></td>
				  <td><?=$row['name']?></td>
				  <td><?=$row['user_name']?></td>
				  <td><?=$row['mobile']?></td>
				  <td><?=$row['alternate_mobile']?></td>
				  <td><?=$row['email']?></td>
				  <td><?=$row['dob']?></td>
				  <td>
				  <?php
					$res_status = $mysqlClass->fetchRow('cities',"city"," where id='".$row['city']."' ");
					echo $res_status['city'];
					?>				  
				  </td>
				  <td>
				  <?php
					$res_status = $mysqlClass->fetchRow('states',"name"," where id='".$row['state']."' ");
					echo $res_status['name'];
					?>
				  </td>
				  <td><?=$row['pin']?></td>
				  <td><?=$row['address']?></td>
				  <td><?=$row['pan_no']?></td>
				  <td><?=$row['aadhar_no']?></td>
				  <?php 
				//echo "<pre />";
				// print_r($resRC);
				  $status=$row['status'];
				  $status_name = $resRC['services_status'][$status];
				 // echo $status;
					if($row['status']==2){ $badge = 'success'; } else if($row['status']==2) { $badge = 'warning'; } else if($row['status']==3) { $badge = 'danger'; } else { $badge = 'info'; }
				  ?>
				  <td>
				  <!--<span class="badge badge-<?=$badge?>">
				  <a href='' onclick="retrun confirm('Do youw')"  ><?=$status_name; ?></a>
				  </span> --> 
		   
						<select class="form-control" name="type" onchange="UpdateServiceStatus(this.value,<?=$row['id']; ?> );" >
						
							<?php
							$res_status = $mysqlClass->fetchAllData('netpaisa_services_status',"id,name","");
							foreach($res_status  AS $status_value){
								$StatusArray[$status_value['id']]=$status_value['name'];
								?>
								<option  <?php if($status==$status_value['id']){  echo "selected"; } ?>  value="<?= $status_value['id']; ?>"><?= $status_value['name']; ?></option>
								<?php
							}
		
							?>
							
							
						</select>
		   
		   
		   </td>
				  <td><?=$row['create_date']?></td>
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
			url: 'ajax/get_recharge_status.php',
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

function UpdateServiceStatus(StatusId,row_id){
	var CheckConfirm =  confirm("Do you want to change the status ?");
	if( CheckConfirm ){
		$.ajax({
			type: 'POST',
			data: "row_id="+row_id+"&status_id="+StatusId,
			cache: false,
			url: 'ajax/UpdateServiceStatus.php',			
			success: function (response){
				console.log(response);							
				var data = JSON.parse(response);
				if (data.ERROR_CODE == 0) { 					
					location.reload();										
				} 
			}
		});
	}
}


</script>
</html>
