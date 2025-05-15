<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start logging
error_log("Starting signin_process.php");

session_start();
require_once "../wp_admin/includes/conn.php";

// Log POST data (excluding password)
error_log("POST data received: " . print_r(['username' => $_POST['username'] ?? 'not set'], true));

// Check if database connection is successful
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

error_log("Database connection successful");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Processing POST request");

    $usernameOrEmail = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        // First try the test account
        if ($usernameOrEmail === 'user' && $password === 'member') {
            error_log("Test account login attempt");

            // Check if test user exists in database
            $check_test_user = "SELECT ID FROM tbl_users WHERE username = 'user' AND role = 'MEMBER'";
            $test_result = $conn->query($check_test_user);

            if ($test_result->num_rows === 0) {
                // Create test user if doesn't exist
                $create_test_user = "INSERT INTO tbl_users (username, password, firstname, lastname, role, ACC_STATUS) 
                                   VALUES ('user', ?, 'Test', 'User', 'MEMBER', 1)";
                $hashed_password = password_hash('member', PASSWORD_DEFAULT);
                $create_stmt = $conn->prepare($create_test_user);
                $create_stmt->bind_param("s", $hashed_password);
                $create_stmt->execute();

                $user_id = $conn->insert_id;
            } else {
                $user_id = $test_result->fetch_assoc()['ID'];
            }

            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = 'user';
            $_SESSION['role'] = 'MEMBER';
            $_SESSION['fullname'] = 'Test User';

            error_log("Test account session set: " . print_r($_SESSION, true));

            try {
                // Check if tbl_activity_log exists
                $result = $conn->query("SHOW TABLES LIKE 'tbl_activity_log'");
                if ($result->num_rows > 0) {
                    error_log("Logging activity for test account");
                    $sql = "INSERT INTO tbl_activity_log (user_id, activity, status, created_at) VALUES (?, 'User logged in', 'completed', NOW())";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $_SESSION['user_id']);
                    $stmt->execute();
                }
            } catch (Exception $e) {
                error_log("Failed to log activity: " . $e->getMessage());
            }

            error_log("Redirecting to home.php");
            header("Location: home.php");
            exit();
        }

        // Regular user login
        $stmt = $conn->prepare("SELECT id, username, password, role, CONCAT(firstname, ' ', lastname) as fullname FROM tbl_users WHERE username = ? AND ACC_STATUS = 1 LIMIT 1");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param('s', $usernameOrEmail);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                error_log("Password verified successfully");
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['fullname'] = $user['fullname'] ?? $user['username'];

                error_log("Session data set: " . print_r($_SESSION, true));

                try {
                    // Check if tbl_activity_log exists
                    $result = $conn->query("SHOW TABLES LIKE 'tbl_activity_log'");
                    if ($result->num_rows > 0) {
                        error_log("Logging activity");
                        $sql = "INSERT INTO tbl_activity_log (user_id, activity, status, created_at) VALUES (?, 'User logged in', 'completed', NOW())";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('i', $user['id']);
                        $stmt->execute();
                    }
                } catch (Exception $e) {
                    error_log("Failed to log activity: " . $e->getMessage());
                }

                error_log("Redirecting to home.php");
                header("Location: home.php");
                exit();
            } else {
                error_log("Password verification failed");
            }
        } else {
            error_log("No user found or account inactive");
        }

        // If login fails
        $_SESSION['error'] = 'Invalid username/email or password.';
        error_log("Login failed, redirecting back to signin.php");
        header('Location: signin.php');
        exit();
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['error'] = 'An error occurred during login. Please try again.';
        header('Location: signin.php');
        exit();
    }
} else {
    error_log("Non-POST request, redirecting to signin.php");
    header('Location: signin.php');
    exit();
}
