<?php
session_start();
include __DIR__ ."/include/conn.inc.php";
include __DIR__ ."/include/csrf_token.inc.php";
include __DIR__ ."/include/baseUrl.inc.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $leaved_id = cleanValue($_GET['id']);
        // Query untuk menghapus data berdasarkan attendance_id
        $deleteQuery = "DELETE FROM leaves WHERE leaves_id = ?";
        $stmt = mysqli_prepare($conn, $deleteQuery);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $leaved_id);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Leave data deleted successfully.')</script>";
                echo "<script>window.location.href = 'leavelist.php'</script>";
                exit();
            } else {
                echo "Failed to delete data from the database.";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Failed to create a prepared statement for data deletion.";
        }
    
}
?>
