<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if(!isset( $_SESSION["user_id"])){
    header("Location: login/login.php");
    exit();
}

$projectData = [];
if (isset($_GET['id'])) {
    $project_id = cleanValue($_GET['id']);

    // Query untuk mengambil data proyek berdasarkan project_id
    $queryProject = "SELECT project_name FROM m_projects WHERE project_id = ?";
    $stmt = mysqli_prepare($conn, $queryProject);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $project_id);
        mysqli_stmt_execute($stmt);
        $projectData = mysqli_stmt_get_result($stmt);
        $projectData = mysqli_fetch_assoc($projectData); 
        mysqli_stmt_close($stmt);
    } else {
        $error = "Failed to create a prepared statement.";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["project_name"]) && !empty($_POST["project_name"])) {
        $newProjectName = $_POST["project_name"];
        if (preg_match("/^[a-zA-Z0-9 ]*$/", $newProjectName)) {
            $newProjectName = cleanValue($newProjectName);

            $checkProjectName = "SELECT project_name FROM m_projects WHERE project_name = ? AND is_deleted = 'N'";
                $checkStmt = mysqli_prepare($conn, $checkProjectName);

                $checkProjectName = "SELECT project_name FROM m_projects WHERE project_name = ? AND is_deleted = 'N'";
                $checkStmt = mysqli_prepare($conn, $checkProjectName);
    
                if ($checkStmt) {
                    mysqli_stmt_bind_param($checkStmt, "s", $newProjectName);
                    mysqli_stmt_execute($checkStmt);
                    $checkResult = mysqli_stmt_get_result($checkStmt);
    
                    if (mysqli_num_rows($checkResult) > 0) {
                        $error = "Project with the same name already exists.";
                    } else {
                        if (isset($_GET['id'])) {
                            $project_id = cleanValue($_GET['id']);
                            $updateQuery = "UPDATE m_projects SET project_name = ?, updated_by = ? WHERE project_id = ?";
                            $stmt = mysqli_prepare($conn, $updateQuery);
    
                            if ($stmt) {
                                $user_id = $_SESSION["user_id"];
                                mysqli_stmt_bind_param($stmt, "sii", $newProjectName, $user_id, $project_id);
    
                                if (mysqli_stmt_execute($stmt)) {
                                    echo "<script>alert('Data project berhasil diperbarui.')</script>";
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
            } else {
                $error = "Input can only contain letters, numbers, and spaces.";
            }
        } else {
            $error = "The project name cannot be empty.";
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
                                    <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']."?id=$project_id"); ?>">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputProjectName">Projet Name</label>
                                                <input type="text" class="form-control" name="project_name" id="inputProjectName" placeholder="Enter Project Name" value="<?= $projectData['project_name'] ?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <button type="submit" class="btn btn-primary">Submit</button>
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