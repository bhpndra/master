<?php 
	// Requred files included
	include_once('inc/head.php'); 
	include_once('inc/header.php'); 
	include_once('classes/user_class.php'); 
	
	$mysqlObj = new mysql_class();
	$helper = new helper_class();
	$userClass = new user_class();	
	
	$filterBy = $helper->clearSlashes($_GET);
	
	$filter = "";			
	if(isset($filterBy['fBySearch'])&& $filterBy['fBySearch']!=""){
		$filter .= " and (concat_ws(' ',d.transaction_id,d.agent_trid,d.mobile,d.bene_code,d.bene_name,d.bene_ac,d.ifsc_code) like '%".$filterBy['fBySearch']."%') ";
	}			
	if(isset($filterBy['fBytransid'])&& $filterBy['fBytransid']!=""){
		$filter .= " and d.`transaction_id`= '".$filterBy['fBytransid']."' ";
	}
	if(isset($filterBy['fBystatus']) && $filterBy['fBystatus']!=""){
		$filter .= " and d.status = '".$filterBy['fBystatus']."'  ";
	}
	
	
?>
        <div class="page-wrapper-row full-height">
            <div class="page-wrapper-middle">
                <!-- BEGIN CONTAINER -->
                <div class="page-container">
                    <!-- BEGIN CONTENT -->
                    <div class="page-content-wrapper">
                        <!-- BEGIN CONTENT BODY -->
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
                                        <span>All App Configurations</span>
                                    </li>
                                </ul>
								
								 <!-- END PAGE BREADCRUMBS -->
								<div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>Advance Filter</div>
                                                </div>
												<div class="portlet-body">
												<div class="row">
													<form method="get" >
															<div class="form-group col-md-3">
																<label>Search</label>
																<div>
																	<input type='text' id="fBySearch" name='fBySearch' value="<?php echo @$filterBy['fBySearch']; ?>"  class='form-control' placeholder="Search Text" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>Domain URL</label>
																<div>
																	<input type='text' id="fBytransid" name='fBytransid' value="<?php echo @$filterBy['fBytransid']; ?>"  class='form-control' placeholder="Domain URL" >
																</div>
															</div>
															<div class="form-group col-md-3">
																<label>App URL</label>
																<div>
																	<input type='text' id="fBytransid" name='fBytransid' value="<?php echo @$filterBy['fBytransid']; ?>"  class='form-control' placeholder="App URL" >
																</div>
															</div>														
															
															<div class="form-group col-md-2">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="all-app-configurations.php" class="btn btn-default">Reset</a>
																</div>
															</div>
														</form>
												</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>All App Configurations</div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">S.No</th>
																	<th scope="col">Domain URL</th>
																	<th scope="col">App URL</th>
																	<th scope="col">Action</th>
																</tr>
                                                            </thead>
                                                            <tbody>
<?php
// for Pagination
include_once('classes/pagination.php'); 
$paginate = new pagin();
$pagiConfig = array();
$pagiConfig['current_page'] = isset($_GET['page']) ? $_GET['page'] : 1;
$pagiConfig['per_page_items'] = 25;
unset($_GET['page']);
$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?".http_build_query($_GET);

//Query

$sql1 = "SELECT 
		gs.user_id as user_id,
		gs.id,
		gs.app_link,
		wl.domain as domain,
		gs.app_link as app_link
		FROM 
		`general_settings` as gs
		left join 
		add_white_label as wl
		on gs.user_id = wl.user_id 
		WHERE gs.app_link != ''";
	
$pagiConfig['total_rows'] = $mysqlObj->countRows($sql1.$filter);
$pagination = $paginate->pagination($pagiConfig);
$sqlQuery = $mysqlObj->mysqlQuery($sql1.$filter." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);			
$i = $pagination['offset'] + 1;

while($rows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
				
				//$user = $mysqlObj->get_field_data('name,cname,mobile,usertype','add_cust'," where id='".$rows["user_id"]."'");
			?>
									<tr>
										<td class="sorting_1"><?php echo $i; ?></td>
										<td><?php echo $rows["domain"]; ?></td>
										<td><?php echo $rows["app_link"]; ?></td>
										<td><a href="update-app-configuration.php?id=<?=base64_encode($rows["user_id"]);?>"><button class="btn btn-info" disabled>Update</button></a></td>
									<?php $i++; } ?>
										
									</tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                
												<?php echo $pagination['pagination']; ?>
												</div>
                                            </div>
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

<script src="new-js/easy-autocomplete/jquery.easy-autocomplete.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
	//   fByUserid *************************************************
	var options = {
	url: function(phrase) {
		return "ajax/easy-autocomplete.php";
	},
	getValue: function(element) { //console.log(JSON.stringify(element))
		return element.name;				
	},
	ajaxSettings: {
		dataType: "json",
		method: "POST",
		data: {
			dataType: "json"
		}
	},
	preparePostData: function(data) { 
		data.searchString = $("#fByUsername").val();  
		data.filterOn = 'fByUsername';  //alert(JSON.stringify(data));
		return data;
	},
	requestDelay: 400
	};
	$("#fByUsername").easyAutocomplete(options);
});			
</script>

</body>
</html>