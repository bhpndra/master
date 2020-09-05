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
            <h1 class="m-0 text-dark">New User Request</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">New User Request</li>
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
							<option <?=$REJECT?> value="REJECT">REJECT</option>
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
				  <h3 class="card-title">New User Request</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body table-responsive">
				  <table id="example1" class="table table-bordered table-striped">
					<thead>
					<tr>
					  <th>#</th>
					  <th>Name</th>
					  <th>Mobile</th>
					  <th>Email</th>
					  <th>User Type</th>
					  <th>Shop Name</th>
					  <th>City</th>
					  <th>State</th>
					  <th>Address</th>
					  <th>Pin Code</th>
					  <th>Request Date</th>
					  <th>Status</th>
					</tr>
					</thead>
					<tbody>
<?php
//$retailerIds = " a.user_id in (SELECT id FROM `add_cust` where `wl_id` = '".$WL_ID."' )";
$k = 0;
$sql = " select * from user_registration_request where wl_id = '".$WL_ID."'";
$res = $mysqlClass->mysqlQuery($sql);
foreach($res as $k=>$row){
?>  
					<tr>
					  <td><?=$k+1?></td>
					  <td><?=$row['name']?></td>
					  <td><?=$row['mobile']?></td>
					  <td><?=$row['email']?></td>
					  <td class="type"><?=$row['user_type']?></td>
					  <td><?=$row['cname']?></td>
					  <td><?=$row['city']?></td>
					  <td><?=$row['state']?></td>
					  <td><?=$row['address']?></td>
					  <td><?=$row['pincode']?></td>
					  <td><?=$row['created_on']?></td>
					  <?php 
						if($row['status']=='SUCCESS'){ $badge = 'success'; } else if($row['status']=='PENDING') { $badge = 'warning'; } else if($row['status']=='FAILED') { $badge = 'danger'; } else { $badge = 'info'; }
						
						$currentStatus = str_replace(" ","",$row['status'])."_R";
						$$currentStatus = "selected";
					  ?>
					  <td>
						<span class="badge badge-<?=$badge?>"><?=$row['status']?></span><br/>
						<?php if($row['status']!="SUCCESS"){ ?>
						<select onchange="update_status(this,'<?=$row['id']?>')">
							<option <?=$PENDING_R?> value="PENDING">PENDING</option>
							<option <?=$SUCCESS_R?> value="SUCCESS">SUCCESS</option>
							<option <?=$REJECT_R?> value="REJECT">REJECT</option>
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
function update_status(e,id){ 
	var select = e;
	var status = $(select).val();
	
	if(confirm("Are you sure to update status ?")){
		
		$.ajax({
			type: 'POST',
			data: {id:id,status:status},
			cache: false,
			url: 'ajax/update_user_request.php',
			success: function (response)
			{ 
				res = JSON.parse(response);
				if(res.MESSAGE=='SUCCESS' && status == 'SUCCESS'){
					var type = $(select).closest('td').children('.type').text();
					if(type=="Distributor"){
						var url = '<?=DOMAIN_NAME?>admin/add-distributor' ;
						window.open(url,"_blank");
					} else {
						var url = '<?=DOMAIN_NAME?>admin/add-retailer' ;
						window.open(url,"_blank");
					}
				}
			}
		});
				
	}
}

</script>
</html>
