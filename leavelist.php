<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login/login.php");
    exit();
}

$limit = 5;
$halaman_aktif = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($halaman_aktif - 1) * $limit;

$query = "SELECT 
leaves.leaves_id, 
leaves.reason, 
leaves.category, 
leaves.start_date, 
leaves.finish_date,
m_divisions.division_name AS division_name,
users.name AS name,
leaves.status
FROM leaves
LEFT JOIN users ON leaves.user_id = users.user_id
LEFT JOIN m_divisions ON leaves.division_id = m_divisions.division_id
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

$filter_division = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filter_division']) && !empty($_GET['filter_division'])) {
    $filter_division = cleanValue($_GET['filter_division']);
    $query .= (strpos($query, 'WHERE') === false) ? " WHERE" : " AND";
    $query .= " m_divisions.division_id = '$filter_division'";
}

$search = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search']) && !empty($_GET['search'])) {
    $search = cleanValue($_GET['search']);
    $query .= (strpos($query, 'WHERE') === false) ? " WHERE" : " AND";
    $query .= " ( users.name LIKE '%$search%' OR leaves.category LIKE '%$search%')";
}
$query .= " ORDER BY leaves.leaves_id DESC";

$jumlah_semua_data = mysqli_num_rows(mysqli_query($conn, $query));
$query .= " LIMIT $limit OFFSET $offset ";
$data = mysqli_query($conn, $query);
$leaveArray = mysqli_fetch_all($data, MYSQLI_ASSOC);
$jumlah_halaman = ceil($jumlah_semua_data / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.inc.php"; ?>
    <title>OLAMS - Leave List</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3 judul_halaman"><strong>Leave List</strong></h1>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="d-flex align-items-center">
                                            <form action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" method="get" class="d-flex">
                                                <label for="inputSearch" class="m-2 mx-2">Search</label>
                                                <input type="text" name="search" id="inputSearch" placeholder="Enter Category or name" class="form-control form-control" value="<?= $search ?>">
                                                <label for="inputRole" class="m-2 mx-2">Role</label>
                                                <select name="filter_division" id="inputRole" class="form-select form-control">
                                                    <option value="">Select Division</option>
                                                    <?php foreach ($divisionOptions as $option) : ?>
                                                        <?php $selected = ($filter_division == $option['division_id']) ? 'selected' : ''; ?>
                                                        <option value="<?= $option['division_id'] ?>" <?= $selected ?>>
                                                            <?= $option['division_name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary mb-2 mx-2">Search</button>
                                                <a class="btn btn-sm btn-warning mb-2 mx-2" href="<?php echo cleanValue($_SERVER['PHP_SELF']); ?>">Reset</a>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-5 text-end">
                                        <a href="leavelist_add.php" class="btn-sm btn-success me-3 text-white text-decoration-none">+ Add Leave</a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table mb-0 mt-3">
                                        <thead>
                                            <tr>
                                                <th scope="col">No</th>
                                                <th scope="col">Full Name</th>
                                                <th scope="col">Division</th>
                                                <th scope="col">Reason</th>
                                                <th scope="col">Category</th>
                                                <th scope="col">Start Date</th>
                                                <th scope="col">Finish Date</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($leaveArray) > 0) : ?>
                                                <?php foreach ($leaveArray as $key => $value) : ?>
                                                    <tr>
                                                        <td><?= $key + 1 + $offset ?></td>
                                                        <td><?= $value['name'] ?></td>
                                                        <td><?= $value['division_name'] ?></td>
                                                        <td><?= $value['reason'] ?></td>
                                                        <td><?= $value['category'] ?></td>
                                                        <td><?= $value['start_date'] ?></td>
                                                        <td><?= $value['finish_date'] ?></td>
                                                        <td><?= $value['status'] ?></td>
                                                        <td>
                                                            <a href="leavelist_update.php?id=<?= $value['leaves_id']; ?>" class="text-warning"><i class="align-middle" data-feather="edit"></i></a>
                                                            <a href="leavelist_delete.php?id=<?= $value['leaves_id']; ?>" class="text-danger"><i class="align-middle ms-3" data-feather="trash-2" onclick="return confirm('Apakah kamu yakin?')"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td colspan="8" style="text-align: center;">Data tidak ada!!</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="dataTables_paginate paging_simple_numbers ms-3 mt-3">
                                    <ul class="pagination justify-content-end">
                                        <?php if ($jumlah_semua_data > $limit) : ?>
                                            <?php if ($halaman_aktif > 1) : ?>
                                                <?php $prevPage = $halaman_aktif - 1; ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $prevPage . ($search ? '&search=' . $search : '') . ($filter_division ? '&filter_division=' . $filter_division : ''); ?>">Previous</a>
                                                </li>
                                            <?php else : ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">Previous</span>
                                                </li>
                                            <?php endif; ?>
                                            <?php for ($i = 1; $i <= $jumlah_halaman; $i++) : ?>
                                                <li class="page-item<?= $i == $halaman_aktif ? ' active' : ''; ?>">
                                                    <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $i . ($search ? '&search=' . $search : '') . ($filter_division ? '&filter_division=' . $filter_division : ''); ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($halaman_aktif < $jumlah_halaman) : ?>
                                                <?php $nextPage = $halaman_aktif + 1; ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $nextPage . ($search ? '&search=' . $search : '') . ($filter_division ? '&filter_division=' . $filter_division : ''); ?>">Next</a>
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
