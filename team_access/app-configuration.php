<?php 
	// Requred files included
	include_once('inc/head.php'); 
	include_once('inc/header.php'); 
	include_once('classes/user_class.php'); 
	
	//Object initiated
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	$userClass = new user_class();
	
	//Store App URL/Configurations
	$msg = "";
	$post = $helper->clearSlashes($_POST);
	if(isset($post['add_url']))
	{
		// Check form data not blank
		if($post['user_id']!='' && $post['app_link']!='')
		{
			if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$post['app_link'])) {
				$nameErr = "Only letters and white space allowed";
				@$msg .= "<div class=\"alert alert-danger alert-dismissable\">";
					$msg .= "Sorry!! App URL is not valid";
				$msg .= "</div>";	
				
			}
			else{				
				//Check app url is already exist or not
				$qryAppURL = $mysqlObj->countRows("select * from `general_settings` WHERE `user_id`='".$post['user_id']."' AND app_link!='' ");
				if($qryAppURL >0)
				{
					@$msg .= "<div class=\"alert alert-danger alert-dismissable\">";
						$msg .= "Sorry!! App URL Already Exist";
					$msg .= "</div>";	
				}
				else{
					$qryAppURL = $mysqlObj->mysqlQuery("UPDATE `general_settings` SET app_link = '".$post['app_link']."' WHERE `user_id`='".$post['user_id']."' ");
					@$msg .= "<div class=\"alert alert-success alert-dismissable\">";
						$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
									&times;
							  </button>";
						$msg .= "App URL set successfully.";
					$msg .= "</div>";			 	
				}
			}			
		}
		else{
			echo "<script> alert('Sorry!! Form data can`t blank.')</script>"; 
		}
	}		
	
	
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
                                        <a href="index.php">Others</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
									<li>
                                        <span>App Configuration</span>
                                    </li>
                                </ul>
								
								 <!-- END PAGE BREADCRUMBS -->
								<div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>App Configuraion
													</div>
                                                </div>
												<div class="portlet-body">
												<?php
													if(@$msg!=""){
														echo $msg;
													}
												
												?>	
												<div class="row">
													<form method="post">
														<div class="form-group col-md-6">
															<label>Domain List</label>
															<div>
																<select class='form-control' name='user_id' required onChange="get_site_name(this.value)">
																		<option value="">All Domain List</option>
																	<?
																		$qry = "SELECT domain,whitelabel_id,user_id FROM `add_white_label` ";
																		$domains = $mysqlObj->mysqlQuery($qry);
																		while($rows = $domains->fetch(PDO::FETCH_ASSOC)){
																	?>
																		<option value="<?=$rows['user_id'];?>"><?=$rows['domain'];?></option>
																	<? } ?>
																</select>
																<span id="site_name" class="text-primary"></span>
															</div>
														</div>
														<div class="form-group col-md-6">
															<label>App URL</label>
															<div>
																<input type='text' id="app_link" name='app_link' class='form-control' placeholder="App URL" required>
															</div>
														</div>	
														<div class="clearfix"></div>
														<div class="form-group col-md-2">
															<label style="opacity:0">&nbsp;</label>
															<div>
																<input type="submit" name="add_url" value="Add URL" class="btn btn-success">
															</div>
														</div>
													</form>
												</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                                <!-- BEGIN PAGE CONTENT INNER -->
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php include_once('inc/footer.php'); ?>

<script>
	function get_site_name(uid)
	{
		$.ajax({
			url:"ajax/get_site_name.php",
			type: 'POST',
			data:{user_id:uid},
			success:function(data){
				$("#site_name").html(data);
			}
		})
	}
</script>
	
</body>
</html>
