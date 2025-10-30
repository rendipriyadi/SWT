@extends('layouts.main')

@section('title', 'Problem Categories - Master Data')

@section('content')
<div class="container-fluid">
    <style>
    /* Enforce center alignment for headers and specific columns */
    .problem-category-table thead th { text-align: center !important; }
    .problem-category-table td.text-center { text-align: center !important; vertical-align: middle; }
    /* Make rows feel clickable with hover state */
    .problem-category-table tbody tr.category-row { cursor: pointer; }
    .problem-category-table tbody tr.category-row:hover { background-color: rgba(13,110,253,0.08); }
    
    /* Dark mode table row hover */
    :root[data-theme="dark"] .problem-category-table tbody tr.category-row:hover { 
      background-color: rgba(102,170,255,0.12) !important; 
    }
    
    /* Dark mode for Delete Confirmation modal */
    :root[data-theme="dark"] #deleteCategoryModal .modal-content {
      background-color: var(--bg-card) !important;
      color: var(--text-primary) !important;
      border-color: var(--border-color) !important;
    }
    
    :root[data-theme="dark"] #deleteCategoryModal .modal-header {
      border-bottom-color: var(--border-color) !important;
    }
    
    :root[data-theme="dark"] #deleteCategoryModal .modal-footer {
      border-top-color: var(--border-color) !important;
    }
    
    :root[data-theme="dark"] #deleteCategoryModal .modal-title {
      color: #dc3545 !important;
    }
    
    :root[data-theme="dark"] #deleteCategoryModal .modal-body {
      color: var(--text-primary) !important;
    }
    
    :root[data-theme="dark"] #deleteCategoryModal .text-danger {
      color: #ff6b6b !important;
    }
    
    :root[data-theme="dark"] #deleteCategoryModal .btn-close {
      filter: invert(1) grayscale(100%) brightness(200%);
    }
    
    /* Dark mode for Category Detail modal */
    :root[data-theme="dark"] #viewCategoryModal .modal-content {
      background-color: var(--bg-card) !important;
      color: var(--text-primary) !important;
      border-color: var(--border-color) !important;
    }
    
    :root[data-theme="dark"] #viewCategoryModal .modal-header {
      border-bottom-color: var(--border-color) !important;
      background-color: var(--bg-surface) !important;
    }
    
    :root[data-theme="dark"] #viewCategoryModal .modal-title {
      color: var(--text-primary) !important;
    }
    
    :root[data-theme="dark"] #viewCategoryModal .modal-body {
      background-color: var(--bg-card) !important;
      color: var(--text-primary) !important;
    }
    
    :root[data-theme="dark"] #viewCategoryModal .modal-footer {
      border-top-color: var(--border-color) !important;
      background-color: var(--bg-surface) !important;
    }
    
    :root[data-theme="dark"] #viewCategoryModal .btn-close {
      filter: invert(1) grayscale(100%) brightness(200%);
    }
    
    :root[data-theme="dark"] #viewCategoryModal .fw-semibold {
      color: var(--text-secondary) !important;
    }
    
    :root[data-theme="dark"] #viewCategoryModal .text-muted {
      color: var(--text-secondary) !important;
    }
    
    /* Dark mode for table content */
    :root[data-theme="dark"] .problem-category-table tbody tr td {
      color: var(--text-primary) !important;
      border-color: var(--border-color) !important;
    }
    
    :root[data-theme="dark"] .problem-category-table thead th {
      color: var(--text-secondary) !important;
      border-color: var(--border-color) !important;
      background-color: var(--bg-surface) !important;
    }
    
    :root[data-theme="dark"] .problem-category-table .text-muted {
      color: var(--text-secondary) !important;
    }
    
    /* Dark mode for color preview borders */
    :root[data-theme="dark"] .color-preview {
      border-color: var(--border-color) !important;
    }
    
    /* Mobile table styles */
    .mobile-table-row {
      cursor: pointer;
    }
    .mobile-table-row:hover {
      background-color: rgba(13,110,253,0.08);
    }
    :root[data-theme="dark"] .mobile-table-row:hover {
      background-color: rgba(102,170,255,0.12) !important;
    }
    .mobile-arrow {
      transition: transform 0.3s ease;
      font-size: 0.75rem;
    }
    .mobile-table-row[aria-expanded="true"] .mobile-arrow {
      transform: rotate(180deg);
    }
    .mobile-details {
      background-color: #f8f9fa;
      border-top: 1px solid #dee2e6;
    }
    :root[data-theme="dark"] .mobile-details {
      background-color: var(--bg-surface);
      border-top-color: var(--border-color);
    }
    .mobile-action-buttons {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }
    .mobile-action-btn {
      flex: 1;
      min-width: 100px;
    }
    </style>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Problem Categories</h1>
            <p class="text-muted mb-0">Manage problem categories for reports</p>
        </div>
        <a href="{{ route('master-data.problem-category.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Category
        </a>
    </div>

    <!-- Categories Table -->
    <div class="card shadow">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($categories->count() > 0)
                <!-- Desktop Table -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover problem-category-table">
                        <thead>
                            <tr>
                                <th class="text-center" width="20%">Problem Category</th>
                                <th class="text-center" width="15%">Color</th>
                                <th class="text-center" width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $index => $category)
                            <tr class="category-row" style="cursor: pointer;" 
                                data-id="{{ $category->id }}"
                                data-name="{{ e($category->name) }}"
                                data-color="{{ e($category->color) }}"
                                data-description="{{ e($category->description ?? '') }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="color-preview me-3" style="width: 20px; height: 20px; background-color: {{ $category->color }}; border-radius: 4px; border: 1px solid #ddd;"></div>
                                        <div>
                                            <strong>{{ $category->name }}</strong>
                                            @if($category->description)
                                                @php
                                                    $desc = strip_tags($category->description ?? '');
                                                    $shortDesc = \Illuminate\Support\Str::words($desc, 10, '...');
                                                @endphp
                                                <br><small class="text-muted">{{ $shortDesc }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center justify-content-center">
                                        <div class="color-preview me-2" style="width: 20px; height: 20px; background-color: {{ $category->color }}; border-radius: 4px; border: 1px solid #ddd;"></div>
                                        <small class="text-muted">{{ $category->color }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1 justify-content-center">
                                        <a href="{{ route('master-data.problem-category.edit', ['problem_category' => $category]) }}" class="btn btn-sm btn-warning" title="Edit" onclick="event.stopPropagation();">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-problem-category-btn" data-id="{{ $category->getRouteKey() }}" data-name="{{ $category->name }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Table -->
                <div class="d-block d-md-none">
                    <div class="table-responsive">
                        <table class="table table-bordered problem-category-table-mobile">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Category</th>
                                    <th style="width: 80px;">Color</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $index => $category)
                                <tr class="mobile-table-row" data-bs-toggle="collapse" data-bs-target="#details{{ $category->id }}" aria-expanded="false">
                                    <td class="text-center">
                                        <span class="d-flex align-items-center justify-content-center gap-1">
                                            {{ $index + 1 }}
                                            <i class="fas fa-chevron-down mobile-arrow"></i>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="color-preview me-2" style="width: 16px; height: 16px; background-color: {{ $category->color }}; border-radius: 3px; border: 1px solid #ddd;"></div>
                                            <strong>{{ $category->name }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">{{ $category->color }}</small>
                                    </td>
                                </tr>
                                <tr class="collapse" id="details{{ $category->id }}">
                                    <td colspan="3" class="p-0">
                                        <div class="mobile-details">
                                            @if($category->description)
                                            <div class="row p-3">
                                                <div class="col-12">
                                                    <strong>Description:</strong><br>
                                                    <span class="text-muted">{{ strip_tags($category->description) }}</span>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="mobile-action-buttons p-3 pt-0">
                                                <a href="{{ route('master-data.problem-category.edit', ['problem_category' => $category]) }}" class="btn btn-sm btn-warning mobile-action-btn">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger mobile-action-btn delete-problem-category-btn" data-id="{{ $category->getRouteKey() }}" data-name="{{ $category->name }}">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Problem Categories Found</h5>
                    <p class="text-muted">Start by creating your first problem category.</p>
                    <a href="{{ route('master-data.problem-category.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add First Category
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden form for SweetAlert2 deletion -->
<form id="deleteProblemCategoryForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<!-- Detail Modal -->
<div class="modal fade" id="viewCategoryModal" tabindex="-1" aria-labelledby="viewCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="viewCategoryModalLabel">Category Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="fw-semibold mb-1">Problem Category</div>
                    <div id="vc_name" class="text-break"></div>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold mb-1">Color</div>
                    <div class="d-flex align-items-center gap-2">
                        <span id="vc_color_swatch" style="display:inline-block;width:20px;height:20px;border-radius:4px;border:1px solid #ddd;"></span>
                        <span id="vc_color_code" class="text-muted"></span>
                    </div>
                </div>
                <div>
                    <div class="fw-semibold mb-1">Description</div>
                    <div id="vc_description" class="text-break"></div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    </div>

@push('scripts')
<script>
// Row click to open detail modal (desktop only)
document.addEventListener('click', function(e){
    const row = e.target.closest('tr.category-row');
    if (!row) return;
    // Ignore clicks on buttons/links inside row
    if (e.target.closest('a, button, .btn')) return;
    const name = row.getAttribute('data-name') || '';
    const color = row.getAttribute('data-color') || '#cccccc';
    const desc = row.getAttribute('data-description') || '—';
    document.getElementById('vc_name').textContent = name;
    document.getElementById('vc_color_code').textContent = color;
    document.getElementById('vc_color_swatch').style.backgroundColor = color;
    document.getElementById('vc_description').textContent = desc;
    new bootstrap.Modal(document.getElementById('viewCategoryModal')).show();
});

// Mobile collapse handler
$('.mobile-table-row').on('click', function(e) {
    // Don't trigger if clicking on action buttons
    if ($(e.target).closest('a, button').length > 0) return;
    
    const targetId = $(this).data('bs-target');
    const isExpanded = $(this).attr('aria-expanded') === 'true';
    
    // Close all other rows first
    $('.mobile-table-row').not(this).each(function() {
        if ($(this).attr('aria-expanded') === 'true') {
            const otherTargetId = $(this).data('bs-target');
            bootstrap.Collapse.getInstance(document.querySelector(otherTargetId))?.hide();
        }
    });
});

// SweetAlert2 delete for problem categories
document.addEventListener('click', function(e){
    const btn = e.target.closest('.delete-problem-category-btn');
    if (!btn) return;
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');

    if (typeof Swal === 'undefined') {
        if (confirm(`Are you sure you want to delete category "${name}"?`)) {
            const form = document.getElementById('deleteProblemCategoryForm');
            form.setAttribute('action', `/master-data/problem-category/${id}`);
            form.submit();
        }
        return;
    }

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const theme = isDark ? {
        background: '#1e1e1e',
        color: '#e0e0e0',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
    } : {
        background: '#ffffff',
        color: '#212529',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
    };
    
    Swal.fire({
        title: 'Delete Confirmation',
        html: `Are you sure you want to delete the category <strong>${name}</strong>?<br><span class="text-danger small">This action cannot be undone.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        ...theme
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteProblemCategoryForm');
            form.setAttribute('action', `/master-data/problem-category/${id}`);
            form.submit();
        }
    });
});
</script>
@endpush
@endsection
