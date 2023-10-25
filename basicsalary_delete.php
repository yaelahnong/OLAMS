<?php 
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if(!isset($_SESSION["user_id"])) {
  header("Location: login.php");
  exit;
}

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
  $basic_salary_id = cleanValue($_GET['id']);
  $user_id = $_SESSION["user_id"];

  // Hapus data basic salary dari database
  $queryDelete = "DELETE FROM m_basic_salaries WHERE basic_salary_id = ?";
  $stmt = mysqli_prepare($conn, $queryDelete);
  mysqli_stmt_bind_param($stmt, "i", $basic_salary_id);
  if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Basic salary data delete successfully.')</script>";
    echo "<script>window.location.href = 'basicsalary.php'</script>";
} else {
    echo "Failed to delete the division data.";
}
} else {
echo "Invalid parameters.";
}

?>