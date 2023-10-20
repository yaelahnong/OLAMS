<?php
session_start();
include __DIR__ . "/../include/conn.inc.php";
include __DIR__ . "/../include/csrf_token.inc.php";
include __DIR__ . "/../include/baseUrl.inc.php";

// var_dump($baseUrl);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	//mememeriksa apakah token csrf valid atau tidak
	if (isset($_POST['csrf_token']) && isCsrfTokenValid($_POST['csrf_token'])) {
		// var_dump(generateToken());
		// exit;
		// Mengambil data yang dikirimkan dari form
		$username = cleanValue($_POST["username"]);
		$password = cleanValue($_POST["password"]);

		if (empty($username) || empty($password)) {
			$error = "username dan password harus di isi.";
		} else {
			// Mencari pengguna berdasarkan username
			$query = "SELECT user_id, username, password FROM users WHERE username = ?";
			$stmt = mysqli_prepare($conn, $query);
			mysqli_stmt_bind_param($stmt, "s", $username);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$user = mysqli_fetch_assoc($result);

			if ($user) {
				if ($user && password_verify($password, $user["password"])) {
					// Password cocok, login berhasil
					$_SESSION['user_id'] = $user['user_id'];
					header("Location: ../userlist.php");
					exit;
				} else {
					$error = "Username dan Password salah!!";
				}
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
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
	<meta name="author" content="AdminKit">
	<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">
	<link rel="canonical" href="../template/css/app.css" />

	<title>Sign In | Olams</title>

	<link href="../styles/app.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

	<style>
		.alert-danger:not(.alert-outline):not(.alert-outline-coloured) {
			background: #f8d7da;
		}

		.alert {
			color: #212529;
			display: flex;
			padding: 0;
		}

		.alert-danger {
			--bs-alert-color: var(--bs-danger-text-emphasis);
			--bs-alert-bg: var(--bs-danger-bg-subtle);
			--bs-alert-border-color: var(--bs-danger-border-subtle);
			--bs-alert-link-color: var(--bs-danger-text-emphasis);
		}

		.alert-dismissible {
			padding-right: 2.85rem;
		}
	</style>
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
								Sign in to your account to continue
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
									<form method="post" action="login.php">
										<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
										<div class="mb-3">
											<label class="form-label">Username</label>
											<input class="form-control form-control-lg" type="text" name="username" placeholder="Enter your Username" required/>
										</div>
										<div class="mb-3">
											<label class="form-label">Password</label>
											<input class="form-control form-control-lg" type="password" name="password" placeholder="Enter your Password" required/>
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

	<script src="../style/app.js"></script>

</body>

</html>