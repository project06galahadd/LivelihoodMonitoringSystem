<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "includes/conn.php";

// Debug session variables
error_log("Session variables: " . print_r($_SESSION, true));

// Check if user is logged in and has admin role
if (!isset($_SESSION['admin']) || trim($_SESSION['admin']) == '') {
    header('Location: signin.php');
    exit();
}

// Get admin user info
$sql = "SELECT * FROM tbl_users WHERE ID = '".$_SESSION['admin']."'";
$query = $conn->query($sql);
if($query->num_rows > 0) {
    $user = $query->fetch_assoc();
    $user_id = $user['ID'];
    $username = $user['USERNAME'];
    $fullname = $user['LASTNAME'].', '.$user['FIRSTNAME'].' '.$user['MI'];
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
        .chat-container {
            height: calc(100vh - 200px);
            display: flex;
            flex-direction: column;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .message {
            margin-bottom: 15px;
            max-width: 70%;
        }
        .message.sent {
            margin-left: auto;
        }
        .message.received {
            margin-right: auto;
        }
        .message-content {
            padding: 10px 15px;
            border-radius: 15px;
            position: relative;
        }
        .sent .message-content {
            background: #007bff;
            color: white;
            border-top-right-radius: 5px;
        }
        .received .message-content {
            background: #e9ecef;
            color: #212529;
            border-top-left-radius: 5px;
        }
        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 5px;
        }
        .sent .message-time {
            text-align: right;
        }
        .chat-input {
            padding: 20px;
            background: white;
            border-top: 1px solid #dee2e6;
        }
        .member-list {
            height: calc(100vh - 200px);
            overflow-y: auto;
        }
        .member-item {
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .member-item:hover {
            background-color: #f8f9fa;
        }
        .member-item.active {
            background-color: #007bff;
            color: white;
        }
        .unread-badge {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            margin-left: 5px;
        }
        /* Prevent sidebar dragging */
        .main-sidebar {
            position: fixed !important;
            width: 250px !important;
            resize: none !important;
            user-select: none !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
        }
        .sidebar {
            width: 250px !important;
            resize: none !important;
        }
        /* Additional sidebar styling */
        .nav-sidebar .nav-item {
            margin-bottom: 5px;
        }
        .nav-sidebar .nav-link {
            padding: 12px 15px;
            color: #333;
            font-weight: 500;
        }
        .nav-sidebar .nav-link:hover {
            background-color: #f8f9fa;
        }
        .nav-sidebar .nav-link.active {
            background-color: #007bff;
            color: white;
        }
        .nav-sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include "includes/navbar.php"; ?>
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
                                        if ($member_result->num_rows > 0):
                                            while ($member = $member_result->fetch_assoc()): 
                                        ?>
                                            <div class="member-item" data-member-id="<?php echo $member['ID']; ?>">
                                                <?php echo htmlspecialchars($member['FIRSTNAME'] . ' ' . $member['LASTNAME']); ?>
                                                <?php if ($member['unread_count'] > 0): ?>
                                                    <span class="unread-badge"><?php echo $member['unread_count']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php 
                                            endwhile;
                                        else:
                                        ?>
                                            <div class="p-3 text-center">No members found</div>
                                        <?php endif; ?>
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
    $(document).ready(function() {
        console.log('Chat page loaded');
        
        // Prevent back button from going to dashboard
        window.history.pushState(null, '', window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, '', window.location.href);
        };
        
        // Allow dashboard navigation but prevent other unwanted redirects
        $(document).on('click', 'a', function(e) {
            const href = $(this).attr('href');
            if (href === 'home.php') {
                // Allow dashboard navigation
                return true;
            } else if (href === 'chat.php') {
                e.preventDefault();
                return false;
            }
        });
        
        let currentMemberId = null;
        let lastMessageId = 0;
        let messageCheckInterval;

        // Debug function
        function debug(message) {
            console.log(message);
        }

        // Check if member parameter is present in URL
        const urlParams = new URLSearchParams(window.location.search);
        const memberId = urlParams.get('member');
        debug('URL member ID: ' + memberId);

        if (memberId) {
            $(`.member-item[data-member-id="${memberId}"]`).click();
        }

        // Handle member selection
        $('.member-item').on('click', function() {
            debug('Member clicked: ' + $(this).data('member-id'));
            
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
            
            debug('Loading messages for member: ' + currentMemberId);
            
            $.get('get_messages.php', {
                member_id: currentMemberId,
                last_id: lastMessageId
            }, function(response) {
                debug('Messages response: ' + JSON.stringify(response));
                
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
                debug('Error loading messages: ' + textStatus + ' - ' + errorThrown);
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
                debug('Error checking new messages: ' + textStatus + ' - ' + errorThrown);
            });
        }

        // Append message to chat
        function appendMessage(message) {
            debug('Appending message: ' + JSON.stringify(message));
            
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
        $('#message-form').on('submit', function(e) {
            e.preventDefault();
            const message = $('#message-input').val().trim();
            if (!message || !currentMemberId) return;

            debug('Sending message to member: ' + currentMemberId);
            
            $.ajax({
                url: 'send_message.php',
                type: 'POST',
                data: {
                    receiver_id: currentMemberId,
                    message: message
                },
                dataType: 'json',
                success: function(response) {
                    debug('Send message response: ' + JSON.stringify(response));
                    
                    if (response && response.success) {
                        $('#message-input').val('');
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
                    debug('Error sending message: ' + textStatus + ' - ' + errorThrown);
                    alert('Error sending message. Please try again.');
                }
            });
        });
    });
    </script>
</body>
</html>
<?php
$member_stmt->close();
$conn->close();
?> 