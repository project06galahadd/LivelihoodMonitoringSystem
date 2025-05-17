<?php
include "includes/header.php";
include "includes/navbar.php";
include "includes/sidebar.php";

// Function to get chat messages between users
function getChatMessages($conn, $sender_id, $receiver_id, $limit = 50)
{
    // Sanitize inputs to prevent SQL injection
    $sender_id = $conn->real_escape_string($sender_id);
    $receiver_id = $conn->real_escape_string($receiver_id);
    $limit = (int)$limit;

    // Query to get messages between two users, ordered by timestamp
    $sql = "SELECT * FROM tbl_chat_messages"
        . " WHERE (sender_id = '$sender_id' AND receiver_id = '$receiver_id')"
        . " OR (sender_id = '$receiver_id' AND receiver_id = '$sender_id')"
        . " ORDER BY timestamp DESC LIMIT $limit";

    $result = $conn->query($sql);

    $messages = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }

    // Reverse to show oldest messages first
    return array_reverse($messages);
}

// Function to send a new chat message
function sendChatMessage($conn, $sender_id, $receiver_id, $message)
{
    // Sanitize inputs
    $sender_id = $conn->real_escape_string($sender_id);
    $receiver_id = $conn->real_escape_string($receiver_id);
    $message = $conn->real_escape_string($message);
    $timestamp = date('Y-m-d H:i:s');

    $sql = "INSERT INTO tbl_chat_messages (sender_id, receiver_id, message, timestamp, status)"
        . " VALUES ('$sender_id', '$receiver_id', '$message', '$timestamp', 'UNREAD')";

    if ($conn->query($sql)) {
        return true;
    }
    return false;
}

// Function to mark messages as read
function markMessagesAsRead($conn, $sender_id, $receiver_id)
{
    // Sanitize inputs
    $sender_id = $conn->real_escape_string($sender_id);
    $receiver_id = $conn->real_escape_string($receiver_id);

    $sql = "UPDATE tbl_chat_messages"
        . " SET status = 'READ'"
        . " WHERE sender_id = '$sender_id'"
        . " AND receiver_id = '$receiver_id'"
        . " AND status = 'UNREAD'";

    return $conn->query($sql);
}

// Function to get unread message count
function getUnreadMessageCount($conn, $user_id)
{
    $user_id = $conn->real_escape_string($user_id);

    $sql = "SELECT COUNT(*) AS total FROM tbl_chat_messages WHERE receiver_id = '$user_id' AND status = 'UNREAD'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Debug session variables
error_log("Session variables: " . print_r($_SESSION, true));

// Check if user is logged in and has admin role
if (!isset($_SESSION['admin']) || trim($_SESSION['admin']) == '') {
    header('Location: signin.php');
    exit();
}

// Get admin user info
$sql = "SELECT * FROM tbl_users WHERE ID = '" . $_SESSION['admin'] . "'";
$query = $conn->query($sql);
if ($query->num_rows > 0) {
    $user = $query->fetch_assoc();
    $user_id = $user['ID'];
    $username = $user['USERNAME'];
    $fullname = $user['LASTNAME'] . ', ' . $user['FIRSTNAME'] . ' ' . $user['MI'];
} else {
    header('Location: signin.php');
    exit();
}

// Prevent caching and force reload
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Set a session variable to indicate we're on the chat page
$_SESSION['current_page'] = 'chat';

// Get all members with their unread message count
$member_query = "SELECT u.ID, u.FIRSTNAME, u.LASTNAME, 
                (SELECT COUNT(*) FROM tbl_chat_messages 
                 WHERE sender_id = u.ID 
                 AND receiver_id = ? 
                 AND is_read = 0) as unread_count 
                FROM tbl_users u 
                WHERE u.ROLE = 'MEMBER' 
                ORDER BY u.LASTNAME ASC, u.FIRSTNAME ASC";
$member_stmt = $conn->prepare($member_query);
$member_stmt->bind_param("i", $user_id);
$member_stmt->execute();
$member_result = $member_stmt->get_result();

// Debug member data
error_log("Member count: " . $member_result->num_rows);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Chat | MSWD</title>
    <link rel="icon" type="image/png" href="/LivelihoodMonitoringSystem/dist/img/Logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/LivelihoodMonitoringSystem/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/LivelihoodMonitoringSystem/dist/css/adminlte.min.css">
    <style>
        /* Base styles */
        body {
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #f4f6f9;
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        /* Sidebar styles */
        .main-sidebar {
            position: fixed !important;
            width: 250px !important;
            height: 100vh !important;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            z-index: 1000 !important;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1) !important;
            transform: translateX(0) !important;
            will-change: transform !important;
            backface-visibility: hidden !important;
            background-color: #343a40 !important;
        }

        .sidebar-collapse .main-sidebar {
            transform: translateX(-250px) !important;
        }

        .sidebar {
            width: 250px !important;
            overflow-y: auto !important;
            scrollbar-width: thin !important;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px !important;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2) !important;
            border-radius: 5px !important;
        }

        /* Navbar styles */
        .main-header {
            position: fixed !important;
            width: 100% !important;
            z-index: 1001 !important;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1) !important;
            transform: translateX(0) !important;
            will-change: transform !important;
            backface-visibility: hidden !important;
            background-color: #343a40 !important;
        }

        .main-header .navbar {
            padding: 0.5rem 1rem !important;
        }

        .main-header .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.3s ease !important;
            font-weight: 500 !important;
            letter-spacing: 0.3px !important;
        }

        .main-header .navbar-nav .nav-link:hover {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.1) !important;
            transform: translateY(-1px) !important;
        }

        /* Content wrapper */
        .content-wrapper {
            margin-left: 250px !important;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            min-height: 100vh !important;
            padding-top: 60px !important;
            will-change: margin-left !important;
            background-color: #f4f6f9 !important;
        }

        .sidebar-collapse .content-wrapper {
            margin-left: 0 !important;
        }

        /* Chat container adjustments */
        .chat-container {
            height: calc(100vh - 140px) !important;
            margin-top: 10px !important;
            background-color: #fff !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2) !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .member-list {
            height: calc(100vh - 140px) !important;
            background-color: #fff !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2) !important;
            overflow-y: auto !important;
        }

        /* Card styles */
        .card {
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2) !important;
            border: none !important;
            border-radius: 0.5rem !important;
            overflow: hidden !important;
        }

        .card-header {
            background-color: #fff !important;
            border-bottom: 1px solid rgba(0, 0, 0, .125) !important;
            padding: 1rem 1.25rem !important;
        }

        .card-title {
            margin-bottom: 0 !important;
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: #343a40 !important;
        }

        /* Message styles */
        .chat-messages {
            flex: 1 !important;
            overflow-y: auto !important;
            padding: 1.5rem !important;
            background: #f8f9fa !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }

        .message {
            margin-bottom: 0 !important;
            max-width: 70%;
            animation: fadeIn 0.3s ease-in-out;
            position: relative;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.sent {
            margin-left: auto;
        }

        .message.received {
            margin-right: auto;
        }

        .message-content {
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            line-height: 1.4;
            font-size: 0.95rem;
        }

        .sent .message-content {
            background: #007bff;
            color: white;
            border-top-right-radius: 5px;
        }

        .received .message-content {
            background: #f8f9fa;
            color: #212529;
            border-top-left-radius: 5px;
        }

        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 4px;
            padding: 0 4px;
        }

        .sent .message-time {
            text-align: right;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Member list styles */
        .member-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f4f6f9;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.95rem;
        }

        .member-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .member-item.active {
            background-color: #e9ecef;
            color: #343a40;
            font-weight: 600;
            border-left: 3px solid #007bff;
        }

        .unread-badge {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            margin-left: 5px;
            animation: pulse 2s infinite;
            font-weight: 600;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Chat input styles */
        .chat-input {
            padding: 1rem;
            background: #fff;
            border-top: 1px solid #dee2e6;
        }

        .chat-input .form-control {
            border-radius: 20px;
            padding: 12px 20px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .chat-input .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .chat-input .btn {
            border-radius: 50%;
            width: 42px;
            height: 42px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background-color: #007bff;
            border-color: #007bff;
        }

        .chat-input .btn:hover {
            transform: scale(1.1);
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .chat-input .btn:active {
            transform: scale(0.95);
        }

        /* Scrollbar styles */
        .chat-messages::-webkit-scrollbar,
        .member-list::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track,
        .member-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb,
        .member-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover,
        .member-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-sidebar {
                transform: translateX(-250px) !important;
            }

            .sidebar-open .main-sidebar {
                transform: translateX(0) !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
            }

            .sidebar-open .content-wrapper {
                margin-left: 250px !important;
            }

            .main-header {
                transform: translateX(0) !important;
            }

            .sidebar-open .main-header {
                transform: translateX(250px) !important;
            }

            .message {
                max-width: 85%;
            }

            .chat-input .form-control {
                font-size: 16px;
                /* Prevent zoom on iOS */
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark sticky-top" style="border:none;background:#343a40">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link">LIVELIHOOD MONITORING SYSTEM</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Full screen toggle -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
                <!-- User dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle mr-1"></i>
                        <?php echo $fullname; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <?php include "includes/sidebar.php"; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 font-weight-bold text-uppercase">Chat</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Member List -->
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Members</h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="member-list">
                                        <?php
                                        if ($member_result->num_rows > 0) {
                                            while ($member = $member_result->fetch_assoc()) {
                                        ?>
                                                <div class="member-item" data-member-id="<?php echo $member['ID']; ?>">
                                                    <?php echo htmlspecialchars($member['FIRSTNAME'] . ' ' . $member['LASTNAME']); ?>
                                                    <?php if ($member['unread_count'] > 0): ?>
                                                        <span class="unread-badge"><?php echo $member['unread_count']; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <div class="p-3 text-center">No members found</div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Area -->
                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title" id="chat-title">Select a member to start chatting</h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="chat-container">
                                        <div class="chat-messages" id="chat-messages">
                                            <!-- Messages will be loaded here -->
                                        </div>
                                        <div class="chat-input">
                                            <form id="message-form" class="d-flex">
                                                <input type="hidden" id="receiver-id" name="receiver_id">
                                                <input type="text" class="form-control mr-2" id="message-input" placeholder="Type your message..." disabled>
                                                <button type="submit" class="btn btn-primary" disabled>
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- jQuery -->
    <script src="/LivelihoodMonitoringSystem/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/LivelihoodMonitoringSystem/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/LivelihoodMonitoringSystem/dist/js/adminlte.min.js"></script>

    <script>
        $(function() {
            // Initialize AdminLTE components
            $('[data-widget="pushmenu"]').PushMenu('init');
            $('[data-widget="treeview"]').Treeview('init');

            // Initialize sidebar toggle with hamburger icon
            $('.fa-bars, .sidebar-toggle').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('body').toggleClass('sidebar-collapse');
                $('.main-sidebar').toggleClass('sidebar-collapse');
            });

            // Handle sidebar menu clicks
            $('.nav-sidebar .nav-item.has-treeview > .nav-link').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $parent = $(this).parent();
                var $treeview = $parent.find('> .nav-treeview');
                // Close other open menus
                $('.nav-sidebar .nav-item.has-treeview').not($parent).removeClass('menu-open').find('> .nav-treeview').slideUp(200);
                // Toggle current menu
                $parent.toggleClass('menu-open');
                $treeview.slideToggle(200);
            });

            // Handle regular menu item clicks
            $('.nav-sidebar .nav-item:not(.has-treeview) > .nav-link').on('click', function(e) {
                $('.nav-sidebar .nav-link').removeClass('active');
                $(this).addClass('active');
            });

            // Handle submenu item clicks
            $('.nav-sidebar .nav-treeview .nav-link').on('click', function(e) {
                e.stopPropagation();
                $('.nav-sidebar .nav-link').removeClass('active');
                $(this).addClass('active');
            });

            // Initialize active state based on current page
            var currentPage = window.location.pathname.split('/').pop();
            $('.nav-sidebar .nav-link').each(function() {
                var href = $(this).attr('href');
                if (href && href.includes(currentPage)) {
                    $(this).addClass('active');
                    $(this).parents('.has-treeview').addClass('menu-open');
                    $(this).parents('.nav-treeview').show();
                }
            });

            // Fix for menu items not being clickable
            $('.nav-sidebar .nav-link, .nav-sidebar .nav-item, .nav-sidebar .nav-treeview').css({
                'pointer-events': 'auto',
                'cursor': 'pointer'
            });

            // Ensure proper event bubbling
            $('.nav-sidebar .nav-treeview').on('click', function(e) {
                e.stopPropagation();
            });

            // Fix for menu open state persistence
            $('.nav-sidebar .nav-item.has-treeview').each(function() {
                if ($(this).find('.nav-link.active').length) {
                    $(this).addClass('menu-open');
                    $(this).find('> .nav-treeview').show();
                }
            });

            // Handle dropdown menus
            $('.dropdown-toggle').dropdown();
            $('.nav-item.dropdown').on('show.bs.dropdown', function() {
                $(this).find('.dropdown-menu').first().stop(true, true).slideDown(200);
            });
            $('.nav-item.dropdown').on('hide.bs.dropdown', function() {
                $(this).find('.dropdown-menu').first().stop(true, true).slideUp(200);
            });

            // Handle sidebar toggle with synchronized transitions
            $('[data-widget="pushmenu"]').on('click', function(e) {
                e.preventDefault();
                const isCollapsed = $('body').hasClass('sidebar-collapse');

                // Prevent multiple clicks during transition
                if ($('.main-sidebar').is(':animated')) return;

                // Toggle classes with synchronized timing
                $('body').toggleClass('sidebar-collapse');

                // Use requestAnimationFrame for smoother transitions
                requestAnimationFrame(() => {
                    if (isCollapsed) {
                        $('.main-sidebar').css('transform', 'translateX(0)');
                        $('.content-wrapper').css('margin-left', '250px');
                        $('.main-header').css('transform', 'translateX(0)');
                    } else {
                        $('.main-sidebar').css('transform', 'translateX(-250px)');
                        $('.content-wrapper').css('margin-left', '0');
                        $('.main-header').css('transform', 'translateX(0)');
                    }
                });
            });

            // Handle window resize with synchronized transitions
            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const width = $(window).width();
                    const isMobile = width <= 768;

                    // Prevent transition during resize
                    $('.main-sidebar, .content-wrapper, .main-header').css('transition', 'none');

                    if (isMobile) {
                        $('body').addClass('sidebar-collapse');
                        $('.main-sidebar').css('transform', 'translateX(-250px)');
                        $('.content-wrapper').css('margin-left', '0');
                        $('.main-header').css('transform', 'translateX(0)');
                    } else {
                        $('body').removeClass('sidebar-collapse');
                        $('.main-sidebar').css('transform', 'translateX(0)');
                        $('.content-wrapper').css('margin-left', '250px');
                        $('.main-header').css('transform', 'translateX(0)');
                    }

                    // Re-enable transitions after resize
                    requestAnimationFrame(() => {
                        $('.main-sidebar, .content-wrapper, .main-header').css('transition', '');
                    });
                }, 250); // Debounce resize events
            });

            // Initialize responsive state
            handleResponsive();

            // Chat functionality
            const messageForm = $('#message-form');
            const messageInput = $('#message-input');
            const sendButton = $('button[type="submit"]');
            let currentMemberId = null;
            let lastMessageId = 0;
            let messageCheckInterval;

            // Member click handler
            $('.member-item').on('click', function() {
                $('.member-item').removeClass('active');
                $(this).addClass('active');

                currentMemberId = $(this).data('member-id');
                $('#receiver-id').val(currentMemberId);
                $('#message-input, button[type="submit"]').prop('disabled', false);
                $('#chat-title').text($(this).text().trim());

                // Clear and load messages
                $('#chat-messages').empty();
                lastMessageId = 0;
                loadMessages();

                // Start checking for new messages
                if (messageCheckInterval) {
                    clearInterval(messageCheckInterval);
                }
                messageCheckInterval = setInterval(checkNewMessages, 3000);
            });

            // Load messages
            function loadMessages() {
                if (!currentMemberId) return;

                $.get('get_messages.php', {
                    member_id: currentMemberId,
                    last_id: lastMessageId
                }, function(response) {
                    if (response.success) {
                        response.messages.forEach(function(message) {
                            appendMessage(message);
                            if (message.message_id > lastMessageId) {
                                lastMessageId = message.message_id;
                            }
                        });
                        scrollToBottom();
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Error loading messages:', textStatus, errorThrown);
                });
            }

            // Check for new messages
            function checkNewMessages() {
                if (!currentMemberId) return;

                $.get('get_messages.php', {
                    member_id: currentMemberId,
                    last_id: lastMessageId
                }, function(response) {
                    if (response.success) {
                        let hasNewMessages = false;
                        response.messages.forEach(function(message) {
                            if (message.message_id > lastMessageId) {
                                appendMessage(message);
                                lastMessageId = message.message_id;
                                hasNewMessages = true;
                            }
                        });
                        if (hasNewMessages) {
                            scrollToBottom();
                        }
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Error checking new messages:', textStatus, errorThrown);
                });
            }

            // Append message to chat
            function appendMessage(message) {
                const isSent = message.sender_id == <?php echo $user_id; ?>;
                const messageHtml = `
                    <div class="message ${isSent ? 'sent' : 'received'}">
                        <div class="message-content">${message.message}</div>
                        <div class="message-time">${message.created_at}</div>
                    </div>
                `;
                $('#chat-messages').append(messageHtml);
            }

            // Scroll to bottom of chat
            function scrollToBottom() {
                const chatMessages = document.getElementById('chat-messages');
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Handle message submission
            messageForm.on('submit', function(e) {
                e.preventDefault();
                const message = messageInput.val().trim();
                if (!message || !currentMemberId) return;

                $.ajax({
                    url: 'send_message.php',
                    type: 'POST',
                    data: {
                        receiver_id: currentMemberId,
                        message: message
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.success) {
                            messageInput.val('');
                            if (response.message) {
                                appendMessage(response.message);
                                scrollToBottom();
                            }
                        } else {
                            alert('Error sending message: ' + (response ? response.message : 'Unknown error'));
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error sending message:', textStatus, errorThrown);
                        alert('Error sending message. Please try again.');
                    }
                });
            });

            // Handle responsive behavior
            function handleResponsive() {
                const width = $(window).width();
                const isMobile = width <= 768;

                // Prevent transition during initial load
                $('.main-sidebar, .content-wrapper, .main-header').css('transition', 'none');

                if (isMobile) {
                    $('body').addClass('sidebar-collapse');
                    $('.main-sidebar').css('transform', 'translateX(-250px)');
                    $('.content-wrapper').css('margin-left', '0');
                    $('.main-header').css('transform', 'translateX(0)');
                } else {
                    $('body').removeClass('sidebar-collapse');
                    $('.main-sidebar').css('transform', 'translateX(0)');
                    $('.content-wrapper').css('margin-left', '250px');
                    $('.main-header').css('transform', 'translateX(0)');
                }

                // Re-enable transitions after initial load
                requestAnimationFrame(() => {
                    $('.main-sidebar, .content-wrapper, .main-header').css('transition', '');
                });
            }
        });
    </script>
</body>

</html>
<?php
if (isset($member_stmt)) {
    $member_stmt->close();
}
if (isset($conn)) {
    $conn->close();
}
?>