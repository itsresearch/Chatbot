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

@section('title', 'Manage Clients')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold" style="color: var(--text-main);">Clients</h1>
            <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">
                Manage all registered clients and their access.
            </p>
        </div>
        <a href="{{ route('superadmin.clients.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Client
        </a>
    </div>

    <div class="card p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Company</th>
                        <th>Websites</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($client->name) }}&background=f97316&color=fff&size=32&bold=true"
                                        class="rounded-circle" width="32" height="32">
                                    <div>
                                        <div class="fw-semibold" style="font-size: 13px;">{{ $client->name }}</div>
                                        <div style="font-size: 11px; color: var(--text-muted);">{{ $client->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size: 13px;">{{ $client->company_name ?? '—' }}</td>
                            <td>
                                <span class="badge" style="background: var(--primary-soft); color: var(--primary-dark);">
                                    {{ $client->websites_count }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $client->is_active ? 'bg-success' : 'bg-secondary' }}"
                                    style="font-size: 10px;">
                                    {{ $client->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="font-size: 12px; color: var(--text-muted);">
                                {{ $client->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('superadmin.clients.show', $client) }}"
                                        class="btn btn-outline-secondary btn-sm" style="padding: 4px 10px; font-size: 12px;"
                                        title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('superadmin.clients.edit', $client) }}"
                                        class="btn btn-outline-secondary btn-sm" style="padding: 4px 10px; font-size: 12px;"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.clients.toggle-status', $client) }}"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary btn-sm"
                                            style="padding: 4px 10px; font-size: 12px;"
                                            title="{{ $client->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i
                                                class="bi {{ $client->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5" style="color: var(--text-muted);">
                                <i class="bi bi-people d-block mb-2" style="font-size: 32px;"></i>
                                No clients registered yet.
                                <a href="{{ route('superadmin.clients.create') }}" class="d-block mt-2"
                                    style="color: var(--primary);">Add your first client</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($clients->hasPages())
            <div class="px-4 py-3 border-top" style="border-color: var(--border-subtle) !important;">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
@endsection
