<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

// Cek apakah pengguna sudah login
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

// Batasi Hak Akses User
if ($_SESSION['role_id'] != 3 && $_SESSION['role_id'] != 4 && $_SESSION['role_id'] != 2) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$projectNameErr = $startDateErr = $finishDateErr = "";
$projectName = $startDate = $finishDate = null;

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
                    // Hanya membuat prepared statement jika tidak ada error
                    $insertQuery = "INSERT INTO m_projects (project_name, start_date, finish_date, created_by) VALUES (?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $insertQuery);

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "sssi", $projectName, $startDate, $finishDate, $user_id);
                        
                        if (mysqli_stmt_execute($stmt)) {
                            echo "<script>alert('Data added successfully');</script>";
                            echo "<script>window.location.href = 'projectlist.php'</script>";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>OLAMS - Add project</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3"><strong>Add Project</strong></h1>
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
                                                <label class="form-label" for="inputProjectName">Project Name</label>
                                                <span style="color: red">*</span><br>
                                                <input type="text" class="form-control" name="project_name" id="inputProjectName" placeholder="Enter Project Name">
                                                <span class="text-danger"><?php echo $projectNameErr; ?></span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputStartDate">Start Date</label>
                                                <span style="color: red">*</span>
                                                <input type="date" class="form-control datepicker" id="inputStartDate" name="start_date">
                                                <span class="text-danger"><?php echo $startDateErr; ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputFinishDate">Finish Date</label>
                                                <span style="color: red">*</span>
                                                <input type="date" class="form-control" id="inputFinishDate" name="finish_date">
                                                <span class="text-danger"><?php echo $finishDateErr; ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($projectNameErr) && empty($startDateErr) && empty($finishDateErr) && !$error) : ?>
                                                    <button type="button" class="btn btn-primary">Submit</button>
                                                <?php else : ?>
                                                    <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to add it?')">Submit</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="sweetalert2.all.min.js"></script>
    <script>
        function showSuccessAlert() {
            Swal.fire({
                title: 'Good job!',
                text: 'Project added successfully!',
                icon: 'success'
            }).then(function() {
                window.location.href = 'projectlist.php';
            });
        }
    </script>
    <?php include "script.inc.php"; ?>
</body>

</html>
