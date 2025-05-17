<?php
session_start();
require_once "includes/conn.php";

// Check if user is logged in and has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if ID is provided
if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'News ID is required']);
    exit;
}

$id = $_POST['id'];

try {
    // Get news details
    $stmt = $conn->prepare("
        SELECT na.*, u.fullname as posted_by_name 
        FROM news_announcements na 
        JOIN tbl_users u ON na.posted_by = u.user_id 
        WHERE na.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $news = $result->fetch_assoc();

    if ($news) {
        echo json_encode(['success' => true, 'data' => $news]);
    } else {
        echo json_encode(['success' => false, 'message' => 'News not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching news: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?> 