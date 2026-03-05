@extends('layouts.panel')
@section('title', 'Chatbot Categories')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('content')
    {{-- Website Switcher Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--text-main);">
                <i class="bi bi-diagram-3-fill me-2" style="color: var(--primary);"></i>Chatbot Categories
            </h4>
            <p class="text-muted mb-0">Manage the knowledge base categories for your chatbot widget.</p>
        </div>
        <a href="{{ route('client.chatbot.categories.create') }}" class="btn btn-primary-gradient btn-sm px-4">
            <i class="bi bi-plus-lg me-1"></i> New Category
        </a>
    </div>

    {{-- Website Tabs --}}
    @if ($websites->count() > 1)
        <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
            @foreach ($websites as $w)
                <a href="{{ route('client.chatbot.categories.index', ['website_id' => $w->id]) }}"
                    class="btn btn-sm {{ $selectedWebsiteId == $w->id ? 'btn-primary-gradient' : 'btn-outline-secondary' }} px-3">
                    <i class="bi bi-globe me-1"></i>{{ $w->name }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Categories List --}}
    @if ($categories->isEmpty())
        <div class="card text-center py-5">
            <div class="mb-3">
                <i class="bi bi-inbox" style="font-size: 48px; color: var(--text-muted);"></i>
            </div>
            <h5 class="text-muted mb-2">No categories yet</h5>
            <p class="text-muted mb-3">Create your first category to build the chatbot knowledge base.</p>
            <div>
                <a href="{{ route('client.chatbot.categories.create') }}" class="btn btn-primary-gradient px-4">
                    <i class="bi bi-plus-lg me-1"></i> Create Category
                </a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach ($categories as $cat)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100" style="padding: 20px;">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <h6 class="fw-bold mb-1">{{ $cat->name }}</h6>
                            <span class="badge bg-light text-dark">{{ $cat->services_count }} service(s)</span>
                        </div>
                        @if ($cat->description)
                            <p class="text-muted small mb-3">{{ Str::limit($cat->description, 80) }}</p>
                        @else
                            <p class="text-muted small mb-3 fst-italic">No description</p>
                        @endif
                        <div class="mt-auto d-flex gap-1">
                            <a href="{{ route('client.chatbot.categories.show', $cat) }}"
                                class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('client.chatbot.categories.edit', $cat) }}"
                                class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('client.chatbot.categories.destroy', $cat) }}" method="POST"
                                onsubmit="return confirm('Delete this category and all its services?')">
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
