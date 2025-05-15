<?php
// Set timezone
if (!ini_get('date.timezone')) {
	date_default_timezone_set('Asia/Manila');
}

session_start();
require_once 'conn.php';

// Check if user is logged in and is a member
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'MEMBER') {
	header('Location: /LivelihoodMonitoringSystem/wp_member/signin.php');
	exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM tbl_users WHERE id = ? AND role = 'MEMBER' AND ACC_STATUS = 1 LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
	$user = $result->fetch_assoc();
	$fullname = $user['username']; // You can update this if you have a fullname field
} else {
	// If user not found or inactive, force logout
	session_destroy();
	header('Location: /LivelihoodMonitoringSystem/wp_member/signin.php');
	exit();
}
?>