<?php
echo "test11111111111111111";



//$conn = mysqli_connect("10.10.2.133", "graminpaymentuser", 'NAv8bQevhyK8sy9r', "graminpayment_db");
$conn = mysqli_connect("10.10.2.133", "netpaisa_sgmmoney", 'pG2CjySphLrGnecp', "netpaisa_sgmmoney");

if (!$conn){
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
else
{
    echo "connect";
    
}
$data =array();

$sql = "SELECT `id`,`outletid`,`name`,`email`,`phone1`,`company`,`address`,`pincode`,`pan_no`,`registration_date`,`outlet_status`,`outlet_kyc`,`dob`,`panimg`,`aadhaar`,`status` FROM outlet_kyc";

         $result = mysqli_query($conn, $sql);

         if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
            
               //echo "ID: " . $row["id"]. "<br>";
               $data[$i]["outletid"] = $row["outletid"];
               $data[$i]["name"] = $row["name"];
               $data[$i]["email"] = $row["email"];
               $data[$i]["mobile"] = $row["mobile"];
               $data[$i]["company"] = $row["company"];
               $data[$i]["pincode"] = $row["pincode"];
               $data[$i]["mobile"] = $row["phone1"];
               $data[$i]["pan_no"] = $row["pan_no"];
               $data[$i]["panimg"] = $row["panimg"];
               $data[$i]["aadhaar"] = $row["aadhaar"];
               $data[$i]["address"] = $row["address"];
               $data[$i]["outlet_status"] = $row["outlet_status"];
               $data[$i]["registration_date"] = $row["registration_date"];
               $data[$i]["outlet_kyc"] = $row["outlet_kyc"];
               $data[$i]["status"] = $row["status"];
               
               $i++;
            }
         } 
         
 mysqli_close($conn);
echo "<pre>";
print_r($data);

$conn_new = mysqli_connect("10.10.2.133", "netpaisa_reseller_user", 'bhhjaf@kfJFTbE8NmEb123', "netpaisa_reseller_db");

foreach($data as $array)
{
     $sql1 = "select id from add_cust where mobile='".$array['mobile']."'";
     $result1 = mysqli_query($conn_new, $sql1);
     $id = mysqli_fetch_assoc($result1);
     
     if($array['outlet_status']=='Pending')
     {
     $outlet_status =0;
     $outlet_kyc =0;
     $status =0;
     }
     else
     {
     $outlet_status =1;
     $outlet_kyc =1;
     $status =1;
     }
     
     if($id['id'])
     {
         $sql_kyc = "select id from outlet_kyc where pan_no='".$array['pan_no']."'";
         $result_kyc = mysqli_query($conn_new, $sql_kyc);
         $kyc_id = mysqli_fetch_assoc($result_kyc);  
    
         if(!$kyc_id['id'])
         {
            $sql_insert = "INSERT INTO `outlet_kyc` ( `user_id`, `outletid`, `name`, `email`, `mobile`, `company`, `address`, `pincode`, `pan_no`, `registration_date`,  `outlet_status`, `outlet_kyc`, `dob`, `panimg`, `aadhaar`, `status`) VALUES
    ('".$id['id']."', '".$array['outletid']."','". $array['name']."','". $array['email']."','".$array['mobile']."','".$array['company']."', '".$array['address']."','".$array['pincode']."','".$array['pan_no']."','".$array['registration_date']."','".$outlet_status."', '".$outlet_kyc."','".$array['dob']."', '".$array['panimg']."', '".$array['aadhaar']."','".$status."' )";
         
             echo $sql_insert.";"."<br>";
         }
     }
     else
     {
         //echo "no data";
     }
    

}
?>

 















