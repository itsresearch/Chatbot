@extends('layouts.panel')
@section('title', 'Create Sub-service')

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
                <li class="breadcrumb-item"><a
                        href="{{ route('client.chatbot.services.show', $service) }}">{{ $service->name }}</a></li>
                <li class="breadcrumb-item active">New Sub-service</li>
            </ol>
        </nav>
    </div>

    <div class="card" style="max-width: 900px;">
        <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2" style="color: var(--primary);"></i>New Sub-service</h5>
        <p class="text-muted mb-3">Adding sub-service to: <strong>{{ $service->category->name }} &rsaquo;
                {{ $service->name }}</strong></p>

        <form method="POST" action="{{ route('client.chatbot.sub-services.store', $service) }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Sub-service Name <span
                        class="text-danger">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    class="form-control @error('name') is-invalid @enderror" placeholder="e.g. E-commerce Integration"
                    required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="short_description" class="form-label fw-semibold">Short Description</label>
                <textarea id="short_description" name="short_description" rows="2"
                    class="form-control @error('short_description') is-invalid @enderror"
                    placeholder="Brief summary shown in the widget list...">{{ old('short_description') }}</textarea>
                @error('short_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="detail_content" class="form-label fw-semibold">
                    Detail Content <small class="text-muted fw-normal">(Rich text — shown when visitor clicks)</small>
                </label>
                <textarea id="detail_content" name="detail_content" class="form-control @error('detail_content') is-invalid @enderror">{{ old('detail_content') }}</textarea>
                @error('detail_content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary-gradient px-4">
                    <i class="bi bi-check-lg me-1"></i> Create Sub-service
                </button>
                <a href="{{ route('client.chatbot.services.show', $service) }}" class="btn btn-light px-4">Cancel</a>
            </div>
        </form>
    </div>

    {{-- CKEditor 5 CDN --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector('#detail_content'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', 'strikethrough', '|',
                'bulletedList', 'numberedList', '|',
                'blockQuote', 'insertTable', 'link', '|',
                'undo', 'redo'
            ],
            heading: {
                options: [{
                        model: 'paragraph',
                        title: 'Paragraph',
                        class: 'ck-heading_paragraph'
                    },
                    {
                        model: 'heading2',
                        view: 'h2',
                        title: 'Heading 2',
                        class: 'ck-heading_heading2'
                    },
                    {
                        model: 'heading3',
                        view: 'h3',
                        title: 'Heading 3',
                        class: 'ck-heading_heading3'
                    },
                ]
            }
        }).catch(err => console.error(err));
    </script>
    <style>
        .ck-editor__editable {
            min-height: 200px;
        }
    </style>
@endsection
