@extends('layouts.main')

@section('title', 'Master Data - Area')

@push('styles')
<link href="{{ asset('css/area-cards.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Area</h1>
            <p class="text-muted">Manage area, station, and person in charges</p>
        </div>
        <div>
            <a href="{{ route('master-data.area.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Area
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
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

    <!-- Data Cards -->
    <div class="row">
        @forelse($areas as $index => $area)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card h-100 area-card position-relative">
                    <a href="{{ route('master-data.area.show', $area) }}" class="text-reset text-decoration-none d-block">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-map-marker-alt me-2"></i>{{ $area->name }}
                            </h6>
                            <span class="badge bg-light text-primary">{{ $area->penanggungJawabs->count() }} Station</span>
                        </div>
                        <div class="card-body">
                        @if($area->penanggungJawabs->count() > 0)
                            <div class="mb-3">
                                <h6 class="text-muted mb-2 small">STATION & PERSON IN CHARGE</h6>
                                @foreach($area->penanggungJawabs as $pj)
                                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom station-item">
                                        <div>
                                            <span class="fw-semibold text-dark">{{ $pj->station }}</span>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">{{ $pj->name }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-circle text-muted fa-2x mb-2"></i>
                                <p class="text-muted mb-0">There is no station yet</p>
                            </div>
                        @endif
                        </div>
                    </a>
                    <div class="card-footer bg-transparent border-0 pt-0 position-relative">
                        <div class="d-flex gap-2">
                            <a href="{{ route('master-data.area.show', $area) }}" 
                               class="btn btn-outline-info btn-sm flex-fill">
                                <i class="fas fa-eye me-1"></i>View Details
                            </a>
                            <a href="{{ route('master-data.area.edit', $area) }}" 
                               class="btn btn-outline-warning btn-sm flex-fill">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <button class="btn btn-outline-danger btn-sm flex-fill delete-btn" 
                                    data-slug="{{ $area->slug }}" 
                                    data-name="{{ $area->name }}">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0 empty-state">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-inbox text-muted fa-3x mb-3"></i>
                        <h5 class="text-muted">No area data yet</h5>
                        <p class="text-muted">Click the "Add Area" button to add the first area</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Hidden form for SweetAlert2 submission -->
<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const areaSlug = $(this).data('slug');
        const areaName = $(this).data('name');
        const actionUrl = '{{ route("master-data.area.destroy", ":slug") }}'.replace(':slug', areaSlug);

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
            html: `Are you sure you want to delete the area <strong>${areaName}</strong>?<br><span class="text-danger small">Deleted data cannot be recovered!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            ...theme
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.setAttribute('action', actionUrl);
                form.submit();
            }
        });
    });
});
</script>
@endpush
