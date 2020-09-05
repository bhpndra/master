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
                    <span>Contact</span>
                </li>
            </ul><!-- /.breadcrumb -->
            <h1>Contact</h1>
        </div><!-- /.container -->
    </div><!-- /.inner-banner -->

    <section class="contact-page-infos sec-pad-top">
        <div class="container">
			<?php 
					// ==============================================
					//@ Home Us Page Content
					//===============================================
					$id = 3 ;
					$login_page_content =  get_page_content($id,$conn,$wl_id);
					$page_content  = str_replace("<p>&nbsp;</p>",'', $login_page_content['page_content']);
					$page_content = str_replace("\r\n",'', $page_content);
					$page_content = str_replace("\'",'', $page_content);
					$page_content = str_replace('\"','', $page_content);
					
				   if(!empty($login_page_content['page_content'])){
					  echo  $page_content;  
					} else {
			?>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="single-contact-page-info">
                        <div class="icon-box">
                            <i class="fa fa-map"></i>
                        </div><!-- /.icon-box -->
                        <p><?=$address?></p>
                    </div><!-- /.single-contact-page-info -->
                </div><!-- /.col-lg-4 -->
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="single-contact-page-info">
                        <div class="icon-box">
                            <i class="fa fa-phone"></i>
                        </div><!-- /.icon-box -->
                        <p>Mobile: <?=$support_number?></p>
                    </div><!-- /.single-contact-page-info -->
                </div><!-- /.col-lg-4 -->
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="single-contact-page-info">
                        <div class="icon-box">
                            <i class="fa fa-envelope"></i>
                        </div><!-- /.icon-box -->
                        <p><?=$email?></p>
                    </div><!-- /.single-contact-page-info -->
                </div><!-- /.col-lg-4 -->
            </div><!-- /.row -->
					<?php } ?>
        </div><!-- /.container -->
    </section><!-- /.contact-page-infos -->


    <section class="contact-page-content sec-pad">
        <div class="container">
            <div class="sec-title text-center">
                <span>Contact with us</span>
                <h2>Let’s meet with our expert.</h2>
            </div><!-- /.sec-title -->
            <form action="#" class="meeting-form contact-form">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="name" placeholder="Your name">
                    </div><!-- /.col-md-12 -->
                    <div class="col-md-4">                            
                        <input type="text" name="email" placeholder="Email address">
                    </div><!-- /.col-md-12 -->
                    <div class="col-md-4">                            
                        <input type="text" name="phone" placeholder="Phone Number">
                    </div><!-- /.col-md-12 -->
                    <div class="col-md-12">                            
                        <textarea name="message" placeholder="Comment or questions"></textarea>
                    </div><!-- /.col-md-12 -->
                    <div class="col-md-12">
                        <div class="btn-box">
                            <button type="submit" class="thm-btn">Submit</button>
                        </div><!-- /.btn-box -->
                    </div><!-- /.col-md-12 -->
                </div><!-- /.row -->
            </form><!-- /.meeting-form -->
            <div class="result"></div><!-- /.result -->
        </div><!-- /.container -->
    </section><!-- /.contact-page-content -->
<?php include('include_front/footer.php'); ?>