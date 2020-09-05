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
            <h1 class="m-0 text-dark">Pan Card Report</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Pan Card Report</li>
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
              <h3 class="card-title">Pan Card Report</h3>
            </div>
            <!-- /.card-header -->
			<div class="card-body">
<?php
if(isset($_GET['dateFrom']) && isset($_GET['dateTo'])){
	$post_fields = $_GET;
	$dateFrom = $_GET['dateFrom'];
	$dateTo   = $_GET['dateTo'];
} else {
	$date1 	  = new DateTime('0 days ago');
	$dateFrom = $date1->format('Y-m-d');
	$dateTo   = date("Y-m-d");
}
?>
			<form method="get" class="row">
			  <div class="form-group col-md-4">
				<label>Date From</label>
				<input type="date" class="form-control" placeholder="Date From" name="dateFrom" value="<?=$dateFrom?>" />
			  </div>
			  <div class="form-group col-md-4">
				<label>Date To</label>
				<input type="date" class="form-control" placeholder="Date From" name="dateTo" value="<?=$dateTo?>" min="<?=$dateFrom?>"/>
			  </div>
			  <div class="form-group col-md-4">
				<label style="opacity:0;display: block;">Button</label>
				<button type="submit" class="btn btn-primary mr-2" >Filter</button>
				<a href="<?=str_replace(".php","",$_SERVER['PHP_SELF'])?>" class="btn btn-dark text-white" >Reset</a>
			  </div>
			</form>
			</div>
            <div class="card-body">
			<div class="table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
					  <th>#</th>                
					  <th>Name</th>
					  <th>user name</th>
					  <th>Mobile</th>
					  <th>Alternate Mobile</th>
					  <th>Email</th>
					  <th>Dob</th>
					  <th>City</th>
					  <th>State</th>
					  <th>PIN</th>
					  <th>Address</th>
					  <th>Pan NO</th>
					  <th>Aadhar NO</th>
					  <th>Status</th>
					  <th>Create Date</th>
                </tr>
<?php
$circle_url = BASE_URL."/api/pan/report.php";
$post_fields["token"] = $_SESSION['TOKEN'];
$responseRC = api_curl($circle_url,$post_fields,$headerArray);
$resRC = json_decode($responseRC,true);
//print_r($resRC);
?>
                </thead>
                <tbody>
				<?php
					if($resRC['ERROR_CODE']==0 && isset($resRC['DATA']) && count($resRC['DATA']) > 0){
						foreach($resRC['DATA'] as $k=>$row){
				?>

				<tr>
				  <td><?=$k+1?></td>
				  <td><?=$row['name']?></td>
				  <td><?=$row['user_name']?></td>
				  <td><?=$row['mobile']?></td>
				  <td><?=$row['alternate_mobile']?></td>
				  <td><?=$row['email']?></td>
				  <td><?=$row['dob']?></td>
				  <td>
				  <?php
					$res_status = $mysqlClass->fetchRow('cities',"city"," where id='".$row['city']."' ");
					echo $res_status['city'];
					?>				  
				  </td>
				  <td>
				  <?php
					$res_status = $mysqlClass->fetchRow('states',"name"," where id='".$row['state']."' ");
					echo $res_status['name'];
					?>
				  </td>
				  <td><?=$row['pin']?></td>
				  <td><?=$row['address']?></td>
				  <td><?=$row['pan_no']?></td>
				  <td><?=$row['aadhar_no']?></td>
				  <?php 
				//echo "<pre />";
				// print_r($resRC);
				  $status=$row['status'];
				  $status_name = $resRC['services_status'][$status];
				 // echo $status;
					if($row['status']==2){ $badge = 'success'; } else if($row['status']==2) { $badge = 'warning'; } else if($row['status']==3) { $badge = 'danger'; } else { $badge = 'info'; }
				  ?>
				  <td><span class="badge badge-<?=$badge?>"><?=$status_name; ?></span> 
		   
		   </td>
				  <td><?=$row['create_date']?></td>
				</tr>
				<?php
						}
					} else {
						echo "<tr><td colspan='15'>".$resRC['MESSAGE']."</td></tr>";
					}
				?>
                </tbody>
                <tfoot>

                </tfoot>
              </table>
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
$(document).ready(function() {
    $("input[name='dateFrom'").change(function() {
    var dateFrom = $(this).val(); //alert(dateFrom);
    $("input[name='dateTo'").val(dateFrom);
    $("input[name='dateTo'").attr("min",dateFrom);
  });
    var table = $('#example1').DataTable( {
        lengthChange: false,
       buttons: [ 'copy', 
				{extend: 'excel', title: '<?=str_replace(".php","",basename($_SERVER['PHP_SELF']))?>'},
				{extend: 'pdf', title: '<?=str_replace(".php","",basename($_SERVER['PHP_SELF']))?>'},
				{extend: 'colvis',  text: 'More'}
				]
    } );
 
    table.buttons().container()
        .appendTo( '#example1_wrapper .col-md-6:eq(0)' );
} );

/*
------------------------------------------------------------------
Sweet alert Written by Shivendra 

*/
function raise_dispute(trans_id){
      swal.fire({
      title:"Write your dispute here",
      text:"",
      text: 'Transaction id: ' + trans_id,
      input: 'textarea',
      showCancelButton: true,
      closeOnConfirm: true,
      animation: "slide-from-top",
      inputPlaceholder: "please write your dispute here"
    }).then((result) => {
          if (result.value) {
            if (result.value!='') {
              //alert(result.value);
                $.ajax({
                  url :  '<?=DOMAIN_NAME?>retailer/ajax/dispute.php',
                  type:  'POST',
                  cache: false,
                  data :  {agent_trid:trans_id, dispute_msg:result.value,dispute_type:'BBPS'},
                 
                  success:function(data)
                  {
                    //alert(data);
                    //console.log(data);         
                    var res = JSON.parse(data);
                    if(res.ERROR_CODE=="0") {
                      Swal.fire(
                          'Success',
                          res.MESSAGE,
                          'success'
                        )
                    } else {
                      Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: res.MESSAGE
                      })
                    }
                  }
                });
            
            } else {
              Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Dispute massage can`t blank!'
                      })
            }
          }
        }); 
   } 
                               
function get_status(trans_id){
		Swal.fire({
			title: "Please Wait",
			text: "Your request is being processed",
			type: "info",
			allowEscapeKey: false,
			showConfirmButton: false
		});
		$.ajax({
			type: 'POST',
			data: {agent_tranid:trans_id},
			cache: false,
			url: 'ajax/bbps/get_status.php',
			dataType: 'text',
			success: function (response)
			{
				console.log(response);							
				var data = JSON.parse(response);
				if (data.ERROR_CODE == 0) { 
					Swal.fire({
						title: "Status",
						text: 'TRANSACTION ' + data.MESSAGE,
						type: "success",
						closeOnConfirm: false,
						timer: 3000
					}).then((result) => {
						if (result.value) {
							//location.reload();
						  }
					});						
				} else { 
					Swal.fire({
						title: 'Status',
						text: '' + data.MESSAGE,
						confirmButtonColor: "#2196F3",
						type: "error"
					}).then((result) => {
						if (result.value) {
							//location.reload();
						  }
					});	
				}
			}
		});
}
</script>
</html>
