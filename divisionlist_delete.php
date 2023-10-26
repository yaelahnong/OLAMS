<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

// membatasi Hak Akses User
if ($_SESSION["role_id"] != 3 || $_SESSION["role_id"] != 4) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $division_id = cleanValue($_GET['id']);
    $user_id = $_SESSION["user_id"];
    
    // Hapus data divisi dari database
    $queryDelete = "DELETE FROM m_divisions WHERE division_id = ?";
    $stmt = mysqli_prepare($conn, $queryDelete);
    mysqli_stmt_bind_param($stmt, "i", $division_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Division data delete successfully.')</script>";
        echo "<script>window.location.href = 'divisionlist.php'</script>";
    } else {
        echo "Failed to delete the division data.";
    }
} else {
    echo "Invalid parameters.";
}
?>
