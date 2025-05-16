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
$required_fields = ['program_id', 'program_name', 'description', 'requirements', 'duration', 'capacity'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        $_SESSION['error'] = "All fields are required";
        header('Location: programs.php');
        exit();
    }
}

// Sanitize input
$program_id = filter_var($_POST['program_id'], FILTER_SANITIZE_NUMBER_INT);
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
    // Update program
    $stmt = $conn->prepare("UPDATE tbl_programs SET 
        program_name = ?, 
        description = ?, 
        requirements = ?, 
        duration = ?, 
        capacity = ? 
        WHERE program_id = ?");
    
    $stmt->bind_param("ssssii", 
        $program_name, 
        $description, 
        $requirements, 
        $duration, 
        $capacity, 
        $program_id
    );
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Program updated successfully";
    } else {
        $_SESSION['error'] = "Error updating program: " . $stmt->error;
    }
    
    $stmt->close();
} catch (Exception $e) {
    $_SESSION['error'] = "An error occurred: " . $e->getMessage();
}

header('Location: programs.php');
exit(); 