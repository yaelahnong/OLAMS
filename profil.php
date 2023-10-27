<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";


if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit();
}
$user_id = $_SESSION["user_id"];

$query = "SELECT 
  users.user_id,
  users.name AS fullname,
  users.username,
  users.email,
  m_roles.name AS role_name
  FROM users
  LEFT JOIN m_roles ON users.role_id = m_roles.role_id
  WHERE users.user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
  $fullname = $row['fullname'];
  $username = $row['username'];
  $email = $row['email'];
  $role = $row['role_name'];
}

$newPasswordErr = $confirmPasswordErr = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Ambil nilai password baru dan konfirmasi password dari form
  $newPassword = isset($_POST['new_password']) ? cleanValue($_POST['new_password']) : NULL;
  $confirmPassword = isset($_POST['confirm_password']) ? cleanValue($_POST['confirm_password']) : NULL;

  if (empty($newPassword) && empty($confirmPassword)) {
    echo "<script>alert('nothing has changed.')</script>";
    echo "<script>window.location.href = 'userlist.php'</script>";
    exit();
  }

  if (strlen($newPassword) < 8) {
    $newPasswordErr = "Password harus terdiri dari setidaknya 8 karakter.";
  } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]+$/", $newPassword)) {
    $newPasswordErr = "Passwords must contain at least one lowercase letter, one uppercase letter, and one number.";
  }

  if (!empty($newPassword) && !empty($confirmPassword)) {
    if (strlen($confirmPassword) < 8) {
      $confirmPasswordErr = "Password harus terdiri dari setidaknya 8 karakter.";
    } elseif ($newPassword !== $confirmPassword) {
      $confirmPasswordErr = "Konfirmasi password tidak sesuai dengan password baru.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]+$/", $confirmPassword)) {
      $confirmPasswordErr = "Passwords must contain at least one lowercase letter, one uppercase letter, and one number.";
    }

    if (empty($newPasswordErr) && empty($confirmPasswordErr)) {
      // Enkripsi password baru dengan password_hash
      $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

      $updateQuery = "UPDATE users SET password = ?, updated_by = ? WHERE user_id = ?";
      $updateStmt = mysqli_prepare($conn, $updateQuery);
      mysqli_stmt_bind_param($updateStmt, "sii", $hashedPassword, $user_id, $user_id);

      if (mysqli_stmt_execute($updateStmt)) {
        // Password berhasil diubah
        echo "<script>alert('password changed successfully.')</script>";
        echo "<script>window.location.href = 'userlist.php'</script>";
        exit();
      } else {
        // Terjadi kesalahan saat mengubah password
        $passwordErr = "Gagal mengubah password.";
      }
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.inc.php"; ?>
  <title>OLAMS - Profil</title>
</head>

<body>
  <div class="wrapper">
    <?php include "components/sidebar.inc.php"; ?>
    <div class="main">
      <?php include "components/navbar.inc.php"; ?>
      <main class="content">
        <div class="container-fluid p-0">
          <h1 class="h1 mb-3 judul_halaman"><strong>Profile Akun Anda:</strong></h1>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                </div>
                <div class="card-body">
                  <form action="<?= cleanValue($_SERVER['PHP_SELF']) ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="row">
                      <div class="col-md-6">
                        <!-- Elemen-elemen di sebelah kiri -->
                        <div class="mb-3">
                          <label class="form-label" for="inputFullname">Fullname</label>
                          <input type="text" class="form-control" name="name" id="inputFullnme" placeholder="Enter fullname" value="<?= $fullname; ?>" disabled>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="inputPassword4">Username</label>
                          <input type="text" class="form-control" name="username" id="inputPassword4" placeholder="Enter username" value="<?= $username; ?>" disabled>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="inputEmail4">Email</label>
                          <input type="email" class="form-control" name="email" id="inputEmail4" placeholder="Enter email" value="<?= $email; ?>" disabled>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="inputState">Role</label>
                          <input type="text" class="form-control" name="role_name" value="<?= $role; ?>" disabled>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <!-- Elemen-elemen di sebelah kanan (Password dan Konfirmasi Password) -->
                        <div class="mb-3">
                          <label class="form-label" for="inputPassword4">Password Baru</label>
                          <input type="password" class="form-control" name="new_password" id="inputPassword4" placeholder="Enter password" value="">
                          <span class="error" style="color: red;"> <?= $newPasswordErr; ?> </span>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="inputPassword4">Confirmation Password</label>
                          <input type="password" class="form-control" name="confirm_password" id="inputPassword4" placeholder="Enter password" value="">
                          <span class="error" style="color: red;"> <?= $confirmPasswordErr; ?> </span>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col align-items-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="userlist.php" class="btn btn-light text-dark text-decoration-none">Cancel</a>
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