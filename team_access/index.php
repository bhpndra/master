<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	
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
                                        <span>Dashboard</span>
                                    </li>
                                </ul>
                                <!-- END PAGE BREADCRUMBS -->
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
<?php //print_r($_SESSION)?>                                   
							<div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="dashboard-stat2 ">
                                        <a href="#">
                                            <div class="display">
                                                <div class="number">

                                                    <small>Retailers</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="icon-pie-chart"></i>
                                                </div>
                                            </div>
                                            <div class="progress-info">
                                                <div class="progress">
                                                    <span style="width: 76%;" class="progress-bar progress-bar-success green-sharp">
                                                    </span>
                                                </div>
                                                <div class="status">
                                                    <div class="status-title"> All </div>
                                                    <div class="status-number"> 
                                                      <?php
                                                        /*
                                                         * Total Number of Retailers
                                                         */
														 
                                                        $retQuery = $mysqlObj->countRows("SELECT `id` FROM `add_cust` WHERE `admin_id` = '".$_SESSION['adminid']."' and usertype = 'RETAILER' and `status`='ENABLED'");
                                                        if ($retQuery > 0) {              
                                                            echo $retQuery;
                                                        } else {
                                                            echo "0";
                                                        }
                                                        ?>
                                                        
                                                    
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="dashboard-stat2 ">
                                        <a href="#">
                                            <div class="display">
                                                <div class="number">
                                                    <small>Distributors</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="icon-pie-chart"></i>
                                                </div>
                                            </div>
                                            <div class="progress-info">
                                                <div class="progress">
                                                    <span style="width: 85%;" class="progress-bar progress-bar-success red-haze">
                                                    </span>
                                                </div>
                                                <div class="status">
                                                    <div class="status-title">All </div>
                                                    <div class="status-number"> 
                                                     <?php
                                                        /*
                                                         * Total Number of Distributors
                                                         */
                                                        $distsQuery = $mysqlObj->countRows("SELECT `id` FROM `add_cust` WHERE `admin_id` = '".$_SESSION['adminid']."' and usertype = 'DISTRIBUTOR' and `status`='ENABLED'");
                                                        if ($distsQuery > 0) {
                                                            echo $distsQuery;
                                                        } else {
                                                            echo "0";
                                                        }
                                                        ?>
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="dashboard-stat2 ">
                                        <a href="#">
                                            <div class="display">
                                                <div class="number">
                                                    <small>White Label</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="icon-pie-chart"></i>
                                                </div>
                                            </div>
                                            <div class="progress-info">
                                                <div class="progress">
                                                    <span style="width: 45%;" class="progress-bar progress-bar-success blue-sharp">
                                                    </span>
                                                </div>
                                                <div class="status">
                                                    <div class="status-title"> All</div>
                                                    <div class="status-number"> 
                                                     <?php
                                                        /*
                                                         * Total Number of Distributors
                                                         */
                                                        $wlQuery = $mysqlObj->countRows("SELECT `id` FROM `add_cust` WHERE `admin_id` = '".$_SESSION['adminid']."' and usertype = 'WL' and `status`='ENABLED'");
                                                        if ($wlQuery > 0) {                  
                                                            echo $wlQuery;
                                                        } else {
                                                            echo "0";
                                                        }
                                                        ?>
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
								
                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="dashboard-stat2 ">
                                        <a href="#">
                                            <div class="display">
                                                <div class="number">
                                                    <small>WL Balance</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="icon-pie-chart"></i>
                                                </div>
                                            </div>
                                            <div class="progress-info">
                                                <div class="progress">
                                                    <span style="width: 45%;" class="progress-bar progress-bar-success green-haze">
                                                    </span>
                                                </div>
                                                <div class="status">
                                                    <div class="status-title"> All</div>
                                                    <div class="status-number"> 
                                                     <?php
                                                        /*
                                                         * Total Number of Distributors
                                                         */
                                                        $wlBQuery = $mysqlObj->mysqlQuery("SELECT sum(wallet_balance) as ta FROM `add_cust` WHERE `admin_id` = '".$_SESSION['adminid']."' and usertype = 'WL' and `status`='ENABLED'")->fetch(PDO::FETCH_ASSOC);
                                                        if ($wlBQuery['ta'] > 0) {                  
                                                            echo $wlBQuery['ta'];
                                                        } else {
                                                            echo "0";
                                                        }
                                                        ?>
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
								
                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="dashboard-stat2 ">
                                        <a href="#">
                                            <div class="display">
                                                <div class="number">
                                                    <small>DT Balance</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="icon-pie-chart"></i>
                                                </div>
                                            </div>
                                            <div class="progress-info">
                                                <div class="progress">
                                                    <span style="width: 45%;" class="progress-bar progress-bar-success red-haze">
                                                    </span>
                                                </div>
                                                <div class="status">
                                                    <div class="status-title"> All</div>
                                                    <div class="status-number"> 
                                                     <?php
                                                        /*
                                                         * Total Number of Distributors
                                                         */
                                                        $dtBQuery = $mysqlObj->mysqlQuery("SELECT sum(wallet_balance) as ta FROM `add_cust` WHERE `admin_id` = '".$_SESSION['adminid']."' and usertype = 'DISTRIBUTOR' and `status`='ENABLED'")->fetch(PDO::FETCH_ASSOC);
                                                        if ($dtBQuery['ta'] > 0) {                  
                                                            echo $dtBQuery['ta'];
                                                        } else {
                                                            echo "0";
                                                        }
                                                        ?>
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
								
								
                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="dashboard-stat2 ">
                                        <a href="#">
                                            <div class="display">
                                                <div class="number">
                                                    <small>RT Balance</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="icon-pie-chart"></i>
                                                </div>
                                            </div>
                                            <div class="progress-info">
                                                <div class="progress">
                                                    <span style="width: 45%;" class="progress-bar progress-bar-success green-haze">
                                                    </span>
                                                </div>
                                                <div class="status">
                                                    <div class="status-title"> All</div>
                                                    <div class="status-number"> 
                                                     <?php
                                                        /*
                                                         * Total Number of Distributors
                                                         */
                                                        $rtBQuery = $mysqlObj->mysqlQuery("SELECT sum(wallet_balance) as ta FROM `add_cust` WHERE `admin_id` = '".$_SESSION['adminid']."' and usertype = 'RETAILER' and `status`='ENABLED'")->fetch(PDO::FETCH_ASSOC);
                                                        if ($rtBQuery['ta'] > 0) {                  
                                                            echo $rtBQuery['ta'];
                                                        } else {
                                                            echo "0";
                                                        }
                                                        ?>
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

								<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="dashboard-stat2 ">
                                        <a href="#">
                                            <div class="display">
                                                <div class="number">
                                                    <small>Tickets</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="icon-pie-chart"></i>
                                                </div>
                                            </div>
                                            <div class="progress-info">
                                                <div class="progress">
                                                    <span style="width: 85%;" class="progress-bar progress-bar-success red-haze">
                                                    </span>
                                                </div>
                                                <div class="status">
                                                    <div class="status-title">Open</div>
                                                    <div class="status-number"> 0(Static) </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                          
								<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="dashboard-stat2 ">
                                        <a href="#">
                                            <div class="display">
                                                <div class="number">
                                                    <small>Fund Request</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="icon-pie-chart"></i>
                                                </div>
                                            </div>
                                            <div class="progress-info">
                                                <div class="progress">
                                                    <span style="width: 85%;" class="progress-bar progress-bar-default blue-haze">
                                                    </span>
                                                </div>
                                                <div class="status">
                                                    <div class="status-title">Pending</div>                                                    
                                                    <div class="status-number">
													<?php
                                                        $FRPQuery = $mysqlObj->countRows("SELECT `id` FROM `add_cust` WHERE `admin_id` = '".$_SESSION['adminid']."' and user_type = 'WL' and `status`='PENDING'");
                                                        if ($FRPQuery > 0) {                  
                                                            echo $FRPQuery;
                                                        } else {
                                                            echo "0";
                                                        }
                                                     ?>
													</div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
								
								<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="dashboard-stat2 ">
                                        <a href="#">
                                            <div class="display">
                                                <div class="number">
                                                    <small>Fund Trans.</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="icon-pie-chart"></i>
                                                </div>
                                            </div>
                                            <div class="progress-info">
                                                <div class="progress">
                                                    <span style="width: 85%;" class="progress-bar progress-bar-default green-haze">
                                                    </span>
                                                </div>
                                                <div class="status">
                                                    <div class="status-title">Amount</div>                                                    
                                                    <div class="status-number">
													<?php
                                                        $FRQuery = $mysqlObj->mysqlQuery("SELECT sum(withdrawl) as ta FROM `admin_trans` WHERE `admin_id` = '".$_SESSION['adminid']."' and tr_type = 'DR' and DATE(`date_created`)=CURDATE()")->fetch(PDO::FETCH_ASSOC);
                                                        if ($FRQuery['ta'] > 0) {                  
                                                            echo $FRQuery['ta'];
                                                        } else {
                                                            echo "0.00";
                                                        }
                                                     ?>
													</div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="dashboard-stat2 ">
                                        <a href="#">
                                            <div class="display">
                                                <div class="number">
                                                    <small>DMT</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="icon-pie-chart"></i>
                                                </div>
                                            </div>
                                            <div class="progress-info">
                                                <div class="progress">
                                                    <span style="width: 45%;" class="progress-bar progress-bar-success blue-sharp">
                                                    </span>
                                                </div>
                                                <div class="status">
                                                    <div class="status-title"> Today</div>
                                                    <div class="status-number"> 
                                                     <?php
                                                        $MTQuery = $mysqlObj->mysqlQuery("SELECT sum(a.amount) as ta FROM `dmt_info` as a left join add_cust as b on b.id = a.user_id WHERE b.admin_id = '".$_SESSION['adminid']."' and b.usertype = 'RETAILER' and DATE(a.date_created)=CURDATE() and a.status = 'SUCCESS'")->fetch(PDO::FETCH_ASSOC);
                                                        if ($MTQuery['ta'] > 0) {                  
                                                            echo $MTQuery['ta'];
                                                        } else {
                                                            echo "0.00";
                                                        }
                                                     ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="dashboard-stat2 ">
                                        <a href="#">
                                            <div class="display">
                                                <div class="number">
                                                    <small>Recharge</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="icon-pie-chart"></i>
                                                </div>
                                            </div>
                                            <div class="progress-info">
                                                <div class="progress">
                                                    <span style="width: 45%;" class="progress-bar progress-bar-success blue-sharp">
                                                    </span>
                                                </div>
                                                <div class="status">
                                                    <div class="status-title"> Today</div>
                                                    <div class="status-number"> 
                                                     <?php
                                                        $RCQuery = $mysqlObj->mysqlQuery("SELECT sum(a.amount) as ta FROM `recharge_info` as a left join add_cust as b on b.id = a.user_id WHERE b.admin_id = '".$_SESSION['adminid']."' and b.usertype = 'RETAILER' and DATE(a.date_created)=CURDATE() and a.status = 'SUCCESS'")->fetch(PDO::FETCH_ASSOC);
                                                        if ($RCQuery['ta'] > 0) {                  
                                                            echo $RCQuery['ta'];
                                                        } else {
                                                            echo "0.00";
                                                        }
                                                     ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="dashboard-stat2 ">
                                        <a href="#">
                                            <div class="display">
                                                <div class="number">
                                                    <small>AEPS</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="icon-pie-chart"></i>
                                                </div>
                                            </div>
                                            <div class="progress-info">
                                                <div class="progress">
                                                    <span style="width: 45%;" class="progress-bar progress-bar-success blue-sharp">
                                                    </span>
                                                </div>
                                                <div class="status">
                                                    <div class="status-title"> Today</div>
                                                    <div class="status-number"> 
                                                     <?php
                                                        $RCQuery = $mysqlObj->mysqlQuery("SELECT sum(a.amount) as ta FROM `recharge_info` as a left join add_cust as b on b.id = a.user_id WHERE b.admin_id = '".$_SESSION['adminid']."' and b.usertype = 'RETAILER' and DATE(a.date_created)=CURDATE() and a.status = 'SUCCESS'")->fetch(PDO::FETCH_ASSOC);
                                                        if ($RCQuery['ta'] > 0) {                  
                                                            echo $RCQuery['ta'];
                                                        } else {
                                                            echo "0.00";
                                                        }
                                                     ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
						   </div>
	
                                    <div class="row">
                                        <div class="col-lg-6 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption"> DMT Balance</div>
                                                </div>
                                                <div class="portlet-body">
												
													<div class="table-responsive">
														Total Amount Transferred : <span id="GetTotalDMTAmtTransferred"></span><br>
														Total DMT Amount Deducted : <span id="GetTotalDmtAmtDeducted"></span>
													</div>

												</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
														Recharge Balance</div>
                                                </div>
                                                <div class="portlet-body">
												
													<div class="table-responsive">
														Total Amount Transferred : 0<span id="GetTotalIDMTAmtTransferred"></span><br>
														Total DMT Amount Deducted : 0<span id="GetTotalIDmtAmtDeducted"></span>
													</div>

												</div>
                                            </div>
                                        </div>
										<div class="col-lg-6 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        Closing Balance</div>
                                                </div>
                                                <div class="portlet-body">
												
													<div class="table-responsive" id="GetClosingBalance">
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
<script>
    jQuery(document).ready(function (){
		$.ajax({
			type: 'POST',
			data: {action: 'GetTotalDMTAmtTransferred'},
			cache: false,
			url: 'ajax/dashboard_ajax_load.php',
			success: function (response)
			{ 
				$("#GetTotalDMTAmtTransferred").text(response);
			}
		});
		$.ajax({
			type: 'POST',
			data: {action: 'GetTotalDmtAmtDeducted'},
			cache: false,
			url: 'ajax/dashboard_ajax_load.php',
			success: function (response)
			{ 
				$("#GetTotalDmtAmtDeducted").text(response);
			}
		});
		get_cloaing_balance();	
	});
	function get_cloaing_balance(){
		$.ajax({
			type: 'POST',
			data: {action: 'GetClosingBalance'},
			cache: false,
			url: 'ajax/balance_ajax_load.php',
			dataType: 'html',
			success: function (response)
			{ 
				$("#GetClosingBalance").html(response);
			}
		});
	}
</script>
</body>
</html>