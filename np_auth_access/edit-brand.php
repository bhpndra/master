<?php
include_once('inc/head.php');
include_once('inc/header.php'); 
include_once 'classes/vouchers.php';
include_once 'classes/vouchers_type.php';

$vouchers = new vouchers();
$voucherType = new vouchers_type();

$helpers = new helper_class();
$get = $helpers->clearSlashes($_GET);
$id = isset($get['id']) ? $get['id'] : die('ERROR! ID not found!');
$vouchers->id = $id;

if (isset($_POST['submit'])){


	$post = $helpers->clearSlashes($_POST);
	
    $vouchers->brand_name = $post['brand_name'];
    $vouchers->voucher_type = $post['voucher_type'];
    $vouchers->commision_type = $post['commision_type'];
    $vouchers->margin = $post['margin'];


    if($vouchers->updateBrand()){
        $msg = true;
    } else {
        $msg = false;
    }

}


$edit = $vouchers->getBrand()->fetch(PDO::FETCH_ASSOC);

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
                                        <span>Update Brand</span>
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
														echo "Success! Brand is Updated.";
													echo "</div>";
												}
												if(isset($msg) && $msg==false){
														echo "<div class=\"alert alert-danger alert-dismissable\">";
															echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
																		&times;
																  </button>";
															echo "Error! Unable to Updated.";
														echo "</div>";
												}
											?>
											<div class="well">
												<div class="row show-grid">
													<div id="datatable_col_reorder_filter" class="dataTables_filter">
														<form action='edit-brand.php?id=<?php echo $_GET['id']?>' role="form" method='post'>
															<div class="form-group col-md-6">
																<label>Brand name</label>
																<div>
																	<input type='text' name='brand_name'  class='form-control' placeholder="Brand name" value="<?php echo $edit['brand_name']; ?>" required>
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
																				<option <?php if($edit['voucher_type']==$row['id']){ echo "selected"; } ?> value="<?php echo $row['id'] ;?>"><?php echo $row['voucher_type_name'] ;?></option>
																			<?php } ?>
																	</select>
																</div>
															</div>
															<div class="form-group col-md-6">
																<label>Commision Type</label>
																<div>
																	<select class='form-control' name='commision_type' required>					
																		<option <?php if($edit['commision_type']=='0'){ echo "selected"; } ?> value="0">In Percentage</option>
																		<option <?php if($edit['commision_type']=='1'){ echo "selected"; } ?> value="1">In Flat</option>
																	</select>
																</div>
															</div>
															<div class="form-group col-md-6">
																<label>Margin (percentage/flat)</label>
																<div>
																	<input type='text' name='margin'  class='form-control' placeholder="Margin" value="<?php echo $edit['margin']; ?>" required>
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
<?php include_once('inc/footer.php'); ?>
</body>
</html>