<?php
session_start();
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


$queryUsers = "SELECT user_id, name FROM users";
$stmt = mysqli_prepare($conn, $queryUsers);

if ($stmt) {
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $user_id, $name);

  while (mysqli_stmt_fetch($stmt)) {
    $users[] = array("user_id" => $user_id, "name" => $name);
  }

  mysqli_stmt_close($stmt);
} else {
  die("Failed to create a prepared statement: " . mysqli_error($conn));
}

// Query SQL untuk mengambil daftar pengguna yang belum ada dalam m_basic_salaries
$queryUsers = "
  SELECT users.user_id, users.name, users.role_id
  FROM users
  LEFT JOIN m_basic_salaries ON users.user_id = m_basic_salaries.user_id
  WHERE users.role_id = 1 AND m_basic_salaries.user_id IS NULL
";
$stmt = mysqli_prepare($conn, $queryUsers);

if ($stmt) {
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $user_id, $name, $user_role_id);

  while (mysqli_stmt_fetch($stmt)) {
    $users[] = array("user_id" => $user_id, "name" => $name, "role_id" => $user_role_id);
  }

  mysqli_stmt_close($stmt);
} else {
  die("Failed to create a prepared statement: " . mysqli_error($conn));
}




if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
    if (isset($_POST["user_id"]) && isset($_POST["total_basic_salary"]) && !empty($_POST["user_id"]) && !empty($_POST["total_basic_salary"])) {
      $user_id = cleanValue($_POST["user_id"]);
      $total_basic_salary = cleanValue(str_replace(',', '', $_POST["total_basic_salary"]));

      // Validasi input total_basic_salary harus berisi angka
      if (empty($total_basic_salary)) {
        // Memformat angka sesuai dengan pemisah ribuan yang digunakan di negara Anda
        $formatted_total_basic_salary = number_format($total_basic_salary);
      } else {
        $error = "Total basic salary must be a numeric value.";
      }

        // Validasi apakah nama sudah ada di database
        $queryCheckName = "SELECT COUNT(*) FROM m_basic_salaries WHERE user_id = ?";
        $stmtCheckName = mysqli_prepare($conn, $queryCheckName);

        if ($stmtCheckName) {
          mysqli_stmt_bind_param($stmtCheckName, "i", $user_id);
          mysqli_stmt_execute($stmtCheckName);
          mysqli_stmt_bind_result($stmtCheckName, $count);

          if (mysqli_stmt_fetch($stmtCheckName)) {
            if ($count > 0) {
              $error = "User with the selected name already exists in the database.";
            } else {
              // Close the previous result and statement
              mysqli_stmt_close($stmtCheckName);

              // Insert data ke database with the created_by information
              $insertQuery = "INSERT INTO m_basic_salaries (user_id, total_basic_salary, created_by) VALUES (?, ?, ?)";
              $stmt = mysqli_prepare($conn, $insertQuery);

              if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iis", $user_id, $total_basic_salary, $user_id);

                if (mysqli_stmt_execute($stmt)) {
                  echo "<script>alert('Basic salary data added successfully.')</script>";
                  echo "<script>window.location.href = 'basicsalary.php'</script>";
                  exit();
                } else {
                  $error = "Failed to save the data.";
                }

                mysqli_stmt_close($stmt);
              } else {
                $error = "Failed to create a prepared statement.";
              }
            }
          } else {
            $error = "Failed to check the name in the database.";
          }
        } else {
          $error = "Failed to create a prepared statement to check the name.";
        }
    } else {
      $error = "User ID and Total Basic Salary are required fields.";
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
  <title>OLAMS - Add Basic Salary</title>
</head>

<body>
  <div class="wrapper">
    <?php include "components/sidebar.inc.php"; ?>
    <div class="main">
      <?php include "components/navbar.inc.php"; ?>
      <main class="content">
        <div class="container-fluid p-0">
          <h1 class="h1 mb-3"><strong>Add Basic Salary</strong></h1>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                </div>
                <div class="card-body">
                  <?php if (isset($error)) { ?>
                    <div class="alert alert-danger alert-dismissible p-3 rounded" role="alert">
                      <div class="alert-message">
                        <?php echo $error; ?>
                      </div>
                      <button type="button" class="btn-close align-items-end" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php } ?>
                  <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="row">
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputUserId">User</label>
                        <span style="color: red">*</span><br>
                        <select class="form-select" name="user_id" id="inputUserId">
                          <?php if ($count == 0) : ?>
                            <?php foreach ($users as $user) : ?>
                              <?php if ($user["role_id"] == 1) : ?>
                                <option value="<?= $user['user_id']; ?>"><?= $user['name']; ?></option>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputTotalBasicSalary">Total Basic Salary</label>
                        <span style="color: red">*</span><br>
                        <input type="text" class="form-control" name="total_basic_salary" id="inputTotalBasicSalary" placeholder="Enter Total Basic Salary">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="col"><?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$error) : ?>
                            <button type="button" name="submit" class="btn btn-primary">Submit</button>
                          <?php else : ?>
                            <button type="submit" name="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to add it?')">Submit</button>
                          <?php endif; ?>
                          <a href="basicsalary.php" class="btn btn-light text-dark text-decoration-none">Cancel</a>
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
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      let inputTotalBasicSalary = document.getElementById("inputTotalBasicSalary");

      inputTotalBasicSalary.addEventListener("input", function(e) {
        let rawValue = e.target.value.replace(/\D/g, "");

        let formattedValue = addCommas(rawValue);

        e.target.value = formattedValue;
      });
    });

    function addCommas(value) {
      let number = parseFloat(value);
      if (isNaN(number)) {
        return "";
      }

      let formattedValue = number.toLocaleString();

      return formattedValue;
    }
  </script>
  <?php include "script.inc.php"; ?>
</body>

</html>