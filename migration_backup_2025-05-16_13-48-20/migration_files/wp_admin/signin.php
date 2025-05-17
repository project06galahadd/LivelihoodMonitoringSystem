<?php
session_start();
// include "../header.php"; // Removed to prevent blank screen

// Check if already logged in
if(isset($_SESSION['admin'])) {
    header('location: home.php?dashboard=home');
    exit();
}

// Handle error messages
if(isset($_GET['error'])) {
    $_SESSION['error'] = 'Invalid username or password';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MSWD - Sign In</title>
    <link rel="icon" type="image/x-icon" href="../dist/img/Logo.png">
    <style>
        :root {
            --admin-primary: #1a237e;
            --admin-secondary: #283593;
            --admin-accent: #3949ab;
            --admin-text: #1a237e;
            --admin-light: #e8eaf6;
        }

        body {
            font-family: 'Source Sans Pro', sans-serif;
            background: none;
            position: relative;
            overflow-x: hidden;
            height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #1a237e; /* Fallback color */
        }

        .admin-login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 350px;
            animation: slideIn 0.5s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
        }

        .admin-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .admin-logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .admin-logo {
            width: 40px;
            height: 40px;
        }

        .admin-logo-text {
            font-size: 18px;
            font-weight: 600;
            color: var(--admin-primary);
            letter-spacing: 0.5px;
        }

        .admin-subtitle {
            color: #546e7a;
            font-size: 12px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .admin-form-group {
            margin-bottom: 15px;
            position: relative;
            width: 100%;
            box-sizing: border-box;
        }

        .admin-form-group i {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
            font-size: 12px;
        }

        .admin-input {
            width: 100%;
            padding: 8px 8px 8px 25px;
            border: 1px solid #e3f2fd;
            border-radius: 4px;
            font-size: 12px;
            transition: all 0.3s ease;
            background: #f5f7ff;
            height: 35px;
            box-sizing: border-box;
        }

        .admin-input:focus {
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 2px rgba(57, 73, 171, 0.1);
            outline: none;
            background: #ffffff;
        }

        .admin-btn {
            width: 100%;
            padding: 8px;
            background: var(--admin-primary);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            height: 35px;
            margin-top: 5px;
        }

        .admin-btn:hover {
            background: var(--admin-secondary);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(26, 35, 126, 0.2);
        }

        .admin-error {
            background: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 12px;
            display: flex;
            align-items: center;
            animation: shake 0.5s ease-in-out;
            border: 1px solid #ffcdd2;
        }

        .admin-error i {
            margin-right: 8px;
            font-size: 12px;
        }

        .admin-footer {
            text-align: center;
            margin-top: 20px;
            color: #546e7a;
            font-size: 11px;
        }

        .admin-footer a {
            color: var(--admin-accent);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .admin-footer a:hover {
            color: var(--admin-primary);
        }

        .admin-forgot-password {
            text-align: right;
            margin-bottom: 10px;
        }

        .admin-forgot-password a {
            color: var(--admin-accent);
            font-size: 11px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .admin-forgot-password a:hover {
            color: var(--admin-primary);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-3px); }
            75% { transform: translateX(3px); }
        }

        @media (max-width: 480px) {
            .admin-login-container {
                padding: 20px;
            }
        }

        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
            pointer-events: none;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(26, 35, 126, 0.85); /* Matching admin theme color */
            z-index: -1;
            pointer-events: none;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Video Background -->
    <video class="video-background" autoplay muted loop>
        <source src="../videos/LivelihoodStreet2.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="overlay"></div>
    <div class="admin-login-container">
        <div class="admin-header">
            <div class="admin-logo-container">
                <img src="../dist/img/LOGO DESIGN.png" alt="MSWD Logo" class="admin-logo">
                <span class="admin-logo-text">MSWD Admin</span>
            </div>
            <p class="admin-subtitle">Livelihood Monitoring System</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="admin-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="signin_process.php">
            <div class="admin-form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="USERNAME" class="admin-input" placeholder="Username" required>
            </div>
            
            <div class="admin-form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="PASSWORD" class="admin-input" placeholder="Password" required>
            </div>

            <div class="admin-forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>

            <button type="submit" name="login" class="admin-btn">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="admin-footer">
            <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>

    <script>
        // Add animation when form is submitted with error
        document.querySelector('form').addEventListener('submit', function(e) {
            if (document.querySelector('.admin-error')) {
                document.querySelector('.admin-error').style.animation = 'shake 0.5s ease-in-out';
            }
        });
    </script>
</body>
</html>