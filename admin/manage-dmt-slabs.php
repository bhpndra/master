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

$msg0 = $helpers->flashAlert_get('slabMessage');

$post = $helpers->clearSlashes($_POST); 


	if(isset($_POST['addDMT'])){
		foreach($post['slabName'] as $k=>$v){
			$values = array(
						"wl_user_id" 	=> $USER_ID,
						"amount_from" 	=> $post['amtFrom'][$k],
						"amount_to"  	=> $post['amtTo'][$k],
						"slab_network"  => 'DMT'.($k+1),
						"slab_name"  	=> $v
						);
			
			$mysqlClass->insertData(' dmt_slabs ', $values);
		}		
		$msg0  = $helpers->flashAlert_set("slabMessage","DMT Slab Added successfully.");
		$helpers->redirect_page("manage-dmt-slabs");
	}

	if(isset($_POST['updateDMT'])){
		//$mysqlClass->mysqlQuery("DELETE FROM `dmt_slabs`  where `wl_user_id` = '".$USER_ID."'  ");
		foreach($post['slabName'] as $k=>$v){
			$values = array(
						"amount_from" 	=> $post['amtFrom'][$k],
						"amount_to"  	=> $post['amtTo'][$k],
						"slab_network"  => 'DMT'.($k+1),
						"slab_name"  	=> $v
						);
			
			$mysqlClass->updateData(' dmt_slabs ', $values, " where id = '".$post['slabId'][$k]."' ");	
		}
		$msg0  = $helpers->flashAlert_set("slabMessage","DMT Slab Updated successfully.");
		$helpers->redirect_page("manage-dmt-slabs");
	}
	
$dmt_slab = $mysqlClass->fetchAllData(" dmt_slabs "," * ","  where `wl_user_id` = '".$USER_ID."' "); 

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Slabs Setting</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Slabs Setting</li>
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
			<div class="col-md-10">
			<?php
				if($msg0){
					echo $helpers->alert_message($msg0,"alert-success");
				}

			?>

				<div class="card card-primary">
				  <div class="card-header">
					<h3 class="card-title">DMT Slabs</h3>
				  </div>
				  <div class="card-body p-3" >
			<?php
				if(count($dmt_slab) > 0){
			?>
					<form role="form" autocomplete="off" method="post">
						
						<div class="row">
							<div class="col-4 form-group">
								<label class="label">Amt From</label>
							</div>
							<div class="col-4 form-group">
								<label class="label">Amt To</label>
							</div>
							<div class="col-4 form-group">
								<label class="label">Slab Name</label>
							</div>
						</div>
						<div class="controls">
						<?php
							
							foreach($dmt_slab as $k2=>$v2){ 
						?>
						<div class="row entry">
							<div class="form-group col-4">
								<input class="form-control" name="amtFrom[]" value="<?=$v2['amount_from']?>" type="text" onchange="amountFrom(this)" placeholder="Amount From" required />
							</div>
							<div class="form-group col-4">
								<input class="form-control" name="amtTo[]" value="<?=$v2['amount_to']?>" type="text" onblur="amountTo(this)" placeholder="Amount To" required />
							</div>
							<div class="form-group col-4">
								<input class="form-control" name="slabName[]" value="<?=$v2['slab_name']?>" type="text" placeholder="Slab Name" required />
								<input class="form-control" name="slabId[]" value="<?=$v2['id']?>" type="hidden" required />
							</div>
						</div>
						<? } ?>
						</div>
					  <div class="card-footer">
						<button type="submit" name="updateDMT" class="btn btn-info">Update</button>
					  </div>
					</form>
	<?php } else { ?>
					<form role="form" autocomplete="off" method="post">
						
						<div class="row">
							<div class="col-4 form-group">
								<label class="label">Amt From</label>
							</div>
							<div class="col-4 form-group">
								<label class="label">Amt To</label>
							</div>
							<div class="col-4 form-group">
								<label class="label">Slab Name</label>
							</div>
						</div>
						<div class="controls">
						<div class="row entry">
							<div class="form-group col-4">
								<input class="form-control" name="amtFrom[]" type="text" onchange="amountFrom(this)" placeholder="Amount From" required />
							</div>
							<div class="form-group col-4">
								<input class="form-control" name="amtTo[]" type="text" onblur="amountTo(this)" placeholder="Amount To" required />
							</div>
							<div class="input-group col-4">
								<input class="form-control" name="slabName[]" type="text" placeholder="Slab Name" required />
								<span class="input-group-btn">
									<button class="btn btn-success btn-add" type="button">
										<span class="fas fa-plus"></span>
									</button>
								</span>
							</div>
						</div>
						</div>
					  <div class="card-footer">
						<button type="submit" name="addDMT" class="btn btn-info">Add</button>
					  </div>
					</form>
<?php } ?>
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
<script>

$(document).ready(function(){
    $(document).on('click', '.btn-add', function(e)
    { //alert();
        e.preventDefault();

        var controlForm = $('.controls'),
            currentEntry = $(this).parents('.entry:first'),
            newEntry = $(currentEntry.clone()).appendTo(controlForm);

        newEntry.find('input').val('');
        controlForm.find('.entry:not(:last) .btn-add')
            .removeClass('btn-add').addClass('btn-remove')
            .removeClass('btn-success').addClass('btn-danger')
            .html('<span class="fas fa-minus"></span>');
    });
	$(document).on('click', '.btn-remove', function(e)
    {
		$(this).parents('.entry:first').remove();

		e.preventDefault();
		return false;
	});
});
function amountTo(e){
	var amtFrom = $(e).closest(".entry").find("input[name='amtFrom[]']").val();
	var amtTo = $(e).val();
	if(parseFloat(amtFrom) >= parseFloat(amtTo)){
		alert("Invalid Slab range !" );
		$(e).val('');
	}
}
function amountFrom(e){
	
}
</script>
</html>
