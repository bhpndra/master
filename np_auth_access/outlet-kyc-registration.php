<?php
	include_once('inc/head.php'); 
	include_once('inc/header.php');
	include_once('inc/apivariable.php');

	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	$post = $helper->clearSlashes($_POST);
	$get = $helper->clearSlashes($_GET);
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
                                        <span> Outlet KYC Registration </span>
                                    </li>
                                </ul>
<?php
					if (isset($_SERVER['HTTPS']) &&
                            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
                            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
                            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                        $protocol = 'https://';
                    } else {
                        $protocol = 'http://';
                    }
                    $file_path = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
                                
                    if (isset($_POST['submit_aadhar'])) {
                        
                        $outletid = $get['outletid'];  //outletid
                        $user_id = $get['uid']; // retailer_id  
                        $aadhaar_no = $post['aadhaar_number']; // retailer aadhaar_number
                        $aadhaar_id = $post['aadharid_set']; // api response aadhaarid
                        $aadhaarimage_name = $_FILES['aadhaarimage']['name'];
                        $aadhaarimage_type = $_FILES['aadhaarimage']['type'];
                        $aadhaarimage_tmp_name = $_FILES['aadhaarimage']['tmp_name'];
                        $aadhaarimage_error = $_FILES['aadhaarimage']['error'];
                        $aadhaarimage_size = $_FILES['aadhaarimage']['size'];

                        

                        if($aadhaarimage_name != '' && $aadhaarimage_type != '' && $aadhaarimage_tmp_name != '' && $aadhaarimage_error != 4 && $aadhaarimage_size != 0){
                            $checkQuery = $mysqlObj->mysqlQuery("SELECT `pan_no` FROM `outlet_kyc` WHERE `user_id`='$user_id' and `sources`='I'");
                            
                            if ($checkQuery->rowCount() > 0) {

                                $data_row = $checkQuery->fetch(PDO::FETCH_ASSOC);
                                $pan_no = $data_row['pan_no'];

                                if ( $aadhaarimage_size < 200000 ) {    

                                    $target_dir = "../r_admin/pan-doc/";
                                    $aadhar_file_name = '';
                                   // $gst_file_name = '';
                                   // $full_file_path = $file_path . "/" . $target_dir;

                                    if (isset($_FILES['aadhaarimage']['name'])) {

                                        $ext = pathinfo($_FILES['aadhaarimage']['name'], PATHINFO_EXTENSION);
                                        if($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'PNG' || $ext == 'JPEG'){

                                        $aadhar_file_name = 'KYC_' . time() . basename(strtolower(str_replace(" ", "", $_FILES["aadhaarimage"]["name"])));
                                        $aadhar_file_tmp = $_FILES['aadhaarimage']['tmp_name'];

                                        move_uploaded_file($aadhar_file_tmp, $target_dir . $aadhar_file_name);
                                    }else {
                                        echo '<script type="text/javascript">alert("Aadhaar image must be JPG or PNG");location.replace("/outlet-kyc-registration.php");</script>';
                                        die;
                                    }


                                        
                                    } 
                                    
                                
                                    //The url you wish to send the POST request to
                                    $url = 'https://netpaisa.com/nps/api/aeps/aadhar_upload_kyc_document';

                                    $img_encoded_url = base64_encode(file_get_contents( $target_dir .  $aadhar_file_name));

                                    //The data you want to send via POST
                                    $fields = [
                                            'api_access_key'        => $api_access_key, 
                                            'outletid'              => $outletid,
                                            'pan_no'                => $pan_no,
                                            'aadhar_no'             => $aadhaar_no,
                                            'aadharid'              => $aadhaar_id,
                                            'aadharfilename'        => 'data:image/jpeg;base64,'. $img_encoded_url
										];
                                       //print_r($fields);       echo "<br/>";                          
                                    $result = $helper->netpaisa_curl($url, $fields);
                                    
                                    $rech_data  = json_decode($result,true);
									//print_r($rech_data);
                                    

                                    if (!empty($rech_data)) {
                                        
                                        //Response
                                        $status = (int)$rech_data['ERR_STATE'];
                                        $message = $rech_data['MSG'];
                                    

                                        if ($status == 0) {
                                            
                                            $mysqlObj->mysqlQuery("UPDATE outlet_kyc SET `aadhaar` ='$aadhaar_no', `aadhaarimg`= '$aadhar_file_name' WHERE `user_id`='$user_id' and `sources`='I'");
                                            
                                            echo "<script>alert('" . "Aadhaar Status : " . $status . " Message : " . $message . "')</script>";

								   
                                        }  else {
                                            echo "<script>alert('" . "Aadhaar Status : " . $status . " Message : " . $message . "')</script>";
                                        }
                                    }
                                } else {
                                    echo "<script>alert('Please select each image with size : 200KB')</script>";
                                    echo "<script>window.location = 'outlet-kyc-registration.php';</script>";
                                }
                            } 
                        
                        } else {
                            echo "<script>alert('Select Aadhar Image!!')</script>";    
                        } 
                        
                        if($aadhaar_no == ''){
                            echo "<script>alert('Input Aadhar Number!!')</script>"; 
                        }
                    }
					
					
					
					
					if (isset($_POST['submit_shop'])) {
                        
                        $outletid = $get['outletid'];  //outletid
                        $user_id = $get['uid']; // retailer_id  
                        $photo_id = $post['photoid_set']; // api response photoid                        

                        $shopimage_name = $_FILES['shopimage']['name'];
                        $shopimage_type = $_FILES['shopimage']['type'];
                        $shopimage_tmp_name = $_FILES['shopimage']['tmp_name'];
                        $shopimage_error = $_FILES['shopimage']['error'];
                        $shopimage_size = $_FILES['shopimage']['size'];

                        if($shopimage_name != '' && $shopimage_type != '' && $shopimage_tmp_name != '' && $shopimage_error != 4 && $shopimage_size != 0){
                            $checkQuery = $mysqlObj->mysqlQuery("SELECT `pan_no` FROM `outlet_kyc` WHERE `user_id`='$user_id' and `sources`='I'");
                            
                            if ($checkQuery->rowCount() > 0) {

                                $data_row = $checkQuery->fetch(PDO::FETCH_ASSOC);
                                $pan_no = $data_row['pan_no'];

                                if ( $shopimage_size < 200000 ) {    

                                    $target_dir = "../r_admin/pan-doc/";
                                    $shopimg_file_name = '';
                                    //$gst_file_name = '';
                                    //$full_file_path = $file_path . "/" . $target_dir;

                                    if (isset($_FILES['shopimage']['name'])) {
                                        $ext = pathinfo($_FILES['shopimage']['name'], PATHINFO_EXTENSION);
                                        if($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'PNG' || $ext == 'JPEG'){

                                        $shopimg_file_name = 'KYC_' . time() . basename(strtolower(str_replace(" ", "", $_FILES["shopimage"]["name"])));
                                        $shopimg_file_tmp = $_FILES['shopimage']['tmp_name'];
                                        
                                        move_uploaded_file($shopimg_file_tmp, $target_dir . $shopimg_file_name);
                                        }else {
                                                            echo '<script type="text/javascript">alert("Shop image slip must be JPG or PNG");location.replace("/outlet-kyc-registration.php");</script>';
                                                            die;
                                                        }
                                        
                                    }
                                    
                           
                                    //The url you wish to send the POST request to
                                    $url = 'https://netpaisa.com/nps/api/aeps/photo_upload_kyc_document';
                                    // echo BASE_URL . $target_dir .  $shopimg_file_name;
                                    $img_encoded_url = base64_encode(file_get_contents( $target_dir .  $shopimg_file_name));

                                    //The data you want to send via POST
                                    $fields = [
                                            'api_access_key'        => $api_access_key, 
                                            'outletid'              => $outletid,
                                            'pan_no'                => $pan_no,
                                            'document_id'           => $photo_id,
                                            'document_filename'     => 'data:image/jpeg;base64,'. $img_encoded_url                                     
                                    ];
                                    
                                    $result = $helper->netpaisa_curl($url, $fields);
                                    
                                    $rech_data  = json_decode($result,true);


                                    if (!empty($rech_data)) {
                                        
                                        $status = (int)$rech_data['ERR_STATE'];
                                        $message = $rech_data['MSG'];
                                    
                                        if ($status == 0) {

                                            $mysqlObj->mysqlQuery("UPDATE outlet_kyc SET  `shopimg`= '$shopimg_file_name'  WHERE `user_id`='$user_id' and `sources`='I'");
                                     
                                            echo "<script>alert('" . "Shop Image Status : " . $status . " Message : " . $message . "')</script>";
                                                
                                        }  else {
                                             echo "<script>alert('" . "Shop Image Error : " . $status . " Message : " . $message . "')</script>";
                                        }
                                    }
                                } else {
                                    echo "<script>alert('Please select each image with size : 200KB')</script>";
                                    echo "<script>window.location = 'outlet-kyc-registration.php';</script>";
                                }
                            }
                        
						} else{
                            echo "<script>alert('Select Shop Image!!')</script>";    
                        }
                        
                        
                    }
					
					
$sql = "SELECT ol.*, u.name as uname,u.cname,u.mobile as umobile,u.city FROM `outlet_kyc` as ol, add_cust as u WHERE ol.user_id = u.id and ol.user_id = '".$get['uid']."' and ol.sources = 'I'";	
$resOutlet = $mysqlObj->mysqlQuery($sql)->fetch(PDO::FETCH_ASSOC);
?>

																<input type="hidden" value="<?=$resOutlet['pan_no']?>" id="panid_outlet"/>
																<input type="hidden" value="<?=$resOutlet['outletid']?>" id="outletid"/>
																<input type="hidden" value="<?=$resOutlet['aadhaar']?>" id="aadharid_outlet"/>
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="portlet box blue">
											<div class="portlet-title">
												<div class="caption">
													<i class="fa fa-cogs"></i>  Outlet KYC Registration  </div>
											</div>
											<div class="portlet-body">
													<div class="well">
													<div class="row show-grid">
														<table class="table table-bordered">
                                                            <thead>
                                                                <tr>
																	<th style="width: 200px;">Agent Details</th>
																	<th style="width: 300px;">Outlet Info</th>
																	<th scope="col">Outlet Status</th>
																	<th style="width: 100px;">Aadhaar</th>
																	<th style="width: 100px;">Shop</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
																<tr>
																	<td>
																		<strong>Name : </strong><?php echo $resOutlet["uname"]; ?><br/>
																		<strong>Company : </strong><?php echo $resOutlet["cname"]; ?><br/>
																		<strong>Mobile : </strong><?php echo $resOutlet["umobile"]; ?><br/>
																	
																	</td>
																	<td>
																		<strong>Name : </strong><?php echo $resOutlet["name"]; ?><br/>
																		<strong>Email : </strong><?php echo $resOutlet["email"]; ?><br/>
																		<strong>Outlet Id : </strong><?php echo $resOutlet["outletid"]; ?><br/>
																		<strong>Mobile : </strong><?php echo $resOutlet["phone1"]; ?><br/>
																		<strong>PAN : </strong><?php echo $resOutlet["pan_no"]; ?><br/>
																		<strong>Reg. Date : </strong><?php echo $resOutlet['registration_date']; ?><br/>
																		<strong>City : </strong><?php echo $resOutlet["city"]; ?><br/>
																		<strong>Address : </strong><?php echo $resOutlet["address"]; ?></td>
																	
																	</td>
																	<td><?php echo $resOutlet["outlet_status"]; ?></td>
																	<td>
																	<?php if(!empty($resOutlet["aadhaarimg"])){ ?>
																	<img src="https://aeps.apnachirag.com/r_admin/pan-doc/<?php echo $resOutlet["aadhaarimg"]; ?>" width="150px" />
																	<br/><br/>
																	<strong>Aadhaar No.: </strong><?php echo $resOutlet["aadhaar"]; ?>
																	<?php } ?>
																	</td>
																	<td>
																	<?php if(!empty($resOutlet["shopimg"])){ ?>
																	<img src="https://aeps.apnachirag.com/r_admin/pan-doc/<?php echo $resOutlet["shopimg"]; ?>" width="150px" />
																	<?php } ?>
																	</td>
																	
																	
																</tr>
                                                            </tbody>
														</table>
													</div>
													<div class="row show-grid">
														<div class="dataTables_filter" id="aadhaarImageUploadFrom">
															<form action='' role="form" method='post' enctype="multipart/form-data">
																<div class="col-md-12"><h3> Outlet KYC Registration </h3></div>
																<div class="form-group col-md-5">
																	<label>Aadhaar Image</label>
																	<div>
																		<input type='file' name='aadhaarimage' id="aadhaarimage"  class='form-control' placeholder="Mobile Number" required>
																	</div>
																</div>
																<div class="form-group col-md-5">
																	<label>Aadhaar Number</label>
																	<div>
																		<input type='text' name='aadhaar_number' id="aadhaar_number"  class='form-control' required>
																		<input type="hidden" id="aadharid_set" name="aadharid_set">
																	</div>
																</div>
																<div class="form-group col-md-2">
																	<label style="opacity: 0;"> Submit Aadhaar</label>
																	<div>
																		<input type="submit" name="submit_aadhar" value="Submit Aadhaar" id="register_pan" class="btn btn-primary"> 
																	</div>
																</div>
                                    
															</form>
														</div>
														<div id="screeningAadhaar"></div>
														
														<div class="dataTables_filter" id="shopImageUploadFrom">
															<form action='' role="form" method='post' enctype="multipart/form-data">
																<div class="form-group col-md-9">
																	<label>Shop Image</label>
																	<div>
																		<input type='file' name='shopimage' id="shopimage"  class='form-control' placeholder="Mobile Number" required>
																	</div>
																</div>
																<div class="form-group col-md-3">
																	<label style="opacity: 0;"> Submit Shop Image</label>
																	<div>
																		<input type="submit" name="submit_shop" value="Submit Shop Image" id="submit_shop" class="btn btn-primary"> 
																		
																	<input type="hidden" id="photoid_set" name="photoid_set">
																	</div>
																</div>
                                    
															</form>
														</div>
														<div id="screeningPhoto"></div>
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
        </div>
<?php include_once('inc/footer.php'); ?>
<script>
    $( document ).ready(function() { 
        var pan_no = $('#panid_outlet').val();
        var outletid = $('#outletid').val();
        var aadhar_no = $('#aadharid_outlet').val();

        $.ajax({
                type: 'POST',
                data: {outletid: outletid, pan_no: pan_no},
                cache: false,
                url: 'ajax/get_adhar_pan_ids.php',
                success: function (response)
                {	
                    console.log(response);
                    var data_res = JSON.parse(response);                    
                    var id = data_res.IDS;
                    var doc = data_res.ID_NAME;
                    //alert(data_res.DATA.data.SCREENING);
					if(id!=0){
						if(id.includes("##")){
							var split_data_res = id.split('##');                        
							$("#aadharid_set").val(split_data_res[0]);
							$("#photoid_set").val(split_data_res[1]);
						} else if (doc == 'Aadhaar / Voter ID' || doc == 'Photo @ Store'){
							if(doc == 'Aadhaar / Voter ID'){
								$("#aadharid_set").val(id);
							}
							if(doc == 'Photo @ Store'){
								$("#photoid_set").val(id);
							}
						}
					}
                    else if (doc == 'Aadhaar / Voter ID' || doc == 'Photo @ Store'){

                        if(doc == 'Aadhaar / Voter ID'){
                            $("#aadharid_set").val(id);
                        }
                        if(doc == 'Photo @ Store'){
                            $("#photoid_set").val(id);
                        }

                    }
					var i = 0;
					var screening = data_res.screening;
					var approved = data_res.approved;
					
						if(screening.includes("Aadhaar / Voter ID")){ 
							$("#aadhaarImageUploadFrom").css("display", "none");
							$("#screeningAadhaar").html("<h4>Aadhaar Screening</h4>");
						}
					
						if(screening.includes("Photo @ Store")){  
							$("#shopImageUploadFrom").css("display", "none");
							$("#screeningPhoto").html("<h4>Photo Screening</h4>");
						}
						
						if(approved.includes("Aadhaar / Voter ID")){ 
							$("#aadhaarImageUploadFrom").css("display", "none");
							$("#screeningAadhaar").html("<h4>Aadhaar Approved</h4>");
						}
					
						if(approved.includes("Photo @ Store")){  
							$("#shopImageUploadFrom").css("display", "none");
							$("#screeningPhoto").html("<h4>Photo Approved</h4>");
						}
											
					//alert($('#photoid_set').val());
					//console.log(data_res);
                }
            });
        
    });
</script>
</body>
</html>