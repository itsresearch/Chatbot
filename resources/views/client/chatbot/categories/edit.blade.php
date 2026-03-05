@extends('layouts.panel')
@section('title', 'Edit Category')

@section('sidebar-menu')
    @include('client.partials.sidebar-menu')
@endsection

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('client.chatbot.categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item active">Edit: {{ $category->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="card" style="max-width: 700px;">
        <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2" style="color: var(--primary);"></i>Edit Category</h5>

        <form method="POST" action="{{ route('client.chatbot.categories.update', $category) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Category Name <span
                        class="text-danger">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                    class="form-control @error('name') is-invalid @enderror" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Description</label>
                <textarea id="description" name="description" rows="3"
                    class="form-control @error('description') is-invalid @enderror" placeholder="Brief description...">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary-gradient px-4">
                    <i class="bi bi-check-lg me-1"></i> Update Category
                </button>
                <a href="{{ route('client.chatbot.categories.index') }}" class="btn btn-light px-4">Cancel</a>
            </div>
        </form>
    </div>
@endsection
