<?php
include 'libs/core.php';
function clean($var) {
	global $mysqli;
	$return = $mysqli->real_escape_string($var);
	return $return;
}
if (isset($_POST['username']) and isset($_POST['password'])) {
	$admin = md5($_POST['username'] . '&' . $_POST['password']);
	$_SESSION['admin'] = $admin;

} 
if (isset($_SESSION['admin'])) {
	$real = $admin_username . '&' . $admin_password;
	$real_admin = md5($real);
	if ($_SESSION['admin'] !== $real_admin) {
		$alert = 'Invalid Login';
		session_destroy();
	} else {
		if (isset($_GET['g']) and isset($_POST['name'])) {
			$update = array(clean($_POST['name']), clean($_POST['description']), clean($_POST['url']), clean($_POST['theme']), clean($_POST['currency']), clean($_POST['api']), clean($_POST['timer']), clean($_POST['reward']), clean($_POST['ref']), clean($_POST['status']), clean($_POST['rewardlink']), clean($_POST['force']));
			for ($i=0; $i <12 ; $i++) {
				$id = $i + 1;
				if ($id == 6 and $update[5] == 'lollollollollollollollollollol') {
			    # do nothing
				} else {
					$mysqli->query("UPDATE settings SET value = '{$update[$i]}' WHERE id = '$id'");
				}
			}
		} elseif (isset($_GET['c']) and isset($_POST['captcha'])) {
			$update = array(clean($_POST['captcha']), clean($_POST['repub']), clean($_POST['resec']), clean($_POST['bitid']), clean($_POST['bitkey']), clean($_POST['bitidwww']), clean($_POST['bitkeywww']), clean($_POST['chall']), clean($_POST['priv']), clean($_POST['hash']));
			for ($i=0; $i <10 ; $i++) {
				$id = $i + 13;
				$mysqli->query("UPDATE settings SET value = '{$update[$i]}' WHERE id = '$id'");
			}
		} elseif (isset($_GET['a']) and isset($_POST['topad'])) {
			$update = array(clean($_POST['topad']), clean($_POST['leftad']), clean($_POST['rightad']), clean($_POST['abovead']), clean($_POST['bottomad']), clean($_POST['modalad']));
			for ($i=0; $i < 6 ; $i++) {
				$id = $i + 23;
				$mysqli->query("UPDATE settings SET value = '{$update[$i]}' WHERE id = '$id'");
			}
		} elseif (isset($_GET['s']) and isset($_POST['iphub'])) {
			$iphub = clean($_POST['iphub']);
			$mysqli->query("UPDATE settings SET value = '$iphub' WHERE id = '29'");
			if (!empty($_POST['banadddress'])) {
				$banadddress = clean($_POST['banadddress']);
				$mysqli->query("INSERT INTO address_blocked (address) VALUES ('$banadddress')");
			}
			if (!empty($_POST['banip'])) {
				$banip = clean($_POST['banip']);
				$mysqli->query("INSERT INTO ip_blocked (address) VALUES ('$banip')");
			}
			if (!empty($_POST['unbanadddress'])) {
				$unbanadddress = clean($_POST['unbanadddress']);
				$mysqli->query("DELETE FROM address_blocked WHERE address = '$unbanadddress'");
			}
			if (!empty($_POST['unbanip'])) {
				$unbanip = clean($_POST['unbanip']);
				$mysqli->query("DELETE FROM ip_blocked WHERE address = '$unbanip'");
			}

		}
	}
} else {
	$alert = 'Please Login';
}
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
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
	<style type="text/css"> 
	body {  
		font-family: 'Saira Extra Condensed', sans-serif;
		font-weight:400;
		font-size:0.875em;
		letter-spacing:0.063em;
	}
	h1 {
		font-weight:900;font-size:3.25em;margin-bottom:15px;text-shadow:2px 3px 0px #898999;line-height:1.2;
	}
	.alert {
		margin-bottom: 20px;
	} 
	footer, .cbc {
		color: #F67F7F;
	}	
</style>
</head>
<body>
	<div class="container">
		<h1 class="text-center">CoinBox Faucet Script</h1>
		<h3 class="text-center">Admin Panel</h3>
		<?php if (isset($alert)) { ?>
		<center>
			<div class="alert alert-danger"><?=$alert?></div>
		</center>
		<form action="" method="post">
			<div class="form-group">
				<label for="user">Admin Username</label>
				<input type="text" name="username" class="form-control" id="user" placeholder="Enter username"> 
			</div>
			<div class="form-group">
				<label for="pass">Password</label>
				<input type="password" name="password" class="form-control" id="pass" placeholder="Password">
			</div> 
			<button type="submit" class="btn btn-primary">Login</button>
		</form>

		<?php } else { ?>

		<ul class="nav nav-pills">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="tab" href="#general">General</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#captcha">Captcha</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#ads">Ads</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#security">Security</a>
			</li>
		</ul>
		<div id="myTabContent" class="tab-content">
			<div class="tab-pane fade show active" id="general">
				
				<form action="?g" method="post">
					<span class="badge badge-success">General Info</span>
					<div class="form-group">
						<label for="name">Name</label>
						<input type="text" name="name" class="form-control" id="name" aria-describedby="namehelp" value="<?=get_info(1)?>">
						<small id="namehelp" class="form-text text-muted">Your Faucet's Name</small>
					</div>
					<div class="form-group">
						<label for="description">Description</label>
						<input type="text" name="description" class="form-control" id="description" aria-describedby="descriptionhelp" value="<?=get_info(2)?>">
						<small id="descriptionhelp" class="form-text text-muted">Say something about your Faucet</small>
					</div>
					<div class="form-group">
						<label for="url">Url</label>
						<input type="url" name="url" class="form-control" id="url" aria-describedby="urlhelp" value="<?=get_info(3)?>">
						<small id="urlhelp" class="form-text text-muted">Your Faucet's Url</small>
					</div>
					<div class="form-group">
						<label for="theme">Select Theme</label>
						<select class="form-control" id="theme" name="theme">
							<?php
							$current_theme = get_info(4);
							switch ($current_theme) {
								case 'default':
								echo '<option value="default" selected>Default</option><option value="materia">Materia</option><option value="other">Other</option>';
								break;
								
								case 'materia':
								echo '<option value="default">Default</option><option value="materia" selected>Materia</option><option value="other">Other</option>';
								break;
								case 'other':
								echo '<option value="default">Default</option><option value="materia">Materia</option><option value="other" selected>Other</option>';
								break;
							}
							?>
						</select>
					</div>
					<span class="badge badge-info">Reward System</span>
					<div class="form-group">
						<label for="api">FaucetHub Api</label>
						<input type="password" name="api" class="form-control" id="api" aria-describedby="apihelp" value="lollollollollollollollollollol">
						<small id="apihelp" class="form-text text-muted">Your FaucetHub Api</small>
					</div>
					<div class="form-group">
						<label for="currency">Currency</label>
						<input type="text" name="currency" class="form-control" id="currency" aria-describedby="currencyhelp" value="<?=get_info(5)?>">
						<small id="currencyhelp" class="form-text text-muted">Your Faucet's Currency (BTC | BCH | DOGE | ETH | BLK | ... you can use any currency supported by FaucetHub)</small>
					</div>
					<div class="form-group">
						<label for="currency">Timer</label>
						<input type="number" name="timer" class="form-control" id="timer" aria-describedby="timerhelp" value="<?=get_info(7)?>">
						<small id="timerhelp" class="form-text text-muted">Your Faucet's Timer in seconds</small>
					</div>
					<div class="form-group">
						<label for="reward">Reward</label>
						<input type="text" name="reward" class="form-control" id="reward" aria-describedby="rewardhelp" value="<?=get_info(8)?>">
						<small id="rewardhelp" class="form-text text-muted">Your Faucet's Reward</small>
					</div>
					<div class="form-group">
						<label for="ref">Referral Commision</label>
						<input type="number" name="ref" class="form-control" id="ref" aria-describedby="refhelp" value="<?=get_info(9)?>">
						<small id="refhelp" class="form-text text-muted">Your Faucet's Referral Commision</small>
					</div>
					<span class="badge badge-primary">Short Link</span>
					<span class="badge badge-danger">Open libs/config.php to setup your short link api :)</span>
					<div class="form-group">
						<label for="rewardlink">Short Link Reward</label>
						<input type="text" name="rewardlink" class="form-control" id="rewardlink" aria-describedby="rewardlinkhelp" value="<?=get_info(11)?>">
						<small id="rewardlinkhelp" class="form-text text-muted">Your Faucet's Short Link Reward</small>
					</div>
					<div class="form-group">
						<label for="status">Short Link Status</label>
						<select class="form-control" id="status" name="status">
							<?php
							$status = get_info(10);
							switch ($status) {
								case 'on':
								echo '<option value="on" selected>On</option><option value="off">Off</option>';
								break;

								default:
								echo '<option value="on">On</option><option value="off" selected>Off</option>';
								break;
							}
							?>
						</select>
					</div>
					<div class="form-group">
						<label for="force">Force Short Link</label>
						<select class="form-control" id="force" name="force">
							<?php
							$status = get_info(12);
							switch ($status) {
								case 'on':
								echo '<option value="on" selected>On</option><option value="off">Off</option>';
								break;
								
								default:
								echo '<option value="on">On</option><option value="off" selected>Off</option>';
								break;
							}
							?>
						</select>
					</div>
					<button type="submit" class="btn btn-success btn-lg btn-block">Save This Page</button>
				</form>

			</div>
			<div class="tab-pane fade" id="captcha">
				<form action="?c" method="post">
					<span class="badge badge-warning">Captcha System</span>
					<div class="form-group">
						<label for="captcha">Captcha Type</label>
						<select class="form-control" id="captcha" name="captcha">
							<?php
							$status = get_info(13);
							switch ($status) {
								case 'recaptcha':
								echo '<option value="recaptcha" selected>Recaptcha</option><option value="solvemedia">Solvemedia</option><option value="bitcaptcha">Bitcaptcha</option>';
								break;
								case 'solvemedia':
								echo '<option value="recaptcha">Recaptcha</option><option value="solvemedia" selected>Solvemedia</option><option value="bitcaptcha">Bitcaptcha</option>';
								break;
								case 'bitcaptcha':
								echo '<option value="recaptcha">Recaptcha</option><option value="solvemedia">Solvemedia</option><option value="bitcaptcha" selected>Bitcaptcha</option>';
								break;
							}
							?>
						</select>
					</div>
					<div class="form-group">
						<label for="repub">Recaptcha Public Key</label>
						<input type="text" name="repub" class="form-control" id="repub" value="<?=get_info(14)?>">
					</div>
					<div class="form-group">
						<label for="resec">Recaptcha Secret Key</label>
						<input type="text" name="resec" class="form-control" id="resec" value="<?=get_info(15)?>">
					</div>
					<br>
					<div class="form-group">
						<label for="bitid">Bitcaptcha Id</label>
						<input type="text" name="bitid" class="form-control" id="bitid" value="<?=get_info(16)?>">
					</div>
					<div class="form-group">
						<label for="bitkey">Bitcaptcha Key</label>
						<input type="text" name="bitkey" class="form-control" id="bitkey" value="<?=get_info(17)?>">
					</div>
					<div class="form-group">
						<label for="bitidwww">Bitcaptcha Id WWW Verson</label>
						<input type="text" name="bitidwww" class="form-control" id="bitidwww" value="<?=get_info(18)?>">
					</div>
					<div class="form-group">
						<label for="bitkeywww">Bitcaptcha Key WWW Verson</label>
						<input type="text" name="bitkeywww" class="form-control" id="bitkeywww" value="<?=get_info(19)?>">
					</div>
					<br>
					<div class="form-group">
						<label for="chall">SolveMedia Challenge Key</label>
						<input type="text" name="chall" class="form-control" id="chall" value="<?=get_info(20)?>">
					</div>
					<div class="form-group">
						<label for="priv">SolveMedia Private Key</label>
						<input type="text" name="priv" class="form-control" id="priv" value="<?=get_info(21)?>">
					</div>
					<div class="form-group">
						<label for="hash">SolveMedia Hash Key</label>
						<input type="text" name="hash" class="form-control" id="hash" value="<?=get_info(22)?>">
					</div>
					<button type="submit" class="btn btn-success btn-lg btn-block">Save This Page</button>
				</form>
			</div>
			<div class="tab-pane fade" id="ads">
				<form action="?a" method="post">
					<span class="badge badge-warning">Advertisements</span>
					<div class="form-group">
						<label for="topad">Top Ad Slot</label>
						<textarea class="form-control" id="topad" rows="4" name="topad"><?=get_info(23)?></textarea>
					</div>
					<div class="form-group">
						<label for="leftad">Left Ad Slot</label>
						<textarea class="form-control" id="leftad" rows="4" name="leftad"><?=get_info(24)?></textarea>
					</div>
					<div class="form-group">
						<label for="rightad">Right Ad Slot</label>
						<textarea class="form-control" id="rightad" rows="4" name="rightad"><?=get_info(25)?></textarea>
					</div>
					<div class="form-group">
						<label for="abovead">Above Form Ad Slot</label>
						<textarea class="form-control" id="abovead" rows="4" name="abovead"><?=get_info(26)?></textarea>
					</div>
					<div class="form-group">
						<label for="bottomad">Bottom Ad Slot</label>
						<textarea class="form-control" id="bottomad" rows="4" name="bottomad"><?=get_info(27)?></textarea>
					</div>
					<div class="form-group">
						<label for="modalad">Modal Ad Slot</label>
						<textarea class="form-control" id="modalad" rows="4" name="modalad"><?=get_info(28)?></textarea>
					</div>
					<button type="submit" class="btn btn-success btn-lg btn-block">Save This Page</button>
				</form>
			</div>
			<div class="tab-pane fade" id="security">
				<form action="?s" method="post">
					<div class="form-group">
						<label for="iphub">IpHub Api</label>
						<input type="text" name="iphub" class="form-control" id="iphub" aria-describedby="iphubhelp" value="<?=get_info(29)?>">
						<small id="iphubhelp" class="form-text text-muted">We suggest you creat your own api at iphub.info</small>
					</div>
					<div class="form-group">
						<label for="banadddress">Ban Address</label>
						<input type="text" name="banadddress" class="form-control" id="banadddress" value="">
					</div>
					<div class="form-group">
						<label for="unbanadddress">UnBan Address</label>
						<input type="text" name="unbanadddress" class="form-control" id="unbanadddress" value="">
					</div>
					<div class="form-group">
						<label for="banip">Ban Ip</label>
						<input type="text" name="banip" class="form-control" id="banip" value="">
					</div>
					<div class="form-group">
						<label for="unbanip">UnBan Ip</label>
						<input type="text" name="unbanip" class="form-control" id="unbanip" value="">
					</div>
					<button type="submit" class="btn btn-success btn-lg btn-block">Save This Page</button>
				</form> 
			</div>
			<div class="alert alert-info text-center">This script uses Cookie to cache some datas in order to reduce mysqli request. So it can take up to 10 minutes for your user to see your updates !</div>
		</div>
		<?php } ?>
		<footer class="text-center">
			<!---Please do not remove the link to support us, thanks!-->
			<p>&copy; 2017 <a href='<?=$faucet['url']?>'><?=$faucet['name']?></a>, <strong id='copyright'>Powered by <a href='http://coinbox.club' class="cbc">CoinBox Script</a></strong></p>
		</footer>  
	</div>
	<script src="https://use.fontawesome.com/7002d3875b.js"></script>
	<script src="template/js/jquery-3.2.1.min.js"></script>
	<script src="template/js/popper.min.js"></script>
	<script src="template/js/bootstrap.min.js"></script>
</body>
</html>
<?php
$mysqli->close();
?>