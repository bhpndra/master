<?php include_once('inc/head.php'); ?>
<?php include_once('inc/header.php'); ?>
<?php
	include_once('classes/user_class.php'); 
	$mysqlObj = new mysql_class();
	$helper   = new helper_class();
	$userClass = new user_class();
	
$now = new DateTime('now');
$month = $now->format('m');

	$filterBy = $helper->clearSlashes($_GET);
	
				
	if(isset($filterBy['monthFilter'])&& $filterBy['monthFilter']!=""){
		$month = $filterBy['monthFilter'];
	}

?>
        <div class="page-wrapper-row full-height">
            <div class="page-wrapper-middle">
                <!-- BEGIN CONTAINER -->
                <div class="page-container">
                    <!-- BEGIN CONTENT -->
                    <div class="page-content-wrapper">
                        <!-- BEGIN CONTENT BODY -->
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
                                        <span>All Payments</span>
                                    </li>
                                </ul>

                                <!-- END PAGE BREADCRUMBS -->
								<div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-cogs"></i>Filter</div>
                                                </div>
												<div class="portlet-body">
												<div class="row">
													<form method="get" >															
															<div class="form-group col-md-3">
																<label>Month</label>
																<div>
																	<select name="monthFilter" class='form-control' >
																	<?php for($m=1;$m<=12;$m++){  $mont = sprintf("%02d", $m) ?>
																		<option <?php if($month==$m){ echo "selected"; } ?>  value="<?=$mont?>"><?=$mont?></option>
																	<?php } ?>
																	</select>
																</div>
															</div>
															<div class="form-group col-md-3">
																<label style="opacity:0">Filter</label>
																<div>
																	<input type="submit" id="" name="filter" value="Filter" class="btn btn-primary"> 
																</div>
															</div>
														</form>
													</div>
												</div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
								
							
                                <!-- BEGIN PAGE CONTENT INNER -->
                                <div class="page-content-inner">
                                    <div class="row">
                                        <div class="col-lg-12 col-xs-12 col-sm-12">
                                            <div class="portlet box blue">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                       <i class="fa fa-cogs"></i>Report
                                                   </div>
                                                </div>
                                                <div class="portlet-body">
							    <table id="datatable_col_reorder" class="display table table-striped table-bordered table-hover">
									<thead>
										<tr role="row">
											<th scope="col">ID</th>
											<th scope="col">Retailer ID</th>
											<th scope="col">Transaction ID</th>
											<th scope="col">Opening Balance</th>
											<th scope="col">Withdrawl</th>
											<th scope="col">Closing Balance</th>
											<th scope="col">Date & Time</th>
											<th scope="col">Comment</th>
										</tr>
									</thead>
									<tbody>
<?php
$sql = "SELECT `id` FROM `add_cust` WHERE `id` IN (309,304,313,315,317,326,322,327,328,329,330,324,336,341,339,344,346,345,320,353,355,356,358,361,363,364,365,367,369,371,385,376,408,412,386,425,402,423,421,417,418,419,429,416,415,431,397,433,434,435,401,400,399,404,426,441,390,389,388,403,428,406,442,395,443,446,448,451,452,430,453,454,449,409,457,455,337,462,463,464,465,466,459,471,384,472,473,474,475,476,477,480,450,479,482,483,413,440,485,486,489,491,488,398,494,495,496,497,498,499,500,501,503,504,490,510,512,514,515,516,517,534,507,513,405,547,548,549,553,481,562,563,564,565,566,568,570,572,578,580,581,333,584,585,586,587,588,536,583,597,605,606,614,620,613,575,624,626,633,635,636,637,641,639,598,651,655,582,659,560,648,618,647,665,668,669,664,672,674,544,678,685,682,247,687,653,683,688,694,684,698,699,701,280,297,630,704,680,654,710,707,642,697,718,420,432,721,722,725,729,727,730,732,689,733,740,747,743,752,754,748,756,757,758,765,766,767,769,763,772,773,771,775,776,340,783,789,784,786,793,796,795,797,799,798,800,802,804,737,809,788,814,816,818,813,819,822,761,768,826,742,827,830,828,831,834,835,791,843,825,848,849,851,422,852,863,511,865,866,868,867,869,870,871,873,875,853,878,877,879,882,883,894,897,899,842,900,901,909,910,914,915,908,916,921,923,753,919,930,931,316,876,933,922,943,947,935,970,780,973,974,981,982,979,1012,1027,984,1026,1126,1131,1154,1140,1072,1340,1380,1435,1436,1294,1464,484,1509,1513,1529,1566,1576,1579,1580,1581,1594,1596,1615,1618,1622,1625,1612,1632,1633,1634,1637,1636,1641,1643,1648,1611,1644,1654,1638,1639,290,1663,1656,1670,1672,1677,1664,1686,1610,1682,1683,1688,1689,1692,1702,1668,1693,1714,1717,1720,1726,1708,1712,1732,1725,1734,1733,1697,1713,1715,1719,1739,1736,1746,1753,1752,1772,1776,1757,1778,1755,1754,1805,1804,1721,1802,1667,1684,1815,1788,1816,1819,1813,1812,1837,1842,1856,1861,1862,1874,1817,1882,1885,1847,1869,1886,1735,1881,1880,1687,1906,1835,1908,1914,1918,1903,1923,1924,1929,1937,1933,1934,1936,1920,1907,1925,1942,1944,1948,1930,1939,1955,1931,1921,1956,1943,1965,1967,1972,1970,1973,1977,1978,1981,1980,1987,1946,1990,1989,1983,1994,1996,1993,2002,2004,2008,2014,2017,2015,2019,2020,2021,2018,1992,1940,2028,2034,2026,2037,2041,2040,2032,2045,2049,2058,2060,2065,2076,2078,2082,2087,2088,2090,2095,2098,2097,2100,2103,2104,2099,2105,2089,2113,2083,2114,1997,2119,2125,2126,1876,2127,2133,2123,2140,2145,2147,2149,2150,2151,2152,2153,2154,2146,2155,2156,2157,2160,2163,2165,2166,2168,2176,2172,2179) ORDER BY `id` ASC ";

	$sqlQuery = $mysqlObj->mysqlQuery($sql);			

				while($userRows = $sqlQuery->fetch(PDO::FETCH_ASSOC)){ 
				 $agent_id = $userRows['id'];
				 
				 $sql_1 = "SELECT `id`,`withdrawl`,`balance`,`transaction_id`,`retailer_id`,`date_created`,`comments` FROM `retailer_trans` WHERE `retailer_id`='$agent_id' && `withdrawl`>10 && MONTH(`date_created`)='$month' && YEAR(`date_created`)='2019' ORDER BY `id` DESC";
				 
				  echo '<tr role="row" class="odd"> 
						<td class="expand" colspan="8">------------------------------------------- [ May 2019 Transaction Start Retailer - '. $agent_id .' ] ------------------------------------------------</td>
					</tr>';
				
					$sqlQuery_1 = $mysqlObj->mysqlQuery($sql_1);	
					while($rows = $sqlQuery_1->fetch(PDO::FETCH_ASSOC)){ 
					
						$id                     = $rows['id'];
						$transaction_id         = $rows['transaction_id'];
						$withdrawl              = $rows['withdrawl'];
						(float) $current_bal    = $rows['balance'];
						$retailer_id            = $rows['retailer_id'];
						$transaction_date       = $rows['date_created'];
						$comment                = $rows['comments'];
						
						$sql_2 = "SELECT `id`,`retailer_id`,`transaction_id`,`balance` FROM `retailer_trans` WHERE id<$id AND retailer_id=$agent_id ORDER BY id DESC LIMIT 1";
						
						$count = $mysqlObj->countRows($sql_2);
						if($count==0){
							
						} else {
							$sqlQuery_2 = $mysqlObj->mysqlQuery($sql_2);
							$prev_bal = $sqlQuery_2->fetch(PDO::FETCH_ASSOC);
							
							$prev_id            = $prev_bal['id'];
							$prevtransaction_id = $prev_bal['transaction_id'];
							$prevbal            = $prev_bal['balance'];
							if ($prevbal) {

							$diff               = $prevbal - (float) $withdrawl;
							$difference         = round($diff, 2);
							$current_balance    = round($current_bal, 2);

							if ( $current_balance > $difference ) {
		?>                      
								<tr role="row" class="odd">
									<td class=" expand"><?php echo $id; ?></td>
									<td class=" expand"><?php echo $agent_id; ?></td>
									<td class="sum"><?php echo $transaction_id; ?></td>
									<td width="150px;"><?php echo $prevbal; ?></td>
									<td width="150px;"><?php echo $withdrawl; ?></td>
									<td width="150px;"><?php echo $current_bal; ?></td>
									<td> <?php echo date('d/m/Y H:i:s', strtotime($transaction_date)); ?></td>
									<td width="150px;"><?php echo $comment; ?></td>
								</tr>
		<?php


								} else {

								}
							}
						}
					}
				
				} 
			?>
												</tbody>
												</table>
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


</body>
</html>
</html>