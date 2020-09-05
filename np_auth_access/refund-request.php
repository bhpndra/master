<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	
	$database1 = new Database();
	$db_1 = $database1->getConnection();
	
	$database2 = new Database();
	$db_2 = $database2->getConnection_2();
	
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
                                        <span>Refund Request</span>
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
                                                        <i class="fa fa-cogs"></i>Refund Request</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" width="150px;"> Sr. </th>
																	<th scope="col" width="150px;"> Topup Trid</th>
																	<th scope="col" width="150px;"> Mobile </th>
																	<th scope="col" width="150px;"> Amount </th>
																	<th scope="col" width="150px;"> Withdrawl Amount </th>
																	<th scope="col" width="150px;"> Benename</th>
																	<th scope="col" width="150px;"> BeneAC</th>
																	<th scope="col" width="150px;"> IFSC Code</th>
																	<th scope="col" width="150px;"> Usertype</th>
																	<th scope="col" width="150px;"> AgentId</th>
																	<th scope="col" width="150px;"> Date</th>
																	<th scope="col" width="150px;"> RefundStatus</th>
																	<th scope="col" width="150px;"> Status</th>
																	<th scope="col" width="150px;"> AC Status</th>
																	<th scope="col" width="150px;" style="width:20%;"> Action </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
			<?php
				
				$sqlQuery = $mysqlObj->mysqlQuery("SELECT * FROM `dmt_info` WHERE `topup_trid`!='' && (`refund_status`='Requested' || `refund_status`='Refunded') ORDER BY `dmt_id` DESC");
				while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){
			?>
									<tr>
										<td width="150px;"> <?= $rows['dmt_id']; ?> </td>
										<td width="150px;" class="topup_trid"><?= $rows['topup_trid']; ?> </td>
										<td width="150px;" class="mobile"> <?= $rows['mobile']; ?>     </td>
										<td width="150px;" class="amount"> <?= $rows['amount']; ?>     </td>
										<td width="150px;" class="wtamount">
											<?php
											$mysqlObj->db_conn  = $db_1;
											$withdrawl = $mysqlObj->get_field_data("`withdrawl`", "retailer_trans","WHERE `transaction_id`='".$rows['topup_trid']."' && `retailer_id`='".$rows['agent_id']."'");
											if(!empty($withdrawl['withdrawl'])){
												echo $withdrawl['withdrawl'];											
											} else {
												$withdrawl = $mysqlObj->get_field_data("`withdrawl`", "distributor_trans","WHERE `transaction_id`='".$rows['topup_trid']."' && `dist_id`='".$rows['agent_id']."'");
												echo $withdrawl['withdrawl'];
											}
											?>
										</td>
										<td width="150px;" class="bene_name"> <?= $rows['bene_name']; ?>  </td>
										<td width="150px;" class="bene_ac"> <?= $rows['bene_ac']; ?>    </td>
										<td width="150px;" class="ifsc_code"> <?= $rows['ifsc_code']; ?>  </td>
										<td width="150px;" class="user_type"> <?= $rows['user_type']; ?>  </td>
										<td width="150px;" class="agent_id"> <?= $rows['agent_id']; ?>   </td>
										<td width="150px;" class="date"> <?= $rows['date']; ?>       </td>
										<td width="150px;" class="refund_status"> <?= $rows['refund_status']; ?> </td>
										<td width="150px;" class="status"> <?= $rows['status']; ?>     </td>
										<td width="150px;" class="ac_status"> <?= $rows['ac_status']; ?>  </td>
										<?php if ($rows['refund_status'] == "Requested") { ?>
										<td> <a href="#" class="btn btn-primary requested" id="<?php echo $rows['dmt_id']; ?>" data-toggle="modal" data-target="#myModal">Process</a></td>
										<?php } ?>
									</tr>
			<?php
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
</html>