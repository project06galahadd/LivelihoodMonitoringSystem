<?php
include 'includes/session.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate input parameters
if (isset($_GET['type']) && isset($_GET['id']) && isset($_GET['STATUS_REMARKS'])) {
    // Sanitize inputs
    $type = trim($_GET['type']);
    $id = trim($_GET['id']);
    $STATUS_REMARKS = trim($_GET['STATUS_REMARKS']);

    // Validate input values
    if (empty($id) || empty($type) || empty($STATUS_REMARKS)) {
        $_SESSION['error'] = "All fields are required!";
        header('location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    if (strlen($STATUS_REMARKS) < 5) {
        $_SESSION['error'] = "Status remarks must be at least 5 characters long!";
        header('location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Determine which table to update based on type
    $table = '';
    $id_field = '';
    switch($type) {
        case 'member':
            $table = 'tbl_members';
            $id_field = 'MEMID';
            break;
        case 'livelihood':
            $table = 'tbl_livelihood';
            $id_field = 'LIVELIHOODID';
            break;
        case 'barangay':
            $table = 'tbl_barangay';
            $id_field = 'BRGY_ID';
            break;
        default:
            $_SESSION['error'] = "Invalid category type!";
            header('location: ' . $_SERVER['HTTP_REFERER']);
            exit();
    }

    try {
        // Begin transaction
        $conn->begin_transaction();

        // Check if record exists and is currently archived
        $check_sql = "SELECT STATUS FROM $table WHERE $id_field = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Record not found!");
        }

        $row = $result->fetch_assoc();
        if ($row['STATUS'] !== 'ARCHIVED') {
            throw new Exception("Record is not currently archived!");
        }

        // Update the record
        $sql = "UPDATE $table SET STATUS='ACTIVE', STATUS_REMARKS=? WHERE $id_field=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $STATUS_REMARKS, $id);

        if (!$stmt->execute()) {
            throw new Exception("Error executing update query: " . $conn->error);
        }

        // Commit transaction
        $conn->commit();
        $_SESSION['success'] = ucfirst($type) . " successfully unarchived.";

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Missing required parameters!";
}

// Redirect back to the appropriate page based on type and result
$redirect_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'archive.php';
if (isset($type)) {
    switch($type) {
        case 'barangay':
            $redirect_page = 'archive_barangays.php';
            break;
        case 'member':
            $redirect_page = 'archive_members.php';
            break;
    }
}

header('location: ' . $redirect_page);
$conn->close();
?>