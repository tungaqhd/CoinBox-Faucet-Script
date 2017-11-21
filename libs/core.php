<?php
session_start();
include 'config.php';
include 'solvemedia.php';
include 'sqn.php';
include 'function.php';
$ip = get_ip();
$time = time();

switch ($currency) {
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

switch ($faucet['captcha']) {
	case 1:
	$captcha_display = solvemedia_get_html($your_challenge_key);
	break;
	case 2:
	$captcha_display = "<div class='g-recaptcha' data-sitekey='{$publickey}' style='margin-left: 3px;'></div><script src='https://www.google.com/recaptcha/api.js' async defer></script>";
	break;
	case 3:
	$id = ((strpos($_SERVER['HTTP_HOST'],'ww.')>0)?$bitcaptcha_id_www:$bitcaptcha_id);
	$captcha_display = "<input type='hidden' name='sqn_captcha' id='sqn_captcha' value='Token'><button type='button' id='login_btn' class='btn btn-primary'>Load Captcha</button><script src='//static.shenqiniao.net/sqn.js?id={$id}&btn=login_btn' type='text/javascript'></script>";
	break;
}
?>