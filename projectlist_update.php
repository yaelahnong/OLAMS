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

$projectNameErr = $startDateErr = $finishDateErr = "";
$projectName = $startDate = $finishDate = null;
$projectData = [];
if (isset($_GET['id'])) {
    $project_id = cleanValue($_GET['id']);

    // Query untuk mengambil data proyek berdasarkan project_id
    $queryProject = "SELECT project_name, start_date, finish_date FROM m_projects WHERE project_id = ?";
    $stmt = mysqli_prepare($conn, $queryProject);
    mysqli_stmt_bind_param($stmt, "i", $project_id);
    mysqli_stmt_execute($stmt);
    $projectData = mysqli_stmt_get_result($stmt);

    if ($projectData && mysqli_num_rows($projectData) > 0) {
        $projectData = mysqli_fetch_assoc($projectData);
        $startDate = $projectData['start_date'];
        $finishDate = $projectData['finish_date'];
    } else {
        echo "Invalid attendance ID.";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {

        $projectName = isset($_POST["project_name"]) ? cleanValue($_POST["project_name"]) : NULL;
        $startDate = isset($_POST["start_date"]) ? cleanValue($_POST["start_date"]) : NULL;
        $finishDate = isset($_POST["finish_date"]) ? cleanValue($_POST["finish_date"]) : NULL;

        if (empty($projectName)) {
            $projectNameErr = "Project Name is required.";
        } elseif (!preg_match("/^[a-zA-Z0-9 ]*$/", $projectName)) {
            $projectNameErr = "Input can only contain letters, numbers, and spaces.";
        }
        if (empty($startDate)) {
            $startDateErr = "Start Date is required.";
        }
        if (empty($finishDate)) {
            $finishDateErr = "Finish Date is required.";
        } elseif (strtotime($finishDate) < strtotime($startDate)) {
            $finishDateErr = "Finish Date cannot be earlier than Start Date.";
        }

        if (empty($projectNameErr) && empty($startDateErr) && empty($finishDateErr)) {
            $checkProjectName = "SELECT project_name FROM m_projects WHERE project_name = ? AND is_deleted = 'N'";
            $checkStmt = mysqli_prepare($conn, $checkProjectName);

            if ($checkStmt) {
                mysqli_stmt_bind_param($checkStmt, "s", $projectName);
                mysqli_stmt_execute($checkStmt);
                $checkResult = mysqli_stmt_get_result($checkStmt);

                if (mysqli_num_rows($checkResult) > 0) {
                    $error = "Project with the same name already exists.";
                } else {
                    if (isset($_GET['id'])) {
                        $project_id = cleanValue($_GET['id']);
                        $updateQuery = "UPDATE m_projects SET project_name = ?, start_date = ?, finish_date = ?, updated_by = ? WHERE project_id = ?";
                        $stmt = mysqli_prepare($conn, $updateQuery);

                        if ($stmt) {
                            $user_id = $_SESSION["user_id"];
                            mysqli_stmt_bind_param($stmt, "sssii", $projectName, $startDate, $finishDate, $user_id, $project_id);

                            if (mysqli_stmt_execute($stmt)) {
                                echo "<script>alert('Project data updated successfully.')</script>";
                                echo "<script>window.location.href = 'projectlist.php'</script>";
                                exit();
                            } else {
                                $error = "Failed to update the project.";
                            }
                            mysqli_stmt_close($stmt);
                        } else {
                            $error = "Failed to create a prepared statement.";
                        }
                    } else {
                        $error = "Project ID not provided.";
                    }
                }
                mysqli_stmt_close($checkStmt);
            } else {
                $error = "Failed to make a prepared statement for duplicate check.";
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
    <title>OLAMS - Edit project</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3"><strong>Edit Project</strong></h1>
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
                                    <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF'] . "?id=$project_id"); ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputProjectName">Projet Name</label>
                                                <span style="color: red">*</span><br>
                                                <input type="text" class="form-control" name="project_name" id="inputProjectName" placeholder="Enter Project Name" value="<?= $projectData['project_name'] ?>">
                                                <span class="text-danger"><?php echo $projectNameErr; ?></span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputStartDate">Start Date</label>
                                                <span style="color: red">*</span>
                                                <input type="date" class="form-control datepicker" id="inputStartDate" name="start_date" value="<?= $startDate; ?>">
                                                <span class="text-danger"><?php echo $startDateErr; ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputFinishDate">Finish Date</label>
                                                <span style="color: red">*</span>
                                                <input type="date" class="form-control" id="inputFinishDate" name="finish_date" value="<?= $finishDate ?>">
                                                <span class="text-danger"><?php echo $finishDateErr; ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($projectNameErr) && empty($startDateErr) && empty($finishDateErr) && !$error) : ?>
                                                    <button type="button" name="update" class="btn btn-primary">Update</button>
                                                <?php else : ?>
                                                    <button type="submit" name="update" class="btn btn-primary" onclick="return confirm('are you sure you will update?')">Update</button>
                                                <?php endif; ?>
                                                <a href="projectlist.php" class="btn btn-light text-dark text-decoration-none">Cancel</a>
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