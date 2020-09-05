<style>
.easy-steps-style-one {
  background: <?=$colorCode?> url(include_front/assist/images/easy-step-bg.png) center center no-repeat;
  background-size: cover;
}
.single-solution-style-one::before {
    background: <?=$colorCode?>;
    border-radius: 6px;
}
.top-bar {
    background: <?=$colorCode?>;
    padding: 14.75px 50px;
}
</style>
    <header class="site-header ">
		<?php include('include_front/top-bar.php'); ?>

        <nav class="navbar navbar-expand-lg navbar-light header-navigation stricky header-style-two">
            <div class="container clearfix">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="logo-box clearfix">
                    <a class="navbar-brand" href="index.php">
                        <img src="<?=BASE_URL?>uploads/logo/<?=$logo?>" />
                    </a>
                    <button class="menu-toggler" data-target="#main-nav-bar">
                        <span class="fa fa-bars"></span>
                    </button>
                </div><!-- /.logo-box -->

                <!-- Collect the nav links, forms, and other content for toggling -->
                <?php include('include_front/nav-menu.php'); ?>
                <!-- /.right-side-box -->
            </div>
            <!-- /.container -->
        </nav>
    </header><!-- /.site-header -->