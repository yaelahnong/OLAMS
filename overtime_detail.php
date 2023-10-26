<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION["user_id"];

$show_overtime_query = "SELECT 
    users.name AS name,
    m_projects.project_name AS project_name,
    m_divisions.division_name AS divisi_name,
    overtimes.type AS type,
    overtimes.start_date AS start_date,
    overtimes.finish_date AS finish_date,
    overtimes.category AS category,
    overtimes.effective_time AS effective_time,
    overtimes.reason AS reason,
    overtimes.submitted_by_admin AS submitted_by_admin,
    overtimes.sent_by_admin AS sent_by_admin,
    overtimes.checked_by_leader AS checked_by_leader,
    overtimes.status AS status,
    overtimes.status_updated_at AS status_updated_at,
    overtimes.status_updated_by AS status_updated_by,
    overtimes.checked_by_leader_at AS checked_by_leader_at
FROM overtimes 
LEFT JOIN users ON overtimes.user_id = users.user_id
LEFT JOIN m_projects ON overtimes.project_id = m_projects.project_id
LEFT JOIN m_divisions ON overtimes.divisi_id = m_divisions.division_id
WHERE overtime_id = ?";

$userQuery = "SELECT user_id, name FROM users";
$userData = mysqli_prepare($conn, $userQuery);
mysqli_stmt_execute($userData);
$userData = mysqli_stmt_get_result($userData);
$userOptions = mysqli_fetch_all($userData, MYSQLI_ASSOC);

$divisionQuery = "SELECT division_id, division_name FROM m_divisions";
$divisionData = mysqli_prepare($conn, $divisionQuery);
mysqli_stmt_execute($divisionData);
$divisionData = mysqli_stmt_get_result($divisionData);
$divisionOptions = mysqli_fetch_all($divisionData, MYSQLI_ASSOC);

$projectQuery = "SELECT project_id, project_name FROM m_projects";
$projectData = mysqli_prepare($conn, $projectQuery);
mysqli_stmt_execute($projectData);
$projectData = mysqli_stmt_get_result($projectData);
$projectOptions = mysqli_fetch_all($projectData, MYSQLI_ASSOC);


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $overtime_id = $_GET['id'];

    $show_overtime_statement = mysqli_prepare($conn, $show_overtime_query);
    mysqli_stmt_bind_param($show_overtime_statement, "i", $overtime_id);
    mysqli_stmt_execute($show_overtime_statement);
    $overtimeData = mysqli_stmt_get_result($show_overtime_statement);
    $overtimeDetails = mysqli_fetch_assoc($overtimeData);
} else {
    echo "Invalid overtime ID.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.inc.php"; ?>
    <title>OLAMS - Overtime Detail</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3"><strong>Overtime Detail</strong></h1>
                    <div class="card">
                        <div class="card-body">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><strong>Fullname</strong></td>
                                        <td><?= $overtimeDetails['name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Project Name</strong></td>
                                        <td><?= $overtimeDetails['project_name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Division Name</strong></td>
                                        <td><?= $overtimeDetails['divisi_name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type</strong></td>
                                        <td><?= $overtimeDetails['type'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Start Date</strong></td>
                                        <td><?= date('d-M-Y H:i', strtotime($overtimeDetails['start_date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Finish Date</strong></td>
                                        <td><?= date('d-M-Y H:i', strtotime($overtimeDetails['finish_date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Category</strong></td>
                                        <td><?= $overtimeDetails['category'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Effective Time</strong></td>
                                        <td><?= $overtimeDetails['effective_time'] ?> hours</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Reason</strong></td>
                                        <td><?= $overtimeDetails['reason'] ?></td>
                                    </tr>
                                    <!-- <tr>
                                        <td><strong>Submitted by Admin</strong></td>
                                        <td><?= $overtimeDetails['submitted_by_admin'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Sent by Admin</strong></td>
                                        <td><?= $overtimeDetails['sent_by_admin'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Checked by Leader</strong></td>
                                        <td><?= $overtimeDetails['checked_by_leader'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Checked by Leader At</strong></td>
                                        <td><?= $overtimeDetails['checked_by_leader_at'] ?></td>
                                    </tr> -->
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td><?= $overtimeDetails['status'] ?></td>
                                    </tr>
                                    <!-- <tr>
                                        <td><strong>Status Updated At</strong></td>
                                        <td><?= $overtimeDetails['status_updated_at'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status Updated By</strong></td>
                                        <td><?= $overtimeDetails['status_updated_by'] ?></td>
                                    </tr> -->
                                </tbody>
                            </table>
                            <a href="overtimelist.php" class="btn btn-warning btn-sm ms-2">Kembali</a>
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
