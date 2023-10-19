<?php
session_start();

// Hapus semua data sesi
session_destroy();

// Redirect ke halaman login atau halaman lainnya jika diperlukan
header("Location: login.php");
exit;
?>
