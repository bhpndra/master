<!DOCTYPE html>
<html lang="en">
<?php include('include_front/head.php'); ?>
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
            <ul class="breadcrumb">
                <li>
                    <a href="#">Home</a>
                </li>
                <li>
                    <span>About Us</span>
                </li>
            </ul><!-- /.breadcrumb -->
            <h1>About Us</h1>
        </div><!-- /.container -->
    </div><!-- /.inner-banner -->
    
    <section class="sec-pad cta-five home-five">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cta-content">
                        <div class="sec-title">
                            <span>Privacy Policy</span>
                        </div><!-- /.sec-title -->
						<?php 
								// ==============================================
								//@ Home Us Page Content
								//===============================================
								$id = 6;
								$login_page_content =  get_page_content($id,$conn,$wl_id);
								$page_content  = str_replace("<p>&nbsp;</p>",'', $login_page_content['page_content']);
								$page_content = str_replace("\r\n",'', $page_content);
							   if(!empty($login_page_content['page_content'])){
								  echo  $page_content;  
							    } else {
									
								}
							  ?>
                        
                    </div><!-- /.cta-content -->
                </div><!-- /.col-lg-6 -->
				
            </div><!-- /.row -->
        </div><!-- /.container -->
    </section><!-- /.black-shape-bg -->

<?php include('include_front/footer.php'); ?>