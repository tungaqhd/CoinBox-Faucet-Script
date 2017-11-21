<?php 
include 'libs/core.php'; 
$logged = false;
if (isset($_POST['username']) and isset($_POST['password']) and !isset($_SESSION['data'])){

	$username = $_POST['username'];
	$password = $_POST['password'];
	$_SESSION['secr'] = get_token(100); 

	if (md5($username . $password . $_SESSION['secr']) == md5($admin_username . $admin_pass . $_SESSION['secr'])) {
		$_SESSION['data'] = md5($username . $password . $_SESSION['secr']);
	} else {
		$alert = 'Invalid Login';
		$_SESSION['secr'] = get_token(100); 
	}
}
if (isset($_SESSION['data'])) {
	if ($_SESSION['data'] == md5($admin_username . $admin_pass . $_SESSION['secr'])) {
		$logged = true;
	} else {
		$_SESSION['secr'] = get_token(100); 
	}
}
if ($logged == true) {
	if (isset($_POST['ip_add'])) {
		$ip_add = $mysqli->real_escape_string($_POST['ip_add']);
		$mysqli->query("INSERT INTO ip_blocked (address) VALUES ('$ip_add')");
	} elseif (isset($_POST['address_add'])) {
		$address_add = $mysqli->real_escape_string($_POST['address_add']);
		$mysqli->query("INSERT INTO address_blocked (address) VALUES ('$address_add')");
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?=$faucet['name']?> - <?=$faucet['description']?></title>
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,900" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
	<link rel="stylesheet" href="template/css/countdown.css"> 
</head>
<body>
	<div class="container">
		<?php
		if ($logged == true) { ?>
		<form action="" method="post">
			<label>Add an address to black list</label>
			<input type="text" name="address_add" class="form-control">
			<button type="submit" class="btn btn-primary">Save</button>
		</form>
		<form action="" method="post">
			<label>Add an ip to black list</label>
			<input type="text" name="ip_add" class="form-control">
			<button type="submit" class="btn btn-primary">Save</button>
		</form>

		<?php } else { if (isset($alert)) {echo $alert;}
		?> 
		<form action="" method="post">
			<div class="form-group">
				<label for="user">Email address</label>
				<input type="text" name="username" class="form-control" id="user" placeholder="Enter username"> 
			</div>
			<div class="form-group">
				<label for="pass">Password</label>
				<input type="password" name="password" class="form-control" id="pass" placeholder="Password">
			</div> 
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
		<?php } ?>
	</div>
</body>
</html> 