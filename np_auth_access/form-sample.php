<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
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
                                        <span>Dashboard</span>
                                    </li>
                                </ul>
                                <!-- END PAGE BREADCRUMBS -->
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12">
											
											<div class="well">
												<div class="row show-grid">
													<div id="datatable_col_reorder_filter" class="dataTables_filter">
														<form method="post">
															<div class="form-group col-md-6">
																<label>Name</label>
																<div>
																	<input type="text" id="name" name="name" " data-placeholder="Type to filter history" class="form-control">
																</div>
															</div>
															
															<div class="form-group col-md-6">
																<label>Email</label>
																<div>
																	<input type="text" id="email" name="email" value="rohit.riseintech@gmail.com" readonly="" data-placeholder="Type to filter history" class="form-control">
																</div>
															</div>
															<div class="form-group col-md-6">
																<label>Username</label>
																<div>
																	<input type="text" id="username" name="username" value="san" readonly="" data-placeholder="Type to filter history" class="form-control">
																</div>
															</div>
															<div class="form-group col-md-6">
																<label>Address</label>
																<div>
																	<input type="text" id="address" name="address" value="New Delhi" data-placeholder="Type to filter history" class="form-control">
																</div>
															</div>
															
															<div class="form-group col-md-6">
																<label>Company Name</label>
																<div>
																	<input type="text" id="company" name="company" value="Net Paisa API Provider" data-placeholder="Type to filter history" class="form-control">
																</div>
															</div>
															<div class="form-group col-md-6">
																<label>City</label>
																<div>
																	<input type="text" id="city" name="city" value="NEW DELHI" data-placeholder="Type to filter history" class="form-control">
																</div>
															</div>
															<div class="form-group col-md-6">
																<label>Mobile Number</label>
																<div>
																	<input type="text" id="mobile" name="mobile" value="7836951028" readonly="" data-placeholder="Type to filter history" class="form-control">
																</div>
															</div>
															
															<div class="form-group col-md-6">
																<label>Pin</label>
																<div>
																	<input type="text" id="pin" name="pin" value="110092" data-placeholder="Type to filter history" class="form-control">
																</div>
															</div>
															<div class="form-group col-md-6">
																<div>
																	<input type="submit" id="save" name="save" data-placeholder="Type to filter history" class="btn btn-primary">
																</div>
															</div>
															
															
															
															
<div class="form-body">

<div class="form-group col-md-3">
<label>From</label>
<div class="input-icon">
<i class="fa fa-calendar font-blue"></i>
<input type="date" value="2019-03-29" class="form-control" placeholder="Left icon" name="date_from" id="date_from"> </div>
</div>
<div class="form-group col-md-3">
<label>To</label>
<div class="input-icon">
<i class="fa fa-calendar font-blue"></i>
<input type="date" value="2019-04-05" class="form-control" placeholder="Left icon" name="date_to" id="date_to"> </div>
</div>







<div class="form-actions col-md-3">
<button type="submit" class="btn blue" name="search" style="width: 100%;">Filter</button>
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