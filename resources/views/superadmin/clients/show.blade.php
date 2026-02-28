@extends('layouts.panel')

@section('sidebar-menu')
    <li class="menu-label">Super Admin</li>
    <li><a href="{{ route('superadmin.dashboard') }}"><i class="bi bi-grid-1x2-fill"></i><span
                class="sidebar-text">Dashboard</span></a></li>
    <li><a href="{{ route('superadmin.clients.index') }}" class="active"><i class="bi bi-people-fill"></i><span
                class="sidebar-text">Clients</span></a></li>
@endsection

@section('title', $client->name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('superadmin.clients.index') }}" class="text-decoration-none"
            style="color: var(--text-muted); font-size: 14px;">
            <i class="bi bi-arrow-left me-1"></i>Back to Clients
        </a>
    </div>

    <div class="row">
        <!-- Client Info Card -->
        <div class="col-lg-5 mb-4">
            <div class="card">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($client->name) }}&background=f97316&color=fff&size=56&bold=true"
                        class="rounded-circle" width="56" height="56">
                    <div>
                        <h4 class="mb-0 fw-bold" style="color: var(--text-main);">{{ $client->name }}</h4>
                        <div style="font-size: 13px; color: var(--text-muted);">{{ $client->email }}</div>
                    </div>
                    <div class="ms-auto">
                        <span class="badge {{ $client->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $client->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div class="mb-3" style="font-size: 14px;">
                    <div class="d-flex justify-content-between py-2 border-bottom"
                        style="border-color: var(--border-subtle) !important;">
                        <span style="color: var(--text-muted);">Company</span>
                        <span class="fw-semibold">{{ $client->company_name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom"
                        style="border-color: var(--border-subtle) !important;">
                        <span style="color: var(--text-muted);">Phone</span>
                        <span class="fw-semibold">{{ $client->phone ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom"
                        style="border-color: var(--border-subtle) !important;">
                        <span style="color: var(--text-muted);">Joined</span>
                        <span class="fw-semibold">{{ $client->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.clients.edit', $client) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <form method="POST" action="{{ route('superadmin.clients.toggle-status', $client) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="bi {{ $client->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} me-1"></i>
                            {{ $client->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('superadmin.clients.destroy', $client) }}"
                        onsubmit="return confirm('Delete this client and all their data?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-secondary btn-sm"
                            style="color: #ef4444; border-color: #fecaca;">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Stats + Websites -->
        <div class="col-lg-7 mb-4">
            <div class="row mb-3">
                <div class="col-4">
                    <div class="stat-card">
                        <div style="flex: 1;">
                            <div class="stat-number">{{ $stats['totalWebsites'] }}</div>
                            <div class="stat-label">Websites</div>
                        </div>
                        <div class="stat-icon bg-info-light"><i class="bi bi-globe"></i></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-card">
                        <div style="flex: 1;">
                            <div class="stat-number">{{ $stats['totalConversations'] }}</div>
                            <div class="stat-label">Conversations</div>
                        </div>
                        <div class="stat-icon bg-warning-light"><i class="bi bi-chat-dots"></i></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-card">
                        <div style="flex: 1;">
                            <div class="stat-number">{{ $stats['totalMessages'] }}</div>
                            <div class="stat-label">Messages</div>
                        </div>
                        <div class="stat-icon bg-primary-light"><i class="bi bi-envelope"></i></div>
                    </div>
                </div>
            </div>

            <div class="card p-0">
                <div class="px-4 pt-3 pb-2 border-bottom" style="border-color: var(--border-subtle) !important;">
                    <h6 class="fw-semibold mb-0" style="color: var(--text-main);">Websites</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Domain</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($client->websites as $website)
                                <tr>
                                    <td class="fw-semibold" style="font-size: 13px;">{{ $website->name }}</td>
                                    <td style="font-size: 13px; color: var(--text-muted);">{{ $website->domain }}</td>
                                    <td>
                                        <span class="badge {{ $website->is_active ? 'bg-success' : 'bg-secondary' }}"
                                            style="font-size: 10px;">
                                            {{ $website->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No websites added yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
