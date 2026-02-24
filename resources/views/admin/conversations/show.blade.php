@extends('layouts.admin')

@section('title', 'Conversation Details')

@section('content')
    @php
        $activeVisitorLabel = $conversation->visitor
            ? 'Visitor ' . substr($conversation->visitor->visitor_token, 0, 8)
            : 'Unknown visitor';
        $activeAvatarName = urlencode($activeVisitorLabel);
    @endphp

    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col d-flex align-items-center justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-1">
                        <a href="{{ route('admin.conversations') }}" class="text-decoration-none text-muted me-2">
                            <i class="bi bi-arrow-left-circle"></i>
                        </a>
                        <h1 class="h4 mb-0">Conversations</h1>
                    </div>
                    <small class="text-muted">Clean, messaging-style inbox for all visitors.</small>
                </div>
                <div>
                    <span data-status-badge
                        class="badge rounded-pill {{ $conversation->status === 'human' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success-emphasis' }}">
                        {{ $conversation->status === 'human' ? 'Human' : 'Bot' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- People / conversation list -->
            <div class="col-lg-4">
                <div class="card p-0" style="height: calc(100vh - 150px); overflow: hidden;">
                    <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
                        <div>
                            <h6 class="mb-0">People</h6>
                            <small class="text-muted">Select a visitor to view chat</small>
                        </div>
                        <span class="badge bg-light text-muted">
                            {{ $conversations->count() }} total
                        </span>
                    </div>

                    <div class="px-3 pt-2 pb-3 border-bottom">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-0 search-input" placeholder="Search people..."
                                id="conversation-search">
                        </div>
                    </div>

                    <div class="chat-list" id="conversation-list">
                        @forelse ($conversations as $conv)
                            @php
                                $isActive = $conv->id === $conversation->id;
                                $label = $conv->visitor
                                    ? 'Visitor ' . substr($conv->visitor->visitor_token, 0, 8)
                                    : 'Unknown visitor';
                                $avatarName = urlencode($label);
                                $lastMessage = $conv->messages->sortBy('created_at')->last();
                            @endphp
                            <a href="{{ route('admin.chat', $conv) }}"
                                class="chat-item d-flex align-items-center {{ $isActive ? 'active' : '' }}"
                                data-visitor-label="{{ \Illuminate\Support\Str::lower($label) }}">
                                <img src="https://ui-avatars.com/api/?name={{ $avatarName }}&background=E5E7EB&color=111827&size=64"
                                    alt="Avatar" width="40" height="40" class="rounded-circle me-3 flex-shrink-0">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="chat-item-name">
                                            {{ $label }}
                                        </div>
                                        <div class="chat-item-time">
                                            @if ($conv->last_message_at)
                                                {{ $conv->last_message_at->format('H:i') }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                    <div class="chat-item-message">
                                        @if ($lastMessage)
                                            {{ \Illuminate\Support\Str::limit($lastMessage->message, 50) }}
                                        @else
                                            <span class="text-muted">No messages yet</span>
                                        @endif
                                    </div>
                                    <div class="mt-1 small text-muted">
                                        {{ $conv->website->name ?? 'Unknown website' }}
                                        · {{ $conv->messages->count() }} messages
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-3 py-4 text-center text-muted small">
                                No conversations yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Active conversation window -->
            <div class="col-lg-8">
                <div class="card p-0 chat-window">
                    <div class="chat-header">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name={{ $activeAvatarName }}&background=FFEDD5&color=7C2D12&size=64"
                                alt="Visitor" width="40" height="40" class="rounded-circle me-2">
                            <div>
                                <div class="fw-semibold">
                                    {{ $activeVisitorLabel }}
                                </div>
                                <small class="text-muted">
                                    {{ $conversation->website->name ?? 'Unknown website' }}
                                    · Started {{ $conversation->created_at->format('M d, Y H:i') }}
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">
                                Last message:
                                {{ $conversation->last_message_at?->diffForHumans() ?? 'N/A' }}
                            </div>
                            <div class="small text-muted">
                                #{{ $conversation->id }} · {{ $conversation->messages->count() }} messages
                            </div>
                        </div>
                    </div>

                    <div id="messages" class="chat-messages">
                        @forelse ($conversation->messages as $message)
                            @php
                                $isVisitor = $message->sender_type === 'visitor';
                                $isAdmin = $message->sender_type === 'admin';
                            @endphp
                            <div class="message {{ $isVisitor ? 'visitor-message' : 'admin-message' }}"
                                data-message-id="{{ $message->id }}">
                                <div>
                                    <div class="message-content">
                                        {{ $message->message }}
                                    </div>
                                    <div class="message-time">
                                        {{ $message->created_at->format('M d, Y H:i') }}
                                        · {{ $isVisitor ? 'Visitor' : ($isAdmin ? 'You' : 'Bot') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div id="empty-state"
                                class="h-100 d-flex align-items-center justify-content-center text-muted small">
                                No messages yet. Start the conversation below.
                            </div>
                        @endforelse
                    </div>

                    <div class="chat-input-area">
                        <form id="admin-send-form" class="w-100 d-flex gap-2">
                            @csrf
                            <input id="admin-message-input" name="message" type="text"
                                class="form-control" placeholder="Type your reply…" autocomplete="off" />
                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                                <span class="me-1">Send</span>
                                <i class="bi bi-send"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messagesEl = document.getElementById('messages');
            const form = document.getElementById('admin-send-form');
            const input = document.getElementById('admin-message-input');
            const sendUrl = "{{ route('admin.chat.send', $conversation) }}";
            const messagesUrl = "{{ route('admin.chat.messages', $conversation) }}";
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const statusBadges = document.querySelectorAll('[data-status-badge]');
            const searchInput = document.getElementById('conversation-search');
            const conversationItems = document.querySelectorAll('#conversation-list .chat-item');

            // Client-side filter for people list
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const term = this.value.trim().toLowerCase();
                    conversationItems.forEach(item => {
                        const label = item.getAttribute('data-visitor-label') || '';
                        item.style.display = !term || label.includes(term) ? '' : 'none';
                    });
                });
            }

            let lastMessageId = 0;
            const messageNodes = messagesEl.querySelectorAll('[data-message-id]');
            if (messageNodes.length > 0) {
                lastMessageId = parseInt(messageNodes[messageNodes.length - 1].dataset.messageId, 10);
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            function setStatusBadge(status) {
                const isHuman = status === 'human';
                statusBadges.forEach(function(badge) {
                    badge.textContent = isHuman ? 'Human' : 'Bot';
                    badge.classList.remove('bg-warning-subtle', 'text-warning-emphasis', 'bg-success-subtle',
                        'text-success-emphasis');
                    if (isHuman) {
                        badge.classList.add('bg-warning-subtle', 'text-warning-emphasis');
                    } else {
                        badge.classList.add('bg-success-subtle', 'text-success-emphasis');
                    }
                });
            }

            function formatTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString(undefined, {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function appendMessage(message) {
                const emptyState = document.getElementById('empty-state');
                if (emptyState) {
                    emptyState.remove();
                }

                const isVisitor = message.sender_type === 'visitor';
                const isAdmin = message.sender_type === 'admin';

                const wrapper = document.createElement('div');
                wrapper.className = 'message ' + (isVisitor ? 'visitor-message' : 'admin-message');
                wrapper.dataset.messageId = message.id;

                const inner = document.createElement('div');

                const bubble = document.createElement('div');
                bubble.className = 'message-content';
                bubble.textContent = message.message;

                const meta = document.createElement('div');
                meta.className = 'message-time';
                meta.textContent = formatTime(message.created_at) + ' · ' + (isVisitor ? 'Visitor' : (isAdmin ?
                    'You' : 'Bot'));

                inner.appendChild(bubble);
                inner.appendChild(meta);
                wrapper.appendChild(inner);
                messagesEl.appendChild(wrapper);

                lastMessageId = message.id;
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            async function fetchMessages() {
                try {
                    const response = await fetch(messagesUrl + '?after_id=' + lastMessageId, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();

                    if (data.status) {
                        setStatusBadge(data.status);
                    }

                    if (Array.isArray(data.messages)) {
                        data.messages.forEach(appendMessage);
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }

            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                const message = input.value.trim();
                if (!message) return;

                input.value = '';
                input.focus();

                try {
                    const response = await fetch(sendUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            message: message
                        })
                    });

                    const data = await response.json();
                    if (data.message) {
                        appendMessage(data.message);
                    }
                    if (data.status) {
                        setStatusBadge(data.status);
                    }
                } catch (error) {
                    console.error('Send error:', error);
                }
            });

            // Faster polling so admin and widget stay in sync
            setInterval(fetchMessages, 2000);
        });
    </script>
@endsection
