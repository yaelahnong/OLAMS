<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";


if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$queryRole = "SELECT m_roles.role_id FROM users 
              LEFT JOIN m_roles ON users.role_id = m_roles.role_id
              WHERE users.user_id = ?";

$roleData = mysqli_prepare($conn, $queryRole);
mysqli_stmt_bind_param($roleData, "i", $user_id);
mysqli_stmt_execute($roleData);
$roleData = mysqli_stmt_get_result($roleData);
$userRole = mysqli_fetch_row($roleData)[0];


$limit = 5;
$halaman_aktif = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($halaman_aktif - 1) * $limit;

$selectedMonth = 'this_month';

if (isset($_GET['month'])) {
  $selectedMonth = $_GET['month'];
}

// Menghitung bulan saat ini dan bulan lalu berdasarkan bulan yang dipilih
if ($selectedMonth === 'this_month') {
  $currentMonth = date('Y-m');
  $lastMonth = date('Y-m', strtotime('-1 month'));
} elseif ($selectedMonth === 'last_month') {
  $currentMonth = date('Y-m', strtotime('-1 month'));
  $lastMonth = date('Y-m', strtotime('-2 months'));
}

// Query  untuk mengambil data pada bulan saat ini
$queryLeaveThisMonth = "SELECT COUNT(leaves_id) AS leaves_id FROM leaves
  WHERE DATE_FORMAT(start_date, '%Y-%m') = '$currentMonth'";
$result = $conn->query($queryLeaveThisMonth);
$row = $result->fetch_assoc();
$totalLeavesThisMonth = $row["leaves_id"];

$queryAttendanceThisMonth = "SELECT COUNT(attendance_id) AS attendance_id FROM attendances
  WHERE DATE_FORMAT(start_date, '%Y-%m') = '$currentMonth'";
$result = $conn->query($queryAttendanceThisMonth);
$row = $result->fetch_assoc();
$totalAttendanceThisMonth = $row["attendance_id"];

$queryOvertimeThisMonth = "SELECT COUNT(overtime_id) AS overtime_id FROM overtimes
  WHERE DATE_FORMAT(start_date, '%Y-%m') = '$currentMonth'";
$result = $conn->query($queryOvertimeThisMonth);
$row = $result->fetch_assoc();
$totalOvertimeThisMonth = $row["overtime_id"];

// Query untuk mengambil data selama sebulan terakhir
$queryLeaveLastMonth = "SELECT COUNT(leaves_id) AS leaves_id FROM leaves
  WHERE DATE_FORMAT(start_date, '%Y-%m') = '$lastMonth'";
$result = $conn->query($queryLeaveLastMonth);
$row = $result->fetch_assoc();
$totalLeavesLastMonth = $row["leaves_id"];

$queryAttendanceLastMonth = "SELECT COUNT(attendance_id) AS attendance_id FROM attendances
  WHERE DATE_FORMAT(start_date, '%Y-%m') = '$lastMonth'";
$result = $conn->query($queryAttendanceLastMonth);
$row = $result->fetch_assoc();
$totalAttendanceLastMonth = $row["attendance_id"];

$queryOvertimeLastMonth = "SELECT COUNT(overtime_id) AS overtime_id FROM overtimes
  WHERE DATE_FORMAT(start_date, '%Y-%m') = '$lastMonth'";
$result = $conn->query($queryOvertimeLastMonth);
$row = $result->fetch_assoc();
$totalOvertimeLastMonth = $row["overtime_id"];



$queryAttendance = "SELECT 
attendances.attendance_id, 
attendances.reason, 
attendances.type, 
attendances.start_date, 
attendances.finish_date,
m_divisions.division_name AS division_name,
users.name AS name
FROM attendances
LEFT JOIN users ON attendances.user_id = users.user_id
LEFT JOIN m_divisions ON attendances.division_id = m_divisions.division_id
";

// Dibawah ini adalah query untuk mengambil data di database
$queryLeave = "SELECT 
leaves.leaves_id, 
leaves.reason, 
leaves.category, 
leaves.start_date AS leaveStart, 
leaves.finish_date AS leaveFinish,
m_divisions.division_name AS division_name,
users.name AS name,
leaves.status
FROM leaves
LEFT JOIN users ON leaves.user_id = users.user_id
LEFT JOIN m_divisions ON leaves.division_id = m_divisions.division_id
";

$queryOvertime = "SELECT
overtimes.overtime_id,
overtimes.type,
overtimes.start_date AS overtimeStart,
overtimes.finish_date AS overtimeFinish,
overtimes.category AS categoryOvertime,
overtimes.reason AS reasonOvertime,
overtimes.status AS statusOvertime,
m_divisions.division_name AS division_name,
users.name AS name
FROM overtimes
LEFT JOIN users ON overtimes.user_id = users.user_id
LEFT JOIN m_divisions ON overtimes.divisi_id = m_divisions.division_id";


// query dibawah ini adalah untuk mengurutkan data yang terbaru
$queryAttendance .= " ORDER BY attendances.attendance_id DESC";
$queryLeave .= " ORDER BY leaves.leaves_id DESC";
$queryOvertime .= " ORDER BY overtimes.overtime_id DESC";

// dibawah ini adalah untuk memberikan maksimal 5 data yang ditampilkan
$jumlah_semua_data = mysqli_num_rows(mysqli_query($conn, $queryAttendance));
$queryAttendance .= " LIMIT $limit OFFSET $offset ";
$data = mysqli_query($conn, $queryAttendance);
$attendanceArray = mysqli_fetch_all($data, MYSQLI_ASSOC);

$jumlah_semua_data = mysqli_num_rows(mysqli_query($conn, $queryLeave));
$queryLeave .= " LIMIT $limit OFFSET $offset ";
$dateLeave = mysqli_query($conn, $queryLeave);
$leaveArray = mysqli_fetch_all($dateLeave, MYSQLI_ASSOC);

$jumlah_semua_data = mysqli_num_rows(mysqli_query($conn, $queryOvertime));
$queryOvertime .= " LIMIT $limit OFFSET $offset ";
$dataOvertime = mysqli_query($conn, $queryOvertime);
$overtimeArray = mysqli_fetch_all($dataOvertime, MYSQLI_ASSOC);



// dibawah ini adalah query untuk mengambil data karyawan yang paling banyak attendance, leaves, dan overtime
$queryTopLeaves = "SELECT users.name, COUNT(*) AS jumlah_cuti
                FROM leaves
                LEFT JOIN users ON leaves.user_id = users.user_id
                GROUP BY users.name
                ORDER BY jumlah_cuti DESC
                LIMIT 1";

$resultTopCuti = $conn->query($queryTopLeaves);
if ($resultTopCuti->num_rows > 0) {
  $rowTopCuti = $resultTopCuti->fetch_assoc();
  $namaKaryawanPalingBanyakCuti = $rowTopCuti["name"];
  $jumlahCutiPalingBanyak = $rowTopCuti["jumlah_cuti"];
} else {
  $namaKaryawanPalingBanyakCuti = "Tidak ada data cuti";
  $jumlahCutiPalingBanyak = 0;
}

$queryTopAttendance = "SELECT users.name, COUNT(*) AS jumlah_sakit
                    FROM attendances
                    LEFT JOIN users ON attendances.user_id = users.user_id
                    GROUP BY users.name
                    ORDER BY jumlah_sakit DESC
                    LIMIT 1";

$resultTopSakit = $conn->query($queryTopAttendance);
if ($resultTopSakit->num_rows > 0) {
  $rowTopSakit = $resultTopSakit->fetch_assoc();
  $namaKaryawanPalingBanyakSakit = $rowTopSakit["name"];
  $jumlahSakitPalingBanyak = $rowTopSakit["jumlah_sakit"];
} else {
  $namaKaryawanPalingBanyakSakit = "Tidak ada data sakit";
  $jumlahSakitPalingBanyak = 0;
}

$queryTopOvertime = "SELECT users.name, COUNT(*) AS jumlah_lembur
                  FROM overtimes
                  LEFT JOIN users ON overtimes.user_id = users.user_id
                  GROUP BY users.name
                  ORDER BY jumlah_lembur DESC
                  LIMIT 1";

$resultTopLembur = $conn->query($queryTopOvertime);
if ($resultTopLembur->num_rows > 0) {
  $rowTopLembur = $resultTopLembur->fetch_assoc();
  $namaKaryawanPalingBanyakLembur = $rowTopLembur["name"];
  $jumlahLemburPalingBanyak = $rowTopLembur["jumlah_lembur"];
} else {
  $namaKaryawanPalingBanyakLembur = "Tidak ada data lembur";
  $jumlahLemburPalingBanyak = 0;
}


// Tampilan Dashboard Untuk User
// Mengambil data cuti pengguna dari database
$queryLeaveUser = "SELECT start_date, finish_date FROM leaves WHERE user_id = ?";
$leaveData = mysqli_prepare($conn, $queryLeaveUser);
mysqli_stmt_bind_param($leaveData, "i", $user_id);
mysqli_stmt_execute($leaveData);
$leaveData = mysqli_stmt_get_result($leaveData);

$jumlahCutiTahunan = 12; // Jumlah cuti tahunan awal

$totalHariCutiDigunakan = 1;

// Iterasi melalui data cuti
while ($row = mysqli_fetch_assoc($leaveData)) {
  $start_date = new DateTime($row["start_date"]);
  $finish_date = new DateTime($row["finish_date"]);

  // Menghitung jumlah hari cuti dalam rentang tanggal
  $interval = $start_date->diff($finish_date);
  $jumlahHariCuti = $interval->days;

  // Tambahkan jumlah hari cuti ke totalHariCutiDigunakan
  $totalHariCutiDigunakan += $jumlahHariCuti;
}

// Menghitung sisa hari cuti
$sisaHariCuti = $jumlahCutiTahunan - $totalHariCutiDigunakan;

$queryLeaveUser = "SELECT COUNT(leaves_id) AS leaves_id FROM leaves WHERE user_id = ?";
$leaveData = mysqli_prepare($conn, $queryLeaveUser);
mysqli_stmt_bind_param($leaveData, "i", $user_id);
mysqli_stmt_execute($leaveData);
$leaveData = mysqli_stmt_get_result($leaveData);
$row = mysqli_fetch_assoc($leaveData);
$totalLeavesUser = $row["leaves_id"];


$queryAttendanceUser = "SELECT COUNT(attendance_id) AS attendance_id FROM attendances WHERE user_id = ?";
$attendanceData = mysqli_prepare($conn, $queryAttendanceUser);
mysqli_stmt_bind_param($attendanceData, "i", $user_id);
mysqli_stmt_execute($attendanceData);
$attendanceData = mysqli_stmt_get_result($attendanceData);
$row = mysqli_fetch_assoc($attendanceData);
$totalAttendanceUser = $row["attendance_id"];

$queryOvertimeUser = "SELECT COUNT(overtime_id) AS overtime_id FROM overtimes WHERE user_id = ?";
$overtimeData = mysqli_prepare($conn, $queryOvertimeUser);
mysqli_stmt_bind_param($overtimeData, "i", $user_id);
mysqli_stmt_execute($overtimeData);
$overtimeData = mysqli_stmt_get_result($overtimeData);
$row = mysqli_fetch_assoc($overtimeData);
$totalOvertimeUser = $row["overtime_id"];


$user_id = $_SESSION['user_id'];

// Query untuk mengambil data attendance milik user
$queryAttendanceUser = "SELECT 
    attendances.attendance_id, 
    attendances.reason, 
    attendances.type, 
    attendances.start_date, 
    attendances.finish_date,
    m_divisions.division_name AS division_name,
    users.name AS name
    FROM attendances
    LEFT JOIN users ON attendances.user_id = users.user_id
    LEFT JOIN m_divisions ON attendances.division_id = m_divisions.division_id
    WHERE attendances.user_id = $user_id
    ORDER BY attendances.attendance_id DESC
    LIMIT $limit OFFSET $offset";

// Query untuk mengambil data leaves milik user
$queryLeaveUser = "SELECT 
    leaves.leaves_id, 
    leaves.reason, 
    leaves.category, 
    leaves.start_date AS leaveStart, 
    leaves.finish_date AS leaveFinish,
    m_divisions.division_name AS division_name,
    users.name AS name,
    leaves.status
    FROM leaves
    LEFT JOIN users ON leaves.user_id = users.user_id
    LEFT JOIN m_divisions ON leaves.division_id = m_divisions.division_id
    WHERE leaves.user_id = $user_id
    ORDER BY leaves.leaves_id DESC
    LIMIT $limit OFFSET $offset";

// Query untuk mengambil data overtime milik user
$queryOvertimeUser = "SELECT
    overtimes.overtime_id,
    overtimes.type,
    overtimes.start_date AS overtimeStart,
    overtimes.finish_date AS overtimeFinish,
    overtimes.category AS categoryOvertime,
    overtimes.reason AS reasonOvertime,
    overtimes.status AS statusOvertime,
    m_divisions.division_name AS division_name,
    users.name AS name
    FROM overtimes
    LEFT JOIN users ON overtimes.user_id = users.user_id
    LEFT JOIN m_divisions ON overtimes.divisi_id = m_divisions.division_id
    WHERE overtimes.user_id = $user_id
    ORDER BY overtimes.overtime_id DESC
    LIMIT $limit OFFSET $offset";


$dataAttendanceUser = mysqli_query($conn, $queryAttendanceUser);
$attendanceArrayUser = mysqli_fetch_all($dataAttendanceUser, MYSQLI_ASSOC);

$dataLeaveUser = mysqli_query($conn, $queryLeaveUser);
$leaveArrayUser = mysqli_fetch_all($dataLeaveUser, MYSQLI_ASSOC);

$dataOvertimeUser = mysqli_query($conn, $queryOvertimeUser);
$overtimeArrayUser = mysqli_fetch_all($dataOvertimeUser, MYSQLI_ASSOC);



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.inc.php"; ?>
  <title>OLAMS - Dashboard</title>
</head>

<body>
  <div class="wrapper">
    <?php include "components/sidebar.inc.php"; ?>
    <div class="main">
      <?php include "components/navbar.inc.php"; ?>
      <main class="content">
        <?php if ($userRole === 2 || $userRole === 3 || $userRole === 4) : ?>
            <div class="container-fluid p-0">
              <h1 class="h1 mb-3 judul_halaman"><strong>Dashboard</strong></h1>
              <div class="row">
                <div class="col-md-3">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">Filter Laporan</h5>
                      <form action="<?= cleanValue($_SERVER['PHP_SELF']) ?>" method="get">
                        <div class="form-group row">
                          <div class="col-12">
                            <label for="monthSelect">Pilih Bulan:</label>
                            <select id="monthSelect" name="month" class="form-select">
                              <option value="this_month" <?= ($selectedMonth === 'this_month') ? 'selected' : '' ?>>Bulan Ini</option>
                              <option value="last_month" <?= ($selectedMonth === 'last_month') ? 'selected' : '' ?>>Bulan Kemarin</option>
                            </select>
                          </div>
                          <div class="col mt-4">
                            <button type="submit" class="btn btn-primary float-right">Tampilkan</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card ml-auto">
                    <div class="card-body">
                      <div class="row">
                        <div class="col mt-0">
                          <h5 class="card-title text-center">Overtime</h5>
                        </div>
                      </div>
                      <h1 class="mt-1 mb-3 text-center"><?= $totalOvertimeThisMonth ?></h1>
                      <div class="mb-0 text-center">
                        <span class="badge badge-primary-light"> <i class="mdi mdi-arrow-bottom-right"></i></span>
                        <span class="text-muted">Total Overtime This Month</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card ml-auto">
                    <div class="card-body">
                      <div class="row">
                        <div class="col mt-0 text-center">
                          <h5 class="card-title">Leave</h5>
                        </div>
                      </div>
                      <h1 class="mt-1 mb-3 text-center"><?= $totalLeavesThisMonth ?></h1>
                      <div class="mb-0 text-center">
                        <span class="badge badge-primary-light"> <i class="mdi mdi-arrow-bottom-right"></i></span>
                        <span class="text-muted">Total Leave This Month</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card ml-auto">
                    <div class="card-body">
                      <div class="row">
                        <div class="col mt-0 text-center">
                          <h5 class="card-title">Attendance</h5>
                        </div>
                      </div>
                      <h1 class="mt-1 mb-3 text-center"><?= $totalAttendanceThisMonth ?></h1>
                      <div class="mb-0 text-center">
                        <span class="badge badge-primary-light"> <i class="mdi mdi-arrow-bottom-right"></i> </span>
                        <span class="text-muted">Total Attendance This Month</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12 d-flex flex-row">
                <div class="col-sm-4 pe-3">
                  <div class="card bg-secondary">
                    <div class="card-body">
                      <div class="row">
                        <div class="col mt-0">
                          <h5 class="card-title text-white">Employees Who Leave the Most</h5>
                        </div>

                        <div class="col-auto">
                          <div class="stat text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users align-middle">
                              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                              <circle cx="9" cy="7" r="4"></circle>
                              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                          </div>
                        </div>
                      </div>
                      <h1 class="mt-1 mb-3 text-white"><?= $namaKaryawanPalingBanyakCuti ?></h1>
                      <div class="mb-0">
                        <span class="text-white"> <i class="mdi mdi-arrow-bottom-right"></i> <?= $jumlahCutiPalingBanyak ?> </span>
                        <span class="text-white">Total Leave</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4 px-3">
                  <div class="card bg-secondary">
                    <div class="card-body">
                      <div class="row">
                        <div class="col mt-0">
                          <h5 class="card-title text-white">Employees who get sick the most</h5>
                        </div>

                        <div class="col-auto">
                          <div class="stat text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users align-middle">
                              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                              <circle cx="9" cy="7" r="4"></circle>
                              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                          </div>
                        </div>
                      </div>
                      <h1 class="mt-1 mb-3 text-white"><?= $namaKaryawanPalingBanyakSakit; ?></h1>
                      <div class="mb-0">
                        <span class="text-white"> <i class="mdi mdi-arrow-bottom-right"></i> <?= $jumlahSakitPalingBanyak; ?> </span>
                        <span class="text-white">Total Attendance</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4 ps-3">
                  <div class="card bg-secondary">
                    <div class="card-body">
                      <div class="row">
                        <div class="col mt-0">
                          <h5 class="card-title text-white">Employees Who Work the Most Overtime</h5>
                        </div>

                        <div class="col-auto">
                          <div class="stat text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users align-middle">
                              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                              <circle cx="9" cy="7" r="4"></circle>
                              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                          </div>
                        </div>
                      </div>
                      <h1 class="mt-1 mb-3 text-white"><?= $namaKaryawanPalingBanyakLembur ?></h1>
                      <div class="mb-0">
                        <span class="text-white"> <i class="mdi mdi-arrow-bottom-right"></i> <?= $jumlahLemburPalingBanyak ?></span>
                        <span class="text-white">Total Overtime</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="content px-0">
                <div class="container-fluid p-0">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h1 class="h1 mb-3 judul_halaman"><strong>Attendance List</strong></h1>
                        </div>
                        <div class="table-responsive">
                          <table class="table mb-0 mt-3">
                            <thead>
                              <tr>
                                <th scope="col">No</th>
                                <th scope="col">Full Name</th>
                                <th scope="col">Division</th>
                                <th scope="col">Reason</th>
                                <th scope="col">Type</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">Finish Date</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (count($attendanceArray) > 0) : ?>
                                <?php foreach ($attendanceArray as $key => $value) : ?>
                                  <tr>
                                    <td><?= $key + 1 + $offset ?></td>
                                    <td><?= $value['name'] ?></td>
                                    <td><?= $value['division_name'] ?></td>
                                    <td>
                                      <?php if (!empty($value['reason'])) {
                                        echo $value['reason'];
                                      } else {
                                        echo "-";
                                      }
                                      ?>
                                    </td>
                                    <td><?= $value['type'] ?></td>
                                    <td><?= date('d-M-Y H:i', strtotime($value['start_date'])) ?></td>
                                    <td><?= date('d-M-Y H:i', strtotime($value['finish_date'])) ?></td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php else : ?>
                                <tr>
                                  <td colspan="7" style="text-align: center;">No records found!!!</td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="">
                <div class="container-fluid p-0">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h1 class="h1 mb-3 judul_halaman"><strong>Leave List</strong></h1>
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
                                <th scope="col">Status</th>
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
                                    <td><?= date('d-M-Y H:i', strtotime($value['leaveStart'])) ?></td>
                                    <td><?= date('d-M-Y H:i', strtotime($value['leaveFinish'])) ?></td>
                                    <td>
                                      <?php

                                      if ($value['status'] === 'Pending') {
                                        $statusClass = 'btn-warning'; // Status "pending"
                                      } elseif ($value['status'] === 'Reject') {
                                        $statusClass = 'btn-danger'; // Status "reject"
                                      } elseif ($value['status'] === 'Approved') {
                                        $statusClass = 'btn-success'; // Status "approved"
                                      }

                                      ?>
                                      <button class="btn btn-sm <?= $statusClass; ?>" disabled>
                                        <?= $value['status'] ?>
                                      </button>
                                    </td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php else : ?>
                                <tr>
                                  <td colspan="8" style="text-align: center;">No records found!!!</td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="">
                <div class="container-fluid p-0">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h1 class="h1 mb-3 judul_halaman"><strong>Overtime List</strong></h1>
                        </div>
                        <div class="table-responsive">
                          <table class="table mb-0 mt-3">
                            <thead>
                              <tr>
                                <th scope="col">No</th>
                                <th scope="col">Full Name</th>
                                <th scope="col">Division</th>
                                <th scope="col">Type Overtime</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">Finish Date</th>
                                <th scope="col">Category</th>
                                <th scope="col">Reason</th>
                                <th scope="col">Status</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (count($overtimeArray) > 0) : ?>
                                <?php foreach ($overtimeArray as $key => $value) : ?>
                                  <tr>
                                    <td><?= $key + 1 + $offset ?></td>
                                    <td><?= $value['name'] ?></td>
                                    <td><?= $value['division_name'] ?></td>
                                    <td><?= $value['type'] ?></td>
                                    <td><?= date('d-M-Y H:i', strtotime($value['overtimeStart'])) ?></td>
                                    <td>
                                      <?php if (!empty($value['overtimeFinish'])) {
                                        echo date('d-M-Y H:i', strtotime($value['overtimeFinish']));
                                      } else {
                                        echo "-";
                                      }
                                      ?>
                                    </td>
                                    <td><?= $value['categoryOvertime'] ?></td>
                                    <td><?= $value['reasonOvertime'] ?></td>
                                    <td>
                                      <?php

                                      if ($value['statusOvertime'] === 'Pending') {
                                        $statusClass = 'btn-warning'; // Status "pending"
                                      } elseif ($value['statusOvertime'] === 'Reject') {
                                        $statusClass = 'btn-danger'; // Status "reject"
                                      } elseif ($value['statusOvertime'] === 'Approved') {
                                        $statusClass = 'btn-success'; // Status "approved"
                                      }

                                      ?>
                                      <button class="btn btn-sm <?= $statusClass ?>" disabled>
                                        <?= $value['statusOvertime'] ?>
                                      </button>
                                    </td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php else : ?>
                                <tr>
                                  <td colspan="8" style="text-align: center;">No records found!!!</td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        <?php elseif ($userRole === 1) : ?>
          <!-- Tampilan untuk User -->
            <div class="container-fluid p-0">
              <h1 class="h1 mb-3 judul_halaman"><strong>Dashboard</strong></h1>
              <div class="row">
                <div class="col-md-3">
                  <div class="card ml-auto">
                    <div class="card-body">
                      <div class="row">
                        <div class="col mt-0">
                          <h5 class="card-title text-center">Remaining Leave</h5>
                        </div>
                      </div>
                      <h1 class="mt-1 mb-3 text-center"> <?= $sisaHariCuti; ?> Days </h1>
                      <div class="mb-0 text-center">
                        <span class="badge badge-primary-light"> <i class="mdi mdi-arrow-bottom-right"></i></span>
                        <span class="text-muted">leave this year</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card ml-auto">
                    <div class="card-body">
                      <div class="row">
                        <div class="col mt-0">
                          <h5 class="card-title text-center">Overtime</h5>
                        </div>
                      </div>
                      <h1 class="mt-1 mb-3 text-center"><?= $totalOvertimeUser ?></h1>
                      <div class="mb-0 text-center">
                        <span class="badge badge-primary-light"> <i class="mdi mdi-arrow-bottom-right"></i></span>
                        <span class="text-muted">Total Overtime This Month</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card ml-auto">
                    <div class="card-body">
                      <div class="row">
                        <div class="col mt-0 text-center">
                          <h5 class="card-title">Leave</h5>
                        </div>
                      </div>
                      <h1 class="mt-1 mb-3 text-center"><?= $totalLeavesUser ?></h1>
                      <div class="mb-0 text-center">
                        <span class="badge badge-primary-light"> <i class="mdi mdi-arrow-bottom-right"></i></span>
                        <span class="text-muted">Total Leave This Month</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card ml-auto">
                    <div class="card-body">
                      <div class="row">
                        <div class="col mt-0 text-center">
                          <h5 class="card-title">Attendance</h5>
                        </div>
                      </div>
                      <h1 class="mt-1 mb-3 text-center"><?= $totalAttendanceUser ?></h1>
                      <div class="mb-0 text-center">
                        <span class="badge badge-primary-light"> <i class="mdi mdi-arrow-bottom-right"></i> </span>
                        <span class="text-muted">Total Attendance This Month</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
                <div class="container-fluid p-0">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h1 class="h1 mb-3 judul_halaman"><strong>Attendance List</strong></h1>
                        </div>
                        <div class="table-responsive">
                          <table class="table mb-0 mt-3">
                            <thead>
                              <tr>
                                <th scope="col">No</th>
                                <th scope="col">Full Name</th>
                                <th scope="col">Division</th>
                                <th scope="col">Reason</th>
                                <th scope="col">Type</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">Finish Date</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (count($attendanceArrayUser) > 0) : ?>
                                <?php foreach ($attendanceArrayUser as $key => $value) : ?>
                                  <tr>
                                    <td><?= $key + 1 + $offset ?></td>
                                    <td><?= $value['name'] ?></td>
                                    <td><?= $value['division_name'] ?></td>
                                    <td><?= $value['reason'] ?></td>
                                    <td><?= $value['type'] ?></td>
                                    <td><?= date('d-M-Y H:i', strtotime($value['start_date'])) ?></td>
                                    <td><?= date('d-M-Y H:i', strtotime($value['finish_date'])) ?></td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php else : ?>
                                <tr>
                                  <td colspan="7" style="text-align: center;">No records found!!!</td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="container-fluid p-0">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h1 class="h1 mb-3 judul_halaman"><strong>Leave List</strong></h1>
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
                                <th scope="col">Status</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (count($leaveArrayUser) > 0) : ?>
                                <?php foreach ($leaveArrayUser as $key => $value) : ?>
                                  <tr>
                                    <td><?= $key + 1 + $offset ?></td>
                                    <td><?= $value['name'] ?></td>
                                    <td><?= $value['division_name'] ?></td>
                                    <td><?= $value['reason'] ?></td>
                                    <td><?= $value['category'] ?></td>
                                    <td><?= date('d-M-Y H:i', strtotime($value['leaveStart'])) ?></td>
                                    <td><?= date('d-M-Y H:i', strtotime($value['leaveFinish'])) ?></td>
                                    <td>
                                      <?php

                                      if ($value['status'] === 'Pending') {
                                        $statusClass = 'btn-warning'; // Status "pending"
                                      } elseif ($value['status'] === 'Reject') {
                                        $statusClass = 'btn-danger'; // Status "reject"
                                      } elseif ($value['status'] === 'Approved') {
                                        $statusClass = 'btn-success'; // Status "approved"
                                      }

                                      ?>
                                      <button class="btn btn-sm <?= $statusClass; ?>" disabled>
                                        <?= $value['status'] ?>
                                      </button>
                                    </td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php else : ?>
                                <tr>
                                  <td colspan="8" style="text-align: center;">No records found!!!</td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="container-fluid p-0">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h1 class="h1 mb-3 judul_halaman"><strong>Overtime List</strong></h1>
                        </div>
                        <div class="table-responsive">
                          <table class="table mb-0 mt-3">
                            <thead>
                              <tr>
                                <th scope="col">No</th>
                                <th scope="col">Full Name</th>
                                <th scope="col">Division</th>
                                <th scope="col">Type Overtime</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">Finish Date</th>
                                <th scope="col">Category</th>
                                <th scope="col">Reason</th>
                                <th scope="col">Status</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (count($overtimeArrayUser) > 0) : ?>
                                <?php foreach ($overtimeArrayUser as $key => $value) : ?>
                                  <tr>
                                    <td><?= $key + 1 + $offset ?></td>
                                    <td><?= $value['name'] ?></td>
                                    <td><?= $value['division_name'] ?></td>
                                    <td><?= $value['type'] ?></td>
                                    <td><?= date('d-M-Y H:i', strtotime($value['overtimeStart'])) ?></td>
                                    <td><?= date('d-M-Y H:i', strtotime($value['overtimeFinish'])) ?></td>
                                    <td><?= $value['categoryOvertime'] ?></td>
                                    <td><?= $value['reasonOvertime'] ?></td>
                                    <td>
                                      <?php

                                      if ($value['statusOvertime'] === 'Pending') {
                                        $statusClass = 'btn-warning'; // Status "pending"
                                      } elseif ($value['statusOvertime'] === 'Reject') {
                                        $statusClass = 'btn-danger'; // Status "reject"
                                      } elseif ($value['statusOvertime'] === 'Approved') {
                                        $statusClass = 'btn-success'; // Status "approved"
                                      }

                                      ?>
                                      <button class="btn btn-sm <?= $statusClass ?>" disabled>
                                        <?= $value['statusOvertime'] ?>
                                      </button>
                                    </td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php else : ?>
                                <tr>
                                  <td colspan="8" style="text-align: center;">No records found!!!</td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        <?php else : ?>
          <div class="col-md-12">
            <div class="alert alert-warning">
              Akses Ditolak: Anda tidak memiliki izin untuk melihat konten ini.
            </div>
          </div>
        <?php endif; ?>
      </main>
      <?php include "components/footer.inc.php"; ?>
    </div>
  </div>
  <?php include "script.inc.php"; ?>
</body>

</html>