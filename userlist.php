<?php
session_start();
include __DIR__ . "/include/baseUrl.inc.php";
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

$query = "SELECT 
users.user_id,
users.name,
users.username,
users.email,
m_roles.name AS role_name
FROM users
LEFT JOIN m_roles ON users.role_id = m_roles.role_id
";

$roleQuery = "SELECT role_id, name FROM m_roles";
$roleData = mysqli_prepare($conn, $roleQuery);
mysqli_stmt_execute($roleData);
$roleData = mysqli_stmt_get_result($roleData);
$roleOptions = mysqli_fetch_all($roleData, MYSQLI_ASSOC);

$filter_role = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filter_role']) && !empty($_GET['filter_role'])) {
  $filter_role = cleanValue($_GET['filter_role']);
  $query .= (strpos($query, 'WHERE') === false) ? " WHERE" : " AND";
  $query .= " m_roles.role_id = '$filter_role'";
}

$search = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search']) && !empty($_GET['search'])) {
  $search = cleanValue($_GET['search']);
  $query .= (strpos($query, 'WHERE') === false) ? " WHERE" : " AND";
  $query .= " ( users.name LIKE '%$search%' OR users.email LIKE '%$search%')";
}
$query .= "ORDER BY users.user_id DESC";

// var_dump($roleOptions);
$jumlah_semua_data = mysqli_num_rows(mysqli_query($conn, $query));
$query .= " LIMIT $limit OFFSET $offset";
$data = mysqli_query($conn, $query);
$karyawanArray = mysqli_fetch_all($data, MYSQLI_ASSOC);
$jumlah_halaman = ceil($jumlah_semua_data / $limit);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.inc.php"; ?>
  <title>OLAMS - User List</title>
</head>

<body>
  <div class="wrapper">
    <?php include "components/sidebar.inc.php"; ?>
    <div class="main">
      <?php include "components/navbar.inc.php"; ?>
      <main class="content">
        <div class="container-fluid p-0">
          <h1 class="h1 mb-3 judul_halaman"><strong>User List</strong></h1>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="d-flex align-items-center">
                      <form action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" method="get" class="d-flex">
                        <label for="inputSearch" class="ms-3 me-2 mt-1">Search</label>
                        <input type="text" name="search" id="inputSearch" placeholder="Enter fullname, username, email" class="form-control form-control-sm" value="<?= $search ?>">
                        <label for="inputRole" class="mx-2 mt-1">Role</label>
                        <select name="filter_role" id="inputRole" class="form-select form-control-sm" style="width: 200px;">
                          <option value="">Select Role</option>
                          <?php foreach ($roleOptions as $option) : ?>
                            <?php $selected = ($filter_role == $option['role_id']) ? 'selected' : ''; ?>
                            <option value="<?= $option['role_id'] ?>" <?= $selected ?>>
                              <?= $option['name'] ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary ms-2 mt-1">Search</button>
                        <a href="<?php echo cleanValue($_SERVER['PHP_SELF']);?>" class="btn btn-warning btn-sm ms-2 mt-1">Reset</a>
                      </form>
                    </div>
                  </div>
                  <div class="col-md-6 text-end mt-2">
                    <a href="userlist_add.php" class="btn-sm btn-success me-3 text-white text-decoration-none">+ Add User</a>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table mb-0 mt-3">
                    <thead>
                      <tr>
                        <th scope="col">No</th>
                        <th scope="col">Full Name</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (count($karyawanArray) > 0) : ?>
                        <?php foreach ($karyawanArray as $key => $value) : ?>
                          <tr>
                            <th scope="row"> <?= $key + 1 + $offset  ?> </th>
                            <td> <?= $value['name'] ?> </td>
                            <td> <?= $value['username'] ?> </td>
                            <td>
                              <a href="<?= $value['email'] ?>"> <?= $value['email'] ?> </a>
                            </td>
                            <td> <?= $value['role_name'] ?></td>
                            <td>
                              <a href="userlist_update.php?id=<?= $value['user_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                              <a href="userlist_delete.php?id=<?= $value['user_id'] ?>" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else : ?>
                        <tr>
                          <td colspan="6" style="text-align: center;">No records found!.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
                <div>
                  <div class="dataTables_paginate paging_simple_numbers ms-3 mt-3" id="datatables-reponsive_paginate">
                    <ul class="pagination justify-content-end">
                      <?php if ($jumlah_semua_data > $limit) : ?>
                        <?php if ($halaman_aktif > 1) : ?>
                          <?php $prevPage = $halaman_aktif - 1; ?>
                          <li class="page-item">
                            <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $prevPage; ?><?php if (!empty($search)) {echo '&search=' . $search;} ?>
                              <?php if (!empty($filter_role)) {
                            echo '&filter_role=' . $filter_role;
                          } ?>">Previous</a>
                          </li>
                        <?php else : ?>
                          <li class="page-item disabled">
                            <span class="page-link">Previous</span>
                          </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $jumlah_halaman; $i++) : ?>
                          <li class="page-item<?= $i == $halaman_aktif ? ' active' : '' ?>">
                            <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $i; ?><?php if (!empty($search)) {echo '&search=' . $search;} ?>
                            <?php if (!empty($filter_role)) {
                            echo '&filter_role=' . $filter_role;
                          } ?>"><?= $i ?></a>
                          </li>
                        <?php endfor; ?>

                        <?php if ($halaman_aktif < $jumlah_halaman) : ?>
                          <?php $nextPage = $halaman_aktif + 1; ?>
                          <li class="page-item">
                            <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $nextPage; ?><?php if (!empty($search)) {echo '&search=' . $search;} ?>
                            <?php if (!empty($filter_role)) {
                            echo '&filter_role=' . $filter_role;
                          } ?>">Next</a>
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
        </div>
      </main>
      <?php include "components/footer.inc.php"; ?>
    </div>
  </div>
  <?php include "script.inc.php"; ?>
</body>

</html>