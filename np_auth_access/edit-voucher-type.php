<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
include_once 'classes/vouchers_type.php';
$voucherType = new vouchers_type();

$helpers = new helper_class();
$get = $helpers->clearSlashes($_GET);
$id = isset($get['id']) ? $get['id'] : die('ERROR! ID not found!');

$voucherType->id = $id;

if (isset($_POST['submit'])){

	$helpers = new helper_class();
	$post = $helpers->clearSlashes($_POST);
	
    $voucherType->voucher_type_name = $post['voucher_type_name'];


    if($voucherType->update()){
	    $msg = true;
    } else {
        $msg = false;
    }

}
$voucherType->getVoucher();

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
                                        <span>Update Voucher Type</span>
                                    </li>
                                </ul>
                                <!-- END PAGE BREADCRUMBS -->
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12">
											<?php
												if(isset($msg) && $msg==true){
													echo "<div class=\"alert alert-success alert-dismissable\">";
														echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
																	&times;
															  </button>";
														 echo "Success! Voucher Type is update.";
													echo "</div>";
												}
												if(isset($msg) && $msg==false){
														echo "<div class=\"alert alert-danger alert-dismissable\">";
															echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
																		&times;
																  </button>";
															echo "Error! Unable to update voucher type.";
														echo "</div>";
												}
											?>
											<div class="well">
												<div class="row show-grid">
													<div id="datatable_col_reorder_filter" class="dataTables_filter">
														<form action='edit-voucher-type.php?id=<?php echo $id; ?>' role="form" method='post'>
															<div class="form-group col-md-8">
																<label>Voucher Type name</label>
																<div>
																	<input type='text' name='voucher_type_name'  class='form-control' placeholder="Voucher Type name" value="<?php echo $voucherType->voucher_type_name; ?>" required>
																</div>
															</div>
															<div class="form-group col-md-4">
																<label style="opacity: 0;"> submit</label>
																<div>
																	<input type="submit" id="save" name="submit"  class="btn btn-primary">
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
<?php include_once('inc/footer.php'); ?>
</body>
</html>