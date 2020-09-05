  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="<?=($siteDetails['logo'])? $siteDetails['logo'] : BASE_URL.'logo/logo.png'?>" alt="Logo" class="brand-image"
           style="background: rgba(255, 255, 255, 0.73);padding: 7px;">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <i class="fas fa-user-circle"></i> 
        </div>
        <div class="info">
          <a href="#" class="d-block"><?=$_SESSION['USER_NAME']?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
		  
		  <li class="nav-item">
            <a href="dashboard" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link ">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Report
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="ledger" class="nav-link ">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Ledger</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="recharge-report" class="nav-link">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Recharge Report</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="dmt-report" class="nav-link">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>DMT Report</p>
                </a>
              </li>
              <li class="nav-item nav-item has-treeview">
                <a href="aeps-report" class="nav-link">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>AEPS Report</p>
                </a>
              </li>
              <li class="nav-item nav-item has-treeview">
                <a href="bbps-report" class="nav-link">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>BBPS Report</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="aeps-wallet-withdrawal" class="nav-link">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>AEPS Wallet Settlement</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="credit-debit-report" class="nav-link">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Credit / Debit Report</p>
                </a>
              </li>
			  <li class="nav-item">
                <a href="pan_card_report_admin" class="nav-link">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Pan Card Report</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview">
			<a href="#" class="nav-link">
              <i class="nav-icon fas fa-user"></i>
              <p>
                Manage User
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="add-distributor" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Add Distributor
                </a>
              </li>
              <li class="nav-item">
                <a href="all-distributor" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>All Distributor
                </a>
              </li>
              <li class="nav-item">
                <a href="add-retailer" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Add Retailer
                </a>
              </li>
              <li class="nav-item">
                <a href="all-retailer" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>All Retailer
                </a>
              </li>
              <li class="nav-item">
                <a href="credit-to-user" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Credit to User
                </a>
              </li>
              <li class="nav-item">
                <a href="debit-from-user" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Debit From User
                </a>
              </li>
              <li class="nav-item">
                <a href="retailer-banks-details" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Retailer Bank Details
                </a>
              </li>
			 </ul>
          </li>		  
          <li class="nav-item has-treeview">
			<a href="#" class="nav-link">
              <i class="nav-icon fas fa-user"></i>
              <p>
                AEPS Outlets
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="aeps-outlet-1" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>AEPS Outlets 1
                </a>
              </li>
              <li class="nav-item">
                <a href="aeps-outlet-2" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>AEPS Outlets 2
                </a>
              </li>
			 </ul>
          </li>
          <li class="nav-item has-treeview">
			<a href="#" class="nav-link">
              <i class="nav-icon fas fa-list-alt"></i>
              <p>
                Request
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="fund-request" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Fund Request
                </a>
              </li>
              <li class="nav-item">
                <a href="online-wallet-refill-request" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>PG Payment Request
                </a>
              </li>
              <li class="nav-item">
                <a href="aeps-bank-withdrawal-request" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>AEPS Bank Withdrawal
                </a>
              </li>
              <li class="nav-item">
                <a href="new-user-request" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>New User Request
                </a>
              </li>
			 </ul>
          </li>
          <li class="nav-item has-treeview">
			<a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-edit"></i>
              <p>
                Manage Profile
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="profile" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Profile
                </a>
              </li>
              <li class="nav-item">
                <a href="add-virtual-fund" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Add Virtual Fund
                </a>
              </li>
              <li class="nav-item">
                <a href="add-fund" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Fund Request (Main Wallet)
                </a>
              </li>
              <li class="nav-item">
                <a href="aeps-settlement" onclick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=SomeSize,height=SomeSize'); return false;" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>AEPS Settlement
                </a>
              </li>
              <li class="nav-item">
                <a href="aeps-settlement-history" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>AEPS Settlement History
                </a>
              </li>
			 </ul>
          </li>
          <li class="nav-item has-treeview">
			<a href="#" class="nav-link">
              <i class="nav-icon fas fa-cogs"></i>
              <p>
                Settings
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="setting" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>General Setting
                </a>
              </li>
              <li class="nav-item">
                <a href="page-setting" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Page Setting
                </a>
              </li>
              <li class="nav-item">
                <a href="sms-setting" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>SMS Setting
                </a>
              </li>
              <li class="nav-item">
                <a href="add-new-package" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Add New Package
                </a>
              </li>
              <li class="nav-item">
                <a href="all-package" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>All Package
                </a>
              </li>
              <li class="nav-item">
                <a href="manage-aeps-dmt-slabs" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>DMT/AEPS Slab
                </a>
              </li>
              <li class="nav-item">
                <a href="manage-banks" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Manage Banks
                </a>
              </li>
              <li class="nav-item">
                <a href="payment-gateway-setup" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Payment Gateway
                </a>
              </li>
			 </ul>
          </li>
		  <li class="nav-item">
            <a href="../logout.php" class="nav-link">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>
                Logout
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
