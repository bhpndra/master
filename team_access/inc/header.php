<body class="page-container-bg-solid">
    <div class="page-wrapper">
        <div class="page-wrapper-row">
            <div class="page-wrapper-top">
                <div class="page-header">
                    <div class="page-header-top">
                        <div class="container">
                            <div class="page-logo">
								<a href="index.php">	
									<img src="http://localhost/netpaisa_newlogo/logo.png" alt="netpaisa.com" class="logo-default">
								</a>
                            </div>
                            <a href="javascript:;" class="menu-toggler"></a>
                            <div class="top-menu">
                                <ul class="nav navbar-nav pull-right">
								<li class="dropdown dropdown-extended dropdown-notification dropdown-dark" id="header_notification_bar">
										<a href="#">
											Welcome :
											<?php print_r($_SESSION[_session_username_]); ?>										
										</a>
										
									</li>
                                    <li class="dropdown dropdown-extended dropdown-notification dropdown-dark" id="header_notification_bar">
										<a href="#">
											Balance:
											<span id="adminBalance">[...] </span>	<i id="refAdmBal" class="fa fa-refresh"></i>										
										</a>
										
									</li>
                                    <li class="dropdown dropdown-extended dropdown-notification dropdown-dark">
                                        <a href="logout.php" class="btn btn-success" style="margin-top: 10px;">Logout</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="page-header-menu">
                        <div class="container">
                            <div class="hor-menu  ">
                                <ul class="nav navbar-nav">
                                    <li class="active">
                                        <a href="index.php"><i class="icon-list icons"></i>Dashboard</a>
                                    </li>
									<?php if($_SESSION[_session_usertype_]=="ADMIN" && $_SESSION[_session_userid_]==1){ ?>
									<!--<li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="icon-user icons"></i>Software
                                                <span class="arrow"></span>
                                            </a>
                                        <ul class="dropdown-menu pull-left">										
                                            <li><a href="add-software-user.php" class="nav-link">Add New User</a></li>
                                            <li><a href="software-list.php" class="nav-link">All User</a></li>
                                            <li><a href="manage-networks.php" class="nav-link">Manage Networks</a></li>
                                            <li><a href="create-package.php" class="nav-link">Create Package</a></li>
                                            <li><a href="all-software-package.php" class="nav-link">All Software Package</a></li>
                                        </ul>
                                    </li>-->
									<?php } ?>
									<?php if($_SESSION[_session_usertype_]=="B2B"){ ?>
									<li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="icon-user icons"></i>Manage B2B Panel
                                                <span class="arrow"></span>
                                            </a>
                                        <ul class="dropdown-menu pull-left">										
                                            <li><a href="create-user-package.php" class="nav-link">Crate Package</a></li>
                                            <li><a href="all-user-package.php" class="nav-link">Manage Package</a></li>
                                            <li><a href="add-site-admin.php" class="nav-link">Add Site Admin</a></li>
                                            <li><a href="manage-site-admin.php" class="nav-link">Manage Site Admin</a></li>
                                            <li><a href="user-list.php" class="nav-link">All User</a></li>
                                        </ul>
                                    </li>
									<?php } else { ?>
									<!--<li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="icon-user icons"></i>Users
                                                <span class="arrow"></span>
                                            </a>
                                        <ul class="dropdown-menu pull-left">										
                                            <li><a href="create-user-package.php" class="nav-link">Crate Package</a></li>
                                            <li><a href="all-user-package.php" class="nav-link">All Package</a></li>
                                            <li><a href="add-site-admin.php" class="nav-link">Add New WL</a></li>
                                            <li><a href="manage-site-admin.php" class="nav-link">All White Label</a></li>
                                            <li><a href="user-list.php" class="nav-link">All User</a></li>
                                            <li><a href="credit-to-user.php" class="nav-link">Credit To User</a></li>
                                            <li><a href="debit-from-user.php" class="nav-link">Debit From User</a></li>
                                        </ul>
                                    </li>--!-->
									<?php } ?>
									<li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="icon-credit-card icons"></i>Others
                                                <span class="arrow"></span>
                                            </a>
											<ul class="dropdown-menu pull-left">
												<li> <a href="app-configuration.php" class="nav-link">App Configuration</a> </li>
												<li> <a href="all-app-configurations.php" class="nav-link">All App Configurations</a> </li>												
											</ul>
                                    </li>
                                   
									<li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="fa-exclamation-circle fa"></i>Setting
                                                <span class="arrow"></span>
                                            </a>
                                        <ul class="dropdown-menu pull-left">
											<li><a href="change-password-admin.php" class="nav-link">Change Password</a></li>
										</ul>
                                    </li>
                                </ul>
                            </div>
                            <!-- END MEGA MENU -->
                        </div>
                    </div>
                    <!-- END HEADER MENU -->
                </div>
                <!-- END HEADER -->
            </div>
        </div>