<?php
session_start();
include __DIR__ . "/include/baseUrl.inc.php";
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: login/login.php");
  exit;
}

$roleQuery = "SELECT role_id, name FROM m_roles";
$roleData = mysqli_prepare($conn, $roleQuery);
mysqli_stmt_execute($roleData);
$roleData = mysqli_stmt_get_result($roleData);
$roleOptions = mysqli_fetch_all($roleData, MYSQLI_ASSOC);

$fullnameErr = $usernameErr = $passwordErr = $emailErr = $roleErr = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
    $fullname = isset($_POST['name']) ? cleanValue($_POST['name']) : NULL;
    $username = isset($_POST['username']) ? cleanValue($_POST['username']) : NULL;
    $email = isset($_POST['email']) ? cleanValue($_POST['email']) : NULL;
    $password = isset($_POST['password']) ? cleanValue($_POST['password']) : NULL;
    $role = isset($_POST['role_id']) ? cleanValue($_POST['role_id']) : null;

    if (empty($fullname)) {
      $fullnameErr = "Nama harus diisi.";
    } elseif (!preg_match("/^[A-Za-z.' ]*$/", $fullname) || strlen($fullname) < 3 || strlen($fullname) > 60) {
      $fullnameErr = "Nama harus terdiri dari 3 hingga 60 karakter huruf, tanda kutip, dan titik diperbolehkan.";
    }

    if (empty($username)) {
      $usernameErr = "Username harus diisi.";
    } elseif (!preg_match("/^[A-Za-z.' ]*$/", $username) || strlen($username) < 3 || strlen($username) > 60) {
      $usernameErr = "Username harus terdiri dari 3 hingga 60 karakter huruf, tanda kutip, dan titik.";
    }

    if (empty($email)) {
      $emailErr = "Email tidak boleh kosong.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) < 6 || strlen($email) > 30) {
      $emailErr = "Email tidak valid. Harus memiliki panjang antara 6 hingga 30 karakter.";
    }

    if (empty($password)) {
      $passwordErr = 'Password harus diisi.';
    } elseif (strlen($password) < 8) {
      $passwordErr = 'Password minimal harus terdiri dari 8 karakter.';
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", $password)) {
      $passwordErr = 'Password harus mengandung setidaknya satu huruf kecil, satu huruf besar, dan satu angka.';
    } else {
      // Hash password 
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    if (empty($role)) {
      $roleErr = "Anda harus memilih (role).";
    }

    if (empty($fullnameErr) && empty($usernameErr) && empty($emailErr) && empty($passwordErr) && empty($roleErr)) {
      $queryUpdate = "UPDATE users SET
    name = ?, 
    username = ?, 
    email = ?, 
    password = ?, 
    role_id = ?
    WHERE user_id = ?";

      $stmt = mysqli_prepare($conn, $queryUpdate);
      mysqli_stmt_bind_param(
        $stmt,
        "ssssss",
        $fullname,
        $username,
        $email,
        $hashedPassword,
        $role,
        $_GET['id']
      );
      mysqli_stmt_execute($stmt);
      echo "<script>alert('Data berhasil diupdate')</script>";
      echo "<script>window.location.replace('userlist.php')</script>";
    }
  } else {
    $TokenErr = "Invalid CSRF token";
  }
}

if (isset($_GET['id'])) {
  $karyawanId = cleanValue($_GET['id']);

  $queryKaryawan = "SELECT
  name,
  username,
  email,
  password,
  role_id
  FROM users WHERE user_id = ? ";

  $resultKaryawan = mysqli_prepare($conn, $queryKaryawan);
  mysqli_stmt_bind_param($resultKaryawan, "s", $karyawanId);
  mysqli_stmt_execute($resultKaryawan);
  $resultKaryawan = mysqli_stmt_get_result($resultKaryawan);

  if ($resultKaryawan) {
    $karyawanData = mysqli_fetch_assoc($resultKaryawan);

    // Mengisi data awal pada variabel
    $fullname = $karyawanData['name'];
    $username = $karyawanData['username'];
    $email = $karyawanData['email'];
    $password = $karyawanData['password'];
    $role = $karyawanData['role_id'];
  } else {
    echo "Data Tidak Ditemukan";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.inc.php"; ?>
  <title>OLAMS - Update User</title>
</head>

<body>
  <div class="wrapper">
    <?php include "components/sidebar.inc.php"; ?>
    <div class="main">
      <?php include "components/navbar.inc.php"; ?>
      <main class="content">
        <div class="container-fluid p-0">
          <h1 class="h1 mb-3"><strong>Update User</strong></h1>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                </div>
                <div class="card-body">
                  <form action="<?= cleanValue($_SERVER['PHP_SELF']) . '?id=' . $_GET['id'] ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="row">
                      <div class="mb-3 col-md-6">
                        <label class="form-la   bel" for="inputEmail4">Fullname</label>
                        <input type="text" class="form-control" name="name" id="inputEmail4" placeholder="Enter fullname" value="<?= $fullname; ?>">
                        <span class="error" style="color: red;"> <?= $fullnameErr; ?> </span>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputPassword4">Username</label>
                        <input type="text" class="form-control" name="username" id="inputPassword4" placeholder="Enter username" value="<?= $username; ?>">
                        <span class="error" style="color: red;"> <?= $usernameErr; ?> </span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputEmail4">Email</label>
                        <input type="email" class="form-control" name="email" id="inputEmail4" placeholder="Enter email" value="<?= $email; ?>">
                        <span class="error" style="color: red;"> <?= $emailErr; ?> </span>
                      </div>
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputPassword4">Password</label>
                        <input type="password" class="form-control" name="password" id="inputPassword4" placeholder="Enter password" value="">
                        <span class="error" style="color: red;"> <?= $passwordErr; ?> </span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputState">Role</label>
                        <div class="input-group mb-3">
                          <select name="role_id" id="inputRole" class="form-select form-control-sm" style="width: 200px;">
                            <option value="">Select role</option>
                            <?php foreach ($roleOptions as $key => $value) : ?>
                              <?php $selected = ($value['role_id'] == $role ? 'selected' : '') ?>
                              <option value="<?= $value['role_id'] ?>" <?= $selected ?>> <?= $value['name'] ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <span class="error" style="color: red;"> <?= $roleErr; ?> </span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="userlist.php" class="btn btn-danger text-white text-decoration-none">Cancel</a>
                      </div>
                    </div>
                  </form>
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