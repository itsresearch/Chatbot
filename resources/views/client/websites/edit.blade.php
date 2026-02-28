@extends('layouts.panel')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('title', 'Edit Website')

@section('content')
    <div class="mb-4">
        <a href="{{ route('client.websites.show', $website) }}" class="text-decoration-none"
            style="color: var(--text-muted); font-size: 14px;">
            <i class="bi bi-arrow-left me-1"></i>Back to {{ $website->name }}
        </a>
        <h1 class="h3 mb-1 mt-2 fw-bold" style="color: var(--text-main);">Edit Website</h1>
    </div>

    <div class="card" style="max-width: 640px;">
        <form method="POST" action="{{ route('client.websites.update', $website) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Website Name
                    *</label>
                <input type="text" name="name" class="form-control search-input @error('name') is-invalid @enderror"
                    value="{{ old('name', $website->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Domain
                    *</label>
                <input type="text" name="domain" class="form-control search-input @error('domain') is-invalid @enderror"
                    value="{{ old('domain', $website->domain) }}" required>
                @error('domain')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Welcome
                    Message</label>
                <input type="text" name="welcome_message" class="form-control search-input"
                    value="{{ old('welcome_message', $website->welcome_message) }}" maxlength="500">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Widget
                    Color</label>
                <div class="d-flex align-items-center gap-3">
                    <input type="color" name="widget_color" id="widgetColor" class="form-control form-control-color"
                        value="{{ old('widget_color', $website->widget_color) }}"
                        style="width: 50px; height: 40px; border-radius: 10px; cursor: pointer;">
                    <input type="text" id="widgetColorText" class="form-control search-input"
                        value="{{ old('widget_color', $website->widget_color) }}" style="max-width: 120px;" readonly>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Website</button>
                <a href="{{ route('client.websites.show', $website) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>

        <hr style="border-color: var(--border-subtle);" class="my-4">

        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-semibold" style="font-size: 14px; color: #ef4444;">Danger Zone</div>
                <div style="font-size: 12px; color: var(--text-muted);">Permanently delete this website and all its data.
                </div>
            </div>
            <form method="POST" action="{{ route('client.websites.destroy', $website) }}"
                onsubmit="return confirm('Delete this website? All conversations, messages, and visitors will be permanently removed.');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm"
                    style="color: #ef4444; border: 1px solid #fecaca; border-radius: 10px; padding: 7px 14px; font-size: 13px;">
                    <i class="bi bi-trash me-1"></i>Delete Website
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('widgetColor')?.addEventListener('input', function() {
            document.getElementById('widgetColorText').value = this.value;
        });
    </script>
@endsection
