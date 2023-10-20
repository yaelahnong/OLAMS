<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login/login.php");
    exit();
}

$divisionData = [];
if (isset($_GET['id'])) {
    $division_id = cleanValue($_GET['id']);

    // Query untuk mengambil data divisi berdasarkan division_id
    $queryDivision = "SELECT division_name FROM m_divisions WHERE division_id = ?";
    $stmt = mysqli_prepare($conn, $queryDivision);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $division_id);
        mysqli_stmt_execute($stmt);
        $divisionData = mysqli_stmt_get_result($stmt);
        $divisionData = mysqli_fetch_assoc($divisionData);
        mysqli_stmt_close($stmt);
    } else {
        $error = "Failed to create a prepared statement.";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["division_name"]) && !empty($_POST["division_name"])) {
        $newDivisionName = $_POST["division_name"];
        if (preg_match("/^[a-zA-Z0-9 ]*$/", $newDivisionName)) {
            $newDivisionName = cleanValue($newDivisionName);

            $checkDivisionName = "SELECT division_name FROM m_divisions WHERE division_name = ?";
            $checkStmt = mysqli_prepare($conn, $checkDivisionName);

            if ($checkStmt) {
                mysqli_stmt_bind_param($checkStmt, "s", $newDivisionName);
                mysqli_stmt_execute($checkStmt);
                $checkResult = mysqli_stmt_get_result($checkStmt);

                if (mysqli_num_rows($checkResult) > 0) {
                    $error = "Division with the same name already exists.";
                } else {
                    if (isset($_GET['id'])) {
                        $division_id = cleanValue($_GET['id']);
                        $updateQuery = "UPDATE m_divisions SET division_name = ?, updated_by = ? WHERE division_id = ?";
                        $stmt = mysqli_prepare($conn, $updateQuery);

                        if ($stmt) {
                            $user_id = $_SESSION["user_id"];
                            mysqli_stmt_bind_param($stmt, "sii", $newDivisionName, $user_id, $division_id);

                            if (mysqli_stmt_execute($stmt)) {
                                echo "<script>alert('Division data updated successfully.')</script>";
                                echo "<script>window.location.href = 'divisionlist.php'</script>";
                                exit();
                            } else {
                                $error = "Failed to update the division.";
                            }
                            mysqli_stmt_close($stmt);
                        } else {
                            $error = "Failed to create a prepared statement.";
                        }
                    } else {
                        $error = "Division ID not provided.";
                    }
                }
                mysqli_stmt_close($checkStmt);
            } else {
                $error = "Failed to make a prepared statement for duplicate check.";
            }
        } else {
            $error = "Input can only contain letters, numbers, and spaces.";
        }
    } else {
        $error = "The division name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.inc.php"; ?>
    <title>OLAMS - Edit Division</title>
</head>

<body>
    <div class="wrapper">
        <?php include "components/sidebar.inc.php"; ?>
        <div class="main">
            <?php include "components/navbar.inc.php"; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3"><strong>Edit Division</strong></h1>
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
                                    <form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']."?id=$division_id"); ?>">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="inputDivisionName">Division Name</label>
                                                <input type="text" class="form-control" name="division_name" id="inputDivisionName" placeholder="Enter Division Name" value="<?= $divisionData['division_name'] ?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                <a href="divisionlist.php" class="btn btn-light text-dark text-decoration-none">Cancel</a>
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
