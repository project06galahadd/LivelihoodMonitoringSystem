<?php
session_start();
include "header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Selection</title>
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            font-family: 'Montserrat', 'Segoe UI', sans-serif;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-out;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-title {
            color: #ffffff;
            margin-bottom: 25px;
            font-size: 1.2em;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-align: center;
        }

        .login-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .login-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-decoration: none;
            display: block;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .member-btn {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }

        .admin-btn {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.3);
        }

        .cancel-btn {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.1);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .back-link {
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            display: inline-block;
            transition: color 0.3s ease;
            font-size: 0.8em;
            text-align: center;
            width: 100%;
        }

        .back-link:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
            }
            
            .login-title {
                font-size: 1em;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">Select Login Type</h1>
        <div class="login-buttons">
            <a href="/LivelihoodMonitoringSystem/wp_member/signin.php" class="login-btn member-btn">Member Login</a>
            <a href="/LivelihoodMonitoringSystem/wp_admin/signin.php" class="login-btn admin-btn">Admin Login</a>
            <a href="/LivelihoodMonitoringSystem/index.php" class="login-btn cancel-btn">Cancel</a>
        </div>
        <a href="/LivelihoodMonitoringSystem/index.php" class="back-link">← Back to Home</a>
    </div>
</body>
</html>