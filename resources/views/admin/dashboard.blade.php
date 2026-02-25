@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-semibold text-light">Chatbot overview</h1>
            <p class="mb-0" style="color: var(--text-muted);">Real-time view of conversations, load, and activity.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $totalConversations }}</div>
                    <div class="stat-label">Total conversations</div>
                </div>
                <div class="stat-icon bg-primary-light">
                    <i class="bi bi-chat-dots"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $humanConversations }}</div>
                    <div class="stat-label">Handled by humans</div>
                </div>
                <div class="stat-icon bg-success-light">
                    <i class="bi bi-person-check"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $botConversations }}</div>
                    <div class="stat-label">Handled by bot</div>
                </div>
                <div class="stat-icon bg-warning-light">
                    <i class="bi bi-robot"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $messagesToday }}</div>
                    <div class="stat-label">Messages today</div>
                </div>
                <div class="stat-icon bg-danger-light">
                    <i class="bi bi-activity"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Conversations Section -->
    <div class="card p-0">
        <div class="d-flex align-items-center justify-content-between px-3 px-md-4 pt-3 pb-2 border-bottom"
            style="border-color: #020617 !important;">
            <div>
                <h5 class="mb-1 text-light fw-semibold">Recent conversations</h5>
                <small style="color: var(--text-muted);">
                    Last {{ $recentConversations->count() }} active threads
                </small>
            </div>
            <a href="{{ route('admin.conversations') }}" class="text-decoration-none"
                style="color: var(--primary); font-weight: 500; font-size: 0.9rem;">
                Open inbox <i class="bi bi-arrow-right-short"></i>
            </a>
        </div>

        <div class="chat-list" style="border-right: none;">
            @forelse ($recentConversations as $conversation)
                @php
                    $lastMessage = $conversation->messages->sortBy('created_at')->last();
                    $label = $conversation->visitor
                        ? 'Visitor ' . substr($conversation->visitor->visitor_token, 0, 8)
                        : 'Unknown visitor';
                    $avatarName = urlencode($label);
                    $isUnread =
                        $conversation->last_message_at &&
                        (!$conversation->admin_viewed_at ||
                            $conversation->last_message_at > $conversation->admin_viewed_at);
                @endphp
                <a href="{{ route('admin.chat', $conversation) }}" class="chat-item d-flex align-items-center"
                    style="text-decoration: none;">
                    <img src="https://ui-avatars.com/api/?name={{ $avatarName }}&background=020617&color=f97316&size=64"
                        alt="Avatar" width="40" height="40" class="rounded-circle me-3 flex-shrink-0">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <div class="chat-item-name {{ $isUnread ? 'fw-bold' : '' }}">
                                    {{ $label }}
                                </div>
                                @if ($isUnread)
                                    <span class="badge rounded-pill"
                                        style="background-color: rgba(249,115,22,0.18); color: #fed7aa; font-size: 0.7rem;">
                                        New
                                    </span>
                                @endif
                            </div>
                            <div class="chat-item-time">
                                @if ($conversation->last_message_at)
                                    {{ $conversation->last_message_at->diffForHumans() }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                        <div class="chat-item-message">
                            @if ($lastMessage)
                                {{ \Illuminate\Support\Str::limit($lastMessage->message, 70) }}
                            @else
                                <span style="color: var(--text-muted);">No messages yet</span>
                            @endif
                        </div>
                        <div class="mt-1 small" style="color: var(--text-muted);">
                            {{ $conversation->website->name ?? 'Unknown website' }}
                            · {{ $conversation->messages->count() }} messages
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-4 py-5 text-center" style="color: var(--text-muted);">
                    No conversations yet. Once visitors start chatting, you will see them here.
                </div>
            @endforelse
        </div>
    </div>
@endsection
