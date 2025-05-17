$(function() {
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
        return date.toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        });
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
        $.get('get_messages.php', {
            last_id: lastMessageId
        }, function(response) {
            if (response.messages && response.messages.length > 0) {
                response.messages.forEach(message => {
                    if (message.id > lastMessageId) {
                        addMessage(message, message.sender_id === userId);
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

            $.post('send_message.php', {
                message: message
            }, function(response) {
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