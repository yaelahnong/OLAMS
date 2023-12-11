<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION["user_id"];

$queryUsers = "SELECT user_id, name FROM users";
$usersData = mysqli_prepare($conn, $queryUsers);
mysqli_stmt_execute($usersData);
$resultUsers = mysqli_stmt_get_result($usersData);
$resultUsers = mysqli_fetch_all($resultUsers, MYSQLI_ASSOC);

$queryDivision = "SELECT division_id, division_name FROM m_divisions";
$divisionData = mysqli_prepare($conn, $queryDivision);
mysqli_stmt_execute($divisionData);
$resultDivision = mysqli_stmt_get_result($divisionData);
$resultDivision = mysqli_fetch_all($resultDivision, MYSQLI_ASSOC);

$fullnameErr = $divisionErr = $reasonErr = $categoryErr = $startDateErr = $finishDateErr = "";
$fullname = $division = $reason = $category = $startDate = $finishDate = NULL;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
    $fullname = isset($_POST["user_id"]) ? cleanValue($_POST["user_id"]) : NULL;
    $division = isset($_POST["division"]) ? cleanValue($_POST["division"]) : NULL;
    $reason = isset($_POST["reason"]) ? cleanValue($_POST["reason"]) : NULL;
    $category = isset($_POST["category"]) ? cleanValue($_POST["category"]) : NULL;
    $startDate = isset($_POST["startDate"]) ? cleanValue($_POST["startDate"]) : NULL;
    $finishDate = isset($_POST["finishDate"]) ? cleanValue($_POST["finishDate"]) : NULL;

    // Validasi data yang diterima dari formulir
    if (empty($fullname)) {
      $fullnameErr = "Full Name is required.";
    }

    if (empty($division)) {
      $divisionErr = "Division is required.";
    }

    if (empty($reason)) {
      $reasonErr = "Reason is required.";
    }

    if (empty($category)) {
      $categoryErr = "Category is required.";
    }

    if (empty($startDate)) {
      $startDateErr = "Start Date is required.";
    } elseif (strtotime($startDate) < strtotime(date('Y-m-d'))) {
      $startDateErr = "Start Date cannot be in the past.";
    }

    if (empty($finishDate)) {
      $finishDateErr = "Finish Date is required.";
    } elseif (strtotime($finishDate) < strtotime($startDate)) {
      $finishDateErr = "Finish Date cannot be earlier than Start Date.";
    }

    // ...

    // Validasi nama dan tanggal sebelum penyisipan
    if (empty($fullnameErr) && empty($divisionErr) && empty($reasonErr) && empty($categoryErr) && empty($startDateErr) && empty($finishDateErr)) {
      // Query untuk memeriksa apakah data dengan nama yang sama dan tanggal yang sama sudah ada
      $checkDuplicateQuery = "SELECT COUNT(*) FROM leaves WHERE user_id = ? AND start_date = ?";
      $checkDuplicateStmt = mysqli_prepare($conn, $checkDuplicateQuery);
      mysqli_stmt_bind_param($checkDuplicateStmt, "is", $fullname, $startDate);
      mysqli_stmt_execute($checkDuplicateStmt);
      mysqli_stmt_bind_result($checkDuplicateStmt, $count);
      mysqli_stmt_fetch($checkDuplicateStmt);

      // Periksa jumlah hasil yang cocok
      if ($count > 0) {
        $error = "Leave data with the same name and start date already exists.";
      } else {
        // Close the checkDuplicateStmt before preparing a new statement
        mysqli_stmt_close($checkDuplicateStmt);

        // Lanjutkan dengan penyisipan data
        $insertQuery = "INSERT INTO leaves (user_id, division_id, reason, category, start_date, finish_date, status, created_by) VALUES (?, ?, ?, ?, ?, ?, 'Pending', ?)";
        $insertStmt = mysqli_prepare($conn, $insertQuery);

        if ($insertStmt) {
          mysqli_stmt_bind_param($insertStmt, "iissssi", $fullname, $division, $reason, $category, $startDate, $finishDate, $user_id);
          if (mysqli_stmt_execute($insertStmt)) {
            echo "<script>alert('Leave data added successfully.')</script>";
            echo "<script>window.location.href = 'leavelist.php'</script>";
            exit();
          } else {
            $error = "Failed to insert data into the database.";
          }
          mysqli_stmt_close($insertStmt);
        } else {
          $error = "Failed to create a prepared statement for data insertion.";
        }
      }
    }

    // Cek apakah $checkDuplicateStmt sudah dibuat sebelum menutupnya
    if (isset($checkDuplicateStmt)) {
      mysqli_stmt_close($checkDuplicateStmt);
    }
  }
} else {
  $TokenErr = "Invalid CSRF token";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.inc.php"; ?>
  <title>OLAMS - Add Leave</title>
</head>

<body>
  <div class="wrapper">
    <?php include "components/sidebar.inc.php"; ?>
    <div class="main">
      <?php include "components/navbar.inc.php"; ?>
      <main class="content">
        <div class="container-fluid p-0">
          <h1 class="h1 mb-3"><strong>Add Leave</strong></h1>
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
                      <div class="mb-3 col-md-6 d-none">
                        <label class="form-label" for="inputUser">User</label>
                        <input type="text" name="user_id" id="inputUser" class="form-control" value="<?= $_SESSION["user_id"]; ?>" readonly>
                        <span class="error" style="color: red;"> <?= $fullnameErr; ?> </span>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputDivision">Division</label>
                        <span style="color: red">*</span>
                        <select class="form-select" id="inputDivision" name="division">
                          <option value="">Select Division</option>
                          <?php foreach ($resultDivision as $key => $division) { ?>
                            <option value="<?= $division['division_id'] ?>"><?= $division['division_name'] ?></option>
                          <?php } ?>
                        </select>
                        <span class="text-danger"><?php echo $divisionErr; ?></span>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputCategory">Category</label>
                        <span style="color: red">*</span>
                        <select class="form-select" id="inputCategory" name="category">
                          <option value="">Select Category</option>
                          <option value="important reason" <?= ($category == "important reason") ? 'selected' : '' ?>>Important Reason</option>
                          <option value="extended" <?= ($category == "extended") ? 'selected' : '' ?>>Extended</option>
                          <option value="Annual" <?= ($category == "Annual") ? 'selected' : '' ?>>Annual</option>
                          <option value="Pregnancy" <?= ($category == "Pregnancy") ? 'selected' : '' ?>>Pregnancy</option>
                        </select>
                        <span class="text-danger"><?php echo $categoryErr; ?></span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputStartDate">Start Date</label>
                        <span style="color: red">*</span>
                        <input type="datetime-local" class="form-control" id="inputStartDate" name="startDate">
                        <span class="text-danger"><?php echo $startDateErr; ?></span>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputFinishDate">Finish Date</label>
                        <span style="color: red">*</span>
                        <input type="datetime-local" class="form-control" id="inputFinishDate" name="finishDate">
                        <span class="text-danger"><?php echo $finishDateErr; ?></span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputReason">Reason</label>
                        <span style="color: red">*</span>
                        <textarea class="form-control" id="inputReason" name="reason" rows="4"></textarea>
                        <span class="text-danger"><?php echo $reasonErr; ?></span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($fullnameErr) && !empty($divisionErr) && !empty($reasonErr) && !empty($categoryErr) && !empty($startDateErr) && !empty($finishDateErr)) : ?>
                          <button type="button" class="btn btn-primary">Submit</button>
                        <?php else : ?>
                          <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to add it?')">Submit</button>
                        <?php endif; ?>
                        <a href="leavelist.php" class="btn btn-light text-dark text-decoration-none">Cancel</a>
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