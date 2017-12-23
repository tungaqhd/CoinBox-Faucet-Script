<?php
$dbHost = "localhost";
$dbUser = "kiemzhrt_teo";
$dbPW = "tung10C@";
$dbName = "kiemzhrt_teo";
$mysqli = mysqli_connect($dbHost, $dbUser, $dbPW, $dbName);
if(mysqli_connect_errno()){
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
} 
// change it now
$admin_username = 'admin';
$admin_password = 'admin';

$link[1] = "http://btc.ms/api/?api=86b6c147ce28028e5c7762afce1656f898279889&url=http://coinbox.club/tung/link.php?k={key}&format=text";

$link[2] = "http://btc.ms/api/?api=86b6c147ce28028e5c7762afce1656f898279889&url=http://coinbox.club/tung/link.php?k={key}&format=text";

$link_default ='http://btc.ms/api/?api=86b6c147ce28028e5c7762afce1656f898279889&url=http://coinbox.club/tung/link.php?k={key}&format=text';
?>   