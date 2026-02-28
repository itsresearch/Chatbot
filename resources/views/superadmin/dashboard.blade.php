@extends('layouts.panel')

@section('sidebar-menu')
    <li class="menu-label">Super Admin</li>
    <li>
        <a href="{{ route('superadmin.dashboard') }}"
            class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('superadmin.clients.index') }}"
            class="{{ request()->routeIs('superadmin.clients.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span class="sidebar-text">Clients</span>
        </a>
    </li>
@endsection

@section('title', 'Super Admin Dashboard')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold" style="color: var(--text-main);">Platform Overview</h1>
            <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">
                Monitor all clients, websites, and conversations across the platform.
            </p>
        </div>
        <a href="{{ route('superadmin.clients.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Client
        </a>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $totalClients }}</div>
                    <div class="stat-label">Clients</div>
                </div>
                <div class="stat-icon bg-primary-light"><i class="bi bi-people"></i></div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $activeClients }}</div>
                    <div class="stat-label">Active</div>
                </div>
                <div class="stat-icon bg-success-light"><i class="bi bi-check-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $totalWebsites }}</div>
                    <div class="stat-label">Websites</div>
                </div>
                <div class="stat-icon bg-info-light"><i class="bi bi-globe"></i></div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $totalConversations }}</div>
                    <div class="stat-label">Conversations</div>
                </div>
                <div class="stat-icon bg-warning-light"><i class="bi bi-chat-dots"></i></div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $totalMessages }}</div>
                    <div class="stat-label">Messages</div>
                </div>
                <div class="stat-icon bg-danger-light"><i class="bi bi-envelope"></i></div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">{{ $messagesToday }}</div>
                    <div class="stat-label">Today</div>
                </div>
                <div class="stat-icon bg-primary-light"><i class="bi bi-activity"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Clients -->
        <div class="col-lg-6 mb-4">
            <div class="card p-0">
                <div class="d-flex align-items-center justify-content-between px-4 pt-3 pb-2 border-bottom"
                    style="border-color: var(--border-subtle) !important;">
                    <div>
                        <h5 class="mb-1 fw-semibold" style="color: var(--text-main);">Recent Clients</h5>
                        <small style="color: var(--text-muted); font-size: 12px;">Newest registered clients</small>
                    </div>
                    <a href="{{ route('superadmin.clients.index') }}" class="text-decoration-none"
                        style="color: var(--primary); font-weight: 500; font-size: 0.9rem;">
                        View all <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Websites</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentClients as $client)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($client->name) }}&background=f97316&color=fff&size=32&bold=true"
                                                alt="" class="rounded-circle" width="32" height="32">
                                            <div>
                                                <div class="fw-semibold" style="font-size: 13px;">{{ $client->name }}</div>
                                                <div style="font-size: 11px; color: var(--text-muted);">
                                                    {{ $client->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge"
                                            style="background: var(--primary-soft); color: var(--primary-dark);">{{ $client->websites_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $client->is_active ? 'bg-success' : 'bg-secondary' }}"
                                            style="font-size: 10px;">
                                            {{ $client->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('superadmin.clients.show', $client) }}"
                                            class="btn btn-outline-secondary btn-sm"
                                            style="padding: 4px 10px; font-size: 12px;">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No clients yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Conversations -->
        <div class="col-lg-6 mb-4">
            <div class="card p-0">
                <div class="d-flex align-items-center justify-content-between px-4 pt-3 pb-2 border-bottom"
                    style="border-color: var(--border-subtle) !important;">
                    <h5 class="mb-1 fw-semibold" style="color: var(--text-main);">Recent Conversations</h5>
                    <small style="color: var(--text-muted); font-size: 12px;">Across all clients</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Visitor</th>
                                <th>Website</th>
                                <th>Client</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentConversations as $conv)
                                <tr>
                                    <td style="font-size: 13px;">
                                        {{ $conv->visitor ? 'Visitor ' . substr($conv->visitor->visitor_token, 0, 8) : 'Unknown' }}
                                    </td>
                                    <td style="font-size: 13px;">{{ $conv->website->name ?? '-' }}</td>
                                    <td style="font-size: 13px;">{{ $conv->website->owner->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $conv->status === 'human' ? 'bg-warning' : 'bg-success' }}"
                                            style="font-size: 10px;">
                                            {{ ucfirst($conv->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No conversations yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
