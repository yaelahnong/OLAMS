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
            <form>
              <div class="row">
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="inputEmail4">Fullname</label>
                  <input type="text" class="form-control" name="name" id="inputEmail4" placeholder="Enter fullname">
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="inputPassword4">Username</label>
                  <input type="text" class="form-control" name="username" id="inputPassword4" placeholder="Enter username">
                </div>
              </div>
              <div class="row">
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="inputEmail4">Email</label>
                  <input type="email" class="form-control" name="email" id="inputEmail4" placeholder="Enter email">
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="inputPassword4">Password</label>
                  <input type="password" class="form-control" name="password" id="inputPassword4" placeholder="Enter password">
                </div>
              </div>
              <div class="row">
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="inputState">Role</label>
                  <div class="input-group mb-3">
                    <select class="form-select flex-grow-1">
                      <option>Select role...</option>
                      <option>User</option>
                      <option>Leader</option>
                      <option>Admin</option>
                      <option>Supervisor</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <button type="submit" class="btn btn-danger">
                    <a href="userlist.php" class="text-white text-decoration-none">Cancel</a>
                  </button>
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