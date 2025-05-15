<?php
session_start();
include "../header.php";

// Check if email is set in session
if (!isset($_SESSION['recovery_email'])) {
    header("location: forgot_password.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "lms_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recovery_code = $_POST['recovery_code'];
    $email = $_SESSION['recovery_email'];
    
    // Check if recovery code matches and is not expired
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? AND recovery_code = ? AND recovery_code_expiry > NOW()");
    $stmt->bind_param("ss", $email, $recovery_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Code is valid, redirect to reset password page
        $_SESSION['recovery_code_verified'] = true;
        header("location: reset_password.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid or expired recovery code.";
        header("location: verify_code.php");
        exit();
    }
}

$conn->close();
?> 