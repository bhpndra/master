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
			'employee_name' => $_SESSION[_session_username_]."_ADMIN",
			'ticket_id' => $post['tid'],
			'comment' => $post['message']
		);
		if($mysqlObj->insertData("ticket_comments", $data)>0){
			echo "<script> window.location = 'tickets-chat.php?tid=".$request['tid']."'; </script>";
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
                                        <span>User's Tickets</span>
                                    </li>
                                </ul>
                                <!-- END PAGE BREADCRUMBS -->
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
<?php
	$sql = "SELECT a.user_id,
		a.id,
		a.subject,
		a.message,
		a.is_active,
		a.created_at,
		a.user_id,
		b.name,
		b.cname,
		b.mobile
FROM `tickets` as a, add_cust as b where a.user_id = b.id and a.id = '".$request['tid']."'";	
$sqlQuery = $mysqlObj->mysqlQuery($sql);
$rows = $sqlQuery->fetch(PDO::FETCH_ASSOC);

?>
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading" id="accordion">
                    <span class="glyphicon glyphicon-comment"></span> Ticket Details
                   
                </div>
            <div class="panel-collapse ">
                <div class="panel-body">
					<table class="table table-bordered">
						<tbody>
							<tr>
								<th style="width: 90px;">User Details</th>
								<td>
									<strong>Name : </strong><?php echo $rows["name"]; ?><br/>
									<strong>Mobile : </strong><?php echo $rows["mobile"]; ?><br/>
									<strong>Company : </strong><?php echo $rows["cname"]; ?>
								</td>
							</tr>
							<tr>								
								<th style="width: 90px;">Subject</th>
								<td>
									<?php if($rows["subject"]=='1')
										{
											echo "Billing Enquiry";
										}
										elseif($rows["subject"]=='2')
										{
											echo "Sales Enquiry";
										}
										else
										{
											echo "Technical Support";
										}
									?>
								</td>
							</tr>
							<tr>
								<th style="width: 90px;">Message</th>
								<td>
									<?php echo $rows["message"]; ?>
								</td>
							</tr>
							<tr>
								<th style="width: 90px;">Date</th>
								<td><?php echo $rows['created_at']; ?></td>
							</tr>
							<tr>
								<th style="width: 90px;">Status</th>
								<td style="width: 115px;">
									<span id="tdStatus"><?php echo $rows['is_active']; ?></span><br/>
									<span style="color:blue">(<?=$helper->create_durations($rows['created_at'])." ago."?>)</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
            </div>
            </div>
        </div>
<?php
	$sql1 = "SELECT * FROM `ticket_comments` where ticket_id = '".$rows['id']."'";	
$sqlQuery1 = $mysqlObj->mysqlQuery($sql1);
$rowReply = $sqlQuery1->fetchAll(PDO::FETCH_ASSOC);

?>
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading" id="accordion">
                    <span class="glyphicon glyphicon-comment"></span>Support Reply
                   
                </div>
            <div class="panel-collapse ">
                <div class="panel-body">
                    <ul class="chat">
						<?php
						if(count($rowReply)>0){ 
							foreach($rowReply as $rp){ ?>
						<?php
							if($rp['user_id']==$rows["user_id"]){
								$class="left";
								$userName = $rows['name'];
								
							} else {
								$class="right";
								$userName = 'Apna Chirag User';
							}
						?>
							<li class="<?php echo $class; ?> clearfix">
								<div class="chat-body clearfix">
									<div class="header">
										<strong class="primary-font"><?php echo $userName; ?></strong><br/>
										<small class="text-muted">
											<span class="glyphicon glyphicon-time"></span><?php echo $helper->create_durations($rp['created_at'])." ago."; ?></small>
									</div>
									<p><?php echo $rp['comment']; ?></p>
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
					<input type="hidden" name="tid" value="<?=$request['tid']?>" />
                        <input id="btn-input" type="text" name="message" class="form-control input-sm" placeholder="Type your message here..." />
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