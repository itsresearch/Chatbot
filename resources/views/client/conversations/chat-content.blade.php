{{-- AJAX-loaded chat content partial --}}
@php
    $visitorLabel = $conversation->visitor
        ? 'Visitor ' . substr($conversation->visitor->visitor_token, 0, 8)
        : 'Unknown visitor';
@endphp

<!-- Header -->
<div class="chat-header-bar"
    style="padding: 16px 20px; border-bottom: 1px solid var(--border-subtle); background: var(--surface); display: flex; align-items: center; justify-content: space-between;">
    <div class="d-flex align-items-center gap-3">
        <img src="{{ asset('images/visitor-avatar.svg') }}" alt="" width="40" height="40"
            class="rounded-circle" style="border: 2px solid var(--border-light);">
        <div>
            <div class="fw-semibold" style="color: var(--text-main);">{{ $visitorLabel }}</div>
            <small style="color: var(--text-muted);">
                {{ $conversation->website->name ?? 'Unknown' }}
                &middot; {{ $conversation->messages->count() }} messages
            </small>
        </div>
    </div>
    <span data-status-badge class="badge rounded-pill"
        style="background: rgba(249,115,22,0.10); color: #ea580c; font-weight: 600;">
        {{ $conversation->status === 'human' ? 'Human' : 'Bot' }}
    </span>
</div>

<!-- Messages -->
<div id="messages"
    style="flex: 1; overflow-y: auto; padding: 24px 20px;
    background: linear-gradient(180deg, var(--surface-soft) 0%, var(--surface) 40%, var(--surface-soft) 100%);">
    @forelse ($conversation->messages as $message)
        @php
            $isVisitor = $message->sender_type === 'visitor';
            $isAdmin = $message->sender_type === 'admin';
        @endphp
        <div class="msg {{ $isVisitor ? 'visitor' : 'admin' }}" data-message-id="{{ $message->id }}"
            style="display: flex; margin-bottom: 16px; {{ $isVisitor ? 'justify-content: flex-start;' : 'justify-content: flex-end;' }}">
            <div>
                <div
                    style="max-width: 100%; padding: 12px 16px; border-radius: 18px; font-size: 14px; line-height: 1.5; word-wrap: break-word;
                    {{ $isVisitor
                        ? 'background: var(--surface); border: 1px solid var(--border-subtle); color: var(--text-main); border-bottom-left-radius: 6px;'
                        : 'background: linear-gradient(135deg, var(--primary), #ea580c); color: #fff; border-bottom-right-radius: 6px;' }}">
                    {{ $message->message }}
                </div>
                <div
                    style="font-size: 11px; color: var(--text-muted); margin-top: 4px; {{ $isVisitor ? '' : 'text-align: right;' }}">
                    {{ $message->created_at->format('M d, H:i') }}
                    &middot; {{ $isVisitor ? 'Visitor' : ($isAdmin ? 'You' : 'Bot') }}
                </div>
            </div>
        </div>
    @empty
        <div id="empty-state" class="h-100 d-flex align-items-center justify-content-center">
            <div class="text-center">
                <i class="bi bi-chat-dots d-block mb-2" style="font-size: 40px; color: var(--text-muted);"></i>
                <div style="color: var(--text-muted); font-size: 14px;">No messages yet</div>
            </div>
        </div>
    @endforelse
</div>

<!-- Input -->
<div style="padding: 14px 20px; border-top: 1px solid var(--border-subtle); background: var(--surface);">
    <form id="admin-send-form" class="d-flex gap-2">
        @csrf
        <input id="admin-message-input" name="message" type="text" class="form-control search-input"
            placeholder="Type your reply…" autocomplete="off">
        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
            <span>Send</span><i class="bi bi-send"></i>
        </button>
    </form>
</div>

<script>
    (function() {
        const messagesEl = document.getElementById('messages');
        const form = document.getElementById('admin-send-form');
        const input = document.getElementById('admin-message-input');
        const sendUrl = "{{ route('client.chat.send', $conversation) }}";
        const messagesUrl = "{{ route('client.chat.messages', $conversation) }}";
        const csrfToken = form.querySelector('input[name="_token"]').value;
        const statusBadges = document.querySelectorAll('[data-status-badge]');

        let lastMessageId = 0;
        const nodes = messagesEl.querySelectorAll('[data-message-id]');
        if (nodes.length) {
            lastMessageId = parseInt(nodes[nodes.length - 1].dataset.messageId, 10);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function setStatus(s) {
            statusBadges.forEach(b => b.textContent = s === 'human' ? 'Human' : 'Bot');
        }

        function fmtTime(d) {
            const dt = new Date(d);
            return dt.toLocaleDateString(undefined, {
                    month: 'short',
                    day: 'numeric'
                }) + ', ' +
                dt.toLocaleTimeString(undefined, {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        }

        function append(msg) {
            const empty = document.getElementById('empty-state');
            if (empty) empty.remove();
            if (messagesEl.querySelector('[data-message-id="' + msg.id + '"]')) return;

            const isV = msg.sender_type === 'visitor';
            const isA = msg.sender_type === 'admin';
            const div = document.createElement('div');
            div.className = 'msg ' + (isV ? 'visitor' : 'admin');
            div.dataset.messageId = msg.id;
            div.style.cssText = 'display:flex;margin-bottom:16px;' + (isV ? 'justify-content:flex-start;' :
                'justify-content:flex-end;');

            div.innerHTML = `<div>
            <div style="max-width:100%;padding:12px 16px;border-radius:18px;font-size:14px;line-height:1.5;word-wrap:break-word;
                ${isV ? 'background:var(--surface);border:1px solid var(--border-subtle);color:var(--text-main);border-bottom-left-radius:6px;' : 'background:linear-gradient(135deg,var(--primary),#ea580c);color:#fff;border-bottom-right-radius:6px;'}">
                ${msg.message.replace(/</g,'&lt;').replace(/>/g,'&gt;')}
            </div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:4px;${isV ? '' : 'text-align:right;'}">
                ${fmtTime(msg.created_at)} · ${isV ? 'Visitor' : (isA ? 'You' : 'Bot')}
            </div>
        </div>`;

            messagesEl.appendChild(div);
            lastMessageId = msg.id;
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
            if (!txt) return;
            input.value = '';
            input.focus();
            try {
                const r = await fetch(sendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message: txt
                    })
                });
                const d = await r.json();
                if (d.message) append(d.message);
                if (d.status) setStatus(d.status);
                pollDelay = 3000;
                idleSince = Date.now();
            } catch (e) {}
        });

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
