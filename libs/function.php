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

function bitcaptcha($chall, $vali, $sec) {
	global $bitcaptcha;
	global $address;
	include 'bitcaptcha/class.geetestlib.php';
	$GtSdk = new GeetestLib($bitcaptcha['id'], $bitcaptcha['key']);
	$data = array(
		"user_id" => $address,
		"client_type" => "web",
		"ip_address" => $_SERVER['REMOTE_ADDR']
	);
	if($_SESSION['gtserver'] == 1){
		$result = $GtSdk->success_validate($chall, $vali, $sec, $data);
		if ($result) {
			return 'ok';
		} else{
			return 'ko';
		}
	}else{
		if ($GtSdk->fail_validate($chall, $vali, $sec)) {
			return 'ok';
		}else{
			return 'ko';
		}
	}
}

function captcha_check($response) { 
	// from Salmen script
	global $secretkey; 
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
		if ($rmn > $faucet['time']) {
			return 'ok';
		} else {
			$wait  = $time_claim + $faucet['time'] - $time;
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
		if ($rmn > $faucet['time']) {
			return 'ok';
		} else {
			$wait  = $time_claim + $faucet['time'] - $time;
			return $wait;
		}	
	} else {
		return 'ok';
	} 	
	return 'no';
}

function iphub() {
	global $ip;
	global $iphub_api;
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
