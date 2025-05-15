<?php
session_start();

// Log logout time if admin session is set
if (isset($_SESSION['admin'])) {
    try {
        include('includes/conn.php');
        date_default_timezone_set('Asia/Manila');
        $ldate = date('Y-m-d h:i:s A', time());
        $uid = $_SESSION['admin'];
        
        // Prepare the query to prevent SQL injection
        $stmt = $conn->prepare("UPDATE tbl_userlog SET LOGOUT = ? WHERE UID = ? ORDER BY ID DESC LIMIT 1");
        $stmt->bind_param("ss", $ldate, $uid);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        // Log the error but don't stop the logout process
        error_log("Logout error: " . $e->getMessage());
    }
}

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to signin page
header('Location: signin.php');
exit();
