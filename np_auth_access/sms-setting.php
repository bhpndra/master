<?php 
	include_once('inc/head.php'); 
	include_once('inc/header.php'); 
	include_once('classes/user_class.php'); 

	$helpers = new helper_class();	
	$mysqlClass = new Mysql_class();
	
	$ADMIN_ID = $_SESSION[_session_userid_];	

	$post = $helpers->clearSlashes($_POST);
	
	if(isset($_POST['addNew'])){
		//print_r($post); die();	
		$values = array(
					"user_id" 				=> '',
					"admin_id" 	 			=> $ADMIN_ID,
					"request_type"  		=> $post['requestType'],
					"param_msg_name"  		=> $post['messageFieldName'],
					"param_mobile_name"  	=> $post['mobFieldName'],
					"url"  					=> $post['url'],
					"api_parameters" 		=> json_encode(array_combine($post['apiFields'],$post['apiFieldsValue'])),
					"api_for"  				=> 'ADMIN',
					);
		
		$mysqlClass->insertData(' sms_pack ', $values);
		echo "<script>alert('Data Inserted Successfully')</script>";
		
	}

	if(isset($_POST['update'])){
		//print_r($post); die();	
		$values = array(
					"request_type"  		=> $post['requestType'],
					"param_msg_name"  		=> $post['messageFieldName'],
					"param_mobile_name"  	=> $post['mobFieldName'],
					"url"  					=> $post['url'],
					"api_parameters" 		=> json_encode(array_combine($post['apiFields'],$post['apiFieldsValue'])),
					"api_for"  				=> 'ADMIN',
					);
		
		$mysqlClass->updateData(' sms_pack ', $values, " WHERE `admin_id`='".$ADMIN_ID."' and user_id='0' and api_for='ADMIN'");		
		echo "<script>alert('Data Updated Successfully')</script>";
	}
	

		$sms_pack = $mysqlClass->mysqlQuery("select * from `sms_pack` WHERE `admin_id`='".$ADMIN_ID."' and user_id='0' and api_for='ADMIN' " )->fetch(PDO::FETCH_ASSOC);

?>
        <div class="page-wrapper-row full-height">
            <div class="page-wrapper-middle">
                <!-- BEGIN CONTAINER -->
                <div class="page-container">
                    <!-- BEGIN CONTENT -->
                    <div class="page-content-wrapper">
                        <!-- BEGIN CONTENT BODY -->
                        <!-- BEGIN PAGE CONTENT BODY -->
                        <div class="page-content">
                            <div class="container">
                                <!-- BEGIN PAGE BREADCRUMBS -->
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="index.html">Home</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <span>Dashboard</span>
                                    </li>
                                </ul>
                                <!-- END PAGE BREADCRUMBS -->
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="portlet box blue">
											<div class="portlet-title">
												<div class="caption">
													<i class="fa fa-cogs"></i> SMS Setting
												</div>
											</div>
											<div class="portlet-body">
												<div class="">
													<div class="row show-grid">													
					
														<div id="datatable_col_reorder_filter" class="dataTables_filter">
															
	<div class="card-body " id="apiForm" >
		<?php if(!empty($sms_pack) && $sms_pack['id'] > 0){ ?>
		<form role="form" autocomplete="off" method="post">
			
						
				<div class="col-md-5 form-group">
					<label class="control-label">Field Name</label>
				</div>
				<div class="col-md-7 form-group">
					<label class="control-label">Field Value</label>
				</div>
			
			<div class="controls">		
				<?php
					$api_parameters = json_decode($sms_pack['api_parameters'],true);
					foreach($api_parameters as $k=>$v){ 
				?>
			<div class="entry">	
				<div class="form-group col-md-6">
					<div>
						<input type='text' id="fByTranid" name="apiFields[]" class='form-control' value="<?=$k?>" placeholder="Field Name" >
					</div>
				</div>
				<div class="form-group col-md-5">
					<div>
						<input type='text' id="fByTranid" name="apiFieldsValue[]" class='form-control' value="<?=$v?>" placeholder="Field Value" >
					</div>
				</div>
				<div class="form-group col-md-1">
					<span class="input-group-btn">
						<button class="btn btn-success btn-add" type="button">
							<span class="fa fa-plus"></span>
						</button>
					</span>
				</div>
			</div>
				<? } ?>
			</div>
			
			<div class="col-md-6 form-group">
				<label class="control-label">Mobile Field Name</label>
				<input type="text" name="mobFieldName" class="form-control" value="<?=$sms_pack['param_mobile_name']?>" required />
			</div>
			<div class="col-md-6 form-group">
				<label class="control-label">Message Field Name</label>
				<input type="text" name="messageFieldName" class="form-control" value="<?=$sms_pack['param_msg_name']?>" required />
			</div>
			<div class="col-md-12 form-group">
				<label class="label">URL</label>
				<input type="text" name="url" class="form-control" value="<?=$sms_pack['url']?>" required />
			</div>
			<div class="col-md-12 form-group">
				<label class="control-label">Request Type</label>
			</div>
			<div class="col-md-2">
				<div class="custom-control custom-radio mr-2">
				  <input class="custom-control-input" type="radio" value="POST" id="radio" name="requestType" <?=($sms_pack['request_type']=="POST")? 'checked' : '' ?> required />
				  <label for="radio" class="custom-control-label">POST</label>
				</div>
			</div>
			<div class="col-md-2">
				<div class="custom-control custom-radio">
				  <input class="custom-control-input" type="radio" value="GET" id="radio1" name="requestType" <?=($sms_pack['request_type']=="GET")? 'checked' : '' ?> required />
				  <label for="radio1" class="custom-control-label">GET</label>
				</div>
			</div>
			<div class="clearfix"></div><br>
			<div class="form-group col-md-12">
				<div>
					<input type="submit" name="update" class="btn btn-info" value="Update">
				</div>
			</div>
			
			<br>
		</form>
		
<?php } else { ?>

		<form role="form" autocomplete="off" method="post">			
			
			<div class="col-md-6 form-group">
				<label class="control-label">Field Name</label>
			</div>
			<div class="col-md-6 form-group">
				<label class="control-label">Field Value</label>
			</div>			
			<div class="controls">
				<div class="entry">
					<div class="form-group col-md-6">
						<input class="form-control" name="apiFields[]" type="text" placeholder="Field Name" required />
					</div>
					<div class="form-group col-md-5">
						<div>
							<input type='text' id="fByTranid" name="apiFieldsValue[]" class='form-control' placeholder="Field Value" >
						</div>
					</div>
					<div class="form-group col-md-1">
						<span class="input-group-btn">
							<button class="btn btn-success btn-add" type="button">
								<span class="fa fa-plus"></span>
							</button>
						</span>
					</div>			
					
				</div>		
			</div>		
			
			<div class="col-md-6 form-group">
				<label class="control-label">Mobile Field Name</label>
				<input type="text" name="mobFieldName" class="form-control" value="" required />
			</div>
			<div class="col-md-6 form-group">
				<label class="control-label">Message Field Name</label>
				<input type="text" name="messageFieldName" class="form-control" value="" required />
			</div>
			<div class="col-md-12 form-group">
				<label class="control-label">URL</label>
				<input type="text" name="url" class="form-control" value="" required />
			</div>
			
			<div class="col-md-12 form-group">
				<label class="control-label">Request Type</label>
			</div>
			<div class="col-md-2">
				<div class="custom-control custom-radio mr-2">
				  <input class="custom-control-input" type="radio" value="POST" id="radio" name="requestType" required />
				  <label for="radio" class="custom-control-label">POST</label>
				</div>
			</div>
			<div class="col-md-2">
				<div class="custom-control custom-radio">
				  <input class="custom-control-input" type="radio" value="GET" id="radio1" name="requestType" required />
				  <label for="radio1" class="custom-control-label">GET</label>
				</div>
			</div>
			<div class="clearfix"></div><br>		
			
			<div class="form-group col-md-12">
				<div>
					<input type="submit" name="addNew" class="btn btn-info" value="Add">
				</div>
			</div>			
		</form>
		
		<?php } ?>
		
	</div>
														</div>
													</div>									
												</div>
											</div>
										</div>
										</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php include_once('inc/footer.php'); ?>

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script>
$(document).ready(function(){
    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });
});
$(document).ready(function(){
    $(document).on('click', '.btn-add', function(e)
    { 
        e.preventDefault();

        var controlForm = $('.controls'),
            currentEntry = $(this).parents('.entry:first'),
            newEntry = $(currentEntry.clone()).appendTo(controlForm);

        newEntry.find('input').val('');
        controlForm.find('.entry:not(:last) .btn-add')
            .removeClass('btn-add').addClass('btn-remove')
            .removeClass('btn-success').addClass('btn-danger')
            .html('<span class="fa fa-minus"></span>');
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

</body>
</html>