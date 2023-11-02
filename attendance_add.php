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

$queryType = "SELECT DISTINCT type FROM attendances";
$typeData = mysqli_prepare($conn, $queryType);
mysqli_stmt_execute($typeData);
$resultType = mysqli_stmt_get_result($typeData);
$resultType = mysqli_fetch_all($resultType, MYSQLI_ASSOC);

$fullnameErr = $divisionErr = $reasonErr = $typeErr = $startDateErr = $finishDateErr = "";
$fullname = $division = $reason = $type = $startDate = $finishDate = NULL;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
        $fullname = isset($_POST["name"]) ? cleanValue($_POST["name"]) : NULL;
        $division = isset($_POST["division"]) ? cleanValue($_POST["division"]) : NULL;
        $reason = isset($_POST["reason"]) ? cleanValue($_POST["reason"]) : NULL;
        $type = isset($_POST["type"]) ? cleanValue($_POST["type"]) : NULL;
        $startDate = isset($_POST["startDate"]) ? cleanValue($_POST["startDate"]) : NULL;
        $finishDate = isset($_POST["finishDate"]) ? cleanValue($_POST["finishDate"]) : NULL;

        // Validasi data yang diterima dari formulir
        if (empty($_POST["name"])) {
            $fullnameErr = "Full Name is required.";
        }

        if (empty($_POST["division"])) {
            $divisionErr = "Division is required.";
        }

        if (empty($_POST["reason"])) {
            $reasonErr = "Reason is required.";
        }

        if (empty($_POST["type"])) {
            $typeErr = "Type is required.";
        }

        if (empty($startDate)) {
            $startDateErr = "Start Date is required.";
        }

        if (empty($finishDate)) {
            $finishDateErr = "Finish Date is required.";
        } elseif (strtotime($finishDate) < strtotime($startDate)) {
            $finishDateErr = "Finish Date cannot be earlier than Start Date.";
        }

        if (empty($fullnameErr) && empty($divisionErr) && empty($reasonErr) && empty($typeErr) && empty($startDateErr) && empty($finishDateErr)) {
            $checkDuplicateQuery = "SELECT COUNT(*) FROM attendances WHERE user_id = ? AND DATE(start_date) = ?";
            $checkDuplicateStmt = mysqli_prepare($conn, $checkDuplicateQuery);
            mysqli_stmt_bind_param($checkDuplicateStmt, "is", $fullname, $startDate);
            mysqli_stmt_execute($checkDuplicateStmt);
            mysqli_stmt_store_result($checkDuplicateStmt);

            mysqli_stmt_bind_result($checkDuplicateStmt, $rowCount);
            mysqli_stmt_fetch($checkDuplicateStmt);

            if ($rowCount > 0) {
                $error = "Attendance data with the same name and start date already exists for the selected date.";
            } else {
                
                $insertQuery = "INSERT INTO attendances (user_id, division_id, reason, type, start_date, finish_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = mysqli_prepare($conn, $insertQuery);
                if ($insertStmt) {
                    mysqli_stmt_bind_param($insertStmt, "iissssi", $fullname, $division, $reason, $type, $startDate, $finishDate, $user_id);
                    if (mysqli_stmt_execute($insertStmt)) {
                        echo "<script>alert('Attendance data added successfully.')</script>";
                        echo "<script>window.location.href = 'attendancelist.php'</script>";
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
    } else {
        $TokenErr = "Invalid CSRF token";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.inc.php"; ?>
    <title>OLAMS - Add Attendance</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3"><strong>Add Attendance</strong></h1>
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
                                            <button type="button" class="btn-close align-items-end" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    <?php } ?>
                                    <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputName">User</label>
                                                <span style="color: red">*</span>
                                                <select class="form-select" id="inputName" name="name">
                                                    <option value="">Select Name</option>
                                                    <?php foreach ($resultUsers as $key => $user) { ?>
                                                    <option value="<?= $user['user_id'] ?>"><?= $user['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo $fullnameErr; ?></span>
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
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputReason">Reason</label>
                                                <span style="color: red">*</span>
                                                <textarea class="form-control" id="inputReason" name="reason" rows="4"></textarea>
                                                <span class="text-danger"><?php echo $reasonErr; ?></span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputType">Type</label>
                                                <span style="color: red">*</span>
                                                <select class="form-select" id="inputType" name="type">
                                                    <option value="">Select Type</option>
                                                    <?php foreach ($resultType as $type) { ?>
                                                    <option value="<?= $type['type'] ?>"><?= $type['type'] ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo $typeErr; ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputStartDate">Start Date</label>
                                                <span style="color: red">*</span>
                                                <input type="date" class="form-control datepicker" id="inputStartDate"
                                                    name="startDate">
                                                <span class="text-danger"><?php echo $startDateErr; ?></span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputFinishDate">Finish Date</label>
                                                <span style="color: red">*</span>
                                                <input type="date" class="form-control" id="inputFinishDate"
                                                    name="finishDate">
                                                <span class="text-danger"><?php echo $finishDateErr; ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                <a href="attendancelist.php"
                                                    class="btn btn-light text-dark text-decoration-none">Cancel</a>
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
