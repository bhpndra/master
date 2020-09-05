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
$id = $msg = '';

$post = isset($_POST) ? $helpers->clearSlashes($_POST) : ''; 
$get = isset($_GET) ? $helpers->clearSlashes($_GET) : '';
$id = base64_decode($get['id']);

	if(isset($_POST['add'])){
			unset($post['add']);
			$post['user_id'] = $USER_ID;
			$post['admin_id'] = $ADMIN_ID;
			$id = $mysqlClass->insertData(" bank_details ", $post);
			if($id>0){
				$helpers->flashAlert_set('add_bank'," Bank added successfully. ");
				$helpers->redirect_page("manage-banks");
			} else {
				$helpers->flashAlert_set('add_bank'," Something Wrong. ");
				$helpers->redirect_page("manage-banks");
			}
			
	}
	
	if(isset($_POST['update'])){
			unset($post['update']);
			$id = $mysqlClass->updateData(" bank_details ", $post, " WHERE `id` = '".$id."' and `user_id`='" . $USER_ID . "' and `admin_id`='" . $ADMIN_ID . "' ");
			$helpers->flashAlert_set('add_bank'," Bank Update successfully. ");
			$helpers->redirect_page("manage-banks?act=edit&id=".$_GET['id']);
	}

$msg = $helpers->flashAlert_get('add_bank');

if(isset($_GET['act']) && $_GET['act']=="edit"){	
	$bankDetail = $mysqlClass->mysqlQuery("SELECT * FROM `bank_details` WHERE `id` = '".$id."' and `user_id`='" . $USER_ID . "' and `admin_id`='" . $ADMIN_ID . "' ")->fetch(PDO::FETCH_ASSOC);	
}

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Manage Banks</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Manage Banks</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
	  
		<div class="row justify-content-center">
		
			<div class="col-md-6">
			<?=($msg!='')? $helpers->alert_message($msg,'alert-success') : '';?>
			<?php if(isset($bankDetail['id']) && $bankDetail['id']==$id){ ?>
			  <div class="card card-dark">
				<div class="card-header">
				  <h3 class="card-title">Update Bank Details</h3>

				</div>
				<form method="post" enctype="multipart/form-data">
				<div class="card-body row">
				  <div class="form-group col-md-6">
					<label for="inputName">Bank Name</label>
					<input type="text"  class="form-control" value="<?=isset($bankDetail['bank_name'])? $bankDetail['bank_name'] : ''?>" name="bank_name" required >
				  </div>
				  <div class="form-group col-md-6">
					<label for="inputName">Account No.</label>
					<input type="text"  class="form-control" value="<?=isset($bankDetail['account_number'])? $bankDetail['account_number'] : ''?>" name="account_number" required >
				  </div>
				  <div class="form-group col-md-6">
					<label for="inputName">IFSC</label>
					<input type="text"  class="form-control" value="<?=isset($bankDetail['ifsc'])? $bankDetail['ifsc'] : ''?>" name="ifsc" required >
				  </div>
				  <div class="form-group col-md-6">
					<label for="inputName">Branch</label>
					<input type="text"  class="form-control" value="<?=isset($bankDetail['branch'])? $bankDetail['branch'] : ''?>" name="branch" required >
				  </div>
				  
				</div>
				  <div class="card-footer">
					<button type="submit" name="update" class="btn btn-primary">Update</button>
				  </div>
				</form>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->
			
			<?php } else { ?>
			  <div class="card card-dark">
				<div class="card-header">
				  <h3 class="card-title">Add Bank Details</h3>

				</div>
				<form method="post" enctype="multipart/form-data">
				<div class="card-body row">
				  <div class="form-group col-md-6">
					<label for="inputName">Bank Name</label>
					<input type="text"  class="form-control" value="" name="bank_name" required >
				  </div>
				  <div class="form-group col-md-6">
					<label for="inputName">Account No.</label>
					<input type="text"  class="form-control" value="" name="account_number" required >
				  </div>
				  <div class="form-group col-md-6">
					<label for="inputName">IFSC</label>
					<input type="text"  class="form-control" value="" name="ifsc" required >
				  </div>
				  <div class="form-group col-md-6">
					<label for="inputName">Branch</label>
					<input type="text"  class="form-control" value="" name="branch" required >
				  </div>
				  
				</div>
				  <div class="card-footer">
					<button type="submit" name="add" class="btn btn-primary">Add</button>
				  </div>
				</form>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->
			<?php } ?>

			</div>
			
			<div class="col-md-6">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Manage Banks</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Bank Name</th>
                  <th>A/C No.</th>
                  <th>Action</th>
                </tr>
                </thead>
				<tbody>
<?php
$bankDetails = $mysqlClass->fetchAllData("bank_details"," * ", " where user_id = '".$USER_ID."' and admin_id = '".$ADMIN_ID."'");
foreach($bankDetails as $bk){
?>  
					<tr>
					  <td><?=$bk['bank_name']?></td>
					  <td><?=$bk['account_number']?></td>
					  <td><a href="manage-banks?act=edit&id=<?=base64_encode($bk['id'])?>" class="btn btn-sm btn-success">Edit</a></td>
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
        buttons: ['colvis' ]
    } );
 
    table.buttons().container()
        .appendTo( '#example1_wrapper .col-md-6:eq(0)' );
} );
</script>
</html>
