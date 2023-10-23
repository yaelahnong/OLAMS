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

$queryProject = "SELECT project_id, project_name FROM m_projects";
$projectData = mysqli_prepare($conn, $queryProject);
mysqli_stmt_execute($projectData);
$resultProject = mysqli_stmt_get_result($projectData);
$resultProject = mysqli_fetch_all($resultProject, MYSQLI_ASSOC);
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
                                                    <?php foreach ($userOptions as $user) : ?>
                                                        <option value="<?= $user['user_id'] ?>"><?= $user['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <!-- <span class="error" style="color: red;"> <?= $userErr; ?> </span> -->
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputProject">Project</label>
                                                <select name="project_id" id="inputProject" class="form-select">
                                                    <option value="">Select Project</option>
                                                    <?php foreach ($projectOptions as $project) : ?>
                                                        <option value="<?= $project['project_id'] ?>"><?= $project['project_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <!-- <span class="error" style="color: red;"> <?= $projectErr; ?> </span> -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputDivision">Division</label>
                                                <select name="divisi_id" id="inputDivision" class="form-select">
                                                    <option value="">Select Division</option>
                                                    <?php foreach ($divisionOptions as $division) : ?>
                                                        <option value="<?= $division['division_id'] ?>"><?= $division['division_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <!-- <span class="error" style="color: red;"> <?= $divisionErr; ?> </span> -->
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputCategory">Category</label>
                                                <input type="text" class="form-control" name="category" id="inputCategory" placeholder="Enter Category" value="<?= $category; ?>">
                                                <!-- <span class="error" style="color: red;"> <?= $categoryErr; ?> </span> -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputType">Type</label>
                                                <input type="text" class="form-control" name="type" id="inputType" placeholder="Enter Type" value="<?= $type; ?>">
                                                <!-- <span class="error" style="color: red;"> <?= $typeErr; ?> </span> -->
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputStartDate">Start Date</label>
                                                <input type="datetime-local" class="form-control" name="start_date" id="inputStartDate" value="<?= $start_date; ?>">
                                                <!-- <span class="error" style="color: red;"> <?= $start_dateErr; ?> </span> -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputFinishDate">Finish Date</label>
                                                <input type="datetime-local" class="form-control" name="finish_date" id="inputFinishDate" value="<?= $finish_date; ?>">
                                                <!-- <span class="error" style="color: red;"> <?= $finish_dateErr; ?> </span> -->
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputEffectiveTime">Effective Time</label>
                                                <input type="text" class="form-control" name="effective_time" id="inputEffectiveTime" placeholder="Enter Effective Time" value="<?= $effective_time; ?>">
                                                <!-- <span class="error" style="color: red;"> <?= $effective_timeErr; ?> </span> -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputReason">Reason</label>
                                                <textarea class="form-control" name="reason" id="inputReason" placeholder="Enter Reason"><?= $reason; ?></textarea>
                                                <!-- <span class="error" style="color: red;"> <?= $reasonErr; ?> </span> -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                <a href="overtime_list.php" class="btn btn-danger text-white text-decoration-none">Cancel</a>
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