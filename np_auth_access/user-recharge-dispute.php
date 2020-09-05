<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	include_once('classes/user_class.php'); 
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	$userClass = new user_class();
	$request = $helper->clearSlashes($_GET);
	$post = $helper->clearSlashes($_POST);
	
	if(isset($post['message']) && !empty($post['message'])){
		$data = array(
			'employee_id' => $_SESSION[_session_userid_],
			'employee_name' => 'ADMIN',
			'dispute_id' => $post['dispute_id'],
			'comment' => $post['message']
		);
		if($mysqlObj->insertData("disputes_comment", $data)>0){
			echo "<script> window.location = 'user-recharge-dispute.php?tid=".$request['tid']."&uid=".$request['uid']."'; </script>";
		}		
	}
	if(isset($request['action']) && $request['action']=="close"){
		$disId = $mysqlObj->get_field_data("id","disputes"," WHERE transaction_id='".$request["tid"]."' AND user_id='".$request['uid']."' ORDER BY dispute_created  DESC limit 1");
		
		$data = array(
			'employee_id' => $_SESSION[_session_userid_],
			'employee_name' => 'ADMIN',
			'status' => 'Close'
		);
		$mysqlObj->updateData("disputes", $data, " where id = '".$disId['id']."'");
		echo "<script> window.location = 'user-recharge-dispute.php?tid=".$request['tid']."&uid=".$request['uid']."'; </script>";
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
                                        <span>User's Dispute</span>
                                    </li>
                                </ul>
                                <!-- END PAGE BREADCRUMBS -->
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">

<?php
$disStatus = $mysqlObj->get_field_data("*","disputes"," WHERE transaction_id='".$request["tid"]."' AND user_id='".$request['uid']."' AND dispute_type = 'Recharge' ORDER BY dispute_created  DESC limit 1");

$userD = $mysqlObj->get_field_data("*","add_cust"," WHERE  id='".$request['uid']."'");
$trans = $mysqlObj->get_field_data("*","recharge_info"," WHERE transaction_id='".$request["tid"]."' ");

$td = $mysqlObj->get_field_data("*","retailer_trans"," where transaction_id = '".$trans["transaction_id"]."'");

if(!empty($disStatus['id'])){
?>
		<div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading" id="accordion">
                    <span class="glyphicon glyphicon-user"></span> User Details
                   
                </div>
            <div class="panel-collapse ">
                <div class="panel-body">
                    <table class="table">
						<tr>
							<th>Name :</th>
							<td><?php echo $userD['name']; ?></td>
						</tr>
						<tr>
							<th>User Id</th>
							<td><?php echo $userD['user']; ?></td>
						</tr>
						<tr>
							<th>Company Name :</th>
							<td><?php echo $userD['cname']; ?></td>
						</tr>
						<tr>
							<th>Mobile :</th>
							<td><?php echo $userD['mobile']; ?></td>
						</tr>
						<tr>
							<th colspan="2" style="text-align:center; background:#ccc">Transaction Details</th>
						</tr>
						<?php if(!empty($trans['transaction_id'])){ ?>
						<tr>
							<th>Transaction Id :</th>
							<td><?php echo $trans['transaction_id']; ?></td>
						</tr>
						<tr>
							<th>Status</th>
							<td><?php echo $trans['status']; ?></td>
						</tr>
						<tr>
							<th>Opening Bal :</th>
							<td>
							<?php $opbl = $userClass->check_transaction_openingBalance($td['retailer_id'],$td['id'],"retailer");
											echo $opbl['balance']; ?>
							</td>
						</tr>
						<tr>
							<th>Amount :</th>
							<td><?php echo $td['withdrawl']; ?></td>
						</tr>
						<tr>
							<th>Closing Bal :</th>
							<td><?php echo $td['balance']; ?></td>
						</tr>
						<tr>
							<th>Recharge Time :</th>
							<td><?php echo $trans['time']; ?></td>
						</tr>
						<?php } else { ?>
						<tr>
							<td colspan="2" align="center" >Details Not Found in Rechage History</td>
						</tr>
						<?php } ?>
						<tr>
							<th colspan="2" style="text-align:center; background:#ccc">Dispute Details</th>
						</tr>
						<tr>
							<th>Dispute Transaction Id :</th>
							<td><?php echo $disStatus['transaction_id']; ?></td>
						</tr>
						<tr>
							<th>Status</th>
							<td><?php echo $disStatus['status']; ?></td>
						</tr>
						<tr>
							<th>Time :</th>
							<td><?php echo $disStatus['dispute_created']; ?></td>
						</tr>
						<tr>
							<th>Comment :</th>
							<td><?php echo $disStatus['message']; ?></td>
						</tr>
					</table>
                </div>
                <div class="panel-footer"> 
					<?php 
						if($disStatus['status']=="Open"){
					?>
                      <a href="user-recharge-dispute.php?tid=<?php echo $request['tid'];?>&uid=<?php echo $request['uid'];?>&action=close" class="btn btn-primary btn-sm" id="btn-chat">Click to Close Dispute</a>
                      <span class="pull-right btn btn-danger btn-sm" id="btn-chat">Open</span>
					<?php } ?>
                </div>
            </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading" id="accordion">
                    <span class="glyphicon glyphicon-comment"></span> Disputes
                   
                </div>
            <div class="panel-collapse ">
                <div class="panel-body">
                    <ul class="chat">
						<?php
						
$dispu = $mysqlObj->fetchAllData("disputes_comment","*"," WHERE dispute_id='".$disStatus["id"]."' ");
						if(count($dispu)>0){ 
							foreach($dispu as $dis){ ?>
						<?php
							if($dis['employee_id']==0){
								$class="left";
								$userName = $userD['name'];
								
							} else {
								$class="right";
								$userName = 'Support User';
							}
							if($dispu['status']=="Close"){
								$dispute_status = 'Close';
							}
						?>
							<li class="<?php echo $class; ?> clearfix">
								<div class="chat-body clearfix">
									<div class="header">
										<strong class="primary-font"><?php echo $userName; ?></strong><br/>
										<small class="text-muted">
											<span class="glyphicon glyphicon-time"></span><?php echo $dis['created_at']; ?></small>
									</div>
									<p><?php echo $dis['comment']; ?></p>
								</div>
							</li>
						<?php
								} 
							}
						?>
                        <!--<li class="right clearfix">
                            <div class="chat-body clearfix">
                                <div class="header">
									<strong class="primary-font">Bhaumik Patel</strong><br/>
                                    <small class="text-muted"><span class="glyphicon glyphicon-time"></span>13 mins ago</small>
                                    
                                </div>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
                                    dolor, quis ullamcorper ligula sodales.
                                </p>
                            </div>
                        </li>-->
                    </ul>
                </div>
                <div class="panel-footer">
					<form method="post">
                    <div class="input-group">
                        <input id="btn-input" type="text" name="message" class="form-control input-sm" placeholder="Type your message here..." />
                        <input id="btn-input" type="hidden" name="dispute_id" value="<?php echo $disStatus["id"]; ?>" />
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-warning btn-sm" id="btn-chat">
                                Send</button>
                        </span>
                    </div>
					</form>
                </div>
            </div>
            </div>
        </div>
 <?php } else { ?>		
	Wrong Details
 <?php } ?>
   
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