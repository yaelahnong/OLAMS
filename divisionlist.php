<?php
session_start();
include __DIR__ . "/include/conn.inc.php";


if (!isset($_SESSION["user_id"])) {
  header("Location: login/login.php");
  exit();
}

// query 1
$show_divisi = "SELECT COUNT(division_id) AS jumlah_semua_data FROM m_divisions";

$parameters = array();

$limit = 5;
$halaman_aktif = isset($_GET['page']) ? cleanValue($_GET['page']) : 1;
$offset = ($halaman_aktif - 1) * $limit;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search']) && !empty($_GET['search'])) {
  $search_divisi = cleanValue($_GET['search']);
  $show_divisi .= " WHERE division_name LIKE ?";
  $parameters[] = "%" . $search_divisi . "%";
}

$stmt1 = mysqli_prepare($conn, $show_divisi);

if (!empty($parameters)) {
  $types = str_repeat("s", count($parameters));
  mysqli_stmt_bind_param($stmt1, $types, ...$parameters);
}

mysqli_stmt_execute($stmt1);
$result1 = mysqli_stmt_get_result($stmt1);
$projectData = mysqli_fetch_assoc($result1);

$jumlah_semua_data = $projectData['jumlah_semua_data']; 

// query 2
$show_divisi = "SELECT division_id, division_name FROM m_divisions";

$parameters = array();

$limit = 5;
$halaman_aktif = isset($_GET['page']) ? cleanValue($_GET['page']) : 1;
$offset = ($halaman_aktif - 1) * $limit;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search']) && !empty($_GET['search'])) {
  $search_divisi = cleanValue($_GET['search']);
  $show_divisi .= " WHERE division_name LIKE ?";
  $parameters[] = "%" . $search_divisi . "%";
}

$show_divisi .= " LIMIT $limit OFFSET $offset ";
$jumlah_halaman = ceil($jumlah_semua_data / $limit);

$prevPage = $halaman_aktif - 1;
$nextPage = $halaman_aktif + 1;

$stmt2 = mysqli_prepare($conn, $show_divisi);

if (!empty($parameters)) {
  $types = str_repeat("s", count($parameters));
  mysqli_stmt_bind_param($stmt2, $types, ...$parameters);
}
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);
$data = mysqli_fetch_all($result2, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.inc.php"; ?>
  <title>OLAMS - Division List</title>
</head>

<body>
  <div class="wrapper">
    <?php include "components/sidebar.inc.php"; ?>
    <div class="main">
      <?php include "components/navbar.inc.php"; ?>
      <main class="content">
        <div class="container-fluid p-0">
          <h1 class="h1 mb-3 judul_halaman"><strong>Division List</strong></h1>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="d-flex align-items-center">
                      <form action="<?= cleanValue($_SERVER['PHP_SELF']); ?>" method="get" class="d-flex">
                        <label for="inputSearch" class="ms-3 me-2">Search</label>
                        <input type="text" name="search" id="inputSearch" placeholder="Enter division name" class="form-control form-control-sm" value="<?php echo $search_divisi ?? ''; ?>">
                        <button type="submit" class="btn btn-sm btn-primary ms-2">Search</button>
                        <a class="btn btn-sm btn-warning mx-2" href="<?php echo cleanValue($_SERVER['PHP_SELF']);?>">Reset</a>
                      </form>
                    </div>
                  </div>
                  <div class="col-md-6 text-end">
                      <a href="divisionlist_add.php" class="btn btn-sm btn-success me-3 text-white text-decoration-none">+ Add Division</a>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table mb-0 mt-3">
                    <thead>
                      <tr>
                        <th scope="col">No</th>
                        <th scope="col">Division name</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (count($data) > 0) : ?>
                        <?php foreach ($data as $key => $row) : ?>
                          <tr>
                            <th scope="row"><?= $key + 1 + $offset ; ?></th>
                            <td><?= $row['division_name']; ?></td>
                            <td>
                              <a href="divisionlist_update.php?id=<?= $row['division_id']; ?>" class="text-warning"><i class="align-middle" data-feather="edit"></i></a>
                              <a href="divisionlist_delete.php?id=<?= $row['division_id']; ?>" class="text-danger"><i class="align-middle ms-3" data-feather="trash-2" onclick="return confirm('Are you sure you want to delete this data?')"></i></a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else : ?>
                        <tr>
                          <td colspan="7" style="text-align: center;">No records found.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
                <div>
                  <div class="dataTables_paginate paging_simple_numbers ms-3 mt-3" id="datatables-reponsive_paginate">
                    <ul class="pagination justify-content-end">
                      <?php if($jumlah_semua_data > $limit) : ?>
                      <?php if ($halaman_aktif > 1) :?>
                          <li class="page-item">
                            <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $prevPage; ?><?php if (!empty($search_divisi)) { echo '&search=' . $search_divisi; } ?>">Previous</a>
                          </li>
                        <?php else : ?>
                          <li class="page-item disabled">
                            <span class="page-link">Previous</span>
                          </li>
                        <?php endif; ?>

                       <!-- Tampilkan tautan halaman -->
                      <?php for ($i = 1; $i <= $jumlah_halaman; $i++) : ?>
                        <li class="page-item<?= $i == $halaman_aktif ? ' active' : '' ?>">
                          <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $i; ?><?php if (!empty($search_divisi)) { echo '&search=' . $search_divisi; } ?>"><?= $i ?></a>
                        </li>
                      <?php endfor; ?>

                      <!-- Tampilkan tautan "Next" jika bukan di halaman terakhir -->
                      <?php if ($halaman_aktif < $jumlah_halaman) : ?>
                        <li class="page-item">
                          <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $nextPage; ?><?php if (!empty($search_divisi)) { echo '&search=' . $search_divisi; } ?>">Next</a>
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