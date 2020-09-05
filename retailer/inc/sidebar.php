  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="<?=($siteDetails['logo'])? $siteDetails['logo'] : BASE_URL.'logo/logo.png'?>" alt="Logo" class="brand-image"
           style="background: rgba(255, 255, 255, 0.73);padding: 7px;">
    </a>
<?php
	$services = explode(",",$_SESSION['SERVICES']);
?>
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
			<a href="#" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Services
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
			<?php if(in_array('aeps',$services)){ ?>
              <li class="nav-item">
                <a href="aeps" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>AEPS
                </a>
              </li>
			  <?php 
					}
				if(in_array('bbps',$services)){
			  ?>
              <li class="nav-item">
                <a href="bill-payments" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Bill Payment
                </a>
              </li>
			  <?php 
					}
				if(in_array('dmt',$services)){
			  ?>
              <li class="nav-item">
                <a href="money-transfer" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>DMT
                </a>
              </li>
			  <?php 
					}
				if(in_array('recharge',$services)){
			  ?>
              <li class="nav-item">
                <a href="recharge" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Recharge
                </a>
              </li>
			  <?php 
					}
			  ?>
              <li class="nav-item">
                <a href="bank-account-opening" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Online Account Open
                </a>
              </li>
              <li class="nav-item">
                <a target="_blank" href="insurance" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Insurance
                </a>
              </li>
			  <li class="nav-item">
                <a  href="add_pan_details" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Pan Card
                </a>
              </li>
			 </ul>
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
                <a href="dmt-report" class="nav-link">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>DMT Report</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="recharge-report" class="nav-link">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Recharge Report</p>
                </a>
              </li>
			  
			   <li class="nav-item">
                <a href="pan_card_report_retailer" class="nav-link">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Pan Card Report</p>
                </a>
              </li>
			  
              <li class="nav-item">
                <a href="online-fund-history" class="nav-link">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Online Wallet Refill</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview">
			<a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-edit"></i>
              <p>
                Account
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
                <a href="certificate" class="nav-link" target="_blank">
                  <i class="far fa-circle nav-icon text-warning"></i>Certificate
                </a>
              </li>
              <li class="nav-item">
                <a href="login-details" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Login Details
                </a>
              </li>
              <li class="nav-item">
                <a href="commission" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Commission Structure
                </a>
              </li>
			 </ul>
          </li>
          <li class="nav-item has-treeview">
			<a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-edit"></i>
              <p>
                Payments
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="online-wallet-refill" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Add Money
                </a>
              </li>
              <li class="nav-item">
                <a href="add-fund" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Add Fund Request
                </a>
              </li>
              <li class="nav-item">
                <a href="fund-request-history" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Fund Request History
                </a>
              </li>
			  <?php 
				if(in_array('payout',$services)){
			  ?>
			  <li class="nav-item">
                <a href="aeps-instant-settlement" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>Instant Settlement
                </a>
              </li>
				<?php } ?>
              <li class="nav-item">
                <a href="aeps-settlement-history" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i>AEPS Settlement History
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
