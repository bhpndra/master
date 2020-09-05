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
			  <div class="overlay" id="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
              <div class="card-body" id="customTabContent">
				<ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
				  <li class="nav-item">
					<a class="nav-link active" onclick="get_operator('PREPAID');" id="mobilePanel-tab" data-toggle="pill" href="#mobilePanel" role="tab" aria-controls="custom-content-above-home" aria-selected="true"><i class="fas fa-mobile-alt"></i> <span>Mobile</span></a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link"  onclick="get_operator('DTH');" id="dthPanel-tab" data-toggle="pill" href="#dthPanel" role="tab" aria-controls="custom-content-above-profile" aria-selected="false"><i class="fas fa-satellite-dish"></i> <span>DTH</span></a>
				  </li>
				</ul>			  
				<div class="tab-content" id="custom-content-above-tabContent">
				  <div class="tab-pane fade pt-4 show active" id="mobilePanel" role="tabpanel" aria-labelledby="custom-content-above-home-tab">
					<!-- Mobile Recharge -->
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
				<!-- Prepaid Form -->
					<div id="prepaid">
						<div class="form-group">
						  <label>Number:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-phone"></i></span>
							</div>
							<input type="text" class="form-control" name="number" />
						  </div>
						  <!-- /.input group -->
						</div>
						
						<div class="form-group">
						  <label>Amount:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-rupee-sign"></i></span>
							</div>
							<input type="text" class="form-control" name="amount" />
						  </div>
							<a style="float: right;" href="javascript:void(0)" data-toggle="modal" data-target="#planPopup" >View Plan</a>
						  <!-- /.input group -->
						</div>
						
						<div class="form-group">
						  <label>Operator:</label>
						  <select class="form-control select2" style="width: 100%;" id="NETWORK_PREPAID" name="network">
							<option selected="selected">Operator</option>
						  </select>
						</div>
						
						<div class="form-group">
						  <label>Network Circle:</label>
						  <select class="form-control select2" style="width: 100%;" id="CIRCLE_PREPAID" name="circle">
							
						  </select>
						</div>
					  
					  <div class="card-footer">
						<button type="submit" onclick="prepaid(this)" class="btn btn-primary">Prepaid</button>
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
							<input type="text" class="form-control" name="number" />
						  </div>
						  <!-- /.input group -->
						</div>
						
						<div class="form-group">
						  <label>Amount:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-rupee-sign"></i></span>
							</div>
							<input type="text" class="form-control" name="amount" />
						  </div>
						  <!-- /.input group -->
						</div>
						
						<div class="form-group">
						  <label>Operator:</label>
						  <select class="form-control select2" name="network" style="width: 100%;" id="NETWORK_POSTPAID">
							<option selected="selected">Operator</option>
						  </select>
						</div>
						
						<div class="form-group">
						  <label>Network Circle:</label>
						  <select class="form-control select2" name="circle" style="width: 100%;"  id="CIRCLE_POSTPAID">
							
						  </select>
						</div>
					  
					  <div class="card-footer">
						<button type="submit" onclick="postpaid(this)" class="btn btn-primary">Postpaid</button>
					  </div>
					 </div>
				  </div>
				  <div class="tab-pane fade pt-4" id="dthPanel" role="tabpanel" aria-labelledby="dthPanel-tab">
					 <div id="dth">
						<div class="form-group">
						  <label>Number:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-phone"></i></span>
							</div>
							<input type="text" class="form-control" name="number" />
						  </div>
						  <!-- /.input group -->
						</div>
						
						<div class="form-group">
						  <label>Amount:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-rupee-sign"></i></span>
							</div>
							<input type="text" name="amount" class="form-control" />
						  </div>
							<a style="float: right;" href="javascript:void(0)" data-toggle="modal" data-target="#planPopup" >View Plan</a>
						  <!-- /.input group -->
						</div>
						
						<div class="form-group">
						  <label>Operator:</label>
						  <select class="form-control select2" style="width: 100%;" id="NETWORK_DTH" name="network">
							<option selected="selected">Operator</option>
						  </select>
						</div>
						
						<div class="form-group">
						  <label>Network Circle:</label>
						  <select class="form-control select2" id="CIRCLE_DTH" name="circle">
							<option selected="selected">Network Circle</option>
						  </select>
						</div>
					  
					  <div class="card-footer">
						<button type="submit" onclick="dth(this)" class="btn btn-primary">DTH Recharge</button>
					  </div>
					 </div>
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
					  <table  class="table m-0 text-small">
						<thead>
						<tr>
						  <th>Order ID</th>
						  <th>Type</th>
						  <th>Amount</th>
						  <th>Operator</th>
						  <th>Type</th>
						  <th>Status</th>
						</tr>
						</thead>
<?php
$circle_url = BASE_URL."/api/recharge/report.php";
$post_fields = array("token"=>$_SESSION['TOKEN'],"limit"=>10);
$responseRC = api_curl($circle_url,$post_fields,$headerArray);
$resRC = json_decode($responseRC,true);
?>
						<tbody>
						<?php
							if($resRC['ERROR_CODE']==0){
								foreach($resRC['DATA'] as $row){
						?>
						<tr>
						  <td><?=$row['agent_trid']?></td>
						  <td><span class="mobile"><?=$row['mobile']?></span><br/><a class="text-small" href="javascript:void(0)" onclick="recharge_repeat(this,'<?=$row['rech_type']?>')">Repeat Recharge</a></td>
						  <td class="amount"><?=$row['amount']?></td>
						  <td class="operator">
							<?=$resRC['NETWORKS'][$row['operator']]?>
							<input type="hidden" value="<?=$row['operator']?>" />
						  </td>
						  <td class="rech_type"><?=$row['rech_type']?></td>
						  <?php 
							if($row['status']=='SUCCESS'){ $badge = 'success'; } else if($row['status']=='PENDING') { $badge = 'warning'; } else if($row['status']=='FAILED') { $badge = 'danger'; } else { $badge = 'info'; }
						  ?>
						  <td><span class="badge badge-<?=$badge?>"><?=$row['status']?></span></td>
						</tr>
						<?php
								}
							}
						?>
						</tbody>
					  </table>
					</div>
					<!-- /.table-responsive -->
				  </div>
				  <!-- /.card-body -->
				  <div class="card-footer clearfix">
					<a href="recharge-report" class="btn btn-sm btn-secondary float-right">View All Recharge</a>
				  </div>
				  <!-- /.card-footer -->
				</div>
			</div>
			
		</div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>

<div class="modal fade" id="planPopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Recharge Plan</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <!--Body-->
      <div class="modal-body">
		<div class="row">
		  <div class="form-group col-sm-5">
			<label>Circle:</label>
			<select class="select2 form-control" id="plan_circle">
			</select>
		  </div>
		  <div class="form-group col-sm-4">
			<label>Operator:</label>
			<select class="select2 form-control" id="plan_operator">
			</select>
		  </div>
		  <div class="form-group col-sm-2">
			<label style="opacity: 0;">Fetch:</label>
			<a class="form-control btn btn-primary" id="fetchPlan" onclick="fetchPlan()" href="javascript:void(0)">Fetch</a>
		  </div>
		</div>
		<hr/>

		<div  style="max-height: 250px;overflow-y: scroll;">
		<table class="table table-hover">
		  <thead>
            <tr>
              <th>Plan</th>
              <th>Description</th>
              <th>Talktime</th>
              <th>Validity</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody id="planList">
			
          </tbody>
        </table>
		</div>
      </div>
      <!--Footer-->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php include("inc/footer.php"); ?>
<script>
	function change_recharge(val){
		if(val=="prepaid"){
			$("#prepaid").css("display","block");
			$("#postpaid").css("display","none");
			get_operator('PREPAID');
		}
		if(val=="postpaid"){
			$("#postpaid").css("display","block");
			$("#prepaid").css("display","none");
			get_operator('POSTPAID');
		}
	}
	function get_operator(val){		
		$("#loading").css("display","flex");
		$.ajax({
			url :  '<?=DOMAIN_NAME?>retailer/ajax/recharge/get_network.php',
			type:  'POST',
			cache: false,
			data :  {operator_type:val},
			dataTpe : "json",
			success:function(response)
			{
				//console.log(response);			   
				$("#NETWORK_"+val).html(response);						
				$("#plan_operator").html(response);						
				$("#loading").css("display","none");
			}
		});
		get_circle(val);
	}
</script>
<script>
$(document).ready(function(){
	get_operator('PREPAID');
});

function get_circle(operator_type){ 	
	$("#loading").css("display","flex");
	$.ajax({
		url :  '<?=DOMAIN_NAME?>retailer/ajax/recharge/get_circle.php',
		type:  'POST',
		cache: false,
		data :  {operator_type:operator_type},
		dataTpe : "json",
		success:function(response)
		{
			//console.log(response);			   
			$("#CIRCLE_"+operator_type).html(response);						
			$("#loading").css("display","none");
		}
	});
}
function postpaid(e){
	var btn = e;
	var number = $(btn).closest("#postpaid").find("input[name='number']").val();
	var amount = $(btn).closest("#postpaid").find("input[name='amount']").val();
	var network = $(btn).closest("#postpaid").find("select[name='network'] option:selected").val();
	var circle = $(btn).closest("#postpaid").find("select[name='circle'] option:selected").val();
	if(number=='' || amount=='' || network=='' || circle=='' ){
		Swal.fire(
		  'Oops...',
		  'Fill all field',
		  'error'
		);
	} else {
		$("#loading").css("display","flex");
		recharge_procces('POSTPAID',number,amount,circle,network);
	}
}
function prepaid(e){
	var btn = e;
	var number = $(btn).closest("#prepaid").find("input[name='number']").val();
	var amount = $(btn).closest("#prepaid").find("input[name='amount']").val();
	var network = $(btn).closest("#prepaid").find("select[name='network'] option:selected").val();
	var circle = $(btn).closest("#prepaid").find("select[name='circle'] option:selected").val();
	if(number=='' || amount=='' || network=='' || circle=='' ){
		Swal.fire(
		  'Oops...',
		  'Fill all field',
		  'error'
		);
	} else {
		$("#loading").css("display","flex");
		recharge_procces('PREPAID',number,amount,circle,network);
	}
}
function dth(e){
	var btn = e;
	var number = $(btn).closest("#dth").find("input[name='number']").val();
	var amount = $(btn).closest("#dth").find("input[name='amount']").val();
	var network = $(btn).closest("#dth").find("select[name='network'] option:selected").val();
	var circle = $(btn).closest("#dth").find("select[name='circle'] option:selected").val();
	if(number=='' || amount=='' || network=='' || circle=='' ){
		Swal.fire(
		  'Oops...',
		  'Fill all field',
		  'error'
		);
	} else {
		$("#loading").css("display","flex");
		recharge_procces('DTH',number,amount,circle,network);
	}
}
function recharge_procces(operator_type,number,amount,circle,network){ 
	$.ajax({
		url :  '<?=DOMAIN_NAME?>retailer/ajax/recharge/get_recharge_commission.php',
		type:  'POST',
		cache: false,
		data :  {operator_type:operator_type,number:number,amount:amount,circle_code:circle,operator:network},
		dataTpe : "json",
		success:function(response)
		{
			//console.log(response);
			var res = JSON.parse(response);
			if(res.status=="0") {
				var htmlText = "Subscriber ID : " + res.data.NUMBER + "<br>"+"Operator : " + res.data.NETWORK + "<br>" +"Amount : " + res.data.AMOUNT + "<br>" + "Commission : " + res.data.COMMISSION + "<br>" + "Your Cost : " + res.data.DEDUCTED_AMT
				Swal.fire({
				  title: res.msg,
				  html: htmlText,
				  type: "success",
				  showCancelButton: true,
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "Ok",
				  closeOnConfirm: false
				}).then((result) => {
					if (result.value) {
						
				$("#loading").css("display","flex");
				/* ------------------------------------------------------ */
					$.ajax({
						url :  '<?=DOMAIN_NAME?>retailer/ajax/recharge/transaction.php',
						type:  'POST',
						cache: false,
						data :  {operator_type:operator_type,number:number,amount:amount,circle_code:circle,operator:network},
						dataTpe : "json",
						success:function(response)
						{
							//console.log(response);
							var res = JSON.parse(response);
							if(res.status=="0") {
								Swal.fire({
								  title: "Transaction Success.",
								  html: res.msg,
								  type: "success",
								  showCancelButton: false,
								  confirmButtonColor: "#DD6B55",
								  confirmButtonText: "Ok",
								  closeOnConfirm: false
								}).then((result) => {
									if (result.value) {
										location.reload();
									  }
								});	
							} else {
								Swal.fire({
								  title: "Error.",
								  html: res.msg,
								  type: "error",
								  showCancelButton: false,
								  confirmButtonColor: "#DD6B55",
								  confirmButtonText: "Ok",
								  closeOnConfirm: false
								}).then((result) => {
									if (result.value) {
										location.reload();
									  }
								});	
							}
							$("#loading").css("display","none");
						}
					});
				/* ------------------------------------------------------ */
					}
				});	
			} else {
				Swal.fire({
				  title: "Error.",
				  html: res.msg,
				  type: "error",
				  showCancelButton: false,
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "Ok",
				  closeOnConfirm: false
				}).then((result) => {
					if (result.value) {
						location.reload();
					  }
				});	
			}
			$("#loading").css("display","none");
		}
	});
}



function recharge_repeat(e,type){
		var btn = e;
		var number = $(btn).closest("tr").find(".mobile").text();
		var amount = $(btn).closest("tr").find(".amount").text();
		var operator = $(btn).closest("tr").find(".operator input[type='hidden']").val();
		
	if(type=="PREPAID"){
		$("#prepaid").find("input[name='number']").val(number);
		$("#prepaid").find("input[name='amount']").val(parseInt(amount));
		$("#prepaid").find("select[name='network']").find("option[value=" + operator +"]").attr('selected', true);
		var selOpt = $("#prepaid").find("select[name='network'] option:selected").text();
		$("#select2-NETWORK_PREPAID-container").text(selOpt);
	}
	
	if(type=="DTH"){		
		$("#dth").find("input[name='number']").val(number);
		$("#dth").find("input[name='amount']").val(parseInt(amount));
		$("#dth").find("select[name='network']").find("option[value=" + operator +"]").attr('selected', true);
		var selOpt = $("#dth").find("select[name='network'] option:selected").text();
		$("#select2-NETWORK_DTH-container").text(selOpt);
	}
}
</script>
<script>
function fetchPlan(){ 
	var mobile_plan_circle     = $("#plan_circle").val();
	var mobile_plan_operator     = $("#plan_operator").val();
	if(mobile_plan_circle == '' && mobile_plan_operator == ''){
		alert("Select Circle and Operator first");
		return false;
	} else {
		
		$.ajax({
			type : 'POST',
			data : {circle_code : mobile_plan_circle,operator : mobile_plan_operator},
			cache: false,
			url : '<?=DOMAIN_NAME?>retailer/ajax/recharge/get_plan.php',
			success : function(response)
			{
				var all_planList = JSON.parse(response);			
				var tableBody = '';
				for(var key in all_planList['PLANS']) {
					 tableBody += "<tr><td>"+all_planList['PLANS'][key]['recharge_short_description']+"</td>";
					 tableBody += "<td>"+all_planList['PLANS'][key]['recharge_description']+"</td>";
					 tableBody += "<td>"+all_planList['PLANS'][key]['recharge_talktime']+"</td>";
					 tableBody += "<td>"+all_planList['PLANS'][key]['recharge_validity']+"</td>";
					 tableBody += "<td>"+all_planList['PLANS'][key]['recharge_value']+"</td></tr>";
				}	
				document.getElementById("planList").innerHTML = tableBody;			
			}
		});
	}
}


<!-- Recharge Plan Circle START -->
    var all_circle = '[{"circle_name":"Select Circle","circle_code":""}, {"circle_name":"Andhra Pradesh","circle_code":"AP"}, {"circle_name":"Arunachal Pradesh","circle_code":"AR"}, {"circle_name":"Assam","circle_code":"AS"}, {"circle_name":"Bihar","circle_code":"BR"}, {"circle_name":"Chandigarh","circle_code":"CH"}, {"circle_name":"Dadra and Nagar Haveli","circle_code":"DN"}, {"circle_name":"Daman and Diu","circle_code":"DD"}, {"circle_name":"Delhi","circle_code":"DL"}, {"circle_name":"Goa","circle_code":"GA"}, {"circle_name":"Gujarat","circle_code":"GJ"}, {"circle_name":"Haryana","circle_code":"HR"}, {"circle_name":"Himachal Pradesh","circle_code":"HP"}, {"circle_name":"Jammu and Kashmir","circle_code":"JK"}, {"circle_name":"Jharkhand","circle_code":"JH"}, {"circle_name":"Karnataka","circle_code":"KA"}, {"circle_name":"Kerala","circle_code":"KL"}, {"circle_name":"Lakshadweep","circle_code":"LD"}, {"circle_name":"Madhya Pradesh / Chhattisgarh","circle_code":"MP"}, {"circle_name":"Maharashtra","circle_code":"MH"}, {"circle_name":"Manipur","circle_code":"MN"}, {"circle_name":"Meghalaya","circle_code":"ML"}, {"circle_name":"Mizoram","circle_code":"MZ"}, {"circle_name":"Nagaland","circle_code":"NL"}, {"circle_name":"Odisha","circle_code":"OR"}, {"circle_name":"Puducherry","circle_code":"PY"}, {"circle_name":"Punjab","circle_code":"PB"}, {"circle_name":"Rajasthan","circle_code":"RJ"}, {"circle_name":"Sikkim","circle_code":"SK"}, {"circle_name":"Tamil Nadu","circle_code":"TN"}, {"circle_name":"Telangana","circle_code":"TG"}, {"circle_name":"Tripura","circle_code":"TR"}, {"circle_name":"Uttar Pradesh","circle_code":"UP"}, {"circle_name":"Uttarakhand","circle_code":"UT"}, {"circle_name":"West Bengal","circle_code":"WB"}] ';
	var all_circle1 = JSON.parse(all_circle);
	var circle_options = '';
	for(var key in all_circle1) {
         circle_options += "<option value=" + all_circle1[key]['circle_code']  + ">" +all_circle1[key]['circle_name'] + "</option>"
    }
	document.getElementById("plan_circle").innerHTML = circle_options;
<!-- Recharge Plan Circle END -->
</script>
</html>
