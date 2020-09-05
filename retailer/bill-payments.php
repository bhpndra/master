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

		<div class="row justify-content-center">
			<div class="col-md-7">

            <div class="card card-dark">
              <div class="card-header">
                <h3 class="card-title">Bill Payment</h3>
              </div>
			  <div class="overlay" id="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
              <div class="card-body" id="customTabContent">
				<ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
				  <li class="nav-item">
					<a class="nav-link text-success active" onclick="get_operator('5');" id="electricity-tab" data-toggle="pill" href="#electricity" role="tab" aria-controls="custom-content-above-home" aria-selected="true"><i class="fas fa-lightbulb"></i> <span>Electircity</span></a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link text-warning"  onclick="get_operator('6');" id="gasbill-tab" data-toggle="pill" href="#gasbill" role="tab" aria-controls="custom-content-above-profile" aria-selected="false"><i class="fas fa-burn"></i> <span>Gas</span></a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link text-dark"  onclick="get_operator('10');" id="waterbill-tab" data-toggle="pill" href="#waterbill" role="tab" aria-controls="custom-content-above-profile" aria-selected="false"><i class="fas fa-faucet"></i> <span>Water</span></a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link text-danger"  onclick="get_operator('13');" id="broadband-tab" data-toggle="pill" href="#broadband" role="tab" aria-controls="custom-content-above-profile" aria-selected="false"><i class="fas fa-network-wired"></i> <span>Broadband</span></a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link text-info"  onclick="get_operator('14');" id="landline-tab" data-toggle="pill" href="#landline" role="tab" aria-controls="custom-content-above-profile" aria-selected="false"><i class="fas fa-blender-phone"></i> <span>Landline</span></a>
				  </li>
				</ul>			  
				<div class="tab-content" id="custom-content-above-tabContent">
				  <div class="tab-pane fade pt-4 show active" id="electricity" role="tabpanel" aria-labelledby="electricity-tab">
					 <div class="paybill">
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
						  <label>CA Number:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-lightbulb"></i></span>
							</div>
							<input type="text" name="consumer_number" class="form-control" />
							<input type="hidden" name="operator_type" value="5" class="form-control" />
						  </div>
						</div>
						
						<div class="form-group">
						  <label>Operator:</label>
						  <select class="form-control select2" onchange="check_operator_status(this)" style="width: 100%;" id="NETWORK_5" name="network">
							<option selected="selected">Operator</option>
						  </select>
						</div>
											  
					  <div class="card-footer">
						<button type="submit" onclick="paybill(this)" class="btn btn-primary">Pay Electircity Bill</button>
					  </div>
					 </div>
				  </div>
				  
				  
				  <div class="tab-pane fade pt-4" id="gasbill" role="tabpanel" aria-labelledby="gasbill-tab">
					 <div class="paybill">
						<div class="form-group">
						  <label>Number:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-phone"></i></span>
							<input type="hidden" name="operator_type" value="6" class="form-control" />
							</div>
							<input type="text" class="form-control" name="number" />
						  </div>
						  <!-- /.input group -->
						</div>
						
						<div class="form-group">
						  <label>ARN Number:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-burn"></i></span>
							</div>
							<input type="text" name="consumer_number" class="form-control" />
						  </div>
						</div>
						
						<div class="form-group">
						  <label>Operator:</label>
						  <select class="form-control select2" onchange="check_operator_status(this)" style="width: 100%;" id="NETWORK_6" name="network">
							<option selected="selected">Operator</option>
						  </select>
						</div>
					  
					  <div class="card-footer">
						<button type="submit" onclick="paybill(this)" class="btn btn-primary">Pay Gas Bill</button>
					  </div>
					 </div>
				  </div>
				  
				  <div class="tab-pane fade pt-4" id="waterbill" role="tabpanel" aria-labelledby="waterbill-tab">
					 <div class="paybill">
						<div class="form-group">
						  <label>Number:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-phone"></i></span>
							</div>
							<input type="text" class="form-control" name="number" />
							<input type="hidden" name="operator_type" value="10" class="form-control" />
						  </div>
						  <!-- /.input group -->
						</div>
						
						<div class="form-group">
						  <label>Consumer Number:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-faucet"></i></span>
							</div>
							<input type="text" name="consumer_number" class="form-control" />
						  </div>
						</div>
						
						<div class="form-group">
						  <label>Operator:</label>
						  <select class="form-control select2" onchange="check_operator_status(this)" style="width: 100%;" id="NETWORK_10" name="network">
							<option selected="selected">Operator</option>
						  </select>
						</div>
					  
					  <div class="card-footer">
						<button type="submit" onclick="paybill(this)" class="btn btn-primary">Pay Water Bill</button>
					  </div>
					 </div>
				  </div>
				  
				  <div class="tab-pane fade pt-4" id="broadband" role="tabpanel" aria-labelledby="broadband-tab">
					 <div class="paybill">
						<div class="form-group">
						  <label>Number:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-phone"></i></span>
							</div>
							<input type="text" class="form-control" name="number" />
							<input type="hidden" name="operator_type" value="13" class="form-control" />
						  </div>
						  <!-- /.input group -->
						</div>
						
						<div class="form-group">
						  <label>Consumer Number:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-network-wired"></i></span>
							</div>
							<input type="text" name="consumer_number" class="form-control" />
						  </div>
						</div>
						
						<div class="form-group">
						  <label>Operator:</label>
						  <select class="form-control select2" onchange="check_operator_status(this)" style="width: 100%;" id="NETWORK_13" name="network">
							<option selected="selected">Operator</option>
						  </select>
						</div>
					  
					  <div class="card-footer">
						<button type="submit" onclick="paybill(this)" class="btn btn-primary">Pay Broadband Bill</button>
					  </div>
					 </div>
				  </div>
				  
				  <div class="tab-pane fade pt-4" id="landline" role="tabpanel" aria-labelledby="landline-tab">
					 <div class="paybill">
						<div class="form-group">
						  <label>Number:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-phone"></i></span>
							</div>
							<input type="text" class="form-control" name="number" />
							<input type="hidden" name="operator_type" value="14" class="form-control" />
						  </div>
						  <!-- /.input group -->
						</div>
						
						<div class="form-group">
						  <label>Consumer Number:</label>

						  <div class="input-group">
							<div class="input-group-prepend">
							  <span class="input-group-text"><i class="fas fa-blender-phone"></i></span>
							</div>
							<input type="text" name="consumer_number" class="form-control" />
						  </div>
						</div>
						
						<div class="form-group">
						  <label>Operator:</label>
						  <select class="form-control select2" onchange="check_operator_status(this)" style="width: 100%;" id="NETWORK_14" name="network">
							<option selected="selected">Operator</option>
						  </select>
						</div>
					  
					  <div class="card-footer">
						<button type="submit" onclick="paybill(this)" class="btn btn-primary">Pay Landline Bill</button>
					  </div>
					 </div>
				  </div>
					
				</div>
			  </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>

		</div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>


<?php include("inc/footer.php"); ?>
<script>
	function get_operator(val){		
		$("#loading").css("display","flex");
		$.ajax({
			url :  '<?=DOMAIN_NAME?>retailer/ajax/bbps/get_network.php',
			type:  'POST',
			cache: false,
			data :  {operator_type:val},
			dataTpe : "json",
			success:function(response)
			{
				//console.log(response);			   
				$("#NETWORK_"+val).html(response);					
				$("#loading").css("display","none");
			}
		});
	}
	
	function check_operator_status(e){		
		$("#loading").css("display","flex");
		var ele_id = $(e).attr('id');
		var operator_value = $("#"+ ele_id + " option:selected").val();
		var operator_name = $("#"+ ele_id + " option:selected").text();
		$.ajax({
			url :  '<?=DOMAIN_NAME?>retailer/ajax/bbps/get_network_status.php',
			type:  'POST',
			cache: false,
			data :  {operator_name:operator_name,operator_value:operator_value},
			dataTpe : "json",
			success:function(response)
			{
				var res = JSON.parse(response);
				if(res.ERROR_CODE==1){
					Swal.fire(
					  'Oops...',
					  'Operator Down',
					  'error'
					);
				}				
				$("#loading").css("display","none");
				
			}
		}); 
	}
	
		
	function fetch_bill(e){
		var btn = e;
		var number = $(btn).closest(".paybill").find("input[name='number']").val();
		var consumer_number = $(btn).closest(".paybill").find("input[name='consumer_number']").val();
		var operator_type = $(btn).closest(".paybill").find("input[name='operator_type']").val();
		var network = $(btn).closest(".paybill").find("select[name='network'] option:selected").val();		
		var operator_value =  $(btn).closest(".paybill").find("select[name='network'] option:selected").val();
		var operator_name =  $(btn).closest(".paybill").find("select[name='network'] option:selected").text();
		//alert(number + " " + consumer_number + " " + network);
		if(number=='' || consumer_number=='' || network=='' ){
			Swal.fire(
			  'Oops...',
			  'Fill all field',
			  'error'
			);
		} else {
			$("#loading").css("display","flex"); //alert(number + " " + consumer_number + " " + network);
			$.ajax({
				url :  '<?=DOMAIN_NAME?>retailer/ajax/bbps/fetch_bill.php',
				type:  'POST',
				cache: false,
				data :  {customer_mobile:number,consumer_number:consumer_number,network:network,operator_value:operator_value,operator_name:operator_name,operator_type:operator_type},
				dataTpe : "json",
				success:function(response)
				{
					var res = JSON.parse(response);
					if(res.ERROR_CODE==0){
						var dueamount = res.DATA.dueamount;
						var reference_id = res.DATA.reference_id;
						var html = '<table class="table text-right">';
							html += '<tr><th class="text-right">Due Bill: </th><td class="text-left">'+ dueamount +'</td></tr>';
							html += '<tr><th class="text-right">Due Date: </th><td class="text-left">'+ res.DATA.duedate +'</td></tr>';
							html += '<tr><th class="text-right">Customer Name: </th><td class="text-left">'+ res.DATA.customername +'</td></tr>';
							html += '<tr><th class="text-right">Bill Number: </th><td class="text-left">'+ res.DATA.billnumber +'</td></tr>';
							html += '<tr><th class="text-right">Bill Date: </th><td class="text-left">'+ res.DATA.billdate +'</td></tr>';
							html += '</table>';
							Swal.fire({
								  title: "Bill Fetch.",
								  html: html,
								  type: "success",
								  showCancelButton: true,
								  confirmButtonColor: "#DD6B55",
								  confirmButtonText: "Process to Pay",
								  closeOnConfirm: true
								}).then((result) => {
									if (result.value) {
										//location.reload();
										bill_procces(btn,reference_id,dueamount);
									  }
								});	
					} else {
						Swal.fire(
						  'Oops...',
						  res.MESSAGE,
						  'error'
						);
					}				
					$("#loading").css("display","none");
					
				}
			});
		}
	}
</script>
<script>
$(document).ready(function(){
	get_operator('5');
});

function paybill(e){
	var btn = e;
	var number = $(btn).closest(".paybill").find("input[name='number']").val();
	var consumer_number = $(btn).closest(".paybill").find("input[name='consumer_number']").val();
	var network = $(btn).closest(".paybill").find("select[name='network'] option:selected").val();
	if(number=='' || consumer_number=='' || network=='' ){
		Swal.fire(
		  'Oops...',
		  'Fill all field',
		  'error'
		);
	} else {
		fetch_bill(btn);
	}
}
function bill_procces(e,reference_id,amount){ 
	$("#loading").css("display","flex");
	var btn = e;
	var customer_mobile = $(btn).closest(".paybill").find("input[name='number']").val();
	var consumer_number = $(btn).closest(".paybill").find("input[name='consumer_number']").val();
	var operator_type = $(btn).closest(".paybill").find("input[name='operator_type']").val();		
	var operator_value =  $(btn).closest(".paybill").find("select[name='network'] option:selected").val();
	var operator_name =  $(btn).closest(".paybill").find("select[name='network'] option:selected").text();
	//alert(reference_id + ' - ' + operator_name + ' - ' + operator_value + ' - ' + operator_type + ' - ' + consumer_number + ' - ' + customer_mobile + ' - ' + amount);
	$.ajax({
		url :  '<?=DOMAIN_NAME?>retailer/ajax/bbps/transaction.php',
		type:  'POST',
		cache: false,
		data :  {operator_value:operator_value,operator_name:operator_name,customer_mobile:customer_mobile,consumer_number:consumer_number,amount:amount,reference_id:reference_id,operator_type:operator_type},
		dataTpe : "json",
		success:function(response)
		{
			var res = JSON.parse(response);
			$("#loading").css("display","none");
			if(res.status==0){
			//console.log(response);
				Swal.fire({
				  title: "Transaction Status",
				  html: res.msg,
				  type: "success",
				  showCancelButton: false,
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "Process to Pay",
				  closeOnConfirm: false
				}).then((result) => {
					if (result.value) {
						location.reload();
					  }
				});	
			} else {
				Swal.fire({
				  title: "Transaction Status",
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


</script>
</html>
