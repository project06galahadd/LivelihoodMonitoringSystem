<?php
session_start();
include "includes/conn.php";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Simple authentication for testing
    if ($username == 'admin' && $password == 'admin') {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'admin';
        $_SESSION['role'] = 'ADMIN';
        header('location: /LivelihoodMonitoringSystem/wp_admin/home.php');
        exit();
    } elseif ($username == 'user' && $password == 'user') {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'user';
        $_SESSION['role'] = 'MEMBER';
        header('location: /LivelihoodMonitoringSystem/wp_member/home.php');
        exit();
    } else {
        $_SESSION['error'] = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign In | SLP</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>
    :root {
      --primary-color: #3498db;
      --secondary-color: #2c3e50;
      --accent-color: #e74c3c;
    }
    
    body {
      background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Source Sans Pro', sans-serif;
    }

    .login-container {
      width: 100%;
      max-width: 400px;
      padding: 20px;
    }

    .login-box {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      overflow: hidden;
      transition: transform 0.3s ease;
    }

    .login-box:hover {
      transform: translateY(-5px);
    }

    .login-logo {
      text-align: center;
      padding: 20px 0;
    }

    .login-logo img {
      max-width: 180px;
      height: auto;
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }

    .login-card-body {
      padding: 30px;
    }

    .login-box-msg {
      text-align: center;
      color: var(--secondary-color);
      font-size: 1.1rem;
      margin-bottom: 25px;
    }

    .input-group {
      margin-bottom: 20px;
    }

    .input-group-text {
      background: transparent;
      border-right: none;
      color: var(--primary-color);
    }

    .form-control {
      border-left: none;
      padding: 12px;
      height: auto;
    }

    .form-control:focus {
      box-shadow: none;
      border-color: var(--primary-color);
    }

    .form-control:focus + .input-group-text {
      border-color: var(--primary-color);
    }

    .btn-primary {
      background: var(--primary-color);
      border: none;
      padding: 12px;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background: var(--secondary-color);
      transform: translateY(-2px);
    }

    .login-type {
      margin-top: 25px;
      text-align: center;
    }

    .login-type p {
      color: var(--secondary-color);
      margin-bottom: 15px;
      font-weight: 500;
    }

    .btn-group {
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn-outline-primary {
      border: none;
      padding: 10px;
      color: var(--secondary-color);
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
      background: var(--primary-color);
      color: white;
    }

    .btn-outline-primary.active {
      background: var(--primary-color);
      color: white;
    }

    .alert {
      border-radius: 8px;
      border: none;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .alert-danger {
      background: #fff5f5;
      color: var(--accent-color);
    }

    @media (max-width: 576px) {
      .login-container {
        padding: 15px;
      }
      
      .login-card-body {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <div class="login-logo">
        <img src="dist/img/Logo.png" alt="SLP Logo">
      </div>
      <div class="card-body login-card-body">
        <p class="login-box-msg">Welcome back! Please sign in to continue</p>
        <?php
        if (isset($_SESSION['error'])) {
          echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="fas fa-exclamation-circle mr-2"></i>
                  ' . $_SESSION['error'] . '
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>';
          unset($_SESSION['error']);
        }
        ?>
        <form action="" method="post">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">
                <i class="fas fa-user"></i>
              </span>
            </div>
            <input type="text" class="form-control" name="username" placeholder="Username" required>
          </div>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">
                <i class="fas fa-lock"></i>
              </span>
            </div>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="submit" name="login" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt mr-2"></i>Sign In
              </button>
            </div>
          </div>
        </form>
        <div class="login-type">
          <p>Test Credentials</p>
          <div class="btn-group">
            <button type="button" class="btn btn-outline-primary active">
              <i class="fas fa-user-shield mr-2"></i>Admin
            </button>
            <button type="button" class="btn btn-outline-primary">
              <i class="fas fa-user mr-2"></i>Member
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="dist/js/adminlte.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.login-type .btn').click(function() {
        $('.login-type .btn').removeClass('active');
        $(this).addClass('active');
      });
    });
  </script>
</body>
</html> 