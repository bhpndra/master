<?php
include_once('inc/head.php');
include_once('inc/header.php');

$helpers = new helper_class();
$mysqlClass = new Mysql_class();
?>

<?php
$l_prepaid_operators = $mysqlClass->mysqlQuery("SELECT * FROM `network` WHERE operator_type='1' AND active_api !='' ");
$l_prepaid_count = $l_prepaid_operators->rowCount();

if ( $l_prepaid_count > 0 ) {
    while ($l_prepaid = $l_prepaid_operators->fetch(PDO::FETCH_ASSOC)) {
        
        $l_prepaid_operator_name[] = $l_prepaid['operator_name'];
        $l_prepaid_my_commission[] = $l_prepaid['my_commission'];

        if ( $l_prepaid['active_api'] == 'Q' ) {
            $l_prepaid_operator_code[] = $l_prepaid['la_operator_code'];
        } else {
            $l_prepaid_operator_code[] = $l_prepaid['rp_operator_code'];
        }
    }
}

$l_postpaid_operators = $mysqlClass->mysqlQuery("SELECT * FROM `network` WHERE operator_type='2' AND active_api !='' ");
$l_postpaid_count = $l_postpaid_operators->rowCount();

if ( $l_postpaid_count > 0 ) {
     while ($l_postpaid = $l_postpaid_operators->fetch(PDO::FETCH_ASSOC)) {
        
        $l_postpaid_operator_name[] = $l_postpaid['operator_name'];
        $l_postpaid_my_commission[] = $l_postpaid['my_commission'];

        if ( $l_postpaid['active_api'] == 'Q' ) {
            $l_postpaid_operator_code[] = $l_postpaid['la_operator_code'];
        } else {
            $l_postpaid_operator_code[] = $l_postpaid['rp_operator_code'];
        }
    }
}

// Instant Pay Prepaid
$insta_prepaid_operators = $mysqlClass->mysqlQuery("SELECT * FROM `instanetwork1` WHERE operator_type=1 ");
$insta_prepaid_count = $insta_prepaid_operators->rowCount();

if ( $insta_prepaid_count > 0 ) {
    while ($insta_prepaid = $insta_prepaid_operators->fetch(PDO::FETCH_ASSOC)) {
        
        $insta_prepaid_operator_name[] = $insta_prepaid['operator_name'];
        $insta_prepaid_my_commission[] = $insta_prepaid['my_commission'];
        $insta_prepaid_operator_code[] = $insta_prepaid['operator_code'];
    }
}

// Instant Pay DTH
$insta_dth_operators = $mysqlClass->mysqlQuery("SELECT * FROM `instanetwork1` WHERE operator_type=3 ");
$insta_dth_count = $insta_dth_operators->rowCount();

if ( $insta_dth_count > 0 ) {
	while ($insta_dth = $insta_dth_operators->fetch(PDO::FETCH_ASSOC)) {
        
        $insta_dth_operator_name[] = $insta_dth['operator_name'];
        $insta_dth_my_commission[] = $insta_dth['my_commission'];
        $insta_dth_operator_code[] = $insta_dth['operator_code'];
    }
}

// Instant Pay mPos
$insta_device_operators = $mysqlClass->mysqlQuery("SELECT * FROM `instanetwork1` WHERE operator_type=15 AND operator_code='MPS' ");
$insta_device_count = $insta_device_operators->rowCount();

if ( $insta_device_count > 0 ) {
	while ($insta_device = $insta_device_operators->fetch(PDO::FETCH_ASSOC)) {
        
        $insta_device_operator_name[] = $insta_device['operator_name'];
        $insta_device_my_commission[] = $insta_device['my_commission'];
        $insta_device_operator_code[] = $insta_device['operator_code'];
    }
}

// Instant Pay TRAVEL
$insta_travels_operators = $mysqlClass->mysqlQuery("SELECT * FROM `instanetwork1` WHERE operator_type='TRAVEL' AND operator_code='BUS' ");
$insta_travels_count = $insta_travels_operators->rowCount();

if ( $insta_travels_count > 0 ) {
	while ($insta_travels = $insta_travels_operators->fetch(PDO::FETCH_ASSOC)) {
        
        $insta_travels_operator_name[] = $insta_travels['operator_name'];
        $insta_travels_my_commission[] = $insta_travels['my_commission'];
        $insta_travels_operator_code[] = $insta_travels['operator_code'];
    }
}

// Instant Pay BroadBand
$insta_broadband_operators = $mysqlClass->mysqlQuery("SELECT * FROM `instanetwork1` WHERE operator_type=13 ");
$insta_broadband_count = $insta_broadband_operators->rowCount();

if ( $insta_broadband_count > 0 ) {
	while ($insta_broadband = $insta_broadband_operators->fetch(PDO::FETCH_ASSOC)) {
        
        $insta_broadband_operator_name[] = $insta_broadband['operator_name'];
        $insta_broadband_my_commission[] = $insta_broadband['my_commission'];
        $insta_broadband_operator_code[] = $insta_broadband['operator_code'];
    }
}

$package_keys_values = array ();

if ( isset($_POST['package-edit']) AND $_POST['package_id'] != '' ) {


    $pack_commission = $mysqlClass->mysqlQuery("SELECT `package_list`.`id`,`package_list`.`package_name`,`package_list`.`creator_id`,`package_commission_new`.`pid`,`package_commission_new`.`network`,`package_commission_new`.`my_commission`,`package_commission_new`.`my_profit`,`package_commission_new`.`wl_profit`,`package_commission_new`.`master_dist_profit`,`package_commission_new`.`dist_profit`,`package_commission_new`.`retailer_profit` FROM `package_list` INNER JOIN `package_commission_new` ON `package_list`.`id` = `package_commission_new`.`pid` WHERE `package_list`.`creator_id`='" . $_SESSION[_session_userid_] . "' && `package_commission_new`.`pid`='" . $_POST['package_id'] . "' ORDER BY `package_commission_new`.`id` ASC");

    if ( $pack_commission->rowCount() > 0 ) {
        while ( $pack_com = $pack_commission->fetch(PDO::FETCH_ASSOC) ) {
            $pids[]             = $pack_com['pid'];
            $network[]          = $pack_com['network'];
            $my_commission[]    = $pack_com['my_commission'];
            $my_profit[]        = $pack_com['my_profit'];
            $wl_profit[]        = $pack_com['wl_profit'];
            $master_dist_profit = $pack_com['master_dist_profit'];
            $dist_profit        = $pack_com['dist_profit'];
            $retailer_profit    = $pack_com['retailer_profit'];

            $package_keys_values[] = $pack_com;
        }
    }

    $pack_name = $mysqlClass->mysqlQuery("SELECT `package_name` FROM `package_list` WHERE `id`='" . $_POST['package_id'] . "'")->fetch(PDO::FETCH_ASSOC);

}
$pack_name = isset( $pack_name['package_name'] ) ? $pack_name['package_name'] : '';
$k = 0;
?>

<!-- MAIN PANEL -->
<div class="page-wrapper-row full-height">
	<div class="page-wrapper-middle">
		<!-- BEGIN CONTAINER -->
		<div class="page-container">
			<!-- BEGIN CONTENT -->
			<div class="page-content-wrapper">
				<!-- BEGIN CONTENT BODY -->
				<!-- BEGIN PAGE HEAD-->
				
				<!-- END PAGE HEAD-->
				<!-- BEGIN PAGE CONTENT BODY -->
				<div class="page-content">
					<div class="container">
						<!-- BEGIN PAGE BREADCRUMBS -->
						<ul class="page-breadcrumb breadcrumb">
							<li>
								<a href="index.html">Home</a>
								<i class="fa fa-circle"></i>
							</li>
							<li>
								<span>Add Api Retailer</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
							<header>
									Commission Packages
							</header>
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12">
								<?php
									if(@$msg!=""){
										echo $msg;
									}
								
								?>
								<div class="well" style="min-height:100px;" >															
                                <div class="col-md-12 ">
									<form method ="post" class="smart-form" action ="" enctype="multipart/form-data">
										<div class="form-group col-md-2 pull-right">
											<div>
											
                                                 <input type="hidden" name="package_new" id="package_new" value="new" />
												<button name="create_new_package" class="btn btn-primary" >Add New Package</button>
											</div>
										</div>
									</form>
								</div>
                                <div class="col-md-12 ">
									<form method ="post" class="smart-form" action ="" enctype="multipart/form-data">
										
										<div class="form-group col-md-6 col-md-offset-2">
											<label>Package:*</label>
											<div>
											<?php
												$mysQuery = $mysqlClass->mysqlQuery("
												select * from package_list WHERE created_by='ADMIN' && `creator_id`='" . $_SESSION[_session_userid_] . "'
												");
											?>
												<select  class='form-control' name="package_id" required>
													<option value="" selected disabled>API user</option>
												<?php
													$num = $mysQuery->rowCount();
													if($num>=0){
														while ($row = $mysQuery->fetch(PDO::FETCH_ASSOC)){
												?>
															<option value="<?php echo $row['id']; ?>" <?php if (@$_POST['package_id'] == $row['id']) {
                                                                    echo "selected";
                                                                } ?>><?php echo $row['package_name']; ?></option>
												<?php
														}
													}
												?>
												</select>
											</div>
										</div>
										<div class="form-group col-md-2">
											<label style="opacity:0">Button</label>
											<div>
												<input type="submit" name="package-edit" class="btn btn-success" value="Edit Package">
											</div>
										</div>

									</form>
								</div>
								</div>
<?php if ( ( isset($_POST['package-edit']) AND $_POST['package_id'] != '' ) OR ( isset($_POST['create_new_package']) && $_POST['package_new'] == 'new' ) ){ ?>								
								<hr class="hr-style"/>
								<div class="well" style="min-height:300px;" >	                                                               
									<form class="form-horizontal" id="commissionForm" action="api_commission_submit.php" method="post" >
											<div class="row" style="margin-left:0;padding:10px;">
												<div class="col-md-12">
													<div class="form-group">
														<label class="control-label col-lg-2 col-md-offset-2">Package Name : <span class="text-danger">*</span></label>
														<div class="col-lg-4">
															<input id="name" name="name" maxlength="60"  value="<?php echo $pack_name; ?>" placeholder="Enter Package Name" type="text" class="form-control" required>
														</div>
													</div>
												</div>
											</div>
											<div class="row">					
												<div class="col-md-12">
													<div class="table-responsive">
														<table class="table border-info-700 table-framed table-xxs">
															
															<tr>
																<th width="21%">Network</th>
																<th width="13%">My Comm [%]</th>
																<th width="13%">My Profit [%]</th>
																<th width="13%">WL Profit [%]</th>
																<th width="13%">M Dist Profit [%]</th>
																<th width="13%">Dist Profit [%]</th>
																<th width="13%">Retail Profit [%]</th>
															</tr>
														
															<tbody>

																<tr class="alpha-primary">
																	<td colspan="7" style="background-color:#9ebce0;color:darkgreen;">LP Prepaid Commission</td>
																</tr>

																<?php
																   for ( $i = 0; $i < $l_prepaid_count; $i++ ) {
																?>
																		<tr class="alpha-primary">
																			<td><?php echo $l_prepaid_operator_name[$i]; ?>
																				<input type="hidden" name="l_prepaid_operator_code[]" value="<?php echo $l_prepaid_operator_code[$i]; ?>">
																			</td>
																			<td>
																				<input type="text" size="10" style="border: 0; background: transparent;" name="l_prepaid_my_commission[]" id="<?php echo 'l_prepaid_my_commission_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : $l_prepaid_my_commission[$i]; ?>" readonly="readonly" />
																			</td>
																			<td><input type="text" style="width:50px;" name="l_prepaid_my_profit[]" id="<?php echo 'l_prepaid_my_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="l_prepaid_wl_profit[]" id="<?php echo 'l_prepaid_wl_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="l_prepaid_master_dist_profit[]" id="<?php echo 'l_prepaid_master_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="l_prepaid_dist_profit[]" id="<?php echo 'l_prepaid_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>"></td>	
																			<td class="client_com_an">
																				<input type="text" size="10" style="width:50px;" name="l_prepaid_retailer_profit[]" id="<?php echo 'l_prepaid_retailer_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : $l_prepaid_my_commission[$i]; ?>"  />
																			</td>
																		</tr>
																<?php
																		$k++;
																	}
																?>

																<tr class="alpha-primary">
																	<td colspan="7" style="background-color:#9ebce0;color:darkgreen;">Postpaid Commission</td>
																</tr>
																
																<?php
																   for ( $i = 0; $i < $l_postpaid_count; $i++ ) {
																?>
																		<tr class="alpha-primary">
																			<td><?php echo $l_postpaid_operator_name[$i]; ?> <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : $l_postpaid_operator_code[$i]; ?> <input type="hidden" name="l_postpaid_operator_code[]" value="<?php echo $l_postpaid_operator_code[$i]; ?>"></td>
																			<td><input type="text" size="10" style="border: 0; background: transparent;" name="l_postpaid_my_commission[]" id="<?php echo 'l_postpaid_my_commission_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : $l_postpaid_my_commission[$i]; ?>" readonly="readonly" /></td>
																			<td><input type="text" style="width:50px;" name="l_postpaid_my_profit[]" id="<?php echo 'l_postpaid_my_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="l_postpaid_wl_profit[]" id="<?php echo 'l_postpaid_wl_profit_'.$i; ?>"  value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="l_postpaid_master_dist_profit[]" id="<?php echo 'l_postpaid_master_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="l_postpaid_dist_profit[]" id="<?php echo 'l_postpaid_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>"></td>	
																			<td class="client_com_an">
																				<input type="text" size="10" style="width:50px;" name="l_postpaid_retailer_profit[]" id="<?php echo 'l_postpaid_retailer_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : $l_postpaid_my_commission[$i]; ?>"  />
																			</td>
																		</tr>
																<?php
																		$k++;
																	}
																?>

															</tbody>						
														</table>					
													</div>
												</div>

												<div class="col-md-12">
													<div class="table-responsive">
														<table class="table border-info-700 table-framed table-xxs">
															
															<tr class="alpha-primary">
																<td colspan="7" style="background-color:#ca6d2b; color:white; text-align:center; font-weight:bold;">INSTANT RECHARGE</td>
															</tr>
														   
															<tr>
																<th width="21%">Network</th>
																<th width="13%">My Comm [%]</th>
																<th width="13%">My Profit [%]</th>
																<th width="13%">WL Profit [%]</th>
																<th width="13%">M Dist Profit [%]</th>
																<th width="13%">Dist Profit [%]</th>
																<th width="13%">Retail Profit [%]</th>
															</tr>
														
															<tbody>

																<tr class="alpha-primary">
																	<td colspan="7" style="background-color:#9ebce0;color:darkgreen;">Prepaid Commission</td>
																</tr>

																<?php
																   for ( $i = 0; $i < $insta_prepaid_count; $i++ ) {
																?>
																		<tr class="alpha-primary">
																			<td><?php echo $insta_prepaid_operator_name[$i]; ?> <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : $insta_prepaid_operator_code[$i]; ?> <input type="hidden" name="insta_prepaid_operator_code[]" value="<?php echo $insta_prepaid_operator_code[$i]; ?>"></td>
																			<td><input type="text" size="10" style="border: 0; background: transparent;" name="insta_prepaid_my_commission[]" id="<?php echo 'insta_prepaid_my_commission_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : $insta_prepaid_my_commission[$i]; ?>" readonly="readonly" /></td>
																			<td><input type="text" style="width:50px;" name="insta_prepaid_my_profit[]" id="<?php echo 'insta_prepaid_my_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_prepaid_wl_profit[]" id="<?php echo 'insta_prepaid_wl_profit_'.$i; ?>"  value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_prepaid_master_dist_profit[]" id="<?php echo 'insta_prepaid_master_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_prepaid_dist_profit[]" id="<?php echo 'insta_prepaid_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>"></td>	
																			<td class="client_com_an">
																				<input type="text" size="10" style="width:50px;" name="insta_prepaid_retailer_profit[]" id="<?php echo 'insta_prepaid_retailer_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : $insta_prepaid_my_commission[$i]; ?>" />
																			</td>
																		</tr>
																<?php
																		$k++;
																	}
																?>

																<tr class="alpha-primary">
																	<td colspan="7" style="background-color:#9ebce0;color:darkgreen;">DTH Commission</td>
																</tr>
																
																<?php
																   for ( $i = 0; $i < $insta_dth_count; $i++ ) {
																?>
																		<tr class="alpha-primary">
																			<td><?php echo $insta_dth_operator_name[$i]; ?> <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : $insta_dth_operator_code[$i]; ?> <input type="hidden" name="insta_dth_operator_code[]" value="<?php echo $insta_dth_operator_code[$i]; ?>"></td>
																			<td><input type="text" size="10" style="border: 0; background: transparent;" name="insta_dth_my_commission[]" id="<?php echo 'insta_dth_my_commission_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : $insta_dth_my_commission[$i]; ?>" readonly="readonly" /></td>
																			<td><input type="text" style="width:50px;" name="insta_dth_my_profit[]" id="<?php echo 'insta_dth_my_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_dth_wl_profit[]" id="<?php echo 'insta_dth_wl_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_dth_master_dist_profit[]" id="<?php echo 'insta_dth_master_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_dth_dist_profit[]" id="<?php echo 'insta_dth_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>"></td>	
																			<td class="client_com_an">
																				<input type="text" size="10" style="width:50px;" name="insta_dth_retailer_profit[]" id="<?php echo 'insta_dth_retailer_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : $insta_dth_my_commission[$i]; ?>" />
																			</td>
																		</tr>
																<?php
																		$k++;
																	}
																?>

																<tr class="alpha-primary">
																	<td colspan="7" style="background-color:#9ebce0;color:darkgreen;">Device Commission</td>
																</tr>
																
																<?php
																   for ( $i = 0; $i < $insta_device_count; $i++ ) {
																?>
																		<tr class="alpha-primary">
																			<td><?php echo $insta_device_operator_name[$i]; ?> <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : $insta_device_operator_code[$i]; ?> <input type="hidden" name="insta_device_operator_code[]" value="<?php echo $insta_device_operator_code[$i]; ?>"></td>
																			<td><input type="text" size="10" style="border: 0; background: transparent;" name="insta_device_my_commission[]" id="<?php echo 'insta_device_my_commission_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : $insta_device_my_commission[$i]; ?>" readonly="readonly" /></td>
																			<td><input type="text" style="width:50px;" name="insta_device_my_profit[]" id="<?php echo 'insta_device_my_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_device_wl_profit[]" id="<?php echo 'insta_device_wl_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_device_master_dist_profit[]" id="<?php echo 'insta_device_master_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_device_dist_profit[]" id="<?php echo 'insta_device_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>"></td>	
																			<td class="client_com_an">
																				<input type="text" size="10" style="width:50px;" name="insta_device_retailer_profit[]" id="<?php echo 'insta_device_retailer_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : $insta_device_my_commission[$i]; ?>"  />
																			</td>
																		</tr>
																<?php
																	   $k++;
																	}
																?>

																<tr class="alpha-primary">
																	<td colspan="7" style="background-color:#9ebce0;color:darkgreen;">TRAVELS Commission</td>
																</tr>
																
																<?php
																   for ( $i = 0; $i < $insta_travels_count; $i++ ) {
																?>
																		<tr class="alpha-primary">
																			<td><?php echo $insta_travels_operator_name[$i]; ?> <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : $insta_travels_operator_code[$i]; ?> <input type="hidden" name="insta_travels_operator_code[]" value="<?php echo $insta_travels_operator_code[$i]; ?>"></td>
																			<td><input type="text" size="10" style="border: 0; background: transparent;" name="insta_travels_my_commission[]" id="<?php echo 'insta_travels_my_commission_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : $insta_travels_my_commission[$i]; ?>" readonly="readonly" /></td>
																			<td><input type="text" style="width:50px;" name="insta_travels_my_profit[]" id="<?php echo 'insta_travels_my_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_travels_wl_profit[]" id="<?php echo 'insta_travels_wl_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_travels_master_dist_profit[]" id="<?php echo 'insta_travels_master_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_travels_dist_profit[]" id="<?php echo 'insta_travels_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>"></td>	
																			<td class="client_com_an">
																				<input type="text" size="10" style="width:50px;" name="insta_travels_retailer_profit[]" id="<?php echo 'insta_travels_retailer_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : $insta_travels_my_commission[$i]; ?>"  />
																			</td>
																		</tr>
																<?php
																	   $k++;
																	}
																?>
																
																<tr class="alpha-primary">
																	<td colspan="7" style="background-color:#9ebce0;color:darkgreen;">BroadBand Commission</td>
																</tr>
																
																<?php
																   for ( $i = 0; $i < $insta_broadband_count; $i++ ) {
																?>
																		<tr class="alpha-primary">
																			<td><?php echo $insta_broadband_operator_name[$i]; ?> <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : $insta_broadband_operator_code[$i]; ?> <input type="hidden" name="insta_broadband_operator_code[]" value="<?php echo $insta_broadband_operator_code[$i]; ?>"></td>
																			<td><input type="text" size="10" style="border: 0; background: transparent;" name="insta_broadband_my_commission[]" id="<?php echo 'insta_broadband_my_commission_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : $insta_broadband_my_commission[$i]; ?>" readonly="readonly" /></td>
																			<td><input type="text" style="width:50px;" name="insta_broadband_my_profit[]" id="<?php echo 'insta_broadband_my_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_broadband_wl_profit[]" id="<?php echo 'insta_broadband_wl_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_broadband_master_dist_profit[]" id="<?php echo 'insta_broadband_master_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>"></td>
																			<td><input type="text" style="width:50px;" name="insta_broadband_dist_profit[]" id="<?php echo 'insta_broadband_dist_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>"></td>	
																			<td class="client_com_an">
																				<input type="text" size="10" style="width:50px;" name="insta_broadband_retailer_profit[]" id="<?php echo 'insta_broadband_retailer_profit_'.$i; ?>" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : $insta_broadband_my_commission[$i]; ?>"  />
																			</td>
																		</tr>
																<?php
																		$k++;
																	}
																?>

															</tbody>						
														</table>					
													</div>
												</div>
												
												 <div class="col-md-12">
													<div class="table-responsive">
														<table class="table border-info-700 table-framed table-xxs">
															
															<tr class="alpha-primary">
																<td colspan="7" style="background-color:#ca6d2b; color:white; text-align:center; font-weight:bold;">AEPS Commission</td>
															</tr>
															
															<tr>
																<th width="21%">Network</th>
																<th width="13%">My Comm [%]</th>
																<th width="13%">My Profit [%]</th>
																<th width="13%">WL Profit [%]</th>
																<th width="13%">M Dist Profit [%]</th>
																<th width="13%">Dist Profit [%]</th>
																<th width="13%">Retail Profit [%]</th>
															</tr>
														
															<tbody>

															   <tr class="alpha-primary">
																	<td>AEPS (500-999) <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : 'ELECTR'; ?> <input type="hidden" name="AEPSCOMM[]" value="AEPS1" > <!-- <font color="red"> [Surcharge ] </font> --> </td>
																	<td><input type="text" size="10" style="border: 0; background: transparent;" name="aeps_500_999_my_commission" id="aeps_500_999_my_commission" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : '1'; ?>" readonly="readonly"  /></td>
																	<td><input type="text" size="10" name="aeps_500_999_my_profit" id="aeps_500_999_my_profit" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>" style="width:50px;"/></td>
																	<td><input type="text" size="10" name="aeps_500_999_wl_profit" id="aeps_500_999_wl_profit" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>" style="width:50px;"/></td>
																	<td><input type="text" size="10" name="aeps_500_999_master_dist_profit" id="aeps_500_999_master_dist_profit" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>" style="width:50px;"/></td>
																	<td><input type="text" size="10" name="aeps_500_999_dist_profit" id="aeps_500_999_dist_profit" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>" style="width:50px;"/></td>
																	<td>
																		<input type="text" size="10" style="width:50px;"  name="aeps_500_999_retailer_profit" id="aeps_500_999_retailer_profit" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : '0'; ?>"   /> 
																	</td>
																</tr>

																<?php $k++; ?>
																
																<tr>
																	<td> AEPS(1000 - 1499)<?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : 'GAS'; ?> <input type="hidden" name="AEPSCOMM[]" value="AEPS2"> <br/><!-- <font color="red"> [ Surcharge ] </font> --> </td>
																	<td><input type="text" size="10" style="border: 0; background: transparent;"  name="aeps_1000_1499_my_commission" id="aeps_1000_1499_my_commission" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : '1'; ?>" readonly="readonly"  /></td>
																	<td><input type="text" size="10" name="aeps_1000_1499_my_profit" style="width:50px;" id="aeps_1000_1499_my_profit" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" name="aeps_1000_1499_wl_profit" style="width:50px;" id="aeps_1000_1499_wl_profit" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" name="aeps_1000_1499_master_dist_profit" style="width:50px;" id="aeps_1000_1499_master_dist_profit" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" name="aeps_1000_1499_dist_profit" style="width:50px;" id="aeps_1000_1499_dist_profit" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" style="width:50px;"  name="aeps_1000_1499_retailer_profit" id="aeps_1000_1499_retailer_profit" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : '0'; ?>"  /> 
																	</td>
																</tr>

																<?php $k++; ?>

																<tr>
																	<td> AEPS(1500 - 1999) <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : 'INSUR'; ?> <input type="hidden" name="AEPSCOMM[]" value="AEPS3"> <!-- <font color="red"> [ Surcharge ] </font> --> </td>
																	<td><input type="text" size="10" style="border: 0; background: transparent;"  name="aeps_1500_1999_my_commission" id="aeps_1500_1999_my_commission" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : '1'; ?>" readonly="readonly"  /></td>
																	<td><input type="text" size="10" name="aeps_1500_1999_my_profit" id="aeps_1500_1999_my_profit" style="width:50px;" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" name="aeps_1500_1999_wl_profit" id="aeps_1500_1999_wl_profit" style="width:50px;" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" name="aeps_1500_1999_master_dist_profit" id="aeps_1500_1999_master_dist_profit" style="width:50px;" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" name="aeps_1500_1999_dist_profit" id="aeps_1500_1999_dist_profit" style="width:50px;" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" style="width:50px;"  name="aeps_1500_1999_retailer_profit" id="aeps_1500_1999_retailer_profit" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : '0'; ?>"  /> 
																	</td>
																</tr>

																<?php $k++; ?>
																  
																<tr>
																	<td>AEPS(2000 - 2999) <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : 'dmt1'; ?> <input type="hidden" name="AEPSCOMM[]" value="AEPS4"><strong>Rs.</strong> <!-- <font color="red"> [Surcharge ] </font> --> </td>
																	<td><input type="text" size="10" style="border: 0; background: transparent;" name="aeps_2000_2999_my_commission" id="aeps_2000_2999_my_commission" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : '1.80'; ?>" readonly="readonly"  /></td>
																	<td><input type="text" style="width:50px;" name="aeps_2000_2999_my_profit" id="aeps_2000_2999_my_profit" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="aeps_2000_2999_wl_profit" id="aeps_2000_2999_wl_profit" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="aeps_2000_2999_master_dist_profit" id="aeps_2000_2999_master_dist_profit" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="aeps_2000_2999_dist_profit" id="aeps_2000_2999_dist_profit" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>"></td>
																	<td>
																		<input type="text" size="10" style="width:50px;" name="aeps_2000_2999_retailer_profit" id="aeps_2000_2999_retailer_profit" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : '1.80'; ?>"   /> 
																	</td>
																</tr>

																<?php $k++; ?>

																<tr>
																	<td>AEPS(3000 - Above) <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : 'dmt2'; ?> <input type="hidden" name="AEPSCOMM[]" value="AEPS5"> <strong>Rs.</strong><!-- <font color="red"> [ Surcharge ] </font> --> </td>
																	<td><input type="text" size="10" style="border: 0; background: transparent;"  name="aeps_3000_above_my_commission" id="aeps_3000_above_my_commission" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : '4.5'; ?>" readonly="readonly"  /></td>
																	<td><input type="text" style="width:50px;" name="aeps_3000_above_my_profit" id="aeps_3000_above_my_profit" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="aeps_3000_above_wl_profit" id="aeps_3000_above_wl_profit" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="aeps_3000_above_master_dist_profit" id="aeps_3000_above_master_dist_profit" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="aeps_3000_above_dist_profit" id="aeps_3000_above_dist_profit" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>"></td>
																	<td><input type="text" size="10" style="width:50px;" name="aeps_3000_above_retailer_profit" id="aeps_3000_above_retailer_profit" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : '4.5'; ?>"   /></td>
																</tr>

																<?php $k++; ?>
																
															</tbody>						
														</table>					
													</div>
												</div>
												

												
												
												<div class="col-md-12">
													<div class="table-responsive">
														<table class="table border-info-700 table-framed table-xxs">
															
															<tr class="alpha-primary">
																<td colspan="7" style="background-color:#ca6d2b; color:white; text-align:center; font-weight:bold;">Surcharge</td>
															</tr>
															
															<tr>
																<th width="21%">Network</th>
																<th width="13%">My Comm [%]</th>
																<th width="13%">My Profit [%]</th>
																<th width="13%">WL Profit [%]</th>
																<th width="13%">M Dist Profit [%]</th>
																<th width="13%">Dist Profit [%]</th>
																<th width="13%">Retail Profit [%]</th>
															</tr>
														
															<tbody>

															   <tr class="alpha-primary">
																	<td>Electricity <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : 'ELECTR'; ?> <input type="hidden" name="surcharge[]" value="ELECTR"> <font color="red"> [Surcharge ] </font> </td>
																	<td><input type="text" size="10" style="border: 0; background: transparent;" name="electricity_my_commission" id="electricity_my_commission" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : '1'; ?>" readonly="readonly"  /></td>
																	<td><input type="text" size="10" name="electricity_my_profit" id="electricity_my_profit" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>" style="width:50px;"/></td>
																	<td><input type="text" size="10" name="electricity_wl_profit" id="electricity_wl_profit" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>" style="width:50px;"/></td>
																	<td><input type="text" size="10" name="electricity_master_dist_profit" id="electricity_master_dist_profit" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>" style="width:50px;"/></td>
																	<td><input type="text" size="10" name="electricity_dist_profit" id="electricity_dist_profit" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>" style="width:50px;"/></td>
																	<td>
																		<input type="text" size="10" style="width:50px;"  name="electricity_retailer_profit" id="electricity_retailer_profit" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : '0'; ?>" /> 
																	</td>
																</tr>

																<?php $k++; ?>
																
																<!--gas-->
																<tr>
																	<td> Gas <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : 'GAS'; ?> <input type="hidden" name="surcharge[]" value="GAS"> <br/><font color="red"> [ Surcharge ] </font> </td>
																	<td><input type="text" size="10" style="border: 0; background: transparent;"  name="gas_my_commission" id="gas_my_commission" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : '1'; ?>" readonly="readonly" /></td>
																	<td><input type="text" size="10" name="gas_my_profit" style="width:50px;" id="gas_my_profit" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" name="gas_wl_profit" style="width:50px;" id="gas_wl_profit" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" name="gas_master_dist_profit" style="width:50px;" id="gas_master_dist_profit" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" name="gas_dist_profit" style="width:50px;" id="gas_dist_profit" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" style="width:50px;"  name="gas_retailer_profit" id="gas_retailer_profit" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : '0'; ?>" /> 
																	</td>
																</tr>

																<?php $k++; ?>

																<!--insurance-->
																<tr>
																	<td> Insurance <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : 'INSUR'; ?> <input type="hidden" name="surcharge[]" value="INSUR"> <font color="red"> [ Surcharge ] </font> </td>
																	<td><input type="text" size="10" style="border: 0; background: transparent;"  name="insurance_my_commission" id="insurance_my_commission" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : '1'; ?>" readonly="readonly"  /></td>
																	<td><input type="text" size="10" name="insurance_my_profit" id="insurance_my_profit" style="width:50px;" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" name="insurance_wl_profit" id="insurance_wl_profit" style="width:50px;" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" name="insurance_master_dist_profit" id="insurance_master_dist_profit" style="width:50px;" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" name="insurance_dist_profit" id="insurance_dist_profit" style="width:50px;" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>" /></td>
																	<td><input type="text" size="10" style="width:50px;"  name="insurance_retailer_profit" id="insurance_retailer_profit" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : '0'; ?>" /> 
																	</td>
																</tr>

																<?php $k++; ?>
																  
																<!-- Account verification-->
																<tr>
																	<td>dmt(Account Verification) <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : 'dmt1'; ?> <input type="hidden" name="surcharge[]" value="dmt1"><strong>Rs.</strong> <font color="red"> [Surcharge ] </font> </td>
																	<td><input type="text" size="10" style="border: 0; background: transparent;" name="dmt_av_my_commission" id="dmt_av_my_commission" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : '1.80'; ?>" readonly="readonly"  /></td>
																	<td><input type="text" style="width:50px;" name="dmt_av_my_profit" id="dmt_av_my_profit" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="dmt_av_wl_profit" id="dmt_av_wl_profit" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="dmt_av_master_dist_profit" id="dmt_av_master_dist_profit" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="dmt_av_dist_profit" id="dmt_av_dist_profit" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>"></td>
																	<td>
																		<input type="text" size="10" style="width:50px;" name="dmt_av_retailer_profit" id="dmt_av_retailer_profit" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : '1.80'; ?>"  /> 
																	</td>
																</tr>

																<?php $k++; ?>

																<tr>
																	<td>dmt(10 - 2000) <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : 'dmt2'; ?> <input type="hidden" name="surcharge[]" value="dmt2"> <strong>Rs.</strong><font color="red"> [ Surcharge ] </font> </td>
																	<td><input type="text" size="10" style="border: 0; background: transparent;"  name="dmt_1_my_commission" id="dmt_1_my_commission" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : '4.5'; ?>" readonly="readonly" /></td>
																	<td><input type="text" style="width:50px;" name="dmt_1_my_profit" id="dmt_1_my_profit" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="dmt_1_wl_profit" id="dmt_1_wl_profit" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="dmt_1_master_dist_profit" id="dmt_1_master_dist_profit" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="dmt_1_dist_profit" id="dmt_1_dist_profit" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>"></td>
																	<td><input type="text" size="10" style="width:50px;" name="dmt_1_retailer_profit" id="dmt_1_retailer_profit" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : '4.5'; ?>"   /></td>
																</tr>

																<?php $k++; ?>

																<tr class="alpha-primary">
																	<td>dmt(2001 - 5000)  <?php //echo ( $package_keys_values[$k]['network'] ) ? $package_keys_values[$k]['network'] : 'dmt3'; ?> <input type="hidden" name="surcharge[]" value="dmt3"><font color="red">% [ Surcharge ] </font> </td>
																	<td><input type="text" size="10" style="border: 0; background: transparent;"  name="dmt_2_my_commission" id="dmt_2_my_commission" value="<?php echo ( $package_keys_values[$k]['my_commission'] ) ? $package_keys_values[$k]['my_commission'] : '0.15'; ?>" readonly="readonly" /></td>
																	<td><input type="text" style="width:50px;" name="dmt_2_my_profit" id="dmt_2_my_profit" value="<?php echo ( $package_keys_values[$k]['my_profit'] ) ? $package_keys_values[$k]['my_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="dmt_2_wl_profit" id="dmt_2_wl_profit" value="<?php echo ( $package_keys_values[$k]['wl_profit'] ) ? $package_keys_values[$k]['wl_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="dmt_2_master_dist_profit" id="dmt_2_master_dist_profit" value="<?php echo ( $package_keys_values[$k]['master_dist_profit'] ) ? $package_keys_values[$k]['master_dist_profit'] : '0'; ?>"></td>
																	<td><input type="text" style="width:50px;" name="dmt_2_dist_profit" id="dmt_2_dist_profit" value="<?php echo ( $package_keys_values[$k]['dist_profit'] ) ? $package_keys_values[$k]['dist_profit'] : '0'; ?>"></td>
																	<td><input type="text" size="10" style="width:50px;"  name="dmt_2_retailer_profit" id="dmt_2_retailer_profit" value="<?php echo ( $package_keys_values[$k]['retailer_profit'] ) ? $package_keys_values[$k]['retailer_profit'] : '0.15'; ?>"  /></td>
																</tr>
																
															</tbody>						
														</table>					
													</div>
												</div>

											</div>
											<div class="text-right">
												<input type="hidden" name="user_id" value="<?php echo $_SESSION[_session_userid_]; ?>" />

												<?php if (isset($_POST['create_new_package']) && $_POST['package_new'] == 'new') { ?>
												
													<input type="hidden" name="add" value="CREATE" />
												
												<?php } else { ?>

													<input type="hidden" name="package_id" value="<?php echo $_POST['package_id']; ?>" />
													<input type="hidden" name="edit" value="UPDATE" />

												<?php } ?>

												<button type="submit" class="btn btn-primary btn-ladda btn-ladda-progress"><span class="ladda-label"><?php if (isset($_POST['create_new_package']) && $_POST['package_new'] == 'new') { echo 'CREATE'; } else {  echo 'UPDATE'; } ?> <i class="icon-arrow-right14 position-right"></i></span><span class="ladda-spinner"></span></button>
											</div>
										</form>

								</div>
								
<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include_once('inc/footer.php'); ?>
<script>
    function getRetailerDistributor(usertype) {

        $.ajax({
            url: 'ajax/get_retailerdistributor.php',
            cache: false,
            type: 'POST',
            data: {usertype: usertype, master: <?php echo $_SESSION[_session_userid_] ?>},
            success: function (response) {
                $("#usernames").html(response);
            }
        });
    }
</script>
</body>
</html>
