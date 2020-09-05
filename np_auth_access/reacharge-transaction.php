<?php
 include_once('inc/head.php');
 include_once('inc/header.php'); 
 include_once('classes/user_class.php'); 

    $mysqlObj = new mysql_class();
    $helper = new helper_class();
    $userClass = new user_class();
    //Filters 
    $filterBy = $helper->clearSlashes($_GET);
    
   $filter   = "";
   $filter1 = "";
   $filter2 = "";
   $filter3 = "";            
   if(isset($filterBy['fByTranid'])&& $filterBy['fByTranid']!=""){
        $filter .= " and (a.transaction_id like '%".$filterBy['fByTranid']."%' or a.agent_trid like '%".$filterBy['fByTranid']."%') ";
        $filter2 .= " and (a.transaction_id like '%".$filterBy['fByTranid']."%' or a.agent_trid like '%".$filterBy['fByTranid']."%') ";
    }
    if(isset($filterBy['fByMobile'])&& $filterBy['fByMobile']!=""){
        $filter .= " and b.`mobile` = '".$filterBy['fByMobile']."' ";
        $filter2 .= " and b.`mobile` = '".$filterBy['fByMobile']."' ";
    }
    if(isset($filterBy['fBycname'])&& $filterBy['fBycname']!=""){
        $filter .= " and b.`cname` like '%".$filterBy['fBycname']."%' ";
        $filter2 .= " and b.`cname` like '%".$filterBy['fBycname']."%' ";
    }   
    if(isset($filterBy['fByUsername'])&& $filterBy['fByUsername']!=""){
        $filter .= " and b.`name` like '%".$filterBy['fByUsername']."%' ";
        $filter2 .= " and b.`name` like '%".$filterBy['fByUsername']."%' ";
    }

    if(isset($filterBy['whitelevel'])&& $filterBy['whitelevel']!=""){
        $filter2 .= "";
    }

    
    
    if(@$filterBy['dateFrom']=="" && @$filterBy['dateTo']==""){
        $date1    = new DateTime('60 days ago');
        $dateFrom = $date1->format('Y-m-d');
        $dateTo   = date("Y-m-d");
        $filter  .= " and DATE(a.`time`) BETWEEN '$dateFrom' AND '$dateTo' ";
        $filter1  .= " and DATE(a.`time`) BETWEEN '$dateFrom' AND '$dateTo' ";
    } else{
        $filter .= " and DATE(a.`time`) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
        $filter1 .= " and DATE(a.`time`) BETWEEN '".$filterBy['dateFrom']."' AND '".$filterBy['dateTo']."' ";
        $dateFrom = $filterBy['dateFrom'];
        $dateTo = $filterBy['dateTo'];
    }   
?>



<div class="page-wrapper-row full-height">
    <div class="page-wrapper-middle">
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
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
                        <span>All Transactions</span>
                        </li>
                    </ul>    

     <!-- END PAGE BREADCRUMBS -->
<div class="page-content-inner">
    <div class="row">
        <div class="col-lg-12 col-xs-12 col-sm-12">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                      <i class="fa fa-cogs"></i>Advance Filter
                    </div>
                </div>
            <div class="portlet-body">
            <div class="row">
                <form method="get" >
                    <div class="form-group col-md-3">
                        <label>Name</label>
                        <div>
                        <input type='text' id="fByUsername" name='fByUsername' value="<?php echo @$filterBy['fByUsername']; ?>"  class='form-control' placeholder="Name" >
                        </div>
                    </div>                                            
                <div class="form-group col-md-3">
                    <label>Users Mobile Number</label>
                    <div>
                    <input type='text' name='fByMobile' value="<?php echo @$filterBy['fByMobile']; ?>"  class='form-control' placeholder="Mobile Number" >
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label>Transaction Id</label>
                    <div>
                    <input type='text' id="fByTranid" name='fByTranid' value="<?php echo @$filterBy['fByTranid']; ?>"  class='form-control' placeholder="Transaction Id" >
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label>Company Name</label>
                    <div>
                    <input type='text' id="fBycname" name='fBycname' value="<?php echo @$filterBy['fBycname']; ?>"  class='form-control' placeholder="Company Name" >
                    </div>
                </div>
                
                <div class="form-group col-md-3">
                    <label>From</label>
                    <div class="input-icon">
                    <i class="fa fa-calendar font-blue"></i>
                    <input type="date" value="<?=$dateFrom?>" class="form-control" placeholder="" name="dateFrom" id="dateFrom">
                    </div>
                </div>
                
                <div class="form-group col-md-3">
                    <label>To</label>
                    <div class="input-icon">
                    <i class="fa fa-calendar font-blue"></i>
                    <input type="date" value="<?=$dateTo?>" class="form-control" placeholder="" name="dateTo" id="dateTo">
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label>White Level</label>
                    <div>
                    <select name="whitelevel" class="form-control" >
                    <?php $sqlwl = "SELECT a.user_id,b.cname,a.domain FROM `add_white_label` as a left join add_cust as b on a.user_id=b.id";
                    $sqlQuery1 = $mysqlObj->mysqlQuery($sqlwl); 
                    while($tran = $sqlQuery1->fetch(PDO::FETCH_ASSOC)){
                    ?>
                      <option value="<?=$tran['user_id']?>"><?=$tran['cname']?>-<?=$tran['domain']?></option>
                    <?php 
                    } ?>
                    </select>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label style="opacity:0">Filter</label>
                    <div>
                    <input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> <a href="reacharge-transaction.php" class="btn btn-default">Reset</a>
                    </div>
                </div>
<?php
    if($filter3!=''){
    $sql = "select sum(a.amount) as total from recharge_info as a where 1 and a.status = 'SUCCESS' and a.user_id in (SELECT acmd.`id` FROM `add_cust` acmd WHERE acmd.`created_by`='DISTRIBUTOR' AND acmd.`creator_id` IN (SELECT acmd.`id` FROM `add_cust` acmd WHERE acmd.`created_by`='DISTRIBUTOR' AND acmd.`creator_id` IN (SELECT acmdw.`id` FROM `add_cust` acmdw WHERE acmdw.`created_by`='WL' AND acmdw.`creator_id`='1269')) or acmd.`creator_id` IN (SELECT acmdw.`id` FROM `add_cust` acmdw WHERE acmdw.`created_by`='WL' AND acmdw.`creator_id`='1269'))".$filter1;
    }else{
        $sql = "select sum(a.amount) as total from recharge_info as a where 1 and a.status = 'SUCCESS' ";
    }
    if($filter2!=""){
        $cond = " and a.user_id in (select b.id from add_cust as b where 1 $filter2)";
        $r_total = $mysqlObj->mysqlQuery($sql.$filter1.$cond)->fetch(PDO::FETCH_ASSOC);
    }else{
        $r_total = $mysqlObj->mysqlQuery($sql.$filter1)->fetch(PDO::FETCH_ASSOC);    
    }
?>				<div class="form-group col-md-3">
                    <label style="opacity:0">Filter</label>
                    <div>
                    <a href="#" class="btn btn-default">Total Success Recharge: <?=$r_total['total']?> </a>
                    </div>
                </div>
                </form>
            </div>
            </div>
            </div>
        </div>
    </div>
</div>  
                                                                                   
<div class="page-content-inner">
    <div class="row">
        <div class="col-lg-12 col-xs-12 col-sm-12">
            <div class="portlet box blue">
                <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs"></i>Reacharge transactions 
                </div>
                <div class="actions">
                                        <a href="export/export-reacharge-transaction.php?<?php echo http_build_query($_GET); ?>" target="_black" class="btn btn-success pull-right" download>Export</a>
                                    </div>
                </div>
    <div class="portlet-body">
        <div class="table-responsive">
<table class="table table-bordered">
    <thead>
    <tr>
    	<th scope="col">#</th>
    	<th scope="col">Users</th>
    	<th scope="col">TrID</th>
		<th scope="col">Mobile</th>
		<th scope="col">Rech. Amount</th>
		<th scope="col">Balance</th>
		<th scope="col">Rech. Type</th>
		<th scope="col">Time</th>
		<th scope="col">Status</th>
    </tr>
    </thead>
<tbody>
<?php
// for Pagination
include_once('classes/pagination.php'); 
$paginate = new pagin();
$pagiConfig = array();
$pagiConfig['current_page']   = isset($_GET['page']) ? $_GET['page'] : 1;
$pagiConfig['per_page_items'] = 8;
unset($_GET['page']);
$pagiConfig['base_url'] = $_SERVER['PHP_SELF']."?".http_build_query($_GET);

	$orderBy = "ORDER BY a.`id` DESC";
	$sql = "select b.name,b.cname,b.mobile as mob,a.* from recharge_info a, add_cust as b where a.user_id = b.id ";
	
	$pagiConfig['total_rows'] = $mysqlObj->countRows($sql.$filter);
	$pagination = $paginate->pagination($pagiConfig);
	$sqlQuery = $mysqlObj->mysqlQuery($sql.$filter.$orderBy." LIMIT ".$pagination['offset'].",".$pagiConfig['per_page_items']);
	$i = $pagination['offset'] + 1;
	while($tran = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
?>
<tr>
	<td><?php echo $i; ?></td>
	<td>
		<strong>Name : 	 </strong><?php echo $tran["name"]; ?><br/>
		<strong>User Mobile : </strong><?php echo $tran["mob"]; ?><br/>
		<strong>Company :</strong><?php echo $tran["cname"]; ?>	
	</td>
	<td><?php echo $tran['transaction_id']; ?></td>
	<td><?php echo $tran['mobile']; ?></td>
	<td><?php echo $tran['amount']; ?></td>
	<td><?php echo $tran['balance']; ?></td>
	<td><?php echo $tran['type']; ?></td>
	<td><?php echo $tran['time']; ?></td>
	<td><button class="btn btn-primary btn-xs"><?php echo $tran['status']; ?></button></td>
</tr>
	<?php $i++; } ?>
</tbody>
</table>
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
			
			//   fByUserid ***********************************************
			var optionsForUserid = {
			url: function(phrase) {
				return "ajax/easy-autocomplete.php";
			},
			getValue: function(element) { 
				return element.user;				
			},
			ajaxSettings: {
				dataType: "json",
				method: "POST",
				data: {
					dataType: "json"
				}
			},
			preparePostData: function(data) { 
				data.searchString = $("#fByUserid").val();  
				data.filterOn = 'fByUserid';
				return data;
			},
			requestDelay: 400
			};
			$("#fByUserid").easyAutocomplete(optionsForUserid);	
			
			//   fBycname ***********************************************
			var optionsForCname = {
			url: function(phrase) {
				return "ajax/easy-autocomplete.php";
			},
			getValue: function(element) { 
				return element.cname;				
			},
			ajaxSettings: {
				dataType: "json",
				method: "POST",
				data: {
					dataType: "json"
				}
			},
			preparePostData: function(data) { 
				data.searchString = $("#fBycname").val();  
				data.filterOn = 'fBycname';
				return data;
			},
			requestDelay: 400
			};
			$("#fBycname").easyAutocomplete(optionsForCname);	
		});
</script>
</body>
</html>
