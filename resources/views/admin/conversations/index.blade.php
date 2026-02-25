@extends('layouts.admin')

@section('title', 'Conversations')

@section('content')
    <div class="d-flex" style="height: calc(100vh - 100px); background: #f9fafb; overflow: hidden;">
        <!-- Sidebar - Conversations List -->
        <div
            style="width: 320px; background: white; border-right: 1px solid #e5e7eb; display: flex; flex-direction: column;">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-900">Messages</h1>
                <p class="text-xs text-gray-500 mt-1">{{ $conversations->count() }} conversations</p>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto" id="conversations-list">
                @forelse ($conversations as $conversation)
                    @php
                        $lastMessage = $conversation->messages->last();
                        $isUnread =
                            $conversation->last_message_at &&
                            (!$conversation->admin_viewed_at ||
                                $conversation->last_message_at > $conversation->admin_viewed_at);
                        $hasVisitorMessage = $lastMessage && $lastMessage->sender_type === 'visitor';
                    @endphp
                    {{-- Only show conversations with valid visitors --}}
                    @if ($conversation->visitor)
                        <div class="conversation-item border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition {{ $isUnread && $hasVisitorMessage ? 'bg-orange-50 hover:bg-orange-100' : '' }}"
                            data-conversation-id="{{ $conversation->id }}"
                            onclick="loadConversation({{ $conversation->id }})">
                            <div class="p-4 flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-12 h-12 rounded-full bg-gradient-to-br {{ $isUnread && $hasVisitorMessage ? 'from-orange-400 to-orange-600' : 'from-blue-400 to-blue-600' }} flex items-center justify-center text-white font-bold text-sm shadow">
                                        {{ $conversation->website?->name[0] ?? 'V' }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <h3
                                            class="text-sm {{ $isUnread && $hasVisitorMessage ? 'font-bold text-gray-900' : 'font-semibold text-gray-800' }} truncate">
                                            Visitor {{ substr($conversation->visitor->visitor_token, 0, 12) }}
                                        </h3>
                                        @if ($isUnread && $hasVisitorMessage)
                                            <span class="flex-shrink-0 w-2.5 h-2.5 bg-orange-500 rounded-full"></span>
                                        @endif
                                    </div>
                                    <p
                                        class="text-xs {{ $isUnread && $hasVisitorMessage ? 'font-semibold text-gray-700' : 'text-gray-500' }} truncate">
                                        @if ($lastMessage)
                                            {{ \Illuminate\Support\Str::limit($lastMessage->message, 40) }}
                                        @else
                                            No messages yet
                                        @endif
                                    </p>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span
                                            class="text-xs px-2 py-0.5 rounded-full 
                                        {{ $conversation->status === 'human' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $conversation->status === 'human' ? 'Human' : 'Bot' }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : 'No activity' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
            </div>
            @endif
        @empty
            <div class="p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                    </path>
                </svg>
                <p class="text-gray-500 font-medium">No conversations yet</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="flex-1 flex flex-col bg-gradient-to-br from-gray-50 to-gray-100">
        <div id="chat-container" class="flex-1 flex items-center justify-center">
            <div class="text-center">
                <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                    </path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Select a conversation</h2>
                <p class="text-gray-500">Choose a conversation from the left to view messages</p>
            </div>
        </div>
    </div>
    </div>

    <script>
        let activeConversationId = null;

        function loadConversation(conversationId) {
            activeConversationId = conversationId;

            // Update active state
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('bg-blue-50', 'border-l-4', 'border-l-orange-500');
            });

            const selectedItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
            if (selectedItem) {
                selectedItem.classList.add('bg-blue-50', 'border-l-4', 'border-l-orange-500');
                // Remove unread styling
                selectedItem.classList.remove('bg-orange-50');
                const badge = selectedItem.querySelector('.bg-orange-500');
                if (badge) badge.remove();
            }

            // Load conversation via AJAX
            const chatContainer = document.getElementById('chat-container');
            chatContainer.innerHTML =
                '<div class="flex items-center justify-center h-full"><div class="text-gray-500">Loading...</div></div>';

            fetch(`/admin/chat/${conversationId}/content`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    chatContainer.innerHTML = html;

                    // Mark as viewed
                    fetch(`/admin/chat/${conversationId}/mark-viewed`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading conversation:', error);
                    chatContainer.innerHTML =
                        '<div class="flex items-center justify-center h-full"><div class="text-red-500">Error loading conversation</div></div>';
                });
        }

        // ── Sidebar live-refresh ──────────────────────────────
        const conversationsList = document.getElementById('conversations-list');
        let sidebarTimer = null;

        function buildConversationItem(conv) {
            const isActive = conv.id === activeConversationId;
            const token = conv.visitor_token || '';
            const label = 'Visitor ' + token.substring(0, 12);
            const unread = conv.is_unread && !isActive;
            const initial = conv.website_name ? conv.website_name[0] : 'V';

            return `<div class="conversation-item border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition ${unread ? 'bg-orange-50 hover:bg-orange-100' : ''} ${isActive ? 'bg-blue-50 border-l-4 border-l-orange-500' : ''}"
                data-conversation-id="${conv.id}"
                onclick="loadConversation(${conv.id})">
                <div class="p-4 flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br ${unread ? 'from-orange-400 to-orange-600' : 'from-blue-400 to-blue-600'} flex items-center justify-center text-white font-bold text-sm shadow">
                            ${initial}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <h3 class="text-sm ${unread ? 'font-bold text-gray-900' : 'font-semibold text-gray-800'} truncate">
                                ${label}
                            </h3>
                            ${unread ? '<span class="flex-shrink-0 w-2.5 h-2.5 bg-orange-500 rounded-full"></span>' : ''}
                        </div>
                        <p class="text-xs ${unread ? 'font-semibold text-gray-700' : 'text-gray-500'} truncate">
                            ${conv.last_message || 'No messages yet'}
                        </p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="text-xs px-2 py-0.5 rounded-full ${conv.status === 'human' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800'}">
                                ${conv.status === 'human' ? 'Human' : 'Bot'}
                            </span>
                            <span class="text-xs text-gray-400">
                                ${conv.last_message_at_human || 'No activity'}
                            </span>
                        </div>
                    </div>
                </div>
            </div>`;
        }

        async function refreshSidebar() {
            try {
                const res = await fetch("{{ route('admin.conversations.list') }}", {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const conversations = await res.json();
                if (!Array.isArray(conversations) || conversations.length === 0) return;

                // Update header count
                const countEl = conversationsList.closest('div')?.querySelector('.text-xs.text-gray-500');
                if (countEl) countEl.textContent = conversations.length + ' conversations';

                conversationsList.innerHTML = conversations.map(buildConversationItem).join('');
            } catch (err) {
                console.error('Sidebar refresh error:', err);
            }
        }

        async function doSidebarPoll() {
            await refreshSidebar();
            sidebarTimer = setTimeout(doSidebarPoll, 5000);
        }

        sidebarTimer = setTimeout(doSidebarPoll, 5000);

        window.addEventListener('beforeunload', function() {
            if (sidebarTimer) clearTimeout(sidebarTimer);
        });

        @if ($conversations->count() > 0)
            // Uncomment to auto-load first conversation
            // loadConversation({{ $conversations->first()->id }});
        @endif
    </script>
@endsection
