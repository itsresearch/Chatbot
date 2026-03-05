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

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Color
                    Style</label>
                <div class="d-flex gap-2">
                    <label class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 border"
                        style="cursor:pointer; {{ old('widget_color_type', 'gradient') === 'gradient' ? 'border-color: var(--primary) !important; background: rgba(249,115,22,0.06);' : 'border-color: var(--border-subtle);' }}"
                        id="label-gradient">
                        <input type="radio" name="widget_color_type" value="gradient"
                            {{ old('widget_color_type', 'gradient') === 'gradient' ? 'checked' : '' }}
                            style="accent-color: var(--primary);">
                        <span style="font-size: 13px;">
                            <span class="fw-semibold">Gradient</span>
                            <span id="gradient-preview"
                                style="display:inline-block;width:32px;height:16px;border-radius:4px;vertical-align:middle;margin-left:6px;background:linear-gradient(135deg, {{ old('widget_color', '#ff7a18') }}, #ea580c);"></span>
                        </span>
                    </label>
                    <label class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 border"
                        style="cursor:pointer; {{ old('widget_color_type', 'gradient') === 'plain' ? 'border-color: var(--primary) !important; background: rgba(249,115,22,0.06);' : 'border-color: var(--border-subtle);' }}"
                        id="label-plain">
                        <input type="radio" name="widget_color_type" value="plain"
                            {{ old('widget_color_type', 'gradient') === 'plain' ? 'checked' : '' }}
                            style="accent-color: var(--primary);">
                        <span style="font-size: 13px;">
                            <span class="fw-semibold">Plain</span>
                            <span id="plain-preview"
                                style="display:inline-block;width:32px;height:16px;border-radius:4px;vertical-align:middle;margin-left:6px;background:{{ old('widget_color', '#ff7a18') }};"></span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--text-secondary);">Widget
                    Position</label>
                <div class="d-flex gap-2">
                    <label class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 border"
                        style="cursor:pointer; {{ old('widget_position', 'bottom-right') === 'bottom-right' ? 'border-color: var(--primary) !important; background: rgba(249,115,22,0.06);' : 'border-color: var(--border-subtle);' }}"
                        id="label-bottom-right">
                        <input type="radio" name="widget_position" value="bottom-right"
                            {{ old('widget_position', 'bottom-right') === 'bottom-right' ? 'checked' : '' }}
                            style="accent-color: var(--primary);">
                        <span style="font-size: 13px;">
                            <i class="bi bi-arrow-down-right me-1"></i><span class="fw-semibold">Bottom Right</span>
                        </span>
                    </label>
                    <label class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 border"
                        style="cursor:pointer; {{ old('widget_position', 'bottom-right') === 'bottom-left' ? 'border-color: var(--primary) !important; background: rgba(249,115,22,0.06);' : 'border-color: var(--border-subtle);' }}"
                        id="label-bottom-left">
                        <input type="radio" name="widget_position" value="bottom-left"
                            {{ old('widget_position', 'bottom-right') === 'bottom-left' ? 'checked' : '' }}
                            style="accent-color: var(--primary);">
                        <span style="font-size: 13px;">
                            <i class="bi bi-arrow-down-left me-1"></i><span class="fw-semibold">Bottom Left</span>
                        </span>
                    </label>
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
        const colorInput = document.getElementById('widgetColor');
        const colorText = document.getElementById('widgetColorText');
        const gradientPreview = document.getElementById('gradient-preview');
        const plainPreview = document.getElementById('plain-preview');

        function darkenHex(hex, pct) {
            let n = parseInt(hex.replace('#', ''), 16);
            let r = Math.max(0, (n >> 16) - Math.round(2.55 * pct));
            let g = Math.max(0, ((n >> 8) & 0xff) - Math.round(2.55 * pct));
            let b = Math.max(0, (n & 0xff) - Math.round(2.55 * pct));
            return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        }

        colorInput?.addEventListener('input', function() {
            colorText.value = this.value;
            gradientPreview.style.background = 'linear-gradient(135deg, ' + this.value + ', ' + darkenHex(this
                .value, 20) + ')';
            plainPreview.style.background = this.value;
        });

        // Radio highlight
        document.querySelectorAll('input[name="widget_color_type"], input[name="widget_position"]').forEach(function(r) {
            r.addEventListener('change', function() {
                this.closest('.mb-3').querySelectorAll('label.border').forEach(function(l) {
                    l.style.borderColor = 'var(--border-subtle)';
                    l.style.background = '';
                });
                this.closest('label').style.borderColor = 'var(--primary)';
                this.closest('label').style.background = 'rgba(249,115,22,0.06)';
            });
        });
    </script>
@endsection
