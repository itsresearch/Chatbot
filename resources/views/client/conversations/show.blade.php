@extends('layouts.panel')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('title', 'Chat')

@section('extra-styles')
    <style>
        .conv-sidebar {
            width: 320px;
            min-width: 320px;
            background: var(--surface);
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            border-radius: 16px 0 0 16px;
            overflow: hidden;
        }

        .conv-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            border-radius: 0 16px 16px 0;
            overflow: hidden;
            background: var(--surface-bg);
        }

        .conv-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-subtle);
            text-decoration: none;
            color: inherit;
            transition: background 0.15s;
            cursor: pointer;
        }

        .conv-item:hover {
            background: var(--surface-soft);
        }

        .conv-item.active {
            background: var(--primary-soft);
            border-left: 3px solid var(--primary);
        }

        .conv-item-unread .conv-item-name {
            font-weight: 700;
        }

        .conv-item-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            flex-shrink: 0;
            border: 2px solid var(--border-light);
        }

        .conv-item-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
        }

        .conv-item-preview {
            font-size: 12px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conv-item-time {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .chat-window {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .chat-header-bar {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-subtle);
            background: var(--surface);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-messages-area {
            flex: 1;
            overflow-y: auto;
            padding: 24px 20px;
            background: linear-gradient(180deg, var(--surface-soft) 0%, var(--surface) 40%, var(--surface-soft) 100%);
        }

        .msg {
            display: flex;
            margin-bottom: 16px;
        }

        .msg.visitor {
            justify-content: flex-start;
        }

        .msg.admin {
            justify-content: flex-end;
        }

        .msg-bubble {
            max-width: 65%;
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .msg.visitor .msg-bubble {
            background: var(--surface);
            border: 1px solid var(--border-subtle);
            color: var(--text-main);
            border-bottom-left-radius: 6px;
        }

        .msg.admin .msg-bubble {
            background: linear-gradient(135deg, var(--primary), #ea580c);
            color: #fff;
            border-bottom-right-radius: 6px;
        }

        .msg-meta {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .msg.admin .msg-meta {
            text-align: right;
        }

        .chat-input-bar {
            padding: 14px 20px;
            border-top: 1px solid var(--border-subtle);
            background: var(--surface);
        }
    </style>
@endsection

@section('content')
    @php
        $activeVisitorLabel = $conversation->visitor
            ? 'Visitor ' . substr($conversation->visitor->visitor_token, 0, 8)
            : 'Unknown visitor';
    @endphp

    <div class="d-flex"
        style="height: calc(100vh - 140px); border-radius: 16px; border: 1px solid var(--border-subtle); overflow: hidden;">
        {{-- Sidebar --}}
        <div class="conv-sidebar">
            <div class="px-3 py-3 border-bottom" style="border-color: var(--border-subtle) !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0 fw-bold" style="color: var(--text-main);">Inbox</h6>
                        <small style="color: var(--text-muted); font-size: 12px;">Select a conversation</small>
                    </div>
                    <span class="badge"
                        style="background: var(--primary-soft); color: var(--primary-dark); font-weight: 600;">
                        {{ $conversations->count() }}
                    </span>
                </div>
            </div>
            <div class="flex-grow-1 overflow-auto" id="conversation-list">
                @foreach ($conversations as $conv)
                    @php
                        $isActive = $conv->id === $conversation->id;
                        $label = $conv->visitor ? 'Visitor ' . substr($conv->visitor->visitor_token, 0, 8) : null;
                        $lastMsg = $conv->messages->sortBy('created_at')->last();
                        $hasVisitorMsg = $lastMsg && $lastMsg->sender_type === 'visitor';
                        $isUnread =
                            $hasVisitorMsg &&
                            $conv->last_message_at &&
                            (!$conv->admin_viewed_at || $conv->last_message_at > $conv->admin_viewed_at);
                    @endphp
                    @if (!$label)
                        @continue
                    @endif
                    <a href="{{ route('client.chat', $conv) }}"
                        class="conv-item {{ $isActive ? 'active' : '' }} {{ $isUnread ? 'conv-item-unread' : '' }}">
                        <img src="{{ asset('images/visitor-avatar.svg') }}" alt="" class="conv-item-avatar">
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="conv-item-name">
                                    {{ $label }}
                                    @if ($isUnread)
                                        <span class="badge ms-1"
                                            style="background: rgba(249,115,22,0.15); color: #ea580c; font-size: 9px; vertical-align: middle;">NEW</span>
                                    @endif
                                </span>
                                <span class="conv-item-time">
                                    {{ $conv->last_message_at ? $conv->last_message_at->format('H:i') : '—' }}
                                </span>
                            </div>
                            <div class="conv-item-preview">
                                @if ($lastMsg)
                                    @if ($lastMsg->sender_type === 'admin')
                                        <span style="color: var(--primary); font-weight: 600;">You:</span>
                                    @endif
                                    {{ Str::limit($lastMsg->message, 45) }}
                                @else
                                    No messages yet
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge"
                                    style="font-size: 10px; padding: 2px 8px;
                                    {{ $conv->status === 'human' ? 'background: #fef3c7; color: #92400e;' : 'background: #dcfce7; color: #166534;' }}">
                                    {{ $conv->status === 'human' ? 'Human' : 'Bot' }}
                                </span>
                                <span style="font-size: 11px; color: var(--text-muted);">
                                    {{ $conv->website->name ?? '' }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Chat Area --}}
        <div class="conv-main">
            <div class="chat-window">
                {{-- Header --}}
                <div class="chat-header-bar">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('images/visitor-avatar.svg') }}" alt="" width="40" height="40"
                            class="rounded-circle" style="border: 2px solid var(--border-light);">
                        <div>
                            <div class="fw-semibold" style="color: var(--text-main);">{{ $activeVisitorLabel }}</div>
                            <small style="color: var(--text-muted);">
                                {{ $conversation->website->name ?? 'Unknown' }}
                                &middot; Started {{ $conversation->created_at->format('M d, Y H:i') }}
                            </small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span data-status-badge class="badge rounded-pill"
                            style="background: rgba(249,115,22,0.10); color: #ea580c; font-weight: 600;">
                            {{ $conversation->status === 'human' ? 'Human' : 'Bot' }}
                        </span>
                        <div class="mt-1" style="font-size: 11px; color: var(--text-muted);">
                            {{ $conversation->messages->count() }} messages
                        </div>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="chat-messages-area" id="messages">
                    @forelse ($conversation->messages as $message)
                        @php
                            $isVisitor = $message->sender_type === 'visitor';
                            $isAdmin = $message->sender_type === 'admin';
                        @endphp
                        <div class="msg {{ $isVisitor ? 'visitor' : 'admin' }}" data-message-id="{{ $message->id }}">
                            <div>
                                <div class="msg-bubble">{{ $message->message }}</div>
                                <div class="msg-meta">
                                    {{ $message->created_at->format('M d, H:i') }}
                                    &middot; {{ $isVisitor ? 'Visitor' : ($isAdmin ? 'You' : 'Bot') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div id="empty-state" class="h-100 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="bi bi-chat-dots d-block mb-2"
                                    style="font-size: 40px; color: var(--text-muted);"></i>
                                <div style="color: var(--text-muted); font-size: 14px;">No messages yet</div>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Input --}}
                <div class="chat-input-bar">
                    <form id="admin-send-form" class="d-flex gap-2">
                        @csrf
                        <input id="admin-message-input" name="message" type="text" class="form-control search-input"
                            placeholder="Type your reply…" autocomplete="off">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                            <span>Send</span><i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messagesEl = document.getElementById('messages');
            const form = document.getElementById('admin-send-form');
            const input = document.getElementById('admin-message-input');
            const sendUrl = "{{ route('client.chat.send', $conversation) }}";
            const messagesUrl = "{{ route('client.chat.messages', $conversation) }}";
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const statusBadges = document.querySelectorAll('[data-status-badge]');

            let lastMessageId = 0;
            const existingMsgs = messagesEl.querySelectorAll('[data-message-id]');
            if (existingMsgs.length) {
                lastMessageId = parseInt(existingMsgs[existingMsgs.length - 1].dataset.messageId, 10);
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            function setStatusBadge(status) {
                statusBadges.forEach(b => {
                    b.textContent = status === 'human' ? 'Human' : 'Bot';
                });
            }

            function formatTime(dateStr) {
                const d = new Date(dateStr);
                return d.toLocaleDateString(undefined, {
                        month: 'short',
                        day: 'numeric'
                    }) + ', ' +
                    d.toLocaleTimeString(undefined, {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
            }

            function appendMessage(msg) {
                const empty = document.getElementById('empty-state');
                if (empty) empty.remove();

                if (messagesEl.querySelector('[data-message-id="' + msg.id + '"]')) return;

                const isVisitor = msg.sender_type === 'visitor';
                const isAdmin = msg.sender_type === 'admin';

                const wrapper = document.createElement('div');
                wrapper.className = 'msg ' + (isVisitor ? 'visitor' : 'admin');
                wrapper.dataset.messageId = msg.id;

                const inner = document.createElement('div');
                const bubble = document.createElement('div');
                bubble.className = 'msg-bubble';
                bubble.textContent = msg.message;

                const meta = document.createElement('div');
                meta.className = 'msg-meta';
                meta.textContent = formatTime(msg.created_at) + ' · ' + (isVisitor ? 'Visitor' : (isAdmin ? 'You' :
                    'Bot'));

                inner.appendChild(bubble);
                inner.appendChild(meta);
                wrapper.appendChild(inner);
                messagesEl.appendChild(wrapper);
                lastMessageId = msg.id;
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            // ── Polling ─────────────────────────────────────────────
            let pollDelay = 3000;
            let pollIdleSince = Date.now();
            let pollTimer = null;

            async function fetchMessages() {
                try {
                    const res = await fetch(messagesUrl + '?after_id=' + lastMessageId, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.status) setStatusBadge(data.status);
                    if (Array.isArray(data.messages)) data.messages.forEach(appendMessage);
                } catch (e) {
                    console.error('Poll error:', e);
                }
            }

            async function doPoll() {
                const prev = lastMessageId;
                await fetchMessages();
                if (lastMessageId > prev) {
                    pollDelay = 3000;
                    pollIdleSince = Date.now();
                } else if (Date.now() - pollIdleSince > 30000) {
                    pollDelay = Math.min(pollDelay + 1000, 8000);
                }
                pollTimer = setTimeout(doPoll, pollDelay);
            }
            pollTimer = setTimeout(doPoll, pollDelay);

            // ── Send message ────────────────────────────────────────
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const text = input.value.trim();
                if (!text) return;
                input.value = '';
                input.focus();

                try {
                    const res = await fetch(sendUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            message: text
                        })
                    });
                    const data = await res.json();
                    if (data.message) appendMessage(data.message);
                    if (data.status) setStatusBadge(data.status);
                    pollDelay = 3000;
                    pollIdleSince = Date.now();
                    refreshSidebar();
                } catch (e) {
                    console.error('Send error:', e);
                }
            });

            // ── Sidebar live refresh ────────────────────────────────
            const convList = document.getElementById('conversation-list');
            const activeConversationId = {{ $conversation->id }};
            let sidebarTimer = null;

            function buildSidebarItem(conv) {
                const isActive = conv.id === activeConversationId;
                const unread = conv.is_unread && !isActive;
                const label = 'Visitor ' + (conv.visitor_token || '').substring(0, 8);

                return `<a href="/client/chat/${conv.id}"
            class="conv-item ${isActive ? 'active' : ''} ${unread ? 'conv-item-unread' : ''}">
            <img src="/images/visitor-avatar.svg" alt="" class="conv-item-avatar">
            <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="conv-item-name">
                        ${label}
                        ${unread ? '<span class="badge ms-1" style="background:rgba(249,115,22,0.15);color:#ea580c;font-size:9px;">NEW</span>' : ''}
                    </span>
                    <span class="conv-item-time">${conv.last_message_at_time || '—'}</span>
                </div>
                <div class="conv-item-preview">
                    ${conv.last_message ? ((conv.last_message_sender === 'admin' ? '<span style="color:var(--primary);font-weight:600;">You:</span> ' : '') + conv.last_message) : 'No messages yet'}
                </div>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge" style="font-size:10px;padding:2px 8px;${conv.status === 'human' ? 'background:#fef3c7;color:#92400e;' : 'background:#dcfce7;color:#166534;'}">
                        ${conv.status === 'human' ? 'Human' : 'Bot'}
                    </span>
                    <span style="font-size:11px;color:var(--text-muted);">${conv.website_name || ''}</span>
                </div>
            </div>
        </a>`;
            }

            async function refreshSidebar() {
                try {
                    const res = await fetch("{{ route('client.conversations.list') }}", {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const convs = await res.json();
                    if (!Array.isArray(convs) || !convs.length) return;
                    convList.innerHTML = convs.map(buildSidebarItem).join('');
                } catch (e) {
                    console.error('Sidebar error:', e);
                }
            }

            async function doSidebarPoll() {
                await refreshSidebar();
                sidebarTimer = setTimeout(doSidebarPoll, 5000);
            }
            sidebarTimer = setTimeout(doSidebarPoll, 5000);

            // ── Echo real-time ──────────────────────────────────────
            if (window.Echo) {
                try {
                    window.Echo.channel('chat.{{ $conversation->id }}')
                        .listen('.MessageSent', function(e) {
                            if (e && e.message) appendMessage(e.message);
                        });
                } catch (err) {
                    console.error('Echo error:', err);
                }
            }

            window.addEventListener('beforeunload', function() {
                if (pollTimer) clearTimeout(pollTimer);
                if (sidebarTimer) clearTimeout(sidebarTimer);
            });
        });
    </script>
@endsection
