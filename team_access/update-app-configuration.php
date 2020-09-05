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
	
	$post = $helper->clearSlashes($_POST);
	if(isset($post['update_url']))
	{
		// Check form data not blank
		if($post['user_id']!='' && $post['app_link']!='')
		{
			if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$post['app_link'])) {
				$nameErr = "Only letters and white space allowed";
				echo "<script> alert('Sorry!! App URL is not valid.')</script>"; 
			}
			else{				
				$qryDomain = $mysqlObj->countRows("select * from `add_white_label` WHERE user_id='".$post['user_id']."'");
				if($qryDomain>0)
				{
					$qryWLUser_id = $mysqlObj->mysqlQuery("select * from `add_white_label` WHERE user_id='".$post['user_id']."' ")->fetch(PDO::FETCH_ASSOC);
					
					$qryAppURL = $mysqlObj->mysqlQuery("UPDATE `general_settings` SET app_link = '".$post['app_link']."' WHERE `user_id`='".$qryWLUser_id['user_id']."' ");
					@$msg .= "<div class=\"alert alert-success alert-dismissable\">";
						$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
									&times;
							  </button>";
						$msg .= "Domain/App URL updated successfully.";
					$msg .= "</div>";	
				}
				else{
					
				}	
				
			}			
		}
		else{
			echo "<script> alert('Sorry!! Form data can`t blank.')</script>"; 
		}
	}		
	$get = $helper->clearSlashes($_GET);
	
	
	// Query for fetch data using id
	$userid = base64_decode($get['id']);
	$qrySel = $mysqlObj->countRows("SELECT user_id, app_link FROM `general_settings` WHERE user_id='".$userid."' ");
	if($qrySel>0)
	{
		$data = $mysqlObj->mysqlQuery("SELECT user_id, app_link FROM `general_settings` WHERE user_id='".$userid."' ")->fetch(PDO::FETCH_ASSOC);		
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
																<select class='form-control' name='user_id' required>
																		<option value="">All Domain List</option>
																	<?
																		$qry = "SELECT domain,whitelabel_id,user_id FROM `add_white_label` ";
																		$domains = $mysqlObj->mysqlQuery($qry);
																		while($rows = $domains->fetch(PDO::FETCH_ASSOC)){
																	?>
																		<option value="<?=$rows['user_id'];?>" <? if($data['user_id'] ==$rows['user_id'] ){echo "selected";}?>><?=$rows['domain'];?></option>
																	<? } ?>
																</select>
															</div>
														</div>
														<div class="form-group col-md-6">
															<label>App URL</label>
															<div>
																<input type='text' id="app_link" name='app_link' class='form-control' placeholder="App URL" value="<?=$data['app_link']?>" required >
															</div>
														</div>									
														<div class="form-group col-md-2">
															<label style="opacity:0">&nbsp;</label>
															<div>
																<input type="submit" name="update_url" value="Update URL" class="btn btn-success" >
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
	
</body>
</html>
