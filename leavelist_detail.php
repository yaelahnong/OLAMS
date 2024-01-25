<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$show_leave_query = "SELECT 
    users.name AS name,
    m_divisions.division_name AS division_name,
    leaves.reason AS reason,
    leaves.category AS category,
    leaves.start_date AS start_date,
    leaves.finish_date AS finish_date,
    leaves.sent_by_admin AS sent_by_admin,
    leaves.status_updated_at AS status_updated_at,
    leaves.status AS status
FROM leaves
LEFT JOIN users ON leaves.user_id = users.user_id
LEFT JOIN m_divisions ON leaves.division_id = m_divisions.division_id
WHERE leaves.leaves_id = ?";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $leave_id = $_GET['id'];

    $show_leave_statement = mysqli_prepare($conn, $show_leave_query);
    mysqli_stmt_bind_param($show_leave_statement, "i", $leave_id);
    mysqli_stmt_execute($show_leave_statement);
    $leaveData = mysqli_stmt_get_result($show_leave_statement);
    $leaveDetails = mysqli_fetch_assoc($leaveData);
} else {
    echo "Invalid leave ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.inc.php"; ?>
    <title>OLAMS - Leave Detail</title>

    <!-- Tambahkan link untuk HTML2PDF dan DataTables CSS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.0/html2pdf.bundle.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3"><strong>Leave Detail</strong></h1>
                    <div class="card">
                        <div class="card-body">
                            <!-- Add the table for leave details similar to the report page -->
                            <table class="table" id="leaveDetailsTable">
                                <thead>
                                    <tr>
                                        <th>Full Name</th>
                                        <th>Division</th>
                                        <th>Reason</th>
                                        <th>Category</th>
                                        <th>Start Date</th>
                                        <th>Finish Date</th>
                                        <th>Sent by Admin</th>
                                        <th>Status Updated At</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= $leaveDetails['name'] ?></td>
                                        <td><?= $leaveDetails['division_name'] ?></td>
                                        <td><?= $leaveDetails['reason'] ?></td>
                                        <td><?= $leaveDetails['category'] ?></td>
                                        <td><?= date('d-M-Y H:i', strtotime($leaveDetails['start_date'])) ?></td>
                                        <td><?= date('d-M-Y H:i', strtotime($leaveDetails['finish_date'])) ?></td>
                                        <td><?= $leaveDetails['sent_by_admin'] ? date('d-M-Y H:i', strtotime($leaveDetails['sent_by_admin'])) : '-' ?></td>
                                        <td><?= $leaveDetails['status_updated_at'] ? date('d-M-Y H:i', strtotime($leaveDetails['status_updated_at'])) : '-' ?></td>
                                        <td><?= $leaveDetails['status'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- Tambahkan tombol Print dan Export PDF -->
                            <div class="mt-3">
                                <button class="btn btn-primary btn-sm" onclick="window.print()">Print</button>
                                <button class="btn btn-danger btn-sm ms-2" onclick="exportPDF()">Export PDF</button>
                            </div>

                            <a href="leavelist.php" class="btn btn-warning btn-sm ms-2">Back</a>
                        </div>
                    </div>
                </div>
            </main>
            <?php include "components/footer.inc.php"; ?>
        </div>
    </div>

    <?php include "script.inc.php"; ?>

    <!-- Tambahkan script untuk DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        // Function untuk export PDF
        function exportPDF() {
            var element = document.body; // Element yang akan di-export ke PDF
            html2pdf(element);
        } 

        // Inisialisasi DataTable pada table leaveDetailsTable
        $(document).ready(function() {
            $('#leaveDetailsTable').DataTable();
        });
    </script>
</body>

</html>
