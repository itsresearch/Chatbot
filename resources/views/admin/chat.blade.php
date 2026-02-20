@extends('layouts.admin')

@section('title', 'View Chat')

@section('extra-styles')
    <style>
        /* Chat Page Specific Styles */
        .chat-container {
            display: flex;
            height: calc(100vh - 130px);
            gap: 0;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .chat-sidebar {
            width: 35%;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
        }

        .chat-list-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .chat-list-header h6 {
            margin: 0;
            color: #1f2937;
            font-weight: 600;
        }

        .chat-list-search {
            margin-top: 15px;
        }

        .chat-list-search input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        .chat-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
        }

        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        @media (max-width: 992px) {
            .chat-container {
                height: auto;
            }

            .chat-sidebar {
                width: 100%;
                max-height: 350px;
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }

            .chat-main {
                height: 500px;
            }
        }

        @media (max-width: 768px) {
            .chat-list {
                display: none;
            }

            .chat-sidebar {
                display: none;
            }

            .chat-container {
                flex-direction: column;
                height: auto;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="mb-3">
        <h1 class="h3 mb-1" style="color: #1f2937; font-weight: 700;">Chat Conversation</h1>
        <p class="text-muted mb-0">View and manage visitor conversations.</p>
    </div>

    <!-- Chat Container -->
    <div class="chat-container">
        <!-- Left Sidebar: Chat List -->
        <div class="chat-sidebar">
            <!-- Header -->
            <div class="chat-list-header">
                <h6>Conversations</h6>
                <div class="chat-list-search">
                    <input type="text" class="form-control" placeholder="Search conversations...">
                </div>
            </div>

            <!-- Chat List -->
            <div class="chat-list">
                <!-- Chat Item 1 - Active -->
                <div class="chat-item active">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=John+Smith&background=667eea&color=fff" alt="John"
                            width="45" height="45" class="rounded-circle me-3">
                        <div style="flex: 1; min-width: 0;">
                            <div class="chat-item-name">John Smith</div>
                            <div class="chat-item-message">Can you help me track my order?</div>
                            <div class="chat-item-time">Today, 2:45 PM</div>
                        </div>
                    </div>
                </div>

                <!-- Chat Item 2 -->
                <div class="chat-item">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=Sarah+Johnson&background=764ba2&color=fff" alt="Sarah"
                            width="45" height="45" class="rounded-circle me-3">
                        <div style="flex: 1; min-width: 0;">
                            <div class="chat-item-name">Sarah Johnson</div>
                            <div class="chat-item-message">Thanks for your help!</div>
                            <div class="chat-item-time">Today, 1:30 PM</div>
                        </div>
                    </div>
                </div>

                <!-- Chat Item 3 -->
                <div class="chat-item">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=Mike+Davis&background=f59e0b&color=fff" alt="Mike"
                            width="45" height="45" class="rounded-circle me-3">
                        <div style="flex: 1; min-width: 0;">
                            <div class="chat-item-name">Mike Davis</div>
                            <div class="chat-item-message">I need technical support</div>
                            <div class="chat-item-time">Today, 12:15 PM</div>
                        </div>
                    </div>
                </div>

                <!-- Chat Item 4 -->
                <div class="chat-item">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=Emma+Wilson&background=10b981&color=fff" alt="Emma"
                            width="45" height="45" class="rounded-circle me-3">
                        <div style="flex: 1; min-width: 0;">
                            <div class="chat-item-name">Emma Wilson</div>
                            <div class="chat-item-message">Do you have this product in blue?</div>
                            <div class="chat-item-time">Yesterday, 4:20 PM</div>
                        </div>
                    </div>
                </div>

                <!-- Chat Item 5 -->
                <div class="chat-item">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=James+Brown&background=ef4444&color=fff" alt="James"
                            width="45" height="45" class="rounded-circle me-3">
                        <div style="flex: 1; min-width: 0;">
                            <div class="chat-item-name">James Brown</div>
                            <div class="chat-item-message">Billing inquiry for enterprise plan</div>
                            <div class="chat-item-time">Yesterday, 10:00 AM</div>
                        </div>
                    </div>
                </div>

                <!-- Chat Item 6 -->
                <div class="chat-item">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=Lisa+Taylor&background=0ea5e9&color=fff" alt="Lisa"
                            width="45" height="45" class="rounded-circle me-3">
                        <div style="flex: 1; min-width: 0;">
                            <div class="chat-item-name">Lisa Taylor</div>
                            <div class="chat-item-message">Feedback about your service</div>
                            <div class="chat-item-time">Feb 18, 3:45 PM</div>
                        </div>
                    </div>
                </div>

                <!-- Chat Item 7 -->
                <div class="chat-item">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=David+Miller&background=8b5cf6&color=fff" alt="David"
                            width="45" height="45" class="rounded-circle me-3">
                        <div style="flex: 1; min-width: 0;">
                            <div class="chat-item-name">David Miller</div>
                            <div class="chat-item-message">Return and refund process</div>
                            <div class="chat-item-time">Feb 17, 11:30 AM</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Chat Window -->
        <div class="chat-main">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="d-flex align-items-center" style="flex: 1;">
                    <img src="https://ui-avatars.com/api/?name=John+Smith&background=667eea&color=fff" alt="John"
                        width="40" height="40" class="rounded-circle me-3">
                    <div>
                        <h6 class="mb-0" style="color: #1f2937; font-weight: 600;">John Smith</h6>
                        <small class="text-success">
                            <i class="bi bi-circle-fill" style="font-size: 8px;"></i> Online
                        </small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-info-circle"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
            </div>

            <!-- Chat Messages Area -->
            <div class="chat-messages">
                <!-- Visitor Message 1 -->
                <div class="message visitor-message">
                    <div>
                        <div class="message-content">
                            Hi there! I need help with my recent order.
                        </div>
                        <div class="message-time">10:30 AM</div>
                    </div>
                </div>

                <!-- Admin Message 1 -->
                <div class="message admin-message">
                    <div>
                        <div class="message-content">
                            Hello John! I'd be happy to help. What can I assist you with?
                        </div>
                        <div class="message-time">10:31 AM</div>
                    </div>
                </div>

                <!-- Visitor Message 2 -->
                <div class="message visitor-message">
                    <div>
                        <div class="message-content">
                            I placed an order yesterday (Order #12345) and I haven't received any tracking information yet.
                        </div>
                        <div class="message-time">10:32 AM</div>
                    </div>
                </div>

                <!-- Admin Message 2 -->
                <div class="message admin-message">
                    <div>
                        <div class="message-content">
                            Let me look that up for you. One moment please...
                        </div>
                        <div class="message-time">10:33 AM</div>
                    </div>
                </div>

                <!-- Visitor Message 3 -->
                <div class="message visitor-message">
                    <div>
                        <div class="message-content">
                            Thank you for checking!
                        </div>
                        <div class="message-time">10:34 AM</div>
                    </div>
                </div>

                <!-- Admin Message 3 -->
                <div class="message admin-message">
                    <div>
                        <div class="message-content">
                            I found your order! It's been packed and is ready to ship. Your tracking number is
                            TRACK123456789.
                        </div>
                        <div class="message-time">10:35 AM</div>
                    </div>
                </div>

                <!-- Admin Message 4 -->
                <div class="message admin-message">
                    <div>
                        <div class="message-content">
                            You should receive it within 2-3 business days. Is there anything else I can help with?
                        </div>
                        <div class="message-time">10:36 AM</div>
                    </div>
                </div>

                <!-- Visitor Message 4 -->
                <div class="message visitor-message">
                    <div>
                        <div class="message-content">
                            Perfect! Thank you so much for your help!
                        </div>
                        <div class="message-time">10:37 AM</div>
                    </div>
                </div>

                <!-- Admin Message 5 -->
                <div class="message admin-message">
                    <div>
                        <div class="message-content">
                            You're very welcome! If you have any other questions, feel free to reach out. Have a great day!
                        </div>
                        <div class="message-time">10:38 AM</div>
                    </div>
                </div>

                <!-- Visitor Message 5 -->
                <div class="message visitor-message">
                    <div>
                        <div class="message-content">
                            Will do! 😊
                        </div>
                        <div class="message-time">10:39 AM</div>
                    </div>
                </div>
            </div>

            <!-- Chat Input Area -->
            <div class="chat-input-area">
                <input type="text" class="form-control" placeholder="Type your message...">
                <button class="btn btn-primary">
                    <i class="bi bi-send"></i> Send
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Auto-scroll to bottom of messages
        const chatMessages = document.querySelector('.chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Chat item click handler
        document.querySelectorAll('.chat-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Send message functionality (demo)
        const sendBtn = document.querySelector('.btn-primary');
        const messageInput = document.querySelector('.chat-input-area input');

        if (sendBtn && messageInput) {
            sendBtn.addEventListener('click', function() {
                const message = messageInput.value.trim();
                if (message) {
                    // Create new admin message
                    const newMessage = document.createElement('div');
                    newMessage.className = 'message admin-message';
                    const time = new Date().toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    newMessage.innerHTML = `
                        <div>
                            <div class="message-content">${message}</div>
                            <div class="message-time">${time}</div>
                        </div>
                    `;
                    chatMessages.appendChild(newMessage);
                    messageInput.value = '';
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            });

            // Send message on Enter key
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendBtn.click();
                }
            });
        }
    </script>
@endsection
