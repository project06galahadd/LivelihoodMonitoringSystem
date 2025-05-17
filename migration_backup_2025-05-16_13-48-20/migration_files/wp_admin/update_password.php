<?php
session_start();
include "../header.php";

// Check if recovery code is verified and email is set
if (!isset($_SESSION['recovery_code_verified']) || !isset($_SESSION['recovery_email'])) {
    header("location: forgot_password.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "lms_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['recovery_email'];
    
    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("location: reset_password.php");
        exit();
    }
    
    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update the password and clear recovery code
    $stmt = $conn->prepare("UPDATE admin SET PASSWORD = ?, recovery_code = NULL, recovery_code_expiry = NULL WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);
    
    if ($stmt->execute()) {
        // Clear session variables
        unset($_SESSION['recovery_code_verified']);
        unset($_SESSION['recovery_email']);
        
        // Set success message
        $_SESSION['success'] = "Password has been reset successfully. Please login with your new password.";
        header("location: signin.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to reset password. Please try again.";
        header("location: reset_password.php");
        exit();
    }
}

$conn->close();
?> 