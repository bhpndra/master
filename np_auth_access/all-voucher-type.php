<?php
include_once('inc/head.php');
include_once('inc/header.php');
include_once 'classes/vouchers_type.php';

$voucherType = new vouchers_type();
?>
<?php
if(isset($_GET['act']) && $_GET['act']=="del"){
	$helpers = new helper_class();
	$get = $helpers->clearSlashes($_GET);	
	$voucherType->id = $get['id'];
	
	if($voucherType->delete()){
		echo "<script> window.location = 'all-voucher-type.php?msg=success' </script>"; 
    } else {
		echo "<script> window.location = 'all-voucher-type.php?msg=faiied' </script>";
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
                                        <span>All Voucher Type</span>
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
														echo "Success! Voucher type is deleted.";
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
                                                        <i class="fa fa-cogs"></i>Voucher Type</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
																	<th>Voucher Type Name</th>
																	<th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
															<?php
																$resVT = $voucherType->getAll(); 
																$num = $resVT->rowCount();
																if($num>=0){
																	while ($row = $resVT->fetch(PDO::FETCH_ASSOC)){
															?>
																	<tr>
																		<td><?php echo $row['voucher_type_name']; ?></td>
																		<td>
																			<a href='edit-voucher-type.php?id=<?php echo $row['id']; ?>' class='btn btn-warning left-margin'>
																			<span class='glyphicon glyphicon-edit'></span> Edit
																			</a>

																			<a href='?id=<?php echo $row['id']; ?>&act=del' onclick="return confirm('Are you sure delete this record');" class='btn btn-danger delete-object'>
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