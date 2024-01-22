<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $attendance_id = cleanValue($_GET['id']);

    // Ambil path file dari database sebelum menghapus data
    $selectPathQuery = "SELECT foto FROM attendances WHERE attendance_id = ?";
    $stmtPath = mysqli_prepare($conn, $selectPathQuery);
    
    if ($stmtPath) {
        mysqli_stmt_bind_param($stmtPath, "i", $attendance_id);
        mysqli_stmt_execute($stmtPath);
        mysqli_stmt_bind_result($stmtPath, $fileToDelete);
        mysqli_stmt_fetch($stmtPath);
        mysqli_stmt_close($stmtPath);

        // Hapus file dari direktori lokal
        if ($fileToDelete && file_exists($fileToDelete)) {
            unlink($fileToDelete);
        }

        // Hapus data dari database setelah mengambil path file
        $deleteQuery = "DELETE FROM attendances WHERE attendance_id = ?";
        $stmt = mysqli_prepare($conn, $deleteQuery);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $attendance_id);

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Attendance data deleted successfully.')</script>";
                echo "<script>window.location.href = 'attendancelist.php'</script>";
                exit();
            } else {
                echo "Failed to delete data from the database.";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Failed to create a prepared statement for data deletion.";
        }
    }
}
?>
