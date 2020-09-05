<?php
include_once('inc/head.php');
include_once('inc/header.php');

?>
<?php
	$helpers = new helper_class();
	$post = $helpers->clearSlashes($_POST);
	$mysqlClass = new Mysql_class();
	$filter = '';

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
                                        <span>All WL Package Commission</span>
                                    </li>
                                </ul>
                                <!-- END PAGE BREADCRUMBS -->
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
											
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>Manage WL Package Commission</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Package Name</th>
																	<th>Created By </th>
																	<th>Created Date </th>
																	<th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
<?php
// for Pagination
include_once('classes/pagination.php'); 
$paginate = new pagin();

$pagiConfig = array();
$pagiConfig['current_page'] = isset($_GET['page']) ? $_GET['page'] : 1;
$pagiConfig['per_page_items'] = 50;
unset($_GET['page']);
$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?".http_build_query($_GET);

$sql = "select * from package_list where creator_id = '".$_SESSION[_session_userid_]."' and created_by = '".$_SESSION[_session_usertype_]."' and created_for = 'WL' ";
$orderBy = "  ORDER BY `id` DESC";
														
$pagiConfig['total_rows'] = $mysqlClass->countRows($sql.$filter);

$pagination = $paginate->pagination($pagiConfig);
$sqlQuery = $mysqlClass->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);

$i = $pagination['offset'] + 1;
												while ($row = $sqlQuery->fetch(PDO::FETCH_ASSOC)){
													?>
														<tr>
															<td><?php echo $row['package_name']; ?></td>
															<td><?php echo $row['created_by']; ?></td>
															<td><?php echo $row['created_on']; ?></td>
															<td>
																<a href='update-wl-user-package.php?id=<?=base64_encode($row['id'])?>' class='btn btn-warning left-margin'>
																<span class='glyphicon glyphicon-edit'></span> Edit
																</a>

																<!--<a href='all-brands.php?id=<?php echo $row['id']; ?>&act=del' onclick="return confirm('Are you sure delete this record?');" class='btn btn-danger delete-object'>
																<span class='glyphicon glyphicon-remove'></span> Delete
																</a>-->
															</td>
														</tr>
													<?php

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