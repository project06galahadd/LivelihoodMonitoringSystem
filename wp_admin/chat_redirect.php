<?php
session_start();
require_once "includes/conn.php";

// Check if user is logged in and has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header('Location: signin.php');
    exit();
}

// Redirect to chat page
header('Location: chat.php');
exit();
?> 