<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

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
$fullname = $division = $reason = $type = $startDate = $finishDate = "";
$attendance_id = null;

if (isset($_GET['id'])) {
    $attendance_id = cleanValue($_GET['id']);

    $queryAttendance = "SELECT user_id, division_id, reason, type, start_date, finish_date FROM attendances WHERE attendance_id = ?";
    $stmt = mysqli_prepare($conn, $queryAttendance);
    mysqli_stmt_bind_param($stmt, "i", $attendance_id);
    mysqli_stmt_execute($stmt);
    $resultAttendance = mysqli_stmt_get_result($stmt);

    if ($resultAttendance && mysqli_num_rows($resultAttendance) > 0) {
        $attendanceData = mysqli_fetch_assoc($resultAttendance);
        $fullname = $attendanceData['user_id'];
        $division = $attendanceData['division_id'];
        $reason = $attendanceData['reason'];
        $type = $attendanceData['type'];
        $startDate = $attendanceData['start_date'];
        $finishDate = $attendanceData['finish_date'];
    } else {
        echo "Invalid attendance ID.";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
        $fullname = isset($_POST["name"]) ? cleanValue($_POST["name"]) : "";
        $division = isset($_POST["division"]) ? cleanValue($_POST["division"]) : "";
        $reason = isset($_POST["reason"]) ? cleanValue($_POST["reason"]) : "";
        $type = isset($_POST["type"]) ? cleanValue($_POST["type"]) : "";
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

        if (empty($type)) {
            $typeErr = "Type is required.";
        }

        if (empty($startDate)) {
            $startDateErr = "Start Date is required.";
        }

        if (empty($finishDate)) {
            $finishDateErr = "Finish Date is required.";
        } elseif (strtotime($finishDate) <= strtotime($startDate)) {
            $finishDateErr = "Finish Date must be later than Start Date.";
        }

        if (empty($fullnameErr) && empty($divisionErr) && empty($reasonErr) && empty($typeErr) && empty($startDateErr) && empty($finishDateErr)) {
            $checkDuplicateQuery = "SELECT COUNT(*) FROM attendances WHERE user_id = ? AND start_date = ? AND attendance_id != ?";
            $stmt = mysqli_prepare($conn, $checkDuplicateQuery);
            mysqli_stmt_bind_param($stmt, "isi", $fullname, $startDate, $attendance_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $error = "An entry for the selected user and start date already exists. Update is not allowed.";
            } else {
                $updateQuery = "UPDATE attendances SET user_id = ?, division_id = ?, reason = ?, type = ?, start_date = ?, finish_date = ?, updated_by = ? WHERE attendance_id = ?";
                $stmt = mysqli_prepare($conn, $updateQuery);
                if ($stmt) {
                    $user_id = $_SESSION["user_id"];
                    mysqli_stmt_bind_param($stmt, "iissssii", $fullname, $division, $reason, $type, $startDate, $finishDate, $user_id, $attendance_id);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "<script>alert('Attendance data updated successfully.')</script>";
                        echo "<script>window.location.href = 'attendancelist.php'</script>";
                        exit();
                    } else {
                        $error = "Failed to update data in the database.";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Failed to create a prepared statement for data update.";
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
    <title>OLAMS - Edit Attendance</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3"><strong>Edit Attendance</strong></h1>
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
                                    <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF'] . "?id=" . $attendance_id); ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputName">User</label>
                                                <span style="color: red">*</span>
                                                <select class="form-select" id="inputName" name="name">
                                                    <option value="">Select Name</option>
                                                    <?php foreach ($resultUsers as $user) { ?>
                                                        <option value="<?= $user['user_id'] ?>" <?= ($user['user_id'] == $fullname) ? 'selected' : '' ?>><?= $user['name'] ?></option>
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
                                                        <option value="<?= $value['division_id'] ?>" <?= ($value['division_id'] == $division) ? 'selected' : '' ?>><?= $value['division_name'] ?></option>
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
                                                <label class="form-label" for="inputType">Type</label>
                                                <span style="color: red">*</span>
                                                <select class="form-select" id="inputType" name="type">
                                                    <option value="">Select Type</option>
                                                    <?php foreach ($resultType as $typeData) { ?>
                                                        <option value="<?= $typeData['type'] ?>" <?= ($typeData['type'] == $type) ? 'selected' : '' ?>><?= $typeData['type'] ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo $typeErr; ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputStartDate">Start Date</label>
                                                <span style="color: red">*</span>
                                                <input type="date" class="form-control" id="inputStartDate" name="startDate" value="<?= $startDate ?>">
                                                <span class="text-danger"><?php echo $startDateErr; ?></span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputFinishDate">Finish Date</label>
                                                <span style="color: red">*</span>
                                                <input type="date" class="form-control" id="inputFinishDate" name="finishDate" value="<?= $finishDate ?>">
                                                <span class="text-danger"><?php echo $finishDateErr; ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($fullnameErr) && !empty($divisionErr) && !empty($reasonErr) && !empty($typeErr) && !empty($startDateErr) && !empty($finishDateErr)) : ?>
                                                    <button type="button" name="submit" class="btn btn-primary">Update</button>
                                                <?php else : ?>
                                                    <button type="submit" class="btn btn-primary" onclick="return confirm('are you sure you will update?')">Update</button>
                                                <?php endif; ?>
                                                <a href="attendancelist.php" class="btn btn-light text-dark text-decoration-none">Cancel</a>
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