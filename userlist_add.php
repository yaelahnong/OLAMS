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

$user_id = $_SESSION["user_id"];

$roleQuery = "SELECT role_id, name FROM m_roles";
$roleData = mysqli_prepare($conn, $roleQuery);
mysqli_stmt_execute($roleData);
$roleData = mysqli_stmt_get_result($roleData);
$roleOptions = mysqli_fetch_all($roleData, MYSQLI_ASSOC);

$fullnameErr = $usernameErr = $passwordErr = $emailErr = $roleErr = "";
$fullname = $username = $email = $password = $role = NULL;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
    $fullname = isset($_POST['name']) ? cleanValue($_POST['name']) : NULL;
    $username = isset($_POST['username']) ? cleanValue($_POST['username']) : NULL;
    $email = isset($_POST['email']) ? cleanValue($_POST['email']) : NULL;
    $password = isset($_POST['password']) ? cleanValue($_POST['password']) : NULL;
    $role = isset($_POST['role_id']) ? cleanValue($_POST['role_id']) : null;

    if (empty($fullname)) {
      $fullnameErr = "Name is required.";
    } elseif (!preg_match("/^[A-Za-z._ ]*$/", $fullname) || strlen($fullname) < 3 || strlen($fullname) > 20) {
      $fullnameErr = "The name should consist of 3 to 20 characters of letters, quotes and periods are allowed.";
    }

    if (empty($username)) {
      $usernameErr = "Username is required.";
    } elseif (!preg_match("/^[A-Za-z._ ]*$/", $username) || strlen($username) < 3 || strlen($username) > 20) {
      $usernameErr = "Usernames should consist of 3 to 20 characters of letters, quotes, and periods.";
    }

    if (empty($email)) {
      $emailErr = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) < 6 || strlen($email) > 30) {
      $emailErr = "Invalid email. Must be between 6 to 30 characters long.";
    }

    if (empty($password)) {
      $passwordErr = 'Password is required.';
    } elseif (strlen($password) < 8) {
      $passwordErr = 'Passwords must be at least 8 characters long.';
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", $password)) {
      $passwordErr = 'Passwords must contain at least one lowercase letter, one uppercase letter, and one number.';
    } else {
      // Hash password 
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    if (empty($role)) {
      $roleErr = "Please select (role).";
    }


    $checkEmail = "SELECT email FROM users WHERE email='$email' LIMIT 1";
    $resulCheckEmail = mysqli_query($conn, $checkEmail);
    $emailSudahAda = mysqli_num_rows($resulCheckEmail) > 0;
    if ($emailSudahAda) {
      echo "<script>alert('Email has been used')</script>";
    }

    $checkUsername = "SELECT username FROM users WHERE username='$username' LIMIT 1";
    $resulCheckUsername = mysqli_query($conn, $checkUsername);
    $usernameSudahAda = mysqli_num_rows($resulCheckUsername) > 0;
    if ($usernameSudahAda) {
      echo "<script>alert('Username has been used')</script>";
    }

    if (empty($fullnameErr) && empty($usernameErr) && empty($emailErr) && empty($passwordErr) && empty($roleErr) && !$emailSudahAda && !$usernameSudahAda) {

      $queryInsert = "INSERT INTO users (name, username, email, password, role_id, created_by) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = mysqli_prepare($conn, $queryInsert);
      mysqli_stmt_bind_param(
        $stmt,
        "ssssss",
        $fullname,
        $username,
        $email,
        $hashedPassword,
        $role,
        $user_id
      );

      mysqli_stmt_execute($stmt);
      echo "<script>alert('Data added successfully');</script>";
      echo "<script>window.location.replace('userlist.php')</script>";
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
  <title>OLAMS - Add User</title>
</head>

<body>
  <div class="wrapper">
    <?php include "components/sidebar.inc.php"; ?>
    <div class="main">
      <?php include "components/navbar.inc.php"; ?>
      <main class="content">
        <div class="container-fluid p-0">
          <h1 class="h1 mb-3"><strong>Add User</strong></h1>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                </div>
                <div class="card-body">
                  <form action="<?= cleanValue($_SERVER['PHP_SELF']) ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="row">
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputFullname">Fullname</label>
                        <input type="text" class="form-control" name="name" id="inputFullnme" placeholder="Enter fullname" value="<?= $fullname; ?>">
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
                              <?php $selected = ($value['role_id'] == $role ? 'selected' : '') ? 'selected' : ''; ?>
                              <option value="<?= $value['role_id'] ?>" <?= $selected ?>><?= $value['name'] ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <span class="error" style="color: red;"> <?= $roleErr; ?> </span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST') : ?>
                          <button type="button" class="btn btn-primary">Submit</button>
                        <?php else : ?>
                          <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to add it?')">Submit</button>
                        <?php endif; ?>
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
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="sweetalert2.all.min.js"></script>
<script src="sweetalert2.min.js"></script>
  <?php include "script.inc.php"; ?>
</body>

</html>