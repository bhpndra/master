<?php
	include("../classes/nps_db_connection.php");
	include("../classes/comman_class.php");
	include("../classes/user_class.php");
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	$userClass = new user_class();
	
	$post = $helpers->clearSlashes($_POST);
	

	if($post['uid']!="")
	{	

		$row = $mysqlClass->get_field_data("*","bankit_outlet_kyc"," where user_id = '".$post['uid']."' and outlet_id = '".$post['outlet_id']."' ");
//print_r($row);
?>

	<table class='table table-bordered'>
		<tbody>
		<tr> 
			<th style="width:150px">Outlet Id:</th> <td style="width:200px"><?=$row['outlet_id']?></td> 
		</tr>
		<tr> 
			<th>Name:</th> <td><?=$row['fname']?> <?=$row['mname']?> <?=$row['lname']?></td> 
		</tr>
		<tr> 
			<th>Company:</th> <td><?=$row['company']?></td> 
		</tr>
		<tr> 
			<th>Mobile:</th> <td><?=$row['mobile']?></td> 
		</tr>
		<tr> 
			<th>Telephone:</th> <td><?=$row['mobile']?></td> 
		</tr>
		<tr> 
			<th>Alter No.:</th> <td><?=$row['alter_mobile']?></td> 
		</tr>
		<tr> 
			<th>Email:</th> <td><?=$row['email_id']?></td> 
		</tr>
		<tr> 
			<th>Date Of Birth:</th> <td><?=$row['dob']?></td> 
		</tr>
		<tr> 
			<th>PAN (ID Card No.):</th> <td><?=$row['pan_no']?></td> 
		</tr>
		<tr> 
			<th>Zip Code:</th> <td><?=$row['pincode']?></td> 
		</tr>
		<tr> 
			<th>District:</th> <td><?=$row['district']?></td> 
		</tr>
		<tr> 
			<th>State:</th> <td><?=$row['state']?></td> 
		</tr>
		<tr> 
			<th>City:</th> <td><?=$row['city']?></td> 
		</tr>
		<tr> 
			<th>Address:</th> <td><?=$row['address']?></td> 
		</tr>
		<tr> 
			<th>Area:</th> <td><?=$row['area']?></td> 
		</tr>
		<tr> 
			<th>Local PIN:</th> <td><?=$row['local_pin']?></td> 
		</tr>
		<tr> 
			<th>Local District:</th> <td><?=$row['local_district']?></td> 
		</tr>
		<tr> 
			<th>Local City:</th> <td><?=$row['local_city']?></td> 
		</tr>
		<tr> 
			<th>Local State:</th> <td><?=$row['local_state']?></td> 
		</tr>
		<tr> 
			<th>Local Address:</th> <td><?=$row['local_address']?></td> 
		</tr>
		<tr> 
			<th>Shop In Code:</th> <td><?=$row['shop_in_code']?></td> 
		</tr>
		<tr> 
			<th>Shop District:</th> <td><?=$row['shop_district']?></td> 
		</tr>	
		<tr> 
			<th>Shop State:</th> <td><?=$row['shop_state']?></td> 
		</tr>		
		<tr> 
			<th>Shop City:</th> <td><?=$row['shop_city']?></td> 
		</tr>		
		<tr> 
			<th>Shop Address:</th> <td><?=$row['shop_address']?></td> 
		</tr>		
		<tr> 
			<th>Shop Area:</th> <td><?=$row['shop_area']?></td> 
		</tr>			

	</tbody>
	</table>

<?php	
	} 
?>