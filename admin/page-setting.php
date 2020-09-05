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


	if(isset($_POST['add']) ){
		$post = $helpers->clearSlashes($_POST); 
		$get = $helpers->clearSlashes($_GET); 
	
		$dataValue = array(
			'user_id' 				=> $USER_ID,
			'user_type' 			=> 'WL',
			'page_content' 			=> $post['content'],
			'page_id' 				=> $get['id'],
			);
		$mysqlClass->insertData(" page_content ", $dataValue);
	
		$helpers->redirect_page("page-setting");
	}

if(isset($_GET['pn']) && isset($_GET['id'])){
	$get = $helpers->clearSlashes($_GET); 
	if(isset($_POST['update']) ){
		$post = $helpers->clearSlashes($_POST);  
	
		$dataValue = array(
			'user_id' 				=> $USER_ID,
			'user_type' 			=> 'WL',
			'page_content' 			=> $post['content'],
			'page_id' 				=> $get['id'],
			);
		$mysqlClass->updateData(" page_content ", $dataValue, " where page_id = '".$get['id']."' and user_id = '".$USER_ID."'");	
		$helpers->redirect_page("page-setting");
	}
	$page = $mysqlClass->mysqlQuery("SELECT * FROM `page_content` where page_id = '".$get['id']."' and user_id = '".$USER_ID."' ")->fetch(PDO::FETCH_ASSOC);		
}


?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Manage Page Content</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Manage Page Content</li>
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
			<div class="col-md-9">
			<?=($msg!='')? $helpers->alert_message($msg,'alert-success') : '';?>
			  <div class="card card-success">
				<div class="card-header">
				  <h3 class="card-title"><?=(isset($get['pn'])) ? $get['pn'] : ''?></h3>

				</div>
				<?php if(isset($page['id'])){ ?>
					<form method="post" enctype="multipart/form-data">
					<div class="card-body pad">
					  <div class="mb-3">
						<textarea class="textarea" name="content" placeholder="Place some text here"
								  style="width: 100%; height: 500px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?=$page['page_content']?></textarea>
					  </div>
					</div>
					  <div class="card-footer">
						<button type="submit" name="update" class="btn btn-primary">Update</button>
					  </div>
					</form>
				<?php } else { ?>						
					<form method="post" enctype="multipart/form-data">
					<div class="card-body pad">
					  <div class="mb-3">
						<textarea class="textarea" name="content" placeholder="Place some text here"
								  style="width: 100%; height: 500px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
					  </div>
					</div>
					  <div class="card-footer">
						<button type="submit" name="add" class="btn btn-primary">Add</button>
					  </div>
					</form>
				<?php } ?>
				<!-- /.card-body -->
				</div>
			  <!-- /.card -->

			</div>

			<div class="col-md-3">
			<?=($msg!='')? $helpers->alert_message($msg,'alert-success') : '';?>
			  <div class="card card-success">
				<div class="card-header">
				  <h3 class="card-title">Manage Page Content</h3>

				</div>
				
<div class="card-body table-responsive">
				  <table id="example1" class="table table-bordered table-striped">
					<thead>
					<tr>
					  <th>Page</th>
					  <th>Action</th>
					</tr>
					</thead>
					<tbody>
<?php
		$pages = $mysqlClass->mysqlQuery("SELECT * FROM `page_list` ");
		while($res = $pages->fetch(PDO::FETCH_ASSOC)){
?>
					<tr>
					  <td><?=$res['page_title']?></td>
					  <td><a href="page-setting?id=<?=$res['page_id']?>&pn=<?=$res['page_title']?>">Edit</a></td>
					  
					</tr>
<?php } ?>  					
					</tbody>
					<tfoot>

					</tfoot>
				  </table>
				</div>
				</div>
			  <!-- /.card -->

			</div>
			
		</div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
<!-- Summernote -->
<script src="<?=DOMAIN_NAME?>dashboard/plugins/summernote/summernote-bs4.min.js"></script>
<script>
  $(function () {
    // Summernote
    $('.textarea').summernote({
  height: 500
})
  })
</script>
</html>
