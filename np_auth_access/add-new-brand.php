<?php
include_once('inc/head.php');
include_once('inc/header.php'); 
include_once 'classes/vouchers.php';
include_once 'classes/vouchers_type.php';

$vouchers = new vouchers();
$voucherType = new vouchers_type();


if (isset($_POST['submit'])){


	$helpers = new helper_class();
	$post = $helpers->clearSlashes($_POST);

	
    $vouchers->brand_name = $post['brand_name'];
    $vouchers->voucher_type = $post['voucher_type'];
    $vouchers->commision_type = $post['commision_type'];
    $vouchers->margin = $post['margin'];
	
    if($vouchers->create()){
		echo "<script> window.location = 'add-new-brand.php?msg=success' </script>";  	
    } else {
		echo "<script> window.location = 'add-new-brand.php?msg=failed' </script>";
		echo $re;
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
                                        <span>Add New Brand</span>
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
													<i class="fa fa-cogs"></i> Add New Brand </div>
											</div>
											<div class="portlet-body">
											<?php
												if(isset($_GET['msg']) && $_GET['msg']=="success"){
													echo "<div class=\"alert alert-success alert-dismissable\">";
														echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
																	&times;
															  </button>";
														echo "Success! Brand is created.";
													echo "</div>";
												}
												if(isset($_GET['msg']) && $_GET['msg']=="failed"){
														echo "<div class=\"alert alert-danger alert-dismissable\">";
															echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
																		&times;
																  </button>";
															echo "Error! Unable to voucher type.";
														echo "</div>";
												}
											?>
												<div class="well">
													<div class="row show-grid">
															<form method="post" action='add-new-brand.php'>
																<div class="form-group col-md-6">
																	<label>Brand name</label>
																	<div>
																		<input type='text' name='brand_name'  class='form-control' placeholder="Brand name" required>
																	</div>
																</div>
																
																<div class="form-group col-md-6">
																	<label>Voucher type</label>
																	<div>
																		<select class='form-control' name='voucher_type' required>
																			<option selected disabled>Select</option>
																			<?php
																				$resVT = $voucherType->getAll();
																				while ($row = $resVT->fetch(PDO::FETCH_ASSOC)){
																			?>
																					<option value="<?php echo $row['id'] ;?>"><?php echo $row['voucher_type_name'] ;?></option>
																				<?php } ?>
																		</select>
																	</div>
																</div>
																<div class="form-group col-md-6">
																	<label>Commision Type</label>
																	<div>
																		<select class='form-control' name='commision_type' required>					
																			<option value="0">In Percentage</option>
																			<option value="1">In Flat</option>
																		</select>
																	</div>
																</div>
																<div class="form-group col-md-6">
																	<label>Margin (percentage/flat)</label>
																	<div>
																		<input type='text' name='margin'  class='form-control' placeholder="Margin" required>
																	</div>
																</div>
																
																<div class="form-group col-md-6">
																	<div>
																		<input type="submit" id="save" name="submit" value="Submit" class="btn btn-primary">
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
            </div>
        </div>
<?php include_once('inc/footer.php'); ?>
</body>
</html>