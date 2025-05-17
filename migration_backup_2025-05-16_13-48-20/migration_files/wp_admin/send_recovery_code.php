<?php
session_start();
include "../header.php";

// Database connection
$conn = new mysqli("localhost", "root", "", "lms_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    // Check if email exists in admin table
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Generate a 6-digit recovery code
        $recovery_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store the recovery code in the database with expiration time
        $expiry_time = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $stmt = $conn->prepare("UPDATE admin SET recovery_code = ?, recovery_code_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $recovery_code, $expiry_time, $email);
        $stmt->execute();
        
        // Send email with recovery code
        $to = $email;
        $subject = "MSWD Admin Portal - Password Recovery Code";
        $message = "
        <html>
        <head>
            <title>Password Recovery Code</title>
        </head>
        <body>
            <h2>Password Recovery Code</h2>
            <p>Your recovery code is: <strong>$recovery_code</strong></p>
            <p>This code will expire in 15 minutes.</p>
            <p>If you did not request this code, please ignore this email.</p>
        </body>
        </html>
        ";
        
        // Set content-type header for sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: MSWD Admin Portal <noreply@mswdportal.com>" . "\r\n";
        
        if (mail($to, $subject, $message, $headers)) {
            $_SESSION['success'] = "Recovery code has been sent to your email.";
            $_SESSION['recovery_email'] = $email;
            header("location: verify_code.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to send recovery code. Please try again.";
            header("location: forgot_password.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Email not found in our records.";
        header("location: forgot_password.php");
        exit();
    }
}

$conn->close();
?> 