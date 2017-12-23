<?php
function get_token($length) {
	$str = "";
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$size = strlen( $chars );
	for( $i = 0; $i < $length; $i++ ) {
		$str .= $chars[ rand( 0, $size - 1 ) ];
	}
	return $str;
}
function get_ip(){ 
	$ip = $_SERVER['REMOTE_ADDR']; 
	return $ip;
}

function captcha_check($response, $secretkey) {
	$Captcha_url = 'https://www.google.com/recaptcha/api/siteverify';
	$Captcha_data = array('secret' => $secretkey, 'response' => $response);
	$Captcha_options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($Captcha_data),
		),
	);
	$Captcha_context  = stream_context_create($Captcha_options);
	$Captcha_result = file_get_contents($Captcha_url, false, $Captcha_context);
	return $Captcha_result;
} 

function checkaddress($address) {
	global $mysqli;
	global $time;
	global $faucet;
	$check = $mysqli->query("SELECT * FROM address_list WHERE bitcoin_address = '$address'");
	if ($check->num_rows == 1) {
		$check = $check->fetch_assoc();
		$time_claim = $check['last'];
		$rmn = $time - $time_claim;
		if ($rmn > $faucet['timer']) {
			return 'ok';
		} else {
			$wait  = $time_claim + $faucet['timer'] - $time;
			return $wait;
		}	
	} else {
		return 'ok';
	} 
	return 'no';
}

function checkip($ip) {
	global $mysqli;
	global $time;
	global $faucet;
	$check = $mysqli->query("SELECT * FROM ip_list WHERE ip_address = '$ip'");
	if ($check->num_rows == 1) {
		$check = $check->fetch_assoc();
		$time_claim = $check['last'];
		$rmn = $time - $time_claim;
		if ($rmn > $faucet['timer']) {
			return 'ok';
		} else {
			$wait  = $time_claim + $faucet['timer'] - $time;
			return $wait;
		}	
	} else {
		return 'ok';
	} 	
	return 'no';
}

function his($faucethub_api) {
	$param = array(
		'api_key' => $faucethub_api,
		'count' => '10'
	);
	$url = 'https://faucethub.io/api/v1/payouts';
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, count($param));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $param); 

	$result = curl_exec($ch);

	curl_close($ch);
	$jsonhis = json_decode($result, TRUE);
	return $jsonhis['rewards'];
}

function iphub($iphub_api) {
	global $ip;
	global $mysqli;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, 'http://v2.api.iphub.info/ip/'.$ip);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Key: ' . $iphub_api));
	$result = curl_exec($ch);
	curl_close($ch);
	$obj = json_decode($result, true);
	if ($obj['block'] == '1') {
		$mysqli->query("INSERT INTO ip_blocked (address) VALUES ('$ip')");
		return 'bad';
	}
}

function get_info($id) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM settings WHERE id = '$id'")->fetch_assoc()['value'];
	return $result;
}

function log_user($address, $ip) {
	global $time;
	global $mysqli;
	$ref ='';
				// save a log of address
	$log_address = $mysqli->query("SELECT * FROM address_list WHERE bitcoin_address = '$address'");
	if ($log_address->num_rows == 1) {
		$mysqli->query("UPDATE address_list SET last = '$time' WHERE bitcoin_address = '$address'");
	} else {
		$mysqli->query("INSERT INTO address_list (bitcoin_address, ref, last) VALUES ('$address', '$ref', '$time')");
	}
                // save a log of ip
	$log_ip = $mysqli->query("SELECT * FROM ip_list WHERE ip_address = '$ip'");
	if ($log_ip->num_rows == 1) {
		$mysqli->query("UPDATE ip_list SET last = '$time' WHERE ip_address = '$ip'");
	} else {
		$mysqli->query("INSERT INTO ip_list (ip_address, last) VALUES ('$ip', '$time')");
	} 

}
function check_blocked_address($address) {
	global $mysqli;
	$check = $mysqli->query("SELECT * FROM address_blocked WHERE address='$address' LIMIT 1");
	if ($check->num_rows == 1) {
		return 'blocked';
	}
}

function check_blocked_ip($ip) {
	global $mysqli;
	$check = $mysqli->query("SELECT * FROM ip_blocked WHERE address='$ip' LIMIT 1");
	if ($check->num_rows == 1) {
		return 'blocked';
	}
}
?>