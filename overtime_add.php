<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login/login.php");
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
        // $effective_time = isset($_POST["effective_time"]) ? cleanValue($_POST["effective_time"]) : NULL;

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
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputUser">User</label>
                                                <select name="user_id" id="inputUser" class="form-select">
                                                    <option value="">Select User</option>
                                                    <?php foreach ($resultUsers as $user) : ?>
                                                        <option value="<?= $user['user_id'] ?>"><?= $user['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="error" style="color: red;"> <?= $fullnameErr; ?> </span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputProject">Project</label>
                                                <select name="project_id" id="inputProject" class="form-select">
                                                    <option value="">Select Project</option>
                                                    <?php foreach ($resultProject as $project) : ?>
                                                        <option value="<?= $project['project_id'] ?>"><?= $project['project_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="error" style="color: red;"> <?= $projectErr; ?> </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputDivision">Division</label>
                                                <select name="divisi_id" id="inputDivision" class="form-select">
                                                    <option value="">Select Division</option>
                                                    <?php foreach ($resultDivision as $division) : ?>
                                                        <option value="<?= $division['division_id'] ?>"><?= $division['division_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="error" style="color: red;"> <?= $divisionErr; ?> </span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Category</label>
                                                <?php $categoryOptions = ["Weekend", "Weekday"];
                                                foreach ($categoryOptions as $categoryOption) : ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="category" id="category_<?= $categoryOption ?>" value="<?= $categoryOption ?>" <?= $category == $categoryOption ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="category_<?= $categoryOption ?>"><?= $categoryOption ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                                <span class="error" style="color: red;"> <?= $categoryErr; ?> </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Type</label>
                                                <?php $typeOptions = ["Normal", "Urgent", "Business Trip"];
                                                foreach ($typeOptions as $typeOption) : ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="type" id="type_<?= $typeOption ?>" value="<?= $typeOption ?>" <?= $type == $typeOption ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="type_<?= $typeOption ?>"><?= $typeOption ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputStartDate">Start Date</label>
                                                <input type="datetime-local" class="form-control" name="start_date" id="inputStartDate" value="<?= $start_date; ?>">
                                                <span class="error" style="color: red;"> <?= $start_dateErr; ?> </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputFinishDate">Finish Date</label>
                                                <input type="datetime-local" class="form-control" name="finish_date" id="inputFinishDate" value="<?= $finish_date; ?>">
                                                <span class="error" style="color: red;"> <?= $finish_dateErr; ?> </span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputReason">Reason</label>
                                                <textarea class="form-control" name="reason" id="inputReason" placeholder="Enter Reason"><?= $reason; ?></textarea>
                                                <span class="error" style="color: red;"> <?= $reasonErr; ?> </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                <a href="overtimelist.php" class="btn btn-danger text-white text-decoration-none">Cancel</a>
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