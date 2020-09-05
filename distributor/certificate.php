<?php
	session_start(); 
	include("../config.php");
	include("../include/lib.php");
	require("../api/classes/db_class.php");
	require("../api/classes/comman_class.php");
	
	$helpers = new Helper_class();
	$mysqlClass = new mysql_class();
	
if(isset($_SESSION['TOKEN'])){
	$url = BASE_URL."/api/users/get_token_details.php";
	$post_fields = array("token"=>$_SESSION['TOKEN']);
	$responseVT = api_curl($url,$post_fields,$headerArray);
	$resVT = json_decode($responseVT,true);
	//die();
	/*if($resVT['ERROR_CODE']==1){
		header("location:../logout.php"); die();
	}
	if($resVT['ERROR_CODE']==0){
		header("location:../logout.php"); die();
	}*/
} else {
	header("location:../logout.php"); die();
}
$url = BASE_URL."/api/get-software-details.php";
$responseSiteDetails = api_curl($url,$post_fields,$headerArray);
$resSTDetails = json_decode($responseSiteDetails,true);
if($resSTDetails['ERROR_CODE']==0){
	$siteDetails = $resSTDetails['DATA'];
	//print_r($siteDetails);
}


$ADMIN_ID = $resVT['DATA']['ADMIN_ID'];  // $resVT defined on head.php
$WL_ID = $resVT['DATA']['WL_ID'];
$USER_ID = $resVT['DATA']['USER_ID'];

if(!isset($USER_ID) || empty($USER_ID)){ die(); }


$general_setting = $mysqlClass->mysqlQuery("select cname from admin WHERE `id`='" . $ADMIN_ID . "' ")->fetch(PDO::FETCH_ASSOC);
$site_name = $general_setting['cname'];

$user = $mysqlClass->mysqlQuery("select * from add_cust WHERE `id`='" . $USER_ID . "' ")->fetch(PDO::FETCH_ASSOC);
$id = $user['user'];
$name = $user['name'];
$address = $user['address'];
$created_on = $user['created_on'];
$cname = $siteDetails['site_name'];
$domain = $siteDetails['domain'];
$logo_src = $siteDetails['logo'];
if($logo_src!=''){
	$logo = '<img src="'.$logo_src.'" width="120px" />';
} else {
	$logo = '';
}
// Include the main TCPDF library (search for installation path).
require_once('tcpdf_master/examples/tcpdf_include.php');
class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		// get the current page break margin
		$bMargin = $this->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $this->AutoPageBreak;
		// disable auto-page-break
		$this->SetAutoPageBreak(false, 0);
		// set bacground image
		$img_file = '../uploads/certificate-bg.jpg';
		//$this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);  //for portrait
		$this->Image($img_file, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);  //for landscape
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$this->setPageMark();
		
	}
}
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------
$pdf->SetMargins(26, 10, 26, 0);
// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

/* NOTE:
 * *********************************************************
 * You can load external XHTML using :
 *
 * $html = file_get_contents('/path/to/your/file.html');
 *
 * External CSS files will be automatically loaded.
 * Sometimes you need to fix the path of the external CSS.
 * *********************************************************
 */

// define some HTML content with style
$html = <<<EOF
<!-- EXAMPLE OF CSS STYLE -->
<style>
	.certificate-body{
		/* height: 600px; */
		width: 848px;
		display:block;
	}
	.certificate-body p{
		font-size: 18px;
		line-height: 25px;
		color: #464646;
		text-align:justify
	}
	.certificate-body p strong{
		color: #000;
	}
	.heading{
		display:block;
		text-align:center;
		font-size: 18px;
	}
	.header{
		text-align:center;
	}
</style>
	<table >
	<tr>
		<td class="header">
			$logo
			<p class="heading"><strong>Certificate of Distributor</strong></p>
		</td>
	</tr>
	<tr>
		<td>
		<div class="certificate-body">
				<p>
This is to certify that <strong>$name</strong> ID $id Located at place (<strong>$address </strong>)
Has been appointed as Registered Distributor of $cname for Providing Essential Service Such As AEPS through $cname Portal ($domain)
This certificate will be valid from date of Issuance </p>

<p>$cname is working as channel partner with $site_name who are working with Corporate Business Correspondent working for Banks ( ICICI bank and Paytm Payment Bank ) as per the Reserve Bank of India guidelines.</p>
<p>This Certificate is valid for 1 Year from the date of issuance.<br/>
				Date of Issue:- $created_on	</p>
		</div>
		</td>
	</tr>
	</table>
EOF;

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Certificate.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
