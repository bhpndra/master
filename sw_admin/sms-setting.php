<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<!-- Bootstrap Color Picker -->
<link rel="stylesheet" href="<?=BASE_URL?>/dashboard/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>

<?php
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];

$msg = $helpers->flashAlert_get('sms_setting_fa');

$post = $helpers->clearSlashes($_POST); 
	if(isset($_POST['addNew'])){
		//print_r($post); die();	
		$values = array(
					"user_id" 				=> $USER_ID,
					"admin_id" 	 			=> $ADMIN_ID,
					"request_type"  		=> $post['requestType'],
					"param_msg_name"  		=> $post['messageFieldName'],
					"param_mobile_name"  	=> $post['mobFieldName'],
					"url"  					=> $post['url'],
					"api_parameters" 		=> json_encode(array_combine($post['apiFields'],$post['apiFieldsValue'])),
					"api_for"  				=> 'WL',
					);
		
		$mysqlClass->insertData(' sms_pack ', $values);		
		$msg0  = $helpers->flashAlert_set("sms_setting_fa","Setting Update Successfully.");
		$helpers->redirect_page("sms-setting");
	}

	if(isset($_POST['update'])){
		//print_r($post); die();	
		$values = array(
					"request_type"  		=> $post['requestType'],
					"param_msg_name"  		=> $post['messageFieldName'],
					"param_mobile_name"  	=> $post['mobFieldName'],
					"url"  					=> $post['url'],
					"api_parameters" 		=> json_encode(array_combine($post['apiFields'],$post['apiFieldsValue'])),
					"api_for"  				=> 'WL',
					);
		
		$mysqlClass->updateData(' sms_pack ', $values, " WHERE `user_id`='".$USER_ID."' and `admin_id`='".$ADMIN_ID."' ");		
		$msg0  = $helpers->flashAlert_set("sms_setting_fa","Setting Update Successfully.");
		$helpers->redirect_page("sms-setting");
	}
	
$resSD = $mysqlClass->mysqlQuery("select sms_api from `add_cust` WHERE `id`='".$USER_ID."' and `wl_id`='".$WL_ID."' and `admin_id`='".$ADMIN_ID."'" )->fetch(PDO::FETCH_ASSOC);
if($resSD['sms_api']=="CUSTOM"){
	$sms_pack = $mysqlClass->mysqlQuery("select * from `sms_pack` WHERE `user_id`='".$USER_ID."' and `admin_id`='".$ADMIN_ID."'" )->fetch(PDO::FETCH_ASSOC);
}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">SMS Setting</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">SMS Setting</li>
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
			<div class="col-md-8">
			<?php
				if($msg){
					echo $helpers->alert_message($msg,"alert-success");
				}

/* 				$smsParameters = json_decode($sms_pack['api_parameters'],true);
				$smsParameters[$sms_pack['param_mobile_name']] = '9716763608';
				$smsParameters[$sms_pack['param_msg_name']] = 'Test Message';
				$smsParameters['request_type'] = $sms_pack['request_type'];
				$smsParameters['url'] = $sms_pack['url'];
				echo $helpers->send_msg_dynamic($smsParameters); */
			?>
			  	<div class="card card-secondary">
				  <div class="card-header">
					<h3 class="card-title">SMS Switch</h3>
				  </div>
				  <div class="card-body row">
					<label class="col-sm-2 col-form-label">SMS API</label>
					<div class="col-sm-10 pt-1">					
						<input type="checkbox" name="my-checkbox" onchange="sms_switch(this)" <?=($resSD['sms_api']=="DEFAULT")? 'checked' : ''; ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Default" data-off-text="Custom API" data-size="small" />
					</div>
				  </div>
				  <div class="card-body " id="apiForm" <?=($resSD['sms_api']=="DEFAULT")? "style='display:none'" : ''; ?>>
	<?php if(!empty($sms_pack) && $sms_pack['id'] > 0){ ?>
					<form role="form" autocomplete="off" method="post">
						
						<div class="row">
							<div class="col-6 form-group">
								<label class="label">Field Name</label>
							</div>
							<div class="col-6 form-group">
								<label class="label">Field Value</label>
							</div>
						</div>
						<div class="controls">
						<?php
							$api_parameters = json_decode($sms_pack['api_parameters'],true);
							foreach($api_parameters as $k=>$v){ 
						?>
						<div class="row entry">
							<div class="form-group col-6">
								<input class="form-control" name="apiFields[]" type="text" value="<?=$k?>" placeholder="Field Name" required />
							</div>
							<div class="input-group col-6">
								<input class="form-control" name="apiFieldsValue[]" type="text" value="<?=$v?>" placeholder="Field Value" required />
								<span class="input-group-btn">
									<button class="btn btn-success btn-add" type="button">
										<span class="fas fa-plus"></span>
									</button>
								</span>
							</div>
						</div>
						<? } ?>
						</div>
						<hr/>
						<div class="row">
							<div class="col-md-6 form-group">
								<label class="label">Mobile Field Name</label>
								<input type="text" name="mobFieldName" class="form-control" value="<?=$sms_pack['param_mobile_name']?>" required />
							</div>
							<div class="col-md-6 form-group">
								<label class="label">Message Field Name</label>
								<input type="text" name="messageFieldName" class="form-control" value="<?=$sms_pack['param_msg_name']?>" required />
							</div>
							<div class="col-md-12 form-group">
								<label class="label">URL</label>
								<input type="text" name="url" class="form-control" value="<?=$sms_pack['url']?>" required />
							</div>
							<div class="col-md-6 form-group">
								<label class="label">Request Type</label>
								<div class="row">
									<div class="custom-control custom-radio mr-2">
									  <input class="custom-control-input" type="radio" value="POST" id="radio" name="requestType" <?=($sms_pack['request_type']=="POST")? 'checked' : '' ?> required />
									  <label for="radio" class="custom-control-label">POST</label>
									</div>
									<div class="custom-control custom-radio">
									  <input class="custom-control-input" type="radio" value="GET" id="radio1" name="requestType" <?=($sms_pack['request_type']=="GET")? 'checked' : '' ?> required />
									  <label for="radio1" class="custom-control-label">GET</label>
									</div>
								</div>
							</div>
						</div>
					  <div class="card-footer">
						<button type="submit" name="update" class="btn btn-info">Update</button>
					  </div>
					</form>
	<?php } else { ?>
					<form role="form" autocomplete="off" method="post">
						
						<div class="row">
							<div class="col-6 form-group">
								<label class="label">Field Name</label>
							</div>
							<div class="col-6 form-group">
								<label class="label">Field Value</label>
							</div>
						</div>
						<div class="controls">
						<div class="row entry">
							<div class="form-group col-6">
								<input class="form-control" name="apiFields[]" type="text" placeholder="Field Name" required />
							</div>
							<div class="input-group col-6">
								<input class="form-control" name="apiFieldsValue[]" type="text" placeholder="Field Value" required />
								<span class="input-group-btn">
									<button class="btn btn-success btn-add" type="button">
										<span class="fas fa-plus"></span>
									</button>
								</span>
							</div>
						</div>
						</div>
						<hr/>
						<div class="row">
							<div class="col-md-6 form-group">
								<label class="label">Mobile Field Name</label>
								<input type="text" name="mobFieldName" class="form-control" value="" required />
							</div>
							<div class="col-md-6 form-group">
								<label class="label">Message Field Name</label>
								<input type="text" name="messageFieldName" class="form-control" value="" required />
							</div>
							<div class="col-md-12 form-group">
								<label class="label">URL</label>
								<input type="text" name="url" class="form-control" value="" required />
							</div>
							<div class="col-md-6 form-group">
								<label class="label">Request Type</label>
								<div class="row">
									<div class="custom-control custom-radio mr-2">
									  <input class="custom-control-input" type="radio" value="POST" id="radio" name="requestType" required />
									  <label for="radio" class="custom-control-label">POST</label>
									</div>
									<div class="custom-control custom-radio">
									  <input class="custom-control-input" type="radio" value="GET" id="radio1" name="requestType" required />
									  <label for="radio1" class="custom-control-label">GET</label>
									</div>
								</div>
							</div>
						</div>
					  <div class="card-footer">
						<button type="submit" name="addNew" class="btn btn-info">Add</button>
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
<!-- bootstrap color picker -->
<script src="<?=BASE_URL?>dashboard/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script>
$(document).ready(function(){
    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });
});
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

function sms_switch(e){
	var swit = e;
	if ($(swit).prop('checked')==true){ 
        //alert('Default');
		updateAPI('DEFAULT');
		$('#apiForm').css('display','none');
    } else {
		updateAPI('CUSTOM');
		$('#apiForm').css('display','block');
	}
}
function updateAPI(switchVal){
	$.ajax({
		type: 'POST',
		data: {switchVal:switchVal},
		cache: false,
		url: 'ajax/sms_switch_update.php',
		success: function (response)
		{ 
			
		}
	});
}
</script>
</html>
