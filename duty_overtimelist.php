<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$limit = 5;
$halaman_aktif = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($halaman_aktif - 1) * $limit;

$show_duty_overtime = "SELECT
duty_overtimes.duty_overtime_id,
users.name AS name,
m_projects.project_name AS project_name, 
m_divisions.division_name AS division_name, 
duty_overtimes.lead_count, 
duty_overtimes.customer_count, 
duty_overtimes.note,
duty_overtimes.start_date,
duty_overtimes.finish_date,
duty_overtimes.status
FROM duty_overtimes
LEFT JOIN users ON duty_overtimes.user_id = users.user_id
LEFT JOIN m_projects ON duty_overtimes.project_id = m_projects.project_id
LEFT JOIN m_divisions ON duty_overtimes.division_id = m_divisions.division_id
";

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

$roleQuery = "SELECT m_roles.role_id FROM users
JOIN m_roles ON users.role_id = m_roles.role_id
WHERE users.user_id = ?";

$roleStatement = mysqli_prepare($conn, $roleQuery);
mysqli_stmt_bind_param($roleStatement, "i", $user_id);
mysqli_stmt_execute($roleStatement);
$roleData = mysqli_stmt_get_result($roleStatement);
$userRole = mysqli_fetch_row($roleData)[0];

if ($userRole === 1) {
    $show_duty_overtime .= " WHERE duty_overtimes.user_id = $user_id";
}

$filter_division = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filter_division']) && !empty($_GET['filter_division'])) {
    $filter_division = cleanValue($_GET['filter_division']);
    $show_duty_overtime .= (strpos($show_duty_overtime, 'WHERE') === false) ? " WHERE" : " AND";
    $show_duty_overtime .= " m_divisions.division_id = '$filter_division'";
}

$filter_project = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filter_project']) && !empty($_GET['filter_project'])) {
    $filter_project = cleanValue($_GET['filter_project']);
    $show_duty_overtime .= (strpos($show_duty_overtime, 'WHERE') === false) ? " WHERE" : " AND";
    $show_duty_overtime .= " m_projects.project_id = '$filter_project'";
}

$search = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search']) && !empty($_GET['search'])) {
    $search = cleanValue($_GET['search']);
    $show_duty_overtime .= (strpos($show_duty_overtime, 'WHERE') === false) ? " WHERE" : " AND";
    $show_duty_overtime .= " ( users.name LIKE '%$search%')";
}

$filter_status = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
    $filter_status = cleanValue($_GET['filter_status']);
    $show_duty_overtime .= (strpos($show_duty_overtime, 'WHERE') === false) ? " WHERE" : " AND";
    $show_duty_overtime .= " duty_overtimes.status = '$filter_status'";
}

$show_duty_overtime .= " ORDER BY duty_overtimes.duty_overtime_id DESC";

$jumlah_semua_data = mysqli_num_rows(mysqli_query($conn, $show_duty_overtime));
$show_duty_overtime .= " LIMIT $limit OFFSET $offset ";
$data = mysqli_query($conn, $show_duty_overtime);
$karyawanArray = mysqli_fetch_all($data, MYSQLI_ASSOC);
$jumlah_halaman = ceil($jumlah_semua_data / $limit);

$updateStatement = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
        if (isset($_POST['duty_overtime_id']) && is_numeric($_POST['duty_overtime_id'])) {
            $overtimeId = cleanValue($_POST['duty_overtime_id']);
            $updateQuery = "";

            if ($userRole === 3) { // Cek jika peran adalah "admin"
                $status = ($_POST['submit'] === "Approve") ? 'Approved' : null;
                $updateQuery = "UPDATE duty_overtimes SET status = ?, approved_by = ?, updated_by = ? WHERE duty_overtime_id = ?";
            } else {
                echo "Anda tidak memiliki izin untuk menyetujui overtime.";
            }


            if ($updateQuery) {
                if ($updateStatement) {
                    mysqli_stmt_close($updateStatement);
                }
                $updateStatement = mysqli_prepare($conn, $updateQuery);

                if ($updateStatement) {
                    mysqli_stmt_bind_param($updateStatement, "siii", $status, $user_id, $user_id, $overtimeId);

                    if (mysqli_stmt_execute($updateStatement)) {
                        echo "<script>alert('Duty overtime data Updated successfully.')</script>";
                        echo "<script>window.location.href = 'duty_overtimelist.php'</script>";
                        exit();
                    } else {
                        echo "Gagal menyimpan data.";
                    }
                } else {
                    echo "Gagal menyiapkan perintah.";
                }
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
    <title>OLAMS - Duty Overtime List</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3 judul_halaman"><strong>Duty Overtime List</strong></h1>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="d-flex align-items-center">
                                            <form action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" method="get" class="d-flex">
                                                <label for="inputSearch" class="m-1 mx-1">Search</label>
                                                <input type="text" name="search" id="inputSearch" placeholder="Enter Name" class="form-control form-control" value="<?= $search ?>">
                                                <label for="inputRole" class="m-1 mx-1">Division</label>
                                                <select name="filter_division" id="inputRole" class="form-select form-control">
                                                    <option value="">Select Division</option>
                                                    <?php foreach ($divisionOptions as $option) : ?>
                                                        <?php $selected = ($filter_division == $option['division_id']) ? 'selected' : ''; ?>
                                                        <option value="<?= $option['division_id'] ?>" <?= $selected ?>>
                                                            <?= $option['division_name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label for="inputRole" class="m-1 mx-1">Project</label>
                                                <select name="filter_project" id="inputRole" class="form-select form-control">
                                                    <option value="">Select Projects</option>
                                                    <?php foreach ($projectOptions as $option) : ?>
                                                        <?php $selected = ($filter_project == $option['project_id']) ? 'selected' : ''; ?>
                                                        <option value="<?= $option['project_id'] ?>" <?= $selected ?>>
                                                            <?= $option['project_name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label for="inputStatus" class="m-1 mx-1">Status</label>
                                                <select name="filter_status" id="inputStatus" class="form-select form-control">
                                                    <option value="">Select Status</option>
                                                    <option value="Approved" <?= ($filter_status === 'Approved') ? 'selected' : '' ?>>Approved</option>
                                                    <option value="Pending" <?= ($filter_status === 'Pending') ? 'selected' : '' ?>>Pending</option>
                                                    <option value="Rejected" <?= ($filter_status === 'Rejected') ? 'selected' : '' ?>>Rejected</option>
                                                </select>
                                                <div class="d-flex align-items-center">
                                                    <button type="submit" class="btn btn-sm btn-primary mb-2 mx-2">Search</button>
                                                    <a class="btn btn-sm btn-warning mb-2 mx-2" href="<?php echo cleanValue($_SERVER['PHP_SELF']); ?>">Reset</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <?php if ($userRole == 1) : ?>
                                            <a href="duty_overtime_add.php" class="btn-sm btn-success me-3 text-white text-decoration-none">+ Add Duty Overtime</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($userRole === 2 || $userRole === 3 || $userRole === 4) : ?>

                                    <div class="table-responsive">
                                        <table class="table mb-0 mt-3">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No</th>
                                                    <th scope="col" style="min-width : 200px;">Full Name</th>
                                                    <th scope="col" style="min-width : 200px;">Project</th>
                                                    <th scope="col" style="min-width : 200px;">Division</th>
                                                    <th scope="col" style="min-width : 120px;">Lead Count</th>
                                                    <th scope="col" style="min-width : 145px;">Customer Count</th>
                                                    <th scope="col" style="min-width : 200px;">Start Date</th>
                                                    <th scope="col" style="min-width : 200px;">Finish Date</th>
                                                    <th scope="col" style="min-width : 210px;">Note</th>
                                                    <th scope="col" style="min-width : 200px;">Status</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (count($karyawanArray) > 0) : ?>
                                                    <?php foreach ($karyawanArray as $key => $value) : ?>
                                                        <?php
                                                        // Menentukan class untuk baris berdasarkan status
                                                        $rowClass = '';
                                                        if ($value['status'] == 'Pending') {
                                                            $rowClass = 'bg-primary-subtle'; // Ganti dengan nama kelas CSS yang sesuai
                                                        } elseif ($value['status'] == 'Rejected') {
                                                            $rowClass = 'bg-danger-subtle'; // Ganti dengan nama kelas CSS yang sesuai
                                                        }
                                                        ?>
                                                        <tr class="<?= $rowClass ?>">
                                                            <td><?= $key + 1 + $offset ?></td>
                                                            <td><?= $value['name'] ?></td>
                                                            <td><?= $value['project_name'] ?></td>
                                                            <td><?= $value['division_name'] ?></td>
                                                            <td><?= $value['lead_count'] ?></td>
                                                            <td><?= $value['customer_count'] ?></td>
                                                            <td><?= date('d-M-Y H:i', strtotime($value['start_date'])) ?></td>
                                                            <td><?= date('d-M-Y H:i', strtotime($value['finish_date'])) ?></td>
                                                            <td><?= empty($value['note']) ? '-' : $value['note'] ?></td>
                                                            <td>
                                                                <?php
                                                                if ($value['status'] === 'Pending') {
                                                                    $statusClass = 'badge bg-warning'; // Status "pending"
                                                                } elseif ($value['status'] === 'Rejected') {
                                                                    $statusClass = 'badge bg-danger'; // Status "reject"
                                                                } elseif ($value['status'] === 'Approved') {
                                                                    $statusClass = 'badge bg-success'; // Status "approved"
                                                                }
                                                                ?>
                                                                <button class="btn btn-sm text-white <?= $statusClass ?>" disabled>
                                                                    <?= $value['status'] ?>
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex">
                                                                    <?php if ($userRole === 3) : // Admin 
                                                                    ?>
                                                                        <a href="duty_overtime_detail.php?id=<?= $value['duty_overtime_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                                                        <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" class="d-inline">
                                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                            <input type="hidden" name="duty_overtime_id" value="<?= $value['duty_overtime_id'] ?>">
                                                                            <?php if ($value['status'] !== 'Approved') : ?>
                                                                                <button type="submit" name="submit" value="Approve" class="btn btn-success btn-sm ms-2" onclick="return confirm('are you sure you will approve it?')">Approve</button>
                                                                            <?php else : ?>
                                                                            <?php endif; ?>
                                                                        </form>
                                                                    <?php elseif ($userRole === 4) : // Supervisor 
                                                                    ?>
                                                                        <a href="duty_overtime_detail.php?id=<?= $value['duty_overtime_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                                                    <?php elseif ($userRole === 2) : // Leader 
                                                                    ?>
                                                                        <a href="duty_overtime_detail.php?id=<?= $value['duty_overtime_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>

                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else : ?>
                                                    <tr>
                                                        <td colspan="8" style="text-align: center;">No records found!!!</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php elseif ($userRole === 1) : ?>
                                    <div class="table-responsive">
                                        <table class="table mb-0 mt-3">
                                            <thead>
                                                <tr>
                                                    <th scope="col">No</th>
                                                    <th scope="col" style="min-width : 200px;">Project</th>
                                                    <th scope="col" style="min-width : 200px;">Division</th>
                                                    <th scope="col" style="min-width : 100px;">Lead Count</th>
                                                    <th scope="col" style="min-width : 100px;">Customer Count</th>
                                                    <th scope="col" style="min-width : 200px;">Start Date</th>
                                                    <th scope="col" style="min-width : 200px;">Finish Date</th>
                                                    <th scope="col" style="min-width : 200px;">Note</th>
                                                    <th scope="col" style="min-width : 200px;">Status</th>
                                                    <th scope="col" style="min-width : 210px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (count($karyawanArray) > 0) : ?>
                                                    <?php foreach ($karyawanArray as $key => $value) : ?>
                                                        <?php
                                                        // Menentukan class untuk baris berdasarkan status
                                                        $rowClass = '';
                                                        if ($value['status'] == 'Pending') {
                                                            $rowClass = 'bg-primary-subtle'; // Ganti dengan nama kelas CSS yang sesuai
                                                        } elseif ($value['status'] == 'Rejected') {
                                                            $rowClass = 'bg-danger-subtle'; // Ganti dengan nama kelas CSS yang sesuai
                                                        }
                                                        ?>
                                                        <tr class="<?= $rowClass ?>">
                                                            <td><?= $key + 1 + $offset ?></td>
                                                            <td><?= $value['project_name'] ?></td>
                                                            <td><?= $value['division_name'] ?></td>
                                                            <td><?= $value['lead_count'] ?></td>
                                                            <td><?= $value['customer_count'] ?></td>
                                                            <td><?= date('d-M-Y H:i', strtotime($value['start_date'])) ?></td>
                                                            <td><?= date('d-M-Y H:i', strtotime($value['finish_date'])) ?></td>
                                                            <td><?= empty($value['note']) ? '-' : $value['note'] ?></td>
                                                            <td>
                                                                <?php
                                                                if ($value['status'] === 'Pending') {
                                                                    $statusClass = 'badge bg-warning'; // Status "pending"
                                                                } elseif ($value['status'] === 'Rejected') {
                                                                    $statusClass = 'badge bg-danger'; // Status "reject"
                                                                } elseif ($value['status'] === 'Approved') {
                                                                    $statusClass = 'badge bg-success'; // Status "approved"
                                                                }
                                                                ?>
                                                                <button class="btn btn-sm text-white <?= $statusClass ?>" disabled>
                                                                    <?= $value['status'] ?>
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex">
                                                                    <?php if ($userRole === 1) : // User 
                                                                    ?>
                                                                        <a href="duty_overtime_detail.php?id=<?= $value['duty_overtime_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                                                        <?php if ($value['status'] !== 'Approved') : ?>
                                                                            <a href="duty_overtime_update.php?id=<?= $value['duty_overtime_id'] ?>" class="btn btn-warning btn-sm ms-2" onclick="return confirm('are you sure you will Edit?')">Edit</a>
                                                                            <a href="duty_overtime_delete.php?id=<?= $value['duty_overtime_id'] ?>" class="btn btn-danger btn-sm ms-2" onclick="return confirm('are you sure you will delete?')">Delete</a>
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else : ?>
                                                    <tr>
                                                        <td colspan="8" style="text-align: center;">No records found!!!</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>

                                <div class="dataTables_paginate paging_simple_numbers ms-3 mt-3">
                                    <ul class="pagination justify-content-end">
                                        <?php if ($jumlah_semua_data > $limit) : ?>
                                            <?php if ($halaman_aktif > 1) : ?>
                                                <?php $prevPage = $halaman_aktif - 1; ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $prevPage . ($search ? '&search=' . $search : '') . ($filter_division ? '&filter_division=' . $filter_division : '') . ($filter_project ? '&filter_project=' . $filter_project : '') . ($filter_status ? '&filter_status=' . $filter_status : ''); ?>">Previous</a>
                                                </li>
                                            <?php else : ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">Previous</span>
                                                </li>
                                            <?php endif; ?>
                                            <?php for ($i = 1; $i <= $jumlah_halaman; $i++) : ?>
                                                <li class="page-item<?= $i == $halaman_aktif ? ' active' : ''; ?>">
                                                    <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $i . ($search ? '&search=' . $search : '') . ($filter_division ? '&filter_division=' . $filter_division : '') . ($filter_project ? '&filter_project=' . $filter_project : '') . ($filter_status ? '&filter_sta$filter_status=' . $filter_status : ''); ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($halaman_aktif < $jumlah_halaman) : ?>
                                                <?php $nextPage = $halaman_aktif + 1; ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $nextPage . ($search ? '&search=' . $search : '') . ($filter_division ? '&filter_division=' . $filter_division : '') . ($filter_project ? '&filter_project=' . $filter_project : '') . ($filter_status ? '&filter_status=' . $filter_status : ''); ?>">Next</a>
                                                </li>
                                            <?php else : ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">Next</span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
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