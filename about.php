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
	// new line added to test git
	
	include('include_front/header/header_template1.php');
	
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
                <div class="col-lg-6">
                    <div class="cta-content">
                        <div class="sec-title">
                            <span>About</span>
                        </div><!-- /.sec-title -->
						<?php 
								// ==============================================
								//@ Home Us Page Content
								//===============================================
								$id = 2 ;
								$login_page_content =  get_page_content($id,$conn,$wl_id);
								$page_content  = str_replace("<p>&nbsp;</p>",'', $login_page_content['page_content']);
								$page_content = str_replace("\r\n",'', $page_content);
								$contentPart1 = substr($page_content,0,strpos($page_content,"</p>"));
								$contentPart2 = substr($page_content,strpos($page_content,"</p>"));
							   if(!empty($login_page_content['page_content'])){
								  echo  $contentPart1;  
							    } else {
									echo "<p>Our motto is to be India's one of the leading recharge and bill payment websites because we believe in making your life simpler and more comfortable. Now imagine if you realize suddenly that your phone needs a recharge since the balance is low or you get a call from home that today happens to be the last day of your television recharge, well with us you donâ€™t have to worry at all as we are an awesome one stop shop for all your recharge and bill payment queries and worries.</p>";
								}
							  ?>
                        
                    </div><!-- /.cta-content -->
                </div><!-- /.col-lg-6 -->
                <div class="col-lg-6 d-flex">
                    <div class="featured-image-box my-auto">
                        <img src="include_front/assist/images/mockup-1-5.png" alt=""/>
                    </div><!-- /.featured-image-box -->
                </div><!-- /.col-lg-6 -->
				<?php 
					   if(!empty($contentPart2)){
				?>
				<div class="col-lg-12">
                    <div class="cta-content">						
						<?=$contentPart2; ?>  
                    </div><!-- /.cta-content -->
                </div><!-- /.col-lg-6 -->
				<?php } ?>
            </div><!-- /.row -->
        </div><!-- /.container -->
    </section><!-- /.black-shape-bg -->

    <section class="service-style-five sec-pad">
        <div class="container">
            <div class="sec-title text-center">
                <span>Services</span>
                <h2>Our preferred Services</h2>
            </div><!-- /.sec-title -->
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="single-service-five">
                        <div class="image-box">
                            <img src="include_front/assist/images/recharge.jpg" alt=""/>
                            <div class="box">
                                <a href="#" class="more-btn"><i class="fa fa-arrow-right"></i></a>
                            </div><!-- /.box -->
                        </div><!-- /.image-box -->
                        <div class="text-box">
                            <h3><a href="#">Recharge</a></h3>
                            <p>We are provides customized recharge softwares for B2B recharge businesses. We offers the best all in one multi mobile recharge that works with all telecom software service providers . . . </p>
                        </div><!-- /.text-box -->
                    </div><!-- /.single-service-five -->
                </div><!-- /.col-lg-4 -->
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="single-service-five">
                        <div class="image-box">
                            <img src="include_front/assist/images/aeps.jpg" alt=""/>
                            <div class="box">
                                <a href="#" class="more-btn"><i class="fa fa-arrow-right"></i></a>
                            </div><!-- /.box -->
                        </div><!-- /.image-box -->
                        <div class="text-box">
                            <h3><a href="#">AEPS</a></h3>
                            <p>Our service offered by the National Payments Corporation of India to banks, financial institutions using 'AADHAAR'. AEPS stands for 'AADHAAR Enabled Payment System' . . .</p>
                        </div><!-- /.text-box -->
                    </div><!-- /.single-service-five -->
                </div><!-- /.col-lg-4 -->
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="single-service-five">
                        <div class="image-box">
                            <img src="include_front/assist/images/bbps.jpg" alt=""/>
                            <div class="box">
                                <a href="#" class="more-btn"><i class="fa fa-arrow-right"></i></a>
                            </div><!-- /.box -->
                        </div><!-- /.image-box -->
                        <div class="text-box">
                            <h3><a href="#">Bill Payment</a></h3>
                            <p>We are an online payment platform which utilizes the best technologies. Allows hassle-free payments through the web and mobile devices. It's secure, integrates easily and is cost-effective. </p>
                        </div><!-- /.text-box -->
                    </div><!-- /.single-service-five -->
                </div><!-- /.col-lg-4 -->
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="single-service-five">
                        <div class="image-box">
                            <img src="include_front/assist/images/money-transfer.jpg" alt=""/>
                            <div class="box">
                                <a href="#" class="more-btn"><i class="fa fa-arrow-right"></i></a>
                            </div><!-- /.box -->
                        </div><!-- /.image-box -->
                        <div class="text-box">
                            <h3><a href="#">Money Transfer</a></h3>
                            <p>We Launched Instant Domestic Money Transfer (DMT) Services. We DMT brings you the convenience of transferring money from your place of residence to any Bank account across the country . . . </p>
                        </div><!-- /.text-box -->
                    </div><!-- /.single-service-five -->
                </div><!-- /.col-lg-4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </section><!-- /.service-style-five -->


    <section class="client-style-one">
        <div class="container">
            <div class="client-carousel-one owl-carousel owl-theme">
                <div class="item">
                    <img src="include_front/assist/images/icon1.png" />
                </div><!-- /.item -->
				<div class="item">
                    <img src="include_front/assist/images/icon2.png" />
                </div><!-- /.item -->
				<div class="item">
                    <img src="include_front/assist/images/icon3.png" />
                </div><!-- /.item -->
				<div class="item">
                    <img src="include_front/assist/images/icon4.png" />
                </div><!-- /.item -->
				<div class="item">
                    <img src="include_front/assist/images/icon5.png" />
                </div><!-- /.item -->
				<div class="item">
                    <img src="include_front/assist/images/icon6.png" />
                </div><!-- /.item -->
				<div class="item">
                    <img src="include_front/assist/images/icon7.png" />
                </div><!-- /.item -->
				<div class="item">
                    <img src="include_front/assist/images/icon8.png" />
                </div><!-- /.item -->
                <div class="item">
            </div><!-- /.client-carousel-one owl-carousel owl-theme -->
        </div><!-- /.container -->
    </section><!-- /.client-style-one -->
<?php include('include_front/footer.php'); ?>