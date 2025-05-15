<?php
session_start();

if (!isset($_SESSION['USER_ID']) || $_SESSION['ROLE'] !== 'User') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
      <span class="brand-text font-weight-light">User Panel</span>
    </a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block"><?= $_SESSION['FULLNAME'] ?></a>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item">
            <a href="#" class="nav-link active"><i class="nav-icon fas fa-home"></i><p>Dashboard</p></a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper p-4">
    <div class="content-header">
      <h1>Welcome, <?= $_SESSION['FULLNAME'] ?>!</h1>
      <p>This is your user dashboard.</p>
    </div>
  </div>

</div>
</body>
</html>