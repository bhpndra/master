<?php include 'include/connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include('include_front/head.php'); ?>
<body>
<div class="page-wrapper">
	<!-- <div class="preloader"></div> -->
<?php
	if($template==1){
		include('include_front/header/header_template1.php');
	} else if($template==2){
		include('include_front/header/header_template2.php');
	} else if($template==3){
		include('include_front/header/header_template3.php');
	} else if($template==4){
		include('include_front/header/header_template4.php');
	} else {
		include('include_front/header/header_template1.php');
	}
	
	
	if($template==1){
		include('include_front/home/template1.php');
	} else if($template==3){
		include('include_front/home/template2.php');
	} else if($template==3){
		include('include_front/home/template3.php');
	} else if($template==4){
		include('include_front/home/template4.php');
	} else {
		include('include_front/home/template0.php');
	}
?>
<!-- Modal -->
<div id="myModal1" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body" style="text-align: center;">
		<img src="new-year-2020.jpeg" width="100%" />
      </div>
    </div>
  </div>
</div>
<?php include('include_front/footer.php'); ?>
<script type="text/javascript">
     $(window).load(function(){
         //$('#myModal1').modal('show');
      });
</script>