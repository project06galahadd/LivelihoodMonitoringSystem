<?php
session_start();
require_once "../wp_admin/includes/conn.php";

// If already logged in as member, redirect to home
if (isset($_SESSION['role']) && $_SESSION['role'] === 'MEMBER') {
    header('Location: home.php');
    exit();
}

// Show error message from session if set
$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Debug information
error_log("Current URL: " . $_SERVER['REQUEST_URI']);
error_log("Server Host: " . $_SERVER['HTTP_HOST']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | MSWD</title>
    <link rel="icon" type="image/png" href="/LivelihoodMonitoringSystem/dist/img/Logo.png">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
        }
        body {
            font-family: 'Source Sans Pro', sans-serif;
            background: var(--primary-color);
            position: relative;
            overflow-x: hidden;
            height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            object-fit: cover;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(44, 62, 80, 0.85);
            z-index: 0;
        }

        .login-box {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            width: 400px;
            margin: 0 auto;
            overflow: hidden;
        }

        .login-logo {
            margin: 30px 0;
            text-align: center;
        }

        .login-logo img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .login-logo img:hover {
            transform: scale(1.05);
        }

        .login-logo h1 {
            color: var(--text-color);
            font-size: 24px;
            font-weight: 600;
            margin-top: 15px;
            letter-spacing: 0.5px;
        }

        .login-card-body {
            padding: 30px;
        }

        .login-box-msg {
            color: var(--text-color);
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 25px;
            text-align: center;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group-text {
            background: var(--secondary-color);
            border: none;
            color: #fff;
            padding: 12px 15px;
            border-radius: 8px 0 0 8px;
        }

        .form-control {
            border: 1px solid #ddd;
            padding: 12px 15px;
            height: auto;
            border-radius: 0 8px 8px 0;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn-primary {
            background: var(--secondary-color);
            border: none;
            padding: 12px 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }

        .register-link a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .register-link a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .alert {
            border: none;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 15px;
            font-weight: 500;
        }

        .alert-danger {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
        }

        .forgot-password {
            text-align: right;
            margin-top: -15px;
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .login-box {
                width: 90%;
                margin: 0 auto;
            }
        }
    </style>
</head>

<body>
    <!-- Video Background -->
    <video class="video-background" autoplay muted loop playsinline>
        <source src="/LivelihoodMonitoringSystem/dist/video/background.mp4" type="video/mp4">
    </video>
    <div class="overlay"></div>

    <div class="login-box">
        <div class="login-logo">
            <img src="/LivelihoodMonitoringSystem/dist/img/Logo.png" alt="MSWD Logo">
            <h1>MSWD Member Portal</h1>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>
        
                <?php
                if(isset($_SESSION['error'])){
                    echo "
                    <div class='alert alert-danger'>
                        <i class='fas fa-exclamation-circle mr-2'></i> ".$_SESSION['error']."
                    </div>
                    ";
                    unset($_SESSION['error']);
                }
                if(isset($_SESSION['success'])){
                    echo "
                    <div class='alert alert-success'>
                        <i class='fas fa-check-circle mr-2'></i> ".$_SESSION['success']."
                    </div>
                    ";
                    unset($_SESSION['success']);
                }
                ?>

                <form action="signin_process.php" method="POST">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <div class="forgot-password">
                        <a href="forgot_password.php">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </button>
                </form>

                <div class="register-link">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
</body>

</html>