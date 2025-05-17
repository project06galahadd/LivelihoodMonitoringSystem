<?php
session_start();
require_once "../wp_admin/includes/conn.php";

// Check if user is logged in and has member role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'MEMBER') {
    header('Location: signin.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$fullname = $_SESSION['fullname'];

// Get all admins
$admin_query = "SELECT ID, FIRSTNAME, LASTNAME FROM tbl_users WHERE ROLE = 'ADMIN' AND ACC_STATUS = 1";
$admin_result = $conn->query($admin_query);

// Debug information
error_log("User ID: " . $user_id);
error_log("Username: " . $username);
error_log("Fullname: " . $fullname);
error_log("Number of admins found: " . $admin_result->num_rows);
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
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .brand-link {
            border-bottom: 1px solid rgba(255,255,255,0.1);
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
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 1.5rem 1rem;
            background: rgba(255,255,255,0.05);
        }

        .user-panel .image img {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(255,255,255,0.2);
        }

        .user-panel .info a {
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-panel .info small {
            color: rgba(255,255,255,0.7);
        }

        .nav-sidebar .nav-item {
            margin: 5px 10px;
        }

        .nav-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .nav-sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .nav-sidebar .nav-link.active {
            background: var(--secondary-color);
            color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .main-header .nav-link {
            color: var(--text-color) !important;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .message-time {
            font-size: 0.75rem;
            color: rgba(0,0,0,0.5);
            margin-top: 0.25rem;
        }

        .message.sent .message-time {
            color: rgba(255,255,255,0.8);
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
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                        <i class="fas fa-user-circle mr-2"></i><?php echo htmlspecialchars($fullname); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user mr-2"></i>Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="home.php" class="brand-link text-center">
                <img src="/LivelihoodMonitoringSystem/dist/img/Logo.png" alt="MSWD Logo" class="brand-image img-circle elevation-3">
                <span class="brand-text font-weight-light">MSWD Member</span>
            </a>

            <!-- Profile Section -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="<?php echo !empty($user_data['profile_picture']) ? '../uploads/profile/' . $user_data['profile_picture'] : '/LivelihoodMonitoringSystem/dist/img/default-avatar.png'; ?>" 
                         class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="profile.php" class="d-block">
                        <?php echo htmlspecialchars($fullname); ?>
                    </a>
                    <small>
                        <i class="fas fa-circle text-success"></i> Online
                    </small>
                </div>
            </div>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="home.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-home"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="livelihood.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'livelihood.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>Livelihood Records</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="household_case.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'household_case.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Household Case Records</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="news.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-newspaper"></i>
                                <p>News & Announcements</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="chat.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-comments"></i>
                                <p>Chat with Admin</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>Profile Settings</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

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
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
    <script>
        $(function () {
            // Initialize sidebar toggle
            $('[data-widget="pushmenu"]').PushMenu('collapse');
            
            // Initialize dropdowns
            $('.dropdown-toggle').dropdown();
            
            // Initialize treeview with accordion disabled
            $('[data-widget="treeview"]').Treeview('init', {
                accordion: false
            });

            // Handle sidebar menu clicks
            $('.nav-sidebar .nav-link').on('click', function(e) {
                $('.nav-sidebar .nav-link').removeClass('active');
                $(this).addClass('active');
            });

            // Chat functionality
            const chatMessages = $('#chat-messages');
            const messageForm = $('#message-form');
            const messageInput = $('#message');
            const sendButton = $('#send-button');
            let lastMessageId = 0;

            function formatMessageTime(timestamp) {
                const date = new Date(timestamp);
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            function addMessage(message, isSent = true) {
                const messageHtml = `
                    <div class="message ${isSent ? 'sent' : 'received'}">
                        <div class="message-content">
                            ${message.message}
                            <div class="message-time">${formatMessageTime(message.timestamp)}</div>
                        </div>
                    </div>
                `;
                chatMessages.append(messageHtml);
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            }

            function loadMessages() {
                $.get('get_messages.php', { last_id: lastMessageId }, function(response) {
                    if (response.messages && response.messages.length > 0) {
                        response.messages.forEach(message => {
                            if (message.id > lastMessageId) {
                                addMessage(message, message.sender_id === <?php echo $user_id; ?>);
                                lastMessageId = message.id;
                            }
                        });
                    }
                });
            }

            // Load messages every 3 seconds
            setInterval(loadMessages, 3000);
            loadMessages();

            messageForm.on('submit', function(e) {
                e.preventDefault();
                const message = messageInput.val().trim();
                
                if (message) {
                    sendButton.prop('disabled', true);
                    
                    $.post('send_message.php', { message: message }, function(response) {
                        if (response.success) {
                            messageInput.val('');
                            addMessage({
                                message: message,
                                timestamp: new Date().toISOString()
                            }, true);
                        }
                        sendButton.prop('disabled', false);
                    });
                }
            });

            // Auto-resize textarea
            messageInput.on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Handle Enter key
            messageInput.on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    messageForm.submit();
                }
            });
        });
    </script>
</body>
</html> 