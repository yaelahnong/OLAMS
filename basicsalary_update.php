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

$formatted_total_basic_salary = ""; // Inisialisasi variabel

// Ambil data pengguna dari database
$queryUsers = "SELECT user_id, name, role_id FROM users WHERE role_id = 1"; // Sesuaikan dengan nama tabel dan kolom yang sesuai
$resultUsers = mysqli_query($conn, $queryUsers);

$users = array();
while ($row = mysqli_fetch_assoc($resultUsers)) {
  $users[] = $row;
}



mysqli_free_result($resultUsers);

// Mendapatkan data gaji dasar yang akan diupdate
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"])) {
  $id = cleanValue($_GET["id"]);
  $query = "SELECT user_id, total_basic_salary FROM m_basic_salaries WHERE basic_salary_id = ?";
  $stmt = mysqli_prepare($conn, $query);

  if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id, $total_basic_salary);

    if (mysqli_stmt_fetch($stmt)) {
      $formatted_total_basic_salary = $total_basic_salary;
      // Data ditemukan, kita bisa menampilkan form untuk mengupdate gaji
      $formatted_total_basic_salary = number_format($total_basic_salary); // Format angka

    } else {
      echo "Data not found."; // Tampilkan pesan jika data tidak ditemukan
      exit;
    }

    mysqli_stmt_close($stmt);
  } else {
    die("Failed to create a prepared statement: " . mysqli_error($conn));
  }
}

// Menyimpan data yang diupdate
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
    if (isset($_POST["id"]) && isset($_POST["total_basic_salary"]) && !empty($_POST["id"]) && !empty($_POST["total_basic_salary"])) {
      $id = cleanValue($_POST["id"]);
      $total_basic_salary = cleanValue(str_replace(',', '', $_POST["total_basic_salary"]));

      // Validasi input total_basic_salary harus berisi angka
      if (is_numeric($total_basic_salary)) {
        $updateQuery = "UPDATE m_basic_salaries SET total_basic_salary = ?, update_by = ? WHERE basic_salary_id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);

        if ($stmt) {
          mysqli_stmt_bind_param($stmt, "dii", $total_basic_salary, $user_id, $id);

          if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Basic salary data updated successfully.')</script>";
            echo "<script>window.location.href = 'basicsalary.php'</script>";
            exit();
          } else {
            $error = "Failed to update the data.";
          }

          mysqli_stmt_close($stmt);
        } else {
          $error = "Failed to create a prepared statement.";
        }
      } else {
        $error = "Total basic salary must be a numeric value.";
      }
    } else {
      $error = "ID and Total Basic Salary are required fields.";
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
  <title>OLAMS - Update Basic Salary</title>
</head>

<body>
  <div class="wrapper">
    <?php include "components/sidebar.inc.php"; ?>
    <div class="main">
      <?php include "components/navbar.inc.php"; ?>
      <main class="content">
        <div class="container-fluid p-0">
          <h1 class="h1 mb-3"><strong>Update Basic Salary</strong></h1>
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
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <div class="row">
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputUserId">User</label>
                        <span style="color: red">*</span><br>
                        <select class="form-select" name="user_id" id="inputUserId" disabled>
                          <?php foreach ($users as $user) : ?>
                            <?php if ($user["role_id"] == 1) : ?>
                              <option value="<?= $user['user_id']; ?>" <?= ($user['user_id'] == $user_id) ? "selected" : "" ?>>
                                <?= $user['name']; ?>
                              </option>
                            <?php endif; ?>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputTotalBasicSalary">Total Basic Salary</label>
                        <span style="color: red">*</span><br>
                        <input type="text" class="form-control" name="total_basic_salary" id="inputTotalBasicSalary" placeholder="Enter Total Basic Salary" value="<?= $formatted_total_basic_salary ?>">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST') : ?>
                          <button type="button" name="submit" class="btn btn-primary">Update</button>
                        <?php else : ?>
                          <button type="submit" class="btn btn-primary" onclick="return confirm('are you sure you will update?')">Update</button>
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