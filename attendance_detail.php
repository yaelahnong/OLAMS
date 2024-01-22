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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $attendance_id = cleanValue($_GET['id']);

    // Fetch the attendance details from the database
    $query = "SELECT 
                attendances.attendance_id,
                attendances.reason, 
                attendances.type, 
                attendances.start_date, 
                attendances.finish_date,
                attendances.foto,
                m_divisions.division_name AS division_name,
                users.name AS name
              FROM attendances
              LEFT JOIN users ON attendances.user_id = users.user_id
              LEFT JOIN m_divisions ON attendances.division_id = m_divisions.division_id
              WHERE attendances.attendance_id = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $attendance_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $attendanceDetails = mysqli_fetch_assoc($result);
    } else {
        exit("Attendance record not found.");
    }
} else {
    exit("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.inc.php"; ?>
    <title>OLAMS - Attendance Detail</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3 judul_halaman"><strong>Attendance Detail</strong></h1>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h2>Attendance Details</h2>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <td><strong>Full Name:</strong></td>
                                            <td><?= $attendanceDetails['name'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Division:</strong></td>
                                            <td><?= $attendanceDetails['division_name'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Reason:</strong></td>
                                            <td><?= $attendanceDetails['reason'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td><?= $attendanceDetails['type'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Start Date:</strong></td>
                                            <td><?= date('d-M-Y', strtotime($attendanceDetails['start_date'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Finish Date:</strong></td>
                                            <td><?= date('d-M-Y', strtotime($attendanceDetails['finish_date'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Foto:</strong></td>
                                            <td>
                                                <?php if (!empty($attendanceDetails['foto']) && file_exists($attendanceDetails['foto'])) : ?>
                                                    <img src="<?= $attendanceDetails['foto'] ?>" alt="Preview" class="rounded" style="width: 300px; height: 300px;">
                                                <?php else : ?>
                                                    <p>-</p>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="card-footer">
                                    <a href="attendancelist.php" class="btn btn-warning">Back</a>
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