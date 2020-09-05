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
//print_r($row);
?>

	<table class='table table-bordered'>
		<tbody>
		<tr> 
			<th style="width:150px">Row Id:</th> <td style="width:200px"><?=$row['id']?></td> 
		</tr>
		<tr> 
			<th>Name:</th> <td><?=$row['name']?></td> 
		</tr>
		<tr> 
			<th>User Id:</th> <td><?=$row['user']?></td> 
		</tr>
		<tr> 
			<th>Mobile:</th> <td><?=$row['mobile']?></td> 
		</tr>
		<tr> 
			<th>Email:</th> <td><?=$row['email']?></td> 
		</tr>
		<tr> 
			<th>Company:</th> <td><?=$row['cname']?></td> 
		</tr>
		<tr> 
			<th>Location:</th> <td><?=$row['city']?></td> 
		</tr>
		<tr> 
			<th>Address:</th> <td><?=$row['address']?></td> 
		</tr>
		<tr> 
			<th>Zip Code:</th> <td><?=$row['pin']?></td> 
		</tr>
		<tr> 
			<th>Created On:</th> <td><?=$row['created_on']?></td> 
		</tr>
		<tr> 
			<th>Services Access:</th> <td><?=$row['service_access']?></td> 
		</tr>
		<tr> 
			<th>User Type:</th> <td><?=$userType['usertype']?></td> 
		</tr>
		<tr> 
			<th>Creator Id:</th> <td><?=$row['creator_id']?></td> 
		</tr>
		<?php
			if($userType['usertype']=="white_label"){
				$doma = $mysqlClass->get_field_data("domain","add_white_label"," where user_id = '".$post['uid']."' ");
		?>
		<tr> 
			<th>WL URL :</th> <td><?=$doma['domain']?></td> 
		</tr>
		<?php
			}
		?>	
		
		<?php			
			$cuserType = $userClass->check_user_type($row['creator_id']);
			if(!empty($cuserType)){
				$crow = $mysqlClass->get_field_data("name,cname,mobile,id","add_cust"," where id = '".$row['creator_id']."' ");
		?>
		<tr> 
			<th colspan="2" style="text-align:center">Created By</th> 
		</tr>
		<tr> 
			<th>Creator Name:</th> <td><?=$crow['name']?> - (#<?=$crow['id']?>)</td> 
		</tr>		
		<tr> 
			<th>Creator Company:</th> <td><?=$crow['cname']?></td> 
		</tr>		
		<tr> 
			<th>Creator Mobile:</th> <td><?=$crow['mobile']?></td> 
		</tr>		
		<tr> 
			<th>Creator User Type:</th> <td><?=$cuserType['usertype']?></td> 
		</tr>
		<?php
			if($cuserType['usertype']=="white_label"){
				$doma = $mysqlClass->get_field_data("domain","add_white_label"," where user_id = '".$row['creator_id']."' ");
		?>
		<tr> 
			<th>WL URL :</th> <td><?=$doma['domain']?></td> 
		</tr>
		<?php
			}
		?>
		<?php } ?>
	</tbody>
	</table>

<?php	
	} 
?>