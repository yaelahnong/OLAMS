<?php
session_start();
include __DIR__ . "/include/conn.inc.php";
include __DIR__ . "/include/csrf_token.inc.php";
include __DIR__ . "/include/baseUrl.inc.php";

// var_dump($baseUrl);
function isUserLoggedIn() {
    return isset($_SESSION["login"]);
}
if (isUserLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	//mememeriksa apakah token csrf valid atau tidak
	if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
		// Mengambil data yang dikirimkan dari form
		$username = cleanValue($_POST["username"]);
		$password = cleanValue($_POST["password"]);

		if (empty($username) && empty($password)) {
			$error = "Username dan password harus di isi.";
		} else {
			// Mencari pengguna berdasarkan username
			$query = "SELECT user_id, username, password, name, role_id FROM users WHERE username = ?";
			$stmt = mysqli_prepare($conn, $query);
			mysqli_stmt_bind_param($stmt, "s", $username);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$user = mysqli_fetch_assoc($result);

			if ($user) {
				if ($user && password_verify($password, $user["password"])) {
					// Password cocok, login berhasil
					$_SESSION['login'] = true;
					$_SESSION['user_id'] = $user['user_id'];
					$_SESSION['name'] = $user['name'];
					$_SESSION['role_id'] = $user['role_id'];
					header("Location: dashboard.php");
					exit;
				} else {
					$error = "Incorrect username and password!!";
				}
			}else {
				$error = "Username and Password not found";
			}
		}
	} else {
		echo "Token Tidak Valid";
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php include "head.inc.php"; ?>
	<title>Sign In | Olams</title>
</head>

<body>
	<main class="d-flex w-100">
		<div class="container d-flex flex-column">
			<div class="row vh-100">
				<div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
					<div class="d-table-cell align-middle">
						<div class="text-center mt-4">
							<h1 class="h2">Welcome Back To OLAMS</h1>
							<p class="lead">
								Sign in with your account to continue
							</p>
						</div>
						<div class="card">
							<div class="card-body">
								<?php if (isset($error)) { ?>
									<div class="alert alert-danger alert-dismissible p-3 rounded" role="alert">
										<div class="alert-message">
											<?php echo $error; ?>
										</div>
										<button type="button" class="btn-close align-items-end" data-bs-dismiss="alert" aria-label="Close"></button>
									</div>
								<?php } ?>
								<div class="m-sm-3">
									<form method="post" action="<?= cleanValue($_SERVER['PHP_SELF']); ?>">
										<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
										<div class="mb-3">
											<label class="form-label">Username</label>
											<input class="form-control form-control-lg" type="text" name="username" placeholder="Enter your Username" />
										</div>
										<div class="mb-3">
											<label class="form-label">Password</label>
											<input class="form-control form-control-lg" type="password" name="password" placeholder="Enter your Password" />
										</div>
										<div class="d-grid gap-2 mt-3">
											<button type="submit" class="btn btn-lg btn-success">Login</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
	<?php include "script.inc.php"; ?>
</body>

</html>