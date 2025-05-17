<?php
session_start();

// Handle form submission
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $error = 'Please enter your registered email address.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Here you would check if the email exists in the database and send a code
        // For now, always show success for demo
        $success = 'If this email is registered, a recovery code has been sent.';
        // In a real implementation, store the email in session and send the code
        $_SESSION['recovery_email'] = $email;
        // Redirect to code verification page in the next step
        header('Location: verify_code.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | MSWD Member</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,600&display=swap">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(-45deg, #e0eafc, #cfdef3, #a1c4fd, #c2e9fb);
            background-size: 400% 400%;
            animation: gradientBG 12s ease-in-out infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', sans-serif;
            position: relative;
            overflow: hidden;
        }
        @keyframes gradientBG {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }
        .forgot-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.13);
            padding: 36px 32px 28px 32px;
            max-width: 370px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 0.7s cubic-bezier(.39,.575,.56,1.000);
            position: relative;
            z-index: 2;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .forgot-logo {
            margin-bottom: 18px;
        }
        .forgot-logo img {
            width: 90px;
            height: auto;
            display: block;
            margin: 0 auto;
            filter: drop-shadow(0 2px 8px rgba(44,62,80,0.08));
        }
        .forgot-title {
            text-align: center;
            font-weight: 700;
            font-size: 1.15rem;
            color: #2c3e50;
            margin-bottom: 18px;
            letter-spacing: 1px;
        }
        .form-group {
            width: 100%;
            margin-bottom: 18px;
        }
        .form-label {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 6px;
            display: block;
            font-size: 0.98em;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #bfc9d1;
            border-radius: 7px;
            font-size: 1em;
            outline: none;
            transition: border 0.2s;
            background: #f7fafd;
        }
        .form-control:focus {
            border-color: #3498db;
            background: #fff;
        }
        .btn-primary {
            width: 100%;
            background: linear-gradient(90deg, #3498db 0%, #6dd5fa 100%);
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 11px 0;
            font-weight: 700;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(52,152,219,0.08);
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #217dbb 0%, #3498db 100%);
        }
        .info-message, .error-message {
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 0.97em;
            width: 100%;
        }
        .info-message {
            background: #eafaf1;
            color: #27ae60;
        }
        .error-message {
            background: #ffeaea;
            color: #e74c3c;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #3498db;
            text-decoration: none;
            font-size: 0.95em;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #217dbb;
        }
        @media (max-width: 480px) {
            .forgot-card {
                padding: 22px 8px 18px 8px;
            }
            .forgot-logo img {
                width: 70px;
            }
        }
    </style>
</head>
<body>
    <div class="forgot-card">
        <div class="forgot-logo">
            <img src="/LivelihoodMonitoringSystem/dist/img/Logo.png" alt="MSWD Logo">
        </div>
        <div class="forgot-title">Forgot Password</div>
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="info-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <div class="form-group">
                <label class="form-label" for="email">Registered Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>
            <button type="submit" class="btn-primary">Send Recovery Code</button>
        </form>
        <a href="/LivelihoodMonitoringSystem/wp_member/signin.php" class="back-link">&larr; Back to Sign In</a>
    </div>
</body>
</html> 