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
                        <a href="{{ route('admin.conversations') }}" class="text-decoration-none me-2"
                            style="color: var(--text-muted);">
                            <i class="bi bi-arrow-left-circle"></i>
                        </a>
                        <h1 class="h4 mb-0" style="color: var(--text-main); font-weight: 700;">Conversations</h1>
                    </div>
                    <div class="small" style="color: var(--text-muted);">Messaging inbox for all visitors.</div>
                </div>
                <div>
                    <span data-status-badge class="badge rounded-pill"
                        style="background-color: rgba(249,115,22,0.10); color: #ea580c; font-weight: 600;">
                        {{ $conversation->status === 'human' ? 'Human' : 'Bot' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- People / conversation list -->
            <div class="col-lg-4">
                <div class="card p-0" style="height: calc(100vh - 150px); overflow: hidden;">
                    <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom"
                        style="border-color: var(--border-subtle) !important;">
                        <div>
                            <h6 class="mb-0" style="color: var(--text-main); font-weight: 700;">Inbox</h6>
                            <small style="color: var(--text-muted); font-size: 12px;">Select a visitor to view
                                chat</small>
                        </div>
                        <span class="badge"
                            style="background-color: var(--primary-soft); color: var(--primary-dark); font-weight: 600;">
                            {{ $conversations->count() }} total
                        </span>
                    </div>

                    {{-- <div class="px-3 pt-2 pb-3 border-bottom" style="border-color: var(--border-subtle) !important;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text search-input border-0">
                                <i class="bi bi-search" style="color: var(--text-muted);"></i>
                            </span>
                            <input type="text" class="form-control border-0 search-input text-light"
                                placeholder="Search people..." id="conversation-search">
                        </div>
                    </div> --}}

                    <div class="chat-list" id="conversation-list">
                        @forelse ($conversations as $conv)
                            @php
                                $isActive = $conv->id === $conversation->id;
                                $label = $conv->visitor
                                    ? 'Visitor ' . substr($conv->visitor->visitor_token, 0, 8)
                                    : null;
                                $avatarName = urlencode($label ?? 'V');
                                $lastMessage = $conv->messages->sortBy('created_at')->last();
                                $hasVisitorMessage = $lastMessage && $lastMessage->sender_type === 'visitor';
                                $isUnread =
                                    $hasVisitorMessage &&
                                    $conv->last_message_at &&
                                    (!$conv->admin_viewed_at || $conv->last_message_at > $conv->admin_viewed_at);
                            @endphp
                            @if (!$label)
                                @continue
                            @endif
                            <a href="{{ route('admin.chat', $conv) }}"
                                class="chat-item d-flex align-items-center {{ $isActive ? 'active' : '' }} {{ $isUnread ? 'chat-item-unread' : '' }}"
                                data-visitor-label="{{ \Illuminate\Support\Str::lower($label) }}">
                                <img src="{{ asset('images/visitor-avatar.svg') }}" alt="Avatar" width="40"
                                    height="40" class="rounded-circle me-3 flex-shrink-0"
                                    style="border: 2px solid var(--border-light);">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="chat-item-name {{ $isUnread ? 'fw-bold' : '' }}">
                                                {{ $label }}
                                            </div>
                                            @if ($isUnread)
                                                <span class="badge rounded-pill"
                                                    style="background-color: rgba(249,115,22,0.15); color: #ea580c; font-size: 0.65rem; font-weight: 700; padding: 3px 8px;">NEW</span>
                                            @endif
                                        </div>
                                        <div class="chat-item-time">
                                            @if ($conv->last_message_at)
                                                {{ $conv->last_message_at->format('H:i') }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                    <div class="chat-item-message {{ $isUnread ? 'fw-bold' : '' }}"
                                        style="{{ $isUnread ? 'color: var(--text-main);' : '' }}">
                                        @if ($lastMessage)
                                            @if ($lastMessage->sender_type === 'admin')
                                                <span style="color: var(--primary); font-weight: 600;">You:</span>
                                            @endif
                                            {{ \Illuminate\Support\Str::limit($lastMessage->message, 50) }}
                                        @else
                                            <span style="color: var(--text-muted);">No messages yet</span>
                                        @endif
                                    </div>
                                    <div class="mt-1 small" style="color: var(--text-muted); font-size: 11px;">
                                        @if ($conv->last_message_at)
                                            {{ $conv->last_message_at->format('d M') }}
                                        @endif
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
                            <img src="{{ asset('images/visitor-avatar.svg') }}" alt="Visitor" width="40"
                                height="40" class="rounded-circle me-2" style="border: 2px solid var(--border-light);">
                            <div>
                                <div class="fw-semibold" style="color: var(--text-main);">
                                    {{ $activeVisitorLabel }}
                                </div>
                                <small style="color: var(--text-muted);">
                                    {{ $conversation->website->name ?? 'Unknown website' }}
                                    · Started {{ $conversation->created_at->format('M d, Y H:i') }}
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="small" style="color: var(--text-muted);">
                                Last message:
                                {{ $conversation->last_message_at?->diffForHumans() ?? 'N/A' }}
                            </div>
                            <div class="small" style="color: var(--text-muted);">
                                id {{ $conversation->id }} · {{ $conversation->messages->count() }} messages
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
                                <div style="max-width: 60%; min-width: 40px;">
                                    <div class="message-content" style="max-width: 100%;">
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
                            <input id="admin-message-input" name="message" type="text" class="form-control"
                                placeholder="Type your reply…" autocomplete="off" />
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
                    badge.classList.remove('bg-warning-subtle', 'text-warning-emphasis',
                        'bg-success-subtle',
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
                bubble.style.maxWidth = '100%';
                bubble.textContent = message.message;

                const meta = document.createElement('div');
                meta.className = 'message-time';
                meta.textContent = formatTime(message.created_at) + ' · ' + (isVisitor ? 'Visitor' : (isAdmin ?
                    'You' : 'Bot'));

                inner.style.maxWidth = '60%';
                inner.style.minWidth = '40px';
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
                        data.messages.forEach(msg => {
                            // Prevent duplicates - skip if already in DOM
                            if (!messagesEl.querySelector('[data-message-id="' + msg.id + '"]')) {
                                appendMessage(msg);
                            }
                        });
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
                        // Prevent duplicate if poll already rendered this message
                        if (!messagesEl.querySelector('[data-message-id="' + data.message.id + '"]')) {
                            appendMessage(data.message);
                        }
                    }
                    if (data.status) {
                        setStatusBadge(data.status);
                    }
                    // Speed up polling after sending
                    pollDelay = 3000;
                    pollIdleSince = Date.now();
                    // Refresh sidebar to show updated preview
                    refreshSidebar();
                } catch (error) {
                    console.error('Send error:', error);
                }
            });

            // Adaptive polling: fast when active, slows when idle
            let pollDelay = 3000;
            let pollIdleSince = Date.now();
            let pollTimer = null;

            async function doPoll() {
                const prevId = lastMessageId;
                await fetchMessages();
                if (lastMessageId > prevId) {
                    pollDelay = 3000;
                    pollIdleSince = Date.now();
                } else if (Date.now() - pollIdleSince > 30000) {
                    pollDelay = Math.min(pollDelay + 1000, 8000);
                }
                pollTimer = setTimeout(doPoll, pollDelay);
            }

            pollTimer = setTimeout(doPoll, pollDelay);

            window.addEventListener('beforeunload', function() {
                if (pollTimer) clearTimeout(pollTimer);
                if (sidebarTimer) clearTimeout(sidebarTimer);
            });

            // ── Sidebar live-refresh ──────────────────────────────
            const conversationList = document.getElementById('conversation-list');
            const totalBadge = conversationList ?
                conversationList.closest('.card')?.querySelector('.badge') :
                null;
            const activeConversationId = {{ $conversation->id }};
            let sidebarTimer = null;
            let sidebarDelay = 5000;

            function buildConversationItem(conv) {
                const isActive = conv.id === activeConversationId;
                const token = conv.visitor_token || '';
                const label = 'Visitor ' + token.substring(0, 8);
                const avatarName = encodeURIComponent(label);
                const unread = conv.is_unread && !isActive;

                return `<a href="/admin/chat/${conv.id}"
                    class="chat-item d-flex align-items-center ${isActive ? 'active' : ''} ${unread ? 'chat-item-unread' : ''}"
                    data-visitor-label="${label.toLowerCase()}"
                    data-conversation-id="${conv.id}">
                    <img src="/images/visitor-avatar.svg"
                        alt="Avatar" width="40" height="40" class="rounded-circle me-3 flex-shrink-0" style="border: 2px solid var(--border-light);">
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="chat-item-name ${unread ? 'fw-bold' : ''}">
                                ${label}
                                ${unread ? '<span class="ms-2 badge" style="background-color:rgba(249,115,22,0.12);color:#ea580c;font-size:9px;">NEW</span>' : ''}
                            </div>
                            <div class="chat-item-time">
                                ${conv.last_message_at_time || '—'}
                            </div>
                        </div>
                        <div class="chat-item-message ${unread ? 'fw-bold' : ''}" style="${unread ? 'color:var(--text-main);' : ''}">
                            ${conv.last_message ? ((conv.last_message_sender === 'admin' ? '<span style="color:var(--primary);font-weight:600;">You:</span> ' : '') + conv.last_message) : '<span style="color:var(--text-muted);">No messages yet</span>'}
                        </div>
                        <div class="mt-1 small" style="color: var(--text-muted); font-size: 11px;">
                            ${conv.last_message_at_date || ''}
                        </div>
                    </div>
                </a>`;
            }

            async function refreshSidebar() {
                try {
                    const res = await fetch("{{ route('admin.conversations.list') }}", {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const conversations = await res.json();

                    if (!Array.isArray(conversations)) return;

                    // Update total badge
                    if (totalBadge) {
                        totalBadge.textContent = conversations.length + ' total';
                    }

                    // Preserve search filter
                    const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';

                    // Rebuild the list
                    conversationList.innerHTML = conversations.length === 0 ?
                        '<div class="px-3 py-4 text-center text-muted small">No conversations yet.</div>' :
                        conversations.map(buildConversationItem).join('');

                    // Re-apply search filter if active
                    if (searchTerm) {
                        conversationList.querySelectorAll('.chat-item').forEach(item => {
                            const label = item.getAttribute('data-visitor-label') || '';
                            item.style.display = label.includes(searchTerm) ? '' : 'none';
                        });
                    }
                } catch (err) {
                    console.error('Sidebar refresh error:', err);
                }
            }

            async function doSidebarPoll() {
                await refreshSidebar();
                sidebarTimer = setTimeout(doSidebarPoll, sidebarDelay);
            }

            sidebarTimer = setTimeout(doSidebarPoll, sidebarDelay);

            // Websocket updates via Laravel Echo (Reverb)
            if (window.Echo) {
                try {
                    window.Echo.channel('chat.{{ $conversation->id }}')
                        .listen('.MessageSent', (e) => {
                            if (!e || !e.message) return;
                            const msg = e.message;
                            if (msg.id && msg.id <= lastMessageId) return;
                            appendMessage(msg);
                        });
                } catch (err) {
                    console.error('Echo subscribe error:', err);
                }
            }
        });
    </script>
@endsection
