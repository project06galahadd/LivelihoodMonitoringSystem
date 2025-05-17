<?php
session_start();
require_once '../includes/conn.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    header('Location: ../signin.php');
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: programs.php');
    exit();
}

// Validate input
$required_fields = ['program_name', 'description', 'requirements', 'duration', 'capacity'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        $_SESSION['error'] = "All fields are required";
        header('Location: programs.php');
        exit();
    }
}

// Sanitize input
$program_name = htmlspecialchars(trim($_POST['program_name']));
$description = htmlspecialchars(trim($_POST['description']));
$requirements = htmlspecialchars(trim($_POST['requirements']));
$duration = htmlspecialchars(trim($_POST['duration']));
$capacity = filter_var($_POST['capacity'], FILTER_SANITIZE_NUMBER_INT);

// Validate capacity
if ($capacity < 1) {
    $_SESSION['error'] = "Capacity must be at least 1";
    header('Location: programs.php');
    exit();
}

try {
    // Insert new program
    $stmt = $conn->prepare("INSERT INTO tbl_programs 
        (program_name, description, requirements, duration, capacity, status) 
        VALUES (?, ?, ?, ?, ?, 'ACTIVE')");
    
    $stmt->bind_param("ssssi", 
        $program_name, 
        $description, 
        $requirements, 
        $duration, 
        $capacity
    );
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Program added successfully";
    } else {
        $_SESSION['error'] = "Error adding program: " . $stmt->error;
    }
    
    $stmt->close();
} catch (Exception $e) {
    $_SESSION['error'] = "An error occurred: " . $e->getMessage();
}

header('Location: programs.php');
exit(); 