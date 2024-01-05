<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

// membatasi Hak Akses User
if ($_SESSION["role_id"] != 3 && $_SESSION["role_id"] != 4) {
    header("Location: dashboard.php");
    exit();
}
$limit = 5;
$halaman_aktif = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($halaman_aktif - 1) * $limit;

$projectQuery = "SELECT project_id, project_name FROM m_projects";
$projectData = mysqli_prepare($conn, $projectQuery);
mysqli_stmt_execute($projectData);
$projectData = mysqli_stmt_get_result($projectData);
$projectOptions = mysqli_fetch_all($projectData, MYSQLI_ASSOC);

$userQuery = "SELECT users.user_id,
users.name,
m_roles.name AS role_name
FROM users
LEFT JOIN m_roles ON users.role_id = m_roles.role_id
";
$userData = mysqli_prepare($conn, $userQuery);
mysqli_stmt_execute($userData);
$userData = mysqli_stmt_get_result($userData);
$userOptions = mysqli_fetch_all($userData, MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.inc.php"; ?>
    <title>OLAMS - Report</title>
    <style>
        th.non-orderable::after {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3 judul_halaman"><strong>Report</strong></h1>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header"></div>
                                <div class="table-responsive">
                                    <table id="reportOvertimes" class="table mb-0 mt-3 d-nowarp">
                                        <thead>
                                            <tr>
                                                <th class="non-orderable"></th>
                                                <?php foreach ($projectOptions as $project) : ?>
                                                    <th colspan="3" class="text-center"><?php echo $project['project_name'] ?></th>
                                                <?php endforeach; ?>
                                                <th colspan="3" class="text-center">TOTAL</th>
                                            </tr>
                                            <tr>
                                                <th scope="col" style="min-width: 210px;" class="text-center">Nama</th>
                                                <?php foreach ($projectOptions as $project) : ?>
                                                    <th scope="col">Normal</th>
                                                    <th scope="col">Holiday</th>
                                                    <th scope="col" style="min-width: 90px;">On Duty</th>
                                                <?php endforeach; ?>
                                                <th scope="col">Normal</th>
                                                <th scope="col">Holiday</th>
                                                <th scope="col" style="min-width: 90px;">On Duty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($userOptions as $key => $employee) : ?>
                                                <tr>
                                                    <?php if ($employee['role_name'] == 'User') : ?>
                                                        <td><?php echo $employee['name'] ?></td>
                                                        <?php
                                                        // Inisialisasi nilai total normal, holiday, dan on duty
                                                        $totalNormalDays = 0;
                                                        $totalHolidayDays = 0;
                                                        $totalOnDutyDays = 0;
                                                        ?>
                                                        <?php foreach ($projectOptions as $project) : ?>
                                                            <?php
                                                            // Query untuk mengambil data overtimes sesuai user_id, project_id, dan kategori tertentu
                                                            $overtimeQuery = "SELECT category, status FROM overtimes WHERE user_id = ? AND project_id = ? AND category IN ('Weekday', 'Weekend')";
                                                            $overtimeData = mysqli_prepare($conn, $overtimeQuery);
                                                            mysqli_stmt_bind_param($overtimeData, "ii", $employee['user_id'], $project['project_id']);
                                                            mysqli_stmt_execute($overtimeData);
                                                            $overtimeResult = mysqli_stmt_get_result($overtimeData);

                                                            // Inisialisasi nilai normal, holiday, dan on duty untuk proyek tertentu
                                                            $normalDays = 0;
                                                            $holidayDays = 0;
                                                            $onDutyDays = 0;

                                                            // Loop through hasil overtimes
                                                            while ($overtime = mysqli_fetch_assoc($overtimeResult)) {
                                                                if ($overtime['category'] == 'Weekday' && $overtime['status'] == 'Approved') {
                                                                    $normalDays++;
                                                                } elseif ($overtime['category'] == 'Weekend' && $overtime['status'] == 'Approved') {
                                                                    $holidayDays++;
                                                                }
                                                            }

                                                            // Query untuk mengambil data duty_overtimes sesuai user_id, project_id, dan status tertentu
                                                            $dutyOvertimeQuery = "SELECT status FROM duty_overtimes WHERE user_id = ? AND project_id = ?";
                                                            $dutyOvertimeData = mysqli_prepare($conn, $dutyOvertimeQuery);
                                                            mysqli_stmt_bind_param($dutyOvertimeData, "ii", $employee['user_id'], $project['project_id']);
                                                            mysqli_stmt_execute($dutyOvertimeData);
                                                            $dutyOvertimeResult = mysqli_stmt_get_result($dutyOvertimeData);

                                                            // Loop through hasil duty_overtimes
                                                            while ($dutyOvertime = mysqli_fetch_assoc($dutyOvertimeResult)) {
                                                                if ($dutyOvertime['status'] == 'Approved') {
                                                                    $onDutyDays++;
                                                                }
                                                            }

                                                            // Tambahkan ke total
                                                            $totalNormalDays += $normalDays;
                                                            $totalHolidayDays += $holidayDays;
                                                            $totalOnDutyDays += $onDutyDays;
                                                            ?>
                                                            <td class="text-center"><?php echo $normalDays ?></td>
                                                            <td class="text-center"><?php echo $holidayDays ?></td>
                                                            <td class="text-center"><?php echo $onDutyDays ?></td>
                                                        <?php endforeach; ?>
                                                        <td class="text-center"><?php echo $totalNormalDays ?></td>
                                                        <td class="text-center"><?php echo $totalHolidayDays ?></td>
                                                        <td class="text-center"><?php echo $totalOnDutyDays ?></td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
    <script>
        $(document).ready(function() {
            $('#reportOvertimes').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel', 'pdf'
                ],
            });
        });
    </script>

</body>

</html>