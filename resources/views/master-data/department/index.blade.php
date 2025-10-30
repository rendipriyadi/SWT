@extends('layouts.main')

@section('title', 'Master Data - Department')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Department</h1>
            <p class="text-muted">Manage department and work group data </p>
        </div>
        <div>
            <a href="{{ route('master-data.department.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header p-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h6 class="m-0 font-weight-bold text-dark">DATA DEPARTMENT</h6>
        </div>
        <div class="card-body p-3">
            <!-- Desktop Table -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered department-table" id="departmentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Supervisor</th>
                            <th class="text-center" style="width: 200px;">Department</th>
                            <th class="text-center">Work Group</th>
                            <th class="text-center">Email</th>
                            <th class="text-center" style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $index => $department)
                        <tr data-supervisor="{{ $department->supervisor }}" data-department="{{ $department->departemen ?? $department->name }}" data-workgroup="{{ $department->workgroup ?: '-' }}" data-email="{{ $department->email ?: '-' }}">
                            <td>{{ $index + 1 }}</td>
                            <td data-supervisor="{{ $department->supervisor }}">{{ $department->supervisor }}</td>
                            <td data-department="{{ $department->departemen ?? $department->name }}">{{ $department->departemen ?? $department->name }}</td>
                            <td data-workgroup="{{ $department->workgroup ?: '-' }}">{{ $department->workgroup ?: '-' }}</td>
                            <td data-email="{{ $department->email ?: '-' }}">{{ $department->email ?: '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('master-data.department.show', ['department' => $department]) }}" class="btn btn-sm btn-info no-row-nav">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('master-data.department.edit', ['department' => $department]) }}" class="btn btn-sm btn-warning no-row-nav">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger no-row-nav delete-dept-btn"
                                        data-action="{{ route('master-data.department.destroy', ['department' => $department]) }}"
                                        data-name="{{ $department->departemen ?? $department->name }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Table -->
            <div class="d-block d-md-none">
                <div class="table-responsive">
                    <table class="table table-bordered department-table-mobile">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Supervisor</th>
                                <th>Department</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departments as $index => $department)
                            <tr class="mobile-table-row" data-bs-toggle="collapse" data-bs-target="#details{{ $department->id }}" aria-expanded="false">
                                <td class="text-center">
                                    <span class="d-flex align-items-center gap-1 ms-2">
                                        {{ $index + 1 }}
                                        <i class="fas fa-chevron-down mobile-arrow"></i>
                                    </span>
                                </td>
                                <td>{{ $department->supervisor }}</td>
                                <td>{{ $department->departemen ?? $department->name }}</td>
                            </tr>
                            <tr class="collapse" id="details{{ $department->id }}">
                                <td colspan="3" class="p-0">
                                    <div class="mobile-details ps-3">
                                        <div class="row p-3">
                                            <div class="col-6">
                                                <strong>Work Group:</strong><br>
                                                <span class="text-muted ms-2">{{ $department->workgroup ?: '-' }}</span>
                                            </div>
                                            <div class="col-6">
                                                <strong>Email:</strong><br>
                                                <span class="text-muted ms-2">{{ $department->email ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="mobile-action-buttons p-3 pt-0">
                                            <a href="{{ route('master-data.department.show', ['department' => $department]) }}" class="btn btn-sm btn-info mobile-action-btn">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('master-data.department.edit', ['department' => $department]) }}" class="btn btn-sm btn-warning mobile-action-btn">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger mobile-action-btn delete-dept-btn"
                                                    data-action="{{ route('master-data.department.destroy', ['department' => $department]) }}"
                                                    data-name="{{ $department->departemen ?? $department->name }}">
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
        </div>
    </div>
</div>

<!-- Department Detail Modal -->
<div class="modal fade" id="departmentDetailModal" tabindex="-1" aria-labelledby="departmentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="departmentDetailModalLabel">
                    <i class="fas fa-building me-2 text-primary"></i>Department Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div class="row g-3 small">
                    <div class="col-md-6">
                        <div class="fw-semibold">Supervisor</div>
                        <div id="dd_supervisor" class="value"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold">Department</div>
                        <div id="dd_department" class="value"></div>
                    </div>
                    <div class="col-12">
                        <div class="fw-semibold">Work Group</div>
                        <div id="dd_workgroup" class="value"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Removed row-click styles; keep default table styles */

/* Collapse animation */
.collapse {
    transition: height 0.3s ease;
}

/* Action buttons spacing */
.gap-2 > * {
    margin-right: 0.5rem;
}

.gap-2 > *:last-child {
    margin-right: 0;
}

/* Bigger typography for Department Details modal */
#departmentDetailModal .modal-dialog { max-width: 900px; }
#departmentDetailModal .modal-body { font-size: 1rem; }
#departmentDetailModal .modal-title { font-size: 1.25rem; }
#departmentDetailModal .fw-semibold { font-size: 0.95rem; color: var(--text-secondary); }
#departmentDetailModal .value { font-size: 1.05rem; color: var(--text-primary); }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Keep native table styles: DataTables disabled on department page
    
    // Removed row-click navigation; using View button only
    
    
    // Handle Bootstrap collapse events for arrow rotation
    $('.collapse').on('show.bs.collapse', function() {
        const targetId = $(this).attr('id');
        const row = $(`[data-bs-target="#${targetId}"]`);
        row.attr('aria-expanded', 'true');
        
        // Add smooth animation class
        $(this).addClass('show');
    });
    
    $('.collapse').on('hide.bs.collapse', function() {
        const targetId = $(this).attr('id');
        const row = $(`[data-bs-target="#${targetId}"]`);
        row.attr('aria-expanded', 'false');
        
        // Remove smooth animation class
        $(this).removeClass('show');
    });
    
    // Enhanced mobile row click with better transitions
    $('.mobile-table-row').on('click', function(e) {
        // Don't trigger if clicking on action buttons
        if ($(e.target).closest('a, button').length > 0) {
            return;
        }
        
        const target = $(this).data('bs-target');
        const isExpanded = $(this).attr('aria-expanded') === 'true';
        
        // Close all other rows with smooth transition
        $('.mobile-table-row').not(this).each(function() {
            const otherTarget = $(this).data('bs-target');
            $(otherTarget).removeClass('show');
            $(this).attr('aria-expanded', 'false');
        });
        
        // Toggle current row
        if (isExpanded) {
            $(this).attr('aria-expanded', 'false');
            $(target).removeClass('show');
        } else {
            $(this).attr('aria-expanded', 'true');
            $(target).addClass('show');
        }
    });
    // SweetAlert2 handler for department deletes (match /area)
    $(document).on('click', '.delete-dept-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const actionUrl = $(this).data('action');
        const deptName = $(this).data('name');

        // Fallback if Swal is unavailable
        if (typeof Swal === 'undefined') {
            if (confirm(`Are you sure you want to delete department "${deptName}"?`)) {
                const form = document.getElementById('deleteDepartmentHiddenForm');
                form.setAttribute('action', actionUrl);
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
            html: `Are you sure you want to delete the department <strong>${deptName}</strong>?<br><span class="text-danger small">Deleted data cannot be recovered!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            ...theme
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteDepartmentHiddenForm');
                form.setAttribute('action', actionUrl);
                form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection

<!-- Hidden form to submit DELETE after SweetAlert2 confirm -->
<form id="deleteDepartmentHiddenForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
