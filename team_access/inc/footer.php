        <div class="page-wrapper-row">
            <div class="page-wrapper-bottom">
                <div class="page-footer">
                    <div class="container"> 2019 &copy;
                        <a target="_blank" href="#">Netpaisa</a>
                    </div>
                </div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>
    <script src="new-js/jquery.min.js" type="text/javascript"></script>
    <script src="new-js/bootstrap.min.js" type="text/javascript"></script>
    <script src="new-js/js.cookie.min.js" type="text/javascript"></script>
    <script src="new-js/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="new-js/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="new-js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="new-js/jquery.bdt.js" type="text/javascript"></script>
    <script src="new-js/jquery.bdt.min.js" type="text/javascript"></script>
    <script src="new-js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="new-js/jszip.min.js" type="text/javascript"></script>
    <script src="new-js/jpdfmake.min.js" type="text/javascript"></script>
    <script src="new-js/vfs_fonts.js" type="text/javascript"></script>
    <script src="new-js/buttons.html5.min.js" type="text/javascript"></script>
    <script src="new-js/pdfmake.min.js" type="text/javascript"></script>
    <script src="new-js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="new-js/sum().js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="new-js/moment.min.js" type="text/javascript"></script>
    <script src="new-js/daterangepicker.min.js" type="text/javascript"></script>
    <script src="new-js/morris.min.js" type="text/javascript"></script>
    <script src="new-js/raphael-min.js" type="text/javascript"></script>
    <script src="new-js/jquery.waypoints.min.js" type="text/javascript"></script>
    <script src="new-js/jquery.counterup.min.js" type="text/javascript"></script>
    <script src="new-js/amcharts.js" type="text/javascript"></script>
    <script src="new-js/amstock.js" type="text/javascript"></script>
    <script src="new-js/fullcalendar.min.js" type="text/javascript"></script>
    <script src="new-js/horizontal-timeline.js" type="text/javascript"></script>
    <script src="new-js/jquery.flot.min.js" type="text/javascript"></script>
    <script src="new-js/jquery.flot.resize.min.js" type="text/javascript"></script>
    <script src="new-js/jquery.flot.categories.min.js" type="text/javascript"></script>
    <script src="new-js/jquery.easypiechart.min.js" type="text/javascript"></script>
    <script src="new-js/jquery.sparkline.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="new-js/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="new-js/dashboard.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="new-js/layout.min.js" type="text/javascript"></script>
    <script src="new-js/demo.min.js" type="text/javascript"></script>
    <script src="new-js/quick-sidebar.min.js" type="text/javascript"></script>
    <script src="new-js/quick-nav.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
        		
        		$(".nav-link").click(function() { 
        			
        			
        			swal({
        				title: "Please Wait",
        				text: "Your request is being processed",
        				type: "info",
						timer: 2000,
        				allowEscapeKey : false,
        				showConfirmButton : false
        			});
        		});
				$("#refAdmBal").click(function(){ 					
					getAdminBalance();
				});
				getAdminBalance();
        	});
			function getAdminBalance(){
				$("#refAdmBal").toggleClass("fa-spin");
				$.ajax({
					cache: false,
					url: "ajax/get_admin_balance.php",
					success: function (response) {
						$("#adminBalance").html(response);	
						$("#refAdmBal").toggleClass("fa-spin");
					}
				});			
			}
    </script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
	<script>
		$(document).ready(function () {
		// Initialize select2
		$(".select2").select2();
		});
	</script>
    <!-- END THEME LAYOUT SCRIPTS -->