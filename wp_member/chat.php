<?php
session_start();
require_once "../wp_admin/includes/conn.php";

// Check if user is logged in and has member role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'MEMBER') {
    header('Location: signin.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];
// Get user profile data
$user_data = null;
try {
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
} catch (Exception $e) { $user_data = null; }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat | MSWD</title>
    <link rel="icon" type="image/png" href="/LivelihoodMonitoringSystem/dist/img/Logo.png">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="/LivelihoodMonitoringSystem/dist/css/chat.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
        }

        body {
            font-family: 'Source Sans Pro', sans-serif;
            background: var(--light-bg);
            position: relative;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .main-sidebar {
            background: var(--primary-color);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .brand-link {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff !important;
            background: var(--primary-color);
            padding: 1rem;
        }

        .brand-link img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .user-panel {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
        }

        .user-panel .image img {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        .user-panel .info a {
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-panel .info small {
            color: rgba(255, 255, 255, 0.7);
        }

        .nav-sidebar .nav-item {
            margin: 5px 10px;
        }

        .nav-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .nav-sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .nav-sidebar .nav-link.active {
            background: var(--secondary-color);
            color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .nav-sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Header Styles */
        .main-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .main-header .nav-link {
            color: var(--text-color) !important;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: var(--secondary-color);
            color: #fff;
        }

        /* Content Styles */
        .content-wrapper {
            background: var(--light-bg);
        }

        .content-header {
            padding: 1.5rem 1rem;
            background: transparent;
        }

        .content-header h1 {
            color: var(--text-color);
            font-weight: 600;
            font-size: 1.8rem;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: var(--secondary-color);
        }

        .breadcrumb-item.active {
            color: var(--text-color);
        }

        /* Chat Styles */
        .chat-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            height: calc(100vh - 250px);
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            background: #fff;
            border-radius: 10px 10px 0 0;
        }

        .chat-header h3 {
            margin: 0;
            color: var(--text-color);
            font-weight: 600;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            background: #f8f9fa;
        }

        .message {
            margin-bottom: 1rem;
            max-width: 80%;
        }

        .message-content {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            position: relative;
        }

        .message.sent {
            margin-left: auto;
        }

        .message.sent .message-content {
            background: var(--secondary-color);
            color: #fff;
        }

        .message.received .message-content {
            background: #fff;
            color: var(--text-color);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .message-time {
            font-size: 0.75rem;
            color: rgba(0, 0, 0, 0.5);
            margin-top: 0.25rem;
        }

        .message.sent .message-time {
            color: rgba(255, 255, 255, 0.8);
        }

        .chat-input {
            padding: 1rem;
            background: #fff;
            border-top: 1px solid #eee;
            border-radius: 0 0 10px 10px;
        }

        .chat-input form {
            display: flex;
            gap: 1rem;
        }

        .chat-input textarea {
            flex: 1;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 0.75rem;
            resize: none;
            height: 60px;
            transition: all 0.3s ease;
        }

        .chat-input textarea:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            outline: none;
        }

        .chat-input button {
            background: var(--secondary-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .chat-input button:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .chat-input button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .status-badge.online {
            background: var(--success-color);
            color: #fff;
        }

        .status-badge.offline {
            background: var(--danger-color);
            color: #fff;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .main-sidebar {
                transform: translateX(-100%);
            }

            .sidebar-open .main-sidebar {
                transform: translateX(0);
            }

            .content-wrapper {
                margin-left: 0;
            }

            .chat-container {
                height: calc(100vh - 200px);
            }

            .message {
                max-width: 90%;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'includes/navbar.php'; ?>

        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Chat with Admin</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                                <li class="breadcrumb-item active">Chat</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="chat-container">
                        <div class="chat-header">
                            <h3>
                                <i class="fas fa-comments mr-2"></i>
                                Chat with Admin
                                <span class="status-badge online">Online</span>
                            </h3>
                        </div>
                        <div class="chat-messages" id="chat-messages">
                            <!-- Messages will be loaded here -->
                        </div>
                        <div class="chat-input">
                            <form id="message-form">
                                <textarea id="message" placeholder="Type your message here..." required></textarea>
                                <button type="submit" id="send-button">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include "includes/footer.php"; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
    <script src="/LivelihoodMonitoringSystem/dist/js/chat.js"></script>
</body>

</html>