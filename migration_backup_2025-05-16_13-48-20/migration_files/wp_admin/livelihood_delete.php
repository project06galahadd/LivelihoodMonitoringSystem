<?php
session_start();
include "includes/conn.php";

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];

    // Delete the livelihood program
    $sql = "DELETE FROM tbl_livelihood WHERE LIVELIHOOD_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Livelihood program "' . $name . '" deleted successfully';
    } else {
        $_SESSION['error'] = 'Error deleting livelihood program';
    }
} else {
    $_SESSION['error'] = 'Invalid request';
}

header('location: livelihood.php');
?>