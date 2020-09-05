<?php
function api_curl($url, $post_fields, $headerArray) { // $headerArray = array( "x-api-key: 12121dss", "HTTP_NETPASS: fsfdasdf" );
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 120,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => $post_fields,
			  CURLOPT_HTTPHEADER => $headerArray,
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			if ($err) {
				echo "cURL Error #:" . $err;
			} else {
				return $response;
			}
		}

?>