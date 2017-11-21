<?php
$result = file_get_contents("http://1ink.cc/api/create.php?uid=12177&uname=Adnanlaghari&url=http://faucettrick.ga/link.php?k=" . $_GET['k']);

echo "http://1ink.cc/" . $result;
?>