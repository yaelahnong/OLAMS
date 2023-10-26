<?php
session_start();
include __DIR__ . "/include/conn.inc.php";

// Periksa apakah pengguna sudah masuk atau belum
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Pastikan duty_overtime_id disediakan melalui GET request atau sesuai kebutuhan Anda
if (isset($_GET["id"])) {
    $duty_overtime_id = $_GET["id"];

    // Buat query untuk mengambil data detail
    $detailQuery = "SELECT
        duty_overtimes.duty_overtime_id,
        users.name AS name,
        m_projects.project_name AS project_name, 
        m_divisions.division_name AS division_name, 
        duty_overtimes.lead_count, 
        duty_overtimes.customer_count, 
        duty_overtimes.note,
        duty_overtimes.status
    FROM duty_overtimes
    LEFT JOIN users ON duty_overtimes.user_id = users.user_id
    LEFT JOIN m_projects ON duty_overtimes.project_id = m_projects.project_id
    LEFT JOIN m_divisions ON duty_overtimes.division_id = m_divisions.division_id
    WHERE duty_overtimes.duty_overtime_id = ?";

    $detailStatement = mysqli_prepare($conn, $detailQuery);
    mysqli_stmt_bind_param($detailStatement, "i", $duty_overtime_id);
    mysqli_stmt_execute($detailStatement);
    $detailData = mysqli_stmt_get_result($detailStatement);
    $detail = mysqli_fetch_assoc($detailData);

    if ($detail['customer_count'] == 0) {
        $effective_time = '5 hours';
    } else {
        $effective_time = '8 hours';
    }
} else {
    // Handle kesalahan jika duty_overtime_id tidak disediakan
    echo "Duty Overtime ID tidak ditemukan.";
    exit();
}

// Sekarang Anda dapat menampilkan data detail pada halaman HTML
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.inc.php"; ?>
    <title>OLAMS - Detail Duty Overtime</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3 judul_halaman"><strong>Detail Duty Overtime</strong></h1>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <td><strong>Nama</strong></td>
                                            <td><?= $detail['name'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Project</strong></td>
                                            <td><?= $detail['project_name'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Division</strong></td>
                                            <td><?= $detail['division_name'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Lead Count</strong></td>
                                            <td><?= $detail['lead_count'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Customer Count</strong></td>
                                            <td><?= $detail['customer_count'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>status</strong></td>
                                            <td><?= $detail['status'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Note</strong></td>
                                            <td><?= empty($detail['note']) ? '-' : $detail['note'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Effective time</strong></td>
                                            <td><?= $effective_time ?></td>
                                        </tr>
                                    </table>
                                    <a href="duty_overtimelist.php" class="btn btn-warning btn-sm ms-2">Kembali</a>
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