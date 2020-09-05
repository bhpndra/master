
    <div id="minimal-bootstrap-carousel" class="carousel slide carousel-fade slider-content-style slider-home-one">
        <!-- Wrapper for slides -->
        <div class="carousel-inner">
<?php   
echo "SELECT * FROM `slider_settings` where wl_id = '".$wl_id."' ORDER BY `id` DESC" ;              
	$class = 1;
	$slider_list = mysqli_query( $conn, "SELECT * FROM `slider_settings` where wl_id = '".$wl_id."' ORDER BY `id` DESC" );
	if(mysqli_num_rows($slider_list) > 0 ){} 
	else{
		$slider_list = mysqli_query( $conn, "SELECT * FROM `slider_settings` where id in(1,2,3) ORDER BY `id` DESC" );
	}
	 while ( $slider_row = mysqli_fetch_array($slider_list) ){
			$slider_img = trim('slider/'.$slider_row["image"]);
?>
            <div <?php if($class == 1){ echo 'class="carousel-item active slide-'.$class.'"'; } else{ echo 'class="carousel-item slide-'.$class.'"'; }?> style="background-image: url(<?=$slider_img?>);">
                <div class="carousel-caption">
                    <div class="container">
                        <div class="box valign-middle">
                            <div class="content text-left">
                                <h3 data-animation="animated fadeInUp">Welcome to <br> <?=$site_name?></h3>
                                <p data-animation="animated fadeInDown">Start Your Own Business with Us.</p>
                                <a data-animation="animated fadeInDown" href="<?=$app_link?>"  target="_blank" class="thm-btn ">Download App</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php 
		
		$class++;
	}
?>
        </div>
        <!-- Controls -->
        <a class="carousel-control-prev" href="#minimal-bootstrap-carousel" role="button" data-slide="prev">
            <i class="fa fa-arrow-left"></i>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#minimal-bootstrap-carousel" role="button" data-slide="next">
            <i class="fa fa-arrow-right"></i>
            <span class="sr-only">Next</span>
        </a>
    </div>

	<section class="white-paper-wrapper sec-pad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="video-box-style-one">
                        <div class="img-box">
                            <img src="include_front/assist/images/our-services.png" >                            
                        </div><!-- /.img-box -->
                    </div><!-- /.video-box-style-one -->
                </div><!-- /.col-lg-6 -->
                <div class="col-lg-6">
                    <div class="white-paper-content">
                        <div class="sec-title">
    						<span>About us</span>
    						<h2>Welcome to <?=$site_name?></h2>
    					</div>
                        <p>
							<?php 
								// ==============================================
								//@ Home Us Page Content
								//===============================================
								$id = 1 ;
										$login_page_content =  get_page_content($id,$conn,$wl_id);
							   if(!empty($login_page_content['page_content'])){
								  echo  substr($login_page_content['page_content'],0,350);  
							    } else {
									echo "$site_name is a Professional B2b Software Company Based in India. We at $site_name provide Recharge (Mobile, Dth, Data Card), Bill Payment System (Electricity, Landline, Mobile Bill Payment), Aadhaar Enabled Payment System (AEPS), Domestic Money Transfer, Travel Booking(Bus, Flight , Hotel Booking), Uti Pan Card.<br/><br/>

We offer a complete online recharge business solution, where internet users can recharge their Mobile (postpaid / prepaid), DTH, Data card, Landline, Gas, Electricity etc and will make payment through payment gateway. If recharge goes fail then amount will be credited into customer's wallet. Our online recharge includes cash back and reward points features helpful to attract customers. Apart from that coupon API can also be integrated.";
								}
							  ?>
						
						</p>
                        <div class="btn-box">
                            <a href="#" class="thm-btn">Read More</a>
                        </div><!-- /.btn-box -->
                    </div><!-- /.white-paper-content -->
                </div><!-- /.col-lg-6 -->
            </div><!-- /.row -->
    </section>

    <section class="solution-style-one sec-pad">
        <div class="container">
            <div class="sec-title text-center">
                <span>Services</span>
                <h2>Our preferred Services</h2>
            </div><!-- /.sec-title -->
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="single-solution-style-one hvr-bounce-to-bottom">
                        <div class="img-box">
                            <img src="include_front/assist/images/solution-1-1.png" />
                        </div><!-- /.img-box -->
                        <div class="text-box">                            
                            <h3><a href="#">Recharge</a></h3>
							<span>We are provides customized recharge softwares for B2B recharge businesses. We offers the best all in one multi mobile recharge that works with all telecom software service providers . . . </span>
                        </div><!-- /.text-box -->
                    </div><!-- /.single-solution-style-one -->
                </div><!-- /.col-lg-6 -->
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="single-solution-style-one hvr-bounce-to-bottom">
                        <div class="img-box">
                            <img src="include_front/assist/images/solution-1-2.png" />
                        </div><!-- /.img-box -->
                        <div class="text-box">
                            <h3><a href="#">AEPS</a></h3>
							<span>Our service offered by the National Payments Corporation of India to banks, financial institutions using 'AADHAAR'. AEPS stands for 'AADHAAR Enabled Payment System' . . . </span>
                        </div><!-- /.text-box -->
                    </div><!-- /.single-solution-style-one -->
                </div><!-- /.col-lg-6 -->
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="single-solution-style-one hvr-bounce-to-bottom">
                        <div class="img-box">
                            <img src="include_front/assist/images/solution-1-3.png" />
                        </div><!-- /.img-box -->
                        <div class="text-box">
                            <h3><a href="#">Bill Payment</a></h3>
							<span>We are an online payment platform which utilizes the best technologies. Allows hassle-free payments through the web and mobile devices. It's secure, integrates easily and is cost-effective. </span>
                        </div><!-- /.text-box -->
                    </div><!-- /.single-solution-style-one -->
                </div><!-- /.col-lg-6 -->
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="single-solution-style-one hvr-bounce-to-bottom">
                        <div class="img-box">
                            <img src="include_front/assist/images/solution-1-4.png" />
                        </div><!-- /.img-box -->
                        <div class="text-box">
                            <h3><a href="#">Money Transfer</a></h3>
							<span>We Launched Instant Domestic Money Transfer (DMT) Services. We DMT brings you the convenience of transferring money from your place of residence to any Bank account across the country . . . </span>
                        </div><!-- /.text-box -->
                    </div><!-- /.single-solution-style-one -->
                </div><!-- /.col-lg-6 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </section><!-- /.solution-style-one -->

    <section class="easy-steps-style-one sec-pad">
        <div class="container">
            <div class="sec-title text-center light">
                
                <h2>Why Choose Us</h2>
            </div><!-- /.sec-title -->
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                    <div class="single-easy-step-one">
                        <i class="icon-handshake"></i>
                        <h3><a href="#">100% Satisfaction</a></h3>
                        <p>All of satisfied customers</p>
                        <a href="#" class="read-more"><i class="fa fa-arrow-right"></i></a>
                    </div><!-- /.single-easy-step-one -->
                </div><!-- /.col-lg-4 -->
                <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                    <div class="single-easy-step-one">
                        <i class="icon-padlock"></i>
                        <h3><a href="#">Fast and secure</a></h3>
                        <p>Fast and secure payment process</p>
                        <a href="#" class="read-more"><i class="fa fa-arrow-right"></i></a>
                    </div><!-- /.single-easy-step-one -->
                </div><!-- /.col-lg-4 -->
                <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                    <div class="single-easy-step-one">
                        <i class="icon-chat"></i>
                        <h3><a href="#">Quick Support</a></h3>
                        <p>We are avalable here with 24/7.</p>
                        <a href="#" class="read-more"><i class="fa fa-arrow-right"></i></a>
                    </div><!-- /.single-easy-step-one -->
                </div><!-- /.col-lg-4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </section><!-- /.easy-steps-style-one -->

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

