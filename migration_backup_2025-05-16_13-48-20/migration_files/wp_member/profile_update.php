<?php
include 'includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate current password
    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['error'] = 'Current password is incorrect.';
        header('Location: profile.php');
        exit();
    }

    // Validate new password if provided
    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = 'New passwords do not match.';
            header('Location: profile.php');
            exit();
        }
        if (strlen($new_password) < 6) {
            $_SESSION['error'] = 'New password must be at least 6 characters long.';
            header('Location: profile.php');
            exit();
        }
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Update user information
        $sql = "UPDATE tbl_users SET username = ?, email = ?";
        $params = [$username, $email];
        $types = "ss";

        // Add password update if new password provided
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql .= ", password = ?";
            $params[] = $hashed_password;
            $types .= "s";
        }

        $sql .= " WHERE id = ?";
        $params[] = $user_id;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            // Log the activity
            $activity_sql = "INSERT INTO tbl_activity_log (user_id, activity, status) VALUES (?, 'Profile updated', 'completed')";
            $activity_stmt = $conn->prepare($activity_sql);
            $activity_stmt->bind_param('i', $user_id);
            $activity_stmt->execute();

            $conn->commit();
            $_SESSION['success'] = 'Profile updated successfully.';
            $_SESSION['username'] = $username; // Update session username
        } else {
            throw new Exception("Error updating profile.");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = 'An error occurred while updating your profile.';
    }

    header('Location: profile.php');
    exit();
} else {
    header('Location: profile.php');
    exit();
} 