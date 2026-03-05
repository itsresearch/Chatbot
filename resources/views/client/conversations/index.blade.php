@extends('layouts.panel')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('title', 'Conversations')

@section('content')
    <div class="card text-center py-5">
        <i class="bi bi-chat-dots d-block mb-3" style="font-size: 48px; color: var(--text-muted);"></i>
        <h5 class="fw-bold mb-2" style="color: var(--text-main);">No conversations yet</h5>
        <p style="color: var(--text-muted); font-size: 14px;">
            @if (session('active_website_id'))
                No conversations found for this website. Try switching to "All Websites" in the header.
            @else
                When visitors start chatting on your website, their conversations will appear here.
            @endif
        </p>
        <div>
            <a href="{{ route('client.websites.index') }}" class="btn btn-primary">
                <i class="bi bi-globe me-1"></i>Manage Websites
            </a>
        </div>
    </div>
@endsection
