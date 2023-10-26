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
$fullname = $division = $reason = $category = $startDate = $finishDate = "";
$leave_id = null;

$fullnameErr = $divisionErr = $reasonErr = $categoryErr = $startDateErr = $finishDateErr = "";
$fullname = $division = $reason = $category = $startDate = $finishDate = "";
$leave_id = null;

if (isset($_GET['id'])) {
    $leave_id = cleanValue($_GET['id']);

    $queryLeave = "SELECT user_id, division_id, reason, category, start_date, finish_date FROM leaves WHERE leaves_id = ?";
    $stmt = mysqli_prepare($conn, $queryLeave);
    mysqli_stmt_bind_param($stmt, "i", $leave_id);
    mysqli_stmt_execute($stmt);
    $resultLeave = mysqli_stmt_get_result($stmt);

    if ($resultLeave && mysqli_num_rows($resultLeave) > 0) {
        $leaveData = mysqli_fetch_assoc($resultLeave);
        $fullname = $leaveData['user_id'];
        $division = $leaveData['division_id'];
        $reason = $leaveData['reason'];
        $category = $leaveData['category'];
        $startDate = date('Y-m-d\TH:i', strtotime($leaveData['start_date']));

        // Perhatikan bahwa kita menggunakan strtotime dan date untuk mengonversi tanggal
        // dan waktu untuk format yang sesuai.
        $finishDate = date('Y-m-d\TH:i', strtotime($leaveData['finish_date']));
    } else {
        echo "Invalid leave ID.";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
      $fullname = isset($_POST["name"]) ? cleanValue($_POST["name"]) : "";
      $division = isset($_POST["division"]) ? cleanValue($_POST["division"]) : "";
      $reason = isset($_POST["reason"]) ? cleanValue($_POST["reason"]) : "";
      $category = isset($_POST["category"]) ? cleanValue($_POST["category"]) : "";
      $startDate = isset($_POST["startDate"]) ? cleanValue($_POST["startDate"]) : "";
      $finishDate = isset($_POST["finishDate"]) ? cleanValue($_POST["finishDate"]) : "";

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

      if (empty($fullnameErr) && empty($divisionErr) && empty($reasonErr) && empty($categoryErr) && empty($startDateErr) && empty($finishDateErr)) {
          // Determine whether this is an operation to add new data or to update
          if ($leave_id === null) {
            $insertQuery = "INSERT INTO leaves (user_id, division_id, reason, category, start_date, finish_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insertQuery);
            if ($stmt) {
                $created_by = $user_id;  // Use user_id from the session
                mysqli_stmt_bind_param($stmt, "iisssssi", $fullname, $division, $reason, $category, $startDate, $finishDate, $created_by);
            }
        } else {
            $updateQuery = "UPDATE leaves SET user_id = ?, division_id = ?, reason = ?, category = ?, start_date = ?, finish_date = ?, updated_by = ? WHERE leaves_id = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);
            if ($stmt) {
                $updated_by = $user_id;  // Use user_id from the session
                mysqli_stmt_bind_param($stmt, "iisssssi", $fullname, $division, $reason, $category, $startDate, $finishDate, $updated_by, $leave_id);
            }
        }

          if ($stmt) {
              if (mysqli_stmt_execute($stmt)) {
                  // Redirect to the leave list page if the operation is successful
                  echo "<script>alert('Leave data updated successfully.')</script>";
                  echo "<script>window.location.href = 'leavelist.php'</script>";
                  exit();
              } else {
                  $error = "Failed to update data in the database: " . mysqli_error($conn);
              }
              mysqli_stmt_close($stmt);
          } else {
              $error = "Failed to create a prepared statement for data update.";
          }
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
    <title>OLAMS - Edit Leave</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3"><strong>Edit Leave</strong></h1>
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
                                    <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF'] . "?id=" . $leave_id); ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputName">Name</label>
                                                <span style="color: red">*</span>
                                                <select class="form-select" id="inputName" name="name">
                                                    <option value="">Select Name</option>
                                                    <?php foreach ($resultUsers as $user) { ?>
                                                        <option value="<?= $user['user_id'] ?>" <?= ($user['user_id'] == $fullname) ? 'selected' : '' ?>>
                                                            <?= $user['name'] ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo $fullnameErr; ?></span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputDivision">Division</label>
                                                <span style="color: red">*</span>
                                                <select class="form-select" id="inputDivision" name="division">
                                                    <option value="">Select Division</option>
                                                    <?php foreach ($resultDivision as $value) { ?>
                                                        <option value="<?= $value['division_id'] ?>" <?= ($value['division_id'] == $division) ? 'selected' : '' ?>>
                                                            <?= $value['division_name'] ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo $divisionErr; ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputReason">Reason</label>
                                                <span style="color: red">*</span>
                                                <textarea class="form-control" id="inputReason" name="reason" rows="4"><?= $reason ?></textarea>
                                                <span class="text-danger"><?php echo $reasonErr; ?></span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputCategory">Category</label>
                                                <span style="color: red">*</span>
                                                <input type="text" class="form-control" id="inputCategory" name="category" value="<?= $category ?>">
                                                <span class="text-danger"><?php echo $categoryErr; ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputStartDate">Start Date</label>
                                                <span style="color: red">*</span>
                                                <input type="datetime-local" class="form-control" id="inputStartDate" name="startDate" value="<?= $startDate ?>">
                                                <span class="text-danger"><?php echo $startDateErr; ?></span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputFinishDate">Finish Date</label>
                                                <span style="color: red">*</span>
                                                <input type="datetime-local" class="form-control" id="inputFinishDate" name="finishDate" value="<?= $finishDate ?>">
                                                <span class="text-danger"><?php echo $finishDateErr; ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <button type="submit" class="btn btn-primary">Update</button>
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
