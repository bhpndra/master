<?php
include_once('inc/head.php');
include_once('inc/header.php');

?>
<?php
	$helpers = new helper_class();
	$mysqlClass = new Mysql_class();
	$get = $helpers->clearSlashes($_GET);
	
	if(isset($_POST['addPage'])){
		$post = $helpers->clearSlashes($_POST);
		unset($post['addPage']);
		$check = $mysqlClass->countRows("select page_name from support_access_pages where page_name = '".$post['page_name']."' ");
		if($check>0){
			echo "<script> alert('Page already available in table '); </script>";
		} else {
			$pgIns = $mysqlClass->insertData('support_access_pages',$post);
			if($pgIns>0){
				echo "<script> alert('Page added successfully! '); window.location = 'edit-role.php?id=".$get['id']."' </script>";
			}
		}
		
	}
	if(isset($_POST['submit'])){
		$post = $helpers->clearSlashes($_POST);
		$data = array('role_name'=>$post['role_name'],'access_pages'=>implode(",",$post['access_pages']));
		$pgIns = $mysqlClass->updateData('support_roles',$data," where id = '".$get['id']."' ");
		if($pgIns>0){
			echo "<script> alert('Role updated successfully! '); window.location = 'edit-role.php?id=".$get['id']."' </script>";
		}
	}
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
								<span>Update Role</span>
							</li>
						</ul>
						
						<div class="page-content-inner">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs"></i> Update Role </div>
							</div>
							<div class="portlet-body">
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12">
								<?php
									if(@$msg!=""){
										echo $msg;
									}
								$res = $mysqlClass->get_field_data("role_name,access_pages","support_roles"," where id = '".$get['id']."' ");
								?>														
                                <div class="col-md-12 ">
									<form method ="post" class="smart-form" action ="">													
										<div class="form-group col-md-4">
											<label>Role Name</label>
											<div>
												<input type='text' name='role_name' value="<?=$res['role_name']?>" class='form-control' placeholder="Role Name" required>
											</div>
										</div>
										<div class="form-group col-md-6">
											<label>Select Page for access</label>
											<input type="text" id="myInput" class='form-control' onkeyup="searchPage()" placeholder="Search for page.." >
											<div>
												<div id="allPages" class='form-control' style="height:200px;overflow-y:scroll">
													<?php
														$utypes = $mysqlClass->fetchAllData("support_access_pages","*", " where id in (".$res['access_pages'].") order by page_name asc ");														
															foreach($utypes as $ut){
													?>
															<label style="width:100%;cursor:pointer;border-bottom:1px solid #eee" >
																<input <?php if($ut['id']=="1"){ echo 'onclick="return false;"'; } ?>  type="checkbox" checked value="<?=$ut['id']?>" name="access_pages[]" /> &nbsp; <?=$ut['page_name']?> (<?=$ut['page_title']?>)
															</label>
													<?php 	}  ?>
													<?php
														$utypes1 = $mysqlClass->fetchAllData("support_access_pages","*", " where id not in (".$res['access_pages'].") order by page_name asc ");														
															foreach($utypes1 as $ut1){
													?>
															<label style="width:100%;cursor:pointer;border-bottom:1px solid #eee" >
																<input <?php if($ut1['id']=="1"){ echo 'onclick="return false;"'; } ?>  type="checkbox" value="<?=$ut1['id']?>" name="access_pages[]" /> &nbsp; <?=$ut1['page_name']?> (<?=$ut1['page_title']?>)
															</label>
													<?php 	}  ?>
												</div>
												<a href="#" data-toggle="modal" data-target="#myModal">Add new page for access</a>
											</div>
										</div>
										<div class="form-group col-md-12">
											<div>
												<input type="submit" name="submit" class="btn btn-success" value="Update Role">
											</div>
										</div>

									</form>
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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title w-100 font-weight-bold">Add Page for support access</h4>
      </div>
      <div class="modal-body mx-3">
	  <form method="post">
        <div class="md-form mb-5">
          <label>Page Name</label>
          <input type="text" class="form-control " name="page_name" placeholder="Exapmle: add-role.php" required >
		  <p style="margin: 0px;color: #9b9b9b;">https://www.netpaisa.com/########/<span style="color: #000;background: yellow;">add-role.php</span></p>
		</div>
		<div class="md-form mb-5">
          <label>Page Title</label>
          <input type="text" class="form-control " name="page_title" placeholder="Page Title (Menu Name)" required >		 
		</div>
      </div>
      <div class="modal-footer d-flex justify-content-center">
        <button type="submit" name="addPage" class="btn btn-primary pull-left">Submit</button>
        <button data-dismiss="modal" aria-label="Close" class="btn btn-danger">Cancel</button>
      </div>
	 </form>
    </div>
  </div>
<script>
function searchPage() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    ul = document.getElementById("allPages");
    li = ul.getElementsByTagName("label");
    for (i = 0; i < li.length; i++) {
        a = li[i];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}
</script>
</body>
</html>