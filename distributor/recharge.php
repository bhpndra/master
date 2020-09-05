<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>


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
	  <?php include("inc/service_box.php");?>

		<div class="row">
			<div class="col-md-6">

            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Recharge</h3>
              </div>
              <div class="card-body">
                <div class="form-group clearfix">
				  <div class="icheck-primary d-inline">
					<input type="radio" id="radioPrimary1" name="r1" value="prepaid" onclick="change_recharge(this.value)" checked="">
					<label for="radioPrimary1">
						Prepaid
					</label>
				  </div>
				  <div class="icheck-primary d-inline">
					<input type="radio" id="radioPrimary2"  value="postpaid" onclick="change_recharge(this.value)" name="r1">
					<label for="radioPrimary2">
						Postpaid
					</label>
				  </div>
				</div>
                <!-- /.form group -->
				<!-- Prepaid Form -->
				<div id="prepaid">
					<div class="form-group">
					  <label>Number:</label>

					  <div class="input-group">
						<div class="input-group-prepend">
						  <span class="input-group-text"><i class="fas fa-phone"></i></span>
						</div>
						<input type="text" class="form-control" />
					  </div>
					  <!-- /.input group -->
					</div>
					
					<div class="form-group">
					  <label>Amount:</label>

					  <div class="input-group">
						<div class="input-group-prepend">
						  <span class="input-group-text"><i class="fas fa-rupee-sign"></i></span>
						</div>
						<input type="text" class="form-control" />
					  </div>
					  <!-- /.input group -->
					</div>
					
					<div class="form-group">
					  <label>Operator:</label>
					  <select class="form-control select2" style="width: 100%;">
						<option selected="selected">Alabama</option>
						<option>Alaska</option>
						<option>California</option>
						<option>Delaware</option>
						<option>Tennessee</option>
						<option>Texas</option>
						<option>Washington</option>
					  </select>
					</div>
					
					<div class="form-group">
					  <label>Network Circle:</label>
					  <select class="form-control select2" style="width: 100%;">
						<option selected="selected">Alabama</option>
						<option>Alaska</option>
						<option>California</option>
						<option>Delaware</option>
						<option>Tennessee</option>
						<option>Texas</option>
						<option>Washington</option>
					  </select>
					</div>
				  
				  <div class="card-footer">
					<button type="submit" class="btn btn-primary">Prepaid</button>
				  </div>
				 </div>
			  
				<!-- Postpaid -->
				<div id="postpaid" style="display:none">
					<div class="form-group">
					  <label>Number:</label>

					  <div class="input-group">
						<div class="input-group-prepend">
						  <span class="input-group-text"><i class="fas fa-phone"></i></span>
						</div>
						<input type="text" class="form-control" />
					  </div>
					  <!-- /.input group -->
					</div>
					
					<div class="form-group">
					  <label>Amount:</label>

					  <div class="input-group">
						<div class="input-group-prepend">
						  <span class="input-group-text"><i class="fas fa-rupee-sign"></i></span>
						</div>
						<input type="text" class="form-control" />
					  </div>
					  <!-- /.input group -->
					</div>
					
					<div class="form-group">
					  <label>Operator:</label>
					  <select class="form-control select2" style="width: 100%;">
						<option selected="selected">Alabama</option>
						<option>Alaska</option>
						<option>California</option>
						<option>Delaware</option>
						<option>Tennessee</option>
						<option>Texas</option>
						<option>Washington</option>
					  </select>
					</div>
					
					<div class="form-group">
					  <label>Network Circle:</label>
					  <select class="form-control select2" style="width: 100%;">
						<option selected="selected">Alabama</option>
						<option>Alaska</option>
						<option>California</option>
						<option>Delaware</option>
						<option>Tennessee</option>
						<option>Texas</option>
						<option>Washington</option>
					  </select>
					</div>
				  
				  <div class="card-footer">
					<button type="submit" class="btn btn-primary">Postpaid</button>
				  </div>
				 </div>
			  
			  </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div>
			<div class="col-md-6">
				<div class="card card-success">
				  <div class="card-header border-transparent">
					<h3 class="card-title">Latest Recharge</h3>

					<div class="card-tools">
					  <button type="button" class="btn btn-tool" data-card-widget="collapse">
						<i class="fas fa-minus"></i>
					  </button>
					  <button type="button" class="btn btn-tool" data-card-widget="remove">
						<i class="fas fa-times"></i>
					  </button>
					</div>
				  </div>
				  <!-- /.card-header -->
				  <div class="card-body p-0">
					<div class="table-responsive">
					  <table class="table m-0">
						<thead>
						<tr>
						  <th>Order ID</th>
						  <th>Type</th>
						  <th>Status</th>
						  <th>Amount</th>
						</tr>
						</thead>
						<tbody>
						<tr>
						  <td><a href="#">OR9842</a></td>
						  <td>DMT</td>
						  <td><span class="badge badge-success">Success</span></td>
						  <td>
							<div class="sparkbar" data-color="#00a65a" data-height="20">5000</div>
						  </td>
						</tr>
						<tr>
						  <td><a href="#">OR1848</a></td>
						  <td>Recharge</td>
						  <td><span class="badge badge-warning">Pending</span></td>
						  <td>
							<div class="sparkbar" data-color="#f39c12" data-height="20">249</div>
						  </td>
						</tr>
						<tr>
						  <td><a href="#">OR7429</a></td>
						  <td>Recharge</td>
						  <td><span class="badge badge-danger">Failed</span></td>
						  <td>
							<div class="sparkbar" data-color="#f56954" data-height="20">399</div>
						  </td>
						</tr>
						<tr>
						  <td><a href="#">OR7429</a></td>
						  <td>Bill Payment</td>
						  <td><span class="badge badge-info">Processing</span></td>
						  <td>
							<div class="sparkbar" data-color="#00c0ef" data-height="20">800</div>
						  </td>
						</tr>
						<tr>
						  <td><a href="#">OR1848</a></td>
						  <td>Recharge</td>
						  <td><span class="badge badge-warning">Pending</span></td>
						  <td>
							<div class="sparkbar" data-color="#f39c12" data-height="20">49</div>
						  </td>
						</tr>
						<tr>
						  <td><a href="#">OR7429</a></td>
						  <td>DMT</td>
						  <td><span class="badge badge-danger">Failed</span></td>
						  <td>
							<div class="sparkbar" data-color="#f56954" data-height="20">1000</div>
						  </td>
						</tr>
						<tr>
						  <td><a href="#">OR9842</a></td>
						  <td>Recharge</td>
						  <td><span class="badge badge-success">Success</span></td>
						  <td>
							<div class="sparkbar" data-color="#00a65a" data-height="20">150</div>
						  </td>
						</tr>
						</tbody>
					  </table>
					</div>
					<!-- /.table-responsive -->
				  </div>
				  <!-- /.card-body -->
				  <div class="card-footer clearfix">
					<a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">View All Orders</a>
				  </div>
				  <!-- /.card-footer -->
				</div>
			</div>
			
		</div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
<?php include("inc/footer.php"); ?>
<script>
	function change_recharge(val){
		if(val=="prepaid"){
			$("#prepaid").css("display","block");
			$("#postpaid").css("display","none");
		}
		if(val=="postpaid"){
			$("#postpaid").css("display","block");
			$("#prepaid").css("display","none");
		}
	}
</script>
</html>
