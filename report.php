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

$projectQuery = "SELECT project_id, project_name FROM m_projects WHERE is_deleted = 'N'";
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

$selectedStartMonth = isset($_GET['filter_from_month']) ? $_GET['filter_from_month'] : date('m');
$selectedEndMonth = isset($_GET['filter_to_month']) ? $_GET['filter_to_month'] : date('m');
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.inc.php"; ?>
    <!-- <link rel="stylesheet" href="library/DataTables/datatables.css"> -->
    <title>OLAMS - Report</title>
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
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="d-flex align-items-center">
                                            <form action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" method="get" class="d-flex">
                                                <label for="inputFromMonth" class="mt-3 mx-4">From</label>
                                                <select name="filter_from_month" id="inputFromMonth" class="form-select form-control mt-2 mb-5">
                                                    <option value="">Select Month</option>
                                                    <option value="01" <?php echo ($selectedStartMonth == '01') ? 'selected' : ''; ?>>January</option>
                                                    <option value="02" <?php echo ($selectedStartMonth == '02') ? 'selected' : ''; ?>>February</option>
                                                    <option value="03" <?php echo ($selectedStartMonth == '03') ? 'selected' : ''; ?>>March</option>
                                                    <option value="04" <?php echo ($selectedStartMonth == '04') ? 'selected' : ''; ?>>April</option>
                                                    <option value="05" <?php echo ($selectedStartMonth == '05') ? 'selected' : ''; ?>>May</option>
                                                    <option value="06" <?php echo ($selectedStartMonth == '06') ? 'selected' : ''; ?>>June</option>
                                                    <option value="07" <?php echo ($selectedStartMonth == '07') ? 'selected' : ''; ?>>July</option>
                                                    <option value="08" <?php echo ($selectedStartMonth == '08') ? 'selected' : ''; ?>>August</option>
                                                    <option value="09" <?php echo ($selectedStartMonth == '09') ? 'selected' : ''; ?>>September</option>
                                                    <option value="10" <?php echo ($selectedStartMonth == '10') ? 'selected' : ''; ?>>October</option>
                                                    <option value="11" <?php echo ($selectedStartMonth == '11') ? 'selected' : ''; ?>>November</option>
                                                    <option value="12" <?php echo ($selectedStartMonth == '12') ? 'selected' : ''; ?>>December</option>
                                                </select>
                                                <label for="inputToMonth" class="mt-3 mx-4">To</label>
                                                <select name="filter_to_month" id="inputToMonth" class="form-select form-control mt-2 mb-5">
                                                    <option value="">Select Month</option>
                                                    <option value="01" <?php echo ($selectedEndMonth == '01') ? 'selected' : ''; ?>>January</option>
                                                    <option value="02" <?php echo ($selectedEndMonth == '02') ? 'selected' : ''; ?>>February</option>
                                                    <option value="03" <?php echo ($selectedEndMonth == '03') ? 'selected' : ''; ?>>March</option>
                                                    <option value="04" <?php echo ($selectedEndMonth == '04') ? 'selected' : ''; ?>>April</option>
                                                    <option value="05" <?php echo ($selectedEndMonth == '05') ? 'selected' : ''; ?>>May</option>
                                                    <option value="06" <?php echo ($selectedEndMonth == '06') ? 'selected' : ''; ?>>June</option>
                                                    <option value="07" <?php echo ($selectedEndMonth == '07') ? 'selected' : ''; ?>>July</option>
                                                    <option value="08" <?php echo ($selectedEndMonth == '08') ? 'selected' : ''; ?>>August</option>
                                                    <option value="09" <?php echo ($selectedEndMonth == '09') ? 'selected' : ''; ?>>September</option>
                                                    <option value="10" <?php echo ($selectedEndMonth == '10') ? 'selected' : ''; ?>>October</option>
                                                    <option value="11" <?php echo ($selectedEndMonth == '11') ? 'selected' : ''; ?>>November</option>
                                                    <option value="12" <?php echo ($selectedEndMonth == '12') ? 'selected' : ''; ?>>December</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary mb-5 mt-2 mx-2">Search</button>
                                                <a class="btn btn-sm btn-warning mb-5 mt-2 mx-2 t-white" href="<?php echo cleanValue($_SERVER['PHP_SELF']); ?>">Reset</a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportOvertimes" class="table mb-0 mt-3 d-nowarp">
                                        <thead>
                                            <tr>
                                                <td class="non-orderable">Name Project</td>
                                                <?php foreach ($projectOptions as $project) : ?>
                                                    <td colspan="3" class="text-center"><?php echo $project['project_name'] ?></td>
                                                <?php endforeach; ?>
                                                <td colspan="3" class="text-center">TOTAL</td>
                                            </tr>
                                            <tr>
                                                <td scope="col">Name</td>
                                                <?php foreach ($projectOptions as $project) : ?>
                                                    <td scope="col">Normal</td>
                                                    <td scope="col">Holiday</td>
                                                    <td scope="col" style="min-width: 90px;">On Duty</td>
                                                <?php endforeach; ?>
                                                <td scope="col">Normal</td>
                                                <td scope="col">Holiday</td>
                                                <td scope="col" style="min-width: 90px;">On Duty</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($userOptions as $key => $employee) : ?>
                                                <?php if ($employee['role_name'] == 'User') : ?>
                                                    <tr>
                                                        <td><?php echo $employee['name'] ?></td>
                                                        <?php
                                                        $totalNormalDays = 0;
                                                        $totalHolidayDays = 0;
                                                        $totalOnDutyDays = 0;
                                                        ?>
                                                        <?php foreach ($projectOptions as $project) : ?>
                                                            <?php
                                                            $overtimeQuery = "SELECT category, status FROM overtimes WHERE user_id = ? AND project_id = ? AND category IN ('Weekday', 'Weekend') AND MONTH(start_date) BETWEEN ? AND ?";
                                                            $overtimeData = mysqli_prepare($conn, $overtimeQuery);
                                                            mysqli_stmt_bind_param($overtimeData, "iiii", $employee['user_id'], $project['project_id'], $selectedStartMonth, $selectedEndMonth);
                                                            mysqli_stmt_execute($overtimeData);
                                                            $overtimeResult = mysqli_stmt_get_result($overtimeData);

                                                            $normalDays = 0;
                                                            $holidayDays = 0;
                                                            $onDutyDays = 0;

                                                            while ($overtime = mysqli_fetch_assoc($overtimeResult)) {
                                                                if ($overtime['category'] == 'Weekday' && $overtime['status'] == 'Approved') {
                                                                    $normalDays++;
                                                                } elseif ($overtime['category'] == 'Weekend' && $overtime['status'] == 'Approved') {
                                                                    $holidayDays++;
                                                                }
                                                            }

                                                            $dutyOvertimeQuery = "SELECT status FROM duty_overtimes WHERE user_id = ? AND project_id = ? AND MONTH(start_date) BETWEEN ? AND ?";
                                                            $dutyOvertimeData = mysqli_prepare($conn, $dutyOvertimeQuery);
                                                            mysqli_stmt_bind_param($dutyOvertimeData, "iiii", $employee['user_id'], $project['project_id'], $selectedStartMonth, $selectedEndMonth);
                                                            mysqli_stmt_execute($dutyOvertimeData);
                                                            $dutyOvertimeResult = mysqli_stmt_get_result($dutyOvertimeData);

                                                            while ($dutyOvertime = mysqli_fetch_assoc($dutyOvertimeResult)) {
                                                                if ($dutyOvertime['status'] == 'Approved') {
                                                                    $onDutyDays++;
                                                                }
                                                            }

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
                                                    </tr>
                                                <?php endif; ?>
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
                buttons: [{
                        extend: 'pdf',
                        orientation: 'landscape',
                        pageSize: 'legal',
                        title: 'Data Overtime',
                        download: 'open'
                    },
                    'excel'
                ]
            });
        });
    </script>
</body>

</html>