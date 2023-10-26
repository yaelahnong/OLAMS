<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$show_leave_query = "SELECT 
    users.name AS name,
    m_divisions.division_name AS division_name,
    leaves.reason AS reason,
    leaves.category AS category,
    leaves.start_date AS start_date,
    leaves.finish_date AS finish_date,
    leaves.status AS status
FROM leaves
LEFT JOIN users ON leaves.user_id = users.user_id
LEFT JOIN m_divisions ON leaves.division_id = m_divisions.division_id
WHERE leaves.leaves_id = ?";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $leave_id = $_GET['id'];

    $show_leave_statement = mysqli_prepare($conn, $show_leave_query);
    mysqli_stmt_bind_param($show_leave_statement, "i", $leave_id);
    mysqli_stmt_execute($show_leave_statement);
    $leaveData = mysqli_stmt_get_result($show_leave_statement);
    $leaveDetails = mysqli_fetch_assoc($leaveData);
} else {
    echo "Invalid leave ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.inc.php"; ?>
    <title>OLAMS - Leave Detail</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3"><strong>Leave Detail</strong></h1>
                    <div class="card">
                        <div class="card-body">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><strong>Full Name</strong></td>
                                        <td><?= $leaveDetails['name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Division</strong></td>
                                        <td><?= $leaveDetails['division_name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Reason</strong></td>
                                        <td><?= $leaveDetails['reason'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Category</strong></td>
                                        <td><?= $leaveDetails['category'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Start Date</strong></td>
                                        <td><?= $leaveDetails['start_date'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Finish Date</strong></td>
                                        <td><?= $leaveDetails['finish_date'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td><?= $leaveDetails['status'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <a href="leavelist.php" class="btn btn-warning btn-sm ms-2">Kembali</a>
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
