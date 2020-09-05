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
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
<?php
	$retailers = $mysqlClass->mysqlQuery("select count(id) as t from add_cust where admin_id = '".$ADMIN_ID."' and usertype = 'RETAILER' and wl_id = '".$WL_ID."' ")->fetch(PDO::FETCH_ASSOC);
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
            <div class="small-box bg-success">
              <div class="inner">
<?php
	$retailersBal = $mysqlClass->mysqlQuery("select sum(wallet_balance) as t from add_cust where admin_id = '".$ADMIN_ID."' and usertype = 'RETAILER' and wl_id = '".$WL_ID."'  ")->fetch(PDO::FETCH_ASSOC);
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
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
<?php
	$distributor = $mysqlClass->mysqlQuery("select count(id) as t from add_cust where admin_id = '".$ADMIN_ID."' and usertype = 'DISTRIBUTOR' and wl_id = '".$WL_ID."'  ")->fetch(PDO::FETCH_ASSOC);
?>
                <h3><?=$distributor['t'] ? $distributor['t'] : '0.00'?></h3>

                <p>Distributor</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
<?php
	$distributorBal = $mysqlClass->mysqlQuery("select sum(wallet_balance) as t from add_cust where usertype = 'DISTRIBUTOR' and wl_id = '".$WL_ID."' and admin_id = '".$ADMIN_ID."'")->fetch(PDO::FETCH_ASSOC);
?>
                <h3><?=$distributorBal['t'] ? $distributorBal['t'] : '0.00'?></h3>

                <p>Distributors Wallet</p>
              </div>
              <div class="icon">
                <i class="fas fa-rupee-sign"></i>
              </div>
            </div>
          </div>
<?php
	$dateFilter = " and DATE(date_created) = CURDATE() ";
	$retailerIds = " and `user_id` in (SELECT id FROM `add_cust` where `wl_id` = '".$WL_ID."' GROUP BY  wl_id)";
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
        </div>
        <!-- /.row -->
		
		<div class="row">
			<div class="col-md-6">
				<div class="card">
				  <div class="card-header">
					<h3 class="card-title">Last 30days Success Transaction Chart</h3>

					<div class="card-tools">
					  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
					  </button>
					  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
					  </button>
					</div>
				  </div>
				  <!-- /.card-header -->
				  <div class="card-body">
					<div class="row">
					  <div class="col-md-8">
						<div class="chart-responsive"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
						  <canvas id="pieChart" height="99" style="display: block; width: 199px; height: 99px;" width="199" class="chartjs-render-monitor"></canvas>
						</div>
						<!-- ./chart-responsive -->
					  </div>
					  <!-- /.col -->
					  <div class="col-md-4">
						<ul class="chart-legend clearfix">
						  <li><i class="far fa-circle text-danger"></i> DMT</li>
						  <li><i class="far fa-circle text-success"></i> Recharge</li>
						  <li><i class="far fa-circle text-warning"></i> AEPS</li>
						  <li><i class="far fa-circle text-info"></i> Bill Payment</li>
						</ul>
					  </div>
					  <!-- /.col -->
					</div>
					<!-- /.row -->
				  </div>
				  <!-- /.card-body -->
				  <!-- /.footer -->
				</div>
			</div>
		</div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
<?php
	$date1 	  = new DateTime('30 days ago');
	$dateFrom = $date1->format('Y-m-d');
	$dateTo   = date("Y-m-d");
	$filter = " and (date(date_created) BETWEEN '$dateFrom' AND '$dateTo') ";
	$retailerIds = " and `user_id` in (SELECT id FROM `add_cust` where `wl_id` = '".$WL_ID."' GROUP BY  wl_id)";
	$dmtCount = $mysqlClass->mysqlQuery("select count(id) as t from dmt_info where 1 and status = 'SUCCESS' ".$retailerIds.$filter)->fetch(PDO::FETCH_ASSOC);
	$rechargeCount = $mysqlClass->mysqlQuery("select count(id) as t from recharge_info where 1 and status = 'SUCCESS' ".$retailerIds.$filter)->fetch(PDO::FETCH_ASSOC);
	$aepsCount = $mysqlClass->mysqlQuery("select count(id) as t from aeps_info where 1 and status = 'SUCCESS' ".$retailerIds.$filter)->fetch(PDO::FETCH_ASSOC);
?>
<script>
$(function () {
  //-------------
  //- PIE CHART -
  //-------------
  // Get context with jQuery - using jQuery's .get() method.
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
    var pieData        = {
      labels: [
          'DMT', 
          'Recharge',
          'AEPS', 
          'Bill Payment'
      ],
      datasets: [
        {
          data: [<?=$dmtCount['t']?>,<?=$rechargeCount['t']?>,0,0],
          backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef'],
        }
      ]
    }
    var pieOptions     = {
      legend: {
        display: false
      }
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    var pieChart = new Chart(pieChartCanvas, {
      type: 'doughnut',
      data: pieData,
      options: pieOptions      
    })

  //-----------------
  //- END PIE CHART -
  //-----------------
});
</script>
</html>
