<?php

		//===============================
		//     Recharge secret info
		//===============================
		$outletid = "668";

		global $la_api;
		global $la_api_roundpay;
		global $la_apikh;

		$luid   = "7169";   //la uid
		$lpin   = "83879179";         //la password
		$txnpwd = "442217";
		$la_api = "http://www.champrecharges.com/api_users/recharge?login_id=";

		//Roundpay api credentials
		$lruid = "9899572326";   //la uid
		$lrpin = "774337";         //la password
		$la_api_roundpay = "http://roundpayapi.in/API/APIService.aspx?userid=";

		//Khushi recharge api credentials
		$luidkh = "9899572326";   //la uid
		$lpinkh = "6812";         //la password
		$la_apikh="http://khushirecharge.in/API/APIService.aspx?userid=";

		//===============================
		//     Bankit KYC Registration
		//===============================
		$np_yesbank_aeps = 'https://www.netpaisa.com/nps/api/aeps/yesbank_aeps_registartion.php';
		$np_yesbank_kyc = 'https://www.netpaisa.com/nps/api/aeps/yesbank_aeps_kyc_update.php';
		$npwl_yesbank_aeps = 'https://www.netpaisa.com/r_admin/ws/aeps/yesbank_aeps_registartion.php';
		$npwl_kyc_url = 'https://www.netpaisa.com/r_admin/ws/aeps/yesbank_aeps_kyc_update.php';


		$netpaisa_api_url = "https://www.netpaisa.com/nps/api/lprecharge";

		/* * *****************************
		 * Net Paisa API Base URL
		 * ****************************** */
		$remitter_details = "https://www.netpaisa.com/nps/api/remitter_details";
		$remitter_registration = "https://www.netpaisa.com/nps/api/remitter";
		$beneficiary_register = "https://www.netpaisa.com/nps/api/beneficiary_register";
		$bene_resend_otp = "https://www.netpaisa.com/nps/api/beneficiary_resend_otp";
		$bene_registration_verify = "https://www.netpaisa.com/nps/api/beneficiary_register_validate";
		$bene_account_verify = "https://www.netpaisa.com/nps/api/account_validate";
		$bene_delete = "https://www.netpaisa.com/nps/api/beneficiary_remove";
		$bene_delete_validate = "https://www.netpaisa.com/nps/api/beneficiary_remove_validate";
		$fund_transfer = "https://www.netpaisa.com/nps/api/transfer";
		$fund_transfer_status = "https://www.netpaisa.com/nps/api/transfer_status";
		$get_bank_details = "https://www.netpaisa.com/nps/api/bank_details";

		/* * *************************************
		 * Net Paisa api access token
		 * ************************************ */
		$api_access_key = "1d3ed868262a1503296270eeebeab3d9";

		/* * *****************************
		* Instapay INDO NEPAL API Base URL
		* ****************************** */
		$bank_branch_list = "https://www.netpaisa.com/nps/api/in_branch_list";
		$get_service_charge = "https://www.netpaisa.com/nps/api/in_service_charge";
		$validate_bank_account = "https://www.netpaisa.com/nps/api/in_validate_bank_account";
		$validate_card_account = "https://www.instantpay.in/ws/pmt/validate_card_number";
		$cash_branch_list = "https://www.instantpay.in/ws/pmt/cash_branch_list";
		$search_txn = "https://www.instantpay.in/ws/pmt/search_txn";
		$complian_txn_list = "https://www.instantpay.in/ws/pmt/compliance_txn_list";
		$verify_txn_list = "https://www.instantpay.in/ws/pmt/verify_txn";
		$send_txn = "https://www.instantpay.in/ws/pmt/send_txn";

		/* * ******************************
		 * Payments API
		 * ********************************* */
		$payment_api = "https://www.instantpay.in/ws/api/transaction";

		//=========================================
		//Instapay Bus API Base URL
		//=========================================
		$get_bus_source = "https://www.instantpay.in/ws/buses/sources";
		$get_bus_destination = "https://www.instantpay.in/ws/buses/destinations";
		$serach_buses = "https://www.instantpay.in/ws/buses/search";
		$bus_seatlayout = "https://www.instantpay.in/ws/buses/seatlayout";
		$bus_boardingpoint = "https://www.instantpay.in/ws/buses/boardingpoint";
		$bus_seatblock = "https://www.instantpay.in/ws/buses/seatblock";
		$bus_bookticket = "https://www.instantpay.in/ws/buses/bookticket";
		$get_bus_ticket = "https://www.instantpay.in/ws/buses/ticket";
		$bus_cancelcharge = "https://www.instantpay.in/ws/buses/cancellationcharge";
		$cancel_bus_ticket = "https://www.instantpay.in/ws/buses/cancelticket";
		$reconfirm_bus_fare = "https://www.instantpay.in/ws/buses/reconfirmfare";

		//=========================================
		// Outlet API Base URL
		//=========================================
		$pan_otp_req_register = "http://netpaisa.com/nps/api/aeps/outlet_registeration_otp.php";
		$outlet_register = "https://netpaisa.com/nps/api/aeps/registeration_outlet";	
		$register_outlet = "https://www.instantpay.in/ws/outlet/register";
		$verify_outlet = "https://www.instantpay.in/ws/outlet/sendOTP";
		$updateplan_outlet = "https://www.instantpay.in/ws/outlet/updatePan";
		$services_outlet = "https://www.instantpay.in/ws/outlet/outlet_services";
		$get_kyc_document_outlet = "https://www.instantpay.in/ws/outlet/requiredDocs";
		$upload_kyc_document_outlet = "https://www.instantpay.in/ws/outlet/uploadDocs";

		/* * *****************************
		 * GET PLAN API Base URL
		 * ****************************** */
		$get_plan = "https://www.instantpay.in/ws/api/plans";

		/* * *****************************
		 * BRAND VOUCHER API Base URL
		 * ****************************** */
		$brandvoucher_list = "https://www.instantpay.in/ws/ppcbv/brand_voucher";
		$brandvoucher_details = "https://www.instantpay.in/ws/ppcbv/product_detail";
		$brandvoucher_transaction = "https://www.instantpay.in/ws/ppcbv/transaction";
		$brandvoucher_transaction_status = "https://www.instantpay.in/ws/ppcbv/transaction_status";
		$resend_digitalvoucher = "https://www.instantpay.in/ws/ppcbv/resend";
		$voucher_balance = "https://www.instantpay.in/ws/ppcbv/voucher_balance";

		/* * *************************************
		 * insta api token
		 * ************************************ */
		$insta_token = "5abbb558eefd2223d68fe0c016f9d19b";

		/* * *************************************
		 * Insta (New) payment bbps API URL
		 * ************************************ */

		$service_provider_detail_url = "https://www.instantpay.in/ws/userresources/bbps_biller";

		/* * ***************************
		* AEPS API Base URL
		* **************************** */
		$aeps_outlet_register = "http://roundpayapi.in/Api/Outlet_Registration.asmx/OutletRegistration";
		$aeps_support_api = "http://roundpayapi.in/Api/Outlet_Registration.asmx/B2B_Detail";
		$aeps_outlet_status_check = "http://roundpayapi.in/Api/Outlet_Registration.asmx/Status_Check";
		$service_plus = "http://roundpayapi.in/Api/Outlet_Registration.asmx/ServicePlus";
		$aeps_kyc_registration ="http://roundpayapi.in/Api/Outlet_Registration.asmx/KYCRegistration";

		/*******************************
		 ***  Master Recharge API URL
		 ******************************/
		$master_rech_status = "http://www.champrecharges.com/api_users/status?login_id=";

?>