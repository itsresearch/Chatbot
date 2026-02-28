@extends('layouts.panel')

@section('sidebar-menu')
    <li class="menu-label">Super Admin</li>
    <li><a href="{{ route('superadmin.dashboard') }}"><i class="bi bi-grid-1x2-fill"></i><span
                class="sidebar-text">Dashboard</span></a></li>
    <li><a href="{{ route('superadmin.clients.index') }}" class="active"><i class="bi bi-people-fill"></i><span
                class="sidebar-text">Clients</span></a></li>
@endsection

@section('title', 'Edit Client')

@section('content')
    <div class="mb-4">
        <a href="{{ route('superadmin.clients.show', $client) }}" class="text-decoration-none"
            style="color: var(--text-muted); font-size: 14px;">
            <i class="bi bi-arrow-left me-1"></i>Back to {{ $client->name }}
        </a>
        <h1 class="h3 mb-1 mt-2 fw-bold" style="color: var(--text-main);">Edit Client</h1>
    </div>

    <div class="card" style="max-width: 640px;">
        <form method="POST" action="{{ route('superadmin.clients.update', $client) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Full Name
                    *</label>
                <input type="text" name="name" class="form-control search-input @error('name') is-invalid @enderror"
                    value="{{ old('name', $client->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Email Address
                    *</label>
                <input type="email" name="email" class="form-control search-input @error('email') is-invalid @enderror"
                    value="{{ old('email', $client->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Company
                    Name</label>
                <input type="text" name="company_name" class="form-control search-input"
                    value="{{ old('company_name', $client->company_name) }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Phone</label>
                <input type="text" name="phone" class="form-control search-input"
                    value="{{ old('phone', $client->phone) }}">
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                        {{ old('is_active', $client->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="isActive" style="font-size: 13px;">Account
                        Active</label>
                </div>
            </div>

            <hr style="border-color: var(--border-subtle);">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">New
                        Password</label>
                    <input type="password" name="password"
                        class="form-control search-input @error('password') is-invalid @enderror"
                        placeholder="Leave blank to keep current">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Confirm
                        Password</label>
                    <input type="password" name="password_confirmation" class="form-control search-input">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Client</button>
                <a href="{{ route('superadmin.clients.show', $client) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
