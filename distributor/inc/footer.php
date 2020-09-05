  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- Default to the left -->
    <strong>Copyright &copy; 2020 <a href="<?=($siteDetails['domain'])? $siteDetails['domain'] : DOMAIN_NAME?>"><?=($siteDetails['site_title'])? $siteDetails['site_title'] : SITE_TITLE?></a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="<?=BASE_URL?>dashboard/plugins/jquery/jquery.min.js"></script>
<!-- Select2 -->
<script src="<?=BASE_URL?>dashboard/plugins/select2/js/select2.full.min.js"></script>
<script src="<?=BASE_URL?>dashboard/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=BASE_URL?>dashboard/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?=DOMAIN_NAME?>dashboard/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>

<script src="<?=BASE_URL?>dashboard/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?=BASE_URL?>dashboard/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?=BASE_URL?>dashboard/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?=BASE_URL?>dashboard/plugins/chart.js/Chart.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=BASE_URL?>dashboard/dist/js/adminlte.min.js"></script>
<script>
$(function () {
	$('.select2').select2();
	
	//var currentPage = $(location).attr('pathname').split('/').slice(-1)[0]; //alert(currentPage);
	var currentUrl = $(location).attr('href').split("?")[0];	
	$('.nav-sidebar a').each(function() {
		  if (this.href === currentUrl) {
			$(this).addClass('active');
			$(this).closest("ul.nav-treeview").css({"display": "block"});
			$(this).closest("li.has-treeview").addClass('menu-open');
		}
	 });
	$("a[id^=show_]").click(function(event) {
		$("#extra_" + $(this).attr('id').substr(5)).slideToggle("slow");
		event.preventDefault();
	})	 
});
$(function () {
	$.ajax({
		type: 'POST',
		cache: false,
		url: 'ajax/get_user_balance.php',
		success: function (response)
		{ 
			var data = JSON.parse(response);
			$("#navWB").html(data.BALANCE.WALLET);
			$("#navAB").html(data.BALANCE.AEPS);
		}
	});
});
</script>
</body>