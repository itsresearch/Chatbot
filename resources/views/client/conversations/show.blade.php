@extends('layouts.panel')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('title', 'Chat')

@section('extra-styles')
    <style>
        /* ── Conversation Layout ──────────────────────────── */
        .conv-container {
            height: calc(100vh - 140px);
            border-radius: 16px;
            border: 1px solid var(--border-subtle);
            overflow: hidden;
            display: flex;
            background: var(--surface);
            box-shadow: var(--shadow-md);
        }

        /* ── Sidebar ──────────────────────────────────────── */
        .conv-sidebar {
            width: 340px;
            min-width: 340px;
            background: var(--surface);
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .conv-sidebar-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .conv-sidebar-search {
            width: 100%;
            padding: 9px 14px 9px 36px;
            font-size: 13px;
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            background: var(--surface-soft);
            color: var(--text-main);
            transition: all 0.2s;
        }

        .conv-sidebar-search:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.10);
        }

        .conv-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-subtle);
            text-decoration: none;
            color: inherit;
            transition: background 0.15s;
            cursor: pointer;
            position: relative;
        }

        .conv-item:hover {
            background: var(--surface-soft);
        }

        .conv-item.active {
            background: var(--primary-soft);
            border-left: 3px solid var(--primary);
            padding-left: 17px;
        }

        .conv-item-unread .conv-item-name {
            font-weight: 700;
        }

        .conv-item-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            flex-shrink: 0;
            object-fit: cover;
            border: 2px solid var(--border-light);
        }

        .conv-item-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
        }

        .conv-item-preview {
            font-size: 12.5px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conv-item-time {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
            font-weight: 500;
        }

        /* ── Main Chat Area ───────────────────────────────── */
        .conv-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: var(--surface-bg);
        }

        .chat-window {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        /* ── Chat Header ──────────────────────────────────── */
        .chat-header-bar {
            padding: 14px 24px;
            border-bottom: 1px solid var(--border-subtle);
            background: var(--surface);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 68px;
        }

        .chat-header-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 2px solid var(--border-light);
            object-fit: cover;
        }

        .chat-header-name {
            font-weight: 700;
            font-size: 15px;
            color: var(--text-main);
            line-height: 1.2;
        }

        .chat-header-sub {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.3;
        }

        .chat-header-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .chat-header-status .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
        }

        /* ── Messages Area ────────────────────────────────── */
        .chat-messages-area {
            flex: 1;
            overflow-y: auto;
            padding: 20px 24px 12px;
            background: var(--surface-bg);
        }

        body.theme-dark .chat-messages-area {
            background: rgb(16, 16, 16);
        }

        /* Date separator */
        .msg-date-sep {
            text-align: center;
            margin: 20px 0 16px;
            position: relative;
        }

        .msg-date-sep span {
            display: inline-block;
            padding: 4px 14px;
            background: var(--surface-elevated);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.3px;
        }

        /* Message row */
        .msg {
            display: flex;
            margin-bottom: 4px;
            align-items: flex-end;
            gap: 8px;
            animation: msgFadeIn 0.25s ease;
        }

        @keyframes msgFadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .msg.visitor {
            justify-content: flex-start;
        }

        .msg.admin {
            justify-content: flex-end;
        }

        .msg+.msg {
            margin-top: 0;
        }

        .msg.visitor+.msg.admin,
        .msg.admin+.msg.visitor {
            margin-top: 14px;
        }

        /* Message avatar (only for visitor) */
        .msg-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            flex-shrink: 0;
            object-fit: cover;
            margin-bottom: 18px;
        }

        .msg-avatar-spacer {
            width: 30px;
            flex-shrink: 0;
        }

        /* Message bubble */
        .msg-bubble {
            padding: 10px 14px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.55;
            word-wrap: break-word;
            position: relative;
        }

        .msg.visitor .msg-bubble {
            background: var(--surface);
            border: 1px solid var(--border-subtle);
            color: var(--text-main);
            border-bottom-left-radius: 4px;
        }

        body.theme-dark .msg.visitor .msg-bubble {
            background: var(--surface-elevated);
            border-color: var(--border-subtle);
        }

        .msg.admin .msg-bubble {
            background: var(--primary);
            color: #fff;
            border-bottom-right-radius: 4px;
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.25);
        }

        /* Consecutive bubbles get smaller radius  */
        .msg.visitor+.msg.visitor .msg-bubble {
            border-top-left-radius: 8px;
        }

        .msg.admin+.msg.admin .msg-bubble {
            border-top-right-radius: 8px;
        }

        /* Message meta */
        .msg-meta {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 3px;
            padding: 0 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .msg.admin .msg-meta {
            justify-content: flex-end;
        }

        /* Message group spacing */
        .msg-group-spacer {
            height: 14px;
        }

        /* ── Input Bar ────────────────────────────────────── */
        .chat-input-bar {
            padding: 12px 24px 14px;
            border-top: 1px solid var(--border-subtle);
            background: var(--surface);
        }

        .chat-input-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--surface-soft);
            border: 1px solid var(--border-subtle);
            border-radius: 24px;
            padding: 6px 6px 6px 8px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .chat-input-wrapper:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.10);
        }

        body.theme-dark .chat-input-wrapper {
            background: var(--surface-elevated);
        }

        .chat-input-wrapper .file-attach-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            padding: 6px 8px;
            cursor: pointer;
            border-radius: 50%;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .chat-input-wrapper .file-attach-btn:hover {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .chat-input-wrapper input[type="text"] {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 14px;
            color: var(--text-main);
            padding: 8px 4px;
            outline: none;
        }

        .chat-input-wrapper input[type="text"]::placeholder {
            color: var(--text-muted);
        }

        .chat-send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: var(--primary-gradient);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.25);
        }

        .chat-send-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 14px rgba(249, 115, 22, 0.35);
        }

        .chat-send-btn i {
            font-size: 18px;
            margin-left: 2px;
        }

        /* ── File Preview Bar ─────────────────────────────── */
        .file-preview-bar {
            display: none;
            padding: 8px 24px;
            background: var(--surface-soft);
            border-top: 1px solid var(--border-subtle);
            align-items: center;
            gap: 10px;
        }

        .file-preview-bar.active {
            display: flex;
        }

        .file-preview-bar .file-info {
            flex: 1;
            overflow: hidden;
            font-size: 13px;
            color: var(--text-main);
            font-weight: 500;
        }

        .file-preview-bar .file-info small {
            color: var(--text-muted);
            font-weight: 400;
        }

        .file-preview-bar .remove-file {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            font-size: 16px;
            padding: 2px 6px;
        }

        /* ── File Attachments in Messages ─────────────────── */
        .msg-file-attachment {
            margin-top: 6px;
            border-radius: 10px;
            overflow: hidden;
        }

        .msg-file-attachment img {
            max-width: 260px;
            max-height: 220px;
            border-radius: 10px;
            display: block;
            cursor: pointer;
        }

        .msg-file-attachment .file-doc {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            transition: background 0.15s;
            font-size: 13px;
        }

        .msg.visitor .msg-file-attachment .file-doc {
            background: rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border-subtle);
            color: var(--text-main);
        }

        body.theme-dark .msg.visitor .msg-file-attachment .file-doc {
            background: rgba(255, 255, 255, 0.06);
        }

        .msg.admin .msg-file-attachment .file-doc {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .msg-file-attachment .file-doc:hover {
            background: rgba(0, 0, 0, 0.08);
        }

        .msg.admin .msg-file-attachment .file-doc:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .msg-file-attachment .file-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 16px;
            flex-shrink: 0;
        }

        .msg.visitor .file-icon {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .msg.admin .file-icon {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        /* ── Responsive ───────────────────────────────────── */
        @media (max-width: 768px) {
            .conv-sidebar {
                display: none;
            }

            .conv-container {
                height: calc(100vh - 100px);
            }

            .msg-bubble {
                max-width: 85%;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $activeVisitorLabel = $conversation->visitor
            ? 'Visitor ' . substr($conversation->visitor->visitor_token, 0, 8)
            : 'Unknown visitor';
    @endphp

    <div class="conv-container">
        {{-- ── Sidebar ──────────────────────────────────────── --}}
        <div class="conv-sidebar">
            <div class="conv-sidebar-header">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 fw-bold" style="font-size: 18px; color: var(--text-main);">Inbox</h6>
                    <span class="badge"
                        style="background: var(--primary-soft); color: var(--primary-dark); font-weight: 700; font-size: 12px; padding: 5px 12px;">
                        {{ $conversations->count() }}
                    </span>
                </div>
                <div class="position-relative">
                    <i class="bi bi-search position-absolute"
                        style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 13px;"></i>
                    <input type="text" class="conv-sidebar-search" placeholder="Search conversations..."
                        id="conv-search">
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
                                            style="background: var(--primary); color: #fff; font-size: 9px; vertical-align: middle; padding: 2px 6px;">NEW</span>
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

        {{-- ── Chat Area ────────────────────────────────────── --}}
        <div class="conv-main">
            <div class="chat-window">
                {{-- Header --}}
                <div class="chat-header-bar">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('images/visitor-avatar.svg') }}" alt="" class="chat-header-avatar">
                        <div>
                            <div class="chat-header-name">{{ $activeVisitorLabel }}</div>
                            <div class="chat-header-sub">
                                {{ $conversation->website->name ?? 'Unknown' }}
                                &middot; {{ $conversation->messages->count() }} messages
                                &middot; Started {{ $conversation->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span data-status-badge class="chat-header-status"
                            style="{{ $conversation->status === 'human' ? 'background: #fef3c7; color: #92400e;' : 'background: #dcfce7; color: #166534;' }}">
                            <span class="status-dot"
                                style="{{ $conversation->status === 'human' ? 'background: #f59e0b;' : 'background: #22c55e;' }}"></span>
                            {{ $conversation->status === 'human' ? 'Human' : 'Bot' }}
                        </span>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="chat-messages-area" id="messages">
                    @php
                        $lastDate = null;
                        $lastSender = null;
                    @endphp
                    @forelse ($conversation->messages as $message)
                        @php
                            $isVisitor = $message->sender_type === 'visitor';
                            $isAdmin = $message->sender_type === 'admin';
                            $msgDate = $message->created_at->format('M d, Y');
                            $showDate = $msgDate !== $lastDate;
                            $sameSender = $message->sender_type === $lastSender;
                            $showAvatar = $isVisitor && !$sameSender;
                        @endphp

                        @if ($showDate)
                            <div class="msg-date-sep"><span>{{ $message->created_at->format('M d, Y') }}</span></div>
                            @php
                                $lastDate = $msgDate;
                                $lastSender = null;
                                $sameSender = false;
                                $showAvatar = $isVisitor;
                            @endphp
                        @endif

                        @if (!$sameSender && $lastSender !== null)
                            <div class="msg-group-spacer"></div>
                        @endif

                        <div class="msg {{ $isVisitor ? 'visitor' : 'admin' }}" data-message-id="{{ $message->id }}">
                            @if ($isVisitor)
                                @if ($showAvatar || !$sameSender)
                                    <img src="{{ asset('images/visitor-avatar.svg') }}" alt="" class="msg-avatar">
                                @else
                                    <div class="msg-avatar-spacer"></div>
                                @endif
                            @endif
                            <div style="max-width: 65%; min-width: 60px;">
                                <div class="msg-bubble">
                                    {{ $message->message }}
                                    @if ($message->hasFile())
                                        <div class="msg-file-attachment">
                                            @if ($message->is_image)
                                                <a href="{{ $message->file_url }}" target="_blank">
                                                    <img src="{{ $message->file_url }}" alt="{{ $message->file_name }}"
                                                        loading="lazy">
                                                </a>
                                            @else
                                                <a href="{{ $message->file_url }}" target="_blank" class="file-doc">
                                                    <span class="file-icon"><i
                                                            class="bi bi-file-earmark-arrow-down"></i></span>
                                                    <span>
                                                        <span
                                                            style="font-weight: 600;">{{ $message->file_name }}</span><br>
                                                        <small>{{ $message->formattedFileSize() }}</small>
                                                    </span>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="msg-meta">
                                    <span>{{ $message->created_at->format('H:i') }}</span>
                                    <span>&middot;</span>
                                    <span>{{ $isVisitor ? 'Visitor' : ($isAdmin ? 'You' : 'Bot') }}</span>
                                </div>
                            </div>
                        </div>

                        @php $lastSender = $message->sender_type; @endphp
                    @empty
                        <div id="empty-state" class="h-100 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <div
                                    style="width: 64px; height: 64px; border-radius: 50%; background: var(--primary-soft); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                    <i class="bi bi-chat-dots" style="font-size: 28px; color: var(--primary);"></i>
                                </div>
                                <div style="color: var(--text-main); font-size: 16px; font-weight: 600;">No messages yet
                                </div>
                                <div style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">Send a message to
                                    start the conversation</div>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- File Preview Bar --}}
                <div class="file-preview-bar" id="file-preview-bar">
                    <i class="bi bi-paperclip" style="color: var(--primary); font-size: 16px;"></i>
                    <div class="file-info">
                        <span id="file-preview-name">—</span>
                        <small id="file-preview-size"></small>
                    </div>
                    <button type="button" class="remove-file" id="remove-file-btn" title="Remove file">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>

                {{-- Input --}}
                <div class="chat-input-bar">
                    <form id="admin-send-form" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="admin-file-input" name="file" style="display: none;"
                            accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.rtf,.zip,.rar,.7z">
                        <div class="chat-input-wrapper">
                            <button type="button" class="file-attach-btn" id="file-attach-btn" title="Attach file">
                                <i class="bi bi-paperclip"></i>
                            </button>
                            <input id="admin-message-input" name="message" type="text"
                                placeholder="Type your message…" autocomplete="off">
                            <button type="submit" class="chat-send-btn" title="Send message">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
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
            const fileInput = document.getElementById('admin-file-input');
            const fileAttachBtn = document.getElementById('file-attach-btn');
            const filePreviewBar = document.getElementById('file-preview-bar');
            const filePreviewName = document.getElementById('file-preview-name');
            const filePreviewSize = document.getElementById('file-preview-size');
            const removeFileBtn = document.getElementById('remove-file-btn');
            const sendUrl = "{{ route('client.chat.send', $conversation) }}";
            const messagesUrl = "{{ route('client.chat.messages', $conversation) }}";
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const statusBadges = document.querySelectorAll('[data-status-badge]');

            let lastMessageId = 0;
            let lastSenderType = null;
            let lastMessageDate = null;
            const existingMsgs = messagesEl.querySelectorAll('[data-message-id]');
            if (existingMsgs.length) {
                const lastEl = existingMsgs[existingMsgs.length - 1];
                lastMessageId = parseInt(lastEl.dataset.messageId, 10);
                lastSenderType = lastEl.classList.contains('visitor') ? 'visitor' : 'admin';
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            // ── Sidebar search ──────────────────────────────────────
            const convSearch = document.getElementById('conv-search');
            if (convSearch) {
                convSearch.addEventListener('input', function() {
                    const q = this.value.toLowerCase();
                    document.querySelectorAll('#conversation-list .conv-item').forEach(item => {
                        const text = item.textContent.toLowerCase();
                        item.style.display = text.includes(q) ? '' : 'none';
                    });
                });
            }

            // ── File attach ─────────────────────────────────────────
            fileAttachBtn.addEventListener('click', () => fileInput.click());

            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    if (file.size > 10 * 1024 * 1024) {
                        alert('File size must be under 10 MB.');
                        this.value = '';
                        return;
                    }
                    filePreviewName.textContent = file.name;
                    filePreviewSize.textContent = ' (' + formatFileSize(file.size) + ')';
                    filePreviewBar.classList.add('active');
                    fileAttachBtn.style.color = 'var(--primary)';
                } else {
                    clearFilePreview();
                }
            });

            removeFileBtn.addEventListener('click', function() {
                fileInput.value = '';
                clearFilePreview();
            });

            function clearFilePreview() {
                filePreviewBar.classList.remove('active');
                filePreviewName.textContent = '—';
                filePreviewSize.textContent = '';
                fileAttachBtn.style.color = '';
            }

            function formatFileSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / 1048576).toFixed(1) + ' MB';
            }

            function setStatusBadge(status) {
                statusBadges.forEach(b => {
                    b.textContent = status === 'human' ? 'Human' : 'Bot';
                    if (status === 'human') {
                        b.style.background = '#fef3c7';
                        b.style.color = '#92400e';
                    } else {
                        b.style.background = '#dcfce7';
                        b.style.color = '#166534';
                    }
                });
            }

            function formatTime(dateStr) {
                const d = new Date(dateStr);
                return d.toLocaleTimeString(undefined, {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function formatDate(dateStr) {
                const d = new Date(dateStr);
                return d.toLocaleDateString(undefined, {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                });
            }

            function getFileIcon(mimeType) {
                if (!mimeType) return 'bi-file-earmark';
                if (mimeType.startsWith('image/')) return 'bi-file-earmark-image';
                if (mimeType === 'application/pdf') return 'bi-file-earmark-pdf';
                if (mimeType.includes('word') || mimeType.includes('document')) return 'bi-file-earmark-word';
                if (mimeType.includes('sheet') || mimeType.includes('excel')) return 'bi-file-earmark-spreadsheet';
                if (mimeType.includes('presentation') || mimeType.includes('powerpoint'))
                    return 'bi-file-earmark-slides';
                if (mimeType.includes('zip') || mimeType.includes('rar') || mimeType.includes('7z'))
                    return 'bi-file-earmark-zip';
                return 'bi-file-earmark-arrow-down';
            }

            function buildFileHtml(msg, isVisitor) {
                if (!msg.file_url) return '';
                const isImage = msg.is_image || (msg.file_type && msg.file_type.startsWith('image/'));
                if (isImage) {
                    return `<div class="msg-file-attachment">
                        <a href="${msg.file_url}" target="_blank">
                            <img src="${msg.file_url}" alt="${msg.file_name || 'Image'}" loading="lazy">
                        </a>
                    </div>`;
                }
                const icon = getFileIcon(msg.file_type);
                const size = msg.file_size ? formatFileSize(msg.file_size) : '';
                return `<div class="msg-file-attachment">
                    <a href="${msg.file_url}" target="_blank" class="file-doc">
                        <span class="file-icon"><i class="bi ${icon}"></i></span>
                        <span>
                            <span style="font-weight:600;">${(msg.file_name || 'File').replace(/</g,'&lt;')}</span><br>
                            <small>${size}</small>
                        </span>
                    </a>
                </div>`;
            }

            function appendMessage(msg) {
                const empty = document.getElementById('empty-state');
                if (empty) empty.remove();

                if (messagesEl.querySelector('[data-message-id="' + msg.id + '"]')) return;

                const isVisitor = msg.sender_type === 'visitor';
                const isAdmin = msg.sender_type === 'admin';
                const senderType = isVisitor ? 'visitor' : 'admin';

                // Date separator
                const msgDate = formatDate(msg.created_at);
                if (msgDate !== lastMessageDate) {
                    const sep = document.createElement('div');
                    sep.className = 'msg-date-sep';
                    sep.innerHTML = `<span>${msgDate}</span>`;
                    messagesEl.appendChild(sep);
                    lastMessageDate = msgDate;
                    lastSenderType = null;
                }

                // Group spacer
                if (lastSenderType && lastSenderType !== senderType) {
                    const spacer = document.createElement('div');
                    spacer.className = 'msg-group-spacer';
                    messagesEl.appendChild(spacer);
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'msg ' + senderType;
                wrapper.dataset.messageId = msg.id;

                let avatarHtml = '';
                if (isVisitor) {
                    if (lastSenderType !== 'visitor') {
                        avatarHtml = `<img src="/images/visitor-avatar.svg" alt="" class="msg-avatar">`;
                    } else {
                        avatarHtml = `<div class="msg-avatar-spacer"></div>`;
                    }
                }

                // Text content
                const textContent = msg.message ? msg.message.replace(/</g, '&lt;').replace(/>/g, '&gt;') : '';

                // File
                const fileHtml = (msg.file_url || msg.file_path) ? buildFileHtml(msg, isVisitor) : '';

                const timeStr = formatTime(msg.created_at);
                const senderLabel = isVisitor ? 'Visitor' : (isAdmin ? 'You' : 'Bot');

                wrapper.innerHTML = `
                    ${avatarHtml}
                    <div style="max-width: 65%; min-width: 60px;">
                        <div class="msg-bubble">${textContent}${fileHtml}</div>
                        <div class="msg-meta">
                            <span>${timeStr}</span>
                            <span>&middot;</span>
                            <span>${senderLabel}</span>
                        </div>
                    </div>
                `;

                messagesEl.appendChild(wrapper);
                lastMessageId = msg.id;
                lastSenderType = senderType;
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

            // ── Send message (with optional file) ───────────────────
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const text = input.value.trim();
                const hasFile = fileInput.files.length > 0;

                if (!text && !hasFile) return;

                const formData = new FormData();
                formData.append('_token', csrfToken);
                if (text) formData.append('message', text);
                if (hasFile) formData.append('file', fileInput.files[0]);

                input.value = '';
                fileInput.value = '';
                clearFilePreview();
                input.focus();

                try {
                    const res = await fetch(sendUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
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
                        ${unread ? '<span class="badge ms-1" style="background:var(--primary);color:#fff;font-size:9px;padding:2px 6px;">NEW</span>' : ''}
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
