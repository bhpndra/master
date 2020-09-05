<?php
include_once('inc/head.php');
include_once('inc/header.php');
$helpers = new helper_class();
$mysqlClass = new Mysql_class();
?>
<?php

	if(isset($_POST['submit'])){
		
		$post = $helpers->clearSlashes($_POST);
		if($post['usertype']=="api_retailer"){
			$data = array(
				'`package_id`' =>  $post['package_id']
					);
			echo $mysqlClass->updateData('add_retailer', $data, " WHERE `user_id`='".$post['userID']."' ");			
			@$msg .= "<div class=\"alert alert-success alert-dismissable\">";
				$msg .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
							&times;
					  </button>";
				$msg .= "Success! Package Updated for api retailer...";
			$msg .= "</div>";
		}
					
	}
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
								<span>Add Api Retailer</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
							<header>
									Api Commission Packages
							</header>
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12">
								<?php
									if(@$msg!=""){
										echo $msg;
									}
								
								?>
								<div class="well" style="min-height:300px;" >															
                                <div class="col-md-12 ">
									<form method ="post" class="smart-form" action ="" enctype="multipart/form-data">
										
													
										<div class="form-group col-md-3">
											<label>User Type:*</label>
											<div>
												<select name="usertype" class="form-control" onChange="getRetailerDistributor(this.value)" required="required" >
													<option value="">Select UserType</option>
													<option value="api_retailer">Api Retailers</option>
												</select>
											</div>
										</div>
										<div class="form-group col-md-3">
											<label>Select User:*</label>
											<div>
												<select name="userID" id="usernames" class="form-control" >
													<option disabled selected>Select User</option>
                                                </select>
											</div>
										</div>
										<div class="form-group col-md-3">
											<label>Package:*</label>
											<div>
											<?php
												$mysQuery = $mysqlClass->mysqlQuery("
												select * from package_list WHERE created_by='ADMIN' && `creator_id`='" . $_SESSION[_session_userid_] . "'
												");
											?>
												<select  class='form-control' name="package_id" required>
													<option value="" selected disabled>API user</option>
												<?php
													$num = $mysQuery->rowCount();
													if($num>=0){
														while ($row = $mysQuery->fetch(PDO::FETCH_ASSOC)){
												?>
															<option value="<?php echo $row['id']; ?>" <?php if (@$_POST['package_id'] == $row['id']) {
                                                                    echo "selected";
                                                                } ?>><?php echo $row['package_name']; ?></option>
												<?php
														}
													}
												?>
												</select>
											</div>
										</div>
										<div class="form-group col-md-2">
											<label style="opacity:0">Button</label>
											<div>
												<input type="submit" name="submit" class="btn btn-success" value="Set Package">
											</div>
										</div>

									</form>
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
    function getRetailerDistributor(usertype) {

        $.ajax({
            url: 'ajax/get_retailerdistributor.php',
            cache: false,
            type: 'POST',
            data: {usertype: usertype, master: <?php echo $_SESSION[_session_userid_] ?>},
            success: function (response) {
                $("#usernames").html(response);
            }
        });
    }
</script>
</body>
</html>
