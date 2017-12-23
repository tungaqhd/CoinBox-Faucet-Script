<?php
session_start();
include 'config.php';
include 'solvemedia.php';
include 'sqn.php';
include 'function.php';
include 'faucethub.php';
$ip = get_ip();
$time = time();

# cache them so website is faster and save resoures
if (!isset($_COOKIE['cache'])) {
	setcookie('cache', 'cache', $time+550);
	$faucet['name'] = get_info(1);
	setcookie('name', $faucet['name'], $time+600);
	$faucet['description'] = get_info(2);
	setcookie('description', $faucet['description'], $time+600);
	$faucet['url'] = get_info(3);
	setcookie('url', $faucet['url'], $time+600);
	$faucet['theme'] = get_info(4);
	setcookie('theme', $faucet['theme'], $time+600);
	$ad['top'] = get_info(23);
	setcookie('top', $ad['top'], $time+600);
	$ad['left'] = get_info(24);
	setcookie('left', $ad['left'], $time+600);
	$ad['right'] = get_info(25);
	setcookie('right', $ad['right'], $time+600);
	$ad['above-form'] = get_info(26);
	setcookie('above-form', $ad['above-form'], $time+600);
	$ad['bottom'] = get_info(27);
	setcookie('bottom', $ad['bottom'], $time+600);
	$ad['modal'] = get_info(28);
	setcookie('modal', $ad['modal'], $time+600);
} else {
	$faucet['name'] = $_COOKIE['name'];
	$faucet['description'] = $_COOKIE['description'];
	$faucet['url'] = $_COOKIE['url'];
	$faucet['theme'] = $_COOKIE['theme'];
	$ad['top'] = $_COOKIE['top'];
	$ad['left'] = $_COOKIE['left'];
	$ad['right'] = $_COOKIE['right'];
	$ad['above-form'] = $_COOKIE['above-form'];
	$ad['bottom'] = $_COOKIE['bottom'];
	$ad['modal'] = $_COOKIE['modal'];
}

$faucet['currency'] = get_info(5);
$faucet['captcha'] = get_info(13);
$faucet['timer'] = get_info(7);
$faucet['reward'] = get_info(8);
$faucet['comission'] = get_info(10);


$balance = get_info(30);
$totalusers = $mysqli->query("SELECT COUNT(id) FROM address_list")->fetch_row()[0];

# get currency name
switch ($faucet['currency']) {
	case 'BTC':
	$currency_name = 'satoshi';
	break;
	case 'BCH':
	$currency_name = 'satoshi';
	break;
	
	default:
	$currency_name = $currency;
	break;
}
# get captcha
switch ($faucet['captcha']) {
	case 'solvemedia':
	$challkey = get_info(20);
	$captcha_display = solvemedia_get_html($challkey);
	break;
	case 'recaptcha':
	$publickey = get_info(14);
	$captcha_display = "<div class='g-recaptcha' data-sitekey='{$publickey}' style='margin-left: 3px;'></div><script src='https://www.google.com/recaptcha/api.js' async defer></script>";
	break;
	case 'bitcaptcha':
	$bit_key_www = get_info(18);
	$bit_key = get_info(16);
	$id = ((strpos($_SERVER['HTTP_HOST'],'ww.')>0)?$bit_key_www:$bit_key);
	$captcha_display = "<input type='hidden' name='sqn_captcha' id='sqn_captcha' value='Token'><button type='button' id='login_btn' class='btn btn-primary'>Load Captcha</button><script src='//static.shenqiniao.net/sqn.js?id={$id}&btn=login_btn' type='text/javascript'></script>";
	break;
}

?>