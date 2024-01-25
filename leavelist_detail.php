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

    <!-- Tambahkan link untuk HTML2PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.0/html2pdf.bundle.js"></script>
    <!-- Tambahkan link untuk jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

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
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><strong>Full Name</strong></td>
                                        <td><?= $leaveDetails['name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Division</strong></td>
                                        <td><?= $leaveDetails['division_name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Reason</strong></td>
                                        <td><?= $leaveDetails['reason'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Category</strong></td>
                                        <td><?= $leaveDetails['category'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Start Date</strong></td>
                                        <td><?= date('d-M-Y H:i', strtotime($leaveDetails['start_date'])) ?></td>

                                    </tr>
                                    <tr>
                                        <td><strong>Finish Date</strong></td>
                                        <td><?= date('d-M-Y H:i', strtotime($leaveDetails['finish_date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Sent by Admin</strong></td>
                                        <td><?= $leaveDetails['sent_by_admin'] ? date('d-M-Y H:i', strtotime($leaveDetails['sent_by_admin'])) : '-' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status Updated At</strong></td>
                                        <td><?= $leaveDetails['status_updated_at'] ? date('d-M-Y H:i', strtotime($leaveDetails['status_updated_at'])) : '-' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td><?= $leaveDetails['status'] ?></td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Tambahkan tombol Print dan Export PDF -->
                            <div class="mt-3">
                                <div class="float-end">
                                    <button id="printButton" class="btn btn-primary btn-sm" onclick="printDocument()">Print</button>
                                    <button id="exportPDFButton" class="btn btn-danger btn-sm ms-2" onclick="exportPDF()">Export PDF</button>
                                </div>
                                <a id="backButton" href="leavelist.php" class="btn btn-warning btn-sm ms-2">Back</a>
                                <!-- Tulisan tanda tangan -->
                                <div id="signature" class="signature text-end mt-3" style="display: none;">
                                    Tanda tangan
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

    <script>
        var exportingPDF = false;

        // Function untuk export PDF menggunakan jsPDF
        function exportPDF() {
            toggleButtons(false);

            var element = document.body;

            // Buat instance jsPDF
            var pdf = new jsPDF();

            // Konversi elemen HTML ke format PDF
            pdf.fromHTML(element, {
                margin: 10,
                pagesplit: true
            });

            // Simpan atau tampilkan PDF
            // pdf.save('leave_detail.pdf'); // Untuk menyimpan PDF
            window.open(pdf.output('bloburl'), '_blank'); // Untuk menampilkan di jendela baru

            // Kembalikan tampilan tombol setelah pemrosesan selesai
            toggleButtons(true);
        }

        // Function untuk menampilkan atau menyembunyikan tombol
        function toggleButtons(show) {
            var printButton = document.getElementById('printButton');
            var exportPDFButton = document.getElementById('exportPDFButton');
            var backButton = document.getElementById('backButton');

            if (show) {
                printButton.style.display = 'inline-block';
                exportPDFButton.style.display = 'inline-block';
                backButton.style.display = 'inline-block';
            } else {
                printButton.style.display = 'none';
                exportPDFButton.style.display = 'none';
                backButton.style.display = 'none';
            }
        }

        // Function untuk beforePrint
        function beforePrint() {
            var signatureElement = document.getElementById('signature');
            signatureElement.style.display = 'block';
        }

        // Function untuk afterPrint
        function afterPrint() {
            var signatureElement = document.getElementById('signature');
            signatureElement.style.display = 'none';

            if (!exportingPDF) {
                // Jika bukan ekspor PDF, kembalikan tampilan tombol setelah pencetakan selesai
                toggleButtons(true);
            }
        }

        // Function untuk printing
        function printDocument() {
            toggleButtons(false);

            var element = document.body;

            // Show signature before printing
            beforePrint();

            window.print();
        }

        // Attach beforePrint and afterPrint events
        if (window.matchMedia) {
            var mediaQueryList = window.matchMedia('print');
            mediaQueryList.addListener(function (mql) {
                if (mql.matches) {
                    beforePrint();
                } else {
                    afterPrint();
                }
            });
        }

        // Attach onafterprint event for browsers that support it
        window.onafterprint = afterPrint;
    </script>
</body>

</html>
