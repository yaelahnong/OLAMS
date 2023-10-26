<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

//Kosongkan Semua Session
$_SESSION = [];
// Hapus semua data sesi
session_unset();
session_destroy();

// Redirect ke halaman login atau halaman lainnya jika diperlukan
header("Location: login.php");
exit;
?>
