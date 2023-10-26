<nav class="navbar navbar-expand navbar-light navbar-bg bg-dark">
  <a class="sidebar-toggle js-sidebar-toggle">
    <i class="hamburger align-self-center"></i>
  </a>

  <div class="navbar-collapse collapse">
    <ul class="navbar-nav navbar-align">
      
      <li class="nav-item dropdown bg-dark">
        <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
          <i class="align-middle" data-feather="settings"></i>
        </a>

        <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
          <span class="text-white"><?php echo $_SESSION['name'] ?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
          <a class="dropdown-item" href="profil.php"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
          <a class="dropdown-item" href="logout.php"><i class="align-middle me-1" data-feather="power"></i>Log out</a>
        </div>
      </li>
    </ul>
  </div>
</nav>