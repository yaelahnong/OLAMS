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
                      <form action="<?= $_SERVER['PHP_SELF']; ?>" method="get" class="d-flex">
                        <label for="inputSearch" class="ms-3 me-2">Search</label>
                        <input type="text" name="search" id="inputSearch" placeholder="Enter fullname, username, email" class="form-control form-control-sm">
                        <label for="inputRole" class="mx-2">Role</label>
                        <select name="filter_role" id="inputRole" class="form-control form-control-sm" style="width: 200px;">
                          <option value="">Select Role</option>
                          <option value="">User</option>
                          <option value="">Leader</option>
                          <option value="">Admin</option>
                          <option value="">Supervisor</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary ms-2">Search</button>
                      </form>
                    </div>
                  </div>
                  <div class="col-md-6 text-end">
                    <button class="btn btn-sm btn-success me-3">
                      <a href="userlist_add.php" class="text-white text-decoration-none">+ Add User</a>
                    </button>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table mb-0 mt-3">
                    <thead>
                      <tr>
                        <th scope="col">No</th>
                        <th scope="col">Fullname</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th scope="row">1</th>
                        <td>Fazri Alfauzi</td>
                        <td>Fazri</td>
                        <td>
                          <a href="mailto:fajrial39@gmail.com">fajrial39@gmail.com</a>
                        </td>
                        <td>User</td>
                        <td>
                          <a href="userlist_update.php" class="text-warning"><i class="align-middle" data-feather="edit"></i></a>
                          <a href="userlist_delete.php" class="text-danger"><i class="align-middle ms-3" data-feather="trash-2"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <th scope="row">2</th>
                        <td>Fazri Alfauzi</td>
                        <td>Fazri</td>
                        <td>fazri@gmail.com</td>
                        <td>Software Development</td>
                        <td>
                          <a href="userlist_update.php" class="text-warning"><i class="align-middle" data-feather="edit"></i></a>
                          <a href="userlist_delete.php" class="text-danger"><i class="align-middle ms-3" data-feather="trash-2"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <th scope="row">3</th>
                        <td>Fazri Alfauzi</td>
                        <td>Fazri</td>
                        <td>fazri@gmail.com</td>
                        <td>Software Development</td>
                        <td>
                          <a href="userlist_update.php" class="text-warning"><i class="align-middle" data-feather="edit"></i></a>
                          <a href="userlist_delete.php" class="text-danger"><i class="align-middle ms-3" data-feather="trash-2"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <th scope="row">4</th>
                        <td>Fazri Alfauzi</td>
                        <td>Fazri</td>
                        <td>fazri@gmail.com</td>
                        <td>Software Development</td>
                        <td>
                          <a href="userlist_update.php" class="text-warning"><i class="align-middle" data-feather="edit"></i></a>
                          <a href="userlist_delete.php" class="text-danger"><i class="align-middle ms-3" data-feather="trash-2"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <th scope="row">5</th>
                        <td>Fazri Alfauzi</td>
                        <td>Fazri</td>
                        <td>fazri@gmail.com</td>
                        <td>Software Development</td>
                        <td>
                          <a href="userlist_update.php" class="text-warning"><i class="align-middle" data-feather="edit"></i></a>
                          <a href="userlist_delete.php" class="text-danger"><i class="align-middle ms-3" data-feather="trash-2"></i></a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div>
                  <div class="dataTables_paginate paging_simple_numbers ms-3 mt-3" id="datatables-reponsive_paginate">
                    <ul class="pagination justify-content-end">
                      <li class="paginate_button page-item previous disabled" id="datatables-reponsive_previous">
                        <a aria-controls="datatables-reponsive" aria-disabled="true" aria-role="link" data-dt-idx="previous" tabindex="0" class="page-link">Previous</a>
                      </li>
                      <li class="paginate_button page-item active">
                        <a class="page-link"> 1 </a>
                      </li>
                      <li class="paginate_button page-item">
                        <a class="page-link"> 2 </a>
                      </li>
                      <li class="paginate_button page-item">
                        <a class="page-link"> 3 </a>
                      </li>
                      <li class="paginate_button page-item">
                        <a class="page-link"> 4 </a>
                      </li>
                      <li class="paginate_button page-item">
                        <a class="page-link"> 5 </a>
                      </li>
                      <li class="paginate_button page-item next" id="datatables-reponsive_next">
                        <a href="#" aria-controls="datatables-reponsive" aria-role="link" data-dt-idx="next" tabindex="0" class="page-link">Next</a>
                      </li>
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