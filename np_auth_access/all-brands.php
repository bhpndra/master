<?php $page = "";?>
<?php
include_once('inc/head.php');
include_once('inc/header.php'); 
include_once('classes/vouchers.php');
include_once('classes/vouchers_type.php');

$vouchers = new vouchers();
$voucherType = new vouchers_type();


if(isset($_GET['act']) && $_GET['act']=="del"){
	$helpers = new helper_class();
	$get = $helpers->clearSlashes($_GET);	
	$vouchers->id = $get['id'];
	
	if($vouchers->delete()){
		echo "<script> window.location = 'all-brands.php?msg=success' </script>"; 
    } else {
		echo "<script> window.location = 'all-brands.php?msg=faiied' </script>";
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
                                        <span>All Brands</span>
                                    </li>
                                </ul>
                                <!-- END PAGE BREADCRUMBS -->
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
											<?php
												if(isset($_GET['msg']) && $_GET['msg']=="success"){
													echo "<div class=\"alert alert-success alert-dismissable\">";
														echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
																	&times;
															  </button>";
														echo "Success! Brand is deleted.";
													echo "</div>";
												}
												if(isset($_GET['msg']) && $_GET['msg']=="failed"){
														echo "<div class=\"alert alert-danger alert-dismissable\">";
															echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">
																		&times;
																  </button>";
															echo "Error! Unable to delete.";
														echo "</div>";
												}
											?>
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>Manage Brands</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Brand Name</th>
																	<th>Voucher Type</th>
																	<th>Margin</th>
																	<th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
													<?php
													include_once('classes/pagination.php'); 
													$paginate = new pagin();
													
													$pagiConfig = array();
													$pagiConfig['current_page'] = $_GET['page'];
													$pagiConfig['total_rows'] = $vouchers->getAll()->rowCount();
													$pagiConfig['per_page_items'] = 10;
													//$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?search=h";
													
													
													$pagination = $paginate->pagination($pagiConfig);

														$resVoucher = $vouchers->getAllWithPagin($pagination['offset'],$pagiConfig['per_page_items']); 
														$num = $resVoucher->rowCount();
														
														if($num>=0){
															while ($row = $resVoucher->fetch(PDO::FETCH_ASSOC)){
													?>
														<tr>
															<td><?php echo $row['brand_name']; ?></td>
															<td>
																<?php
																	$voucherType->id = $row['voucher_type'];
																	$voucherType->getName();
																	echo $voucherType->name;
																?>
															</td>
															<td>
																<?php
																	if($row['commision_type']==0){
																		echo $row['margin']."%";
																	} else{
																		echo $row['margin']."rs";
																	}																	
																?>
															</td>
															<td>
																<a href='edit-brand.php?id=<?php echo $row['id']; ?>' class='btn btn-warning left-margin'>
																<span class='glyphicon glyphicon-edit'></span> Edit
																</a>

																<a href='all-brands.php?id=<?php echo $row['id']; ?>&act=del' onclick="return confirm('Are you sure delete this record?');" class='btn btn-danger delete-object'>
																<span class='glyphicon glyphicon-remove'></span> Delete
																</a>
															</td>
														</tr>
													<?php
															}
														}
													?>
                                                            </tbody>
                                                        </table>
                                                    </div>
													<?php echo $pagination['pagination']; ?>
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