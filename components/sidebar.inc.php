<nav id="sidebar" class="sidebar js-sidebar">
  <div class="sidebar-content js-simplebar">
    <a class="sidebar-brand" href="dashboard.php">
    <img src="images/photos/logo.png" class="avatar img-fluid rounded-5 me-1" alt="Charles Hall" style="width: 60px; height: 40px;"/>
      <span class="align-middle">OLAMS</span>
    </a>

    <ul class="sidebar-nav">
      <li class="sidebar-header">
        Menu
      </li>

      <li class="sidebar-item <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
        <a class="sidebar-link" href="dashboard.php">
          <i class="align-middle" data-feather="grid"></i> <span class="align-middle">Dashboard</span>
        </a>
      </li>

      <li class="sidebar-item  <?= (basename($_SERVER['PHP_SELF']) == 'overtimelist.php') ? 'active' : ''; ?>">
        <a class="sidebar-link" href="overtimelist.php">
          <i class="align-middle" data-feather="clock"></i> <span class="align-middle">Overtime</span>
        </a>
      </li>

      <li class="sidebar-item  <?= (basename($_SERVER['PHP_SELF']) == 'leavelist.php') ? 'active' : ''; ?>">
        <a class="sidebar-link" href="leavelist.php">
          <i class="align-middle" data-feather="calendar"></i> <span class="align-middle">Leave</span>
        </a>
      </li>

      <li class="sidebar-item  <?= (basename($_SERVER['PHP_SELF']) == 'duty_overtimelist.php') ? 'active' : ''; ?>">
        <a class="sidebar-link" href="duty_overtimelist.php">
          <i class="align-middle" data-feather="clock"></i> <span class="align-middle">Duty Overtime</span>
        </a>
      </li>

      
      <li class="sidebar-item  <?= (basename($_SERVER['PHP_SELF']) == 'attendancelist.php') ? 'active' : ''; ?>">
        <a class="sidebar-link" href="attendancelist.php">
          <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Attendance</span>
        </a>
      </li>

      <?php if($_SESSION['role_id'] === 3 || $_SESSION['role_id'] === 4) : ?>

      <li class="sidebar-header">
        Master Data
      </li>

      <li class="sidebar-item  <?= (basename($_SERVER['PHP_SELF']) == 'projectlist.php') ? 'active' : ''; ?>">
        <a class="sidebar-link" href="projectlist.php">
          <i class="align-middle" data-feather="square"></i> <span class="align-middle">Master Project</span>
        </a>
      </li>

      <li class="sidebar-item  <?= (basename($_SERVER['PHP_SELF']) == 'divisionlist.php') ? 'active' : ''; ?>">
        <a class="sidebar-link" href="divisionlist.php">
          <i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Master Division</span>
        </a>
      </li>

      <li class="sidebar-item  <?= (basename($_SERVER['PHP_SELF']) == 'basicsalary.php') ? 'active' : ''; ?>">
        <a class="sidebar-link" href="basicsalary.php">
          <i class="align-middle" data-feather="grid"></i> <span class="align-middle">Master Basic Salary</span>
        </a>
      </li>

      <li class="sidebar-header">
        User Management
      </li>

      <li class="sidebar-item  <?= (basename($_SERVER['PHP_SELF']) == 'userlist.php') ? 'active' : ''; ?>">
        <a class="sidebar-link" href="userlist.php">
          <i class="align-middle" data-feather="user-plus"></i> <span class="align-middle">User List</span>
        </a>
      </li>

      <?php elseif($_SESSION['role_id'] === 2) : ?>
      <li class="sidebar-header">
        Master Data
      </li>

      <li class="sidebar-item  <?= (basename($_SERVER['PHP_SELF']) == 'projectlist.php') ? 'active' : ''; ?>">
        <a class="sidebar-link" href="projectlist.php">
          <i class="align-middle" data-feather="square"></i> <span class="align-middle">Master Project</span>
        </a>
      </li>

      <li class="sidebar-item  <?= (basename($_SERVER['PHP_SELF']) == 'divisionlist.php') ? 'active' : ''; ?>">
        <a class="sidebar-link" href="divisionlist.php">
          <i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Master Division</span>
        </a>
      </li>
      <?php endif; ?>
    </ul>

  </div>
</nav>