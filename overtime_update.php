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

$queryProject = "SELECT project_id, project_name FROM m_projects WHERE is_deleted='N'";
$projectData = mysqli_prepare($conn, $queryProject);
mysqli_stmt_execute($projectData);
$resultProject = mysqli_stmt_get_result($projectData);
$resultProject = mysqli_fetch_all($resultProject, MYSQLI_ASSOC);

if (isset($_GET['id'])) {
    $overtimeId = cleanValue($_GET['id']);
    $queryOvertime = "SELECT 
    overtimes.user_id, 
    m_projects.project_name AS project_name,
    m_divisions.division_name AS divisi_name,
    overtimes.project_id, 
    overtimes.divisi_id, 
    overtimes.category, 
    overtimes.type, 
    overtimes.reason, 
    overtimes.start_date, 
    overtimes.finish_date, 
    overtimes.effective_time, 
    overtimes.status 
    FROM overtimes 
    LEFT JOIN m_projects ON overtimes.project_id = m_projects.project_id 
    LEFT JOIN m_divisions ON overtimes.divisi_id = m_divisions.division_id 
    WHERE overtime_id = ?";
    $stmt = mysqli_prepare($conn, $queryOvertime);
    mysqli_stmt_bind_param($stmt, "i", $overtimeId);
    mysqli_stmt_execute($stmt);
    $resultOvertime = mysqli_stmt_get_result($stmt);

    if ($resultOvertime) {
        $row = mysqli_fetch_assoc($resultOvertime);
        $user_id = $row['user_id'];
        $project_id = $row['project_id'];
        $project_name = $row['project_name'];
        $divisi_id = $row['divisi_id'];
        $divisi_name = $row['divisi_name'];
        $category = $row['category'];
        $type = $row['type'];
        $reason = $row['reason'];
        $start_date = date('Y-m-d\TH:i', strtotime($row['start_date']));
        $finish_date = date('Y-m-d\TH:i', strtotime($row['finish_date']));
        $effective_time = $row['effective_time'];
        $status = $row['status'];
    }
}


$fullnameErr = $divisionErr = $reasonErr = $typeErr = $start_dateErr = $finish_dateErr = $categoryErr = $projectErr = $effective_timeErr = "";
if (isset($_POST['update']) && isset($_POST['overtime_id'])) {
    if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
        $fullname = isset($_POST["user_id"]) ? cleanValue($_POST["user_id"]) : NULL;
        $project_id = isset($_POST["project_id"]) ? cleanValue($_POST["project_id"]) : NULL;
        $divisi_id = isset($_POST["divisi_id"]) ? cleanValue($_POST["divisi_id"]) : NULL;
        $category = isset($_POST["category"]) ? cleanValue($_POST["category"]) : NULL;
        $type = isset($_POST["type"]) ? cleanValue($_POST["type"]) : NULL;
        $reason = isset($_POST["reason"]) ? cleanValue($_POST["reason"]) : NULL;
        $start_date = isset($_POST["start_date"]) ? cleanValue($_POST["start_date"]) : NULL;
        $finish_date = isset($_POST["finish_date"]) ? cleanValue($_POST["finish_date"]) : NULL;
        $effective_time = isset($_POST["effective_time"]) ? cleanValue($_POST["effective_time"]) : NULL;

        if (empty($fullname)) {
            $fullnameErr = "Full Name is required";
        }
        if (empty($project_id)) {
            $projectErr = "Project is required";
        }
        if (empty($divisi_id)) {
            $divisionErr = "Division is required";
        }
        if (empty($category)) {
            $categoryErr = "Category is required";
        }
        if (empty($type)) {
            $typeErr = "Type is required";
        }
        if (empty($start_date)) {
            $start_dateErr = "Start Date is required";
        }
        if (empty($finish_date)) {
            $finish_dateErr = "Finish Date is required";
        }
        if (empty($reason)) {
            $reasonErr = "Reason is required";
        }
        if ($status === 'Approved') {
            // Validasi jika status sudah "Approved" dan "Effective Time" harus diisi dan tidak boleh angka minus
            if (empty($effective_time)) {
                $effective_timeErr = "Effective Time is required.";
            } elseif (!is_numeric($effective_time) || intval($effective_time) < 0) {
                $effective_timeErr = "Effective Time must be a non-negative integer.";
            }
        } else {
            // Jika status bukan "Approved", pastikan "Effective Time" tidak diisi
            if (!empty($effective_time)) {
                $effective_timeErr = "Effective Time can only be filled when status is Approved.";
            }
        }
        if (empty($fullnameErr) && empty($projectErr) && empty($divisionErr) && empty($categoryErr) && empty($typeErr) && empty($start_dateErr) && empty($finish_dateErr) && empty($reasonErr) && empty($effective_timeErr)) {
            if ($status === 'Approved') {
                // Query update dengan kolom "Effective Time"
                $updateQuery = "UPDATE overtimes SET user_id = ?, project_id = ?, divisi_id = ?, category = ?, type = ?, start_date = ?, finish_date = ?, reason = ?, effective_time = ?, updated_by = ? WHERE overtime_id = ?";
                $updateStatement = mysqli_prepare($conn, $updateQuery);
                mysqli_stmt_bind_param($updateStatement, "iiisssssiii", $fullname, $project_id, $divisi_id, $category, $type, $start_date, $finish_date, $reason, $effective_time, $user_id, $overtimeId);
            } else {
                // Query update tanpa kolom "Effective Time"
                $updateQuery = "UPDATE overtimes SET user_id = ?, project_id = ?, divisi_id = ?, category = ?, type = ?, start_date = ?, finish_date = ?, reason = ?, updated_by = ? WHERE overtime_id = ?";
                $updateStatement = mysqli_prepare($conn, $updateQuery);
                mysqli_stmt_bind_param($updateStatement, "iiisssssii", $fullname, $project_id, $divisi_id, $category, $type, $start_date, $finish_date, $reason, $user_id, $overtimeId);
            }
            if (mysqli_stmt_execute($updateStatement)) {
                echo "<script>alert('Overtime data updated successfully.')</script>";
                echo "<script>window.location.href = 'overtimelist.php'</script>";
                exit();
            } else {
                echo "Failed to update data.";
            }

            mysqli_stmt_close($updateStatement);
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
    <title>OLAMS - Update Overtime</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3"><strong>Update Overtime</strong></h1>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="card-body">
                                    <form action="<?= cleanValue($_SERVER['PHP_SELF'] . "?id=" . $overtimeId) ?>" method="post">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <?php if (isset($status) && ($status === 'Pending' || $status === 'Rejected')) : ?>
                                            <div class="row">
                                                <div class="mb-3 col-md-6 d-none">
                                                    <label class="form-label" for="inputUser">User</label>
                                                    <input type="text" name="user_id" id="inputUser" class="form-control" value="<?= $_SESSION["user_id"]; ?>" readonly>
                                                    <span class="error" style="color: red;"> <?= $fullnameErr; ?> </span>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label" for="inputProject">Project</label>
                                                    <select name="project_id" id="inputProject" class="form-select">
                                                        <option value="">Select Project</option>
                                                        <?php foreach ($resultProject as $project) : ?>
                                                            <option value="<?= $project['project_id'] ?>" <?= $project['project_id'] == $project_id ? 'selected' : '' ?>><?= $project['project_name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <span class="error" style="color: red;"> <?= $projectErr; ?> </span>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label" for="inputDivision">Division</label>
                                                    <select name="divisi_id" id="inputDivision" class="form-select">
                                                        <option value="">Select Division</option>
                                                        <?php foreach ($resultDivision as $division) : ?>
                                                            <option value="<?= $division['division_id'] ?>" <?= $division['division_id'] == $divisi_id ? 'selected' : '' ?>><?= $division['division_name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <span class="error" style="color: red;"> <?= $divisionErr; ?> </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Category</label><br>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="category" id="category_weekend" value="Weekend" <?php if (isset($category) && $category === "Weekend") echo "checked"; ?>>
                                                        <label class="form-check-label" for="category_weekend">Weekend</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="category" id="category_weekday" value="Weekday" <?php if (isset($category) && $category === "Weekday") echo "checked"; ?>>
                                                        <label class="form-check-label" for="category_weekday">Weekday</label>
                                                    </div>
                                                    <span class="error" style="color: red;"><?= $categoryErr; ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Type</label><br>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="type" id="type_Normal" value="Normal" <?php if (isset($type) && $type === "Normal") echo "checked"; ?>>
                                                        <label class="form-check-label" for="type_Normal">Normal</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="type" id="type_Urgent" value="Urgent" <?php if (isset($type) && $type === "Urgent") echo "checked"; ?>>
                                                        <label class="form-check-label" for="type_Urgent">Urgent</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="type" id="type_BusinessTrip" value="Business Trip" <?php if (isset($type) && $type === "Business Trip") echo "checked"; ?>>
                                                        <label class="form-check-label" for="type_BusinessTrip">Business Trip</label>
                                                    </div>
                                                    <span class="error" style="color: red;"><?= $typeErr; ?></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label" for="inputStartDate">Start Date</label>
                                                    <input type="datetime-local" class="form-control" name="start_date" id="inputStartDate" value="<?= $start_date; ?>">
                                                    <span class="error" style="color: red;"> <?= $start_dateErr; ?> </span>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label" for="inputFinishDate">Finish Date</label>
                                                    <input type="datetime-local" class="form-control" name="finish_date" id="inputFinishDate" value="<?= $finish_date; ?>">
                                                    <span class="error" style="color: red;"> <?= $finish_dateErr; ?> </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label" for="inputReason">Reason</label>
                                                    <textarea class="form-control" name="reason" id="inputReason" placeholder="Enter Reason"><?= $reason; ?></textarea>
                                                    <span class="error" style="color: red;"> <?= $reasonErr; ?> </span>
                                                </div>
                                            </div>
                                        <?php elseif (isset($status) && ($status === 'Approved')) : ?>
                                            <!-- perbedaan complit dan edit -->
                                            <div class="row">
                                                <div class="mb-3 col-md-6 d-none">
                                                    <label class="form-label" for="inputUser">User</label>
                                                    <input type="text" name="user_id" id="inputUser" class="form-control" value="<?= $_SESSION["user_id"]; ?>" readonly>
                                                    <span class="error" style="color: red;"> <?= $fullnameErr; ?> </span>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label" for="inputProject">Proyek</label>
                                                    <input type="text" name="project_name" id="inputProjectName" class="form-control" value="<?= $project_name; ?>" readonly>
                                                    <input type="hidden" name="project_id" value="<?= $project_id; ?>">
                                                    <span class="error" style="color: red;"> <?= $projectErr; ?> </span>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label" for="inputDivision">Divisi</label>
                                                    <input type="text" name="division_name" id="inputDivisionName" class="form-control" value="<?= $divisi_name; ?>" readonly>
                                                    <input type="hidden" name="divisi_id" value="<?= $divisi_id; ?>">
                                                    <span class="error" style="color: red;"> <?= $divisionErr; ?> </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Category</label><br>
                                                    <input class="form-control" type="text" name="category" value="<?= $category; ?>" readonly>
                                                    <span class="error" style="color: red;"><?= $categoryErr; ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Type</label><br>
                                                    <input class="form-control" type="text" name="type" value="<?= $type ?>" readonly>
                                                    <span class="error" style="color: red;"><?= $typeErr; ?></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label" for="inputStartDate">Start Date</label>
                                                    <input type="datetime-local" class="form-control" name="start_date" id="inputStartDate" value="<?= $start_date; ?>" readonly>
                                                    <span class="error" style="color: red;"> <?= $start_dateErr; ?> </span>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label" for="inputFinishDate">Finish Date</label>
                                                    <input type="datetime-local" class="form-control" name="finish_date" id="inputFinishDate" value="<?= $finish_date; ?>" readonly>
                                                    <span class="error" style="color: red;"> <?= $finish_dateErr; ?> </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label" for="inputReason">Reason</label>
                                                    <textarea class="form-control" name="reason" id="inputReason" placeholder="Enter Reason" readonly><?= $reason; ?></textarea>
                                                    <span class="error" style="color: red;"> <?= $reasonErr; ?> </span>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label" for="inputEffectiveTime">Effective Time (Hours)</label>
                                                    <input type="number" class="form-control" name="effective_time" id="inputEffectiveTime" placeholder="Enter effective time..." value="<?= $effective_time; ?>">
                                                    <span class="error" style="color: red;"> <?= $effective_timeErr; ?> </span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <input type="hidden" name="overtime_id" value="<?= $overtimeId; ?>">
                                        <div class="row">
                                            <div class="col">
                                                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($fullnameErr) && !empty($projectErr) && !empty($divisionErr) && !empty($categoryErr) && !empty($typeErr) && !empty($start_dateErr) && !empty($finish_dateErr) && !empty($reasonErr) && !empty($effective_timeErr)) : ?>
                                                    <button type="button" name="update" class="btn btn-primary">Update</button>
                                                <?php else : ?>
                                                    <button type="submit" name="update" class="btn btn-primary" onclick="return confirm('are you sure you will update?')">Update</button>
                                                <?php endif; ?>
                                                <a href="overtimelist.php" class="btn btn-light text-dark text-decoration-none">Cancel</a>
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