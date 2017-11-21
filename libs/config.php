<?php
$dbHost = "localhost";
$dbUser = "username";
$dbPW = "database pass";
$dbName = "database name";
$mysqli = mysqli_connect($dbHost, $dbUser, $dbPW, $dbName);
if(mysqli_connect_errno()){
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
} 
// change it now
$admin_username = 'admin';
$admin_pass = 'admin';

// basic info
$faucet['name'] = 'BitGuide';
$faucet['description'] = 'Free BitCoin';
$faucet['url'] = 'http://bitguide.info/';
$currency = 'BTC';
$theme = 3; // now you can choose you faucet's theme 1->3 :)

$faucet['captcha'] = 3; // 1 for solvemedia, 2 for recaptcha, 3 for bitcaptcha
// bitcaptcha setting
$bitcaptcha_id = '';
$bitcaptcha_key = '';
// if you site has www verson, you should add them too
$bitcaptcha_id_www = '';
$bitcaptcha_key_www = '';

// config solve media captcha
$your_challenge_key = '';
$privkey= '';
$hashkey= '';

//recaptcha setting
$secretkey = ''; // your recaptcha private key
$publickey = ''; // your recaptcha public key

// iphub setting, use it to block proxy. get your api at iphub.info
$iphub_api = 'ODA6cXgyY004NzlLdTlvaExrNHJpY2pSaGFtWVdoZWFNVGU=';

// config your reward
$faucet['reward'] = 1; // your faucet's reward
$faucet['time'] = 50;  // time to wait beetwen 2 claims, in second.
$faucet['ref'] = 15;

$faucethub_api = "";

// config your short link, read full instruction at http://coinbox.club/
$config_link['status'] = 'on'; // turn on or off short link bounus
$config_link['reward'] = 3; // short link bounus amout
     //  start config short link api
$link[1] = "http://coin.mg/api/?api=acefbf14f0e9b8cee80cd05035facade0530fd1e&url=http://bitguide.info/link.php?k={key}&format=text";
$link[6] = "http://coin.mg/api/?api=acefbf14f0e9b8cee80cd05035facade0530fd1e&url=http://bitguide.info/link.php?k={key}&format=text";

$link[2] = "http://btc.ms/api/?api=86b6c147ce28028e5c7762afce1656f898279889&url=http://bitguide.info/link.php?k={key}&format=text";

$link[4] = "http://btc.ms/api/?api=86b6c147ce28028e5c7762afce1656f898279889&url=http://bitguide.info/link.php?k={key}&format=text";

$link[3] = "https://madpay.net/api/?api=90720ad27f78aa8777bd69400270f6e7c243b474&url=http://bitguide.info/link.php?k={key}&format=text";

$link[5] = "http://psl.io/api?api=0bd81d31f53b9f943128658b6b6bb7d0b9ef2cd9&url=http://bitguide.info/link.php?k={key}&format=text";
$link[6] = "http://psl.io/api?api=0bd81d31f53b9f943128658b6b6bb7d0b9ef2cd9&url=http://bitguide.info/link.php?k={key}&format=text";
$link[7] = "http://psl.io/api?api=0bd81d31f53b9f943128658b6b6bb7d0b9ef2cd9&url=http://bitguide.info/link.php?k={key}&format=text";
$link[8] = "http://psl.io/api?api=0bd81d31f53b9f943128658b6b6bb7d0b9ef2cd9&url=http://bitguide.info/link.php?k={key}&format=text";
$link[9] = "http://psl.io/api?api=0bd81d31f53b9f943128658b6b6bb7d0b9ef2cd9&url=http://bitguide.info/link.php?k={key}&format=text";
$link[10] = "http://psl.io/api?api=0bd81d31f53b9f943128658b6b6bb7d0b9ef2cd9&url=http://bitguide.info/link.php?k={key}&format=text";
$link[11] = "http://psl.io/api?api=0bd81d31f53b9f943128658b6b6bb7d0b9ef2cd9&url=http://bitguide.info/link.php?k={key}&format=text";
$link[12] = "http://psl.io/api?api=0bd81d31f53b9f943128658b6b6bb7d0b9ef2cd9&url=http://bitguide.info/link.php?k={key}&format=text";
$link[13] = "http://psl.io/api?api=0bd81d31f53b9f943128658b6b6bb7d0b9ef2cd9&url=http://bitguide.info/link.php?k={key}&format=text";

// ad spaces
$ad['top'] = 'ad top';
$ad['left'] = 'ad left';
$ad['right'] = 'ad right';
$ad['above-form'] = 'ad above form';
$ad['bottom'] = 'ad bottom';
$ad['modal'] = '<font color="red">ad modal</font>';
?>   