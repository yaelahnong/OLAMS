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

$fullnameErr = $divisionErr = $reasonErr = $typeErr = $start_dateErr = $finish_dateErr = $categoryErr = $projectErr = "";
$fullname = $division = $reason = $type = $startDate = $finishDate = $category = $project = NULL;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
        $fullname = isset($_POST["user_id"]) ? cleanValue($_POST["user_id"]) : NULL;
        $project_id = isset($_POST["project_id"]) ? cleanValue($_POST["project_id"]) : NULL;
        $divisi_id = isset($_POST["divisi_id"]) ? cleanValue($_POST["divisi_id"]) : NULL;
        $category = isset($_POST["category"]) ? cleanValue($_POST["category"]) : NULL;
        $type = isset($_POST["type"]) ? cleanValue($_POST["type"]) : NULL;
        $reason = isset($_POST["reason"]) ? cleanValue($_POST["reason"]) : NULL;
        $start_date = isset($_POST["start_date"]) ? cleanValue($_POST["start_date"]) : NULL;
        $finish_date = isset($_POST["finish_date"]) ? cleanValue($_POST["finish_date"]) : NULL;

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

        if (empty($fullnameErr) && empty($projectErr) && empty($divisionErr) && empty($categoryErr) && empty($typeErr) && empty($start_dateErr) && empty($finish_dateErr) && empty($reasonErr)) {
            $insertQuery = "INSERT INTO overtimes (user_id, project_id, divisi_id, category, type, start_date, finish_date, reason, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $insertStatement = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($insertStatement, "iiisssssi", $fullname, $project_id, $divisi_id, $category, $type, $start_date, $finish_date, $reason, $user_id);

            if (mysqli_stmt_execute($insertStatement)) {
                $overtime_id = mysqli_insert_id($conn);
                $insertHistoryQuery = "INSERT INTO overtimes_histories (overtime_id, user_id, created_by) VALUES (?, ?, ?)";
                $insertHistoryStatement = mysqli_prepare($conn, $insertHistoryQuery);
                mysqli_stmt_bind_param($insertHistoryStatement, "iii", $overtime_id, $fullname, $user_id);
                mysqli_stmt_execute($insertHistoryStatement);

                echo "<script>alert('Overtime data added successfully.')</script>";
                echo "<script>window.location.href = 'overtimelist.php'</script>";
                exit();
            } else {
                echo "Failed to save data.";
            }

            mysqli_stmt_close($insertStatement);
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
    <title>OLAMS - Add Overtime</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3"><strong>Add Overtime</strong></h1>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="card-body">
                                    <form action="<?= cleanValue($_SERVER['PHP_SELF']) ?>" method="post">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <div class="row">
                                            <div class="mb-3 col-md-6 d-none">
                                                <label class="form-label" for="inputUser">User</label>
                                                <input type="text" name="user_id" id="inputUser" class="form-control" value="<?= $_SESSION["user_id"]; ?>" readonly>
                                                <span class="error" style="color: red;"> <?= $fullnameErr; ?> </span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputProject">Project</label>
                                                <span style="color: red">*</span>
                                                <select name="project_id" id="inputProject" class="form-select">
                                                    <option value="">Select Project</option>
                                                    <?php foreach ($resultProject as $project) : ?>
                                                        <option value="<?= $project['project_id'] ?>"><?= $project['project_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="error" style="color: red;"> <?= $projectErr; ?> </span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputDivision">Division</label>
                                                <span style="color: red">*</span>
                                                <select name="divisi_id" id="inputDivision" class="form-select">
                                                    <option value="">Select Division</option>
                                                    <?php foreach ($resultDivision as $division) : ?>
                                                        <option value="<?= $division['division_id'] ?>"><?= $division['division_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="error" style="color: red;"> <?= $divisionErr; ?> </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Category</label>
                                                <span style="color: red">*</span><br>
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
                                                <label class="form-label">Type</label>
                                                <span style="color: red">*</span><br>
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
                                                <span style="color: red">*</span>
                                                <input type="datetime-local" class="form-control" name="start_date" id="inputStartDate">
                                                <span class="error" style="color: red;"> <?= $start_dateErr; ?> </span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputFinishDate">Finish Date</label>
                                                <span style="color: red">*</span>
                                                <input type="datetime-local" class="form-control" name="finish_date" id="inputFinishDate">
                                                <span class="error" style="color: red;"> <?= $finish_dateErr; ?> </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputReason">Reason</label>
                                                <span style="color: red">*</span>
                                                <textarea class="form-control" name="reason" id="inputReason" placeholder="Enter Reason"><?= $reason; ?></textarea>
                                                <span class="error" style="color: red;"> <?= $reasonErr; ?> </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($fullnameErr) && !empty($projectErr) && !empty($divisionErr) && !empty($categoryErr) && !empty($typeErr) && !empty($start_dateErr) && !empty($finish_dateErr) && !empty($reasonErr) ) : ?>
                                                    <button type="button" class="btn btn-primary">Submit</button>
                                                <?php else : ?>
                                                    <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to add it?')">Submit</button>
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