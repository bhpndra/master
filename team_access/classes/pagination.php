<?php
class Pagin{
	function pagination($pagiConfig){

	$page = empty($pagiConfig['current_page']) ? 1 : $pagiConfig['current_page'] ;
	$limit = empty($pagiConfig['per_page_items']) ? 0 : $pagiConfig['per_page_items'] ;
	$total_rows = empty($pagiConfig['total_rows']) ? 0 : $pagiConfig['total_rows'] ;
	$baseUrl = empty($pagiConfig['base_url']) ? $_SERVER['PHP_SELF']."?" : $pagiConfig['base_url'] ;
	
	
	$records_per_page = $limit; 
	$from_record_num = ($records_per_page * $page) - $records_per_page; 
	
		$paginate ="<ul class=\"pagination\">";

		// button for first page
		if($page>1){
			$paginate .="<li><a href=' " . htmlspecialchars($baseUrl) . " ' title='Go to the first page.'>";
			$paginate .=" << First ";
			$paginate .="</a></li>";
		}


		// Returns the next highest integer value by rounding up value if necessary. 18/5=3,6 ~ 4
		$total_pages = ceil($total_rows / $records_per_page); //ceil — Round fractions up

		// range of num of links to show
		$range = 2;

		// display number of link to 'range of pages' and wrap around 'current page'
		$initial_num = $page - $range;
		$condition_limit_num = ($page + $range) + 1;


		for ($x=$initial_num; $x<$condition_limit_num; $x++) {

			// setting the current page
			if (($x > 0) && ($x <= $total_pages)) {

				// display current page
				if ($x == $page) {
					$paginate .="<li class='active'><a href=\"#\">$x <span class=\"sr-only\">(current)</span></a></li>";
				}

				// not current page
				else {
					$paginate .="<li><a href='" . htmlspecialchars($baseUrl) . "&page=$x'>$x</a></li>";
				}
			}
		}

		// button for last page
		if($page<$total_pages){
			$paginate .="<li><a href='" . htmlspecialchars($baseUrl) . "&page={$total_pages}' title='Last page is {$total_pages}.'>";
			$paginate .="Last >> ";
			$paginate .="</a></li>";
		}

		$paginate .="</ul>";
		
		return array("pagination"=>$paginate,"offset"=>$from_record_num);
	}
}





