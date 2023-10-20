<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login/login.php");
    exit();
}

$sekarang = strval(date("Y-m-d H:i:s"));
if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $project_id = cleanValue($_GET['id']);
    $user_id = $_SESSION["user_id"];
    
    // Update data di database dengan menandai sebagai dihapus
    $queryDelete = "UPDATE m_projects SET is_deleted = 'Y', deleted_at = ?, deleted_by = ? WHERE project_id = ?";
    $stmt = mysqli_prepare($conn, $queryDelete);
    mysqli_stmt_bind_param($stmt, "sii", $sekarang, $user_id, $project_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data project berhasil dihapus.')</script>";
        echo "<script>window.location.href = 'projectlist.php'</script>";
    } else {
        echo "Gagal menandai proyek sebagai dihapus.";
    }
} else {
    echo "Parameter tidak valid.";
}
?>
