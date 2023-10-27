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

$query = "SELECT
leaves.leaves_id,
leaves.user_id,
users.name AS name,
m_divisions.division_name AS division_name,
leaves.reason,
leaves.category,
leaves.start_date,
leaves.finish_date,
leaves.status,
leaves.status_updated_at,
leaves.status_updated_by
FROM leaves
LEFT JOIN users ON leaves.user_id = users.user_id
LEFT JOIN m_divisions ON leaves.division_id = m_divisions.division_id";

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

$roleQuery = "SELECT m_roles.role_id FROM users
JOIN m_roles ON users.role_id = m_roles.role_id
WHERE users.user_id = ?";

$roleStatement = mysqli_prepare($conn, $roleQuery);
mysqli_stmt_bind_param($roleStatement, "i", $user_id);
mysqli_stmt_execute($roleStatement);
$roleData = mysqli_stmt_get_result($roleStatement);
$userRole = mysqli_fetch_row($roleData)[0];

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
  $query .= " (users.name LIKE '%$search%' OR leaves.category LIKE '%$search%')";
}

$query .= " ORDER BY leaves.leaves_id DESC";

$jumlah_semua_data = mysqli_num_rows(mysqli_query($conn, $query));
$query .= " LIMIT $limit OFFSET $offset ";
$data = mysqli_query($conn, $query);
$leaveArray = mysqli_fetch_all($data, MYSQLI_ASSOC);
$jumlah_halaman = ceil($jumlah_semua_data / $limit);

$updateStatement = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
  if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
      if (isset($_POST['leaves_id']) && is_numeric($_POST['leaves_id'])) {
          $leaveId = cleanValue($_POST['leaves_id']);

          if ($userRole === 4 || $userRole === 3) {
              $newStatus = ($_POST['submit'] === "Reject") ? 'Reject' : 'Approved';
              $supervisorId = $user_id;

              $updateQuery = "UPDATE leaves SET status = ?, status_updated_by = ?, status_updated_at = NOW() WHERE leaves_id = ?";
              $updateStatement = mysqli_prepare($conn, $updateQuery);

              if ($updateStatement) {
                  mysqli_stmt_bind_param($updateStatement, "sii", $newStatus, $supervisorId, $leaveId);

                  if (mysqli_stmt_execute($updateStatement)) {
                      if ($_POST['submit'] === "Reject") {
                          echo "<script>alert('Leave rejected.')</script>";
                      } else if ($_POST['submit'] === "Approve") {
                          echo "<script>alert('Leave approved.')</script>";
                      }

                      echo "<script>window.location.href = 'leavelist.php'</script>";
                  } else {
                      echo "Kesalahan saat memperbarui data: " . mysqli_error($conn);
                  }

                  mysqli_stmt_close($updateStatement);
              } else {
                  echo "Gagal menyiapkan pernyataan utama: " . mysqli_error($conn);
              }
          } else {
              echo "Anda tidak memiliki izin untuk memodifikasi data cuti.";
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
                    <?php if ($userRole == 1) : ?>
                          <a href="leavelist_add.php" class="btn-sm btn-success me-3 text-white text-decoration-none">+ Add Leave</a>
                    <?php endif ;?>
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
                            <td><?= date('d-M-Y H:i', strtotime($value['start_date'])) ?></td>
                            <td><?= date('d-M-Y H:i', strtotime($value['finish_date'])) ?></td>
                            <td>
                              <div class="d-flex">
                                <?php if ($userRole === 3) : // Cek apakah peran sama dengan admin 
                                ?>
                                  <a href="leavelist_detail.php?id=<?= $value['leaves_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                  <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="leaves_id" value="<?= $value['leaves_id'] ?>">
                                    <input type="hidden" name="user_id" value="<?= $value['user_id'] ?>">
                                    <button type="submit" name="submit" value="Check" class="btn btn-success btn-sm ms-2" onclick="return alert('Leave submited')">Submit</button>
                                  </form>
                                <?php elseif ($userRole === 1) : // Cek apakah peran sama dengan user
                                ?>
                                  <a href="leavelist_delete.php?id=<?= $value['leaves_id']; ?>" class="btn btn-danger btn-sm ms-2" onclick="return confirm('Are you sure?')">Delete</a>
                                  <a href="leavelist_detail.php?id=<?= $value['leaves_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                  <a href="leavelist_update.php?id=<?= $value['leaves_id'] ?>" class="btn btn-warning btn-sm ms-2">Edit</a>
                                <?php elseif ($userRole === 4) : // Cek apakah peran sama dengan supervisor 
                                ?>
                                  <a href="leavelist_detail.php?id=<?= $value['leaves_id'] ?>" class="btn btn-primary btn-sm ms-2">Detail</a>
                                  <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" class="d-flex">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="leaves_id" value="<?= $value['leaves_id'] ?>">
                                    <input type="hidden" name="user_id" value="<?= $value['user_id'] ?>">
                                    <button type="submit" name="submit" value="Approve" class="btn btn-success btn-sm ms-2">Approve</button>
                                    <button type="submit" name="submit" value="Reject" class="btn btn-danger btn-sm ms-2">Reject</button>
                                  </form>
                                <?php endif; ?>
                              </div>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else : ?>
                        <tr>
                          <td colspan="8" style="text-align: center;">No records found!.</td>
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