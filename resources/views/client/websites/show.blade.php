@extends('layouts.panel')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('title', $website->name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('client.websites.index') }}" class="text-decoration-none"
            style="color: var(--text-muted); font-size: 14px;">
            <i class="bi bi-arrow-left me-1"></i>Back to Websites
        </a>
    </div>

    <div class="row">
        <!-- Website Details -->
        <div class="col-lg-5 mb-4">
            <div class="card mb-3">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div
                        style="width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
                        background: linear-gradient(135deg, {{ $website->widget_color }}20, {{ $website->widget_color }}10);
                        color: {{ $website->widget_color }}; font-size: 24px; font-weight: 700;">
                        {{ strtoupper(substr($website->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold" style="color: var(--text-main);">{{ $website->name }}</h4>
                        <div style="font-size: 13px; color: var(--text-muted);">{{ $website->domain }}</div>
                    </div>
                    <span class="ms-auto badge {{ $website->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $website->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="d-flex gap-3 mb-3">
                    <div class="text-center flex-fill py-2" style="background: var(--surface-soft); border-radius: 10px;">
                        <div class="fw-bold" style="color: var(--text-main);">{{ $website->conversations_count }}</div>
                        <div style="color: var(--text-muted); font-size: 11px;">Conversations</div>
                    </div>
                    <div class="text-center flex-fill py-2" style="background: var(--surface-soft); border-radius: 10px;">
                        <div class="fw-bold" style="color: var(--text-main);">{{ $website->visitors_count }}</div>
                        <div style="color: var(--text-muted); font-size: 11px;">Visitors</div>
                    </div>
                </div>

                <div style="font-size: 14px;">
                    <div class="d-flex justify-content-between py-2 border-bottom"
                        style="border-color: var(--border-subtle) !important;">
                        <span style="color: var(--text-muted);">Welcome Message</span>
                        <span class="fw-semibold text-end" style="max-width: 60%;">{{ $website->welcome_message }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom"
                        style="border-color: var(--border-subtle) !important;">
                        <span style="color: var(--text-muted);">Widget Color</span>
                        <div class="d-flex align-items-center gap-2">
                            <div
                                style="width: 18px; height: 18px; border-radius: 4px; background: {{ $website->widget_color }};">
                            </div>
                            <span class="fw-semibold">{{ $website->widget_color }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-color: var(--border-subtle) !important;">
                        <span style="color: var(--text-muted);">Created</span>
                        <span class="fw-semibold">{{ $website->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('client.websites.edit', $website) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <form method="POST" action="{{ route('client.websites.regenerate-key', $website) }}"
                        onsubmit="return confirm('Regenerate API key? Your current widget embed code will stop working.');">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-repeat me-1"></i>Regenerate Key
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Embed Code -->
        <div class="col-lg-7 mb-4">
            <div class="card">
                <h5 class="fw-bold mb-1" style="color: var(--text-main);">Embed Code</h5>
                <p style="color: var(--text-muted); font-size: 13px;" class="mb-3">
                    Copy and paste this code into your website's HTML, just before the closing <code>&lt;/body&gt;</code>
                    tag.
                </p>

                <div style="background: #1e293b; border-radius: 12px; padding: 20px; position: relative;">
                    <button type="button" id="copyBtn" class="btn btn-sm" title="Copy"
                        style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.1); color: #94a3b8; border: none; border-radius: 8px; padding: 6px 12px; font-size: 12px;">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                    <pre id="embedCode"
                        style="color: #e2e8f0; font-size: 13px; margin: 0; white-space: pre-wrap; word-break: break-all; font-family: 'Fira Code', monospace;"><code>&lt;script&gt;
  window.ChatbotWidgetConfig = {
    apiUrl: '{{ $serverUrl }}/api/chat/send',
    apiKey: '{{ $website->api_key }}',
    serverUrl: '{{ $serverUrl }}',
    logoUrl: '{{ $serverUrl }}/images/chatbot-logo.png'
  };
&lt;/script&gt;
&lt;script src="{{ $serverUrl }}/widget/widget-loader.js"&gt;&lt;/script&gt;</code></pre>
                </div>

                <div class="mt-3 p-3"
                    style="background: var(--surface-soft); border-radius: 10px; border: 1px solid var(--border-subtle);">
                    <div class="fw-semibold mb-1" style="font-size: 13px; color: var(--text-main);">
                        <i class="bi bi-key me-2" style="color: var(--primary);"></i>API Key
                    </div>
                    <code
                        style="font-size: 12px; color: var(--text-secondary); word-break: break-all;">{{ $website->api_key }}</code>
                </div>

                <div class="mt-3 p-3" style="background: #eff6ff; border-radius: 10px; border: 1px solid #bfdbfe;">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-info-circle" style="color: #3b82f6; font-size: 16px; margin-top: 2px;"></i>
                        <div style="font-size: 13px; color: #1e40af;">
                            <strong>Note:</strong> Keep your API key private. The widget will automatically adapt its color
                            and welcome message based on your settings above.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('copyBtn')?.addEventListener('click', function() {
            const code = document.getElementById('embedCode').textContent;
            navigator.clipboard.writeText(code).then(() => {
                this.innerHTML = '<i class="bi bi-check2 me-1"></i>Copied!';
                this.style.color = '#10b981';
                setTimeout(() => {
                    this.innerHTML = '<i class="bi bi-clipboard me-1"></i>Copy';
                    this.style.color = '#94a3b8';
                }, 2000);
            });
        });
    </script>
@endsection
