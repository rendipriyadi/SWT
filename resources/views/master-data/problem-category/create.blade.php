@extends('layouts.main')

@section('title', 'Create Problem Category - Master Data')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create Problem Category</h1>
            <p class="text-muted mb-0">Add a new problem category for reports</p>
        </div>
        <a href="{{ route('master-data.problem-category.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Categories
        </a>
    </div>

    <!-- Create Form -->
    <div class="card shadow">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('master-data.problem-category.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required
                                   placeholder="Enter category name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="color" class="form-label">Color <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color" name="color" value="{{ old('color', '#6c757d') }}" required>
                                <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                       id="colorText" value="{{ old('color', '#6c757d') }}" readonly>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="2" 
                              placeholder="Optional description for this category">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('master-data.problem-category.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorText = document.getElementById('colorText');
    
    colorInput.addEventListener('input', function() {
        colorText.value = this.value;
    });
    
    colorText.addEventListener('input', function() {
        if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
            colorInput.value = this.value;
        }
    });
});
</script>
@endsection
