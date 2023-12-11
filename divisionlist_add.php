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
if ($_SESSION['role_id'] != 3 && $_SESSION['role_id'] != 4 && $_SESSION['role_id'] != 2) {
  header("Location: dashboard.php");
  exit();
}
$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
    if (isset($_POST["division_name"]) && !empty($_POST["division_name"])) {
      $divisionName = $_POST["division_name"];

      // Validasi input harus berisi huruf, angka, dan spasi
      if (preg_match("/^[a-zA-Z0-9 ]*$/", $divisionName)) {
        // Jika valid, bersihkan nilai
        $divisionName = cleanValue($divisionName);

        // Cek apakah divisi dengan nama yang sama persis sudah ada
        $checkDivisionName = "SELECT division_name FROM m_divisions WHERE division_name = ?";
        $checkStmt = mysqli_prepare($conn, $checkDivisionName);

        if ($checkStmt) {
          mysqli_stmt_bind_param($checkStmt, "s", $divisionName);
          mysqli_stmt_execute($checkStmt);
          $checkResult = mysqli_stmt_get_result($checkStmt);

          if (mysqli_num_rows($checkResult) > 0) {
            $error = "Division with the same name already exists.";
          } else {
            // Jika tidak ada yang sama, baru lakukan penyimpanan
            $insertQuery = "INSERT INTO m_divisions (division_name, created_by) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insertQuery);

            if ($stmt) {
              mysqli_stmt_bind_param($stmt, "ss", $divisionName, $user_id);
              if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Data added.')</script>";
                echo "<script>window.location.href = 'divisionlist.php'</script>";
                exit();
              } else {
                $error = "Failed to save the data.";
              }

              mysqli_stmt_close($stmt);
            } else {
              $error = "Failed to make a prepared statement.";
            }
          }

          mysqli_stmt_close($checkStmt);
        } else {
          $error = "Failed to make a prepared statement for duplicate check.";
        }
      } else {
        $error = "Input can only contain letters, numbers, and spaces.";
      }
    } else {
      $error = "The division name cannot be empty.";
    }
  } else {
    $TokenErr = "Invalid CSRF token";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.inc.php"; ?>
  <title>OLAMS - Add Division</title>
</head>

<body>
  <div class="wrapper">
    <?php include "components/sidebar.inc.php"; ?>
    <div class="main">
      <?php include "components/navbar.inc.php"; ?>
      <main class="content">
        <div class="container-fluid p-0">
          <h1 class="h1 mb-3"><strong>Add Division</strong></h1>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                </div>
                <div class="card-body">
                  <?php if (isset($error)) { ?>
                    <div class="alert alert-danger alert-dismissible p-3 rounded" role="alert">
                      <div class="alert-message">
                        <?php echo $error; ?>
                      </div>
                      <button type="button" class="btn-close align-items-end" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php } ?>
                  <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="row">
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputDivisionName">Division Name</label>
                        <span style="color: red">*</span><br>
                        <input type="text" class="form-control" name="division_name" id="inputDivisionName" placeholder="Enter Division Name">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$error) : ?>
                          <button type="button" class="btn btn-primary">Submit</button>
                        <?php else : ?>
                          <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to add it?')">Submit</button>
                        <?php endif; ?>
                        <a href="divisionlist.php" class="btn btn-light text-dark text-decoration-none">Cancel</a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
      <?php include "components/footer.inc.php"; ?>
    </div>
  </div>
  <?php include "script.inc.php"; ?>
</body>

</html>