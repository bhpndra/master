    <footer class="site-footer">
        <div class="upper-footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 d-flex">
                        <div class="footer-widget links-widget my-auto links-widget-one">
                            <div class="title-box">
                                <h3>Quick Link</h3>
                            </div><!-- /.title-box -->
                            <ul class="link-lists">
                                <li><a href="index.php">Home</a></li>
                                <li><a href="about.php">About</a></li>
                                <li><a href="services.php">Services</a></li>
                                <li><a href="contact.php">Contact Us</a></li>
                                <li><a href="retailer_signup.php">Registration</a></li>
                                <li><a href="terms-and-conditions.php">Terms and Conditions</a></li>
                                <li><a href="privacy-policy.php">Privacy Policy</a></li>
                            </ul>
                        </div><!-- /.footer-widget -->
                    </div><!-- /.col-lg-3 -->
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 d-flex">
                        <div class="footer-widget links-widget my-auto">
                            <div class="title-box">
                                <h3>Get In Touch</h3>
                            </div><!-- /.title-box -->
                            <ul class="link-lists">
                                <li><a href="#"><i class="fa fa-map"></i> &nbsp; <?=$address?></a></li>
								<li><a href="#"><i class="fa fa-envelope"></i> &nbsp; <?=$email?></a></li>
								<li><a href="#"><i class="fa fa-phone"></i> &nbsp; <?=$support_number?></a></li>
                            </ul>
                        </div><!-- /.footer-widget -->
                    </div><!-- /.col-lg-2 -->
                    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 d-flex">
                        <div class="footer-widget my-auto">
                            <div class="btn-box">
                                <a href="<?=$app_link?>" target="_blank" class="thm-btn">Download App</a>
                                <span class="btn-tag-line">Try a better way <i class="payonline-icon-share"></i></span>
                            </div><!-- /.btn-box -->
                            <div class="social">
                                <a href="#"><i class="fa fa-twitter"></i></a>
                                <a href="#"><i class="fa fa-facebook"></i></a>
                                <a href="#"><i class="fa fa-linkedin"></i></a>
                            </div><!-- /.social -->
                        </div><!-- /.footer-widget -->
                    </div><!-- /.col-lg-5 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </div><!-- /.upper-footer -->
        <div class="bottom-footer">
            <div class="container">
                <p>&copy; Copyright 2019 by <a href="#" ><?=!empty($copyright) ? $copyright : 'Risein Tech'?></a></p>
            </div><!-- /.container -->
        </div><!-- /.bottom-footer -->
    </footer><!-- /.site-footer -->


</div><!-- /.page-wrapper -->

<a href="#" data-target="html" class="scroll-to-target scroll-to-top"><i class="fa fa-long-arrow-up"></i></a>
<!-- /.scroll-to-top -->


<script src="include_front/assist/js/jquery.js"></script>
<script src="include_front/assist/js/bootstrap.bundle.min.js"></script>
<script src="include_front/assist/js/jquery.magnific-popup.min.js"></script>
<script src="include_front/assist/js/owl.carousel.min.js"></script>
<script src="include_front/assist/js/isotope.js"></script>
<script src="include_front/assist/js/bootstrap-select.min.js"></script>
<script src="include_front/assist/js/jquery.bxslider.min.js"></script>
<script src="include_front/assist/js/jquery.validate.min.js"></script>
<script src="include_front/assist/js/theme.js"></script>


</body>
</html>