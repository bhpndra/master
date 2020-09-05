<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php include_once('classes/user_class.php'); ?>
<?php
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	//echo $helper->hashPin('1234')['encrypted'];

	if(isset($_GET['act']) && $_GET['act']=='edit'){
		$get = $helper->clearSlashes($_GET);
		$row = $mysqlObj->get_field_data(" * ", 'bank_details', " where id = '".$get['id']."' and admin_id = '".$_SESSION[_session_userid_]."' and user_id = '0'");
		//print_r($row);
		if(isset($_POST['update'])){
			unset($_POST['update']);
			$post = $helper->clearSlashes($_POST);
			$id = $mysqlObj->updateData('bank_details', $post, " where id = '".$get['id']."' and admin_id = '".$_SESSION[_session_userid_]."' and user_id = '0'");
			if($id > 0){
				echo "<script> window.location = 'admin-bank-details.php'; </script>"; die();
			}
		}
	}
	
	if(isset($_POST['add'])){
		unset($_POST['add']);
		$post = $helper->clearSlashes($_POST);
		$post['admin_id'] = $_SESSION[_session_userid_];
		$post['user_id'] = 0;
		$id = $mysqlObj->insertData('bank_details', $post);
		if($id > 0){
			echo "<script> window.location = 'admin-bank-details.php'; </script>"; die();
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
                                        <a href="index.html">Home</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <span>Banks Details</span>
                                    </li>
                                </ul>

                                <!-- END PAGE BREADCRUMBS -->
								<div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>Add Banks</div>
                                                </div>
												<div class="portlet-body">
												<div class="row">
												<?php if(isset($_GET['act']) && $_GET['act']=='edit'){ ?>
													<form method="post" >
															<div class="form-group col-md-3">
																<label>Bank Name</label>
																<div>
																	<input type='text' id="fByUsername" name='bank_name' value="<?=$row['bank_name']?>"  class='form-control' placeholder="" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Account No.</label>
																<div>
																	<input type='text' id="fByUserid" name='account_number' value="<?=$row['account_number']?>"  class='form-control' placeholder="" >
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label>IFSC</label>
																<div>
																	<input type='text' name='ifsc' value="<?=$row['ifsc']?>"  class='form-control' placeholder="" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Branch Name</label>
																<div>
																	<input type='text' id="fBycname" name='branch' value="<?=$row['branch']?>"  class='form-control' placeholder="" >
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="update" value="Update" class="btn btn-primary">
																</div>
															</div>
														</form>
												<?php } else { ?>
													<form method="post" >
															<div class="form-group col-md-3">
																<label>Bank Name</label>
																<div>
																	<input type='text' id="fByUsername" name='bank_name' value=""  class='form-control' placeholder="" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Account No.</label>
																<div>
																	<input type='text' id="fByUserid" name='account_number' value=""  class='form-control' placeholder="" >
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label>IFSC</label>
																<div>
																	<input type='text' name='ifsc' value=""  class='form-control' placeholder="" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Branch Name</label>
																<div>
																	<input type='text' id="fBycname" name='branch' value=""  class='form-control' placeholder="" >
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="add" value="Add" class="btn btn-primary">
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
								
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>All Pending Fund Request</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" width="30px;">#</th>
                                                                    <th scope="col" >Bank Name</th>
                                                                    <th scope="col" width="">Account No.</th>
                                                                    <th scope="col" width="">IFSC</th>
                                                                    <th scope="col" width="">Branch</th>
                                                                    <th scope="col" width="">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
<?php
// for Pagination
$orderBy = " ";
$i = 0;
$sql = "SELECT * FROM `bank_details`  where admin_id = '".$_SESSION[_session_userid_]."' and user_id = '0'";	
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$orderBy);	
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
			?>
									<tr>
										<td width="50px;" class="topup_trid"><?= ++$i; ?> </td>
										<td width="50px;" class="topup_trid"><?=$rows['bank_name']; ?> </td>
										<td width="50px;" class="topup_trid"><?=$rows['account_number']; ?> </td>
										<td width="50px;" class="topup_trid"><?=$rows['ifsc']; ?> </td>
										<td width="50px;" class="topup_trid"><?=$rows['branch']; ?> </td>
										<td width="50px;" class="topup_trid"><a class="btn btn-sm btn-primary" href="admin-bank-details.php?id=<?=$rows['id']; ?>&act=edit">Edit</a></td>
										
									</tr>
				<?php } ?>
                                                            </tbody>
                                                        </table>
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
</body>
</html>
</html>