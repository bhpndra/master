<?php
	 session_start(); 
	 include("../config.php"); 
	 include("../include/lib.php"); 
	 include("inc/head.php"); 
	 include("inc/nav.php"); 
	 include("inc/sidebar.php");
 ?>
<?php
$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];
/* echo "<div style='float:right'>";
print_r($resVT['DATA']); 
echo "</div>"; */
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
       <!-- Small boxes (Stat box) -->
        <div class="row justify-content-center">
<?php
	$dateFilter = " and DATE(date_created) = CURDATE() ";
	$retailerIds = " and `user_id` in (SELECT id FROM `add_cust` where `wl_id` = '".$WL_ID."' and creator_id = '".$USER_ID."' GROUP BY  wl_id)";
	$dmtTotal = $mysqlClass->mysqlQuery("select sum(amount) as t from dmt_info where 1 and status = 'SUCCESS' ".$retailerIds.$dateFilter)->fetch(PDO::FETCH_ASSOC);
	$rechargeTotla = $mysqlClass->mysqlQuery("select sum(amount) as t from recharge_info where 1 and status = 'SUCCESS' ".$retailerIds.$dateFilter)->fetch(PDO::FETCH_ASSOC);
	$aepsTotal = $mysqlClass->mysqlQuery("select sum(amount) as t from aeps_info where 1 and status = 'SUCCESS' ".$retailerIds.$dateFilter)->fetch(PDO::FETCH_ASSOC);
?>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                <h3><?=$dmtTotal['t'] ? $dmtTotal['t'] : '0.00'?></h3>
                <p>Today's Total DMT</p>
              </div>
              <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?=$rechargeTotla['t'] ? $rechargeTotla['t'] : '0.00'?></h3>
                <p>Today's Total Recharge</p>
              </div>
              <div class="icon">
                <i class="fas fa-mobile-alt"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?=$aepsTotal['t'] ? $aepsTotal['t'] : '0.00'?></h3>
                <p>Today's Total AEPS</p>
              </div>
              <div class="icon">
                <i class="fas fa-fingerprint"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>0.00</h3>
                <p>Today's Total Bill Payment</p>
              </div>
              <div class="icon">
                <i class="fas fa-file-invoice"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-light">
              <div class="inner">
<?php
	$retailers = $mysqlClass->mysqlQuery("select count(id) as t from add_cust where usertype = 'RETAILER' and creator_id = '".$USER_ID."' ")->fetch(PDO::FETCH_ASSOC);
?>
                <h3><?=$retailers['t'] ? $retailers['t'] : '0.00'?></h3>

                <p>Retailers</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-light">
              <div class="inner">
<?php
	$retailersBal = $mysqlClass->mysqlQuery("select sum(wallet_balance) as t from add_cust where usertype = 'RETAILER' and creator_id = '".$USER_ID."' ")->fetch(PDO::FETCH_ASSOC);
?>
                <h3><?=$retailersBal['t'] ? $retailersBal['t'] : '0.00'?></h3>

                <p>Retailers Wallet</p>
              </div>
              <div class="icon">
                <i class="fas fa-rupee-sign"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
		
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>

</html>
