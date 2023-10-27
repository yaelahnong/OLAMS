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

$show_overtime = "SELECT
overtimes.overtime_id,
overtimes.user_id,
users.name AS name,
m_projects.project_name AS project_name, 
m_divisions.division_name AS division_name, 
overtimes.type, 
overtimes.start_date, 
overtimes.finish_date,
overtimes.status
FROM overtimes
LEFT JOIN users ON overtimes.user_id = users.user_id
LEFT JOIN m_projects ON overtimes.project_id = m_projects.project_id
LEFT JOIN m_divisions ON overtimes.divisi_id = m_divisions.division_id";

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

if($userRole === 1){
    $show_overtime .= " WHERE overtimes.user_id = $user_id";
}

$filter_division = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filter_division']) && !empty($_GET['filter_division'])) {
    $filter_division = cleanValue($_GET['filter_division']);
    $show_overtime .= (strpos($show_overtime, 'WHERE') === false) ? " WHERE" : " AND";
    $show_overtime .= " m_divisions.division_id = '$filter_division'";
}

$filter_project = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filter_project']) && !empty($_GET['filter_project'])) {
    $filter_project = cleanValue($_GET['filter_project']);
    $show_overtime .= (strpos($show_overtime, 'WHERE') === false) ? " WHERE" : " AND";
    $show_overtime .= " m_projects.project_id = '$filter_project'";
}

$search = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' and isset($_GET['search']) and !empty($_GET['search'])) {
    $search = cleanValue($_GET['search']);
    $show_overtime .= (strpos($show_overtime, 'WHERE') === false) ? " WHERE" : " AND";
    $show_overtime .= " (users.name LIKE '%$search%' OR overtimes.type LIKE '%$search%')";
}

$show_overtime .= " ORDER BY overtimes.overtime_id DESC";

$jumlah_semua_data = mysqli_num_rows(mysqli_query($conn, $show_overtime));
$show_overtime .= " LIMIT $limit OFFSET $offset ";
$data = mysqli_query($conn, $show_overtime);
$karyawanArray = mysqli_fetch_all($data, MYSQLI_ASSOC);
$jumlah_halaman = ceil($jumlah_semua_data / $limit);

$updateStatement = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
        if (isset($_POST['overtime_id']) && is_numeric($_POST['overtime_id'])) {
            $overtimeId = cleanValue($_POST['overtime_id']);
            $userId = cleanValue($_POST['user_id']);

            $typeQuery = "SELECT overtime_id, type FROM overtimes WHERE overtime_id = ?";
            $typeStatement = mysqli_prepare($conn, $typeQuery);
            mysqli_stmt_bind_param($typeStatement, "i", $overtimeId);
            mysqli_stmt_execute($typeStatement);
            $typeResult = mysqli_stmt_get_result($typeStatement);
            if ($typeRow = mysqli_fetch_assoc($typeResult)) {
                $type = $typeRow['type'];
            } else {
                echo "Data tidak ditemukan";
            }

            if (isset($_POST['submit']) && ($_POST['submit'] === "Reject" || $_POST['submit'] === "Approve" || $_POST['submit'] === "Check")) {
                if ($userRole === 4) { // jika peran adalah "supervisor"
                    $newStatus = ($_POST['submit'] === "Reject") ? 'Rejected' : 'Approved';
                    $updateQuery = "UPDATE overtimes SET status = ?, status_updated_by = ?, status_updated_at = NOW() WHERE overtime_id = ?";
                } elseif ($userRole === 2) { // Jika peran adalah "leader"
                    // Periksa tipe lembur
                    $newStatus = ($_POST['submit'] === "Reject") ? 'Rejected' : 'Approved';
                    if ($type === "Urgent") {
                        $updateQuery = "UPDATE overtimes SET status = ?, status_updated_by = ?, status_updated_at = NOW() WHERE overtime_id = ?";
                    } else {
                        $updateQuery = "UPDATE overtimes SET checked_by_leader_at = NOW(), checked_by_leader = ?, updated_by = ? WHERE overtime_id = ?";
                    }
                } elseif ($userRole === 3) { // Jika peran adalah "admin"
                    $newStatus = ($_POST['submit'] === "Reject") ? 'Rejected' : 'Approved';
                    $updateQuery = "UPDATE overtimes SET sent_by_admin = NOW(), submitted_by_admin = ?, updated_by = ? WHERE overtime_id = ?";
                } else {
                    echo "<script>alert('Overtime data failed to be modified.')</script>";
                    echo "<script>window.location.href = 'overtimelist.php'</script>";
                    exit();
                }
                $updateStatement = mysqli_prepare($conn, $updateQuery);

                if ($updateStatement) {
                    if ($userRole === 4) {
                        mysqli_stmt_bind_param($updateStatement, "sii", $newStatus, $user_id, $overtimeId);
                    } elseif ($userRole === 2 && $type === "Urgent") {
                        mysqli_stmt_bind_param($updateStatement, "sii", $newStatus, $user_id, $overtimeId);
                    } else {
                        mysqli_stmt_bind_param($updateStatement, "iii", $user_id, $user_id, $overtimeId);
                    }

                    $insertHistoryQuery = "INSERT INTO overtimes_histories (overtime_id, user_id, status, created_by, updated_at, updated_by) VALUES (?, ?, ?, ?, NOW(), ?)";
                    $insertHistoryStatement = mysqli_prepare($conn, $insertHistoryQuery);

                    if ($insertHistoryStatement) {

                        mysqli_stmt_bind_param($insertHistoryStatement, "iisii", $overtimeId, $userId, $newStatus, $userId, $user_id);

                        if (mysqli_stmt_execute($insertHistoryStatement) && mysqli_stmt_execute($updateStatement)) {
                            echo "<script>alert('Data lembur Diperbarui dengan sukses.')</script>";
                            echo "<script>window.location.href = 'overtimelist.php'</script>";
                            exit();
                        } else {
                            echo "Gagal menyisipkan data ke overtimes_histories atau memperbarui data lembur.";
                        }
                    } else {
                        echo "Gagal menyiapkan pernyataan untuk tabel histori.";
                    }
                } else {
                    echo "Gagal menyiapkan pernyataan utama.";
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
    <title>OLAMS - Overtime List</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3 judul_halaman"><strong>Overtime List</strong></h1>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="d-flex align-items-center">
                                            <form action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" method="get" class="d-flex">
                                                <label for="inputSearch" class="m-2 mx-2">Search</label>
                                                <input type="text" name="search" id="inputSearch" placeholder="Enter Type or name" class="form-control form-control" value="<?= $search ?>">
                                                <label for="inputRole" class="m-2 mx-2">Division</label>
                                                <select name="filter_division" id="inputRole" class="form-select form-control">
                                                    <option value="">Select Division</option>
                                                    <?php foreach ($divisionOptions as $option) : ?>
                                                        <?php $selected = ($filter_division == $option['division_id']) ? 'selected' : ''; ?>
                                                        <option value="<?= $option['division_id'] ?>" <?= $selected ?>>
                                                            <?= $option['division_name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label for="inputRole" class="m-2 mx-2">Project</label>
                                                <select name="filter_project" id="inputRole" class="form-select form-control">
                                                    <option value="">Select Projects</option>
                                                    <?php foreach ($projectOptions as $option) : ?>
                                                        <?php $selected = ($filter_project == $option['project_id']) ? 'selected' : ''; ?>
                                                        <option value="<?= $option['project_id'] ?>" <?= $selected ?>>
                                                            <?= $option['project_name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary mb-2 mx-2">Search</button>
                                                <a class="btn btn-sm btn-warning mb-2 mx-2" href="<?php echo cleanValue($_SERVER['PHP_SELF']); ?>">Reset</a>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <?php if ($userRole == 1) : ?>
                                            <a href="overtime_add.php" class="btn-sm btn-success me-3 text-white text-decoration-none">+ Add Overtime</a>
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
                                                <th scope="col" style="min-width : 210px;">Project</th>
                                                <th scope="col" style="min-width : 200px;">Division</th>
                                                <th scope="col">Type</th>
                                                <th scope="col" style="min-width : 200px;">Start Date</th>
                                                <th scope="col" style="min-width : 200px;">Finish Date</th>
                                                <th scope="col">Status</th>
                                                <th scope="col" style="min-width : 210px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($karyawanArray) > 0) : ?>
                                                <?php foreach ($karyawanArray as $key => $value) : ?>
                                                    <tr>
                                                        <td><?= $key + 1 + $offset ?></td>
                                                        <td><?= $value['name'] ?></td>
                                                        <td><?= $value['project_name'] ?></td>
                                                        <td><?= $value['division_name'] ?></td>
                                                        <td><?= $value['type'] ?></td>
                                                        <td><?= date('d-M-Y H:i', strtotime($value['start_date'])) ?></td>
                                                        <td><?= date('d-M-Y H:i', strtotime($value['finish_date'])) ?></td>
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
                                                                <?php if ($userRole === 3) : // Cek apakah peran sama dengan admin 
                                                                ?>
                                                                    <a href="overtime_detail.php?id=<?= $value['overtime_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                                                    <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" style="display: inline;">
                                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                        <input type="hidden" name="overtime_id" value="<?= $value['overtime_id'] ?>">
                                                                        <input type="hidden" name="user_id" value="<?= $value['user_id'] ?>">
                                                                        <button type="submit" name="submit" value="Check" class="btn btn-success btn-sm ms-2">Submit</button>
                                                                    </form>
                                                                <?php elseif ($userRole === 1) : // Cek apakah peran sama dengan user
                                                                ?>
                                                                    <?php if ($value['status'] !== 'Approved') : ?>
                                                                        <a href="overtime_delete.php?id=<?= $value['overtime_id']; ?>" class="btn btn-danger btn-sm ms-2" onclick="return confirm('Apakah kamu yakin?')">Delete</a>
                                                                    <?php endif; ?>
                                                                    <a href="overtime_detail.php?id=<?= $value['overtime_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                                                    <a href="overtime_update.php?id=<?= $value['overtime_id'] ?>" class="btn btn-warning btn-sm ms-2">Edit</a>
                                                                <?php elseif ($userRole === 4) : // Cek apakah peran sama dengan supervisor 
                                                                ?>
                                                                    <a href="overtime_detail.php?id=<?= $value['overtime_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                                                    <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" class="d-flex">
                                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                        <input type="hidden" name="overtime_id" value="<?= $value['overtime_id'] ?>">
                                                                        <input type="hidden" name="user_id" value="<?= $value['user_id'] ?>">
                                                                        <button type="submit" name="submit" value="Approve" class="btn btn-success btn-sm ms-2">Approve</button>
                                                                        <button type="submit" name="submit" value="Reject" class="btn btn-danger btn-sm ms-2">Reject</button>
                                                                    </form>
                                                                <?php elseif ($userRole === 2) : // Cek apakah peran sama dengan leader 
                                                                ?>
                                                                    <a href="overtime_detail.php?id=<?= $value['overtime_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                                                    <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" class="d-flex">
                                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                        <input type="hidden" name="overtime_id" value="<?= $value['overtime_id'] ?>">
                                                                        <input type="hidden" name="user_id" value="<?= $value['user_id'] ?>">
                                                                        <?php if ($userRole === 2 && $value['type'] === 'Urgent') : ?>
                                                                            <button type="submit" name="submit" value="Approve" class="btn btn-success btn-sm ms-2">Approve</button>
                                                                        <?php else : ?>
                                                                            <button type="submit" name="submit" value="Check" class="btn btn-success btn-sm ms-2">Check</button>
                                                                        <?php endif; ?>
                                                                        <button type="submit" name="submit" value="Reject" class="btn btn-danger btn-sm ms-2">Reject</button>
                                                                    </form>
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
                                <?php elseif($userRole === 1) : ?>

                                    <div class="table-responsive">
                                    <table class="table mb-0 mt-3">
                                        <thead>
                                            <tr>
                                                <th scope="col">No</th>
                                                <th scope="col" style="min-width : 200px;">Full Name</th>
                                                <th scope="col" style="min-width : 200px;">Project</th>
                                                <th scope="col" style="min-width : 200px;">Division</th>
                                                <th scope="col">Type</th>
                                                <th scope="col" style="min-width : 200px;">Start Date</th>
                                                <th scope="col" style="min-width : 200px;">Finish Date</th>
                                                <th scope="col">Status</th>
                                                <th scope="col" style="min-width : 210px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($karyawanArray) > 0) : ?>
                                                <?php foreach ($karyawanArray as $key => $value) : ?>
                                                    <tr>
                                                        <td><?= $key + 1 + $offset ?></td>
                                                        <td><?= $value['name'] ?></td>
                                                        <td><?= $value['project_name'] ?></td>
                                                        <td><?= $value['division_name'] ?></td>
                                                        <td><?= $value['type'] ?></td>
                                                        <td><?= date('d-M-Y H:i', strtotime($value['start_date'])) ?></td>
                                                        <td><?= date('d-M-Y H:i', strtotime($value['finish_date'])) ?></td>
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
                                                                <?php if ($userRole === 3) : // Cek apakah peran sama dengan admin 
                                                                ?>
                                                                    <a href="overtime_detail.php?id=<?= $value['overtime_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                                                    <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" style="display: inline;">
                                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                        <input type="hidden" name="overtime_id" value="<?= $value['overtime_id'] ?>">
                                                                        <input type="hidden" name="user_id" value="<?= $value['user_id'] ?>">
                                                                        <button type="submit" name="submit" value="Check" class="btn btn-success btn-sm ms-2">Submit</button>
                                                                    </form>
                                                                <?php elseif ($userRole === 1) : // Cek apakah peran sama dengan user
                                                                ?>
                                                                    <?php if ($value['status'] !== 'Approved' && $value['status'] !== 'Rejected') : ?>
                                                                        <a href="overtime_delete.php?id=<?= $value['overtime_id']; ?>" class="btn btn-danger btn-sm ms-2" onclick="return confirm('Apakah kamu yakin?')">Delete</a>
                                                                    <?php endif; ?>
                                                                    <a href="overtime_detail.php?id=<?= $value['overtime_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                                                    <a href="overtime_update.php?id=<?= $value['overtime_id'] ?>" class="btn btn-warning btn-sm ms-2">Edit</a>
                                                                <?php elseif ($userRole === 4) : // Cek apakah peran sama dengan supervisor 
                                                                ?>
                                                                    <a href="overtime_detail.php?id=<?= $value['overtime_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                                                    <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" class="d-flex">
                                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                        <input type="hidden" name="overtime_id" value="<?= $value['overtime_id'] ?>">
                                                                        <input type="hidden" name="user_id" value="<?= $value['user_id'] ?>">
                                                                        <button type="submit" name="submit" value="Approve" class="btn btn-success btn-sm ms-2">Approve</button>
                                                                        <button type="submit" name="submit" value="Reject" class="btn btn-danger btn-sm ms-2">Reject</button>
                                                                    </form>
                                                                <?php elseif ($userRole === 2) : // Cek apakah peran sama dengan leader 
                                                                ?>
                                                                    <a href="overtime_detail.php?id=<?= $value['overtime_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                                                    <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" class="d-flex">
                                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                        <input type="hidden" name="overtime_id" value="<?= $value['overtime_id'] ?>">
                                                                        <input type="hidden" name="user_id" value="<?= $value['user_id'] ?>">
                                                                        <?php if ($userRole === 2 && $value['type'] === 'Urgent') : ?>
                                                                            <button type="submit" name="submit" value="Approve" class="btn btn-success btn-sm ms-2">Approve</button>
                                                                        <?php else : ?>
                                                                            <button type="submit" name="submit" value="Check" class="btn btn-success btn-sm ms-2">Check</button>
                                                                        <?php endif; ?>
                                                                        <button type="submit" name="submit" value="Reject" class="btn btn-danger btn-sm ms-2">Reject</button>
                                                                    </form>
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
                                                    <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $prevPage . ($search ? '&search=' . $search : '') . ($filter_division ? '&filter_division=' . $filter_division : '') . ($filter_project ? '&filter_project=' . $filter_project : ''); ?>">Previous</a>
                                                </li>
                                            <?php else : ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">Previous</span>
                                                </li>
                                            <?php endif; ?>
                                            <?php for ($i = 1; $i <= $jumlah_halaman; $i++) : ?>
                                                <li class="page-item<?= $i == $halaman_aktif ? ' active' : ''; ?>">
                                                    <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $i . ($search ? '&search=' . $search : '') . ($filter_division ? '&filter_division=' . $filter_division : '') . ($filter_project ? '&filter_project=' . $filter_project : ''); ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($halaman_aktif < $jumlah_halaman) : ?>
                                                <?php $nextPage = $halaman_aktif + 1; ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $nextPage . ($search ? '&search=' . $search : '') . ($filter_division ? '&filter_division=' . $filter_division : '') . ($filter_project ? '&filter_project=' . $filter_project : ''); ?>">Next</a>
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