<!DOCTYPE html>
<html lang="en">
<?php
	include('include_front/head.php'); 
	include('include/lib.php'); 
?>
<link rel="stylesheet" type="text/css" id="theme" href="include_front/assist/css/sweetalert.css"/>
<body>
<div class="page-wrapper">
<?php
	$template = 1;
	$template = @$_GET['tem'];
	
	if($template==2){
		include('include_front/header/header_template2.php');
	} else if($template==3){
		include('include_front/header/header_template3.php');
	} else if($template==4){
		include('include_front/header/header_template4.php');
	} else {
		include('include_front/header/header_template1.php');
	}
	
?>
    <div class="inner-banner text-center">
        <div class="container">
            <h1>Registration Request </h1>
        </div><!-- /.container -->
    </div><!-- /.inner-banner -->
    
<?php
$msg = '';
  if (isset($_POST['add'])) {
    $reg_url = BASE_URL . "/api/registration-request.php";
    $_POST_fields = array(
			        'name' 					=> $_POST['name'],
					'mobile' 				=> $_POST['mobile'],
					'email' 				=> $_POST['email'],
					'type' 					=> $_POST['type'],
					'cname'	 				=> $_POST['cname'],
					'city' 					=> $_POST['city'],
					'state' 				=> $_POST['state'],
					'address' 	    	    => $_POST['address'],
					'DOMAIN_NAME' 	    	    => DOMAIN_NAME,
					'pin' 				    => $_POST['pin']
				    );
    				
				   $responseAPI = api_curl($reg_url, $_POST_fields, $headerArray);
				    $resAPI = json_decode($responseAPI, true);
				  // print_r($responseAPI);	
				    if ($resAPI['ERROR_CODE'] == 0) {				        
				        $msg = $resAPI['MESSAGE'];				       
				    } else {
				        $msg = $resAPI['MESSAGE'];
				    }
} 

?>

   
<?php
//print_r($curl_res_arr);
?>
    <section class="contact-page-content sec-pad">
        <div class="container">
            <div class="sec-title text-center">
                <h2>Registration From</h2>
            </div><!-- /.sec-title -->
			<div class="alert alert-primary" <?=(!empty($msg))? 'style="display:block"' : 'style="display:none"' ;?>><?=(!empty($msg))? $msg : '' ;?></div><!-- /.result -->
            <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" id="retailer_signup" class="meeting-form contact-form" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="name" placeholder="Full Name" required />
                    </div>
                    <div class="col-md-6">                            
                        <input type="text" name="mobile" id="mobile_number"  onblur="check_mobile(this.value)"  placeholder="Number"required >
                    </div>
                    <div class="col-md-6">                            
                        <input type="text" name="email" placeholder="Email" id="email" onblur="check_email(this.value)" required />
                    </div>                   
                    <div class="col-md-6">                            
                        <select name="type" style="display: block;border: none;outline: none;background-color: transparent;border-radius: 8px;height: 65px;border: 1px solid #CCD7E0;font-size: 16px;font-weight: 600;color: #798593;width: 100%;padding-left: 30px;">
							<option>Retailer</option>
							<option>Distributor</option>
						</select>
                    </div>                  
                    <div class="col-md-6">                            
                        <input type="text" name="cname" placeholder="Shop Name" required />
                    </div>
                    <div class="col-md-6">                            
                        <input type="text" name="city" placeholder="City"required >
                    </div>
                    <div class="col-md-6">                            
                        <input type="text" name="state" placeholder="State"required >
                    </div>
                    <div class="col-md-6">                            
                        <input type="text" name="pin" placeholder="Zip Code"required >
                    </div>
                    <div class="col-md-12"> 
						<input type="text" name="address" placeholder="Address"required >
                    </div>
                    <div class="col-md-12">
                        <div class="btn-box">
                        	 <button type="submit" id="submit" name="add" class="btn btn-primary">Request To Registration</button>
                             <!-- <a href="" id="submit" class="thm-btn">Submit</a>          -->
                               </div><!-- /.btn-box -->
                    </div><!-- /.col-md-12 -->
                </div><!-- /.row -->
            </form><!-- /.meeting-form -->
            
        </div><!-- /.container -->
    </section><!-- /.contact-page-content -->
<!-- /.client-style-one -->
<?php include('include_front/footer.php'); ?>