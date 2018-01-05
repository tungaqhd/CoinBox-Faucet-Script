<?php 
include 'libs/core.php'; 

if (isset($_GET['r']) && !isset($_COOKIE['ref'])) {
	$reff = $mysqli->real_escape_string($_GET['r']);
	setcookie('ref',  $reff, time()+86400000);
}

if (isset($_POST['address']) and isset($_POST['token'])) { 
	
    # clean user's input
	$address = $mysqli->real_escape_string($_POST['address']);
	if (!isset($_COOKIE['address'])) {
		setcookie('address', $address, time()+1000000);
	} 
    # end 
	if ($_POST['token'] == $_SESSION['token']) {

		# check captcha
		if (isset($_POST['g-recaptcha-response']) && $faucet['captcha'] == 'recaptcha') {
			$secret = get_info(15);
			$CaptchaCheck = json_decode(captcha_check($_POST['g-recaptcha-response'], $secret))->success; 
		} elseif (isset($_POST["adcopy_challenge"]) && isset($_POST["adcopy_response"])&& $faucet['captcha'] == 'solvemedia') {
			$privatekey = get_info(21);
			$hashkey = get_info(22);
			$solvemedia_response = solvemedia_check_answer($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["adcopy_challenge"],$_POST["adcopy_response"],$hashkey);
			$CaptchaCheck = $solvemedia_response->is_valid;
		} elseif (isset($_POST['sqn_captcha']) and $faucet['captcha'] == 'bitcaptcha') {
			$captcha_key = ((strpos($_SERVER['HTTP_HOST'],'ww.')>0)?get_info(19):get_info(17));
			$CaptchaCheck = sqn_validate($_POST['sqn_captcha'], $captcha_key, $id);
		} else {
			$alert = "<center><img style='max-width: 200px;' src='template/img/bots.png'><br><div class='alert alert-warning'>Invalid Captcha</div></center>"; 
		}
		if ($CaptchaCheck and !isset($alert)) {
			if (check_blocked_ip($ip) == 'blocked') {
				$alert = "<center><img style='max-width: 200px;' src='template/img/bots.png'><br><div class='alert alert-warning'>Your Ip Is Blocked. Please Contact Admin.</div></center>";
			} elseif (check_blocked_address($address) == 'blocked') {
				$alert = "<center><img style='max-width: 200px;' src='template/img/bots.png'><br><div class='alert alert-warning'>Your Address Is Blocked. Please Contact Admin.</div></center>";
			} elseif (!empty(get_info(29)) and iphub(get_info(29)) == 'bad') {
				$alert = "<center><img style='max-width: 200px;' src='template/img/bots.png'><br><div class='alert alert-warning'>Your Ip Is Blocked By IpHub</div></center>";
				$mysqli->query("INSERT INTO ip_blocked (address) VALUES ('$ip')");
			} elseif (checkaddress($address) !== 'ok') {
				$alert = "<center><img style='max-width: 200px;' src='template/img/bots.png'><br><div class='alert alert-warning'>Your Address is not ready to claim!</div><br><div id='CountDownTimer' data-timer='" . checkaddress($address) . "' style='width: 100%;'></div></center>";
			} elseif (checkip($ip) !== 'ok') {
				$alert = "<center><img style='max-width: 200px;' src='template/img/bots.png'><br><div class='alert alert-warning'>Your Ip Address is not ready to claim!</div><br><div id='CountDownTimer' data-timer='" . checkip($ip) . "' style='width: 100%;'></div></center>";
			} else {
				
				# check short link
				if (get_info(12) == 'on' or (isset($_POST['link']) and get_info(10) == 'on')) {
					$key = get_token(15); 
					for ($i=1; $i <= count($link) ; $i++) { 
						if (!isset($_COOKIE[$i])) {
							$mysqli->query("INSERT INTO link (bitcoin_address, sec_key, ip) VALUES ('$address', '$key', '$ip')");
							log_user($address, $ip);
							setcookie($i, 'fuck cheater :P', time() + 86400);
							$url = $link[$i];
							$full_url = str_replace("{key}",$key,$url);
							$short_link = file_get_contents($full_url);
							break;
						}
					}
					if (!isset($short_link)) {
						$mysqli->query("INSERT INTO link (bitcoin_address, sec_key, ip) VALUES ('$address', '$key', '$ip')");
						log_user($address, $ip);
						$url = $link_default;
						$full_url = str_replace("{key}",$key,$url);
						$short_link = file_get_contents($full_url);
					} 
					header("Location: ". $short_link);
					echo '<script> window.location.href="' .$short_link. '"; </script>';
					die('Redirecting you to short link, please wait ...');
				} else {

					#normal claim
					$faucethub_api = get_info(6);
					$currency = $faucet['currency'];
					$faucethub = new FaucetHub($faucethub_api, $currency);
					$result = $faucethub->send($address, $faucet['reward'], $ip);
					if (isset($_COOKIE['ref']) && $address !== $_COOKIE['ref']) {
						$ref = $mysqli->real_escape_string($_COOKIE['ref']);
						$amt = floor($faucet['reward'] / 100 * $faucet['ref']);
						$s = $faucethub->sendReferralEarnings($ref, $amt);
					}
					if ($result['success'] == true) {
						log_user($address, $ip);
						$new_balance = $result['balance'];
						$mysqli->query("UPDATE settings SET value = '$new_balance' WHERE id = '30'");
						$alert = "<center><img style='max-width: 200px;' src='template/img/trophy.png'><br>{$result['html']}</center>";
					} else {
						$alert = "<center><img style='max-width: 200px;' src='template/img/trophy.png'><br><div class='alert alert-danger'>Failed to send your reward :(</div></center>"; 
					}
				}
			}
		} else {
			$alert = "<center><img style='max-width: 200px;' src='template/img/bots.png'><br><div class='alert alert-warning'>Invalid Captcha</div></center>"; 
		}
	} else {
		$alert = "<center><img style='max-width: 200px;' src='template/img/bots.png'><br><div class='alert alert-warning'>Invalid Token</div></center>"; 
	}
}

// check if user has completed a short link
if (isset($_GET['k'])) {
	$key = $mysqli->real_escape_string($_GET['k']);
	$check = $mysqli->query("SELECT * FROM link WHERE sec_key = '$key' and ip = '$ip' LIMIT 1");
	if ($check->num_rows == 1) { 
		$check = $check->fetch_assoc();
		$address = $check['bitcoin_address'];
		$mysqli->query("DELETE FROM link WHERE sec_key = '$key'");
		$faucethub_api = get_info(6);
		$faucethub = new FaucetHub($faucethub_api, $faucet['currency']);
		$rew = get_info(11) + $faucet['reward'];
		$result = $faucethub->send($address, $rew, $ip);
		$new_balance = $result['balance'];
		$mysqli->query("UPDATE settings SET value = '$new_balance' WHERE id = '30'");
		if (isset($_COOKIE['ref']) && $address !== $_COOKIE['ref']) {
			$ref = $mysqli->real_escape_string($_COOKIE['ref']);
			$amt = floor($rew / 100 * $faucet['ref']);
			$s = $faucethub->sendReferralEarnings($ref, $amt);
		}
		$alert = "<center><img style='max-width: 200px;' src='template/img/trophy.png'><br>{$result['html']}</center>";
	} else {
		$alert = "<center><img style='max-width: 200px;' src='template/img/bots.png'><br><div class='alert alert-warning'>Invalid Key !</div></center>";
	}
}

$_SESSION['token'] = get_token(70);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?=$faucet['name']?> - <?=$faucet['description']?></title> 
	<link rel="shortcut icon" href="template//img/favicon.ico" type="image/x-icon">
	<link rel="icon" href="template/img/favicon.ico" type="template/image/x-icon">
	<link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="template/css/<?=$faucet['theme']?>.css"> 
	<link rel="stylesheet" href="template/css/countdown.css"> 
	<style type="text/css"> 
	body {  
		font-family: 'Saira Extra Condensed', sans-serif;
		font-weight:400;
		font-size:0.875em;
		letter-spacing:0.063em;
	}
	img, iframe {
		max-width: 100%;
	}
	.login {
		background-color: rgba(226, 212, 296, 0.3);
		padding-top: 20px;
		padding-bottom: 20px;
		border-radius: 20px 20px 20px 20px;
	}
	.login:hover {
		background-color: rgba(226, 212, 296, 0.5);
	}
	.alert {
		margin-bottom: 20px;
	}  
	footer, .cbc {
		color: #F67F7F;
	}	
	.ribbon {
		font-weight:900;
		font-size:1.8em;
		margin-bottom:30px;
		text-shadow:2px 3px 0px #898999;
		line-height:1.2;
		color: #E4DEED;
		width: 50%;
		position: relative;
		background: #ba89b6;
		text-align: center;
		padding: 1em 2em;  
		margin: 1em auto 1.5em;   
	}
	.ribbon:hover {
		background-color: #D5A2D1;
	}
	.ribbon:before, .ribbon:after {
		content: "";
		position: absolute;
		display: block;
		bottom: -1em;
		border: 1.5em solid #986794;
		z-index: -1;
	}
	.ribbon:before {
		left: -2em;
		border-right-width: 1.5em;
		border-left-color: transparent;
	}
	.ribbon:after {
		right: -2em;
		border-left-width: 1.5em;
		border-right-color: transparent;
	}
	.ribbon .ribbon-content:before, .ribbon .ribbon-content:after {
		content: "";
		position: absolute;
		display: block;
		border-style: solid;
		border-color: #804f7c transparent transparent transparent;
		bottom: -1em;
	}
	.ribbon .ribbon-content:before {
		left: 0;
		border-width: 1em 0 0 1em;
	}
	.ribbon .ribbon-content:after {
		right: 0;
		border-width: 1em 1em 0 0;
	}
</style>
</head>
<body> 
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
		<a class="navbar-brand" href="index.php"><?=$faucet['name']?></a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarColor01">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item active">
					<a class="nav-link" href="index.php"><i class="fa fa-home" aria-hidden="true"></i> Home <span class="sr-only">(current)</span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#"><i class="fa fa-info" aria-hidden="true"></i> About us</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#"><i class="fa fa-envelope-open" aria-hidden="true"></i> Contact</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="http://coinbox.club/threads/free-coinbox-faucet-script.5/"><i class="fa fa-bolt" aria-hidden="true"></i> CoinBox Script</a>
				</li>
			</ul>
			<ul class="navbar-nav ml-auto">
				<li class="nav-item active">
					<a class="nav-link" href="#"><i class="fa fa-balance-scale" aria-hidden="true"></i> Faucet Balance: <?=get_info(30)?> <?=$currency_name?> <span class="sr-only">(current)</span></a>
				</li>
			</ul>
		</div>
	</nav>
	<center>
		<?=$ad['top']?>
	</center>
	<h1 class="ribbon ribbon-content">Welcome to <?=$faucet['name']?></h1>
	<div class="container-fluid" style="margin-top: 30px;">
		<div class="row">
			<div class="col-sm-3 text-center" style="margin-top: 20px;">
				<?=$ad['left']?>
			</div>
			<div class="col-sm-6 login">
				<div class="alert alert-success text-center" style="margin-top: 10px;">
					<p><i class="fa fa-trophy" aria-hidden="true"></i> Claim <?=$faucet['reward']?> <?=$currency_name?> every <?=floor($faucet['timer']/60)?> minutes .</p>
				</div>
				<center>
					<?=$ad['above-form']?>
				</center>
				<?php if (isset($alert)) { ?>
				<div class="modal fade" id="alert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					<div class="modal-dialog" role="document">
						<div class="modal-content"> 
							<div class="modal-body">
								<?=$alert?>  
							</div>
						</div>
					</div>
				</div>
				<?php } if (checkip($ip) == 'ok') { ?>
				<form action="" method="post">
					<input type="hidden" name="token" value="<?=$_SESSION['token']?>">
					<div class="form-group">
						<span class="badge badge-warning control-label">Your Bitcoin Address</span>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon"><img src="template/img/wallet.png" width="40px"></div>
								<input type="text" class="form-control" name="address" <?php if(isset($_COOKIE['address'])) {echo "value='" . $_COOKIE['address'] . "'";} else {echo 'placeholder="Must be linked to FaucetHub first"'; } ?> style="border-radius: 0px 20px 20px 0px;">
							</div>
						</div>
					</div> 
					<center>
						<?=$ad['bottom']?> 
					</center>
					<div class="form-group">
						<span class="badge badge-danger control-label">Complete Captcha</span>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon"><img src="template/img/captcha.png" width="40px"></div>
								<?=$captcha_display?>
							</div>
						</div>
					</div>
					<?php if (get_info(10) == 'on' and get_info(12) !== 'on') { for ($i=1; $i <= count($link) ; $i++) { if (!isset($_COOKIE[$i])) { ?>
					<label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
						<input type="checkbox" name="link" value="yes" class="custom-control-input" checked>
						<span class="custom-control-indicator"></span>
						<span class="custom-control-description"><i class="fa fa-gift" aria-hidden="true"></i> <strong>I want to click <font color="#F67F7F">SHORT LINK</font> and receive<font color="#F67F7F"> + <?=get_info(8)?> satoshi bounus</font></strong></span>
					</label> 
					<?php break; } }} ?>
					<button type="button" class="btn btn-warning btn-lg btn-block" style="margin-bottom: 20px;" data-toggle="modal" data-target="#next"><i class="fa fa-paper-plane" aria-hidden="true"></i> <strong>Claim Free Bitcoin</strong></button>
					<div class="modal fade bd-example-modal-lg" id="next" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="exampleModalLabel">Final Step</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<?=$ad['modal']?>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="submit" class="btn btn-primary" id="claim">Claim Your Coin</button>
								</div>
							</div>
						</div>
					</div>
					<code>Ref link: <?=$faucet['url']?>?r=Your_bitcoin_address</code>
				</form>
				<?php } else { $wait= 1; echo "<div class='alert alert-info'>You have to wait</div><br><div id='CountDownTimer' data-timer='" . checkip($ip) . "' style='width: 100%;'></div>"; } ?> 
				<center><a href='http://coinbox.club' target='_blank'><img src='http://coinbox.club/gif.gif'></a></center>
				<!---You can keep this banner to support us, thanks !-->
			</div>
			<div class="col-sm-3 text-center" style="margin-top: 20px;">
				<?=$ad['right']?>
			</div>
		</div>
	</div>
	<br>
	<footer class="text-center">
		<!---Please do not remove the link to support us, thanks!-->
		<p>&copy; 2017 <a href='<?=$faucet['url']?>'><?=$faucet['name']?></a>, <strong id='copyright'>Powered by <a href='http://coinbox.club' class="cbc">CoinBox Script</a></strong></p>
	</footer> 
	<script src="template/js/jquery-3.2.1.min.js"></script>
	<script src="template/js/popper.min.js"></script>
	<script src="template/js/bootstrap.min.js"></script>
	<script src="https://use.fontawesome.com/7002d3875b.js"></script>
	<script src="template/js/adblock.js"></script>
	<?php if (isset($alert)) { ?>
	<script type='text/javascript'>$('#alert').modal('show');</script>
	<?php  } ?>
	<script type="text/javascript"> var fauceturl = '<?=$faucet['url']?>'; </script>
	<script type="text/javascript" src="template/js/timer.js"></script>
	<script type="text/javascript" src="template/js/faucet.js"></script>
</body>
</html>
<?php
$mysqli->close();
?>
