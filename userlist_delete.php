<?php 
session_start();
include __DIR__ . "/include/baseUrl.inc.php";
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    // query untuk menghapus data karyawan berdasarkan id_karyawan
    $delete_query = "DELETE FROM users WHERE user_id = $user_id";
    $stmt = mysqli_query($conn, $delete_query);
    echo "<script>alert('Data deleted successfully.')</script>";
    echo "<script>window.location.href = 'userlist.php' </script>";
    
}

?>