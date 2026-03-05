@extends('layouts.panel')
@section('title', $service->name . ' — Sub-services')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('client.chatbot.categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('client.chatbot.categories.show', $service->category) }}">{{ $service->category->name }}</a>
                </li>
                <li class="breadcrumb-item active">{{ $service->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--text-main);">{{ $service->name }}</h4>
            @if ($service->description)
                <p class="text-muted mb-0">{{ $service->description }}</p>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('client.chatbot.sub-services.create', $service) }}"
                class="btn btn-primary-gradient btn-sm px-4">
                <i class="bi bi-plus-lg me-1"></i> Add Sub-service
            </a>
            <a href="{{ route('client.chatbot.services.edit', $service) }}" class="btn btn-outline-warning btn-sm px-3">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($service->subServices->isEmpty())
        <div class="card text-center py-5">
            <div class="mb-3">
                <i class="bi bi-diagram-2" style="font-size: 48px; color: var(--text-muted);"></i>
            </div>
            <h5 class="text-muted mb-2">No sub-services yet</h5>
            <p class="text-muted mb-3">Add sub-services with detailed descriptions for visitors.</p>
            <div>
                <a href="{{ route('client.chatbot.sub-services.create', $service) }}" class="btn btn-primary-gradient px-4">
                    <i class="bi bi-plus-lg me-1"></i> Add Sub-service
                </a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach ($service->subServices as $sub)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100" style="cursor: pointer;"
                        onclick="window.location='{{ route('client.chatbot.sub-services.show', $sub) }}'">
                        <h6 class="fw-bold mb-2" style="color: var(--text-main);">{{ $sub->name }}</h6>
                        @if ($sub->short_description)
                            <p class="text-muted small mb-3">{{ Str::limit($sub->short_description, 80) }}</p>
                        @else
                            <p class="text-muted small mb-3"><em>No description</em></p>
                        @endif
                        <div class="d-flex gap-1 mt-auto">
                            <a href="{{ route('client.chatbot.sub-services.show', $sub) }}"
                                class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('client.chatbot.sub-services.edit', $sub) }}"
                                class="btn btn-sm btn-outline-warning" onclick="event.stopPropagation();" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('client.chatbot.sub-services.destroy', $sub) }}" method="POST"
                                onsubmit="event.stopPropagation(); return confirm('Delete this sub-service?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
