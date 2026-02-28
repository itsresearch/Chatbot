@extends('layouts.panel')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('title', 'Websites')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold" style="color: var(--text-main);">Your Websites</h1>
            <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">
                Manage your websites and chatbot widgets.
            </p>
        </div>
        <a href="{{ route('client.websites.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Website
        </a>
    </div>

    <div class="row">
        @forelse ($websites as $website)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                style="width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
                                background: linear-gradient(135deg, {{ $website->widget_color }}20, {{ $website->widget_color }}10);
                                color: {{ $website->widget_color }}; font-size: 20px; font-weight: 700;">
                                {{ strtoupper(substr($website->name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold" style="color: var(--text-main);">{{ $website->name }}</h6>
                                <small style="color: var(--text-muted);">{{ $website->domain }}</small>
                            </div>
                        </div>
                        <span class="badge {{ $website->is_active ? 'bg-success' : 'bg-secondary' }}"
                            style="font-size: 10px;">
                            {{ $website->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="d-flex gap-3 mb-3" style="font-size: 13px;">
                        <div class="text-center flex-fill py-2"
                            style="background: var(--surface-soft); border-radius: 10px;">
                            <div class="fw-bold" style="color: var(--text-main);">{{ $website->conversations_count }}</div>
                            <div style="color: var(--text-muted); font-size: 11px;">Conversations</div>
                        </div>
                        <div class="text-center flex-fill py-2"
                            style="background: var(--surface-soft); border-radius: 10px;">
                            <div class="fw-bold" style="color: var(--text-main);">{{ $website->visitors_count }}</div>
                            <div style="color: var(--text-muted); font-size: 11px;">Visitors</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-auto">
                        <a href="{{ route('client.websites.show', $website) }}" class="btn btn-primary btn-sm flex-fill">
                            <i class="bi bi-eye me-1"></i>View
                        </a>
                        <a href="{{ route('client.websites.edit', $website) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card text-center py-5">
                    <i class="bi bi-globe d-block mb-3" style="font-size: 48px; color: var(--text-muted);"></i>
                    <h5 class="fw-bold mb-2" style="color: var(--text-main);">No websites yet</h5>
                    <p style="color: var(--text-muted); font-size: 14px;">Add your first website to start using the chatbot
                        widget.</p>
                    <div>
                        <a href="{{ route('client.websites.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Add Your First Website
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
