@extends('layouts.panel')
@section('title', $category->name . ' — Services')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('client.chatbot.categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--text-main);">{{ $category->name }}</h4>
            @if ($category->description)
                <p class="text-muted mb-0">{{ $category->description }}</p>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('client.chatbot.services.create', $category) }}" class="btn btn-primary-gradient btn-sm px-4">
                <i class="bi bi-plus-lg me-1"></i> Add Service
            </a>
            <a href="{{ route('client.chatbot.categories.edit', $category) }}" class="btn btn-outline-warning btn-sm px-3">
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

    @if ($category->services->isEmpty())
        <div class="card text-center py-5">
            <div class="mb-3">
                <i class="bi bi-layers" style="font-size: 48px; color: var(--text-muted);"></i>
            </div>
            <h5 class="text-muted mb-2">No services yet</h5>
            <p class="text-muted mb-3">Add services under this category for visitors to browse.</p>
            <div>
                <a href="{{ route('client.chatbot.services.create', $category) }}" class="btn btn-primary-gradient px-4">
                    <i class="bi bi-plus-lg me-1"></i> Add Service
                </a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach ($category->services as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100" style="padding: 20px;">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <h6 class="fw-bold mb-1">{{ $service->name }}</h6>
                            <span class="badge bg-light text-dark">{{ $service->sub_services_count }} sub(s)</span>
                        </div>
                        @if ($service->description)
                            <p class="text-muted small mb-3">{{ Str::limit($service->description, 80) }}</p>
                        @else
                            <p class="text-muted small mb-3 fst-italic">No description</p>
                        @endif
                        <div class="mt-auto d-flex gap-1">
                            <a href="{{ route('client.chatbot.services.show', $service) }}"
                                class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('client.chatbot.services.edit', $service) }}"
                                class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('client.chatbot.services.destroy', $service) }}" method="POST"
                                onsubmit="return confirm('Delete this service and all its sub-services?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
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
