<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";

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

$fullnameErr = $divisionErr = $noteErr = $customerCountErr = $leadCountErr = $projectErr = "";
$fullname = $division = $note = $customerCount = $leadCount = $project = NULL;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
        $fullname = isset($_POST["user_id"]) ? cleanValue($_POST["user_id"]) : NULL;
        $project = isset($_POST["project_id"]) ? cleanValue($_POST["project_id"]) : NULL;
        $division = isset($_POST["divisi_id"]) ? cleanValue($_POST["divisi_id"]) : NULL;
        $lead_count = isset($_POST["lead_count"]) ? cleanValue($_POST["lead_count"]) : NULL;
        $customer_count = isset($_POST["customer_count"]) ? cleanValue($_POST["customer_count"]) : NULL;
        $note = isset($_POST["note"]) ? cleanValue($_POST["note"]) : NULL;

        if (empty($fullname)) {
            $fullnameErr = "Full Name is required";
        }
        if (empty($project)) {
            $projectErr = "Project is required";
        }
        if (empty($division)) {
            $divisionErr = "Division is required";
        }

        if (!is_numeric($lead_count)) {
            $leadCountErr = "Lead Count must be a number.";
        }

        if (!is_numeric($customer_count)) {
            $customerCountErr = "Customer Count must be a number.";
        } elseif ($customer_count > $lead_count) {
            $customerCountErr = "Number of Customers cannot be more than the number of leads.";
        }

        if (!empty($note)) {
            if (!preg_match('/^[A-Za-z0-9.,\s]*$/', $note)) {
                $noteErr = "Note should only contain letters, numbers, spaces, dots, and commas.";
            }
        }

        if (empty($fullnameErr) && empty($projectErr) && empty($divisionErr) && empty($leadCountErr) && empty($customerCountErr) && empty($noteErr)) {
            $insertQuery = "INSERT INTO duty_overtimes (user_id, project_id, division_id, lead_count, customer_count, note, created_by)
                            VALUES (?, ?, ?, ?, ?, ?, ?)";

            $insertStatement = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($insertStatement, "iiiiiss", $fullname, $project, $division, $lead_count, $customer_count, $note, $user_id);

            if (mysqli_stmt_execute($insertStatement)) {
                echo "<script>alert('Duty overtime data added successfully.')</script>";
                echo "<script>window.location.href = 'duty_overtimelist.php'</script>";
                exit();
            } else {
                echo "Failed to save data.";
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
    <title>OLAMS - Add Duty Overtime</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3 judul_halaman"><strong>Add Duty Overtime</strong></h1>
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
                                                <span style="color: red">*</span>
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
                                                <span style="color: red">*</span>
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
                                                <span style="color: red">*</span>
                                                <select name="divisi_id" id="inputDivision" class="form-select">
                                                    <option value="">Select Division</option>
                                                    <?php foreach ($resultDivision as $division) : ?>
                                                        <option value="<?= $division['division_id'] ?>"><?= $division['division_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="error" style="color: red;"> <?= $divisionErr; ?> </span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="lead_count" class="form-label">Lead Count</label>
                                                <span style="color: red">*</span>
                                                <input type="number" name="lead_count" id="lead_count" class="form-control">
                                                <span class="error" style="color: red;"> <?= $leadCountErr; ?> </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label for="customer_count" class="form-label">Customer Count</label>
                                                <span style="color: red">*</span>
                                                <input type="number" name="customer_count" id="customer_count" class="form-control">
                                                <span class="error" style="color: red;"> <?= $customerCountErr; ?> </span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="note" class="form-label">Note</label>
                                                <textarea name="note" id="note" class="form-control"></textarea>
                                                <span class="error" style="color: red;"> <?= $noteErr; ?> </span>
                                            </div>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                        <a href="duty_overtimelist.php" class="btn btn-danger text-white text-decoration-none">Cancel</a>
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