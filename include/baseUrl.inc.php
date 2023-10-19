<?php 
$baseUrl = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != 'off' ? 'https' : 'http' .
"://".$_SERVER['SERVER_NAME'].
$_SERVER['PHP_SELF'];
// var_dump($baseUrl);
?>