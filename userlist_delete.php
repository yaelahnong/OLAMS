<?php 
session_start();
include __DIR__ . "/include/baseUrl.inc.php";
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";

if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit();
}

// membatasi Hak Akses User
if ($_SESSION["role_id"] != 3 && $_SESSION["role_id"] != 4) {
  header("Location: dashboard.php");
  exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && is_numeric($_GET['id'])) {
  $user_id = $_GET['id'];

  try {
      // Cek apakah data pengguna ada di tabel anak yang berbeda
      $checkChildQuery = "
        SELECT COUNT(user_id) FROM attendances WHERE user_id = ? 
        UNION ALL 
        SELECT COUNT(user_id) FROM overtimes WHERE user_id = ?
      ";
      $stmtCheckChild = mysqli_prepare($conn, $checkChildQuery);

      if ($stmtCheckChild) {
          mysqli_stmt_bind_param($stmtCheckChild, "ii", $user_id, $user_id);
          mysqli_stmt_execute($stmtCheckChild);
          mysqli_stmt_bind_result($stmtCheckChild, $count);

          if (mysqli_stmt_fetch($stmtCheckChild)) {
              // Jika data ada di tabel anak, tampilkan pesan peringatan
              if ($count > 0) {
                  echo "<script>alert('Cannot delete user as it has related records in child table.')</script>";
                  echo "<script>window.location.href = 'userlist.php'</script>";
                  exit();
              } else {
                  // Jika tidak ada data di tabel anak yang berbeda, lanjutkan proses penghapusan
                  $delete_query = "DELETE FROM users WHERE user_id = $user_id";
                  $stmt = mysqli_query($conn, $delete_query);
                  echo "<script>alert('Data deleted successfully.')</script>";
                  echo "<script>window.location.href = 'userlist.php'</script>";
                  exit();
              }
          } else {
              echo "<script>alert('Failed to fetch child table data.')</script>";
              echo "<script>window.location.href = 'userlist.php'</script>";
          }

          mysqli_stmt_close($stmtCheckChild);
      } else {
          echo "<script>alert('Failed to create a prepared statement for child table check.')</script>";
          echo "<script>window.location.href = 'userlist.php'</script>";
      }
  } catch (Exception $e) {
      // Handle any exceptions that occur
      echo "<script>alert(`An error occurred: " . $e->getMessage() . "`);window.location.href = 'userlist.php'</script>";
  }
}
?>