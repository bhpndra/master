<?php //session_start();
require 'jwt.php';
class jwt_encode_decode{
	function encode_token($data){
			/**************************************************************/
						/*              Token              */
			/**************************************************************/
			define('SECRET_KEY','RECHARGE-DMT-ROCK2017') ;
			define('ALGORITHM', 'HS512');
			
	//echo base64_encode(random_bytes(32));		
			$tokenId    = base64_encode(random_bytes(32));
			$issuedAt   = time();


			/*
				* Create the token as an array
			*/
			$data = [
			'iat'  => $issuedAt,         // Issued at: time when the token was generated
			'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
			'c_time'  => time(),          // Json Token Id: an unique identifier for the token
			'data' => $data
			];
			$secretKey = base64_decode(SECRET_KEY);
			
			/// Here we will transform this array into JWT:
			$jwt = JWT::encode($data, $secretKey, ALGORITHM ); 
			
			 //$unencodedArray = ['jwt' => $jwt];
			 $jwt = str_replace('\"','', json_encode($jwt));
			 $jwt = str_replace('"','', $jwt);
			 
			 //on success
			return $jwt;
	}
	function decode_token($user_token){
		
		try {
				define('SECRET_KEY','RECHARGE-DMT-ROCK2017') ;
				define('ALGORITHM', 'HS512');
				$secretKey = base64_decode(SECRET_KEY);
			$data = str_replace('"','',$user_token);    //token retrieved
			
			// Here we will transform this array into JWT:
			$jwt = JWT::decode(
					$data,      //Data to be encoded in the JWT
					$secretKey, // The signing key
					ALGORITHM 
					); 
			
			$tokenid = $jwt->jti;
			
			if(($jwt->c_time + (3600 * 1)) > time()){ // 3600sec = 1h
				return $jwt->data;
			} else {
				return 'Token Expired/Not Valid';
			}
		
		} catch (\Exception $e) { // Also tried JwtException
			return 'Token Expired/Not Valid';
		}
		//print_r($jwt);
		
	}
}		
?>