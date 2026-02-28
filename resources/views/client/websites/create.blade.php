@extends('layouts.panel')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('title', 'Add Website')

@section('content')
    <div class="mb-4">
        <a href="{{ route('client.websites.index') }}" class="text-decoration-none"
            style="color: var(--text-muted); font-size: 14px;">
            <i class="bi bi-arrow-left me-1"></i>Back to Websites
        </a>
        <h1 class="h3 mb-1 mt-2 fw-bold" style="color: var(--text-main);">Add New Website</h1>
        <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">Register a website to get your chatbot embed
            code.</p>
    </div>

    <div class="card" style="max-width: 640px;">
        <form method="POST" action="{{ route('client.websites.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Website Name
                    *</label>
                <input type="text" name="name" class="form-control search-input @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" placeholder="e.g. My Restaurant" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Domain
                    *</label>
                <input type="text" name="domain" class="form-control search-input @error('domain') is-invalid @enderror"
                    value="{{ old('domain') }}" placeholder="e.g. myrestaurant.com" required>
                @error('domain')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Enter the domain where the chatbot will be embedded.</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Welcome
                    Message</label>
                <input type="text" name="welcome_message"
                    class="form-control search-input @error('welcome_message') is-invalid @enderror"
                    value="{{ old('welcome_message', 'Hi there! How can I help you today?') }}" maxlength="500">
                @error('welcome_message')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">The first message visitors see when they open the chat.</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Widget
                    Color</label>
                <div class="d-flex align-items-center gap-3">
                    <input type="color" name="widget_color" id="widgetColor" class="form-control form-control-color"
                        value="{{ old('widget_color', '#ff7a18') }}"
                        style="width: 50px; height: 40px; border-radius: 10px; cursor: pointer;">
                    <input type="text" id="widgetColorText" class="form-control search-input"
                        value="{{ old('widget_color', '#ff7a18') }}" style="max-width: 120px;" readonly>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Website</button>
                <a href="{{ route('client.websites.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('widgetColor')?.addEventListener('input', function() {
            document.getElementById('widgetColorText').value = this.value;
        });
    </script>
@endsection
