<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php

if($_SESSION[_session_usertype_]!="ADMIN" && $_SESSION[_session_userid_]!=1){
	echo "<script> window.location = 'index.php'; </script>"; die();
}
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	if(isset($filterBy['fByUsername'])&& $filterBy['fByUsername']!=""){
		$filter .= " and name like '%".$filterBy['fByUsername']."%' ";
	}
	if(isset($filterBy['fByUserid'])&& $filterBy['fByUserid']!=""){
		$filter .= " and user like '%".$filterBy['fByUserid']."%' ";
	}
	if(isset($filterBy['fByMobile'])&& $filterBy['fByMobile']!=""){
		$filter .= " and mobileno like '%".$filterBy['fByMobile']."%' ";
	}
	if(isset($filterBy['fBycname'])&& $filterBy['fBycname']!=""){
		$filter .= " and cname like '%".$filterBy['fBycname']."%' ";
	}
	if(isset($filterBy['fByUsertype'])&& $filterBy['fByUsertype']!=""){
		$filter .= " and userType ='".$filterBy['fByUsertype']."' ";
	}

	if(isset($filterBy['dateFrom']) && isset($filterBy['dateTo']) && $filterBy['dateFrom']!="" && $filterBy['dateTo']!=""){
		$filter .= " and STR_TO_DATE(created_on, '%d/%b/%Y') BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
		$dateFrom = $filterBy['dateFrom'];
		$dateTo = $filterBy['dateTo'];
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
                                        <span>All Users</span>
                                    </li>
                                </ul>
                                <!-- END PAGE BREADCRUMBS -->
								<div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>Advance Filter</div>
                                                </div>
												<div class="portlet-body">
												<div class="row">
													<form method="get" >
															<div class="form-group col-md-3">
																<label>Name</label>
																<div>
																	<input type='text' id="fByUsername" name='fByUsername' value="<?php echo @$filterBy['fByUsername']; ?>"  class='form-control' placeholder="Name" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Username</label>
																<div>
																	<input type='text' id="fByUserid" name='fByUserid' value="<?php echo @$filterBy['fByUserid']; ?>"  class='form-control' placeholder="Username" >
																</div>
															</div>
															
															<div class="form-group col-md-3">
																<label>Mobile Number</label>
																<div>
																	<input type='text' name='fByMobile' value="<?php echo @$filterBy['fByMobile']; ?>"  class='form-control' placeholder="Mobile Number" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Company Name</label>
																<div>
																	<input type='text' id="fBycname" name='fBycname' value="<?php echo @$filterBy['fBycname']; ?>"  class='form-control' placeholder="Company Name" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>User Type</label>
																<div>
																<?php
																	if(@$filterBy['fByUsertype']!=""){
																		$selected = str_replace(" ","",$filterBy['fByUsertype']);
																		$$selected = "selected";
																	}
																?>
																	<select class='form-control' name='fByUsertype' >					
																		<option value="">All</option>
																		<option <?=@$ADMIN?> value="ADMIN">ADMIN</option>
																		<option <?=@$B2B?> value="B2B">B2B Software</option>
																		<option <?=@$RESELLER?> value="RESELLER">Reseller</option>
																	</select>
																</div>
															</div>
	
															
															<div class="form-group col-md-2">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="software-list.php" class="btn btn-default">Reset</a>
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
                                <div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>All Users</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" width="30px;">#</th>
                                                                    <th scope="col" width="150px;">User Details</th>
                                                                    <th scope="col" width="150px;">Role</th>
                                                                    <th scope="col" width="150px;">Wallet Bal.</th>
                                                                    <th scope="col" width="150px;">City</th>
                                                                    <th scope="col" width="150px;">Address</th>
                                                                    <th scope="col" width="150px;">Action</th>
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
$sql = "SELECT * FROM `admin` where 1 ";
$orderBy = "  ORDER BY `id` DESC";		
		
	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
	
 $i = $pagination['offset'] + 1;
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
			?>
									<tr>
										<td width="30px;"> <?= $i; ?> </td>
										<td width="250px;">
											<strong>Name:</strong> <?= $rows['name']; ?> - (#<?= $rows['id']; ?>)<br/>
											<strong>Username:</strong> <?= $rows['suser_id']; ?><br/>
											<strong>Mobile:</strong> <?= $rows['mobileno']; ?><br/>
											<strong>Email:</strong> <?= $rows['email']; ?><br/>
											<strong>Company:</strong> <?= $rows['cname']; ?><br/>											
										</td>
										<td >
											<?= $rows['userType']; ?><br/>
											<!--<a href="javascript:void(0)" onclick="get_user_hierarchy('<?=$rows['id']?>')" >User Hierarchy</a>-->
										</td>
										<td > <?= $rows['balance']; ?></td>
										<td > <?= $rows['city']; ?></td>
										<td > <?= $rows['address']; ?></td>
										<td > <a href="update-software-user.php?id=<?=$rows['id']; ?>" class="btn btn-sm btn-primary" >Edit</a></td>
										
										
										
										<?php $i++; } ?>
									</tr>
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
<div class="modal fade" id="modaluserdetails" tabindex="-1" role="dialog" aria-labelledby="modaluserdetails"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title w-100 font-weight-bold">User Details</h4>
      </div>
      <div class="modal-body mx-3" id="userdetailbox">
	  
	  </div>
    </div>
  </div>
</div>		
<?php include_once('inc/footer.php'); ?>

<!--code end 22 february 2020-->
</body>
</html>
</html>