<?php 
 session_start(); 
 include("../config.php"); 
 include("../include/lib.php"); 
 include("inc/head.php"); 
 include("inc/nav.php"); 
 include("inc/sidebar.php"); 
 
	

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">AEPS Instant Settlement</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">AEPS Instant Settlement</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
	  
		<div class="row">
			<div class="col-md-12">
			  <div class="card">
				<div class="card-header">
				  <h3 class="card-title">AEPS Instant Settlement</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body table-responsive">
				  <table id="example1" class="table table-bordered table-striped">
					<thead>
					<tr>
					  <th>#</th>
					  <th>Bank_Name</th>
					  <th>Account_No.</th>
					  <th>IFSC.</th>
					  <th>A/C Holder Name</th>
					  <th>Amount</th>
					  <th>Transaction Id</th>
					  <th>Group Id</th>
					  <th>Request Date</th>
					  <th>Update Date</th>
					  <th>Status</th>
					</tr>
					</thead>
					<tbody>
<?php
$circle_url = BASE_URL."/api/aeps/aeps-settlement-history.php";
$post_fields["token"] = $_SESSION['TOKEN'];
$post_fields["status"] = "PENDING";
$responseRC = api_curl($circle_url,$post_fields,$headerArray);
$resRC = json_decode($responseRC,true);
?>
<?php
	if($resRC['ERROR_CODE']==0 && isset($resRC['DATA']) && count($resRC['DATA']) > 0){
		foreach($resRC['DATA'] as $k=>$row){
?> 
					<tr>
					  <td><?=$k+1?></td>
					  <td class="dataBank"><?=$row['bank_name']?></td>
					  <td class="dataAC"><?=$row['account_number']?></td>
					  <td class="dataIFSC"><?=$row['ifsc']?></td>
					  <td><?=$row['account_name']?></td>
					  <td class="dataAmount"><?=$row['amount']?></td>
					  <td><?=$row['transaction_id']?></td>
					  <td><?=$row['group_id']?></td>
					  <td><?=$row['settlement_date']?></td>
					  <td><?=$row['payment_date']?></td>
					  <td>
						<div style="display:block;width: 141px;"><label>IMPS: <input type="radio" name="mode1" value="IMPS" class="mode"></label> &nbsp; | &nbsp; <label>NEFT: <input type="radio" name="mode1" value="NEFT" class="mode" checked=""></label></div>
						<a href="javascript:void(0);" class="btn btn-sm btn-success" onclick="get_settlementDetails('<?=$row['group_id']?>','<?=$row['id']?>',this)"><i class="fa fa-eye"></i> Payout </a>
					  </td>
					</tr>
<?php
		}
	} else {
		echo "<tr><td colspan='10'>".$resRC['MESSAGE']."</td></tr>";
	}
?>					
					</tbody>
					<tfoot>

					</tfoot>
				  </table>
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
$(document).ready(function() {
    var table = $('#example1').DataTable( {
        lengthChange: false,
        buttons: [ 'copy', 
				{extend: 'excel', title: '<?=str_replace(".php","",basename($_SERVER['PHP_SELF']))?>'},
				{extend: 'pdf', title: '<?=str_replace(".php","",basename($_SERVER['PHP_SELF']))?>'},
				{extend: 'colvis',  text: 'More'} ],
		columnDefs: [
            {
                targets: [-1],
                visible: true
            }
        ]
    } );
 
    table.buttons().container()
        .appendTo( '#example1_wrapper .col-md-6:eq(0)' );
} );
function get_settlementDetails(gid,id,e){
			var select = e;
			var mode =  $(select).parent('td').find('input[type="radio"]:checked').val(); 					
			var account = $(select).parent('td').parent('tr').children('.dataAC').text();
			var amount = $(select).parent('td').parent('tr').children('.dataAmount').text();
			var ifsc = $(select).parent('td').parent('tr').children('.dataIFSC').text();
			$(select).parent('td').html('');
//alert(ifsc); return false;
			if(account=='' || amount=='' || ifsc=='' || mode == ''){
				alert('Some details missing!');
			} 
			else {
				var htmlText = "Amount : " + amount + "<br>" + "Account : " + account + "<br>" + "IFSC : " + ifsc;
				Swal.fire({
				  title: "PayOut",
				  html: htmlText,
				  type: "success",
				  showCancelButton: true,
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "Ok",
				  closeOnConfirm: false
				}).then((result) => {
					if (result.value) {						
						$.ajax({
							type: 'POST',
							data: {account:account,amount:amount,ifsc:ifsc,id:id,gid:gid,mode:mode},
							cache: false,
							url: 'ajax/aeps/payout.php',
							success: function (response)
							{ 
								res = JSON.parse(response);
								if(res.ERROR_CODE==0 ){									
									Swal.fire({
										  title: "Transaction Status.",
										  html: res.MESSAGE,
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
								}
							}
						}); 								
					}		
				});	
			}
	 
			
		}
</script>
</html>
