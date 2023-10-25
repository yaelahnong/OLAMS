<?php
session_start();
include __DIR__ ."/include/conn.inc.php";
include __DIR__ ."/include/csrf_token.inc.php";
include __DIR__ ."/include/baseUrl.inc.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $overtime_id = cleanValue($_GET['id']);
        $deleteQuery = "DELETE FROM duty_overtimes WHERE duty_overtime_id = ?";
        $stmt = mysqli_prepare($conn, $deleteQuery);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $overtime_id);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Duty overtime data deleted successfully.')</script>";
                echo "<script>window.location.href = 'duty_overtimelist.php'</script>";
                exit();
            } else {
                echo "Failed to delete da2ta from the database.";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Failed to create a prepared statement for data deletion.";
        }
    
}
?>
