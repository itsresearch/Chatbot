{{-- AJAX-loaded chat content partial --}}
@php
    $visitorLabel = $conversation->visitor
        ? 'Visitor ' . substr($conversation->visitor->visitor_token, 0, 8)
        : 'Unknown visitor';
@endphp

<!-- Header -->
<div class="chat-header-bar"
    style="padding: 14px 24px; border-bottom: 1px solid var(--border-subtle); background: var(--surface); display: flex; align-items: center; justify-content: space-between; min-height: 68px;">
    <div class="d-flex align-items-center gap-3">
        <img src="{{ asset('images/visitor-avatar.svg') }}" alt="" width="42" height="42"
            class="rounded-circle" style="border: 2px solid var(--border-light); object-fit: cover;">
        <div>
            <div style="font-weight: 700; font-size: 15px; color: var(--text-main); line-height: 1.2;">
                {{ $visitorLabel }}</div>
            <div style="font-size: 12px; color: var(--text-muted); line-height: 1.3;">
                {{ $conversation->website->name ?? 'Unknown' }}
                &middot; {{ $conversation->messages->count() }} messages
            </div>
        </div>
    </div>
    <span data-status-badge class="d-inline-flex align-items-center gap-1"
        style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
        {{ $conversation->status === 'human' ? 'background: #fef3c7; color: #92400e;' : 'background: #dcfce7; color: #166534;' }}">
        <span
            style="width: 7px; height: 7px; border-radius: 50%; display: inline-block;
            {{ $conversation->status === 'human' ? 'background: #f59e0b;' : 'background: #22c55e;' }}"></span>
        {{ $conversation->status === 'human' ? 'Human' : 'Bot' }}
    </span>
</div>

<!-- Messages -->
<div id="messages" style="flex: 1; overflow-y: auto; padding: 20px 24px 12px; background: var(--surface-bg);">
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
            <div style="text-align: center; margin: 20px 0 16px;">
                <span
                    style="display: inline-block; padding: 4px 14px; background: var(--surface-elevated); border: 1px solid var(--border-subtle); border-radius: 20px; font-size: 11px; font-weight: 600; color: var(--text-muted);">
                    {{ $msgDate }}
                </span>
            </div>
            @php
                $lastDate = $msgDate;
                $lastSender = null;
                $sameSender = false;
                $showAvatar = $isVisitor;
            @endphp
        @endif

        @if (!$sameSender && $lastSender !== null)
            <div style="height: 14px;"></div>
        @endif

        <div class="msg {{ $isVisitor ? 'visitor' : 'admin' }}" data-message-id="{{ $message->id }}"
            style="display: flex; margin-bottom: 4px; align-items: flex-end; gap: 8px; {{ $isVisitor ? 'justify-content: flex-start;' : 'justify-content: flex-end;' }}">
            @if ($isVisitor)
                @if ($showAvatar || !$sameSender)
                    <img src="{{ asset('images/visitor-avatar.svg') }}" alt=""
                        style="width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0; object-fit: cover; margin-bottom: 18px;">
                @else
                    <div style="width: 30px; flex-shrink: 0;"></div>
                @endif
            @endif
            <div style="max-width: 65%; min-width: 60px;">
                <div
                    style="padding: 10px 14px; border-radius: 18px; font-size: 14px; line-height: 1.55; word-wrap: break-word;
                    {{ $isVisitor
                        ? 'background: var(--surface); border: 1px solid var(--border-subtle); color: var(--text-main); border-bottom-left-radius: 4px;'
                        : 'background: var(--primary); color: #fff; border-bottom-right-radius: 4px; box-shadow: 0 2px 8px rgba(249,115,22,0.25);' }}
                    {{ $sameSender && $isVisitor ? 'border-top-left-radius: 8px;' : '' }}
                    {{ $sameSender && $isAdmin ? 'border-top-right-radius: 8px;' : '' }}">
                    {{ $message->message }}
                    @if ($message->hasFile())
                        <div style="margin-top:{{ $message->message ? '8px' : '0' }}">
                            @if ($message->is_image)
                                <a href="{{ url('/chat-files/' . $message->id) }}" target="_blank">
                                    <img src="{{ url('/chat-files/' . $message->id) }}"
                                        alt="{{ $message->file_name }}" loading="lazy"
                                        style="max-width:260px;max-height:220px;border-radius:10px;display:block;cursor:pointer;">
                                </a>
                            @else
                                <a href="{{ url('/chat-files/' . $message->id) }}" target="_blank"
                                    style="display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;text-decoration:none;
                                    {{ $isVisitor ? 'background:rgba(0,0,0,0.04);color:var(--text-main);' : 'background:rgba(255,255,255,.18);color:#fff;' }}">
                                    <i class="bi bi-file-earmark-arrow-down" style="font-size:20px;"></i>
                                    <span>
                                        <span style="font-weight:600;">{{ $message->file_name }}</span><br>
                                        <small>{{ $message->formattedFileSize() }}</small>
                                    </span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
                <div
                    style="font-size: 11px; color: var(--text-muted); margin-top: 3px; padding: 0 4px; display: flex; align-items: center; gap: 4px; {{ $isAdmin ? 'justify-content: flex-end;' : '' }}">
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
                <div style="color: var(--text-main); font-size: 16px; font-weight: 600;">No messages yet</div>
                <div style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">Send a message to start the
                    conversation</div>
            </div>
        </div>
    @endforelse
</div>

<!-- Input -->
<div style="padding: 12px 24px 14px; border-top: 1px solid var(--border-subtle); background: var(--surface);">
    <div id="file-preview-bar"
        style="display:none;align-items:center;gap:8px;padding:6px 12px;margin-bottom:8px;border-radius:10px;background:var(--surface-soft);border:1px solid var(--border-subtle);font-size:13px;">
        <i class="bi bi-paperclip" style="color: var(--primary);"></i>
        <span id="file-preview-name" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">—</span>
        <span id="file-preview-size" style="color:var(--text-muted);"></span>
        <button type="button" id="remove-file-btn"
            style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:16px;padding:0 4px;">&times;</button>
    </div>
    <form id="admin-send-form">
        @csrf
        <input type="file" id="admin-file-input" style="display:none;"
            accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.rtf,.zip,.rar,.7z">
        <div style="display: flex; align-items: center; gap: 10px; background: var(--surface-soft); border: 1px solid var(--border-subtle); border-radius: 24px; padding: 6px 6px 6px 8px; transition: border-color 0.2s, box-shadow 0.2s;"
            id="chat-input-wrap">
            <button type="button" id="file-attach-btn"
                style="background: none; border: none; color: var(--text-muted); padding: 6px 8px; cursor: pointer; border-radius: 50%; font-size: 20px; display: flex; align-items: center;">
                <i class="bi bi-paperclip"></i>
            </button>
            <input id="admin-message-input" name="message" type="text" placeholder="Type your message…"
                autocomplete="off"
                style="flex: 1; border: none; background: transparent; font-size: 14px; color: var(--text-main); padding: 8px 4px; outline: none;">
            <button type="submit"
                style="width: 40px; height: 40px; border-radius: 50%; border: none; background: var(--primary-gradient); color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0; box-shadow: 0 2px 8px rgba(249,115,22,0.25);">
                <i class="bi bi-send-fill" style="font-size: 18px; margin-left: 2px;"></i>
            </button>
        </div>
    </form>
</div>

<script>
    (function() {
            const messagesEl = document.getElementById('messages');
            const form = document.getElementById('admin-send-form');
            const input = document.getElementById('admin-message-input');
            const fileInput = document.getElementById('admin-file-input');
            const fileAttachBtn = document.getElementById('file-attach-btn');
            const filePreviewBar = document.getElementById('file-preview-bar');
            const filePreviewName = document.getElementById('file-preview-name');
            const filePreviewSize = document.getElementById('file-preview-size');
            const removeFileBtn = document.getElementById('remove-file-btn');
            const chatInputWrap = document.getElementById('chat-input-wrap');
            const sendUrl = "{{ route('client.chat.send', $conversation) }}";
            const messagesUrl = "{{ route('client.chat.messages', $conversation) }}";
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const statusBadges = document.querySelectorAll('[data-status-badge]');

            let lastMessageId = 0;
            let lastSenderType = null;
            let lastMessageDate = null;
            const nodes = messagesEl.querySelectorAll('[data-message-id]');
            if (nodes.length) {
                const lastEl = nodes[nodes.length - 1];
                lastMessageId = parseInt(lastEl.dataset.messageId, 10);
                lastSenderType = lastEl.classList.contains('visitor') ? 'visitor' : 'admin';
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            // Focus styling for input wrapper
            input.addEventListener('focus', () => {
                chatInputWrap.style.borderColor = 'var(--primary)';
                chatInputWrap.style.boxShadow = '0 0 0 3px rgba(249,115,22,0.10)';
            });
            input.addEventListener('blur', () => {
                chatInputWrap.style.borderColor = 'var(--border-subtle)';
                chatInputWrap.style.boxShadow = 'none';
            });

            // ── File attach ─────────────────────────────
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
                    filePreviewSize.textContent = ' (' + fmtSize(file.size) + ')';
                    filePreviewBar.style.display = 'flex';
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
                filePreviewBar.style.display = 'none';
                filePreviewName.textContent = '—';
                filePreviewSize.textContent = '';
                fileAttachBtn.style.color = '';
            }

            function fmtSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / 1048576).toFixed(1) + ' MB';
            }

            function setStatus(s) {
                statusBadges.forEach(b => b.textContent = s === 'human' ? 'Human' : 'Bot');
            }

            function fmtTime(d) {
                const dt = new Date(d);
                return dt.toLocaleTimeString(undefined, {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function fmtDate(d) {
                const dt = new Date(d);
                return dt.toLocaleDateString(undefined, {
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
                    return `<div style="margin-top:${msg.message ? '8px' : '0'}">
                    <a href="${msg.file_url}" target="_blank">
                        <img src="${msg.file_url}" alt="${(msg.file_name||'Image').replace(/</g,'&lt;')}" loading="lazy" style="max-width:260px;max-height:220px;border-radius:10px;display:block;cursor:pointer;">
                    </a>
                </div>`;
                }
                const icon = getFileIcon(msg.file_type);
                const size = msg.file_size ? fmtSize(msg.file_size) : '';
                return `<div style="margin-top:${msg.message ? '8px' : '0'}">
                <a href="${msg.file_url}" target="_blank" style="display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;text-decoration:none;
                    ${isVisitor ? 'background:rgba(0,0,0,0.04);color:var(--text-main);' : 'background:rgba(255,255,255,.18);color:#fff;'}">
                    <i class="bi ${icon}" style="font-size:20px;"></i>
                    <span>
                        <span style="font-weight:600;">${(msg.file_name||'File').replace(/</g,'&lt;')}</span><br>
                        <small>${size}</small>
                    </span>
                </a>
            </div>`;
            }

            function append(msg) {
                const empty = document.getElementById('empty-state');
                if (empty) empty.remove();
                if (messagesEl.querySelector('[data-message-id="' + msg.id + '"]')) return;

                const isV = msg.sender_type === 'visitor';
                const isA = msg.sender_type === 'admin';
                const senderType = isV ? 'visitor' : 'admin';

                // Date separator
                const msgDate = fmtDate(msg.created_at);
                if (msgDate !== lastMessageDate) {
                    const sep = document.createElement('div');
                    sep.style.cssText = 'text-align:center;margin:20px 0 16px;';
                    sep.innerHTML =
                        `<span style="display:inline-block;padding:4px 14px;background:var(--surface-elevated);border:1px solid var(--border-subtle);border-radius:20px;font-size:11px;font-weight:600;color:var(--text-muted);">${msgDate}</span>`;
                    messagesEl.appendChild(sep);
                    lastMessageDate = msgDate;
                    lastSenderType = null;
                }

                // Group spacer
                if (lastSenderType && lastSenderType !== senderType) {
                    const spacer = document.createElement('div');
                    spacer.style.height = '14px';
                    messagesEl.appendChild(spacer);
                }

                const div = document.createElement('div');
                div.className = 'msg ' + senderType;
                div.dataset.messageId = msg.id;
                div.style.cssText = 'display:flex;margin-bottom:4px;align-items:flex-end;gap:8px;' + (isV ?
                    'justify-content:flex-start;' : 'justify-content:flex-end;');

                let avatarHtml = '';
                if (isV) {
                    if (lastSenderType !== 'visitor') {
                        avatarHtml =
                            '<img src="/images/visitor-avatar.svg" alt="" style="width:30px;height:30px;border-radius:50%;flex-shrink:0;object-fit:cover;margin-bottom:18px;">';
                    } else {
                        avatarHtml = '<div style="width:30px;flex-shrink:0;"></div>';
                    }
                }

                const textHtml = msg.message ? msg.message.replace(/</g, '&lt;').replace(/>/g, '&gt;') : '';
                const fileHtml = (msg.file_url || msg.file_path) ? buildFileHtml(msg, isV) : '';
                const sameSender = lastSenderType === senderType;

                div.innerHTML = `${avatarHtml}<div style="max-width:65%;min-width:60px;">
            <div style="padding:10px 14px;border-radius:18px;font-size:14px;line-height:1.55;word-wrap:break-word;
                ${isV ? 'background:var(--surface);border:1px solid var(--border-subtle);color:var(--text-main);border-bottom-left-radius:4px;' : 'background:var(--primary);color:#fff;border-bottom-right-radius:4px;box-shadow:0 2px 8px rgba(249,115,22,0.25);'}
                ${sameSender && isV ? 'border-top-left-radius:8px;' : ''}
                ${sameSender && isA ? 'border-top-right-radius:8px;' : ''}">
                ${textHtml}${fileHtml}
            </div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:3px;padding:0 4px;display:flex;align-items:center;gap:4px;${isV ? '' : 'justify-content:flex-end;'}">
                <span>${fmtTime(msg.created_at)}</span><span>&middot;</span><span>${isV ? 'Visitor' : (isA ? 'You' : 'Bot')}</span>
            </div></div>`;

                messagesEl.appendChild(div);
                lastMessageId = msg.id;
                lastSenderType = senderType;
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            // Polling
            let pollDelay = 3000,
                idleSince = Date.now(),
                pt = null;
            async function poll() {
                try {
                    const r = await fetch(messagesUrl + '?after_id=' + lastMessageId, {
                        headers: {
                            Accept: 'application/json'
                        }
                    });
                    const d = await r.json();
                    if (d.status) setStatus(d.status);
                    if (Array.isArray(d.messages)) d.messages.forEach(append);
                } catch (e) {}
            }
            async function doPoll() {
                const prev = lastMessageId;
                await poll();
                if (lastMessageId > prev) {
                    pollDelay = 3000;
                    idleSince = Date.now();
                } else if (Date.now() - idleSince > 30000) pollDelay = Math.min(pollDelay + 1000, 8000);
                pt = setTimeout(doPoll, pollDelay);
            }
            pt = setTimeout(doPoll, pollDelay);

            form.addEventListener('submit', async function(e) {
                            e.preventDefault();
                            const txt = input.value.trim();
                            const hasFile = fileInput.files.length > 0;
                            if (!txt && !hasFile) return;

                            const formData = new FormData();
                            formData.append('_token', csrfToken);
                            if (txt) formData.append('message', txt);
                            if (hasFile) formData.append('file', fileInput.files[0]);

                            input.value = '';
                            fileInput.value = '';
                            clearFilePreview();
                            input.focus();

                            try {
                                const r = await fetch(sendUrl, {
                                    method: 'POST',
                                    headers: {
                                        Accept: 'application/json',
                                        'X-CSRF-TOKEN': csrfToken
                                    },

                                    // Echo
                                    if (window.Echo) {
                                        try {
                                            window.Echo.channel('chat.{{ $conversation->id }}')
                                                .listen('.MessageSent', e => {
                                                    if (e && e.message) append(e.message);
                                                });
                                        } catch (e) {}
                                    }
                                })();
</script>
