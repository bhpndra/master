<?php session_start(); ?>
<?php include("../config.php"); ?>
<?php include("../include/lib.php"); ?>
<?php include("inc/head.php"); ?>
<?php include("inc/nav.php"); ?>
<?php include("inc/sidebar.php"); ?>

<style type="text/css">
.error{color: red;
    font-weight: normal ! important;}
</style>
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
	    <div class="col-md-12">
	        <div class="card card-info">
	            <div class="card-header">
	              <h3 class="card-title">Religare Insurance</h3>
	            </div>
	            <!-- /.card-header -->
	            <div id="mypolicy"></div>
	            <!-- form start -->
	            <form class="form-horizontal" method="post" id="submitproposal" novalidate="novalidate">
	            <div class="card-body">
	            <div class="row">
	            	<div class="form-group col-sm-12">
	            		<div class="error-msg"></div>
	                </div>
	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">Choose Policy</label>
	                  <div>
	                    <select class="form-control" name="sumInsured" id="sumInsured">
							<option selected="selected" value="">Cover Type, Sum Insured</option>
							<option value="001">INDIVIDUAL, 100000</option>
							<option value="002">INDIVIDUAL, 200000</option>
						</select>
	                  </div>
	                </div>
	            </div>


	            <div class="row proposalform">
	                <div class="form-group col-sm-2">
	                  <label class="col-form-label">Title</label>
	                  <div>
	                    <select class="form-control" name="titleCd">
							<option selected="selected" value="" disabled>Choose Title</option>
							<option value="MR">MR</option>
							<option value="MS">MS</option>
						</select>
	                  </div>
	                </div>
	                <div class="form-group col-sm-5">
	                  <label class="col-form-label">First Name</label>
	                  <div>
	                    <input type="text" class="form-control" name="firstName">
	                  </div>
	                </div>

	                <div class="form-group col-sm-5">
	                  <label class="col-form-label">Last Name</label>
	                  <div>
	                    <input type="text" class="form-control" name="lastName">
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">Gender</label>
	                  <div>
	                    <select class="form-control" name="genderCd">
							<option selected="selected" value="" disabled>Choose</option>
							<option value="MALE">MALE</option>
							<option value="FEMALE">FEMALE</option>
						</select>
	                  </div>
	                </div>
	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">Date of Birth<small>(Min Age: 18 Years)</small></label>
	                  <div>
	                    <input type="date" max="<?php $dt=date("Y-m-d"); $date=strtotime($dt.' -18 year'); echo date('Y-m-d', $date);?>" class="form-control" name="birthDt">
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">Mobile No</label>
	                  <div>
	                    <input type="tel" class="form-control" name="contactNum">
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">Email</label>
	                  <div>
	                    <input type="email" class="form-control" name="emailAddress">
	                  </div>
	                </div>

	                <div class="col-sm-12">
	                	<label class="col-form-label" style="color:#17a2b8;">PERMANENT ADDRESS</label>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">Address Line 1</label>
	                  <div>
	                    <input type="text" class="form-control" name="addressLine1Lang1">
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">Address Line 2</label>
	                  <div>
	                    <input type="text" class="form-control" name="addressLine2Lang1">
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">State</label>
	                  <div>
	                    <select class="form-control" name="stateCd" id="stateCd" onchange="get_city(this.value)">
						</select>
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">City</label>
	                  <div>
	                    <select class="form-control" name="cityCd" id="cityCd">
						</select>
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">Pin Code</label>
	                  <div>
	                    <input type="number" class="form-control" name="pinCode">
	                  </div>
	                </div>

	                <div class="col-sm-12">
	                	<label class="col-form-label" style="color:#17a2b8;">COMMUNICATION ADDRESS</label>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">Address Line 1</label>
	                  <div>
	                    <input type="text" class="form-control" name="addressLine1Lang1c">
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">Address Line 2</label>
	                  <div>
	                    <input type="text" class="form-control" name="addressLine2Lang1c">
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">State</label>
	                  <div>
	                    <select class="form-control" name="stateCdc" id="stateCdc" onchange="get_cityc(this.value)">
						</select>
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">City</label>
	                  <div>
	                    <select class="form-control" name="cityCdc" id="cityCdc">
						</select>
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">Pin Code</label>
	                  <div>
	                    <input type="number" class="form-control" name="pinCodec">
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                </div>

	                <div class="form-group col-sm-12">
	                  <div>
	                    <input type="checkbox" class="" name="tnc_check">
	                  </div>
	                  <div>I agree to Terms and conditions</div>
	                </div>
	                <div class="form-group col-sm-12">
	                  <div>
	                    <input type="checkbox" class="" name="ghdcheck">
	                  </div>
	                  <div>
	                    I hereby declare that all proposed members are in good health and entirely free from any mental or physical impairments or deformities, disease/condition. Also, none of the proposed member are habitual consumer of alcohol, tobacco, gutka or any recreational drugs.
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                </div>


	                <!--<div class="form-group col-sm-6">
	                  <label class="col-form-label">ID Proof</label>
	                  <div>
	                    <select class="form-control" name="identityTypeCd">
							<option selected="selected" value="" disabled>Choose</option>
							<option value="PAN">PAN</option>
							<option value="PASSPORT">PASSPORT</option>
						</select>
	                  </div>
	                </div>

	                <div class="form-group col-sm-6">
	                  <label class="col-form-label">ID Number</label>
	                  <div>
	                    <input type="text" class="form-control" name="identityNum">
	                  </div>
	                </div> -->




	                <div class="form-group col-sm-12">
	                    <button type="submit" class="btn btn-info form-btn subbtn">Proceed</button>
	                </div>
	            </div>
	            </div>
	              <!-- /.card-body -->
	              <div class="card-footer">
	                
	              </div>
	              <!-- /.card-footer -->
	            </form>



	            <form style="display: none;" class="form-horizontal col-sm-6" style="margin: auto;" method="post" id="submitpayment" novalidate="novalidate">
	            <div class="card-body">
	            <div class="row">
	            	<div class="form-group col-sm-12">
	            		<!--<div class="error-msg"></div>-->
	                </div>
	                <div class="col-sm-12">
	                  <label class="col-form-label">Name:</label><span id="res_firstName"></span> <span id="res_lastName"></span>
	                </div>
	                <div class="col-sm-12">
	                  <label class="col-form-label">Term:</label><span id="res_term"></span>
	                </div>
	                <div class="col-sm-12">
	                  <label class="col-form-label">Cover Type:</label><span id="res_coverType"></span>
	                </div>
	                <div class="col-sm-12">
	                  <label class="col-form-label">Premium:</label><span id="res_premium"></span>
	                  <input type="hidden" id="res_premium2" name="res_premium">
	                  <input type="hidden" id="proposalNum" name="proposalNum">
	                </div>

	                <div class="form-group col-sm-12">
	                    <button type="submit" class="btn btn-info form-btn pmnt">Pay Now</button>
	                </div>
	            </div>
		        </div>
		    	</form>
	          </div>
	    </div>
      </div>


      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>



<?php include("inc/footer.php"); ?>
<script src="<?=DOMAIN_NAME?>dashboard/plugins/jquery-validation/jquery.validate.min.js"></script>
<script type="text/javascript"> 
	$(function() {
	    $('.proposalform').hide(); 
	    $('#sumInsured').change(function(){
	        if($('#sumInsured').val() != '') {
	            $('.proposalform').show(); 
	        } else {
	            $('.proposalform').hide(); 
	        } 
	    });
	});

	function get_state(){
		$.ajax({
			url :  '<?=DOMAIN_NAME?>retailer/ajax/insurance/get_state.php',
			type:  'POST',
			cache: false,
			data :  {/**/},
			dataTpe : "json",
			success:function(response)
			{
				//console.log(response);			   
				$("#stateCd").html(response);
				$("#stateCdc").html(response);
			}
		});
	}

	function get_policy(){
		$.ajax({
			url :  '<?=DOMAIN_NAME?>retailer/ajax/insurance/get_policy.php',
			type:  'POST',
			cache: false,
			data :  {/**/},
			dataTpe : "json",
			success:function(response)
			{
				var res = JSON.parse(response);
				if(res.data.status=='success'){
					//$('#submitproposal').hide();
					$("#mypolicy").html('<div class="card-body"><div class="row"><div class="col-sm-12"><p>Recent Created Policy: <b>Name: </b>'+res.data.policies[0].firstName+' '+res.data.policies[0].lastName+' <b>Policy No: </b> '+res.data.policies[0].policyNum+' <a href="<?=DOMAIN_NAME?>retailer/ajax/insurance/religare_policy_pdf.php?policyno='+res.data.policies[0].policyNum+'" class="btn btn-sm btn-secondary">Download Policy</a></p></div></div></div>');
					//alert(res.data.policies[0].policyNum);
				}
				else{$('#submitproposal').show();}
				//console.log(response);			   
				//$("#stateCd").html(response);
				//$("#stateCdc").html(response);
			}
		});
	}

	$(document).ready(function(){
		get_state();
		get_policy();
		$('#submitpayment').hide();
	});

$("#submitpayment").validate({
      rules: {
        proposalNum: {
            required: true
        },
      },
      messages: {
      },
      submitHandler: function(form) {

      			Swal.fire({
				  title: "Are you sure to make payment?",
				  html: "payment will be deducted from your NetPaisa wallet",
				  type: "success",
				  showCancelButton: true,
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "Ok",
				  closeOnConfirm: false
						}).then((result) => {
					if (result.value) {

					var myform = document.getElementById("submitpayment");
			         //alert(myform.firstName.value);
			        var fdata = new FormData(myform ); 
			        //alert(myform.proposalNum.value);
			        $.ajax({
						url :  '<?=DOMAIN_NAME?>retailer/ajax/insurance/transaction.php',
						type:  'POST',
						cache: false,
						data :  {proposalNum:myform.proposalNum.value,amount:myform.res_premium.value},
						dataTpe : "json",
						beforeSend: function (data) {
            $(".error-msg").html('');
            $(".pmnt").html("Wait...").prop('disabled',true);
          },
						success:function(response)
						{
							//console.log(response);
							var res = JSON.parse(response);
							$(".pmnt").html("Pay Now").prop('disabled',false);
							if(res.status=="0") {
								var resp_details;
								if(res.relpayment.responseData['status']==1){
									resp_details="payment successfully completed Your Policy No is: "+res.relpayment.chequeDDReqResIO['policyNum'];
								}
								else{ resp_details="payment deducted from wallet but pending at religare."}
								Swal.fire({
								  title: "Transaction Success.",
								  html: resp_details,
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
										//location.reload();
									  }
								});	
							}
							//$("#loading").css("display","none");
						}
					});





					}
				});

        
    }



});




	function get_city(val){
		$.ajax({
			url :  '<?=DOMAIN_NAME?>retailer/ajax/insurance/get_city.php',
			type:  'POST',
			cache: false,
			data :  {statecd:val},
			dataTpe : "json",
			success:function(response)
			{
				//console.log(response);			   
				$("#cityCd").html(response);
			}
		});
	}

	function get_cityc(val){
		$.ajax({
			url :  '<?=DOMAIN_NAME?>retailer/ajax/insurance/get_city.php',
			type:  'POST',
			cache: false,
			data :  {statecd:val},
			dataTpe : "json",
			success:function(response)
			{
				console.log(response);			   
				$("#cityCdc").html(response);
			}
		});
	}


function alertMessage(type,message) {
    if (type=='error') {
      type = 'danger';
    }
  
    return "<div class='alert alert-"+type+" alert-dismissible'> <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a> "+message+" </div>";
  }

$.validator.addMethod("lettersonly", function(value, element) 
{
return this.optional(element) || /^[a-z ]+$/i.test(value);
}, "Letters and spaces only please");
$("#submitproposal").validate({
      rules: {
        titleCd: {
            required: true
        },
        firstName: {
            required: true,
            lettersonly: true
        },
        lastName: {
            required: true,
            lettersonly: true
        },
        contactNum: {
            required: true,
            digits: true,
            minlength: 10,
            maxlength: 10
        },
        emailAddress: {
            required: true
        },
        birthDt: {
            required: true
        },
        addressLine1Lang1: {
            required: true
        },
        cityCd: {
            required: true
        },
        stateCd: {
            required: true
        },
        genderCd: {
            required: true
        },
        pinCode: {
            required: true
        },
        addressLine1Lang1c: {
            required: true
        },
        cityCdc: {
            required: true
        },
        stateCdc: {
            required: true
        },
        pinCodec: {
            required: true
        },
        ghdcheck: {
            required: true
        },
        tnc_check: {
            required: true
        },
      },
      messages: {
      },
      submitHandler: function(form) {
        var myform = document.getElementById("submitproposal");
         //alert(myform.firstName.value);
        var fdata = new FormData(myform );
  
        $.ajax({
          type: "POST",
          url: "<?=DOMAIN_NAME?>retailer/ajax/insurance/get_religare_proposal.php",
          data: fdata,
          cache: false,
          processData: false,
          contentType: false,
          beforeSend: function (data) {
            $(".error-msg").html('');
            $(".subbtn").html("Wait...").prop('disabled',true);
          },
          success: function (response) {  
            setTimeout(function(){
              var dataOBJ;
                try {
                  dataOBJ = JSON.parse(response);
                  var obj = dataOBJ.data; //alert(obj.msg.intPolicyDataIO.policy.partyDOList['0'].firstName);
                  $(".subbtn").html("Proceed").prop('disabled',false);
  
                  if (obj.status=='success') {
                    //$('#submitproposal').hide(); 
                  	//alert(obj.msg.intPolicyDataIO.policy['premium']);
                  	//alert(obj.msg.intPolicyDataIO.policy.partyDOList['0'].firstName);

                  	$('#submitproposal').hide();
                  	$("#res_firstName").html(obj.msg.intPolicyDataIO.policy.partyDOList['0'].firstName);
                  	$("#res_lastName").html(obj.msg.intPolicyDataIO.policy.partyDOList['0'].lastName);
                  	$("#res_term").html(obj.msg.intPolicyDataIO.policy['term']);
                  	$("#res_coverType").html(obj.msg.intPolicyDataIO.policy['coverType']);
                  	$("#res_premium").html(obj.msg.intPolicyDataIO.policy['premium']);
                  	$("#res_premium2").val(obj.msg.intPolicyDataIO.policy['premium']); 
                  	$("#proposalNum").val(obj.msg.intPolicyDataIO.policy['proposalNum']); 
                  	$('#submitpayment').show();
                  	$('#mypolicy').hide();
                  //obj.msg.responseData['status']
                   

                    //$(".error-msg").html(alertMessage('success',obj.msg));
                    //window.location.href="https://vipraone.com/register";
                  }
                  else {
                    $(".error-msg").html(alertMessage('error',obj.msg));
              $('html, body').animate({
              scrollTop: $("html").offset().top
          }, 2000);
                  }
                }
                catch(err) {
                  $(".subbtn").html("Proceed").prop('disabled',false);
                  $(".error-msg").html(alertMessage('error','Some error occured, please try again.'));
              $('html, body').animate({
              scrollTop: $("html").offset().top
          }, 2000);
                }
            },500);
          },
          error: function () {
              $(".subbtn").html("Proceed").prop('disabled',false);
            $(".error-msg").html(alertMessage('error','Some error occured, please try again.'));
              $('html, body').animate({
              scrollTop: $("html").offset().top
          }, 2000);
             
          }
  
      });
  
      }
  });
</script>
</html>
