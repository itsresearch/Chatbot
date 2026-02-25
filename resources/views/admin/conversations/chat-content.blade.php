<!-- Chat Header -->
<div style="background: var(--surface, #ffffff); border-bottom: 1px solid #f3e8de;"
    class="px-6 py-4 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <div
            class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-lg font-bold text-white shadow-md">
            {{ $conversation->website?->name[0] ?? 'V' }}
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-900">
                @if ($conversation->visitor)
                    Visitor {{ substr($conversation->visitor->visitor_token, 0, 12) }}
                @else
                    Unknown visitor
                @endif
            </p>
            <p class="text-xs text-gray-500 font-medium">
                {{ $conversation->website->name ?? 'Unknown website' }} • {{ $conversation->messages->count() }}
                messages
            </p>
        </div>
    </div>
    <div>
        <span data-status-badge
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-full
            @if ($conversation->status === 'human') bg-amber-100 text-amber-800
            @else bg-green-100 text-green-800 @endif">
            <span
                class="w-2 h-2 rounded-full {{ $conversation->status === 'human' ? 'bg-amber-600' : 'bg-green-600' }}"></span>
            {{ $conversation->status === 'human' ? 'Human' : 'Bot' }}
        </span>
    </div>
</div>

<!-- Messages Container -->
<div id="messages" class="flex-1 overflow-y-auto px-6 py-4 space-y-4 scroll-smooth"
    style="background: linear-gradient(180deg, #fef7f0 0%, #ffffff 40%, #fef7f0 100%);">
    @forelse ($conversation->messages as $message)
        @php
            $isVisitor = $message->sender_type === 'visitor';
            $isAdmin = $message->sender_type === 'admin';
        @endphp
        <div class="flex {{ $isVisitor ? 'justify-start' : 'justify-end' }} group"
            data-message-id="{{ $message->id }}">
            <div class="flex items-end gap-3 {{ $isVisitor ? '' : 'flex-row-reverse' }} max-w-xs md:max-w-sm">
                <div class="flex-shrink-0">
                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow-sm
                        {{ $isVisitor ? 'bg-blue-500 text-white' : 'bg-orange-500 text-white' }}">
                        {{ $isVisitor ? 'V' : 'A' }}
                    </div>
                </div>
                <div class="flex flex-col {{ $isVisitor ? 'items-start' : 'items-end' }}">
                    <div class="px-4 py-3 shadow-sm
                        {{ $isVisitor
                            ? 'bg-white text-gray-900 border border-gray-200'
                            : 'bg-gradient-to-br from-orange-500 to-orange-600 text-white' }}"
                        style="border-radius: 20px; {{ $isVisitor ? 'border-bottom-left-radius: 6px;' : 'border-bottom-right-radius: 6px;' }}">
                        <p class="text-sm leading-relaxed break-words">{{ $message->message }}</p>
                    </div>
                    <div
                        class="text-xs font-medium mt-1.5
                        {{ $isVisitor ? 'text-gray-500' : 'text-orange-600' }}">
                        {{ $message->created_at->format('H:i') }} •
                        {{ $isVisitor ? 'Visitor' : ($isAdmin ? 'You' : 'Bot') }}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div id="empty-state" class="flex items-center justify-center h-full text-center py-16">
            <div>
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                    </path>
                </svg>
                <p class="text-gray-500 font-medium">No messages yet</p>
                <p class="text-gray-400 text-sm mt-1">Messages will appear here</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Message Input -->
<div style="border-top: 1px solid #f3e8de; background: var(--surface, #ffffff);" class="px-6 py-4">
    <form id="admin-send-form" class="space-y-3">
        @csrf
        <div class="flex gap-3 items-end">
            <div class="flex-1">
                <input id="admin-message-input" name="message" type="text"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                    placeholder="Type your reply..." autocomplete="off" />
            </div>
            <button type="submit"
                class="px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-sm font-semibold rounded-lg hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all shadow-sm hover:shadow-md flex-shrink-0">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </div>
        <p class="text-xs text-gray-500 font-medium">Sending a reply will mark this conversation as human.</p>
    </form>
</div>

<script>
    (function() {
        const messagesEl = document.getElementById('messages');
        const form = document.getElementById('admin-send-form');
        const input = document.getElementById('admin-message-input');
        const sendUrl = "{{ route('admin.chat.send', $conversation) }}";
        const messagesUrl = "{{ route('admin.chat.messages', $conversation) }}";
        const csrfToken = form.querySelector('input[name="_token"]').value;
        const statusBadges = document.querySelectorAll('[data-status-badge]');

        let lastMessageId = 0;
        const messageNodes = messagesEl.querySelectorAll('[data-message-id]');
        if (messageNodes.length > 0) {
            lastMessageId = parseInt(messageNodes[messageNodes.length - 1].dataset.messageId, 10);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function setStatusBadge(status) {
            const isHuman = status === 'human';
            statusBadges.forEach(function(badge) {
                const dot = badge.querySelector('.w-2');
                const text = badge.childNodes[badge.childNodes.length - 1];

                if (text && text.nodeType === Node.TEXT_NODE) {
                    text.textContent = isHuman ? 'Human' : 'Bot';
                }

                badge.classList.remove('bg-amber-100', 'text-amber-800', 'bg-green-100', 'text-green-800');
                if (isHuman) {
                    badge.classList.add('bg-amber-100', 'text-amber-800');
                    if (dot) {
                        dot.classList.remove('bg-green-600');
                        dot.classList.add('bg-amber-600');
                    }
                } else {
                    badge.classList.add('bg-green-100', 'text-green-800');
                    if (dot) {
                        dot.classList.remove('bg-amber-600');
                        dot.classList.add('bg-green-600');
                    }
                }
            });
        }

        function formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString('en-US', {
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
            wrapper.className = isVisitor ? 'flex justify-start group' : 'flex justify-end group';
            wrapper.dataset.messageId = message.id;

            const row = document.createElement('div');
            row.className = 'flex items-end gap-3 max-w-xs md:max-w-sm ' + (isVisitor ? '' : 'flex-row-reverse');

            const avatarWrapper = document.createElement('div');
            avatarWrapper.className = 'flex-shrink-0';

            const avatar = document.createElement('div');
            avatar.className =
                'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow-sm ' +
                (isVisitor ? 'bg-blue-500 text-white' : 'bg-orange-500 text-white');
            avatar.textContent = isVisitor ? 'V' : (isAdmin ? 'A' : 'B');

            avatarWrapper.appendChild(avatar);

            const content = document.createElement('div');
            content.className = 'flex flex-col ' + (isVisitor ? 'items-start' : 'items-end');

            const bubble = document.createElement('div');
            bubble.className =
                'px-4 py-3 shadow-sm ' +
                (isVisitor ? 'bg-white text-gray-900 border border-gray-200' :
                    'bg-gradient-to-br from-orange-500 to-orange-600 text-white');
            bubble.style.borderRadius = '20px';
            if (isVisitor) {
                bubble.style.borderBottomLeftRadius = '6px';
            } else {
                bubble.style.borderBottomRightRadius = '6px';
            }

            const text = document.createElement('p');
            text.className = 'text-sm leading-relaxed break-words';
            text.textContent = message.message;

            const meta = document.createElement('div');
            meta.className = 'text-xs font-medium mt-1.5 ' + (isVisitor ? 'text-gray-500' : 'text-orange-600');
            meta.textContent = formatTime(message.created_at) + ' • ' + (isVisitor ? 'Visitor' : (isAdmin ? 'You' :
                'Bot'));

            bubble.appendChild(text);
            content.appendChild(bubble);
            content.appendChild(meta);

            row.appendChild(avatarWrapper);
            row.appendChild(content);
            wrapper.appendChild(row);
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
            } catch (error) {
                console.error('Send error:', error);
            }
        });

        // Adaptive polling: fast when active, slows when idle
        let pollDelay = 3000;
        let pollIdleSince = Date.now();
        let pollTimer = null;

        // ── WebSocket updates via Laravel Echo (Reverb) ──────
        if (window.Echo) {
            window.Echo.channel('chat.{{ $conversation->id }}')
                .listen('.MessageSent', (e) => {
                    if (e && e.message) {
                        const msg = e.message;
                        if (msg.id && msg.id <= lastMessageId) return;

                        // Display new message from admin or bot
                        if (msg.sender_type === 'admin' || msg.sender_type === 'bot') {
                            const existing = messagesEl.querySelector('[data-message-id="' + msg.id + '"]');
                            if (!existing) {
                                appendMessage(msg);
                            }
                        }
                    }
                });
        } else {
            // Fallback to polling if Echo not available
            async function doPoll() {
                // Stop if content was replaced (navigated to another conversation)
                if (!document.contains(messagesEl)) return;

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
        }

        window.addEventListener('beforeunload', function() {
            if (pollTimer) clearTimeout(pollTimer);
            if (window.Echo) {
                window.Echo.leave('chat.{{ $conversation->id }}');
            }
        });
    })();
</script>
