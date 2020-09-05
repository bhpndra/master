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
$msg = '';

$user_details = $helpers->flashAlert_get('user_details_set');
$msg = $helpers->flashAlert_get('new_user_set');
$get = $helpers->clearSlashes($_GET); 
if(isset($get['act']) && $get['act']=="del"){
	$row = base64_decode($get['rid']);
	$uid = base64_decode($get['uid']);
	//echo " DELETE FROM `retailer_bank_details` WHERE id = '".$row."' and user_id = '".$uid."' ";
	$mysqlClass->mysqlQuery(" DELETE FROM `retailer_bank_details` WHERE id = '".$row."' and user_id = '".$uid."' ");
	$helpers->redirect_page("retailer-banks-details");
}

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Retailer Bank Details (For Settlement)</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Retailer Bank Details (For Settlement)</li>
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
			<?php 
				if($msg){
					echo $helpers->alert_message($msg,"alert-success");
				} 
				if($user_details){
					echo $helpers->alert_message($user_details,"alert-info");
				}
			?>
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Retailer Bank Details (For Settlement)</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Name</th>
                  <th>Login Id</th>
                  <th>Mobile</th>
                  <th>Account No</th>
                  <th>Account Name</th>
                  <th>IFSC</th>
                  <th>Branch</th>
                  <th>Bank</th>
                  <th>Action</th>
                </tr>
                </thead>
				<tbody>
<?php
$sql = "select a.bank_name,
			   a.account_number,
			   a.account_name,
			   a.ifsc,
			   a.branch,
			   a.id,
			   a.user_id,
			   b.name,
			   b.user,
			   b.mobile
		from retailer_bank_details as a left join add_cust as b  on b.id = a.user_id  WHERE b.admin_id = '".$ADMIN_ID."' and b.usertype = 'RETAILER' and b.wl_id = '".$WL_ID."'";
$query = $mysqlClass->mysqlQuery($sql);
while($res = $query->fetch(PDO::FETCH_ASSOC)){
?>  
					<tr>
					  <td><?=$res['name']?></td>
					  <td><?=$res['user']?></td>
					  <td><?=$res['mobile']?></td>
					  <td><?=$res['account_number']?></td>
					  <td><?=$res['account_name']?></td>
					  <td><?=$res['ifsc']?></td>
					  <td><?=$res['branch']?></td>
					  <td><?=$res['bank_name']?></td>
					  <td>
						  <a href="retailer-banks-details?act=del&rid=<?=base64_encode($res['id'])?>&uid=<?=base64_encode($res['user_id'])?>"  class="btn btn-sm btn-dark mb-1" style="min-width:100px;"><i class="fas fa-trash"></i> Delete</a>
					  </td>
					</tr>
<?php }?>              
				
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
