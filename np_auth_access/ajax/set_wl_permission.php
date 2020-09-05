<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include("../classes/user_class.php");
	
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	$userClass = new user_class();
	
	$post = $helpers->clearSlashes($_REQUEST);	

	if($post['uid']!="")
	{	
		$userType = $userClass->check_user_type($post['uid']); 
		$row = $mysqlClass->get_field_data("*","add_cust"," where id = '".$post['uid']."' ");
		$permssionchk = $mysqlClass->get_field_data("*","wl_setting_permission"," where wl_id = '".$post['uid']."' ");
?>

	<table class='table table-bordered'>
		<tbody>
			<tr> 
				<th style="width:150px">WL Admin Name:</th> <td style="width:200px"><?=$row['name'].' (#'.$row['id'].')'?></td> 
			</tr>
			<tr> 
				<th>SMS Package:</th> <td><input type="checkbox" name="sms_pack_chk" id="sms_pack_chk" <? if($permssionchk['sms_package'] == 1){echo "checked";}?> /></td> 
			</tr>
			<tr> 
				<th>Bulk-SMS:</th> <td><input type="checkbox" name="bulk_sms_chk" id="bulk_sms_chk" <? if($permssionchk['bulk_sms'] == 1){echo "checked";}?>  /></td> 
			</tr>
			<tr> 
				<th>Bulk-Email:</th> <td><input type="checkbox" name="bulk_email_chk" id="bulk_email_chk" <? if($permssionchk['bulk_email'] == 1){echo "checked";}?> /></td> 
			</tr>
			<tr> 
				<th>Payment Gateway:</th> <td><input type="checkbox" name="payment_gateway_chk" id="payment_gateway_chk" <? if($permssionchk['payment_gateway'] == 1){echo "checked";}?> /></td> 
			</tr>	
			<tr> 
				<td colspan="2"><input type="button" name="permsBtn" value="Save Permission" class="btn btn-info" onclick='save_permission("<?=$row['id'];?>")' /></td> 
			</tr>
		</tbody>
	</table>

<?php	
	} 
?>