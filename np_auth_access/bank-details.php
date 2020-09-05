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
			echo "<script> window.location = 'user-dmt-dispute.php?tid=".$request['tid']."&uid=".$request['uid']."'; </script>";
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
		echo "<script> window.location = 'user-dmt-dispute.php?tid=".$request['tid']."&uid=".$request['uid']."'; </script>";
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
$disStatus = $mysqlObj->get_field_data("*","disputes"," WHERE transaction_id='".$request["tid"]."' AND user_id='".$request['uid']."' AND dispute_type = 'Money Transfer' ORDER BY dispute_created  DESC limit 1");

$userD = $mysqlObj->get_field_data("*","add_cust"," WHERE  id='".$request['uid']."'");
$dmtInfoD = $mysqlObj->get_field_data("*","dmt_info"," WHERE  topup_trid='".$disStatus['transaction_id']."'");

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
						<tr><th colspan="2" style="text-align:center; background:#ccc">Transaction Details</th></tr>
						<tr><td>
							<strong>Tr. ID: </strong><?php echo $dmtInfoD["topup_trid"]; ?><br/>
							<strong>Amount: </strong><?php echo $dmtInfoD["amount"]; ?><br/>
							<strong>Date: </strong><?php echo $dmtInfoD["date"]; ?><br/>
							<strong>Status: </strong><?php echo $dmtInfoD['status']; ?>
						</td></tr>
						<tr><th colspan="2" style="text-align:center; background:#ccc">Balance Details</th></tr>
						<td id="balDetails<?php echo $dmtInfoD["dmt_id"]; ?>">
							<a href="javascript:void(0)" onclick="getBalanceDetails('<?php echo $dmtInfoD["topup_trid"]; ?>','<?php echo $dmtInfoD["user_type"]; ?>','<?php echo $dmtInfoD["agent_id"]; ?>','<?php echo $dmtInfoD["dmt_id"]; ?>')">Get Balance Details</a>
						</td></tr>
						<tr><th colspan="2" style="text-align:center; background:#ccc">Agent Details</th></tr>
						<tr><td>
							<strong>Name: </strong><?php echo $userD["name"]; ?><br/>
							<strong>Shop: </strong><?php echo $userD["cname"]; ?><br/>
							<strong>Mobile: </strong><?php echo $userD["mobile"]; ?>					
						</td></tr>
						<tr><th colspan="2" style="text-align:center; background:#ccc">Bene. Details</th></tr>
						<tr><td>
							<strong>Code: </strong><?php echo $dmtInfoD["bene_code"]; ?><br/><strong>Name : </strong><?php echo $dmtInfoD["bene_name"]; ?><br/>
							<strong>A/C : </strong><?php echo $dmtInfoD["bene_ac"]; ?><br/>
							<strong>IFSC : </strong><?php echo $dmtInfoD["ifsc_code"]; ?>					
						</td></tr>
					</table>
                </div>
                <div class="panel-footer"> 
					<?php 
						if($disStatus['status']=="Open"){
					?>
                      <a href="user-dmt-dispute.php?tid=<?php echo $request['tid'];?>&uid=<?php echo $request['uid'];?>&action=close" class="btn btn-primary btn-sm" id="btn-chat">Click to Close Dispute</a>
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
								$userName = 'Netpaisa Team';
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
<script>
	
		function getBalanceDetails(tranId,userTyep,userID,rowId){
			$.ajax({
				type: 'POST',
				data: {tranId:tranId, userTyep:userTyep, userID:userID, rowId:rowId  },
				cache: false,
				url: 'ajax/get_dmt_balance_details.php',
				success: function (response)
				{ 
					$("#balDetails"+rowId).html(response);
				}
			});
		}
</script>
</body>
</html>