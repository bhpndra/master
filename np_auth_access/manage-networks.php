<?php
include_once('inc/head.php');
include_once('inc/header.php');

if($_SESSION[_session_usertype_]!="ADMIN" && $_SESSION[_session_userid_]!=1){
	echo "<script> window.location = 'index.php'; </script>"; die();
}
?>
<?php
	$helpers = new helper_class();
	$post = $helpers->clearSlashes($_POST);
	$mysqlClass = new Mysql_class();
	//print_r($post);
?>
<!-- MAIN PANEL -->
<div class="page-wrapper-row full-height">
	<div class="page-wrapper-middle">
		<!-- BEGIN CONTAINER -->
		<div class="page-container">
			<!-- BEGIN CONTENT -->
			<div class="page-content-wrapper">
				<!-- BEGIN CONTENT BODY -->
				<!-- BEGIN PAGE HEAD-->
				
				<!-- END PAGE HEAD-->
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
								<span>Manage Networks</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Manage Networks </div>
							</div>
							<div class="portlet-body">
							<div class="row">
								<?php
									if(@$msg!=""){
										echo $msg;
									}
$sqlQuery = $mysqlClass->mysqlQuery("SELECT * FROM `network` ");							
								?>														
                                <div class="controls">
								<?php while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){  ?>
									<div class="col-md-12 entry">
										<div class="form-group col-sm-2">
											<input class="form-control" name="operator_name" type="text" value="<?=$rows['operator_name']?>" placeholder="operator_name" required />
											<input class="form-control" name="row_id" type="hidden" value="<?=$rows['id']?>" placeholder="" required />
										</div>
										<div class="form-group col-sm-1">
											<input class="form-control" name="commission" type="text" value="<?=$rows['commission']?>" placeholder="commission" required />
										</div>
										<div class="form-group col-sm-2">
											<select name="commission_type" class="form-control" required>
												<option <?=($rows['commission_type']=='PERCENT')? "selected" : "" ?>  value="PERCENT">PERCENT</option>
												<option <?=($rows['commission_type']=='FLAT')? "selected" : "" ?>  value="FLAT">FLAT</option>
											</select>
										</div>
										<div class="form-group col-sm-2">
											<input class="form-control" name="operator_type" type="text" value="<?=$rows['operator_type']?>" placeholder="operator_type" required />
										</div>
										<div class="form-group col-sm-2">
											<input class="form-control" name="np_operator_code" type="text" value="<?=$rows['np_operator_code']?>" placeholder="np_operator_code" required />
										</div>
										<div class="form-group col-sm-2">
											<select name="activated_operator" class="form-control" required>
												<option <?=($rows['activated_operator']==0)? "selected" : "" ?> value="0">Active</option>
												<option <?=($rows['activated_operator']==1)? "selected" : "" ?> value="1">Deactive</option>
											</select>
										</div>
										<div class="input-group col-sm-1">
											<button type="button" onclick="update_operator(this)" class="form-control btn btn-primary" name="update">Update</button>
										</div>
									</div>
								<?php } ?>
									<div class="col-md-12 entry">
										<div class="form-group col-sm-2">
											<input class="form-control" name="operator_name" type="text" value="" placeholder="operator_name" required />
											<input class="form-control" name="row_id" type="hidden" value="" placeholder="" required />
										</div>
										<div class="form-group col-sm-1">
											<input class="form-control" name="commission" type="text" value="" placeholder="commission" required />
										</div>
										<div class="form-group col-sm-2">
											<select name="commission_type" class="form-control" required>
												<option value="PERCENT">PERCENT</option>
												<option value="FLAT">FLAT</option>
											</select>
										</div>
										<div class="form-group col-sm-2">
											<input class="form-control" name="operator_type" type="text" value="" placeholder="operator_type" required />
										</div>
										<div class="form-group col-sm-2">
											<input class="form-control" name="np_operator_code" type="text" value="" placeholder="np_operator_code" required />
										</div>
										<div class="form-group col-sm-1">
											<select name="activated_operator" class="form-control" required>
												<option value="0">Active</option>
												<option value="1">Deactive</option>
											</select>
										</div>
										<div class="input-group col-sm-2">
											<button type="button" onclick="update_operator(this)" class="form-control btn btn-primary" name="update">Update</button>
											<span class="input-group-btn">
												<button class="btn btn-success btn-add" type="button">
													<span class="fa fa-plus"></span>
												</button>
											</span>
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
            .html('<span class="fa fa-minus"></span>');
    });
	$(document).on('click', '.btn-remove', function(e)
    {
		$(this).parents('.entry:first').remove();

		e.preventDefault();
		return false;
	});
});
function  update_operator(e){
	var btn = e;
	var operator_name = $(e).closest(".entry").find("input[name='operator_name']").val();
	var commission = $(e).closest(".entry").find("input[name='commission']").val();
	var operator_type = $(e).closest(".entry").find("input[name='operator_type']").val();
	var np_operator_code = $(e).closest(".entry").find("input[name='np_operator_code']").val();
	var row_id = $(e).closest(".entry").find("input[name='row_id']").val();
	var activated_operator = $(e).closest(".entry").find("select[name='activated_operator'] option:selected").val();
	var commission_type = $(e).closest(".entry").find("select[name='commission_type'] option:selected").val();
	
	if(row_id==''){
		$.ajax({
			type: 'POST',
			data: {operator_name:operator_name, commission:commission, operator_type:operator_type, np_operator_code:np_operator_code, activated_operator:activated_operator, submit:'add', commission_type:commission_type },
			cache: false,
			url: 'ajax/manage-networks.php',
			success: function (response){ 
				$(btn).html('Done');
			}
		 });
	} else {
		$.ajax({
			type: 'POST',
			data: {operator_name:operator_name, commission:commission, operator_type:operator_type, np_operator_code:np_operator_code, activated_operator:activated_operator, submit:'update' , row_id:row_id, commission_type:commission_type },
			cache: false,
			url: 'ajax/manage-networks.php',
			success: function (response){ 
				$(btn).html('Done');
			}
		 });
		
	}
}
</script>
</body>
</html>
