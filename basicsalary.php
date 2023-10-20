<?php
session_start();
include __DIR__ . "/include/conn.inc.php";

if (!isset($_SESSION["user_id"])) {
  header("Location: login/login.php");
  exit();
}

$show_basic_salary = "SELECT bs.basic_salary_id, u.name, bs.total_basic_salary, bs.created_at
                     FROM m_basic_salaries bs
                     INNER JOIN users u ON bs.user_id = u.user_id";

$parameters = array();

$limit = 5;
$halaman_aktif = isset($_GET['page']) ? cleanValue($_GET['page']) : 1;
$offset = ($halaman_aktif - 1) * $limit;

$search_basic_salary = ""; // Inisialisasi variabel

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search']) && !empty($_GET['search'])) {
  $search_basic_salary = cleanValue($_GET['search']);
  $show_basic_salary .= " WHERE u.name LIKE ?";
  $parameters[] = "%" . $search_basic_salary . "%";
}

$show_basic_salary .= " ORDER BY bs.created_at DESC";

$stmt1 = mysqli_prepare($conn, $show_basic_salary);

if (!empty($parameters)) {
  $types = str_repeat("s", count($parameters));
  mysqli_stmt_bind_param($stmt1, $types, ...$parameters);
}

mysqli_stmt_execute($stmt1);
$result1 = mysqli_stmt_get_result($stmt1);

$show_basic_salary .= " LIMIT $limit OFFSET $offset ";

// Hitung jumlah data
$count_query = "SELECT COUNT(*) AS jumlah_data FROM m_basic_salaries";
$count_result = mysqli_query($conn, $count_query);
$count_data = mysqli_fetch_assoc($count_result);
$jumlah_semua_data = $count_data['jumlah_data'];

$jumlah_halaman = ceil($jumlah_semua_data / $limit);
$prevPage = $halaman_aktif - 1;
$nextPage = $halaman_aktif + 1;

$stmt2 = mysqli_prepare($conn, $show_basic_salary);

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
  <title>OLAMS - Basic Salary List</title>
</head>

<body>
  <div class="wrapper">
    <?php include "components/sidebar.inc.php"; ?>
    <div class="main">
      <?php include "components/navbar.inc.php"; ?>
      <main class="content">
        <div class="container-fluid p-0">
          <h1 class="h1 mb-3 judul_halaman"><strong>Basic Salary List</strong></h1>
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
                        <input type="text" name="search" id="inputSearch" placeholder="Enter user name " class="form-control form-control-sm" value="<?php echo $search_basic_salary ?? ''; ?>">
                        <button type="submit" class="btn btn-sm btn-primary ms-2">Search</button>
                        <a class="btn btn-sm btn-warning mx-2" href="<?php echo cleanValue($_SERVER['PHP_SELF']); ?>">Reset</a>
                      </form>
                    </div>
                  </div>
                  <div class="col-md-6 text-end">
                    <!-- Link to Add Basic Salary -->
                    <a href="basicsalary_add.php" class="btn btn-sm btn-success me-3 text-white text-decoration-none">+ Add Basic Salary</a>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table mb-0 mt-3">
                    <thead>
                      <tr>
                        <th scope="col">No</th>
                        <th scope="col">Fullname</th>
                        <th scope="col">Total Basic Salary</th>
                        <th scope="col">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (count($data) > 0) : ?>
                        <?php foreach ($data as $key => $row) : ?>
                          <tr>
                            <td><?= $key + 1 + $offset; ?></td> <!-- Menggunakan nomor baris (row number) -->
                            <td><?= $row['name']; ?></td>
                            <td><?= number_format($row['total_basic_salary']); ?></td>
                            <td>
                              <!-- Link to Update and Delete Basic Salary -->
                              <a href="basicsalary_update.php?id=<?= $row['basic_salary_id']; ?>" class="text-warning"><i class="align-middle" data-feather="edit"></i></a>
                              <a href="basicsalary_delete.php?id=<?= $row['basic_salary_id']; ?>" class="text-danger"><i class="align-middle ms-3" data-feather="trash-2" onclick="return confirm('Are you sure?')"></i></a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else : ?>
                        <tr>
                          <td colspan="5" style="text-align: center;">No records found.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
                <div class="dataTables_paginate paging_simple_numbers ms-3 mt-3" id="datatables-reponsive_paginate">
  <ul class="pagination justify-content-end">
    <?php if (!$search_basic_salary || (count($data) > 5 && $halaman_aktif > 1)) : ?>
      <?php if ($halaman_aktif > 1) : ?>
        <li class="page-item">
          <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $prevPage; ?><?php if (!empty($search_basic_salary)) {
                                                                                                      echo '&search=' . $search_basic_salary;
                                                                                                    } ?>">Previous</a>
        </li>
      <?php else : ?>
        <li class="page-item disabled">
          <span class="page-link">Previous</span>
        </li>
      <?php endif; ?>

      <!-- Display page links -->
      <?php for ($i = 1; $i <= $jumlah_halaman; $i++) : ?>
        <li class="page-item<?= $i == $halaman_aktif ? ' active' : '' ?>">
          <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $i; ?><?php if (!empty($search_basic_salary)) {
                                                                                                echo '&search=' . $search_basic_salary;
                                                                                              } ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>

      <!-- Display "Next" link if not on the last page -->
      <?php if ($halaman_aktif < $jumlah_halaman) : ?>
        <li class="page-item">
          <a class="page-link" href="<?= cleanValue($_SERVER['PHP_SELF']) . '?page=' . $nextPage; ?><?php if (!empty($search_basic_salary)) {
                                                                                                      echo '&search=' . $search_basic_salary;
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
      </main>
      <?php include "components/footer.inc.php"; ?>
    </div>
  </div>
  <?php include "script.inc.php"; ?>
</body>

</html>