@extends('layouts.panel')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold" style="color: var(--text-main);">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">
                Here's an overview of your chatbot activity.
            </p>
        </div>
        <a href="{{ route('client.websites.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Website
        </a>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $stats['totalConversations'] }}</div>
                    <div class="stat-label">Total Conversations</div>
                </div>
                <div class="stat-icon bg-primary-light"><i class="bi bi-chat-dots"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $stats['humanConversations'] }}</div>
                    <div class="stat-label">Handled by You</div>
                </div>
                <div class="stat-icon bg-success-light"><i class="bi bi-person-check"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $stats['botConversations'] }}</div>
                    <div class="stat-label">Handled by Bot</div>
                </div>
                <div class="stat-icon bg-warning-light"><i class="bi bi-robot"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $stats['messagesToday'] }}</div>
                    <div class="stat-label">Messages Today</div>
                </div>
                <div class="stat-icon bg-danger-light"><i class="bi bi-activity"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Websites -->
        <div class="col-lg-5 mb-4">
            <div class="card p-0">
                <div class="d-flex align-items-center justify-content-between px-4 pt-3 pb-2 border-bottom"
                    style="border-color: var(--border-subtle) !important;">
                    <div>
                        <h5 class="mb-1 fw-semibold" style="color: var(--text-main);">Your Websites</h5>
                        <small style="color: var(--text-muted); font-size: 12px;">{{ $websites->count() }}
                            registered</small>
                    </div>
                    <a href="{{ route('client.websites.index') }}" class="text-decoration-none"
                        style="color: var(--primary); font-weight: 500; font-size: 0.9rem;">
                        Manage <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Website</th>
                                <th>Chats</th>
                                <th>Visitors</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($websites as $website)
                                <tr>
                                    <td>
                                        <a href="{{ route('client.websites.show', $website) }}"
                                            class="text-decoration-none">
                                            <div class="fw-semibold" style="font-size: 13px; color: var(--text-main);">
                                                {{ $website->name }}</div>
                                            <div style="font-size: 11px; color: var(--text-muted);">{{ $website->domain }}
                                            </div>
                                        </a>
                                    </td>
                                    <td><span class="badge"
                                            style="background: var(--primary-soft); color: var(--primary-dark);">{{ $website->conversations_count }}</span>
                                    </td>
                                    <td><span class="badge"
                                            style="background: rgba(59,130,246,0.10); color: #3b82f6;">{{ $website->visitors_count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4" style="color: var(--text-muted);">
                                        No websites yet.
                                        <a href="{{ route('client.websites.create') }}" style="color: var(--primary);">Add
                                            one now</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Conversations -->
        <div class="col-lg-7 mb-4">
            <div class="card p-0">
                <div class="d-flex align-items-center justify-content-between px-4 pt-3 pb-2 border-bottom"
                    style="border-color: var(--border-subtle) !important;">
                    <div>
                        <h5 class="mb-1 fw-semibold" style="color: var(--text-main);">Recent Conversations</h5>
                        <small style="color: var(--text-muted); font-size: 12px;">Latest visitor chats</small>
                    </div>
                    <a href="{{ route('client.conversations') }}" class="text-decoration-none"
                        style="color: var(--primary); font-weight: 500; font-size: 0.9rem;">
                        Open inbox <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>
                <div class="chat-list" style="border-right: none; max-height: 400px;">
                    @forelse ($recentConversations as $conversation)
                        @php
                            $lastMessage = $conversation->messages->sortBy('created_at')->last();
                            $label = $conversation->visitor
                                ? 'Visitor ' . substr($conversation->visitor->visitor_token, 0, 8)
                                : 'Unknown visitor';
                            $isUnread =
                                $conversation->last_message_at &&
                                (!$conversation->admin_viewed_at ||
                                    $conversation->last_message_at > $conversation->admin_viewed_at);
                        @endphp
                        <a href="{{ route('client.chat', $conversation) }}"
                            class="chat-item d-flex align-items-center {{ $isUnread ? 'chat-item-unread' : '' }}"
                            style="text-decoration: none;">
                            <img src="{{ asset('images/visitor-avatar.svg') }}" alt="Avatar" width="40"
                                height="40" class="rounded-circle me-3 flex-shrink-0"
                                style="border: 2px solid var(--border-light);">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="chat-item-name {{ $isUnread ? 'fw-bold' : '' }}">{{ $label }}
                                        </div>
                                        @if ($isUnread)
                                            <span class="badge rounded-pill"
                                                style="background-color: rgba(249,115,22,0.12); color: #ea580c; font-size: 0.7rem; font-weight: 600;">New</span>
                                        @endif
                                    </div>
                                    <div class="chat-item-time">
                                        {{ $conversation->last_message_at?->diffForHumans() ?? '—' }}
                                    </div>
                                </div>
                                <div class="chat-item-message {{ $isUnread ? 'fw-bold' : '' }}">
                                    @if ($lastMessage)
                                        @if ($lastMessage->sender_type === 'admin')
                                            <span style="color: var(--primary); font-weight: 600;">You:</span>
                                        @endif
                                        {{ \Illuminate\Support\Str::limit($lastMessage->message, 70) }}
                                    @else
                                        <span style="color: var(--text-muted);">No messages yet</span>
                                    @endif
                                </div>
                                <div class="mt-1 small" style="color: var(--text-muted); font-size: 11px;">
                                    {{ $conversation->website->name ?? '' }} ·
                                    {{ $conversation->last_message_at?->format('d M') }}
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-5 text-center" style="color: var(--text-muted);">
                            No conversations yet. Add a website and embed the widget to start receiving chats.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
