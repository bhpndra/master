<body class="page-container-bg-solid">
    <div class="page-wrapper">
        <div class="page-wrapper-row">
            <div class="page-wrapper-top">
                <div class="page-header">
                    <div class="page-header-top">
                        <div class="container">
                            <div class="page-logo">
								<a href="index.php">	
									<img src="http://localhost/netpaisa_new/logo/logo_1.png" alt="netpaisa.com" class="logo-default">
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
									<li class="menu-dropdown classic-menu-dropdown ">
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
                                    </li>
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
									<li class="menu-dropdown classic-menu-dropdown ">
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
                                    </li>
									<?php } ?>
									<li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="icon-credit-card icons"></i>Reports
                                                <span class="arrow"></span>
                                            </a>
											<ul class="dropdown-menu pull-left">
												<li> <a href="dmt-report.php" class="nav-link">DMT Transaction</a> </li>
												<li><a href="recharge-history.php" class="nav-link">Recharge Transaction</a></li>
												<!--<li> <a href="aeps-bank-withdrawl.php" class="nav-link">Bank Balance Withdrawl</a> </li>
												<li> <a href="aeps-wallet-withdrawl.php" class="nav-link">AESP Wallet Withdrawl</a> </li>
												<li> <a href="aeps-transaction-history.php" class="nav-link">AEPS Transaction</a> </li>-->
												
												<li> <a href="#" class="nav-link">AEPS Transaction</a> </li>
												<li> <a href="#" class="nav-link">AEPS Bank Withdrawl</a> </li>
												<li> <a href="#" class="nav-link">AESP Wallet Withdrawl</a> </li>
												
												<li><a href="fund-transfer-statement.php" class="nav-link">Fund Transfer Statement</a></li>
												<li><a href="fund-debit-statement.php" class="nav-link">Fund Debit Statement</a></li>
												<li><a href="bbps-transaction.php" class="nav-link">BBPS Transactions</a></li>
											</ul>
                                    </li>
                                    <li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="fa fa-exchange"></i>Transactions
                                                <span class="arrow"></span>
                                            </a>
                                        <ul class="dropdown-menu pull-left">
                                            <li><a href="white-label-transaction.php" class="nav-link">White label transactions</a></li>
                                            <li><a href="distributer-transaction.php" class="nav-link">Distributer transactions</a></li>
                                            <li><a href="retailer-transaction.php" class="nav-link">Retailer transactions</a></li>
                                            <li><a href="admin-transaction.php" class="nav-link">Admin transactions</a></li>
										</ul>
                                    </li>
									<!--<li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="fa-exclamation-circle fa"></i>AEPS
                                                <span class="arrow"></span>
                                            </a>
                                        <ul class="dropdown-menu pull-left">
                                            <li><a href="registered-yesbank-outlets.php" class="nav-link">Registered YesBank Outlets</a></li>
                                            <li><a href="registered-outlets-status.php" class="nav-link">Registered ICICI Outlets</a></li>
											<li><a href="outlet-registration.php" class="nav-link">ICICI AEPS Registration</a></li>
                                        </ul>
                                    </li>
									<li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="fa-exclamation-circle fa"></i>Payments
                                                <span class="arrow"></span>
                                            </a>
                                        <ul class="dropdown-menu pull-left">
                                            <li><a href="fund-request.php" class="nav-link">Fund Requests</a></li>
                                            <li><a href="fund-request-history.php" class="nav-link">Fund Requests History</a></li>
                                        </ul>
                                    </li>
									<li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="icon-user icons"></i>Employee
                                                <span class="arrow"></span>
                                            </a>
                                        <ul class="dropdown-menu pull-left">
                                            <li><a href="add-new-employee.php" class="nav-link">Add New Employee</a></li>
                                            <li><a href="employee-list.php" class="nav-link">All Employees</a></li>
                                            <li><a href="add-role.php" class="nav-link">Add Role</a></li>
                                            <li><a href="role-list.php" class="nav-link">All Roles</a></li>
                                        </ul>
                                    </li>
									<li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="fa-exclamation-circle fa"></i>Support
                                                <span class="arrow"></span>
                                            </a>
                                        <ul class="dropdown-menu pull-left">
                                            <li><a href="recharge-dispute-history.php" class="nav-link">Recharge Dispute</a></li>
                                            <li><a href="dmt-dispute-history.php" class="nav-link">DMT Dispute</a></li>
											<li><a href="tickets.php" class="nav-link">Tickets</a></li>
                                        </ul>
                                    </li>-->
									<li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="fa fa-question-circle"></i>Support
											<span class="arrow"></span>
										</a>
                                        <ul class="dropdown-menu pull-left">
											<li><a href="disputes.php" class="nav-link">Disputes</a></li>
										</ul>
                                    </li>
									<li class="menu-dropdown classic-menu-dropdown ">
                                        <a href="javascript:;"> <i class="fa-exclamation-circle fa"></i>Setting
											<span class="arrow"></span>
										</a>
                                        <ul class="dropdown-menu pull-left">
											<li><a href="add-fund.php" class="nav-link">Add Fund</a></li>
											<li><a href="sms-setting.php" class="nav-link">SMS Setting</a></li>
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