@extends('layouts.panel')
@section('title', $subService->name . ' — Detail')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('client.chatbot.categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('client.chatbot.categories.show', $subService->service->category) }}">{{ $subService->service->category->name }}</a>
                </li>
                <li class="breadcrumb-item"><a
                        href="{{ route('client.chatbot.services.show', $subService->service) }}">{{ $subService->service->name }}</a>
                </li>
                <li class="breadcrumb-item active">{{ $subService->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--text-main);">{{ $subService->name }}</h4>
            @if ($subService->short_description)
                <p class="text-muted mb-0">{{ $subService->short_description }}</p>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('client.chatbot.sub-services.edit', $subService) }}"
                class="btn btn-outline-warning btn-sm px-3">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <form action="{{ route('client.chatbot.sub-services.destroy', $subService) }}" method="POST"
                onsubmit="return confirm('Delete this sub-service?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm px-3">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        @if ($subService->detail_content)
            <div class="border rounded p-4 bg-white">
                <h6 class="fw-bold text-muted mb-3">
                    <i class="bi bi-file-richtext me-1"></i> Detail Content (shown to visitors)
                </h6>
                <div class="content-preview">
                    {!! $subService->detail_content !!}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-file-text" style="font-size: 36px; color: var(--text-muted);"></i>
                <p class="text-muted mt-2 mb-0">No detail content added yet. <a
                        href="{{ route('client.chatbot.sub-services.edit', $subService) }}">Add content</a></p>
            </div>
        @endif
    </div>

    <style>
        .content-preview h2 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-top: 1rem;
        }

        .content-preview h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 0.8rem;
        }

        .content-preview p {
            margin-bottom: 0.8rem;
            line-height: 1.6;
        }

        .content-preview ul,
        .content-preview ol {
            padding-left: 1.5rem;
            margin-bottom: 0.8rem;
        }

        .content-preview table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        .content-preview table th,
        .content-preview table td {
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
        }

        .content-preview table th {
            background: #f9fafb;
            font-weight: 600;
        }

        .content-preview blockquote {
            border-left: 4px solid var(--primary);
            padding-left: 1rem;
            color: #6b7280;
        }

        .content-preview a {
            color: var(--primary);
        }
    </style>
@endsection
