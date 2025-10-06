@extends('layouts.main')

@section('title', 'Area Details - ' . $area->name)

@push('styles')
<style>
.area-detail-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.station-table {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.station-table .table {
    margin: 0;
}

.station-table .table th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #495057;
    padding: 1rem;
}

.station-table .table td {
    border: none;
    padding: 1rem;
    vertical-align: middle;
}

.station-table .table tbody tr {
    border-bottom: 1px solid #e9ecef;
}

.station-table .table tbody tr:last-child {
    border-bottom: none;
}

.email-badge {
    background: #e3f2fd;
    color: #1976d2;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.no-email-badge {
    background: #f5f5f5;
    color: #757575;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.station-name {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1.1rem;
}

.person-name {
    color: #6c757d;
    font-size: 0.95rem;
}

.back-btn {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid #ffffff !important;
    color: white;
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: #ffffff !important;
    color: white;
    transform: translateY(-2px);
}

.stats-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    color: #667eea;
}

.stats-label {
    color: #6c757d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Area Detail Header -->
    <div class="area-detail-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2">
                    <i class="fas fa-map-marker-alt me-3"></i>{{ $area->name }}
                </h1>
                <p class="mb-0 opacity-75">Area Details & Station Information</p>
            </div>
            <div>
                <a href="{{ route('master-data.area.index') }}" class="btn back-btn">
                    <i class="fas fa-arrow-left me-2"></i>Back to Areas
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics removed as requested -->

    <!-- Station Details Table -->
    <div class="station-table">
        <div class="p-4 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Station Details
                </h5>
                <div>
                    <a href="{{ route('master-data.area.edit', $area->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit Area
                    </a>
                </div>
            </div>
        </div>

        @if($area->penanggungJawabs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="30%">Station</th>
                            <th width="35%">Person in Charge</th>
                            <th width="35%">Email Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($area->penanggungJawabs as $pj)
                            <tr>
                                <td>
                                    <div class="station-name">{{ $pj->station }}</div>
                                </td>
                                <td>
                                    <div class="person-name">{{ $pj->name }}</div>
                                </td>
                                <td>
                                    @if($pj->email)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope text-primary me-2"></i>
                                            <span class="email-badge">{{ $pj->email }}</span>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope text-muted me-2"></i>
                                            <span class="no-email-badge">No email assigned</span>
                                        </div>
                                    @endif
                                </td>
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-exclamation-circle text-muted fa-3x mb-3"></i>
                <h5 class="text-muted">No Stations Found</h5>
                <p class="text-muted">This area doesn't have any stations yet.</p>
                <a href="{{ route('master-data.area.edit', $area->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Stations
                </a>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling for better UX
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
            this.style.transform = 'translateX(5px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.transform = 'translateX(0)';
        });
    });
});
</script>
@endpush
