<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	include_once('classes/user_class.php'); 
	$mysqlObj = new mysql_class();
	$helper   = new helper_class();
	$userClass = new user_class();
	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";
	$date1 	  = new DateTime('1 days ago');
	$dateTo = $date1->format('Y-m-d');	
	
	if(isset($filterBy['dateTo']) && $filterBy['dateTo']!=""){
		$dateTo   = $filterBy['dateTo'];
	} 
	
/* 
	if(isset($_GET['ret_export'])){
		$sql = "SELECT ac.`id` ,ac.`status`,ac.`user`,ac.`cname`,rt.balance FROM `add_cust` ac 
		INNER JOIN `retailer_trans` rt ON rt.retailer_id=ac.id 
		WHERE ac.`id` IN (SELECT `user_id` FROM `add_retailer`) AND rt.id IN (SELECT max(rtr.id) FROM `retailer_trans` rtr WHERE DATE(rtr.date_created)<='$dateTo' group by rtr.retailer_id)";
	
		$sqlQuery = $mysqlObj->mysqlQuery($sql);
		$data = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
		switch("export-to-excel") {
		case "export-to-excel" :
			$filename = "retailer_opening_".time().".xls";		 
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"$filename\"");
			ExportFile($data);
			exit();
		default :
			die("Unknown action ");
			break;
		}
		
	}
	
	if(isset($_GET['dist_export'])){
		$sql = "SELECT ac.`id` ,ac.`status`,ac.`user`,ac.`cname`,dt.balance FROM `add_cust` ac 
		INNER JOIN `distributor_trans` dt ON dt.dist_id=ac.id 
		WHERE ac.`id` IN (SELECT `user_id` FROM `add_distributer`) AND dt.id IN (SELECT max(dst.id) FROM `distributor_trans` dst WHERE DATE(dst.date_created)<='$dateTo' group by dst.dist_id)";
	
		$sqlQuery = $mysqlObj->mysqlQuery($sql);
		$data = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
		switch("export-to-excel") {
		case "export-to-excel" :
			$filename = "distributer_opening_".time().".xls";		 
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"$filename\"");
			ExportFile($data);
			exit();
		default :
			die("Unknown action ");
			break;
		}
		
	}	
	
	if(isset($_GET['wl_export'])){
		$sql = "SELECT ac.`id` ,ac.`status`,ac.`user`,ac.`cname`,wlt.balance FROM `add_cust` ac INNER JOIN `wl_trans` wlt ON wlt.wluser_id=ac.id WHERE ac.`id` IN (SELECT `user_id` FROM `add_white_label`) AND wlt.id IN (SELECT max(wl.id) FROM `wl_trans` wl WHERE DATE(wl.date_created)<='$dateTo' group by wl.wluser_id)";
	
		$sqlQuery = $mysqlObj->mysqlQuery($sql);
		$data = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
		switch("export-to-excel") {
		case "export-to-excel" :
			$filename = "whitelabel_opening_".time().".xls";		 
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"$filename\"");
			ExportFile($data);
			exit();
		default :
			die("Unknown action ");
			break;
		}
	}
function ExportFile($records) {
	$heading = false;
		if(!empty($records))
		  foreach($records as $row) {
			if(!$heading) {
			  // display field/column names as a first row
			  echo implode("\t", array_keys($row)) . "\n";
			  $heading = true;
			}
			echo implode("\t", array_values($row)) . "\n";
		  }
		exit;
}	
 */
	
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
                                        <span>Closing Balance</span>
                                    </li>
                                </ul>
								<div class="alert alert-success">
								  <strong>Note!</strong> Selected date closing balance = Next date opening balance.
								</div>
                                <!-- END PAGE BREADCRUMBS -->
								<div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>Export White Label Closing Balance</div>
                                                </div>
												<div class="portlet-body">
												<div class="row">
													<form method="get" action="export/export-user-closing-balance.php" >
															<div class="form-group col-md-3">
																<label>Date</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" value="<?=$dateTo?>" class="form-control" placeholder="" name="dateTo" id="dateTo">
																</div>
															</div>
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="wl_export" value="Export" class="btn btn-primary">
																</div>
															</div>
														</form>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>	

								<div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>Export Distributer Closing Balance</div>
                                                </div>
												<div class="portlet-body">
												<div class="row">
													<form method="get" action="export/export-user-closing-balance.php" >
															<div class="form-group col-md-3">
																<label>Date</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" value="<?=$dateTo?>" class="form-control" placeholder="" name="dateTo" id="dateTo">
																</div>
															</div>
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="dist_export" value="Export" class="btn btn-primary">
																</div>
															</div>
														</form>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>

								<div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>Export Retailer Closing Balance</div>
                                                </div>
												<div class="portlet-body">
												<div class="row">
													<form method="get" action="export/export-user-closing-balance.php" >
															<div class="form-group col-md-3">
																<label>Date</label>
																<div class="input-icon">
																	<i class="fa fa-calendar font-blue"></i>
																	<input type="date" value="<?=$dateTo?>" class="form-control" placeholder="" name="dateTo" id="dateTo">
																</div>
															</div>
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="ret_export" value="Export" class="btn btn-primary">
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
</html>